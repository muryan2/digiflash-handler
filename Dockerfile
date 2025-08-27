# Gunakan image dasar PHP
FROM php:8.2-apache

# Salin file PHP Anda ke direktori kerja
COPY handle_topup.php /var/www/html/

# Ubah konfigurasi Apache untuk mengaktifkan mod_rewrite
RUN a2enmod rewrite

# Buat file .htaccess untuk mengarahkan semua permintaan ke handle_topup.php
COPY .htaccess /var/www/html/

# Expose port yang digunakan oleh Apache
EXPOSE 80