# ── Stage 1: Build Frontend Assets ───────────────────────────────────────────
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --no-audit
COPY . .
RUN npm run build

# ── Stage 2: Install Composer Dependencies ────────────────────────────────────
FROM composer:2 AS vendor
WORKDIR /app
COPY composer*.json ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ── Stage 3: Production Alpine Runtime (Nginx + PHP-FPM + Supervisord) ────────
FROM php:8.4-fpm-alpine

# Install system dependencies, Nginx, and Supervisord
RUN apk add --no-cache \
    bash \
    curl \
    nginx \
    supervisor \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    icu-dev

# Install and configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip gd bcmath opcache intl

# Configure production OPcache
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install Composer CLI in runtime container for emergency maintenance
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application codebase
COPY . .

# Copy production PHP vendor dependencies from Stage 2
COPY --from=vendor /app/vendor ./vendor

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Copy Nginx and Supervisord configurations
COPY docker/nginx-prod.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /var/www/html/docker/entrypoint.sh

# Configure file ownership and execution permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/docker/entrypoint.sh /var/www/html/start.sh \
    && mkdir -p /var/log/supervisor /run/nginx

EXPOSE 10000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
