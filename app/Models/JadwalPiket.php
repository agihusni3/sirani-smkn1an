<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class JadwalPiket extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pikets';

    protected $fillable = [
        'hari',
        'guru_id',
        'keterangan',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Konversi tanggal ke nama hari bahasa Indonesia (Senin s/d Minggu).
     */
    public static function getHariIndonesia($tanggal = null): string
    {
        $dateObj = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $map = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            0 => 'Minggu',
            7 => 'Minggu',
        ];

        return $map[$dateObj->dayOfWeek] ?? 'Senin';
    }

    /**
     * Cek apakah seorang guru terdaftar sebagai Guru Piket pada hari ini.
     */
    public static function isGuruPiketHariIni(int $guruId, $tanggal = null): bool
    {
        $guru = Guru::find($guruId);
        if (!$guru || $guru->status !== 'aktif') {
            return false;
        }

        $hari = self::getHariIndonesia($tanggal);

        // Hari Sabtu & Minggu adalah hari libur sekolah (tidak ada tugas piket)
        if (in_array($hari, ['Sabtu', 'Minggu'])) {
            return false;
        }

        return self::where('hari', $hari)
            ->where('guru_id', $guruId)
            ->exists();
    }

    /**
     * Ambil seluruh jadwal piket hari ini beserta relasi guru.
     */
    public static function getJadwalHariIni($tanggal = null)
    {
        $hari = self::getHariIndonesia($tanggal);
        return self::where('hari', $hari)->with('guru')->get();
    }
}
