#!/bin/sh
php artisan make:seeder $1
chown -R 1000:1000 database/seeds/$1.php
chmod -R g+w database/seeds/$1.php
echo "Seeder created."
