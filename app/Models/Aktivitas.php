<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id',
        'kegiatan_id',
        'pegawai_nip',
        'triwulan',
        'tahapan',
        'tanggal_mulai',
        'tanggal_selesai',
        'uraian',
        'penjelasan_kegiatan',
        'realisasi_kegiatan',
        'lampiran',
    ];

    protected $casts = [
        'lampiran' => 'array',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanMaster::class, 'kegiatan_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_nip', 'nip');
    }
}
