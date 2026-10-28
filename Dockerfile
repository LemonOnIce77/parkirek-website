# syntax=docker/dockerfile:1

# =====================================================================
#  Parkirek - Sistem Parkir Digital Telkom University Surabaya
#  Multi-stage build:
#    1) assets  : Node 22  -> bundle frontend (Vite + Tailwind)
#    2) vendor  : Composer -> install dependency PHP (production only)
#    3) runtime : PHP 8.3 FPM + Nginx + Supervisor -> server aplikasi
# =====================================================================

# ---------------------------------------------------------------------
# Stage 1 : Build asset frontend
# ---------------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js ./
COPY public/ ./public/
COPY resources/ ./resources/

RUN npm run build \
    && test -f public/build/manifest.json

# ---------------------------------------------------------------------
# Stage 2 : Install dependency PHP
# ---------------------------------------------------------------------
FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --ignore-platform-req=ext-gd

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-scripts \
    && rm -rf public/build public/hot

# ---------------------------------------------------------------------
# Stage 3 : Runtime (PHP-FPM + Nginx + Supervisor)
# ---------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS runtime

ENV APP_HOME=/var/www/html \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_MEMORY_LIMIT=512M \
    TZ=Asia/Jakarta

# Dependencies runtime + build untuk ekstensi PHP
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        tzdata \
        libpng \
        libjpeg-turbo \
        libwebp \
        freetype \
        oniguruma \
        icu-libs \
        libzip \
        libxml2 \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        oniguruma-dev \
        icu-dev \
        libzip-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    # NB: opcache sudah aktif secara bawaan pada image resmi php
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        mbstring \
        intl \
        zip \
        gd \
        exif \
        bcmath \
        pcntl \
    && apk del .build-deps \
    && rm -rf /tmp/*

# Konfigurasi PHP, PHP-FPM, Nginx, Supervisor
COPY docker/php/php.ini            /usr/local/etc/php/conf.d/zz-parkirek.ini
COPY docker/php/php-fpm.conf       /usr/local/etc/php-fpm.d/zz-parkirek.conf
COPY docker/nginx/nginx.conf       /etc/nginx/nginx.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh          /usr/local/bin/entrypoint

# Source code aplikasi + asset frontend hasil build
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

RUN rm -f /etc/nginx/conf.d/default.conf \
    && mkdir -p /run/nginx /var/log/nginx \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/testing \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/logs \
        /var/www/html/storage/app/public \
        /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /usr/local/bin/entrypoint \
    && php -v \
    && nginx -v

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
