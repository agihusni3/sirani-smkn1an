<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanSekolah;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratDanBackupTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Siswa $siswa;
    private Rombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $guru = Guru::create(['nama' => 'Bapak Budi Wali, S.Kom.', 'status' => 'aktif']);
        
        $this->rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id'   => $guru->id,
        ]);

        $this->siswa = Siswa::create([
            'nis'        => '1001',
            'nisn'       => '0012345678',
            'nama'       => 'Muhammad Rizky Pratama',
            'nama_ortu'  => 'Bapak Hendra',
            'no_hp_ortu' => '081234567890',
            'status'     => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id'           => $this->siswa->id,
            'rombel_id'          => $this->rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_admin_bisa_mengakses_dan_memperbarui_pengaturan_profil_sekolah()
    {
        $response = $this->actingAs($this->admin)->get('/pengaturan-sekolah');
        $response->assertOk();
        $response->assertSee('Pengaturan Profil Sekolah');
        $response->assertSee('SMK NEGERI 1 AIR NANINGAN');

        $updateResponse = $this->actingAs($this->admin)->post('/pengaturan-sekolah', [
            'nama_instansi_atas' => 'PEMERINTAH PROVINSI LAMPUNG',
            'nama_dinas'         => 'DINAS PENDIDIKAN DAN KEBUDAYAAN',
            'nama_sekolah'       => 'SMK NEGERI 1 AIR NANINGAN HEBAT',
            'npsn'               => '69888999',
            'alamat'             => 'Jl. Raya Air Naningan No. 10',
            'kecamatan'          => 'Air Naningan',
            'kabupaten'          => 'Kab. Tanggamus',
            'provinsi'           => 'Lampung',
            'kode_pos'           => '35379',
            'email'              => 'info@smkn1airnaningan.sch.id',
            'website'            => 'smkn1airnaningan.sch.id',
            'nama_kepala_sekolah'=> 'Drs. H. Ahmad Sudrajat, M.Pd.',
            'nip_kepala_sekolah' => '19750510 200003 1 005',
        ]);

        $updateResponse->assertRedirect(route('admin.pengaturan-sekolah.index'));
        $this->assertDatabaseHas('pengaturan_sekolahs', [
            'nama_sekolah' => 'SMK NEGERI 1 AIR NANINGAN HEBAT',
        ]);
    }

    public function test_cetak_surat_panggilan_ortu_merender_data_siswa_dan_kop_dinas()
    {
        $response = $this->actingAs($this->admin)->get('/surat/cetak?siswa_id=' . $this->siswa->id . '&kategori=panggilan_ortu');
        $response->assertOk();
        $response->assertSee('SURAT PANGGILAN ORANG TUA / WALI MURID');
        $response->assertSee('Muhammad Rizky Pratama');
        $response->assertSee('X RPL 1');
        $response->assertSee('Bapak Hendra');
        $response->assertSee('Bapak Budi Wali, S.Kom.');
    }

    public function test_cetak_surat_berdasarkan_id_notifikasi()
    {
        $notif = NotifikasiOrtu::create([
            'siswa_id'  => $this->siswa->id,
            'kategori'  => 'panggilan_ortu',
            'tanggal'   => Carbon::today()->toDateString(),
            'no_tujuan' => '681234567890',
            'nama_ortu' => 'Bapak Hendra',
            'judul'     => 'Panggilan Ortu',
            'pesan'     => 'Mohon hadir ke sekolah',
            'status'    => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get('/surat/cetak/' . $notif->id);
        $response->assertOk();
        $response->assertSee('Muhammad Rizky Pratama');
        $response->assertSee('SURAT PANGGILAN ORANG TUA / WALI MURID');
    }

    public function test_cetak_berita_acara_dinamis_merender_data_musyawarah_dan_komitmen()
    {
        $response = $this->actingAs($this->admin)->get('/surat/cetak?siswa_id=' . $this->siswa->id . '&kategori=berita_acara&nama_wali_hadir=Bpk.+Hendra+Pratama&catatan_hasil=Komitmen+kehadiran+pagi+dan+bimbingan+belajar');
        $response->assertOk();
        $response->assertSee('BERITA ACARA TINDAK LANJUT');
        $response->assertSee('Muhammad Rizky Pratama');
        $response->assertSee('Bpk. Hendra Pratama');
        $response->assertSee('Komitmen kehadiran pagi dan bimbingan belajar');
        $response->assertSee('BA-BK');
    }

    public function test_admin_bisa_mengakses_panel_backup_dan_mengunduh_file()
    {
        $response = $this->actingAs($this->admin)->get('/backup');
        $response->assertOk();
        $response->assertSee('Pencadangan &amp; Backup Database', false);
        $response->assertSee('Unduh Cadangan Sekarang');

        $downloadResponse = $this->actingAs($this->admin)->get('/backup/download');
        $downloadResponse->assertOk();
        $this->assertStringContainsString('attachment;', $downloadResponse->headers->get('content-disposition'));
    }

    public function test_cetak_surat_bebas_masalah_dan_resume_presensi_3_tahun()
    {
        $response = $this->actingAs($this->admin)->get('/siswa/' . $this->siswa->id . '/surat-bebas-masalah');
        $response->assertOk();
        $response->assertSee('SURAT KETERANGAN BEBAS KASUS DISIPLIN &amp; RESUME PRESENSI', false);
        $response->assertSee('Muhammad Rizky Pratama');
        $response->assertSee('BERSIH &amp; BEBAS TANGGUNGAN KASUS DISIPLIN', false);
    }
}
