@extends('layouts.app')

@section('title', 'Informasi Versi — SIM-ASET')
@section('page-title', 'Informasi Versi')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center p-5">
                <img src="{{ asset('images/sim-aset_logo.svg') }}" alt="Logo SIM-ASET" class="d-inline-block" style="width: 100px; height: 100px; margin-bottom: 1.5rem;">
                <h4 class="fw-bold mb-1">SIM-ASET</h4>
                <p class="text-muted mb-4">Sistem Inventaris Aset Daerah</p>

                <div class="d-flex justify-content-center gap-3 mb-4">
                    <span class="badge bg-primary px-3 py-2">Versi 1.0.0</span>
                    <span class="badge bg-success px-3 py-2">Stable Build</span>
                </div>

                <div class="text-start bg-light p-4 rounded text-muted" style="font-size: 0.9rem;">
                    <p class="mb-2"><strong>Platform:</strong> Laravel 12.x / PHP 8.2+</p>
                    <p class="mb-2"><strong>Frontend:</strong> Bootstrap 5, Blade, TomSelect</p>
                    <p class="mb-2"><strong>Lisensi:</strong> Free Open-Source</p>
                    <p class="mb-0"><strong>Dibuat Oleh:</strong> Aiyub Heriyanto</p>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-3 text-muted" style="font-size: 0.85rem;">
                &copy; {{ date('Y') }} SIM-ASET. Hak cipta dilindungi.
            </div>
        </div>
    </div>
</div>
@endsection
