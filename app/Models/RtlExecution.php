<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtlExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'rtl_id',
        'triwulan',
        'tahun',
        'catatan_progres',
        'file_bukti_dukung',
        'verified_by',
    ];

    public function rtl()
    {
        return $this->belongsTo(Rtl::class);
    }

    public function verifier()
    {
        return $this->belongsTo(Pegawai::class, 'verified_by', 'nip');
    }
}
