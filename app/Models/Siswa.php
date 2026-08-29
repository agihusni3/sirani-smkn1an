<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AuditLog;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($m) {
            AuditLog::catat('create', 'siswa', "Siswa baru ditambahkan: {$m->nama} (NIS: {$m->nis})", null, $m->only(['nis','nisn','nama','status']), $m);
        });
        static::updated(function ($m) {
            if ($m->wasChanged()) {
                AuditLog::catat('update', 'siswa', "Data siswa diubah: {$m->nama}", $m->getOriginal(), $m->getChanges(), $m);
            }
        });
        static::deleted(function ($m) {
            AuditLog::catat('delete', 'siswa', "Siswa dihapus: {$m->nama} (NIS: {$m->nis})", $m->only(['nis','nisn','nama','status']), null, $m);
        });
    }

    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'nama_ortu',
        'no_hp_ortu',
        'no_hp_siswa',
        'foto',
        'face_embedding',
        'face_registered_at',
        'status',
    ];

    protected $casts = [
        'face_embedding' => 'array',
        'face_registered_at' => 'datetime',
    ];

    protected $appends = ['foto_url', 'nomor_hp_ortu'];

    public function getNomorHpOrtuAttribute(): ?string
    {
        return $this->attributes['no_hp_ortu'] ?? $this->attributes['nomor_hp_ortu'] ?? null;
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }

        $namaEncoded = urlencode($this->nama);
        return "https://ui-avatars.com/api/?name={$namaEncoded}&background=CA8A04&color=ffffff&bold=true&size=200";
    }

    public function siswaRombel(): HasMany
    {
        return $this->hasMany(SiswaRombel::class, 'siswa_id');
    }

    public function siswaRombels(): HasMany
    {
        return $this->hasMany(SiswaRombel::class, 'siswa_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'pemilik_id')->where('pemilik_type', 'siswa');
    }

    public function notifikasiOrtus(): HasMany
    {
        return $this->hasMany(NotifikasiOrtu::class, 'siswa_id');
    }
}
