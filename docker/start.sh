#!/bin/bash

# Pastikan port menggunakan variable environment dari Railway dengan regex yang aman
echo "Listen ${PORT:-8080}" > /etc/apache2/ports.conf
sed -i -E "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT:-8080}>/g" /etc/apache2/sites-available/000-default.conf

# Bersihkan dan cache konfigurasi (baik dijalankan setiap boot)
php artisan config:cache
php artisan route:cache  

# Paksa migrasi database
php artisan migrate --force

# PERBAIKAN PENTING: Jika sebelumnya php artisan dijalankan sebagai 'root', 
# file log/cache yang dibuat akan dimiliki oleh root. Ini membuat Apache (www-data) 
# crash karena tidak bisa menulis log! Kita kembalikan kepemilikannya.
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Gunakan 'exec' agar apache2 mengambil PID 1, mencegah Railway mendeteksi 
# bash parent yang mati duluan sebagai "Crashed"
exec apache2-foreground
