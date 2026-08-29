<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LaporanFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($user);
    }

    public function test_filter_laporan_harian_siswa(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '1001', 'nama' => 'Budi Siswa', 'status' => 'aktif']);
        $sr = SiswaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswa->id,
            'siswa_rombel_id' => $sr->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '07:05:00',
            'status' => 'hadir',
            'sumber_absen' => 'rfid_gate_1',
        ]);

        $res = $this->get('/laporan?kategori=siswa&periode=harian&tanggal=' . Carbon::today()->toDateString());
        $res->assertStatus(200);
        $res->assertSee('Budi Siswa');
    }

    public function test_filter_laporan_guru_bulanan(): void
    {
        $guru = Guru::create(['nip' => '19800101', 'nama' => 'Drs. Supardi', 'jabatan' => 'Guru', 'status' => 'aktif']);
        Absensi::create([
            'pemilik_type' => 'guru',
            'pemilik_id' => $guru->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '06:55:00',
            'status' => 'hadir',
            'sumber_absen' => 'Lobi_Guru',
        ]);

        $bulan = Carbon::today()->format('Y-m');
        $res = $this->get('/laporan?kategori=guru&periode=bulanan&bulan=' . $bulan);
        $res->assertStatus(200);
        $res->assertSee('Drs. Supardi');
    }

    public function test_export_csv_laporan_siswa_dan_guru(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '1001', 'nama' => 'Budi Siswa', 'status' => 'aktif']);
        $sr = SiswaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswa->id,
            'siswa_rombel_id' => $sr->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '07:05:00',
            'status' => 'hadir',
            'sumber_absen' => 'rfid_gate_1',
        ]);

        $resSiswa = $this->get('/laporan/export-csv?kategori=siswa&periode=harian&tanggal=' . Carbon::today()->toDateString());
        $resSiswa->assertStatus(200);
        $resSiswa->assertHeader('Content-Disposition');

        $guru = Guru::create(['nip' => '19800101', 'nama' => 'Drs. Supardi', 'jabatan' => 'Guru', 'status' => 'aktif']);
        Absensi::create([
            'pemilik_type' => 'guru',
            'pemilik_id' => $guru->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '06:55:00',
            'status' => 'hadir',
            'sumber_absen' => 'Lobi_Guru',
        ]);

        $resGuru = $this->get('/laporan/export-csv?kategori=guru&periode=harian&tanggal=' . Carbon::today()->toDateString());
        $resGuru->assertStatus(200);
        $resGuru->assertHeader('Content-Disposition');
    }

    public function test_rekap_mingguan_siswa_per_kelas(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '1001', 'nama' => 'Budi Siswa', 'status' => 'aktif']);
        $sr = SiswaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        $res = $this->get("/laporan?kategori=siswa&periode=mingguan&rombel_id={$rombel->id}");
        $res->assertStatus(200);
        $res->assertSee('Budi Siswa');
        $res->assertSee('X TKJ 1');
    }

    public function test_laporan_individu_siswa_rinci(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '1001', 'nama' => 'Budi Siswa', 'status' => 'aktif']);
        $sr = SiswaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswa->id,
            'siswa_rombel_id' => $sr->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '07:05:00',
            'jam_pulang' => '15:35:00',
            'status' => 'hadir',
            'sumber_absen' => 'rfid_gate_1',
        ]);

        $res = $this->get("/laporan?kategori=siswa&periode=individu&siswa_id={$siswa->id}");
        $res->assertStatus(200);
        $res->assertSee('Budi Siswa');
        $res->assertSee('07:05:00');
        $res->assertSee('15:35:00');
    }

    public function test_cetak_pdf_laporan_presensi(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '1001', 'nama' => 'Budi Siswa', 'status' => 'aktif']);
        $sr = SiswaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswa->id,
            'siswa_rombel_id' => $sr->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '07:05:00',
            'status' => 'hadir',
            'sumber_absen' => 'rfid_gate_1',
        ]);

        $res = $this->get('/laporan/cetak-pdf?kategori=siswa&periode=harian&tanggal=' . Carbon::today()->toDateString());
        $res->assertOk();
        $res->assertSee('LAPORAN REKAPITULASI PRESENSI');
        $res->assertSee('Budi Siswa');
    }

    public function test_wali_kelas_terkunci_hanya_ke_rombel_sendiri_dan_tidak_bisa_akses_presensi_guru(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);

        $guru = Guru::create(['nip' => '19900101', 'nama' => 'Pak Wali Test', 'jabatan' => 'Guru', 'status' => 'aktif']);

        $waliUser = User::create([
            'name' => 'Wali Kelas Test',
            'email' => 'wali_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'wali_kelas',
            'guru_id' => $guru->id,
        ]);

        $rombelWali = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan->id,
            'nama_rombel' => 'X TKJ 1',
            'tingkat' => 'X',
            'wali_kelas_id' => $guru->id,
        ]);

        $rombelLain = Rombel::create([
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id' => $jurusan->id,
            'nama_rombel' => 'X TKJ 2',
            'tingkat' => 'X',
        ]);

        $siswaWali = Siswa::create(['nis' => '2001', 'nama' => 'Siswa Kelas Sendiri', 'status' => 'aktif']);
        SiswaRombel::create(['siswa_id' => $siswaWali->id, 'rombel_id' => $rombelWali->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        $siswaLain = Siswa::create(['nis' => '2002', 'nama' => 'Siswa Kelas Lain', 'status' => 'aktif']);
        SiswaRombel::create(['siswa_id' => $siswaLain->id, 'rombel_id' => $rombelLain->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        $this->actingAs($waliUser);

        // Akses laporan siswa -> data kelas lain tidak boleh muncul
        $res = $this->get('/laporan?kategori=siswa&periode=harian');
        $res->assertOk();
        $res->assertSee('X TKJ 1');
        $res->assertDontSee('X TKJ 2');
        $res->assertDontSee('Presensi Guru &');

        // Jika mencoba request kategori=guru -> otomatis diarahkan ke siswa
        $resGuru = $this->get('/laporan?kategori=guru&periode=harian');
        $resGuru->assertOk();
        $resGuru->assertViewHas('kategori', 'siswa');
    }

    public function test_guru_bk_tidak_bisa_akses_presensi_guru(): void
    {
        $bkUser = User::create([
            'name' => 'Guru BK Test',
            'email' => 'bk_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru_bk',
        ]);

        $this->actingAs($bkUser);

        $res = $this->get('/laporan?kategori=guru&periode=harian');
        $res->assertOk();
        $res->assertViewHas('kategori', 'siswa');
        $res->assertDontSee('Presensi Guru &');
    }

    public function test_guru_piket_dapat_mengakses_laporan_siswa(): void
    {
        $piketUser = User::create([
            'name' => 'Guru Piket Test',
            'email' => 'piket_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru_piket',
        ]);

        $this->actingAs($piketUser);
        $res = $this->get('/laporan');
        $res->assertOk();
        $res->assertSee('Rekapitulasi Presensi');
    }

    public function test_role_tidak_berwenang_ditolak_akses_laporan(): void
    {
        $guruBiasa = User::create([
            'name' => 'Guru Biasa',
            'email' => 'guru_biasa@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $this->actingAs($guruBiasa);
        $res = $this->get('/laporan');
        $res->assertStatus(403);
    }
}

