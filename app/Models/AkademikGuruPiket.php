<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikGuruPiket extends Model
{
    protected $table = 'akademik_guru_pikets';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'hari',
        'waka_piket_id',
        'guru_ids',
        'catatan',
    ];

    protected $casts = [
        'guru_ids' => 'array',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function wakaPiket(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'waka_piket_id');
    }

    public function getGuruListAttribute()
    {
        if (empty($this->guru_ids)) return collect([]);
        return Guru::whereIn('id', $this->guru_ids)->get();
    }
}
