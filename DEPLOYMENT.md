# Deployment Guide AccessHub for Shared Hosting

Dokumen ini difokuskan untuk deployment AccessHub ke `Shared Hosting`, terutama jika source code dihubungkan ke repository:

```text
https://github.com/arifinprofitformula-idn/accesshub
```

dan source code aplikasi ditempatkan di:

```text
/home/arvadigi/repositories/accesshub
```

Panduan ini dibuat untuk pemula dan mengutamakan alur deploy yang aman, rapi, dan realistis untuk hosting berbasis cPanel atau shared hosting Linux serupa.

## Ringkasan Struktur Deploy

Struktur yang direkomendasikan:

- source code Laravel: `/home/arvadigi/repositories/accesshub`
- web root domain: `public_html` atau document root domain/addon domain
- folder yang diakses web server harus menunjuk ke folder `public` milik Laravel

Jika hosting mendukung custom document root:

- arahkan domain langsung ke:

```text
/home/arvadigi/repositories/accesshub/public
```

Jika hosting tidak mendukung custom document root:

- source code tetap di `/home/arvadigi/repositories/accesshub`
- isi folder `public` dipasang ke `public_html`
- file `index.php` di `public_html` disesuaikan agar menunjuk ke source code Laravel

## 1. Requirement Server

Minimal kebutuhan server:

- PHP 8.3 atau lebih baru
- Composer 2.x
- MySQL 8+ atau MariaDB 10.6+
- Git
- SSL/HTTPS
- cron job

Jika hosting mendukung Node.js, itu lebih baik. Jika tidak, asset frontend bisa dibuild di lokal lalu diupload.

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

## 2. Repository dan Lokasi Source Code

Repository yang dipakai:

```text
https://github.com/arifinprofitformula-idn/accesshub
```

Lokasi source code di server:

```text
/home/arvadigi/repositories/accesshub
```

Semua command di panduan ini diasumsikan dijalankan dari folder tersebut, kecuali jika disebutkan lain.

## 3. Clone Project ke Server

Jika hosting menyediakan SSH, masuk ke server lalu jalankan:

```bash
cd /home/arvadigi/repositories
git clone https://github.com/arifinprofitformula-idn/accesshub.git accesshub
cd /home/arvadigi/repositories/accesshub
```

Jika folder `accesshub` sudah ada dan sudah terhubung ke repository, cukup:

```bash
cd /home/arvadigi/repositories/accesshub
git pull origin main
```

Jika hosting tidak menyediakan Git di server:

- clone repository di lokal
- upload source code ke `/home/arvadigi/repositories/accesshub`

## 4. Setup File .env

Copy file environment:

```bash
cd /home/arvadigi/repositories/accesshub
cp .env.example .env
```

Lalu isi `.env` untuk production. Contoh:

```env
APP_NAME=AccessHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domainkamu.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=nama_user_db
DB_PASSWORD=password_db

LOG_CHANNEL=stack
LOG_LEVEL=error

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

Catatan penting:

- gunakan `APP_ENV=production`
- gunakan `APP_DEBUG=false`
- gunakan `APP_URL` sesuai domain HTTPS final
- jangan upload file `.env` ke repository

## 5. Install Dependency Composer

Jika Composer tersedia di server:

```bash
cd /home/arvadigi/repositories/accesshub
composer install --no-dev --optimize-autoloader
```

Jika Composer tidak tersedia di server:

- jalankan command berikut di lokal:

```bash
composer install --no-dev --optimize-autoloader
```

- lalu upload folder `vendor` ke server

## 6. Install Dependency npm dan Build Asset

Jika hosting mendukung Node.js:

```bash
cd /home/arvadigi/repositories/accesshub
npm install
npm run build
```

Jika hosting tidak mendukung Node.js:

jalankan di lokal:

```bash
npm install
npm run build
```

lalu upload hasil berikut ke server:

- `public/build`

Jika ada perubahan frontend besar, ulangi proses build dan upload folder `public/build`.

## 7. Generate APP_KEY

Setelah `.env` siap:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan key:generate
```

## 8. Setup Database

Buat database dan user database dari cPanel:

1. buka `MySQL Databases`
2. buat database baru
3. buat user database baru
4. hubungkan user ke database
5. beri `ALL PRIVILEGES`

Setelah itu, masukkan detail database ke `.env`.

## 9. Jalankan Migration

