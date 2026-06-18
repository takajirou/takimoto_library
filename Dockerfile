FROM php:8.4-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache git unzip zip \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]