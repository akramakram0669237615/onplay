FROM php:8.3-apache

# تفعيل Apache Rewrite
RUN a2enmod rewrite

# مجلد المشروع
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . /var/www/html

# إعطاء صلاحيات لمجلد storage
RUN mkdir -p /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Render يستخدم متغير PORT
EXPOSE 10000

CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:80>/<VirtualHost *:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
