<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Services\FaceScanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RfidGuruScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_bisa_scan_wajah_masuk_dan_pulang(): void
    {
        $guru = Guru::create([
            'nip' => '198501012010011001',
            'nama' => 'Drs. H. Ahmad Dahlan',
            'jabatan' => 'Kepala Sekolah',
            'status' => 'aktif',
            'face_embedding' => [0.1, 0.2, 0.3],
            'face_registered_at' => now(),
        ]);

        $service = new FaceScanService();

        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-14 07:05:00'));

        // Scan 1: Jam Masuk
        $resMasuk = $service->scanPerson('guru', $guru->id, 'face_kiosk');
        $this->assertEquals('success', $resMasuk['status']);
        $this->assertEquals('jam_masuk', $resMasuk['type']);
        $this->assertNotEmpty($resMasuk['data']['foto_url']);
        $this->assertEquals('Drs. H. Ahmad Dahlan', $resMasuk['data']['nama']);
        $this->assertEquals('Kepala Sekolah', $resMasuk['data']['rombel_atau_jabatan']);

        // Scan 2 (Instant Double Tap / Debounce): Belum jam pulang
        $resDebounce = $service->scanPerson('guru', $guru->id, 'face_kiosk');
        $this->assertEquals('info', $resDebounce['status']);
        $this->assertEquals('sudah_masuk', $resDebounce['type']);

        // Majukan waktu ke jam kepulangan pada hari yang sama
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-14 15:30:00'));

        // Scan 3: Jam Pulang Berhasil
        $resPulang = $service->scanPerson('guru', $guru->id, 'face_kiosk');
        $this->assertEquals('success', $resPulang['status']);
        $this->assertEquals('jam_pulang', $resPulang['type']);
        $this->assertNotEmpty($resPulang['data']['foto_url']);

        \Illuminate\Support\Carbon::setTestNow(); // Reset time mock
    }

    public function test_guru_honor_dapat_scan_masuk_pada_jadwal_dan_di_luar_jadwal_mengajar(): void
    {
        // Buat guru honor yang hanya mengajar hari Senin & Kamis
        $guruHonor = Guru::create([
            'nip' => null,
            'nama' => 'Budi Santoso, S.Kom',
            'jabatan' => 'Guru Kejuruan RPL',
            'jenis_kepegawaian' => 'honor',
            'hari_mengajar' => ['Senin', 'Kamis'],
            'status' => 'aktif',
            'face_embedding' => [0.4, 0.5, 0.6],
            'face_registered_at' => now(),
        ]);

        $this->assertTrue($guruHonor->isHonor());
        $this->assertEquals('Guru Honor (GTT)', $guruHonor->label_kepegawaian);

        $service = new FaceScanService();

        // 1. Uji di hari Selasa (2026-08-18: Luar jadwal mengajar)
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-18 07:15:00')); // Selasa
        $this->assertFalse($guruHonor->isHariMengajar('2026-08-18'));

        // Scan masuk di luar jadwal tetap diizinkan dan tercatat sah
        $resLuarJadwal = $service->scanPerson('guru', $guruHonor->id, 'face_kiosk');
        $this->assertEquals('success', $resLuarJadwal['status']);
        $this->assertEquals('jam_masuk', $resLuarJadwal['type']);
        $this->assertStringContainsString('Luar Jadwal Mengajar', $resLuarJadwal['message']);

        // 2. Uji di hari Kamis (2026-08-20: Sesuai jadwal mengajar)
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse('2026-08-20 07:10:00')); // Kamis
        $this->assertTrue($guruHonor->isHariMengajar('2026-08-20'));

        $resJadwal = $service->scanPerson('guru', $guruHonor->id, 'face_kiosk');
        $this->assertEquals('success', $resJadwal['status']);
        $this->assertEquals('jam_masuk', $resJadwal['type']);

        // 3. Jalankan evaluasi alpha otomatis: pastikan guru honor tidak pernah diberi status Alpha
        \Illuminate\Support\Facades\Artisan::call('absensi:evaluasi-alpha');
        
        $absenRabu = \App\Models\Absensi::where('pemilik_type', 'guru')
            ->where('pemilik_id', $guruHonor->id)
            ->where('tanggal', '2026-08-19') // Rabu: guru honor tidak hadir dan tidak scan
            ->first();

        // Tidak ada record alpha yang dibuat untuk guru honor
        $this->assertNull($absenRabu);

        \Illuminate\Support\Carbon::setTestNow(); // Reset time mock
    }
}
