<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\KartuRfid;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\RfidScanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RfidAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Siswa $siswa;
    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin RFID',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $rombel = Rombel::create([
            'nama_rombel' => 'X RPL',
            'tingkat' => 'X',
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan->id,
        ]);

        $this->siswa = Siswa::create([
            'nis' => '99001',
            'nama' => 'Budi Santoso',
            'status' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa->id,
            'rombel_id' => $rombel->id,
            'tahun_ajaran_id' => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'nama' => 'Bambang Sudarsono, S.Pd.',
            'nip' => '198501012010011001',
            'status' => 'aktif',
            'jenis_kepegawaian' => 'pns',
            'jabatan' => 'Guru Produktif',
        ]);
    }

    /**
     * 1. Test Pairing Kartu RFID ke Siswa dan Guru
     */
    public function test_admin_bisa_pairing_kartu_rfid(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/rfid-pair', [
            'uid' => 'UID-BUDI-001',
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('kartu_rfids', [
            'uid' => 'UID-BUDI-001',
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * 2. Test Scan Kartu RFID Masuk
     */
    public function test_scan_rfid_masuk_berhasil_tercatat(): void
    {
        KartuRfid::pair('UID-BUDI-001', 'siswa', $this->siswa->id);

        Carbon::setTestNow(Carbon::parse('2026-09-01 06:45:00'));

        $jadwal = \App\Models\JadwalHariIni::getJadwalAktif('2026-09-01');
        $jadwal->bukaSesi('Petugas Piket');

        $service = new RfidScanService();
        $result = $service->scanRfid('UID-BUDI-001', 'kios_rfid');

        $this->assertTrue($result['success']);
        $this->assertEquals('jam_masuk', $result['type']);
        $this->assertEquals('Budi Santoso', $result['data']['nama']);
        $this->assertEquals('X RPL', $result['data']['rombel_atau_jabatan']);

        $this->assertDatabaseHas('absensis', [
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa->id,
            'sumber_absen' => 'rfid',
        ]);
    }

    /**
     * 3. Test Scan Kartu RFID Belum Terdaftar
     */
    public function test_scan_rfid_tidak_dikenal_ditolak(): void
    {
        $service = new RfidScanService();
        $result = $service->scanRfid('UID-UNKNOWN-XXX', 'kios_rfid');

        $this->assertFalse($result['success']);
        $this->assertEquals('kartu_tidak_dikenal', $result['type']);
    }

    /**
     * 4. Test Halaman Kios RFID
     */
    public function test_halaman_kios_rfid_bisa_diakses_oleh_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/kios-rfid');
        $response->assertOk();
        $response->assertSee('Smart Gate RFID');
        $response->assertSee('Tempelkan Kartu RFID / e-KTP');
    }
}
