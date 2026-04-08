@extends('layouts.guest')

@section('title', 'Selamat datang')

@section('content')
<div class="d-flex min-vh-100 align-items-center justify-content-center p-3 ptpn-login-main">
    <div class="ptpn-login-card" style="max-width: 520px;">
        <p class="small text-success fw-bold text-uppercase mb-2" style="letter-spacing:0.12em;">PT Perkebunan Nusantara IV · Regional 4</p>
        <h1 class="h4 fw-bold mb-2" style="color: var(--ptpn-green-deep);">Monitoring &amp; penagihan mitra</h1>
        <p class="text-muted small mb-4">Sistem informasi berita acara kunjungan dengan nuansa hijau dan oranye khas lingkungan perkebunan.</p>
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-4">Masuk ke aplikasi</a>
                            </div>
                        </div>
@endsection