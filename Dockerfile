FROM dunglas/frankenphp:php8.2.31-bookworm

RUN apt-get update && apt-get install -y \
    ca-certificates git unzip zip libzip-dev \
    default-mysql-client \
    nodejs npm \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=ext-zip

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN npm run build

EXPOSE 8000

CMD ["sh", "-c", "php artisan package:discover --ansi && php artisan storage:link && php artisan config:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
