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
        <div class="col-md-8">
            <div class="card ptpn-card ptpn-trend-chart h-100 border-0">
                <div class="ptpn-trend-chart__header px-4 pt-4 pb-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="ptpn-trend-chart__icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Tren Kunjungan 6 Bulan Terakhir</h5>
                            <p class="text-muted small mb-0">Perkembangan aktivitas penagihan mitra binaan</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-3 px-md-4 pb-4 pt-0 position-relative">
                    @if (array_sum($chartData['values']) === 0)
                        <div class="ptpn-trend-chart__empty">
                            <i class="fa-solid fa-chart-simple"></i>
                            <span>Belum ada kunjungan dalam 6 bulan terakhir</span>
                        </div>
                    @endif
                    <div class="ptpn-trend-chart__canvas-wrap">
                        <canvas id="visitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card ptpn-card border-0 shadow-sm">
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
                <div class="col-12">
                    <div class="card ptpn-card border-0 shadow-sm">
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
                <div class="col-12">
                    <div class="card ptpn-card border-0 shadow-sm">
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
                        <th class="py-3 text-uppercase small fw-bold text-muted">Jam</th>
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
                            <td>
                                <div class="text-dark">{{ $row->created_at->translatedFormat('H:i') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('monitoring.show', $row) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                    <i class="fa-solid fa-eye me-1 small"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('visitChart');
    if (!canvas) {
        return;
    }

    const labels = @json($chartData['labels']);
    const values = @json($chartData['values']);
    const lastIndex = values.length - 1;
    const maxVal = Math.max(...values, 0);
    const suggestedMax = maxVal === 0 ? 8 : Math.max(Math.ceil(maxVal * 1.3), maxVal + 1);

    const ctx = canvas.getContext('2d');
    const fillGradient = ctx.createLinearGradient(0, 0, 0, 300);
    fillGradient.addColorStop(0, 'rgba(21, 128, 61, 0.45)');
    fillGradient.addColorStop(0.55, 'rgba(21, 128, 61, 0.12)');
    fillGradient.addColorStop(1, 'rgba(234, 88, 12, 0.04)');

    const lineGradient = ctx.createLinearGradient(0, 0, canvas.offsetWidth || 600, 0);
    lineGradient.addColorStop(0, '#0d4f2d');
    lineGradient.addColorStop(0.7, '#15803d');
    lineGradient.addColorStop(1, '#ea580c');

    const barColors = values.map((_, i) => {
        if (i === lastIndex) {
            return 'rgba(234, 88, 12, 0.28)';
        }
        return 'rgba(21, 128, 61, 0.14)';
    });

    new Chart(canvas, {
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Kunjungan',
                    data: values,
                    backgroundColor: barColors,
                    hoverBackgroundColor: barColors.map((c, i) => i === lastIndex ? 'rgba(234, 88, 12, 0.45)' : 'rgba(21, 128, 61, 0.28)'),
                    borderRadius: { topLeft: 10, topRight: 10, bottomLeft: 4, bottomRight: 4 },
                    borderSkipped: false,
                    barPercentage: 0.58,
                    categoryPercentage: 0.72,
                    order: 2,
                },
                {
                    type: 'line',
                    label: 'Tren',
                    data: values,
                    borderColor: lineGradient,
                    backgroundColor: fillGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.42,
                    pointRadius: values.map((_, i) => i === lastIndex ? 7 : 5),
                    pointHoverRadius: 9,
                    pointBackgroundColor: values.map((_, i) => i === lastIndex ? '#ea580c' : '#15803d'),
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBorderWidth: 3,
                    order: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            animation: {
                duration: 900,
                easing: 'easeOutQuart',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(5, 46, 22, 0.92)',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 13 },
                    padding: 14,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        title: (items) => items[0]?.label ?? '',
                        label: (ctx) => ' ' + ctx.parsed.y + ' kunjungan',
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax,
                    ticks: {
                        stepSize: maxVal <= 10 ? 1 : undefined,
                        precision: 0,
                        color: '#64748b',
                        font: { size: 11, weight: '500' },
                        padding: 8,
                    },
                    grid: {
                        color: 'rgba(13, 79, 45, 0.08)',
                        drawBorder: false,
                    },
                    border: { display: false },
                },
                x: {
                    ticks: {
                        color: '#475569',
                        font: { size: 11, weight: '600' },
                        maxRotation: 0,
                    },
                    grid: { display: false },
                    border: { display: false },
                },
            },
        },
    });
});
</script>
@endpush
