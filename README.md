# Sistem Absensi Siswa Sekolah

Aplikasi administrasi absensi berbasis Laravel 10, PHP 8.1, MySQL, Blade, Bootstrap 5, dan Vite. Mendukung tiga role (Admin, Kepala Sekolah, Wali Kelas), QR kamera, absensi manual, kartu siswa, serta laporan Excel/PDF.

## Fitur utama

- Login email/username, status akun, login terakhir, dan pembatasan role di backend.
- Master siswa, guru, kelas, pengguna, serta identitas sekolah.
- QR unik acak; regenerasi langsung menonaktifkan token lama.
- Scan kamera browser dengan validasi kelas, jeda scan, rate limit, transaksi, dan pencegahan duplikat.
- Absensi manual massal (hadir, terlambat, izin, sakit, alfa).
- Dashboard responsif dan laporan terfilter Excel/PDF.
- 30 siswa, 5 kelas, 5 guru, serta contoh absensi tujuh hari dari seeder.

## Persyaratan

PHP 8.1 dengan ekstensi mbstring, openssl, pdo_mysql, gd, fileinfo, xml, zip; Composer 2; MySQL/MariaDB; Node.js dan npm. Document root produksi harus mengarah ke folder `public`.

## Instalasi localhost

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL bernama `web_absensi`, lalu sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Pada Windows PowerShell yang memblokir `npm.ps1`, gunakan `npm.cmd install` dan `npm.cmd run build`.

## Akun demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@sekolah.test | password |
| Kepala Sekolah | kepala@sekolah.test | password |
| Wali Kelas 1A | wali1a@sekolah.test | password |
| Wali Kelas 1B | wali1b@sekolah.test | password |

Segera ganti password demo di lingkungan nyata.

## Kamera

Browser hanya mengizinkan kamera pada HTTPS atau `localhost`. Izinkan kamera saat diminta, gunakan kamera belakang pada HP, pastikan QR mendapat cahaya cukup, dan jangan membuka scanner di dua tab. Deployment publik wajib HTTPS.

## Pengujian

```bash
php artisan test
php artisan route:list
```

## Deployment VPS Ubuntu + Nginx

Instal PHP 8.1-FPM beserta ekstensi, MySQL, Composer, Node, dan Nginx. Clone aplikasi ke `/var/www/web_absensi`, buat `.env` produksi (`APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS), lalu jalankan instalasi di atas dengan `npm run build`.

```bash
sudo chown -R www-data:www-data /var/www/web_absensi/storage /var/www/web_absensi/bootstrap/cache
sudo chmod -R 775 /var/www/web_absensi/storage /var/www/web_absensi/bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Konfigurasi root Nginx ke `/var/www/web_absensi/public`, arahkan request PHP ke PHP-FPM, aktifkan TLS (misalnya Certbot), dan restart Nginx/PHP-FPM. Jalankan backup database terjadwal.

## Troubleshooting

- `could not find driver`: aktifkan `pdo_mysql` (atau `pdo_sqlite` saat test).
- QR/PDF gagal: pastikan ekstensi `gd`, `xml`, dan `mbstring` aktif.
- Excel gagal: aktifkan `zip` dan `xml`.
- Foto tidak tampil: jalankan `php artisan storage:link` dan pastikan `FILESYSTEM_DISK=public` bila diinginkan.
- Kamera tidak muncul: gunakan HTTPS/localhost, periksa izin browser, lalu muat ulang.
- Perubahan `.env` belum terbaca: jalankan `php artisan optimize:clear`.
- Reset data demo: `php artisan migrate:fresh --seed` (menghapus seluruh data database).
