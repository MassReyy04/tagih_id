@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="ptpn-page-title h3 mb-4">Profil saya</h1>

            <div class="card ptpn-card shadow-sm border-0 mb-4">
                <div class="card-header ptpn-card-header fw-bold">Update informasi profil</div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" id="profileForm">
                        @csrf
                        @method('patch')

                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <div class="position-relative">
                                    @if($user->profile_photo)
                                        <img src="{{ $user->profilePhotoUrl() }}" alt="Profile Photo" class="rounded-circle border shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border shadow-sm" style="width: 80px; height: 80px;">
                                            <i class="fa-solid fa-user fa-2x text-muted opacity-50"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <label for="profile_photo" class="form-label fw-semibold small mb-1">Foto profil</label>
                                <div class="d-flex gap-2">
                                    <input type="file" name="profile_photo" id="profile_photo" class="form-control form-control-sm @error('profile_photo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                                    @if($user->profile_photo)
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-photo-btn" title="Hapus foto">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="form-text x-small">JPG, PNG, WEBP (maks. 10MB).</div>
                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold small mb-1">Nama lengkap</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold small mb-1">Alamat email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="fw-bold mb-2 small">Ganti password (opsional)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold small mb-1">Password baru</label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold small mb-1">Konfirmasi</label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success px-5 fw-bold" id="saveBtn">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan perubahan
                            </button>
                        </div>
                    </form>

                    @if($user->profile_photo)
                        <form id="delete-photo-form" action="{{ route('profile.photo.destroy') }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>

            <div class="card border-danger shadow-sm border-0">
                <div class="card-header bg-danger text-white fw-bold border-0">Informasi akun</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4 text-muted">Role</div>
                        <div class="col-sm-8 fw-bold">
                            <span class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4 text-muted">Akun dibuat</div>
                        <div class="col-sm-8 small">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

const deleteBtn = document.querySelector('.delete-photo-btn');
if (deleteBtn) {
    deleteBtn.addEventListener('click', function() {
        Swal.fire({
            title: 'Hapus foto profil?',
            text: "Foto akan dihapus secara permanen dan kembali ke inisial nama.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-photo-form').submit();
            }
        });
    });
}
</script>
@endpush
