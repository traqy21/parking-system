#!/bin/sh
touch storage/logs/laravel.log
chown -R 1000:1000 .
chmod -R 775 database/ resources/ database/ config/ bootstrap/
chmod -R 777 storage/
composer update
composer dump-autoload
php artisan cache:clear
php artisan config:cache
php artisan view:clear
php artisan config:cache
php artisan route:clear
php artisan migrate
php artisan db:seed
