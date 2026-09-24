<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\HariLibur;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class HariLiburService
{
    /**
     * Menetapkan status libur darurat / mendadak untuk hari ini (atau tanggal tertentu).
     * Secara otomatis membatalkan status Alpha siswa & guru, sinkronisasi kasus disiplin,
     * serta membatalkan draf notifikasi WA yang belum terkirim.
     *
     * @param string $namaLibur
     * @param string|null $keterangan
     * @param string|null $petugas
     * @param string|null $tanggal (format Y-m-d, default: hari ini)
     * @return array
     */
    public static function tetapkanLiburDarurat(
        string $namaLibur = 'Libur Khusus Sekolah',
        ?string $keterangan = null,
        ?string $petugas = null,
        ?string $tanggal = null
    ): array {
        $targetDate = $tanggal ?: Carbon::today()->toDateString();
        $petugasName = $petugas ?: (auth()->user()?->name ?? 'Petugas Piket / Admin');

        return DB::transaction(function () use ($namaLibur, $keterangan, $petugasName, $targetDate) {
            // 1. Simpan atau perbarui data HariLibur khusus hari ini
            $libur = HariLibur::where('tanggal_mulai', '<=', $targetDate)
                ->where('tanggal_selesai', '>=', $targetDate)
                ->first();

            if (!$libur) {
                $libur = HariLibur::create([
                    'nama_libur'      => $namaLibur,
                    'tanggal_mulai'   => $targetDate,
                    'tanggal_selesai' => $targetDate,
                    'jenis'           => 'khusus_sekolah',
                    'keterangan'      => $keterangan ?: 'Diliburkan mendadak oleh pihak sekolah',
                    'created_by'      => $petugasName,
                ]);
            } else {
                // Perbarui jika sebelumnya belum bertipe khusus_sekolah atau ingin memperbarui keterangan
                $libur->update([
                    'nama_libur' => $namaLibur,
                    'keterangan' => $keterangan ?: $libur->keterangan,
                ]);
            }

            // 2. Rollback seluruh status Alpha yang terlanjur tercatat pada tanggal tersebut
            $rollbackStats = self::rollbackAlphaHariLibur($targetDate);

            // 3. Catat ke Audit Log
            AuditLog::catat(
                'libur_darurat_ditetapkan',
                'piket',
                "Sekolah diliburkan mendadak pada {$targetDate} ({$namaLibur}) oleh {$petugasName}. " .
                "Hasil rollback: {$rollbackStats['siswa_alpha_dibatalkan']} Alpha siswa & {$rollbackStats['guru_alpha_dibatalkan']} Alpha guru dibatalkan."
            );

            return [
                'success'                 => true,
                'libur'                   => $libur,
                'siswa_alpha_dibatalkan'  => $rollbackStats['siswa_alpha_dibatalkan'],
                'guru_alpha_dibatalkan'   => $rollbackStats['guru_alpha_dibatalkan'],
                'notifikasi_dibatalkan'   => $rollbackStats['notifikasi_dibatalkan'],
            ];
        });
    }

    /**
     * Membatalkan seluruh absensi Alpha, mereset poin disiplin siswa, dan
     * membatalkan antrean notifikasi ortu pada tanggal atau rentang tanggal tertentu.
     *
     * @param string $tanggalMulai (Y-m-d)
     * @param string|null $tanggalSelesai (Y-m-d)
     * @return array
     */
    public static function rollbackAlphaHariLibur(string $tanggalMulai, ?string $tanggalSelesai = null): array
    {
        $startDate = Carbon::parse($tanggalMulai)->startOfDay();
        $endDate   = $tanggalSelesai ? Carbon::parse($tanggalSelesai)->endOfDay() : Carbon::parse($tanggalMulai)->endOfDay();

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->toDateString();
        }

        $totalSiswaAlpha = 0;
        $totalGuruAlpha  = 0;
        $totalNotifBatal = 0;
        $affectedSiswaIds = [];

        $tglAwal  = $startDate->toDateString();
        $tglAkhir = $endDate->toDateString();

        // 1. Cari semua record Absensi berstatus Alpha di tanggal-tanggal tersebut
        $alphaAbsensis = Absensi::where(function($q) use ($dates, $tglAwal, $tglAkhir) {
                $q->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                  ->orWhereIn('tanggal', $dates);
            })
            ->where('status', 'alpha')
            ->get();

        foreach ($alphaAbsensis as $absen) {
            if ($absen->pemilik_type === 'siswa') {
                $totalSiswaAlpha++;
                $affectedSiswaIds[] = $absen->pemilik_id;
            } elseif ($absen->pemilik_type === 'guru') {
                $totalGuruAlpha++;
            }
            $absen->delete();
        }

        // 2. Sinkronisasi ulang poin kedisiplinan siswa yang sempat terdampak Alpha
        $affectedSiswaIds = array_unique($affectedSiswaIds);
        foreach ($affectedSiswaIds as $siswaId) {
            try {
                KasusDisiplin::syncFromPresensi($siswaId);
            } catch (\Throwable $e) {
                // Jangan gagalkan proses jika ada edge case data siswa
            }
        }

        // 3. Batalkan draft/pending notifikasi WhatsApp ortu untuk kategori alpha/belum hadir
        try {
            $notifQuery = NotifikasiOrtu::where(function($q) use ($dates, $tglAwal, $tglAkhir) {
                    $q->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                      ->orWhere(function($sub) use ($dates) {
                          foreach ($dates as $d) {
                              $sub->orWhereDate('tanggal', $d);
                          }
                      });
                })
                ->whereIn('kategori', ['alpha', 'belum_hadir', 'sp1', 'sp2', 'panggilan_ortu'])
                ->whereIn('status', ['pending', 'diverifikasi']);

            $totalNotifBatal = $notifQuery->count();
            $notifQuery->delete();
        } catch (\Throwable $e) {
            // abaikan jika tabel belum ada atau error
        }

        return [
            'siswa_alpha_dibatalkan' => $totalSiswaAlpha,
            'guru_alpha_dibatalkan'  => $totalGuruAlpha,
            'notifikasi_dibatalkan'  => $totalNotifBatal,
        ];
    }

    /**
     * Membatalkan libur darurat yang sempat ditetapkan sebelumnya.
     *
     * @param int $id
     * @param string|null $petugas
     * @return bool
     */
    public static function batalkanLiburDarurat(int $id, ?string $petugas = null): bool
    {
        $libur = HariLibur::findOrFail($id);
        $namaLibur = $libur->nama_libur;
        $tanggalMulai = $libur->tanggal_mulai;
        $petugasName = $petugas ?: (auth()->user()?->name ?? 'Petugas Piket / Admin');

        $libur->delete();

        AuditLog::catat(
            'libur_darurat_dibatalkan',
            'piket',
            "Libur darurat/khusus ({$namaLibur}) pada tanggal {$tanggalMulai} dibatalkan oleh {$petugasName}."
        );

        return true;
    }
}
