FROM php:5-apache
ENV REMOTE_XDEBUG_HOST 172.18.0.1
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng12-dev \
	&& docker-php-ext-install -j$(nproc) iconv mcrypt \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install mysql mysqli pdo_mysql
RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug
ADD ./phpdocker/php/php-ini-overrides.ini /usr/local/etc/php/conf.d/99-overrides.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_host=${REMOTE_XDEBUG_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini