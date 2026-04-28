#!/bin/bash

cd /home/pardekha/pardekhan || exit

git pull origin main

composer install --no-dev --optimize-autoloader

php artisan migrate --force
php artisan optimize:clear
