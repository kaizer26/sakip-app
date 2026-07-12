<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id',
        'nama_output',
        'penjelasan_ro',
        'target_volume',
        'jenis_output',
        'periode',
        'is_achieved',
        'file_path',
    ];

    /**
     * Mutator for nama_output (KBBI Title Case)
     */
    public function setNamaOutputAttribute($value)
    {
        $this->attributes['nama_output'] = $this->formatKBBI($value);
    }

    /**
     * Mutator for jenis_output (capitalize)
     */
    public function setJenisOutputAttribute($value)
    {
        $this->attributes['jenis_output'] = ucfirst(strtolower($value));
    }

    /**
     * Mutator for periode (capitalize)
     */
    public function setPeriodeAttribute($value)
    {
        $this->attributes['periode'] = ucfirst(strtolower($value));
    }

    private function formatKBBI($string)
    {
        $smallWords = [
            'di', 'ke', 'dari', 'pada', 'dalam', 'yang', 'untuk', 'bagi', 
            'dengan', 'serta', 'dan', 'atau', 'tapi', 'namun', 'melainkan', 
            'karena', 'demi', 'supaya', 'agar', 'sebab', 'sebagai', 'si', 'sang', 'per'
        ];

        $words = explode(' ', strtolower($string));
        
        foreach ($words as $i => $word) {
            // Capitalize if it's the first word or not in smallWords list
            if ($i === 0 || !in_array($word, $smallWords)) {
                $words[$i] = ucfirst($word);
            }
        }
        
        return implode(' ', $words);
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function outputRealisasis()
    {
        return $this->hasMany(OutputRealisasi::class);
    }
}
