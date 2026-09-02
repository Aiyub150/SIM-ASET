<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM-ASET — Inventaris Aset Daerah')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --sidebar-width: 240px;
            --topbar-height: 60px;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #1e3a5f;
            --text-muted-custom: #64748b;
            --border: #e2e8f0;
            --surface: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            background: var(--surface);
            color: #1e293b;
        }

        /* ── SIDEBAR ─────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-brand .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
        }

        .brand-icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-name {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }
        .sidebar-brand .brand-subtitle {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.45);
            line-height: 1;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.3);
            padding: 0.75rem 0.5rem 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 2px;
            transition: background .15s, color .15s;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-link.active {
            background: var(--primary);
            color: #fff;
        }

        .sidebar-link svg {
            flex-shrink: 0;
            opacity: 0.75;
        }
        .sidebar-link.active svg, .sidebar-link:hover svg { opacity: 1; }

        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            background: rgba(255,255,255,0.06);
            margin-bottom: 0.5rem;
        }

        .user-avatar {
            width: 32px; height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name { font-size: 0.8rem; font-weight: 600; color: #fff; line-height: 1.2; }
        .user-role { font-size: 0.7rem; color: rgba(255,255,255,0.45); line-height: 1; }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            background: transparent;
            color: rgba(255,255,255,0.55);
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, color .15s;
        }
        .btn-logout:hover { background: #ef4444; border-color: #ef4444; color: #fff; }

        /* ── MAIN AREA ───────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ──────────────────────────────── */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        /* ── PAGE CONTENT ────────────────────────── */
        .page-content {
            padding: 1.5rem;
            flex: 1;
        }

        /* ── CARDS ───────────────────────────────── */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            background: #fff;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            font-size: 0.9rem;
            color: #1e293b;
        }

        /* ── TABLE ───────────────────────────────── */
        .table th {
            background: var(--surface);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: var(--text-muted-custom);
            border-bottom: 1px solid var(--border);
        }

        .table td {
            border-color: var(--border);
            font-size: 0.875rem;
            vertical-align: middle;
            padding: 0.8rem 0.9rem;
        }

        .table-hover tbody tr:hover { background: var(--primary-light); }

        /* ── BADGES ──────────────────────────────── */
        .badge {
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            padding: 0.3em 0.7em;
        }

        /* ── BUTTONS ─────────────────────────────── */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.45rem 1rem;
            transition: all .15s;
        }

        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        .btn-sm { padding: 0.3rem 0.7rem; font-size: 0.8rem; }

        /* ── FORMS ───────────────────────────────── */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid var(--border);
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.35rem;
        }

        /* ── ALERTS ──────────────────────────────── */
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.875rem;
        }

        .alert-success { background: #f0fdf4; color: #166534; border-left: 3px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 3px solid #ef4444; }
        .alert-info    { background: #eff6ff; color: #1e40af; border-left: 3px solid #3b82f6; }
        .alert-warning { background: #fffbeb; color: #92400e; border-left: 3px solid #f59e0b; }

        /* ── PAGINATION ──────────────────────────── */
        .pagination .page-link {
            border-radius: 6px !important;
            border: 1px solid var(--border);
            color: var(--primary);
            font-size: 0.8rem;
            margin: 0 2px;
            padding: 0.35rem 0.65rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* ── MOBILE TOGGLE ───────────────────────── */
        .sidebar-toggle {
            display: none;
            border: none;
            background: none;
            padding: 0.25rem;
            cursor: pointer;
            margin-right: 0.75rem;
        }

        /* ── STAT CARDS ──────────────────────────── */
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .25s;
            }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .page-content { padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- ══ SIDEBAR ══════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <a href="{{ route('loans.index') }}" class="brand-logo">
                <img src="{{ asset('images/sim-aset_logo.svg') }}"
                     alt="SIM-ASET Logo"
                     style="width:36px; height:36px; flex-shrink:0; border-radius:9px;">
                <div>
                    <div class="brand-name">SIM-ASET</div>
                    <div class="brand-subtitle">Inventaris Aset Daerah</div>
                </div>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Transaksi</div>

            <a href="{{ route('loans.index') }}"
               class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                </svg>
                Peminjaman
            </a>

            @role('Super Admin')
            <div class="nav-section-label" style="margin-top:.5rem;">Master Data</div>

            <a href="{{ route('borrowers.index') }}"
               class="sidebar-link {{ request()->routeIs('borrowers.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816z"/>
                </svg>
                Data Instansi
            </a>

            <a href="{{ route('items.index') }}"
               class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.922l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.629 13.09a1 1 0 0 1-.629-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                </svg>
                Master Barang
            </a>

            <div class="nav-section-label" style="margin-top:.5rem;">Pengelolaan</div>

            <a href="{{ route('stocks.index') }}"
               class="sidebar-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                </svg>
                Kartu Stok
            </a>

            <a href="{{ route('reports.index') }}"
               class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                    <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
                </svg>
                Laporan
            </a>
            @endrole
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="min-width:0;">
                    <div class="user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="user-role text-truncate">{{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- ══ MAIN WRAPPER ══════════════════════════════ -->
    <div class="main-wrapper" id="main-wrapper">

        <!-- Topbar -->
        <div class="topbar">
            <button class="sidebar-toggle" id="sidebar-toggle" onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#64748b" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </button>
            <span class="page-title">@yield('page-title', 'Dashboard')</span>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size:.8rem;">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success') || session('error'))
        <div class="px-4 pt-3" style="padding-bottom:0;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16" style="margin-top:-2px;"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.75rem;"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16" style="margin-top:-2px;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.75rem;"></button>
                </div>
            @endif
        </div>
        @endif

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>

        <footer style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background:#fff; text-align:center;">
            <small class="text-muted" style="font-size:.75rem;">© {{ date('Y') }} Aiyub Heriyanto — SIM-ASET Inventaris Aset Daerah</small>
        </footer>
    </div>

    <!-- Overlay mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebar-overlay');
            const isOpen   = sidebar.classList.toggle('open');
            overlay.style.display = isOpen ? 'block' : 'none';
        }
    </script>

    @stack('scripts')
</body>
</html>
