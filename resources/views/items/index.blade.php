@extends('layouts.app')

@section('title', 'Master Data Barang — SIM-ASET')
@section('page-title', 'Master Data Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Master Data Barang</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftar seluruh inventaris aset yang terdaftar</p>
    </div>
    @can('items.create')
    <a href="{{ route('items.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Barang
    </a>
    @endcan
</div>

<div class="card">
    <div class="px-4 py-3 border-bottom bg-light">
        <form method="GET" action="{{ route('items.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, SKU, vendor..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" id="item_category_filter" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="ownership_status" id="item_ownership_filter" class="form-select form-select-sm">
                    <option value="">Semua Kepemilikan</option>
                    <option value="internal" {{ request('ownership_status') == 'internal' ? 'selected' : '' }}>Milik Sendiri</option>
                    <option value="vendor" {{ request('ownership_status') == 'vendor' ? 'selected' : '' }}>Milik Vendor</option>
                    <option value="external" {{ request('ownership_status') == 'external' ? 'selected' : '' }}>Milik Instansi Lain</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    @hasanyrole('Super Admin|Admin')
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom gap-3 flex-wrap">
        <label class="form-check mb-0 d-flex align-items-center gap-2">
            <input type="checkbox" id="select-all-items" class="form-check-input">
            <span>Pilih semua</span>
        </label>
        <div>
            <button type="button" id="bulk-label-button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2 2h2v2H2z"/><path d="M6 0v6H0V0zM1 1v4h4V1zm4 7H0v6h6V8zm-5 1h4v4H1zm11-9h-2v2h2zm4 0h-6v6h6zm-5 1h4v4h-4zm-1 9h-2v2h2zm4 0h-2v2h2zm-2 2h-2v2h2zm4 0h-2v2h2zm-2 2h-2v2h2zm-4-4h-2v2h2zm2-2h-2v2h2zm-2-2h-2v2h2z"/>
                </svg>
                Cetak Label Terpilih
            </button>
        </div>
    </div>
    @endhasanyrole
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    @hasanyrole('Super Admin|Admin')
                    <th class="ps-4" style="width:5%;"><input type="checkbox" id="select-all-header" class="form-check-input" aria-label="Pilih semua barang"></th>
                    @endhasanyrole
                    <th style="width:5%;">No</th>
                    <th style="width:14%;">Kode (SKU)</th>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th style="width:14%;">Kategori</th>
                    <th class="text-center" style="width:11%;">Total Fisik</th>
                    <th class="text-center" style="width:11%;">Tersedia</th>
                    @hasanyrole('Super Admin|Admin')
                    <th class="text-center pe-4" style="width:9%;">Aksi</th>
                    @endhasanyrole
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    @php $utilization = $item->total_qty > 0 ? round((($item->total_qty - $item->available_qty) / $item->total_qty) * 100) : 0; @endphp
                    <tr>
                        @hasanyrole('Super Admin|Admin')
                        <td class="ps-4">
                            <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}" aria-label="Pilih {{ $item->name }}">
                        </td>
                        @endhasanyrole
                        <td class="text-muted">{{ $items->firstItem() + $index }}</td>
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:600; color:#2563eb;">
                                {{ $item->sku }}
                            </span>
                        </td>
                        <td>
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="Foto" style="max-height: 40px; border-radius: 4px;">
                            @else
                                <span class="text-muted" style="font-size: 0.75rem;">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600; color:#1e293b;">{{ $item->name }}</div>
                            @if(($item->ownership_status ?? 'internal') === 'vendor')
                                <span class="badge mt-1" style="font-size:0.7rem; font-weight:500; background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
                                        <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h1a.5.5 0 0 1 0 1H0a.5.5 0 0 1 0-1h1V9a.5.5 0 0 1 .5-.5m2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5"/>
                                    </svg>Vendor: {{ $item->owner_name ?? 'Pihak Ketiga' }}
                                </span>
                            @elseif($item->ownership_status === 'external')
                                <span class="badge mt-1" style="font-size:0.7rem; font-weight:500; background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" class="me-1" viewBox="0 0 16 16" style="margin-top:-2px;">
                                        <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                        <path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1z"/>
                                    </svg>Instansi: {{ $item->owner_name ?? 'Instansi Lain' }}
                                </span>
                            @else
                                <span class="badge mt-1" style="font-size:0.7rem; font-weight:500; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;">
                                    Milik Sendiri
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:500; font-size:.75rem;">
                                {{ $item->category?->name ?? '—' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $item->total_qty }}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold {{ $item->available_qty == 0 ? 'text-danger' : 'text-success' }}">
                                {{ $item->available_qty }}
                            </span>
                            @if($item->total_qty > 0)
                                <div class="progress mt-1" style="height:3px; width:60px; margin:0 auto;">
                                    <div class="progress-bar {{ $utilization > 80 ? 'bg-danger' : 'bg-success' }}"
                                         style="width:{{ $utilization }}%;"></div>
                                </div>
                            @endif
                        </td>
                        @hasanyrole('Super Admin|Admin')
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <a href="{{ route('items.label', ['item' => $item->id, 'type' => '2d']) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Cetak Label Barcode Kotak">
                                    Cetak Label
                                </a>
                            </div>
                        </td>
                        @endhasanyrole
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.922l6.5 2.6z"/>
                            </svg>
                            Belum ada data barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllHeader = document.getElementById('select-all-header');
        const selectAllLabel = document.getElementById('select-all-items');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkButton = document.getElementById('bulk-label-button');

        const syncSelectAll = () => {
            const allChecked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(cb => cb.checked);
            selectAllHeader.checked = allChecked;
            selectAllLabel.checked = allChecked;
        };

        const toggleAll = (checked) => {
            checkboxes.forEach(cb => cb.checked = checked);
            syncSelectAll();
        };

        selectAllHeader?.addEventListener('change', (e) => toggleAll(e.target.checked));
        selectAllLabel?.addEventListener('change', (e) => toggleAll(e.target.checked));
        checkboxes.forEach(cb => cb.addEventListener('change', syncSelectAll));

        bulkButton?.addEventListener('click', function () {
            const selected = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (!selected.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih minimal satu barang terlebih dahulu.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            const url = new URL('{{ route('items.labels') }}', window.location.origin);
            url.searchParams.set('type', '2d');
            selected.forEach(id => url.searchParams.append('items[]', id));
            window.open(url.toString(), '_blank');
        });

        const categoryFilter = document.getElementById('item_category_filter');
        if (categoryFilter && typeof TomSelect !== 'undefined') {
            let catInit = false;
            new TomSelect(categoryFilter, {
                create: false,
                placeholder: 'Semua Kategori',
                allowEmptyOption: true,
                onInitialize: function() {
                    catInit = true;
                },
                onChange: function() {
                    if (!catInit) return;
                    categoryFilter.closest('form').submit();
                }
            });
        }

        const ownershipFilter = document.getElementById('item_ownership_filter');
        if (ownershipFilter && typeof TomSelect !== 'undefined') {
            let ownInit = false;
            new TomSelect(ownershipFilter, {
                create: false,
                placeholder: 'Semua Kepemilikan',
                allowEmptyOption: true,
                onInitialize: function() {
                    ownInit = true;
                },
                onChange: function() {
                    if (!ownInit) return;
                    ownershipFilter.closest('form').submit();
                }
            });
        }
    });
</script>

@endsection
