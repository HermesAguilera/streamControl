## Etapa 1: builder front-end (Node)
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package*.json vite.config.js ./
# Use npm install when package-lock.json is not present (safer in CI without lockfile)
RUN npm install --silent
COPY resources ./resources
RUN npm run build

## Etapa final: PHP
FROM php:8.2-cli
WORKDIR /var/www

## Dependencias del sistema (no interactivas, paquetes compatibles)
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates apt-transport-https gnupg curl build-essential \
        git unzip zip \
        libzip-dev zlib1g-dev \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libicu-dev libpq-dev netcat procps \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql pgsql bcmath zip intl gd \
    && apt-get purge -y --auto-remove build-essential \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*
ENV DEBIAN_FRONTEND=dialog

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código de la aplicación
COPY . .

# Copiar assets generados por el builder (Vite -> public/build)
COPY --from=node_builder /app/public/build ./public/build

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Publicar assets de Filament (no necesita la DB)
RUN php artisan filament:assets --no-interaction || true

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache public && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# Copiar script que espera la DB y arranca la app
COPY docker/wait-and-run.sh /usr/local/bin/wait-and-run.sh
RUN chmod +x /usr/local/bin/wait-and-run.sh

CMD ["/usr/local/bin/wait-and-run.sh"]