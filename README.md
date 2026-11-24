# Aplikasi Catatan Pribadi

Aplikasi latihan Laravel 30 Hari untuk mengelola catatan pribadi dengan autentikasi, lampiran, arsip, pencarian, serta impor dan ekspor CSV.

## Fitur

- Daftar, masuk, dan keluar menggunakan email.
- Catatan privat per pengguna dengan tambah, lihat, edit, dan hapus.
- Validasi catatan dan lampiran JPG, PNG, PDF, atau TXT (maksimal 2 MB); lampiran disimpan privat dan hanya dapat diunduh pemilik.
- Arsip dan pemulihan catatan.
- Pencarian, pengurutan terbaru/terlama, pagination, dan jumlah catatan.
- Ekspor catatan aktif ke CSV dan impor maksimal 5.000 catatan per file CSV.
- Konfirmasi hapus dengan modal Bootstrap dan notifikasi flash.

## Menjalankan Secara Lokal

1. Pasang dependensi PHP: `composer install`.
2. Salin `.env.example` menjadi `.env`, lalu atur koneksi database.
3. Jalankan `php artisan key:generate`.
4. Jalankan migrasi: `php artisan migrate`.
5. Pasang dan bangun aset: `npm install` lalu `npm run build`.
6. Jalankan aplikasi: `php artisan serve`.
7. Daftar melalui `/register`, lalu kelola catatan di `/catatan`.

Jangan unggah `.env`, `vendor/`, `node_modules/`, log, atau file lokal dari `storage/` ke repositori.

## Teknologi

- Laravel 13
- PHP 8.3
- SQLite atau MySQL
- Bootstrap 5
