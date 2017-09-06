FROM php:7.0-apache


COPY docker/php.ini /usr/local/etc/php/
COPY ./ /var/www/html/

WORKDIR /var/www/html/

COPY ./docker/umbriaopenapi_vh.conf /etc/apache2/sites-available/
RUN a2ensite umbriaopenapi_vh.conf

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        git

RUN docker-php-ext-install zip pdo_mysql
RUN pecl install xdebug
RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

RUN cd /var/www/html
RUN chmod +x install.sh

