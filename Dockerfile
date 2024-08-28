FROM php:8.3-fpm

# Gerekli paketleri yükle
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    cron \
    sudo \
    iproute2 \
    iputils-ping \
    procps \
    bash \
    libpq-dev \
    nano \
    vim \
    libicu-dev \
    redis-server \
    libhiredis-dev \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP uzantılarını kur
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql sockets pcntl exif pdo_pgsql intl

# Redis uzantısını yükle
RUN pecl install redis && docker-php-ext-enable redis

# Composer'ı kur
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/backend

# Proje dosyalarını kopyala
COPY ./source /var/www/backend

# Supervisor yapılandırma dosyasını kopyala
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Entrypoint script ekle
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Entrypoint scripti çalıştır
ENTRYPOINT ["entrypoint.sh"]