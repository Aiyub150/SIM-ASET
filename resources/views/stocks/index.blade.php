@extends('layouts.app')

@section('title', 'Kartu Stok — SIM-ASET')
@section('page-title', 'Kartu Stok (Buku Besar)')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Kartu Stok Aset</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Riwayat seluruh pergerakan fisik inventaris aset daerah</p>
    </div>
    @can('stocks.adjust')
    <a href="{{ route('stocks.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Input Mutasi Baru
    </a>
    @endcan
</div>

<div class="card">
    <div class="px-4 py-3 border-bottom bg-light">
        <form method="GET" action="{{ route('stocks.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ref/nama/SKU..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="type" id="stock_type_filter" class="form-select form-select-sm">
                    <option value="">Semua Tipe Mutasi</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk (Penambahan)</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar (Pengurangan)</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian (Audit)</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Waktu</th>
                    <th>No. Referensi</th>
                    <th>Barang</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Mutasi</th>
                    <th class="text-center">Saldo Awal</th>
                    <th class="text-center">Saldo Akhir</th>
                    <th>Catatan</th>
                    <th>Pencatat</th>
                    <th class="pe-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    @php $isIn = $movement->type === 'in'; @endphp
                    <tr>
                        <td class="ps-4" style="font-size:.8rem; color:#64748b;">
                            {{ $movement->created_at->userTzDate('d/m/Y') }}<br>
                            <span style="font-size:.73rem;">{{ $movement->created_at->userTzTime('H:i') }}</span>
                        </td>
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:600;">
                                {{ $movement->reference_code }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:500;">{{ $movement->item->name }}</span><br>
                            <span style="font-family:monospace; font-size:.75rem; color:#64748b;">{{ $movement->item->sku }}</span>
                        </td>
                        <td class="text-center">
                            @if($isIn)
                                <span class="badge bg-success">Masuk</span>
                            @elseif($movement->type === 'out')
                                <span class="badge bg-secondary">Keluar</span>
                            @elseif($movement->type === 'broken')
                                <span class="badge bg-danger">Rusak</span>
                            @else
                                <span class="badge" style="background:#1e293b; color:#fff;">Hilang</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold {{ $isIn ? 'text-success' : 'text-danger' }}">
                            {{ $isIn ? '+' : '−' }}{{ $movement->qty }}
                        </td>
                        <td class="text-center text-muted">{{ $movement->balance_before }}</td>
                        <td class="text-center fw-bold">{{ $movement->balance_after }}</td>
                        <td style="max-width: 180px;">
                            @if(!empty($movement->notes))
                                <span class="text-truncate d-inline-block text-secondary" style="max-width: 160px; font-size: .82rem;" title="{{ $movement->notes }}">
                                    {{ $movement->notes }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: .8rem; font-style: italic;">-</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:.83rem;">{{ $movement->user->name ?? '-' }}</td>
                        <td class="pe-4 text-center">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary py-1 px-2 btn-detail-movement"
                                    data-ref="{{ $movement->reference_code }}"
                                    data-item="{{ $movement->item->name }}"
                                    data-sku="{{ $movement->item->sku }}"
                                    data-type="{{ $movement->type }}"
                                    data-type-label="{{ $isIn ? 'Masuk (Penambahan)' : ($movement->type === 'out' ? 'Keluar (Pengurangan)' : ($movement->type === 'broken' ? 'Rusak' : 'Hilang')) }}"
                                    data-qty="{{ ($isIn ? '+' : '−') . $movement->qty }}"
                                    data-before="{{ $movement->balance_before }}"
                                    data-after="{{ $movement->balance_after }}"
                                    data-user="{{ $movement->user->name ?? '-' }}"
                                    data-time="{{ $movement->created_at->userTzDate('d M Y') }} {{ $movement->created_at->userTzTime('H:i') }}"
                                    data-notes="{{ $movement->notes ?? '' }}"
                                    title="Lihat Detail & Catatan Mutasi">
                                <i class="fa-solid fa-eye me-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                            </svg>
                            Belum ada riwayat mutasi stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $movements->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Modal Detail Mutasi Stok --}}
<div class="modal fade" id="modalDetailMovement" tabindex="-1" aria-labelledby="modalDetailMovementLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold" id="modalDetailMovementLabel">
                    <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Rincian Mutasi Stok
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <div class="text-muted small">No. Referensi</div>
                        <div class="fw-bold font-monospace text-primary fs-6" id="detail-ref">-</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Waktu Pencatatan</div>
                        <div class="fw-medium small" id="detail-time">-</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">Barang Inventaris</div>
                    <div class="fw-bold fs-6" id="detail-item">-</div>
                    <div class="small text-muted font-monospace" id="detail-sku">-</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Tipe Mutasi</div>
                            <span class="badge mt-1" id="detail-badge">-</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Perubahan Qty</div>
                            <div class="fw-bold mt-1 fs-6" id="detail-qty">-</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Saldo Sebelum</div>
                            <div class="fw-bold mt-1" id="detail-before">0</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light text-center">
                            <div class="text-muted small">Saldo Sesudah</div>
                            <div class="fw-bold mt-1 text-primary" id="detail-after">0</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1 fw-medium">
                        <i class="fa-solid fa-clipboard-list me-1 text-secondary"></i>Catatan / Alasan Mutasi:
                    </div>
                    <div class="p-3 border rounded bg-light-subtle text-dark" style="min-height: 75px; white-space: pre-wrap; font-size: .88rem;" id="detail-notes">-</div>
                </div>

                <div class="text-muted small pt-2 border-top d-flex align-items-center justify-content-between">
                    <span>Dicatat oleh:</span>
                    <strong class="text-dark" id="detail-user">-</strong>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal detail mutasi
        const detailModalEl = document.getElementById('modalDetailMovement');
        const detailModal = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;

        document.querySelectorAll('.btn-detail-movement').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('detail-ref').textContent = this.dataset.ref;
                document.getElementById('detail-time').textContent = this.dataset.time;
                document.getElementById('detail-item').textContent = this.dataset.item;
                document.getElementById('detail-sku').textContent = this.dataset.sku;
                document.getElementById('detail-qty').textContent = this.dataset.qty;
                document.getElementById('detail-before').textContent = this.dataset.before;
                document.getElementById('detail-after').textContent = this.dataset.after;
                document.getElementById('detail-user').textContent = this.dataset.user;
                
                const notes = (this.dataset.notes || '').trim();
                document.getElementById('detail-notes').textContent = notes ? notes : '(Tidak ada catatan tambahan)';

                const badge = document.getElementById('detail-badge');
                badge.textContent = this.dataset.typeLabel;
                badge.className = 'badge mt-1 ' + (this.dataset.type === 'in' ? 'bg-success' : (this.dataset.type === 'out' ? 'bg-secondary' : 'bg-danger'));

                if (detailModal) detailModal.show();
            });
        });
        const stockTypeFilter = document.getElementById('stock_type_filter');
        if (stockTypeFilter && typeof TomSelect !== 'undefined') {
            let typeInit = false;
            new TomSelect(stockTypeFilter, {
                create: false,
                controlInput: null,
                placeholder: 'Semua Tipe Mutasi',
                allowEmptyOption: true,
                onInitialize: function() {
                    typeInit = true;
                },
                onChange: function() {
                    if (!typeInit) return;
                    stockTypeFilter.closest('form').submit();
                }
            });
        }
    });
</script>
@endpush

@endsection
