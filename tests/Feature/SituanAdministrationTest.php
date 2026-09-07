<?php

namespace Tests\Feature;

use App\Models\ArsipDokumenPtk;
use App\Models\BukuSkKepsek;
use App\Models\DisposisiSurat;
use App\Models\Guru;
use App\Models\KlasifikasiSurat;
use App\Models\PelayananSurat;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SituanAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kepsek;
    protected Guru $guru;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::create([
            'nama_sekolah'         => 'SMK Negeri 1 Air Naningan',
            'npsn'                 => '69900000',
            'nama_kepala_sekolah'  => 'Drs. H. PENDIDIKAN, M.Pd.',
            'nip_kepala_sekolah'   => '19750101 200003 1 002',
            'alamat_lengkap'       => 'Jl. Raya Air Naningan No. 01',
        ]);

        $this->admin = User::create([
            'name'     => 'Admin TU',
            'email'    => 'admin@smkn1airnaningan.sch.id',
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);

        $this->kepsek = User::create([
            'name'     => 'Kepala Sekolah',
            'email'    => 'kepsek@smkn1airnaningan.sch.id',
            'role'     => 'kepala_sekolah',
            'password' => Hash::make('password'),
        ]);

        $this->guru = Guru::create([
            'nama'                 => 'Budi Santoso, S.Pd.',
            'nip'                  => '19850510 201001 1 015',
            'jenis_ptk'            => 'Guru Mata Pelajaran',
            'status_kepegawaian'   => 'PNS',
            'golongan_ruang'       => 'III/b',
            'tmt_kgb_terakhir'     => Carbon::today()->subYears(2)->subDays(10)->format('Y-m-d'), // Jatuh tempo
            'tmt_pangkat_terakhir' => Carbon::today()->subYears(3)->format('Y-m-d'),
            'status'               => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'nama'          => 'Ahmad Fauzi',
            'nisn'          => '0061234567',
            'nis'           => '20241001',
            'tempat_lahir'  => 'Tanggamus',
            'tanggal_lahir' => '2008-04-12',
            'jenis_kelamin' => 'L',
            'status'        => 'aktif',
        ]);

        // Seed klasifikasi surat
        KlasifikasiSurat::firstOrCreate(['kode' => '421.3'], ['nama' => 'Pendidikan Menengah Kejuruan (SMK)', 'kategori' => 'Kurikulum & Kelembagaan']);
        KlasifikasiSurat::firstOrCreate(['kode' => '422.4'], ['nama' => 'Surat Keterangan Siswa & Mutasi', 'kategori' => 'Kesiswaan']);
        KlasifikasiSurat::firstOrCreate(['kode' => '821.2'], ['nama' => 'Kenaikan Gaji Berkala (KGB)', 'kategori' => 'Kepegawaian']);
    }

    public function test_admin_dapat_mencatat_surat_masuk_dan_nomor_agenda_otomatis(): void
    {
        $response = $this->actingAs($this->admin)->post(route('situan.surat-masuk.store'), [
            'nomor_surat_asal' => '005/123/DISDIK/2026',
            'pengirim'         => 'Dinas Pendidikan Provinsi Lampung',
            'tanggal_surat'    => date('Y-m-d'),
            'tanggal_diterima' => date('Y-m-d'),
            'perihal'          => 'Undangan Rapat Koordinasi Kepala SMK',
            'tingkat_urgensi'  => 'segera',
        ]);

        $response->assertRedirect(route('situan.surat-masuk.index'));

        $this->assertDatabaseHas('surat_masuks', [
            'nomor_agenda'     => 1,
            'nomor_surat_asal' => '005/123/DISDIK/2026',
            'status_disposisi' => 'menunggu',
        ]);
    }

    public function test_kepala_sekolah_dapat_memberikan_disposisi_digital_dan_mencetak_lembar_a5(): void
    {
        $suratMasuk = SuratMasuk::create([
            'nomor_agenda'     => 1,
            'tahun_agenda'     => (int) date('Y'),
            'nomor_surat_asal' => '005/999/DISDIK/2026',
            'pengirim'         => 'Cabang Dinas Pendidikan Wilayah II',
            'tanggal_surat'    => date('Y-m-d'),
            'tanggal_diterima' => date('Y-m-d'),
            'perihal'          => 'Verifikasi Bantuan Operasional',
            'tingkat_urgensi'  => 'segera',
            'status_disposisi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->kepsek)->post(route('situan.surat-masuk.disposisi', $suratMasuk->id), [
            'penerima_user_id' => $this->admin->id,
            'instruksi_flags'  => ['tindak_lanjuti', 'siapkan_laporan'],
            'catatan_kepsek'   => 'Tolong siapkan SPJ dan data rombel untuk diverifikasi besok.',
            'batas_waktu'      => date('Y-m-d', strtotime('+3 days')),
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('disposisi_surats', [
            'surat_masuk_id'   => $suratMasuk->id,
            'pemberi_user_id'  => $this->kepsek->id,
            'penerima_user_id' => $this->admin->id,
            'catatan_kepsek'   => 'Tolong siapkan SPJ dan data rombel untuk diverifikasi besok.',
        ]);

        $this->assertDatabaseHas('surat_masuks', [
            'id'               => $suratMasuk->id,
            'status_disposisi' => 'didisposisi',
        ]);

        // Cek cetak lembar disposisi A5
        $cetak = $this->actingAs($this->admin)->get(route('situan.surat-masuk.cetak-disposisi', $suratMasuk->id));
        $cetak->assertOk();
        $cetak->assertSee('LEMBAR DISPOSISI KEPALA SEKOLAH');
        $cetak->assertSee('Tolong siapkan SPJ dan data rombel');
    }

    public function test_surat_keluar_menghasilkan_nomor_baku_kemendikdasmen_otomatis(): void
    {
        $response = $this->actingAs($this->admin)->post(route('situan.surat-keluar.store'), [
            'kode_klasifikasi' => '421.3',
            'tujuan_surat'     => 'Dinas Tenaga Kerja dan Transmigrasi',
            'perihal'          => 'Permohonan Kerjasama Sertifikasi Kompetensi Siswa',
            'tanggal_surat'    => date('Y-m-d'),
            'penandatangan'    => 'Kepala Sekolah',
            'jenis_surat'      => 'umum',
        ]);

        $response->assertRedirect(route('situan.surat-keluar.index'));

        $thisYear = (int) date('Y');
        $romawi = SuratKeluar::romawiBulan((int) date('n'));
        $expectedNomor = "001/421.3/SMKN1AN/{$romawi}/{$thisYear}";

        $this->assertDatabaseHas('surat_keluars', [
            'nomor_agenda'        => 1,
            'tahun_agenda'        => $thisYear,
            'kode_klasifikasi'    => '421.3',
            'nomor_surat_lengkap' => $expectedNomor,
        ]);
    }

    public function test_loket_surat_siswa_menerbitkan_suket_aktif_lengkap_dengan_qr_code(): void
    {
        $response = $this->actingAs($this->admin)->post(route('situan.pelayanan.buat'), [
            'siswa_id'        => $this->siswa->id,
            'jenis_pelayanan' => 'suket_aktif',
            'keperluan'       => 'Persyaratan Pengajuan Beasiswa PIP Kemendikdasmen',
            'tanggal_surat'   => date('Y-m-d'),
        ]);

        $pelayanan = PelayananSurat::where('siswa_id', $this->siswa->id)->first();
        $this->assertNotNull($pelayanan);
        $this->assertNotNull($pelayanan->kode_verifikasi_qr);

        $response->assertRedirect(route('situan.pelayanan.cetak', $pelayanan->id));

        // Cek halaman cetak resmi
        $cetak = $this->actingAs($this->admin)->get(route('situan.pelayanan.cetak', $pelayanan->id));
        $cetak->assertOk();
        $cetak->assertSee('SURAT KETERANGAN SISWA AKTIF');
        $cetak->assertSee($this->siswa->nama);
        $cetak->assertSee('Persyaratan Pengajuan Beasiswa PIP Kemendikdasmen');

        // Cek verifikasi publik via QR hash (dapat diakses tanpa login)
        $publicVerify = $this->get(route('situan.verifikasi-surat', $pelayanan->kode_verifikasi_qr));
        $publicVerify->assertOk();
        $publicVerify->assertSee('DOKUMEN RESMI TERVERIFIKASI');
        $publicVerify->assertSee($this->siswa->nama);
        $publicVerify->assertSee('0061234567');
    }

    public function test_verifikasi_surat_dengan_hash_palsu_menampilkan_peringatan_tidak_valid(): void
    {
        $fakeHash = 'random_hash_yang_tidak_ada_di_database_123456';
        $response = $this->get(route('situan.verifikasi-surat', $fakeHash));
        $response->assertOk();
        $response->assertSee('DOKUMEN TIDAK VALID / TIDAK DITEMUKAN');
    }

    public function test_radar_kgb_mendeteksi_status_jatuh_tempo_dan_cetak_pengantar_resmi(): void
    {
        $response = $this->actingAs($this->admin)->get(route('situan.radar-kgb.index'));
        $response->assertOk();
        $response->assertSee($this->guru->nama);
        $response->assertSee('Pengusulan KGB'); // Label status merah jatuh tempo

        // Cetak pengantar KGB ke Disdik
        $cetakPengantar = $this->actingAs($this->admin)->get(route('situan.radar-kgb.cetak-pengantar', $this->guru->id));
        $cetakPengantar->assertOk();
        $cetakPengantar->assertSee('Usul Kenaikan Gaji Berkala (KGB)');
        $cetakPengantar->assertSee($this->guru->nama);
        $cetakPengantar->assertSee('19850510 201001 1 015');
    }

    public function test_lemari_arsip_digital_ptk_dapat_mengunggah_berkas(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('sk_kgb_terakhir.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('situan.arsip-ptk.store', $this->guru->id), [
            'kategori_berkas' => 'sk_kgb_terakhir',
            'nama_dokumen'    => 'SK Kenaikan Gaji Berkala 2024',
            'nomor_dokumen'   => '821.2/045/V.01/2024',
            'tanggal_dokumen' => '2024-05-10',
            'file_dokumen'    => $file,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('arsip_dokumen_ptks', [
            'guru_id'         => $this->guru->id,
            'kategori_berkas' => 'sk_kgb_terakhir',
            'nama_dokumen'    => 'SK Kenaikan Gaji Berkala 2024',
        ]);
    }

    public function test_buku_sk_kepsek_dapat_mendaftarkan_sk_baru(): void
    {
        $response = $this->actingAs($this->admin)->post(route('situan.buku-sk.store'), [
            'tentang_sk'         => 'Pembagian Tugas Mengajar Semester Ganjil TA 2026/2027',
            'tanggal_ditetapkan' => date('Y-m-d'),
            'kategori_sk'        => 'Pembagian Tugas PBM',
        ]);

        $response->assertRedirect(route('situan.buku-sk.index'));

        $this->assertDatabaseHas('buku_sk_kepseks', [
            'nomor_urut_sk' => 1,
            'kategori_sk'   => 'Pembagian Tugas PBM',
            'tentang_sk'    => 'Pembagian Tugas Mengajar Semester Ganjil TA 2026/2027',
        ]);
    }
}
