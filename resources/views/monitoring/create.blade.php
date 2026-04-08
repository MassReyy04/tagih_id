@extends('layouts.app')

@section('title', 'Input Berita Acara')

@section('content')
<div class="container">
    <h1 class="ptpn-page-title h3 mb-2">Input berita acara kunjungan</h1>
    <p class="text-muted mb-4">Lengkapi formulir, ambil lokasi (izinkan akses GPS di perangkat), lalu tanda tangan di kedua kolom.</p>

    <form action="{{ route('monitoring.store') }}" method="post" enctype="multipart/form-data" id="formBam" class="card ptpn-card">
        @csrf
        <div class="card-body">
            @include('monitoring.partials.fields', ['m' => null])

            <hr class="my-4">

            <h2 class="h6">Foto &amp; geotagging</h2>
            <p class="small text-muted">Unggah foto dari perangkat. Koordinat diisi otomatis dari Geolocation API (HTTPS disarankan).</p>
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label" for="foto">Foto lokasi / kunjungan</label>
                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                    @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-end">
                    <button type="button" class="btn btn-warning text-dark fw-semibold" id="btnGeo">📍 Ambil koordinat GPS</button>
                    <div class="small mt-2" id="geoStatus">Belum ada koordinat.</div>
                </div>
            </div>
            @include('monitoring.partials.geo-address-fields', ['m' => null])

            <hr class="my-4">

            @include('monitoring.partials.signature-pad-fields')
        </div>
        <div class="card-footer ptpn-card-header d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('monitoring.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const geocodeUrl = @json(route('geocode.reverse'));
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const geoStatus = document.getElementById('geoStatus');
    const geoFields = {
        jalan: document.getElementById('geo_jalan'),
        kelurahan: document.getElementById('geo_kelurahan'),
        kecamatan: document.getElementById('geo_kecamatan'),
        kota: document.getElementById('geo_kota'),
        provinsi: document.getElementById('geo_provinsi'),
        kodePos: document.getElementById('geo_kode_pos'),
    };

    function applyGeoPayload(data) {
        geoFields.jalan.value = data.geo_jalan || '';
        geoFields.kelurahan.value = data.geo_kelurahan || '';
        geoFields.kecamatan.value = data.geo_kecamatan || '';
        geoFields.kota.value = data.geo_kota || '';
        geoFields.provinsi.value = data.geo_provinsi || '';
        geoFields.kodePos.value = data.geo_kode_pos || '';
    }

    function clearGeoAddressFields() {
        Object.values(geoFields).forEach(function (el) { if (el) el.value = ''; });
    }

    document.getElementById('btnGeo').addEventListener('click', function () {
        if (!navigator.geolocation) {
            geoStatus.textContent = 'Browser tidak mendukung geolocation.';
            return;
        }
        geoStatus.textContent = 'Mengambil lokasi…';
        navigator.geolocation.getCurrentPosition(
            async function (pos) {
                const lat = pos.coords.latitude.toFixed(7);
                const lng = pos.coords.longitude.toFixed(7);
                latInput.value = lat;
                lngInput.value = lng;
                geoStatus.textContent = 'Koordinat tersimpan, mengambil detail alamat…';

                try {
                    const url = new URL(geocodeUrl, window.location.origin);
                    url.searchParams.set('lat', lat);
                    url.searchParams.set('lng', lng);
                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    const payload = await response.json();
                    applyGeoPayload(payload);
                    geoStatus.textContent = 'Lokasi & alamat tersimpan: ' + lat + ', ' + lng;
                } catch (error) {
                    clearGeoAddressFields();
                    geoStatus.textContent = 'Koordinat tersimpan; alamat gagal diambil (simpan tetap, server akan coba isi ulang).';
                }
            },
            function (err) {
                geoStatus.textContent = 'Gagal: ' + (err.message || 'izin ditolak atau GPS tidak tersedia');
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });
})();
</script>
@include('monitoring.partials.signature-pad-script', ['formId' => 'formBam', 'requireBothSignatures' => true])
@endpush
