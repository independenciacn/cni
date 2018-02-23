FROM former03/dev03-apache-php-5.2-squeeze
ENV REMOTE_XDEBUG_HOST 172.18.0.1
RUN echo "deb http://archive.debian.org/debian squeeze main" > /etc/apt/sources.list
RUN echo "deb http://archive.debian.org/debian squeeze-lts main" >> /etc/apt/sources.list
RUN echo "Acquire::Check-Valid-Until false;" >> /etc/apt/apt.conf
RUN apt-get update && apt-get install -y autoconf
RUN apt-get remove -y mysql-server
RUN pecl install xdebug-2.2.7
ADD ./phpdocker/php/php-ini-overrides.ini /etc/php5/conf.d/99-overrides.ini
RUN echo "xdebug.remote_enable=1" >> /etc/php5/conf.d/xdebug.ini
RUN echo "xdebug.remote_host=${REMOTE_XDEBUG_HOST}" >> /etc/php5/conf.d/xdebug.ini