<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\IzinSiswa;
use App\Models\SiswaRombel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AbsensiSatuBulanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai seeding absensi 1 bulan (Agustus 2026)...');

        $siswaRombels = SiswaRombel::where('status_keanggotaan', 'aktif')
            ->whereNotNull('siswa_id')
            ->with('siswa')
            ->get();

        if ($siswaRombels->isEmpty()) {
            $this->command->warn('Tidak ada SiswaRombel aktif. Seeder dibatalkan.');
            return;
        }

        $this->command->info("Ditemukan {$siswaRombels->count()} siswa aktif.");

        $hariLiburNasional = ['2026-08-17'];
        $hariKerja = [];
        $tanggal = Carbon::create(2026, 8, 1);
        $akhir   = Carbon::create(2026, 8, 29);
        while ($tanggal->lte($akhir)) {
            if (!in_array($tanggal->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])
                && !in_array($tanggal->toDateString(), $hariLiburNasional)) {
                $hariKerja[] = $tanggal->toDateString();
            }
            $tanggal->addDay();
        }

        $this->command->info('Hari kerja: ' . count($hariKerja) . ' hari (libur HUT RI 17 Agustus dilewati).');

        $deleted = Absensi::whereBetween('tanggal', ['2026-08-01', '2026-08-29'])->delete();
        IzinSiswa::whereBetween('tanggal', ['2026-08-01', '2026-08-29'])->delete();
        $this->command->info("Hapus {$deleted} data absensi lama Agustus 2026.");

        $totalDibuat = 0;
        $totalIzin   = 0;
        $bar = $this->command->getOutput()->createProgressBar($siswaRombels->count());
        $bar->start();

        foreach ($siswaRombels as $sr) {
            $siswaId = (int) $sr->siswa_id;
            $profil  = $this->profilSiswa($siswaId);

            foreach ($hariKerja as $tgl) {
                $status = $this->randomStatus($profil, $tgl, $siswaId);

                if (in_array($status, ['izin', 'sakit'])) {
                    IzinSiswa::create([
                        'siswa_id'       => $siswaId,
                        'tanggal'        => $tgl,
                        'jenis'          => $status,
                        'keterangan'     => $status === 'sakit' ? $this->keteranganSakit() : $this->keteranganIzin(),
                        'status'         => 'disetujui',
                        'disetujui_oleh' => 'Guru Piket',
                        'created_at'     => Carbon::parse($tgl . ' 07:30:00'),
                        'updated_at'     => Carbon::parse($tgl . ' 07:30:00'),
                    ]);
                    $totalIzin++;

                    Absensi::create([
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $siswaId,
                        'siswa_rombel_id' => $sr->id,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => $status,
                        'sumber_absen'    => 'piket',
                        'keterangan'      => $status === 'sakit' ? 'Sakit - dicatat piket' : 'Izin - dicatat piket',
                        'created_at'      => Carbon::parse($tgl . ' 08:00:00'),
                        'updated_at'      => Carbon::parse($tgl . ' 08:00:00'),
                    ]);

                } elseif ($status === 'alpha') {
                    Absensi::create([
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $siswaId,
                        'siswa_rombel_id' => $sr->id,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => null,
                        'jam_pulang'      => null,
                        'status'          => 'alpha',
                        'sumber_absen'    => 'sistem',
                        'keterangan'      => 'Tidak hadir tanpa keterangan',
                        'created_at'      => Carbon::parse($tgl . ' 13:00:00'),
                        'updated_at'      => Carbon::parse($tgl . ' 13:00:00'),
                    ]);

                } elseif ($status === 'bolos') {
                    $jam = $this->jamMasuk(false);
                    Absensi::create([
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $siswaId,
                        'siswa_rombel_id' => $sr->id,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => $jam,
                        'jam_pulang'      => null,
                        'status'          => 'bolos',
                        'sumber_absen'    => 'face',
                        'keterangan'      => 'Pulang sebelum jam pulang resmi',
                        'created_at'      => Carbon::parse($tgl . ' ' . $jam),
                        'updated_at'      => Carbon::parse($tgl . ' ' . $jam),
                    ]);

                } else {
                    $terlambat = ($status === 'terlambat');
                    $jamMasuk  = $this->jamMasuk($terlambat);
                    $jamPulang = $this->jamPulang();
                    Absensi::create([
                        'pemilik_type'    => 'siswa',
                        'pemilik_id'      => $siswaId,
                        'siswa_rombel_id' => $sr->id,
                        'tanggal'         => $tgl,
                        'jam_masuk'       => $jamMasuk,
                        'jam_pulang'      => $jamPulang,
                        'status'          => $terlambat ? 'terlambat' : 'hadir',
                        'sumber_absen'    => rand(0, 1) ? 'face' : 'rfid',
                        'keterangan'      => $terlambat ? 'Terlambat ' . rand(5, 45) . ' menit' : null,
                        'created_at'      => Carbon::parse($tgl . ' ' . $jamMasuk),
                        'updated_at'      => Carbon::parse($tgl . ' ' . $jamPulang),
                    ]);
                }

                $totalDibuat++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("Selesai! Total absensi dibuat : {$totalDibuat}");
        $this->command->info("Total izin/sakit dibuat      : {$totalIzin}");
        $this->command->info("Rata-rata per siswa          : " . round($totalDibuat / max($siswaRombels->count(), 1), 1) . " hari");
    }

    private function profilSiswa(int $id): string
    {
        $r = ($id * 17) % 10;
        if ($r < 6) return 'rajin';
        if ($r < 8) return 'biasa';
        return 'bermasalah';
    }

    private function randomStatus(string $profil, string $tanggal, int $siswaId): string
    {
        $seed = abs(crc32($tanggal . '-' . $siswaId)) % 100;
        $p = match ($profil) {
            'rajin'   => ['hadir' => 75, 'terlambat' => 10, 'izin' => 7, 'sakit' => 6, 'alpha' => 2, 'bolos' => 0],
            'biasa'   => ['hadir' => 60, 'terlambat' => 15, 'izin' => 10, 'sakit' => 8, 'alpha' => 5, 'bolos' => 2],
            default   => ['hadir' => 45, 'terlambat' => 18, 'izin' => 10, 'sakit' => 7, 'alpha' => 12, 'bolos' => 8],
        };
        $cum = 0;
        foreach ($p as $s => $pct) {
            $cum += $pct;
            if ($seed < $cum) return $s;
        }
        return 'hadir';
    }

    private function jamMasuk(bool $terlambat): string
    {
        $menit = $terlambat ? rand(7 * 60 + 16, 9 * 60) : rand(6 * 60 + 30, 7 * 60 + 14);
        return sprintf('%02d:%02d:00', intdiv($menit, 60), $menit % 60);
    }

    private function jamPulang(): string
    {
        $menit = rand(15 * 60 + 30, 16 * 60 + 30);
        return sprintf('%02d:%02d:00', intdiv($menit, 60), $menit % 60);
    }

    private function keteranganSakit(): string
    {
        return ['Demam', 'Sakit kepala', 'Flu dan batuk', 'Sakit perut', 'Diare', 'Sakit gigi'][rand(0, 5)];
    }

    private function keteranganIzin(): string
    {
        return ['Keperluan keluarga', 'Ada acara keluarga', 'Mengurus administrasi',
                'Keperluan mendesak', 'Izin menghadiri acara', 'Perjalanan luar kota'][rand(0, 5)];
    }
}
