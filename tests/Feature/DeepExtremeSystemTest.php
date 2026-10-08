<?php

namespace Tests\Feature;

use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikAsesmenSoal;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikMataPelajaran;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeepExtremeSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guruUser;
    protected Guru $guru;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected TahunAjaran $ta;
    protected Rombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Admin Deep Test',
            'email' => 'admin_deep@smkn1airnaningan.sch.id',
            'username' => 'admin_deep',
            'password' => Hash::make('SecretPassword123!'),
            'role' => 'admin',
        ]);

        $this->guru = Guru::create([
            'nip' => '198701012010011005',
            'nama' => 'Drs. Supriyanto, M.Pd.',
            'jenis_kelamin' => 'L',
        ]);

        $this->guruUser = User::create([
            'name' => 'Drs. Supriyanto, M.Pd.',
            'email' => 'supriyanto@smkn1airnaningan.sch.id',
            'username' => 'supriyanto',
            'password' => Hash::make('GuruPassword123!'),
            'role' => 'guru',
            'guru_id' => $this->guru->id,
        ]);

        $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $this->rombel = Rombel::create([
            'nama_rombel' => 'XI RPL 1',
            'tingkat' => 'XI',
            'tahun_ajaran_id' => $this->ta->id,
            'jurusan_id' => $jurusan->id,
        ]);

        $this->siswa1 = Siswa::create([
            'nis' => '77001',
            'nisn' => '0077001111',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2008-05-15',
            'status' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'nis' => '77002',
            'nisn' => '0077002222',
            'nama' => 'Citra Lestari',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2008-08-20',
            'status' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa1->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $this->siswa2->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    /**
     * UJI 1: Malicious File Upload & Shell Upload Defense
     * Menguji upload file berbahaya (PHP script, polyglot shell, extension ganda)
     */
    public function test_deep_malicious_file_upload_defense(): void
    {
        $maliciousFiles = [
            UploadedFile::fake()->create('shell.php', 10, 'text/x-php'),
            UploadedFile::fake()->create('exploit.phtml', 10, 'application/x-php'),
            UploadedFile::fake()->create('backdoor.php.jpg', 15, 'text/x-php'),
            UploadedFile::fake()->create('malware.exe', 50, 'application/x-msdownload'),
            UploadedFile::fake()->create('script.phar', 20, 'application/octet-stream'),
            UploadedFile::fake()->create('payload.blade.php', 10, 'text/plain'),
        ];

        foreach ($maliciousFiles as $file) {
            // Upload ke e-kabinet PTK
            $res = $this->actingAs($this->admin)->post(route('situan.ekabinet.ptk.store'), [
                'guru_id' => $this->guru->id,
                'kategori_berkas' => 'ijazah',
                'nama_dokumen' => 'Dokumen Palsu',
                'file_dokumen' => $file,
            ]);

            // Harus ditolak dengan status validasi 422 atau redirect dengan validation error
            if ($res->getStatusCode() === 302) {
                $res->assertSessionHasErrors('file_dokumen');
            } else {
                $this->assertEquals(422, $res->getStatusCode(), "File berbahaya berhasil lolos tanpa validasi!");
            }
        }
    }

    /**
     * UJI 2: CBT Anti-Cheat & Answer Leak Prevention (Zero Exposure of Kunci Jawaban)
     */
    public function test_deep_cbt_answer_leak_and_session_lock(): void
    {
        $matpel = AkademikMataPelajaran::create([
            'kode_mapel' => 'KODERPL',
            'nama_mapel' => 'Pemrograman Web',
            'tingkat' => 'XI',
            'tahun_ajaran_id' => $this->ta->id,
            'jenis' => 'kejuruan',
        ]);

        $distribusi = AkademikDistribusiMengajar::create([
            'guru_id' => $this->guru->id,
            'mata_pelajaran_id' => $matpel->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => '1',
            'total_jam_per_minggu' => 4,
        ]);

        $asesmen = AkademikAsesmenOnline::create([
            'distribusi_id' => $distribusi->id,
            'judul' => 'Ujian Tengah Semester Pemrograman',
            'jenis' => 'uts',
            'semester' => '1',
            'durasi_menit' => 60,
            'is_active' => true,
            'target_tipe' => 'semua',
        ]);

        $soal = AkademikAsesmenSoal::create([
            'asesmen_id' => $asesmen->id,
            'nomor' => 1,
            'tipe' => 'pilihan_ganda',
            'pertanyaan' => 'Tag HTML untuk hyperlink adalah?',
            'opsi_a' => '<a>',
            'opsi_b' => '<link>',
            'opsi_c' => '<href>',
            'opsi_d' => '<p>',
            'kunci_jawaban' => 'A',
            'bobot' => 10,
        ]);

        // 1. Verifikasi model serialization tidak membocorkan kunci jawaban ke JSON / Array
        $soalArray = $soal->toArray();
        $this->assertArrayNotHasKey('kunci_jawaban', $soalArray, "KRITIS: Kunci jawaban bocor dalam serialisasi model!");
        $this->assertStringNotContainsString('"kunci_jawaban"', $soal->toJson(), "KRITIS: Kunci jawaban bocor dalam output JSON!");

        // 2. Simulasi Siswa 1 login ke portal CBT
        $this->withSession(['cbt_siswa_id' => $this->siswa1->id]);

        // 3. Siswa 1 submit jawaban tapi mengirim 'siswa_id' = Siswa 2 (IDOR Attempt)
        $resSubmit = $this->post(route('portal.asesmen.submit', ['id' => $asesmen->id]), [
            'siswa_id' => $this->siswa2->id, // Siswa 1 mencoba mengatasnamakan Siswa 2
            'jawaban' => [$soal->id => 'A'],
        ]);

        $resSubmit->assertRedirect();

        // 4. Pastikan hasil ujian HANYA tercatat untuk Siswa 1 (dari session server), BUKAN Siswa 2
        $hasilSiswa1 = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)
            ->where('siswa_id', $this->siswa1->id)
            ->first();
        $this->assertNotNull($hasilSiswa1, "Hasil ujian Siswa 1 tidak tercatat!");
        $this->assertEquals(100, $hasilSiswa1->nilai);

        $hasilSiswa2 = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)
            ->where('siswa_id', $this->siswa2->id)
            ->first();
        $this->assertNull($hasilSiswa2, "KERENTANAN IDOR: Siswa 1 berhasil memalsukan hasil ujian atas nama Siswa 2!");
    }

    /**
     * UJI 3: Grade Numeric Boundary & Overflow Defense
     */
    public function test_deep_grade_numeric_boundary_defense(): void
    {
        // Uji input nilai PKL dengan nilai di luar batas kewajaran
        $invalidGrades = [
            -10,
            105,
            99999,
            'seratus',
            '100; DROP TABLE users',
        ];

        foreach ($invalidGrades as $grade) {
            $res = $this->actingAs($this->admin)->put('/dcc/akademik/pkl/siswa/1/nilai', [
                'nilai_pkl' => $grade,
                'status' => 'selesai',
            ]);

            // Wajib ditolak oleh validasi (redirect back with errors atau 422)
            if ($res->getStatusCode() === 302) {
                $res->assertSessionHasErrors('nilai_pkl');
            } else {
                $this->assertTrue(in_array($res->getStatusCode(), [404, 422]));
            }
        }
    }

    /**
     * UJI 4: Date Tampering & Negative Duration on Leave Request
     */
    public function test_deep_date_tampering_defense(): void
    {
        // Ajukan izin siswa dengan tanggal selesai SEBELUM tanggal mulai (negative interval)
        $res = $this->actingAs($this->admin)->post('/piket/izin-siswa', [
            'siswa_id' => $this->siswa1->id,
            'kategori' => 'sakit',
            'tanggal_mulai' => '2026-10-15',
            'tanggal_selesai' => '2026-10-10', // mundur 5 hari!
            'keterangan' => 'Uji tanggal mundur',
        ]);

        if ($res->getStatusCode() === 302) {
            // Validasi after_or_equal wajib menangkap error ini
            $this->assertTrue(session()->has('errors') || session()->has('error'));
        } else {
            $this->assertNotEquals(200, $res->getStatusCode());
        }
    }

    /**
     * UJI 5: Bcrypt Strength & Type Confusion Resistance on Authentication
     */
    public function test_deep_authentication_type_confusion_resistance(): void
    {
        // 1. Password dengan format Magic Hash (PHP Type Juggling / loose comparison probe)
        $magicHashUser = User::create([
            'name' => 'Magic User',
            'email' => 'magic@smkn1airnaningan.sch.id',
            'username' => 'magicuser',
            'password' => Hash::make('0e123456789012345678901234567890'),
            'role' => 'guru',
        ]);

        // Login dengan password lain yang bernilai floating zero serupa
        $this->assertFalse(Hash::check('0e987654321098765432109876543210', $magicHashUser->password));

        // 2. Password dengan karakter string panjang (72 bytes Bcrypt limit check)
        $longPassword = str_repeat('BcryptLimitCheck12345', 5);
        $hashLong = Hash::make($longPassword);
        $this->assertTrue(Hash::check($longPassword, $hashLong));
    }

    /**
     * UJI 6: Webhook Non-POST Method Probing & Method Isolation
     */
    public function test_deep_deploy_webhook_method_isolation(): void
    {
        // Webhook deploy hanya boleh menerima POST
        $resGet = $this->get('/api/deploy-webhook');
        $this->assertTrue(in_array($resGet->getStatusCode(), [403, 405]), "GET request ke deploy-webhook tidak ditolak!");

        $resPut = $this->put('/api/deploy-webhook');
        $this->assertTrue(in_array($resPut->getStatusCode(), [403, 405]), "PUT request ke deploy-webhook tidak ditolak!");

        $resDelete = $this->delete('/api/deploy-webhook');
        $this->assertTrue(in_array($resDelete->getStatusCode(), [403, 405]), "DELETE request ke deploy-webhook tidak ditolak!");
    }

    /**
     * UJI 7: Protection Against Privilege Escalation on User Registration/Profile
     */
    public function test_deep_privilege_escalation_prevention(): void
    {
        // Guru mencoba mengupdate profilnya sendiri dan menyuntikkan 'role' => 'admin'
        $res = $this->actingAs($this->guruUser)->post('/profil', [
            'name' => 'Hacker Guru',
            'email' => 'supriyanto@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'is_admin' => 1,
        ]);

        // Periksa data guru di database: role HARUS tetap 'guru'
        $this->guruUser->refresh();
        $this->assertEquals('guru', $this->guruUser->role, "KRITIS: Terjadi Privilege Escalation! Guru berhasil mengubah role menjadi admin!");
    }
}
