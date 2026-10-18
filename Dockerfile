FROM serversideup/php:8.4-fpm-nginx

USER root
RUN install-php-extensions gd \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# Script pembuat folder storage saat container start
COPY docker/entrypoint.d/10-storage-dirs.sh /etc/entrypoint.d/10-storage-dirs.sh
RUN chmod +x /etc/entrypoint.d/10-storage-dirs.sh

# ---- Dependensi PHP (layer ter-cache) ----
COPY --chown=www-data:www-data composer.json composer.lock ./
USER www-data
RUN composer install --no-interaction --prefer-dist --no-dev \
    --no-scripts --no-autoloader

# ---- Dependensi Node (layer ter-cache) ----
COPY --chown=www-data:www-data package.json package-lock.json* ./
RUN npm ci

# ---- Kode aplikasi ----
COPY --chown=www-data:www-data . .

# Jaring pengaman saat build
RUN mkdir -p bootstrap/cache \
        storage/logs \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
    && chmod -R ug+rwX bootstrap/cache storage

# Autoload + package:discover (sudah otomatis menjalankan post-autoload-dump)
RUN composer dump-autoload --optimize --no-dev

# ---- Nilai build-time frontend (publik, ikut ke browser) ----
# JANGAN masukkan REVERB_APP_SECRET di sini.
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST=absensimercusuar.biz.id
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

RUN test -n "$VITE_REVERB_APP_KEY" || (echo "ERROR: VITE_REVERB_APP_KEY kosong. Pakai --build-arg VITE_REVERB_APP_KEY=..." && exit 1)

RUN npm run build && rm -rf node_modules