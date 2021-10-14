#!/bin/sh
php artisan make:model Models/$1
chown -R 1000:1000 app/Models
echo "Model created."
