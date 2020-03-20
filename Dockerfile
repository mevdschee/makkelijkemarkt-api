FROM php:7.0.15-fpm-alpine

ARG DEBIAN_FRONTEND=noninteractive

EXPOSE 8080

RUN apk update && apk upgrade

RUN apk add bash

RUN apk add nginx && mkdir /run/nginx

COPY . /app

COPY Docker/docker-entrypoint.sh /app/docker-entrypoint.sh

RUN chown -R www-data:www-data /app/app/cache \
    && chmod 770 /app/app/cache \
    && chown -R www-data:www-data /app/app/logs \
    && chmod 770 /app/app/logs \
    && chown -R www-data:www-data /app/web/media \
    && chmod 770 /app/web/media \
    && chmod 777 /app/docker-entrypoint.sh

COPY Docker/docker.nginx /etc/nginx/conf.d/dashboard.conf

COPY Docker/php.ini /usr/local/etc/php/php.ini

COPY Docker/10-opcache.ini /usr/local/etc/php/conf.d/10-opcache.ini

RUN sed -i '/\;listen\.mode\ \=\ 0660/c\listen\.mode=0666' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i '/pm.max_children = 5/c\pm.max_children = 20' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i '/\;pm\.max_requests\ \=\ 500/c\pm\.max_requests\ \=\ 100' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i '/\;security\.limit_extensions \= \.php \.php3 \.php4 \.php5 \.php7/c\security\.limit_extensions \= \.php' /usr/local/etc/php-fpm.d/www.conf \
  && sed -e 's/;clear_env = no/clear_env = no/' -i /usr/local/etc/php-fpm.d/www.conf

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php \
	&& php composer.phar install --prefer-dist --no-scripts

CMD /app/docker-entrypoint.sh
