<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class JadwalHariIni extends Model
{
    use HasFactory;

    protected $table = 'jadwal_hari_inis';

    protected $fillable = [
        'tanggal',
        'jam_masuk_toleransi',
        'jam_pulang_mulai',
        'jam_tutup_gerbang',
        'keterangan',
        'diubah_oleh',
        'is_sesi_buka',
        'dibuka_oleh',
        'waktu_buka_sesi',
        'waktu_tutup_sesi',
    ];

    protected $casts = [
        'is_sesi_buka' => 'boolean',
        'waktu_buka_sesi' => 'datetime',
        'waktu_tutup_sesi' => 'datetime',
    ];

    /**
     * Mengambil jadwal aktif untuk tanggal tertentu (default hari ini),
     * atau otomatis membuat default berdasarkan hari (Jumat vs Senin-Kamis).
     */
    public static function getJadwalAktif(?string $tanggal = null): self
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $dateStr = $date->toDateString();

        $jadwal = self::where('tanggal', $dateStr)->first();
        if ($jadwal) {
            return $jadwal;
        }

        // Default: Hari Jumat jam pulang 11:30, Senin-Kamis 15:30, Tutup Gerbang 18:00
        $isJumat = ($date->dayOfWeek === Carbon::FRIDAY);
        $jamPulangDefault = $isJumat ? '11:30:00' : '15:30:00';
        $ketDefault = $isJumat ? 'Jadwal Hari Jumat (Pulang Cepat)' : 'Jadwal Reguler';

        return self::create([
            'tanggal' => $dateStr,
            'jam_masuk_toleransi' => '07:15:00',
            'jam_pulang_mulai' => $jamPulangDefault,
            'jam_tutup_gerbang' => '18:00:00',
            'keterangan' => $ketDefault,
            'diubah_oleh' => 'Sistem Otomatis',
            'is_sesi_buka' => true,
        ]);
    }

    /**
     * Cek apakah sesi gerbang saat ini terbuka
     */
    public static function isSesiAktif(?string $tanggal = null): bool
    {
        $jadwal = self::getJadwalAktif($tanggal);
        return (bool) $jadwal->is_sesi_buka;
    }

    /**
     * Buka sesi gerbang oleh Petugas Piket / Admin
     */
    public function bukaSesi(string $petugasNama): void
    {
        $this->update([
            'is_sesi_buka' => true,
            'dibuka_oleh' => $petugasNama,
            'waktu_buka_sesi' => now(),
            'diubah_oleh' => $petugasNama,
        ]);
    }

    /**
     * Tutup sesi gerbang oleh Petugas Piket / Admin
     */
    public function tutupSesi(string $petugasNama): void
    {
        $this->update([
            'is_sesi_buka' => false,
            'dibuka_oleh' => $petugasNama,
            'waktu_tutup_sesi' => now(),
            'diubah_oleh' => $petugasNama,
        ]);
    }
}
