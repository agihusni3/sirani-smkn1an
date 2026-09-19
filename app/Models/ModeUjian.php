<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ModeUjian extends Model
{
    use HasFactory;

    protected $table = 'mode_ujians';

    protected $fillable = [
        'nama_ujian',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_masuk_toleransi',
        'jam_pulang_mulai',
        'jam_tutup_gerbang',
        'panitia_guru_ids',
        'nonaktifkan_piket_reguler',
        'is_aktif',
        'keterangan',
        'diubah_oleh',
    ];

    protected $casts = [
        'tanggal_mulai'             => 'date:Y-m-d',
        'tanggal_selesai'           => 'date:Y-m-d',
        'panitia_guru_ids'          => 'array',
        'nonaktifkan_piket_reguler' => 'boolean',
        'is_aktif'                  => 'boolean',
    ];

    /**
     * Dapatkan Mode Ujian yang aktif pada tanggal tertentu (default hari ini).
     */
    public static function getModeAktif(?string $tanggal = null): ?self
    {
        $dateStr = $tanggal ?: Carbon::today()->toDateString();

        return self::where('is_aktif', true)
            ->where('tanggal_mulai', '<=', $dateStr)
            ->where('tanggal_selesai', '>=', $dateStr)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Cek apakah guru tertentu terdaftar sebagai Panitia Ujian yang aktif hari ini.
     */
    public static function isPanitiaAktif(?int $guruId, ?string $tanggal = null): bool
    {
        if (!$guruId) return false;

        $mode = self::getModeAktif($tanggal);
        if (!$mode) return false;

        $panitiaIds = $mode->panitia_guru_ids ?? [];
        if (!is_array($panitiaIds)) return false;

        return in_array((int) $guruId, array_map('intval', $panitiaIds), true);
    }

    /**
     * Ambil koleksi Guru yang menjadi panitia pelaksana ujian ini.
     */
    public function getDaftarPanitiaAttribute(): Collection
    {
        $ids = $this->panitia_guru_ids ?? [];
        if (empty($ids) || !is_array($ids)) {
            return collect();
        }

        return Guru::whereIn('id', $ids)->get();
    }
}
