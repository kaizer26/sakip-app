<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorMedia extends Model
{
    use HasFactory;

    protected $table = 'indikator_media';

    protected $fillable = [
        'indikator_id',
        'field',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    /**
     * URL publik file media.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
