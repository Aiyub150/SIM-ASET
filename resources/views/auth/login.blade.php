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
