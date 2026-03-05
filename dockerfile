# Etapa base con PHP 8.2 CLI
FROM php:8.2-cli

# Establece el directorio de trabajo
WORKDIR /var/www

# Instala dependencias del sistema y extensiones PHP necesarias (Añadido libpq-dev para Postgres)
RUN apt-get update && apt-get install -y \
    git unzip zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        bcmath \
        zip \
        intl \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia los archivos del proyecto
COPY . .

# Instala dependencias (Sin ejecutar scripts de artisan todavía)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Damos permisos a las carpetas de almacenamiento (Vital para que no de error 500)
RUN chmod -R 775 storage bootstrap/cache && chown -R www-data:www-data /var/www

# Expone el puerto
EXPOSE 10000

# COMANDO DE INICIO: Aquí es donde ocurre la magia.
# Ejecutamos las migraciones y la limpieza cuando la DB ya está conectada.
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}