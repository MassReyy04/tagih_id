@php
    $geo = $m ?? null;
@endphp
<p class="small text-muted mb-2">Koordinat dan rincian alamat di bawah terisi otomatis setelah tombol GPS (reverse geocoding).</p>
<div class="row g-2 mb-2">
    <div class="col-md-6">
        <label class="form-label small mb-0" for="latitude">Lintang</label>
        <input type="text" class="form-control form-control-sm bg-light" name="latitude" id="latitude" readonly
               value="{{ old('latitude', $geo?->latitude ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small mb-0" for="longitude">Bujur</label>
        <input type="text" class="form-control form-control-sm bg-light" name="longitude" id="longitude" readonly
               value="{{ old('longitude', $geo?->longitude ?? '') }}">
    </div>
</div>
<div class="row g-2 small">
    <div class="col-md-6">
        <label class="form-label small mb-0" for="geo_jalan">Jalan</label>
        <input type="text" class="form-control form-control-sm bg-light" name="geo_jalan" id="geo_jalan" readonly
               value="{{ old('geo_jalan', $geo?->geo_jalan ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small mb-0" for="geo_kelurahan">Kelurahan</label>
        <input type="text" class="form-control form-control-sm bg-light" name="geo_kelurahan" id="geo_kelurahan" readonly
               value="{{ old('geo_kelurahan', $geo?->geo_kelurahan ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small mb-0" for="geo_kecamatan">Kecamatan</label>
        <input type="text" class="form-control form-control-sm bg-light" name="geo_kecamatan" id="geo_kecamatan" readonly
               value="{{ old('geo_kecamatan', $geo?->geo_kecamatan ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small mb-0" for="geo_kota">Kota / Kabupaten</label>
        <input type="text" class="form-control form-control-sm bg-light" name="geo_kota" id="geo_kota" readonly
               value="{{ old('geo_kota', $geo?->geo_kota ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small mb-0" for="geo_provinsi">Provinsi</label>
        <input type="text" class="form-control form-control-sm bg-light" name="geo_provinsi" id="geo_provinsi" readonly
               value="{{ old('geo_provinsi', $geo?->geo_provinsi ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small mb-0" for="geo_kode_pos">Kode pos</label>
        <input type="text" class="form-control form-control-sm bg-light" name="geo_kode_pos" id="geo_kode_pos" readonly
               value="{{ old('geo_kode_pos', $geo?->geo_kode_pos ?? '') }}">
    </div>
</div>
