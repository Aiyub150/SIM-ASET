<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Mutasi Stok Aset</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 600px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; font-size: 0.9em; display: block; margin-top: 5px; }
        .alert-error { background: #fee; padding: 10px; color: red; margin-bottom: 20px; border: 1px solid #fcc; }
        .alert-success { background: #efe; padding: 10px; color: green; margin-bottom: 20px; border: 1px solid #cfc; }
        .btn-submit { padding: 10px 20px; background: #0056b3; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #555; }
    </style>
</head>
<body>

    <a href="{{ route('stocks.index') }}" class="btn-back">← Kembali ke Kartu Stok</a>

    <h2>Form Input Mutasi Stok</h2>
    <p style="color: #666; font-size: 0.9em;">Gunakan form ini untuk mencatat pengadaan barang baru, barang rusak, atau hilang. Data yang sudah disimpan tidak dapat dihapus.</p>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('stocks.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Nomor Referensi (Surat/BAST/Faktur):</label>
            <input type="text" name="reference_code" value="{{ old('reference_code') }}" placeholder="Contoh: BAST/2026/08/001" required>
            @error('reference_code') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Pilih Barang:</label>
            <select name="item_id" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->sku }} - {{ $item->name }} (Total Fisik: {{ $item->total_qty }}, Tersedia: {{ $item->available_qty }})
                    </option>
                @endforeach
            </select>
            @error('item_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Tipe Mutasi:</label>
            <select name="type" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Barang Masuk / Pengadaan (+)</option>
                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Barang Keluar / Hibah (-)</option>
                <option value="broken" {{ old('type') == 'broken' ? 'selected' : '' }}>Barang Rusak Total (-)</option>
                <option value="lost" {{ old('type') == 'lost' ? 'selected' : '' }}>Barang Hilang (-)</option>
            </select>
            @error('type') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Jumlah (Qty):</label>
            <input type="number" name="qty" min="1" value="{{ old('qty') }}" required>
            @error('qty') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Catatan Tambahan (Opsional):</label>
            <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            @error('notes') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-submit">Simpan Mutasi Permanen</button>
    </form>

</body>
</html>