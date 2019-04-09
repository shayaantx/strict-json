FROM php:7.2

RUN apt-get update && apt-get install git zip unzip -y
RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /app

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install

COPY . .
CMD ["vendor/bin/phpunit", "test"]
