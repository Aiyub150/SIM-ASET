@extends('layouts.app')

@section('title', 'Master Data Barang — SIM-ASET')
@section('page-title', 'Master Data Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Master Data Barang</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftar seluruh inventaris aset yang terdaftar</p>
    </div>
    <a href="{{ route('items.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Barang
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:5%;">#</th>
                    <th style="width:14%;">Kode (SKU)</th>
                    <th>Nama Barang</th>
                    <th style="width:14%;">Kategori</th>
                    <th class="text-center" style="width:11%;">Total Fisik</th>
                    <th class="text-center" style="width:11%;">Tersedia</th>
                    <th class="text-center pe-4" style="width:9%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    @php $utilization = $item->total_qty > 0 ? round((($item->total_qty - $item->available_qty) / $item->total_qty) * 100) : 0; @endphp
                    <tr>
                        <td class="ps-4 text-muted">{{ $items->firstItem() + $index }}</td>
                        <td>
                            <span style="font-family:monospace; font-size:.82rem; font-weight:600; color:#2563eb;">
                                {{ $item->sku }}
                            </span>
                        </td>
                        <td style="font-weight:500;">{{ $item->name }}</td>
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
                        <td class="text-center pe-4">
                            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
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

@endsection
