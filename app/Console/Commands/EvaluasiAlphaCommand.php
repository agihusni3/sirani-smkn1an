<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\IzinSiswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EvaluasiAlphaCommand extends Command
{
    protected $signature = 'absensi:evaluasi-alpha {tanggal?}';
    protected $description = 'Mengevaluasi siswa aktif yang belum hadir (Alpha) dan siswa bolos (tidak tap pulang tanpa izin)';

    public function handle()
    {
        $tanggal = $this->argument('tanggal') ?? Carbon::today()->toDateString();

        // Cek apakah tanggal ini adalah hari libur (Weekend / Tanggal Merah / Cuti Terdaftar)
        if (HariLibur::isLibur($tanggal)) {
            $libur = HariLibur::getLiburHariIni($tanggal);
            $namaLibur = $libur ? $libur->nama_libur : (HariLibur::isWeekend($tanggal) ? 'Akhir Pekan (Sabtu/Minggu)' : 'Hari Libur');
            $this->info("Tanggal {$tanggal} adalah {$namaLibur}. Evaluasi kehadiran & alpha otomatis DILEWATI (SKIP).");
            return 0;
        }

        $this->info("Menjalankan evaluasi kehadiran & deteksi bolos otomatis untuk tanggal: {$tanggal}");

        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (!$taAktif) {
            $this->error("Tidak ada Tahun Ajaran aktif.");
            return 1;
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
            DB::transaction(function () use ($membership, $tanggal, &$countAlpha, &$countBolos) {
                $absensi = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $membership->siswa_id)
                    ->where('tanggal', $tanggal)
                    ->first();

                if (!$absensi) {
                    // 1. Siswa sama sekali tidak hadir & tanpa izin -> Alpha
                    Absensi::create([
                        'pemilik_type' => 'siswa',
                        'pemilik_id' => $membership->siswa_id,
                        'siswa_rombel_id' => $membership->id,
                        'tanggal' => $tanggal,
                        'status' => 'alpha',
                        'sumber_absen' => 'auto_evaluasi_alpha',
                    ]);
                    $countAlpha++;

                    // Buat draf notifikasi Alpha untuk diverifikasi
                    try {
                        if ($membership->siswa) {
                            \App\Services\NotifikasiDraftService::buatDraft($membership->siswa, 'alpha', [
                                'tanggal' => $tanggal,
                                'jam'     => Carbon::now()->format('H:i'),
                            ], 'sistem_cron');

                            // Cek apakah akumulasi Alpha/Bolos telah mencapai 3x untuk panggilan orang tua
                            \App\Services\NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($membership->siswa, 'sistem_cron');

                            // Sinkronisasi otomatis ke Buku Kasus Disiplin
                            \App\Models\KasusDisiplin::syncFromPresensi($membership->siswa_id);
                        }
                    } catch (\Throwable $e) {
                        // ignore/log
                    }
                } elseif (in_array($absensi->status, ['hadir', 'terlambat']) && is_null($absensi->jam_pulang)) {
                    // 2. Siswa tap masuk pagi, tapi tidak tap pulang sampai evaluasi sore
                    // Cek apakah ada izin pulang cepat/dispensasi yang disetujui
                    $adaIzin = IzinSiswa::where('siswa_id', $membership->siswa_id)
                        ->where('tanggal', $tanggal)
                        ->where('status', 'disetujui')
                        ->exists();

                    if (!$adaIzin) {
                        $absensi->update([
                            'status' => 'bolos',
                            'sumber_absen' => 'auto_evaluasi_bolos',
                        ]);
                        $countBolos++;

                        // Buat draf notifikasi Bolos untuk diverifikasi
                        try {
                            if ($membership->siswa) {
                                \App\Services\NotifikasiDraftService::buatDraft($membership->siswa, 'bolos', [
                                    'tanggal' => $tanggal,
                                    'jam'     => Carbon::now()->format('H:i'),
                                ], 'sistem_cron');

                                // Cek apakah akumulasi Alpha/Bolos telah mencapai 3x untuk panggilan orang tua
                                \App\Services\NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($membership->siswa, 'sistem_cron');

                                // Sinkronisasi otomatis ke Buku Kasus Disiplin
                                \App\Models\KasusDisiplin::syncFromPresensi($membership->siswa_id);
                            }
                        } catch (\Throwable $e) {
                            // ignore/log
                        }
                    }
                }
            });
        }

        $this->info("Evaluasi selesai: {$countAlpha} siswa Alpha, {$countBolos} siswa ditandai Bolos.");
        return 0;
    }
}
