<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\SuratKeluar;
use App\Models\BukuSkKepsek;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersuratanSiraniSituanSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Siswa $siswa;
    private Rombel $rombel;
    private Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer & Jaringan']);
        $this->guru = Guru::create([
            'nama' => 'Drs. Ahmad Fauzi, M.Pd.',
            'nip' => '198001012005011001',
            'status' => 'aktif',
            'status_kepegawaian' => 'PNS',
            'tmt_kgb_terakhir' => '2024-01-01',
        ]);

        $this->rombel = Rombel::create([
            'nama_rombel'     => 'XI TKJ 1',
            'tingkat'         => 11,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id'   => $this->guru->id,
        ]);

        $this->siswa = Siswa::create([
            'nis'        => '2001',
            'nisn'       => '0098765432',
            'nama'       => 'Dimas Maulana',
            'nama_ortu'  => 'Bambang Sudibyo',
            'no_hp_ortu' => '081299998888',
            'status'     => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id'           => $this->siswa->id,
            'rombel_id'          => $this->rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_surat_panggilan_ortu_sirani_otomatis_tercatat_di_buku_agenda_surat_keluar_situan(): void
    {
        // Pastikan awal surat keluar masih 0
        $this->assertEquals(0, SuratKeluar::count());

        $notif = NotifikasiOrtu::create([
            'siswa_id'  => $this->siswa->id,
            'kategori'  => 'panggilan_ortu',
            'tanggal'   => Carbon::today()->toDateString(),
            'no_tujuan' => '681299998888',
            'nama_ortu' => 'Bambang Sudibyo',
            'judul'     => 'Panggilan Ortu Sesi 1',
            'pesan'     => 'Kehadiran wali ke sekolah',
            'status'    => 'pending',
        ]);

        // Akses cetak surat panggilan ortu di SIRANI
        $response = $this->actingAs($this->admin)->get(route('surat.cetak', $notif->id));
        $response->assertOk();

        // Cek bahwa surat keluar tersinkronisasi ke DB
        $this->assertEquals(1, SuratKeluar::count());
        $surat = SuratKeluar::first();
        $this->assertEquals('421.3', $surat->kode_klasifikasi);
        $this->assertEquals('sirani_kesiswaan', $surat->sumber_modul);
        $this->assertEquals('Panggilan Orang Tua', $surat->kategori_surat);
        $this->assertStringContainsString('001/421.3/SMKN1AN/', $surat->nomor_surat_lengkap);
        $this->assertStringContainsString('Dimas Maulana', $surat->perihal);

        // Cek relasi di notifikasi
        $notif->refresh();
        $this->assertEquals($surat->id, $notif->surat_keluar_id);

        // Verifikasi muncul di daftar Surat Keluar SITUAN TU
        $indexResponse = $this->actingAs($this->admin)->get(route('situan.surat-keluar.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee($surat->nomor_surat_lengkap);
        $indexResponse->assertSee('SIRANI Kesiswaan');
        $indexResponse->assertSee('Total Terbit: 1 Surat');
    }

    public function test_berita_acara_bk_sirani_otomatis_tercatat_di_buku_agenda_surat_keluar_situan(): void
    {
        $response = $this->actingAs($this->admin)->get(
            route('surat.cetak') . '?siswa_id=' . $this->siswa->id . '&kategori=berita_acara'
        );
        $response->assertOk();

        $surat = SuratKeluar::where('sumber_modul', 'sirani_bk')->first();
        $this->assertNotNull($surat);
        $this->assertStringContainsString('BA-BK', $surat->nomor_surat_lengkap);
        $this->assertStringContainsString('421.3', $surat->nomor_surat_lengkap);
        $this->assertEquals('Berita Acara BK', $surat->kategori_surat);

        // Verifikasi muncul di tabel SITUAN
        $indexResponse = $this->actingAs($this->admin)->get(route('situan.surat-keluar.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('SIRANI BK');
        $indexResponse->assertSee('Berita Acara BK');
    }

    public function test_surat_keterangan_bebas_masalah_tercatat_di_surat_keluar_situan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('siswa.surat-bebas-masalah', $this->siswa->id));
        $response->assertOk();

        $surat = SuratKeluar::where('kategori_surat', 'Suket Bebas Masalah')->first();
        $this->assertNotNull($surat);
        $this->assertEquals('421.5', $surat->kode_klasifikasi);
        $this->assertStringContainsString('001/421.5/SMKN1AN/', $surat->nomor_surat_lengkap);
        $this->assertEquals('sirani_disiplin', $surat->sumber_modul);
    }

    public function test_sk_kasus_disiplin_tercatat_di_buku_register_sk_dan_agenda_surat_keluar(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'    => $this->siswa->id,
            'status_tahap'=> 'tahap_4_kepsek',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.disiplin.sk.cetak', $kasus->id));
        $response->assertOk();

        $kasus->refresh();
        $this->assertNotNull($kasus->buku_sk_id);
        $this->assertNotNull($kasus->surat_keluar_id);

        $bukuSk = BukuSkKepsek::find($kasus->buku_sk_id);
        $this->assertNotNull($bukuSk);
        $this->assertStringContainsString('SK-DISIPLIN', $bukuSk->nomor_sk_lengkap);

        $suratKeluar = SuratKeluar::find($kasus->surat_keluar_id);
        $this->assertNotNull($suratKeluar);
        $this->assertEquals('sk_kepsek', $suratKeluar->jenis_surat);
        $this->assertEquals('sirani_disiplin', $suratKeluar->sumber_modul);
        $response->assertSee($suratKeluar->nomor_surat_lengkap);
    }
}
