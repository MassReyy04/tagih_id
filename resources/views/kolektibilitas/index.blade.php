@extends('layouts.app')

@section('title', 'Kinerja Kolektibilitas')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4 align-items-end">
        <div class="col-lg-8">
            <h1 class="ptpn-page-title h2 mb-1">Kinerja Kolektibilitas</h1>
            <p class="text-muted mb-0">
                Laporan agregat saldo piutang berdasarkan kualitas pinjaman —
                <span class="text-success fw-semibold">PTPN IV Regional 4</span>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                <i class="fa-solid fa-rotate me-1"></i> Data diperbarui otomatis setiap hari
            </span>
        </div>
    </div>

    <div class="card ptpn-card mb-4">
        <div class="card-header ptpn-card-header fw-bold">Filter periode</div>
        <div class="card-body">
            <form method="get" action="{{ route('kolektibilitas.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Tanggal data</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-8 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('kolektibilitas.index') }}" class="btn btn-outline-secondary">Hari ini</a>
                </div>
            </form>
            <p class="small text-muted mb-0 mt-3">
                Data per <strong>{{ $tanggal->translatedFormat('d F Y') }}</strong>
                · {{ number_format($report['mitra_count']) }} mitra dengan sisa pinjaman &gt; 0
            </p>
        </div>
    </div>

    <div class="card ptpn-card mb-4">
        <div class="card-header ptpn-card-header fw-bold text-center">Kinerja Kolektibilitas {{ $report['tahun'] }}</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kualitas Pinjaman</th>
                        <th>Umur Piutang</th>
                        <th class="text-end">Saldo Piutang</th>
                        <th class="text-center" style="width: 7rem;">%</th>
                        <th class="text-end pe-4" style="width: 12rem;">Perkalian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report['rows'] as $row)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $row['label'] }}</td>
                            <td class="text-muted">{{ $row['umur'] }}</td>
                            <td class="text-end">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $row['bobot_label'] }}</td>
                            <td class="text-end pe-4">
                                @if ($row['perkalian'] > 0)
                                    Rp {{ number_format($row['perkalian'], 0, ',', '.') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-light">
                        <td class="ps-4 fw-bold">Jumlah</td>
                        <td></td>
                        <td class="text-end fw-bold">Rp {{ number_format($report['jumlah_saldo'], 0, ',', '.') }}</td>
                        <td></td>
                        <td class="text-end fw-bold pe-4">Rp {{ number_format($report['jumlah_perkalian'], 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-4 fw-bold">Bermasalah</td>
                        <td class="text-muted">—</td>
                        <td class="text-end fw-bold">Rp {{ number_format($report['saldo_bermasalah'], 0, ',', '.') }}</td>
                        <td class="text-muted text-center">—</td>
                        <td class="text-muted pe-4">—</td>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-4 fw-bold">Total</td>
                        <td></td>
                        <td class="text-end fw-bold">Rp {{ number_format($report['total_saldo'], 0, ',', '.') }}</td>
                        <td></td>
                        <td class="text-end fw-bold pe-4">Rp {{ number_format($report['total_perkalian'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="ptpn-kolektibilitas-hitung">
            <div class="ptpn-kolektibilitas-hitung__title text-center fw-bold py-2">
                Tahun {{ $report['tahun'] }}
            </div>
            <div class="p-3 p-md-4">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-4">
                        <table class="table table-sm table-borderless mb-0 ptpn-kolektibilitas-hitung__labels">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Tingkat Kolektibilitas</td>
                                    <td class="text-end font-monospace">{{ number_format($report['jumlah_perkalian'], 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Outstanding Pinjaman</td>
                                    <td class="text-end font-monospace">{{ number_format($report['jumlah_saldo'], 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-5 text-center">
                        <div class="ptpn-kolektibilitas-hitung__formula mx-auto">
                            <div class="font-monospace">{{ number_format($report['jumlah_perkalian'], 0, ',', '.') }}</div>
                            <hr class="my-1 border-dark opacity-75">
                            <div class="font-monospace">{{ number_format($report['jumlah_saldo'], 0, ',', '.') }}</div>
                        </div>
                        <div class="mt-2 fw-semibold">× 100%</div>
                    </div>
                    <div class="col-lg-3">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Nilai</td>
                                    <td class="text-end fw-bold fs-5">
                                        @if ($report['nilai'] !== null)
                                            {{ number_format($report['nilai'], 2, ',', '.') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Skor</td>
                                    <td class="text-end">
                                        @if ($report['skor'] !== null)
                                            <span class="fw-bold fs-5">{{ $report['skor'] }}</span>
                                            <span class="d-block small text-muted">{{ $report['skor_label'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card ptpn-card h-100">
                <div class="card-header ptpn-card-header fw-bold">Input saldo bermasalah</div>
                <div class="card-body">
                    <p class="small text-muted">
                        Saldo piutang bermasalah di luar klasifikasi Lancar–Macet. Diisi manual oleh admin dan disimpan per tanggal.
                    </p>
                    <form method="post" action="{{ route('kolektibilitas.bermasalah') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}">
                        <div class="mb-3">
                            <label class="form-label" for="saldo_bermasalah">Saldo bermasalah (Rp)</label>
                            <input type="number" step="1" min="0" class="form-control @error('saldo_bermasalah') is-invalid @enderror"
                                   id="saldo_bermasalah" name="saldo_bermasalah"
                                   value="{{ old('saldo_bermasalah', (int) $report['saldo_bermasalah']) }}" required>
                            @error('saldo_bermasalah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan saldo bermasalah</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card ptpn-card h-100">
                <div class="card-header ptpn-card-header fw-bold">Riwayat snapshot harian</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th class="text-end">Total saldo piutang</th>
                                <th class="text-end pe-4">Nilai perkalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($snapshots as $snap)
                                @php
                                    $totalSaldo = (float) $snap->saldo_lancar
                                        + (float) $snap->saldo_kurang_lancar
                                        + (float) $snap->saldo_diragukan
                                        + (float) $snap->saldo_macet
                                        + (float) $snap->saldo_bermasalah;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('kolektibilitas.index', ['tanggal' => $snap->tanggal->format('Y-m-d')]) }}" class="text-decoration-none">
                                            {{ $snap->tanggal->translatedFormat('d M Y') }}
                                        </a>
                                    </td>
                                    <td class="text-end font-monospace">{{ number_format($totalSaldo, 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace pe-4">{{ number_format($snap->nilai_perkalian_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada snapshot tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card ptpn-card border-0 bg-light">
        <div class="card-body small text-muted">
            <strong class="text-dark">Cara perhitungan:</strong>
            Saldo piutang diambil dari data kunjungan terbaru per mitra (berdasarkan nomor induk).
            Klasifikasi mengikuti <em>hari tunggakan</em> yang diinput saat berita acara.
            Nilai perkalian = Saldo × bobot (Lancar 100%, Kurang Lancar 75%, Diragukan 25%, Macet 0%).
            Tingkat kolektibilitas = Jumlah perkalian ÷ Jumlah saldo × 100%.
            Skor: &gt; 80 = 4, &gt; 70 = 3, &gt; 60 = 2, selain itu = 1.
        </div>
    </div>
</div>
@endsection
