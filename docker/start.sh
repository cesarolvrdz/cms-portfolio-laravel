#!/bin/bash

# Start script for Railway deployment
set -e

echo "Starting L# Test Laravel configuration with the new APP_KEY
echo "Testing Laravel configuration with APP_KEY..."
php artisan env 2>/dev/null | head -5 || echo "Environment command failed"

# Check if Laravel can read the APP_KEY from configuration
echo "Verifying Laravel APP_KEY configuration..."
# Use a more direct test that doesn't trigger cache operations
if php -r 'require_once "vendor/autoload.php"; $dotenv = Dotenv\Dotenv::createImmutable(__DIR__); $dotenv->load(); echo "Direct ENV APP_KEY: " . ($_ENV["APP_KEY"] ?? "MISSING") . PHP_EOL;' 2>/dev/null | grep -q "base64:"; then
    echo "✓ APP_KEY is accessible from environment"
else
    echo "⚠ APP_KEY not found in environment, clearing all caches..."
    rm -rf bootstrap/cache/*.php || true
    rm -rf storage/framework/cache/data/* || true
    sleep 2
finitialization..."

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
# Set up basic directories first
echo "Setting up initial directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

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

# CRITICAL: Change cache driver to file to avoid SQLite dependency during startup
echo "Setting cache driver to file to avoid database dependency..."
sed -i "s|CACHE_STORE=.*|CACHE_STORE=file|g" .env

# FORCE clear any existing cached configuration that might reference database cache
echo "Force clearing any cached configuration..."
rm -rf bootstrap/cache/config.php || true
rm -rf bootstrap/cache/services.php || true
rm -rf storage/framework/cache/data/* || true

# Create storage directories and database FIRST
echo "Creating storage directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions early
echo "Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs

# Create SQLite database before any cache operations
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    echo "Setting up SQLite database..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Clear all cached configurations AFTER database exists
echo "Clearing all Laravel caches after database setup..."
php artisan config:clear --quiet || true
php artisan cache:clear --quiet || true
php artisan view:clear --quiet || true
rm -rf bootstrap/cache/*.php || true

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

echo "Direct environment variable check:"
php -r 'require_once "vendor/autoload.php"; $dotenv = Dotenv\Dotenv::createImmutable(__DIR__); $dotenv->load(); echo "APP_KEY: " . ($_ENV["APP_KEY"] ? "SET (" . substr($_ENV["APP_KEY"], 0, 15) . "...)" : "NOT SET") . PHP_EOL; echo "CACHE_STORE: " . ($_ENV["CACHE_STORE"] ?? "default") . PHP_EOL;' 2>/dev/null || echo "Environment verification failed"

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
