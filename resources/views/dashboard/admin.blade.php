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
            <div class="card ptpn-card h-100 border-start border-4 border-primary cursor-pointer transition-hover"
                 data-bs-toggle="modal" data-bs-target="#modalMitraUnik" role="button"
                 data-date-from="{{ $dateFrom->toDateString() }}"
                 data-date-to="{{ $dateTo->toDateString() }}"
                 data-user-id="{{ $userId ?? '' }}"
                 style="transition: transform .18s ease, box-shadow .18s ease;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 25px -8px rgba(13,79,45,.25)';"
                 onmouseout="this.style.transform='';this.style.boxShadow='';">
                <div class="card-body">
                    <div class="text-muted small fw-semibold">Total mitra binaan <span class="text-muted">(unik)</span>
                        <i class="fa-solid fa-circle-info ms-1 text-primary opacity-75" title="Klik untuk melihat daftar mitra lengkap beserta kecamatan"></i>
                    </div>
                    <div class="h3 mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                        {{ number_format($totalMitra) }}
                        <i class="fa-solid fa-arrow-up-right-from-square text-primary opacity-75" style="font-size:1rem;"></i>
                    </div>
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
        <div class="card-header ptpn-card-header fw-bold">Rekapan Detail Monitoring</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
                    <tr>
                        <th class="ps-4" style="width: 3rem;">NO.</th>
                        <th>NAMA MITRA</th>
                        <th>NAMA USAHA</th>
                        <th>NIM</th>
                        <th class="text-end">NILAI PINJAMAN</th>
                        <th class="text-end">SISA PINJAMAN</th>
                        <th>CATATAN</th>
                        <th class="pe-4 text-center" style="width: 10rem;">AKSI</th>
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
                            <td class="small text-muted">{{ Str::limit($row->catatan, 50) }}</td>
                            <td class="pe-4 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('monitoring.edit', $row->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('monitoring.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data mitra {{ addslashes($row->nama_mitra) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
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
                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
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

<!-- Modal Daftar Mitra Binaan Unik -->
<div class="modal fade" id="modalMitraUnik" tabindex="-1" aria-labelledby="modalMitraUnikLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem;">
            <div class="modal-header" style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); border-radius: 1rem 1rem 0 0;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalMitraUnikLabel">
                        <i class="fa-solid fa-users text-success me-2"></i>Daftar Mitra Binaan Unik
                    </h5>
                    <div class="small text-muted mt-1" id="modalMitraUnikPeriode">-</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        <i class="fa-solid fa-user-check me-1"></i>
                        Total: <span id="modalMitraUnikTotal" class="fw-bold">0</span> mitra
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="px-4 py-3 border-bottom bg-light bg-opacity-50">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass small"></i>
                                </span>
                                <input type="search" id="modalMitraUnikSearch"
                                       class="form-control border-start-0 ps-0"
                                       placeholder="Cari nama mitra, NIM, usaha, kecamatan, kelurahan…">
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end small text-muted">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Data sesuai filter tanggal &amp; petugas pada halaman rekap
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="modalMitraUnikTableWrap" style="max-height: 55vh;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;" class="sticky-top">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold" style="width: 3.5rem;">No</th>
                                <th class="py-3 text-uppercase small fw-bold">Nama Mitra</th>
                                <th class="py-3 text-uppercase small fw-bold">NIM</th>
                                <th class="py-3 text-uppercase small fw-bold">Nama Usaha</th>
                                <th class="py-3 text-uppercase small fw-bold">Kelurahan / Kecamatan</th>
                                <th class="text-end pe-4 py-3 text-uppercase small fw-bold">Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody id="modalMitraUnikBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <div class="spinner-border text-success spinner-border-sm me-2" role="status"></div>
                                    Memuat data mitra…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light bg-opacity-50 rounded-bottom border-top-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
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

    // ============================================================
    // Modal Daftar Mitra Binaan Unik + Live Search via AJAX
    // ============================================================
    (function () {
        const modalEl = document.getElementById('modalMitraUnik');
        if (!modalEl) return;

        const endpoint = @json(route('dashboard.admin.mitra-unik'));
        const tbody = document.getElementById('modalMitraUnikBody');
        const searchInput = document.getElementById('modalMitraUnikSearch');
        const periodeEl = document.getElementById('modalMitraUnikPeriode');
        const totalEl = document.getElementById('modalMitraUnikTotal');

        let currentFilters = { date_from: '', date_to: '', user_id: '' };
        let searchTimer = null;
        let currentAbort = null;

        function setLoadingState() {
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">'
                + '<div class="spinner-border text-success spinner-border-sm me-2" role="status"></div>'
                + 'Memuat data mitra…</td></tr>';
        }

        async function loadMitraUnik(search = '') {
            if (!tbody || !totalEl || !periodeEl) return;

            if (currentAbort) currentAbort.abort();
            const ctrl = new AbortController();
            currentAbort = ctrl;

            const params = new URLSearchParams();
            if (currentFilters.date_from) params.set('date_from', currentFilters.date_from);
            if (currentFilters.date_to) params.set('date_to', currentFilters.date_to);
            if (currentFilters.user_id) params.set('user_id', currentFilters.user_id);
            if (search.trim()) params.set('q', search.trim());

            try {
                setLoadingState();
                const res = await fetch(endpoint + '?' + params.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    signal: ctrl.signal,
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const json = await res.json();
                if (!ctrl.signal.aborted) {
                    periodeEl.textContent = 'Periode: ' + (json.periode || '-');
                    totalEl.textContent = json.total ?? 0;
                    tbody.innerHTML = json.html || '';
                }
            } catch (e) {
                if (e && e.name === 'AbortError') return;
                if (!ctrl.signal.aborted) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-5">'
                        + '<i class="fa-solid fa-triangle-exclamation me-2"></i>'
                        + 'Gagal memuat data: ' + (e && e.message ? e.message : 'terjadi kesalahan')
                        + '</td></tr>';
                }
            }
        }

        // Buka modal: ambil filter dari card yang diklik
        modalEl.addEventListener('show.bs.modal', function (evt) {
            const opener = evt && evt.relatedTarget ? evt.relatedTarget : null;
            if (opener && opener.getAttribute) {
                currentFilters = {
                    date_from: opener.getAttribute('data-date-from') || currentFilters.date_from,
                    date_to: opener.getAttribute('data-date-to') || currentFilters.date_to,
                    user_id: opener.getAttribute('data-user-id') || currentFilters.user_id,
                };
            }
            if (searchInput) searchInput.value = '';
            loadMitraUnik('');
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            if (currentAbort) { currentAbort.abort(); currentAbort = null; }
        });

        // Live search dengan debounce 350ms
        searchInput && searchInput.addEventListener('input', function () {
            const val = this.value;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadMitraUnik(val), 350);
        });

        // Enter langsung cari tanpa debounce
        searchInput && searchInput.addEventListener('keydown', function (e) {
            if (e && e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimer);
                loadMitraUnik(this.value);
            }
        });
    })();
</script>
@endpush
