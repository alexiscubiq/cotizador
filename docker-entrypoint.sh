#!/bin/bash

# Exit on error
set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Seeding database with initial data..."
php artisan db:seed --force

echo "Starting Laravel development server..."
exec php artisan serve --host=0.0.0.0 --port=8080
