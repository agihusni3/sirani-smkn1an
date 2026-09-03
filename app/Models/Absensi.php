<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'pemilik_type',
        'pemilik_id',
        'siswa_rombel_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'sumber_absen',
        'keterangan',
    ];

    public function siswaRombel(): BelongsTo
    {
        return $this->belongsTo(SiswaRombel::class, 'siswa_rombel_id');
    }

    /** Relasi ke Siswa (dipakai ketika pemilik_type = 'siswa') */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'pemilik_id');
    }

    /** Relasi ke Guru (dipakai ketika pemilik_type = 'guru') */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'pemilik_id');
    }
}
