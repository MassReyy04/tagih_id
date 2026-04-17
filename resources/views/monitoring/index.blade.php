@extends('layouts.app')

@section('title', 'Data Berita Acara')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="ptpn-page-title h2 mb-1">Data Berita Acara</h1>
            <p class="text-muted mb-0">Riwayat monitoring &amp; penagihan mitra binaan</p>
        </div>
        <a href="{{ route('monitoring.create') }}" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
            <i class="fa-solid fa-circle-plus me-1"></i> Input Berita Acara
        </a>
    </div>

    @if (session('delete'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show px-4 py-3" style="border-left:5px solid #dc3545!important; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-trash-can fa-lg text-danger"></i>
                <div class="fw-semibold text-dark">{{ session('delete') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="get" class="card ptpn-card border-0 shadow-sm p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-12 col-lg-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass small"></i>
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Cari mitra, usaha, nomor surat…">
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ request('kecamatan') }}" class="form-control" placeholder="Semua Kecamatan">
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Kelurahan</label>
                <input type="text" name="kelurahan" value="{{ request('kelurahan') }}" class="form-control" placeholder="Semua Kelurahan">
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-6 col-lg-2">
                <label class="form-label small fw-bold text-muted text-uppercase">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-12 text-end mt-3">
                <a href="{{ route('monitoring.index') }}" class="btn btn-light px-4 rounded-pill me-2">Reset</a>
                <button type="submit" class="btn btn-primary px-4 rounded-pill">
                    <i class="fa-solid fa-filter me-1 small"></i> Terapkan Filter
                </button>
            </div>
        </div>
    </form>

    <div class="card ptpn-card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Nomor Surat</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Mitra & Usaha</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Petugas</th>
                        <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $row)
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
                                <div class="text-dark fw-medium">{{ $row->tanggal->translatedFormat('d F Y') }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center fw-bold border shadow-sm" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                        {{ substr($row->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-muted small">{{ $row->user?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                    <a href="{{ route('monitoring.show', $row) }}" class="btn btn-sm btn-white border-0 px-3" title="Detail">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('monitoring.edit', $row) }}" class="btn btn-sm btn-white border-0 px-3 border-start" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-success"></i>
                                    </a>
                                     @if (auth()->user()->isAdmin())
                                        <form action="{{ route('monitoring.destroy', $row) }}" method="post" class="d-inline delete-form">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Tidak ditemukan data monitoring.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="card-footer bg-transparent border-top-0 pt-0">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('.delete-form');
        Swal.fire({
            title: 'Hapus data ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            didOpen: () => {
                if (typeof playNotificationSound === 'function') {
                    // Opsional: putar suara peringatan jika ada
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
