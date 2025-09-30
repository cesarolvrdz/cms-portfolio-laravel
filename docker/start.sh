#!/bin/bash

# Start script for Railway deployment
set -e

echo "Starting Laravel application initialization..."

# Set proper working directory
cd /var/www

# Check if .env exists, if not create from Railway example
if [ ! -f .env ]; then
    echo "Creating .env from Railway example..."
    if [ -f .env.railway.example ]; then
        cp .env.railway.example .env
    else
        cp .env.example .env
    fi
fi

# Fix APP_URL for Railway - use Railway's provided URL or fallback
if [ ! -z "$RAILWAY_STATIC_URL" ]; then
    export APP_URL="https://$RAILWAY_STATIC_URL"
    echo "Setting APP_URL to: $APP_URL"
    # Update .env file
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
elif [ ! -z "$RAILWAY_PUBLIC_DOMAIN" ]; then
    export APP_URL="https://$RAILWAY_PUBLIC_DOMAIN"
    echo "Setting APP_URL to: $APP_URL"
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
else
    export APP_URL="http://localhost:8080"
    echo "Using fallback APP_URL: $APP_URL"
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env
fi

# Clear any existing cache safely
echo "Clearing caches..."
rm -rf bootstrap/cache/*.php || true
rm -rf storage/framework/cache/data/* || true
rm -rf storage/framework/views/* || true
php artisan config:clear --quiet || echo "Config clear failed, continuing..."

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    # Set minimal config first
    export APP_URL="http://localhost:8080"
    php artisan key:generate --force --quiet
fi

# Ensure we have proper configuration
echo "Setting production environment..."
sed -i "s|APP_ENV=.*|APP_ENV=production|g" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" .env
sed -i "s|LOG_CHANNEL=.*|LOG_CHANNEL=stderr|g" .env

# Create storage directories
echo "Creating storage directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create database if using SQLite
if grep -q "DB_CONNECTION=sqlite" .env; then
    echo "Setting up SQLite database..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Run migrations if database is accessible
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, database may not be ready"

# Cache configuration and routes for better performance
echo "Caching application..."
php artisan config:cache --quiet || echo "Config cache failed, continuing..."
# Skip route cache to avoid URI issues during startup
echo "Skipping route cache to avoid startup issues..."

# Skip view caching during startup to avoid issues
echo "Skipping view cache during startup..."

# Optimize autoloader
echo "Optimizing autoloader..."
composer dump-autoload --optimize --no-dev

# Ensure log directory exists and is writable
mkdir -p /var/log/nginx /var/log/supervisor
touch /var/log/nginx/access.log /var/log/nginx/error.log
chown -R www-data:www-data /var/log/nginx

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm --daemonize

# Wait a moment for PHP-FPM to start
sleep 2

# Test that PHP-FPM is running
if ! pgrep php-fpm > /dev/null; then
    echo "ERROR: PHP-FPM failed to start"
    exit 1
fi

# Start supervisor to manage nginx
echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf -n
