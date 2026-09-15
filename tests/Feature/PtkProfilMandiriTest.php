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
        $res->assertDontSee('Catatan Presensi Saya');
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

    public function test_guru_dapat_mengunggah_foto_profil_mandiri_dan_sinkron_dengan_data_ptk()
    {
        $foto = UploadedFile::fake()->image('profil_budi.jpg', 400, 400);

        $res = $this->actingAs($this->userGuruA)->post(route('ptk.update-foto'), [
            'foto' => $foto,
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        // Pastikan foto tersimpan di database model Guru (Data PTK)
        $this->guruA->refresh();
        $this->assertNotNull($this->guruA->foto);
        $this->assertStringStartsWith('foto_guru/', $this->guruA->foto);
        Storage::disk('public')->assertExists($this->guruA->foto);

        // Pastikan accessor foto_url menghasilkan URL storage yang valid
        $this->assertStringContainsString('storage/' . $this->guruA->foto, $this->guruA->foto_url);

        // Pastikan foto ter-render di halaman profil mandiri PTK
        $resPage = $this->actingAs($this->userGuruA)->get(route('ptk.profil-saya'));
        $resPage->assertSee(e($this->guruA->foto_url), false);
    }

    public function test_guru_dapat_memperbarui_biodata_mandiri_dan_sinkron_ke_data_ptk()
    {
        $payload = [
            'nama_lengkap'         => 'Budi Santoso',
            'gelar_depan'          => 'Dr.',
            'gelar_belakang'       => 'M.Pd.',
            'nip'                  => '198501012010011005',
            'nik'                  => '3201010101850001',
            'nuptk'                => '1234567890123456',
            'tempat_lahir'         => 'Bandar Lampung',
            'tanggal_lahir'        => '1985-01-01',
            'jenis_kelamin'        => 'L',
            'agama'                => 'Islam',
            'no_hp'                => '081234567890',
            'alamat'               => 'Jl. Pendidikan No. 10 Air Naningan',
            'jenis_kepegawaian'    => 'pns',
            'golongan_pangkat'     => 'Pembina',
            'golongan_ruang'       => 'IV/a',
            'jabatan'              => 'Guru Ahli Madya',
            'tugas_tambahan'       => 'Waka Kurikulum',
            'tmt_kerja'            => '2010-01-01',
            'tmt_pangkat_terakhir' => '2022-04-01',
            'tmt_kgb_terakhir'     => '2024-04-01',
            'pendidikan_terakhir'  => 'S2',
            'jurusan_kuliah'       => 'Manajemen Pendidikan',
            'kampus'               => 'Universitas Negeri Yogyakarta',
            'tahun_lulus'          => '2012',
            'status_sertifikasi'   => 'sudah',
            'nomor_serdik'         => '1234567890',
            'mapel_diampu'         => 'Fisika, Matematika',
        ];

        $res = $this->actingAs($this->userGuruA)->post(route('ptk.update-biodata'), $payload);

        $res->assertRedirect();
        $res->assertSessionHas('success');

        $this->guruA->refresh();
        $this->assertEquals('Dr. Budi Santoso, M.Pd.', $this->guruA->nama);
        $this->assertEquals('Budi Santoso', $this->guruA->nama_lengkap);
        $this->assertEquals('3201010101850001', $this->guruA->nik);
        $this->assertEquals('Bandar Lampung', $this->guruA->tempat_lahir);
        $this->assertEquals('081234567890', $this->guruA->no_hp);
        $this->assertEquals('Pembina', $this->guruA->golongan_pangkat);
        $this->assertEquals('Pembina', $this->guruA->pangkat);
        $this->assertEquals('IV/a', $this->guruA->golongan_ruang);
        $this->assertEquals('Guru Ahli Madya', $this->guruA->jabatan);
        $this->assertEquals('Waka Kurikulum', $this->guruA->tugas_tambahan);
        $this->assertEquals('2024-04-01', $this->guruA->tmt_kgb_terakhir);
        $this->assertEquals('2026-04-01', $this->guruA->tmt_kgb_berikutnya);

        // Pastikan tampilan halaman profil merefleksikan perubahan
        $resPage = $this->actingAs($this->userGuruA)->get(route('ptk.profil-saya'));
        $resPage->assertSee('Dr. Budi Santoso, M.Pd.');
        $resPage->assertSee('IV/a');
        $resPage->assertSee('081234567890');
    }
}


