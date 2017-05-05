FROM php:5-apache
ENV REMOTE_XDEBUG_HOST 172.18.0.1
RUN docker-php-ext-install mysql mysqli pdo_mysql
RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug
ADD ./phpdocker/php/php-ini-overrides.ini /usr/local/etc/php/conf.d/99-overrides.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_host=${REMOTE_XDEBUG_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini