<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel')) — PTPN IV Regional 4</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        .swal2-toast {
            padding: 0.75rem 1rem !important;
        }
    </style>
</head>
<body class="ptpn-body">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark navbar-ptpn shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="rounded-1" style="height: 2.2rem; width: auto; background: white; padding: 2px;">
                    <span class="d-none d-sm-inline">Monitoring &amp; Penagihan Mitra</span>
                    <span class="d-sm-none">Penagihan Mitra</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" href="{{ route('home') }}">
                                    <i class="fa-solid fa-house-chimney me-1 small opacity-75"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('monitoring.index') ? 'active fw-bold' : '' }}" href="{{ route('monitoring.index') }}">
                                    <i class="fa-solid fa-file-invoice me-1 small opacity-75"></i> Data Berita Acara
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('monitoring.create') ? 'active fw-bold' : '' }}" href="{{ route('monitoring.create') }}">
                                    <i class="fa-solid fa-circle-plus me-1 small opacity-75"></i> Input Baru
                                </a>
                            </li>
                            @if (Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active fw-bold' : '' }}" href="{{ route('dashboard.admin') }}">
                                        <i class="fa-solid fa-chart-pie me-1 small opacity-75"></i> Rekap &amp; Monitoring
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.users.index') }}">
                                        <i class="fa-solid fa-users-gear me-1 small opacity-75"></i> Kelola Petugas
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" href="{{ route('login') }}">
                                        <i class="fa-solid fa-right-to-bracket me-1 small"></i> Masuk
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item me-lg-3 mb-2 mb-lg-0">
                                <span class="badge rounded-pill {{ Auth::user()->role === 'admin' ? 'badge-role-admin' : 'badge-role-petugas' }} px-3 py-2 shadow-sm">
                                    <i class="fa-solid {{ Auth::user()->role === 'admin' ? 'fa-user-shield' : 'fa-user' }} me-1 small"></i>
                                    {{ Auth::user()->role === 'admin' ? 'Admin' : 'Petugas Penagih' }}
                                </span>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @if(Auth::user()->profile_photo)
                                        <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="Profile" class="rounded-circle border shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2" style="border-radius:1rem; min-width: 180px;">
                                    <div class="dropdown-header text-uppercase small fw-bold text-muted letter-spacing-1">Pengaturan Akun</div>
                                    <a class="dropdown-item py-2 {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                        <i class="fa-solid fa-user-gear me-2 text-primary opacity-75"></i> Profil Saya
                                    </a>
                                    <div class="dropdown-divider mx-3"></div>
                                    <a class="dropdown-item py-2" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa-solid fa-power-off me-2 text-danger opacity-75"></i> Keluar
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 py-md-5">
            @yield('content')
        </main>

        <footer class="pb-4 text-center small text-muted">
            <div class="container">
                <span class="text-success fw-semibold">PT Perkebunan Nusantara IV</span>
                · Regional 4 · Sistem informasi penagihan mitra binaan
            </div>
        </footer>
    </div>
    @stack('scripts')
    
    {{-- Audio Elements --}}
    <audio id="soundSuccess" preload="auto">
        <source src="{{ asset('sounds/simpan.wav') }}" type="audio/wav">
    </audio>
    <audio id="soundDelete" preload="auto">
        <source src="{{ asset('sounds/hapus.wav') }}" type="audio/wav">
    </audio>

    <script>
        function playNotificationSound(type) {
            const sound = document.getElementById(type === 'success' ? 'soundSuccess' : 'soundDelete');
            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(error => {
                    console.log("Autoplay blocked or audio file missing:", error);
                });
            }
        }
    </script>
    
    @if (session('status') || session('success'))
        <script>
            playNotificationSound('success');
            
            // Animasi Konfeti Kecil
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#22c55e', '#166534', '#ffffff']
            });

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('status') ?? session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end',
                timerProgressBar: true,
                background: '#f0fdf4',
                color: '#166534',
                iconColor: '#22c55e',
                showClass: {
                    popup: 'animate__animated animate__fadeInRight'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight'
                }
            });
        </script>
    @endif

    @if (session('delete'))
        <script>
            playNotificationSound('delete');
            Swal.fire({
                icon: 'success',
                title: 'Dihapus!',
                text: "{{ session('delete') }}",
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end',
                timerProgressBar: true,
                background: '#fff1f2',
                color: '#991b1b',
                iconColor: '#f43f5e',
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight'
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                showConfirmButton: true,
                confirmButtonColor: '#ea580c',
            });
        </script>
    @endif
</body>
</html>
