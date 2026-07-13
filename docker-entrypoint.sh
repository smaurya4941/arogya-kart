#!/bin/bash
set -e

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# We do NOT run migrations here in entrypoint because Render's Release Command handles it better.
# However, if you are not using Render's Release Command, uncomment the line below.
# php artisan migrate --force

# Render injects the PORT environment variable. Update Apache configuration at runtime.
APACHE_PORT=${PORT:-80}
sed -i "s/Listen 80/Listen ${APACHE_PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${APACHE_PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Execute the main container command (apache2-foreground)
exec "$@"
