<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HariLiburTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_kalender_hari_libur(): void
    {
        $response = $this->actingAs($this->admin)->get('/hari-libur');
        $response->assertOk();
        $response->assertSee('Kalender Hari Libur');
    }

    public function test_admin_bisa_menambah_jadwal_hari_libur(): void
    {
        $res = $this->actingAs($this->admin)->post('/hari-libur', [
            'nama_libur'      => 'Hari Raya Idul Fitri',
            'tanggal_mulai'   => '2026-04-10',
            'tanggal_selesai' => '2026-04-12',
            'jenis'           => 'libur_nasional',
            'keterangan'      => 'Libur Bersama Hari Raya',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('hari_liburs', [
            'nama_libur'    => 'Hari Raya Idul Fitri',
            'tanggal_mulai' => '2026-04-10',
            'jenis'         => 'libur_nasional',
        ]);
    }

    public function test_admin_bisa_menghapus_jadwal_hari_libur(): void
    {
        $libur = HariLibur::create([
            'nama_libur'      => 'Libur Uji Coba',
            'tanggal_mulai'   => '2026-05-15',
            'tanggal_selesai' => '2026-05-15',
            'jenis'           => 'khusus_sekolah',
        ]);

        $res = $this->actingAs($this->admin)->delete("/hari-libur/{$libur->id}");
        $res->assertRedirect();
        $this->assertDatabaseMissing('hari_liburs', ['id' => $libur->id]);
    }

    public function test_isi_preset_hari_libur_nasional(): void
    {
        $res = $this->actingAs($this->admin)->post('/hari-libur/preset', [
            'tahun' => 2026,
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('hari_liburs', [
            'nama_libur'    => 'Hari Kemerdekaan Republik Indonesia',
            'tanggal_mulai' => '2026-08-17',
        ]);
    }

    public function test_helper_is_libur_mendeteksi_weekend_dan_tanggal_terdaftar(): void
    {
        // 1. Weekend (Sabtu: 2026-08-22, Minggu: 2026-08-23)
        $this->assertTrue(HariLibur::isLibur('2026-08-22'));
        $this->assertTrue(HariLibur::isLibur('2026-08-23'));

        // 2. Hari Biasa belum terdaftar (Senin: 2026-08-10) -> false
        $this->assertFalse(HariLibur::isLibur('2026-08-10'));

        // 3. Daftarkan Hari Libur Nasional di Hari Senin
        HariLibur::create([
            'nama_libur'      => 'Hari Kemerdekaan',
            'tanggal_mulai'   => '2026-08-17',
            'tanggal_selesai' => '2026-08-17',
            'jenis'           => 'libur_nasional',
        ]);

        $this->assertTrue(HariLibur::isLibur('2026-08-17'));
        $liburHariIni = HariLibur::getLiburHariIni('2026-08-17');
        $this->assertNotNull($liburHariIni);
        $this->assertEquals('Hari Kemerdekaan', $liburHariIni->nama_libur);
    }

    public function test_command_evaluasi_alpha_otomatis_skip_pada_hari_libur(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $siswa = Siswa::create(['nis' => '8001', 'nama' => 'Budi Siswa Libur', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id'           => $siswa->id,
            'rombel_id'          => $rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Tetapkan 17 Agustus 2026 sebagai Hari Libur
        HariLibur::create([
            'nama_libur'      => 'Hari Kemerdekaan RI',
            'tanggal_mulai'   => '2026-08-17',
            'tanggal_selesai' => '2026-08-17',
            'jenis'           => 'libur_nasional',
        ]);

        // Jalankan artisan command untuk 2026-08-17
        $this->artisan('absensi:evaluasi-alpha', ['tanggal' => '2026-08-17'])
            ->assertExitCode(0);

        // Pastikan TIDAK ADA catatan absensi alpha yang dibuat untuk siswa
        $this->assertDatabaseMissing('absensis', [
            'pemilik_id' => $siswa->id,
            'tanggal'    => '2026-08-17',
        ]);
    }

    public function test_role_non_admin_dapat_melihat_kalender_tetapi_ditolak_melakukan_crud(): void
    {
        $guru = User::create([
            'name'     => 'Bapak Guru',
            'email'    => 'guru@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'guru',
        ]);

        $kepsek = User::create([
            'name'     => 'Ibu Kepsek',
            'email'    => 'kepsek@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'kepala_sekolah',
        ]);

        // 1. Role Guru & Kepsek bisa melihat kalender (Read / R)
        $resGuru = $this->actingAs($guru)->get('/hari-libur');
        $resGuru->assertOk();
        $resGuru->assertSee('Akses Tinjau Kalender');
        $resGuru->assertDontSee('+ Tambah Hari Libur');

        $resKepsek = $this->actingAs($kepsek)->get('/hari-libur');
        $resKepsek->assertOk();
        $resKepsek->assertSee('Akses Tinjau Kalender');

        // 2. Role Guru ditolak saat mencoba menambah libur (Create)
        $resCreate = $this->actingAs($guru)->post('/hari-libur', [
            'nama_libur'    => 'Libur Ilegal',
            'tanggal_mulai' => '2026-09-01',
            'jenis'         => 'khusus_sekolah',
        ]);
        $resCreate->assertForbidden();

        // 3. Role Guru ditolak saat mencoba menghapus libur (Delete)
        $libur = HariLibur::create([
            'nama_libur'      => 'Hari Santri',
            'tanggal_mulai'   => '2026-10-22',
            'tanggal_selesai' => '2026-10-22',
            'jenis'           => 'khusus_sekolah',
        ]);
        $resDelete = $this->actingAs($guru)->delete("/hari-libur/{$libur->id}");
        $resDelete->assertForbidden();

        // 4. Role Guru ditolak saat mencoba mengisi preset
        $resPreset = $this->actingAs($guru)->post('/hari-libur/preset', ['tahun' => 2026]);
        $resPreset->assertForbidden();
    }
}
