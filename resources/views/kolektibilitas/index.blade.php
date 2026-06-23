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
                Input saldo piutang per kualitas pinjaman —
                <span class="text-success fw-semibold">PTPN IV Regional 4</span>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                <i class="fa-solid fa-pen-to-square me-1"></i> Input manual kolektibilitas
            </span>
        </div>
    </div>

    <div class="card ptpn-card mb-4">
        <div class="card-header ptpn-card-header fw-bold">
            <i class="fa-solid fa-pen-to-square me-1"></i> Input Saldo Kolektibilitas
        </div>
        <div class="card-body">
            <p class="small text-muted mb-3">
                Pilih tanggal lalu klik <strong>Tampilkan</strong> untuk memuat data saldo tanggal tersebut,
                atau <strong>Hari ini</strong> untuk tanggal sekarang.
                Data tanggal hari ini akan tampil di dashboard depan setelah disimpan.
            </p>

            <form method="post" action="{{ route('kolektibilitas.saldo') }}" id="kolektibilitasSaldoForm">
                @csrf
                @method('PUT')

                <div class="row g-3 align-items-end mb-4 pb-4 border-bottom">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1" for="kolektibilitas_tanggal">Tanggal data</label>
                        <input type="date" name="tanggal" id="kolektibilitas_tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', $tanggal->format('Y-m-d')) }}"
                            max="{{ now()->format('Y-m-d') }}" required>
                        @error('tanggal')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary" id="btnMuatTanggal">Tampilkan</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnHariIni">Hari ini</button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-medium" for="saldo_lancar">Lancar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="saldo_lancar" id="saldo_lancar"
                                class="form-control @error('saldo_lancar') is-invalid @enderror"
                                value="{{ old('saldo_lancar', $saldoForForm['lancar']) }}"
                                min="0" step="1" required>
                        </div>
                        @error('saldo_lancar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-medium" for="saldo_kurang_lancar">Kurang Lancar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="saldo_kurang_lancar" id="saldo_kurang_lancar"
                                class="form-control @error('saldo_kurang_lancar') is-invalid @enderror"
                                value="{{ old('saldo_kurang_lancar', $saldoForForm['kurang_lancar']) }}"
                                min="0" step="1" required>
                        </div>
                        @error('saldo_kurang_lancar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-medium" for="saldo_diragukan">Diragukan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="saldo_diragukan" id="saldo_diragukan"
                                class="form-control @error('saldo_diragukan') is-invalid @enderror"
                                value="{{ old('saldo_diragukan', $saldoForForm['diragukan']) }}"
                                min="0" step="1" required>
                        </div>
                        @error('saldo_diragukan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-medium" for="saldo_macet">Macet</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="saldo_macet" id="saldo_macet"
                                class="form-control @error('saldo_macet') is-invalid @enderror"
                                value="{{ old('saldo_macet', $saldoForForm['macet']) }}"
                                min="0" step="1" required>
                        </div>
                        @error('saldo_macet')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-medium" for="saldo_bermasalah">Bermasalah</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="saldo_bermasalah" id="saldo_bermasalah"
                                class="form-control @error('saldo_bermasalah') is-invalid @enderror"
                                value="{{ old('saldo_bermasalah', $saldoForForm['bermasalah']) }}"
                                min="0" step="1" required>
                        </div>
                        @error('saldo_bermasalah')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card ptpn-card">
        <div class="card-header ptpn-card-header d-flex align-items-center gap-2 px-4 py-3">
            <i class="fa-solid fa-clock-rotate-left text-success"></i>
            <span class="fw-bold">Riwayat Input Kolektibilitas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: linear-gradient(90deg, #CBE1D4 0%, #D7E8DC 50%, #E0DFCC 100%); color: #14532d;">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold">Tanggal</th>
                        <th class="py-3 text-uppercase small fw-bold text-end">Lancar</th>
                        <th class="py-3 text-uppercase small fw-bold text-end">Kurang Lancar</th>
                        <th class="py-3 text-uppercase small fw-bold text-end">Diragukan</th>
                        <th class="py-3 text-uppercase small fw-bold text-end">Macet</th>
                        <th class="py-3 text-uppercase small fw-bold text-end">Bermasalah</th>
                        <th class="text-end pe-4 py-3 text-uppercase small fw-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $item)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $item->tanggal->translatedFormat('d F Y') }}</td>
                            <td class="text-end font-monospace">Rp {{ number_format($item->saldo_lancar, 0, ',', '.') }}</td>
                            <td class="text-end font-monospace">Rp {{ number_format($item->saldo_kurang_lancar, 0, ',', '.') }}</td>
                            <td class="text-end font-monospace">Rp {{ number_format($item->saldo_diragukan, 0, ',', '.') }}</td>
                            <td class="text-end font-monospace">Rp {{ number_format($item->saldo_macet, 0, ',', '.') }}</td>
                            <td class="text-end font-monospace text-danger fw-bold">Rp {{ number_format($item->saldo_bermasalah, 0, ',', '.') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                    <button type="button" class="btn btn-sm btn-white border-0 px-3 edit-kolek-btn" 
                                        data-tanggal="{{ $item->tanggal->format('Y-m-d') }}"
                                        title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square text-success"></i>
                                    </button>
                                    <form action="{{ route('kolektibilitas.destroy', $item) }}" method="post" class="d-inline delete-kolek-form">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-sm btn-white border-0 px-3 border-start delete-kolek-btn" title="Hapus Data">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada riwayat input kolektibilitas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayat->hasPages() || $riwayat->total() > 0)
            <div class="card-footer bg-transparent border-top-0 pt-3 d-flex flex-wrap align-items-center justify-content-between gap-4">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <form method="get" class="d-flex align-items-center gap-3">
                        @foreach(request()->except('per_page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label class="form-label mb-0 small fw-medium text-muted">Rows Per Page</label>
                        <select name="per_page" class="form-select form-select-sm rounded-pill" style="width: auto;" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>ALL</option>
                        </select>
                    </form>
                    <div class="small text-muted">
                        Showing {{ $riwayat->firstItem() }} to {{ $riwayat->lastItem() }} of {{ $riwayat->total() }} results
                    </div>
                </div>
                @if ($riwayat->hasPages())
                    <div class="ms-auto">{{ $riwayat->links('pagination::bootstrap-4') }}</div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tanggalInput = document.getElementById('kolektibilitas_tanggal');
    const indexUrl = @json(route('kolektibilitas.index'));
    const btnMuat = document.getElementById('btnMuatTanggal');
    const btnHariIni = document.getElementById('btnHariIni');

    function goToDate(dateValue) {
        if (!dateValue) return;
        window.location.href = indexUrl + '?tanggal=' + encodeURIComponent(dateValue);
    }

    btnMuat?.addEventListener('click', function () {
        goToDate(tanggalInput?.value);
    });

    btnHariIni?.addEventListener('click', function () {
        window.location.href = indexUrl;
    });

    // Fitur Edit: Muat data ke form
    document.querySelectorAll('.edit-kolek-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tgl = this.getAttribute('data-tanggal');
            goToDate(tgl);
            // Form akan otomatis terisi setelah page reload dengan parameter tanggal
        });
    });

    // Fitur Hapus dengan SweetAlert2
    document.querySelectorAll('.delete-kolek-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.delete-kolek-form');
            Swal.fire({
                title: 'Hapus data kolektibilitas?',
                text: "Data pada tanggal ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    tanggalInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            goToDate(tanggalInput.value);
        }
    });
});
</script>
@endpush
