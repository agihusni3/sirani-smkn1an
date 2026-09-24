<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalHariIni;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalSekolahTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_jadwal_sekolah_dan_sesi_bisa_diakses_oleh_admin(): void
    {
        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/jadwal-sekolah');

        $response->assertOk();
        $response->assertSee('Jam Operasional Sekolah');
        $response->assertSee('Mode Sumatif');
        $response->assertSee('Liburkan Hari Ini');
        $response->assertSee('modalKelolaModeUjian');
        $response->assertSee('modalLiburDarurat');
    }
}
