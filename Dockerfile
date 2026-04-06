FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    bcmath \
    gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Carpeta de trabajo
WORKDIR /var/www

# Copiar proyecto
COPY . .


# Instalar Laravel
# Instalar Laravel
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Generar autoload manualmente después
RUN composer dump-autoload --optimize

# Permisos
RUN chmod -R 777 storage bootstrap/cache

# Puerto
EXPOSE 10000

# Evitar que artisan falle por falta de APP_KEY durante el build
ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
ENV APP_ENV=production

# Ejecutar servidor
CMD php artisan serve --host=0.0.0.0 --port=10000