FROM php:8.2-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Habilita o mod_rewrite
RUN a2enmod rewrite

# Permite .htaccess no diretório público
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY ./public /var/www/html
COPY ./app /var/www/app
COPY ./config /var/www/config

EXPOSE 80
