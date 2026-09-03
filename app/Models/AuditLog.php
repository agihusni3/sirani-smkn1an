<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'aksi',
        'modul',
        'target_type',
        'target_id',
        'deskripsi',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    // ──────────────────────────────────────────────
    // Relasi
    // ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ──────────────────────────────────────────────
    // Helper statis — catat log dengan mudah
    // ──────────────────────────────────────────────

    /**
     * Catat satu entri audit.
     *
     * @param  string      $aksi      create|update|delete|transisi|koreksi|scan|login|logout
     * @param  string      $modul     nama modul (siswa, absensi, rfid, siklus, izin, disiplin, auth …)
     * @param  string      $deskripsi kalimat singkat yang dapat dibaca manusia
     * @param  array|null  $dataLama  data sebelum perubahan
     * @param  array|null  $dataBaru  data sesudah perubahan
     * @param  mixed|null  $target    instance model Eloquent (untuk target_type & target_id)
     */
    public static function catat(
        string $aksi,
        string $modul,
        string $deskripsi,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        mixed  $target   = null
    ): void {
        try {
            self::create([
                'user_id'     => Auth::id(),
                'aksi'        => $aksi,
                'modul'       => $modul,
                'target_type' => $target ? class_basename($target) : null,
                'target_id'   => $target?->getKey(),
                'deskripsi'   => $deskripsi,
                'data_lama'   => $dataLama,
                'data_baru'   => $dataBaru,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable) {
            // Jangan biarkan kegagalan audit menghentikan operasi utama
        }
    }

    // ──────────────────────────────────────────────
    // Label & warna untuk tampilan
    // ──────────────────────────────────────────────

    public function badgeClass(): string
    {
        return match (strtolower($this->aksi)) {
            'create'             => 'badge-create',
            'update'             => 'badge-update',
            'delete'             => 'badge-delete',
            'transisi'           => 'badge-transisi',
            'koreksi'            => 'badge-koreksi',
            'scan'               => 'badge-scan',
            'login'              => 'badge-login',
            'login_face'         => 'badge-face',
            'logout'             => 'badge-logout',
            'buka_sesi_gerbang'  => 'badge-gerbang-buka',
            'tutup_sesi_gerbang' => 'badge-gerbang-tutup',
            default              => 'badge-default',
        };
    }

    public function aksiLabel(): string
    {
        return match (strtolower($this->aksi)) {
            'create'             => 'Tambah Data',
            'update'             => 'Ubah Data',
            'delete'             => 'Hapus Data',
            'transisi'           => 'Transisi',
            'koreksi'            => 'Koreksi',
            'scan'               => 'Smart Gate Scan',
            'login'              => 'Login Akun',
            'login_face'         => 'Face ID Login',
            'logout'             => 'Logout',
            'buka_sesi_gerbang'  => 'Buka Gerbang',
            'tutup_sesi_gerbang' => 'Tutup Gerbang',
            default              => ucwords(str_replace('_', ' ', $this->aksi)),
        };
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['modul'])) {
            $query->where('modul', $filters['modul']);
        }
        if (!empty($filters['aksi'])) {
            $query->where('aksi', $filters['aksi']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['dari'])) {
            $query->whereDate('created_at', '>=', $filters['dari']);
        }
        if (!empty($filters['sampai'])) {
            $query->whereDate('created_at', '<=', $filters['sampai']);
        }
        if (!empty($filters['cari'])) {
            $query->where('deskripsi', 'like', '%' . $filters['cari'] . '%');
        }

        return $query;
    }
}
