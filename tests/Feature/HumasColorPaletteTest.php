<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HumasColorPaletteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@smkn1an.sch.id',
        ]);
    }

    public function test_halaman_admin_berita_memuat_palette_warna_baru(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.berita.index'));
        $response->assertOk();
        $response->assertSee('#3368a0', false);
        $response->assertSee('#66a3bf', false);
        $response->assertSee('#c8dfdb', false);
        $response->assertSee('#f2efe7', false);
    }

    public function test_halaman_admin_banner_memuat_palette_warna_baru(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.banner.index'));
        $response->assertOk();
        $response->assertSee('#3368a0', false);
        $response->assertSee('#66a3bf', false);
        $response->assertSee('#c8dfdb', false);
        $response->assertSee('#f2efe7', false);
    }

    public function test_halaman_admin_statistik_memuat_palette_warna_baru(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.statistik.web'));
        $response->assertOk();
        $response->assertSee('#3368a0', false);
        $response->assertSee('#66a3bf', false);
        $response->assertSee('#c8dfdb', false);
        $response->assertSee('#f2efe7', false);
    }
}
