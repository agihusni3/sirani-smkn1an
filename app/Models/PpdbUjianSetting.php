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
        'jumlah_soal_pg',
        'jumlah_soal_esai',
        'kunci_jawaban_pg',  // dipertahankan untuk backward compat, tapi kunci utama sekarang di tiap soal
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

    public function soals()
    {
        return $this->hasMany(PpdbSoalUjian::class, 'ppdb_ujian_setting_id')->orderBy('nomor_urut');
    }

    public function soalPg()
    {
        return $this->hasMany(PpdbSoalUjian::class, 'ppdb_ujian_setting_id')
                    ->where('tipe_soal', 'pg')->orderBy('nomor_urut');
    }

    public function soalEsai()
    {
        return $this->hasMany(PpdbSoalUjian::class, 'ppdb_ujian_setting_id')
                    ->where('tipe_soal', 'esai')->orderBy('nomor_urut');
    }

    /** Dapatkan setting ujian aktif saat ini */
    public static function getAktif(): ?self
    {
        $setting = self::where('is_active', true)->latest()->first();
        if (!$setting) {
            $setting = self::latest()->first();
        }
        return $setting;
    }

    /**
     * Hitung koreksi otomatis jawaban PG peserta.
     * Jawaban siswa: ['nomor_urut' => 'A', ...] (key = nomor_urut asli soal)
     */
    public function koreksiPg(array $jawabanSiswa): array
    {
        // Ambil soal PG beserta kunci dari bank soal
        $soalPg = $this->soalPg()->get()->keyBy('nomor_urut');

        $benar  = 0;
        $salah  = 0;
        $kosong = 0;
        $totalPg = max($soalPg->count(), (int) ($this->jumlah_soal_pg ?: 30));

        foreach ($soalPg as $nomor => $soal) {
            $kunci = $soal->kunci_jawaban ? strtoupper(trim($soal->kunci_jawaban)) : null;
            $jawab = null;
            if (isset($jawabanSiswa[(string) $soal->id])) {
                $jawab = strtoupper(trim($jawabanSiswa[(string) $soal->id]));
            } elseif (isset($jawabanSiswa[(string) $nomor])) {
                $jawab = strtoupper(trim($jawabanSiswa[(string) $nomor]));
            }

            if (empty($jawab)) {
                $kosong++;
            } elseif ($kunci && $jawab === $kunci) {
                $benar++;
            } else {
                $salah++;
            }
        }

        // Fallback ke kunci lama jika bank soal kosong
        if ($soalPg->isEmpty()) {
            return $this->koreksiPgLegacy($jawabanSiswa);
        }

        $bobotPg = (float) ($this->bobot_pg ?: 70.00);
        $skorPg  = $totalPg > 0 ? round(($benar / $totalPg) * $bobotPg, 2) : 0.00;

        return [
            'benar'   => $benar,
            'salah'   => $salah,
            'kosong'  => $kosong,
            'skor_pg' => $skorPg,
            'maks_pg' => $bobotPg,
        ];
    }

    /** Fallback koreksi PG dari kolom kunci_jawaban_pg lama */
    protected function koreksiPgLegacy(array $jawabanSiswa): array
    {
        $kunci   = $this->kunci_jawaban_pg ?: [];
        $totalPg = (int) ($this->jumlah_soal_pg ?: 30);
        $benar = $salah = $kosong = 0;

        for ($i = 1; $i <= $totalPg; $i++) {
            $k = isset($kunci[(string) $i]) ? strtoupper(trim($kunci[(string) $i])) : null;
            $j = isset($jawabanSiswa[(string) $i]) ? strtoupper(trim($jawabanSiswa[(string) $i])) : null;
            if (empty($j)) $kosong++;
            elseif ($k && $j === $k) $benar++;
            else $salah++;
        }

        $bobotPg = (float) ($this->bobot_pg ?: 70.00);
        return [
            'benar'   => $benar,
            'salah'   => $salah,
            'kosong'  => $kosong,
            'skor_pg' => $totalPg > 0 ? round(($benar / $totalPg) * $bobotPg, 2) : 0,
            'maks_pg' => $bobotPg,
        ];
    }
}
