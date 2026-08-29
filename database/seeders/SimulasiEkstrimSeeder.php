<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SimulasiEkstrimSeeder
 *
 * Simulasi ekstrim:
 * - 500 siswa baru (tambah ke existing)
 * - Rombel baru (20 kelas @25 siswa)
 * - Absensi 1 tahun penuh: Agustus 2025 – Juli 2026 (Tahun Ajaran 2025/2026)
 * - Menggunakan bulk insert untuk performa maksimal
 */
class SimulasiEkstrimSeeder extends Seeder
{
    // Nama-nama untuk generate siswa dummy
    private array $namaDepanPria   = ['Ahmad','Budi','Candra','Dedi','Eko','Fajar','Galih','Hendra','Ivan','Joko','Kevin','Lutfi','Maulana','Nanda','Oscar','Pandu','Qori','Reza','Sandi','Taufik','Ucok','Viki','Wahyu','Xander','Yoga','Zaki','Arif','Bayu','Dimas','Egi'];
    private array $namaDepanWanita = ['Ayu','Bunga','Citra','Dewi','Eka','Fitri','Gita','Hani','Indah','Jihan','Kiki','Laila','Mela','Nisa','Olivia','Putri','Qonita','Rini','Sari','Tiara','Ulfa','Vina','Wulan','Xena','Yuli','Zahra','Anis','Bella','Dian','Elis'];
    private array $namaBelakang    = ['Pratama','Saputra','Wijaya','Santoso','Nugroho','Kusuma','Hidayat','Firmansyah','Rahayu','Lestari','Sari','Dewi','Wati','Utami','Handayani','Purnama','Setiawan','Cahyono','Prasetyo','Wibowo','Susanto','Hartono','Prayogo','Gunawan','Sulastri','Wahyuni','Nuraini','Astuti','Anggraini','Kurniawan'];
    private array $namaOrtu        = ['Bpk. Suroto','Bpk. Waluyo','Bpk. Sarjono','Bpk. Sutrisno','Bpk. Bambang','Bpk. Marsono','Bpk. Slamet','Ibu Sumiati','Bpk. Juminten','Bpk. Paimin'];

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 SIMULASI EKSTRIM SIRANI');
        $this->command->info('   500 Siswa + 1 Tahun Penuh Absensi');
        $this->command->info('');

        DB::statement('PRAGMA journal_mode=WAL;');
        DB::statement('PRAGMA synchronous=OFF;');
        DB::statement('PRAGMA cache_size=10000;');

        // ══════════════════════════════════════════════
        // STEP 1: Buat Rombel Baru (20 kelas untuk 500 siswa)
        // ══════════════════════════════════════════════
        $this->command->info('📚 [1/4] Membuat 20 rombel baru...');

        $taId       = 1; // TA 2025/2026 (id=1)
        $jurusans   = [1 => 'RPL', 2 => 'APHP', 3 => 'TSM'];
        $tingkats   = ['X', 'XI', 'XII'];
        $rombelsNew = [];
        $now        = now();

