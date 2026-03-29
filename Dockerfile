FROM php:8.2-apache

# Install dependencies + PHP extensions + enable rewrite
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install \
    pdo_mysql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install NodeJS 20 via nodesource BEFORE fixing MPM
# (apt post-install hooks can re-enable mpm_event if node is installed after)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# PERMANENTLY fix MPM conflict using a2dismod/a2enmod (more reliable than manual symlinks)
# This runs AFTER all apt installs so nothing can undo it
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork \
    && rm -f /etc/apache2/mods-enabled/mpm_event.conf \
             /etc/apache2/mods-enabled/mpm_event.load \
             /etc/apache2/mods-enabled/mpm_worker.conf \
             /etc/apache2/mods-enabled/mpm_worker.load

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Hapus file 'hot' agar Laravel tidak mencoba konek ke Vite dev server di production
RUN rm -f public/hot

# Buat baseline .env agar artisan command tidak error dan key:generate bisa menulis file
RUN cp .env.example .env

# Generate APP_KEY jika belum ada (untuk environment build)
RUN php artisan key:generate --no-interaction 2>/dev/null || true

# Pastikan www-data bisa menulis ke storage, bootstrap cache, DAN folder database (untuk lock SQLite)
RUN chown -R www-data:www-data storage bootstrap/cache database
RUN chmod -R 775 storage bootstrap/cache database

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh && sed -i 's/\r$//' /start.sh

EXPOSE 8080
CMD ["/start.sh"]
