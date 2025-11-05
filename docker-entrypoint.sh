#!/bin/bash

# Exit on error
set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Seeding database with initial data..."
php artisan db:seed --force

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting nginx..."
exec nginx -g 'daemon off;'
