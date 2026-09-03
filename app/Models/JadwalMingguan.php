<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMingguan extends Model
{
    protected $fillable = [
        'hari',
        'jam_masuk_toleransi',
        'jam_pulang_mulai',
        'jam_tutup_gerbang',
        'is_aktif',
        'keterangan',
        'diubah_oleh',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Ambil jadwal mingguan terurut dari Senin sampai Jumat
     */
    public static function getJadwalSeninJumat()
    {
        $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        $semua = self::all()->keyBy('hari');
        $hasil = collect();

        foreach ($urutanHari as $h) {
            if (isset($semua[$h])) {
                $hasil->push($semua[$h]);
            } else {
                // Buat default jika belum ada
                $isJumat = ($h === 'Jumat');
                $item = self::create([
                    'hari' => $h,
                    'jam_masuk_toleransi' => '07:15:00',
                    'jam_pulang_mulai' => $isJumat ? '11:30:00' : '15:30:00',
                    'jam_tutup_gerbang' => '17:00:00',
                    'is_aktif' => true,
                    'keterangan' => $isJumat ? 'Jadwal Khusus Hari Jumat' : 'Jadwal Reguler KBM',
                    'diubah_oleh' => 'Sistem Otomatis',
                ]);
                $hasil->push($item);
            }
        }

        return $hasil;
    }

    /**
     * Cari jadwal berdasarkan nama hari bahasa Indonesia (Senin, Selasa, dst)
     */
    public static function getByHari(string $hari): ?self
    {
        return self::where('hari', ucfirst(strtolower($hari)))->first();
    }
}
