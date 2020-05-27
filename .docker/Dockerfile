FROM php:7.3-fpm-alpine

# Dépendences nécessaires pour les extensions PHP, plus Wget/Git/SSH
RUN apt-get update && apt-get install --no-install-recommends -yq ${BUILD_PACKAGES} \
    build-essential \
    wget \
    ssh \
    git \
    libmcrypt-dev \
    libicu-dev \
    libzip-dev \
    && apt-get clean

# Quelques extensions PHP recommmandées
ENV PHP_EXTENSIONS opcache pdo_mysql pcntl intl zip
RUN docker-php-ext-install ${PHP_EXTENSIONS}

# Installation de composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

# Activation d'Apache mod_rewrite
RUN a2enmod rewrite

# On configure un vhost, pour ne pas avoir de public/ ou web/ dans l'URL
COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf