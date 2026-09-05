<?php

namespace Tests\Feature;

use App\Models\BeritaSekolah;
use App\Models\PpdbPendaftar;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_tidak_bisa_akses_portal_dialihkan_ke_login(): void
    {
        $response = $this->get('/portal');
        $response->assertRedirect('/login');
    }

    public function test_guru_biasa_bisa_mengakses_dcc_portal_dan_melihat_status_izin_modul(): void
    {
        $guru = User::create([
            'name' => 'Guru Biasa',
            'email' => 'guru@smkn1airnaningan.sch.id',
            'role' => 'guru',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($guru)->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('DCC SMKN 1 AN');
        $response->assertSee('Buka Modul SIRANI');
    }

    public function test_guru_biasa_ditolak_akses_modul_ppdb_dan_web_humas_tanpa_wewenang(): void
    {
        $guru = User::create([
            'name' => 'Guru Biasa',
            'email' => 'guru@smkn1airnaningan.sch.id',
            'role' => 'guru',
            'password' => Hash::make('password'),
        ]);

        // Guru berhak ke SIRANI
        $this->actingAs($guru)->get('/dashboard')->assertOk();

        // Guru biasa ditolak akses ke Modul PPDB Admin
        $this->actingAs($guru)->get('/admin/ppdb')->assertStatus(403);

        // Guru biasa ditolak akses ke Modul Web Humas Admin
        $this->actingAs($guru)->get('/admin/berita')->assertStatus(403);
    }

    public function test_admin_bisa_mengakses_portal_dan_melihat_modul_aktif_serta_roadmap(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Buat dummy data
        Siswa::create([
            'nisn' => '0012345678',
            'nama' => 'Siswa Test',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        BeritaSekolah::create([
            'judul' => 'Peluncuran Ekosistem Digital 2026',
            'slug' => 'peluncuran-ekosistem-digital-2026',
            'ringkasan' => 'SMKN 1 Air Naningan meluncurkan ekosistem digital modular.',
            'konten' => '<p>Konten rilis berita lengkap.</p>',
            'penulis_id' => $admin->id,
            'is_published' => true,
        ]);

        $jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak'
        ]);

        PpdbPendaftar::create([
            'no_pendaftaran' => 'PPDB-2026-001',
            'nisn' => '0098765432',
            'nama_lengkap' => 'Calon Siswa 1',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Tanggamus',
            'tanggal_lahir' => '2010-01-01',
            'asal_sekolah' => 'SMPN 1 Air Naningan',
            'alamat' => 'Air Naningan RT 01',
            'jurusan_id_1' => $jurusan->id,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->get('/portal');

        $response->assertStatus(200);
        $response->assertSee('DCC SMKN 1 AN');
        $response->assertSee('Digital Command Center');
        $response->assertSee('Selamat Bertugas, Super Administrator');

        // 3 Modul Aktif
        $response->assertSee('SIRANI');
        $response->assertSee('Sistem Absensi &amp; Ketertiban Siswa/Guru', false);
        $response->assertSee('PPDB ONLINE 2026');
        $response->assertSee('WEB PROFIL &amp; HUMAS', false);

        // 5 Modul Roadmap
        $response->assertSee('SITUAN');
        $response->assertSee('Tata Usaha &amp; Persuratan', false);
        $response->assertSee('AKADEMIK &amp; KBM', false);
        $response->assertSee('SARPRAS &amp; ASET', false);
        $response->assertSee('TEFA &amp; UNIT PRODUKSI', false);
        $response->assertSee('PERPUSTAKAAN DIGITAL');
    }

    public function test_kepala_sekolah_bisa_mengakses_portal(): void
    {
        $kepsek = User::create([
            'name' => 'Bapak Kepala Sekolah',
            'email' => 'kepsek@smkn1airnaningan.sch.id',
            'role' => 'kepala_sekolah',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($kepsek)->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Selamat Bertugas, Bapak Kepala Sekolah');
    }

    public function test_alias_rute_admin_portal_dan_hub_berfungsi(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)->get('/admin/portal')->assertStatus(200);
        $this->actingAs($admin)->get('/hub')->assertStatus(200);
    }

    public function test_sidebar_dan_header_actions_menyediakan_shortcut_pusat_kendali_untuk_admin(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('DCC SMKN 1 AN');
        $response->assertDontSee('app-launcher-wrap');
    }

    public function test_sidebar_sirani_terisolasi_dan_bersih_dari_menu_ppdb_dan_web(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        // Memuat navigasi SIRANI
        $response->assertSee('SIRANI');
        $response->assertSee('Dasbor Utama');
        $response->assertSee('Buku Kasus Disiplin');
        // Tidak memuat menu PPDB dan Web di sidebar SIRANI
        $response->assertDontSee('Panitia PPDB 2026');
        $response->assertDontSee('Hero &amp; Banner Web', false);
    }

    public function test_sidebar_ppdb_terisolasi_dan_bersih_dari_menu_presensi_sirani(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin/ppdb');
        $response->assertStatus(200);
        // Memuat navigasi PPDB 2026
        $response->assertSee('PPDB 2026');
        $response->assertSee('Dasbor &amp; Statistik', false);
        $response->assertSee('DCC SMKN 1 AN');
        // Tidak memuat menu absensi SIRANI di workspace PPDB
        $response->assertDontSee('Buku Kasus Disiplin');
        $response->assertDontSee('Piket Harian');
        $response->assertDontSee('Smart Gate Presensi');
    }

    public function test_sidebar_web_humas_terisolasi_dan_bersih_dari_menu_presensi_sirani(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin/berita');
        $response->assertStatus(200);
        // Memuat navigasi Humas
        $response->assertSee('HUMAS &amp; WEB', false);
        $response->assertSee('Kelola Berita &amp; Rilis', false);
        $response->assertSee('Hero Slider &amp; Banner', false);
        $response->assertSee('DCC SMKN 1 AN');
        // Tidak memuat menu absensi SIRANI di workspace Humas
        $response->assertDontSee('Buku Kasus Disiplin');
        $response->assertDontSee('Piket Harian');
        $response->assertDontSee('Smart Gate Presensi');
    }
}
