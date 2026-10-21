<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 — Terlalu Banyak Permintaan | SIM-ASET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/sim-aset_logo.svg') }}">
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
        .error-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            background: #ffffff;
        }
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1.25rem;
            font-weight: 600;
            transition: background .2s, transform .1s;
        }
        .btn-primary-custom:hover { background: var(--primary-dark); transform: translateY(-1px); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div style="width: 100%; max-width: 480px; padding: 1.5rem;">
        <div class="error-card card text-center p-4 p-md-5">
            <div class="mb-3">
                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill font-monospace fw-bold" style="font-size: 0.95rem;">
                    HTTP 429 &bull; TOO MANY REQUESTS
                </span>
            </div>
            <div class="my-3">
                <div class="d-inline-flex p-3 bg-danger-subtle rounded-circle text-danger mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                    </svg>
                </div>
                <h4 class="fw-bold text-dark mb-2">Akses Dibatasi Sementara</h4>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Sistem mendeteksi terlalu banyak permintaan atau percobaan login berturut-turut dari alamat IP atau perangkat ini. Demi perlindungan data aset daerah, akses dikunci sementara selama 15 menit.
                </p>
            </div>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary-custom text-white">
                    Kembali ke Halaman Login
                </a>
                <a href="{{ route('guide') }}" class="btn btn-light text-secondary border">
                    Buku Panduan SIM-ASET
                </a>
            </div>
        </div>
        <p class="text-center text-white mt-3" style="opacity:.75; font-size:.8rem;">
            &copy; 2026 Sistem Inventaris Aset Daerah &bull; Perlindungan Brute-Force & Rate Limiting
        </p>
    </div>
</body>
</html>
