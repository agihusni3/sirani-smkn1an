<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtJawabanSiswa extends Model
{
    use HasFactory;

    protected $table = 'cbt_jawaban_siswas';

    protected $fillable = [
        'peserta_ujian_id',
        'soal_id',
        'jawaban_siswa',
        'is_ragu',
        'is_benar',
        'skor',
    ];

    protected $casts = [
        'is_ragu'  => 'boolean',
        'is_benar' => 'boolean',
        'skor'     => 'float',
    ];

    public function peserta()
    {
        return $this->belongsTo(CbtPesertaUjian::class, 'peserta_ujian_id');
    }

    public function soal()
    {
        return $this->belongsTo(CbtSoal::class, 'soal_id');
    }
}
