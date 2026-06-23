<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKolektibilitasSaldoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'saldo_lancar' => ['required', 'numeric', 'min:0'],
            'saldo_kurang_lancar' => ['required', 'numeric', 'min:0'],
            'saldo_diragukan' => ['required', 'numeric', 'min:0'],
            'saldo_macet' => ['required', 'numeric', 'min:0'],
            'saldo_bermasalah' => ['required', 'numeric', 'min:0'],
        ];
    }
}
