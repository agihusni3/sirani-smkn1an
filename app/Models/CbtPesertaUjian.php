<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtPesertaUjian extends Model
{
    use HasFactory;

    protected $table = 'cbt_peserta_ujians';

    protected $fillable = [
        'jadwal_ujian_id',
        'siswa_id',
        'rombel_id',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'nilai_pg',
        'nilai_esai',
        'nilai_akhir',
        'is_tuntas',
        'token_used',
        'ip_address',
        'user_agent',
        'jumlah_pelanggaran',
    ];

    protected $casts = [
        'waktu_mulai'        => 'datetime',
        'waktu_selesai'      => 'datetime',
        'nilai_pg'           => 'float',
        'nilai_esai'         => 'float',
        'nilai_akhir'        => 'float',
        'is_tuntas'          => 'boolean',
        'jumlah_pelanggaran' => 'integer',
    ];

    public function jadwal()
    {
        return $this->belongsTo(CbtJadwalUjian::class, 'jadwal_ujian_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function jawabans()
    {
        return $this->hasMany(CbtJawabanSiswa::class, 'peserta_ujian_id');
    }

    public function logAktivitas()
    {
        return $this->hasMany(CbtLogAktivitas::class, 'peserta_ujian_id')->orderBy('waktu_catat', 'desc');
    }

    /**
     * Hitung nilai otomatis & ketuntasan KKTP
     */
    public function hitungNilaiOtomatis(): void
    {
        $soals = $this->jadwal->bankSoal->soals;
        $totalBobot = $soals->sum('bobot_nilai') ?: 1.0;

        $totalSkorDidapat = 0.0;
        $totalBobotPg = 0.0;
        $totalSkorPg = 0.0;
        $totalBobotEsai = 0.0;
        $totalSkorEsai = 0.0;

        $jawabanMap = $this->jawabans->keyBy('soal_id');

        foreach ($soals as $soal) {
            $jwb = $jawabanMap->get($soal->id);
            if (!$jwb) continue;

            if ($soal->jenis_soal === 'pg') {
                $totalBobotPg += $soal->bobot_nilai;
                $benar = (strtoupper(trim($jwb->jawaban_siswa ?? '')) === strtoupper(trim($soal->kunci_jawaban ?? '')));
                $skor = $benar ? (float)$soal->bobot_nilai : 0.0;
                $jwb->update([
                    'is_benar' => $benar,
                    'skor'     => $skor,
                ]);
                $totalSkorPg += $skor;
                $totalSkorDidapat += $skor;
            } elseif ($soal->jenis_soal === 'esai') {
                $totalBobotEsai += $soal->bobot_nilai;
                $totalSkorEsai += (float)$jwb->skor;
                $totalSkorDidapat += (float)$jwb->skor;
            }
        }

        $persenPg = $totalBobotPg > 0 ? round(($totalSkorPg / $totalBobotPg) * 100, 2) : 0;
        $persenEsai = $totalBobotEsai > 0 ? round(($totalSkorEsai / $totalBobotEsai) * 100, 2) : null;
        $nilaiMurni = round(($totalSkorDidapat / $totalBobot) * 100, 2);

        $nilaiFinal = $nilaiMurni;
        $jadwal = $this->jadwal;

        // Penanganan Kebijakan Remedial
        if ($jadwal->is_remedial && $jadwal->parent_jadwal_id) {
            $pesertaSebelumnya = self::where('jadwal_ujian_id', $jadwal->parent_jadwal_id)
                ->where('siswa_id', $this->siswa_id)
                ->first();
            $nilaiLama = $pesertaSebelumnya ? (float)$pesertaSebelumnya->nilai_akhir : 0;

            switch ($jadwal->remedial_policy) {
                case 'cap_kktp':
                    $nilaiFinal = ($nilaiMurni > $jadwal->kktp) ? (float)$jadwal->kktp : $nilaiMurni;
                    break;
                case 'nilai_tertinggi':
                    $nilaiFinal = max($nilaiLama, $nilaiMurni);
                    break;
                case 'rata_rata':
                    $nilaiFinal = round(($nilaiLama + $nilaiMurni) / 2, 2);
                    break;
                case 'murni':
                default:
                    $nilaiFinal = $nilaiMurni;
                    break;
            }
        }

        $isTuntas = ($nilaiFinal >= $jadwal->kktp);

        $this->update([
            'nilai_pg'    => $persenPg,
            'nilai_esai'  => $persenEsai,
            'nilai_akhir' => $nilaiFinal,
            'is_tuntas'   => $isTuntas,
        ]);
    }
}
