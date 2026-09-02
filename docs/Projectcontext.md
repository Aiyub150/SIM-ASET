# Project Context: SIM ASET (Sistem Inventaris Aset Daerah)

## 1. Project Story (Latar Belakang)
Sistem ini dibangun untuk mengatasi masalah klasik pada instansi pemerintahan: **hilangnya jejak audit fisik aset negara**. 
Dalam audit pemerintahan (seperti oleh BPK), perbedaan antara catatan sistem dan fisik di gudang tanpa adanya riwayat mutasi yang jelas dikategorikan sebagai temuan pelanggaran hukum. Banyak sistem inventaris amatir gagal karena mengizinkan Admin mengubah jumlah stok secara langsung (via operasi `UPDATE` sederhana) atau menghapus data transaksi (via `DELETE`). 

SIM ASET dirancang sebagai *Enterprise Resource Planning* (ERP) berskala mikro yang menolak keras praktik manipulasi data. Sistem memisahkan secara tegas antara **Data Master** (Barang & Instansi) yang dapat diperbarui, dengan **Data Transaksi** (Buku Besar Mutasi & Peminjaman) yang bersifat *Immutable* (permanen dan hanya dapat ditambahkan/*append-only*).

## 2. Project Goal (Tujuan Utama)
1. **Audit-Ready Compliance:** Menciptakan jejak rekam aset yang 100% dapat diaudit. Setiap penambahan atau pengurangan fisik barang harus terikat pada Nomor Surat/BAST yang sah.
2. **Race-Condition Prevention:** Memastikan integritas data saat diakses secara konkuren (banyak petugas melakukan peminjaman barang yang sama pada detik yang sama).
3. **Role-Based Access Control (RBAC) Strict Isolation:** Memisahkan wewenang secara definitif antara `Super Admin` (pengelola data master dan mutasi permanen) dengan `Staff Logistik` (pencatat peminjaman rutin).
4. **Automated Document Generation:** Menyediakan *output* dokumen legal (Berita Acara Serah Terima) dalam format PDF yang presisi.

## 3. Project Process & Architecture (Alur & Arsitektur Sistem)
Sistem ini dibangun menggunakan **Laravel 12** dan antarmuka **Bootstrap 5**.

### A. Core Modules & Data Flow
*   **Master Data (`items`, `borrowers`):** 
    *   Dikelola melalui operasi CRUD standar (tanpa fitur hapus permanen untuk mencegah *orphan data*).
    *   **Aturan Kritis:** Saat entitas `items` baru dibuat, stok fisik (`total_qty` & `available_qty`) **WAJIB bernilai 0**. Tidak boleh ada *input* kuantitas pada saat inisialisasi master data.
*   **Stock Ledger / Buku Besar (`stock_movements`):**
    *   Satu-satunya pintu masuk untuk mengubah kuantitas barang.
    *   Tipe mutasi: `in` (pengadaan), `out` (hibah/keluar), `broken` (rusak), `lost` (hilang).
    *   Kolom `balance_before` dan `balance_after` mengunci jejak perhitungan untuk mencegah intervensi via SQL murni (*database tampering*).
*   **Loan Lifecycle (`loans`, `loan_items`):**
    *   Peminjaman mengunci stok berdasarkan ketersediaan (`available_qty`).
    *   Mendukung pengembalian parsial (sebagian). Transaksi hanya berubah status menjadi `completed` jika sisa hutang semua barang di dalam *invoice* tersebut mencapai angka 0.

### B. Engineering Standards & Defense Mechanisms
*   **Pessimistic Locking:** Menggunakan `lockForUpdate()` pada *Service layer* (`StockService`, `LoanService`) untuk mencegah *Race Condition*.
*   **Fat Service, Skinny Controller:** Logika bisnis komputasi stok dipisahkan ke kelas *Service*, sedangkan *Controller* murni mengatur *HTTP request/response*.
*   **Defense in Depth:** Validasi berlapis dimulai dari HTML `min/max` atribut, form Request validation (mencegah *payload tampering*), hingga Exception lemparan dari Service layer.
*   **N+1 Query Elimination:** Penggunaan `with()` secara mutlak pada setiap *query Eloquent* yang berelasi pada halaman *index* dan *show*.

---

## 4. References & Benchmarks
Untuk *AI agent* atau *developer* selanjutnya yang ingin mengembangkan fitur ini (seperti penambahan depresiasi aset, *barcode scanning*, atau *multi-warehouse*), berikut adalah standar repositori *open-source* Laravel yang harus dipelajari dan dijadikan tolak ukur:

1.  **[Snipe-IT (Asset Management)](https://github.com/snipe/snipe-it)**
    *   *Relevansi:* Ini adalah standar industri (Gold Standard) untuk sistem manajemen aset TI berbasis *open-source* yang ditulis dengan Laravel.
    *   *Fokus Pelajaran:* Perhatikan bagaimana Snipe-IT menangani *Check-in/Check-out* barang, pelacakan histori aset berdasarkan entitas pengguna, serta struktur *database* untuk *custom fields*.
2.  **[Akaunting](https://github.com/akaunting/akaunting)**
    *   *Relevansi:* Perangkat lunak akuntansi dengan Laravel.
    *   *Fokus Pelajaran:* Pelajari cara Akaunting mengelola arsitektur *Ledger* (Buku Besar) ganda, sistem pelaporan (Reports), dan modul perlindungan transaksi yang tidak bisa dimodifikasi.
3.  **[Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)**
    *   *Relevansi:* Panduan struktur kode bersih.
    *   *Fokus Pelajaran:* Implementasi *Single Responsibility Principle* (SRP), penggunaan *FormRequests*, dan ekstraksi logika ke dalam *Action/Service classes*.
4.  **[Spatie Laravel-Permission](https://github.com/spatie/laravel-permission)**
    *   *Relevansi:* Pustaka inti RBAC yang digunakan dalam SIM ASET.
    *   *Fokus Pelajaran:* Praktik terbaik menerapkan *Middleware* berbasis otorisasi dan penggunaan *Blade Directives* untuk UI adaptif.

**Catatan untuk AI Agent Selanjutnya:**