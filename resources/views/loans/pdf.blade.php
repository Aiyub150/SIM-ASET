<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BAST Peminjaman - {{ $loan->loan_code }}</title>
    <style>
        /* CSS murni standar, hindari external link/CDN */
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        
        /* Kop Surat */
        .kop-surat { border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; text-align: center; }
        .kop-surat h2, .kop-surat h3, .kop-surat p { margin: 0; padding: 0; }
        
        /* Tabel Konten */
        .table-data th, .table-data td { border: 1px solid #000; padding: 8px; text-align: left; }
        .table-data th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        
        /* Bagian Tanda Tangan */
        .signature-area { margin-top: 50px; width: 100%; }
        .signature-area td { border: none; text-align: center; width: 50%; vertical-align: bottom; height: 100px; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h3>PEMERINTAH DAERAH PROVINSI</h3>
        <h2>DINAS PENGELOLAAN ASET DAN BARANG</h2>
        <p>Jl. Jendral Sudirman No. 1, Telp: (021) 1234567</p>
    </div>

    <h3 style="text-align: center; text-decoration: underline; margin-bottom: 5px;">BERITA ACARA SERAH TERIMA PINJAM PAKAI BARANG</h3>
    <p style="text-align: center; margin-top: 0;">Nomor: {{ $loan->loan_code }} / BAST / {{ now()->year }}</p>

    <p>Pada hari ini, tanggal <strong>{{ $loan->borrow_date->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini:</p>

    <table style="margin-bottom: 20px;">
        <tr>
            <td style="width: 30%;">Nama Admin / Petugas</td>
            <td style="width: 5%;">:</td>
            <td style="width: 65%;"><strong>{{ $loan->user->name }}</strong></td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Pengelola Barang Milik Daerah</td>
        </tr>
        <tr>
            <td colspan="3">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</td>
        </tr>
    </table>

    <table style="margin-bottom: 20px;">
        <tr>
            <td style="width: 30%;">Nama Peminjam</td>
            <td style="width: 5%;">:</td>
            <td style="width: 65%;"><strong>{{ $loan->borrower->pic_name }}</strong></td>
        </tr>
        <tr>
            <td>Instansi / Dinas</td>
            <td>:</td>
            <td>{{ $loan->borrower->institution_name }}</td>
        </tr>
        <tr>
            <td colspan="3">Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</td>
        </tr>
    </table>

    <p>PIHAK PERTAMA menyerahkan barang kepada PIHAK KEDUA dengan rincian sebagai berikut untuk dikembalikan selambat-lambatnya pada tanggal <strong>{{ $loan->due_date->translatedFormat('d F Y') }}</strong>:</p>

    <table class="table-data" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Kode Barang (SKU)</th>
                <th style="width: 55%;">Nama Barang</th>
                <th style="width: 15%;" class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loan->loanItems as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->item->sku }}</td>
                    <td>{{ $detail->item->name }}</td>
                    <td class="text-center">{{ $detail->qty }} Unit</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Demikian Berita Acara Serah Terima ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

    <table class="signature-area">
        <tr>
            <td>
                <p>PIHAK KEDUA,</p>
                <br><br><br>
                <p><strong>{{ $loan->borrower->pic_name }}</strong></p>
                <p>NIP. .........................</p>
            </td>
            <td>
                <p>PIHAK PERTAMA,</p>
                <br><br><br>
                <p><strong>{{ $loan->user->name }}</strong></p>
                <p>NIP. .........................</p>
            </td>
        </tr>
    </table>

</body>
</html>