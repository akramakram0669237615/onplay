<?php

require __DIR__ . '/../src/bootstrap.php';


/*
|--------------------------------------------------------------------------
| API ROUTER
|--------------------------------------------------------------------------
*/

$path = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';


/*
|--------------------------------------------------------------------------
| العثور على /api/
|--------------------------------------------------------------------------
*/

$pos = strpos($path, '/api/');

if ($pos === false) {

    api_out([
        'success' => false,
        'error' => [
            'code' => 404,
            'message' => 'API endpoint not found.'
        ]
    ], 404);

}


/*
|--------------------------------------------------------------------------
| تقسيم الرابط
|--------------------------------------------------------------------------
|
| مثال:
| /api/v1/banners
|
*/

$route = substr($path, $pos + 5);

$parts = explode(
    '/',
    trim($route, '/')
);


/*
|--------------------------------------------------------------------------
| API VERSION
|--------------------------------------------------------------------------
*/

$version = array_shift($parts);

if ($version !== 'v1') {

    api_out([
        'success' => false,
        'error' => [
            'code' => 404,
            'message' => 'API version not found.'
        ]
    ], 404);

}


/*
|--------------------------------------------------------------------------
| HELPER: العناصر المفعلة فقط
|--------------------------------------------------------------------------
*/

function api_visible_items($items)
{
    if (!is_array($items)) {
        return [];
    }

    return array_values(
        array_filter(
            $items,
            function ($item) {

                return ($item['enabled'] ?? true) !== false;

            }
        )
    );
}


/*
|--------------------------------------------------------------------------
| CURRENT ENDPOINT
|--------------------------------------------------------------------------
*/

$type = $parts[0] ?? '';


/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
|
| /api/v1/home
|
*/

if ($type === 'home') {

    $home = db_read('home');

    api_out([
        'success' => true,
        'page' => 'home',
        'data' => $home
    ]);

}


/*
|--------------------------------------------------------------------------
| APP CONFIG
|--------------------------------------------------------------------------
|
| /api/v1/app/config
|
*/

if (
    $type === 'app'
    &&
    ($parts[1] ?? '') === 'config'
) {

    $settings = db_read('settings');

    api_out([
        'success' => true,
        'app' => $settings
    ]);

}


/*
|--------------------------------------------------------------------------
| NOTIFICATIONS
|--------------------------------------------------------------------------
|
| /api/v1/notifications
|
*/

if ($type === 'notifications') {

    $items = api_visible_items(
        db_read('notifications')
    );

    api_out([
        'success' => true,
        'items' => $items
    ]);

}


/*
|--------------------------------------------------------------------------
| BANNERS
|--------------------------------------------------------------------------
|
| /api/v1/banners
|
*/

if ($type === 'banners') {

    $items = api_visible_items(
        db_read('banners')
    );

    api_out([
        'success' => true,
        'page' => 'banners',
        'items' => $items
    ]);

}


/*
|--------------------------------------------------------------------------
| CONTENT TYPES
|--------------------------------------------------------------------------
*/

$files = [
    'categories',
    'channels',
    'matches',
    'movies',
    'series'
];


/*
|--------------------------------------------------------------------------
| LIST
|--------------------------------------------------------------------------
|
| مثال:
| /api/v1/channels
| /api/v1/movies
|
*/

if (
    in_array($type, $files, true)
    &&
    count($parts) === 1
) {

    $items = api_visible_items(
        db_read($type)
    );

    api_out([
        'success' => true,
        'page' => $type,
        'items' => $items
    ]);

}


/*
|--------------------------------------------------------------------------
| SINGLE ITEM
|--------------------------------------------------------------------------
|
| مثال:
| /api/v1/channels/1
|
*/

if (
    in_array($type, $files, true)
    &&
    isset($parts[1])
) {

    $id = (int) $parts[1];

    $item = find_item(
        $type,
        $id
    );


    /*
    |--------------------------------------------------------------------------
    | ITEM NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$item) {

        api_out([
            'success' => false,
            'error' => [
                'code' => 404,
                'message' => 'Item not found.'
            ]
        ], 404);

    }


    /*
    |--------------------------------------------------------------------------
    | EPISODES
    |--------------------------------------------------------------------------
    |
    | /api/v1/series/1/episodes
    |
    */

    if (
        ($parts[2] ?? '') === 'episodes'
    ) {

        api_out([
            'success' => true,
            'episodes' => $item['episodes'] ?? []
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | SINGLE ITEM RESPONSE
    |--------------------------------------------------------------------------
    */

    api_out([
        'success' => true,
        'data' => $item
    ]);

}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
|
| /api/v1/search?q=...
|
*/

if ($type === 'search') {

    $query = mb_strtolower(
        trim(
            (string) ($_GET['q'] ?? '')
        )
    );


    $searchFiles = [
        'channels',
        'matches',
        'movies',
        'series'
    ];


    $results = [];


    if ($query !== '') {

        foreach ($searchFiles as $file) {

            $items = api_visible_items(
                db_read($file)
            );


            foreach ($items as $item) {

                $name =
                    $item['name']
                    ??
                    $item['title']
                    ??
                    '';


                $searchText =
                    mb_strtolower(
                        $name . ' ' .
                        ($item['description'] ?? '')
                    );


                if (
                    mb_strpos(
                        $searchText,
                        $query
                    ) !== false
                ) {

                    $results[] = [
                        'type' => $file,
                        'id' => $item['id'] ?? null,
                        'title' => $name,
                        'image' =>
                            $item['image']
                            ??
                            $item['poster']
                            ??
                            ''
                    ];

                }

            }

        }

    }


    api_out([
        'success' => true,
        'query' => $query,
        'items' => $results
    ]);

}


/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

api_out([
    'success' => false,
    'error' => [
        'code' => 404,
        'message' => 'API endpoint not found.'
    ]
], 404);
