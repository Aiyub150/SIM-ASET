<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIM-ASET') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            min-height: 100vh;
        }
        .auth-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .auth-logo-area {
            background: #fff;
            border-radius: 16px 16px 0 0;
            padding: 1.75rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            padding: 0.6rem 0.875rem;
            font-size: 0.9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            transition: background .2s, transform .1s;
        }
        .btn-primary-custom:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-primary-custom:active { transform: translateY(0); }
        .form-label { font-weight: 500; font-size: 0.875rem; color: #374151; margin-bottom: 0.4rem; }
        .invalid-feedback { font-size: 0.8rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div style="width: 100%; max-width: 420px; padding: 1rem;">
        <div class="auth-card card">
            <div class="auth-logo-area">
                <img src="{{ asset('images/sim-aset_banner.png') }}"
                     alt="SIM-ASET — Sistem Inventaris Aset Daerah"
                     style="max-width: 320px; width: 100%; height: auto;">
            </div>
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
        <p class="text-center text-white mt-3" style="opacity:.6; font-size:.8rem;">© {{ date('Y') }} Sistem Inventaris Aset Daerah</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
