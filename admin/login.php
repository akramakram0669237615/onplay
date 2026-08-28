<?php require __DIR__.'/../src/bootstrap.php';
if(is_admin()){header('Location:index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['username']??'');$pass=$_POST['password']??'';
 foreach(db_read('users') as $u)if(($u['username']??'')===$name&&password_verify($pass,$u['password']??'')){$_SESSION['user']=$u['id'];header('Location:index.php');exit;}
 $error='بيانات الدخول غير صحيحة';
}
?><!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/admin.css"><title>ONPLAY Login</title></head><body class="login"><form method="post" class="login-box"><div class="brand">▶ ONPLAY</div><h2>تسجيل الدخول</h2><?php if($error):?><p class="alert"><?=$error?></p><?php endif?><label>اسم المستخدم</label><input name="username" required><label>كلمة المرور</label><input type="password" name="password" required><button>دخول إلى لوحة التحكم</button><small>غيّر حساب المدير الافتراضي بعد أول دخول.</small></form></body></html>