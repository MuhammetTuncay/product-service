# Use PHP 8.3 FPM
FROM php:8.3-fpm

# Install necessary packages and PHP extensions
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
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql sockets pcntl exif pdo_pgsql intl

# Install Redis PHP extension and enable it
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/backend

# Copy project files into the container
COPY ./source /var/www/backend

# Copy Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy and set up the entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Run Artisan commands (this will run every time the image is built, which is usually not desired)
RUN php artisan migrate
RUN php artisan db:seed
RUN php artisan cache:clear
RUN php artisan route:clear
RUN php artisan view:clear
RUN php artisan config:clear
RUN php artisan permission:cache-reset


# Expose necessary ports
EXPOSE 9000

# Define the entrypoint to run the custom script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
