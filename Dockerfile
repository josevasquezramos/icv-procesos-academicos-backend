FROM php:8.2-fpm-alpine

RUN apk add --no-cache --update nginx wget postgresql-dev git unzip libzip-dev zip \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    imagemagick build-base autoconf

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /app

COPY . /app

RUN docker-php-ext-install pdo pdo_pgsql \
 && docker-php-ext-configure zip \
 && docker-php-ext-install zip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd \
 && pecl install imagick \
 && docker-php-ext-enable imagick

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data: /app

CMD ["sh", "/app/docker/startup.sh"]
