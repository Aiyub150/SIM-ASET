@extends('layouts.app')

@section('title', isset($item) ? 'Edit Barang — SIM-ASET' : 'Tambah Barang — SIM-ASET')
@section('page-title', isset($item) ? 'Edit Master Barang' : 'Tambah Master Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ isset($item) ? 'Edit Master Barang' : 'Tambah Barang Baru' }}</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ isset($item) ? 'Perbarui identitas barang yang ada' : 'Daftarkan aset baru ke dalam sistem' }}
        </p>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
</div>

<div class="row">
    <div class="col-md-7 mx-auto">
        <div class="card">
            <div class="card-header">{{ isset($item) ? 'Identitas Barang' : 'Data Barang Baru' }}</div>
            <div class="card-body p-4">

                @if(!isset($item))
                    <div class="alert alert-info mb-4" style="font-size:.84rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <strong>Kode SKU</strong> akan di-generate otomatis berdasarkan kategori yang dipilih.
                        Stok awal otomatis <strong>0</strong> — tambahkan stok melalui menu <strong>Kartu Stok</strong>.
                    </div>
                @endif

                <form action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}" method="POST">
                    @csrf
                    @if(isset($item)) @method('PUT') @endif

                    {{-- KATEGORI — wajib, menentukan prefix SKU --}}
                    <div class="mb-3">
                        <label class="form-label">Kategori Barang</label>
                        <select name="category_id" id="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                {{ isset($item) ? 'disabled' : 'required' }}>
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-prefix="{{ $category->sku_prefix }}"
                                        data-next="{{ $category->next_sku_number }}"
                                        {{ old('category_id', $item->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- Saat edit, kirim value via hidden karena select disabled tidak dikirim --}}
                        @if(isset($item))
                            <input type="hidden" name="category_id" value="{{ $item->category_id }}">
                            <div class="form-text text-muted" style="font-size:.78rem;">
                                Kategori tidak dapat diubah setelah barang tersimpan.
                            </div>
                        @endif
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- SKU — readonly untuk tambah (auto-generate), readonly untuk edit --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Kode Barang (SKU)
                            @if(!isset($item))
                                <span class="badge ms-1" style="background:#eff6ff; color:#2563eb; font-size:.7rem; font-weight:500;">
                                    Auto-generate
                                </span>
                            @endif
                        </label>
                        <input type="text" name="sku" id="sku_preview"
                               class="form-control @error('sku') is-invalid @enderror"
                               value="{{ old('sku', $item->sku ?? '') }}"
                               placeholder="Pilih kategori untuk melihat pratinjau SKU"
                               readonly
                               style="font-family:monospace; background:#f8fafc; color:#64748b;">
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            @if(isset($item))
                                SKU tidak dapat diubah setelah barang tersimpan.
                            @else
                                SKU ditentukan otomatis. Format: <code>PREFIX-NNN</code> (contoh: <code>FURN-003</code>).
                            @endif
                        </div>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- NAMA BARANG --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $item->name ?? '') }}"
                               placeholder="Contoh: Tenda Pleton 6x14 Meter" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- QTY — hanya Super Admin --}}
                    @role('Super Admin')
                    <div class="mb-4">
                        <label class="form-label">
                            Jumlah Awal (Qty)
                            <span class="text-muted" style="font-weight:400;">(opsional)</span>
                        </label>
                        <input type="number" name="total_qty"
                               class="form-control @error('total_qty') is-invalid @enderror"
                               value="{{ old('total_qty', $item->total_qty ?? '') }}"
                               min="0" step="1" placeholder="0">
                        <div class="form-text text-muted" style="font-size:.78rem;">
                            @if(isset($item))
                                Mengubah total qty akan menyesuaikan stok tersedia secara proporsional.
                            @else
                                Kosongkan jika stok belum diketahui — bisa ditambah melalui menu Kartu Stok.
                            @endif
                        </div>
                        @error('total_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @endrole

                    <button type="submit"
                            class="btn {{ isset($item) ? 'btn-warning' : 'btn-primary' }} w-100 fw-bold py-2">
                        {{ isset($item) ? 'Simpan Perubahan' : 'Simpan Barang Baru' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(!isset($item))
<script>
(function () {
    const sel     = document.getElementById('category_id');
    const preview = document.getElementById('sku_preview');

    function updatePreview() {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.dataset.prefix) {
            preview.value = '';
            preview.placeholder = 'Pilih kategori untuk melihat pratinjau SKU';
            return;
        }
        const prefix = opt.dataset.prefix;
        const num    = parseInt(opt.dataset.next, 10);
        preview.value = prefix + '-' + String(num).padStart(3, '0');
    }

    sel.addEventListener('change', updatePreview);

    // Restore saat ada old() setelah validation error
    if (sel.value) updatePreview();
})();
</script>
@endif
@endpush
