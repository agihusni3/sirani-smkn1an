<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtSoal extends Model
{
    use HasFactory;

    protected $table = 'cbt_soals';

    protected $fillable = [
        'bank_soal_id',
        'nomor_urut',
        'jenis_soal',
        'pertanyaan',
        'media_gambar',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'bobot_nilai',
        'kode_tp',
        'pembahasan',
    ];

    protected $casts = [
        'nomor_urut'  => 'integer',
        'bobot_nilai' => 'float',
    ];

    public function bankSoal()
    {
        return $this->belongsTo(CbtBankSoal::class, 'bank_soal_id');
    }

    public function jawabanSiswas()
    {
        return $this->hasMany(CbtJawabanSiswa::class, 'soal_id');
    }
}
