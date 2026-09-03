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

        // Ambil konfigurasi jam operasional mingguan jika ada (Senin - Jumat)
        $namaHariMap = [
            Carbon::MONDAY => 'Senin',
            Carbon::TUESDAY => 'Selasa',
            Carbon::WEDNESDAY => 'Rabu',
            Carbon::THURSDAY => 'Kamis',
            Carbon::FRIDAY => 'Jumat',
            Carbon::SATURDAY => 'Sabtu',
            Carbon::SUNDAY => 'Minggu',
        ];
        $namaHari = $namaHariMap[$date->dayOfWeek] ?? 'Senin';
        $jadwalMingguan = class_exists(JadwalMingguan::class) ? JadwalMingguan::getByHari($namaHari) : null;

        if ($jadwalMingguan && $jadwalMingguan->is_aktif) {
            $jamMasuk = $jadwalMingguan->jam_masuk_toleransi;
            $jamPulang = $jadwalMingguan->jam_pulang_mulai;
            $jamTutup = $jadwalMingguan->jam_tutup_gerbang;
            $ket = $jadwalMingguan->keterangan ?: "Jadwal {$namaHari}";
        } else {
            $isJumat = ($date->dayOfWeek === Carbon::FRIDAY);
            $jamMasuk = '07:15:00';
            $jamPulang = $isJumat ? '11:30:00' : '15:30:00';
            $jamTutup = '17:00:00';
            $ket = $isJumat ? 'Jadwal Hari Jumat (Pulang Cepat)' : 'Jadwal Reguler';
        }

        return self::create([
            'tanggal' => $dateStr,
            'jam_masuk_toleransi' => $jamMasuk,
            'jam_pulang_mulai' => $jamPulang,
            'jam_tutup_gerbang' => $jamTutup,
            'keterangan' => $ket,
            'diubah_oleh' => "Sistem Otomatis (Template {$namaHari})",
            'is_sesi_buka' => true,
        ]);
    }


    /**
     * Cek apakah sesi gerbang saat ini terbuka (Smart Gate selalu aktif otomatis)
     */
    public static function isSesiAktif(?string $tanggal = null): bool
    {
        return true;
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
