<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Pengumuman;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengumumanTest extends TestCase
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
        $jurusan = Jurusan::create(['kode_jurusan' => 'TSM', 'nama_jurusan' => 'Teknik Sepeda Motor']);
        $guru = Guru::create(['nama' => 'Bpk. Hendra Wali', 'status' => 'aktif']);

        $this->rombel = Rombel::create([
            'nama_rombel'     => 'XII TSM 1',
            'tingkat'         => 12,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id'   => $guru->id,
        ]);

        $this->siswa = Siswa::create([
            'nis'        => '202601',
            'nisn'       => '0012345678',
            'nama'       => 'Ahmad Fathoni',
            'nama_ortu'  => 'Bapak Fathoni',
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

    public function test_admin_bisa_mengakses_halaman_pengumuman()
    {
        $response = $this->actingAs($this->admin)->get('/pengumuman');
        $response->assertOk();
        $response->assertSee('Pusat Pengumuman &amp; Broadcast', false);
        $response->assertSee('Formulir Penerbitan Pengumuman');
    }

    public function test_buat_pengumuman_semua_siswa_dengan_broadcast_wa()
    {
        $response = $this->actingAs($this->admin)->post('/pengumuman', [
            'judul'           => 'Pemberitahuan Upacara Hari Pramuka',
            'isi_pesan'       => 'Seluruh peserta didik wajib berseragam pramuka lengkap.',
            'kategori'        => 'kegiatan',
            'target_tipe'     => 'semua',
            'kirim_wa'        => '1',
            'tampil_portal'   => '1',
            'tampil_kios'     => '1',
            'tanggal_mulai'   => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect(route('pengumuman.index'));
        $this->assertDatabaseHas('pengumumans', [
            'judul'          => 'Pemberitahuan Upacara Hari Pramuka',
            'kategori'       => 'kegiatan',
            'target_tipe'    => 'semua',
            'total_target'   => 1,
            'total_terkirim' => 1,
        ]);
    }

    public function test_buat_pengumuman_khusus_rombel()
    {
        $response = $this->actingAs($this->admin)->post('/pengumuman', [
            'judul'           => 'Simulasi Uji Kompetensi Keahlian TSM',
            'isi_pesan'       => 'Membawa perlengkapan praktek dan wearpack resmi.',
            'kategori'        => 'akademik',
            'target_tipe'     => 'rombel',
            'target_id'       => (string) $this->rombel->id,
            'kirim_wa'        => '1',
            'tampil_portal'   => '1',
            'tanggal_mulai'   => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect(route('pengumuman.index'));
        $this->assertDatabaseHas('pengumumans', [
            'judul'       => 'Simulasi Uji Kompetensi Keahlian TSM',
            'target_tipe' => 'rombel',
            'target_nama' => 'Rombel XII TSM 1',
        ]);
    }

    public function test_toggle_status_aktif_pengumuman()
    {
        $p = Pengumuman::create([
            'judul'       => 'Pengumuman Uji Coba',
            'isi_pesan'   => 'Isi testing',
            'kategori'    => 'umum',
            'target_tipe' => 'semua',
            'is_active'   => true,
            'created_by'  => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post("/pengumuman/{$p->id}/toggle");
        $this->assertFalse($p->fresh()->is_active);

        $this->actingAs($this->admin)->post("/pengumuman/{$p->id}/toggle");
        $this->assertTrue($p->fresh()->is_active);
    }

    public function test_hapus_pengumuman()
    {
        $p = Pengumuman::create([
            'judul'       => 'Pengumuman Dihapus',
            'isi_pesan'   => 'Isi testing delete',
            'kategori'    => 'umum',
            'target_tipe' => 'semua',
            'is_active'   => true,
            'created_by'  => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->delete("/pengumuman/{$p->id}");
        $this->assertDatabaseMissing('pengumumans', ['id' => $p->id]);
    }

    public function test_pengumuman_aktif_tampil_di_portal_ortu()
    {
        Pengumuman::create([
            'judul'         => 'PENGUMUMAN LIBUR RAPAT GURU',
            'isi_pesan'     => 'Kegiatan belajar mandiri di rumah.',
            'kategori'      => 'darurat',
            'target_tipe'   => 'semua',
            'tampil_portal' => true,
            'is_active'     => true,
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'created_by'    => $this->admin->id,
        ]);

        $response = $this->get('/cek-presensi');
        $response->assertOk();
        $response->assertSee('PENGUMUMAN LIBUR RAPAT GURU');
        $response->assertSee('Kegiatan belajar mandiri di rumah.');
    }

    public function test_buat_pengumuman_khusus_siswa_pribadi()
    {
        $this->siswa->update(['no_hp_siswa' => '08987654321']);

        $response = $this->actingAs($this->admin)->post('/pengumuman', [
            'judul'              => 'Instruksi Membawa Baju Wearpack Bengkel',
            'isi_pesan'          => 'Besok praktikum kelistrikan sepeda motor wajib wearpack.',
            'kategori'           => 'akademik',
            'target_tipe'        => 'rombel',
            'target_id'          => (string) $this->rombel->id,
            'kirim_wa'           => '1',
            'target_penerima_wa' => 'siswa',
            'tampil_kios'        => '1',
            'tanggal_mulai'      => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect(route('pengumuman.index'));
        $this->assertDatabaseHas('pengumumans', [
            'judul'              => 'Instruksi Membawa Baju Wearpack Bengkel',
            'target_penerima_wa' => 'siswa',
            'total_target'       => 1,
            'total_terkirim'     => 1,
        ]);
    }

    public function test_buat_pengumuman_dengan_upload_banner_gambar()
    {
        Storage::fake('public');

        $fakeImage = UploadedFile::fake()->image('poster_lomba.png', 800, 600);

        $response = $this->actingAs($this->admin)->post('/pengumuman', [
            'judul'         => 'Lomba Kompetensi Siswa (LKS) SMK',
            'isi_pesan'     => 'Pendaftaran LKS Tingkat Kabupaten telah dibuka.',
            'banner_gambar' => $fakeImage,
            'kategori'      => 'kegiatan',
            'target_tipe'   => 'semua',
            'tampil_portal' => '1',
            'tanggal_mulai' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect(route('pengumuman.index'));
        
        $p = Pengumuman::where('judul', 'Lomba Kompetensi Siswa (LKS) SMK')->first();
        $this->assertNotNull($p);
        $this->assertNotNull($p->banner_gambar);
        Storage::disk('public')->assertExists($p->banner_gambar);
    }

    public function test_guru_piket_bisa_mengakses_dan_membuat_pengumuman_broadcast()
    {
        $guruPiket = User::factory()->create(['role' => 'guru_piket']);

        // 1. Akses halaman pengumuman
        $this->actingAs($guruPiket)->get('/pengumuman')->assertStatus(200);

        // 2. Buat broadcast pengumuman piket
        $response = $this->actingAs($guruPiket)->post('/pengumuman', [
            'judul'         => 'Pengumuman Pulang Cepat Hari Hujan Lebat',
            'isi_pesan'     => 'Diumumkan kepada seluruh siswa KBM selesai pukul 14:00.',
            'kategori'      => 'kedisiplinan',
            'target_tipe'   => 'semua',
            'tampil_portal' => '1',
            'tanggal_mulai' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect(route('pengumuman.index'));
        $this->assertDatabaseHas('pengumumans', [
            'judul' => 'Pengumuman Pulang Cepat Hari Hujan Lebat',
        ]);
    }
}
