FROM php:7.2-apache

RUN apt-get update
RUN apt-get install zip unzip
RUN apt-get install -y libfreetype6-dev zlib1g-dev libpng-dev libjpeg62-turbo-dev
RUN docker-php-ext-install pdo_mysql mysqli
RUN docker-php-ext-install exif
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# allow url rewrites so index.php is not required in urls
RUN a2enmod rewrite headers

# overwrite apache conf to change document root
COPY /infrastructure/dev/docker/apache /etc/apache2/sites-enabled

# Copy the application
COPY . /var/www/html

# change permissions on files so the api can function
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data /var/www/html/bootstrap
RUN chown -R www-data:www-data /var/www/html/storage

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R 1000:1000 .
RUN touch storage/logs/laravel.log
RUN chmod -R 775 database/ resources/ database/ config/ bootstrap/
RUN chmod -R 775 storage/

RUN composer install

EXPOSE 80 443
