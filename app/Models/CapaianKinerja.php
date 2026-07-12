<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id',
        'tahun',
        'triwulan',
        'link_bukti_kinerja',
        'link_bukti_tindak_lanjut',
        'penjelasan_lainnya',
        'dasar_hitung',
        'argumen_logis',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}
