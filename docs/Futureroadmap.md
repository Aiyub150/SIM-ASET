# Roadmap Pengembangan SIM ASET (Sistem Inventaris Aset Daerah)

Dokumen ini berisi peta jalan (*roadmap*) fitur-fitur strategis yang direkomendasikan untuk pengembangan SIM ASET di masa mendatang. Fitur-fitur ini dirancang untuk mengangkat aplikasi dari sekadar "pencatat aset" menjadi **Sistem Tata Kelola Aset Menyeluruh (Enterprise Asset Management)** yang sesuai dengan standar akuntansi dan birokrasi pemerintahan berskala besar.

---

## 1. Integrasi Barcode & QR Code
**Deskripsi Fitur:**
Sistem secara otomatis menghasilkan (generate) Label QR Code unik untuk setiap jenis barang atau per unit barang fisik. Aplikasi (atau *scanner* eksternal) dapat digunakan untuk memindai kode ini saat proses *check-out* (peminjaman) dan *check-in* (pengembalian).

**Manfaat bagi User & Instansi:**
*   **Efisiensi Waktu:** Staf tidak perlu lagi mencari barang secara manual di *dropdown* yang berisi ribuan *item*. Cukup pindai (scan), dan data barang otomatis masuk ke keranjang peminjaman.
*   **Akurasi Absolut:** Mengeliminasi *human error* (salah pilih barang) saat proses serah terima fisik.

## 2. Modul Stock Opname (Audit Fisik Tahunan)
**Deskripsi Fitur:**
Sebuah modul khusus yang dibuka hanya pada masa tutup buku (akhir tahun). Sistem akan "membekukan" sementara mutasi barang dan meminta auditor untuk memasukkan jumlah fisik riil yang ada di gudang. Sistem kemudian mencetak "Laporan Diskrepansi" (Selisih) antara data *Ledger* dan fisik riil.

**Manfaat bagi User & Instansi:**
*   **Kepatuhan Regulasi (Compliance):** Memfasilitasi proses pelaporan BPK di akhir tahun.
*   **Transparansi:** Jika ada selisih, sistem memaksa auditor untuk membuat BAST Kehilangan/Penyesuaian untuk menyamakan saldo, sehingga kebocoran aset dapat dilacak.

## 3. Sistem Persetujuan Berjenjang (Multi-Tier Approval Workflow)
**Deskripsi Fitur:**
Saat ini, semua peminjaman langsung disetujui (aktif). Ke depannya, peminjaman aset bernilai tinggi (seperti kendaraan dinas, laptop, kamera) akan masuk ke status `Pending` dan membutuhkan klik *Approve* dari Kepala Bagian atau Kepala Dinas melalui akun mereka.

**Manfaat bagi User & Instansi:**
*   **Kendali Berlapis:** Staf logistik tidak menanggung risiko sendirian saat mengeluarkan aset bernilai tinggi atau aset strategis.
*   **Birokrasi Digital:** Menggantikan proses tanda tangan pengajuan di atas meja dengan persetujuan satu klik di dalam sistem (paperless).

## 4. Pelacakan Perawatan & Servis Berkala (Maintenance Tracking)
**Deskripsi Fitur:**
Menambahkan sub-modul untuk mencatat jadwal pemeliharaan aset (misalnya: jadwal ganti oli mobil dinas, cuci AC tahunan, atau kalibrasi alat kesehatan). Aset yang sedang diservis akan berubah statusnya menjadi `In Maintenance` dan otomatis tidak bisa dipinjam.

**Manfaat bagi User & Instansi:**
*   **Visibilitas Anggaran:** Pimpinan dapat melihat rekam jejak biaya perawatan per aset, membantu keputusan apakah sebuah mobil lebih baik dijual/dilelang atau terus diperbaiki.
*   **Perpanjangan Umur Aset:** Perawatan yang tidak terlewat akan menjaga nilai dan utilitas barang negara.

## 5. Perhitungan Penyusutan Nilai Aset (Depreciation Calculation)
**Deskripsi Fitur:**
Setiap aset yang diinput diberikan kolom *Harga Beli*, *Tahun Pembelian*, dan *Masa Pakai*. Sistem akan secara otomatis menghitung nilai penyusutan (menggunakan metode Garis Lurus / *Straight-Line*) sehingga nilai sisa aset (*Book Value*) di tahun berjalan dapat diketahui.

**Manfaat bagi User & Instansi:**
*   **Standar Akuntansi:** Laporan yang dihasilkan langsung bisa digunakan oleh Bagian Keuangan Pemda untuk dimasukkan ke dalam Neraca Keuangan Daerah.
*   **Manajemen Penghapusan (Pemutihan):** Memudahkan pimpinan mendeteksi aset mana yang nilainya sudah Rp0 dan layak untuk dilelang atau dimusnahkan.

## 6. Notifikasi Otomatis (Email / WhatsApp Gateway)
**Deskripsi Fitur:**
Integrasi dengan layanan pihak ketiga (seperti Fonnte untuk WhatsApp atau SMTP untuk Email) yang akan menjalankan *Cron Job* (tugas otomatis) setiap pagi.

**Manfaat bagi User & Instansi:**
*   **Proaktif, Bukan Reaktif:** Peminjam otomatis menerima pesan pengingat seperti: *"Halo, peminjaman 5 Unit Tenda akan jatuh tempo besok."* Ini akan menekan angka keterlambatan pengembalian secara drastis tanpa perlu staf menelepon satu per satu.
*   **Peringatan Stok Tipis:** Super Admin menerima notifikasi jika stok barang *consumable* (habis pakai) mulai menipis dan perlu pengadaan baru.