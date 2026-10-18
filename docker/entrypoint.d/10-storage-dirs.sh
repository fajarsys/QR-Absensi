#!/bin/sh
set -e

# Jalan tiap container start.
# Memastikan struktur storage ada walau folder di-mount dari volume kosong.
mkdir -p /var/www/html/storage/app/public \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs