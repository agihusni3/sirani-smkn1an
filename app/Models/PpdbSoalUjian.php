<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbSoalUjian extends Model
{
    use HasFactory;

    protected $table = 'ppdb_soal_ujians';

    protected $fillable = [
        'ppdb_ujian_setting_id',
        'nomor_urut',
        'tipe_soal',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'bobot_nilai',
        'gambar_soal',
    ];

    protected $casts = [
        'bobot_nilai' => 'decimal:2',
    ];

    public function setting()
    {
        return $this->belongsTo(PpdbUjianSetting::class, 'ppdb_ujian_setting_id');
    }

    /** Apakah soal ini PG */
    public function isPg(): bool
    {
        return $this->tipe_soal === 'pg';
    }

    /** Ambil label opsi sebagai array ['A' => ..., 'B' => ..., ...] */
    public function getOpsiArray(): array
    {
        return array_filter([
            'A' => $this->opsi_a,
            'B' => $this->opsi_b,
            'C' => $this->opsi_c,
            'D' => $this->opsi_d,
            'E' => $this->opsi_e,
        ]);
    }
}
