<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id',
        'triwulan',
        'tahun',
        'status_kendala',
        'deskripsi',
        'solusi_sementara',
        'pegawai_nip',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function rtls()
    {
        return $this->hasMany(Rtl::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_nip', 'nip');
    }
}
