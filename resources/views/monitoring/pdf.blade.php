<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Berita Acara — {{ $m->nomor_surat }}</title>
    <style>
        @page { margin: 20px 30px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5px;
            color: #111;
            line-height: 1.4;
        }
        .center { text-align: center; }
        .title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 4px;
        }
        .nomor { font-size: 9.5px; margin-bottom: 12px; font-weight: normal; }
        .divider { border-top: 1px solid #000; margin: 10px 0; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.meta td { vertical-align: top; padding: 4px 6px; border: 1px solid #333; }
        table.meta td.k { width: 30%; font-weight: bold; background: #f8f8f8; }
        table.sig { width: 100%; margin-top: 15px; border-collapse: collapse; }
        table.sig td { width: 50%; vertical-align: top; text-align: center; padding: 5px; }
        .sig-img { max-height: 55px; max-width: 150px; margin: 2px auto; }
        .sig-line { margin-top: 15px; border-top: 0.5px solid #000; width: 80%; margin-left: auto; margin-right: auto; padding-top: 4px; font-size: 9px; }
        .small { font-size: 8.5px; color: #444; }
        .footer-note { margin-top: 15px; font-style: italic; opacity: 0.8; }
        
        /* Lampiran Section */
        .lampiran-block {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
        }
        .lampiran-title {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 8px;
            color: #333;
        }
        table.lampiran-grid { width: 100%; border-collapse: collapse; }
        td.lampiran-col-foto { width: 50%; padding-right: 15px; text-align: center; }
        td.lampiran-col-geo { width: 50%; vertical-align: top; }
        .lampiran-foto-wrap img {
            max-width: 100%;
            max-height: 245px;
            border: 1px solid #ddd;
            border-radius: 4px;
            object-fit: cover;
        }
        .geo-card {
            border-radius: 8px;
            border: 1px solid #e0f2e5;
            background: #f2fbf5;
            padding: 8px 12px;
            font-size: 8px;
        }
        .geo-card-title {
            font-weight: bold;
            margin-bottom: 6px;
            color: #2e7d32;
            font-size: 9px;
            border-bottom: 1px solid #e0f2e5;
            padding-bottom: 4px;
        }
        table.geo { width: 100%; border-collapse: collapse; }
        table.geo td { vertical-align: top; padding: 2px 0; }
        table.geo td.k { width: 70px; font-weight: bold; color: #555; }
        table.geo td.v { color: #111; }
        .geo-map-wrap { margin-top: 10px; border: 1px solid #eee; border-radius: 4px; overflow: hidden; }
        .geo-map-wrap img { width: 100%; max-height: 120px; display: block; object-fit: cover; }
    </style>
</head>
<body>
    <div class="center">
        <img src="{{ public_path('images/logo.jpg') }}" alt="Logo PTPN IV Regional 4" style="max-width: 70px; height: auto;">
        <div class="title">Berita Acara Kunjungan Penagihan Mitra Binaan</div>
        <div class="nomor">Nomor: <strong>{{ $m->nomor_surat }}</strong></div>
    </div>

    <div class="divider"></div>

    <p style="margin-bottom: 10px;">Pada hari ini, tanggal <strong>{{ $m->tanggal->translatedFormat('d F Y') }}</strong>, telah dilaksanakan kunjungan penagihan dengan rincian sebagai berikut:</p>

    <table class="meta">
        <tr>
            <td class="k">Nama mitra</td>
            <td>{{ $m->nama_mitra }}</td>
        </tr>
        <tr>
            <td class="k">Nama usaha</td>
            <td>{{ $m->nama_usaha }}</td>
        </tr>
        <tr>
            <td class="k">Nomor induk mitra</td>
            <td>{{ $m->nomor_induk }}</td>
        </tr>
        <tr>
            <td class="k">Alamat</td>
            <td>{{ $m->alamat }}</td>
        </tr>
        <tr>
            <td class="k">No. HP</td>
            <td>{{ $m->no_hp }}</td>
        </tr>
        <tr>
            <td class="k">Nilai pinjaman</td>
            <td>Rp {{ number_format($m->nilai_pinjaman, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="k">Sisa pinjaman</td>
            <td>Rp {{ number_format($m->sisa_pinjaman, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="k">Alasan tidak membayar</td>
            <td>{{ $m->alasan ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">Janji pelunasan</td>
            <td>{{ $m->janji ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">Kebutuhan saat ini</td>
            <td>{{ $m->kebutuhan ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">Catatan</td>
            <td>{{ $m->catatan ?: '—' }}</td>
        </tr>
    </table>

    <table class="sig">
        <tr>
            <td>
                @if ($mitraSig)
                    <img class="sig-img" src="{{ $mitraSig }}" alt="TTD mitra">
                @endif
                <div class="sig-line">
                    Mitra binaan<br>
                    <strong>{{ $m->nama_mitra }}</strong>
                </div>
            </td>
            <td>
                @if ($petugasSig)
                    <img class="sig-img" src="{{ $petugasSig }}" alt="TTD petugas">
                @endif
                <div class="sig-line">
                    Petugas penagih<br>
                    <strong>{{ $m->user?->name ?? '________________' }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note small">
        Dokumen ini dibuat secara elektronik melalui sistem Monitoring &amp; Penagihan Mitra Binaaan Regional 4.<br>
        Dicetak pada {{ now()->translatedFormat('d F Y') }}.
    </div>

    @if ($fotoSig)
        <div class="lampiran-block">
            <div class="lampiran-title">Lampiran Bukti Kunjungan</div>

            <table class="lampiran-grid">
                <tr>
                    <td class="lampiran-col-foto">
                        <div class="lampiran-foto-wrap">
                            <img src="{{ $fotoSig }}" alt="Foto lampiran kunjungan">
                            <div style="font-size: 7px; margin-top: 4px; color: #666;">Foto Dokumentasi Kunjungan</div>
                        </div>
                    </td>
                    <td class="lampiran-col-geo">
                        @if ($m->latitude || $m->longitude || $m->geo_jalan || $m->geo_kelurahan || $m->geo_kecamatan || $m->geo_kota || $m->geo_provinsi || $m->geo_kode_pos)
                            <div class="geo-card">
                                <div class="geo-card-title">Informasi Lokasi (Geotag)</div>
                                <table class="geo">
                                    @if ($m->latitude || $m->longitude)
                                        <tr>
                                            <td class="k">Koordinat</td>
                                            <td class="v">: {{ $m->latitude }}, {{ $m->longitude }}</td>
                                        </tr>
                                    @endif
                                    @if ($m->geo_jalan)
                                        <tr>
                                            <td class="k">Alamat</td>
                                            <td class="v">: {{ $m->geo_jalan }}</td>
                                        </tr>
                                    @endif
                                    @if ($m->geo_kelurahan)
                                        <tr>
                                            <td class="k">Kelurahan</td>
                                            <td class="v">: {{ $m->geo_kelurahan }}</td>
                                        </tr>
                                    @endif
                                    @if ($m->geo_kecamatan)
                                        <tr>
                                            <td class="k">Kecamatan</td>
                                            <td class="v">: {{ $m->geo_kecamatan }}</td>
                                        </tr>
                                    @endif
                                    @if ($m->geo_kota)
                                        <tr>
                                            <td class="k">Kota/Kab.</td>
                                            <td class="v">: {{ $m->geo_kota }}</td>
                                        </tr>
                                    @endif
                                </table>
                                
                                @if ($mapSig)
                                    <div class="geo-map-wrap">
                                        <img src="{{ $mapSig }}" alt="Peta Lokasi">
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif
</body>
</html>
