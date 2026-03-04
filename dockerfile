# Etapa base con PHP 8.2 CLI
FROM php:8.2-cli

# Establece el directorio de trabajo
WORKDIR /var/www

# Instala dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git unzip zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        zip \
        intl \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia solo los archivos necesarios del proyecto (sin .env)
COPY . .

# Instala dependencias de Composer (sin dev, optimizado)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Genera APP_KEY si no existe (Render inyecta variables, pero por si acaso)
RUN php artisan key:generate --force || true

# Limpia cachés de configuración, rutas y vistas
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan optimize

# Expone el puerto que Render usa (Render asigna $PORT automáticamente)
EXPOSE 10000

# Comando de inicio: sirve la app en el puerto que Render asigna
CMD php artisan serve --host=0.0.0.0 --port=$PORT