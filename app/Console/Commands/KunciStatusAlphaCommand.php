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
    protected $signature = 'piket:kunci-alpha {tanggal?}';
    protected $description = 'Pukul 10:00 — kunci status siswa & guru yang belum hadir menjadi Alpha';

    public function handle()
    {
        $tanggal = $this->argument('tanggal') ?? Carbon::today()->toDateString();

        if (HariLibur::isLibur($tanggal)) {
            $this->info("Tanggal {$tanggal} adalah hari libur. Kunci Alpha dilewati.");
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
            ->get();

        foreach ($activeMemberships as $membership) {
            DB::transaction(function () use ($membership, $tanggal, &$countSiswaAlpha) {
                // Cek apakah sudah ada record absensi
                $absensi = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $membership->siswa_id)
                    ->where('tanggal', $tanggal)
                    ->first();

                if ($absensi) return; // Sudah ada catatan (hadir/izin/dll) — lewati

                // Cek izin yang sudah disetujui
                $adaIzin = IzinSiswa::where('siswa_id', $membership->siswa_id)
                    ->where('tanggal', $tanggal)
                    ->where('status', 'disetujui')
                    ->exists();

                if ($adaIzin) return; // Ada izin resmi — lewati

                // Kunci sebagai Alpha
                Absensi::create([
                    'pemilik_type'    => 'siswa',
                    'pemilik_id'      => $membership->siswa_id,
                    'siswa_rombel_id' => $membership->id,
                    'tanggal'         => $tanggal,
                    'status'          => 'alpha',
                    'sumber_absen'    => 'auto_kunci_piket',
                    'keterangan'      => 'Dikunci otomatis sistem pukul 10:00 — tanpa keterangan dari Guru Piket',
                ]);
                $countSiswaAlpha++;

                // Buat draf notifikasi Alpha
                try {
                    if ($membership->siswa) {
                        \App\Services\NotifikasiDraftService::buatDraft($membership->siswa, 'alpha', [
                            'tanggal' => $tanggal,
                            'jam'     => '10:00',
                        ], 'sistem_cron');

                        \App\Services\NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($membership->siswa, 'sistem_cron');
                        \App\Models\KasusDisiplin::syncFromPresensi($membership->siswa_id);
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            });
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

        foreach ($guruBelumHadir as $guru) {
            // Cek izin guru yang sudah disetujui
            $adaIzin = IzinGuru::where('guru_id', $guru->id)
                ->where('tanggal', $tanggal)
                ->where('status', 'disetujui')
                ->exists();

            if ($adaIzin) continue;

            Absensi::create([
                'pemilik_type' => 'guru',
                'pemilik_id'   => $guru->id,
                'tanggal'      => $tanggal,
                'status'       => 'alpha',
                'sumber_absen' => 'auto_kunci_piket',
                'keterangan'   => 'Dikunci otomatis sistem pukul 10:00 — tanpa keterangan dari Guru Piket',
            ]);
            $countGuruAlpha++;
        }

        $this->info("Kunci selesai: {$countSiswaAlpha} siswa & {$countGuruAlpha} guru dikunci sebagai Alpha.");
        return 0;
    }
}