        $rombelId = DB::table('rombels')->max('id') + 1;
        foreach ($tingkats as $tingkat) {
            foreach ($jurusans as $jId => $jKode) {
                for ($kls = 2; $kls <= 3; $kls++) { // kelas 2 & 3 (kelas 1 sudah ada)
                    $rombelsNew[] = [
                        'tahun_ajaran_id' => $taId,
                        'jurusan_id'      => $jId,
                        'nama_rombel'     => "$tingkat $jKode $kls",
                        'tingkat'         => $tingkat,
                        'wali_kelas_id'   => rand(10, 26), // guru id range
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
            }
        }
        DB::table('rombels')->insert($rombelsNew);
        $allRombelIds = DB::table('rombels')->where('tahun_ajaran_id', $taId)->pluck('id')->toArray();
        $this->command->info('   ✅ ' . count($rombelsNew) . ' rombel baru dibuat. Total rombel TA 2025/2026: ' . count($allRombelIds));

        // ══════════════════════════════════════════════
        // STEP 2: Generate 500 Siswa Baru
        // ══════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('👨‍🎓 [2/4] Membuat 500 siswa baru...');

        $maxNis  = (int) DB::table('siswas')->max('nis') + 1;
        $maxNisn = (int) DB::table('siswas')->max('nisn') + 1;

        $siswaChunks   = [];
        $siswaRombelChunks = [];
        $siswaIds      = [];

        $lastSiswaId = DB::table('siswas')->max('id') ?? 0;

        for ($i = 0; $i < 500; $i++) {
            $isWanita   = ($i % 2 === 0);
            $namaDepan  = $isWanita
                ? $this->namaDepanWanita[$i % count($this->namaDepanWanita)]
                : $this->namaDepanPria[$i % count($this->namaDepanPria)];
            $namaBelakang = $this->namaBelakang[$i % count($this->namaBelakang)];
            $nama       = $namaDepan . ' ' . $namaBelakang;
            $nis        = str_pad($maxNis + $i, 8, '0', STR_PAD_LEFT);
            $nisn       = str_pad($maxNisn + $i, 10, '0', STR_PAD_LEFT);

            $siswaChunks[] = [
                'nis'              => $nis,
                'nisn'             => $nisn,
                'nama'             => $nama,
                'status'           => 'aktif',
                'foto'             => null,
                'nama_ortu'        => $this->namaOrtu[$i % count($this->namaOrtu)],
                'no_hp_ortu'       => '0812' . str_pad(rand(10000000, 99999999), 8, '0'),
                'no_hp_siswa'      => '0813' . str_pad(rand(10000000, 99999999), 8, '0'),
                'face_embedding'   => null,
                'face_registered_at' => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        // Bulk insert siswa dalam chunk 100
        foreach (array_chunk($siswaChunks, 100) as $chunk) {
            DB::table('siswas')->insert($chunk);
        }

        // Ambil semua ID siswa baru yang baru saja dibuat
        $newSiswaIds = DB::table('siswas')
            ->where('id', '>', $lastSiswaId)
            ->pluck('id')
            ->toArray();

        $this->command->info('   ✅ 500 siswa baru dibuat (ID ' . min($newSiswaIds) . '–' . max($newSiswaIds) . ')');

        // ══════════════════════════════════════════════
        // STEP 3: Assign Siswa ke Rombel (SiswaRombel)
        // ══════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('🏫 [3/4] Assign siswa ke rombel...');

        foreach ($newSiswaIds as $idx => $siswaId) {
            $rombelId = $allRombelIds[$idx % count($allRombelIds)];
            $siswaRombelChunks[] = [
                'siswa_id'           => $siswaId,
                'rombel_id'          => $rombelId,
                'tahun_ajaran_id'    => $taId,
                'status_keanggotaan' => 'aktif',
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }

        foreach (array_chunk($siswaRombelChunks, 200) as $chunk) {
            DB::table('siswa_rombels')->insert($chunk);
        }
        $this->command->info('   ✅ 500 siswa ter-assign ke ' . count($allRombelIds) . ' rombel');

        // ══════════════════════════════════════════════
        // STEP 4: Generate Absensi 1 Tahun Penuh
        // ══════════════════════════════════════════════
        $this->command->info('');
        $this->command->info('📅 [4/4] Membuat absensi 1 tahun penuh (Agustus 2025 – Juli 2026)...');
        $this->command->info('   ⚡ Menggunakan bulk insert untuk performa maksimal');
        $this->command->info('');

        // Hanya hapus absensi yang terkait siswa baru
        // (tidak hapus data existing siswa lama)

        // Hari libur nasional Indonesia TA 2025/2026
        $hariLibur = [
            '2025-08-17', // HUT RI
            '2025-09-05', // Maulid Nabi
            '2025-12-25', // Natal
            '2025-12-26', // Cuti bersama
            '2026-01-01', // Tahun Baru
            '2026-01-29', // Imlek
            '2026-03-20', // Hari Raya Nyepi
            '2026-03-27', // Wafat Isa Almasih
            '2026-04-02', // Isra Mi'raj
            '2026-04-03', // Awal Ramadan (perkiraan)
            '2026-05-01', // Hari Buruh
            '2026-05-14', // Kenaikan Isa Almasih
            '2026-05-26', // Hari Raya Waisak
            '2026-06-01', // Hari Pancasila
        ];

        // Libur semester (perkiraan)
        $liburSemester = [];
        for ($d = 22; $d <= 31; $d++) $liburSemester[] = "2025-12-$d";
        for ($d = 1; $d <= 7; $d++) $liburSemester[] = "2026-01-0$d";

        $semuaLibur = array_merge($hariLibur, $liburSemester);

        // Kumpulkan hari kerja
        $hariKerja = [];
        $tgl = Carbon::create(2025, 8, 1);
        $akhir = Carbon::create(2026, 7, 31);

        while ($tgl->lte($akhir)) {
            if (!in_array($tgl->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])
                && !in_array($tgl->toDateString(), $semuaLibur)) {
                $hariKerja[] = $tgl->toDateString();
            }
            $tgl->addDay();
        }

        $totalHariKerja = count($hariKerja);
        $this->command->info("   📆 Hari kerja: {$totalHariKerja} hari");
        $this->command->info("   👨‍🎓 Siswa baru: " . count($newSiswaIds));
        $estimasi = $totalHariKerja * count($newSiswaIds);
        $this->command->info("   📊 Estimasi record: ~" . number_format($estimasi) . " absensi");
        $this->command->info('');

        // Ambil mapping siswa_id -> siswa_rombel_id untuk siswa baru
        $srMap = DB::table('siswa_rombels')
            ->whereIn('siswa_id', $newSiswaIds)
            ->where('tahun_ajaran_id', $taId)
            ->pluck('id', 'siswa_id')
            ->toArray();

        // Profile siswa: 0-5=rajin, 6-7=biasa, 8-9=bermasalah
        $profils = [];
        foreach ($newSiswaIds as $sid) {
            $r = ($sid * 13) % 10;
            $profils[$sid] = $r < 6 ? 'rajin' : ($r < 8 ? 'biasa' : 'bermasalah');
        }

        $peluang = [
            'rajin'       => [75, 10, 7, 6, 2, 0],   // hadir, terlambat, izin, sakit, alpha, bolos
            'biasa'       => [60, 15, 10, 8, 5, 2],
            'bermasalah'  => [45, 18, 10, 7, 12, 8],
        ];

        $bar = $this->command->getOutput()->createProgressBar($totalHariKerja);
        $bar->setFormat(' %current%/%max% hari [%bar%] %percent:3s%% | ⏱ %elapsed:6s% | ETA %estimated:-6s%');
        $bar->start();

        $totalDibuat = 0;
        $batchSize   = 2000; // Insert 2000 record sekaligus
        $batch       = [];

        foreach ($hariKerja as $tanggal) {
            foreach ($newSiswaIds as $siswaId) {
                $profil   = $profils[$siswaId];
                $peluangArr = $peluang[$profil];
                $srId     = $srMap[$siswaId] ?? null;
                if (!$srId) continue;

                // Determine status via hash-based seed (deterministic)
                $seed = abs(crc32($tanggal . '-' . $siswaId)) % 100;
                $status = 'hadir';
                $labels = ['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'bolos'];
                $cum = 0;
                foreach ($peluangArr as $idx => $pct) {
                    $cum += $pct;
                    if ($seed < $cum) {
                        $status = $labels[$idx];
                        break;
                    }
                }

                $jamMasuk  = null;
                $jamPulang = null;
                $keterangan = null;
                $sumber = 'face';

                if ($status === 'hadir') {
                    $m = ($siswaId * 7 + abs(crc32($tanggal))) % 45 + 6 * 60 + 30;
                    $jamMasuk = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
                    $p = ($siswaId * 3 + abs(crc32($tanggal))) % 60 + 15 * 60 + 30;
                    $jamPulang = sprintf('%02d:%02d:00', intdiv($p, 60), $p % 60);
                    $sumber = ($siswaId % 2 === 0) ? 'face' : 'rfid';

                } elseif ($status === 'terlambat') {
                    $m = ($siswaId * 11 + abs(crc32($tanggal))) % 105 + 7 * 60 + 16;
                    $jamMasuk = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
                    $p = ($siswaId * 5 + abs(crc32($tanggal))) % 60 + 15 * 60 + 30;
                    $jamPulang = sprintf('%02d:%02d:00', intdiv($p, 60), $p % 60);
                    $menit = $m - (7 * 60 + 15);
                    $keterangan = 'Terlambat ' . $menit . ' menit';
                    $sumber = ($siswaId % 2 === 0) ? 'face' : 'rfid';

                } elseif ($status === 'alpha') {
                    $keterangan = 'Tidak hadir tanpa keterangan';

                } elseif ($status === 'bolos') {
                    $m = ($siswaId * 7 + abs(crc32($tanggal))) % 45 + 6 * 60 + 30;
                    $jamMasuk = sprintf('%02d:%02d:00', intdiv($m, 60), $m % 60);
                    $keterangan = 'Pulang sebelum jam pulang resmi';

                } elseif (in_array($status, ['izin', 'sakit'])) {
                    $keterangan = $status === 'sakit' ? 'Sakit' : 'Izin keperluan';
                }

                $createdAt = $jamMasuk
                    ? $tanggal . ' ' . $jamMasuk
                    : $tanggal . ' 13:00:00';

                $batch[] = [
                    'pemilik_type'    => 'siswa',
                    'pemilik_id'      => $siswaId,
                    'siswa_rombel_id' => $srId,
                    'tanggal'         => $tanggal,
                    'jam_masuk'       => $jamMasuk,
                    'jam_pulang'      => $jamPulang,
                    'status'          => $status,
                    'sumber_absen'    => in_array($status, ['alpha','izin','sakit']) ? 'sistem' : $sumber,
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
            $bar->advance();
        }

        // Insert sisa batch
        if (!empty($batch)) {
            DB::table('absensis')->insert($batch);
            $totalDibuat += count($batch);
        }

        $bar->finish();
        $this->command->newLine(2);

        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════╗');
        $this->command->info('║       SIMULASI EKSTRIM SELESAI! 🎉       ║');
        $this->command->info('╠══════════════════════════════════════════╣');
        $this->command->info('║  Rombel baru     : ' . str_pad(count($rombelsNew), 5) . '                  ║');
        $this->command->info('║  Siswa baru      : ' . str_pad(count($newSiswaIds), 5) . '                  ║');
        $this->command->info('║  Hari kerja      : ' . str_pad($totalHariKerja, 5) . '                  ║');
        $this->command->info('║  Absensi dibuat  : ' . str_pad(number_format($totalDibuat), 10) . '           ║');
        $this->command->info('║  Total DB        : ' . str_pad(number_format(DB::table('absensis')->count()), 10) . '           ║');
        $this->command->info('╚══════════════════════════════════════════╝');
    }
}
