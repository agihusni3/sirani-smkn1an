<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;

class KartuRfid extends Model
{
    use HasFactory;

    protected $table = 'kartu_rfids';

    protected $fillable = [
        'uid',
        'pemilik_type',
        'pemilik_id',
        'status',
        'tanggal_nonaktif',
    ];

    protected $casts = [
        'tanggal_nonaktif' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($m) {
            AuditLog::catat(
                'create',
                'rfid',
                "Kartu RFID ({$m->uid}) didaftarkan untuk {$m->pemilik_type} ID: {$m->pemilik_id}",
                null,
                $m->only(['uid', 'pemilik_type', 'pemilik_id', 'status']),
                $m
            );
        });

        static::updated(function ($m) {
            if ($m->wasChanged('status') && $m->status === 'nonaktif') {
                AuditLog::catat(
                    'update',
                    'rfid',
                    "Kartu RFID ({$m->uid}) dinonaktifkan",
                    ['status' => 'aktif'],
                    ['status' => 'nonaktif', 'tanggal_nonaktif' => $m->tanggal_nonaktif],
                    $m
                );
            }
        });
    }

    /**
     * Relasi ke Pemilik (Siswa atau Guru)
     */
    public function pemilik()
    {
        return $this->pemilik_type === 'siswa'
            ? $this->belongsTo(Siswa::class, 'pemilik_id')
            : $this->belongsTo(Guru::class, 'pemilik_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'pemilik_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'pemilik_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Helper pairing/registrasi kartu baru untuk pemilik
     */
    public static function pair(string $uid, string $pemilikType, int $pemilikId): self
    {
        $cleanUid = strtoupper(trim($uid));

        // Nonaktifkan kartu lama milik orang ini jika ada
        self::where('pemilik_type', $pemilikType)
            ->where('pemilik_id', $pemilikId)
            ->where('status', 'aktif')
            ->update([
                'status' => 'nonaktif',
                'tanggal_nonaktif' => now(),
            ]);

        // Cek apakah UID sudah pernah terdaftar
        $existing = self::where('uid', $cleanUid)->first();
        if ($existing) {
            $existing->update([
                'pemilik_type' => $pemilikType,
                'pemilik_id' => $pemilikId,
                'status' => 'aktif',
                'tanggal_nonaktif' => null,
            ]);
            return $existing;
        }

        return self::create([
            'uid' => $cleanUid,
            'pemilik_type' => $pemilikType,
            'pemilik_id' => $pemilikId,
            'status' => 'aktif',
        ]);
    }
}
