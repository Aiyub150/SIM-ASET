<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventaris Aset Daerah')</title>
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); border: none; margin-bottom: 20px; }
        .table th { background-color: #e9ecef; }
    </style>
</head>
<body>

    <!-- Navigasi Utama -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('loans.index') }}">SIM-ASET</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('loans.index') }}">Peminjaman</a>
                    </li>
                   <!-- Sembunyikan menu ini dari Staff Biasa -->
                    @role('Super Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('borrowers.index') }}">Data Instansi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('items.index') }}">Master Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stocks.index') }}">Kartu Stok</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.index') }}">Laporan</a>
                    </li>
                    @endrole
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        Halo, <strong>{{ auth()->user()->name }}</strong> 
                        <span class="badge bg-secondary ms-1">{{ auth()->user()->roles->pluck('name')->first() }}</span>
                    </span>
                    
                    <!-- FORM LOGOUT MUTLAK DENGAN POST & CSRF -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger fw-bold">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Kontainer Dinamis untuk View Lain -->
    <div class="container">
        <!-- Area Pesan Flash (Global) -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Area Konten Spesifik Halaman -->
        @yield('content')
    </div>

    <!-- Bootstrap JS via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Tempat injeksi JavaScript tambahan (jika halaman butuh JS khusus) -->
    @stack('scripts')
</body>
</html>