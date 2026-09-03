# Rencana Implementasi Barcode & QR Code (SIM-ASET)

Dokumen ini merangkum rancangan arsitektur dan alur kerja (workflow) untuk integrasi fitur pemindaian optik (Barcode 1D & QR Code 2D) pada aplikasi SIM-ASET. Fitur ini dirancang untuk mempercepat proses *check-out* (peminjaman) dan *check-in* (pengembalian) dengan tetap mempertahankan keandalan operasional jika perangkat keras mengalami kendala.

---

## 1. Pendekatan Format Visual (1D & 2D)
Sistem akan memfasilitasi dua jenis *output* label berdasarkan SKU (Stock Keeping Unit) barang untuk mengakomodasi berbagai skenario di lapangan:

*   **Barcode Garis Klasik (1D):** 
    Standar industri yang sering digunakan oleh instansi pemerintah di lingkungan kantor/gudang pusat. Mudah dicetak dan cepat dibaca oleh perangkat **USB Barcode Scanner** standar.
*   **QR Code (2D):**
    Opsi modern yang lebih ringkas. Sangat berguna untuk kondisi lapangan atau gudang luar di mana perangkat PC tidak tersedia. QR Code lebih mudah dipindai menggunakan **Kamera Ponsel (Smartphone) atau Webcam** bawaan laptop dari jarak dan sudut yang lebih fleksibel.

## 2. Modul Cetak Label (Backend to Physical)
*   **Fitur:** Tombol "Cetak Label" di halaman Master Barang.
*   **Alur:** Super Admin memilih beberapa barang, sistem menggunakan pustaka seperti `milon/barcode` (untuk 1D) atau `simplesoftwareio/simple-qrcode` (untuk 2D) untuk men-*generate* dokumen PDF berisi label berjejer.
*   **Output:** Stiker label yang mencantumkan *Barcode/QR Code*, SKU, dan Nama Barang.

---

## 3. Alur Peminjaman Otomatis & Hybrid (Check-out)
Halaman `loans.create` akan dirombak menjadi sistem *Point of Sale* (POS) hibrida.

*   **Input Area Utama (Scan/Ketik):** Di bagian atas keranjang barang, terdapat satu kolom input besar dengan fokus (autofocus) berlabel: **"Scan Barcode / Ketik SKU Di Sini"**.
*   **Pemindaian Cepat:** Saat staf menembak label dengan *scanner* (atau via kamera web), kode SKU akan terisi dan otomatis ter-*submit* (Enter) oleh perangkat. 
*   **AJAX Listener:** *JavaScript listener* menangkap *event* Enter tersebut, melakukan *fetch* data barang ke *server* di latar belakang, dan otomatis menambahkan baris barang baru ke dalam keranjang atau menambah *Qty* (+1) jika barang sudah ada di keranjang.
*   **Sistem Fallback (Manual & Dropdown):** 
    1.  Jika label fisik rusak/sobek, staf dapat **mengetik kode SKU secara manual** di kolom tersebut layaknya kasir minimarket.
    2.  Fitur lama **"Tambah Barang Lainnya"** (menggunakan *dropdown select*) TIDAK DIHAPUS, melainkan diletakkan di bawah sebagai opsi alternatif mutlak jika seluruh sistem pemindai bermasalah atau staf lupa membawa barang ke meja admin.

## 4. Alur Pengembalian Otomatis (Check-in)
Halaman detail peminjaman (`loans.show`) akan menerapkan logika *listener* yang sama untuk memproses pengembalian.

*   **Alur:** Saat staf memindai *barcode* barang yang dikembalikan, *JavaScript* akan mencocokkan SKU tersebut dengan daftar hutang barang di dalam transaksi tersebut.
*   **Eksekusi:** Sistem otomatis mengisi angka pada kolom "Jumlah Kembali", mengurangi risiko kesalahan manusia dalam memasukkan jumlah barang yang lunas. 
*   **Fallback:** Form pengembalian manual berbasis *dropdown* tetap dipertahankan sebagai *backup*.

---

## 5. Rekomendasi Integrasi Teknologi (Tech Stack)
*   **Pemindai USB (Keyboard Emulator):** Tidak memerlukan pengaturan *hardware* khusus. OS membaca *scanner* murni sebagai *keyboard*. Hanya butuh logika *JavaScript* `keypress` standar.
*   **Pemindai Kamera Web/HP:** Menggunakan *library* *open-source* berbasis JavaScript seperti **Html5Qrcode** (`html5-qrcode`) untuk membuka akses kamera peramban secara aman (HTTPS) tanpa perlu menginstal aplikasi pihak ketiga di *smartphone* pengguna.