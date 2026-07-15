<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SasaranAnggaran extends Model
{
    protected $fillable = [
        'kode',
        'tahun',
        'pagu_awal',
        'pagu_revisi',
        'realisasi_tw1',
        'realisasi_tw2',
        'realisasi_tw3',
        'realisasi_tw4',
    ];
}
