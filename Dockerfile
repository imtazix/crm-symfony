FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev zip unzip git curl libonig-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

ENV APP_ENV=dev
