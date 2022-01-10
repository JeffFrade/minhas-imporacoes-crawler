FROM composer:1 AS composer
FROM php:7.4-fpm

USER root
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get update \
    && apt-get install -y \
        autoconf \
        git \
    && useradd -m mic
RUN docker-php-ext-install \
        mysqli \
        pdo_mysql \
        pdo
WORKDIR /minhas-importacoes-crawler

RUN chown -R mic:mic /minhas-importacoes-crawler
USER mic
