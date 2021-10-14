#!/bin/sh
php artisan make:controller $1 --resource
chown -R 1000:1000 app/Http/Controllers/$1.php
chmod -R g+w app/Http/Controllers/$1.php
echo "Controller created."
