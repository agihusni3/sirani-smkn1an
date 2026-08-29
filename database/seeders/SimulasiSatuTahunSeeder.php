<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimulasiSatuTahunSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧹 Memulai Pembersihan Data Absensi & Simulasi Ulang 1 Tahun Kebelakang...');

        DB::statement('PRAGMA journal_mode=WAL;');
        DB::statement('PRAGMA synchronous=OFF;');
        DB::statement('PRAGMA cache_size=10000;');

        // 1. CHUNK/DELETE SEMUA DATA ABSENSI LAMA
        DB::table('absensis')->truncate();
        DB::table('izin_siswas')->truncate();
        DB::table('izin_gurus')->truncate();
        DB::table('notifikasi_ortus')->truncate();
        DB::table('kasus_disiplin_dokumens')->truncate();
        DB::table('kasus_disiplins')->truncate();

        $this->command->info('✅ Seluruh data absensi, izin, notifikasi, dan kasus disiplin lama telah DIBERSIHKAN.');

        // 2. HITUNG HARI KERJA (2025-08-29 s.d. 2026-08-29)
        $endDate = Carbon::create(2026, 8, 29);
        $startDate = Carbon::create(2025, 8, 29);

        $hariLibur = [
            '2025-08-17', '2025-09-05', '2025-10-24',
            '2025-12-25', '2025-12-26',
            '2026-01-01', '2026-01-29',
            '2026-03-20', '2026-03-27',
            '2026-04-02', '2026-04-03',
            '2026-05-01', '2026-05-14', '2026-05-26', '2026-06-01', '2026-08-17'
        ];
        $liburSemester = array_merge(
            array_map(fn($d) => "2025-12-{$d}", range(22, 31)),
            array_map(fn($d) => sprintf('2026-01-%02d', $d), range(1, 7)),
            array_map(fn($d) => sprintf('2026-06-%02d', $d), range(22, 30)),
            array_map(fn($d) => sprintf('2026-07-%02d', $d), range(1, 10))
        );
        $semuaLibur = array_merge($hariLibur, $liburSemester);

        $hariKerja = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            if (!$curr->isWeekend() && !in_array($curr->toDateString(), $semuaLibur)) {
                $hariKerja[] = $curr->toDateString();
            }
            $curr->addDay();
        }

        $this->command->info("📅 Rentang Simulasi: " . $startDate->toDateString() . " s/d " . $endDate->toDateString() . " (" . count($hariKerja) . " Hari Efektif Sekolah)");

        // 3. GENERATE ABSENSI SISWA (526 Siswa)
        $siswas = DB::table('siswas')
            ->whereIn('status', ['aktif', 'pkl'])
            ->select('id', 'nis', 'nama', 'status')
            ->get();

        $siswaRombelMap = DB::table('siswa_rombels')
            ->where('status_keanggotaan', 'aktif')
            ->pluck('rombel_id', 'siswa_id')
            ->toArray();

        $this->command->info("👨‍🎓 Memproses " . count($siswas) . " Siswa...");

        $batchAbsensi = [];
        $batchIzin = [];
        $now = now()->toDateTimeString();

        $alasanIzin = ['Keperluan Keluarga', 'Acara Adat Pernikahan', 'Bepergian Luar Kota', 'Keperluan Mendesak'];
        $alasanSakit = ['Demam & Flu', 'Sakit Kepala Berat', 'Rawat Jalan Puskesmas', 'Sakit Lambung', 'Tipes'];

        foreach ($siswas as $idx => $s) {
            $rombelId = $siswaRombelMap[$s->id] ?? null;
            $isPkl = ($s->status === 'pkl');

            // Karakter kedisiplinan siswa (distribusi realistis)
            $randKarakter = rand(1, 100);
            if ($randKarakter <= 70) {
                // Sangat Rajin (Hadir 88%, Telat 7%, Izin 3%, Sakit 1%, Alpha 1%)
                $p = [88, 7, 3, 1, 1, 0];
            } elseif ($randKarakter <= 90) {
                // Sedang (Hadir 78%, Telat 12%, Izin 5%, Sakit 2%, Alpha 2%, Bolos 1%)
                $p = [78, 12, 5, 2, 2, 1];
            } else {
                // Sering Bermasalah (Hadir 60%, Telat 20%, Izin 5%, Sakit 5%, Alpha 5%, Bolos 5%)
                $p = [60, 20, 5, 5, 5, 5];
            }

            foreach ($hariKerja as $tglStr) {
                if ($isPkl) {
                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s->id,
                        'siswa_rombel_id' => $rombelId,
                        'tanggal'         => $tglStr,
                        'jam_masuk'       => '07:30:00',
                        'jam_pulang'      => '16:00:00',
                        'status'          => 'hadir',
                        'sumber_absen'    => 'pkl_otomatis',
                        'keterangan'      => 'Siswa Kegiatan PKL / Magang',
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                    continue;
                }

                $roll = rand(1, 100);
                if ($roll <= $p[0]) {
                    // HADIR TEPAT
                    $m = rand(40, 59);
                    $jamMasuk = sprintf('06:%02d:00', $m);
                    $jamPulang = sprintf('15:%02d:00', rand(30, 55));
                    $status = 'hadir';
                    $ket = null;
                } elseif ($roll <= $p[0] + $p[1]) {
                    // TERLAMBAT
                    $m = rand(16, 45);
                    $jamMasuk = sprintf('07:%02d:00', $m);
                    $jamPulang = sprintf('15:%02d:00', rand(30, 55));
                    $status = 'terlambat';
                    $ket = 'Terlambat ' . ($m - 15) . ' menit';
                } elseif ($roll <= $p[0] + $p[1] + $p[2]) {
                    // IZIN
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'izin';
                    $ket = $alasanIzin[array_rand($alasanIzin)];

                    $batchIzin[] = [
                        'siswa_id'       => $s->id,
                        'tanggal'        => $tglStr,
                        'jenis'          => 'izin',
                        'keterangan'     => $ket,
                        'file_pendukung' => null,
                        'status'         => 'disetujui',
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                } elseif ($roll <= $p[0] + $p[1] + $p[2] + $p[3]) {
                    // SAKIT
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'sakit';
                    $ket = $alasanSakit[array_rand($alasanSakit)];

                    $batchIzin[] = [
                        'siswa_id'       => $s->id,
                        'tanggal'        => $tglStr,
                        'jenis'          => 'sakit',
                        'keterangan'     => $ket,
                        'file_pendukung' => null,
                        'status'         => 'disetujui',
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                } elseif ($roll <= $p[0] + $p[1] + $p[2] + $p[3] + $p[4]) {
                    // ALPHA
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'alpha';
                    $ket = 'Tanpa Keterangan (Alpha)';
                } else {
                    // BOLOS
                    $jamMasuk = sprintf('07:%02d:00', rand(10, 30));
                    $jamPulang = null;
                    $status = 'bolos';
                    $ket = 'Meninggalkan Sekolah Tanpa Izin (Bolos)';
                }

                $batchAbsensi[] = [
                    'pemilik_type'    => 'siswa',
                    'pemilik_id'      => $s->id,
                    'siswa_rombel_id' => $rombelId,
                    'tanggal'         => $tglStr,
                    'jam_masuk'       => $jamMasuk,
                    'jam_pulang'      => $jamPulang,
                    'status'          => $status,
                    'sumber_absen'    => 'simulasi_1tahun',
                    'keterangan'      => $ket,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                if (count($batchAbsensi) >= 500) {
                    foreach (array_chunk($batchAbsensi, 50) as $chunk) {
                        DB::table('absensis')->insert($chunk);
                    }
                    $batchAbsensi = [];
                }
                if (count($batchIzin) >= 500) {
                    foreach (array_chunk($batchIzin, 50) as $chunk) {
                        DB::table('izin_siswas')->insert($chunk);
                    }
                    $batchIzin = [];
                }
            }
        }

        if (!empty($batchAbsensi)) {
            foreach (array_chunk($batchAbsensi, 50) as $chunk) {
                DB::table('absensis')->insert($chunk);
            }
            $batchAbsensi = [];
        }
        if (!empty($batchIzin)) {
            foreach (array_chunk($batchIzin, 50) as $chunk) {
                DB::table('izin_siswas')->insert($chunk);
            }
            $batchIzin = [];
        }

        $this->command->info("✅ Absensi Siswa Selesai. Total Record Siswa: " . DB::table('absensis')->where('pemilik_type', 'siswa')->count());

        // 4. GENERATE ABSENSI GURU & PEGAWAI (17 Guru)
        $gurus = DB::table('gurus')->where('status', 'aktif')->get();
        $this->command->info("👩‍🏫 Memproses " . count($gurus) . " Guru & Pegawai...");

        $batchGuruAbsen = [];
        foreach ($gurus as $g) {
            $isKepsek = ($g->jabatan === 'Kepala Sekolah');
            $pG = $isKepsek ? [92, 3, 3, 2] : [87, 5, 4, 4]; // Hadir, Telat, Izin/Dinas, Sakit

            foreach ($hariKerja as $tglStr) {
                $roll = rand(1, 100);
                if ($roll <= $pG[0]) {
                    $m = rand(30, 59);
                    $jamMasuk = sprintf('06:%02d:00', $m);
                    $jamPulang = sprintf('15:%02d:00', rand(30, 55));
                    $status = 'hadir';
                    $ket = null;
                } elseif ($roll <= $pG[0] + $pG[1]) {
                    $m = rand(16, 35);
                    $jamMasuk = sprintf('07:%02d:00', $m);
                    $jamPulang = sprintf('15:%02d:00', rand(30, 55));
                    $status = 'terlambat';
                    $ket = 'Terlambat ' . ($m - 15) . ' menit';
                } elseif ($roll <= $pG[0] + $pG[1] + $pG[2]) {
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'izin';
                    $ket = 'Dinas Luar / Kegiatan Instansi';
                } else {
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'sakit';
                    $ket = 'Sakit (Izin Kesehatan)';
                }

                $batchGuruAbsen[] = [
                    'pemilik_type' => 'guru',
                    'pemilik_id'   => $g->id,
                    'tanggal'      => $tglStr,
                    'jam_masuk'    => $jamMasuk,
                    'jam_pulang'   => $jamPulang,
                    'status'       => $status,
                    'sumber_absen' => 'simulasi_1tahun',
                    'keterangan'   => $ket,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }
        if (!empty($batchGuruAbsen)) {
            foreach (array_chunk($batchGuruAbsen, 50) as $chunk) {
                DB::table('absensis')->insert($chunk);
            }
        }

        $this->command->info("✅ Absensi Guru Selesai. Total Record Guru: " . DB::table('absensis')->where('pemilik_type', 'guru')->count());

        // 5. GENERATE DOSSIER KASUS DISIPLIN UNTUK SISWA BERMASALAH
        $this->command->info("📋 Mengakumulasi Kasus Kedisiplinan Siswa...");
        $siswaPelanggar = DB::table('absensis')
            ->where('pemilik_type', 'siswa')
            ->whereIn('status', ['alpha', 'bolos', 'terlambat'])
            ->selectRaw('pemilik_id, 
                SUM(CASE WHEN status="alpha" THEN 1 ELSE 0 END) as tot_alpha,
                SUM(CASE WHEN status="bolos" THEN 1 ELSE 0 END) as tot_bolos,
                SUM(CASE WHEN status="terlambat" THEN 1 ELSE 0 END) as tot_telat')
            ->groupBy('pemilik_id')
            ->get();

        $batchKasus = [];
        foreach ($siswaPelanggar as $sp) {
            $alpha = (int) $sp->tot_alpha;
            $bolos = (int) $sp->tot_bolos;
            $telat = (int) $sp->tot_telat;
            $poin  = ($alpha * 10) + ($bolos * 15) + ($telat * 5);

            if ($poin < 10) continue;

            $statusTahap = match(true) {
                $poin >= 100 => 'tahap_4_kepsek',
                $poin >= 60  => 'tahap_3_wakasis',
                $poin >= 30  => 'tahap_2_bk',
                default      => 'tahap_1_wali_kelas',
            };

            $batchKasus[] = [
                'siswa_id'                => $sp->pemilik_id,
                'status_tahap'            => $statusTahap,
                'total_alpha'             => $alpha,
                'total_bolos'             => $bolos,
                'total_terlambat'         => $telat,
                'total_poin_pelanggaran'  => $poin,
                'is_active'               => true,
                'created_at'              => $now,
                'updated_at'              => $now,
            ];
        }

        if (!empty($batchKasus)) {
            foreach (array_chunk($batchKasus, 50) as $chunk) {
                DB::table('kasus_disiplins')->insert($chunk);
            }
        }

        $this->command->info("🎉 SIMULASI 1 TAHUN SELESAI!");
        $this->command->info("   📊 Total Record Absensi: " . DB::table('absensis')->count());
        $this->command->info("   📋 Total Kasus Disiplin: " . DB::table('kasus_disiplins')->count());
    }
}
