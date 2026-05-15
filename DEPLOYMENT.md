# Deployment Guide AccessHub

Dokumen ini dibuat untuk pemula. Ikuti langkahnya berurutan. Jika ragu, mulai dari bagian `Local Development` dulu sampai aplikasi berjalan normal.

## Isi Dokumen

1. Ringkasan requirement
2. Setup local development
3. Deploy ke shared hosting cPanel
4. Deploy ke VPS Ubuntu + Nginx + MySQL
5. Deploy dengan Laravel Forge
6. Queue dan scheduler
7. Backup database
8. Update aplikasi setelah deploy
9. Checklist security production
10. Troubleshooting singkat

## 1. Requirement Server

### Minimum software

- PHP 8.3 atau lebih baru
- Composer 2.x
- Node.js 20+ dan npm
- MySQL 8+ atau MariaDB 10.6+
- Git

### PHP extension yang dibutuhkan

- `bcmath`
- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `filter`
- `gd` atau `imagick`
- `json`
- `mbstring`
- `openssl`
- `pdo`
- `pdo_mysql`
- `session`
- `tokenizer`
- `xml`
- `zip`

### Web server

- Apache atau Nginx

### Akses yang ideal

- SSH untuk server Linux
- akses database MySQL
- kemampuan membuat cron job
- kemampuan mengarahkah domain ke folder `public`

## 2. Local Development

### Langkah 1. Clone project

```bash
git clone <repository-url> accesshub
cd accesshub
```

Jika project berasal dari file zip, extract dulu lalu masuk ke folder project.

### Langkah 2. Copy file environment

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

### Langkah 3. Atur `.env`

Contoh `.env` untuk local dengan MySQL:

```env
APP_NAME=AccessHub
APP_ENV=local
APP_DEBUG=true
APP_URL=http://accesshub.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accesshub
DB_USERNAME=root
DB_PASSWORD=
```

Jika ingin cepat, kamu juga bisa memakai SQLite dengan mengubah:

```env
DB_CONNECTION=sqlite
```

Lalu pastikan file `database/database.sqlite` ada.

### Langkah 4. Install dependency Composer

```bash
composer install
```

### Langkah 5. Install dependency npm

```bash
npm install
```

### Langkah 6. Generate APP_KEY

```bash
php artisan key:generate
```

### Langkah 7. Migrasi database

```bash
php artisan migrate
```

### Langkah 8. Seed user awal dan data awal

```bash
php artisan db:seed
```

Atau langsung sekaligus:

```bash
php artisan migrate --seed
```

### Langkah 9. Buat storage link

```bash
php artisan storage:link
```

### Langkah 10. Jalankan aplikasi

Backend:

```bash
php artisan serve
```

Frontend dev:

```bash
npm run dev
```

### Default credential lokal

- `superadmin@accesshub.test` / `password`
- `admin@accesshub.test` / `password`
- `staff@accesshub.test` / `password`

Ganti password default setelah login.

### Testing lokal

```bash
php artisan test
```

### Build asset production lokal

```bash
npm run build
```

## 3. Shared Hosting cPanel

Deploy di shared hosting biasanya paling tricky karena `public_html` dan akses SSH bisa terbatas. Ikuti alur ini pelan-pelan.

### Langkah 1. Pastikan hosting mendukung

Checklist:

- PHP 8.3+
- Composer support atau SSH
- Node.js support atau build asset dilakukan di lokal
- MySQL database
- cron job
- SSL/HTTPS

Jika hosting tidak mendukung Node.js, build asset di komputer lokal lalu upload hasil folder `public/build`.

### Langkah 2. Upload source code

Rekomendasi struktur:

- source code aplikasi di luar `public_html`
- isi folder `public` dipindahkan atau diarahkan ke `public_html`

Contoh:

- `/home/username/accesshub` untuk source code
- `/home/username/public_html` untuk web root

### Langkah 3. Clone via SSH atau upload zip

Jika ada SSH:

```bash
cd /home/username
git clone <repository-url> accesshub
cd accesshub
```

Jika tidak ada SSH:

- upload zip project
- extract lewat File Manager

### Langkah 4. Copy `.env`

```bash
cp .env.example .env
```

### Langkah 5. Isi `.env` production

Contoh penting:

