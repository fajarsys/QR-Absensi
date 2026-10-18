# ---- Stage 1: build aset (dibuang setelah selesai) ----
FROM node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .

# Publik (ikut terkirim ke browser). JANGAN masukkan REVERB_APP_SECRET.
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST=absensimercusuar.biz.id
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

RUN test -n "$VITE_REVERB_APP_KEY" || (echo "ERROR: VITE_REVERB_APP_KEY kosong. Pakai --build-arg VITE_REVERB_APP_KEY=..." && exit 1)
RUN npm run build

# ---- Stage 2: image runtime ----
FROM serversideup/php:8.4-fpm-nginx

USER root
RUN install-php-extensions gd

COPY docker/entrypoint.d/10-storage-dirs.sh /etc/entrypoint.d/10-storage-dirs.sh
RUN chmod +x /etc/entrypoint.d/10-storage-dirs.sh

COPY --chown=www-data:www-data composer.json composer.lock ./
USER www-data
RUN composer install --no-interaction --prefer-dist --no-dev \
    --no-scripts --no-autoloader

COPY --chown=www-data:www-data . .

RUN mkdir -p bootstrap/cache storage/logs \
        storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R ug+rwX bootstrap/cache storage

RUN composer dump-autoload --optimize --no-dev

# Hasil build aset dari stage 1
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build