<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KolektibilitasSaldoInput extends Model
{
    protected $table = 'kolektibilitas_saldo_input';

    protected $fillable = [
        'tanggal',
        'saldo_lancar',
        'saldo_kurang_lancar',
        'saldo_diragukan',
        'saldo_macet',
        'saldo_bermasalah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo_lancar' => 'decimal:2',
        'saldo_kurang_lancar' => 'decimal:2',
        'saldo_diragukan' => 'decimal:2',
        'saldo_macet' => 'decimal:2',
        'saldo_bermasalah' => 'decimal:2',
    ];
}
