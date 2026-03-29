#!/bin/bash
sed -i "s/Listen 80/Listen ${PORT:-8080}/g" \
    /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT:-8080}>/g" \
    /etc/apache2/sites-available/000-default.conf
php artisan config:cache
php artisan route:cache  
php artisan migrate --force
apache2-foreground
