<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KolektibilitasMitra extends Model
{
    protected $table = 'kolektibilitas_mitra';

    protected $fillable = [
        'nomor_induk',
        'nama_mitra',
        'hari_tunggakan',
    ];

    protected $casts = [
        'hari_tunggakan' => 'integer',
    ];
}
