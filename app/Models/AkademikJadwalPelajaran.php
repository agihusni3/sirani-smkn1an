<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikJadwalPelajaran extends Model
{
    protected $table = 'akademik_jadwal_pelajarans';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'hari',
        'jam_ke',
        'pukul',
        'rombel_id',
        'guru_id',
        'mata_pelajaran_id',
        'distribusi_id',
        'kode_guru',
        'singkatan_mapel',
        'kegiatan_khusus',
        'is_locked',
        'warna_bg',
        'resource_key',  // copy dari mapel untuk query cepat tanpa JOIN
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(AkademikMataPelajaran::class, 'mata_pelajaran_id');
    }

    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id');
    }
}
