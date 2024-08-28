#!/bin/bash

echo "Starting entrypoint.sh"

# Ensure .env file exists
if [ ! -f /var/www/backend/.env ]; then
  echo ".env file does not exist. Creating from .env.example."
  cp /var/www/backend/.env.example /var/www/backend/.env
fi

# Composer install or update
if [ -f /var/www/backend/composer.lock ]; then
  composer install --no-dev --optimize-autoloader
else
  composer update --no-dev --optimize-autoloader
fi

# Run Laravel commands
if [ ! -L "/var/www/backend/public/storage" ]; then
  php artisan storage:link
fi

# Run migrations, seeds, and cache clears
php artisan migrate --force
php artisan db:seed --force
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan permission:cache-reset

# Create necessary directories and set permissions
mkdir -p /var/www/backend/storage /var/www/backend/bootstrap/cache
chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache /var/www/backend/vendor

# Create Supervisor log and run directories
mkdir -p /var/log/supervisor
mkdir -p /var/run/supervisor

# Start Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
