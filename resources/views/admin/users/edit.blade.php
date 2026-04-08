@extends('layouts.app')

@section('title', 'Edit Petugas')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-end">
        <div class="col-lg-8">
            <h1 class="ptpn-page-title h2 mb-1">Edit Petugas</h1>
            <p class="text-muted mb-0">Perbarui informasi akun <span class="fw-semibold">petugas penagih</span>.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card ptpn-card">
                <div class="card-header ptpn-card-header fw-bold">Data Petugas</div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.users.update', $user) }}" class="vstack gap-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="form-label small text-muted mb-1">Nama</label>
                            <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div>
                            <label class="form-label small text-muted mb-1">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div>
                            <label class="form-label small text-muted mb-1">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control" minlength="8">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter.</div>
                        </div>
                        <div class="d-flex gap-2 pt-2">
                            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
