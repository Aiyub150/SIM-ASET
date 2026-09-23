<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panduan Penggunaan SIM-ASET 2026</title>
    <style>
        @page {
            margin: 20mm 18mm 20mm 18mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            line-height: 1.6;
            color: #1e293b;
            text-align: justify;
            text-justify: inter-word;
        }
        .page-break {
            page-break-after: always;
        }

        /* ── RUNNING FOOTER (ICON KECIL & NOMOR HALAMAN) ── */
        .running-footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            height: 8mm;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5pt;
            color: #64748b;
        }
        .running-footer table {
            width: 100%;
            border-collapse: collapse;
        }
        .running-footer td {
            vertical-align: middle;
            padding: 3px 0 0;
            border: none;
        }
        .page-number:after {
            content: counter(page);
        }

        /* ── COVER PAGE (LOGO PANJANG / BANNER) ─────── */
        .cover-wrapper {
            text-align: center;
            padding-top: 40px;
        }
        .cover-badge-top {
            display: inline-block;
            background-color: #eff6ff;
            color: #2563eb;
            font-size: 8.5pt;
            font-weight: bold;
            padding: 5px 14px;
            border-radius: 20px;
            border: 1px solid #bfdbfe;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        
        /* Logo Panjang (Banner Horizontal) */
        .cover-banner-wrapper {
            margin: 10px auto 30px;
            text-align: center;
        }
        .cover-banner-table {
            margin: 0 auto;
            border-collapse: collapse;
            border: none;
        }
        .cover-banner-icon-cell {
            vertical-align: middle;
            border: none;
            padding-right: 20px;
        }
        .cover-banner-icon {
            display: block;
        }
        .cover-banner-text-cell {
            vertical-align: middle;
            border: none;
            text-align: left;
        }
        .banner-brand-title {
            font-size: 30pt;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 6px;
        }
        .banner-brand-divider {
            height: 2px;
            background-color: #1e293b;
            width: 250px;
            margin-bottom: 7px;
        }
        .banner-brand-sub {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .cover-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a5f;
            margin: 30px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        .cover-subtitle {
            font-size: 10.5pt;
            color: #475569;
            margin-bottom: 35px;
            line-height: 1.5;
            text-align: center;
        }
        .cover-box {
            border: 2px solid #2563eb;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 16px 22px;
            width: 82%;
            margin: 0 auto 35px;
            text-align: left;
        }
        .cover-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .cover-box td {
            padding: 4px 0;
            font-size: 9.5pt;
            vertical-align: top;
            border: none;
            text-align: left;
        }
        .cover-footer-note {
            margin-top: 50px;
            font-size: 8.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            text-align: center;
        }

        /* ── DAFTAR ISI (TABLE OF CONTENTS) ─────────── */
        .toc-header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
            margin-bottom: 22px;
        }
        .toc-header h2 {
            margin: 0;
            font-size: 15pt;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .toc-header p {
            margin: 4px 0 0;
            font-size: 9pt;
            color: #64748b;
            text-align: center;
        }
        .toc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .toc-table td {
            padding: 5px 0;
            border: none;
            font-size: 9pt;
            vertical-align: bottom;
        }
        .toc-main {
            font-weight: bold;
            color: #0f172a;
            white-space: nowrap;
        }
        .toc-sub {
            padding-left: 18px !important;
            color: #475569;
            white-space: nowrap;
        }
        .toc-dots {
            width: 100%;
            border-bottom: 1px dotted #94a3b8;
            height: 12px;
        }
        .toc-page {
            text-align: right !important;
            white-space: nowrap;
            padding-left: 8px !important;
            color: #2563eb;
            font-weight: bold;
        }

        /* ── KONTEN PANDUAN ─────────────────────────── */
        h2.section-title {
            color: #1e3a5f;
            font-size: 12.5pt;
            font-weight: bold;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h3.subsection-title {
            color: #0f172a;
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        p {
            margin: 0 0 10px;
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.6;
        }
        ul, ol {
            margin: 0 0 12px;
            padding-left: 22px;
            text-align: justify;
            text-justify: inter-word;
        }
        li {
            margin-bottom: 5px;
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.55;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 16px;
        }
        .table-data th, .table-data td {
            border: 1px solid #cbd5e1;
            padding: 6px 9px;
            font-size: 8.5pt;
        }
        .table-data th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
        }
        .table-data td {
            text-align: justify;
            text-justify: inter-word;
            vertical-align: top;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .badge-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .badge-primary { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        
        .box-info {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 9px 12px;
            margin: 11px 0;
            font-size: 9pt;
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.55;
        }
        .box-warning {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 9px 12px;
            margin: 11px 0;
            font-size: 9pt;
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.55;
        }
        .step-number {
            font-weight: bold;
            color: #2563eb;
        }
    </style>
</head>
<body>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- RUNNING FOOTER DOKUMEN (DENGAN ICON KECIL)                       --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div class="running-footer">
        <table>
            <tr>
                <td style="width: 70%; text-align: left;">
                    @if(file_exists(public_path('images/sim-aset_logo.svg')))
                        <img src="{{ public_path('images/sim-aset_logo.svg') }}" width="13" style="vertical-align: middle; margin-right: 5px;" alt="Icon">
                    @endif
                    Panduan Penggunaan <strong>SIM-ASET</strong> &bull; Dibuat oleh <strong>Aiyub Heriyanto</strong> (Tahun 2026)
                </td>
                <td style="width: 30%; text-align: right; font-weight: bold; color: #1e3a5f;">
                    Halaman <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 1: COVER BUKU PANDUAN PENGGUNAAN (LOGO PANJANG)          --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div class="cover-wrapper">
        <div class="cover-badge-top">
            Buku Panduan Operasional Sistem
        </div>

        {{-- Logo Panjang (Banner Horizontal) Sesuai Permintaan --}}
        <div class="cover-banner-wrapper">
            <table class="cover-banner-table">
                <tr>
                    <td class="cover-banner-icon-cell">
                        @if(file_exists(public_path('images/sim-aset_logo.svg')))
                            <img src="{{ public_path('images/sim-aset_logo.svg') }}" width="85" class="cover-banner-icon" alt="Logo SIM-ASET">
                        @endif
                    </td>
                    <td class="cover-banner-text-cell">
                        <div class="banner-brand-title">SIM - ASET</div>
                        <div class="banner-brand-divider"></div>
                        <div class="banner-brand-sub">SISTEM INVENTARIS ASET<br>PEMERINTAH DAERAH</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="cover-title">
            BUKU PANDUAN PENGGUNAAN &amp; PENJELASAN UI/UX
        </div>
        <div class="cover-subtitle">
            Tata Cara &amp; Petunjuk Praktis Pengoperasian Aplikasi Manajemen Aset dan Logistik Daerah
        </div>

        <div class="cover-box">
            <table>
                <tr>
                    <td style="width: 32%; font-weight: bold; color:#1e3a5f;">Nama Sistem</td>
                    <td style="width: 3%;">:</td>
                    <td style="width: 65%;"><strong>SIM-ASET (Sistem Inventaris Aset Daerah)</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color:#1e3a5f;">Dibuat Oleh</td>
                    <td>:</td>
                    <td><strong>Aiyub Heriyanto</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color:#1e3a5f;">Tahun Pembuatan</td>
                    <td>:</td>
                    <td><strong>Tahun 2026</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color:#1e3a5f;">Versi Panduan</td>
                    <td>:</td>
                    <td>Versi 1.0.0 (Panduan Pengguna)</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color:#1e3a5f;">Tujuan Dokumen</td>
                    <td>:</td>
                    <td>Petunjuk Pengoperasian Mandiri bagi Seluruh Pengguna &amp; Petugas Barang</td>
                </tr>
            </table>
        </div>

        <div class="cover-footer-note">
            Buku petunjuk praktis tata cara mengoperasikan aplikasi SIM-ASET secara mandiri &bull; Diterbitkan Tahun 2026
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 2: DAFTAR ISI / DAFTAR HALAMAN LENGKAP                   --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div class="toc-header">
        <h2>DAFTAR ISI PANDUAN</h2>
        <p>Struktur Pembahasan &amp; Petunjuk Halaman Pengoperasian SIM-ASET</p>
    </div>

    <table class="toc-table">
        <tr>
            <td class="toc-main">BAB I. MENGENAL SIM-ASET &amp; ANTARMUKA (UI/UX)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 3</td>
        </tr>
        <tr>
            <td class="toc-sub">1.1 Desain yang Mudah Dipahami &amp; Ramah Pengguna Awam</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 3</td>
        </tr>
        <tr>
            <td class="toc-sub">1.2 Menu Navigasi Sidebar &amp; Ringkasan Dashboard</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 3</td>
        </tr>
        <tr>
            <td class="toc-sub">1.3 Arti Warna Status Barang &amp; Transaksi</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 3</td>
        </tr>

        <tr>
            <td class="toc-main" style="padding-top: 10px;">BAB II. PERAN PENGGUNA &amp; MENU AKSES (ROLE)</td>
            <td style="padding-top: 10px;"><div class="toc-dots"></div></td>
            <td class="toc-page" style="padding-top: 10px;">Halaman 4</td>
        </tr>
        <tr>
            <td class="toc-sub">2.1 Super Admin (Pengendali Sistem &amp; Manajemen Akun)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 4</td>
        </tr>
        <tr>
            <td class="toc-sub">2.2 Admin Logistik (Master Barang, Label, &amp; Kartu Stok)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 4</td>
        </tr>
        <tr>
            <td class="toc-sub">2.3 Staff Logistik (Pencatat Peminjaman &amp; Pengembalian Barang)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 4</td>
        </tr>

        <tr>
            <td class="toc-main" style="padding-top: 10px;">BAB III. CARA MENCATAT PEMINJAMAN BARANG (MULTI-ITEM)</td>
            <td style="padding-top: 10px;"><div class="toc-dots"></div></td>
            <td class="toc-page" style="padding-top: 10px;">Halaman 5</td>
        </tr>
        <tr>
            <td class="toc-sub">3.1 Panduan 5 Langkah Membuat Transaksi Peminjaman</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 5</td>
        </tr>
        <tr>
            <td class="toc-sub">3.2 Cara Meminjam Banyak Barang Sekaligus (Multi-Item)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 5</td>
        </tr>
        <tr>
            <td class="toc-sub">3.3 Pengecekan Otomatis Sisa Stok Gudang</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 5</td>
        </tr>

        <tr>
            <td class="toc-main" style="padding-top: 10px;">BAB IV. CARA MEMPROSES PENGEMBALIAN BARANG</td>
            <td style="padding-top: 10px;"><div class="toc-dots"></div></td>
            <td class="toc-page" style="padding-top: 10px;">Halaman 6</td>
        </tr>
        <tr>
            <td class="toc-sub">4.1 Pengembalian Lengkap Sekaligus (100% Selesai)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 6</td>
        </tr>
        <tr>
            <td class="toc-sub">4.2 Pengembalian Bertahap (Sebagian Unit Belum Kembali)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 6</td>
        </tr>
        <tr>
            <td class="toc-sub">4.3 Mempercepat Pengembalian dengan Scan Barcode / Kamera</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 6</td>
        </tr>

        <tr>
            <td class="toc-main" style="padding-top: 10px;">BAB V. MASTER BARANG &amp; CARA CETAK LABEL STIKER</td>
            <td style="padding-top: 10px;"><div class="toc-dots"></div></td>
            <td class="toc-page" style="padding-top: 10px;">Halaman 7</td>
        </tr>
        <tr>
            <td class="toc-sub">5.1 Mengenal Kode Unik SKU Otomatis</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 7</td>
        </tr>
        <tr>
            <td class="toc-sub">5.2 Tiga Pilihan Format Cetak Label (Standar, Barcode, QR Code)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 7</td>
        </tr>
        <tr>
            <td class="toc-sub">5.3 Mencetak Banyak Label Sekaligus (Cetak Massal)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 7</td>
        </tr>

        <tr>
            <td class="toc-main" style="padding-top: 10px;">BAB VI. KARTU STOK, LAPORAN, &amp; BERITA ACARA (BAST)</td>
            <td style="padding-top: 10px;"><div class="toc-dots"></div></td>
            <td class="toc-page" style="padding-top: 10px;">Halaman 8</td>
        </tr>
        <tr>
            <td class="toc-sub">6.1 Menyesuaikan Stok Barang (Barang Masuk, Rusak, Hilang)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 8</td>
        </tr>
        <tr>
            <td class="toc-sub">6.2 Mengunduh Laporan Rekapitulasi Peminjaman</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 8</td>
        </tr>
        <tr>
            <td class="toc-sub">6.3 Mencetak Berita Acara Serah Terima (BAST PDF)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 8</td>
        </tr>

        <tr>
            <td class="toc-main" style="padding-top: 10px;">BAB VII. PEMECAHAN MASALAH (TROUBLESHOOTING) &amp; FAQ</td>
            <td style="padding-top: 10px;"><div class="toc-dots"></div></td>
            <td class="toc-page" style="padding-top: 10px;">Halaman 9</td>
        </tr>
        <tr>
            <td class="toc-sub">7.1 Solusi Scanner Kamera Tidak Terbaca / Izin Ditolak</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 9</td>
        </tr>
        <tr>
            <td class="toc-sub">7.2 Pemecahan Masalah Scanner Barcode USB</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 9</td>
        </tr>
        <tr>
            <td class="toc-sub">7.3 Pertanyaan yang Sering Diajukan (FAQ)</td>
            <td><div class="toc-dots"></div></td>
            <td class="toc-page">Halaman 9</td>
        </tr>
    </table>

    <div class="box-info" style="margin-top: 25px;">
        <strong>Informasi Dokumen:</strong> Buku panduan ini dapat dibuka di perangkat komputer, tablet, ponsel, maupun dicetak di kertas HVS A4 untuk dijadikan panduan meja kerja bagi staf logistik dan pemakai sistem.
    </div>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 3: BAB I FILOSOFI UI/UX & NAVIGASI SISTEM                --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB I. Mengenal SIM-ASET &amp; Antarmuka (UI/UX)</h2>
    <p>
        <strong>SIM-ASET</strong> adalah aplikasi manajemen logistik dan inventaris barang yang dirancang agar dapat dioperasikan dengan mudah dan nyaman oleh siapa saja, termasuk pengguna yang masih awam dengan sistem komputer. Tampilan aplikasi dibuat bersih, sederhana, dan memandu pengguna langkah demi langkah saat melakukan input data.
    </p>

    <h3 class="subsection-title">1.1 Desain yang Mudah Dipahami &amp; Ramah Pengguna Awam</h3>
    <p>
        Setiap formulir pada aplikasi telah dilengkapi dengan kotak pencarian otomatis, teks bantuan penjelas, dan tombol konfirmasi yang jelas. Hal ini bertujuan agar pengguna terhindar dari salah ketik dan tidak perlu menghafal kode-kode barang yang rumit.
    </p>

    <h3 class="subsection-title">1.2 Menu Navigasi Sidebar &amp; Ringkasan Dashboard</h3>
    <ul>
        <li>
            <strong>Menu Samping (Sidebar):</strong> Terletak di sebelah kiri layar, mengelompokkan modul aplikasi seperti Dashboard, Peminjaman, Data Barang, Kartu Stok, dan Laporan. Menu akan menyesuaikan secara otomatis dengan akun yang sedang login.
        </li>
        <li>
            <strong>Kartu Ringkasan (Dashboard):</strong> Terletak di halaman depan setelah masuk, menampilkan angka langsung mengenai total stok barang di gudang, berapa barang yang sedang dipinjam pihak lain, dan berapa transaksi yang sudah selesai.
        </li>
        <li>
            <strong>Kotak Pilihan Pintar (TomSelect):</strong> Pada saat memilih barang atau instansi, Anda cukup mengetikkan huruf awal nama yang dicari, dan sistem akan langsung menampilkan daftar yang cocok tanpa perlu menggulir ke bawah satu per satu.
        </li>
    </ul>

    <h3 class="subsection-title">1.3 Arti Warna Status Barang &amp; Transaksi</h3>
    <p>
        Untuk mempermudah melihat kondisi transaksi tanpa harus membaca detail teks yang panjang, sistem menggunakan penanda warna:
    </p>
    <ul>
        <li>
            <span class="badge-status badge-success">SELESAI</span> : Barang pinjaman sudah dikembalikan semuanya ke gudang secara utuh dan transaksi telah ditutup.
        </li>
        <li>
            <span class="badge-status badge-warning">AKTIF</span> : Barang masih berada di tangan instansi peminjam dan memiliki batas tanggal pengembalian yang perlu dipantau.
        </li>
        <li>
            <span class="badge-status badge-danger">HABIS</span> : Fisik barang di gudang sedang kosong (0 unit) sehingga sementara waktu tidak bisa dipinjamkan.
        </li>
        <li>
            <span class="badge-status badge-primary">TERSEDIA</span> : Barang ada di gudang dan siap dipinjamkan jika ada permohonan.
        </li>
    </ul>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 4: BAB II PERAN PENGGUNA & MENU AKSES                    --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB II. Peran Pengguna &amp; Menu Akses (Role)</h2>
    <p>
        Untuk menjaga kerapian dan keamanan data bersama, hak akses pengguna di dalam SIM-ASET dibagi menjadi 3 tingkatan peran:
    </p>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 22%;">Peran (Role)</th>
                <th style="width: 42%;">Tugas Utama</th>
                <th style="width: 36%;">Jangkauan Menu</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Super Admin</strong></td>
                <td>
                    Penanggung jawab utama sistem: membuat dan mengelola akun pengguna, mereset password staf jika lupa, mengatur kategori barang, serta memantau seluruh transaksi dan log mutasi di sistem.
                </td>
                <td>
                    Dapat membuka seluruh menu aplikasi tanpa batasan.
                </td>
            </tr>
            <tr>
                <td><strong>Admin Logistik</strong></td>
                <td>
                    Pengelola data inventaris: memasukkan data barang baru, mencetak label stiker barcode/QR code, mendaftarkan nama instansi peminjam, dan mengisi mutasi fisik di kartu stok.
                </td>
                <td>
                    Dapat mengelola barang dan stok, namun tidak bisa mengedit atau menambah akun staf lain.
                </td>
            </tr>
            <tr>
                <td><strong>Staff Logistik</strong></td>
                <td>
                    Petugas loket lapangan: mencatat transaksi peminjaman baru, memindai barcode barang saat diambil atau dikembalikan, dan mencetak lembar Berita Acara Serah Terima (BAST).
                </td>
                <td>
                    Hanya melihat daftar transaksi yang dicatat oleh akun dirinya sendiri demi keamanan dan privasi kerja.
                </td>
            </tr>
        </tbody>
    </table>

    <div class="box-info">
        <strong>Pentingnya Akun Pribadi:</strong> Gunakan akun masing-masing saat mencatat transaksi. Setiap transaksi yang dibuat akan mencantumkan nama petugas yang bertanggung jawab agar mudah dikonfirmasi jika ada barang yang perlu ditelusuri.
    </div>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 5: BAB III CARA MENCATAT PEMINJAMAN                      --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB III. Cara Mencatat Peminjaman Barang</h2>
    <p>
        Pencatatan peminjaman barang dilakukan melalui 5 langkah praktis berikut agar barang yang keluar selalu terpantau dengan rapi:
    </p>

    <ol>
        <li>
            <span class="step-number">Langkah 1 (Buka Form):</span> Klik menu <strong>Peminjaman</strong> di sebelah kiri, lalu tekan tombol <strong>+ Buat Peminjaman</strong>.
        </li>
        <li>
            <span class="step-number">Langkah 2 (Pilih Peminjam):</span> Pada kotak <strong>Instansi / Peminjam</strong>, ketik nama dinas, bagian, atau nama orang yang meminjam, lalu pilih dari daftar yang muncul.
        </li>
        <li>
            <span class="step-number">Langkah 3 (Tentukan Tanggal):</span> Masukkan <strong>Tanggal Pinjam</strong> dan tanggal <strong>Batas Pengembalian</strong>. Tuliskan keperluan peminjaman pada kolom Catatan (contoh: "Kegiatan pelatihan di aula lantai 2").
        </li>
        <li>
            <span class="step-number">Langkah 4 (Pilih Barang):</span> Masukkan barang yang dipinjam dengan salah satu cara berikut:
            <ul>
                <li><strong>Gunakan Pemindai (Scan):</strong> Arahkan scanner laser atau kamera ke label barcode/QR code pada bodi barang. Barang akan otomatis masuk ke tabel.</li>
                <li><strong>Pilih Manual:</strong> Pilih nama barang dari menu dropdown, lalu isi berapa unit yang dipinjam. Jika meminjam beberapa jenis barang yang berbeda, klik tombol <em>+ Tambah Baris</em>.</li>
            </ul>
        </li>
        <li>
            <span class="step-number">Langkah 5 (Simpan):</span> Periksa kembali daftar barang dan jumlahnya, kemudian klik tombol <strong>Proses &amp; Simpan Peminjaman</strong>. Stok barang di sistem akan langsung terpotong secara otomatis.
        </li>
    </ol>

    <div class="box-warning">
        <strong>Pencegahan Kehabisan Stok:</strong> Sistem tidak akan memproses peminjaman jika jumlah yang Anda ketik melebihi sisa stok riil yang ada di gudang.
    </div>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 6: BAB IV CARA MEMPROSES PENGEMBALIAN                    --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB IV. Cara Memproses Pengembalian Barang</h2>
    <p>
        Ketika peminjam membawa barang kembali ke gudang, staf logistik mencatat pengembalian tersebut dengan cara:
    </p>

    <h3 class="subsection-title">4.1 Pengembalian Lengkap Sekaligus</h3>
    <p>
        Jika seluruh barang yang dipinjam dikembalikan lengkap dalam waktu bersamaan:
    </p>
    <ul>
        <li>Buka rincian peminjaman dari menu <strong>Peminjaman</strong>.</li>
        <li>Periksa kondisi fisik barang, kabel adaptor, dan kelengkapannya.</li>
        <li>Klik tombol <strong>"Kembalikan Semua Sisa"</strong>. Sistem akan otomatis mengisi angka barang yang kembali.</li>
        <li>Klik tombol <strong>Simpan Pengembalian</strong>. Status transaksi berubah menjadi <span class="badge-status badge-success">SELESAI</span> dan stok gudang otomatis bertambah kembali.</li>
    </ul>

    <h3 class="subsection-title">4.2 Pengembalian Bertahap (Sebagian Unit Belum Kembali)</h3>
    <p>
        Jika peminjam baru mengembalikan sebagian barang (misal: meminjam 4 proyektor, baru dikembalikan 2 proyektor):
    </p>
    <ul>
        <li>Buka rincian transaksi peminjaman.</li>
        <li>Ketikkan angka <strong>2</strong> pada kolom pengembalian hari ini untuk barang tersebut.</li>
        <li>Biarkan sisa 2 unit lainnya tetap belum dikembalikan.</li>
        <li>Klik tombol <strong>Simpan Pengembalian</strong>. Transaksi akan tetap berstatus <span class="badge-status badge-warning">AKTIF</span> sampai sisa barang diserahkan kembali.</li>
    </ul>

    <h3 class="subsection-title">4.3 Mempercepat Pengembalian dengan Scan Barcode</h3>
    <p>
        Anda dapat langsung mengarahkan alat scan barcode ke stiker barang yang dikembalikan. Sistem akan otomatis mengenali barang tersebut di dalam daftar peminjaman dan mengisi angka pengembalian secara tepat.
    </p>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 7: BAB V MASTER BARANG & CETAK LABEL                     --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB V. Master Data Barang &amp; Cara Cetak Label</h2>
    <p>
        Data master barang memuat identitas aset, kategori, merek, dan nomor identifikasi unik (SKU).
    </p>

    <h3 class="subsection-title">5.1 Mengenal Kode Unik SKU Otomatis</h3>
    <p>
        Aplikasi secara otomatis membuatkan kode SKU unik saat barang baru didaftarkan, berdasarkan singkatan kategori barang (contoh: Kategori Elektronik disingkat <code>ELEC-001</code>, <code>ELEC-002</code>, dst). Dengan adanya kode ini, setiap fisik barang memiliki nomor pengenal tersendiri yang tidak tertukar.
    </p>

    <h3 class="subsection-title">5.2 Tiga Pilihan Format Cetak Label Stiker</h3>
    <p>
        Pada halaman Master Barang, Anda dapat mencetak stiker label aset dengan 3 varian:
    </p>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 25%;">Varian Label</th>
                <th style="width: 45%;">Tampilan Label</th>
                <th style="width: 30%;">Saran Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Standar (Lengkap)</strong></td>
                <td>
                    Menampilkan garis Barcode 1D di sebelah kiri dan kode QR 2D di sebelah kanan, disertai nama barang dan nomor SKU.
                </td>
                <td>
                    Paling disarankan untuk bodi laptop, PC, proyektor, dan printer.
                </td>
            </tr>
            <tr>
                <td><strong>Barcode 1D Saja</strong></td>
                <td>
                    Menampilkan garis barcode memanjang ke samping dengan nomor SKU di bawahnya.
                </td>
                <td>
                    Cocok untuk kardus kemasan, rak barang, atau map berkas.
                </td>
            </tr>
            <tr>
                <td><strong>QR Code 2D Saja</strong></td>
                <td>
                    Menampilkan kode kotak matriks yang praktis dipindai dari kamera ponsel.
                </td>
                <td>
                    Sangat cocok untuk barang berukuran kecil seperti kamera, handy-talkie, atau modem.
                </td>
            </tr>
        </tbody>
    </table>

    <div class="box-info">
        <strong>Mencetak Banyak Label Sekaligus:</strong> Centang beberapa barang yang ingin dicetak pada tabel data barang, lalu klik tombol <em>"Cetak Label Terpilih"</em> di atas tabel untuk mencetak semuanya dalam satu lembar stiker.
    </div>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 8: BAB VI KARTU STOK, LAPORAN & BAST                     --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB VI. Kartu Stok, Laporan, &amp; Berita Acara (BAST)</h2>
    <p>
        Modul ini digunakan untuk memantau sirkulasi barang di luar transaksi peminjaman serta menghasilkan dokumen pertanggungjawaban.
    </p>

    <h3 class="subsection-title">6.1 Menyesuaikan Stok Barang (Kartu Stok)</h3>
    <ul>
        <li>
            <strong>Barang Masuk (Stock In):</strong> Dipilih saat ada penambahan barang baru yang datang dari pengadaan atau hibah.
        </li>
        <li>
            <strong>Barang Keluar / Rusak / Hilang (Stock Out / Broken / Lost):</strong> Dipilih saat ada barang yang rusak berat, hilang, atau dihapuskan dari daftar inventaris.
        </li>
    </ul>

    <h3 class="subsection-title">6.2 Mengunduh Laporan Rekapitulasi</h3>
    <p>
        Buka menu <strong>Laporan</strong>, pilih bulan dan tahun yang ingin dilihat, lalu klik tombol <strong>Export PDF</strong>. Dokumen laporan rekapitulasi peminjaman akan otomatis diunduh dalam bentuk PDF rapi lengkap dengan nomor kontak peminjam dan alamat instansi.
    </p>

    <h3 class="subsection-title">6.3 Mencetak Berita Acara Serah Terima (BAST PDF)</h3>
    <p>
        Pada setiap transaksi peminjaman, terdapat tombol <strong>Cetak BAST</strong>. Tombol ini akan membuka lembar bukti serah terima resmi yang siap dicetak untuk ditandatangani oleh pihak peminjam dan petugas penyerah barang.
    </p>

    <div class="page-break"></div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 9: BAB VII PEMECAHAN MASALAH & FAQ                       --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <h2 class="section-title">BAB VII. Pemecahan Masalah &amp; Tanya Jawab (FAQ)</h2>
    <p>
        Jika Anda mengalami kesulitan saat mengoperasikan aplikasi, silakan ikuti petunjuk praktis berikut:
    </p>

    <h3 class="subsection-title">7.1 Solusi Scanner Kamera &amp; Alat Barcode USB</h3>
    <ul>
        <li>
            <strong>Kamera Tidak Terbuka di Layar:</strong>
            <br>Periksa notifikasi izin di browser Anda (di samping kolom alamat website) dan pastikan opsi <strong>"Allow / Izinkan Kamera"</strong> sudah aktif.
        </li>
        <li>
            <strong>Kamera di Ponsel Buram:</strong>
            <br>Gunakan pilihan dropdown kamera dan pilih opsi <em>"Kamera Belakang (Mobile / Default)"</em> agar lensa dapat memfokuskan barcode secara tajam.
        </li>
        <li>
            <strong>Alat Scanner USB Tidak Mengetik:</strong>
            <br>Pastikan kursor mouse Anda sudah diklik di dalam kotak input SKU sebelum menekan pelatuk scanner laser.
        </li>
    </ul>

    <h3 class="subsection-title">7.2 Pertanyaan yang Sering Diajukan (FAQ)</h3>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 35%;">Pertanyaan</th>
                <th style="width: 65%;">Penjelasan &amp; Solusi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Mengapa stok langsung berkurang padahal barang belum diambil?</strong></td>
                <td>
                    Sistem langsung mengamankan stok saat transaksi disimpan agar barang tersebut tidak dipinjam oleh staf lain pada waktu yang sama.
                </td>
            </tr>
            <tr>
                <td><strong>Bagaimana jika peminjam lupa mengembalikan tepat waktu?</strong></td>
                <td>
                    Transaksi akan tetap berstatus "Aktif" dengan warna oranye. Anda dapat melihat nomor telepon peminjam di menu peminjaman untuk menghubungi yang bersangkutan.
                </td>
            </tr>
            <tr>
                <td><strong>Bagaimana jika jaringan internet kantor mendadak mati?</strong></td>
                <td>
                    Gunakan <strong>dokumen panduan PDF ini</strong> yang telah disimpan atau dicetak untuk menjalankan pencatatan sementara secara manual sampai koneksi jaringan kembali pulih.
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 14px; text-align: center; font-size: 8pt; color: #94a3b8;">
        <strong>SIM-ASET</strong> &bull; Buku Panduan Operasional Pengguna &bull; Dibuat oleh <strong>Aiyub Heriyanto</strong> (Tahun 2026)
    </div>

</body>
</html>
