<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panduan Penggunaan — SIM-ASET')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/sim-aset_logo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --surface: #f8fafc;
            --border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            scroll-behavior: smooth;
        }
        .guide-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1050;
        }
        .btn-login-header {
            background: var(--primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 0.5rem 1.1rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-login-header:hover {
            background: var(--primary-dark);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .guide-content-area {
            flex: 1;
            padding: 2rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        .guide-footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 2rem 1rem;
            margin-top: 3rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="guide-navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/sim-aset_logo.svg') }}" alt="SIM-ASET Logo" style="width: 36px; height: 36px; border-radius: 8px;">
                <div>
                    <span class="fw-bold text-dark d-block leading-tight" style="font-size: 1.05rem;">SIM-ASET</span>
                    <span class="text-muted d-block" style="font-size: 0.72rem;">Sistem Inventaris Aset Daerah</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-md-inline-block px-3 py-2" style="font-size: 0.75rem;">
                    Buku Panduan v1.0.0 (2026)
                </span>
                <a href="{{ route('login') }}" class="btn-login-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                    Masuk ke Sistem
                </a>
            </div>
        </div>
    </header>

    <main class="guide-content-area">
        @yield('content')
    </main>

    <footer class="guide-footer">
        <div class="container text-center">
            <img src="{{ asset('images/sim-aset_logo.svg') }}" alt="SIM-ASET" style="width: 44px; height: 44px; margin-bottom: 0.75rem;">
            <p class="fw-bold mb-1" style="color: #1e293b;">SIM-ASET — Sistem Informasi Manajemen Aset & Logistik Daerah</p>
            <p class="text-muted small mb-2">Dirancang & Dibuat oleh <strong>Aiyub Heriyanto</strong> &copy; Tahun 2026</p>
            <p class="text-muted" style="font-size: 0.75rem;">Pedoman resmi operasional tata kelola inventaris barang milik daerah.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
