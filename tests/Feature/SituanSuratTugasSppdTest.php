<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\KlasifikasiSurat;
use App\Models\PengaturanSekolah;
use App\Models\Sppd;
use App\Models\SuratKeluar;
use App\Models\SuratTugas;
use App\Models\SuratTugasAnggota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SituanSuratTugasSppdTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Guru $guru1;
    protected Guru $guru2;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::create([
            'nama_sekolah'        => 'SMK Negeri 1 Air Naningan',
            'npsn'                => '69900000',
            'nama_kepala_sekolah' => 'Aprida, S.Si.',
            'nip_kepala_sekolah'  => '197904172008012019',
            'alamat_lengkap'      => 'Jl. Raya Air Naningan No. 01',
        ]);

        KlasifikasiSurat::create([
            'kode'      => '094',
            'nama'      => 'Perjalanan Dinas',
            'kategori'  => 'Kepegawaian',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name'     => 'Staff TU',
            'email'    => 'tu@smkn1airnaningan.sch.id',
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->guru1 = Guru::create([
            'nama'              => 'Ahmad Ridwan, S.Kom.',
            'nip'               => '198701012011011001',
            'jabatan'           => 'Guru Kejuruan RPL',
            'golongan_pangkat'  => 'Penata',
            'golongan_ruang'    => 'III/c',
            'status'            => 'aktif',
        ]);

        $this->guru2 = Guru::create([
            'nama'              => 'Siti Nurhaliza, M.Pd.',
            'nip'               => '199202022019032002',
            'jabatan'           => 'Guru Bahasa Inggris',
            'golongan_pangkat'  => 'Penata Muda',
            'golongan_ruang'    => 'III/a',
            'status'            => 'aktif',
        ]);
    }

    public function test_halaman_index_surat_tugas_dapat_diakses(): void
    {
        $response = $this->actingAs($this->admin)->get(route('situan.surat-tugas.index'));
        $response->assertStatus(200);
        $response->assertSee('Surat Tugas &amp; SPPD Terpadu', false);
        $response->assertSee('Ahmad Ridwan');
    }

    public function test_dapat_menerbitkan_surat_tugas_dan_sppd_multi_personil(): void
    {
        $payload = [
            'maksud_tugas'      => 'Monitoring dan Evaluasi (Monev) Siswa PKL di PT Telkom Bandar Lampung',
            'dasar_penugasan'   => 'Surat Tugas PKL SMK Negeri 1 Air Naningan Tahun 2026',
            'tempat_berangkat'  => 'Air Naningan, Tanggamus',
            'tempat_tujuan'     => 'Bandar Lampung',
            'lokasi_spesifik'   => 'Kantor Witel Lampung',
            'tanggal_mulai'     => '2026-09-20',
            'tanggal_selesai'   => '2026-09-21',
            'alat_transportasi' => 'Kendaraan Dinas',
            'sumber_anggaran'   => 'BOS Reguler SMK Negeri 1 Air Naningan',
            'terbitkan_sppd'    => '1',
            'tingkat_biaya'     => 'Tingkat C',
            'kode_klasifikasi'  => '094',
            'anggotas'          => [
                [
                    'guru_id'          => $this->guru1->id,
                    'nama'             => $this->guru1->nama,
                    'nip'              => $this->guru1->nip,
                    'pangkat_golongan' => 'Penata (III/c)',
                    'jabatan'          => $this->guru1->jabatan,
                    'peran'            => 'Ketua Rombongan',
                ],
                [
                    'guru_id'          => $this->guru2->id,
                    'nama'             => $this->guru2->nama,
                    'nip'              => $this->guru2->nip,
                    'pangkat_golongan' => 'Penata Muda (III/a)',
                    'jabatan'          => $this->guru2->jabatan,
                    'peran'            => 'Anggota',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('situan.surat-tugas.store'), $payload);
        $response->assertRedirect(route('situan.surat-tugas.index'));
        $response->assertSessionHas('success');

        // Pastikan tabel surat_tugas terisi
        $this->assertDatabaseHas('surat_tugas', [
            'tempat_tujuan' => 'Bandar Lampung',
            'lama_hari'     => 2,
        ]);

        $st = SuratTugas::first();
        $this->assertNotNull($st);
        $this->assertEquals(2, $st->anggotas()->count());
        $this->assertEquals(2, $st->sppds()->count());

        // Pastikan nomor agenda surat keluar terbit otomatis
        $this->assertDatabaseHas('surat_keluars', [
            'id'                  => $st->surat_keluar_id,
            'kode_klasifikasi'    => '094',
            'nomor_surat_lengkap' => $st->nomor_surat_tugas,
            'jenis_surat'         => 'surat_tugas',
        ]);

        // Pastikan SPPD memiliki nomor urut dan detail sesuai
        $sppd = Sppd::where('surat_tugas_id', $st->id)->first();
        $this->assertNotNull($sppd);
        $this->assertStringContainsString('094/SPPD.', $sppd->nomor_sppd);
    }

    public function test_cetak_surat_tugas_dan_sppd(): void
    {
        $st = SuratTugas::create([
            'nomor_surat_tugas'     => '001/094/SMKN1AN/IX/2026',
            'kode_klasifikasi'      => '094',
            'dasar_penugasan'       => 'Program Kerja',
            'maksud_tugas'          => 'Rapat Koordinasi MKKS SMK Se-Lampung',
            'tempat_berangkat'      => 'Air Naningan',
            'tempat_tujuan'         => 'Bandar Lampung',
            'tanggal_mulai'         => '2026-09-20',
            'tanggal_selesai'       => '2026-09-20',
            'lama_hari'             => 1,
            'pejabat_penandatangan' => 'Kepala Sekolah',
            'status'                => 'disetujui',
        ]);

        $anggota = SuratTugasAnggota::create([
            'surat_tugas_id'   => $st->id,
            'guru_id'          => $this->guru1->id,
            'nama'             => $this->guru1->nama,
            'nip'              => $this->guru1->nip,
            'pangkat_golongan' => 'Penata (III/c)',
            'jabatan'          => $this->guru1->jabatan,
            'peran'            => 'Ketua Rombongan',
        ]);

        $sppd = Sppd::create([
            'surat_tugas_id'             => $st->id,
            'surat_tugas_anggota_id'     => $anggota->id,
            'guru_id'                    => $this->guru1->id,
            'nomor_sppd'                 => '094/SPPD.001/SMKN1AN/IX/2026',
            'nama_pelaksana'             => $this->guru1->nama,
            'nip_pelaksana'              => $this->guru1->nip,
            'pangkat_golongan'           => 'Penata (III/c)',
            'jabatan'                    => $this->guru1->jabatan,
            'tingkat_biaya'              => 'Tingkat C',
            'maksud_perjalanan'          => $st->maksud_tugas,
            'tempat_berangkat'           => 'Air Naningan',
            'tempat_tujuan'              => 'Bandar Lampung',
            'lama_perjalanan'            => 1,
            'tanggal_berangkat'          => '2026-09-20',
            'tanggal_harus_kembali'      => '2026-09-20',
            'instansi_pembeban_anggaran' => 'SMK Negeri 1 Air Naningan',
        ]);

        // 1. Cetak Surat Tugas
        $respTugas = $this->actingAs($this->admin)->get(route('situan.surat-tugas.cetak-surat', $st->id));
        $respTugas->assertStatus(200);
        $respTugas->assertSee('SURAT PERINTAH TUGAS');
        $respTugas->assertSee($this->guru1->nama);

        // 2. Cetak SPPD Lembar I & II Visum
        $respSppd = $this->actingAs($this->admin)->get(route('situan.surat-tugas.cetak-sppd', [$st->id, $sppd->id]));
        $respSppd->assertStatus(200);
        $respSppd->assertSee('SURAT PERINTAH PERJALANAN DINAS');
        $respSppd->assertSee('LEMBAR VISUM SPPD (LEMBAR II)');

        // 3. Cetak Paket 3-in-1
        $respPaket = $this->actingAs($this->admin)->get(route('situan.surat-tugas.cetak-paket', $st->id));
        $respPaket->assertStatus(200);
        $respPaket->assertSee('SURAT PERINTAH TUGAS');
        $respPaket->assertSee('LEMBAR VISUM SPPD (LEMBAR II)');
    }

    public function test_verifikasi_publik_surat_tugas_via_qr_hash(): void
    {
        $st = SuratTugas::create([
            'nomor_surat_tugas'     => '002/094/SMKN1AN/IX/2026',
            'kode_klasifikasi'      => '094',
            'maksud_tugas'          => 'Bimtek Kurikulum Vokasi SMK di Jakarta',
            'tempat_berangkat'      => 'Air Naningan',
            'tempat_tujuan'         => 'Jakarta',
            'tanggal_mulai'         => '2026-09-25',
            'tanggal_selesai'       => '2026-09-27',
            'lama_hari'             => 3,
            'pejabat_penandatangan' => 'Kepala Sekolah',
            'status'                => 'disetujui',
        ]);

        $suratKeluar = SuratKeluar::create([
            'nomor_agenda'        => 2,
            'tahun_agenda'        => 2026,
            'kode_klasifikasi'    => '094',
            'nomor_surat_lengkap' => $st->nomor_surat_tugas,
            'tujuan_surat'        => 'Ahmad Ridwan',
            'perihal'             => 'Surat Tugas Bimtek Kurikulum Vokasi',
            'tanggal_surat'       => '2026-09-25',
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'surat_tugas',
            'sumber_modul'        => 'situan_tugas',
            'kategori_surat'      => 'Surat Tugas & SPPD',
        ]);

        $st->update(['surat_keluar_id' => $suratKeluar->id]);

        // Verifikasi publik tanpa login
        $response = $this->get(route('situan.verifikasi-surat', $st->kode_verifikasi_qr));
        $response->assertStatus(200);
        $response->assertSee('DOKUMEN RESMI TERVERIFIKASI');
        $response->assertSee('002/094/SMKN1AN/IX/2026');
    }
}
