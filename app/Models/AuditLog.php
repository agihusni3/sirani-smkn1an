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
    // Kategori & Kelompok Modul Ekosistem
    // ──────────────────────────────────────────────

    public const MODUL_SITUAN = ['situan', 'siswa', 'guru', 'rombel', 'siklus', 'settings', 'backup'];
    public const MODUL_SIRANI = ['sirani', 'absensi', 'piket', 'izin', 'izin_guru', 'rfid', 'disiplin', 'jadwal'];
    public const MODUL_PPDB = ['ppdb', 'ppdb_verifikasi', 'ppdb_seleksi', 'ppdb_migrasi'];
    public const MODUL_WEB_HUMAS = ['web_humas', 'web', 'humas', 'berita', 'banner'];

    public function namaModulGroup(): string
    {
        $modul = strtolower($this->modul);
        if (in_array($modul, self::MODUL_SITUAN)) {
            return 'SITUAN (Tata Usaha)';
        }
        if (in_array($modul, self::MODUL_SIRANI)) {
            return 'SIRANI (Presensi)';
        }
        if (in_array($modul, self::MODUL_PPDB)) {
            return 'PPDB 2026';
        }
        if (in_array($modul, self::MODUL_WEB_HUMAS)) {
            return 'Web & Humas';
        }
        if ($modul === 'auth') {
            return 'Otentikasi / DCC';
        }
        return strtoupper($this->modul);
    }

    public function modulGroupBadge(): array
    {
        $modul = strtolower($this->modul);
        if (in_array($modul, self::MODUL_SITUAN)) {
            return [
                'nama' => 'SITUAN',
                'sub'  => 'Tata Usaha',
                'warna'=> '#0284c7',
                'bg'   => 'rgba(2, 132, 199, 0.12)',
                'border'=> 'rgba(2, 132, 199, 0.3)',
                'icon' => 'bi-buildings-fill',
            ];
        }
        if (in_array($modul, self::MODUL_SIRANI)) {
            return [
                'nama' => 'SIRANI',
                'sub'  => 'Presensi & Disiplin',
                'warna'=> '#10b981',
                'bg'   => 'rgba(16, 185, 129, 0.12)',
                'border'=> 'rgba(16, 185, 129, 0.3)',
                'icon' => 'bi-fingerprint',
            ];
        }
        if (in_array($modul, self::MODUL_PPDB)) {
            return [
                'nama' => 'PPDB 2026',
                'sub'  => 'Panitia Seleksi',
                'warna'=> '#d97706',
                'bg'   => 'rgba(217, 119, 6, 0.12)',
                'border'=> 'rgba(217, 119, 6, 0.3)',
                'icon' => 'bi-mortarboard-fill',
            ];
        }
        if (in_array($modul, self::MODUL_WEB_HUMAS)) {
            return [
                'nama' => 'HUMAS & WEB',
                'sub'  => 'Publikasi',
                'warna'=> '#0ea5e9',
                'bg'   => 'rgba(14, 165, 233, 0.12)',
                'border'=> 'rgba(14, 165, 233, 0.3)',
                'icon' => 'bi-globe-americas',
            ];
        }
        return [
            'nama' => strtoupper($this->modul),
            'sub'  => 'Sistem',
            'warna'=> '#64748b',
            'bg'   => 'rgba(100, 116, 139, 0.12)',
            'border'=> 'rgba(100, 116, 139, 0.3)',
            'icon' => 'bi-shield-shaded',
        ];
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeSituan($query)
    {
        return $query->whereIn('modul', self::MODUL_SITUAN);
    }

    public function scopeSirani($query)
    {
        return $query->whereIn('modul', self::MODUL_SIRANI);
    }

    public function scopePpdb($query)
    {
        return $query->whereIn('modul', self::MODUL_PPDB);
    }

    public function scopeWebHumas($query)
    {
        return $query->whereIn('modul', self::MODUL_WEB_HUMAS);
    }

    public function scopeAuthModul($query)
    {
        return $query->where('modul', 'auth');
    }

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