```env
APP_NAME=AccessHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://accesshub.domainkamu.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=nama_user_db
DB_PASSWORD=password_db

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Langkah 6. Install dependency Composer

Jika SSH tersedia:

```bash
composer install --no-dev --optimize-autoloader
```

Jika Composer tidak tersedia di hosting:

- jalankan `composer install --no-dev --optimize-autoloader` di lokal atau VPS build machine
- upload folder `vendor`

### Langkah 7. Install/build asset frontend

Jika hosting mendukung Node:

```bash
npm install
npm run build
```

Jika tidak mendukung Node:

- jalankan `npm install`
- jalankan `npm run build`
- upload hasil `public/build` ke server

### Langkah 8. Generate APP_KEY

```bash
php artisan key:generate
```

### Langkah 9. Migrasi database

```bash
php artisan migrate --force
```

### Langkah 10. Seed data awal

Jika ini instalasi pertama:

```bash
php artisan db:seed --force
```

### Langkah 11. Storage link

```bash
php artisan storage:link
```

Jika `storage:link` diblokir hosting:

- buat symlink manual jika diizinkan
- atau gunakan file manager untuk memastikan `public/storage` mengarah ke `storage/app/public`

### Langkah 12. Atur permission folder

Umumnya:

```bash
chmod -R 775 storage bootstrap/cache
```

Jika perlu set owner:

```bash
chown -R username:username storage bootstrap/cache
```

### Langkah 13. Atur document root

Paling ideal:

- domain diarahkan ke folder `public`

Jika cPanel tidak mengizinkan document root custom:

- pindahkan isi folder `public` ke `public_html`
- lalu edit `index.php` agar path ke `vendor/autoload.php` dan `bootstrap/app.php` sesuai lokasi source code

Contoh penyesuaian umum:

```php
require __DIR__.'/../accesshub/vendor/autoload.php';
$app = require_once __DIR__.'/../accesshub/bootstrap/app.php';
```

Sesuaikan dengan struktur hosting kamu.

### Langkah 14. Aktifkan HTTPS

Di cPanel biasanya:

- aktifkan SSL dari AutoSSL atau Let’s Encrypt
- pastikan domain sudah resolve
- ubah `APP_URL` menjadi `https://...`

Jika ingin force HTTPS dari Laravel, bisa gunakan proxy/web server configuration yang benar. Biasanya cukup dari hosting panel atau `.htaccess`.

### Langkah 15. Cache konfigurasi production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 16. Queue dan cron

Jika memakai queue database:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Di shared hosting biasanya gunakan cron untuk restart worker secara periodik jika supervisor tidak tersedia.

Cron scheduler:

```bash
* * * * * cd /home/username/accesshub && php artisan schedule:run >> /dev/null 2>&1
```

Catatan:

- saat ini AccessHub belum punya task scheduler wajib
- cron di atas disiapkan agar aman untuk pengembangan fitur ke depan

## 4. VPS Ubuntu + Nginx + MySQL

Panduan ini cocok untuk Ubuntu 24.04 atau 22.04.

### Langkah 1. Install package server

```bash
sudo apt update
sudo apt install -y nginx mysql-server git unzip curl
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-curl php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl
```

Install Composer:

```bash
cd /tmp
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
```

Install Node.js 20:

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Langkah 2. Buat database dan user MySQL

Masuk ke MySQL:

```bash
sudo mysql
```

Lalu jalankan:

```sql
CREATE DATABASE accesshub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'accesshub_user'@'localhost' IDENTIFIED BY 'ganti_password_kuat';
GRANT ALL PRIVILEGES ON accesshub.* TO 'accesshub_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 3. Clone project

```bash
cd /var/www
sudo git clone <repository-url> accesshub
cd accesshub
```

### Langkah 4. Install dependency

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Langkah 5. Buat `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Contoh `.env` production:

