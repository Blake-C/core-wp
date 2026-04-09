FROM wordpress:6.9.4-php8.3-fpm-alpine
RUN apk upgrade --no-cache && apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug-3.4.2 \
    && docker-php-ext-enable xdebug \
    && apk del $PHPIZE_DEPS
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY php-opcache-dev.ini /usr/local/etc/php/conf.d/php-opcache.ini
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
