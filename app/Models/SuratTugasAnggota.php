<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugasAnggota extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas_anggotas';

    protected $fillable = [
        'surat_tugas_id',
        'guru_id',
        'nama',
        'nip',
        'pangkat_golongan',
        'jabatan',
        'peran',
        'keterangan',
        'urutan',
    ];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function sppd()
    {
        return $this->hasOne(Sppd::class, 'surat_tugas_anggota_id');
    }
}
