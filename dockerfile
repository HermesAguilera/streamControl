FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        bcmath \
        zip \
        intl \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Copia el .env local al contenedor (asegura que existe y tiene DB_CONNECTION=mysql)
COPY .env .env
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Limpia cachés de configuración y vistas para que tome el .env correcto
RUN php artisan optimize:clear

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000