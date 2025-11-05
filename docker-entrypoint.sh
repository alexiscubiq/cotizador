#!/bin/bash

# Exit on error
set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Starting Laravel development server..."
exec php artisan serve --host=0.0.0.0 --port=8080
