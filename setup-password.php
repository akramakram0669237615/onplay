<?php
// احذف هذا الملف بعد الاستخدام!
if(!isset($_GET['password']))exit('أضف ?password=NemoWeb@2026');
require __DIR__.'/src/bootstrap.php';
$u=db_read('users');$u[0]['password']=password_hash((string)$_GET['password'],PASSWORD_DEFAULT);db_write('users',$u);
echo 'تم إنشاء كلمة المرور. احذف setup-password.php الآن.';
