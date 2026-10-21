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
                    <span class="badge bg-primary px-3 py-2 fs-6">Versi 1.2.0</span>
                    <span class="badge bg-success px-3 py-2 fs-6">Stable Build</span>
                </div>

                <div class="text-start bg-light p-4 rounded text-muted mb-4" style="font-size: 0.9rem;">
                    <p class="mb-2"><strong>Platform:</strong> Laravel 12.x / PHP 8.2+</p>
                    <p class="mb-2"><strong>Frontend:</strong> Bootstrap 5, Blade, TomSelect</p>
                    <p class="mb-2"><strong>Lisensi:</strong> Free Open-Source</p>
                    <p class="mb-0"><strong>Dibuat Oleh:</strong> Aiyub Heriyanto</p>
                </div>

                <div class="text-start border rounded p-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.575.111 1.17zm-.171 1.937q.05-.554.05-1.125h1q0 .626-.056 1.243zM8 5a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 9.293V5.5A.5.5 0 0 1 8 5m-4.833 7.858a7 7 0 0 0 1.205 1.205l-.663.75a8 8 0 0 1-1.377-1.377zm-1.378-1.377a7 7 0 0 0-.75-1.205l.75-.663a8 8 0 0 1 1.377 1.377zm-.75-1.205a7 7 0 0 0-.45-.893l.87-.493a7 7 0 0 1 .439.27l-.615.789a8 8 0 0 0-.244.327M1.019 8.515a7 7 0 0 0 .299.985l-.976.219A8 8 0 0 1 0 8.589l1.019-.074zm.45-2.004a7 7 0 0 0 .27.439l-.87.493a8 8 0 0 1-.654-.979l.789-.615a7 7 0 0 0 .302.418m1.79-1.834a7 7 0 0 0 .796.653l-.69.724a8 8 0 0 1-.91-.747zm1.352-.744a7 7 0 0 0 .468.214l-.45-.893a8 8 0 0 1 1.088-.45l.313.95a7 7 0 0 0-.483.179m2.507-.53a7 7 0 0 0 1.025.1l-.17-.985q-.575-.1-1.17-.111zm1.937.171q.554-.05 1.125-.05v-1q-.626 0-1.243.056z"/>
                        </svg>
                        Catatan Rilis (Changelog Versi 1.2.0)
                    </h6>
                    <ul class="list-unstyled mb-0" style="font-size: 0.88rem;">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">Feat</span>
                            <span><strong>Pengaturan Format Kode SKU & Dokumen:</strong> Kustomisasi format nomor SKU barang, kode transaksi peminjaman, dan BAST dengan anatomi visual interaktif dan sinkronisasi massal data riwayat.</span>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">Feat</span>
                            <span><strong>Manajemen Hak Akses (Dynamic RBAC):</strong> Matriks perizinan granular berbasis role & direct user permission dengan sticky header responsif dan pencatatan audit trail humanis.</span>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <span class="badge bg-info-subtle text-info border border-info-subtle mt-1">Audit</span>
                            <span><strong>Audit Trail & Session Tracking:</strong> Komparasi visual data sebelum-sesudah (before/after) pada detail log dan pemantauan status sesi online/offline pengguna secara realtime.</span>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle mt-1">v1.1.0</span>
                            <span><em>Fitur sebelumnya (v1.1.0):</em> Log Server, Hardening OWASP, Avatar Pengguna, Buku Panduan Penggunaan & PDF Resmi.</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-3 text-muted" style="font-size: 0.85rem;">
                &copy; {{ date('Y') }} SIM-ASET. Hak cipta dilindungi.
            </div>
        </div>
    </div>
</div>
@endsection
