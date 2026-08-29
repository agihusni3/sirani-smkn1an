<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_akses_dashboard_tanpa_login_dialihkan_ke_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_sukses_langsung_masuk_ke_dashboard(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@smkn1airnaningan.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_login_gagal_menampilkan_pesan_error(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@smkn1airnaningan.sch.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_logout_berhasil_dan_dialihkan_ke_login(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_semua_halaman_admin_terproteksi_dan_tidak_bisa_diakses_tanpa_login(): void
    {
        $protectedRoutes = [
            '/dashboard',
            '/siswa',
            '/guru',
            '/rombel',
            '/siklus-siswa',
            '/jadwal-piket',
            '/laporan',
            '/notifikasi',
            '/pengaturan-sekolah',
            '/backup',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_admin_memiliki_akses_ke_semua_halaman_sistem(): void
    {
        $admin = User::create([
            'name'     => 'Super Administrator',
            'email'    => 'admin@smkn1airnaningan.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $routes = [
            '/dashboard',
            '/siswa',
            '/guru',
            '/rombel',
            '/siklus-siswa',
            '/laporan',
            '/jadwal-piket',
            '/jam-operasional',
            '/notifikasi',
            '/pengaturan-sekolah',
            '/backup',
        ];

        foreach ($routes as $route) {
            $res = $this->actingAs($admin)->get($route);
            $res->assertOk();
        }
    }
}
