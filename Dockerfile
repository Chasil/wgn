FROM php:7.0-apache
WORKDIR /var/www/html

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install pdo_mysql
RUN pecl install apc && docker-php-ext-enable apc

RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www/html