<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SimulasiAbsensiGuruSeeder
 *
 * Simulasi absensi 17 guru selama 1 tahun penuh:
 * Tahun Ajaran 2025/2026 → Agustus 2025 – Juli 2026
 *
 * Profil guru:
 * - Kepala Sekolah  : sangat disiplin, hadir >95%
 * - PNS             : disiplin tinggi, hadir ~92%
 * - PPPK            : variasi: rajin, biasa, kadang sakit/izin
 *
 * Status yang digunakan:
 * hadir | terlambat | izin | sakit | alpha (dinas luar = izin dinas)
 */
class SimulasiAbsensiGuruSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🏫 SIMULASI ABSENSI GURU — 1 TAHUN PENUH');
        $this->command->info('   Agustus 2025 – Juli 2026 (TA 2025/2026)');
        $this->command->info('');

        DB::statement('PRAGMA journal_mode=WAL;');
        DB::statement('PRAGMA synchronous=OFF;');

        // Hapus absensi guru lama
        $deleted = DB::table('absensis')->where('pemilik_type', 'guru')->delete();
        DB::table('izin_gurus')->delete();
        $this->command->info("   🗑  Hapus {$deleted} absensi guru lama.");

        // ── Kumpulkan hari kerja ──────────────────────────────────────────
        $hariLibur = [
            '2025-08-17', '2025-09-05',
            '2025-12-25', '2025-12-26',
            '2026-01-01', '2026-01-29',
            '2026-03-20', '2026-03-27',
            '2026-04-02', '2026-04-03',
            '2026-05-01', '2026-05-14',
            '2026-05-26', '2026-06-01',
        ];
        $liburSemester = array_merge(
            array_map(fn($d) => "2025-12-{$d}", range(22, 31)),
            array_map(fn($d) => sprintf('2026-01-%02d', $d), range(1, 7))
        );
        $semuaLibur = array_merge($hariLibur, $liburSemester);

        $hariKerja = [];
        $tgl = Carbon::create(2025, 8, 1);
        while ($tgl->lte(Carbon::create(2026, 7, 31))) {
            if (!in_array($tgl->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])
                && !in_array($tgl->toDateString(), $semuaLibur)) {
                $hariKerja[] = $tgl->toDateString();
            }
            $tgl->addDay();
        }

        $totalHariKerja = count($hariKerja);
        $this->command->info("   📆 Hari kerja: {$totalHariKerja} hari");

        // ── Profil per guru ───────────────────────────────────────────────
        $gurus = DB::table('gurus')->where('status', 'aktif')->get();
        $this->command->info("   👩‍🏫 Guru aktif: {$gurus->count()} orang");
        $this->command->info('');

        /**
         * Peluang status [hadir, terlambat, izin, sakit, dinas_luar, alpha]
         * dinas_luar → disimpan sbg status='izin' + keterangan='Dinas luar/rapat'
         */
        $profilPeluang = [
            'kepsek'  => [93, 2,  2, 2,  1, 0],
            'pns'     => [88, 3,  3, 4,  2, 0],
            'pppk'    => [82, 6,  5, 5,  2, 0],
            'honorer' => [75, 8,  7, 6,  2, 2],
        ];

        $keteranganIzin   = ['Keperluan keluarga', 'Urusan administrasi', 'Izin mendampingi kegiatan lomba', 'Keperluan pribadi mendesak'];
        $keteranganSakit  = ['Sakit demam', 'Sakit lambung', 'Rawat inap keluarga', 'Tidak sehat - konfirmasi via WA'];
        $keteranganDinas  = ['Rapat Dinas Kabupaten', 'Pelatihan Guru di Dinas', 'Workshop Kurikulum Merdeka', 'Bimtek K13', 'Kunjungan Dinas ke Provinsi'];

        $batch        = [];
        $izinBatch    = [];
        $batchSize    = 2000;
        $totalDibuat  = 0;

        $bar = $this->command->getOutput()->createProgressBar($gurus->count());
        $bar->setFormat(' %current%/%max% guru [%bar%] %percent:3s%% | ⏱ %elapsed:6s%');
        $bar->start();

        foreach ($gurus as $guru) {
            // Tentukan profil
            if ($guru->jabatan === 'Kepala Sekolah') {
                $profil = 'kepsek';
            } elseif ($guru->jenis_kepegawaian === 'pns') {
                $profil = 'pns';
            } elseif ($guru->jenis_kepegawaian === 'pppk') {
                $profil = 'pppk';
            } else {
                $profil = 'honorer';
            }

            $peluang = $profilPeluang[$profil];
            $labels  = ['hadir', 'terlambat', 'izin', 'sakit', 'dinas_luar', 'alpha'];

            foreach ($hariKerja as $tanggal) {
                $seed   = abs(crc32($tanggal . '-guru-' . $guru->id)) % 100;
                $status = 'hadir';
                $cum    = 0;
                foreach ($peluang as $idx => $pct) {
                    $cum += $pct;
                    if ($seed < $cum) {
                        $status = $labels[$idx];
                        break;
                    }
                }

                $jamMasuk   = null;
                $jamPulang  = null;
                $keterangan = null;
                $sumber     = 'rfid';

                switch ($status) {
                    case 'hadir':
                        // Guru PNS/kepsek lebih disiplin jam masuk
                        $baseMin = ($profil === 'kepsek') ? 6 * 60 + 30 : 6 * 60 + 45;
                        $varMin  = ($profil === 'kepsek') ? 20 : 30;
                        $m = ($guru->id * 7 + abs(crc32($tanggal))) % $varMin + $baseMin;
                        $jamMasuk  = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
                        $p = ($guru->id * 3 + abs(crc32($tanggal))) % 60 + 15 * 60 + 30;
                        $jamPulang = sprintf('%02d:%02d:00', intdiv($p, 60), $p % 60);
                        $sumber    = ($guru->id % 2 === 0) ? 'face' : 'rfid';
                        break;

                    case 'terlambat':
                        $m = ($guru->id * 11 + abs(crc32($tanggal))) % 75 + 7 * 60 + 16;
                        $jamMasuk  = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
                        $p = ($guru->id * 5 + abs(crc32($tanggal))) % 60 + 15 * 60 + 30;
                        $jamPulang = sprintf('%02d:%02d:00', intdiv($p, 60), $p % 60);
                        $keterangan = 'Terlambat ' . ($m - (7 * 60 + 15)) . ' menit';
                        $sumber = ($guru->id % 2 === 0) ? 'face' : 'rfid';
                        break;

                    case 'izin':
                        $keterangan = $keteranganIzin[abs(crc32($tanggal . $guru->id)) % count($keteranganIzin)];
                        $izinBatch[] = [
                            'guru_id'       => $guru->id,
                            'tanggal'       => $tanggal,
                            'jenis'         => 'izin',
                            'status'        => 'disetujui',
                            'keterangan'    => $keterangan,
                            'disetujui_oleh' => 'Kepala Sekolah',
                            'file_pendukung' => null,
                            'created_at'    => $tanggal . ' 07:00:00',
                            'updated_at'    => $tanggal . ' 07:00:00',
                        ];
                        break;

                    case 'sakit':
                        $keterangan = $keteranganSakit[abs(crc32($tanggal . $guru->id)) % count($keteranganSakit)];
                        $izinBatch[] = [
                            'guru_id'       => $guru->id,
                            'tanggal'       => $tanggal,
                            'jenis'         => 'sakit',
                            'status'        => 'disetujui',
                            'keterangan'    => $keterangan,
                            'disetujui_oleh' => 'Kepala Sekolah',
                            'file_pendukung' => null,
                            'created_at'    => $tanggal . ' 07:00:00',
                            'updated_at'    => $tanggal . ' 07:00:00',
                        ];
                        break;

                    case 'dinas_luar':
                        $keterangan = $keteranganDinas[abs(crc32($tanggal . $guru->id)) % count($keteranganDinas)];
                        $status = 'izin'; // simpan sebagai izin dinas di absensi
                        $izinBatch[] = [
                            'guru_id'       => $guru->id,
                            'tanggal'       => $tanggal,
                            'jenis'         => 'dinas_luar',
                            'status'        => 'disetujui',
                            'keterangan'    => $keterangan,
                            'disetujui_oleh' => 'Kepala Sekolah',
                            'file_pendukung' => null,
                            'created_at'    => $tanggal . ' 07:00:00',
                            'updated_at'    => $tanggal . ' 07:00:00',
                        ];
                        break;

                    case 'alpha':
                        $keterangan = 'Tidak hadir tanpa keterangan';
                        break;
                }

                $createdAt = $jamMasuk ? $tanggal . ' ' . $jamMasuk : $tanggal . ' 13:00:00';

                $batch[] = [
                    'pemilik_type'    => 'guru',
                    'pemilik_id'      => $guru->id,
                    'siswa_rombel_id' => null,
                    'tanggal'         => $tanggal,
                    'jam_masuk'       => $jamMasuk,
                    'jam_pulang'      => $jamPulang,
                    'status'          => $status,
                    'sumber_absen'    => in_array($status, ['izin', 'sakit', 'alpha']) ? 'sistem' : $sumber,
                    'keterangan'      => $keterangan,
                    'created_at'      => $createdAt,
                    'updated_at'      => $jamPulang ? $tanggal . ' ' . $jamPulang : $createdAt,
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('absensis')->insert($batch);
                    $totalDibuat += count($batch);
                    $batch = [];
                }
            }

            // Flush izin per guru agar tidak terlalu besar
            if (!empty($izinBatch)) {
                DB::table('izin_gurus')->insert($izinBatch);
                $izinBatch = [];
            }

            $bar->advance();
        }

        // Flush sisa batch
        if (!empty($batch)) {
            DB::table('absensis')->insert($batch);
            $totalDibuat += count($batch);
        }
        if (!empty($izinBatch)) {
            DB::table('izin_gurus')->insert($izinBatch);
        }

        $bar->finish();
        $this->command->newLine(2);

        // ── Statistik akhir ────────────────────────────────────────────────
        $stats = DB::table('absensis')
            ->where('pemilik_type', 'guru')
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->get();

        $totalIzinGuru = DB::table('izin_gurus')->count();

        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║       SIMULASI ABSENSI GURU SELESAI! 🎉          ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info(sprintf('║  Guru             : %-5d                        ║', $gurus->count()));
        $this->command->info(sprintf('║  Hari kerja       : %-5d hari                   ║', $totalHariKerja));
        $this->command->info(sprintf('║  Absensi dibuat   : %-10s                 ║', number_format($totalDibuat)));
        $this->command->info(sprintf('║  Izin/Sakit/Dinas : %-5d record                 ║', $totalIzinGuru));
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  DISTRIBUSI STATUS:                              ║');

        $icons = ['hadir' => '✅', 'terlambat' => '⏰', 'izin' => '📝', 'sakit' => '🩺', 'alpha' => '❌'];
        foreach ($stats as $s) {
            $icon = $icons[$s->status] ?? '❓';
            $pct  = round($s->c / max($totalDibuat, 1) * 100, 1);
            $this->command->info(sprintf(
                '║  %s %-12s : %6s (%5s%%)                ║',
                $icon, strtoupper($s->status), number_format($s->c), $pct
            ));
        }

        $this->command->info('╠══════════════════════════════════════════════════╣');
        $totalDB = DB::table('absensis')->count();
        $this->command->info(sprintf('║  Total absensi DB : %-10s                 ║', number_format($totalDB)));
        $dbSize = round(filesize(database_path('database.sqlite')) / 1024 / 1024, 2);
        $this->command->info(sprintf('║  Ukuran DB SQLite : %-6s MB                    ║', $dbSize));
        $this->command->info('╚══════════════════════════════════════════════════╝');
    }
}
