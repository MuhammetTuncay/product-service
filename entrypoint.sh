#!/bin/bash

echo "Starting entrypoint.sh"

# Eğer .env dosyası yoksa .env.example'dan kopyalama adımı
# if [ ! -f /var/www/backend/.env ]; then
#   echo ".env file does not exist. Creating from .env.example."
#   cp /var/www/backend/.env.example /var/www/backend/.env
# fi

# Composer install veya update işlemi
if [ -f /var/www/backend/composer.lock ]; then
  composer install --no-dev --optimize-autoloader
else
  composer update --no-dev --optimize-autoloader
fi

# Laravel komutları
if [ ! -L "/var/www/backend/public/storage" ]; then
  php artisan storage:link
fi
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan permission:cache-reset

# Gerekli dizinleri oluştur ve izinleri ayarla
mkdir -p /var/www/backend/storage /var/www/backend/bootstrap/cache
chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache /var/www/backend/vendor

# Supervisor log ve run dizinlerini oluştur
mkdir -p /var/log/supervisor
mkdir -p /var/run/supervisor

# Supervisor'u başlat
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
