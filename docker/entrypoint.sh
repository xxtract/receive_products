#!/bin/bash

# Wait for database to be ready (if needed)
if [ ! -z "$DB_HOST" ]; then
    until nc -z -v -w30 $DB_HOST $DB_PORT
    do
        echo "Waiting for database connection..."
        sleep 5
    done
fi

# Generate application key if not set
php artisan key:generate --no-interaction --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start Apache in foreground
exec "$@"
