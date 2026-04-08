@extends('layouts.app')

@section('title', 'Rekap & Monitoring')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-end">
        <div class="col-lg-8">
            <h1 class="ptpn-page-title h2 mb-1">Rekap &amp; Monitoring</h1>
            <p class="text-muted mb-0">Rekap monitoring penagihan &amp; kinerja petugas — <span class="text-success fw-semibold">PTPN IV Regional 4</span></p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('monitoring.index') }}" class="btn btn-outline-primary btn-sm">Data berita acara</a>
        </div>
    </div>

    <div class="card ptpn-card mb-4">
        <div class="card-header ptpn-card-header fw-bold">Filter data</div>
        <div class="card-body">
            <form method="get" action="{{ route('dashboard.admin') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Dari tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Sampai tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to', $dateTo->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Petugas</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua petugas</option>
                        @foreach ($petugasList as $p)
                            <option value="{{ $p->id }}" @selected((string) request('user_id') === (string) $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Terapkan filter</button>
                    <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
            <p class="small text-muted mb-0 mt-3">
                Periode ringkasan &amp; tabel petugas: <strong>{{ $dateFrom->translatedFormat('d M Y') }}</strong> — <strong>{{ $dateTo->translatedFormat('d M Y') }}</strong>.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card ptpn-card h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="text-muted small fw-semibold">Total data penagihan</div>
                    <div class="h3 mb-0 fw-bold" style="color: var(--ptpn-green-deep);">{{ number_format($totalPenagihan) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card ptpn-card h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="text-muted small fw-semibold">Total mitra binaan <span class="text-muted">(unik)</span></div>
                    <div class="h3 mb-0 fw-bold text-primary">{{ number_format($totalMitra) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card ptpn-card h-100 border-start border-4 border-warning">
                <div class="card-body">
                    <div class="text-muted small fw-semibold">Kunjungan hari ini</div>
                    <div class="h3 mb-0 fw-bold" style="color: var(--ptpn-orange-deep);">{{ number_format($kunjunganHariIni) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card ptpn-card h-100 border-start border-4" style="border-color: #198754 !important;">
                <div class="card-body">
                    <div class="text-muted small fw-semibold">Penagihan bulan ini</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ number_format($penagihanBulanIni) }}</div>
                    <div class="small text-muted">{{ now()->translatedFormat('F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card ptpn-card h-100">
                <div class="card-header ptpn-card-header fw-bold">Jumlah penagihan per bulan</div>
                <div class="card-body" style="min-height: 280px;">
                    <canvas id="chartMonthly" height="240"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card ptpn-card h-100">
                <div class="card-header ptpn-card-header fw-bold">Kunjungan per hari <span class="fw-normal small text-muted">(sesuai filter tanggal)</span></div>
                <div class="card-body" style="min-height: 280px;">
                    <canvas id="chartDaily" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card ptpn-card mb-4">
        <div class="card-header ptpn-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-bold">Rekapan Detail Monitoring</span>
            <span class="small text-muted">Berdasarkan filter yang diterapkan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 3rem;">NO.</th>
                        <th>NAMA MITRA</th>
                        <th>NAMA USAHA</th>
                        <th>NIM</th>
                        <th class="text-end">NILAI PINJAMAN</th>
                        <th class="text-end">SISA PINJAMAN</th>
                        <th class="pe-4">CATATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapDetail as $index => $row)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $index + 1 }}.</td>
                            <td class="fw-bold">{{ $row->nama_mitra }}</td>
                            <td>{{ $row->nama_usaha }}</td>
                            <td><code class="text-success small">{{ $row->nomor_induk }}</code></td>
                            <td class="text-end">Rp {{ number_format($row->nilai_pinjaman, 0, ',', '.') }}</td>
                            <td class="text-end text-danger fw-medium">Rp {{ number_format($row->sisa_pinjaman, 0, ',', '.') }}</td>
                            <td class="pe-4 small text-muted">{{ Str::limit($row->catatan, 50) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Tidak ada data detail untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card ptpn-card mb-4">
        <div class="card-header ptpn-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-bold">Monitoring petugas penagih</span>
            <span class="small text-muted">Diurutkan dari yang paling aktif</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 4rem;">Rank</th>
                        <th>Nama petugas</th>
                        <th class="text-end">Jumlah kunjungan</th>
                        <th class="text-end">Jumlah input data</th>
                        <th class="text-end">Dengan janji pelunasan</th>
                        <th class="text-center">Indikator</th>
                        <th class="pe-4 text-end">Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapPetugas as $row)
                        <tr>
                            <td class="ps-4">
                                @if ($row->rank === 1)
                                    <span class="badge bg-warning text-dark">#1</span>
                                @else
                                    <span class="text-muted">#{{ $row->rank }}</span>
                                @endif
                            </td>
                            <td class="fw-medium">{{ $row->nama_petugas }}</td>
                            <td class="text-end">{{ number_format($row->total_kunjungan) }}</td>
                            <td class="text-end">{{ number_format($row->total_input) }}</td>
                            <td class="text-end">{{ number_format($row->penagihan_berhasil) }}</td>
                            <td class="text-center">
                                @if ($row->activity === 'active')
                                    <span class="badge rounded-pill bg-success">Aktif</span>
                                @else
                                    <span class="badge rounded-pill bg-danger">Kurang aktif</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <span class="d-inline-block rounded-circle" style="width:12px;height:12px;background: {{ $row->activity === 'active' ? '#198754' : '#dc3545' }};" title="{{ $row->activity === 'active' ? 'Di atas rata-rata' : 'Di bawah rata-rata' }}"></span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Tidak ada data pada periode ini.</td>
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
    (function () {
        const green = getComputedStyle(document.documentElement).getPropertyValue('--ptpn-green-deep').trim() || '#14532d';
        const orange = getComputedStyle(document.documentElement).getPropertyValue('--ptpn-orange-deep').trim() || '#c2410c';

        const monthly = @json($chartMonthly);
        const daily = @json($chartDaily);

        new Chart(document.getElementById('chartMonthly'), {
            type: 'line',
            data: {
                labels: monthly.labels,
                datasets: [{
                    label: 'Jumlah penagihan',
                    data: monthly.values,
                    borderColor: green,
                    backgroundColor: 'rgba(20, 83, 45, 0.08)',
                    fill: true,
                    tension: 0.25,
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });

        new Chart(document.getElementById('chartDaily'), {
            type: 'bar',
            data: {
                labels: daily.labels,
                datasets: [{
                    label: 'Kunjungan',
                    data: daily.values,
                    backgroundColor: orange,
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { ticks: { maxRotation: 45, minRotation: 0 } },
                },
            },
        });
    })();
</script>
@endpush
