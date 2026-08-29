<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AuditLog;

class IzinGuru extends Model
{
    use HasFactory;

    protected $table = 'izin_gurus';

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($m) {
            AuditLog::catat('create', 'izin_guru', "Izin guru dicatat: jenis={$m->jenis}, tanggal={$m->tanggal}, guru_id={$m->guru_id}", null, $m->only(['guru_id','tanggal','jenis','status','keterangan','file_pendukung']), $m);
        });
        static::deleted(function ($m) {
            AuditLog::catat('delete', 'izin_guru', "Catatan izin guru dihapus: jenis={$m->jenis}, tanggal={$m->tanggal}, guru_id={$m->guru_id}", $m->only(['guru_id','tanggal','jenis','status']), null, $m);
        });
    }

    protected $fillable = [
        'guru_id',
        'tanggal',
        'jenis',
        'status',
        'keterangan',
        'file_pendukung',
        'disetujui_oleh',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_pendukung) {
            return asset('storage/' . $this->file_pendukung);
        }
        return null;
    }
}
