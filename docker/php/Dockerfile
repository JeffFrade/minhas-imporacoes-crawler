FROM composer:1 AS composer
FROM php:7.4-fpm

USER root
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get update \
    && apt-get install -y \
        autoconf \
        git \
        zip \
        unzip \
        zlib1g-dev \
        libssl-dev \
        libpng-dev \
        libzip-dev \
    && useradd -m mic
RUN docker-php-ext-install \
        gd \
        mysqli \
        pdo_mysql \
        pdo \
        zip
WORKDIR /minhas-importacoes-crawler

RUN chown -R mic:mic /minhas-importacoes-crawler
USER mic
