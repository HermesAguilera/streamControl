## Etapa 1: builder front-end (Node)
FROM node:18-alpine AS node_builder
WORKDIR /app
ENV NODE_ENV=development
COPY package*.json vite.config.js ./
# Use npm install with legacy-peer-deps to avoid peer-deps resolution failures
RUN npm install --legacy-peer-deps --silent
COPY resources ./resources
RUN npm run build
ENV NODE_ENV=production

## Etapa final: PHP
FROM php:8.2-cli
WORKDIR /var/www

## Dependencias del sistema (no interactivas, paquetes compatibles)
ENV DEBIAN_FRONTEND=noninteractive

# Actualizar índices y comprobar disponibilidad de paquetes antes de instalar
RUN apt-get update && apt-get -y upgrade || true
RUN apt-cache policy libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libpq-dev zlib1g-dev || true

# Instalar herramientas básicas primero (ayuda a aislar errores)
RUN apt-get install -y --no-install-recommends \
        ca-certificates gnupg curl \
        git unzip zip netcat procps \
    && rm -rf /var/lib/apt/lists/*

# Instalar librerías de desarrollo por separado
RUN apt-get update && apt-get install -y --no-install-recommends \
        build-essential libzip-dev zlib1g-dev \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libicu-dev libpq-dev \
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