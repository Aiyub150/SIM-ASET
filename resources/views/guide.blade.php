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
        border-radius: 12px;
        padding: 1.25rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }
    .ui-element-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
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
        margin-right: 0.75rem;
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

    /* ── FAQ ACCORDION ────────────────────────── */
    .faq-accordion .accordion-item {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        overflow: hidden;
        margin-bottom: 0.85rem;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .faq-accordion .accordion-item:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    }
    .faq-accordion .accordion-button {
        font-size: 0.95rem;
        padding: 1.1rem 1.25rem;
        color: #1e293b;
        background-color: #ffffff;
        border-radius: 12px !important;
        box-shadow: none !important;
        transition: background-color 0.2s, color 0.2s;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        color: #1d4ed8;
        background-color: #eff6ff;
        border-bottom: 1px solid #dbeafe;
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        font-weight: 600;
    }
    .faq-accordion .accordion-button:focus {
        box-shadow: none !important;
    }
    .faq-accordion .accordion-collapse {
        visibility: visible !important;
    }
    .faq-accordion .accordion-collapse.collapse.show {
        visibility: visible !important;
        display: block !important;
    }
    .faq-accordion .accordion-body {
        padding: 1.25rem;
        font-size: 0.9rem;
        line-height: 1.7;
        color: #334155 !important;
        background: #ffffff;
        visibility: visible !important;
    }

    /* ── ROLE CARD ────────────────────────────── */
    .role-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .role-card .card-header {
        padding: 0.85rem 1.25rem;
        border-bottom: none;
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
                        Versi Rilis 1.1.0 (2026)
                    </span>
                    <span class="badge" style="background: rgba(255,255,255,0.2); font-size: 0.78rem; padding: 0.5rem 0.8rem;">
                        Modul: Peminjaman &bull; Master Barang &bull; Kartu Stok &bull; Laporan BAST
                    </span>
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
            <a href="{{ route('guide.pdf') }}" target="_blank" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-2 fw-semibold px-3 py-1 shadow-sm" style="border-radius: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                Cetak / Unduh PDF Panduan
            </a>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="#pengenalan" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">1. Filosofi & Tampilan UI/UX</a>
            <a href="#format-kode" class="btn btn-sm btn-outline-primary py-1 fw-bold" style="font-size: 0.78rem;"><i class="fa-solid fa-barcode me-1"></i> 2. Standarisasi Format Kode SKU & Dokumen</a>
            <a href="#administrasi-sistem" class="btn btn-sm btn-outline-primary py-1 fw-bold" style="font-size: 0.78rem;"><i class="fa-solid fa-sliders me-1"></i> 3. Pengaturan Administrasi Sistem & Audit Trail</a>
            <a href="#peran-pengguna" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">4. Hak Akses (Dynamic RBAC)</a>
            <a href="#alur-peminjaman" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">5. Alur Peminjaman Aset</a>
            <a href="#alur-pengembalian" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">6. Alur Pengembalian Aset</a>
            <a href="#master-barang" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">7. Master Barang & Label</a>
            <a href="#kartu-stok" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">8. Kartu Stok & Buku Besar</a>
            <a href="#laporan-bast" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">9. Laporan & Cetak BAST</a>
            <a href="#tips-scanner" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">10. Penggunaan Scanner & Kamera</a>
            <a href="#faq" class="btn btn-sm btn-outline-secondary py-1" style="font-size: 0.78rem;">11. Tanya Jawab (FAQ)</a>
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
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <div class="fw-bold text-dark mb-1">
                        <span class="text-primary me-1">&bull;</span> Navigasi Sidebar (Kiri)
                    </div>
                    <p class="text-muted small mb-0">
                        Menu samping di sebelah kiri mengelompokkan fitur secara logis (Utama, Transaksi, Master Data, Pengelolaan, Administrasi, dan Pengaturan). Pada layar ponsel, menu ini dapat dibuka melalui tombol toggle tiga garis.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <div class="fw-bold text-dark mb-1">
                        <span class="text-success me-1">&bull;</span> Kartu Metrik (Angka Kunci)
                    </div>
                    <p class="text-muted small mb-0">
                        Dashboard menampilkan kotak ringkasan berwarna: Total Fisik Barang, Barang Tersedia (Siap Dipinjam), Peminjaman Aktif, dan Transaksi Selesai agar pimpinan dapat melihat situasi logistik secara instan.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
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
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
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
    {{-- BAGIAN 2: STANDARISASI FORMAT KODE SKU & DOKUMEN       --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="format-kode">
        <div class="section-heading">
            <div class="section-icon-box" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5zM3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">2. Cara Membaca & Memahami Format Kode Sistem</h4>
                <small class="text-muted">Panduan membaca struktur kode SKU Barang, Nomor Transaksi Peminjaman, dan Nomor Berita Acara (BAST)</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            Di dalam SIM-ASET, setiap kode referensi dibuat secara terstruktur, unik, dan memiliki arti fungsional. Hal ini bertujuan agar staf logistik maupun pimpinan dapat langsung mengidentifikasi asal-usul, tanggal transaksi, serta nomor urut aset tanpa perlu membuka dokumen fisik secara manual.
        </p>

        <div class="row g-4 mt-2">
            <!-- 1. SKU Barang -->
            <div class="col-md-4">
                <div class="border rounded-4 p-3 bg-light h-100">
                    <span class="badge bg-primary mb-2">Kode Barang (SKU)</span>
                    <h6 class="fw-bold font-monospace text-dark mb-2">ELEK-001</h6>
                    <p class="text-muted small mb-3">Format identitas unik yang tertera pada label stiker fisik QR Code setiap barang.</p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><strong>1. Awalan (Prefix):</strong> <code>ELEK</code> menandakan inisial kategori barang (contoh: <em>Elektronik</em> atau <em>Mebel</em>).</li>
                        <li class="mb-2"><strong>2. Pemisah (Separator):</strong> Tanda hubung <code>-</code> memisahkan kategori dengan nomor urut.</li>
                        <li><strong>3. Akhiran (Suffix):</strong> <code>001</code> adalah nomor registrasi urut barang dalam kategori tersebut.</li>
                    </ul>
                </div>
            </div>

            <!-- 2. Kode Peminjaman -->
            <div class="col-md-4">
                <div class="border rounded-4 p-3 bg-light h-100">
                    <span class="badge bg-success mb-2">Kode Peminjaman</span>
                    <h6 class="fw-bold font-monospace text-dark mb-2">PJM-20261020-RRBQA</h6>
                    <p class="text-muted small mb-3">Format nomor registrasi transaksi peminjaman aset untuk mencegah bentrok data.</p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><strong>1. Awalan (Prefix):</strong> <code>PJM</code> singkatan dari dokumen Peminjaman.</li>
                        <li class="mb-2"><strong>2. Format Tanggal:</strong> <code>20261020</code> menunjukkan tanggal transaksi dibuat (Tahun 2026, Bulan 10, Hari 20).</li>
                        <li><strong>3. Akhiran Unik:</strong> <code>RRBQA</code> adalah 5 karakter acak unik anti-tabrakan data (atau nomor urut dinamis).</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Nomor BAST Mutasi Stok -->
            <div class="col-md-4">
                <div class="border rounded-4 p-3 bg-light h-100">
                    <span class="badge bg-info text-dark mb-2">Nomor Berita Acara (BAST)</span>
                    <h6 class="fw-bold font-monospace text-dark mb-2">BAST/2026/10/001</h6>
                    <p class="text-muted small mb-3">Format penomoran surat resmi Berita Acara Serah Terima untuk mutasi stok.</p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><strong>1. Awalan (Prefix):</strong> <code>BAST</code> adalah klasifikasi dokumen Berita Acara.</li>
                        <li class="mb-2"><strong>2. Periode Waktu:</strong> <code>2026/10</code> menandakan tahun dan bulan penerbitan mutasi.</li>
                        <li><strong>3. Nomor Urut Buku:</strong> <code>001</code> merupakan nomor urut berkas transaksi mutasi pada bulan berjalan.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="alert alert-primary-subtle border border-primary-subtle rounded-3 small mt-3 mb-0">
            <i class="fa-solid fa-sliders me-1"></i> <strong>Kustomisasi Format Kode:</strong> Super Admin dapat mengubah struktur prefix, separator, format tanggal, dan akhiran kode ini secara dinamis melalui menu <strong>Administration Settings &rarr; Manajemen Kode SKU</strong> dengan fitur <em>Live Preview</em> seketika, serta opsi sinkronisasi riwayat data lama.
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 3: PENGATURAN ADMINISTRASI SISTEM & AUDIT LOG   --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="guide-section-card" id="administrasi-sistem">
        <div class="section-heading">
            <div class="section-icon-box" style="background: rgba(15, 23, 42, 0.08); color: #0f172a;">
                <i class="fa-solid fa-sliders fs-5"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">3. Pengaturan Administrasi Sistem & Audit Trail (Rilis 1.2.0)</h4>
                <small class="text-muted">Tata kelola peran dinamis, matriks hak akses, kustomisasi nomor dokumen, dan riwayat audit perbandingan</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            Pembaruan versi 1.2.0 memperkenalkan modul <strong>Administration Settings</strong> yang diperuntukkan bagi Super Admin untuk mengelola sistem secara fleksibel tanpa memerlukan modifikasi kode program (<em>zero code configuration</em>).
        </p>

        <div class="row g-3">
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <div class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-user-shield text-primary me-1"></i> Peran Fleksibel (Dynamic Roles)
                    </div>
                    <p class="text-muted small mb-0">
                        Super Admin dapat menambah jabatan/peran kustom baru (misalnya <em>Auditor Inspektorat</em>, <em>Kepala Gudang</em>, atau <em>Petugas Lapangan</em>) lengkap dengan deskripsi tugasnya.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <div class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-table-cells text-success me-1"></i> Matriks 26 Granular Hak Akses
                    </div>
                    <p class="text-muted small mb-0">
                        Dilengkapi <em>Sticky Column</em> dan label Bahasa Indonesia ramah awam. Izin dikelompokkan ke 6 modul dengan tombol <em>Check All</em> dan filter peran interaktif.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <div class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-rotate text-warning me-1"></i> Sinkronisasi Riwayat Data Massal
                    </div>
                    <p class="text-muted small mb-0">
                        Ketika format kode SKU atau nomor transaksi diubah, Super Admin dapat mencentang opsi <strong>Terapkan ke Seluruh Data Riwayat</strong> untuk memperbarui seluruh data masa lalu secara seragam.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <div class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-clock-rotate-left text-danger me-1"></i> Audit Trail Before vs After
                    </div>
                    <p class="text-muted small mb-0">
                        Setiap perubahan data penting (Barang, Format Kode, Kategori, Pengguna) dicatat dalam log detail dengan tampilan perbandingan dua kolom: <strong>Sebelum (Merah)</strong> vs <strong>Sesudah (Hijau)</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BAGIAN 4: HAK AKSES PERAN PENGGUNA                   --}}
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
                <h4 class="mb-0 fw-bold">4. Pembagian Peran Pengguna (Dynamic RBAC & Override)</h4>
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
                    <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">5. Panduan Alur Transaksi Peminjaman Barang</h4>
                <small class="text-muted">Prosedur resmi pengeluaran barang inventaris untuk pihak eksternal/internal</small>
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

            <div class="col-lg-5 mt-4 mt-lg-0 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between" style="border-left: 4px solid #10b981;">
                    <div>
                        <div class="fw-bold text-dark mb-2" style="font-size: 0.9rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#10b981" class="me-1" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            Fitur Keamanan: Validasi Stok Otomatis
                        </div>
                        <p class="text-muted small mb-3" style="line-height: 1.7;">
                            Sistem tidak akan mengizinkan peminjaman melebihi stok yang saat ini tersedia (<em>Available Qty</em>). Jika ada 5 unit fisik namun 3 sedang dipinjam instansi lain, sistem membatasi maksimal hanya 2 unit yang dapat dipinjam.
                        </p>
                    </div>
                    <div class="border-top pt-2">
                        <small class="text-muted d-block mb-1">Contoh Perhitungan:</small>
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            <span class="badge bg-secondary">Total Fisik: 10</span>
                            <span class="text-muted">&minus;</span>
                            <span class="badge bg-warning text-dark">Dipinjam: 4</span>
                            <span class="text-muted">&equals;</span>
                            <span class="badge bg-success">Tersedia: 6 Unit</span>
                        </div>
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
                <h4 class="mb-0 fw-bold">6. Panduan Alur Pengembalian Barang (Penuh & Parsial)</h4>
                <small class="text-muted">Prosedur pengecekan fisik, verifikasi unit kembali, dan pembaruan sisa pinjaman</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            Dalam praktiknya di dinas, seringkali instansi peminjam baru mengembalikan sebagian barang karena kegiatan yang masih berlanjut. SIM-ASET mengakomodasi kondisi ini secara fleksibel tanpa merusak catatan inventaris:
        </p>

        <div class="row g-3">
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-success mb-2">1. Kembalikan Semua Sisa</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.7;">
                            Jika seluruh barang telah kembali dalam kondisi utuh, klik tombol hijau <strong>"Kembalikan Semua Sisa"</strong>. Sistem akan mengisi seluruh kuantitas sisa hutang secara otomatis. Transaksi berubah status menjadi <span class="badge bg-success">Selesai</span>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-primary mb-2">2. Pengembalian Bertahap (Parsial)</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.7;">
                            Bila instansi baru memulangkan 2 dari 5 unit barang, cukup ketik angka 2 pada input jumlah. Sisa 3 unit akan tetap tercatat sebagai tanggungan dan status transaksi tetap <span class="badge" style="background:#fef3c7; color:#92400e;">Aktif</span> sampai semua lunas.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-2">3. Verifikasi via Scan Barcode</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.7;">
                            Saat barang masuk kembali ke pintu gudang, petugas dapat langsung mengarahkan scanner ke label barang. Sistem langsung mencocokkan item pada transaksi tersebut dan mengisi jumlah pengembalian secara presisi.
                        </p>
                    </div>
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
                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">7. Master Data Barang & Pencetakan Label Aset</h4>
                <small class="text-muted">Katalogisasi aset, format label QR Code 2D eksklusif, dan penelusuran lokasi</small>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-2">Penomoran SKU Otomatis Berbasis Kategori</h6>
                    <p class="text-muted small" style="line-height: 1.7;">
                        Setiap barang yang didaftarkan akan secara otomatis mendapatkan nomor <strong>SKU (Stock Keeping Unit)</strong> berdasarkan awalan kategori. Contoh: kategori <em>Elektronik</em> (awalan <code>ELEC</code>) akan menghasilkan <code>ELEC-001</code>, <code>ELEC-002</code>, dan seterusnya secara berurutan dan terkunci.
                    </p>

                    <h6 class="fw-bold text-dark mb-2">Pilihan Format Cetak Label</h6>
                    <p class="text-muted small mb-2">Pada menu <em>Master Barang</em>, Anda dapat mencetak stiker label aset dengan 3 pilihan format:</p>
                    <div class="list-group list-group-flush mb-0">
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="badge bg-primary me-2">Barcode Kotak (QR Code 2D) — Standar Utama</span>
                            <small class="text-muted">Format kotak matriks 2D beresolusi tinggi, bebas redundansi, dan sangat responsif dipindai melalui scanner USB maupun kamera HP.</small>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="badge bg-secondary me-2">Barcode 1D Saja</span>
                            <small class="text-muted">Format garis lurus tradisional, cocok untuk dipindai menggunakan pemindai barcode laser konvensional.</small>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="badge bg-info text-white me-2">Garis 1D + Kotak QR 2D</span>
                            <small class="text-muted">Format kombinasi barcode garis di atas dan QR Code di samping/bawah untuk fleksibilitas arsip ganda.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 text-center d-flex align-items-stretch">
                <div class="p-4 bg-light rounded-3 border w-100 d-flex flex-column justify-content-center align-items-center shadow-sm">
                    <div class="fw-bold text-dark mb-3" style="font-size: 0.88rem;">Contoh Tampilan Label SIM-ASET</div>
                    <div class="p-3 bg-white border rounded-3 d-inline-block shadow-sm" style="max-width: 240px; text-align: center;">
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
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">8. Kartu Stok & Buku Besar Mutasi Fisik</h4>
                <small class="text-muted">Pencatatan penambahan, mutasi masuk, rusak, dan hilang di luar sirkulasi peminjaman</small>
            </div>
        </div>

        <p class="text-muted" style="line-height: 1.7;">
            Menu <strong>Kartu Stok</strong> (khusus Super Admin & Admin) berfungsi sebagai buku besar (<em>ledger</em>) untuk mencatat peristiwa fisik aset di luar siklus pinjam-kembali:
        </p>

        <div class="row g-3">
            <div class="col-md-6 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-success mb-2">Penambahan Stok (Tipe: In)</h6>
                        <p class="text-muted small mb-3" style="line-height: 1.7;">
                            Digunakan saat ada pengadaan barang baru dari APBD, hibah instansi lain, atau penemuan barang inventaris yang belum tercatat.
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Efek: Total Fisik (+) &amp; Stok Tersedia (+)</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-danger mb-2">Pengurangan Stok (Tipe: Out / Broken / Lost)</h6>
                        <p class="text-muted small mb-3" style="line-height: 1.7;">
                            Digunakan saat barang dihapuskan dari buku aset, barang rusak berat/afkir yang tidak dapat dipakai lagi, atau hilang karena bencana/pencurian.
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Efek: Total Fisik (&minus;) &amp; Stok Tersedia (&minus;)</span>
                    </div>
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
                    <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">9. Laporan Rekapitulasi & Cetak Dokumen BAST</h4>
                <small class="text-muted">Ekspor data per periode dan pencetakan dokumen pertanggungjawaban hukum</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <h6 class="fw-bold text-dark mb-2">Laporan Rekapitulasi Bulanan</h6>
                    <p class="text-muted small" style="line-height: 1.7;">
                        Menu <strong>Laporan</strong> menyajikan rekapitulasi transaksi pada periode bulan dan tahun yang dipilih. Dilengkapi filter pemilih bulan kalender interaktif sehingga Anda dapat memeriksa data masa lalu ataupun proyeksi ke depan dengan satu sentuhan.
                    </p>
                    <ul class="text-muted small ps-3 mb-0">
                        <li>Kolom tabel menyajikan No, Tanggal, Kode Transaksi, Instansi Peminjam (lengkap dengan nama PIC), Nomor Telepon, Lokasi/Alamat, dan Status.</li>
                        <li>Dapat diekspor langsung ke dokumen PDF berorientasi lanskap untuk arsip bulanan dinas.</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="ui-element-card w-100">
                    <h6 class="fw-bold text-dark mb-2">Berita Acara Serah Terima (BAST) PDF</h6>
                    <p class="text-muted small" style="line-height: 1.7;">
                        Dokumen BAST dicetak setiap kali ada serah terima peminjaman barang. Dokumen ini memuat:
                    </p>
                    <ul class="text-muted small ps-3 mb-0">
                        <li>Kop surat resmi kedinasan dan nomor surat BAST.</li>
                        <li>Identitas <strong>PIHAK PERTAMA</strong> (Admin/Staff Logistik penyedia barang).</li>
                        <li>Identitas <strong>PIHAK KEDUA</strong> (Nama Peminjam, Instansi, Nomor Telepon, dan Alamat).</li>
                        <li>Tabel daftar barang yang diserahkan beserta batas waktu jatuh tempo pengembalian.</li>
                        <li>Kolom tanda tangan bermaterai/kedinasan kedua belah pihak.</li>
                    </ul>
                </div>
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
                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2z"/>
                    <path d="M5.5 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm5 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">10. Tips Pemindaian Barcode & Penggunaan Kamera</h4>
                <small class="text-muted">Panduan optimalisasi kamera ponsel dan scanner USB saat operasional sibuk</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-2">Menggunakan Scanner USB / Bluetooth</h6>
                        <p class="text-muted small mb-0" style="line-height: 1.7;">
                            Alat scanner barcode fisik (bentuk pistol genggam) bekerja seperti keyboard komputer. Cukup colokkan kabel USB ke komputer atau hubungkan bluetooth. Letakkan kursor pada kolom input scan lalu tembakkan laser ke label. Barcode akan langsung terinput dan diproses seketika.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 d-flex align-items-stretch">
                <div class="ui-element-card w-100 justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-2">Menggunakan Kamera Laptop / Smartphone</h6>
                        <p class="text-muted small mb-3" style="line-height: 1.7;">
                            Pada kotak pemilih perangkat, pilih <strong>"Kamera Belakang (Mobile)"</strong> atau <strong>"Kamera Depan / Webcam"</strong>. Kotak preview video kamera akan muncul di layar.
                        </p>
                    </div>
                    <div class="small text-muted bg-white p-2 rounded-2 border">
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
                    <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                </svg>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">11. Pertanyaan yang Sering Diajukan (FAQ)</h4>
                <small class="text-muted">Solusi cepat untuk kendala operasional yang kerap dihadapi di lapangan</small>
            </div>
        </div>

        <div class="accordion faq-accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Mengapa akun Staff Logistik tidak bisa melihat data peminjaman milik staf lain?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Hal ini merupakan penerapan prinsip akuntabilitas dan pemisahan wewenang. Setiap staf bertanggung jawab penuh atas barang yang diserahterimakan di mejanya. Hanya Administrator dan Super Admin yang memiliki kewenangan mengaudit seluruh transaksi secara komprehensif.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Bagaimana jika barang yang dikembalikan peminjam dalam keadaan rusak atau hilang?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Selesaikan transaksi pengembalian di menu Peminjaman terlebih dahulu agar status transaksi tuntas, kemudian Administrator wajib mencatat pengurangan stok di menu <strong>Kartu Stok</strong> dengan memilih tipe <em>"Rusak (Broken)"</em> atau <em>"Hilang (Lost)"</em> disertai nomor surat Berita Acara Kerusakan/Kehilangan sebagai dasar pembukuan aset daerah.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Apakah barcode label dapat dicetak menggunakan printer biasa (kertas HVS / stiker)?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Ya. Output cetak label SIM-ASET berformat PDF dengan standar vektor SVG sehingga sangat tajam dan tidak pecah. Anda dapat mencetaknya menggunakan printer kantor biasa di kertas stiker label HVS, stiker thermal, maupun kertas glossy khusus inventaris barang.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Siapa yang dapat saya hubungi bila terjadi kendala teknis atau lupa password?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Silakan hubungi <strong>Super Admin</strong> dinas Anda. Super Admin memiliki menu <em>Kelola Pengguna</em> untuk mereset kata sandi Anda dan memverifikasi profil akun.
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 pt-3 border-top d-flex justify-content-center flex-wrap gap-2">
            <a href="{{ route('guide.pdf') }}" target="_blank" class="btn btn-outline-danger px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                </svg>
                Cetak Dokumen PDF Panduan
            </a>
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
