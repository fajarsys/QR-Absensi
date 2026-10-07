FROM serversideup/php:8.4-fpm-nginx
USER root
RUN install-php-extensions gd
COPY s6-overlay/ /etc/s6-overlay/
COPY nginx/reverb.conf /etc/nginx/server-opts.d/reverb.conf
USER www-data
