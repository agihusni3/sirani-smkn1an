<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtBankSoal extends Model
{
    use HasFactory;

    protected $table = 'cbt_bank_soals';

    protected $fillable = [
        'kode_bank',
        'nama_bank',
        'mata_pelajaran',
        'tingkat',
        'jurusan_id',
        'guru_id',
        'kktp_default',
        'deskripsi',
        'is_shared',
    ];

    protected $casts = [
        'kktp_default' => 'float',
        'is_shared'    => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function soals()
    {
        return $this->hasMany(CbtSoal::class, 'bank_soal_id')->orderBy('nomor_urut', 'asc');
    }

    public function jadwals()
    {
        return $this->hasMany(CbtJadwalUjian::class, 'bank_soal_id');
    }
}
