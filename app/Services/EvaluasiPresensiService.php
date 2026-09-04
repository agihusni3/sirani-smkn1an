<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\KasusDisiplin;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluasiPresensiService
{
    /**
     * Evaluasi otomatis yang aman dipanggil kapan saja (on-access).
     * Mengevaluasi tanggal masa lalu atau hari ini jika sudah melewati batas jam kepulangan / sesi tutup.
     */
    public static function evaluasiOtomatisJikaWaktunya(?string $tanggal = null, bool $force = false): array
    {
        $targetDate = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $dateStr = $targetDate->toDateString();

        // 1. Jangan evaluasi hari libur / tanggal merah (kecuali dipaksa)
        if (!$force && HariLibur::isLibur($dateStr)) {
            return ['status' => 'skipped', 'message' => 'Hari Libur'];
        }

        // 2. Jika tanggal adalah hari ini
        if ($targetDate->isToday()) {
            $jadwal = JadwalHariIni::getJadwalAktif($dateStr);
            $nowStr = Carbon::now()->format('H:i:s');

            // Waktu evaluasi: jika dipaksa atau waktu sekarang sudah melewati jam tutup gerbang / jam 17:00
            $jamBatasEvaluasi = $jadwal->jam_tutup_gerbang ?: '17:00:00';
            $sudahWaktuEvaluasi = $force || ($nowStr >= $jamBatasEvaluasi);

            if (!$sudahWaktuEvaluasi) {
                return ['status' => 'skipped', 'message' => 'Belum masuk waktu evaluasi sore (setelah jam tutup sekolah ' . substr($jamBatasEvaluasi, 0, 5) . ')'];
            }

        } elseif ($targetDate->isFuture()) {
            return ['status' => 'skipped', 'message' => 'Tanggal masa depan'];
        }

        // 3. Jalankan evaluasi
        return self::jalankanEvaluasi($dateStr, $force);
    }

    /**
     * Eksekusi inti evaluasi status Alpha dan Bolos untuk tanggal tertentu.
     */
    public static function jalankanEvaluasi(string $tanggal, bool $force = false): array
    {
        $taAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::first();
        if (!$taAktif) {
            return ['status' => 'error', 'message' => 'Tahun Ajaran aktif tidak ditemukan'];
        }

        $activeMemberships = SiswaRombel::where('tahun_ajaran_id', $taAktif->id)
            ->where('status_keanggotaan', 'aktif')
            ->whereHas('siswa', function ($query) {
                $query->where('status', 'aktif');
            })
            ->get();

        $countAlpha = 0;
        $countBolos = 0;

        foreach ($activeMemberships as $membership) {
            try {
                DB::transaction(function () use ($membership, $tanggal, &$countAlpha, &$countBolos) {
                    $absensi = Absensi::where('pemilik_type', 'siswa')
                        ->where('pemilik_id', $membership->siswa_id)
                        ->where('tanggal', $tanggal)
                        ->first();

                    if (!$absensi) {
                        // 1. Siswa sama sekali tidak tap hadir & tanpa izin -> ALPHA
                        $adaIzin = IzinSiswa::where('siswa_id', $membership->siswa_id)
                            ->where('tanggal', $tanggal)
                            ->where('status', 'disetujui')
                            ->first();

                        if ($adaIzin) {
                            Absensi::create([
                                'pemilik_type' => 'siswa',
                                'pemilik_id' => $membership->siswa_id,
                                'siswa_rombel_id' => $membership->id,
                                'tanggal' => $tanggal,
                                'status' => in_array($adaIzin->jenis, ['sakit', 'izin', 'dispen']) ? $adaIzin->jenis : 'izin',
                                'sumber_absen' => 'auto_evaluasi_izin',
                            ]);
                        } else {
                            Absensi::create([
                                'pemilik_type' => 'siswa',
                                'pemilik_id' => $membership->siswa_id,
                                'siswa_rombel_id' => $membership->id,
                                'tanggal' => $tanggal,
                                'status' => 'alpha',
                                'sumber_absen' => 'auto_evaluasi_alpha',
                            ]);
                            $countAlpha++;

                            if ($membership->siswa) {
                                try {
                                    NotifikasiDraftService::buatDraft($membership->siswa, 'alpha', [
                                        'tanggal' => $tanggal,
                                        'jam'     => Carbon::now()->format('H:i'),
                                    ], 'sistem_cron');
                                    NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($membership->siswa, 'sistem_cron');
                                    KasusDisiplin::syncFromPresensi($membership->siswa_id);
                                } catch (\Throwable $e) {
                                    Log::warning("Gagal buat notif alpha: " . $e->getMessage());
                                }
                            }
                        }
                    } elseif (in_array($absensi->status, ['hadir', 'terlambat']) && is_null($absensi->jam_pulang)) {
                        // 2. Siswa tap masuk pagi, tapi tidak tap pulang -> BOLOS (Pulang Tanpa Izin)
                        $adaIzinPulang = IzinSiswa::where('siswa_id', $membership->siswa_id)
                            ->where('tanggal', $tanggal)
                            ->where('status', 'disetujui')
                            ->exists();

                        if (!$adaIzinPulang) {
                            $absensi->update([
                                'status'      => 'bolos',
                                'sumber_absen' => 'auto_evaluasi_bolos',
                            ]);
                            $countBolos++;

                            if ($membership->siswa) {
                                try {
                                    // Kirim WA otomatis langsung ke orang tua (tanpa perlu verifikasi admin)
                                    NotifikasiDraftService::kirimNotifikasiBolosOtomatis(
                                        $membership->siswa,
                                        $tanggal,
                                        'sistem_evaluasi'
                                    );
                                    // Cek akumulasi & buat panggilan ortu jika melewati batas
                                    NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($membership->siswa, 'sistem_cron');
                                    KasusDisiplin::syncFromPresensi($membership->siswa_id);
                                } catch (\Throwable $e) {
                                    Log::warning("Gagal kirim notif bolos: " . $e->getMessage());
                                }
                            }
                        }
                    }
                });
            } catch (\Throwable $e) {
                Log::error("Error evaluasi siswa {$membership->siswa_id}: " . $e->getMessage());
            }
        }

        return [
            'status' => 'success',
            'alpha_count' => $countAlpha,
            'bolos_count' => $countBolos,
            'message' => "Evaluasi tanggal {$tanggal} selesai: {$countAlpha} Alpha, {$countBolos} Bolos.",
        ];
    }
}
