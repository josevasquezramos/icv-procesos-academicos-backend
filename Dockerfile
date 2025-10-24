FROM php:8.2-fpm-alpine

# 1. Instala solo las dependencias de *runtime* (las que la app necesita para correr)
# Se añade --update para refrescar los repositorios de paquetes
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

# 2. Instala dependencias de *compilación* (--virtual), compila las extensiones y borra las dependencias
RUN apk add --no-cache --update --virtual .build-deps \
    build-base \
    autoconf \
    postgresql-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    imagemagick-dev  # <--- ¡ESTA ES LA LÍNEA CLAVE QUE FALTABA!
  
  # 3. Configura e instala extensiones PHP (incluyendo imagick)
  && docker-php-ext-configure zip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) pdo pdo_pgsql zip gd \
  && pecl install imagick \
  && docker-php-ext-enable imagick \

  # 4. Limpia todo (borra el paquete virtual .build-deps)
  && apk del .build-deps

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /app

COPY . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data: /app

CMD ["sh", "/app/docker/startup.sh"]
