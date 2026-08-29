<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AkunGuruKepsekTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_admin_bisa_buat_guru_beserta_akun_login(): void
    {
        $this->actingAs($this->admin);

        $res = $this->post('/guru', [
            'nip' => '198501012010011001',
            'nama' => 'Drs. H. Ahmad Fauzi, M.Pd',
            'jabatan' => 'Kepala Sekolah',
            'email_akun' => 'kepsek@smkn1airnaningan.sch.id',
            'password_akun' => 'password123',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('gurus', ['nip' => '198501012010011001', 'nama' => 'Drs. H. Ahmad Fauzi, M.Pd']);
        $this->assertDatabaseHas('users', [
            'email' => 'kepsek@smkn1airnaningan.sch.id',
            'role' => 'kepala_sekolah',
        ]);
    }

    public function test_admin_bisa_atur_dan_update_akun_guru_tersedia(): void
    {
        $this->actingAs($this->admin);

        $guru = Guru::create([
            'nip' => '199002022020022002',
            'nama' => 'Siti Nurhaliza, S.Pd',
            'jabatan' => 'Guru Mata Pelajaran',
            'status' => 'aktif',
        ]);

        // Buat akun baru untuk guru
        $res = $this->post("/guru/{$guru->id}/akun", [
            'email' => 'siti@smkn1airnaningan.sch.id',
            'password' => 'rahasia123',
            'role' => 'guru',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('users', [
            'guru_id' => $guru->id,
            'email' => 'siti@smkn1airnaningan.sch.id',
            'role' => 'guru',
        ]);

        // Update email akun guru
        $resUpdate = $this->post("/guru/{$guru->id}/akun", [
            'email' => 'sitinur@smkn1airnaningan.sch.id',
            'role' => 'guru',
        ]);

        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('users', [
            'guru_id' => $guru->id,
            'email' => 'sitinur@smkn1airnaningan.sch.id',
            'role' => 'guru',
        ]);
    }

    public function test_admin_bisa_hapus_akun_login_guru(): void
    {
        $this->actingAs($this->admin);

        $guru = Guru::create([
            'nip' => '199203032021031003',
            'nama' => 'Budi Santoso, S.Kom',
            'jabatan' => 'Guru Produktif',
            'status' => 'aktif',
        ]);

        User::create([
            'name' => $guru->nama,
            'email' => 'budi@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'guru_id' => $guru->id,
            'role' => 'guru',
        ]);

        $this->assertDatabaseHas('users', ['guru_id' => $guru->id]);

        $res = $this->delete("/guru/{$guru->id}/akun");
        $res->assertRedirect();
        $this->assertDatabaseMissing('users', ['guru_id' => $guru->id]);
        $this->assertDatabaseHas('gurus', ['id' => $guru->id]); // data guru tetap ada
    }

    public function test_login_akun_kepala_sekolah_langsung_ke_dasbor_utama(): void
    {
        $kepsek = Guru::create([
            'nip' => '197501012000011001',
            'nama' => 'Dr. H. Mulyadi, M.Pd',
            'jabatan' => 'Kepala Sekolah',
            'status' => 'aktif',
        ]);

        User::create([
            'name' => $kepsek->nama,
            'email' => 'mulyadi.kepsek@smkn1airnaningan.sch.id',
            'password' => bcrypt('kepsek123'),
            'guru_id' => $kepsek->id,
            'role' => 'kepala_sekolah',
        ]);

        $res = $this->post('/login', [
            'email' => 'mulyadi.kepsek@smkn1airnaningan.sch.id',
            'password' => 'kepsek123',
        ]);

        $res->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_akun_guru_langsung_ke_dasbor_utama(): void
    {
        $guru = Guru::create([
            'nip' => '199101012019011002',
            'nama' => 'Rahmat Hidayat, S.Pd',
            'jabatan' => 'Guru Matematika',
            'status' => 'aktif',
        ]);

        User::create([
            'name' => $guru->nama,
            'email' => 'rahmat@smkn1airnaningan.sch.id',
            'password' => bcrypt('guru1234'),
            'guru_id' => $guru->id,
            'role' => 'guru',
        ]);

        $res = $this->post('/login', [
            'email' => 'rahmat@smkn1airnaningan.sch.id',
            'password' => 'guru1234',
        ]);

        $res->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
