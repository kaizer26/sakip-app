<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelRo extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id',
        'tahun',
        'triwulan',
        'ro',
        'realisasi_volume_ro',
        'progres_ro',
        'pagu_awal',
        'pagu_revisi',
        'pagu_sisa',
        'pagu_realisasi',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}
