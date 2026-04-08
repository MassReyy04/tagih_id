@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="row g-0 ptpn-login-wrap">
    <div class="col-lg-5 col-xl-4 ptpn-login-aside d-none d-lg-flex">
        <div class="ptpn-login-aside-inner">
            <p class="lead-brand mb-0">PTPN IV — Nuansa Jambi</p>
            <h1>Monitoring &amp; penagihan mitra binaan</h1>
            <p class="mt-3 mb-0 opacity-90" style="max-width: 22rem; line-height: 1.65;">
                Aplikasi berita acara kunjungan dengan nomor surat otomatis, tanda tangan digital, dan geotagging lokasi.
            </p>
            <div class="ptpn-leaf-pattern" aria-hidden="true">🌿 🍃 🌴</div>
        </div>
    </div>

    <div class="col-lg-7 col-xl-8 ptpn-login-main">
        <div class="ptpn-login-card mx-auto shadow-lg">
            <div class="text-center d-lg-none mb-3">
                <p class="small text-success fw-bold text-uppercase mb-1" style="letter-spacing:0.15em;">PTPN IV Regional 4</p>
                <h2 class="h5 text-dark mb-0">Masuk ke sistem</h2>
            </div>
            <h2 class="d-none d-lg-block mb-1">Masuk ke sistem</h2>
            <p class="text-muted small mb-4">
                Gunakan email dan kata sandi yang sudah terdaftar di database.
                Sandi contoh biasanya <code class="text-success">password</code> — hanya email di kartu bawah (atau yang dibuat admin) yang valid.
            </p>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           placeholder="nama@domain.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Kata sandi</label>
                    <div class="input-group input-group-lg">
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                id="togglePassword"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                                title="Lihat / sembunyikan sandi">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 py-2" id="btnLogin">
                    <span id="btnLoginText">Masuk</span>
                    <span id="btnLoginSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>

                @if (Route::has('password.request'))
                    <div class="text-center mt-3">
                        <a class="small text-decoration-none" style="color: var(--ptpn-orange);" href="{{ route('password.request') }}">
                            Lupa kata sandi?
                        </a>
                    </div>
                @endif
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-copy').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var text = btn.getAttribute('data-copy') || '';
        if (navigator.clipboard && text) {
            navigator.clipboard.writeText(text).then(function () {
                showPtpnToast('Disalin ke clipboard');
            });
        }
    });
});

// Toggle lihat / sembunyikan password ("mata")
var togglePasswordBtn = document.getElementById('togglePassword');
var passwordInput = document.getElementById('password');
var eyeIcon = document.getElementById('eyeIcon');
if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener('click', function () {
        var isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        // ganti icon (eye vs eye-off) tanpa plugin
        if (eyeIcon) {
            eyeIcon.innerHTML = isPassword
                ? '<path d="M3 3l18 18"></path><path d="M10.58 10.58A2.99 2.99 0 0 0 12 15a3 3 0 0 0 2.42-4.42"></path><path d="M9.88 9.88A10.43 10.43 0 0 1 12 8c7 0 11 8 11 8a21.86 21.86 0 0 1-5.08 6.28"></path><path d="M6.11 6.11A21.86 21.86 0 0 0 1 12s4 8 11 8c1.49 0 2.78-.31 3.87-.82"></path>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    });
}

function showPtpnToast(msg) {
    var el = document.getElementById('ptpn-toast');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(window._ptpnToastT);
    window._ptpnToastT = setTimeout(function () { el.classList.remove('show'); }, 2200);
}

// Loading state saat submit login
var loginForm = document.getElementById('loginForm');
var btnLogin = document.getElementById('btnLogin');
var btnLoginText = document.getElementById('btnLoginText');
var btnLoginSpinner = document.getElementById('btnLoginSpinner');
if (loginForm && btnLogin && btnLoginSpinner && btnLoginText) {
    loginForm.addEventListener('submit', function () {
        btnLogin.disabled = true;
        btnLoginText.textContent = 'Memproses...';
        btnLoginSpinner.classList.remove('d-none');
    });
}
</script>
@endpush
