# ONPLAY Complete Admin
## الروابط
- لوحة التحكم: `/admin/login.php`
- API: `/api/v1/home`
- الإعدادات: `/api/v1/app/config`

## أول دخول
لإنشاء كلمة مرور مضمونة، افتح مؤقتًا ملف `storage/data/users.json` وضع hash ناتج عن:
`password_hash('كلمة_مرور_قوية', PASSWORD_DEFAULT)`
أو استخدم سكربت PHP صغير لتوليدها، ثم احذف السكربت.

الحسابات: owner / manager / editor. في النسخة الحالية Owner وحده يستطيع إدارة حسابات الإدارة.

## Render
المشروع يحتوي Dockerfile و render.yaml. تنبيه: تخزين JSON على خدمة سحابية قد لا يكون دائمًا بدون Persistent Disk؛ لا تعتمد عليه كقاعدة إنتاجية دائمة.
