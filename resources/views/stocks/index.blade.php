<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar / Kartu Stok Aset</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.9em; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .alert-success { background: #efe; padding: 10px; color: green; margin-bottom: 20px; border: 1px solid #cfc; }
        .btn-blue { padding: 8px 15px; background: #0056b3; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
        
        /* Indikator Tipe Mutasi */
        .type-in { color: #155724; background-color: #d4edda; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        .type-out { color: #721c24; background-color: #f8d7da; padding: 3px 8px; border-radius: 4px; font-weight: bold; }
        
        .qty-plus { color: green; font-weight: bold; }
        .qty-minus { color: red; font-weight: bold; }
        
        .pagination { margin-top: 20px; }
    </style>
</head>
<body>

    <a href="{{ route('loans.index') }}" style="text-decoration: none; color: #555;">← Ke Daftar Peminjaman</a>

    <h2>Kartu Stok Aset (Buku Besar)</h2>
    
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('stocks.create') }}" class="btn-blue">+ Input Mutasi Baru</a>

    <table>
        <thead>
            <tr>
                <th>Waktu Transaksi</th>
                <th>No. Referensi</th>
                <th>Barang (SKU)</th>
                <th>Tipe</th>
                <th>Mutasi (Qty)</th>
                <th>Saldo Awal</th>
                <th>Saldo Akhir</th>
                <th>Admin Pencatat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                @php
                    $isIn = $movement->type === 'in';
                @endphp
                <tr>
                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $movement->reference_code }}</strong></td>
                    <td>{{ $movement->item->name }} ({{ $movement->item->sku }})</td>
                    <td>
                        @if($isIn)
                            <span class="type-in">Masuk</span>
                        @else
                            <span class="type-out">{{ ucfirst($movement->type) }}</span>
                        @endif
                    </td>
                    
                    <!-- Warna hijau untuk masuk, merah untuk keluar -->
                    <td class="{{ $isIn ? 'qty-plus' : 'qty-minus' }}">
                        {{ $isIn ? '+' : '-' }} {{ $movement->qty }}
                    </td>
                    
                    <td style="color: #666;">{{ $movement->balance_before }}</td>
                    <td style="font-weight: bold;">{{ $movement->balance_after }}</td>
                    <td>{{ $movement->user->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Belum ada riwayat mutasi stok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $movements->links() }}
    </div>

</body>
</html>