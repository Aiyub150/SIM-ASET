<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM-ASET — Sistem Informasi Manajemen Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-pink-500 selection:text-white">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center shadow-lg shadow-pink-500/30">
                        <i class="fa-solid fa-boxes-stacked text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900">SIM-ASET</span>
                </div>
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-pink-600 px-4 py-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold bg-pink-500 hover:bg-pink-600 text-white px-5 py-2.5 rounded-lg transition shadow-md shadow-pink-500/20">
                            Coba Demo <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="pt-28 pb-16 lg:pt-40 lg:pb-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            
            <!-- Decorative blobs -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-pink-400 opacity-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 rounded-full bg-blue-400 opacity-20 blur-3xl"></div>

            <div class="text-center relative z-10 max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                    Kelola Aset Lebih Pintar, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-violet-500">
                        Lebih Cepat, Lebih Mudah
                    </span>
                </h1>
                <p class="mt-4 text-lg md:text-xl text-slate-600 mb-10">
                    Sistem Informasi Manajemen Aset (SIM-ASET) open-source untuk memantau peminjaman, pengembalian, dan ketersediaan stok barang menggunakan teknologi Barcode Scanner mutakhir.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('login') }}" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold px-8 py-3.5 rounded-xl transition shadow-lg shadow-pink-500/30 text-lg flex items-center justify-center gap-2">
                        Masuk ke Aplikasi
                    </a>
                    <a href="https://github.com/aiyub/SIM-ASET" target="_blank" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold px-8 py-3.5 rounded-xl transition shadow-sm text-lg flex items-center justify-center gap-2">
                        <i class="fa-brands fa-github text-xl"></i> GitHub Repo
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="mt-24 grid md:grid-cols-3 gap-8 relative z-10">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Barcode Scanner</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Pindai barang dengan kamera smartphone atau scanner USB secara instan tanpa perlu ketik manual.
                    </p>
                </div>
                
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Manajemen Stok Real-time</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Pantau ketersediaan barang dan histori mutasi aset dengan data yang selalu mutakhir setiap detiknya.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-violet-100 text-violet-600 rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Laporan & Cetak Label</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Cetak bukti peminjaman dan label barcode untuk setiap aset yang terdaftar dalam sekali klik.
                    </p>
                </div>
            </div>

            <div class="mt-20 text-center">
                <p class="text-slate-500 text-sm">
                    Dibangun dengan penuh semangat menggunakan 
                    <span class="font-semibold text-red-500">Laravel 11</span>, 
                    <span class="font-semibold text-purple-600">Bootstrap 5</span>, dan 
                    <span class="font-semibold text-sky-500">Tailwind CSS</span>.
                </p>
            </div>
        </div>
    </main>

</body>
</html>
