FROM php:7.2-fpm-alpine

RUN apk --update --no-cache add git

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions pdo_mysql intl gd zip

WORKDIR /var/www/html

CMD php-fpm

EXPOSE 9000