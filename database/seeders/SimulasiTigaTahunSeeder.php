<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimulasiTigaTahunSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 MEMULAI GENERASI DATA SPESIFIKASI SMKN 1 AIR NANINGAN:');
        $this->command->info('   • 3 Jurusan: APHP, RPL, TSM');
        $this->command->info('   • 3 Angkatan: X, XI, XII (Masing-masing 3 Rombel = 27 Rombel)');
        $this->command->info('   • 36 Siswa per Rombel = 972 Siswa Total');
        $this->command->info('   • Simulasi Absensi 3 Tahun Penuh (2023 - 2026)');

        // Optimasi SQLite High Throughput
        DB::statement('PRAGMA journal_mode = WAL;');
        DB::statement('PRAGMA synchronous = OFF;');
        DB::statement('PRAGMA cache_size = 100000;');
        DB::statement('PRAGMA temp_store = MEMORY;');
        DB::statement('PRAGMA foreign_keys = OFF;');

        // 1. Reset Seluruh Tabel Transaksi & Master
        DB::table('kasus_disiplin_dokumens')->delete();
        DB::table('kasus_disiplin_logs')->delete();
        DB::table('kasus_disiplin_pelanggarans')->delete();
        DB::table('kasus_disiplin_rewards')->delete();
        DB::table('kasus_disiplins')->delete();
        DB::table('notifikasi_ortus')->delete();
        DB::table('izin_gurus')->delete();
        DB::table('izin_siswas')->delete();
        DB::table('absensis')->delete();
        DB::table('siswa_rombels')->delete();
        DB::table('siswas')->delete();
        DB::table('rombels')->delete();
        DB::table('gurus')->delete();
        DB::table('jurusans')->delete();
        DB::table('tahun_ajarans')->delete();
        DB::table('jadwal_pikets')->delete();
        DB::table('jadwal_hari_inis')->delete();
        DB::table('pengumumans')->delete();
        DB::table('audit_logs')->delete();
        DB::table('users')->delete();

        $this->command->info('✅ Tabel data lama berhasil dibersihkan.');

        // 2. Tahun Ajaran
        $taId = DB::table('tahun_ajarans')->insertGetId([
            'nama'       => '2026/2027',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('tahun_ajarans')->insert([
            ['nama' => '2025/2026', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => '2024/2025', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Jurusan (APHP, RPL, TSM)
        $jurusanData = [
            ['kode' => 'APHP', 'nama' => 'Agribisnis Pengolahan Hasil Pertanian'],
            ['kode' => 'RPL',  'nama' => 'Rekayasa Perangkat Lunak'],
            ['kode' => 'TSM',  'nama' => 'Teknik Sepeda Motor'],
        ];

        $jurusanIds = [];
        foreach ($jurusanData as $j) {
            $jurusanIds[$j['kode']] = DB::table('jurusans')->insertGetId([
                'kode_jurusan' => $j['kode'],
                'nama_jurusan' => $j['nama'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Helper random 512-D L2 Normalized Face Embedding
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

        // 4. Guru & Staf (35 Pegawai)
        $namaGuruList = [
            'Drs. H. Ahmad Sudrajat, M.Pd.', // Kepsek
            'Budi Santoso, S.Kom.',          // Wali Kelas RPL
            'Siti Aminah, S.Pd.',            // Waka Kurikulum
            'Drs. Bambang Hendrawan',        // Waka Kesiswaan
            'Rina Marlina, S.Pd.',           // Guru BK
            'Eko Prasetyo, S.T.',            // Wali Kelas TSM
            'Dewi Lestari, S.TP.',           // Wali Kelas APHP
            'Hendra Kurniawan, M.Kom.',
            'Sri Wahyuni, S.Pd.',
            'Ahmad Fauzi, S.T.',
            'Nurul Hidayah, S.Pd.I.',
            'Agus Setiawan, S.Pd.',
            'Indah Permata, S.Pd.',
            'Joko Susilo, S.T.',
            'Maya Anggraini, S.TP.',
            'Rahmat Hidayat, S.Pd.',
            'Dian Pratama, S.Kom.',
            'Tri Astuti, S.Pd.',
            'Wahyu Ramadhan, S.T.',
            'Yuliana Sari, S.Pd.',
            'Doni Firmansyah, S.Pd.',
            'Eni Sulistiawati, S.Pd.',
            'Fajar Nugroho, S.Kom.',
            'Gita Savitri, S.Pd.',
            'Hadi Pranoto, S.T.',
            'Irfan Maulana, S.Pd.',
            'Kurniawati, S.TP.',
            'Lukman Hakim, S.Pd.',
            'Mega Utami, S.Kom.',
            'Nugroho Adiputra, S.T.',
            'Oki Setiawan, S.Pd.',
            'Putri Anggraini, S.Pd.',
            'Rizky Pratama, S.T.',
            'Siska Rahmawati, S.Pd.',
            'Taufik Hidayat, S.Pd.',
        ];

        $jabatanList = [
            'Kepala Sekolah', 'Wali Kelas', 'Waka Kurikulum', 'Waka Kesiswaan', 'Guru BK',
            'Wali Kelas', 'Wali Kelas', 'Guru Kejuruan RPL', 'Guru Bahasa Indonesia', 'Guru Kejuruan TSM',
            'Guru Agama Islam', 'Guru Penjasorkes', 'Guru Matematika', 'Guru Kejuruan TSM', 'Guru Kejuruan APHP',
            'Guru Sejarah', 'Guru Kejuruan RPL', 'Guru Bahasa Inggris', 'Guru Fisika', 'Guru Kimia',
            'Wali Kelas', 'Wali Kelas', 'Wali Kelas', 'Wali Kelas', 'Wali Kelas',
            'Wali Kelas', 'Wali Kelas', 'Wali Kelas', 'Wali Kelas', 'Wali Kelas',
            'Wali Kelas', 'Wali Kelas', 'Wali Kelas', 'Wali Kelas', 'Wali Kelas',
        ];

        $guruIds = [];
        foreach ($namaGuruList as $idx => $namaGuru) {
            $nip = '198' . mt_rand(0, 9) . sprintf('%02d%02d', mt_rand(1, 12), mt_rand(1, 28)) . '201001' . ($idx < 10 ? '100' : '10') . ($idx + 1);
            $gId = DB::table('gurus')->insertGetId([
                'nip'                => $nip,
                'nama'               => $namaGuru,
                'jabatan'            => $jabatanList[$idx] ?? 'Guru Mapel',
                'status'             => 'aktif',
                'jenis_kepegawaian'  => ($idx < 20) ? 'pns' : 'p3k',
                'no_hp'              => '08' . mt_rand(1111111111, 9999999999),
                'hari_mengajar'      => json_encode(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
                'face_embedding'     => $generateEmbedding(),
                'face_registered_at' => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            $guruIds[] = $gId;
        }

        // Akun Pengguna (Users)
        // 1. Superadmin
        DB::table('users')->insert([
            'name'       => 'Administrator SMKN 1 Air Naningan',
            'email'      => 'admin@smkn1airnaningan.sch.id',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'guru_id'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Akun Guru Utama
        $roleMap = [
            0 => 'kepala_sekolah',
            1 => 'wali_kelas',
            2 => 'waka_kurikulum',
            3 => 'waka_kesiswaan',
            4 => 'guru_bk',
        ];

        foreach ($guruIds as $idx => $gId) {
            $role = $roleMap[$idx] ?? 'guru';
            $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', explode(',', $namaGuruList[$idx])[0]));
            DB::table('users')->insert([
                'name'       => $namaGuruList[$idx],
                'email'      => "{$cleanName}@smkn1airnaningan.sch.id",
                'password'   => Hash::make('password'),
                'role'       => $role,
                'guru_id'    => $gId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Rombel (27 Rombel = 3 Jurusan x 3 Tingkat x 3 Kelas)
        $tingkatList = ['X', 'XI', 'XII'];
        $rombels = [];
        $rombelIndex = 0;

        foreach ($jurusanData as $j) {
            $jKode = $j['kode'];
            $jId   = $jurusanIds[$jKode];

            foreach ($tingkatList as $tingkat) {
                for ($k = 1; $k <= 3; $k++) {
                    $namaRombel = "{$tingkat} {$jKode} {$k}";
                    $waliId = $guruIds[$rombelIndex % count($guruIds)];
                    $rId = DB::table('rombels')->insertGetId([
                        'nama_rombel'     => $namaRombel,
                        'tingkat'         => $tingkat,
                        'jurusan_id'      => $jId,
                        'tahun_ajaran_id' => $taId,
                        'wali_kelas_id'   => $waliId,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);

                    $rombels[] = [
                        'id'          => $rId,
                        'nama_rombel' => $namaRombel,
                        'tingkat'     => $tingkat,
                        'jurusan_id'  => $jId,
                    ];
                    $rombelIndex++;
                }
            }
        }
        $this->command->info("✅ Berhasil membuat " . count($rombels) . " Rombel (3 Jurusan x 3 Tingkat x 3 Rombel).");

        // 6. Siswa (972 Siswa = 27 Rombel x 36 Siswa)
        $firstNames = ['Ahmad', 'Budi', 'Chandra', 'Dimas', 'Eko', 'Fajar', 'Galih', 'Hadi', 'Ilham', 'Joko', 'Kevin', 'Lukman', 'Mahendra', 'Noval', 'Oki', 'Putra', 'Rian', 'Satria', 'Taufik', 'Wahyu', 'Yusuf', 'Zacky', 'Annisa', 'Bella', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana', 'Indah', 'Jasmine', 'Kirana', 'Lestari', 'Maya', 'Nabila', 'Putri', 'Rani', 'Siti', 'Tia', 'Vina', 'Winda', 'Zahra'];
        $lastNames  = ['Pratama', 'Saputra', 'Setiawan', 'Hidayat', 'Kurniawan', 'Ramadhan', 'Wijaya', 'Kusuma', 'Santoso', 'Utama', 'Nugroho', 'Wibowo', 'Firmansyah', 'Maulana', 'Anggraini', 'Lestari', 'Permata', 'Sari', 'Wahyuni', 'Astuti', 'Rahmawati', 'Maharani'];

        $siswaList = [];
        $siswaRombelList = [];
        $nisCounter = 2401001;
        $nowStr = now()->toDateTimeString();

        foreach ($rombels as $r) {
            for ($s = 1; $s <= 36; $s++) {
                $fName = $firstNames[array_rand($firstNames)];
                $lName = $lastNames[array_rand($lastNames)];

                $nis = (string)$nisCounter++;
                $nisn = '008' . mt_rand(1000000, 9999999);

                $statusSiswa = ($r['tingkat'] === 'XI' && $s > 30) ? 'pkl' : 'aktif';

                $siswaList[] = [
                    'nis'                => $nis,
                    'nisn'               => $nisn,
                    'nama'               => "{$fName} {$lName}",
                    'status'             => $statusSiswa,
                    'nama_ortu'          => 'Bpk/Ibu ' . $lName,
                    'no_hp_ortu'         => '08' . mt_rand(1111111111, 9999999999),
                    'no_hp_siswa'        => '08' . mt_rand(1111111111, 9999999999),
                    'face_embedding'     => $generateEmbedding(),
                    'face_registered_at' => $nowStr,
                    'created_at'         => $nowStr,
                    'updated_at'         => $nowStr,
                    'rombel_id'          => $r['id'],
                ];
            }
        }

        // Insert Siswa dalam chunk
        $this->command->info("⚡ Memasukkan 972 data induk siswa ke database...");
        foreach (array_chunk($siswaList, 100) as $chunk) {
            foreach ($chunk as $row) {
                $rombelId = $row['rombel_id'];
                unset($row['rombel_id']);
                $sId = DB::table('siswas')->insertGetId($row);

                $siswaRombelList[] = [
                    'siswa_id'           => $sId,
                    'rombel_id'          => $rombelId,
                    'tahun_ajaran_id'    => $taId,
                    'status_keanggotaan' => 'aktif',
                    'created_at'         => $nowStr,
                    'updated_at'         => $nowStr,
                ];
            }
        }

        // Insert Siswa Rombel
        foreach (array_chunk($siswaRombelList, 200) as $srChunk) {
            DB::table('siswa_rombels')->insert($srChunk);
        }
        $this->command->info("✅ Berhasil mendaftarkan 972 Siswa (36 Siswa di setiap 27 Rombel).");

        // 7. SIMULASI ABSENSI 3 TAHUN (2023-08-29 s/d 2026-08-29)
        $this->command->info("⚡ Membangun kalender kerja 3 tahun efektif (2023 - 2026)...");

        $startDate = Carbon::create(2023, 8, 29);
        $endDate   = Carbon::create(2026, 8, 29);

        // Hari libur nasional & cuti bersama
        $hariLibur = [
            '2023-08-17','2023-09-28','2023-10-28','2023-12-25','2023-12-26',
            '2024-01-01','2024-02-08','2024-03-11','2024-03-12','2024-03-29',
            '2024-04-10','2024-04-11','2024-05-01','2024-05-09','2024-05-23',
            '2024-06-01','2024-06-17','2024-08-17','2024-09-16','2024-12-25','2024-12-26',
            '2025-01-01','2025-01-27','2025-01-28','2025-01-29',
            '2025-03-20','2025-03-28','2025-03-29','2025-04-18',
            '2025-05-01','2025-05-12','2025-05-29','2025-06-01','2025-08-17',
            '2026-01-01','2026-01-29','2026-03-20','2026-03-27',
            '2026-04-02','2026-04-03','2026-05-01','2026-05-14',
            '2026-05-26','2026-06-01','2026-08-17',
        ];

        // Libur semester
        $liburSemester = [];
        foreach ([2023, 2024, 2025] as $yr) {
            foreach (range(22, 31) as $d) $liburSemester[] = "{$yr}-12-" . sprintf('%02d', $d);
            foreach (range(1, 8) as $d)   $liburSemester[] = ($yr+1) . "-01-" . sprintf('%02d', $d);
            foreach (range(22, 30) as $d) $liburSemester[] = "{$yr}-06-" . sprintf('%02d', $d);
            foreach (range(1, 10) as $d)  $liburSemester[] = "{$yr}-07-" . sprintf('%02d', $d);
        }

        $allHolidays = array_unique(array_merge($hariLibur, $liburSemester));

        $schoolDays = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            if (!$curr->isWeekend() && !in_array($curr->toDateString(), $allHolidays)) {
                $schoolDays[] = $curr->toDateString();
            }
            $curr->addDay();
        }

        $totalHari = count($schoolDays);
        $this->command->info("📅 Total Hari Sekolah Efektif: {$totalHari} hari");

        $allSiswaDb = DB::table('siswas')->select('id', 'status')->get();
        $allGuruDb  = DB::table('gurus')->select('id')->get();
        $totalSiswa = count($allSiswaDb);
        $totalGuru  = count($allGuruDb);

        $targetRecords = ($totalSiswa + $totalGuru) * $totalHari;
        $this->command->info("🎯 Target Volume: " . number_format($targetRecords) . " Record Absensi...");

        // Probabilitas Kehadiran Siswa
        // 82% Hadir Tepat Waktu, 10% Terlambat, 4% Izin/Sakit, 3% Alpha, 1% Bolos
        $insertBatch = [];
        $batchSize = 2000;
        $totalInserted = 0;

        $t0 = microtime(true);

        foreach ($schoolDays as $tgl) {
            // 1. Siswa Absensi
            foreach ($allSiswaDb as $s) {
                $rand = mt_rand(1, 100);
                $status = 'hadir';
                $jamMasuk = sprintf('06:%02d:%02d', mt_rand(30, 59), mt_rand(10, 59));
                $jamPulang = sprintf('15:%02d:%02d', mt_rand(0, 30), mt_rand(10, 59));

                if ($rand <= 82) {
                    $status = 'hadir';
                } elseif ($rand <= 92) {
                    $status = 'terlambat';
                    $jamMasuk = sprintf('07:%02d:%02d', mt_rand(16, 45), mt_rand(10, 59));
                } elseif ($rand <= 95) {
                    $status = 'izin';
                    $jamMasuk = null;
                    $jamPulang = null;
                } elseif ($rand <= 97) {
                    $status = 'sakit';
                    $jamMasuk = null;
                    $jamPulang = null;
                } elseif ($rand <= 99) {
                    $status = 'alpha';
                    $jamMasuk = null;
                    $jamPulang = null;
                } else {
                    $status = 'bolos';
                    $jamPulang = null;
                }

                $insertBatch[] = [
                    'pemilik_type'    => 'siswa',
                    'pemilik_id'      => $s->id,
                    'siswa_rombel_id' => null,
                    'tanggal'         => $tgl,
                    'jam_masuk'       => $jamMasuk,
                    'jam_pulang'      => $jamPulang,
                    'status'          => $status,
                    'sumber_absen'    => 'kiosk_face',
                    'created_at'      => "{$tgl} " . ($jamMasuk ?? '07:00:00'),
                    'updated_at'      => "{$tgl} " . ($jamPulang ?? '15:00:00'),
                ];

                if (count($insertBatch) >= $batchSize) {
                    DB::table('absensis')->insert($insertBatch);
                    $totalInserted += count($insertBatch);
                    $insertBatch = [];

                    if ($totalInserted % 50000 === 0) {
                        $this->command->info("   ⏳ Progres: " . number_format($totalInserted) . " baris tersimpan...");
                    }
                }
            }

            // 2. Guru Absensi (Tingkat kehadiran 96%)
            foreach ($allGuruDb as $g) {
                $gRand = mt_rand(1, 100);
                $gStatus = 'hadir';
                $gMasuk = sprintf('06:%02d:%02d', mt_rand(30, 55), mt_rand(10, 59));
                $gPulang = sprintf('15:%02d:%02d', mt_rand(15, 59), mt_rand(10, 59));

                if ($gRand <= 92) {
                    $gStatus = 'hadir';
                } elseif ($gRand <= 96) {
                    $gStatus = 'terlambat';
                    $gMasuk = sprintf('07:%02d:%02d', mt_rand(16, 35), mt_rand(10, 59));
                } elseif ($gRand <= 98) {
                    $gStatus = 'izin';
                    $gMasuk = null;
                    $gPulang = null;
                } else {
                    $gStatus = 'sakit';
                    $gMasuk = null;
                    $gPulang = null;
                }

                $insertBatch[] = [
                    'pemilik_type'    => 'guru',
                    'pemilik_id'      => $g->id,
                    'siswa_rombel_id' => null,
                    'tanggal'         => $tgl,
                    'jam_masuk'       => $gMasuk,
                    'jam_pulang'      => $gPulang,
                    'status'          => $gStatus,
                    'sumber_absen'    => 'kiosk_face',
                    'created_at'      => "{$tgl} " . ($gMasuk ?? '06:45:00'),
                    'updated_at'      => "{$tgl} " . ($gPulang ?? '15:30:00'),
                ];

                if (count($insertBatch) >= $batchSize) {
                    DB::table('absensis')->insert($insertBatch);
                    $totalInserted += count($insertBatch);
                    $insertBatch = [];
                }
            }
        }

        // Flush sisa batch
        if (count($insertBatch) > 0) {
            DB::table('absensis')->insert($insertBatch);
            $totalInserted += count($insertBatch);
        }

        $t1 = microtime(true);
        $execDuration = round($t1 - $t0, 2);

        // 8. Inisialisasi Jadwal Hari Ini
        $todayStr = date('Y-m-d');
        DB::table('jadwal_hari_inis')->insert([
            'tanggal'             => $todayStr,
            'jam_masuk_toleransi' => '07:15:00',
            'jam_pulang_mulai'    => '15:30:00',
            'jam_tutup_gerbang'   => '18:00:00',
            'keterangan'          => 'Sesi Presensi Smart Gate Reguler',
            'is_sesi_buka'        => true,
            'dibuka_oleh'         => 'Drs. H. Ahmad Sudrajat, M.Pd.',
            'waktu_buka_sesi'     => now(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // 9. Kasus Disiplin Sampel untuk Siswa dengan Alpha / Terlambat Berulang
        $problematicSiswas = DB::table('absensis')
            ->where('pemilik_type', 'siswa')
            ->whereIn('status', ['alpha', 'terlambat', 'bolos'])
            ->select('pemilik_id', DB::raw('count(*) as total_pelanggaran'))
            ->groupBy('pemilik_id')
            ->orderBy('total_pelanggaran', 'desc')
            ->limit(15)
            ->get();

        foreach ($problematicSiswas as $idx => $ps) {
            $siswaRow = DB::table('siswas')->where('id', $ps->pemilik_id)->first();
            if ($siswaRow) {
                $alphaCount = DB::table('absensis')->where('pemilik_type', 'siswa')->where('pemilik_id', $siswaRow->id)->where('status', 'alpha')->count();
                $terlambatCount = DB::table('absensis')->where('pemilik_type', 'siswa')->where('pemilik_id', $siswaRow->id)->where('status', 'terlambat')->count();
                $bolosCount = DB::table('absensis')->where('pemilik_type', 'siswa')->where('pemilik_id', $siswaRow->id)->where('status', 'bolos')->count();

                DB::table('kasus_disiplins')->insert([
                    'siswa_id'                => $siswaRow->id,
                    'tahun_ajaran_id'         => $taId,
                    'total_alpha'             => $alphaCount,
                    'total_bolos'             => $bolosCount,
                    'total_terlambat'         => $terlambatCount,
                    'status_tahap'            => ($idx < 5) ? 'tahap_1_wali_kelas' : (($idx < 10) ? 'tahap_2_bk' : 'selesai_pembinaan'),
                    'catatan_wali_kelas'      => 'Telah dilakukan teguran lisan dan koordinasi pembinaan dengan wali murid.',
                    'catatan_bk'              => ($idx >= 5) ? 'Konseling individu telah dilaksanakan, siswa berkomitmen memperbaiki jam tidur dan keberangkatan.' : null,
                    'hasil_musyawarah_bk'     => ($idx >= 10) ? 'Perjanjian kedisiplinan ditandatangani bersama orang tua.' : null,
                    'is_active'               => ($idx < 10),
                    'total_poin_pelanggaran'  => ($alphaCount * 5) + ($bolosCount * 5) + ($terlambatCount * 2),
                    'total_poin_pemulihan'    => 0,
                    'created_at'              => now()->subDays(mt_rand(2, 60)),
                    'updated_at'              => now(),
                ]);
            }
        }

        DB::statement('PRAGMA foreign_keys = ON;');

        $this->command->info("\n============================================================");
        $this->command->info("🎉 GENERASI DATA SPESIFIKASI SMKN 1 AIR NANINGAN SELESAI!");
        $this->command->info("============================================================");
        $this->command->info("📚 Jurusan           : 3 (APHP, RPL, TSM)");
        $this->command->info("🏫 Rombel Aktif       : " . count($rombels) . " Kelas (3 Tingkat x 3 Kelas)");
        $this->command->info("👥 Total Siswa        : " . number_format($totalSiswa) . " Siswa (36 Siswa/Rombel)");
        $this->command->info("👨‍🏫 Total Guru & Staf  : " . count($guruIds) . " Pegawai");
        $this->command->info("📊 Total Data Absensi : " . number_format($totalInserted) . " Baris Record (3 Tahun)");
        $this->command->info("⏱️ Waktu Injeksi Data : {$execDuration} detik");
        $this->command->info("============================================================\n");
    }
}
