ARG PHP_VERSION=8.3

FROM php:${PHP_VERSION}-fpm-alpine

RUN apk --update --virtual build-deps add git \
    curl zip libzip-dev libmcrypt libmcrypt-dev openssh-client icu-dev \
    libxml2-dev make bash unzip file \
    && rm -rf /tmp/* \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY ./ /var/www/html/

WORKDIR /var/www/html

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install
CMD ["php-fpm"]

EXPOSE 9000
