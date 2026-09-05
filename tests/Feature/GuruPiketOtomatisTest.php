<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalPiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruPiketOtomatisTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_piket_tidak_perlu_switch_role_pada_hari_bertugas(): void
    {
        Carbon::setTestNow('2026-09-08 08:00:00'); // Hari Selasa

        $guru = Guru::create([
            'nama'    => 'Agi Husni Widodo',
            'jabatan' => 'Guru Kejuruan RPL',
            'status'  => 'aktif',
        ]);

        $user = User::create([
            'name'     => $guru->nama,
            'email'    => 'agi@sirani.local',
            'password' => bcrypt('secret123'),
            'role'     => 'guru',
            'guru_id'  => $guru->id,
        ]);

        // Tugaskan piket di hari Selasa
        JadwalPiket::create([
            'hari'       => 'Selasa',
            'guru_id'    => $guru->id,
            'keterangan' => 'Piket Pagi',
        ]);

        // 1. Role switcher tidak boleh memuat 'guru_piket'
        $this->assertNotContains('guru_piket', $user->getAvailableRoles());

        // 2. Pada hari bertugas (Selasa), guru otomatis dianggap piket hari ini tanpa perlu switch akun/role
        $this->assertTrue($user->isPiketHariIni());
        $this->assertTrue($user->isGuruPiket());

        // 3. Dapat mengakses dashboard piket secara langsung
        $res = $this->actingAs($user)->get('/piket');
        $res->assertOk();

        Carbon::setTestNow(); // Reset
    }

    public function test_guru_piket_ditolak_membuka_dashboard_piket_di_luar_hari_tugas(): void
    {
        Carbon::setTestNow('2026-09-05 08:00:00'); // Hari Sabtu (bukan hari tugas)

        $guru = Guru::create([
            'nama'    => 'Agi Husni Widodo',
            'jabatan' => 'Guru Kejuruan RPL',
            'status'  => 'aktif',
        ]);

        $user = User::create([
            'name'     => $guru->nama,
            'email'    => 'agi@sirani.local',
            'password' => bcrypt('secret123'),
            'role'     => 'guru',
            'guru_id'  => $guru->id,
        ]);

        // Ditugaskan di hari Selasa (bukan Sabtu)
        JadwalPiket::create([
            'hari'       => 'Selasa',
            'guru_id'    => $guru->id,
            'keterangan' => 'Piket Pagi',
        ]);

        // 1. Bukan piket hari ini
        $this->assertFalse($user->isPiketHariIni());
        $this->assertFalse($user->isGuruPiket());

        // 2. Dilarang membuka dashboard piket (HTTP 403)
        $res = $this->actingAs($user)->get('/piket');
        $res->assertForbidden();

        Carbon::setTestNow(); // Reset
    }

    public function test_admin_dan_waka_kesiswaan_selalu_dapat_mengakses_dashboard_piket(): void
    {
        Carbon::setTestNow('2026-09-05 08:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $resAdmin = $this->actingAs($admin)->get('/piket');
        $resAdmin->assertOk();

        $wakasis = User::factory()->create(['role' => 'waka_kesiswaan']);
        $resWakasis = $this->actingAs($wakasis)->get('/piket');
        $resWakasis->assertOk();

        Carbon::setTestNow();
    }
}
