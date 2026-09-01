<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Peminjaman: {{ $loan->loan_code }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 900px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .alert-error { background: #fee; padding: 10px; color: red; margin-bottom: 20px; border: 1px solid #fcc; }
        .alert-success { background: #efe; padding: 10px; color: green; margin-bottom: 20px; border: 1px solid #cfc; }
        .badge { padding: 5px 10px; color: white; border-radius: 4px; font-size: 0.9em; }
        .bg-green { background: green; }
        .bg-red { background: red; }
        .bg-yellow { background: #d39e00; }
        .item-row { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background: #fafafa; }
        .return-section { background: #e9ecef; padding: 20px; border-radius: 5px; margin-top: 30px; }
    </style>
</head>
<body>

    <a href="{{ route('loans.index') }}">← Kembali ke Daftar</a>
    <a href="{{ route('loans.print', $loan->id) }}" target="_blank">Cetak BAST PDF</a>

    <h2>Detail Peminjaman: {{ $loan->loan_code }}</h2>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- Informasi Utama -->
    <table style="width: 50%;">
        <tr><th>Instansi</th><td>{{ $loan->borrower->institution_name }} ({{ $loan->borrower->pic_name }})</td></tr>
        <tr><th>Admin Pencatat</th><td>{{ $loan->user->name }}</td></tr>
        <tr><th>Tanggal Pinjam</th><td>{{ $loan->borrow_date->format('d M Y') }}</td></tr>
        <tr><th>Jatuh Tempo</th><td>{{ $loan->due_date->format('d M Y') }}</td></tr>
        <tr><th>Status</th>
            <td>
                @if($loan->status === 'completed')
                    <span class="badge bg-green">Selesai ({{ $loan->return_date->format('d M Y') }})</span>
                @elseif($loan->due_date->isPast())
                    <span class="badge bg-red">Terlambat</span>
                @else
                    <span class="badge bg-yellow">Aktif</span>
                @endif
            </td>
        </tr>
    </table>

    <h3>Rincian Barang</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Barang (SKU)</th>
                <th>Jumlah Pinjam</th>
                <th>Sudah Kembali</th>
                <th>Sisa Hutang</th>
                <th>Status Item</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loan->loanItems as $detail)
                @php $sisa = $detail->qty - $detail->returned_qty; @endphp
                <tr>
                    <td>{{ $detail->item->name }} ({{ $detail->item->sku }})</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->returned_qty }}</td>
                    <td style="font-weight: bold; color: {{ $sisa > 0 ? 'red' : 'green' }}">{{ $sisa }}</td>
                    <td>
                        @if($sisa == 0)
                            <span style="color: green;">Lunas</span>
                        @else
                            <span style="color: red;">Belum Lunas</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tampilkan Form Pengembalian HANYA JIKA status bukan completed -->
    @if($loan->status !== 'completed' && $pendingItems->count() > 0)
        <div class="return-section">
            <h3>Proses Pengembalian Barang</h3>
            <p>Pilih barang yang ingin dikembalikan saat ini (bisa parsial):</p>
            
            <form action="{{ route('loans.return', $loan->id) }}" method="POST">
                @csrf
                
                <div id="return-items-container">
                    <div class="item-row" data-index="0">
                        <label>Barang:</label>
                        <select name="items[0][loan_item_id]" class="loan-item-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($pendingItems as $pending)
                                <option value="{{ $pending->id }}" data-max="{{ $pending->qty - $pending->returned_qty }}">
                                    {{ $pending->item->name }} (Sisa Hutang: {{ $pending->qty - $pending->returned_qty }})
                                </option>
                            @endforeach
                        </select>

                        <label>Jml Kembali:</label>
                        <!-- max akan diupdate lewat JS agar user tidak bisa input lebih dari hutang via HTML -->
                        <input type="number" name="items[0][return_qty]" class="return-qty-input" min="1" required>
                    </div>
                </div>

                <button type="button" id="btn-add-return" style="margin-bottom: 20px;">+ Tambah Barang Lain</button>
                <br>
                <button type="submit" style="padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer;">
                    Eksekusi Pengembalian
                </button>
            </form>
        </div>
    @endif

    <!-- Template JS -->
    <div id="return-template" style="display: none;">
        <div class="item-row">
            <label>Barang:</label>
            <select class="loan-item-select" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($pendingItems as $pending)
                    <option value="{{ $pending->id }}" data-max="{{ $pending->qty - $pending->returned_qty }}">
                        {{ $pending->item->name }} (Sisa Hutang: {{ $pending->qty - $pending->returned_qty }})
                    </option>
                @endforeach
            </select>
            <label>Jml Kembali:</label>
            <input type="number" class="return-qty-input" min="1" required>
            <button type="button" class="btn-remove">Batal</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika untuk mengubah max attribut input number sesuai sisa hutang
            function attachMaxLogic(row) {
                const select = row.querySelector('.loan-item-select');
                const input = row.querySelector('.return-qty-input');
                
                select.addEventListener('change', function() {
                    const selectedOption = select.options[select.selectedIndex];
                    const maxQty = selectedOption.getAttribute('data-max');
                    if(maxQty) {
                        input.setAttribute('max', maxQty);
                        input.value = maxQty; // Auto-fill dengan sisa maksimal
                    } else {
                        input.removeAttribute('max');
                        input.value = '';
                    }
                });
            }

            const firstRow = document.querySelector('#return-items-container .item-row');
            if(firstRow) attachMaxLogic(firstRow);

            let index = 1;
            const container = document.getElementById('return-items-container');
            const template = document.getElementById('return-template').querySelector('.item-row');
            const btnAdd = document.getElementById('btn-add-return');

            if(btnAdd) {
                btnAdd.addEventListener('click', function () {
                    const newRow = template.cloneNode(true);
                    newRow.querySelector('.loan-item-select').setAttribute('name', `items[${index}][loan_item_id]`);
                    newRow.querySelector('.return-qty-input').setAttribute('name', `items[${index}][return_qty]`);
                    
                    newRow.querySelector('.btn-remove').addEventListener('click', function () {
                        newRow.remove();
                    });

                    attachMaxLogic(newRow);
                    container.appendChild(newRow);
                    index++;
                });
            }
        });
    </script>
</body>
</html>