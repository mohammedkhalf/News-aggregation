FROM php:8.3-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    bash \
    git \
    unzip \
    autoconf \
    gcc \
    g++ \
    make \
    libc-dev

# Install PHP extensions
RUN docker-php-ext-install \
    intl \
    zip \
    pdo_pgsql \
    opcache

# Install Redis PHP extension (version 4.3.0 or higher required)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
