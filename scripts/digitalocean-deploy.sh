#!/bin/bash
set -e

echo "Starting DigitalOcean deployment..."

# Instalar dependencias de Composer
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Crear directorios necesarios
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Establecer permisos
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Optimizar para producción
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones y seeders
echo "Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force

echo "DigitalOcean deployment completed successfully!"