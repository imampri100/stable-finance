# Use PHP 8.0 as the base image
FROM php:8.0-apache

WORKDIR /var/www/html
COPY . /var/www/html

RUN apt-get update && apt-get install -y inotify-tools && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mysqli
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]