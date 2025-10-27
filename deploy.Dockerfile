FROM php:8.0-fpm

WORKDIR /var/www/html
COPY . /var/www/html

RUN docker-php-ext-install pdo_mysql mysqli

EXPOSE 9000
CMD ["php-fpm"]