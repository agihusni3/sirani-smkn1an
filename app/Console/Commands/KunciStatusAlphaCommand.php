<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KunciStatusAlphaCommand extends Command
{
    protected $signature = 'piket:kunci-alpha {tanggal?} {--force}';
    protected $description = 'Pukul 09:00 — kunci status siswa & guru yang belum hadir menjadi Alpha';

    public function handle()
    {
        $tanggal = $this->argument('tanggal') ?? Carbon::today()->toDateString();
        $force = (bool) $this->option('force');

        if (HariLibur::isLibur($tanggal)) {
            $this->info("Tanggal {$tanggal} adalah hari libur. Kunci Alpha dilewati.");
            return 0;
        }

        $cacheKey = 'piket_kunci_alpha_ran_' . $tanggal;
        if (!$force && \Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $this->info("Kunci status Alpha untuk {$tanggal} sudah pernah dijalankan hari ini.");
            return 0;
        }

        $this->info("Menjalankan kunci status Alpha untuk: {$tanggal}");

        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (!$taAktif) {
            $this->error("Tidak ada Tahun Ajaran aktif.");
            return 1;
        }

        $countSiswaAlpha = 0;
        $countGuruAlpha  = 0;

        // ── Kunci Siswa ───────────────────────────────────────────────────────
        $activeMemberships = SiswaRombel::where('tahun_ajaran_id', $taAktif->id)
            ->where('status_keanggotaan', 'aktif')
            ->whereHas('siswa', fn($q) => $q->where('status', 'aktif'))
            ->with(['siswa:id,nama,nisn,status', 'rombel:id,nama_rombel'])
            ->get();

        // Bulk lookup ID yang sudah memiliki catatan absensi atau izin resmi
        $existingSiswaAbsensiMap = Absensi::where('pemilik_type', 'siswa')
            ->where('tanggal', $tanggal)
            ->pluck('pemilik_id')
            ->flip()
            ->all();

        $existingIzinSiswaMap = IzinSiswa::where('tanggal', $tanggal)
            ->where('status', 'disetujui')
            ->pluck('siswa_id')
            ->flip()
            ->all();

        foreach ($activeMemberships as $membership) {
            // Sudah ada catatan absensi atau izin resmi — lewati langsung tanpa query
            if (isset($existingSiswaAbsensiMap[$membership->siswa_id]) || isset($existingIzinSiswaMap[$membership->siswa_id])) {
                continue;
            }

            try {
                DB::transaction(function () use ($membership, $tanggal, &$countSiswaAlpha) {
                    Absensi::create([
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $membership->siswa_id,
                        'siswa_rombel_id' => $membership->id,
                        'tanggal'         => $tanggal,
                        'status'          => 'alpha',
                        'sumber_absen'    => 'auto_kunci_piket',
                        'keterangan'      => 'Dikunci otomatis sistem pukul 09:00 — tanpa keterangan dari Guru Piket',
                    ]);
                    $countSiswaAlpha++;

                    if ($membership->siswa) {
                        try {
                            \App\Services\NotifikasiDraftService::buatDraft($membership->siswa, 'alpha', [
                                'tanggal' => $tanggal,
                                'jam'     => '09:00',
                            ], 'sistem_cron');

                            \App\Services\NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($membership->siswa, 'sistem_cron');
                            \App\Models\KasusDisiplin::syncFromPresensi($membership->siswa_id);

                            if (!empty($membership->siswa->nisn)) {
                                $kelas = $membership->rombel->nama_rombel ?? 'Siswa';
                                \App\Services\PushNotificationService::sendToSiswa(
                                    $membership->siswa->nisn,
                                    '🚨 Tidak Hadir (Alpha): ' . $membership->siswa->nama,
                                    "Hingga pukul 09:00 WIB, ananda ({$kelas}) belum hadir di sekolah tanpa keterangan (Alpha). Ketuk untuk cek riwayat absensi.",
                                    '/presensi-siswa/' . urlencode($membership->siswa->nisn)
                                );
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }
                    }
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // ── Kunci Guru ────────────────────────────────────────────────────────
        $hariIniIndo = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd');
        $absensiGuruIds = Absensi::where('pemilik_type', 'guru')
            ->where('tanggal', $tanggal)
            ->pluck('pemilik_id');

        $guruBelumHadir = Guru::where('status', 'aktif')
            ->whereNotIn('id', $absensiGuruIds)
            ->get()
            ->filter(fn($g) => $g->isWajibHadirHari($hariIniIndo));

        $existingIzinGuruMap = IzinGuru::where('tanggal', $tanggal)
            ->where('status', 'disetujui')
            ->pluck('guru_id')
            ->flip()
            ->all();

        foreach ($guruBelumHadir as $guru) {
            if (isset($existingIzinGuruMap[$guru->id])) continue;

            try {
                Absensi::create([
                    'pemilik_type' => 'guru',
                    'pemilik_id'   => $guru->id,
                    'tanggal'      => $tanggal,
                    'status'       => 'alpha',
                    'sumber_absen' => 'auto_kunci_piket',
                    'keterangan'   => 'Dikunci otomatis sistem pukul 09:00 — tanpa keterangan dari Guru Piket',
                ]);
                $countGuruAlpha++;
            } catch (\Throwable $e) {
                // ignore
            }
        }

        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->endOfDay());

        $this->info("Kunci selesai: {$countSiswaAlpha} siswa & {$countGuruAlpha} guru dikunci sebagai Alpha.");
        return 0;
    }
}
