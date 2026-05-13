FROM dunglas/frankenphp:php8.2.31-bookworm

RUN apt-get update && apt-get install -y \
    ca-certificates git unzip zip libzip-dev \
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

RUN npm run build

RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize

EXPOSE 8000

CMD php artisan migrate --force \
    && php artisan storage:link \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan serve --host=0.0.0.0 --port=$PORT