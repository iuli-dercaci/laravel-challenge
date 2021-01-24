FROM php:7.4-fpm-alpine

RUN apk update
RUN apk add libmcrypt-dev openssl
RUN docker-php-ext-install pdo

COPY . /var/app
WORKDIR /var/app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install
CMD php artisan serve --host=0.0.0.0 --port=8000


EXPOSE 8000

# CMD tail -f /dev/null
