<x-guest-layout>
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <h6 class="fw-600 mb-1" style="font-weight:600; color:#1e293b;">Masuk ke Sistem</h6>
    <p class="text-muted mb-4" style="font-size:.82rem;">Masukkan kredensial akun Anda untuk melanjutkan.</p>

    {{-- Banner Lockout Countdown jika Terkena Proteksi Brute Force --}}
    @php
        $activeLockoutSeconds = (int) ($lockoutSeconds ?? session('lockout_seconds', 0));
    @endphp

    <div id="lockout-timer-box" class="alert alert-danger p-3 mb-4 rounded-3 border-danger shadow-sm" style="{{ $activeLockoutSeconds > 0 ? '' : 'display:none;' }}">
        <div class="d-flex align-items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-danger flex-shrink-0" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
            </svg>
            <strong class="text-danger" style="font-size: .88rem;">Akun / IP Terkunci Sementara</strong>
        </div>
        <p class="mb-2 text-danger-emphasis" style="font-size: .8rem; line-height: 1.4;">
            Terdeteksi lebih dari 5 kali kegagalan login berturut-turut. Demi keamanan, sistem mengunci akses login selama 15 menit. Pembersihan cache browser atau local storage tidak akan mereset masa penguncian.
        </p>
        <div class="bg-white rounded-2 p-2 border border-danger-subtle text-center">
            <span class="text-muted small">Waktu tunggu tersisa:</span>
            <div id="lockout-countdown" class="font-monospace fw-bold text-danger fs-5">00:00</div>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" id="form-login">
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

        <button type="submit" id="btn-login-submit" class="btn btn-primary-custom btn-primary text-white w-100">
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let secondsLeft = {{ (int) ($activeLockoutSeconds ?? 0) }};
        const countdownEl = document.getElementById('lockout-countdown');
        const timerBox = document.getElementById('lockout-timer-box');
        const submitBtn = document.getElementById('btn-login-submit');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        function formatTime(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }

        if (secondsLeft > 0 && countdownEl) {
            timerBox.style.display = 'block';
            if (submitBtn) submitBtn.disabled = true;
            if (emailInput) emailInput.disabled = true;
            if (passwordInput) passwordInput.disabled = true;
            countdownEl.textContent = formatTime(secondsLeft);

            const interval = setInterval(function () {
                secondsLeft--;
                if (secondsLeft <= 0) {
                    clearInterval(interval);
                    countdownEl.textContent = '00:00';
                    timerBox.className = 'alert alert-success p-3 mb-4 rounded-3 border-success shadow-sm';
                    timerBox.innerHTML = '<div class="d-flex align-items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-success" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg><strong class="text-success" style="font-size: .88rem;">Kunci telah dibuka!</strong></div><p class="mb-0 text-success-emphasis mt-1" style="font-size: .8rem;">Silakan masukkan kembali email dan password Anda untuk masuk.</p>';
                    if (submitBtn) submitBtn.disabled = false;
                    if (emailInput) emailInput.disabled = false;
                    if (passwordInput) passwordInput.disabled = false;
                } else {
                    countdownEl.textContent = formatTime(secondsLeft);
                }
            }, 1000);
        }
    });
    </script>
</x-guest-layout>
