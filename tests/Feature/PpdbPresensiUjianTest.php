<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\PpdbAbsensiUjian;
use App\Models\PpdbPendaftar;
use App\Models\PpdbUjianPeserta;
use App\Models\PpdbUjianSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PpdbPresensiUjianTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $jurusan;
    protected $setting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Panitia Presensi',
            'email'    => 'panitia.presensi@smkn1airnaningan.sch.id',
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kuota'        => 36,
            'is_aktif'     => true,
        ]);

        $this->setting = PpdbUjianSetting::create([
            'judul_ujian'    => 'Ujian Seleksi CBT PPDB 2026',
            'tahun_ajaran'   => '2026/2027',
            'jumlah_soal_pg' => 30,
            'bobot_pg'       => 70,
            'bobot_esai'     => 30,
            'durasi_menit'   => 60,
            'is_active'      => true,
            'created_by'     => $this->admin->id,
        ]);
    }

    protected function buatPendaftar($no, $nisn, $nama, $status = 'terverifikasi')
    {
        return PpdbPendaftar::create([
            'no_pendaftaran'     => $no,
            'nisn'               => $nisn,
            'nama_lengkap'       => $nama,
            'jenis_kelamin'      => 'L',
            'tempat_lahir'       => 'Tanggamus',
            'tanggal_lahir'      => '2010-01-01',
            'asal_sekolah'       => 'SMPN 1 Air Naningan',
            'alamat'             => 'Air Naningan RT 01',
            'no_hp_ortu'         => '08123456789',
            'jurusan_id_1'       => $this->jurusan->id,
            'status'             => $status,
            'jadwal_tes_tanggal' => Carbon::today()->toDateString(),
            'jadwal_tes_sesi'    => 'Sesi 1',
            'jadwal_tes_ruang'   => 'Lab Komputer 1',
            'nilai_rata_rata'    => 85.00,
        ]);
    }

    public function test_admin_bisa_mengakses_kios_presensi()
    {
        $this->buatPendaftar('PPDB-2026-0001', '0012345678', 'Ahmad Dani');

        $response = $this->actingAs($this->admin)->get(route('admin.ppdb.presensi.kios'));

        $response->assertStatus(200);
        $response->assertSee('Kios Presensi Ujian Tulis CBT');
        $response->assertSee('2D BARCODE / QR SCANNER');
        $response->assertSee('AHMAD DANI');
    }

    public function test_scan_barcode_2d_berhasil_mencatat_kehadiran()
    {
        $pendaftar = $this->buatPendaftar('PPDB-2026-0001', '0012345678', 'Budi Santoso');

        $response = $this->actingAs($this->admin)->postJson(route('admin.ppdb.presensi.scan'), [
            'input_code' => 'PPDB-2026-0001',
            'metode'     => 'barcode_scanner',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'pendaftar' => [
                'no_pendaftaran' => 'PPDB-2026-0001',
                'nama_lengkap'   => 'BUDI SANTOSO',
            ]
        ]);

        $this->assertDatabaseHas('ppdb_absensi_ujians', [
            'ppdb_pendaftar_id' => $pendaftar->id,
            'no_pendaftaran'    => 'PPDB-2026-0001',
            'status_kehadiran'  => 'hadir',
        ]);
    }

    public function test_scan_barcode_menolak_siswa_yang_berkasnya_belum_terverifikasi()
    {
        $pendaftar = $this->buatPendaftar('PPDB-2026-0002', '0012345679', 'Citra Lestari', 'menunggu');

        $response = $this->actingAs($this->admin)->postJson(route('admin.ppdb.presensi.scan'), [
            'input_code' => 'PPDB-2026-0002',
            'metode'     => 'barcode_scanner',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'invalid_status',
        ]);

        $this->assertDatabaseMissing('ppdb_absensi_ujians', [
            'ppdb_pendaftar_id' => $pendaftar->id,
        ]);
    }

    public function test_scan_barcode_ganda_mengembalikan_status_already_attended()
    {
        $pendaftar = $this->buatPendaftar('PPDB-2026-0003', '0012345680', 'Doni Pratama');

        // Scan pertama
        $this->actingAs($this->admin)->postJson(route('admin.ppdb.presensi.scan'), [
            'input_code' => 'PPDB-2026-0003',
        ]);

        // Scan kedua (dobel)
        $response = $this->actingAs($this->admin)->postJson(route('admin.ppdb.presensi.scan'), [
            'input_code' => 'PPDB-2026-0003',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'already_attended',
        ]);
        $response->assertSee('SUDAH tercatat hadir');

        // Jumlah di database tetap 1
        $this->assertEquals(1, PpdbAbsensiUjian::where('ppdb_pendaftar_id', $pendaftar->id)->count());
    }

    public function test_cbt_gatekeeper_memblokir_siswa_yang_belum_presensi()
    {
        $pendaftar = $this->buatPendaftar('PPDB-2026-0004', '0012345681', 'Eka Saputra');

        // Akses langsung halaman kerjakan CBT tanpa scan presensi
        $response = $this->get(route('ppdb.ujian.kerjakan', ['nomor' => $pendaftar->no_pendaftaran]));

        $response->assertRedirect(route('ppdb.ujian.konfirmasi', ['nomor' => $pendaftar->no_pendaftaran]));
        $response->assertSessionHas('error');
    }

    public function test_cbt_gatekeeper_mengizinkan_siswa_yang_sudah_presensi()
    {
        $pendaftar = $this->buatPendaftar('PPDB-2026-0005', '0012345682', 'Fajar Nugraha');

        // Presensi terlebih dahulu
        PpdbAbsensiUjian::create([
            'ppdb_pendaftar_id'     => $pendaftar->id,
            'ppdb_ujian_setting_id' => $this->setting->id,
            'no_pendaftaran'        => $pendaftar->no_pendaftaran,
            'jadwal_tanggal'        => Carbon::today()->toDateString(),
            'sesi_ujian'            => 'Sesi 1',
            'ruang_ujian'           => 'Lab Komputer 1',
            'waktu_hadir'           => now(),
            'status_kehadiran'      => 'hadir',
            'metode_presensi'       => 'barcode_scanner',
        ]);

        // Sekarang akses pengerjaan CBT
        $response = $this->get(route('ppdb.ujian.kerjakan', ['nomor' => $pendaftar->no_pendaftaran]));

        $response->assertStatus(200);
        $response->assertSee('Ujian CBT');
        $response->assertSee('Fajar Nugraha');
    }

    public function test_panitia_dapat_mengesahkan_dan_membatalkan_presensi_manual()
    {
        $pendaftar = $this->buatPendaftar('PPDB-2026-0006', '0012345683', 'Gilang Ramadhan');

        // Hadir manual
        $responseManual = $this->actingAs($this->admin)->post(route('admin.ppdb.presensi.manual', $pendaftar->id));
        $responseManual->assertSessionHas('success');

        $absensi = PpdbAbsensiUjian::where('ppdb_pendaftar_id', $pendaftar->id)->first();
        $this->assertNotNull($absensi);
        $this->assertEquals('manual_panitia', $absensi->metode_presensi);

        // Batalkan presensi
        $responseBatal = $this->actingAs($this->admin)->delete(route('admin.ppdb.presensi.batal', $absensi->id));
        $responseBatal->assertSessionHas('success');

        $this->assertDatabaseMissing('ppdb_absensi_ujians', ['id' => $absensi->id]);
    }

    public function test_cetak_daftar_hadir_dan_berita_acara_dapat_diakses()
    {
        $this->buatPendaftar('PPDB-2026-0007', '0012345684', 'Hana Pertiwi');

        $response = $this->actingAs($this->admin)->get(route('admin.ppdb.presensi.cetak'));

        $response->assertStatus(200);
        $response->assertSee('DAFTAR HADIR &amp; BERITA ACARA PELAKSANAAN UJIAN SELEKSI', false);
        $response->assertSee('HANA PERTIWI');
    }
}
