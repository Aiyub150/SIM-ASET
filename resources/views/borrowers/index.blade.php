@extends('layouts.app')

@section('title', 'Master Data Instansi — SIM-ASET')
@section('page-title', 'Master Data Instansi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Master Data Instansi</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Daftar instansi atau pihak yang dapat meminjam aset</p>
    </div>
    <a href="{{ route('borrowers.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Instansi
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width:5%;">#</th>
                    <th>Nama Instansi / Organisasi</th>
                    <th style="width:18%;">Penanggung Jawab</th>
                    <th style="width:15%;">Nomor Kontak</th>
                    <th style="width:20%;">Alamat</th>
                    <th class="text-center pe-4" style="width:9%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowers as $index => $borrower)
                    <tr>
                        <td class="ps-4 text-muted">{{ $borrowers->firstItem() + $index }}</td>
                        <td style="font-weight:500;">{{ $borrower->institution_name }}</td>
                        <td>{{ $borrower->pic_name }}</td>
                        <td>
                            <span style="font-family:monospace; font-size:.83rem;">{{ $borrower->contact_number }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.83rem;">{{ $borrower->address ?: '—' }}</td>
                        <td class="text-center pe-4">
                            <a href="{{ route('borrowers.edit', $borrower->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#cbd5e1" viewBox="0 0 16 16" class="d-block mx-auto mb-2">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                            Belum ada data instansi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($borrowers->hasPages())
    <div class="px-4 py-3 border-top">
        {{ $borrowers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
