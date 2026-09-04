<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SertifikatGuru extends Model
{
    use HasFactory;

    protected $table = 'sertifikat_gurus';

    protected $fillable = [
        'guru_id',
        'nama_pelatihan',
        'penyelenggara',
        'tahun',
        'file_sertifikat',
    ];

    protected $appends = ['file_url'];

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_sertifikat && file_exists(public_path('storage/' . $this->file_sertifikat))) {
            return asset('storage/' . $this->file_sertifikat);
        }
        return null;
    }
}
