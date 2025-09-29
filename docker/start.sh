#!/bin/sh

# Generate Laravel app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating app key..."
    php artisan key:generate --show --no-ansi -q
fi

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create SQLite database if it doesn't exist
if [ ! -f "/var/www/database/database.sqlite" ]; then
    echo "Creating SQLite database..."
    touch /var/www/database/database.sqlite
    chmod 664 /var/www/database/database.sqlite
fi

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
