<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinLog;

class SimulasiTigaTahunLengkapSeeder extends Seeder
{
    public function run(): void
    {
        echo "🚀 MEMULAI SIMULASI ABSENSI & KESISWAAN 3 TAHUN (2023 - 2026)\n";

        // Optimasi SQLite High Performance
        DB::statement('PRAGMA journal_mode = WAL;');
        DB::statement('PRAGMA synchronous = OFF;');
        DB::statement('PRAGMA cache_size = 100000;');
        DB::statement('PRAGMA temp_store = MEMORY;');
        DB::statement('PRAGMA foreign_keys = OFF;');

        // 1. Bersihkan transaksi lama
        DB::table('kasus_disiplin_dokumens')->truncate();
        DB::table('kasus_disiplin_logs')->truncate();
        DB::table('kasus_disiplin_pelanggarans')->truncate();
        DB::table('kasus_disiplin_rewards')->truncate();
        DB::table('kasus_disiplins')->truncate();
        DB::table('notifikasi_ortus')->truncate();
        DB::table('izin_gurus')->truncate();
        DB::table('izin_siswas')->truncate();
        DB::table('absensis')->truncate();
        DB::table('siswa_rombels')->truncate();
        DB::table('siswas')->truncate();

        echo "✅ Tabel data siswa & absensi lama telah dibersihkan.\n";

        // 2. Cek Tahun Ajaran Aktif & Rombel
        $taAktif = DB::table('tahun_ajarans')->where('is_active', true)->first();
        if (!$taAktif) {
            $taId = DB::table('tahun_ajarans')->insertGetId([
                'nama'       => '2026/2027',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $taId = $taAktif->id;
        }

        $rombels = DB::table('rombels')->select('id', 'nama_rombel', 'tingkat')->get();

        // 3. Generate Nama Siswa Realistis untuk 9 Rombel
        $firstNamesL = ['Ahmad', 'Budi', 'Dimas', 'Eko', 'Fajar', 'Galih', 'Hadi', 'Ilham', 'Joko', 'Kurnia', 'Lukman', 'Muhammad', 'Naufal', 'Pratama', 'Rangga', 'Rizky', 'Satria', 'Taufik', 'Wahyu', 'Yoga', 'Zulham', 'Bayu', 'Danu', 'Farhan', 'Gilang'];
        $firstNamesP = ['Aulia', 'Bella', 'Citra', 'Dina', 'Eka', 'Fitri', 'Gita', 'Hani', 'Indah', 'Jasmine', 'Karina', 'Lestari', 'Maya', 'Nabila', 'Putri', 'Rani', 'Siti', 'Tia', 'Utami', 'Vina', 'Wulan', 'Zahra', 'Anisa', 'Dewi', 'Intan'];
        $lastNames   = ['Saputra', 'Pratama', 'Kusuma', 'Hidayat', 'Ramadhan', 'Wijaya', 'Nugroho', 'Firmansyah', 'Santoso', 'Permana', 'Setiawan', 'Wibowo', 'Kurniawan', 'Maulana', 'Adiputra', 'Syahputra', 'Gunawan', 'Lestari', 'Wulandari', 'Anggraini'];

        $generateEmbedding = function () {
            $vec = [];
            $sumSq = 0;
            for ($i = 0; $i < 512; $i++) {
                $val = (mt_rand(-10000, 10000) / 10000);
                $vec[] = $val;
                $sumSq += $val * $val;
            }
            $norm = sqrt($sumSq) ?: 1.0;
            return json_encode(array_map(fn($v) => round($v / $norm, 6), $vec));
        };

        $siswaList = [];
        $nisCounter = 230101;
        $now = now()->toDateTimeString();

        foreach ($rombels as $r) {
            // Tiap kelas diisi 28 siswa = 252 siswa total
            for ($i = 1; $i <= 28; $i++) {
                $gender = ($i % 2 === 0) ? 'L' : 'P';
                $fn = ($gender === 'L') ? $firstNamesL[array_rand($firstNamesL)] : $firstNamesP[array_rand($firstNamesP)];
                $ln = $lastNames[array_rand($lastNames)];
                $nama = "{$fn} {$ln}";

                $siswaId = DB::table('siswas')->insertGetId([
                    'nis'                => (string) $nisCounter++,
                    'nisn'               => '00' . mt_rand(10000000, 99999999),
                    'nama'               => $nama,
                    'status'             => 'aktif',
                    'nama_ortu'          => 'Orang Tua ' . $nama,
                    'no_hp_ortu'         => '08' . mt_rand(111111111, 999999999),
                    'no_hp_siswa'        => '08' . mt_rand(111111111, 999999999),
                    'face_embedding'     => $generateEmbedding(),
                    'face_registered_at' => $now,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                $srId = DB::table('siswa_rombels')->insertGetId([
                    'siswa_id'           => $siswaId,
                    'rombel_id'          => $r->id,
                    'tahun_ajaran_id'    => $taId,
                    'status_keanggotaan' => 'aktif',
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                $siswaList[] = [
                    'id'              => $siswaId,
                    'nama'            => $nama,
                    'rombel_id'       => $r->id,
                    'siswa_rombel_id' => $srId,
                ];
            }
        }

        $totalSiswa = count($siswaList);
        echo "👨‍🎓 {$totalSiswa} Siswa berhasil didaftarkan ke 9 Rombel.\n";

        // 4. Guru Data
        $gurus = DB::table('gurus')->where('status', 'aktif')->get();
        $totalGuru = count($gurus);
        echo "👨‍🏫 Terhubung dengan {$totalGuru} Guru aktif.\n";

        // 5. Kalender Hari Kerja 3 Tahun Penuh (1 September 2023 - 1 September 2026)
        $startDate = Carbon::create(2023, 9, 1);
        $endDate   = Carbon::create(2026, 9, 1);

        $hariLiburNasional = [
            '2023-09-28', '2023-12-25', '2023-12-26',
            '2024-01-01', '2024-02-08', '2024-02-09', '2024-02-10', '2024-03-11', '2024-03-29',
            '2024-04-10', '2024-04-11', '2024-05-01', '2024-05-09', '2024-05-23', '2024-06-01',
            '2024-06-17', '2024-07-07', '2024-08-17', '2024-09-16', '2024-12-25',
            '2025-01-01', '2025-01-29', '2025-03-20', '2025-03-31', '2025-04-01', '2025-05-01',
            '2025-05-12', '2025-05-29', '2025-06-01', '2025-06-07', '2025-08-17',
            '2026-01-01', '2026-02-17', '2026-03-20', '2026-03-21', '2026-04-03', '2026-05-01',
            '2026-05-14', '2026-05-26', '2026-06-01', '2026-08-17',
        ];

        $hariKerja = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $tglStr = $curr->toDateString();
            if (!$curr->isWeekend() && !in_array($tglStr, $hariLiburNasional)) {
                $month = $curr->month;
                $day = $curr->day;
                $isLiburSemester = ($month === 6 && $day >= 24) || ($month === 7 && $day <= 10) || ($month === 12 && $day >= 24);
                if (!$isLiburSemester) {
                    $hariKerja[] = $tglStr;
                }
            }
            $curr->addDay();
        }

        $totalHari = count($hariKerja);
        echo "📅 Menghasilkan absensi untuk {$totalHari} Hari Efektif Sekolah (Rentang 3 Tahun).\n";

        // 6. Generate Absensi Siswa
        echo "⚡ Memproses Absensi Siswa...\n";
        $batchAbsensi = [];
        $batchIzin = [];
        $insertedCount = 0;

        // Distribusi Profil Siswa
        $siswaProfiles = [];
        foreach ($siswaList as $idx => $s) {
            $rand = mt_rand(1, 100);
            if ($rand <= 72) {
                // Sangat Rajin (Hadir 92%, Telat 5%, Izin 2%, Sakit 1%)
                $siswaProfiles[$s['id']] = ['hadir' => 92, 'terlambat' => 97, 'izin' => 99, 'sakit' => 100];
            } elseif ($rand <= 92) {
                // Rata-rata (Hadir 80%, Telat 12%, Izin 4%, Sakit 2%, Alpha 2%)
                $siswaProfiles[$s['id']] = ['hadir' => 80, 'terlambat' => 92, 'izin' => 96, 'sakit' => 98];
            } else {
                // Sering Terlambat / Alpha (Hadir 62%, Telat 22%, Izin 6%, Sakit 4%, Alpha 6%)
                $siswaProfiles[$s['id']] = ['hadir' => 62, 'terlambat' => 84, 'izin' => 90, 'sakit' => 94];
            }
        }

        foreach ($hariKerja as $tgl) {
            foreach ($siswaList as $s) {
                $prof = $siswaProfiles[$s['id']];
                $roll = mt_rand(1, 100);

                if ($roll <= $prof['hadir']) {
                    // Hadir Tepat Waktu (06:40 - 07:14)
                    $jamMasuk = sprintf('%02d:%02d:%02d', 6, mt_rand(40, 59), mt_rand(0, 59));
                    if (mt_rand(1, 10) === 1) {
                        $jamMasuk = sprintf('%02d:%02d:%02d', 7, mt_rand(0, 14), mt_rand(0, 59));
                    }
                    $jamPulang = sprintf('%02d:%02d:%02d', 15, mt_rand(30, 59), mt_rand(0, 59));

                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s['id'],
                        'siswa_rombel_id' => $s['siswa_rombel_id'],
                        'tanggal'         => $tgl,
                        'jam_masuk'       => $jamMasuk,
                        'jam_pulang'      => $jamPulang,
                        'status'          => 'hadir',
                        'sumber_absen'    => 'face_id',
                        'keterangan'      => null,
                        'created_at'      => "{$tgl} {$jamMasuk}",
                        'updated_at'      => "{$tgl} {$jamPulang}",
                    ];
                } elseif ($roll <= $prof['terlambat']) {
                    // Terlambat (07:16 - 07:45)
                    $jamMasuk = sprintf('%02d:%02d:%02d', 7, mt_rand(16, 45), mt_rand(0, 59));
                    $jamPulang = sprintf('%02d:%02d:%02d', 15, mt_rand(30, 59), mt_rand(0, 59));

                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s['id'],
                        'siswa_rombel_id' => $s['siswa_rombel_id'],
                        'tanggal'         => $tgl,
                        'jam_masuk'       => $jamMasuk,
                        'jam_pulang'      => $jamPulang,
                        'status'          => 'terlambat',
                        'sumber_absen'    => 'face_id',
                        'keterangan'      => null,
                        'created_at'      => "{$tgl} {$jamMasuk}",
                        'updated_at'      => "{$tgl} {$jamPulang}",
                    ];
                } elseif ($roll <= $prof['izin']) {
                    // Izin
                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s['id'],
                        'siswa_rombel_id' => $s['siswa_rombel_id'],
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'izin',
                        'sumber_absen'    => 'manual_piket',
                        'keterangan'      => 'Izin keperluan keluarga',
                        'created_at'      => "{$tgl} 07:00:00",
                        'updated_at'      => "{$tgl} 07:00:00",
                    ];
                    $batchIzin[] = [
                        'siswa_id'   => $s['id'],
                        'tanggal'    => $tgl,
                        'jenis'      => 'izin',
                        'status'     => 'disetujui',
                        'keterangan' => 'Keperluan keluarga / acara dinas keluarga',
                        'created_at' => "{$tgl} 07:00:00",
                        'updated_at' => "{$tgl} 07:00:00",
                    ];
                } elseif ($roll <= $prof['sakit']) {
                    // Sakit
                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s['id'],
                        'siswa_rombel_id' => $s['siswa_rombel_id'],
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'sakit',
                        'sumber_absen'    => 'manual_piket',
                        'keterangan'      => 'Keterangan sakit',
                        'created_at'      => "{$tgl} 07:00:00",
                        'updated_at'      => "{$tgl} 07:00:00",
                    ];
                    $batchIzin[] = [
                        'siswa_id'   => $s['id'],
                        'tanggal'    => $tgl,
                        'jenis'      => 'sakit',
                        'status'     => 'disetujui',
                        'keterangan' => 'Demam / Istirahat rawat jalan surat dokter',
                        'created_at' => "{$tgl} 07:00:00",
                        'updated_at' => "{$tgl} 07:00:00",
                    ];
                } else {
                    // Alpha (Tanpa Keterangan)
                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s['id'],
                        'siswa_rombel_id' => $s['siswa_rombel_id'],
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'alpha',
                        'sumber_absen'    => 'otomatis_sistem',
                        'keterangan'      => 'Alpha tanpa keterangan',
                        'created_at'      => "{$tgl} 18:00:00",
                        'updated_at'      => "{$tgl} 18:00:00",
                    ];
                }

                if (count($batchAbsensi) >= 1000) {
                    foreach (array_chunk($batchAbsensi, 50) as $chunk) {
                        DB::table('absensis')->insert($chunk);
                    }
                    $insertedCount += count($batchAbsensi);
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
            $insertedCount += count($batchAbsensi);
        }
        if (!empty($batchIzin)) {
            foreach (array_chunk($batchIzin, 50) as $chunk) {
                DB::table('izin_siswas')->insert($chunk);
            }
        }

        echo "✅ {$insertedCount} Record Absensi Siswa 3 Tahun berhasil dibuat.\n";

        // 7. Generate Absensi Dewan Guru
        echo "⚡ Memproses Absensi 18 Guru...\n";
        $batchGuruAbsensi = [];
        $batchGuruIzin = [];

        foreach ($hariKerja as $tgl) {
            foreach ($gurus as $g) {
                $roll = mt_rand(1, 100);
                if ($roll <= 90) {
                    // Hadir (06:30 - 07:15)
                    $jamMasuk = sprintf('%02d:%02d:%02d', 6, mt_rand(30, 59), mt_rand(0, 59));
                    $jamPulang = sprintf('%02d:%02d:%02d', 15, mt_rand(30, 59), mt_rand(0, 59));
                    $batchGuruAbsensi[] = [
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $g->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => $jamMasuk,
                        'jam_pulang'      => $jamPulang,
                        'status'          => 'hadir',
                        'sumber_absen'    => 'face_id',
                        'keterangan'      => null,
                        'created_at'      => "{$tgl} {$jamMasuk}",
                        'updated_at'      => "{$tgl} {$jamPulang}",
                    ];
                } elseif ($roll <= 96) {
                    // Terlambat
                    $jamMasuk = sprintf('%02d:%02d:%02d', 7, mt_rand(16, 35), mt_rand(0, 59));
                    $jamPulang = sprintf('%02d:%02d:%02d', 15, mt_rand(30, 59), mt_rand(0, 59));
                    $batchGuruAbsensi[] = [
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $g->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => $jamMasuk,
                        'jam_pulang'      => $jamPulang,
                        'status'          => 'terlambat',
                        'sumber_absen'    => 'face_id',
                        'keterangan'      => null,
                        'created_at'      => "{$tgl} {$jamMasuk}",
                        'updated_at'      => "{$tgl} {$jamPulang}",
                    ];
                } elseif ($roll <= 98) {
                    // Dinas Luar / Izin
                    $batchGuruAbsensi[] = [
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $g->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'izin',
                        'sumber_absen'    => 'manual_piket',
                        'keterangan'      => 'Tugas Kedinasan / Pelatihan',
                        'created_at'      => "{$tgl} 07:00:00",
                        'updated_at'      => "{$tgl} 07:00:00",
                    ];
                    $batchGuruIzin[] = [
                        'guru_id'    => $g->id,
                        'tanggal'    => $tgl,
                        'jenis'      => 'dinas_luar',
                        'status'     => 'disetujui',
                        'keterangan' => 'Tugas Pelatihan MGMP / Bimtek Cabang Dinas',
                        'created_at' => "{$tgl} 07:00:00",
                        'updated_at' => "{$tgl} 07:00:00",
                    ];
                } else {
                    // Sakit
                    $batchGuruAbsensi[] = [
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $g->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'sakit',
                        'sumber_absen'    => 'manual_piket',
                        'keterangan'      => 'Keterangan Dokter',
                        'created_at'      => "{$tgl} 07:00:00",
                        'updated_at'      => "{$tgl} 07:00:00",
                    ];
                    $batchGuruIzin[] = [
                        'guru_id'    => $g->id,
                        'tanggal'    => $tgl,
                        'jenis'      => 'sakit',
                        'status'     => 'disetujui',
                        'keterangan' => 'Sakit / Surat Dokter',
                        'created_at' => "{$tgl} 07:00:00",
                        'updated_at' => "{$tgl} 07:00:00",
                    ];
                }

                if (count($batchGuruAbsensi) >= 500) {
                    foreach (array_chunk($batchGuruAbsensi, 50) as $chunk) {
                        DB::table('absensis')->insert($chunk);
                    }
                    $batchGuruAbsensi = [];
                }
            }
        }

        if (!empty($batchGuruAbsensi)) {
            foreach (array_chunk($batchGuruAbsensi, 50) as $chunk) {
                DB::table('absensis')->insert($chunk);
            }
        }
        if (!empty($batchGuruIzin)) {
            foreach (array_chunk($batchGuruIzin, 50) as $chunk) {
                DB::table('izin_gurus')->insert($chunk);
            }
        }

        // 8. Hitung Ulang Akumulasi Alpha & Kasus Disiplin Otomatis
        echo "🛡️ Membangun Dossier Kasus Disiplin & Poin Pelanggaran...\n";
        foreach ($siswaList as $s) {
            KasusDisiplin::syncFromPresensi($s['id']);
        }

        // 9. Berikan beberapa sample kasus realistis di tiap jenjang
        $activeKasus = KasusDisiplin::where('is_active', true)->get();
        if ($activeKasus->isNotEmpty()) {
            $tahapList = ['tahap_1_wali_kelas', 'tahap_2_bk', 'tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan'];
            foreach ($activeKasus->take(15) as $kIdx => $kasus) {
                $targetTahap = $tahapList[$kIdx % count($tahapList)];
                $kasus->update(['status_tahap' => $targetTahap]);

                // Tambahkan log kronologis
                KasusDisiplinLog::create([
                    'kasus_disiplin_id' => $kasus->id,
                    'tahap'             => 'tahap_1_wali_kelas',
                    'judul_kegiatan'    => 'Pemanggilan & Bimbingan Wali Kelas',
                    'uraian_tindakan'   => 'Siswa dipanggil oleh Wali Kelas mengenai catatan ketidakhadiran berturut-turut. Diberikan teguran lisan dan surat pernyataan komitmen.',
                    'petugas_nama'      => 'Wali Kelas',
                    'petugas_role'      => 'wali_kelas',
                    'tanggal_kegiatan'  => '2026-08-10',
                ]);

                if (in_array($targetTahap, ['tahap_2_bk', 'tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan'])) {
                    KasusDisiplinLog::create([
                        'kasus_disiplin_id' => $kasus->id,
                        'tahap'             => 'tahap_2_bk',
                        'judul_kegiatan'    => 'Konseling Individual & Panggilan Ortu I',
                        'uraian_tindakan'   => 'Guru BK mengadakan sesi konseling individual bersama orang tua siswa. Disepakati pemantauan buku penghubung dan jadwal harian di rumah.',
                        'petugas_nama'      => 'Ari Apriansah,S.Pd.',
                        'petugas_role'      => 'guru_bk',
                        'tanggal_kegiatan'  => '2026-08-18',
                    ]);
                }

                if (in_array($targetTahap, ['tahap_3_wakasis', 'tahap_4_kepsek'])) {
                    KasusDisiplinLog::create([
                        'kasus_disiplin_id' => $kasus->id,
                        'tahap'             => 'tahap_3_wakasis',
                        'judul_kegiatan'    => 'Sidang Pleno Kesiswaan & Penerbitan SP 2',
                        'uraian_tindakan'   => 'Sidang pleno bersama tim ketertiban sekolah dan waka kesiswaan. Penerbitan Surat Peringatan II dan tugas pembinaan kebersihan lingkungan sekolah.',
                        'petugas_nama'      => 'Waka Kesiswaan',
                        'petugas_role'      => 'waka_kesiswaan',
                        'tanggal_kegiatan'  => '2026-08-25',
                    ]);
                }
            }
        }

        echo "🎉 SIMULASI 3 TAHUN SELESAI DENGAN SUKSES!\n";
    }
}
