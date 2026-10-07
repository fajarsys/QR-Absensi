FROM serversideup/php:8.4-fpm-nginx

USER root
# Instal dependensi sistem
RUN install-php-extensions gd
RUN apk add --no-cache nodejs npm || (apt-get update && apt-get install -y nodejs npm)

# Salin KODE Laravel dan otomatis ubah hak milik
COPY --chown=www-data:www-data . /var/www/html

USER www-data
# Instal dependensi aplikasi
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install && npm run build