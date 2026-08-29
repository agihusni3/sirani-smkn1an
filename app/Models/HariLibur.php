<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    protected $table = 'hari_liburs';

    protected $fillable = [
        'nama_libur',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];

    /**
     * Memeriksa apakah suatu tanggal merupakan akhir pekan (Sabtu / Minggu).
     */
    public static function isWeekend(?string $tanggal = null): bool
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        return $date->isSaturday() || $date->isSunday();
    }

    /**
     * Memeriksa apakah suatu tanggal adalah hari libur (Weekend atau Tanggal Merah / Cuti Terdaftar).
     */
    public static function isLibur(?string $tanggal = null): bool
    {
        $dateStr = $tanggal ?: Carbon::today()->toDateString();

        if (self::isWeekend($dateStr)) {
            return true;
        }

        return self::where('tanggal_mulai', '<=', $dateStr)
            ->where('tanggal_selesai', '>=', $dateStr)
            ->exists();
    }

    /**
     * Mengambil instance HariLibur aktif pada tanggal tertentu (jika terdaftar di database).
     */
    public static function getLiburHariIni(?string $tanggal = null): ?self
    {
        $dateStr = $tanggal ?: Carbon::today()->toDateString();

        return self::where('tanggal_mulai', '<=', $dateStr)
            ->where('tanggal_selesai', '>=', $dateStr)
            ->first();
    }

    /**
     * Mengambil daftar hari libur dalam rentang bulan & tahun kalender tertentu.
     */
    public static function getLiburBulan(int $bulan, int $tahun)
    {
        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        return self::where(function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
              ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
              ->orWhere(function ($sub) use ($startOfMonth, $endOfMonth) {
                  $sub->where('tanggal_mulai', '<=', $startOfMonth)
                      ->where('tanggal_selesai', '>=', $endOfMonth);
              });
        })->orderBy('tanggal_mulai', 'asc')->get();
    }

    /**
     * Badge tampilan jenis libur.
     */
    public function getJenisBadgeAttribute(): string
    {
        switch ($this->jenis) {
            case 'libur_nasional':
                return '<span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.3); font-size:11px; padding:2px 8px; border-radius:12px; font-weight:700;">Libur Nasional</span>';
            case 'cuti_bersama':
                return '<span class="badge" style="background:rgba(245,158,11,0.12); color:#D97706; border:1px solid rgba(245,158,11,0.3); font-size:11px; padding:2px 8px; border-radius:12px; font-weight:700;">Cuti Bersama</span>';
            case 'libur_semester':
                return '<span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; border:1px solid rgba(59,130,246,0.3); font-size:11px; padding:2px 8px; border-radius:12px; font-weight:700;">Libur Semester</span>';
            case 'khusus_sekolah':
            default:
                return '<span class="badge" style="background:rgba(168,85,247,0.12); color:#9333EA; border:1px solid rgba(168,85,247,0.3); font-size:11px; padding:2px 8px; border-radius:12px; font-weight:700;">Khusus Sekolah</span>';
        }
    }
}
