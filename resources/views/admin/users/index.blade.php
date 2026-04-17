@extends('layouts.app')

@section('title', 'Kelola Petugas')

@section('content')
<div class="container">
    @if (session('delete'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert" style="border-left: 4px solid #dc3545 !important;">
            {{ session('delete') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm" role="alert" style="border-left: 4px solid #dc3545 !important;">
            <div class="fw-semibold mb-1">Periksa input:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-4 align-items-end">
        <div class="col-lg-8">
            <h1 class="ptpn-page-title h2 mb-1">Kelola Petugas</h1>
            <p class="text-muted mb-0">Tambah atau perbarui akun <span class="fw-semibold">petugas penagih</span>.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1 small"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card ptpn-card border-0 shadow-sm">
                <div class="card-header ptpn-card-header d-flex align-items-center gap-2 px-4 py-3">
                    <i class="fa-solid fa-user-plus text-success"></i>
                    <span class="fw-bold">Tambah Petugas</span>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('admin.users.store') }}" class="vstack gap-3">
                        @csrf
                        <div>
                            <label class="form-label small fw-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Nama Lengkap</label>
                            <input name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div>
                            <label class="form-label small fw-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="nama@ptpn.ac.id">
                        </div>
                        <div>
                            <label class="form-label small fw-bold text-muted mb-1 text-uppercase" style="letter-spacing: 0.5px;">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter">
                            <div class="form-text small opacity-75">Gunakan kombinasi huruf dan angka.</div>
                        </div>
                        <div class="d-flex gap-2 pt-2">
                            <button class="btn btn-primary px-4 py-2 shadow-sm rounded-pill" type="submit">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan
                            </button>
                            <a class="btn btn-outline-secondary px-4 py-2 rounded-pill" href="{{ route('admin.users.index') }}">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card ptpn-card border-0 shadow-sm">
                <div class="card-header ptpn-card-header d-flex flex-wrap justify-content-between align-items-center gap-2 px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-users text-success"></i>
                        <span class="fw-bold">Daftar Petugas</span>
                    </div>
                    <form method="get" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                        <div class="input-group input-group-sm">
                            <input name="q" class="form-control border-end-0" placeholder="Cari..." value="{{ $q }}">
                            <button class="btn btn-outline-primary border-start-0" type="submit">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Nama</th>
                                <th class="py-3 text-uppercase small fw-bold text-muted">Email</th>
                                <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center fw-bold border shadow-sm" style="width: 32px; height: 32px;">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <span class="fw-bold text-dark">{{ $u->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="bg-success bg-opacity-10 text-success px-2 py-1 rounded small">{{ $u->email }}</code>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-white border-0 px-3" title="Edit">
                                                <i class="fa-solid fa-user-pen text-primary"></i>
                                            </a>
                                            <form method="post" action="{{ route('admin.users.destroy', $u) }}" class="d-inline delete-user-form">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn" title="Hapus Petugas">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-user-slash fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0">Belum ada petugas terdaftar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="card-footer bg-white border-0">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-user-btn').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('.delete-user-form');
        Swal.fire({
            title: 'Hapus petugas ini?',
            text: "Data petugas dan semua riwayat inputnya akan terhapus!",
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
</script>
@endpush

