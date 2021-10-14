#!/bin/sh
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:clear

