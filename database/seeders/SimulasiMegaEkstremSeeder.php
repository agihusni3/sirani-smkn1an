<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimulasiMegaEkstremSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 MEMULAI SIMULASI ULTRA EKSTREM: TARGET 1.8+ JUTA DATA ABSENSI (5X LIPAT)...');

        // Optimasi mesin database SQLite untuk volume jutaan baris
        DB::statement('PRAGMA journal_mode = WAL;');
        DB::statement('PRAGMA synchronous = OFF;');
        DB::statement('PRAGMA cache_size = 100000;');
        DB::statement('PRAGMA temp_store = MEMORY;');
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Bersihkan data absensi lama
        DB::statement('DELETE FROM kasus_disiplin_dokumens');
        DB::statement('DELETE FROM kasus_disiplins');
        DB::statement('DELETE FROM notifikasi_ortus');
        DB::statement('DELETE FROM izin_gurus');
        DB::statement('DELETE FROM izin_siswas');
        DB::statement('DELETE FROM absensis');
        $this->command->info('✅ Data absensi lama dibersihkan.');

        // Rentang waktu: 15 Tahun Histori (2011 s.d. 2026) untuk mensimulasikan beban 1.8M+ record
        $endDate   = Carbon::create(2026, 8, 29);
        $startDate = Carbon::create(2011, 8, 29);

        $hariKerja = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            // Hari kerja sekolah Senin - Jumat
            if (!$curr->isWeekend()) {
                // Skip libur akhir tahun & pertengahan tahun
                $m = $curr->month;
                $d = $curr->day;
                $isHoliday = ($m == 12 && $d >= 22) || ($m == 1 && $d <= 5) || ($m == 6 && $d >= 22) || ($m == 7 && $d <= 10);
                if (!$isHoliday) {
                    $hariKerja[] = $curr->toDateString();
                }
            }
            $curr->addDay();
        }

        $totalHari = count($hariKerja);
        $siswas = DB::table('siswas')->whereIn('status', ['aktif', 'pkl'])->select('id', 'status')->get();
        $gurus  = DB::table('gurus')->where('status', 'aktif')->select('id')->get();
        $totalSiswa = count($siswas);
        $totalGuru  = count($gurus);

        $targetRecord = ($totalSiswa + $totalGuru) * $totalHari;
        $this->command->info("📅 Total Hari Sekolah: " . number_format($totalHari) . " hari");
        $this->command->info("👥 Total Partisipan   : {$totalSiswa} Siswa + {$totalGuru} Guru = " . ($totalSiswa + $totalGuru));
        $this->command->info("🎯 Target Beban Data  : " . number_format($targetRecord) . " BARIS ABSENSI!");

        $siswaRombelMap = DB::table('siswa_rombels')
            ->where('status_keanggotaan', 'aktif')
            ->pluck('rombel_id', 'siswa_id')
            ->toArray();

        $pdo = DB::getPdo();
        $stmtAbsen = $pdo->prepare("INSERT OR IGNORE INTO absensis 
            (pemilik_type, pemilik_id, siswa_rombel_id, tanggal, jam_masuk, jam_pulang, status, sumber_absen, keterangan, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $now = now()->toDateTimeString();
        $insertedCount = 0;
        $t0 = microtime(true);

        $pdo->beginTransaction();

        // ── 1. GENERATE SISWA (526 SISWA × 3300+ HARI) ──
        $this->command->info("⚡ Memproses data kehadiran siswa...");
        foreach ($siswas as $idx => $s) {
            $rombelId = $siswaRombelMap[$s->id] ?? null;
            $isPkl    = ($s->status === 'pkl');
            $baseJam  = 35 + (($s->id * 3) % 25); // 06:35 - 06:59

            // Karakter siswa
            $karakter = ($s->id % 10); // 0-6 rajin, 7-8 biasa, 9 sering bermasalah

            foreach ($hariKerja as $tglStr) {
                if ($isPkl) {
                    $jamMasuk  = '07:30:' . sprintf('%02d', rand(0, 59));
                    $jamPulang = '16:00:' . sprintf('%02d', rand(0, 59));
                    $status    = 'hadir';
                    $ket       = 'PKL';
                } else {
                    $r = rand(1, 100);
                    if ($karakter <= 6) {
                        // 88% hadir, 8% telat, 2% izin, 2% sakit
                        if ($r <= 88) {
                            $jamMasuk  = sprintf('06:%02d:%02d', max(0, min(59, $baseJam + rand(-5, 5))), rand(0, 59));
                            $jamPulang = sprintf('15:%02d:%02d', rand(30, 58), rand(0, 59));
                            $status    = 'hadir'; $ket = null;
                        } elseif ($r <= 96) {
                            $jamMasuk  = sprintf('07:%02d:%02d', rand(16, 45), rand(0, 59));
                            $jamPulang = sprintf('15:%02d:%02d', rand(30, 58), rand(0, 59));
                            $status    = 'terlambat'; $ket = 'Terlambat';
                        } elseif ($r <= 98) {
                            $jamMasuk = null; $jamPulang = null; $status = 'izin'; $ket = 'Izin Urusan Keluarga';
                        } else {
                            $jamMasuk = null; $jamPulang = null; $status = 'sakit'; $ket = 'Sakit';
                        }
                    } elseif ($karakter <= 8) {
                        // 78% hadir, 12% telat, 5% izin, 3% sakit, 2% alpha
                        if ($r <= 78) {
                            $jamMasuk  = sprintf('06:%02d:%02d', rand(40, 59), rand(0, 59));
                            $jamPulang = sprintf('15:%02d:%02d', rand(30, 55), rand(0, 59));
                            $status    = 'hadir'; $ket = null;
                        } elseif ($r <= 90) {
                            $jamMasuk  = sprintf('07:%02d:%02d', rand(16, 45), rand(0, 59));
                            $jamPulang = sprintf('15:%02d:%02d', rand(30, 55), rand(0, 59));
                            $status    = 'terlambat'; $ket = 'Terlambat';
                        } elseif ($r <= 95) {
                            $jamMasuk = null; $jamPulang = null; $status = 'izin'; $ket = 'Izin';
                        } elseif ($r <= 98) {
                            $jamMasuk = null; $jamPulang = null; $status = 'sakit'; $ket = 'Sakit';
                        } else {
                            $jamMasuk = null; $jamPulang = null; $status = 'alpha'; $ket = 'Alpha';
                        }
                    } else {
                        // Bermasalah
                        if ($r <= 55) {
                            $jamMasuk  = sprintf('06:%02d:%02d', rand(45, 59), rand(0, 59));
                            $jamPulang = sprintf('15:%02d:%02d', rand(30, 50), rand(0, 59));
                            $status    = 'hadir'; $ket = null;
                        } elseif ($r <= 75) {
                            $jamMasuk  = sprintf('07:%02d:%02d', rand(16, 55), rand(0, 59));
                            $jamPulang = sprintf('15:%02d:%02d', rand(30, 50), rand(0, 59));
                            $status    = 'terlambat'; $ket = 'Terlambat';
                        } elseif ($r <= 85) {
                            $jamMasuk = null; $jamPulang = null; $status = 'alpha'; $ket = 'Alpha';
                        } elseif ($r <= 93) {
                            $jamMasuk  = sprintf('07:%02d:%02d', rand(10, 30), rand(0, 59));
                            $jamPulang = null;
                            $status    = 'bolos'; $ket = 'Bolos';
                        } else {
                            $jamMasuk = null; $jamPulang = null; $status = 'sakit'; $ket = 'Sakit';
                        }
                    }
                }

                $stmtAbsen->execute([
                    'siswa',
                    $s->id,
                    $rombelId,
                    $tglStr,
                    $jamMasuk,
                    $jamPulang,
                    $status,
                    'simulasi_ultra',
                    $ket,
                    $now,
                    $now
                ]);

                $insertedCount++;
                if ($insertedCount % 50000 === 0) {
                    $pdo->commit();
                    $pdo->beginTransaction();
                    $this->command->getOutput()->write("\r   ⏳ Progres: " . number_format($insertedCount) . " baris tersimpan...");
                }
            }
        }

        // ── 2. GENERATE GURU ──
        $this->command->newLine();
        $this->command->info("⚡ Memproses data kehadiran guru...");
        foreach ($gurus as $g) {
            $baseJam = 28 + (($g->id * 4) % 20); // 06:28 - 06:48

            foreach ($hariKerja as $tglStr) {
                $r = rand(1, 100);
                if ($r <= 90) {
                    $jamMasuk  = sprintf('06:%02d:%02d', max(0, min(59, $baseJam + rand(-5, 5))), rand(0, 59));
                    $jamPulang = sprintf('15:%02d:%02d', rand(30, 59), rand(0, 59));
                    $status    = 'hadir'; $ket = null;
                } elseif ($r <= 95) {
                    $jamMasuk  = sprintf('07:%02d:%02d', rand(16, 35), rand(0, 59));
                    $jamPulang = sprintf('15:%02d:%02d', rand(30, 59), rand(0, 59));
                    $status    = 'terlambat'; $ket = 'Terlambat';
                } elseif ($r <= 98) {
                    $jamMasuk = null; $jamPulang = null; $status = 'dinas_luar'; $ket = 'Dinas Luar';
                } else {
                    $jamMasuk = null; $jamPulang = null; $status = 'sakit'; $ket = 'Sakit';
                }

                $stmtAbsen->execute([
                    'guru',
                    $g->id,
                    null,
                    $tglStr,
                    $jamMasuk,
                    $jamPulang,
                    $status,
                    'simulasi_ultra',
                    $ket,
                    $now,
                    $now
                ]);

                $insertedCount++;
                if ($insertedCount % 50000 === 0) {
                    $pdo->commit();
                    $pdo->beginTransaction();
                    $this->command->getOutput()->write("\r   ⏳ Progres: " . number_format($insertedCount) . " baris tersimpan...");
                }
            }
        }

        $pdo->commit();
        DB::statement('PRAGMA foreign_keys = ON;');

        $t1 = microtime(true);
        $durSec = round($t1 - $t0, 2);

        $totalFinal = DB::table('absensis')->count();
        $this->command->newLine();
        $this->command->info("🎉 SEEDER ULTRA EKSTREM SELESAI!");
        $this->command->info("📊 Total Baris Absensi di Database: " . number_format($totalFinal) . " DATA");
        $this->command->info("⏱️ Waktu Injeksi Data: {$durSec} detik");
    }
}
