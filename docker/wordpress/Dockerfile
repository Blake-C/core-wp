FROM wordpress:6.9.4-php8.3-fpm-alpine
RUN apk upgrade --no-cache
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug-3.4.2 \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
