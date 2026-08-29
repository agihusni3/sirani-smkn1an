<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\JadwalHariIni;
use App\Models\User;
use App\Services\FaceScanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JadwalHarianScanTest extends TestCase
{
    use RefreshDatabase;

    protected TahunAjaran $ta;
    protected Rombel $rombel;
    protected Siswa $siswa;
    protected FaceScanService $scanService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $this->rombel = Rombel::create([
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
            'nama_rombel' => 'XII RPL 1',
            'tingkat' => 'XII',
        ]);

        $this->siswa = Siswa::create([
            'nis' => '20249999',
            'nama' => 'Bintang Pratama',
            'status' => 'aktif',
            'face_embedding' => [0.11, 0.22, 0.33],
            'face_registered_at' => now(),
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->scanService = new FaceScanService();
    }

    public function test_penetapan_jadwal_hari_ini_oleh_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.jadwal.update'), [
            'jam_masuk_mulai' => '06:00',
            'jam_masuk_toleransi' => '07:15',
            'jam_masuk_selesai' => '08:00',
            'jam_pulang_mulai' => '13:00',
            'jam_pulang_selesai' => '17:00',
            'keterangan' => 'Pulang Cepat Rapat Guru',
        ]);

        $response->assertSessionHas('success');

        $jadwal = JadwalHariIni::getJadwalAktif();
        $this->assertEquals('13:00', substr($jadwal->jam_pulang_mulai, 0, 5));
        $this->assertEquals('Pulang Cepat Rapat Guru', $jadwal->keterangan);
    }

    public function test_scan_wajah_masuk_dan_pulang_tercatat_akurat(): void
    {
        // 1. Scan Masuk pagi jam 07:00
        Carbon::setTestNow(Carbon::parse('2026-08-14 07:00:00'));
        $resMasuk = $this->scanService->scanPerson('siswa', $this->siswa->id);
        $this->assertEquals('success', $resMasuk['status']);
        $this->assertEquals('jam_masuk', $resMasuk['type']);

        // 2. Scan siang sebelum jam pulang (10:00) -> status sudah masuk
        Carbon::setTestNow(Carbon::parse('2026-08-14 10:00:00'));
        $resDebounce = $this->scanService->scanPerson('siswa', $this->siswa->id);
        $this->assertEquals('info', $resDebounce['status']);
        $this->assertEquals('sudah_masuk', $resDebounce['type']);

        // 3. Scan sore jam kepulangan (15:35)
        Carbon::setTestNow(Carbon::parse('2026-08-14 15:35:00'));
        $resPulang = $this->scanService->scanPerson('siswa', $this->siswa->id);
        $this->assertEquals('success', $resPulang['status']);
        $this->assertEquals('jam_pulang', $resPulang['type']);

        $absensi = \App\Models\Absensi::where('pemilik_id', $this->siswa->id)->first();
        $this->assertEquals('15:35:00', $absensi->jam_pulang);

        Carbon::setTestNow();
    }

    public function test_admin_bisa_akses_halaman_jam_operasional(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin Utama',
            'email' => 'admin_jadwal@smkn1.sch.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.jadwal.index'));
        $response->assertStatus(200);
        $response->assertSee('Jam Operasional Sekolah');
    }

    public function test_admin_bisa_update_jam_operasional(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin Utama',
            'email' => 'admin_update@smkn1.sch.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.jadwal.update'), [
            'jam_masuk_toleransi' => '07:30',
            'jam_pulang_mulai' => '14:00',
            'jam_tutup_gerbang' => '17:30',
            'keterangan' => 'Jadwal Masa Ujian Semester',
        ]);

        $response->assertRedirect(route('admin.jadwal.index'));
        $response->assertSessionHas('success');

        $jadwal = JadwalHariIni::getJadwalAktif();
        $this->assertEquals('07:30:00', $jadwal->jam_masuk_toleransi);
        $this->assertEquals('14:00:00', $jadwal->jam_pulang_mulai);
        $this->assertEquals('17:30:00', $jadwal->jam_tutup_gerbang);
        $this->assertEquals('Jadwal Masa Ujian Semester', $jadwal->keterangan);
    }

    public function test_guru_piket_bisa_melihat_jam_operasional(): void
    {
        $piket = \App\Models\User::create([
            'name' => 'Guru Piket Jadwal',
            'email' => 'piket_jadwal@smkn1.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru_piket',
        ]);

        $response = $this->actingAs($piket)->get(route('admin.jadwal.index'));
        $response->assertStatus(200);
        $response->assertSee('Jam Operasional Sekolah');
    }
}
