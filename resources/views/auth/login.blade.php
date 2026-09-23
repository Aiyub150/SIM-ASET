<x-guest-layout>
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <h6 class="fw-600 mb-1" style="font-weight:600; color:#1e293b;">Masuk ke Sistem</h6>
    <p class="text-muted mb-4" style="font-size:.82rem;">Masukkan kredensial akun Anda untuk melanjutkan.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="contoh@pemda.go.id">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                <label for="remember_me" class="form-check-label" style="font-size:.83rem;">Ingat saya</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom btn-primary text-white w-100">
            Masuk
        </button>

        <div class="text-center mt-3">
            <a href="{{ route('guide') }}" class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-2 w-100 py-2 fw-semibold" style="font-size:0.85rem; border-radius: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                </svg>
                Buku Panduan Penggunaan SIM-ASET
            </a>
        </div>
    </form>

    <div class="mt-4 pt-3 border-top">
        <p class="text-sm text-slate-500 font-semibold mb-2">Demo Accounts:</p>
        <div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs text-slate-600">
            <div class="mb-2 pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-800">Super Admin</span><br>
                Email: superadmin@simaset.com<br>
                Pass: password123
            </div>
            <div class="mb-2 pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-800">Admin</span><br>
                Email: admin@simaset.com<br>
                Pass: password123
            </div>
            <div>
                <span class="font-bold text-slate-800">Staff</span><br>
                Email: staff@simaset.com<br>
                Pass: password123
            </div>
        </div>
    </div>
</x-guest-layout>
