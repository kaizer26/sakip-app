<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rtl extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'deskripsi_rtl',
        'pic_nip',
        'due_date',
        'status_rtl',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function pic()
    {
        return $this->belongsTo(Pegawai::class, 'pic_nip', 'nip');
    }

    public function executions()
    {
        return $this->hasMany(RtlExecution::class);
    }
}
