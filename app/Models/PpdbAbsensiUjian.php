<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbAbsensiUjian extends Model
{
    use HasFactory;

    protected $table = 'ppdb_absensi_ujians';

    protected $fillable = [
        'ppdb_pendaftar_id',
        'ppdb_ujian_setting_id',
        'no_pendaftaran',
        'jadwal_tanggal',
        'sesi_ujian',
        'ruang_ujian',
        'waktu_hadir',
        'status_kehadiran',
        'metode_presensi',
        'petugas_user_id',
        'catatan',
    ];

    protected $casts = [
        'jadwal_tanggal' => 'date',
        'waktu_hadir'    => 'datetime',
    ];

    public function pendaftar()
    {
        return $this->belongsTo(PpdbPendaftar::class, 'ppdb_pendaftar_id');
    }

    public function setting()
    {
        return $this->belongsTo(PpdbUjianSetting::class, 'ppdb_ujian_setting_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_user_id');
    }
}
