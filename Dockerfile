FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite \
    && a2dismod mpm_event \
    && a2enmod mpm_prefork

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080
CMD ["/start.sh"]
