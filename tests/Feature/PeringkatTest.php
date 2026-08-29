<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\PengaturanSekolah;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PeringkatTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $ta;
    protected $rombel;
    protected $siswa1;
    protected $siswa2;
    protected $guru1;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::create([
            'nama_sekolah' => 'SMKN 1 Air Naningan',
            'npsn' => '69888998',
            'kepala_sekolah' => 'H. AGUNG WIDODO, M.Pd',
            'nip_kepala_sekolah' => '197505122000031002',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Administrator SIRANI',
        ]);

        $this->ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->rombel = Rombel::create([
            'nama_rombel' => 'XII RPL 1',
            'tingkat' => 'XII',
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $this->ta->id,
        ]);

        $this->siswa1 = Siswa::create([
            'nis' => '20260101',
            'nama' => 'Siswa Juara 1 Teladan',
            'status' => 'aktif',
        ]);
        SiswaRombel::create([
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'nis' => '20260102',
            'nama' => 'Siswa Juara 2 Rajin',
            'status' => 'aktif',
        ]);
        SiswaRombel::create([
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->guru1 = Guru::create([
            'nip' => '198501012010011001',
            'nama' => 'Guru Teladan Disiplin',
            'jabatan' => 'Guru Kejuruan RPL',
            'status' => 'aktif',
        ]);

        // Buat absensi siswa 1 (100% tepat waktu)
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa1->id,
            'tanggal' => Carbon::create(2026, 8, 24)->toDateString(),
            'jam_masuk' => '06:55:00',
            'status' => 'hadir',
        ]);

        // Buat absensi siswa 2 (terlambat 1x)
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa2->id,
            'tanggal' => Carbon::create(2026, 8, 24)->toDateString(),
            'jam_masuk' => '07:25:00',
            'status' => 'terlambat',
        ]);

        // Buat absensi guru 1
        Absensi::create([
            'pemilik_type' => 'guru',
            'pemilik_id' => $this->guru1->id,
            'tanggal' => Carbon::create(2026, 8, 24)->toDateString(),
            'jam_masuk' => '06:50:00',
            'status' => 'hadir',
        ]);
    }

    public function test_halaman_peringkat_dapat_diakses_dengan_sukses()
    {
        $response = $this->actingAs($this->admin)->get('/peringkat');
        $response->assertStatus(200);
        $response->assertSee('Peringkat &amp; Apresiasi Kehadiran', false);
        $response->assertSee('Siswa Juara 1 Teladan');
    }

    public function test_filter_kategori_guru_menampilkan_leaderboard_guru()
    {
        $response = $this->actingAs($this->admin)->get('/peringkat?kategori=guru');
        $response->assertStatus(200);
        $response->assertSee('Guru Teladan Disiplin');
    }

    public function test_cetak_piagam_penghargaan_siswa_merender_sertifikat()
    {
        $response = $this->actingAs($this->admin)->get("/peringkat/piagam-siswa/{$this->siswa1->id}?rank=1&predikat=Teladan+Utama");
        $response->assertStatus(200);
        $response->assertSee('PIAGAM PENGHARGAAN');
        $response->assertSee('Siswa Juara 1 Teladan');
        $response->assertSee('SMKN 1 Air Naningan');
    }

    public function test_cetak_piagam_penghargaan_guru_merender_sertifikat()
    {
        $response = $this->actingAs($this->admin)->get("/peringkat/piagam-guru/{$this->guru1->id}?rank=1");
        $response->assertStatus(200);
        $response->assertSee('PIAGAM PENGHARGAAN');
        $response->assertSee('Guru Teladan Disiplin');
    }

    public function test_export_csv_rekap_peringkat_berhasil()
    {
        $response = $this->actingAs($this->admin)->get('/peringkat/export-csv?kategori=siswa&periode=semester');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_admin_bisa_upload_dan_reset_template_piagam()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('template_sertifikat_custom.png', 1920, 1080);

        $response = $this->actingAs($this->admin)->postJson('/peringkat/upload-template', [
            'template_gambar' => $file,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertNotNull(PengaturanSekolah::getAktif()->template_piagam);

        // Test save config
        $configResponse = $this->actingAs($this->admin)->postJson('/peringkat/save-template-config', [
            'config' => [
                'showBg' => true,
                'showKop' => false,
                'bodyOffsetY' => 20,
            ]
        ]);
        $configResponse->assertStatus(200);
        $this->assertStringContainsString('bodyOffsetY', PengaturanSekolah::getAktif()->template_piagam_config);

        // Test reset template
        $resetResponse = $this->actingAs($this->admin)->postJson('/peringkat/reset-template');
        $resetResponse->assertStatus(200);
        $this->assertNull(PengaturanSekolah::getAktif()->fresh()->template_piagam);
    }
}
