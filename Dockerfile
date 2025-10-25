FROM php:8.2-fpm-alpine

# 1. Instala solo las dependencias de *runtime* (las que la app necesita para correr)
RUN apk add --no-cache --update \
    nginx \
    git \
    unzip \
    zip \
    postgresql-libs \
    freetype \
    libjpeg-turbo \
    libpng \
    imagemagick

# 2. Instala dependencias de compilación, compila extensiones y limpia (TODO EN UN SOLO RUN)
RUN apk add --no-cache --update --virtual .build-deps \
    build-base \
    autoconf \
    postgresql-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    imagemagick-dev \
  # Continúa el MISMO comando RUN
  && docker-php-ext-configure zip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) pdo pdo_pgsql zip gd \
  && pecl install imagick \
  && docker-php-ext-enable imagick \
  # Limpia las dependencias de compilación al final del MISMO RUN
  && apk del .build-deps

# --- El resto de tu archivo (está perfecto) ---

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /app

COPY . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data: /app

CMD ["sh", "/app/docker/startup.sh"]
