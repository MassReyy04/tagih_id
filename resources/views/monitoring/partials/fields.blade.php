@php
    $model = $m ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nama_mitra">Nama mitra <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('nama_mitra') is-invalid @enderror" id="nama_mitra" name="nama_mitra"
               value="{{ old('nama_mitra', $model->nama_mitra ?? '') }}" required>
        @error('nama_mitra')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="nama_usaha">Nama usaha <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('nama_usaha') is-invalid @enderror" id="nama_usaha" name="nama_usaha"
               value="{{ old('nama_usaha', $model->nama_usaha ?? '') }}" required>
        @error('nama_usaha')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="nomor_induk">Nomor induk mitra <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('nomor_induk') is-invalid @enderror" id="nomor_induk" name="nomor_induk"
               value="{{ old('nomor_induk', $model->nomor_induk ?? '') }}" required>
        @error('nomor_induk')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="no_hp">No. HP <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp"
               value="{{ old('no_hp', $model->no_hp ?? '') }}" required>
        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="alamat">Alamat <span class="text-danger">*</span></label>
        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="2" required>{{ old('alamat', $model->alamat ?? '') }}</textarea>
        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="nilai_pinjaman">Nilai pinjaman <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" class="form-control @error('nilai_pinjaman') is-invalid @enderror" id="nilai_pinjaman" name="nilai_pinjaman"
               value="{{ old('nilai_pinjaman', $model->nilai_pinjaman ?? '') }}" required>
        @error('nilai_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="sisa_pinjaman">Sisa pinjaman <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" class="form-control @error('sisa_pinjaman') is-invalid @enderror" id="sisa_pinjaman" name="sisa_pinjaman"
               value="{{ old('sisa_pinjaman', $model->sisa_pinjaman ?? '') }}" required>
        @error('sisa_pinjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tanggal">Tanggal kunjungan <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal"
               value="{{ old('tanggal', isset($model) && $model->tanggal ? $model->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Nomor surat BAM di-generate otomatis (urutan per hari pada tanggal ini: format BAM nn/tanggal/bulan(Romawi)/tahun).</div>
    </div>
    <div class="col-12">
        <label class="form-label" for="alasan">Alasan tidak membayar</label>
        <textarea class="form-control @error('alasan') is-invalid @enderror" id="alasan" name="alasan" rows="2">{{ old('alasan', $model->alasan ?? '') }}</textarea>
        @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="janji">Janji pelunasan</label>
        <input type="text" class="form-control @error('janji') is-invalid @enderror" id="janji" name="janji"
               value="{{ old('janji', $model->janji ?? '') }}">
        @error('janji')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="kebutuhan">Kebutuhan saat ini</label>
        <input type="text" class="form-control @error('kebutuhan') is-invalid @enderror" id="kebutuhan" name="kebutuhan"
               value="{{ old('kebutuhan', $model->kebutuhan ?? '') }}">
        @error('kebutuhan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="catatan">Catatan</label>
        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="2">{{ old('catatan', $model->catatan ?? '') }}</textarea>
        @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
