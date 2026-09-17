# SIM-ASET

Sistem Inventaris Aset Daerah berbasis Laravel untuk mengelola data barang, stok, peminjaman, pengembalian, serta laporan operasional inventaris di lingkungan pemerintah daerah.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Spatie Permission](https://img.shields.io/badge/Spatie-Permission-orange?style=for-the-badge)

---

## Tentang Proyek

Project ini adalah versi yang sedang dikembangkan untuk kebutuhan pengelolaan aset pemerintahan secara terstruktur dan audit-friendly.

Tujuan utama dari aplikasi ini adalah:

- mengelola master data barang dan instansi peminjam,
- menjaga integritas stok melalui ledger mutasi,
- membatasi akses berdasarkan role pengguna,
- mencatat seluruh proses peminjaman dan pengembalian secara terdokumentasi,
- menghasilkan laporan dan dokumen PDF yang dapat dipakai sebagai bukti operasional.

Aplikasi ini dibangun dengan Laravel 12, Bootstrap 5, serta Spatie Laravel Permission untuk kontrol akses.

---

## Status Versi Saat Ini

Versi project yang ada saat ini sudah mencakup fitur inti berikut:

- autentikasi pengguna dengan Laravel Breeze,
- role management dengan 3 level utama: Super Admin, Admin, dan Staff Logistik,
- manajemen barang dan instansi peminjam,
- pencatatan mutasi stok dan Kartu Stok,
- transaksi peminjaman dan pengembalian barang,
- validasi keamanan input dan format data,
- laporan bulanan dan PDF,
- akses fitur berdasarkan peran akun.

Dokumen lanjutan mengenai roadmap dan konteks proyek tersedia di folder docs.

---

## Struktur Role Saat Ini

### Super Admin
- mengakses semua modul operasional,
- dapat mengelola pengguna dan role,
- mengatur konfigurasi akses sistem.

### Admin
- dapat mengakses modul master data, stok, peminjaman, report,
- tidak dapat mengelola role atau menambah user baru.

### Staff Logistik
- dapat membuat dan melihat transaksi peminjaman yang dibuat dirinya,
- tidak dapat mengelola user maupun role,
- tidak dapat melihat seluruh transaksi milik user lain.

---

## Alur Kerja Aplikasi

Alur kerja aplikasi yang benar sesuai versi sekarang adalah sebagai berikut:

```text
Login
  ↓
Siapkan Master Data
  ├─ Kategori
  ├─ Barang
  ├─ Instansi / Peminjam
  └─ Lokasi (jika dibutuhkan)
  ↓
Input Stok Awal / Mutasi Stok
  ↓
Buat Transaksi Peminjaman
  ├─ pilih peminjam
  ├─ pilih barang
  ├─ pilih qty
  └─ simpan transaksi
  ↓
Validasi stok tersedia
  ↓
Transaksi aktif / status dipinjam
  ↓
Proses pengembalian barang
  ├─ pengembalian sebagian
  └─ pengembalian penuh
  ↓
Update ketersediaan stok
  ↓
Laporan & PDF
```

---

## Arsitektur Aplikasi

Aplikasi dibangun menggunakan pola arsitektur MVC (Model-View-Controller) dengan Service Layer untuk memisahkan logika bisnis yang kompleks dari Controller.

### Tech Stack
- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Bootstrap 5.x, Blade Templating, TomSelect/Select2, Chart.js
- **Database**: SQLite (Development), MySQL/MariaDB (Production)
- **Otentikasi & Otorisasi**: Laravel Breeze, Spatie Laravel Permission

### Struktur Database & ERD Context

Berikut adalah gambaran relasi entitas utama dalam sistem:
- **`users`**: Menyimpan data login dan role (Super Admin, Admin, Staff Logistik).
- **`categories`**: Klasifikasi master barang (memiliki `prefix` untuk generate SKU).
- **`items`**: Master data barang, terhubung dengan `categories`. Menyimpan total qty dan available qty.
- **`borrowers`**: Entitas instansi/pihak luar yang meminjam barang.
- **`loans`**: Data header transaksi peminjaman (terhubung ke `users` pembuat dan `borrowers`).
- **`loan_items`**: Detail barang apa saja yang dipinjam beserta jumlahnya (terhubung ke `loans` dan `items`).
- **`stock_movements`**: Ledger mutasi stok (in/out), terhubung ke `items` dan `users`, tidak pernah di-delete untuk menjaga audit trail.

### Alur Proses Bisnis (Service Layer)

Project ini memisahkan logika bisnis dari controller agar lebih aman dan terstruktur.

```text
Browser
  ↓
Routes
  ↓
Controller
  ↓
Service Layer
  ├─ LoanService (Transaksi Peminjaman & Pengembalian)
  └─ StockService (Mutasi & Ledger Stok)
  ↓
Model / Database
```

- `LoanService` menangani pembuatan transaksi peminjaman dan proses pengembalian.
- `StockService` menangani mutasi stok dan perubahan saldo barang.
- Penggunaan **Database Transactions** dan `lockForUpdate()` dipakai untuk mencegah race condition saat mutasi/transaksi terjadi bersamaan.
- Perubahan ketersediaan stok (`available_qty`) terjadi otomatis beriringan dengan `stock_movements` atau status dari `loan_items`.

---

## Fitur Utama yang Sudah Ada

### 1. Autentikasi & Role Access
- login/logout menggunakan Laravel Breeze,
- redirect langsung ke login untuk route yang tidak boleh diakses publik,
- route register dan forgot-password diarahkan ke halaman login,
- akses fitur dibatasi berdasarkan role.

### 2. Master Data Barang
- input barang baru,
- otomatis generate SKU berdasarkan kategori,
- validasi input nama barang untuk mencegah karakter berbahaya,
- perubahan total stok wajib tetap konsisten dengan barang yang masih dipinjam.

### 3. Master Data Instansi / Peminjam
- data instansi, PIC, kontak, dan alamat,
- validasi nama instansi agar aman dari karakter berbahaya,
- tidak ada akses publik untuk menambah data tanpa login.

### 4. Kartu Stok / Stock Movement
- mencatat semua mutasi barang masuk, keluar, rusak, dan hilang,
- format referensi wajib mengikuti pola: `BAST/YYYY/MM/NNN`,
- setiap mutasi terdokumentasi dengan keterangan dan user yang membuatnya.

### 5. Peminjaman Barang
- satu transaksi dapat berisi beberapa item,
- validasi stok yang tersedia sebelum transaksi disimpan,
- catatan peminjaman bisa diisi pada form,
- status transaksi aktif sampai seluruh item dikembalikan.

### 6. Pengembalian Barang
- mendukung pengembalian sebagian maupun penuh,
- update available_qty dilakukan setelah pengembalian,
- status otomatis berubah menjadi completed ketika semua item sudah kembali.

### 7. Laporan dan PDF
- laporan rekapitulasi bulanan,
- laporan stok / mutasi,
- export PDF untuk transaksi dan laporan.

### 8. Keamanan Input
- sanitasi XSS awal pada middleware,
- validasi FormRequest untuk pencegahan payload berbahaya,
- validasi format nomor referensi dan nama entitas,
- filter terhadap karakter HTML/script yang tidak aman.

---

## Preview Fitur Utama

Berikut beberapa preview halaman utama dari aplikasi yang tersedia di folder docs/images dengan versi final yang dimaksudkan untuk dokumentasi.

### Dashboard
![Preview Dashboard](docs/images/dashboardv3.png)

### Master Barang
![Preview Master Barang](docs/images/itemsv3.png)

### Daftar Peminjam
![Preview Daftar Peminjam](docs/images/borrowersv3.png)

### Daftar Peminjaman
![Preview Daftar Peminjaman](docs/images/loansv3.png)

### Form Peminjaman
![Preview Form Peminjaman](docs/images/loan-createv3.png)

### Kartu Stok / Mutasi
![Preview Kartu Stok](docs/images/stock-movementsv3.png)

### Laporan
![Preview Laporan](docs/images/reportsv2.png)

---

## Proses Kerja yang Benar di Aplikasi Saat Ini

### A. Persiapan data awal
1. Login sebagai Super Admin atau Admin.
2. Siapkan kategori barang.
3. Tambahkan barang baru.
4. Jika diperlukan, isi stok awal.
5. Tambahkan data instansi peminjam.

### B. Membuat peminjaman
1. Buka menu peminjaman.
2. Pilih instansi peminjam.
3. Pilih item yang dipinjam.
4. Masukkan jumlah.
5. Isi catatan jika ada.
6. Sistem akan memvalidasi ketersediaan stok.
7. Simpan transaksi.

### C. Pengembalian barang
1. Buka detail transaksi peminjaman.
2. Masukkan jumlah barang yang dikembalikan.
3. Sistem akan mengecek sisa hutang item.
4. Simpan pengembalian.
5. Stok tersedia akan bertambah kembali.

### D. Monitoring data
1. Periksa Kartu Stok untuk riwayat mutasi.
2. Gunakan laporan untuk melihat rekap bulanan.
3. Pastikan semua perubahan stok berkaitan dengan riwayat audit yang jelas.

---

## Rute Akses Saat Ini

Beberapa rute utama yang ada dalam aplikasi:

```text
/login
/logout
/dashboard
/items
/borrowers
/stocks
/reports
/users
```

Akses route dikelola berdasarkan role:

- `items`, `borrowers`, `stocks`, `reports` -> Admin dan Super Admin
- `users` -> Super Admin saja
- `loans` -> semua user login, namun Staff Logistik hanya melihat transaksi yang dibuat dirinya

---

## Data Awal (Seeder)

Saat menjalankan seeder, aplikasi akan mengisi role dan akun default seperti berikut:

```text
Email: superadmin@pemda.go.id
Password: password123
Role: Super Admin
```

```text
Email: admin@pemda.go.id
Password: password123
Role: Admin
```

```text
Email: staff@pemda.go.id
Password: password123
Role: Staff Logistik
```

> Kredensial ini hanya untuk kebutuhan development dan testing. Jangan dipakai di lingkungan produksi.

---

## Persyaratan Sistem

- PHP 8.2+
- Composer
- Node.js + NPM
- Database Laravel yang didukung (SQLite atau MySQL)

---

## Instalasi Cepat

### 1. Clone project

```bash
git clone https://github.com/Aiyub150/SIM-ASET.git
cd web_inventaris
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Konfigurasi environment

```bash
copy .env.example .env
```

atau di PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5. Generate key

```bash
php artisan key:generate
```

### 6. Konfigurasi database

Untuk SQLite:

```bash
touch database/database.sqlite
```

Pastikan `.env` berisi:

```env
DB_CONNECTION=sqlite
```

Atau untuk MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_inventaris
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Jalankan migrasi

```bash
php artisan migrate
```

### 8. Jalankan seeder

```bash
php artisan db:seed
```

### 9. Jalankan aplikasi

```bash
php artisan serve
```

Lalu buka:

```text
http://127.0.0.1:8000
```

---

## Dokumentasi Pendukung

Dokumen referensi proyek saat ini berada di folder docs:

- docs/Projectcontext.md
- docs/Ecosystem.md
- docs/Futureroadmap.md
- docs/Refactorndebug.md

Dokumen-dokumen ini menjelaskan konteks bisnis, arsitektur, roadmap, dan catatan refactor yang sudah diproses pada project.

---

## Catatan Teknis

- frontend menggunakan Bootstrap 5 dan Blade,
- transaksi dibuat melalui service layer agar lebih aman dan dapat dipantau,
- validasi input dan sanitasi XSS diterapkan agar data tidak masuk dengan karakter mencurigakan,
- mutasi stok merupakan satu-satunya mekanisme yang digunakan untuk perubahan saldo barang,
- struktur project fokus pada audit trail, keamanan data, dan kontrol akses berbasis role.

---

## Lisensi

Project ini dibuat untuk kebutuhan internal sistem inventaris aset daerah dan dapat disesuaikan dengan kebijakan organisasi atau instansi yang menggunakannya.

---

## Pengembang

Aiyub Heriyanto

GitHub: https://github.com/Aiyub150

 # #   I n t e g r a s i   &   A P I   E k s t e r n a l 
 S I M - A S E T   m e n g g u n a k a n   A P I   d a r i   h t t p s : / / a p i . k e m e n d e s a . l i n k / l i b u r - n a s i o n a l   u n t u k   m e n a r i k   d a t a   H a r i   L i b u r   N a s i o n a l   y a n g   d i t a m p i l k a n   p a d a   D a s h b o a r d   d a n   M o d a l   K a l e n d e r .   A P I   i n i   d i - c a c h e   s e l a m a   1   h a r i   ( 2 4   j a m )   d i   s i s i   s e r v e r   u n t u k   m e n g h e m a t   _ b a n d w i d t h _   d a n   m e m i n i m a l i s i r   _ d o w n t i m e _ . 
 
 # #   P e r s y a r a t a n   F i t u r   S c a n n e r   ( K a m e r a ) 
 F i t u r   p e m i n d a i a n   B a r c o d e   /   Q R   C o d e   p a d a   f o r m   p e m i n j a m a n   m e n g a n d a l k a n   A P I   g e t U s e r M e d i a   b a w a a n   b r o w s e r .   * * A g a r   b r o w s e r   m e n g i z i n k a n   a k s e s   k e   p e r a n g k a t   k a m e r a ,   a p l i k a s i   S I M - A S E T   h a r u s   d i j a l a n k a n   d i   a t a s   k o n e k s i   y a n g   a m a n   ( H T T P S ) * * .   J i k a   A n d a   m e n g a k s e s n y a   s e c a r a   l o k a l   d i   m e s i n   p e n g e m b a n g a n   ( m i s a l :   h t t p : / / l o c a l h o s t ) ,   b r o w s e r   p a d a   u m u m n y a   m e m b e r i k a n   p e n g e c u a l i a n   _ s e c u r e - c o n t e x t _ .   N a m u n ,   j i k a   A n d a   m e n g a k s e s n y a   d a r i   j a r i n g a n   L A N   ( h t t p : / / 1 9 2 . 1 6 8 . x . x ) ,   f i t u r   k a m e r a   k e m u n g k i n a n   b e s a r   a k a n   d i t o l a k   o l e h   b r o w s e r   s e l u l e r   m a u p u n   d e s k t o p .   G u n a k a n   S S L / T L S   p a d a   t a h a p a n   _ p r o d u c t i o n _   a t a u   _ s t a g i n g _ .  
 