FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libpng-dev libonig-dev libxml2-dev \
    nodejs npm \
    && docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN chown -R www-data:www-data storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo '#!/bin/bash\n\
sed -i "s/Listen 80/Listen ${PORT:-8080}/g" \
    /etc/apache2/ports.conf\n\
sed -i "s/:80>/:${PORT:-8080}>/g" \
    /etc/apache2/sites-available/000-default.conf\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan migrate --force\n\
apache2-foreground' > /start.sh

RUN chmod +x /start.sh

CMD ["/start.sh"]
