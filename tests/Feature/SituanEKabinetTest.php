<?php

namespace Tests\Feature;

use App\Models\ArsipDokumenPtk;
use App\Models\ArsipSekolah;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SituanEKabinetTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $stafTu;
    protected User $kepsek;
    protected User $guruUser;
    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'     => 'admin',
            'username' => 'admin_tu',
        ]);

        $this->stafTu = User::factory()->create([
            'role'     => 'staf_tu',
            'username' => 'staf_tu_1',
        ]);

        $this->kepsek = User::factory()->create([
            'role'     => 'kepala_sekolah',
            'username' => 'kepsek_tu',
        ]);

        $this->guruUser = User::factory()->create([
            'role'     => 'guru',
            'username' => 'guru_biasa',
        ]);

        $this->guru = Guru::create([
            'nama'               => 'Budi Santoso, S.Pd.',
            'nip'                => '198505102010011015',
            'status'             => 'aktif',
            'jenis_ptk'          => 'Guru Mapel',
            'golongan_ruang'     => 'III/b',
            'status_kepegawaian' => 'PNS',
        ]);
    }

    public function test_admin_dan_staf_tu_dapat_mengakses_e_kabinet_sentral(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get(route('situan.ekabinet.index'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('E-Kabinet Digital Terpusat');
        $responseAdmin->assertSee('Lemari Arsip & E-Kabinet Sekolah');

        $responseStafTu = $this->actingAs($this->stafTu)->get(route('situan.ekabinet.index'));
        $responseStafTu->assertOk();
        $responseStafTu->assertSee('Laci Arsip PTK & Kepegawaian');
        $responseStafTu->assertSee('Laci Arsip Lembaga & MoU DUDI');

        $responseKepsek = $this->actingAs($this->kepsek)->get(route('situan.ekabinet.index'));
        $responseKepsek->assertOk();
    }

    public function test_guru_biasa_ditolak_mengakses_e_kabinet_sentral(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('situan.ekabinet.index'));
        $response->assertForbidden();
    }

    public function test_staf_tu_dapat_mengunggah_arsip_ptk_terpusat_dari_e_kabinet(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('sk_kgb_budi.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->stafTu)->post(route('situan.ekabinet.ptk.store'), [
            'guru_id'         => $this->guru->id,
            'kategori_berkas' => 'sk_kgb_terakhir',
            'nama_dokumen'    => 'SK Kenaikan Gaji Berkala 2026 Budi',
            'nomor_dokumen'   => '821.2/100/SMKN1AN/2026',
            'tanggal_dokumen' => '2026-03-01',
            'file_dokumen'    => $file,
        ]);

        $response->assertRedirect(route('situan.ekabinet.index', ['tab' => 'ptk']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('arsip_dokumen_ptks', [
            'guru_id'         => $this->guru->id,
            'kategori_berkas' => 'sk_kgb_terakhir',
            'nama_dokumen'    => 'SK Kenaikan Gaji Berkala 2026 Budi',
        ]);
    }

    public function test_staf_tu_dapat_mengunggah_dokumen_lembaga_dan_mou_industri(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('mou_toyota.pdf', 800, 'application/pdf');

        $response = $this->actingAs($this->stafTu)->post(route('situan.ekabinet.lembaga.store'), [
            'kategori_arsip'   => 'mou_industri',
            'nama_arsip'       => 'MoU Praktek Kerja Lapangan dengan Auto 2000',
            'nomor_dokumen'    => '421.5/088/SMKN1AN/2026',
            'mitra_instansi'   => 'PT Astra International - Auto 2000',
            'tanggal_dokumen'  => '2026-01-15',
            'tanggal_berakhir' => '2029-01-15',
            'file_dokumen'     => $file,
            'keterangan'       => 'Kerjasama magang dan penyelarasan kurikulum TKRO',
        ]);

        $response->assertRedirect(route('situan.ekabinet.index', ['tab' => 'lembaga']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('arsip_sekolahs', [
            'kategori_arsip' => 'mou_industri',
            'nama_arsip'     => 'MoU Praktek Kerja Lapangan dengan Auto 2000',
            'mitra_instansi' => 'PT Astra International - Auto 2000',
        ]);
    }

    public function test_penghapusan_berkas_ptk_dan_lembaga_dari_e_kabinet(): void
    {
        Storage::fake('public');

        $arsipPtk = ArsipDokumenPtk::create([
            'guru_id'         => $this->guru->id,
            'kategori_berkas' => 'ijazah',
            'nama_dokumen'    => 'Ijazah S1 Pendidikan Budi',
            'file_path'       => 'arsip_ptk/test_ijazah.pdf',
        ]);

        $arsipLembaga = ArsipSekolah::create([
            'kategori_arsip' => 'akreditasi',
            'nama_arsip'     => 'Sertifikat Akreditasi A BAN-SM 2024',
            'file_path'      => 'arsip_sekolah/test_akreditasi.pdf',
        ]);

        // Hapus Arsip PTK
        $resDeletePtk = $this->actingAs($this->admin)->delete(route('situan.ekabinet.ptk.destroy', $arsipPtk->id));
        $resDeletePtk->assertRedirect(route('situan.ekabinet.index', ['tab' => 'ptk']));
        $this->assertDatabaseMissing('arsip_dokumen_ptks', ['id' => $arsipPtk->id]);

        // Hapus Arsip Lembaga
        $resDeleteLembaga = $this->actingAs($this->admin)->delete(route('situan.ekabinet.lembaga.destroy', $arsipLembaga->id));
        $resDeleteLembaga->assertRedirect(route('situan.ekabinet.index', ['tab' => 'lembaga']));
        $this->assertDatabaseMissing('arsip_sekolahs', ['id' => $arsipLembaga->id]);
    }

    public function test_sertifikat_pelatihan_guru_otomatis_tersinkronisasi_dengan_e_kabinet(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('sertifikat_diklat.pdf', 300, 'application/pdf');

        // 1. Upload dari menu guru / portofolio
        $response = $this->actingAs($this->admin)->post("/guru/{$this->guru->id}/sertifikat", [
            'nama_pelatihan'  => 'Pelatihan Pembelajaran Berbasis AI',
            'penyelenggara'   => 'BBPPMPV BMTI',
            'tahun'           => '2026',
            'file_sertifikat' => $file,
        ]);

        $response->assertSessionHas('success');

        // Pastikan tersimpan di sertifikat_gurus
        $this->assertDatabaseHas('sertifikat_gurus', [
            'guru_id'        => $this->guru->id,
            'nama_pelatihan' => 'Pelatihan Pembelajaran Berbasis AI',
            'penyelenggara'  => 'BBPPMPV BMTI',
        ]);

        // Pastikan otomatis tersinkronkan ke E-Kabinet (arsip_dokumen_ptks)
        $this->assertDatabaseHas('arsip_dokumen_ptks', [
            'guru_id'         => $this->guru->id,
            'kategori_berkas' => 'sertifikat_pelatihan',
            'nama_dokumen'    => 'Sertifikat: Pelatihan Pembelajaran Berbasis AI (BBPPMPV BMTI)',
        ]);

        // Cek tampil di halaman E-Kabinet
        $responseKabinet = $this->actingAs($this->admin)->get(route('situan.ekabinet.index', ['tab' => 'ptk']));
        $responseKabinet->assertStatus(200);
        $responseKabinet->assertSee('Pelatihan Pembelajaran Berbasis AI');
        $responseKabinet->assertSee('Sertifikat Pelatihan / Diklat');
    }
}
