<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaceRecognitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-08-27 07:05:00'));

        // Buat tahun ajaran aktif
        TahunAjaran::create([
            'nama' => '2026/2027 Ganjil',
            'is_active' => true,
        ]);
    }

    public function test_halaman_kios_scan_wajah_bisa_diakses_secara_publik()
    {
        // Root / sekarang menjadi default Smart Gate Presensi
        $responseRoot = $this->get('/');
        $responseRoot->assertOk();
        $responseRoot->assertSee('Smart Gate Presensi');
        $responseRoot->assertSee('Arahkan Wajah ke Kamera');

        // /kios-wajah juga tetap bisa diakses
        $response = $this->get('/kios-wajah');
        $response->assertOk();
        $response->assertSee('Smart Gate Presensi');

        // /kios-rfid dialihkan ke Kios Biometrik AI
        $responseRfid = $this->get('/kios-rfid');
        $responseRfid->assertRedirect('/');
    }

    public function test_api_face_enroll_mendaftarkan_embedding_siswa_dan_guru()
    {
        $siswa = Siswa::create([
            'nis'    => '1001',
            'nama'   => 'Farel Prayoga',
            'status' => 'aktif',
        ]);

        $fakeEmbedding = array_fill(0, 128, 0.123456);

        // 1. Tanpa login ditolak (Proteksi Keamanan Endpoint)
        $unauthResponse = $this->postJson('/api/v1/face-enroll', [
            'type'      => 'siswa',
            'id'        => $siswa->id,
            'embedding' => $fakeEmbedding,
        ]);
        $this->assertTrue(in_array($unauthResponse->status(), [401, 419, 302]));

        // 2. Dengan login admin / operator berhasil
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->postJson('/api/v1/face-enroll', [
            'type'      => 'siswa',
            'id'        => $siswa->id,
            'embedding' => $fakeEmbedding,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
        ]);

        $siswa->refresh();
        $this->assertNotNull($siswa->face_embedding);
        $this->assertNotNull($siswa->face_registered_at);
        $this->assertCount(128, is_array($siswa->face_embedding) ? $siswa->face_embedding : json_decode($siswa->face_embedding, true));
    }

    public function test_api_get_descriptors_mengembalikan_vektor_wajah_terdaftar()
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $siswa = Siswa::create([
            'nis'            => '1002',
            'nama'           => 'Aisyah Putri',
            'status'         => 'aktif',
            'face_embedding' => json_encode(array_fill(0, 128, 0.5)),
        ]);

        SiswaRombel::create([
            'siswa_id'           => $siswa->id,
            'rombel_id'          => $rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $guru = Guru::create([
            'nip'            => '198501012010011001',
            'nama'           => 'Bapak Guru Teladan',
            'status'         => 'aktif',
            'face_embedding' => json_encode(array_fill(0, 128, -0.25)),
        ]);

        $response = $this->getJson('/api/v1/face-descriptors');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'count'   => 2,
        ]);

        $response->assertJsonFragment(['nama' => 'Aisyah Putri', 'type' => 'siswa']);
        $response->assertJsonFragment(['nama' => 'Bapak Guru Teladan', 'type' => 'guru']);
    }

    public function test_api_face_scan_mencatat_presensi_masuk_dan_pulang_siswa()
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer Jaringan']);
        $rombel = Rombel::create([
            'nama_rombel'     => 'XI TKJ 2',
            'tingkat'         => 11,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $siswa = Siswa::create([
            'nis'    => '1003',
            'nama'   => 'Rian Firmansyah',
            'status' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id'           => $siswa->id,
            'rombel_id'          => $rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // 1. Scan Masuk Wajah
        $responseMasuk = $this->postJson('/api/v1/face-scan', [
            'type' => 'siswa',
            'id'   => $siswa->id,
        ]);

        $responseMasuk->assertOk();
        $responseMasuk->assertJson([
            'success' => true,
            'type'    => 'jam_masuk',
        ]);

        $this->assertDatabaseHas('absensis', [
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $siswa->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'sumber_absen' => 'face_kiosk',
        ]);
    }

    public function test_api_face_scan_mencatat_presensi_guru()
    {
        $guru = Guru::create([
            'nip'    => '199002022015021002',
            'nama'   => 'Ibu Sri Wahyuni, M.Pd',
            'status' => 'aktif',
        ]);

        $response = $this->postJson('/api/v1/face-scan', [
            'type' => 'guru',
            'id'   => $guru->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type'    => 'jam_masuk',
        ]);

        $this->assertDatabaseHas('absensis', [
            'pemilik_type' => 'guru',
            'pemilik_id'   => $guru->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'sumber_absen' => 'face_kiosk',
        ]);
    }

    public function test_login_biometrik_wajah_berhasil_masuk_ke_dasbor()
    {
        $guru = Guru::create([
            'nip'    => '198501012010011001',
            'nama'   => 'Drs. Budi Santoso',
            'status' => 'aktif',
        ]);

        $user = \App\Models\User::create([
            'name'     => 'Drs. Budi Santoso',
            'email'    => 'budi@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'guru',
            'guru_id'  => $guru->id,
            'status'   => 'aktif',
        ]);

        $response = $this->postJson('/login/face', [
            'guru_id' => $guru->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success'  => true,
            'redirect' => '/dashboard',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_biometrik_wajah_ditolak_jika_belum_punya_akun()
    {
        $guru = Guru::create([
            'nip'    => '198901012019011009',
            'nama'   => 'Ahmad Fauzi, S.Kom',
            'status' => 'aktif',
        ]);

        $response = $this->postJson('/login/face', [
            'guru_id' => $guru->id,
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertGuest();
    }
}
