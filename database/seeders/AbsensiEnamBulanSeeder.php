<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinPelanggaran;
use App\Models\KasusDisiplinReward;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiEnamBulanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('🚀 GENERATOR DATA DUMMY ABSENSI 6 BULAN (APRIL – SEPTEMBER 2026)');
        $this->command->info('   SMK NEGERI 1 AIR NANINGAN — SISTEM SIRANI');
        $this->command->info('════════════════════════════════════════════════════════════════');

        DB::statement('PRAGMA journal_mode=WAL;');
        DB::statement('PRAGMA synchronous=OFF;');
        DB::statement('PRAGMA cache_size=10000;');

        // 1. Rentang Tanggal 6 Bulan Penuh (1 April 2026 s/d 25 September 2026)
        $startDate = Carbon::create(2026, 4, 1);
        $endDate   = Carbon::create(2026, 9, 25);

        // 2. Daftar Hari Libur Nasional & Kalender Pendidikan
        $liburNasional = [
            '2026-04-03', // Wafat Isa Almasih
            '2026-05-01', // Hari Buruh
            '2026-05-14', // Kenaikan Isa Almasih
            '2026-05-26', // Hari Raya Idul Adha / Waisak
            '2026-06-01', // Hari Lahir Pancasila
            '2026-08-17', // HUT Kemerdekaan RI
        ];

        // Ambil hari libur dari database jika ada
        $dbLiburs = [];
        try {
            $hariLiburs = HariLibur::where('tanggal_mulai', '<=', $endDate->toDateString())
                ->where('tanggal_selesai', '>=', $startDate->toDateString())
                ->get();
            foreach ($hariLiburs as $hl) {
                $cur = Carbon::parse($hl->tanggal_mulai);
                $endHl = Carbon::parse($hl->tanggal_selesai);
                while ($cur->lte($endHl)) {
                    $dbLiburs[] = $cur->toDateString();
                    $cur->addDay();
                }
            }
        } catch (\Throwable $e) {}

        $semuaLibur = array_unique(array_merge($liburNasional, $dbLiburs));

        // 3. Kumpulkan Hari Efektif Sekolah (Senin - Jumat)
        $hariKerja = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $tglStr = $curr->toDateString();
            if (!$curr->isWeekend() && !in_array($tglStr, $semuaLibur)) {
                $hariKerja[] = $tglStr;
            }
            $curr->addDay();
        }

        $this->command->info("📅 Rentang Tanggal : {$startDate->toDateString()} s/d {$endDate->toDateString()}");
        $this->command->info("🗓  Hari Efektif    : " . count($hariKerja) . " hari kerja sekolah (Sabtu/Minggu & Libur dilewati)");

        // 4. Bersihkan data absensi & izin pada rentang tanggal ini
        $this->command->info('🧹 Membersihkan rekaman absensi lama pada periode 6 bulan ini...');
        Absensi::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])->delete();
        IzinSiswa::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])->delete();
        IzinGuru::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])->delete();
        KasusDisiplinReward::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])->delete();
        KasusDisiplinPelanggaran::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])->delete();

        // 5. Muat Data Siswa & Rombel
        $siswas = Siswa::whereIn('status', ['aktif', 'pkl'])->get();
        if ($siswas->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada siswa aktif di database. Seeder dihentikan.');
            return;
        }

        $siswaRombelMap = SiswaRombel::where('status_keanggotaan', 'aktif')
            ->pluck('id', 'siswa_id')
            ->toArray();

        $this->command->info("👨‍🎓 Memproses absensi untuk {$siswas->count()} siswa...");

        $batchAbsensi = [];
        $batchIzin = [];
        $now = now()->toDateTimeString();

        $alasanIzin = [
            'Keperluan keluarga mendesak',
            'Menghadiri acara pernikahan keluarga',
            'Mengurus dokumen kependudukan',
            'Ada urusan keluarga di luar kota',
            'Menjaga orang tua/anggota keluarga sakit'
        ];

        $alasanSakit = [
            'Demam tinggi & flu batuk',
            'Sakit kepala berat / migrain',
            'Sakit perut / gangguan lambung',
            'Pemeriksaan medis di Puskesmas',
            'Gejala tipes disarankan istirahat dokter'
        ];

        $alasanTerlambat = [
            'Kendaraan mogok / ban bocor di jalan',
            'Hujan deras di perjalanan',
            'Terjebak antrean jalan raya',
            'Rantai motor putus',
            'Membantu orang tua sebelum berangkat'
        ];

        // 6. Generate Absensi Siswa
        $bar = $this->command->getOutput()->createProgressBar($siswas->count());
        $bar->start();

        foreach ($siswas as $s) {
            $srId = $siswaRombelMap[$s->id] ?? null;
            $isPkl = ($s->status === 'pkl');

            // Profil kedisiplinan per siswa (berbasis hash id agar deterministik)
            $seedKarakter = abs(crc32('karakter-' . $s->id)) % 100;
            if ($seedKarakter < 72) {
                // Sangat Disiplin (Hadir 88%, Terlambat 5%, Izin 3%, Sakit 2%, Alpha 2%, Bolos 0%)
                $p = [88, 5, 3, 2, 2, 0];
            } elseif ($seedKarakter < 90) {
                // Biasa / Sedang (Hadir 78%, Terlambat 10%, Izin 4%, Sakit 3%, Alpha 4%, Bolos 1%)
                $p = [78, 10, 4, 3, 4, 1];
            } else {
                // Perlu Pembinaan (Hadir 62%, Terlambat 16%, Izin 5%, Sakit 4%, Alpha 8%, Bolos 5%)
                $p = [62, 16, 5, 4, 8, 5];
            }

            foreach ($hariKerja as $tglStr) {
                if ($isPkl) {
                    $batchAbsensi[] = [
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $s->id,
                        'siswa_rombel_id' => $srId,
                        'tanggal'         => $tglStr,
                        'jam_masuk'       => '07:30:00',
                        'jam_pulang'      => '16:00:00',
                        'status'          => 'hadir',
                        'sumber_absen'    => 'kios_wajah',
                        'keterangan'      => 'Siswa Kegiatan PKL / Magang Industri',
                        'created_at'      => $tglStr . ' 07:30:00',
                        'updated_at'      => $tglStr . ' 16:00:00',
                    ];
                    continue;
                }

                $roll = abs(crc32($tglStr . '-siswa-' . $s->id)) % 100;

                if ($roll < $p[0]) {
                    // HADIR TEPAT WAKTU (06:40 s/d 07:14)
                    $totalMenitMasuk = (6 * 60 + 40) + (abs(crc32($tglStr . '-m-' . $s->id)) % 35);
                    $jamMasuk = sprintf('%02d:%02d:00', intdiv($totalMenitMasuk, 60), $totalMenitMasuk % 60);

                    $totalMenitPulang = (15 * 60 + 30) + (abs(crc32($tglStr . '-p-' . $s->id)) % 30);
                    $jamPulang = sprintf('%02d:%02d:00', intdiv($totalMenitPulang, 60), $totalMenitPulang % 60);
                    $status = 'hadir';
                    $sumber = (abs(crc32($tglStr . '-s-' . $s->id)) % 10 < 7) ? 'kios_wajah' : 'rfid';
                    $ket = null;

                } elseif ($roll < $p[0] + $p[1]) {
                    // TERLAMBAT (07:16 s/d 08:05)
                    $menitTelat = 1 + (abs(crc32($tglStr . '-telat-' . $s->id)) % 50);
                    $totalMenitMasuk = (7 * 60 + 15) + $menitTelat;
                    $jamMasuk = sprintf('%02d:%02d:00', intdiv($totalMenitMasuk, 60), $totalMenitMasuk % 60);

                    $totalMenitPulang = (15 * 60 + 30) + (abs(crc32($tglStr . '-p-' . $s->id)) % 30);
                    $jamPulang = sprintf('%02d:%02d:00', intdiv($totalMenitPulang, 60), $totalMenitPulang % 60);
                    $status = 'terlambat';
                    $sumber = 'kios_wajah';
                    $alasanIdx = abs(crc32($tglStr . '-altelat-' . $s->id)) % count($alasanTerlambat);
                    $ket = "Terlambat {$menitTelat} menit - " . $alasanTerlambat[$alasanIdx];

                } elseif ($roll < $p[0] + $p[1] + $p[2]) {
                    // IZIN
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'izin';
                    $sumber = 'manual_izin_piket';
                    $alasanIdx = abs(crc32($tglStr . '-izin-' . $s->id)) % count($alasanIzin);
                    $ket = $alasanIzin[$alasanIdx];

                    $batchIzin[] = [
                        'siswa_id'       => $s->id,
                        'tanggal'        => $tglStr,
                        'jenis'          => 'izin',
                        'keterangan'     => $ket,
                        'file_pendukung' => null,
                        'status'         => 'disetujui',
                        'disetujui_oleh' => 'Guru Piket',
                        'created_at'     => $tglStr . ' 07:20:00',
                        'updated_at'     => $tglStr . ' 07:25:00',
                    ];

                } elseif ($roll < $p[0] + $p[1] + $p[2] + $p[3]) {
                    // SAKIT
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'sakit';
                    $sumber = 'manual_izin_piket';
                    $alasanIdx = abs(crc32($tglStr . '-sakit-' . $s->id)) % count($alasanSakit);
                    $ket = $alasanSakit[$alasanIdx];

                    $batchIzin[] = [
                        'siswa_id'       => $s->id,
                        'tanggal'        => $tglStr,
                        'jenis'          => 'sakit',
                        'keterangan'     => $ket,
                        'file_pendukung' => null,
                        'status'         => 'disetujui',
                        'disetujui_oleh' => 'Guru Piket',
                        'created_at'     => $tglStr . ' 07:15:00',
                        'updated_at'     => $tglStr . ' 07:20:00',
                    ];

                } elseif ($roll < $p[0] + $p[1] + $p[2] + $p[3] + $p[4]) {
                    // ALPHA
                    $jamMasuk = null;
                    $jamPulang = null;
                    $status = 'alpha';
                    $sumber = 'auto_kunci_piket';
                    $ket = 'Tidak hadir tanpa keterangan';

                } else {
                    // BOLOS
                    $jamMasuk = '07:05:00';
                    $jamPulang = null;
                    $status = 'bolos';
                    $sumber = 'kios_wajah';
                    $ket = 'Meninggalkan jam pelajaran sekolah tanpa izin piket';
                }

                $batchAbsensi[] = [
                    'pemilik_type'    => 'siswa',
                    'pemilik_id'      => $s->id,
                    'siswa_rombel_id' => $srId,
                    'tanggal'         => $tglStr,
                    'jam_masuk'       => $jamMasuk,
                    'jam_pulang'      => $jamPulang,
                    'status'          => $status,
                    'sumber_absen'    => $sumber,
                    'keterangan'      => $ket,
                    'created_at'      => $tglStr . ' ' . ($jamMasuk ?: '07:30:00'),
                    'updated_at'      => $tglStr . ' ' . ($jamPulang ?: ($jamMasuk ?: '16:00:00')),
                ];

                // Chunk insert agar performa kencang & hemat memori
                if (count($batchAbsensi) >= 500) {
                    foreach (array_chunk($batchAbsensi, 100) as $chunk) {
                        DB::table('absensis')->insert($chunk);
                    }
                    $batchAbsensi = [];
                }
                if (count($batchIzin) >= 300) {
                    foreach (array_chunk($batchIzin, 100) as $chunk) {
                        DB::table('izin_siswas')->insert($chunk);
                    }
                    $batchIzin = [];
                }
            }
            $bar->advance();
        }

        if (!empty($batchAbsensi)) {
            foreach (array_chunk($batchAbsensi, 100) as $chunk) {
                DB::table('absensis')->insert($chunk);
            }
            $batchAbsensi = [];
        }
        if (!empty($batchIzin)) {
            foreach (array_chunk($batchIzin, 100) as $chunk) {
                DB::table('izin_siswas')->insert($chunk);
            }
            $batchIzin = [];
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ Seluruh data absensi siswa berhasil dibuat.');

        // 7. Generate Absensi Guru & Pegawai
        $gurus = Guru::where('status', 'aktif')->get();
        if ($gurus->isNotEmpty()) {
            $this->command->info("👩‍🏫 Memproses absensi untuk {$gurus->count()} Guru & Pegawai...");
            $batchGuruAbsen = [];

            foreach ($gurus as $g) {
                $isKepsek = str_contains(strtolower($g->jabatan ?? ''), 'kepala sekolah');
                $pG = $isKepsek ? [94, 3, 2, 1] : [88, 6, 3, 3]; // Hadir, Terlambat, Izin/Dinas, Sakit

                foreach ($hariKerja as $tglStr) {
                    $roll = abs(crc32($tglStr . '-guru-' . $g->id)) % 100;
                    if ($roll < $pG[0]) {
                        $m = 30 + (abs(crc32($tglStr . '-gm-' . $g->id)) % 25);
                        $jamMasuk = sprintf('06:%02d:00', $m);
                        $jamPulang = sprintf('15:%02d:00', 30 + (abs(crc32($tglStr . '-gp-' . $g->id)) % 30));
                        $status = 'hadir';
                        $ket = null;
                    } elseif ($roll < $pG[0] + $pG[1]) {
                        $m = 16 + (abs(crc32($tglStr . '-gt-' . $g->id)) % 25);
                        $jamMasuk = sprintf('07:%02d:00', $m);
                        $jamPulang = sprintf('15:%02d:00', 30 + (abs(crc32($tglStr . '-gp-' . $g->id)) % 30));
                        $status = 'terlambat';
                        $ket = "Terlambat {$m} menit";
                    } elseif ($roll < $pG[0] + $pG[1] + $pG[2]) {
                        $jamMasuk = null;
                        $jamPulang = null;
                        $status = 'izin';
                        $ket = 'Dinas Luar / Kegiatan Instansi Cabang Dinas';
                    } else {
                        $jamMasuk = null;
                        $jamPulang = null;
                        $status = 'sakit';
                        $ket = 'Sakit (Izin Kesehatan)';
                    }

                    $batchGuruAbsen[] = [
                        'pemilik_type'    => 'guru',
                        'pemilik_id'      => $g->id,
                        'siswa_rombel_id' => null,
                        'tanggal'         => $tglStr,
                        'jam_masuk'       => $jamMasuk,
                        'jam_pulang'      => $jamPulang,
                        'status'          => $status,
                        'sumber_absen'    => 'kios_wajah',
                        'keterangan'      => $ket,
                        'created_at'      => $tglStr . ' ' . ($jamMasuk ?: '07:15:00'),
                        'updated_at'      => $tglStr . ' ' . ($jamPulang ?: ($jamMasuk ?: '16:00:00')),
                    ];
                }
            }

            if (!empty($batchGuruAbsen)) {
                foreach (array_chunk($batchGuruAbsen, 100) as $chunk) {
                    DB::table('absensis')->insert($chunk);
                }
            }
            $this->command->info('✅ Seluruh data absensi guru berhasil dibuat.');
        }

        // 8. Sinkronisasi Dossier Karakter & Kasus Kedisiplinan Siswa
        $this->command->info('⚖️  Menyinkronkan akumulasi poin & kasus kedisiplinan siswa...');
        $totalKasus = 0;
        $totalReward = 0;

        foreach ($siswas as $s) {
            try {
                $kasus = KasusDisiplin::syncFromPresensi($s->id);
                if ($kasus && $kasus->exists) {
                    $totalKasus++;

                    // Berikan reward realistis bagi siswa yang aktif dan memiliki poin
                    if ($kasus->total_poin_pelanggaran >= 15) {
                        KasusDisiplinReward::create([
                            'kasus_disiplin_id' => $kasus->id,
                            'siswa_id'          => $s->id,
                            'nama_tindakan'     => 'Petugas Sholat Berjamaah & Kebersihan',
                            'poin_dikurangi'    => 5,
                            'tanggal'           => Carbon::create(2026, 9, 15)->toDateString(),
                            'dicatat_oleh'      => 'Guru PAI & Pembina Karakter',
                            'catatan'           => 'Aktif membantu ketertiban sholat dhuhur berjamaah di musholla sekolah',
                        ]);
                        $totalReward++;

                        if ($kasus->total_poin_pelanggaran >= 35) {
                            KasusDisiplinReward::create([
                                'kasus_disiplin_id' => $kasus->id,
                                'siswa_id'          => $s->id,
                                'nama_tindakan'     => 'Kehadiran Disiplin Beruntun (Streak 14 Hari)',
                                'poin_dikurangi'    => 5,
                                'tanggal'           => Carbon::create(2026, 9, 22)->toDateString(),
                                'dicatat_oleh'      => 'Sistem Otomatis SIRANI',
                                'catatan'           => 'Tercatat hadir tepat waktu selama 14 hari sekolah berturut-turut',
                            ]);
                            $totalReward++;
                        }

                        // Resync poin bersih setelah reward ditambahkan
                        KasusDisiplin::syncFromPresensi($s->id);
                    }
                }
            } catch (\Throwable $e) {}
        }

        $totalAbsensiSiswa = DB::table('absensis')->where('pemilik_type', 'siswa')->count();
        $totalAbsensiGuru  = DB::table('absensis')->where('pemilik_type', 'guru')->count();
        $totalIzinSiswa    = DB::table('izin_siswas')->count();

        $this->command->newLine();
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('🎉 SEEDING DATA DUMMY 6 BULAN SELESAI DENGAN SUKSES!');
        $this->command->info("   📊 Total Record Absensi Siswa : {$totalAbsensiSiswa} baris");
        $this->command->info("   👨‍🏫 Total Record Absensi Guru  : {$totalAbsensiGuru} baris");
        $this->command->info("   📝 Total Surat Izin/Sakit     : {$totalIzinSiswa} surat");
        $this->command->info("   📋 Total Kasus Disiplin Siswa : {$totalKasus} siswa");
        $this->command->info("   ⭐ Total Reward/Apresiasi     : {$totalReward} tindakan positif");
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->newLine();
    }
}
