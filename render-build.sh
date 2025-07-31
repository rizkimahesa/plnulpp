#!/usr/bin/env bash

set -o errexit

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Laravel setup
php artisan key:generate
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan storage:link
