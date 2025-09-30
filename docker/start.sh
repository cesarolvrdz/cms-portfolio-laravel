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

# Clear bootstrap cache that might contain old config
echo "Clearing bootstrap cache..."
rm -rf bootstrap/cache/*.php || true

# Clear any existing cache safely
echo "Clearing application caches..."
rm -rf bootstrap/cache/*.php || true
rm -rf storage/framework/cache/data/* || true
rm -rf storage/framework/views/* || true

# Force clear config cache before generating key
export APP_URL="http://localhost:8080"
php artisan config:clear --quiet || echo "Config clear failed, continuing..."

# Generate app key if not set
# Clear all cached configurations BEFORE checking APP_KEY
echo "Clearing all Laravel caches before APP_KEY setup..."
php artisan config:clear --quiet || true
php artisan cache:clear --quiet || true
php artisan view:clear --quiet || true
rm -rf bootstrap/cache/*.php || true
rm -rf storage/framework/cache/data/* || true
rm -rf storage/framework/views/* || true

echo "Checking APP_KEY..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    # Generate a random key manually since artisan might not work
    APP_KEY="base64:$(openssl rand -base64 32)"
    echo "Generated key: $APP_KEY"
    # Update .env file
    sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" .env
    echo "APP_KEY set in .env file"
else
    echo "APP_KEY already exists in .env"
fi

# Verify the key was set
echo "Current APP_KEY: $(grep APP_KEY .env)"

# Test Laravel configuration loading with the new APP_KEY
echo "Testing Laravel configuration with APP_KEY..."
php artisan env 2>/dev/null | head -5 || echo "Environment command failed"

# Check if Laravel can read the APP_KEY from configuration
echo "Verifying Laravel APP_KEY configuration..."
if php -r "
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$config = \$app->make('config');
echo 'APP_KEY from Laravel config: ' . (\$config->get('app.key') ? 'PRESENT' : 'MISSING') . PHP_EOL;
" 2>/dev/null | grep -q "PRESENT"; then
    echo "✓ Laravel can read APP_KEY from configuration"
else
    echo "⚠ Laravel cannot read APP_KEY, clearing all caches..."
    php artisan config:clear --quiet || true
    php artisan cache:clear --quiet || true
    rm -rf bootstrap/cache/*.php || true
    sleep 2
fi

# Stop any existing PHP-FPM processes to avoid port conflicts
echo "Cleaning up any existing PHP-FPM processes..."
pkill -f php-fpm || true
sleep 2

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
echo "Caching application configuration..."
# Force clear any cached config first to ensure fresh config
php artisan config:clear --quiet || true
# Then cache with the new APP_KEY
php artisan config:cache --quiet || echo "Config cache failed, continuing..."
# Skip route cache to avoid URI issues during startup
echo "Skipping route cache to avoid startup issues..."

# Skip view caching during startup to avoid issues
echo "Skipping view cache during startup..."

# Final APP_KEY verification before starting services
echo "=== FINAL APP_KEY VERIFICATION ==="
echo "Environment file APP_KEY:"
grep "^APP_KEY=" .env || echo "No APP_KEY found in .env file!"

echo "Laravel configuration APP_KEY test:"
php artisan tinker --execute="
echo 'Config APP_KEY: ' . (config('app.key') ? 'SET (' . substr(config('app.key'), 0, 15) . '...)' : 'NOT SET') . PHP_EOL;
echo 'App canDecrypt test: ' . (app('encrypter') ? 'WORKING' : 'FAILED') . PHP_EOL;
" 2>/dev/null || echo "Laravel APP_KEY verification failed"

# Ensure .env file has proper permissions
chmod 644 .env
echo "=== END VERIFICATION ==="

# Optimize autoloader
echo "Optimizing autoloader..."
composer dump-autoload --optimize --no-dev

# Ensure log directory exists and is writable
mkdir -p /var/log/nginx /var/log/supervisor
touch /var/log/nginx/access.log /var/log/nginx/error.log
chown -R www-data:www-data /var/log/nginx

# Don't start PHP-FPM here since supervisor will handle it
echo "PHP-FPM will be managed by supervisor..."

# Show final .env for debugging
echo "Final .env contents:"
cat .env

# Start supervisor to manage nginx
echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf -n
