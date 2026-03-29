#!/bin/bash

# Pastikan port menggunakan variable environment dari Railway dengan regex yang aman
echo "Listen ${PORT:-8080}" > /etc/apache2/ports.conf
sed -i -E "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT:-8080}>/g" /etc/apache2/sites-available/000-default.conf

# Hapus file hot agar Laravel tidak konek ke Vite dev server di production
rm -f /var/www/html/public/hot

# Generate APP_KEY jika kosong (Railway harus set APP_KEY di environment variable)
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY tidak ditemukan! Generating sementara..."
    php artisan key:generate --no-interaction
fi

# Buat symlink storage → public/storage jika belum ada
php artisan storage:link --no-interaction 2>/dev/null || true

# Safety net: pastikan hanya mpm_prefork yang aktif sebelum Apache start
# Ini mencegah error "More than one MPM loaded" di runtime
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true
rm -f /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_worker.conf \
      /etc/apache2/mods-enabled/mpm_worker.load

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
