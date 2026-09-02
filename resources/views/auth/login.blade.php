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
</x-guest-layout>
