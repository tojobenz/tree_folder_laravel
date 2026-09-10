#!/bin/bash

# Set working directory
cd /var/www/html

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Start PHP-FPM in the background
php-fpm -D

# Start nginx in the foreground
nginx -g 'daemon off;'