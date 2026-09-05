<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\BeritaSekolah;
use App\Models\Jurusan;
use App\Models\PpdbPendaftar;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\User;
use App\Models\WebsiteBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModulAuditLogTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
    }

    private function createStafTu(): User
    {
        return User::create([
            'name' => 'Staf Tata Usaha',
            'email' => 'tu@smkn1airnaningan.sch.id',
            'role' => 'staf_tu',
            'password' => Hash::make('password'),
        ]);
    }

    private function createPanitiaPpdb(): User
    {
        return User::create([
            'name' => 'Panitia PPDB',
            'email' => 'panitia.ppdb@smkn1airnaningan.sch.id',
            'role' => 'panitia_ppdb',
            'password' => Hash::make('password'),
        ]);
    }

    private function createHumas(): User
    {
        return User::create([
            'name' => 'Tim Humas',
            'email' => 'humas@smkn1airnaningan.sch.id',
            'role' => 'humas',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_audit_log_scopes_mengisolasi_pencatatan_antar_modul(): void
    {
        $admin = $this->createAdmin();

        // 1. Catat log SITUAN
        AuditLog::catat('create', 'siswa', 'Menambah data siswa baru', null, ['nama' => 'Budi Santoso']);
        AuditLog::catat('update', 'guru', 'Memperbarui biodata guru', ['nip' => '123'], ['nip' => '1234']);

        // 2. Catat log SIRANI
        AuditLog::catat('scan', 'absensi', 'Scan masuk RFID gerbang');
        AuditLog::catat('create', 'izin', 'Menerbitkan surat dispensasi siswa');

        // 3. Catat log PPDB
        AuditLog::catat('update', 'ppdb', 'Verifikasi berkas calon siswa');

        // 4. Catat log Web Humas
        AuditLog::catat('create', 'web_humas', 'Publikasi rilis berita SMK');

        // Verifikasi Isolasi Query
        $this->assertEquals(2, AuditLog::situan()->count());
        $this->assertEquals(2, AuditLog::sirani()->count());
        $this->assertEquals(1, AuditLog::ppdb()->count());
        $this->assertEquals(1, AuditLog::webHumas()->count());
        $this->assertEquals(6, AuditLog::count()); // Global agregator
    }

    public function test_verifikasi_ppdb_mencatat_audit_log_khusus_ppdb(): void
    {
        $admin = $this->createAdmin();

        $jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $pendaftar = PpdbPendaftar::create([
            'no_pendaftaran' => 'PPDB-2026-001',
            'nama_lengkap' => 'Calon Siswa Baru',
            'nisn' => '0098765432',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Tanggamus',
            'tanggal_lahir' => '2010-01-01',
            'asal_sekolah' => 'SMPN 1 Air Naningan',
            'alamat' => 'Air Naningan RT 01',
            'jurusan_id_1' => $jurusan->id,
            'status' => 'menunggu_verifikasi',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.ppdb.update_status', $pendaftar->id), [
            'status_pendaftaran' => 'berkas_valid',
            'jurusan_diterima_id' => $jurusan->id,
            'catatan' => 'Berkas lengkap dan sesuai',
        ]);

        $response->assertSessionHas('success');

        // Verifikasi tercatat di log PPDB
        $logPpdb = AuditLog::ppdb()->latest()->first();
        $this->assertNotNull($logPpdb);
        $this->assertEquals('ppdb', $logPpdb->modul);
        $this->assertEquals('update', $logPpdb->aksi);
        $this->assertStringContainsString('Calon Siswa Baru', $logPpdb->deskripsi);
    }

    public function test_publikasi_berita_mencatat_audit_log_khusus_web_humas(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.berita.store'), [
            'judul' => 'Peresmian Gedung Workshop Baru',
            'kategori' => 'berita',
            'ringkasan' => 'Gedung workshop baru resmi digunakan untuk praktik.',
            'konten' => 'Isi berita peresmian workshop SMKN 1 Air Naningan.',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.berita.index'));

        $logHumas = AuditLog::webHumas()->latest()->first();
        $this->assertNotNull($logHumas);
        $this->assertEquals('web_humas', $logHumas->modul);
        $this->assertEquals('create', $logHumas->aksi);
        $this->assertStringContainsString('Peresmian Gedung Workshop Baru', $logHumas->deskripsi);
    }

    public function test_akses_halaman_log_situan(): void
    {
        $admin = $this->createAdmin();
        $stafTu = $this->createStafTu();

        // 1. Admin bisa akses
        $respAdmin = $this->actingAs($admin)->get(route('situan.log'));
        $respAdmin->assertStatus(200);
        $respAdmin->assertSee('Audit Log Modul SITUAN');
        $respAdmin->assertSee('Log Aktivitas TU');

        // 2. Staf TU bisa akses
        $respTu = $this->actingAs($stafTu)->get(route('situan.log'));
        $respTu->assertStatus(200);

        // 3. User tanpa wewenang SITUAN ditolak
        $userLain = User::create([
            'name' => 'Warga Luar',
            'email' => 'luar@smkn1airnaningan.sch.id',
            'role' => 'siswa',
            'password' => Hash::make('password'),
        ]);
        $respDitolak = $this->actingAs($userLain)->get(route('situan.log'));
        $respDitolak->assertStatus(403);
    }

    public function test_akses_halaman_log_ppdb(): void
    {
        $admin = $this->createAdmin();
        $panitia = $this->createPanitiaPpdb();

        $respAdmin = $this->actingAs($admin)->get(route('admin.ppdb.log'));
        $respAdmin->assertStatus(200);
        $respAdmin->assertSee('Audit Trail Modul PPDB 2026');

        $respPanitia = $this->actingAs($panitia)->get(route('admin.ppdb.log'));
        $respPanitia->assertStatus(200);
    }

    public function test_akses_halaman_log_web_humas(): void
    {
        $admin = $this->createAdmin();
        $humas = $this->createHumas();

        $respAdmin = $this->actingAs($admin)->get(route('admin.berita.log'));
        $respAdmin->assertStatus(200);
        $respAdmin->assertSee('Audit Trail Modul Humas &amp; Website', false);

        $respHumas = $this->actingAs($humas)->get(route('admin.berita.log'));
        $respHumas->assertStatus(200);
    }

    public function test_master_dcc_audit_menampilkan_badge_group_semua_modul(): void
    {
        $admin = $this->createAdmin();

        AuditLog::catat('create', 'siswa', 'Menambah siswa');
        AuditLog::catat('scan', 'absensi', 'Scan gerbang');
        AuditLog::catat('update', 'ppdb', 'Verifikasi calon');
        AuditLog::catat('create', 'web_humas', 'Publikasi artikel');

        $response = $this->actingAs($admin)->get(route('audit.index'));
        $response->assertStatus(200);
        $response->assertSee('SITUAN');
        $response->assertSee('SIRANI');
        $response->assertSee('PPDB 2026');
        $response->assertSee('HUMAS &amp; WEB', false);
    }
}