Setelah database siap:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan migrate --force
```

### Jika muncul error `Table 'users' already exists`

Error seperti ini berarti database yang kamu pakai sudah memiliki tabel Laravel lama, tetapi status migration di database belum sinkron.

Contoh error:

```text
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'users' already exists
```

Penyebab paling umum:

- database yang dipakai bukan database kosong
- tabel `users` sudah pernah dibuat manual atau dari install lama
- tabel `migrations` kosong atau belum ada, sehingga Laravel mengira semua migration belum pernah dijalankan

### Cara cek kondisi database

Masuk ke phpMyAdmin atau MySQL lalu cek:

- apakah tabel `users` sudah ada
- apakah tabel `migrations` ada
- apakah tabel `migrations` berisi data migration lama

### Solusi aman jika ini install baru dan data lama tidak dipakai

Jika database memang boleh dikosongkan:

1. hapus semua tabel lama dari database lewat phpMyAdmin
2. pastikan database kosong
3. jalankan ulang:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan migrate --force
php artisan db:seed --force
```

Ini adalah solusi paling bersih untuk instalasi pertama.

### Solusi aman jika database lama masih ingin dipakai

Jika database sudah berisi data penting, jangan hapus tabel sembarangan.

Lakukan langkah berikut:

1. backup database terlebih dahulu
2. cek tabel mana yang sudah ada
3. sinkronkan database lama secara manual

Dalam kondisi ini, biasanya ada 2 pilihan:

- buat database baru yang kosong khusus untuk AccessHub
- atau rapikan struktur migration lama agar sesuai dengan schema AccessHub saat ini

Untuk shared hosting, opsi terbaik biasanya:

- buat database baru yang kosong
- update `.env` agar memakai database baru tersebut
- jalankan migration dari awal

### Rekomendasi untuk AccessHub

Untuk deploy pertama AccessHub di shared hosting, paling aman gunakan:

- database baru yang kosong
- lalu jalankan migration dan seeder dari nol

Langkah ringkas:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
```

Jika ini install pertama dan kamu juga ingin langsung isi data awal:

```bash
php artisan db:seed --force
```

Atau langsung sekaligus:

```bash
php artisan migrate --seed --force
```

## 10. Seeder User Awal

Seeder bawaan AccessHub membuat user default:

- `superadmin@accesshub.test` / `password`
- `admin@accesshub.test` / `password`
- `staff@accesshub.test` / `password`

Setelah berhasil login, segera ganti password default tersebut.

## 11. Setup Storage Link

Jalankan:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan storage:link
```

Jika shared hosting tidak mengizinkan symlink:

- cek apakah `public/storage` sudah bisa diarahkan ke `storage/app/public`
- jika tidak bisa, minta bantuan provider hosting atau gunakan file manager sesuai batasan hosting

## 12. Setup Permission Folder

Minimal folder berikut harus writable:

- `storage`
- `bootstrap/cache`

Jika SSH tersedia:

```bash
cd /home/arvadigi/repositories/accesshub
chmod -R 775 storage bootstrap/cache
```

Jika perlu:

```bash
chown -R arvadigi:arvadigi storage bootstrap/cache
```

Jika tidak ada SSH:

- atur permission lewat File Manager cPanel

## 13. Setting Domain dan Document Root

Ada 2 kemungkinan deployment.

### Opsi A. Hosting mendukung custom document root

Ini opsi terbaik.

Arahkan domain atau subdomain ke:

```text
/home/arvadigi/repositories/accesshub/public
```

Dengan cara ini, kamu tidak perlu memindahkan isi folder `public`.

### Opsi B. Hosting tidak mendukung custom document root

Jika domain hanya bisa diarahkan ke `public_html`, lakukan langkah berikut:

1. source code tetap di:

```text
/home/arvadigi/repositories/accesshub
```

2. copy isi folder `public` ke `public_html`
3. edit file `public_html/index.php`
4. sesuaikan path bootstrap dan autoload

Contoh `index.php`:

```php
require __DIR__.'/../repositories/accesshub/vendor/autoload.php';

$app = require_once __DIR__.'/../repositories/accesshub/bootstrap/app.php';
```

Catatan:

- sesuaikan path jika struktur folder hosting berbeda
- jangan pindahkan seluruh source code ke `public_html`
- yang boleh diakses publik hanya isi folder `public`

## 14. Setting HTTPS

Aktifkan SSL dari cPanel:

- gunakan AutoSSL atau Let’s Encrypt jika tersedia
- pastikan domain sudah resolve ke hosting

Setelah SSL aktif:

- ubah `APP_URL` di `.env` menjadi `https://domainkamu.com`

