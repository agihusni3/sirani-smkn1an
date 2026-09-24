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

    public function test_banner_sumatif_di_piket_tidak_ada_tombol_dan_hilang_jika_sumatif_berakhir(): void
    {
        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Kondisi aktif: tanggal hari ini dalam rentang
        $mode = \App\Models\ModeUjian::create([
            'nama_ujian' => 'Sumatif Tengah Semester Gasal',
            'tipe' => 'STS',
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'jam_pulang_mulai' => '11:30:00',
            'is_aktif' => true,
        ]);

        $resAktif = $this->actingAs($admin)->get('/piket');
        $resAktif->assertOk();
        $resAktif->assertSee('PEKAN SUMATIF AKTIF: Sumatif Tengah Semester Gasal');
        // Tombol di dalam banner sudah dihapus
        $resAktif->assertDontSee('Atur Sumatif');

        // 2. Kondisi berakhir: tanggal_selesai sudah lewat di masa lalu
        $mode->update([
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->subDays(1)->toDateString(),
        ]);

        $resBerakhir = $this->actingAs($admin)->get('/piket');
        $resBerakhir->assertOk();
        // Banner otomatis hilang saat sumatif telah berakhir
        $resBerakhir->assertDontSee('PEKAN SUMATIF AKTIF');
    }
}
