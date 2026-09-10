#!/bin/bash

# Set working directory
cd /var/www/html

# Remove existing .env if it exists to avoid conflicts
rm -f .env

# Create .env file from environment variables
cat > .env <<EOF
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_KEY=${APP_KEY}
APP_URL=${APP_URL:-http://localhost}

DB_CONNECTION=sqlite

CACHE_DRIVER=${CACHE_DRIVER:-file}
SESSION_DRIVER=${SESSION_DRIVER:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

ADMINEMAIL=${ADMINEMAIL:-admin@example.com}
SOCIETENAME="${SOCIETENAME:-Ma Société}"

JWT_SECRET=${JWT_SECRET}
EOF

# Create SQLite database if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Clear config cache
php artisan config:clear

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Start PHP-FPM in the background
php-fpm -D

# Start nginx in the foreground
nginx -g 'daemon off;'