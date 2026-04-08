@extends('layouts.app')

@section('title', 'Detail Berita Acara')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" style="border-left:4px solid #ea580c!important;">{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="ptpn-page-title h3 mb-1">Detail berita acara</h1>
            <p class="text-muted mb-0"><code class="text-success">{{ $m->nomor_surat }}</code></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('monitoring.pdf', $m) }}" class="btn btn-warning text-dark fw-semibold" target="_blank" rel="noopener">PDF berita acara</a>
            <a href="{{ route('monitoring.edit', $m) }}" class="btn btn-outline-primary">Edit</a>
            <a href="{{ route('monitoring.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card ptpn-card">
                <div class="card-header ptpn-card-header fw-bold">Data mitra &amp; pinjaman</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nama mitra</dt>
                        <dd class="col-sm-8">{{ $m->nama_mitra }}</dd>
                        <dt class="col-sm-4">Nama usaha</dt>
                        <dd class="col-sm-8">{{ $m->nama_usaha }}</dd>
                        <dt class="col-sm-4">Nomor induk</dt>
                        <dd class="col-sm-8">{{ $m->nomor_induk }}</dd>
                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $m->alamat }}</dd>
                        <dt class="col-sm-4">No. HP</dt>
                        <dd class="col-sm-8">{{ $m->no_hp }}</dd>
                        <dt class="col-sm-4">Nilai pinjaman</dt>
                        <dd class="col-sm-8">Rp {{ number_format($m->nilai_pinjaman, 2, ',', '.') }}</dd>
                        <dt class="col-sm-4">Sisa pinjaman</dt>
                        <dd class="col-sm-8">Rp {{ number_format($m->sisa_pinjaman, 2, ',', '.') }}</dd>
                        <dt class="col-sm-4">Tanggal kunjungan</dt>
                        <dd class="col-sm-8">{{ $m->tanggal->translatedFormat('d F Y') }}</dd>
                        <dt class="col-sm-4">Alasan tidak membayar</dt>
                        <dd class="col-sm-8">{{ $m->alasan ?: '—' }}</dd>
                        <dt class="col-sm-4">Janji pelunasan</dt>
                        <dd class="col-sm-8">{{ $m->janji ?: '—' }}</dd>
                        <dt class="col-sm-4">Kebutuhan saat ini</dt>
                        <dd class="col-sm-8">{{ $m->kebutuhan ?: '—' }}</dd>
                        <dt class="col-sm-4">Catatan</dt>
                        <dd class="col-sm-8">{{ $m->catatan ?: '—' }}</dd>
                        <dt class="col-sm-4">Petugas input</dt>
                        <dd class="col-sm-8">{{ $m->user?->name ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card ptpn-card mt-4">
                <div class="card-header ptpn-card-header fw-bold">Tanda tangan</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="small text-muted mb-2">Mitra binaan</div>
                            @if ($m->signature_mitra)
                                <img src="{{ $m->signatureMitraUrl() }}" alt="TTD mitra" class="img-fluid border rounded" style="max-height:160px;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-2">Petugas</div>
                            @if ($m->signature_petugas)
                                <img src="{{ $m->signaturePetugasUrl() }}" alt="TTD petugas" class="img-fluid border rounded" style="max-height:160px;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card ptpn-card mb-4">
                <div class="card-header ptpn-card-header fw-bold">Foto</div>
                <div class="card-body text-center">
                    @if ($m->foto)
                        <a href="{{ $m->fotoUrl() }}" target="_blank" rel="noopener">
                            <img src="{{ $m->fotoUrl() }}" alt="Foto" class="img-fluid rounded border">
                        </a>
                    @else
                        <p class="text-muted mb-0">Tidak ada foto.</p>
                    @endif
                </div>
            </div>

            <div class="card ptpn-card">
                <div class="card-header ptpn-card-header fw-bold">Lokasi (geotag)</div>
                <div class="card-body">
                    @if ($m->latitude !== null && $m->longitude !== null)
                        <p class="small mb-2">Lintang: {{ $m->latitude }}<br>Bujur: {{ $m->longitude }}</p>
                        <dl class="row small mb-3">
                            <dt class="col-4">Jalan</dt>
                            <dd class="col-8">{{ $m->geo_jalan ?: '—' }}</dd>
                            <dt class="col-4">Kelurahan</dt>
                            <dd class="col-8">{{ $m->geo_kelurahan ?: '—' }}</dd>
                            <dt class="col-4">Kecamatan</dt>
                            <dd class="col-8">{{ $m->geo_kecamatan ?: '—' }}</dd>
                            <dt class="col-4">Kota/Kab.</dt>
                            <dd class="col-8">{{ $m->geo_kota ?: '—' }}</dd>
                            <dt class="col-4">Provinsi</dt>
                            <dd class="col-8">{{ $m->geo_provinsi ?: '—' }}</dd>
                            <dt class="col-4">Kode pos</dt>
                            <dd class="col-8">{{ $m->geo_kode_pos ?: '—' }}</dd>
                        </dl>
                        <div class="ratio ratio-4x3 rounded overflow-hidden border">
                            <iframe
                                title="Peta lokasi"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://maps.google.com/maps?q={{ $m->latitude }},{{ $m->longitude }}&hl=id&z=16&output=embed">
                            </iframe>
                        </div>
                    @else
                        <p class="text-muted small mb-0">Koordinat belum diisi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
