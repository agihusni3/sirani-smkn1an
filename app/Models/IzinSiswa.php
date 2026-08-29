<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AuditLog;

class IzinSiswa extends Model
{
    use HasFactory;

    protected $table = 'izin_siswas';

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($m) {
            AuditLog::catat('create', 'izin', "Izin siswa dicatat: jenis={$m->jenis}, tanggal={$m->tanggal}, siswa_id={$m->siswa_id}", null, $m->only(['siswa_id','tanggal','jenis','status','keterangan','file_pendukung']), $m);
        });
        static::deleted(function ($m) {
            AuditLog::catat('delete', 'izin', "Catatan izin dihapus: jenis={$m->jenis}, tanggal={$m->tanggal}, siswa_id={$m->siswa_id}", $m->only(['siswa_id','tanggal','jenis','status']), null, $m);
        });
    }

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jenis',
        'status',
        'keterangan',
        'file_pendukung',
        'disetujui_oleh',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_pendukung) {
            return asset('storage/' . $this->file_pendukung);
        }
        return null;
    }
}
