<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalHariIni;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SesiGerbangTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $guruPiket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = User::factory()->create([
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role'  => 'admin',
        ]);

        $guru = Guru::create([
            'nip'    => '198001012010011005',
            'nama'   => 'Bapak Budi Santoso, S.Pd.',
            'status' => 'aktif',
        ]);

        $this->guruPiket = User::factory()->create([
            'email'   => 'piket@smkn1airnaningan.sch.id',
            'role'    => 'guru_piket',
            'guru_id' => $guru->id,
        ]);
    }

    public function test_scan_wajah_ditolak_jika_sesi_gerbang_ditutup()
    {
        $today = Carbon::today()->toDateString();
        $jadwal = JadwalHariIni::getJadwalAktif($today);
        $jadwal->update(['is_sesi_buka' => false, 'dibuka_oleh' => 'Petugas']);

        $siswa = Siswa::create([
            'nis'            => '2026101',
            'nama'           => 'Rahmat Hidayat',
            'status'         => 'aktif',
            'face_embedding' => array_fill(0, 128, 0.5),
        ]);

        $res = $this->postJson('/api/v1/face-scan', [
            'type'   => 'siswa',
            'id'     => $siswa->id,
            'device' => 'face_kiosk',
        ]);

        $res->assertOk();
        $res->assertJson([
            'status' => 'warning',
            'type'   => 'gerbang_ditutup',
        ]);

        $this->assertDatabaseMissing('absensis', [
            'pemilik_id' => $siswa->id,
            'tanggal'    => $today,
        ]);
    }

    public function test_guru_piket_bisa_buka_dan_tutup_sesi_gerbang()
    {
        $today = Carbon::today()->toDateString();
        $jadwal = JadwalHariIni::getJadwalAktif($today);

        // 1. Tutup sesi gerbang
        $resTutup = $this->actingAs($this->guruPiket)->post('/piket/toggle-gerbang', [
            'status' => 'tutup',
        ]);
        $resTutup->assertRedirect();
        $this->assertFalse(JadwalHariIni::isSesiAktif($today));

        // 2. Buka sesi gerbang
        $resBuka = $this->actingAs($this->guruPiket)->post('/piket/toggle-gerbang', [
            'status' => 'buka',
        ]);
        $resBuka->assertRedirect();
        $this->assertTrue(JadwalHariIni::isSesiAktif($today));
    }

    public function test_scan_wajah_berhasil_ketika_sesi_gerbang_aktif()
    {
        $today = Carbon::today()->toDateString();
        $jadwal = JadwalHariIni::getJadwalAktif($today);
        $jadwal->update(['is_sesi_buka' => true]);

        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $siswa = Siswa::create([
            'nis'            => '2026102',
            'nama'           => 'Ahmad Dani',
            'status'         => 'aktif',
            'face_embedding' => array_fill(0, 128, 0.5),
        ]);

        SiswaRombel::create([
            'siswa_id'           => $siswa->id,
            'rombel_id'          => $rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $res = $this->postJson('/api/v1/face-scan', [
            'type'   => 'siswa',
            'id'     => $siswa->id,
            'device' => 'face_kiosk',
        ]);

        $res->assertOk();
        $res->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('absensis', [
            'pemilik_id' => $siswa->id,
            'tanggal'    => $today,
        ]);
    }
}
