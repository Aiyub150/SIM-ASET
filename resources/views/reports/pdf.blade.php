<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulan {{ $monthName }} {{ $year }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; }
        .text-center { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #d9d9d9; font-weight: bold; }
        .section-title { font-weight: bold; margin-bottom: 10px; margin-top: 20px; }
    </style>
</head>
<body>

    <h2 class="text-center" style="margin-bottom: 5px;">REKAPITULASI INVENTARIS ASET DAERAH</h2>
    <h3 class="text-center" style="margin-top: 0; font-weight: normal;">Periode: {{ $monthName }} {{ $year }}</h3>
    <hr style="border: 1px solid #000; margin-bottom: 20px;">

    <div class="section-title">A. RINCIAN TRANSAKSI PEMINJAMAN ASET</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal Pinjam</th>
                <th style="width: 20%;">Kode Transaksi</th>
                <th style="width: 35%;">Instansi Peminjam</th>
                <th style="width: 15%;">Tgl Jatuh Tempo</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $index => $loan)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                    <td>{{ $loan->loan_code }}</td>
                    <td>{{ $loan->borrower->institution_name }}</td>
                    <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                    <td class="text-center">{{ strtoupper($loan->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada transaksi tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">B. RINCIAN BUKU BESAR (MUTASI FISIK ASET)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Waktu Eksekusi</th>
                <th style="width: 25%;">Nomor Referensi (Surat)</th>
                <th style="width: 35%;">Nama Barang</th>
                <th style="width: 10%; text-align: center;">Tipe Mutasi</th>
                <th style="width: 10%; text-align: center;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $index => $mov)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->reference_code }}</td>
                    <td>{{ $mov->item->name }}</td>
                    <td class="text-center">{{ strtoupper($mov->type) }}</td>
                    <td class="text-center">{{ $mov->qty }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada mutasi stok tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px; float: right; width: 300px; text-align: center;">
        <p>Mengetahui,</p>
        <p style="margin-bottom: 70px;">Kepala Bagian Aset Daerah</p>
        <p style="font-weight: bold; text-decoration: underline;">( Nama Pimpinan )</p>
        <p>NIP. .........................</p>
    </div>

</body>
</html>