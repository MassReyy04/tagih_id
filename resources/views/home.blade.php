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
        @if (Auth::user() && !Auth::user()->isRegional())
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('monitoring.create') }}" class="btn btn-primary btn-lg px-4">
                    <span class="me-1">＋</span> Input berita acara
                </a>
            </div>
        @endif
    </div>

    <div class="row g-3 mb-3" id="kolektibilitas-dashboard">
        <div class="col-12">
            <div class="card ptpn-card">
                <div class="card-header ptpn-card-header fw-bold">
                    <span>Kolektibilitas {{ now()->year }}</span>
                </div>
                <div class="card-body ptpn-kolek-chart-wrap" style="cursor: pointer;" title="Klik batang untuk detail harian">
                    <canvas id="kolektibilitasMonthlyChartHome" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12">
            @php
                $outstandingJumlah = (float) ($kolektibilitasSummary['jumlah_saldo'] ?? 0);
                $tingkat = (float) ($kolektibilitasSummary['jumlah_perkalian'] ?? 0);
                $nilai = $kolektibilitasSummary['nilai'] !== null ? (float) $kolektibilitasSummary['nilai'] : 0.0;
                $skor = $kolektibilitasSummary['skor'] !== null ? (int) $kolektibilitasSummary['skor'] : 0;
            @endphp
            <div class="row g-3 align-items-stretch">
                <div class="col-lg-8">
                    <div class="card ptpn-card h-100 ptpn-kolek-panel ptpn-kolek-table-card">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle ptpn-kolek-side__table">
                                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
                                    <tr>
                                        <th>Kualitas Pinjaman</th>
                                        <th>Umur Piutang</th>
                                        <th class="text-end">Saldo Piutang</th>
                                        <th class="text-center">%</th>
                                        <th class="text-end">Rata-rata tertimbang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kolektibilitasSummary['rows'] as $row)
                                        <tr class="ptpn-kolek-row ptpn-kolek-row--{{ $row['key'] }}">
                                            <td>{{ $row['label'] }}</td>
                                            <td>{{ $row['umur'] }}</td>
                                            <td class="text-end font-monospace">{{ number_format($row['saldo'], 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $row['bobot_label'] }}</td>
                                            <td class="text-end font-monospace">
                                                @if ($row['perkalian'] > 0)
                                                    {{ number_format($row['perkalian'], 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="ptpn-kolek-row ptpn-kolek-row--jumlah fw-bold">
                                        <td style="background: #14532d !important; color: white !important;">Jumlah</td>
                                        <td class="ptpn-kolek-side__empty-cell" style="background: #14532d !important; color: white !important;"></td>
                                        <td class="text-end font-monospace" style="background: #14532d !important; color: white !important;">{{ number_format($kolektibilitasSummary['jumlah_saldo'], 0, ',', '.') }}</td>
                                        <td class="ptpn-kolek-side__empty-cell" style="background: #14532d !important; color: white !important;"></td>
                                        <td class="text-end font-monospace" style="background: #14532d !important; color: white !important;">{{ number_format($kolektibilitasSummary['jumlah_perkalian'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="ptpn-kolek-row ptpn-kolek-row--bermasalah" style="background-color: rgba(220, 53, 69, 0.12);">
                                        <td style="color: #b91c1c !important; font-weight: 800;">Bermasalah</td>
                                        <td class="ptpn-kolek-side__empty-cell"></td>
                                        <td class="text-end font-monospace" style="color: #b91c1c !important; font-weight: 800;">{{ number_format($kolektibilitasSummary['saldo_bermasalah'], 0, ',', '.') }}</td>
                                        <td class="ptpn-kolek-side__empty-cell"></td>
                                        <td class="text-end font-monospace" style="color: #b91c1c !important;">-</td>
                                    </tr>
                                    <tr class="ptpn-kolek-row ptpn-kolek-row--total fw-bold">
                                        <td style="background: #14532d !important; color: white !important;">Total</td>
                                        <td class="ptpn-kolek-side__empty-cell" style="background: #14532d !important; color: white !important;"></td>
                                        <td class="text-end font-monospace" style="background: #14532d !important; color: white !important;">{{ number_format($kolektibilitasSummary['total_saldo'], 0, ',', '.') }}</td>
                                        <td class="ptpn-kolek-side__empty-cell" style="background: #14532d !important; color: white !important;"></td>
                                        <td class="text-end font-monospace" style="background: #14532d !important; color: white !important;">{{ number_format($kolektibilitasSummary['total_perkalian'], 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card ptpn-card h-100 ptpn-kolek-panel ptpn-kolek-formula-card d-flex flex-column">
                        <div class="ptpn-kolek-panel__header">Tingkat Kolektibilitas</div>
                        <div class="ptpn-kolek-formula-panel">
                            <div class="ptpn-kolek-formula-step">
                                <div class="ptpn-kolek-formula-step__eq">=</div>
                                <div class="ptpn-kolek-formula-step__content">
                                    <div class="ptpn-kolek-formula-math">
                                        <span class="ptpn-kolek-formula-frac">
                                            <span class="ptpn-kolek-formula-frac__num">Rata-rata tertimbang</span>
                                            <span class="ptpn-kolek-formula-frac__line"></span>
                                            <span class="ptpn-kolek-formula-frac__den">Jumlah saldo piutang</span>
                                        </span>
                                        <span class="ptpn-kolek-formula-math__suffix">× 100%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ptpn-kolek-formula-step ptpn-kolek-formula-step--values">
                                <div class="ptpn-kolek-formula-step__eq">=</div>
                                <div class="ptpn-kolek-formula-step__content">
                                    <div class="ptpn-kolek-formula-math">
                                        <span class="ptpn-kolek-formula-frac">
                                            <span class="ptpn-kolek-formula-frac__num">{{ number_format($tingkat, 0, ',', '.') }}</span>
                                            <span class="ptpn-kolek-formula-frac__line"></span>
                                            <span class="ptpn-kolek-formula-frac__den">{{ number_format($outstandingJumlah, 0, ',', '.') }}</span>
                                        </span>
                                        <span class="ptpn-kolek-formula-math__suffix">× 100%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ptpn-kolek-formula-step ptpn-kolek-formula-step--result">
                                <div class="ptpn-kolek-formula-step__eq">=</div>
                                <div class="ptpn-kolek-formula-step__content ptpn-kolek-formula-step__content--result">
                                    @if ($kolektibilitasSummary['nilai'] !== null)
                                        {{ number_format($nilai, 2, ',', '.') }}%
                                    @else
                                        —%
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-lg-8">
            <div class="card ptpn-card ptpn-trend-chart h-100 border-0">
                <div class="ptpn-trend-chart__header px-4 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ptpn-trend-chart__icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Data Kunjungan {{ $visitChart['year'] }}</h5>
                    </div>
                </div>
                <div class="card-body px-3 px-md-4 pb-4 pt-0 position-relative flex-grow-1 d-flex flex-column">
                    <div class="ptpn-trend-chart__canvas-wrap flex-grow-1" style="cursor: pointer;" title="Klik batang untuk detail harian">
                        @if (array_sum($visitChart['monthly']['values']) === 0)
                            <div class="ptpn-trend-chart__empty">
                                <i class="fa-solid fa-chart-simple"></i>
                                <span>Belum ada kunjungan pada tahun ini</span>
                            </div>
                        @endif
                        <canvas id="visitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-flex">
            <div class="card ptpn-card ptpn-visit-stats h-100 w-100 border-0">
                <div class="ptpn-visit-stats__header px-4 py-3">
                    <h5 class="fw-bold mb-0">Ringkasan</h5>
                </div>
                <div class="ptpn-visit-stats__body">
                    <div class="ptpn-visit-stats__item">
                        <div class="stat-card-icon stat-card-icon--green shadow-sm">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div class="ptpn-visit-stats__content">
                            <div class="ptpn-visit-stats__label">Total Kunjungan</div>
                            <div class="ptpn-visit-stats__value ptpn-visit-stats__value--green">{{ number_format($total) }}</div>
                        </div>
                    </div>
                    <div class="ptpn-visit-stats__item">
                        <div class="stat-card-icon stat-card-icon--orange shadow-sm">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="ptpn-visit-stats__content">
                            <div class="ptpn-visit-stats__label">Kunjungan Bulan Ini</div>
                            <div class="ptpn-visit-stats__value ptpn-visit-stats__value--orange">{{ number_format($bulanIni) }}</div>
                        </div>
                    </div>
                    <div class="ptpn-visit-stats__item ptpn-visit-stats__item--last">
                        <div class="stat-card-icon stat-card-icon--muted shadow-sm">
                            <i class="fa-solid fa-leaf"></i>
                        </div>
                        <div class="ptpn-visit-stats__content">
                            <div class="ptpn-visit-stats__label">Periode Aktif</div>
                            <div class="ptpn-visit-stats__value ptpn-visit-stats__value--period">{{ now()->translatedFormat('F Y') }}</div>
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
                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Nomor Surat</th>
                        <th class="py-3 text-uppercase small fw-bold">Mitra</th>
                        <th class="py-3 text-uppercase small fw-bold">Petugas</th>
                        <th class="py-3 text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-uppercase small fw-bold">Jam</th>
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

<div class="modal fade" id="kolektibilitasDetailModalHome" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem;">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="kolektibilitasDetailTitleHome">Detail harian</h5>
                    <div class="small text-muted" id="kolektibilitasDetailCountHome">0 data tersimpan</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="kolektibilitasDetailChartWrapHome" style="height: 280px;">
                    <canvas id="kolektibilitasDetailChartHome"></canvas>
                </div>
                <div class="table-responsive mt-3" style="max-height: 240px;">
                    <table class="table table-sm table-hover mb-0 align-middle">
                                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
                                    <tr>
                                        <th class="ps-3 py-2 text-uppercase small fw-bold">Tanggal input</th>
                                        <th class="text-end pe-3 py-2 text-uppercase small fw-bold">Nilai (%)</th>
                                    </tr>
                                </thead>
                        <tbody id="kolektibilitasDetailTableHome"></tbody>
                    </table>
                </div>
                <div class="small text-muted mt-2">
                    Batang grafik menampilkan nilai terakhir tiap bulan. Klik batang untuk melihat semua data harian yang sudah disimpan.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="visitDetailModalHome" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem;">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="visitDetailTitleHome">Detail harian</h5>
                    <div class="small text-muted" id="visitDetailCountHome">0 hari dengan input</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="small fw-semibold text-muted mb-2">Tren harian</div>
                        <div id="visitDetailChartWrapHome" style="height: 240px;">
                            <canvas id="visitDetailChartHome"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="small fw-semibold text-muted mb-2" id="visitDetailPetugasTitleHome">Petugas yang input</div>
                        <div id="visitDetailPetugasChartWrapHome" style="height: 240px;">
                            <canvas id="visitDetailPetugasChartHome"></canvas>
                        </div>
                        <div id="visitDetailPetugasEmptyHome" class="text-muted small text-center py-5 d-none">
                            Pilih tanggal untuk melihat petugas yang input.
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <div class="table-responsive" style="max-height: 200px;">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
                                    <tr>
                                        <th class="ps-3 py-2 text-uppercase small fw-bold">Tanggal input</th>
                                        <th class="text-end pe-3 py-2 text-uppercase small fw-bold">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="visitDetailTableHome"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive" style="max-height: 200px;">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Petugas</th>
                                        <th class="text-end">Jumlah input</th>
                                    </tr>
                                </thead>
                                <tbody id="visitDetailPetugasTableHome"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    Klik tanggal pada tabel atau titik grafik garis untuk melihat diagram batang per petugas beserta total input pada hari tersebut.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kolektibilitas chart (dashboard)
    const kolektibilitasData = @json($kolektibilitasChart ?? null);
    const kolektibilitasCanvas = document.getElementById('kolektibilitasMonthlyChartHome');
    if (kolektibilitasCanvas && kolektibilitasData?.monthly) {
        const green = getComputedStyle(document.documentElement).getPropertyValue('--ptpn-green-mid').trim() || '#15803d';
        const orange = getComputedStyle(document.documentElement).getPropertyValue('--ptpn-orange').trim() || '#ea580c';

        const values = kolektibilitasData.monthly.values.map(v => (v === null ? null : Number(Number(v).toFixed(2))));
        const labels = kolektibilitasData.monthly.labels;
        const keys = kolektibilitasData.monthly.keys;
        const lastDataIndex = values.reduce((acc, value, index) => value !== null ? index : acc, -1);

        const kctx = kolektibilitasCanvas.getContext('2d');
        const chartHeight = kolektibilitasCanvas.offsetHeight || 280;

        const fillGradient = kctx.createLinearGradient(0, 0, 0, chartHeight);
        fillGradient.addColorStop(0, 'rgba(21, 128, 61, 0.45)');
        fillGradient.addColorStop(0.55, 'rgba(21, 128, 61, 0.12)');
        fillGradient.addColorStop(1, 'rgba(234, 88, 12, 0.04)');

        const lineGradient = kctx.createLinearGradient(0, 0, kolektibilitasCanvas.offsetWidth || 600, 0);
        lineGradient.addColorStop(0, '#0d4f2d');
        lineGradient.addColorStop(0.7, green);
        lineGradient.addColorStop(1, orange);

        const barColors = values.map((value, index) => {
            if (value === null) {
                return 'rgba(100, 116, 139, 0.08)';
            }
            if (index === lastDataIndex) {
                return 'rgba(234, 88, 12, 0.28)';
            }
            return 'rgba(21, 128, 61, 0.14)';
        });

        const barHoverColors = values.map((value, index) => {
            if (value === null) {
                return 'rgba(100, 116, 139, 0.12)';
            }
            if (index === lastDataIndex) {
                return 'rgba(234, 88, 12, 0.45)';
            }
            return 'rgba(21, 128, 61, 0.28)';
        });

        new Chart(kolektibilitasCanvas, {
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Nilai (%)',
                        data: values,
                        backgroundColor: barColors,
                        hoverBackgroundColor: barHoverColors,
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
                        spanGaps: true,
                        pointRadius: values.map((value, index) => {
                            if (value === null) return 0;
                            return index === lastDataIndex ? 7 : 5;
                        }),
                        pointHoverRadius: 9,
                        pointBackgroundColor: values.map((value, index) => {
                            if (value === null) return 'transparent';
                            return index === lastDataIndex ? orange : green;
                        }),
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
                        filter: (item) => item.datasetIndex === 0,
                        callbacks: {
                            title: (items) => items[0]?.label ?? '',
                            label: (c) => c.parsed.y === null ? ' Belum ada data' : (' ' + c.parsed.y.toFixed(2).replace('.', ',') + '%'),
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100,
                        ticks: { callback: (v) => v + '%', color: '#64748b', font: { size: 11, weight: '600' } },
                        grid: { color: 'rgba(13, 79, 45, 0.08)', drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: { color: '#475569', font: { size: 11, weight: '600' }, maxRotation: 0 },
                        grid: { display: false },
                        border: { display: false }
                    }
                },
                onClick: function (_evt, elements) {
                    if (!elements?.length) return;
                    const barElement = elements.find((el) => el.datasetIndex === 0) ?? elements[0];
                    if (barElement.datasetIndex !== 0) return;
                    const i = barElement.index;
                    if (values[i] === null) return;
                    const monthKey = keys[i];
                    openKolektibilitasMonthDetail(monthKey, labels[i]);
                }
            }
        });

        let detailChart = null;

        function showKolektibilitasModal(modalEl) {
            if (window.bootstrap?.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                return;
            }

            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
            modalEl.setAttribute('aria-modal', 'true');
            document.body.classList.add('modal-open');

            let backdrop = document.getElementById('kolektibilitasModalBackdropHome');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'kolektibilitasModalBackdropHome';
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }

            modalEl.querySelector('.btn-close')?.addEventListener('click', function () {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
                backdrop?.remove();
            }, { once: true });
        }

        function openKolektibilitasMonthDetail(monthKey, monthLabel) {
            const month = kolektibilitasData.dailyByMonth?.[monthKey] ?? { labels: [], values: [], entries: [] };

            const modalEl = document.getElementById('kolektibilitasDetailModalHome');
            const titleEl = document.getElementById('kolektibilitasDetailTitleHome');
            const detailCanvas = document.getElementById('kolektibilitasDetailChartHome');
            const tableBody = document.getElementById('kolektibilitasDetailTableHome');
            const countEl = document.getElementById('kolektibilitasDetailCountHome');
            const chartWrap = document.getElementById('kolektibilitasDetailChartWrapHome');
            if (!modalEl || !titleEl || !detailCanvas) return;

            titleEl.textContent = 'Detail harian: ' + monthLabel;

            const entries = month.entries ?? [];
            if (countEl) {
                countEl.textContent = entries.length + ' data tersimpan';
            }

            if (tableBody) {
                if (entries.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="2" class="text-muted text-center py-3">Belum ada data input untuk bulan ini.</td></tr>';
                } else {
                    tableBody.innerHTML = entries.map(function (entry) {
                        const nilai = entry.nilai === null
                            ? '—'
                            : Number(entry.nilai).toFixed(2).replace('.', ',') + '%';
                        return '<tr><td>' + entry.label + '</td><td class="text-end font-monospace fw-semibold">' + nilai + '</td></tr>';
                    }).join('');
                }
            }

            if (chartWrap) {
                chartWrap.classList.toggle('d-none', entries.length === 0);
            }

            if (detailChart) {
                detailChart.destroy();
                detailChart = null;
            }

            if (entries.length === 0) {
                showKolektibilitasModal(modalEl);
                return;
            }

            const dLabels = month.labels;
            const dValues = month.values.map(v => (v === null ? null : Number(Number(v).toFixed(2))));

            const dctx = detailCanvas.getContext('2d');
            const lineGradient = dctx.createLinearGradient(0, 0, detailCanvas.offsetWidth || 600, 0);
            lineGradient.addColorStop(0, green);
            lineGradient.addColorStop(1, orange);

            detailChart = new Chart(detailCanvas, {
                type: 'line',
                data: {
                    labels: dLabels,
                    datasets: [{
                        label: 'Nilai (%)',
                        data: dValues,
                        borderColor: lineGradient,
                        backgroundColor: 'rgba(21, 128, 61, 0.10)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: green,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(5, 46, 22, 0.92)',
                            displayColors: false,
                            callbacks: {
                                label: (c) => c.parsed.y === null ? ' Belum ada data' : (' ' + c.parsed.y.toFixed(2).replace('.', ',') + '%'),
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 100,
                            ticks: { callback: (v) => v + '%', color: '#64748b', font: { size: 11, weight: '600' } },
                            grid: { color: 'rgba(13, 79, 45, 0.08)', drawBorder: false },
                            border: { display: false }
                        },
                        x: {
                            ticks: { color: '#475569', font: { size: 11, weight: '600' }, maxRotation: 0 },
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });

            showKolektibilitasModal(modalEl);
        }
    }

    const canvas = document.getElementById('visitChart');
    const visitData = @json($visitChart ?? null);
    if (!canvas || !visitData?.monthly) {
        return;
    }

    const green = getComputedStyle(document.documentElement).getPropertyValue('--ptpn-green-mid').trim() || '#15803d';
    const orange = getComputedStyle(document.documentElement).getPropertyValue('--ptpn-orange').trim() || '#ea580c';

    const labels = visitData.monthly.labels;
    const keys = visitData.monthly.keys;
    const values = visitData.monthly.values.map((v) => Number(v));
    const lastIndex = values.length - 1;
    const maxVal = Math.max(...values, 0);
    const suggestedMax = maxVal === 0 ? 8 : Math.max(Math.ceil(maxVal * 1.3), maxVal + 1);

    const ctx = canvas.getContext('2d');
    const chartHeight = canvas.offsetHeight || 300;

    const fillGradient = ctx.createLinearGradient(0, 0, 0, chartHeight);
    fillGradient.addColorStop(0, 'rgba(21, 128, 61, 0.45)');
    fillGradient.addColorStop(0.55, 'rgba(21, 128, 61, 0.12)');
    fillGradient.addColorStop(1, 'rgba(234, 88, 12, 0.04)');

    const lineGradient = ctx.createLinearGradient(0, 0, canvas.offsetWidth || 600, 0);
    lineGradient.addColorStop(0, '#0d4f2d');
    lineGradient.addColorStop(0.7, green);
    lineGradient.addColorStop(1, orange);

    const barColors = values.map((value, index) => {
        if (value <= 0) {
            return 'rgba(100, 116, 139, 0.08)';
        }
        if (index === lastIndex) {
            return 'rgba(234, 88, 12, 0.28)';
        }
        return 'rgba(21, 128, 61, 0.14)';
    });

    const barHoverColors = values.map((value, index) => {
        if (value <= 0) {
            return 'rgba(100, 116, 139, 0.12)';
        }
        if (index === lastIndex) {
            return 'rgba(234, 88, 12, 0.45)';
        }
        return 'rgba(21, 128, 61, 0.28)';
    });

    let visitDetailChart = null;
    let visitDetailPetugasChart = null;
    let currentVisitEntries = [];
    let selectedVisitDate = null;

    const petugasBarColors = [
        'rgba(21, 128, 61, 0.75)',
        'rgba(234, 88, 12, 0.75)',
        'rgba(30, 79, 132, 0.75)',
        'rgba(168, 85, 247, 0.75)',
        'rgba(220, 38, 38, 0.75)',
        'rgba(14, 116, 144, 0.75)',
    ];

    function showVisitModal(modalEl) {
        if (window.bootstrap?.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }

        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');
    }

    function renderVisitDateTable(entries, selectedTanggal) {
        const tableBody = document.getElementById('visitDetailTableHome');
        if (!tableBody) return;

        if (entries.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="2" class="text-muted text-center py-3">Belum ada input pada bulan ini.</td></tr>';
            return;
        }

        tableBody.innerHTML = entries.map(function (entry) {
            const isActive = entry.tanggal === selectedTanggal;
            return '<tr class="visit-date-row' + (isActive ? ' table-active' : '') + '" data-tanggal="' + entry.tanggal + '" role="button" style="cursor:pointer;">'
                + '<td>' + entry.label + '</td>'
                + '<td class="text-end font-monospace fw-semibold">' + Number(entry.jumlah).toLocaleString('id-ID') + '</td>'
                + '</tr>';
        }).join('');

        tableBody.querySelectorAll('.visit-date-row').forEach(function (row) {
            row.addEventListener('click', function () {
                const tanggal = row.getAttribute('data-tanggal');
                const entry = currentVisitEntries.find(function (item) { return item.tanggal === tanggal; });
                if (entry) {
                    selectVisitDateEntry(entry);
                }
            });
        });
    }

    function highlightVisitLineChartSelection() {
        if (!visitDetailChart || !currentVisitEntries.length) return;

        visitDetailChart.data.datasets[0].pointBackgroundColor = currentVisitEntries.map(function (entry) {
            return entry.tanggal === selectedVisitDate ? orange : green;
        });
        visitDetailChart.data.datasets[0].pointRadius = currentVisitEntries.map(function (entry) {
            return entry.tanggal === selectedVisitDate ? 6 : 4;
        });
        visitDetailChart.update('none');
    }

    function renderVisitPetugasForDate(entry) {
        const petugasCanvas = document.getElementById('visitDetailPetugasChartHome');
        const petugasTableBody = document.getElementById('visitDetailPetugasTableHome');
        const petugasTitleEl = document.getElementById('visitDetailPetugasTitleHome');
        const petugasChartWrap = document.getElementById('visitDetailPetugasChartWrapHome');
        const petugasEmptyEl = document.getElementById('visitDetailPetugasEmptyHome');
        if (!petugasCanvas || !entry) return;

        const byPetugas = entry.byPetugas ?? [];
        const totalJumlah = Number(entry.jumlah || 0) || byPetugas.reduce(function (sum, row) {
            return sum + Number(row.jumlah || 0);
        }, 0);

        if (petugasTitleEl) {
            petugasTitleEl.textContent = 'Petugas pada ' + entry.label;
        }

        if (petugasTableBody) {
            if (byPetugas.length === 0) {
                petugasTableBody.innerHTML = '<tr><td colspan="2" class="text-muted text-center py-3">Belum ada petugas pada tanggal ini.</td></tr>';
            } else {
                const rowsHtml = byPetugas.map(function (row) {
                    return '<tr><td>' + row.name + '</td><td class="text-end font-monospace fw-semibold">' + Number(row.jumlah).toLocaleString('id-ID') + '</td></tr>';
                }).join('');
                petugasTableBody.innerHTML = rowsHtml
                    + '<tr class="table-light fw-semibold"><td>Total</td><td class="text-end font-monospace">' + totalJumlah.toLocaleString('id-ID') + '</td></tr>';
            }
        }

        const hasPetugas = byPetugas.length > 0;
        if (petugasChartWrap) petugasChartWrap.classList.toggle('d-none', !hasPetugas);
        if (petugasEmptyEl) petugasEmptyEl.classList.toggle('d-none', hasPetugas);

        if (visitDetailPetugasChart) {
            visitDetailPetugasChart.destroy();
            visitDetailPetugasChart = null;
        }

        if (!hasPetugas) return;

        const chartLabels = byPetugas.map(function (row) { return row.name; }).concat(['Total']);
        const chartValues = byPetugas.map(function (row) { return Number(row.jumlah); }).concat([totalJumlah]);
        const chartColors = byPetugas.map(function (_row, index) {
            return petugasBarColors[index % petugasBarColors.length];
        }).concat(['rgba(13, 79, 45, 0.88)']);

        visitDetailPetugasChart = new Chart(petugasCanvas, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah input',
                    data: chartValues,
                    backgroundColor: chartColors,
                    borderRadius: { topLeft: 6, topRight: 6 },
                    borderSkipped: false,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(5, 46, 22, 0.92)',
                        callbacks: {
                            label: (c) => ' ' + c.parsed.y + ' kunjungan',
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { size: 11, weight: '600' }
                        },
                        grid: { color: 'rgba(13, 79, 45, 0.08)', drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: { color: '#475569', font: { size: 11, weight: '600' }, maxRotation: 45 },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    function selectVisitDateEntry(entry) {
        if (!entry) return;

        selectedVisitDate = entry.tanggal;
        renderVisitDateTable(currentVisitEntries, selectedVisitDate);
        highlightVisitLineChartSelection();
        renderVisitPetugasForDate(entry);
    }

    function openVisitMonthDetail(monthKey, monthLabel) {
        const month = visitData.dailyByMonth?.[monthKey] ?? {
            labels: [], values: [], entries: [], byPetugas: [],
        };
        const modalEl = document.getElementById('visitDetailModalHome');
        const titleEl = document.getElementById('visitDetailTitleHome');
        const detailCanvas = document.getElementById('visitDetailChartHome');
        const countEl = document.getElementById('visitDetailCountHome');
        const chartWrap = document.getElementById('visitDetailChartWrapHome');
        const petugasChartWrap = document.getElementById('visitDetailPetugasChartWrapHome');
        const petugasEmptyEl = document.getElementById('visitDetailPetugasEmptyHome');
        if (!modalEl || !titleEl || !detailCanvas) return;

        titleEl.textContent = 'Detail harian: ' + monthLabel;

        currentVisitEntries = month.entries ?? [];
        selectedVisitDate = null;
        const byPetugas = month.byPetugas ?? [];
        const totalKunjungan = currentVisitEntries.reduce((sum, entry) => sum + Number(entry.jumlah || 0), 0);

        if (countEl) {
            countEl.textContent = currentVisitEntries.length + ' hari · ' + byPetugas.length + ' petugas · total ' + totalKunjungan + ' kunjungan';
        }

        renderVisitDateTable(currentVisitEntries, null);

        if (petugasChartWrap) petugasChartWrap.classList.add('d-none');
        if (petugasEmptyEl) petugasEmptyEl.classList.remove('d-none');

        const petugasTableBody = document.getElementById('visitDetailPetugasTableHome');
        if (petugasTableBody) {
            petugasTableBody.innerHTML = '<tr><td colspan="2" class="text-muted text-center py-3">Pilih tanggal di sebelah kiri.</td></tr>';
        }

        const hasCharts = currentVisitEntries.length > 0;
        if (chartWrap) chartWrap.classList.toggle('d-none', !hasCharts);

        if (visitDetailChart) {
            visitDetailChart.destroy();
            visitDetailChart = null;
        }
        if (visitDetailPetugasChart) {
            visitDetailPetugasChart.destroy();
            visitDetailPetugasChart = null;
        }

        if (!hasCharts) {
            showVisitModal(modalEl);
            return;
        }

        const dLabels = month.labels;
        const dValues = month.values.map((v) => Number(v));
        const dctx = detailCanvas.getContext('2d');
        const detailLineGradient = dctx.createLinearGradient(0, 0, detailCanvas.offsetWidth || 600, 0);
        detailLineGradient.addColorStop(0, green);
        detailLineGradient.addColorStop(1, orange);

        visitDetailChart = new Chart(detailCanvas, {
            type: 'line',
            data: {
                labels: dLabels,
                datasets: [{
                    label: 'Kunjungan',
                    data: dValues,
                    borderColor: detailLineGradient,
                    backgroundColor: 'rgba(21, 128, 61, 0.10)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: green,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (_event, elements) {
                    if (!elements.length) return;
                    const entry = currentVisitEntries[elements[0].index];
                    if (entry) {
                        selectVisitDateEntry(entry);
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(5, 46, 22, 0.92)',
                        displayColors: false,
                        callbacks: {
                            label: (c) => ' ' + c.parsed.y + ' kunjungan',
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { size: 11, weight: '600' }
                        },
                        grid: { color: 'rgba(13, 79, 45, 0.08)', drawBorder: false },
                        border: { display: false }
                    },
                    x: {
                        ticks: { color: '#475569', font: { size: 11, weight: '600' }, maxRotation: 0 },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });

        selectVisitDateEntry(currentVisitEntries[0]);
        showVisitModal(modalEl);
    }

    new Chart(canvas, {
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Kunjungan',
                    data: values,
                    backgroundColor: barColors,
                    hoverBackgroundColor: barHoverColors,
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
                    pointRadius: values.map((value, index) => value <= 0 ? 0 : (index === lastIndex ? 7 : 5)),
                    pointHoverRadius: 9,
                    pointBackgroundColor: values.map((value, index) => {
                        if (value <= 0) return 'transparent';
                        return index === lastIndex ? orange : green;
                    }),
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
                    filter: (item) => item.datasetIndex === 0,
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
            onClick: function (_evt, elements) {
                if (!elements?.length) return;
                const barElement = elements.find((el) => el.datasetIndex === 0) ?? elements[0];
                if (barElement.datasetIndex !== 0) return;
                const i = barElement.index;
                openVisitMonthDetail(keys[i], labels[i]);
            },
        },
    });
});
</script>
@endpush
