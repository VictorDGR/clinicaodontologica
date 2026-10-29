#!/bin/sh
set -e

cd /var/www/html

# Render entrega la URL pública en RENDER_EXTERNAL_URL
export APP_URL="${APP_URL:-$RENDER_EXTERNAL_URL}"

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crea o actualiza las tablas; el seeder solo carga datos la primera vez
php artisan migrate --force
php artisan db:seed --force

chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
