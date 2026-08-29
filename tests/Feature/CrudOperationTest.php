<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CrudOperationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);
    }

    public function test_tambah_siswa_berhasil(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);

        $res = $this->post('/siswa', [
            'nis' => '990011',
            'nisn' => '00990011',
            'nama' => 'Siswa Baru Test',
            'rombel_id' => $rombel->id,
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('siswas', ['nis' => '990011', 'nama' => 'Siswa Baru Test']);
    }

    public function test_update_siswa_berhasil(): void
    {
        $siswa = Siswa::create(['nis' => '990022', 'nama' => 'Nama Awal', 'status' => 'aktif']);

        $res = $this->put("/siswa/{$siswa->id}", [
            'nis' => '990022',
            'nama' => 'Nama Setelah Edit',
            'status' => 'aktif',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('siswas', ['id' => $siswa->id, 'nama' => 'Nama Setelah Edit']);
    }

    public function test_export_siswa_csv(): void
    {
        Siswa::create(['nis' => '112233', 'nama' => 'Siswa Export', 'status' => 'aktif']);

        $res = $this->get('/siswa/export');
        $res->assertStatus(200);
        $res->assertHeader('Content-Disposition');
    }

    public function test_import_siswa_csv(): void
    {
        $content = "NIS,NISN,Nama Siswa,Rombel,Status\n554433,00554433,Siswa Import CSV,X TKJ 1,aktif";
        $file = UploadedFile::fake()->createWithContent('siswa.csv', $content);

        $res = $this->post('/siswa/import', ['file' => $file]);
        $res->assertRedirect();
        $this->assertDatabaseHas('siswas', ['nis' => '554433', 'nama' => 'Siswa Import CSV']);
    }

    public function test_tambah_siswa_dengan_kontak_ortu_berhasil(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL 1', 'tingkat' => 'X']);

        $res = $this->post('/siswa', [
            'nis' => '99887766',
            'nama' => 'Siswa Lengkap Kontak',
            'nama_ortu' => 'Bpk. Hendra',
            'no_hp_ortu' => '081299887766',
            'rombel_id' => $rombel->id,
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('siswas', [
            'nis' => '99887766',
            'nama_ortu' => 'Bpk. Hendra',
            'no_hp_ortu' => '081299887766',
        ]);
    }

    public function test_tambah_guru_berhasil(): void
    {
        $res = $this->post('/guru', [
            'nip' => '199001012020011005',
            'nama' => 'Guru Baru Test, S.Pd',
            'jabatan' => 'Guru Matematika',
            'no_hp' => '081399887766',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('gurus', [
            'nip' => '199001012020011005',
            'no_hp' => '081399887766',
        ]);
    }

    public function test_update_guru_berhasil(): void
    {
        $guru = Guru::create(['nip' => '199001012020011006', 'nama' => 'Guru Awal', 'jabatan' => 'Guru', 'status' => 'aktif', 'no_hp' => '0811111111']);

        $res = $this->put("/guru/{$guru->id}", [
            'nip' => '199001012020011006',
            'nama' => 'Guru Setelah Update',
            'jabatan' => 'Guru Mata Pelajaran',
            'no_hp' => '0822222222',
            'status' => 'aktif',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'nama' => 'Guru Setelah Update',
            'jabatan' => 'Guru Mata Pelajaran',
            'no_hp' => '0822222222',
        ]);
    }

    public function test_export_guru_csv(): void
    {
        Guru::create(['nip' => '199001012020011007', 'nama' => 'Guru Export', 'jabatan' => 'Guru', 'status' => 'aktif']);

        $res = $this->get('/guru/export');
        $res->assertStatus(200);
        $res->assertHeader('Content-Disposition');
    }

    public function test_import_guru_csv(): void
    {
        $content = "NIP,Nama Guru / Pegawai,Jabatan,Status\n199001012020011008,Guru Import CSV,Guru Pengajar,aktif";
        $file = UploadedFile::fake()->createWithContent('guru.csv', $content);

        $res = $this->post('/guru/import', ['file' => $file]);
        $res->assertRedirect();
        $this->assertDatabaseHas('gurus', ['nip' => '199001012020011008', 'nama' => 'Guru Import CSV']);
    }

    public function test_tambah_rombel_berhasil(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);

        $res = $this->post('/rombel', [
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan->id,
            'nama_rombel' => 'XI RPL 1',
            'tingkat' => 'XI',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('rombels', ['nama_rombel' => 'XI RPL 1']);
    }

    public function test_registrasi_dan_enroll_wajah_siswa(): void
    {
        $siswa = Siswa::create(['nis' => '880011', 'nama' => 'Siswa Face Enroll', 'status' => 'aktif']);

        $res = $this->postJson('/api/v1/face-enroll', [
            'type' => 'siswa',
            'id' => $siswa->id,
            'embedding' => array_fill(0, 128, 0.12),
        ]);

        $res->assertOk();
        $res->assertJson(['success' => true]);
        $this->assertNotNull($siswa->fresh()->face_embedding);
    }

    public function test_registrasi_dan_enroll_wajah_guru(): void
    {
        $guru = Guru::create(['nip' => '198501012015011001', 'nama' => 'Guru Face Enroll', 'jabatan' => 'Guru', 'status' => 'aktif']);

        $res = $this->postJson('/api/v1/face-enroll', [
            'type' => 'guru',
            'id' => $guru->id,
            'embedding' => array_fill(0, 128, 0.45),
        ]);

        $res->assertOk();
        $res->assertJson(['success' => true]);
        $this->assertNotNull($guru->fresh()->face_embedding);
    }

    public function test_tambah_siswa(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL 1', 'tingkat' => 'X']);

        $res = $this->post('/siswa', [
            'nis' => '778899',
            'nama' => 'Siswa Baru',
            'rombel_id' => $rombel->id,
        ]);

        $res->assertRedirect();
        $siswa = Siswa::where('nis', '778899')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals('Siswa Baru', $siswa->nama);
    }

    public function test_tambah_guru(): void
    {
        $res = $this->post('/guru', [
            'nip' => '198501012015011001',
            'nama' => 'Guru Baru',
            'jabatan' => 'Guru Mata Produktif',
        ]);

        $res->assertRedirect();
        $guru = Guru::where('nip', '198501012015011001')->first();
        $this->assertNotNull($guru);
        $this->assertEquals('Guru Baru', $guru->nama);
    }
}
