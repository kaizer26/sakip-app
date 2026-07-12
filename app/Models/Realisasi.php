<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id', 'triwulan', 'realisasi_kumulatif',
        'realisasi_x', 'realisasi_y',
    ];

    /**
     * Audit log perubahan nilai realisasi.
     */
    public function logs()
    {
        return $this->hasMany(RealisasiLog::class);
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function getCapaianTriwulanAttribute()
    {
        $target = $this->indikator->target;
        $targetField = 'target_tw' . $this->triwulan;
        $targetVal = $target ? $target->$targetField : 0;
        
        if ($targetVal == 0) return 0;
        return ($this->realisasi_kumulatif / $targetVal) * 100;
    }

    /**
     * Capaian berbasis X/Y jika tersedia.
     */
    public function getCapaianXyAttribute(): ?float
    {
        if ($this->realisasi_y && $this->realisasi_y > 0) {
            return round(($this->realisasi_x / $this->realisasi_y) * 100, 2);
        }
        return null;
    }
}

