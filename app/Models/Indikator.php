<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;
    
    public function getRouteKeyName()
    {
        return 'kode';
    }

    protected $fillable = [
        'kode',
        'kode_tujuan',
        'kode_sasaran',
        'kode_indikator_kinerja',
        'tujuan',
        'sasaran',
        'indikator_kinerja',
        'jenis_indikator',
        'periode',
        'tipe',
        'satuan',
        'target_tahunan',
        'tahun',
        'pic_id',
        'dasar_hitung',
        'basis_data',
        'triwulan',
        'narasi_analisis',
        'kendala',
        'solusi',
        'rencana_tindak_lanjut',
        'pic_tindak_lanjut',
        'batas_waktu',
        'severity',
        'file_bukti_kinerja',
        'file_bukti_tindak_lanjut',
        'definisi_x',
        'definisi_y',
    ];

    public function pic()
    {
        return $this->belongsTo(Pegawai::class, 'pic_id');
    }

    public function target()
    {
        return $this->hasOne(Target::class);
    }

    public function realisasis()
    {
        return $this->hasMany(Realisasi::class);
    }

    public function tabelRos()
    {
        return $this->hasMany(TabelRo::class);
    }

    /**
     * Scope: tampilkan hanya indikator yang boleh dilihat oleh user tertentu.
     * Admin melihat semua, pegawai hanya melihat indikator di mana ia PIC,
     * ketua tim, atau anggota kegiatan.
     */
    public function scopeVisibleTo($query, $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $pegawaiId = $user->pegawai_id;
        if (!$pegawaiId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($pegawaiId) {
            $q->where('pic_id', $pegawaiId)
              ->orWhereHas('kegiatanMasters', function ($q2) use ($pegawaiId) {
                  $q2->where('ketua_tim_id', $pegawaiId)
                     ->orWhereHas('anggotas', function ($q3) use ($pegawaiId) {
                         $q3->where('pegawai_id', $pegawaiId);
                     });
              });
        });
    }

    public function outputRealisasis()
    {
        return $this->hasMany(OutputRealisasi::class);
    }

    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class);
    }

    public function kegiatanMasters()
    {
        return $this->hasMany(KegiatanMaster::class);
    }

    public function outputMasters()
    {
        return $this->hasMany(OutputMaster::class);
    }

    public function analisis()
    {
        return $this->hasMany(Analisis::class);
    }

    public function capaianKinerjas()
    {
        return $this->hasMany(CapaianKinerja::class);
    }

    public function getCapaianTahunanAttribute()
    {
        $realisasiTerakhir = $this->realisasis()->orderBy('triwulan', 'desc')->first();
        if (!$realisasiTerakhir || $this->target_tahunan == 0) return 0;
        return ($realisasiTerakhir->realisasi_kumulatif / $this->target_tahunan) * 100;
    }

    public function getStatusWarnaAttribute()
    {
        $capaian = $this->capaian_tahunan;
        if ($capaian >= 100) return 'success';
        if ($capaian >= 80) return 'warning';
        return 'danger';
    }

    public function getOutputProgressAttribute()
    {
        $total = $this->output_masters_count ?? $this->outputMasters()->count();
        $completed = $this->completed_outputs_count ?? $this->outputMasters()->where('is_achieved', true)->count();

        if ($total == 0) return "-";
        
        return "{$completed}/{$total}";
    }

}
