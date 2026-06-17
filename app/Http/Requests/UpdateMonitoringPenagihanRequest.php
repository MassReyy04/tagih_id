<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonitoringPenagihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nama_mitra' => ['required', 'string', 'max:255'],
            'nama_usaha' => ['required', 'string', 'max:255'],
            'nomor_induk' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'no_hp' => ['required', 'string', 'max:32'],
            'nilai_pinjaman' => ['required', 'numeric', 'min:0'],
            'sisa_pinjaman' => ['required', 'numeric', 'min:0'],
            'alasan' => ['nullable', 'string'],
            'janji' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'kebutuhan' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'geo_jalan' => ['nullable', 'string', 'max:255'],
            'geo_kelurahan' => ['nullable', 'string', 'max:255'],
            'geo_kecamatan' => ['nullable', 'string', 'max:255'],
            'geo_kota' => ['nullable', 'string', 'max:255'],
            'geo_provinsi' => ['nullable', 'string', 'max:255'],
            'geo_kode_pos' => ['nullable', 'string', 'max:16'],
            'signature_mitra' => ['nullable', 'string'],
            'signature_petugas' => ['nullable', 'string'],
        ];
    }
}
