<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteVisitor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteVisitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_kunjungan_halaman_publik_otomatis_tercatat_di_database(): void
    {
        $this->get(route('web.beranda'))
            ->assertStatus(200);

        $this->assertDatabaseHas('website_visitors', [
            'url'         => '/',
            'device_type' => 'desktop',
        ]);

        $record = WebsiteVisitor::first();
        $this->assertNotNull($record);
        $this->assertEquals(Carbon::today()->toDateString(), Carbon::parse($record->visited_date)->toDateString());
    }

    public function test_kunjungan_berulang_ip_sama_terhitung_unique_visitor_satu(): void
    {
        // Hit beranda dua kali dari IP yang sama
        $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.100'])
            ->get(route('web.beranda'))
            ->assertStatus(200);

        $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.100'])
            ->get(route('web.berita.index'))
            ->assertStatus(200);

        $stats = WebsiteVisitor::getSummaryStats();

        // Total 2 views, tapi unique pengunjung hanya 1
        $this->assertEquals(2, $stats['today_views']);
        $this->assertEquals(1, $stats['today_unique']);
    }

    public function test_rute_admin_dan_auth_tidak_dicatat_oleh_pelacak_pengunjung(): void
    {
        $this->get('/login');

        $this->assertDatabaseMissing('website_visitors', [
            'url' => '/login',
        ]);
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_grafik_pengunjung_dialihkan_ke_login(): void
    {
        $this->get(route('admin.statistik.web'))
            ->assertRedirect(route('login'));
    }

    public function test_guru_non_admin_ditolak_mengakses_halaman_grafik_pengunjung(): void
    {
        $guruUser = User::factory()->create([
            'role' => 'guru',
        ]);

        $this->actingAs($guruUser)
            ->get(route('admin.statistik.web'))
            ->assertStatus(403);
    }

    public function test_admin_bisa_mengakses_halaman_grafik_pengunjung_dan_melihat_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Simulasikan 1 kunjungan publik
        WebsiteVisitor::create([
            'ip_hash'      => 'testhash123',
            'url'          => '/',
            'device_type'  => 'mobile',
            'browser'      => 'Chrome',
            'visited_date' => Carbon::today()->toDateString(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.statistik.web'));

        $response->assertStatus(200);
        $response->assertSee('Grafik &amp; Analisis Pengunjung Website', false);
        $response->assertSee('Tren Kunjungan Harian');
        $response->assertSee('Perangkat Pengunjung');
        $response->assertSee('Halaman Paling Sering Dikunjungi');
    }

    public function test_admin_melihat_tombol_grafik_pengunjung_di_dcc_portal(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.portal'))
            ->assertStatus(200)
            ->assertSee('Grafik Pengunjung');
    }
}
