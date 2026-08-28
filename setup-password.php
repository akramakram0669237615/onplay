<?php
// setup-password.php
// احذف هذا الملف بعد الانتهاء من إنشاء كلمة المرور.

declare(strict_types=1);

// تحميل ملفات المشروع
$bootstrap = __DIR__ . '/src/bootstrap.php';

if (!file_exists($bootstrap)) {
    exit('خطأ: لم يتم العثور على الملف src/bootstrap.php');
}

require_once $bootstrap;

// التحقق من وجود الدوال المطلوبة
if (!function_exists('db_read') || !function_exists('db_write')) {
    exit('خطأ: الدالتان db_read أو db_write غير موجودتين.');
}

// يجب إرسال كلمة المرور
if (!isset($_POST['password']) || trim((string)$_POST['password']) === '') {
    ?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد كلمة المرور</title>
    <style>
        body {
            margin: 0;
            background: #0b0b0b;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .box {
            width: 90%;
            max-width: 400px;
            background: #161616;
            padding: 30px;
            border-radius: 15px;
            box-sizing: border-box;
            border: 1px solid #333;
        }

        h2 {
            text-align: center;
            color: #ff1a1a;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-top: 15px;
            box-sizing: border-box;
            background: #222;
            border: 1px solid #444;
            border-radius: 8px;
            color: white;
            font-size: 16px;
        }

        button {
            width: 100%;
            margin-top: 15px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #e50914;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #ff1a24;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>ONPLAY</h2>

    <form method="POST">
        <input
            type="password"
            name="password"
            placeholder="أدخل كلمة المرور الجديدة"
            required
            minlength="6"
        >

        <button type="submit">
            إنشاء كلمة المرور
        </button>
    </form>
</div>

</body>
</html>
<?php

exit;

}

// قراءة المستخدمين
$users = db_read('users');

// التحقق من صحة البيانات
if (!is_array($users)) {
exit('خطأ: بيانات المستخدمين غير صحيحة.');
}

// إذا لم يوجد أي مستخدم، أنشئ مستخدمًا افتراضيًا
if (count($users) === 0) {

$users[] = [
    'username' => 'admin',
    'password' => password_hash(
        (string)$_POST['password'],
        PASSWORD_DEFAULT
    )
];

} else {

// تغيير كلمة مرور أول مستخدم
$users[0]['password'] = password_hash(
    (string)$_POST['password'],
    PASSWORD_DEFAULT
);

}

// حفظ البيانات
db_write('users', $users);

echo '

<!DOCTYPE html><html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تم بنجاح</title>
    <style>
        body {
            background: #0b0b0b;
            color: white;
            font-family: Arial;
            text-align: center;
            padding-top: 100px;
        }    h2 {
        color: #00c853;
    }

    p {
        color: #aaa;
    }
</style>

</head><body><h2>✓ تم إنشاء كلمة المرور بنجاح</h2>

<p>
    يمكنك الآن تسجيل الدخول إلى لوحة التحكم.
</p>

<p style="color:#ff4444;">
    مهم: احذف ملف setup-password.php الآن لحماية حسابك.
</p>

</body>
</html>
';
?>
