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
        $response->assertSee('Data Control Center');
        $response->assertSee('Selamat Bertugas, Super Administrator');

        // 4 Modul Aktif
        $response->assertSee('4 Modul Ekosistem Terpadu');
        $response->assertSee('SITUAN');
        $response->assertSee('Sistem Informasi Tata Usaha SMKN 1 Air Naningan');
        $response->assertSee('SIRANI');
        $response->assertSee('Sistem Informasi Responsif Absensi');
        $response->assertSee('PPDB ONLINE 2026');
        $response->assertSee('WEB PROFIL &amp; HUMAS', false);

        // 4 Modul Roadmap Masa Depan
        $response->assertSee('AKADEMIK &amp; KBM', false);
        $response->assertSee('SARPRAS &amp; ASET', false);
        $response->assertSee('TEFA &amp; UNIT PRODUKSI', false);
        $response->assertSee('PERPUSTAKAAN DIGITAL');
    }

    public function test_staf_tu_bisa_akses_modul_situan_data_pokok(): void
    {
        $tu = User::create([
            'name' => 'Staf Tata Usaha',
            'email' => 'tu@smkn1airnaningan.sch.id',
            'role' => 'staf_tu',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($tu)->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('SITUAN');
        $response->assertSee('Buka Modul SITUAN');
        $this->assertTrue($tu->canAccessSituan());
    }

    public function test_wali_kelas_bisa_read_dan_update_siswa_rombelnya_dan_ditolak_rombel_lain(): void
    {
        $guruWali = \App\Models\Guru::create([
            'nama' => 'Wali Kelas X RPL 1',
            'nip' => '198501012010011001',
            'status' => 'aktif',
        ]);

        $userWali = User::create([
            'name' => 'Wali Kelas X RPL 1',
            'email' => 'walixrpl1@smkn1airnaningan.sch.id',
            'role' => 'wali_kelas',
            'guru_id' => $guruWali->id,
            'password' => Hash::make('password'),
        ]);

        $jurusan = \App\Models\Jurusan::firstOrCreate(['kode_jurusan' => 'RPL'], ['nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $ta = \App\Models\TahunAjaran::firstOrCreate(['nama' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

        $rombel1 = \App\Models\Rombel::create([
            'nama_rombel' => 'X RPL 1',
            'tingkat' => 10,
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id' => $guruWali->id,
        ]);

        $rombel2 = \App\Models\Rombel::create([
            'nama_rombel' => 'X RPL 2',
            'tingkat' => 10,
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $siswa1 = Siswa::create([
            'nisn' => '1111111111',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        \App\Models\SiswaRombel::create([
            'siswa_id' => $siswa1->id,
            'rombel_id' => $rombel1->id,
            'tahun_ajaran_id' => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $siswa2 = Siswa::create([
            'nisn' => '2222222222',
            'nama' => 'Dewi Lestari',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);

        \App\Models\SiswaRombel::create([
            'siswa_id' => $siswa2->id,
            'rombel_id' => $rombel2->id,
            'tahun_ajaran_id' => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // 1. Wali kelas hanya melihat siswa di rombel binaannya
        $responseIndex = $this->actingAs($userWali)->get('/siswa');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Budi Santoso');
        $responseIndex->assertDontSee('Dewi Lestari');

        // 2. Wali kelas sukses update data siswa di kelasnya
        $responseUpdate = $this->actingAs($userWali)->put("/siswa/{$siswa1->id}", [
            'nisn' => '1111111111',
            'nama' => 'Budi Santoso Updated',
            'status' => 'aktif',
            'no_hp_ortu' => '081234567890',
        ]);
        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa1->id,
            'nama' => 'Budi Santoso Updated',
            'no_hp_ortu' => '081234567890',
        ]);

        // 3. Wali kelas ditolak update siswa rombel lain
        $responseReject = $this->actingAs($userWali)->put("/siswa/{$siswa2->id}", [
            'nisn' => '2222222222',
            'nama' => 'Hacked Dewi',
            'status' => 'aktif',
        ]);
        $responseReject->assertSessionHas('error');
        $this->assertDatabaseMissing('siswas', [
            'id' => $siswa2->id,
            'nama' => 'Hacked Dewi',
        ]);
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

    public function test_situan_memiliki_dashboard_sendiri_dan_dapat_diakses_oleh_staf_tu_dan_admin(): void
    {
        $tu = User::create([
            'name' => 'Staf Tata Usaha SMKN1',
            'email' => 'stafftu@smkn1airnaningan.sch.id',
            'role' => 'staf_tu',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($tu)->get('/situan');
        $response->assertStatus(200);
        $response->assertSee('SITUAN — SMKN 1 AN');
        $response->assertSee('Pusat Data Induk');
        $response->assertSee('Peserta Didik Aktif');
        $response->assertSee('Audit Kelengkapan Data Pokok Siswa');
        $response->assertSee('Dasbor Tata Usaha');
        // Pastikan terisolasi dari operasional harian presensi SIRANI
        $response->assertDontSee('Smart Gate Presensi');
        $response->assertDontSee('Buku Kasus Disiplin');
    }

    public function test_guru_biasa_ditolak_akses_ke_dashboard_situan(): void
    {
        $guru = User::create([
            'name' => 'Guru Pengajar',
            'email' => 'gurupengajar@smkn1airnaningan.sch.id',
            'role' => 'guru',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($guru)->get('/situan');
        $response->assertStatus(403);
    }

    public function test_dcc_dashboard_menggunakan_palette_warna_baru_dan_font_hitam(): void
    {
        $admin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin-palette@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('admin-portal.css');

        $cssContent = file_get_contents(public_path('css/admin-portal.css'));
        // Verifikasi keberadaan token 4 warna Color Hunt: #3368a0, #66a3bf, #c8dfdb, #f2efe7
        $this->assertStringContainsString('#3368a0', strtolower($cssContent));
        $this->assertStringContainsString('#66a3bf', strtolower($cssContent));
        $this->assertStringContainsString('#c8dfdb', strtolower($cssContent));
        $this->assertStringContainsString('#f2efe7', strtolower($cssContent));

        // Verifikasi modul tema spesifik
        $this->assertStringContainsString('.card-situan', $cssContent);
        $this->assertStringContainsString('.card-sirani', $cssContent);
        $this->assertStringContainsString('.card-ppdb', $cssContent);
        $this->assertStringContainsString('.card-web', $cssContent);

        // Verifikasi font hitam tetap berlaku pada dashboard DCC
        $this->assertStringContainsString('color: #000000', $cssContent);
    }
}

