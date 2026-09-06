<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbUjianSetting extends Model
{
    use HasFactory;

    protected $table = 'ppdb_ujian_settings';

    protected $fillable = [
        'judul_ujian',
        'tahun_ajaran',
        'file_pdf_soal',
        'nama_file_asli',
        'jumlah_soal_pg',
        'jumlah_soal_esai',
        'kunci_jawaban_pg',
        'bobot_pg',
        'bobot_esai',
        'durasi_menit',
        'is_active',
        'petunjuk_ujian',
        'buka_pada',
        'tutup_pada',
        'created_by',
    ];

    protected $casts = [
        'kunci_jawaban_pg' => 'array',
        'bobot_pg'         => 'decimal:2',
        'bobot_esai'       => 'decimal:2',
        'is_active'        => 'boolean',
        'buka_pada'        => 'datetime',
        'tutup_pada'       => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pesertas()
    {
        return $this->hasMany(PpdbUjianPeserta::class, 'ppdb_ujian_setting_id');
    }

    /**
     * Dapatkan setting ujian aktif saat ini
     */
    public static function getAktif(): ?self
    {
        $setting = self::where('is_active', true)->latest()->first();
        if (!$setting) {
            $setting = self::latest()->first();
        }
        return $setting;
    }

    /**
     * Hitung koreksi otomatis jawaban PG peserta
     */
    public function koreksiPg(array $jawabanSiswa): array
    {
        $kunci = $this->kunci_jawaban_pg ?: [];
        $totalPg = (int) ($this->jumlah_soal_pg ?: 30);
        
        $benar = 0;
        $salah = 0;
        $kosong = 0;

        for ($i = 1; $i <= $totalPg; $i++) {
            $nomorStr = (string) $i;
            $kunciNomor = isset($kunci[$nomorStr]) ? strtoupper(trim($kunci[$nomorStr])) : null;
            $jawabSiswa = isset($jawabanSiswa[$nomorStr]) ? strtoupper(trim($jawabanSiswa[$nomorStr])) : null;

            if (empty($jawabSiswa)) {
                $kosong++;
            } elseif ($kunciNomor && $jawabSiswa === $kunciNomor) {
                $benar++;
            } else {
                $salah++;
            }
        }

        // Nilai PG diskalakan ke bobot PG (default bobot 70 dari 100)
        $bobotPg = (float) ($this->bobot_pg ?: 70.00);
        $skorPg = $totalPg > 0 ? round(($benar / $totalPg) * $bobotPg, 2) : 0.00;

        return [
            'benar'   => $benar,
            'salah'   => $salah,
            'kosong'  => $kosong,
            'skor_pg' => $skorPg,
            'maks_pg' => $bobotPg,
        ];
    }
}
