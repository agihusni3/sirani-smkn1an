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
            AuditLog::catat('create', 'siswa', "Siswa baru ditambahkan: {$m->nama} (NISN: {$m->nisn})", null, $m->only(['nisn','nama','status']), $m);
        });
        static::updated(function ($m) {
            if ($m->wasChanged()) {
                AuditLog::catat('update', 'siswa', "Data siswa diubah: {$m->nama}", $m->getOriginal(), $m->getChanges(), $m);
            }
        });
        static::deleted(function ($m) {
            AuditLog::catat('delete', 'siswa', "Siswa dihapus: {$m->nama} (NISN: {$m->nisn})", $m->only(['nisn','nama','status']), null, $m);
        });
    }

    protected $fillable = [
        'nisn',
        'nama',
        'nama_ortu',
        'no_hp_ortu',
        'no_hp_siswa',
        'foto',
        'status',
    ];

    protected $casts = [];

    protected $appends = ['foto_url', 'nomor_hp_ortu'];

    public function getNomorHpOrtuAttribute(): ?string
    {
        return $this->attributes['no_hp_ortu'] ?? $this->attributes['nomor_hp_ortu'] ?? null;
    }

    public function getNoHpAttribute(): ?string
    {
        return $this->attributes['no_hp_siswa'] ?? null;
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

    public function kartuRfid(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KartuRfid::class, 'pemilik_id')->where('pemilik_type', 'siswa')->where('status', 'aktif');
    }

    public function kartuRfids(): HasMany
    {
        return $this->hasMany(KartuRfid::class, 'pemilik_id')->where('pemilik_type', 'siswa');
    }

    /**
     * Standarisasi nomor HP ke format 628...
     */
    public static function normalizePhone(?string $phone): string
    {
        if (empty($phone)) return '';
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) return '';

        if (str_starts_with($clean, '08')) {
            $clean = '628' . substr($clean, 2);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '628' . substr($clean, 1);
        }

        return $clean;
    }

    /**
     * Cek apakah nomor HP siswa dan nomor HP ortu terdeteksi sama (duplikat bentrok)
     */
    public function hasDuplicateContact(): bool
    {
        $siswaHp = static::normalizePhone($this->no_hp_siswa);
        $ortuHp  = static::normalizePhone($this->no_hp_ortu);

        if (empty($siswaHp) || empty($ortuHp)) {
            return false;
        }

        return $siswaHp === $ortuHp;
    }
}
