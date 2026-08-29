<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AuditLog;

class SiswaRombel extends Model
{
    use HasFactory;

    protected $table = 'siswa_rombels';

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($m) {
            AuditLog::catat('create', 'siklus', "Siswa ditambahkan ke rombel (siswa_id:{$m->siswa_id}, rombel_id:{$m->rombel_id})", null, $m->only(['siswa_id','rombel_id','tahun_ajaran_id','status_keanggotaan']), $m);
        });
        static::updated(function ($m) {
            if ($m->wasChanged('status_keanggotaan')) {
                AuditLog::catat('transisi', 'siklus', "Status rombel siswa berubah: {$m->getOriginal('status_keanggotaan')} → {$m->status_keanggotaan} (siswa_id:{$m->siswa_id})", ['status_keanggotaan' => $m->getOriginal('status_keanggotaan')], ['status_keanggotaan' => $m->status_keanggotaan], $m);
            }
        });
    }

    protected $fillable = [
        'siswa_id',
        'rombel_id',
        'tahun_ajaran_id',
        'status_keanggotaan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
