<?php

namespace Tests\Feature;

use App\Models\AkademikPklSiswa;
use App\Models\AkademikPklTempat;
use App\Models\Guru;
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
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExtremeSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guruUser;
    protected User $guruLainUser;
    protected Guru $guru1;
    protected Guru $guru2;
    protected Siswa $siswa;
    protected TahunAjaran $ta;
    protected Rombel $rombel;
    protected KartuRfid $kartu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'username' => 'admin_test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer dan Jaringan']);
        $this->rombel = Rombel::create([
            'nama_rombel' => 'XII TKJ 1',
            'tingkat' => 'XII',
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
        ]);

        $this->guru1 = Guru::create([
            'nip' => '198001012005011001',
            'nama' => 'Bambang Pembimbing',
            'jenis_kelamin' => 'L',
        ]);

        $this->guru2 = Guru::create([
            'nip' => '198502022008011002',
            'nama' => 'Siti Guru Lain',
            'jenis_kelamin' => 'P',
        ]);

        $this->guruUser = User::create([
            'name' => 'Bambang Pembimbing',
            'email' => 'bambang@smkn1airnaningan.sch.id',
            'username' => 'bambang',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'guru_id' => $this->guru1->id,
        ]);

        $this->guruLainUser = User::create([
            'name' => 'Siti Guru Lain',
            'email' => 'siti@smkn1airnaningan.sch.id',
            'username' => 'siti',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'guru_id' => $this->guru2->id,
        ]);

        $this->siswa = Siswa::create([
            'nis' => '88001',
            'nisn' => '0088001122',
            'nama' => 'Ahmad Fuzzer',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->kartu = KartuRfid::create([
            'uid' => 'EXTREME9988',
            'pemilik_type' => 'siswa',
            'pemilik_id' => $this->siswa->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * TEST 1: SQL Injection Fuzzing pada parameter pencarian, filter, dan verifikasi
     */
    public function test_extreme_sql_injection_defense(): void
    {
        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE users; --",
            "' UNION SELECT id, name, password, email FROM users --",
            "1' AND SLEEP(5) --",
            "\" OR \"\"=\"",
            "' OR 1=1 #",
            "admin' --",
        ];

        foreach ($payloads as $sqli) {
            // 1. Cek portal ortu / direct link
            $res = $this->get('/presensi-siswa/' . urlencode($sqli));
            $this->assertNotEquals(500, $res->getStatusCode(), "SQLi payload menyebabkan uncaught error 500: {$sqli}");
            $this->assertTrue(in_array($res->getStatusCode(), [200, 302, 404]), "Status tidak lazim pada portal ortu: " . $res->getStatusCode());

            // 2. Cek verifikasi surat publik
            $resSurat = $this->get('/verifikasi-surat/' . urlencode($sqli));
            $this->assertNotEquals(500, $resSurat->getStatusCode(), "SQLi payload menyebabkan uncaught error 500 pada verifikasi surat: {$sqli}");

            // 3. Cek API RFID Scan dengan payload SQLi
            $resRfid = $this->postJson('/api/v1/rfid-scan', ['uid' => $sqli]);
            $this->assertNotEquals(500, $resRfid->getStatusCode(), "SQLi payload menyebabkan uncaught error 500 pada RFID API: {$sqli}");
        }

        // Pastikan tabel users tetap utuh dan data admin tidak hilang
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    /**
     * TEST 2: Path Traversal & LFI Attack Resistance
     */
    public function test_extreme_path_traversal_resistance(): void
    {
        $traversals = [
            '../../../../.env',
            '..%2F..%2F..%2F.env',
            '....//....//....//.env',
            '/etc/passwd',
            '../../../../etc/passwd',
            'php://filter/read=convert.base64-encode/resource=index.php',
        ];

        foreach ($traversals as $path) {
            // 1. Ekabinet file download (harus 403 atau 404, tidak boleh 200 dengan isi file .env)
            $res = $this->actingAs($this->admin)->get('/situan/ekabinet/file/' . $path);
            $this->assertTrue(in_array($res->getStatusCode(), [403, 404, 302]));
            if ($res->getStatusCode() === 200) {
                $this->assertStringNotContainsString('APP_KEY', $res->getContent());
                $this->assertStringNotContainsString('DB_PASSWORD', $res->getContent());
            }

            // 2. PPDB Berkas download
            $resPpdb = $this->actingAs($this->admin)->get('/admin/ppdb/berkas/' . $path);
            $this->assertTrue(in_array($resPpdb->getStatusCode(), [403, 404, 302]));
            if ($resPpdb->getStatusCode() === 200) {
                $this->assertStringNotContainsString('APP_KEY', $resPpdb->getContent());
            }
        }
    }

    /**
     * TEST 3: Extreme Input Boundaries & Fuzzing (Buffer overflow simulation & unicode anomaly)
     */
    public function test_extreme_input_boundaries_and_fuzzing(): void
    {
        // 1. String raksasa (50.000 karakter)
        $giantString = str_repeat('A', 50000);
        $res = $this->get('/presensi-siswa/' . substr($giantString, 0, 500));
        $this->assertNotEquals(500, $res->getStatusCode());

        // 2. Karakter kontrol, null byte, dan multi-byte UTF-8
        $weirdInputs = [
            "Test\0NullByte",
            "𝒯ℯ𝓈𝓉 𝒰𝓃𝒾𝒸ℴ𝒹ℯ 🔥🚀💯",
            "\r\n\t\x0B\x0C",
            "<?php phpinfo(); ?>",
            "<script>alert(1)</script>",
        ];

        foreach ($weirdInputs as $input) {
            $res = $this->get('/presensi-siswa/' . urlencode($input));
            $this->assertNotEquals(500, $res->getStatusCode(), "Payload karakter kontrol melempar fatal error 500!");
        }

        // 3. Integer overflow & anomali numerik pada query parameter
        $numericOverflows = [
            '9223372036854775808',
            '-9223372036854775809',
            '-1',
            'NaN',
            'Infinity',
            '0xDEADBEEF',
        ];

        foreach ($numericOverflows as $num) {
            $res = $this->actingAs($this->admin)->get('/surat/cetak/' . $num);
            $this->assertNotEquals(500, $res->getStatusCode(), "Angka overflow melempar fatal error 500!");
        }
    }

    /**
     * TEST 4: Mass Assignment & Broken Access Control pada Penilaian PKL
     */
    public function test_extreme_mass_assignment_and_idor_on_pkl(): void
    {
        $dudi = AkademikPklTempat::create([
            'nama_dudi' => 'PT Cyber Tech Solusindo',
            'bidang_usaha' => 'IT Solution',
        ]);

        // Coba injeksi nilai_pkl=100 dan predikat_pkl=A saat penempatan (Mass-Assignment Probe)
        $resStore = $this->actingAs($this->admin)->post(route('akademik.pkl.siswa.store'), [
            'siswa_id' => $this->siswa->id,
            'pkl_tempat_id' => $dudi->id,
            'guru_pembimbing_id' => $this->guru1->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status' => 'aktif',
            'nilai_pkl' => 99.99,
            'predikat_pkl' => 'A',
        ]);

        $resStore->assertRedirect();

        // Pastikan nilai_pkl dan predikat_pkl TIDAK bocor tersimpan saat penempatan
        $pklRecord = AkademikPklSiswa::where('siswa_id', $this->siswa->id)->first();
        $this->assertNotNull($pklRecord);
        $this->assertNull($pklRecord->nilai_pkl, "Kerentanan Mass-Assignment: nilai_pkl berhasil diinjeksi saat penempatan!");
        $this->assertNull($pklRecord->predikat_pkl, "Kerentanan Mass-Assignment: predikat_pkl berhasil diinjeksi saat penempatan!");

        // Uji Otorisasi: Guru 2 (bukan pembimbing) mencoba mengupdate nilai Guru 1 (IDOR Probe)
        $resUnauthorizedUpdate = $this->actingAs($this->guruLainUser)->put(route('akademik.pkl.nilai', ['id' => $pklRecord->id]), [
            'nilai_pkl' => 95,
            'status' => 'selesai',
        ]);

        // Wajib ditolak dengan 403 Forbidden
        $resUnauthorizedUpdate->assertStatus(403);

        // Guru 1 (pembimbing sah) mengupdate nilai
        $resAuthorizedUpdate = $this->actingAs($this->guruUser)->put(route('akademik.pkl.nilai', ['id' => $pklRecord->id]), [
            'nilai_pkl' => 88,
            'status' => 'selesai',
        ]);

        $resAuthorizedUpdate->assertRedirect();
        $pklRecord->refresh();
        $this->assertEquals(88, $pklRecord->nilai_pkl);
        $this->assertEquals('A', $pklRecord->predikat_pkl);
    }

    /**
     * TEST 5: Concurrency / Anti-Replay Simulation pada RFID Service
     */
    public function test_extreme_concurrency_anti_double_presence(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-09 07:10:00'));

        $service = new RfidScanService();

        // Scan pertama (Jam Masuk)
        $hasil1 = $service->scanRfid($this->kartu->uid);
        $this->assertTrue($hasil1['success']);
        $this->assertEquals('jam_masuk', $hasil1['type']);

        // Tembakan kedua berulang dalam hitungan detik yang sama (Rapid replay / cooldown)
        $hasil2 = $service->scanRfid($this->kartu->uid);

        // Sistem harus menangkap cooldown double scan
        $this->assertEquals('cooldown_double_scan', $hasil2['type'], "Anti-double scan cooldown gagal mendeteksi!");

        Carbon::setTestNow();
    }

    /**
     * TEST 6: Formula Injection Protection (CWE-1236)
     */
    public function test_extreme_formula_injection_sanitization(): void
    {
        // Masukkan nama siswa dengan payload Formula Injection
        $payloadSiswa = Siswa::create([
            'nis' => '88002',
            'nisn' => '0088001123',
            'nama' => "=cmd|' /C calc'!A0",
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        // Uji ekspor CSV Laporan
        $response = $this->actingAs($this->admin)->get('/laporan/export-csv?jenis=siswa&tanggal_mulai=2026-10-01&tanggal_selesai=2026-10-31');
        $this->assertNotEquals(500, $response->getStatusCode());

        if ($response->getStatusCode() === 200) {
            $content = $response->streamedContent();
            // Payload tidak boleh diawali dengan tanda sama dengan mentah tanpa kutip tunggal
            $this->assertStringNotContainsString(',=cmd', $content);
        }
    }

    /**
     * TEST 7: Role-Based Access Control (RBAC) Hardening
     */
    public function test_extreme_rbac_strict_enforcement(): void
    {
        // 1. Akses Tamu (Unauthenticated) ke endpoint sensitif
        $this->get('/sirani')->assertRedirect('/login');
        $this->get('/dcc')->assertRedirect('/login');
        $this->get('/dcc/akademik/pkl')->assertRedirect('/login');
        $this->get('/surat')->assertRedirect('/login');

        // 2. Guru biasa mencoba mengakses dashboard admin Situan / E-Kabinet
        $this->actingAs($this->guruUser)->get('/situan/dashboard')->assertStatus(403);

        // 3. Guru biasa mencoba mengakses menu seleksi PPDB Admin
        $this->actingAs($this->guruUser)->get('/admin/ppdb')->assertStatus(403);
    }

    /**
     * TEST 8: Anti-DoS & Rate Limiting Enforcement pada Verifikasi Surat Publik
     */
    public function test_extreme_rate_limiter_public_verification(): void
    {
        $hash = 'valid-test-hash-123';

        // Lakukan 60 request (batas limit)
        for ($i = 0; $i < 60; $i++) {
            $res = $this->get('/verifikasi-surat/' . $hash);
            $this->assertNotEquals(429, $res->getStatusCode(), "Rate limiter terpicu terlalu dini pada request ke-{$i}");
        }

        // Request ke-61 wajib diblokir oleh throttle middleware dengan HTTP 429
        $blockedRes = $this->get('/verifikasi-surat/' . $hash);
        $this->assertEquals(429, $blockedRes->getStatusCode(), "Rate limiter gagal memblokir flooding request (HTTP 429 tidak terpicu)!");
    }

    /**
     * TEST 9: XSS Injections across form endpoints
     */
    public function test_extreme_xss_injections(): void
    {
        $xssPayload = "<script>alert('XSS_AUDIT_PWNED')</script>";

        // Tambah tempat PKL dengan payload XSS
        $res = $this->actingAs($this->admin)->post(route('akademik.pkl.tempat.store'), [
            'nama_dudi' => 'Mitra Tech ' . $xssPayload,
            'bidang_usaha' => 'IT & Software',
        ]);

        $res->assertRedirect();

        // Verifikasi bahwa nama tersimpan tanpa merusak rendering
        $tempat = AkademikPklTempat::where('nama_dudi', 'like', '%Mitra Tech%')->first();
        $this->assertNotNull($tempat);

        // Tampilkan halaman index PKL dan pastikan skrip tidak dieksekusi mentah (harus escaped oleh blade e())
        $page = $this->actingAs($this->admin)->get(route('akademik.pkl.index'));
        $page->assertOk();
        $page->assertSee(e('Mitra Tech ' . $xssPayload), false);
    }
}
