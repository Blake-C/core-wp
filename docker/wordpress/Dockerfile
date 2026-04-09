FROM wordpress:6.9-php8.3
RUN pecl install xdebug-3.4.2 && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
