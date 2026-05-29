FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install mysqli pdo_mysql zip

RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/