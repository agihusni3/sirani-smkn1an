<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalOrtuTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_portal_ortu_bisa_diakses_secara_publik()
    {
        $response = $this->get('/cek-presensi');
        $response->assertOk();
        $response->assertSee('Portal Wali Murid');
        $response->assertSee('Pantau Kehadiran Putra/Putri Anda');
    }

    public function test_pencarian_siswa_dengan_nis_menampilkan_profil_dan_kehadiran()
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $guru = Guru::create(['nama' => 'Bapak Budi Wali', 'status' => 'aktif']);
        $rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id'   => $guru->id,
        ]);

        $siswa = Siswa::create([
            'nis'        => '1001',
            'nisn'       => '0012345678',
            'nama'       => 'Muhammad Rizky',
            'nama_ortu'  => 'Bapak Ahmad',
            'no_hp_ortu' => '081234567890',
            'status'     => 'aktif',
        ]);

        $sr = SiswaRombel::create([
            'siswa_id'           => $siswa->id,
            'rombel_id'          => $rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Absensi hari ini
        Absensi::create([
            'pemilik_type'    => 'siswa',
            'pemilik_id'      => $siswa->id,
            'siswa_rombel_id' => $sr->id,
            'tanggal'         => Carbon::today()->toDateString(),
            'jam_masuk'       => '07:05:00',
            'jam_pulang'      => '15:30:00',
            'status'          => 'hadir',
        ]);

        $response = $this->get('/cek-presensi?keyword=1001');
        $response->assertOk();
        $response->assertSee('Muhammad Rizky');
        $response->assertSee('X RPL 1');
        $response->assertSee('Bapak Budi Wali');
        $response->assertSee('Hadir Tepat Waktu');
        $response->assertSee('07:05 WIB');
    }

    public function test_pencarian_dengan_nis_tidak_ditemukan()
    {
        $response = $this->get('/cek-presensi?keyword=999999');
        $response->assertOk();
        $response->assertSee('Data Siswa Tidak Ditemukan');
    }

    public function test_filter_bulan_riwayat_kehadiran()
    {
        $siswa = Siswa::create([
            'nis'    => '1002',
            'nama'   => 'Siti Aisyah',
            'status' => 'aktif',
        ]);

        // Buat absensi bulan lalu
        $lastMonth = Carbon::today()->subMonth();
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $siswa->id,
            'tanggal'      => $lastMonth->copy()->startOfMonth()->toDateString(),
            'jam_masuk'    => '07:25:00',
            'status'       => 'terlambat',
        ]);

        $response = $this->get('/cek-presensi?keyword=1002&bulan=' . $lastMonth->format('Y-m'));
        $response->assertOk();
        $response->assertSee('Siti Aisyah');
        $response->assertSee('Terlambat');
    }

    public function test_siswa_memiliki_riwayat_surat_izin_ditampilkan_di_portal()
    {
        $siswa = Siswa::create([
            'nis'    => '1003',
            'nama'   => 'Fajar Nugraha',
            'status' => 'aktif',
        ]);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $siswa->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'status'       => 'sakit',
            'keterangan'   => 'Demam tinggi dan istirahat dokter',
        ]);

        $response = $this->get('/cek-presensi?keyword=1003');
        $response->assertOk();
        $response->assertSee('Fajar Nugraha');
        $response->assertSee('Sakit');
    }

    public function test_akses_langsung_presensi_siswa_via_url_nis()
    {
        $siswa = Siswa::create([
            'nis'    => '1004',
            'nama'   => 'Dian Permata',
            'status' => 'aktif',
        ]);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $siswa->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'jam_masuk'    => '07:10:00',
            'status'       => 'hadir',
        ]);

        $response = $this->get('/presensi-siswa/1004');
        $response->assertOk();
        $response->assertSee('Dian Permata');
        $response->assertSee('07:10 WIB');
        $response->assertSee('Hadir Tepat Waktu');
    }

    public function test_akses_langsung_cek_presensi_via_url_nis()
    {
        $siswa = Siswa::create([
            'nis'    => '1005',
            'nama'   => 'Eko Prasetyo',
            'status' => 'aktif',
        ]);

        $response = $this->get('/cek-presensi/1005');
        $response->assertOk();
        $response->assertSee('Eko Prasetyo');
    }

    public function test_portal_ortu_menampilkan_portofolio_karakter_dan_reward()
    {
        $siswa = Siswa::create([
            'nis'    => '1006',
            'nama'   => 'Rian Firmansyah',
            'status' => 'aktif',
        ]);

        $kasus = \App\Models\KasusDisiplin::create([
            'siswa_id'                => $siswa->id,
            'status_tahap'            => 'tahap_1_wali_kelas',
            'total_alpha'             => 1,
            'total_poin_pelanggaran'  => 10,
            'total_poin_pemulihan'    => 5,
            'is_active'               => true,
        ]);

        \App\Models\KasusDisiplinReward::create([
            'kasus_disiplin_id' => $kasus->id,
            'siswa_id'          => $siswa->id,
            'nama_tindakan'     => 'Petugas Sholat Berjamaah',
            'poin_dikurangi'    => 5,
            'tanggal'           => Carbon::today()->toDateString(),
            'dicatat_oleh'      => 'Guru PAI',
        ]);

        $response = $this->get('/cek-presensi/1006');
        $response->assertOk();
        $response->assertSee('Portofolio Karakter & Kredit Kedisiplinan');
        $response->assertSee('Petugas Sholat Berjamaah');
        $response->assertSee('Restorative Justice');
    }
}
