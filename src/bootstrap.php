<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| LOAD CONFIG
|--------------------------------------------------------------------------
*/

$config = require __DIR__ . '/../config.php';


/*
|--------------------------------------------------------------------------
| DATA DIRECTORY
|--------------------------------------------------------------------------
|
| على Render نستخدم Persistent Disk:
| /var/data
|
| محليًا أو على استضافة عادية:
| storage/data
|
*/

$renderDataDir = '/var/data';


/*
|--------------------------------------------------------------------------
| SOURCE DATA DIRECTORY
|--------------------------------------------------------------------------
|
| هذا هو مكان ملفات JSON الأصلية الموجودة داخل المشروع.
|
*/

$projectDataDir = $config['data_dir']
    ?? __DIR__ . '/../storage/data';


/*
|--------------------------------------------------------------------------
| DETECT RENDER PERSISTENT DISK
|--------------------------------------------------------------------------
*/

if (is_dir($renderDataDir) && is_writable($renderDataDir)) {

    /*
    |--------------------------------------------------------------------------
    | Render Persistent Storage
    |--------------------------------------------------------------------------
    */

    $config['data_dir'] = $renderDataDir;


} else {

    /*
    |--------------------------------------------------------------------------
    | Local / Normal Hosting
    |--------------------------------------------------------------------------
    */

    $config['data_dir'] = $projectDataDir;

}


/*
|--------------------------------------------------------------------------
| CREATE DATA DIRECTORY
|--------------------------------------------------------------------------
*/

if (!is_dir($config['data_dir'])) {

    if (!mkdir(
        $config['data_dir'],
        0775,
        true
    ) && !is_dir($config['data_dir'])) {

        throw new RuntimeException(
            'Unable to create data directory: '
            . $config['data_dir']
        );

    }

}


/*
|--------------------------------------------------------------------------
| FIRST RUN MIGRATION
|--------------------------------------------------------------------------
|
| إذا كنا على Render والـ Persistent Disk فارغ،
| انسخ ملفات JSON الأصلية من المشروع إليه.
|
| مهم:
| لا يتم استبدال أي ملف موجود مسبقًا.
| لذلك لن يتم حذف تعديلاتك.
|
*/

