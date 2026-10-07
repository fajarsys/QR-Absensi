FROM serversideup/php:8.4-fpm-nginx

USER root
# Instal dependensi sistem
RUN install-php-extensions gd
RUN apk add --no-cache nodejs npm || (apt-get update && apt-get install -y nodejs npm)

# ---- Dependensi PHP (layer ter-cache selama composer.json/lock tidak berubah) ----
COPY --chown=www-data:www-data composer.json composer.lock ./
USER www-data
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

# ---- Dependensi Node (layer ter-cache selama package.json/lock tidak berubah) ----
COPY --chown=www-data:www-data package.json package-lock.json* ./
RUN npm install

# ---- Kode aplikasi ----
COPY --chown=www-data:www-data . .

# Finalisasi composer (autoload + package discovery)
RUN composer dump-autoload --optimize

# ---- Nilai build-time untuk frontend (Echo/Reverb) ----
# Ini publik (ikut terkirim ke browser). JANGAN masukkan REVERB_APP_SECRET di sini.
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST=absensimercusuar.biz.id
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

# Fail cepat kalau lupa kirim build-arg, daripada baru ketahuan di browser
RUN test -n "$VITE_REVERB_APP_KEY" || (echo "ERROR: VITE_REVERB_APP_KEY kosong. Pakai --build-arg VITE_REVERB_APP_KEY=..." && exit 1)

RUN npm run build