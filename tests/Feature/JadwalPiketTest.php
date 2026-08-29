<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalPiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalPiketTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_mengakses_dan_menambah_jadwal_piket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = Guru::create(['nama' => 'Pak Joko', 'jabatan' => 'Guru IPA', 'status' => 'aktif']);

        $response = $this->actingAs($admin)->get('/jadwal-piket');
        $response->assertOk();

        $postRes = $this->actingAs($admin)->post('/jadwal-piket', [
            'hari' => 'Senin',
            'guru_id' => $guru->id,
            'keterangan' => 'Koordinator Piket',
        ]);

        $postRes->assertRedirect();
        $this->assertDatabaseHas('jadwal_pikets', [
            'hari' => 'Senin',
            'guru_id' => $guru->id,
            'keterangan' => 'Koordinator Piket',
        ]);
    }

    public function test_guru_bisa_login_via_face_biometric(): void
    {
        $guru = Guru::create([
            'nama' => 'Ibu Siti',
            'jabatan' => 'Guru',
            'status' => 'aktif',
            'face_embedding' => [0.1, 0.2, 0.3],
            'face_registered_at' => now(),
        ]);
        User::create([
            'name' => $guru->nama,
            'email' => 'siti@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'guru_id' => $guru->id,
            'role' => 'guru',
        ]);

        $res = $this->postJson('/login/face', ['guru_id' => $guru->id]);
        $res->assertOk();
        $res->assertJson(['success' => true]);
        $this->assertAuthenticated();
    }

    public function test_guru_tidak_dikenal_ditolak_login_face(): void
    {
        $res = $this->postJson('/login/face', ['guru_id' => 99999]);
        $res->assertStatus(404);
        $this->assertGuest();
    }

    public function test_hapus_jadwal_piket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = Guru::create(['nama' => 'Guru Hapus Piket', 'jabatan' => 'Guru Mata Pelajaran', 'status' => 'aktif']);
        $jp = JadwalPiket::create(['hari' => 'Rabu', 'guru_id' => $guru->id]);

        $res = $this->actingAs($admin)->delete("/jadwal-piket/{$jp->id}");
        $res->assertRedirect();
        $this->assertDatabaseMissing('jadwal_pikets', ['id' => $jp->id]);
    }

    public function test_guru_piket_bisa_koreksi_absensi_siswa_dan_guru(): void
    {
        $guruPiketUser = User::factory()->create(['role' => 'guru_piket']);
        $siswa = \App\Models\Siswa::create(['nis' => '7788', 'nama' => 'Siswa Test Koreksi', 'status' => 'aktif']);
        $guru = Guru::create(['nama' => 'Guru Test Koreksi', 'jabatan' => 'Guru', 'status' => 'aktif']);

        $absenSiswa = \App\Models\Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $siswa->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'jam_masuk'    => '07:30:00',
            'status'       => 'terlambat',
        ]);

        $absenGuru = \App\Models\Absensi::create([
            'pemilik_type' => 'guru',
            'pemilik_id'   => $guru->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'jam_masuk'    => '07:40:00',
            'status'       => 'terlambat',
        ]);

        // 1. Koreksi Siswa: Terlambat -> Hadir
        $resSiswa = $this->actingAs($guruPiketUser)->put("/piket/absensi/{$absenSiswa->id}", [
            'status'     => 'hadir',
            'jam_masuk'  => '07:10',
            'jam_pulang' => '15:30',
            'keterangan' => 'Terkonfirmasi hadir tepat waktu',
        ]);
        $resSiswa->assertRedirect();
        $this->assertEquals('hadir', $absenSiswa->fresh()->status);
        $this->assertEquals('koreksi_piket_manual', $absenSiswa->fresh()->sumber_absen);

        // 2. Koreksi Guru: Terlambat -> Izin (Dinas Luar)
        $resGuru = $this->actingAs($guruPiketUser)->put("/piket/absensi/{$absenGuru->id}", [
            'status'     => 'izin',
            'keterangan' => 'Dinas luar pengawas ujian',
        ]);
        $resGuru->assertRedirect();
        $this->assertEquals('izin', $absenGuru->fresh()->status);
    }
}
