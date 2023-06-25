# Pull php-apache image 
FROM php:8.1-apache

# use root user
USER root 

# Install the dependecies to run php
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip
 

# Set working directory to the apache public directory 
WORKDIR /var/www/


# Create cache directory and change ownership
RUN mkdir -p var/cache/dev && chown -R www-data:www-data var/cache/dev
RUN mkdir -p var/log && chown -R www-data:www-data var/log

# Copy all files into the apache public directory
COPY . .

# Copy the apache configuration file into apache container
COPY apache.conf  /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# run the script
ENTRYPOINT bash -c "\
    php bin/console cache:clear --env=prod && \
    php bin/console doctrine:schema:update --force --complete && \
    apache2-foreground \
    "