```env
APP_NAME=AccessHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://accesshub.domainkamu.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accesshub
DB_USERNAME=accesshub_user
DB_PASSWORD=ganti_password_kuat

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Langkah 6. Migrasi dan seed

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### Langkah 7. Permission folder

```bash
sudo chown -R www-data:www-data /var/www/accesshub
sudo find /var/www/accesshub -type f -exec chmod 644 {} \;
sudo find /var/www/accesshub -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/accesshub/storage /var/www/accesshub/bootstrap/cache
```

### Langkah 8. Konfigurasi Nginx

Buat file:

```bash
sudo nano /etc/nginx/sites-available/accesshub
```

Isi dasar:

```nginx
server {
    listen 80;
    server_name accesshub.domainkamu.com;

    root /var/www/accesshub/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/accesshub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Langkah 9. Setting domain

Di panel DNS domain kamu, arahkan:

- `A record` ke IP VPS
- atau `www` CNAME ke domain utama

Tunggu propagasi DNS selesai.

### Langkah 10. Aktifkan HTTPS dengan Let’s Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d accesshub.domainkamu.com
```

Setelah berhasil:

- pastikan `APP_URL` memakai `https://`
- reload config bila perlu:

```bash
php artisan config:clear
php artisan config:cache
```

### Langkah 11. Cache production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Optional Laravel Forge

Jika memakai Laravel Forge, prosesnya lebih mudah.

### Langkah umum

1. Buat server di Forge.
2. Hubungkan repository Git.
3. Buat site baru dengan domain AccessHub.
4. Set web directory ke `/public`.
5. Tambahkan environment `.env`.
6. Jalankan deploy script.
7. Aktifkan database MySQL.
8. Aktifkan SSL dari Forge.
9. Tambahkan daemon queue jika diperlukan.
10. Tambahkan scheduler dari Forge.

### Contoh deploy script Forge

```bash
cd /home/forge/accesshub
git pull origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true
```

## 6. Queue dan Scheduler

### Queue

AccessHub saat ini belum bergantung pada queue untuk fitur utama, tetapi `QUEUE_CONNECTION=database` sudah aman dipakai untuk ekspansi fitur ke depan.

Jika ingin mengaktifkan queue database:

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work --tries=3 --timeout=90
```

Di VPS, jalankan queue dengan Supervisor.

Contoh konfigurasi Supervisor:

```ini
[program:accesshub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/accesshub/artisan queue:work --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/accesshub/storage/logs/worker.log
stopwaitsecs=3600
```

Setelah itu:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start accesshub-worker:*
```

### Scheduler

Saat ini tidak ada task scheduler wajib, tetapi best practice production tetap menambahkan cron:

```bash
* * * * * cd /var/www/accesshub && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Backup Database

### Backup manual MySQL

```bash
mysqldump -u accesshub_user -p accesshub > accesshub-backup.sql
```

### Backup dengan timestamp

```bash
mysqldump -u accesshub_user -p accesshub > accesshub-$(date +%F-%H%M).sql
```

### Restore backup

```bash
mysql -u accesshub_user -p accesshub < accesshub-backup.sql
```

### Rekomendasi

- simpan backup harian
- simpan backup di lokasi terpisah dari server utama
- pastikan backup database dienkripsi jika dipindahkan ke cloud storage

## 8. Update Aplikasi Setelah Deploy

Setiap ada update code:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Jika server tidak menjalankan queue worker, `php artisan queue:restart` bisa dilewati.

Jika kamu build asset di lokal, upload perubahan folder `public/build`.

## 9. Checklist Security Production

Wajib dicek sebelum go live:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` sudah memakai `https://`
- SSL/HTTPS aktif
- password database kuat
- user default sudah ganti password
- hanya port penting yang dibuka
- folder `storage` dan `bootstrap/cache` writable
- folder lain tidak world-writable
- jangan commit `.env`
- jangan commit file backup database
- jangan simpan password platform di `access_items`
- pastikan route `/admin` tidak bisa diakses staff
- jalankan `php artisan migrate --force`
- jalankan `php artisan config:cache`
- jalankan `php artisan route:cache`
- jalankan `php artisan view:cache`
- buat backup database sebelum update besar
- review log aplikasi secara berkala

## 10. Troubleshooting Singkat

### Error 500 setelah deploy

Cek:

- `.env` benar atau tidak
- `APP_KEY` sudah ada atau belum
- permission folder `storage` dan `bootstrap/cache`
- dependency `vendor` sudah terinstall atau belum
- file build Vite sudah ada atau belum

### Asset CSS/JS tidak muncul

Cek:

- apakah `npm run build` sudah dijalankan
- apakah folder `public/build` ikut terupload
- apakah cache browser perlu dibersihkan

### Login gagal terus

Cek:

- database user ada atau tidak
- password default benar atau tidak
- user `is_active` bernilai aktif
- session table sudah termigrasi

### Symlink storage gagal

Cek:

- apakah server mengizinkan symlink
- apakah `public/storage` sudah mengarah ke `storage/app/public`

### Halaman blank setelah update

Jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Ringkasan Command Production

Instalasi awal:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Update aplikasi:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```