if (
    $config['data_dir'] === $renderDataDir
    &&
    is_dir($projectDataDir)
) {

    $jsonFiles = glob(
        rtrim($projectDataDir, '/')
        . '/*.json'
    );


    if (is_array($jsonFiles)) {

        foreach ($jsonFiles as $sourceFile) {

            $fileName = basename(
                $sourceFile
            );


            $targetFile =
                rtrim($renderDataDir, '/')
                . '/'
                . $fileName;


            /*
            |--------------------------------------------------------------------------
            | انسخ فقط إذا الملف غير موجود
            |--------------------------------------------------------------------------
            */

            if (!file_exists($targetFile)) {

                copy(
                    $sourceFile,
                    $targetFile
                );

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/

$sessionName = $config['session_name']
    ?? 'ONPLAY_SESSION';


if (session_status() === PHP_SESSION_NONE) {

    session_name($sessionName);

    session_start();

}


/*
|--------------------------------------------------------------------------
| DATA FILE PATH
|--------------------------------------------------------------------------
*/

function data_path(string $name): string
{
    global $config;


    $safeName = preg_replace(
        '/[^a-z0-9_-]/i',
        '',
        $name
    );


    return rtrim(
        $config['data_dir'],
        '/'
    )
    . '/'
    . $safeName
    . '.json';
}


/*
|--------------------------------------------------------------------------
| READ JSON
|--------------------------------------------------------------------------
*/

function db_read(string $name): array
{
    $path = data_path($name);


    if (!is_file($path)) {

        return [];

    }


    $content = file_get_contents(
        $path
    );


    if ($content === false) {

        return [];

    }


    $data = json_decode(
        $content,
        true
    );


    return is_array($data)
        ? $data
        : [];
}


/*
|--------------------------------------------------------------------------
| WRITE JSON SAFELY
|--------------------------------------------------------------------------
|
| الحفظ يتم في ملف مؤقت ثم يتم استبدال الملف.
| هذا يمنع تلف ملف JSON أثناء الكتابة.
|
*/

function db_write(
    string $name,
    array $data
): bool
{
    $path = data_path($name);


    $directory = dirname(
        $path
    );


    /*
    |--------------------------------------------------------------------------
    | تأكد من وجود المجلد
    |--------------------------------------------------------------------------
    */

    if (!is_dir($directory)) {

        if (!mkdir(
            $directory,
            0775,
            true
        ) && !is_dir($directory)) {

            return false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | JSON
    |--------------------------------------------------------------------------
    */

    $json = json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
        |
        JSON_UNESCAPED_SLASHES
        |
        JSON_PRETTY_PRINT
    );


    if ($json === false) {

        return false;

    }


    /*
    |--------------------------------------------------------------------------
    | TEMP FILE
    |--------------------------------------------------------------------------
    */

    $tempPath =
        $path
        . '.'
        . uniqid('tmp_', true);


    /*
    |--------------------------------------------------------------------------
    | WRITE TEMP
    |--------------------------------------------------------------------------
    */

    if (
        file_put_contents(
            $tempPath,
            $json,
            LOCK_EX
        ) === false
    ) {

        return false;

    }


    /*
    |--------------------------------------------------------------------------
    | ATOMIC REPLACE
    |--------------------------------------------------------------------------
    */

    if (
        !rename(
            $tempPath,
            $path
        )
    ) {

        /*
        |--------------------------------------------------------------------------
        | CLEAN TEMP FILE
        |--------------------------------------------------------------------------
        */

        if (file_exists($tempPath)) {

            unlink(
                $tempPath
            );

        }

        return false;

    }


    return true;
}


/*
|--------------------------------------------------------------------------
| NEXT ID
|--------------------------------------------------------------------------
*/

function next_id(array $items): int
{
    $maxId = 0;


    foreach ($items as $item) {

        $id = (int) (
            $item['id']
            ?? 0
        );


        if ($id > $maxId) {

            $maxId = $id;

        }

    }


    return $maxId + 1;
}


/*
|--------------------------------------------------------------------------
| FIND ITEM
|--------------------------------------------------------------------------
*/

function find_item(
    string $file,
    int $id
): ?array
{
    foreach (
        db_read($file)
        as $item
    ) {

        if (
            (int) (
                $item['id']
                ?? 0
            ) === $id
        ) {

            return $item;

        }

    }


    return null;
}


/*
|--------------------------------------------------------------------------
| SAVE ITEM
|--------------------------------------------------------------------------
*/

function save_item(
    string $file,
    array $item,
    ?int $id = null
): int
{
    $items = db_read(
        $file
    );


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    if ($id !== null) {

        foreach (
            $items
            as $key => $existing
        ) {

            if (
                (int) (
                    $existing['id']
                    ?? 0
                ) === $id
            ) {

                $item['id'] = $id;

                $items[$key] = $item;

                db_write(
                    $file,
                    $items
                );

                return $id;

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    $newId = next_id(
        $items
    );


    $item['id'] = $newId;

    $items[] = $item;


    db_write(
        $file,
        $items
    );


    return $newId;
}


/*
|--------------------------------------------------------------------------
| DELETE ITEM
|--------------------------------------------------------------------------
*/

function delete_item(
    string $file,
    int $id
): void
{
    $items = db_read(
        $file
    );


    $items = array_values(
        array_filter(
            $items,
            function ($item) use ($id) {

                return
                    (int) (
                        $item['id']
                        ?? 0
                    ) !== $id;

            }
        )
    );


    db_write(
        $file,
        $items
    );
}


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

function is_admin(): bool
{
    return isset(
        $_SESSION['user']
    );
}


function current_user(): ?array
{
    if (
        !isset(
            $_SESSION['user']
        )
    ) {

        return null;

    }


    return find_item(
        'users',
        (int) $_SESSION['user']
    );
}


function has_role(
    string $role
): bool
{
    $user = current_user();


    if (!$user) {

        return false;

    }


    $userRole =
        $user['role']
        ?? '';


    /*
    |--------------------------------------------------------------------------
    | Owner لديه جميع الصلاحيات
    |--------------------------------------------------------------------------
    */

    return
        $userRole === 'owner'
        ||
        $userRole === $role;
}


/*
|--------------------------------------------------------------------------
| REQUIRE LOGIN
|--------------------------------------------------------------------------
*/

function require_login(): void
{
    if (!is_admin()) {

        header(
            'Location: login.php'
        );

        exit;

    }
}


/*
|--------------------------------------------------------------------------
| REQUIRE OWNER
|--------------------------------------------------------------------------
*/

function require_owner(): void
{
    require_login();


    if (!has_role('owner')) {

        http_response_code(
            403
        );


        exit(
            'غير مصرح لك بهذه العملية'
        );

    }

}


/*
|--------------------------------------------------------------------------
| HTML ESCAPE
|--------------------------------------------------------------------------
*/

function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| API OUTPUT
|--------------------------------------------------------------------------
*/

function api_out(
    array $data,
    int $code = 200
): never
{
    http_response_code(
        $code
    );


    header(
        'Content-Type: application/json; charset=utf-8'
    );


    header(
        'Access-Control-Allow-Origin: *'
    );


    header(
        'Access-Control-Allow-Methods: GET, POST, OPTIONS'
    );


    header(
        'Access-Control-Allow-Headers: Content-Type, Authorization'
    );


    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
        |
        JSON_UNESCAPED_SLASHES
        |
        JSON_PRETTY_PRINT
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| OPTIONS REQUEST
|--------------------------------------------------------------------------
*/

if (
    ($_SERVER['REQUEST_METHOD'] ?? '')
    === 'OPTIONS'
) {

    http_response_code(
        204
    );

    header(
        'Access-Control-Allow-Origin: *'
    );

    header(
        'Access-Control-Allow-Methods: GET, POST, OPTIONS'
    );

    header(
        'Access-Control-Allow-Headers: Content-Type, Authorization'
    );

    exit;

}
