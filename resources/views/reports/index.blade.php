<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .filter-box { background: #f4f4f4; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; }
        .btn { padding: 8px 15px; border: none; cursor: pointer; color: white; text-decoration: none; border-radius: 4px; }
        .btn-blue { background: #0056b3; }
        .btn-red { background: #dc3545; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>

    <a href="{{ route('loans.index') }}" style="text-decoration: none; color: #555;">← Ke Dashboard</a>

    <h2>Laporan Bulanan Inventaris</h2>

    <div class="filter-box">
        <form action="{{ route('reports.index') }}" method="GET" style="display: inline-block;">
            <label>Bulan:</label>
            <select name="month">
                @for($i = 1; $i <= 12; $i++)
                    @php $val = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                    </option>
                @endfor
            </select>

            <label>Tahun:</label>
            <select name="year">
                @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="btn btn-blue">Filter Data</button>
        </form>

        <!-- Tombol Cetak (Meneruskan parameter filter yang sedang aktif) -->
        <a href="{{ route('reports.export-pdf', ['month' => $month, 'year' => $year]) }}" class="btn btn-red" target="_blank">
            Cetak PDF Laporan
        </a>
    </div>

    <h3>Pratinjau Peminjaman Baru</h3>
    <table>
        <tr><th>Tanggal</th><th>Kode</th><th>Peminjam</th><th>Status</th></tr>
        @forelse($loans as $loan)
            <tr>
                <td>{{ $loan->borrow_date->format('d/m/Y') }}</td>
                <td>{{ $loan->loan_code }}</td>
                <td>{{ $loan->borrower->institution_name }}</td>
                <td>{{ ucfirst($loan->status) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align: center;">Tidak ada transaksi peminjaman di bulan ini.</td></tr>
        @endforelse
    </table>

    <h3>Pratinjau Mutasi Stok Permanen</h3>
    <table>
        <tr><th>Tanggal</th><th>Ref/Surat</th><th>Barang</th><th>Tipe</th><th>Jumlah</th></tr>
        @forelse($movements as $mov)
            <tr>
                <td>{{ $mov->created_at->format('d/m/Y') }}</td>
                <td>{{ $mov->reference_code }}</td>
                <td>{{ $mov->item->name }}</td>
                <td>{{ strtoupper($mov->type) }}</td>
                <td>{{ $mov->qty }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align: center;">Tidak ada pergerakan stok di bulan ini.</td></tr>
        @endforelse
    </table>

</body>
</html>