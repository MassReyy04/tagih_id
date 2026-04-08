<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringPenagihan extends Model
{
    protected $table = 'monitoring_penagihan';

    protected $fillable = [
        'nomor_surat',
        'nama_mitra',
        'nama_usaha',
        'nomor_induk',
        'alamat',
        'no_hp',
        'nilai_pinjaman',
        'sisa_pinjaman',
        'alasan',
        'janji',
        'catatan',
        'kebutuhan',
        'tanggal',
        'signature_mitra',
        'signature_petugas',
        'foto',
        'latitude',
        'longitude',
        'geo_jalan',
        'geo_kelurahan',
        'geo_kecamatan',
        'geo_kota',
        'geo_provinsi',
        'geo_kode_pos',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nilai_pinjaman' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fotoUrl(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    public function signatureMitraUrl(): ?string
    {
        return $this->signature_mitra ? asset('storage/'.$this->signature_mitra) : null;
    }

    public function signaturePetugasUrl(): ?string
    {
        return $this->signature_petugas ? asset('storage/'.$this->signature_petugas) : null;
    }
}
