<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorAnggaran extends Model
{
    protected $fillable = [
        'indikator_id',
        'tahun',
        'pagu_awal',
        'pagu_revisi',
        'realisasi_tw1',
        'realisasi_tw2',
        'realisasi_tw3',
        'realisasi_tw4',
        'kode_kegiatan',
        'nama_kegiatan',
        'kode_ro',
        'nama_ro',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }
}
