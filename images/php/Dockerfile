FROM php:7.3-fpm
RUN apt-get update \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini
WORKDIR /var/www
CMD ["php-fpm"]
