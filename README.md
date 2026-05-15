# AccessHub

AccessHub adalah aplikasi web internal berbasis Laravel untuk menyimpan, mencari, mengelola, dan membuka link kerja penting seperti Google Sheets, Google Docs, Google Drive, Canva, dashboard website, SOP, invoice, dan metadata akses platform tanpa menyimpan password asli.

AccessHub dibangun agar:
- ringan dan cepat dibuka
- aman untuk banyak user
- mobile friendly
- mudah dikembangkan
- siap deploy ke shared hosting maupun VPS

## Fitur Utama

- Authentication dengan user aktif/nonaktif
- Filament Admin Panel di `/admin`
- Internal app UI di `/dashboard`, `/app/links`, dan `/app/access-items`
- Link Manager dengan kategori, tag, favorite, archive, dan visibility berbasis role
- Access Item Manager untuk metadata akses non-password
- Role dan permission menggunakan `spatie/laravel-permission`
- Activity log menggunakan `spatie/laravel-activitylog`
- Soft delete untuk data penting
- Security hardening dasar untuk validation, authorization, rate limit login, dan sanitasi log

## Stack

- PHP 8.3+
- Laravel 13
- MySQL 8+ atau MariaDB 10.6+
- Tailwind CSS
- Blade
- Filament 5
- Laravel Sanctum
- Vite

## Struktur Akses

- `Super Admin`
  Bisa mengelola semua data, user, kategori, dan log aktivitas.
- `Admin`
  Bisa mengelola link dan access item, tetapi tidak bisa mengelola Super Admin.
- `Staff`
  Hanya bisa melihat data sesuai permission dan visibility yang diberikan.

## Keamanan Penting

- AccessHub bukan password manager.
- Jangan pernah menambahkan field `password` ke tabel `access_items`.
- Password platform tidak boleh disimpan di database.
- Gunakan field `password_location` hanya untuk referensi lokasi password eksternal seperti Bitwarden atau Google Password Manager.

## Kebutuhan Server

- PHP `8.3` atau lebih baru
- Composer `2.x`
- Node.js `20+` dan npm
- MySQL `8+` atau MariaDB `10.6+`
- Extension PHP umum Laravel:
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

## Instalasi Cepat Lokal

1. Clone project:

```bash
git clone <repository-url> accesshub
cd accesshub
```

2. Copy environment:

```bash
cp .env.example .env
```

3. Install dependency backend:

```bash
composer install
```

4. Install dependency frontend:

```bash
npm install
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Atur database di `.env`, lalu jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

7. Buat storage symlink:

```bash
php artisan storage:link
```

8. Jalankan Vite dev server:

```bash
npm run dev
```

9. Jalankan Laravel:

```bash
php artisan serve
```

## Default User Lokal

Seeder bawaan membuat akun berikut:

- `superadmin@accesshub.test` / `password`
- `admin@accesshub.test` / `password`
- `staff@accesshub.test` / `password`

Ganti password default setelah login pertama.

## Command Penting

- Jalankan test:

```bash
php artisan test
```

- Format code:

```bash
vendor/bin/pint
```

- Build asset production:

```bash
npm run build
```

- Clear cache:

```bash
php artisan optimize:clear
```

## URL Aplikasi

- Login: `/login`
- Dashboard internal: `/dashboard`
- Link manager internal: `/app/links`
- Access item internal: `/app/access-items`
- Admin panel Filament: `/admin`

## Deployment

Panduan deployment lengkap tersedia di [DEPLOYMENT.md](DEPLOYMENT.md).

Dokumen tersebut mencakup:
- local development
- shared hosting cPanel
- VPS Ubuntu + Nginx + MySQL
- Laravel Forge
- checklist security production
- backup, update, queue, dan scheduler

## Checklist Setelah Install

- pastikan `.env` sudah benar
- pastikan `APP_ENV` sesuai environment
- pastikan `APP_DEBUG=false` untuk production
- pastikan database sudah termigrasi
- pastikan `php artisan storage:link` sudah dijalankan
- pastikan akun default sudah diganti password-nya
- pastikan HTTPS aktif di server online

## Catatan Developer

- `created_by` dipakai untuk data penting seperti link dan access item.
- Activity log aktif untuk login, logout, create, update, archive, delete, dan open action.
- Staff tidak boleh bisa mengakses route admin.
- Visibility data staff dibatasi lewat policy dan query scope.
# accesshub
