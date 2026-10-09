<?php

namespace Tests\Feature;

use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikAsesmenSoal;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikMataPelajaran;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\JadwalHariIni;
use App\Models\Jurusan;
use App\Models\KartuRfid;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\RfidScanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Pengujian Keamanan Tingkat Tinggi Berdasarkan Metodologi Formal:
 * OWASP Web Security Testing Guide (WSTG v4.2) & NIST SP 800-115
 */
class WstgMethodologySecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $waliKelasUser;
    protected Guru $guruWali;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected TahunAjaran $ta;
    protected Rombel $rombel1;
    protected Rombel $rombel2;
    protected KartuRfid $kartuSiswa1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'WSTG Security Auditor',
            'email' => 'auditor@smkn1airnaningan.sch.id',
            'username' => 'wstg_auditor',
            'password' => Hash::make('AuditorPass9988!'),
            'role' => 'admin',
        ]);

        $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);

        $this->guruWali = Guru::create([
            'nip' => '198203032009011003',
            'nama' => 'Budi Santoso, S.Kom.',
            'jenis_kelamin' => 'L',
        ]);

        $this->waliKelasUser = User::create([
            'name' => 'Budi Santoso, S.Kom.',
            'email' => 'wali_rpl1@smkn1airnaningan.sch.id',
            'username' => 'wali_rpl1',
            'password' => Hash::make('WaliPass123!'),
            'role' => 'wali_kelas',
            'guru_id' => $this->guruWali->id,
        ]);

        $this->rombel1 = Rombel::create([
            'nama_rombel' => 'X RPL 1',
            'tingkat' => 10,
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
            'wali_kelas_id' => $this->guruWali->id,
        ]);

        $this->rombel2 = Rombel::create([
            'nama_rombel' => 'X RPL 2',
            'tingkat' => 10,
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
        ]);

        $this->siswa1 = Siswa::create([
            'nis' => '10001',
            'nisn' => '0010001111',
            'nama' => 'Siswa Kelas Satu',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'nis' => '10002',
            'nisn' => '0010002222',
            'nama' => 'Siswa Kelas Dua',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombel1->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombel2->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->kartuSiswa1 = KartuRfid::create([
            'uid' => 'WSTG998877',
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa1->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * WSTG-BUSL-01: Business Logic Testing - Circumvention of Work Flows (Re-submission Denial)
     */
    public function test_wstg_busl_01_cbt_re_submission_denial(): void
    {
        $matpel = AkademikMataPelajaran::create([
            'kode_mapel' => 'WSTG01',
            'nama_mapel' => 'Basis Data',
            'tingkat' => 'X',
            'tahun_ajaran_id' => $this->ta->id,
            'jenis' => 'kejuruan',
        ]);

        $distribusi = AkademikDistribusiMengajar::create([
            'guru_id' => $this->guruWali->id,
            'mata_pelajaran_id' => $matpel->id,
            'rombel_id' => $this->rombel1->id,
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => '1',
            'total_jam_per_minggu' => 4,
        ]);

        $asesmen = AkademikAsesmenOnline::create([
            'distribusi_id' => $distribusi->id,
            'judul' => 'Ujian Akhir Semester Basis Data',
            'jenis' => 'uas',
            'semester' => '1',
            'durasi_menit' => 60,
            'is_active' => true,
            'target_tipe' => 'semua',
        ]);

        $soal = AkademikAsesmenSoal::create([
            'asesmen_id' => $asesmen->id,
            'nomor' => 1,
            'tipe' => 'pilihan_ganda',
            'pertanyaan' => 'Perintah DDL adalah?',
            'opsi_a' => 'CREATE',
            'opsi_b' => 'SELECT',
            'opsi_c' => 'INSERT',
            'opsi_d' => 'UPDATE',
            'kunci_jawaban' => 'A',
            'bobot' => 10,
        ]);

        // Siswa 1 telah menyelesaikan ujian dengan nilai 50
        AkademikAsesmenHasil::create([
            'asesmen_id' => $asesmen->id,
            'siswa_id' => $this->siswa1->id,
            'jawaban' => [$soal->id => 'B'], // salah
            'nilai' => 0,
            'is_selesai' => true,
            'mulai_pada' => now()->subMinutes(30),
            'selesai_pada' => now()->subMinutes(10),
            'status_kejujuran' => 'jujur',
        ]);

        // Simulasi siswa 1 mencoba menembak submit ulang dengan jawaban A (benar)
        $res = $this->withSession(['cbt_siswa_id' => $this->siswa1->id])
            ->postJson(route('portal.asesmen.submit', ['id' => $asesmen->id]), [
                'jawaban' => [$soal->id => 'A'],
            ]);

        // Sistem wajib menolak dengan 422
        $res->assertStatus(422);
        $res->assertJsonFragment(['error' => 'Anda sudah menyelesaikan asesmen ini sebelumnya.']);

        // Pastikan nilai di database tetap 0 dan tidak ter-overwrite
        $hasil = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)->where('siswa_id', $this->siswa1->id)->first();
        $this->assertEquals(0, $hasil->nilai, "KERENTANAN BUSINESS LOGIC: Siswa berhasil menimpa nilai ujian yang sudah dikunci!");
    }

    /**
     * WSTG-BUSL-02: Business Logic - Inactive & Expired Exam Submission Rejection
     */
    public function test_wstg_busl_02_cbt_inactive_and_expired_exam(): void
    {
        $matpel = AkademikMataPelajaran::create([
            'kode_mapel' => 'WSTG02',
            'nama_mapel' => 'Jaringan Komputer',
            'tingkat' => 'X',
            'tahun_ajaran_id' => $this->ta->id,
            'jenis' => 'kejuruan',
        ]);

        $distribusi = AkademikDistribusiMengajar::create([
            'guru_id' => $this->guruWali->id,
            'mata_pelajaran_id' => $matpel->id,
            'rombel_id' => $this->rombel1->id,
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => '1',
            'total_jam_per_minggu' => 4,
        ]);

        // 1. Asesmen tidak aktif
        $asesmenInactive = AkademikAsesmenOnline::create([
            'distribusi_id' => $distribusi->id,
            'judul' => 'Ujian Nonaktif',
            'jenis' => 'harian',
            'semester' => '1',
            'durasi_menit' => 30,
            'is_active' => false,
            'target_tipe' => 'semua',
        ]);

        $resInactive = $this->withSession(['cbt_siswa_id' => $this->siswa1->id])
            ->postJson(route('portal.asesmen.submit', ['id' => $asesmenInactive->id]), [
                'jawaban' => [],
            ]);
        $resInactive->assertStatus(422);

        // 2. Asesmen yang sudah melewati tenggat waktu (expired)
        $asesmenExpired = AkademikAsesmenOnline::create([
            'distribusi_id' => $distribusi->id,
            'judul' => 'Ujian Kadaluwarsa',
            'jenis' => 'harian',
            'semester' => '1',
            'durasi_menit' => 30,
            'is_active' => true,
            'ditutup_pada' => now()->subHours(2), // 2 jam yang lalu
            'target_tipe' => 'semua',
        ]);

        $resExpired = $this->withSession(['cbt_siswa_id' => $this->siswa1->id])
            ->postJson(route('portal.asesmen.submit', ['id' => $asesmenExpired->id]), [
                'jawaban' => [],
            ]);
        $resExpired->assertStatus(422);
    }

    /**
     * WSTG-BUSL-03: Business Logic - Premature RFID Departure Prevention
     */
    public function test_wstg_busl_03_rfid_premature_departure_denial(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-09 07:05:00'));
        $service = new RfidScanService();

        // 1. Tap Masuk (Berhasil)
        $masuk = $service->scanRfid($this->kartuSiswa1->uid);
        $this->assertTrue($masuk['success']);
        $this->assertEquals('jam_masuk', $masuk['type']);

        // 2. Majukan waktu ke jam 10:30 (masih jam KBM, sebelum jam pulang 14:30)
        Carbon::setTestNow(Carbon::parse('2026-10-09 10:30:00'));

        $tapPulangCepat = $service->scanRfid($this->kartuSiswa1->uid);

        // Wajib ditolak dengan tipe 'belum_waktunya_pulang'
        $this->assertEquals('belum_waktunya_pulang', $tapPulangCepat['type']);

        Carbon::setTestNow();
    }

    /**
     * WSTG-BUSL-04: Business Logic - Holiday Attendance Rejection
     */
    public function test_wstg_busl_04_holiday_attendance_rejection(): void
    {
        $today = '2026-10-09';
        HariLibur::create([
            'nama_libur' => 'Libur Nasional Uji Metodologi',
            'jenis' => 'libur_nasional',
            'tanggal_mulai' => $today,
            'tanggal_selesai' => $today,
            'keterangan' => 'Libur Nasional Uji Metodologi',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-10-09 07:10:00'));
        $service = new RfidScanService();

        $scan = $service->scanRfid($this->kartuSiswa1->uid);

        // Harus teridentifikasi sebagai hari libur
        $this->assertEquals('hari_libur', $scan['type']);

        Carbon::setTestNow();
    }

    /**
     * WSTG-ATHN-02: Authentication Testing - User Enumeration Defense
     */
    public function test_wstg_athn_02_identical_error_message_on_auth_failure(): void
    {
        // 1. Coba login dengan username yang TIDAK TERDAFTAR
        $resNonExistent = $this->post('/login', [
            'username' => 'ghost_user_does_not_exist',
            'password' => 'anypassword',
        ]);

        $resNonExistent->assertSessionHasErrors('email');
        $errorNonExistent = session('errors')->first('email');

        // 2. Coba login dengan username yang BENAR tapi password SALAH
        $resWrongPassword = $this->post('/login', [
            'username' => $this->admin->username,
            'password' => 'WrongPassword123!',
        ]);

        $resWrongPassword->assertSessionHasErrors('email');
        $errorWrongPassword = session('errors')->first('email');

        // WSTG Compliance: Pesan error wajib 100% IDENTIK untuk mencegah user enumeration
        $this->assertEquals($errorNonExistent, $errorWrongPassword, "KERENTANAN USER ENUMERATION: Pesan error login membocorkan status keberadaan username!");
        $this->assertEquals('Identitas (Username/Nama/Email/NIP) atau kata sandi yang Anda masukkan salah.', $errorNonExistent);
    }

    /**
     * WSTG-SESS-03: Session Management - Complete Session Destruction on Logout
     */
    public function test_wstg_sess_03_session_destruction_on_logout(): void
    {
        // 1. Login user
        $this->actingAs($this->admin);
        $this->assertTrue(Auth::check());

        // 2. Lakukan logout
        $resLogout = $this->post('/logout');
        $resLogout->assertRedirect('/login');

        // 3. Pastikan otentikasi musnah
        $this->assertFalse(Auth::check(), "Session tidak ter-invalidasi saat logout!");
    }

    /**
     * WSTG-ATHZ-02: Authorization Testing - Horizontal Privilege Escalation Prevention
     */
    public function test_wstg_athz_02_cross_class_horizontal_escalation_denial(): void
    {
        // Wali Kelas Rombel 1 mencoba memodifikasi siswa di Rombel 2
        $resTamper = $this->actingAs($this->waliKelasUser)->put("/siswa/{$this->siswa2->id}", [
            'nama' => 'Hacked Name by Other Wali Kelas',
            'status' => 'aktif',
        ]);

        $resTamper->assertSessionHas('error');

        // Pastikan nama siswa 2 di database TIDAK termutasi
        $this->siswa2->refresh();
        $this->assertEquals('Siswa Kelas Dua', $this->siswa2->nama, "KERENTANAN BOLA/IDOR: Wali kelas berhasil mengubah data siswa di rombel lain!");
    }

    /**
     * WSTG-CRYP-01: Cryptography & Unicode Normalization Resilience
     */
    public function test_wstg_cryp_01_unicode_normalization_and_zero_width_space(): void
    {
        // Sisipkan zero-width space (\u{200B}) pada input login
        $zwspUsername = "wstg\u{200B}_auditor";

        $res = $this->post('/login', [
            'username' => $zwspUsername,
            'password' => 'AuditorPass9988!',
        ]);

        // Sistem tidak boleh crash / melempar 500 error
        $this->assertNotEquals(500, $res->getStatusCode());
    }
}
