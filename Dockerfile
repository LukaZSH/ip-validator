FROM composer:2 as builder

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-interaction

FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    tzdata \
    git \
    unzip \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN a2ensite 000-default.conf

WORKDIR /var/www/html

COPY --from=builder /app/vendor/ /var/www/html/vendor/

COPY . .

RUN chown -R www-data:www-data /var/www/html
