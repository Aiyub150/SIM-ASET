# SIM-ASET: Technical Documentation

[![Version](https://img.shields.io/badge/version-1.1.0-blue.svg)](https://github.com/Aiyub150/SIM-ASET)
[![Framework](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Security](https://img.shields.io/badge/security-OWASP%20Hardened-success.svg)]()

SIM-ASET adalah sistem informasi manajemen inventaris aset daerah yang dikembangkan menggunakan kerangka kerja Laravel. Repositori ini berisi dokumentasi arsitektur, struktur basis data, spesifikasi sistem, serta panduan teknis operasional (rilis stabil mutakhir: **Versi 1.1.0**).

## Spesifikasi Teknis (Tech Stack)

*   **Backend Framework**: Laravel 12.x
*   **Runtime**: PHP 8.2+
*   **Frontend**: Bootstrap 5.x, Vanilla JavaScript
*   **Database Engine**: Mendukung SQLite (lokal/development) dan MySQL/MariaDB (production)
*   **Authentication & Authorization**: Laravel Breeze & Spatie Laravel Permission
*   **PDF Generation**: barryvdh/laravel-dompdf
*   **Charts & Visualization**: Chart.js

---

## Arsitektur Sistem

Sistem ini menerapkan pola desain **MVC (Model-View-Controller)** yang dikombinasikan dengan **Service Layer Pattern** untuk mengisolasi *business logic* dari kontroler. 

### Alur Request (Request Lifecycle)
1. **Routing & Middleware**: Request masuk melalui `routes/web.php` dan disaring oleh middleware `auth` serta `role` (Spatie). Sistem menerapkan isolasi akses ketat (misal: `Staff Logistik` hanya memiliki akses ke rute peminjaman yang diotorisasi khusus untuk ID pengguna mereka).
2. **Form Request Validation**: Semua input mutasi (*POST/PUT*) divalidasi ketat melalui instance `FormRequest` bawaan Laravel untuk memfilter *payload* yang tidak valid dan mencegah *Cross-Site Scripting* (XSS).
3. **Controller**: Bertugas mengelola siklus penerimaan *request HTTP*, memanggil *Service Layer*, dan menyusun *response* (Instance View atau Object JSON).
4. **Service Layer**: Entitas inti logika (*business rules*) dipisahkan ke dalam folder `app/Services/` (contoh: `LoanService.php`, `StockService.php`).
5. **Database Transaction**: Seluruh operasi yang melibatkan multi-tabel dibungkus di dalam `DB::transaction` guna menjamin atomisitas transaksi. Jika *query* ke-2 gagal, perubahan *query* ke-1 akan di-*rollback*.
6. **Pessimistic Locking**: Mengimplementasikan konstruksi `lockForUpdate()` saat mengeksekusi kueri pada tabel `items`. Hal ini ditujukan untuk mencegah timbulnya *race condition* (inkonsistensi kalkulasi kolom `available_qty` jika dua atau lebih sesi sistem melakukan peminjaman/mutasi pada objek aset yang sama di milidetik yang bersamaan).

---

## Struktur Basis Data (Entity Relationship)

Skema sistem bergantung pada relasi transaksional yang berpusat pada integritas pergerakan stok barang:

*   **`users`**: Entitas pengguna yang direlasikan ke polimorfik tabel peran (Super Admin, Admin, Staff Logistik).
*   **`categories`**: Klasifikasi master aset. Memiliki kolom `prefix` untuk referensi pembentukan nomor seri / SKU secara dinamis saat instansiasi `items` baru.
*   **`items`**: Entitas barang/aset. Memiliki kolom fisik `total_qty` (akumulasi absolut) dan `available_qty` (stok sisa mutakhir yang bebas dipinjamkan).
*   **`borrowers`**: Entitas data peminjam institusi. Memuat meta spasial (`latitude` dan `longitude`).
*   **`stock_movements`**: Ledger/Buku Besar berbasis *append-only* (tidak boleh dihapus). Menampung log riwayat mutasi (in, out, broken, lost). Memiliki foreign key ke `items` dan `users` (sebagai pelaksana).
*   **`loans` & `loan_items`**: Header dan detail transaksi peminjaman (tipe *One-to-Many*). `loan_items` memiliki kolom state `return_qty` untuk mengakomodasi alur pengembalian secara parsial. State header `loans` dinyatakan selesai (*completed*) apabila agregasi nilai total `return_qty` di *child-table* seimbang dengan total `qty` pinjam.

---

## Integrasi & Penggunaan API Eksternal

### 1. Kemendesa API (Libur Nasional)
*   **Fungsi**: Restorasi data agregat hari libur nasional dan cuti bersama pada modul *Dashboard* untuk menyajikan konteks operasional.
*   **Implementasi**: 
    *   Koneksi HTTP Endpoint: `https://api.kemendesa.link/libur-nasional/api/holidays/{tahun}.json`
    *   Panggilan API diinkapsulasi dalam kelas `CalendarService` di sisi server (Backend PHP) menggunakan fasad `Http` guna menghindari pembatasan *CORS* pada *Browser*.
    *   **Caching Strategy**: JSON Payload di-*cache* (diingat dalam memori sistem via `Cache::remember`) selama *Time-to-Live* (TTL) 24 jam dengan struktur *key* spesifik per kombinasi bulan/tahun (cth: `holidays_2026_9`). Hal ini sangat krusial untuk menekan *request limit* HTTP dan menjamin redundansi (dasbor tetap menyala) tatkala node server API Kemendesa mengalami *downtime*.

### 2. Leaflet.js (Geospasial / Web Mapping)
*   **Fungsi**: Membaca, menentukan, dan menyimpan vektor koordinat (*latitude/longitude*) dari entitas institusi `borrowers`.
*   **Implementasi**:
    *   Distribusi modul JS dan CSS dilayani secara statis melalui CDN eksternal *unpkg*.
    *   **Data Input (`create/edit`)**: Rutinitas JavaScript mengikat *listener* *event* `dragend` pada L.marker dan *event* `click` pada grid peta untuk mengekstrak objek `latlng` yang langsung dialirkan mutasinya ke *hidden DOM input field*.
    *   **Data Render (`index`)**: Mencegah pemuatan masif lapisan *TileLayer* pada seluruh indeks tabel (penyebab utama *out of memory / memory leak*). Leaflet hanya diinisialisasi melalui injeksi DOM (*mount*) di dalam *Lifecycle event Modal Bootstrap* (`shown.bs.modal`) secara sekuensial (*on-demand*) pada baris spesifik yang ditekan pengguna.

### 3. MediaDevices API / WebRTC (Barcode Scanner)
*   **Fungsi**: Akuisisi data string SKU perangkat keras pada antarmuka *Loans* tanpa instrumen input manual.
*   **Implementasi**:
    *   Memanfaatkan antarmuka API HTML5 `navigator.mediaDevices.getUserMedia()` untuk integrasi aliran video *real-time*.
    *   **Syarat Infrastruktur / Policy**: API modern ini diikat oleh standard keamanan W3C yang mewajibkan protokol jaringan **HTTPS (Secure Context)**. Browser modern akan secara sepihak memblokir izin akses *hardware stream* jika layanan diakses melalui protokol tidak aman (*HTTP*) di luar ranah `localhost`. *Deployment* ke fase *Staging* atau *Production* harus difasilitasi oleh sertifikat *SSL/TLS*.

---

## Preview Antarmuka Sistem

Koleksi pratinjau visual dari modul operasional di dalam aplikasi:

### Dashboard
![Preview Dashboard](docs/images/dashboardv3.png)

### Master Barang
![Preview Master Barang](docs/images/itemsv3.png)

### Daftar Peminjam
![Preview Daftar Peminjam](docs/images/borrowersv3.png)

### Map Peminjam
![Preview Map Peminjam](docs/images/mappopupv3.png)

### Daftar Peminjaman
![Preview Daftar Peminjaman](docs/images/loansv3.png)

### Form Peminjaman
![Preview Form Peminjaman](docs/images/loan-createv3.png)

### Kartu Stok / Mutasi
![Preview Kartu Stok](docs/images/stock-movementsv3.png)

### Laporan
![Preview Laporan](docs/images/reportsv3.png)

---

## Keamanan Data (Security & Boundaries)

1.  **Data Isolation Constraint (Tenant-like Boundaries)**:
    Pengguna yang diikat pada peran `Staff Logistik` dipaksa patuh pada klausa kueri Eloquent global berbasis *identifier* (e.g., `where('user_id', auth()->id())`) pada lapis *Controller*. Skema arsitektur ini memisahkan blok data yang memungkinkan Staf A dicegah secara *backend* untuk melakukan *read, update,* maupun injeksi memanipulasi *ID Payload* peminjaman milik Staf B di *route endpoint* yang terekspos manapun.
2.  **Cross-Site Scripting (XSS) Mitigation**:
    *   Render DOM Sinkronus: *Output buffer* melalui mesin Blade ditangani oleh sintaks eksklusif `{{ }}` yang secara internal mengeksekusi instruksi `htmlspecialchars` bawaan PHP.
    *   Render DOM Asinkronus (AJAX): Respon tipe `application/json` yang dikonsumsi oleh blok perulangan JavaScript disanitasi menggunakan fungsi implementasi *context-aware escaping* yang merubah kode injeksi skrip (`<`, `>`, `&`, `"`, `'`) menjadi format HTML *entities* yang aman (*harmless*) sebelum disisipkan melalui *property* `innerHTML`.
    *   Middleware Sanitasi `XssSanitizer`: Memangkas script berbahaya dan entity encoding berbahaya pada seluruh request mutasi.
3.  **OWASP Security Headers**:
    Mengimplementasikan `SecurityHeadersMiddleware` yang menyuntikkan standar proteksi web:
    *   `X-Frame-Options: SAMEORIGIN` (mitigasi Clickjacking)
    *   `X-Content-Type-Options: nosniff` (mitigasi MIME confusion)
    *   `Referrer-Policy: strict-origin-when-cross-origin`
    *   `Permissions-Policy: camera=(self), microphone=(), geolocation=(), payment=()`
    *   `Strict-Transport-Security (HSTS)` saat HTTPS aktif
    *   `Content-Security-Policy (CSP)` membatasi script dan resource hanya ke internal dan CDN terdaftar
    *   Penyembunyian header identifikasi server `X-Powered-By`.
4.  **Brute-Force & Rate Limiting**:
    Pembatasan ketat percobaan autentikasi via middleware `throttle:5,1` pada rute `POST /login`.
5.  **Audit Trail & Trusted Proxies**:
    *   Konfigurasi `trustProxies` resmi di `bootstrap/app.php` untuk mencegah spoofing header `X-Forwarded-For`.
    *   `ActivityLogger` mencatat IP asli pengguna secara akurat ke tabel `activity_logs`.

---

## Catatan Rilis & Pembaruan (Changelog)

### [1.1.0] - 2026-10-12
* **Fitur Baru (Super Admin)**:
  * **Modul Log Server**: Pemantau riwayat log aplikasi Laravel secara live dengan filter tingkatan log (Info, Warning, Error), unduh berkas, dan utilitas pembersihan log berbasis dialog SweetAlert2. Dilengkapi resolver IP pengguna riil (bukan IP server proxy).
  * **Modul Log History (Audit Trail)**: Pencatatan otomatis setiap aktivitas penting sistem (Login, Logout, Peminjaman, Mutasi Stok, Barang, Kategori, User) dengan inspeksi detail payload JSON.
* **Peningkatan Keamanan (OWASP Compliance)**:
  * Implementasi `SecurityHeadersMiddleware` (CSP, HSTS, X-Frame-Options, nosniff, dll.).
  * Mitigasi BFCache & History Leakage (CWE-525) dengan *Strict Anti-Cache Headers* (`no-store, no-cache, must-revalidate`) dan `Clear-Site-Data: "cache", "storage"` saat logout.
  * Invalidation client-side via listener `pageshow` di master layout yang memaksa reload/redirect ke `/login` jika tombol back/undo ditekan pasca logout.
  * Proteksi DoS & Brute-Force melalui *Rate Limiting* menyeluruh pada Login, Lupa Password, Ekspor PDF Laporan, Cetak Label Barcode, Operasi Log Server, dan Mutasi Transaksi Operasional.
  * Konfigurasi *trusted proxies* untuk integritas evaluasi IP pengguna.
  * Patch keamanan dependensi kritis (`shell-quote` dan `league/commonmark`).
  * Automated regression test suite untuk seluruh matriks otorisasi RBAC (36 endpoint) dan Rate Limiting / Anti-Cache (61 feature tests).
* **Peningkatan Antarmuka & UX**:
  * Tampilan thumbnail foto profil/avatar pengguna (36px) pada tabel manajemen pengguna.
  * Standarisasi cetak label inventaris berbasis 2D QR Code.
  * Perbaikan interaktivitas accordion FAQ dan keseragaman kartu flexbox pada halaman Buku Panduan (`/guide`) serta template PDF resmi.

### [1.0.0] - 2026-09-01
* Rilis perdana sistem SIM-ASET dengan fitur inti: Dashboard ringkasan aset, Master Barang & SKU otomatis, Peminjam & Peta Interaktif Leaflet, Transaksi Peminjaman multi-barang, Kartu Stok / Ledger Mutasi, Laporan Bulanan & Ekspor PDF, serta Manajemen Peran (Super Admin, Admin, Staff Logistik).

---

## Panduan Pemasangan (Environment Setup)

1. **Clone Repositori & Instalasi Dependensi Inti**:
   ```bash
   git clone https://github.com/Aiyub150/SIM-ASET.git
   cd SIM-ASET
   composer install
   npm install && npm run build
   ```

2. **Konfigurasi Parameter Lingkungan (*Environment*)**:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   *(Penyesuaian `DB_CONNECTION` dapat diedit pada konfigurasi `.env`. Lingkungan lokal akan memanfaatkan SQLite).*

3. **Migrasi Schema & Bootstraping Metadata (Seeding)**:
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```
   *(Rutinitas `seeder` akan membangun indeks izin peran Spatie dan menyuntikkan 3 pengguna penguji/administrator default).*

4. **Eksekusi Layanan Web Server**:
   ```bash
   php artisan serve
   ```
   *(Pekerja layanan mendengarkan koneksi pada *port* standar HTTP lokal `http://127.0.0.1:8000`).*