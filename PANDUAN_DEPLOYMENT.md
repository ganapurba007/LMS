# 🚀 Panduan Lengkap Deployment LMS (Laravel & Tabler)

Dokumen ini berisi panduan komprehensif untuk proses instalasi awal (*first-time deployment*) dan pembaruan rutin (*continuous update*) aplikasi LMS ke server production (cPanel / OmniHost / Shared Hosting / VPS).

---

## 📌 1. Informasi Lingkungan & Server Production

- **Domain Utama**: `https://ruangterra.my.id`
- **Control Panel**: cPanel / OmniHost
- **PHP Version**: PHP 8.2+ (Ekstensi: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `pdo_mysql`, `Tokenizer`, `XML`, `cURL`)
- **Database Engine**: MySQL / MariaDB
- **Web Server Root**: `public_html` mengarah ke folder `public/` aplikasi Laravel
- **Repository GitHub**: `https://github.com/ganapurba007/template_tabler.git` (atau `https://github.com/ganapurba007/LMS.git`)

---

## 🛠️ 2. Langkah Deployment Pertama Kali (Initial Setup)

### A. Clone Repository ke Server
Masuk ke terminal cPanel / SSH, lalu jalankan:
```bash
cd ~
git clone https://github.com/ganapurba007/template_tabler.git lms_app
cd lms_app
```

### B. Konfigurasi File Environment (`.env`)
Salin contoh file `.env` dan sesuaikan:
```bash
cp .env.example .env
nano .env
```
Isi konfigurasi penting berikut:
```env
APP_NAME="LMS Dani"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ruangterra.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ruangter_lms
DB_USERNAME=ruangter_lms_dani
DB_PASSWORD=PasswordDatabaseAnda

# Queue & Cache (Gunakan database/file di shared hosting)
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# Konfigurasi SMTP Email (Password Reset & Notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailanda@gmail.com
MAIL_PASSWORD=app_password_16_karakter
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@ruangterra.my.id"
MAIL_FROM_NAME="LMS Dani"

# Konfigurasi Pusher (Realtime Diskusi & Notifikasi)
PUSHER_APP_ID=xxxxxx
PUSHER_APP_KEY=xxxxxx
PUSHER_APP_SECRET=xxxxxx
PUSHER_APP_CLUSTER=ap1
```

### C. Install Dependensi PHP & Generate App Key
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
```

### D. Setup Database & Migrasi
Buat database dan user di menu **cPanel MySQL Databases**, lalu berikan **ALL PRIVILEGES**. Kemudian jalankan di terminal:
```bash
php artisan migrate --seed --force
```
> **Catatan Import SQL:** Jika mengimpor dari file SQL dump phpMyAdmin, pastikan baris `CREATE DATABASE ...` dan `USE ...` dihapus/di-uncheck agar tidak memicu error `#1044 Access Denied`.

### E. Hubungkan Storage Link
```bash
php artisan storage:link
```

### F. Atur Izin Akses Folder (Permissions)
```bash
chmod -R 775 storage bootstrap/cache
```

### G. Konfigurasi Document Root Web Server
Agar web aman dan URL bersih:
- **Opsi 1 (DocumentRoot di cPanel):** Masuk ke cPanel > *Domains*, ubah Document Root domain `ruangterra.my.id` menjadi `/home/ruangter/lms_app/public`.
- **Opsi 2 (Symlink public_html jika Document Root tidak bisa diubah):**
  ```bash
  cd ~
  rm -rf public_html
  ln -s /home/ruangter/lms_app/public public_html
  ```

---

## 🔄 3. Prosedur Update Rutin (Setelah Melakukan Git Push)

Setiap kali Anda selesai mengembangkan fitur di local dan melakukan `git push origin master`, lakukan langkah berikut di server production:

```bash
# 1. Masuk ke folder proyek
cd ~/lms_app

# 2. Tarik kode terbaru dari GitHub
git pull origin master

# 3. Update package dependensi (jika ada penambahan package)
composer install --no-dev --optimize-autoloader

# 4. Jalankan migrasi database baru (jika ada)
php artisan migrate --force

# 5. Bersihkan dan buat ulang cache konfigurasi serta route
php artisan optimize:clear
php artisan optimize
php artisan view:cache

# 6. Selesai! Web sudah terupdate dengan aman.
```

---

## ⏱️ 4. Setup Cron Job & Background Tasks (cPanel)

Agar fitur pengingat deadline kuis/tugas, pembersihan token kedaluwarsa, dan notifikasi email terjadwal berjalan otomatis, tambahkan Cron Job di cPanel:

1. Buka menu **Cron Jobs** di cPanel.
2. Atur waktu: **Every Minute (`* * * * *`)**.
3. Masukkan perintah:
   ```bash
   /usr/local/bin/php /home/ruangter/lms_app/artisan schedule:run >> /dev/null 2>&1
   ```
   *(Sesuaikan path PHP cPanel dan path folder aplikasi Anda)*.

---

## 🛡️ 5. Troubleshooting & FAQ

| Gejala / Error | Penyebab | Solusi |
| :--- | :--- | :--- |
| **Error 500 / Blank White Screen** | Izin folder atau APP_KEY belum digenerate | Jalankan `chmod -R 775 storage bootstrap/cache` dan pastikan `APP_KEY` terisi di file `.env`. Cek `storage/logs/laravel.log`. |
| **Asset CSS / JS / Gambar Hilang** | Symlink storage belum terpasang atau salah URL | Jalankan `php artisan storage:link` dan pastikan `APP_URL` di `.env` sudah menggunakan `https://ruangterra.my.id`. |
| **#1044 Access Denied saat Import SQL** | Query `CREATE DATABASE` dijalankan user non-root | Hapus perintah `CREATE DATABASE` dan `USE` di bagian atas file `.sql`, lalu import langsung ke dalam database yang sudah dibuat. |
| **Debug Bar Muncul di Production** | Package `barryvdh/laravel-debugbar` aktif | Pastikan `APP_DEBUG=false` di `.env` dan jalankan `composer install --no-dev`. |
| **Email Reset Password Gagal Kirim** | Kredensial SMTP Gmail belum menggunakan App Password | Gunakan **Google App Password** 16 karakter (bukan password akun Google utama) dengan 2FA aktif. |

---

*Panduan ini dibuat otomatis dan dapat diperbarui sewaktu-waktu sesuai perkembangan arsitektur aplikasi LMS.*
