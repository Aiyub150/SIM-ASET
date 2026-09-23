@extends(auth()->check() ? 'layouts.app' : 'layouts.guest-guide')

@section('title', 'Buku Panduan Penggunaan & UI/UX — SIM-ASET')
@section('page-title', 'Buku Panduan Penggunaan')

@section('content')
<style>
    /* Styling khusus panduan */
    .guide-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #2563eb 100%);
        border-radius: 16px;
        padding: 3rem 2.5rem;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .guide-hero::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .author-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(8px);
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 500;
        letter-spacing: 0.02em;
    }
    .toc-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .guide-section-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .section-heading {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .section-icon-box {
        width: 38px;
        height: 38px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ui-element-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .ui-element-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .step-badge {
        width: 28px;
        height: 28px;
        background: #2563eb;
        color: #ffffff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        margin-right: 0.5rem;
        flex-shrink: 0;
    }
    .step-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.25rem;
    }
    .role-badge-pill {
        font-weight: 600;
        font-size: 0.78rem;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
    }
</style>

<div class="guide-container">

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- HERO COVER / COVER BUKU PANDUAN                       --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-hero">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/sim-aset_logo.svg') }}" alt="Logo SIM-ASET" style="width: 58px; height: 58px; background: rgba(255,255,255,0.15); padding: 6px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.25);">
                    <div>
                        <div class="author-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                            Dibuat oleh Aiyub Heriyanto &bull; Tahun 2026
                        </div>
                    </div>
                </div>

                <h1 class="fw-bold mb-2 text-white" style="font-size: 2.1rem; letter-spacing: -0.02em;">
                    Buku Panduan Penggunaan SIM-ASET
                </h1>
                <p class="text-white-50 mb-3" style="font-size: 1.05rem; line-height: 1.6;">
                    Pedoman Lengkap dan Penjelasan Antarmuka (UI/UX) Sistem Informasi Inventaris Aset & Logistik Daerah untuk Seluruh Tingkat Pengguna.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-white text-dark px-3 py-2 fw-semibold" style="font-size: 0.78rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#2563eb" class="me-1" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                        </svg>
                        Versi Rilis 1.0.0 (2026)
                    </span>
                    <span class="badge" style="background: rgba(255,255,255,0.2); font-size: 0.78rem; padding: 0.5rem 0.8rem;">
                        Modul: Peminjaman &bull; Master Barang &bull; Kartu Stok &bull; Laporan BAST
                    </span>
                    @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-light fw-bold px-3 py-2 ms-auto" style="border-radius: 8px;">
                        Masuk ke Aplikasi &rarr;
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light fw-bold px-3 py-2 ms-auto" style="border-radius: 8px;">
                        Buka Dashboard &rarr;
                    </a>
                    @endguest
                </div>
            </div>

            <div class="col-lg-4 text-center mt-4 mt-lg-0 d-none d-lg-block">
                <div style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 1.5rem; backdrop-filter: blur(10px);">
                    <img src="{{ asset('images/sim-aset_banner.png') }}" alt="SIM-ASET Banner" style="max-width: 100%; height: auto; border-radius: 8px;">
                    <div class="mt-2 text-white-50" style="font-size: 0.75rem;">
                        Transparan &bull; Akuntabel &bull; Real-Time
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- DAFTAR ISI CEPAT (QUICK NAVIGATION)                   --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="toc-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="fw-bold text-dark" style="font-size: 0.88rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#2563eb" class="me-1" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Daftar Topik Panduan:
            </span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="#pengenalan" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">1. Filosofi & Tampilan UI/UX</a>
            <a href="#peran-pengguna" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">2. Hak Akses (Role)</a>
            <a href="#alur-peminjaman" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">3. Alur Peminjaman Aset</a>
            <a href="#alur-pengembalian" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">4. Alur Pengembalian Aset</a>
            <a href="#master-barang" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">5. Master Barang & Label</a>
            <a href="#kartu-stok" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">6. Kartu Stok & Buku Besar</a>
            <a href="#laporan-bast" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">7. Laporan & Cetak BAST</a>
            <a href="#tips-scanner" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">8. Penggunaan Scanner & Kamera</a>
            <a href="#faq" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">9. Tanya Jawab (FAQ)</a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 1: FILOSOFI & DESAIN UI/UX                     --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="pengenalan">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">1. Mengenal SIM-ASET & Pemahaman Tampilan UI/UX</h4>
                <small class="text-muted">Prinsip kemudahan, kejelasan visual, dan alur kerja yang intuitif</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            <strong>SIM-ASET</strong> (Sistem Informasi Manajemen Aset & Logistik Daerah) dirancang dengan antarmuka yang bersih, modern, dan mudah dipahami oleh staf pemerintah yang belum terbiasa dengan aplikasi kompleks. Semua elemen visual telah disesuaikan agar memberikan informasi yang jelas dalam hitungan detik.
        </p>

        <div class="row g-3 mt-2">
            <div class="col-md-6 col-lg-3">
                <div class="ui-element-card">
                    <div class="fw-bold text-dark mb-1">
                        <span class="text-primary me-1">&bull;</span> Navigasi Sidebar (Kiri)
                    </div>
                    <p class="text-muted small mb-0">
                        Menu samping di sebelah kiri mengelompokkan fitur secara logis (Utama, Transaksi, Master Data, Pengelolaan, Administrasi, dan Pengaturan). Pada layar ponsel, menu ini dapat dibuka melalui tombol toggle tiga garis.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="ui-element-card">
                    <div class="fw-bold text-dark mb-1">
                        <span class="text-success me-1">&bull;</span> Kartu Metrik (Angka Kunci)
                    </div>
                    <p class="text-muted small mb-0">
                        Dashboard menampilkan kotak ringkasan berwarna: Total Fisik Barang, Barang Tersedia (Siap Dipinjam), Peminjaman Aktif, dan Transaksi Selesai agar pimpinan dapat melihat situasi logistik secara instan.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="ui-element-card">
                    <div class="fw-bold text-dark mb-1">
                        <span class="text-warning me-1">&bull;</span> Label & Lencana Warna
                    </div>
                    <p class="text-muted small mb-0">
                        <span class="badge bg-success">Selesai</span> menandakan tuntas; 
                        <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span> menandakan barang masih dipinjam; 
                        <span class="badge bg-danger">Habis</span> memberi peringatan stok fisik di gudang sedang kosong.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="ui-element-card">
                    <div class="fw-bold text-dark mb-1">
                        <span class="text-info me-1">&bull;</span> Kotak Pencarian Terpadu
                    </div>
                    <p class="text-muted small mb-0">
                        Pilihan barang dan instansi menggunakan teknologi <em>TomSelect</em>, di mana kolom pencarian menyatu langsung di dalam pilihan dropdown sehingga Anda tidak perlu repot mencari secara manual di daftar yang panjang.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 2: HAK AKSES PERAN PENGGUNA                   --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="peran-pengguna">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">2. Pembagian Peran Pengguna (Role & Hak Akses)</h4>
                <small class="text-muted">Menjamin integritas data, keamanan operasional, dan pemisahan tugas kedinasan</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card h-100 border-primary" style="background: #f8fafc;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Super Admin</span>
                        <span class="badge bg-white text-primary">Tingkat Tertinggi</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="small text-muted mb-2">Ditujukan untuk Administrator IT atau Kepala Bidang Pengelolaan Aset.</p>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                            <li><strong>Kelola Pengguna:</strong> Menambah akun, mereset password, dan menetapkan peran (Super Admin, Admin, Staff).</li>
                            <li><strong>Master Data Penuh:</strong> Menambah barang, mengelola kategori dan prefix SKU, serta instansi peminjam.</li>
                            <li><strong>Pengawasan Menyeluruh:</strong> Melihat semua data transaksi, kartu stok mutasi, dan laporan daerah.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100 border-info" style="background: #f8fafc;">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Admin Logistik</span>
                        <span class="badge bg-white text-info">Pengelola Operasional</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="small text-muted mb-2">Ditujukan untuk Pengurus Barang Pengguna atau Penatausahaan Barang.</p>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                            <li><strong>Master Inventaris:</strong> Menambah & mengedit barang, mengunggah foto fisik, dan mencetak label barcode (1D/2D).</li>
                            <li><strong>Mutasi Fisik:</strong> Mencatat stok masuk (pengadaan) dan stok keluar (rusak/hilang/afkir) di Kartu Stok.</li>
                            <li><strong>Peminjaman & Laporan:</strong> Melihat seluruh transaksi peminjaman dan mengekspor rekapitulasi PDF dinas.</li>
                            <li><em>Tidak memiliki akses ke menu Kelola Pengguna.</em></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100 border-success" style="background: #f8fafc;">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Staff Logistik</span>
                        <span class="badge bg-white text-success">Petugas Pelaksana</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="small text-muted mb-2">Ditujukan untuk Petugas Gudang atau Petugas Lapangan Serah Terima Barang.</p>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.7;">
                            <li><strong>Transaksi Mandiri:</strong> Mencatat peminjaman baru dan mengeksekusi pengembalian barang via scan barcode.</li>
                            <li><strong>Privasi Transaksi:</strong> Hanya melihat dan mengelola transaksi peminjaman yang dicatat atas nama dirinya sendiri.</li>
                            <li><strong>Dokumen BAST:</strong> Mencetak lembar Berita Acara Serah Terima (PDF) resmi untuk pihak peminjam.</li>
                            <li><em>Tidak dapat mengubah master data barang atau mutasi fisik gudang.</em></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 3: ALUR PEMINJAMAN ASET                        --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="alur-peminjaman">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">3. Panduan Alur Transaksi Peminjaman Barang</h4>
                <small class="text-muted">Prosedur resmi peminjaman aset dari input instansi hingga serah terima fisik</small>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="step-item">
                    <div class="step-badge">1</div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Buka Halaman Form Peminjaman Baru</h6>
                        <p class="text-muted small mb-0">Klik menu <strong>Peminjaman</strong> pada sidebar, lalu tekan tombol biru <strong>+ Buat Peminjaman</strong>.</p>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-badge">2</div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Pilih Instansi & Tentukan Tanggal</h6>
                        <p class="text-muted small mb-0">
                            Pilih instansi peminjam (ketik nama dinas/PIC pada dropdown), tentukan <strong>Tanggal Pinjam</strong> dan <strong>Batas Pengembalian (Jatuh Tempo)</strong>, serta tambahkan catatan peruntukan bila diperlukan.
                        </p>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-badge">3</div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Tambahkan Barang yang Dipinjam</h6>
                        <p class="text-muted small mb-1">Anda memiliki 2 metode praktis untuk memasukkan barang:</p>
                        <ul class="text-muted small ps-3 mb-0">
                            <li><strong>Metode Scanner / Kamera:</strong> Arahkan barcode label pada barang ke alat scanner USB atau aktifkan kamera webcam/HP. Barang akan otomatis ditambahkan ke daftar.</li>
                            <li><strong>Metode Manual:</strong> Cari nama barang pada kotak daftar barang, lalu ketik jumlah (kuantitas) unit yang ingin dipinjam. Gunakan tombol <em>+ Tambah Baris</em> bila meminjam lebih dari satu jenis barang.</li>
                        </ul>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-badge">4</div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Simpan & Terbitkan Nomor Transaksi</h6>
                        <p class="text-muted small mb-0">
                            Klik tombol <strong>Proses & Simpan Peminjaman</strong>. Sistem akan mengunci data (mencegah bentrok stok), mengurangi stok barang yang tersedia di gudang secara otomatis, dan menerbitkan Nomor Transaksi unik (contoh: <code>TRX-202609-001</code>).
                        </p>
                    </div>
                </div>

                <div class="step-item mb-0">
                    <div class="step-badge">5</div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Cetak Dokumen BAST (Berita Acara)</h6>
                        <p class="text-muted small mb-0">
                            Buka detail transaksi lalu klik <strong>Cetak BAST</strong>. Lembar format standar pemerintah akan diunduh dalam format PDF, lengkap dengan identitas kedua pihak dan kolom tanda tangan resmi.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="bg-light p-3 rounded border">
                    <div class="fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#10b981" class="me-1" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Fitur Keamanan: Validasi Stok Otomatis
                    </div>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Sistem tidak akan mengizinkan peminjaman melebihi stok yang saat ini tersedia (<em>Available Qty</em>). Jika ada 5 unit fisik namun 3 sedang dipinjam instansi lain, sistem membatasi maksimal hanya 2 unit yang dapat dipinjam.
                    </p>
                    <div class="border-top pt-2">
                        <small class="text-muted d-block">Contoh Perhitungan:</small>
                        <span class="badge bg-secondary">Total Fisik: 10</span> &minus; 
                        <span class="badge bg-warning text-dark">Sedang Dipinjam: 4</span> &equals; 
                        <span class="badge bg-success">Tersedia: 6 Unit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 4: ALUR PENGEMBALIAN ASET                      --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="alur-pengembalian">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">4. Panduan Alur Pengembalian Barang (Penuh & Parsial)</h4>
                <small class="text-muted">Mendukung pengembalian bertahap maupun langsung tuntas dengan sekali klik</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            Dalam praktiknya di dinas, seringkali instansi peminjam baru mengembalikan sebagian barang karena kegiatan yang masih berlanjut. SIM-ASET mengakomodasi kondisi ini secara fleksibel tanpa merusak catatan inventaris:
        </p>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="ui-element-card">
                    <h6 class="fw-bold text-success mb-2">1. Kembalikan Semua Sisa</h6>
                    <p class="text-muted small mb-0">
                        Jika seluruh barang telah kembali dalam kondisi utuh, klik tombol hijau <strong>"Kembalikan Semua Sisa"</strong>. Sistem akan mengisi seluruh kuantitas sisa hutang secara otomatis. Transaksi berubah status menjadi <span class="badge bg-success">Selesai</span>.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ui-element-card">
                    <h6 class="fw-bold text-primary mb-2">2. Pengembalian Bertahap (Parsial)</h6>
                    <p class="text-muted small mb-0">
                        Bila instansi baru memulangkan 2 dari 5 unit barang, cukup ketik angka 2 pada input jumlah. Sisa 3 unit akan tetap tercatat sebagai tanggungan dan status transaksi tetap <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span> sampai semua lunas.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ui-element-card">
                    <h6 class="fw-bold text-dark mb-2">3. Verifikasi via Scan Barcode</h6>
                    <p class="text-muted small mb-0">
                        Saat barang masuk kembali ke pintu gudang, petugas dapat langsung mengarahkan scanner ke label barang. Sistem langsung mencocokkan item pada transaksi tersebut dan mengisi jumlah pengembalian secara presisi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 5: MASTER BARANG & LABEL BARCODE               --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="master-barang">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.922l6.5 2.6z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">5. Master Data Barang & Pencetakan Label Aset</h4>
                <small class="text-muted">Standardisasi SKU barang milik daerah dan opsi cetak label Barcode 1D / QR Code 2D</small>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-lg-7">
                <h6 class="fw-bold text-dark mb-2">Penomoran SKU Otomatis Berbasis Kategori</h6>
                <p class="text-muted small" style="line-height: 1.7;">
                    Setiap barang yang didaftarkan akan secara otomatis mendapatkan nomor <strong>SKU (Stock Keeping Unit)</strong> berdasarkan awalan kategori. Contoh: kategori <em>Elektronik</em> (awalan <code>ELEC</code>) akan menghasilkan <code>ELEC-001</code>, <code>ELEC-002</code>, dan seterusnya secara berurutan dan terkunci.
                </p>

                <h6 class="fw-bold text-dark mb-2">Pilihan Format Cetak Label</h6>
                <p class="text-muted small mb-2">Pada menu <em>Master Barang</em>, Anda dapat mencetak stiker label aset dengan 3 pilihan format:</p>
                <div class="list-group list-group-flush mb-3">
                    <div class="list-group-item px-0 py-2 border-0">
                        <span class="badge bg-primary me-2">Standar (Barcode + QR)</span>
                        <small class="text-muted">Menampilkan barcode garis 1D di atas dan QR Code 2D di bawah, lengkap dengan SKU dan nama instansi.</small>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <span class="badge bg-secondary me-2">Barcode 1D Saja</span>
                        <small class="text-muted">Format garis lurus tradisional, cocok untuk dipindai menggunakan pemindai barcode pistol laser standar.</small>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <span class="badge bg-info text-white me-2">QR Code 2D Saja</span>
                        <small class="text-muted">Format kotak matriks 2D beresolusi tinggi, sangat responsif dipindai melalui kamera ponsel petugas.</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 text-center">
                <div class="p-3 bg-light rounded border">
                    <div class="fw-bold text-dark mb-2" style="font-size: 0.85rem;">Contoh Tampilan Label SIM-ASET</div>
                    <div class="p-3 bg-white border rounded d-inline-block shadow-sm" style="max-width: 240px; text-align: center;">
                        <div class="fw-bold" style="font-size: 0.72rem; color: #1e3a5f;">PEMERINTAH DAERAH PROVINSI</div>
                        <div class="text-muted" style="font-size: 0.65rem; margin-bottom: 6px;">DINAS PENGELOLAAN ASET</div>
                        <div class="p-2 border rounded bg-light mb-2">
                            <span style="font-family:monospace; font-weight:700; font-size:0.95rem; color:#2563eb;">ELEC-001</span>
                            <div style="font-size: 0.72rem; font-weight:600; color:#334155;">Laptop Asus Core i7</div>
                        </div>
                        <div class="badge bg-primary text-white" style="font-size: 0.65rem;">Stiker Fisik Inventaris Resmi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 6: KARTU STOK & BUKU BESAR MUTASI              --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="kartu-stok">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">6. Kartu Stok & Buku Besar Mutasi Fisik</h4>
                <small class="text-muted">Pencatatan riwayat penambahan atau pengurangan fisik barang non-peminjaman</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            Menu <strong>Kartu Stok</strong> (khusus Super Admin & Admin) berfungsi sebagai buku besar (<em>ledger</em>) untuk mencatat peristiwa fisik aset di luar siklus pinjam-kembali:
        </p>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded border h-100">
                    <h6 class="fw-bold text-success mb-2">Penambahan Stok (Tipe: In)</h6>
                    <p class="text-muted small mb-2">
                        Digunakan saat ada pengadaan barang baru dari APBD, hibah instansi lain, atau penemuan barang inventaris yang belum tercatat.
                    </p>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Efek: Total Fisik (+) &amp; Stok Tersedia (+)</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded border h-100">
                    <h6 class="fw-bold text-danger mb-2">Pengurangan Stok (Tipe: Out / Broken / Lost)</h6>
                    <p class="text-muted small mb-2">
                        Digunakan saat barang dihapuskan dari buku aset, barang rusak berat/afkir yang tidak dapat dipakai lagi, atau hilang karena bencana/pencurian.
                    </p>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Efek: Total Fisik (&minus;) &amp; Stok Tersedia (&minus;)</span>
                </div>
            </div>
        </div>

        <div class="mt-3 p-3 bg-warning-subtle text-warning-emphasis rounded border border-warning-subtle small">
            <strong>Aturan Format No. Referensi:</strong> Setiap mutasi wajib menyertakan nomor surat berita acara dengan format resmi: <code>BAST/YYYY/MM/XXX</code> (Contoh: <code>BAST/2026/09/001</code>). Sistem melakukan validasi otomatis agar penomoran arsip tertib dan tidak acak.
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 7: LAPORAN & DOKUMEN CETAK BAST                --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="laporan-bast">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">7. Laporan Rekapitulasi & Cetak Dokumen BAST</h4>
                <small class="text-muted">Pelaporan bulanan, filter kalender interaktif, dan lembar legalitas pertanggungjawaban</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <h6 class="fw-bold text-dark mb-2">Laporan Rekapitulasi Bulanan</h6>
                <p class="text-muted small" style="line-height: 1.7;">
                    Menu <strong>Laporan</strong> menyajikan rekapitulasi transaksi pada periode bulan dan tahun yang dipilih. Dilengkapi filter pemilih bulan kalender interaktif sehingga Anda dapat memeriksa data masa lalu ataupun proyeksi ke depan dengan satu sentuhan.
                </p>
                <ul class="text-muted small ps-3">
                    <li>Kolom tabel menyajikan No, Tanggal, Kode Transaksi, Instansi Peminjam (lengkap dengan nama PIC), Nomor Telepon, Lokasi/Alamat, dan Status.</li>
                    <li>Dapat diekspor langsung ke dokumen PDF berorientasi lanskap untuk arsip bulanan dinas.</li>
                </ul>
            </div>

            <div class="col-lg-6">
                <h6 class="fw-bold text-dark mb-2">Berita Acara Serah Terima (BAST) PDF</h6>
                <p class="text-muted small" style="line-height: 1.7;">
                    Dokumen BAST dicetak setiap kali ada serah terima peminjaman barang. Dokumen ini memuat:
                </p>
                <ul class="text-muted small ps-3">
                    <li>Kop surat resmi kedinasan dan nomor surat BAST.</li>
                    <li>Identitas <strong>PIHAK PERTAMA</strong> (Admin/Staff Logistik penyedia barang).</li>
                    <li>Identitas <strong>PIHAK KEDUA</strong> (Nama Peminjam, Instansi, Nomor Telepon, dan Alamat).</li>
                    <li>Tabel daftar barang yang diserahkan beserta batas waktu jatuh tempo pengembalian.</li>
                    <li>Kolom tanda tangan bermaterai/kedinasan kedua belah pihak.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 8: PENGGUNAAN SCANNER & KAMERA                 --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="tips-scanner">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                    <path d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">8. Tips Pemindaian Barcode & Penggunaan Kamera</h4>
                <small class="text-muted">Panduan menggunakan scanner genggam USB maupun kamera smartphone/laptop</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded border h-100">
                    <h6 class="fw-bold text-dark mb-2">Menggunakan Scanner USB / Bluetooth</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        Alat scanner barcode fisik (bentuk pistol genggam) bekerja seperti keyboard komputer. Cukup colokkan kabel USB ke komputer atau hubungkan bluetooth. Letakkan kursor pada kolom input scan lalu tembakkan laser ke label. Barcode akan langsung terinput dan diproses seketika.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-light rounded border h-100">
                    <h6 class="fw-bold text-dark mb-2">Menggunakan Kamera Laptop / Smartphone</h6>
                    <p class="text-muted small mb-2" style="line-height: 1.6;">
                        Pada kotak pemilih perangkat, pilih <strong>"Kamera Belakang (Mobile)"</strong> atau <strong>"Kamera Depan / Webcam"</strong>. Kotak preview video kamera akan muncul di layar.
                    </p>
                    <div class="small text-muted bg-white p-2 rounded border">
                        <strong>Catatan Izin Browser:</strong> Pastikan Anda menekan tombol <em>"Izinkan / Allow"</em> saat browser meminta izin kamera, serta gunakan koneksi aman (HTTPS atau localhost).
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 9: TANYA JAWAB UMUM (FAQ)                      --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="faq">
        <div class="section-heading">
            <div class="section-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                    <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">9. Pertanyaan yang Sering Diajukan (FAQ)</h4>
                <small class="text-muted">Solusi cepat untuk kendala operasional yang kerap dihadapi di lapangan</small>
            </div>
        </div>

        <div class="accordion" id="faqAccordion">
            <div class="accordion-item border mb-2 rounded">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Mengapa akun Staff Logistik tidak bisa melihat data peminjaman milik staf lain?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small" style="line-height: 1.6;">
                        Hal ini merupakan penerapan prinsip akuntabilitas dan pemisahan wewenang. Setiap staf bertanggung jawab penuh atas barang yang diserahterimakan di mejanya. Hanya Administrator dan Super Admin yang memiliki kewenangan mengaudit seluruh transaksi secara komprehensif.
                    </div>
                </div>
            </div>

            <div class="accordion-item border mb-2 rounded">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Bagaimana jika barang yang dikembalikan peminjam dalam keadaan rusak atau hilang?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small" style="line-height: 1.6;">
                        Selesaikan transaksi pengembalian di menu Peminjaman terlebih dahulu agar status transaksi tuntas, kemudian Administrator wajib mencatat pengurangan stok di menu <strong>Kartu Stok</strong> dengan memilih tipe <em>"Rusak (Broken)"</em> atau <em>"Hilang (Lost)"</em> disertai nomor surat Berita Acara Kerusakan/Kehilangan sebagai dasar pembukuan aset daerah.
                    </div>
                </div>
            </div>

            <div class="accordion-item border mb-2 rounded">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Apakah barcode label dapat dicetak menggunakan printer biasa (kertas HVS / stiker)?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small" style="line-height: 1.6;">
                        Ya. Output cetak label SIM-ASET berformat PDF dengan standar vektor SVG sehingga sangat tajam dan tidak pecah. Anda dapat mencetaknya menggunakan printer kantor biasa di kertas stiker label HVS, stiker thermal, maupun kertas glossy khusus inventaris barang.
                    </div>
                </div>
            </div>

            <div class="accordion-item border rounded">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Siapa yang dapat saya hubungi bila terjadi kendala teknis atau lupa password?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small" style="line-height: 1.6;">
                        Silakan hubungi <strong>Super Admin</strong> dinas Anda. Super Admin memiliki menu <em>Kelola Pengguna</em> untuk mereset kata sandi Anda dan memverifikasi profil akun.
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted small mb-3">SIM-ASET &bull; Dikembangkan dan Disempurnakan untuk Tata Kelola Logistik Pemerintah Daerah</p>
            @guest
            <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius: 8px;">
                Masuk ke Halaman Login &rarr;
            </a>
            @else
            <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius: 8px;">
                Kembali ke Dashboard &rarr;
            </a>
            @endguest
        </div>
    </div>

</div>
@endsection
