# Etapa 1: builder con Composer
FROM composer:2.7 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# ⬇️ agrega los --ignore-platform-req
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts \
    --ignore-platform-req=ext-bcmath --ignore-platform-req=ext-gd
COPY . .
RUN composer dump-autoload --optimize --no-scripts

# Etapa 2: runtime PHP-FPM
FROM php:8.3-fpm-alpine
WORKDIR /var/www/html

# Extensiones necesarias para Laravel
RUN apk add --no-cache icu-dev oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql intl gd zip exif bcmath opcache

# Opcional: optimizaciones de opcache
COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copiamos código (sin vendor aún)
COPY . .

# Copiamos vendor desde el builder
COPY --from=vendor /app/vendor ./vendor

# Permisos para storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm", "-F"]
