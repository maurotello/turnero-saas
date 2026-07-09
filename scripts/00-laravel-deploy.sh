#!/usr/bin/env bash
echo "Instalando dependencias de Composer..."
composer install --no-dev --working-dir=/var/www/html --optimize-autoloader

echo "Cacheando configuración y rutas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Corriendo migraciones..."
php artisan migrate --force
php artisan storage:link