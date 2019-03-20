FROM php:7.2-fpm

RUN apt-get update && apt-get install -yq git zip unzip libpng-dev libpq-dev && \
    echo "date.timezone = Europe/Warsaw" > /usr/local/etc/php/php.ini && \
    rm -rf /var/lib/apt/lists/* && \
    php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm -rf /tmp/composer-setup.php

WORKDIR /var/www/html/persei-api
ADD install.sh install.sh
RUN chmod +x install.sh

