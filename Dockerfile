FROM php:7.0.15-fpm-alpine

ARG DEBIAN_FRONTEND=noninteractive

EXPOSE 8080

RUN apk update && apk upgrade

RUN apk add bash

RUN apk add nginx && mkdir /run/nginx

RUN apk add postgresql-dev && docker-php-ext-install pdo_pgsql pgsql

COPY . /app

COPY Docker/docker-entrypoint.sh /app/docker-entrypoint.sh

COPY Docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY Docker/nginx/conf.d/server_header.conf /etc/nginx/conf.d/server_header.conf
COPY Docker/nginx/conf.d/makkelijkemarkt-api.conf /etc/nginx/conf.d/makkelijkemarkt-api.conf

COPY Docker/php/php.ini /usr/local/etc/php/php.ini
COPY Docker/php/conf.d/10-opcache.ini /usr/local/etc/php/conf.d/10-opcache.ini

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php && php composer.phar install --prefer-dist --no-scripts

CMD /app/docker-entrypoint.sh
