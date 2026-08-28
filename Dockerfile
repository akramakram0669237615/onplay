FROM php:8.3-apache

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

EXPOSE 10000

CMD ["/bin/sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/:80/:${PORT:-10000}/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
