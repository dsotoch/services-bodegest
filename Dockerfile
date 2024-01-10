FROM php:8.2-fpm

WORKDIR /bodegest-service

COPY . /bodegest-service/

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install  --no-interaction
EXPOSE 80

CMD ["php-fpm"]



