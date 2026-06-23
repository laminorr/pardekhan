#!/bin/bash

cd /home/pardekha/pardekhan || exit

git pull origin main

composer install --no-dev --optimize-autoloader

php artisan migrate --force

# پاک‌سازی کش‌ها (همیشه امن)
php artisan optimize:clear

# کش مجدد (اگر خطا داد، deploy را متوقف نمی‌کند)
php artisan config:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# راه‌اندازی مجدد queue (در صورت استفاده)
php artisan queue:restart 2>/dev/null || true

echo "Deploy completed at $(date)"
