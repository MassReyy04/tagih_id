<?php

namespace App\Http\Requests;

use App\Models\MonitoringPenagihan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateKolektibilitasMitraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'nomor_induk' => ['required', 'string', 'max:255'],
            'hari_tunggakan' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $nim = trim((string) $this->input('nomor_induk'));

            $exists = MonitoringPenagihan::query()
                ->where('nomor_induk', $nim)
                ->where('sisa_pinjaman', '>', 0)
                ->exists();

            if (! $exists) {
                $validator->errors()->add(
                    'nomor_induk',
                    'NIM tidak ditemukan atau mitra tidak memiliki sisa pinjaman aktif.'
                );
            }
        });
    }
}
