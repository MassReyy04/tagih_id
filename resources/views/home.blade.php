@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert" style="border-left: 4px solid #ea580c !important;">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h1 class="ptpn-page-title h2 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Ringkasan monitoring dan penagihan mitra binaan — <span class="text-success fw-semibold">PTPN IV Regional 4</span></p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('monitoring.create') }}" class="btn btn-primary btn-lg px-4">
                <span class="me-1">＋</span> Input berita acara
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card ptpn-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="stat-card-icon stat-card-icon--green shadow-sm">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Total Kunjungan</div>
                        <div class="h2 mb-0 fw-bold" style="color: var(--ptpn-green-deep);">{{ number_format($total) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card ptpn-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="stat-card-icon stat-card-icon--orange shadow-sm">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Kunjungan Bulan Ini</div>
                        <div class="h2 mb-0 fw-bold" style="color: var(--ptpn-orange-deep);">{{ number_format($bulanIni) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card ptpn-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="stat-card-icon stat-card-icon--muted shadow-sm">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.5px;">Periode Aktif</div>
                        <div class="h4 fw-bold mb-0" style="color: var(--ptpn-green);">{{ now()->translatedFormat('F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card ptpn-card border-0 shadow-sm">
        <div class="card-header ptpn-card-header d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-success"></i>
                <span class="fw-bold">Kunjungan Terbaru</span>
            </div>
            <a href="{{ route('monitoring.index') }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                Lihat semua data <i class="fa-solid fa-arrow-right ms-1 small"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Nomor Surat</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Mitra</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Petugas</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                        <th class="text-end pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($terbaru as $row)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-success bg-opacity-10 text-success fw-medium px-2 py-1" style="font-family: 'Courier New', monospace; font-size: 0.85rem;">
                                    {{ $row->nomor_surat }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_mitra }}</div>
                                <div class="small text-muted">{{ $row->nama_usaha }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center fw-bold border" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ substr($row->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-muted small">{{ $row->user?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark">{{ $row->tanggal->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('monitoring.show', $row) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                    <i class="fa-solid fa-eye me-1 small"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada data kunjungan terbaru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
