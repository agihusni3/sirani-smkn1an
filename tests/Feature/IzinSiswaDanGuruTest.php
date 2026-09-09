<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\IzinGuru;
use App\Models\IzinSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IzinSiswaDanGuruTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Siswa $siswa;
    private Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->siswa = Siswa::create([
            'nis'        => '3001',
            'nisn'       => '0098765431',
            'nama'       => 'Fajar Ramadhan',
            'status'     => 'aktif',
        ]);

        $this->guru = Guru::create([
            'nama'   => 'Dra. Siti Rahmah',
            'status' => 'aktif',
        ]);
    }

    public function test_halaman_izin_siswa_dapat_diakses_tanpa_error(): void
    {
        $response = $this->actingAs($this->admin)->get('/izin-siswa');
        $response->assertOk();
        $response->assertSee('Surat Izin');
    }

    public function test_admin_dapat_menghapus_catatan_izin_siswa_dan_guru(): void
    {
        $izinSiswa = IzinSiswa::create([
            'siswa_id'   => $this->siswa->id,
            'jenis'      => 'sakit',
            'tanggal'    => '2026-09-09',
            'keterangan' => 'Demam tinggi',
        ]);

        $izinGuru = IzinGuru::create([
            'guru_id'       => $this->guru->id,
            'jenis'         => 'izin',
            'tanggal'       => '2026-09-09',
            'keterangan'    => 'Keperluan keluarga',
        ]);

        $delSiswa = $this->actingAs($this->admin)->delete(route('izin-siswa.destroy', $izinSiswa->id));
        $delSiswa->assertRedirect();
        $this->assertDatabaseMissing('izin_siswas', ['id' => $izinSiswa->id]);

        $delGuru = $this->actingAs($this->admin)->delete(route('izin-guru.destroy', $izinGuru->id));
        $delGuru->assertRedirect();
        $this->assertDatabaseMissing('izin_gurus', ['id' => $izinGuru->id]);
    }
}
