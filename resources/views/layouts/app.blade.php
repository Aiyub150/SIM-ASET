<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM-ASET — Inventaris Aset Daerah')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/sim-aset_logo.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script>
        (function() {
            try {
                const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                if (tz) {
                    const cookieMatch = document.cookie.match(/(^|;)\s*user_timezone\s*=\s*([^;]+)/);
                    const currentTz = cookieMatch ? decodeURIComponent(cookieMatch[2]) : null;
                    if (currentTz !== tz) {
                        document.cookie = "user_timezone=" + encodeURIComponent(tz) + "; path=/; max-age=31536000; SameSite=Lax";
                        if (!currentTz) {
                            window.location.reload();
                        }
                    }
                }
            } catch (e) {}
        })();
    </script>
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 64px;
            /* Blue theme */
            --primary: #2563eb; /* Blue-600 */
            --primary-dark: #1d4ed8; /* Blue-700 */
            --primary-light: #eff6ff; /* Blue-50 */
            --sidebar-bg: #1e293b; /* Slate-800 */
            --sidebar-hover: #334155; /* Slate-700 */
            --sidebar-active: #475569; /* Slate-600 */
            --text-muted-custom: #64748b; /* Slate-500 */
            --border: #e2e8f0; /* Slate-200 */
            --surface: #f1f5f9; /* Slate-100 */
        }

        * { box-sizing: border-box; }

        /* Custom TomSelect Enhancements */
        .ts-wrapper.form-select,
        .ts-wrapper.form-select-sm {
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        .ts-control {
            border: 1px solid #ced4da !important;
            border-radius: 0.5rem !important;
            padding: 0.45rem 2.25rem 0.45rem 0.75rem !important;
            font-size: 0.875rem !important;
            box-shadow: none !important;
            background-color: #fff !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 16px 12px !important;
            min-height: 38px !important;
            display: flex !important;
            align-items: center !important;
            cursor: pointer !important;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
        }
        .ts-control:hover {
            border-color: #94a3b8 !important;
        }
        .ts-wrapper.focus .ts-control,
        .ts-control.focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.2) !important;
        }
        .ts-wrapper.single .ts-control::after {
            display: none !important;
        }
        .ts-dropdown {
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            padding: 0.35rem !important;
            margin-top: 4px !important;
            z-index: 1060 !important;
            background-color: #fff !important;
        }
        .ts-dropdown .option {
            border-radius: 0.4rem !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.85rem !important;
            border-bottom: 1px solid #f8fafc;
            transition: background-color 0.15s ease-in-out;
        }
        .ts-dropdown .option:last-child {
            border-bottom: none;
        }
        .ts-dropdown .ts-dropdown-content {
            max-height: 280px;
            overflow-y: auto;
        }
        .ts-dropdown .ts-dropdown-content::-webkit-scrollbar {
            width: 6px;
        }
        .ts-dropdown .ts-dropdown-content::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
        .ts-dropdown .active {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
        }
        .ts-wrapper.form-select-sm .ts-control {
            padding: 0.28rem 1.8rem 0.28rem 0.6rem !important;
            font-size: 0.8125rem !important;
            border-radius: 0.375rem !important;
            min-height: 31px !important;
            background-size: 12px 10px !important;
            background-position: right 0.5rem center !important;
        }
        .ts-wrapper .ts-control > input {
            font-size: inherit !important;
        }

        /* ── COLLAPSE & ACCORDION FIX ─────────────── */
        .collapse.show,
        .accordion-collapse.collapse.show,
        .accordion-collapse.collapse,
        .accordion-body {
            visibility: visible !important;
        }

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
        .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            display: inline-block;
            vertical-align: middle;
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
            <a href="{{ route('dashboard') }}" class="brand-logo">
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
            <div class="nav-section-label">Utama</div>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8.5a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5zm-2.5-3a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0v-8a.5.5 0 0 1 .5-.5zm5 4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5z"/>
                    <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm13 2v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                </svg>
                Dashboard
            </a>

            @php
                $user = auth()->user();
                $hasPerm = function(string $perm) use ($user): bool {
                    if (!$user) return false;
                    if ($user->hasRole('Super Admin')) return true;
                    if ($user->hasRole('Admin') && in_array($perm, ['loans.view', 'borrowers.view', 'items.view', 'stocks.view', 'reports.view', 'categories.view'])) return true;
                    if ($user->hasRole('Staff Logistik') && in_array($perm, ['loans.view', 'items.view', 'reports.view'])) return true;
                    return rescue(fn() => $user->can($perm), false) ?? false;
                };

                $canSeeLoans = $hasPerm('loans.view');
                $canSeeBorrowers = $hasPerm('borrowers.view');
                $canSeeItems = $hasPerm('items.view');
                $canSeeStocks = $hasPerm('stocks.view');
                $canSeeReports = $hasPerm('reports.view');
                $canSeeCategories = $hasPerm('categories.view');

                $canAccessLogHistory = $user->hasRole('Super Admin') || rescue(fn() => $user->can('audit.view'), false);
                $canAccessRoles = $user->hasRole('Super Admin') || rescue(fn() => $user->can('roles.manage'), false);
                $canAccessPermissions = $user->hasRole('Super Admin') || rescue(fn() => $user->can('permissions.manage'), false);
                $canAccessUsers = $user->hasRole('Super Admin') || rescue(fn() => $user->can('users.view'), false);
                $canAccessSku = $user->hasRole('Super Admin') || rescue(fn() => $user->can('sku.manage'), false);

                $canSeeAdminSettings = $canAccessLogHistory || $canAccessRoles || $canAccessPermissions || $canAccessUsers || $canAccessSku;
                $isAdminSettingsActive = request()->routeIs('admin-settings.*') || request()->routeIs('superadmin.log-history*') || request()->routeIs('users.*');
            @endphp

            @if($canSeeLoans)
            <div class="nav-section-label" style="margin-top:.5rem;">Transaksi</div>
            <a href="{{ route('loans.index') }}"
               class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm4.5 5.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0-2a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                </svg>
                Peminjaman
            </a>
            @endif

            @if($canSeeBorrowers || $canSeeItems)
            <div class="nav-section-label" style="margin-top:.5rem;">Master Data</div>

            @if($canSeeBorrowers)
            <a href="{{ route('borrowers.index') }}"
               class="sidebar-link {{ request()->routeIs('borrowers.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816z"/>
                </svg>
                Data Instansi
            </a>
            @endif

            @if($canSeeItems)
            <a href="{{ route('items.index') }}"
               class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.922l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.629 13.09a1 1 0 0 1-.629-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                </svg>
                Master Barang
            </a>
            @endif
            @endif

            @if($canSeeStocks)
            <div class="nav-section-label" style="margin-top:.5rem;">Pengelolaan</div>

            <a href="{{ route('stocks.index') }}"
               class="sidebar-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                </svg>
                Kartu Stok
            </a>
            @endif

            @if($canSeeReports)
            <div class="nav-section-label" style="margin-top:.5rem;">Laporan & Analitik</div>
            <a href="{{ route('reports.index') }}"
               class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                    <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
                </svg>
                Laporan
            </a>
            @endif

            @if($canSeeCategories)
            <div class="nav-section-label" style="margin-top:.5rem;">Administrasi</div>
            <a href="{{ route('categories.index') }}"
               class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M3.5 2a1.5 1.5 0 0 0-1.5 1.5v9A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 12.5 2h-9zM2.5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-9z"/>
                    <path fill-rule="evenodd" d="M6.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-3zM5 5.5A1.5 1.5 0 0 1 6.5 4h3A1.5 1.5 0 0 1 11 5.5v1A1.5 1.5 0 0 1 9.5 8h-3A1.5 1.5 0 0 1 5 6.5v-1z"/>
                </svg>
                Manajemen Kategori
            </a>
            @endif
            @if($canSeeAdminSettings)
            <div class="nav-item">
                <a class="sidebar-link d-flex justify-content-between align-items-center {{ $isAdminSettingsActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#adminSettingsCollapse" 
                   role="button" 
                   aria-expanded="{{ $isAdminSettingsActive ? 'true' : 'false' }}" 
                   aria-controls="adminSettingsCollapse">
                    <div class="d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.185 1.184l-.16.291a1.873 1.873 0 0 0 1.115 2.693l.319.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.16a1.873 1.873 0 0 0-2.693 1.115l-.094.319c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.693-1.115l-.291.16c-.764.415-1.6-.42-1.185-1.184l.16-.291a1.873 1.873 0 0 0-1.115-2.693l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094a1.873 1.873 0 0 0 1.115-2.693l-.16-.291c-.415-.764.42-1.6 1.185-1.184l.291.16a1.873 1.873 0 0 0 2.693-1.115l.094-.319z"/>
                        </svg>
                        <span>Administration Settings</span>
                    </div>
                    <svg class="sidebar-chevron transition-transform" style="transition: transform .2s; {{ $isAdminSettingsActive ? 'transform: rotate(180deg);' : '' }}" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </a>
                
                <div class="collapse {{ $isAdminSettingsActive ? 'show' : '' }} ps-3 mt-1" id="adminSettingsCollapse">
                    @if($canAccessLogHistory)
                    <a href="{{ route('admin-settings.log-history') }}"
                       class="sidebar-link {{ request()->routeIs('admin-settings.log-history*') || request()->routeIs('superadmin.log-history*') ? 'active' : '' }}" style="font-size: 0.82rem; padding: 0.45rem 0.65rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.376.862c.324.23.633.486.924.764l.698-.716a8.01 8.01 0 0 0-1.07-1.025l-.552.977zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        Log History
                    </a>
                    @endif

                    @if($canAccessRoles)
                    <a href="{{ route('admin-settings.roles.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin-settings.roles.*') ? 'active' : '' }}" style="font-size: 0.82rem; padding: 0.45rem 0.65rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56z"/>
                        </svg>
                        Manajemen Role
                    </a>
                    @endif

                    @if($canAccessPermissions)
                    <a href="{{ route('admin-settings.permissions.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin-settings.permissions.*') ? 'active' : '' }}" style="font-size: 0.82rem; padding: 0.45rem 0.65rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1.337-.337A3.5 3.5 0 0 1 3.5 11.5zM3 6.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                        </svg>
                        Manajemen Hak Akses
                    </a>
                    @endif

                    @if($canAccessUsers)
                    <a href="{{ route('users.index') }}"
                       class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" style="font-size: 0.82rem; padding: 0.45rem 0.65rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                            <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                        </svg>
                        Kelola Pengguna
                    </a>
                    @endif

                    @if($canAccessSku)
                    <a href="{{ route('admin-settings.code-formats.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin-settings.code-formats.*') ? 'active' : '' }}" style="font-size: 0.82rem; padding: 0.45rem 0.65rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5zM3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7z"/>
                        </svg>
                        Manajemen Kode SKU
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <div class="nav-section-label" style="margin-top:.5rem;">Pengaturan</div>
            <a href="{{ route('profile.edit') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
                Edit Profil
            </a>
            <a href="{{ route('settings.version') }}"
               class="sidebar-link {{ request()->routeIs('settings.version') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
                Informasi Versi
            </a>
            <a href="{{ route('guide') }}"
               class="sidebar-link {{ request()->routeIs('guide') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                </svg>
                Panduan Penggunaan
            </a>
        </nav>

        <div class="sidebar-footer text-center py-3">
            <span class="badge bg-white bg-opacity-10 text-white-50 font-monospace" style="font-size: 0.7rem;">
                SIM-ASET v1.2.0
            </span>
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
                @hasanyrole('Super Admin|Admin')
                <button type="button" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#calendarModal" style="width: 32px; height: 32px; padding: 0;" title="Kalender Aktivitas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                </button>
                @endhasanyrole
                <span class="text-muted fw-medium d-none d-sm-inline" style="font-size:.85rem;">{{ \App\Services\TimezoneHelper::formatTranslated(now(), 'd F Y') }}</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-1 d-none d-sm-inline" style="font-size: 0.72rem;" title="Zona Waktu Perangkat Anda">
                    {{ \App\Services\TimezoneHelper::getTimezoneLabel() }}
                </span>

                <!-- User Profile Dropdown (Topbar Kanan Atas) -->
                <div class="dropdown ms-1">
                    <button class="btn btn-sm btn-light border d-flex align-items-center gap-2 px-2 py-1 shadow-sm rounded-pill" 
                            type="button" 
                            id="topbarUserDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false" 
                            style="transition: all .2s ease;">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="d-none d-md-flex flex-column text-start" style="line-height: 1.1;">
                            <span class="fw-semibold text-dark text-truncate" style="font-size: 0.8rem; max-width: 120px;">{{ auth()->user()->name }}</span>
                            <span class="text-muted" style="font-size: 0.68rem;">{{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#64748b" class="ms-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border py-2" aria-labelledby="topbarUserDropdown" style="min-width: 220px; border-radius: 12px;">
                        <li class="px-3 py-2 border-bottom mb-1 bg-light bg-opacity-50">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ auth()->user()->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ auth()->user()->email }}</div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1" style="font-size: 0.65rem;">
                                {{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.edit') }}" style="font-size: 0.82rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#475569" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                </svg>
                                <span>Edit Profil</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" style="font-size: 0.82rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
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

    <!-- Calendar Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="calendarModalLabel">Kalender</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
             <div class="p-3">
                 <div class="text-center mb-3">
                     <h6 class="fw-bold mb-1">{{ now()->translatedFormat('F Y') }}</h6>
                     <span class="badge bg-primary">Hari ini: {{ now()->translatedFormat('d M Y') }}</span>
                 </div>
                 @php
                     $globalHolidays = app(\App\Services\CalendarService::class)->getHolidaysForCurrentMonth();
                 @endphp
                 <p class="text-muted small fw-bold mb-2">LIBUR NASIONAL</p>
                 @if(count($globalHolidays) > 0)
                     <div class="list-group list-group-flush small border rounded">
                         @foreach($globalHolidays as $holiday)
                             <div class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center">
                                 <span>{{ $holiday['title'] }}</span>
                                 <span class="badge {{ $holiday['is_cuti'] ? 'bg-warning text-dark' : 'bg-danger' }}">{{ \Carbon\Carbon::parse($holiday['date'])->format('d M') }}</span>
                             </div>
                         @endforeach
                     </div>
                 @else
                     <div class="p-3 bg-light rounded text-muted small text-center">
                         Tidak ada libur nasional bulan ini.
                     </div>
                 @endif
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Overlay mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebar-overlay');
            const isOpen   = sidebar.classList.toggle('open');
            overlay.style.display = isOpen ? 'block' : 'none';
        }

        // Listen collapse state to rotate chevron
        document.addEventListener('DOMContentLoaded', function() {
            const adminCollapse = document.getElementById('adminSettingsCollapse');
            if (adminCollapse) {
                adminCollapse.addEventListener('show.bs.collapse', function () {
                    const chevron = document.querySelector('a[href="#adminSettingsCollapse"] .sidebar-chevron');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                });
                adminCollapse.addEventListener('hide.bs.collapse', function () {
                    const chevron = document.querySelector('a[href="#adminSettingsCollapse"] .sidebar-chevron');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                });
            }
        });

        window.alert = function (message) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: String(message),
                confirmButtonColor: '#2563eb'
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success'))
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: @json(session('error')),
                    confirmButtonColor: '#2563eb'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: @json('<div class="text-start">' . implode('<br>', $errors->all()) . '</div>'),
                    confirmButtonColor: '#2563eb'
                });
            @endif

            @if(session('status') && !session('success'))
                Toast.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: @json(session('status'))
                });
            @endif

            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;

                // Abaikan form pencarian / filter GET
                if (form.method && form.method.toLowerCase() === 'get') return;

                // Cek apakah form memiliki konfirmasi eksplisit atau merupakan aksi delete
                const hasExplicitConfirm = form.dataset && form.dataset.confirm;
                const methodInput = form.querySelector('input[name="_method"]');
                const isDelete = methodInput && methodInput.value.toUpperCase() === 'DELETE';

                if ((hasExplicitConfirm || isDelete) && !form.dataset.confirmed) {
                    e.preventDefault();

                    const title = form.dataset.confirmTitle || (isDelete ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan');
                    const text = form.dataset.confirm || (isDelete ? 'Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.' : 'Apakah data yang dimasukkan sudah benar?');
                    const icon = isDelete ? 'warning' : 'question';
                    const confirmColor = isDelete ? '#dc2626' : '#2563eb';
                    const confirmBtnText = isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: confirmColor,
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: confirmBtnText,
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            });
        });

        // BFCache Protection: Prevent stale authenticated DOM restoration after logout
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
                window.location.reload();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
