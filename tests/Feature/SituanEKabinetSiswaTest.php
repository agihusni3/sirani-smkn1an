<?php

namespace Tests\Feature;

use App\Models\ArsipDokumenSiswa;
use App\Models\PpdbPendaftar;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SituanEKabinetSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $stafTu;
    protected Siswa $siswa;
    protected Rombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'     => 'admin',
            'username' => 'admin_tu_siswa',
        ]);

        $this->stafTu = User::factory()->create([
            'role'     => 'staf_tu',
            'username' => 'staf_tu_siswa',
        ]);

        $ta = TahunAjaran::create([
            'nama'      => '2026/2027',
            'is_active' => true,
        ]);

        $jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => '10',
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $this->siswa = Siswa::create([
            'nama'          => 'Ahmad Fauzi',
            'nisn'          => '0081234567',
            'jenis_kelamin' => 'L',
            'status'        => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id'           => $this->siswa->id,
            'rombel_id'          => $this->rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    public function test_admin_dan_staf_tu_dapat_melihat_tab_laci_arsip_siswa(): void
    {
        $response = $this->actingAs($this->stafTu)->get(route('situan.ekabinet.index', ['tab' => 'siswa']));
        $response->assertOk();
        $response->assertSee('Laci Arsip Siswa (Peserta Didik)');
        $response->assertSee('Total Arsip Siswa');
    }

    public function test_staf_tu_dapat_mengunggah_berkas_arsip_siswa_ke_ekabinet(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('ijazah_smp_fauzi.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->stafTu)->post(route('situan.ekabinet.siswa.store'), [
            'siswa_id'        => $this->siswa->id,
            'kategori_berkas' => 'ijazah_smp',
            'nama_dokumen'    => 'Ijazah SMP Negeri 1 Air Naningan',
            'nomor_dokumen'   => 'DN-01/D-SMP/26/001234',
            'tanggal_dokumen' => '2026-06-15',
            'file_dokumen'    => $file,
            'keterangan'      => 'Asli terverifikasi untuk berkas induk',
        ]);

        $response->assertRedirect(route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $this->siswa->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('arsip_dokumen_siswas', [
            'siswa_id'        => $this->siswa->id,
            'kategori_berkas' => 'ijazah_smp',
            'nama_dokumen'    => 'Ijazah SMP Negeri 1 Air Naningan',
            'nomor_dokumen'   => 'DN-01/D-SMP/26/001234',
        ]);

        $arsip = ArsipDokumenSiswa::where('siswa_id', $this->siswa->id)->first();
        $this->assertNotNull($arsip);
        Storage::disk('public')->assertExists($arsip->file_path);
    }

    public function test_arsip_siswa_dapat_difilter_berdasarkan_rombel_dan_kategori(): void
    {
        ArsipDokumenSiswa::create([
            'siswa_id'        => $this->siswa->id,
            'kategori_berkas' => 'akta_kelahiran',
            'nama_dokumen'    => 'Akta Kelahiran Fauzi',
            'file_path'       => 'arsip_siswa/' . $this->siswa->id . '/akta.pdf',
        ]);

        $responseFilter = $this->actingAs($this->admin)->get(route('situan.ekabinet.index', [
            'tab'            => 'siswa',
            'rombel_id'      => $this->rombel->id,
            'kategori_siswa' => 'akta_kelahiran',
        ]));

        $responseFilter->assertOk();
        $responseFilter->assertSee('Akta Kelahiran Fauzi');
        $responseFilter->assertSee('Ahmad Fauzi');
    }

    public function test_staf_tu_dapat_menghapus_berkas_siswa_dari_ekabinet(): void
    {
        Storage::fake('public');

        $path = 'arsip_siswa/' . $this->siswa->id . '/test_kk.pdf';
        Storage::disk('public')->put($path, 'dummy content');

        $arsip = ArsipDokumenSiswa::create([
            'siswa_id'        => $this->siswa->id,
            'kategori_berkas' => 'kartu_keluarga',
            'nama_dokumen'    => 'KK Keluarga Fauzi',
            'file_path'       => $path,
        ]);

        $response = $this->actingAs($this->stafTu)->delete(route('situan.ekabinet.siswa.destroy', $arsip->id));
        $response->assertRedirect(route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $this->siswa->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('arsip_dokumen_siswas', ['id' => $arsip->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_sinkronisasi_otomatis_berkas_dari_ppdb_ke_ekabinet_siswa(): void
    {
        Storage::fake('public');

        $kkPath = 'ppdb/kk_fauzi.pdf';
        $aktaPath = 'ppdb/akta_fauzi.pdf';
        Storage::disk('public')->put($kkPath, 'kk pdf');
        Storage::disk('public')->put($aktaPath, 'akta pdf');

        // Buat data pendaftar PPDB yang match dengan siswa
        PpdbPendaftar::create([
            'no_pendaftaran' => 'PPDB-2026-001',
            'nisn'           => $this->siswa->nisn,
            'nama_lengkap'   => $this->siswa->nama,
            'jenis_kelamin'  => 'L',
            'tempat_lahir'   => 'Tanggamus',
            'tanggal_lahir'  => '2010-01-01',
            'asal_sekolah'   => 'SMPN 1 Air Naningan',
            'alamat'         => 'Air Naningan',
            'no_hp_ortu'     => '08123456789',
            'jurusan_id_1'   => $this->rombel->jurusan_id,
            'status'         => 'diterima',
            'siswa_id'       => $this->siswa->id,
            'berkas_kk'      => $kkPath,
            'berkas_akta'    => $aktaPath,
        ]);

        $responseSync = $this->actingAs($this->admin)->post(route('situan.ekabinet.sync-ppdb'));
        $responseSync->assertRedirect(route('situan.ekabinet.index', ['tab' => 'siswa']));
        $responseSync->assertSessionHas('success');

        // Verifikasi bahwa berkas PPDB terkonversi menjadi arsip_dokumen_siswas
        $this->assertDatabaseHas('arsip_dokumen_siswas', [
            'siswa_id'        => $this->siswa->id,
            'kategori_berkas' => 'kartu_keluarga',
            'file_path'       => $kkPath,
        ]);

        $this->assertDatabaseHas('arsip_dokumen_siswas', [
            'siswa_id'        => $this->siswa->id,
            'kategori_berkas' => 'akta_kelahiran',
            'file_path'       => $aktaPath,
        ]);
    }
}
