<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KolektibilitasBermasalah extends Model
{
    protected $table = 'kolektibilitas_bermasalah';

    protected $fillable = [
        'tanggal',
        'saldo_bermasalah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo_bermasalah' => 'decimal:2',
    ];
}
