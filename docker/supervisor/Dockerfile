FROM phpdockerio/php:8.2-fpm

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        git \
        php8.2-amqp \
        php8.2-gd \
        php8.2-intl \
        php8.2-mcrypt \
        php8.2-mongodb \
        php8.2-mysql \
        php8.2-redis \
        php8.2-uuid \
        php8.2-xdebug \
        php8.2-bcmath \
        php8.2-yaml \
        supervisor; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN mkdir -p "/etc/supervisor/logs"
COPY supervisord.conf /etc/supervisor/
CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]
