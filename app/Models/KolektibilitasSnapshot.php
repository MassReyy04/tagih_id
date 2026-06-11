<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KolektibilitasSnapshot extends Model
{
    protected $table = 'kolektibilitas_snapshots';

    protected $fillable = [
        'tanggal',
        'saldo_lancar',
        'saldo_kurang_lancar',
        'saldo_diragukan',
        'saldo_macet',
        'saldo_bermasalah',
        'nilai_perkalian_total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo_lancar' => 'decimal:2',
        'saldo_kurang_lancar' => 'decimal:2',
        'saldo_diragukan' => 'decimal:2',
        'saldo_macet' => 'decimal:2',
        'saldo_bermasalah' => 'decimal:2',
        'nilai_perkalian_total' => 'decimal:2',
    ];
}
