<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\NotifikasiDraftService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiPanggilanAlphaTest extends TestCase
{
    use RefreshDatabase;

    private User $waliUser;
    private User $adminUser;
    private Guru $guru;
    private Siswa $siswa;
    private Rombel $rombel;
    private TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);

        $this->ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        
        $this->guru = Guru::create([
            'nip'    => '198501012010011001',
            'nama'   => 'Bapak Budi Santoso, S.Pd.',
            'no_hp'  => '085211223344',
            'status' => 'aktif'
        ]);

        $this->waliUser = User::factory()->create([
            'role'    => 'wali_kelas',
            'guru_id' => $this->guru->id,
        ]);

        $this->rombel = Rombel::create([
            'nama_rombel'     => 'XI RPL 2',
            'tingkat'         => 11,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $this->ta->id,
            'wali_kelas_id'   => $this->guru->id,
        ]);

        $this->siswa = Siswa::create([
            'nis'        => '2001',
            'nisn'       => '0023456789',
            'nama'       => 'Andi Setiawan',
            'nama_ortu'  => 'Ibu Wulandari',
            'no_hp_ortu' => '081298765432',
            'status'     => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id'           => $this->siswa->id,
            'rombel_id'          => $this->rombel->id,
            'tahun_ajaran_id'    => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_akumulasi_3_kali_alpha_otomatis_menerbitkan_draf_panggilan_ortu()
    {
        // 1. Catat 2x Alpha (belum mencapai 3)
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->subDays(4)->toDateString(),
            'status'       => 'alpha',
        ]);
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->subDays(2)->toDateString(),
            'status'       => 'alpha',
        ]);

        $draft1 = NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($this->siswa);
        $this->assertNull($draft1);

        // 2. Catat Alpha ke-3
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'status'       => 'alpha',
        ]);

        $draft2 = NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($this->siswa);
        $this->assertNotNull($draft2);
        $this->assertEquals('panggilan_ortu', $draft2->kategori);
        $this->assertEquals('pending', $draft2->status);
        $this->assertStringContainsString('3x ketidakhadiran tanpa keterangan', $draft2->pesan);
        $this->assertStringContainsString('Andi Setiawan', $draft2->pesan);

        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id' => $this->siswa->id,
            'kategori' => 'panggilan_ortu',
        ]);
    }

    public function test_pesan_otomatis_terkirim_langsung_ke_wali_kelas_tanpa_verifikasi_manual()
    {
        // Berikan 3 Alpha
        for ($i = 1; $i <= 3; $i++) {
            Absensi::create([
                'pemilik_type' => 'siswa',
                'pemilik_id'   => $this->siswa->id,
                'tanggal'      => Carbon::today()->subDays($i)->toDateString(),
                'status'       => 'alpha',
            ]);
        }

        // Panggil evaluasi
        NotifikasiDraftService::cekAkumulasiAlphaDanBuatPanggilan($this->siswa);

        // Verifikasi bahwa notifikasi ke wali kelas berstatus TERKIRIM langsung tanpa menunggu verifikasi
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id'  => $this->siswa->id,
            'kategori'  => 'peringatan_wali_kelas',
            'no_tujuan' => '085211223344',
            'status'    => 'terkirim',
        ]);

        $notifWali = NotifikasiOrtu::where('siswa_id', $this->siswa->id)
            ->where('kategori', 'peringatan_wali_kelas')
            ->first();

        $this->assertNotNull($notifWali);
        $this->assertEquals('Sistem Otomatis (Auto Sent)', $notifWali->diverifikasi_oleh);
        $this->assertNotNull($notifWali->waktu_kirim);
        $this->assertStringContainsString('Bapak/Ibu Wali Kelas', $notifWali->pesan);
        $this->assertStringContainsString('Andi Setiawan', $notifWali->pesan);
        $this->assertStringContainsString('2001', $notifWali->pesan);
        $this->assertStringContainsString('XI RPL 2', $notifWali->pesan);
        $this->assertStringContainsString('3x Pelanggaran', $notifWali->pesan);
        $this->assertStringContainsString('surat/cetak?siswa_id=', $notifWali->pesan);
    }

    public function test_hanya_administrator_yang_dapat_mengubah_ketentuan_pelanggaran()
    {
        // 1. Wali Kelas mencoba mengubah -> Ditolak (403)
        $responseWali = $this->actingAs($this->waliUser)->post('/notifikasi/pengaturan', [
            'wa_provider'        => 'simulasi',
            'ambang_batas_alpha' => 4,
            'template_terlambat' => 'test',
            'template_alpha'     => 'test',
            'template_izin'      => 'test',
            'template_sakit'     => 'test',
            'template_bolos'     => 'test',
            'template_wali_kelas'=> 'test',
        ]);
        $responseWali->assertStatus(403);

        // 2. Admin mengubah -> Berhasil
        $responseAdmin = $this->actingAs($this->adminUser)->post('/notifikasi/pengaturan', [
            'wa_provider'                => 'simulasi',
            'ambang_batas_alpha'         => 4,
            'hitung_bolos_bersama_alpha' => '1',
            'auto_notif_wali_kelas'      => '1',
            'template_terlambat'         => 'Test Terlambat',
            'template_alpha'             => 'Test Alpha',
            'template_izin'              => 'Test Izin',
            'template_sakit'             => 'Test Sakit',
            'template_bolos'             => 'Test Bolos',
            'template_wali_kelas'        => 'Template Peringatan Wali Kustom {nama_siswa}',
        ]);
        $responseAdmin->assertRedirect();

        $setting = PengaturanNotifikasi::first();
        $this->assertEquals(4, $setting->ambang_batas_alpha);
        $this->assertEquals('Template Peringatan Wali Kustom {nama_siswa}', $setting->template_wali_kelas);
    }

    public function test_evaluasi_alpha_command_memicu_panggilan_ortu_ketika_mencapai_3_alpha()
    {
        // Paksa ke hari kerja (Senin) agar command tidak di-skip karena weekend
        Carbon::setTestNow(Carbon::parse('next monday'));

        // Berikan 2 Alpha sebelumnya
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->subDays(3)->toDateString(),
            'status'       => 'alpha',
        ]);
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->subDays(1)->toDateString(),
            'status'       => 'alpha',
        ]);

        // Jalankan evaluasi alpha otomatis hari ini
        $this->artisan('absensi:evaluasi-alpha', ['tanggal' => Carbon::today()->toDateString()])
            ->assertExitCode(0);

        // Siswa sekarang punya 3 Alpha dan harus ada draf panggilan ortu + notif wali kelas
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id' => $this->siswa->id,
            'kategori' => 'panggilan_ortu',
        ]);
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id' => $this->siswa->id,
            'kategori' => 'peringatan_wali_kelas',
            'status'   => 'terkirim',
        ]);

        Carbon::setTestNow(); // reset
    }
}

