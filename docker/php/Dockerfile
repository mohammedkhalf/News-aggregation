FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev

RUN docker-php-ext-install \
    intl \
    zip \
    opcache \
    pdo \
    pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist

RUN chown -R www-data:www-data var

USER www-data
