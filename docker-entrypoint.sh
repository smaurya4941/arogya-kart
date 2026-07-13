#!/bin/bash
set -e

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# We do NOT run migrations here in entrypoint because Render's Release Command handles it better.
# However, if you are not using Render's Release Command, uncomment the line below.
# php artisan migrate --force

# Execute the main container command (apache2-foreground)
exec "$@"
