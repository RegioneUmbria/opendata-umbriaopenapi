FROM php:7.0-apache


COPY docker/php.ini /usr/local/etc/php/
COPY ./ /var/www/html/

WORKDIR /var/www/html/

COPY ./docker/umbriaopenapi_vh.conf /etc/apache2/sites-available/
RUN a2ensite umbriaopenapi_vh.conf

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
		libzip-dev \
        git	\
		nano

RUN docker-php-ext-install zip pdo_mysql
RUN pecl install xdebug-3.0.0
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN cd /var/www/html
RUN chmod +x install.sh

