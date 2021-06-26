FROM php:7.4-fpm

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update --fix-missing --no-install-recommends
RUN apt-get -y upgrade

RUN apt-get -y install --fix-missing apt-utils build-essential git curl libcurl4 zip libzip4 libzip-dev openssl libonig-dev

RUN pecl install xdebug \
        && docker-php-ext-enable xdebug \
        && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.0.14
