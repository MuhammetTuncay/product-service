FROM nginx:1.22

RUN apt update -y
RUN apt-get install -y software-properties-common wget gnupg2 git cron
RUN curl https://packages.sury.org/php/apt.gpg | apt-key add -
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |  tee /etc/apt/sources.list.d/php.list
RUN apt-get update -y
RUN apt-get install -y php8.2-fpm php8.2-dom  php8.2-zip php8.2-bcmath php8.2-mongodb php8.2-curl php8.2-intl php8.2-redis php8.2-mysql php8.2-soap supervisor -y

#COPY newrelic-php5-10.14.0.3-linux.tar.gz .
#RUN tar -xzvf newrelic-php5-10.14.0.3-linux.tar.gz
#RUN cd  newrelic-php5-10.14.0.3-linux && echo 1 | ./newrelic-install
#RUN rm -rf /etc/php/8.2/fpm/conf.d/newrelic.ini

RUN rm -rf /etc/nginc/nginx.conf
ADD nginx/nginx.conf /etc/nginx/

#COPY cron-container /etc/cron.d/cron-container
#RUN chmod 0644 /etc/cron.d/cron-container
#RUN crontab /etc/cron.d/cron-container

COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d
RUN sed -i 's/memory_limit = 128M/memory_limit = -1/g' /etc/php/8.2/fpm/php.ini
WORKDIR /var/www/html/
COPY --chown=www-data:www-data  . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --working-dir /var/www/html/src/

CMD php /var/www/html/src/artisan cache:clear && php /var/www/html/src/artisan config:cache && service php8.2-fpm start && service cron start && supervisord && nginx -g 'daemon off;'
