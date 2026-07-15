<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analisis extends Model
{
    use HasFactory;

    protected $table = 'analisis';

    protected $fillable = [
        'indikator_id',
        'pegawai_nip',
        'triwulan',
        'narasi_analisis',
        'kendala',
        'solusi',
        'rencana_tindak_lanjut',
        'penjelasan_lainnya',
        'pic_tindak_lanjut',
        'batas_waktu',
        'severity',
        'link_bukti_kinerja',
        'link_bukti_tindak_lanjut',
        'file_bukti_kinerja',
        'file_bukti_tindak_lanjut',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_nip', 'nip');
    }

}