Lalu bersihkan dan cache ulang config:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan config:clear
php artisan config:cache
```

## 15. Cache Production

Setelah deploy selesai:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ingin reset semua cache terlebih dahulu:

```bash
php artisan optimize:clear
```

## 16. Queue Jika Dibutuhkan

Saat ini AccessHub masih bisa berjalan tanpa queue worker aktif terus-menerus untuk fitur utamanya, tetapi konfigurasi `QUEUE_CONNECTION=database` tetap bisa disiapkan.

Jika nanti dibutuhkan:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan queue:work --tries=3 --timeout=90
```

Di shared hosting biasanya queue tidak dijalankan seperti di VPS dengan Supervisor. Jika provider mendukung cron, kamu bisa diskusikan pola eksekusi periodik sesuai kebutuhan fitur.

## 17. Scheduler Jika Dibutuhkan

Walau saat ini belum ada task scheduler wajib, sebaiknya tetap siapkan cron Laravel:

```bash
* * * * * cd /home/arvadigi/repositories/accesshub && php artisan schedule:run >> /dev/null 2>&1
```

Tambahkan lewat menu `Cron Jobs` di cPanel.

## 18. Backup Database

### Backup manual dari server

```bash
mysqldump -u nama_user_db -p nama_database > accesshub-backup.sql
```

### Backup dengan timestamp

```bash
mysqldump -u nama_user_db -p nama_database > accesshub-$(date +%F-%H%M).sql
```

### Restore backup

```bash
mysql -u nama_user_db -p nama_database < accesshub-backup.sql
```

Jika tidak ada SSH:

- export database dari phpMyAdmin

Rekomendasi:

- backup sebelum update besar
- backup rutin harian atau mingguan
- simpan backup di luar hosting utama

## 19. Cara Update Aplikasi Setelah Deploy

Setiap ada update code baru dari repository:

```bash
cd /home/arvadigi/repositories/accesshub
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan frontend dan server mendukung Node.js:

```bash
npm install
npm run build
```

Jika server tidak mendukung Node.js:

- build asset di lokal
- upload ulang folder `public/build`

Jika queue dipakai:

```bash
php artisan queue:restart
```

## 20. Checklist Security Production

Checklist wajib sebelum go live:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` sudah `https://`
- SSL aktif
- password default user awal sudah diganti
- password database kuat
- `.env` tidak bisa diakses publik
- hanya folder `public` yang menjadi web root
- `storage` dan `bootstrap/cache` writable
- route `/admin` tidak boleh bisa diakses Staff
- jangan simpan password platform di `access_items`
- jalankan `php artisan migrate --force`
- jalankan `php artisan config:cache`
- jalankan `php artisan route:cache`
- jalankan `php artisan view:cache`
- backup database sebelum update besar

## 21. Troubleshooting Singkat

### Error 500 setelah deploy

Cek:

- `.env` sudah benar atau belum
- `APP_KEY` sudah dibuat atau belum
- folder `storage` dan `bootstrap/cache` writable atau belum
- folder `vendor` sudah ada atau belum
- build asset Vite sudah ada atau belum

### CSS atau JS tidak muncul

Cek:

- `npm run build` sudah dijalankan atau belum
- folder `public/build` sudah ikut terupload atau belum
- cache browser perlu dibersihkan atau tidak

### Login gagal

Cek:

- user hasil seeder sudah ada atau belum
- password benar atau tidak
- user aktif atau tidak
- database session sudah termigrasi atau belum

### Error `Table already exists` saat migrate

Cek:

- apakah database yang dipakai benar-benar kosong
- apakah `.env` menunjuk ke database yang benar
- apakah tabel `users` atau tabel lain sudah ada dari install lama
- apakah tabel `migrations` kosong atau belum ada

Solusi paling aman untuk install pertama:

- buat database baru yang kosong
- update `.env`
- jalankan ulang:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
```

### Storage link gagal

Cek:

- hosting mengizinkan symlink atau tidak
- `public/storage` sudah mengarah ke `storage/app/public` atau belum

### Setelah update halaman blank

Jalankan:

```bash
cd /home/arvadigi/repositories/accesshub
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 22. Ringkasan Command Shared Hosting

### Instalasi awal

```bash
cd /home/arvadigi/repositories/accesshub
cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika server mendukung Node.js:

```bash
cd /home/arvadigi/repositories/accesshub
npm install
npm run build
```

### Update aplikasi

```bash
cd /home/arvadigi/repositories/accesshub
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan frontend:

```bash
npm install
npm run build
```
