<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikBankSoal extends Model
{
    protected $table = 'akademik_bank_soals';

    protected $fillable = [
        'mata_pelajaran_id',
        'guru_id',
        'tipe',
        'topik',
        'pertanyaan',
        'gambar_url',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'bobot',
        'pembahasan',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(AkademikMataPelajaran::class, 'mata_pelajaran_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function getOpsiAttribute(): array
    {
        $out = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $h) {
            $k = 'opsi_' . strtolower($h);
            if (!empty($this->$k)) {
                $out[$h] = $this->$k;
            }
        }
        return $out;
    }
}
