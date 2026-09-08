<?php

namespace Tests\Feature;

use App\Models\KlasifikasiSurat;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersuratanManualDanAdhocTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);

        KlasifikasiSurat::firstOrCreate(
            ['kode' => '421.3'],
            ['nama' => 'Sekolah Menengah Kejuruan', 'kategori' => 'Pendidikan', 'is_active' => true]
        );
        KlasifikasiSurat::firstOrCreate(
            ['kode' => '005'],
            ['nama' => 'Undangan Kedinasan', 'kategori' => 'Umum', 'is_active' => true]
        );
    }

    public function test_tu_dapat_menerbitkan_nomor_surat_dengan_penomoran_manual_kustom(): void
    {
        $response = $this->actingAs($this->admin)->post(route('situan.surat-keluar.store'), [
            'mode_penomoran'      => 'manual',
            'nomor_surat_manual'  => '015.A/421.3/SMKN1AN/IX/2026',
            'nomor_agenda_manual' => 15,
            'kode_klasifikasi'    => '421.3',
            'tujuan_surat'        => 'Kepala Dinas Pendidikan Provinsi Lampung',
            'perihal'             => 'Laporan Khusus Penyesuaian Anggaran',
            'tanggal_surat'       => '2026-09-09',
            'penandatangan'       => 'Aprida, S.Si.',
            'jenis_surat'         => 'umum',
        ]);

        $response->assertRedirect(route('situan.surat-keluar.index'));
        $this->assertDatabaseHas('surat_keluars', [
            'nomor_surat_lengkap' => '015.A/421.3/SMKN1AN/IX/2026',
            'nomor_agenda'        => 15,
            'is_nomor_manual'     => true,
            'perihal'             => 'Laporan Khusus Penyesuaian Anggaran',
        ]);
    }

    public function test_tu_dapat_membuat_surat_bebas_adhoc_dan_mencetak_lembar_a4_berkop_resmi(): void
    {
        $response = $this->actingAs($this->admin)->post(route('situan.surat-keluar.store'), [
            'mode_penomoran'         => 'otomatis',
            'kode_klasifikasi'       => '005',
            'tujuan_surat'           => 'Bapak/Ibu Pengurus Komite Sekolah',
            'perihal'                => 'Undangan Rapat Pleno Pengembangan SMK',
            'tanggal_surat'          => '2026-09-09',
            'penandatangan'          => 'Aprida, S.Si.',
            'jabatan_penandatangan'  => 'Kepala Sekolah',
            'sifat_surat'            => 'Penting',
            'lampiran'               => '1 (satu) Berkas',
            'isi_surat'              => 'Sehubungan dengan agenda kerja sama, kami mengundang Bapak/Ibu hadir pada musyawarah pleno.',
            'tembusan'               => '1. Arsip TU',
            'jenis_surat'            => 'umum',
        ]);

        $response->assertRedirect(route('situan.surat-keluar.index'));
        $surat = SuratKeluar::where('kode_klasifikasi', '005')->first();
        $this->assertNotNull($surat);
        $this->assertEquals('Surat Dinas Bebas', $surat->kategori_surat);
        $this->assertNotNull($surat->link_cetak);

        // Uji cetak dokumen A4
        $cetakResponse = $this->actingAs($this->admin)->get(route('situan.surat-keluar.cetak', $surat->id));
        $cetakResponse->assertOk();
        $cetakResponse->assertSee($surat->nomor_surat_lengkap);
        $cetakResponse->assertSee('Bapak/Ibu Pengurus Komite Sekolah');
        $cetakResponse->assertSee('Undangan Rapat Pleno Pengembangan SMK');
        $cetakResponse->assertSee('Sehubungan dengan agenda kerja sama');
        $cetakResponse->assertSee('1. Arsip TU');
    }

    public function test_tu_dapat_mengedit_dan_menghapus_surat_keluar(): void
    {
        $surat = SuratKeluar::create([
            'nomor_agenda'        => 1,
            'tahun_agenda'        => 2026,
            'kode_klasifikasi'    => '421.3',
            'nomor_surat_lengkap' => '001/421.3/SMKN1AN/IX/2026',
            'tujuan_surat'        => 'Target Awal',
            'perihal'             => 'Perihal Awal',
            'tanggal_surat'       => '2026-09-09',
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'umum',
        ]);

        // Edit
        $updateResponse = $this->actingAs($this->admin)->put(route('situan.surat-keluar.update', $surat->id), [
            'nomor_surat_lengkap' => '001.Revisi/421.3/SMKN1AN/IX/2026',
            'nomor_agenda'        => 1,
            'kode_klasifikasi'    => '421.3',
            'tujuan_surat'        => 'Target Baru Dikoreksi',
            'perihal'             => 'Perihal Setelah Revisi',
            'tanggal_surat'       => '2026-09-09',
            'penandatangan'       => 'Plt. Kepala Sekolah',
            'jenis_surat'         => 'umum',
        ]);

        $updateResponse->assertRedirect(route('situan.surat-keluar.index'));
        $surat->refresh();
        $this->assertEquals('001.Revisi/421.3/SMKN1AN/IX/2026', $surat->nomor_surat_lengkap);
        $this->assertEquals('Target Baru Dikoreksi', $surat->tujuan_surat);

        // Hapus
        $deleteResponse = $this->actingAs($this->admin)->delete(route('situan.surat-keluar.destroy', $surat->id));
        $deleteResponse->assertRedirect(route('situan.surat-keluar.index'));
        $this->assertDatabaseMissing('surat_keluars', ['id' => $surat->id]);
    }
}
