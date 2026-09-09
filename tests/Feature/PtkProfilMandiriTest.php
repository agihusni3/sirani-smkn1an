<?php

namespace Tests\Feature;

use App\Models\ArsipDokumenPtk;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PtkProfilMandiriTest extends TestCase
{
    use RefreshDatabase;

    private Guru $guruA;
    private User $userGuruA;
    private Guru $guruB;
    private User $userGuruB;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->guruA = Guru::create([
            'nama'               => 'Budi Santoso, S.Pd.',
            'nip'                => '198501012010011005',
            'status'             => 'aktif',
            'status_kepegawaian' => 'PNS',
            'pangkat'            => 'Penata',
            'golongan_ruang'     => 'III/c',
        ]);

        $this->userGuruA = User::factory()->create([
            'name'     => 'Budi Santoso',
            'username' => 'budisantoso',
            'role'     => 'guru',
            'guru_id'  => $this->guruA->id,
        ]);

        $this->guruB = Guru::create([
            'nama'               => 'Siti Aminah, M.Pd.',
            'nip'                => '199002022015022003',
            'status'             => 'aktif',
            'status_kepegawaian' => 'PPPK',
            'pangkat'            => 'Ahli Pertama',
            'golongan_ruang'     => 'IX',
        ]);

        $this->userGuruB = User::factory()->create([
            'name'     => 'Siti Aminah',
            'username' => 'sitiaminah',
            'role'     => 'guru',
            'guru_id'  => $this->guruB->id,
        ]);
    }

    public function test_guru_dapat_mengakses_halaman_profil_dan_berkas_mandiri()
    {
        $res = $this->actingAs($this->userGuruA)->get(route('ptk.profil-saya'));

        $res->assertOk();
        $res->assertSee('Budi Santoso, S.Pd.');
        $res->assertSee('198501012010011005');
        $res->assertSee('Biodata Kepegawaian');
        $res->assertSee('Lemari Berkas Digital');
        $res->assertSee('Catatan Presensi Saya');
    }

    public function test_guru_dapat_mengunggah_berkas_digital_mandiri()
    {
        $file = UploadedFile::fake()->create('sk_pangkat_budi.pdf', 300, 'application/pdf');

        $res = $this->actingAs($this->userGuruA)->post(route('ptk.unggah-berkas'), [
            'kategori_berkas' => 'sk_pangkat_terakhir',
            'nama_dokumen'    => 'SK Kenaikan Pangkat Golongan III/c',
            'nomor_dokumen'   => '821.2/123/DISDIK/2025',
            'tanggal_dokumen' => '2025-04-01',
            'file_dokumen'    => $file,
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->assertDatabaseHas('arsip_dokumen_ptks', [
            'guru_id'         => $this->guruA->id,
            'kategori_berkas' => 'sk_pangkat_terakhir',
            'nama_dokumen'    => 'SK Kenaikan Pangkat Golongan III/c',
            'nomor_dokumen'   => '821.2/123/DISDIK/2025',
        ]);

        $dokumen = ArsipDokumenPtk::where('guru_id', $this->guruA->id)->first();
        $this->assertNotNull($dokumen);
        Storage::disk('public')->assertExists($dokumen->file_path);

        // Pastikan terlihat di halaman profil guru
        $resPage = $this->actingAs($this->userGuruA)->get(route('ptk.profil-saya'));
        $resPage->assertSee('SK Kenaikan Pangkat Golongan III/c');
    }

    public function test_guru_dapat_menghapus_berkas_sendiri_tetapi_dilarang_menghapus_berkas_guru_lain()
    {
        // Dokumen milik Guru A
        $dokA = ArsipDokumenPtk::create([
            'guru_id'         => $this->guruA->id,
            'kategori_berkas' => 'ktp',
            'nama_dokumen'    => 'Scan KTP Budi',
            'file_path'       => 'arsip_ptk/test_a.pdf',
        ]);

        // Dokumen milik Guru B
        $dokB = ArsipDokumenPtk::create([
            'guru_id'         => $this->guruB->id,
            'kategori_berkas' => 'ijazah',
            'nama_dokumen'    => 'Ijazah S2 Siti',
            'file_path'       => 'arsip_ptk/test_b.pdf',
        ]);

        // 1. Guru A mencoba menghapus dokumen Guru B -> Harus 403 Forbidden
        $resCuri = $this->actingAs($this->userGuruA)->delete(route('ptk.hapus-berkas', $dokB->id));
        $resCuri->assertStatus(403);
        $this->assertDatabaseHas('arsip_dokumen_ptks', ['id' => $dokB->id]);

        // 2. Guru A menghapus dokumen miliknya sendiri -> Berhasil
        $resHapus = $this->actingAs($this->userGuruA)->delete(route('ptk.hapus-berkas', $dokA->id));
        $resHapus->assertRedirect();
        $resHapus->assertSessionHas('success');
        $this->assertDatabaseMissing('arsip_dokumen_ptks', ['id' => $dokA->id]);
    }
}
