<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinDokumen;
use App\Models\KasusDisiplinLog;
use App\Models\Jurusan;
use App\Models\PengaturanSekolah;
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

class KasusDisiplinTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $guruBk;
    protected User $waliKelas;
    protected Siswa $siswa;
    protected Rombel $rombel;
    protected TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);

        $guru = Guru::create(['nip' => '198501012010011001', 'nama' => 'Budi Santoso, S.Pd', 'jabatan' => 'Wali Kelas']);
        $this->rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $this->ta->id,
            'wali_kelas_id'   => $guru->id,
        ]);

        $this->siswa = Siswa::create(['nis' => '9001', 'nama' => 'Siswa Pelanggar Disiplin', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id'           => $this->siswa->id,
            'rombel_id'          => $this->rombel->id,
            'tahun_ajaran_id'    => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $this->admin = User::create([
            'name'     => 'Admin Utama',
            'email'    => 'admin@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $guruBkModel = Guru::create(['nip' => '198602022010022002', 'nama' => 'Dewi Lestari, S.Psi', 'jabatan' => 'Guru BK']);
        $this->guruBk = User::create([
            'name'     => 'Dewi BK',
            'email'    => 'bk@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'guru_id'  => $guruBkModel->id,
            'role'     => 'guru_bk',
        ]);

        $this->waliKelas = User::create([
            'name'     => 'Budi Wali',
            'email'    => 'wali@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'guru_id'  => $guru->id,
            'role'     => 'wali_kelas',
        ]);
    }

    public function test_admin_dan_guru_bisa_mengakses_halaman_buku_kasus_disiplin(): void
    {
        $response = $this->actingAs($this->admin)->get('/disiplin');
        $response->assertOk();
        $response->assertSee('Buku Kasus & Penegakan Disiplin Siswa');

        $resBk = $this->actingAs($this->guruBk)->get('/disiplin');
        $resBk->assertOk();

        $resWali = $this->actingAs($this->waliKelas)->get('/disiplin');
        $resWali->assertOk();
    }

    public function test_tambah_kasus_disiplin_manual(): void
    {
        $res = $this->actingAs($this->admin)->post('/disiplin', [
            'siswa_id'     => $this->siswa->id,
            'status_tahap' => 'tahap_1_wali_kelas',
            'catatan'      => 'Siswa sering terlambat lebih dari 30 menit.',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('kasus_disiplins', [
            'siswa_id'     => $this->siswa->id,
            'status_tahap' => 'tahap_1_wali_kelas',
            'catatan_wali_kelas' => 'Siswa sering terlambat lebih dari 30 menit.',
        ]);
    }

    public function test_show_dossier_kasus_siswa(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
        ]);

        $res = $this->actingAs($this->guruBk)->get("/disiplin/{$kasus->id}");
        $res->assertOk();
        $res->assertSee($this->siswa->nama);
        $res->assertSee('Timeline Kronologis');
        $res->assertSee('Brankas Bukti Digital');
    }

    public function test_tambah_log_timeline_kronologis(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
        ]);

        $res = $this->actingAs($this->guruBk)->post("/disiplin/{$kasus->id}/log", [
            'judul_kegiatan'   => 'Kunjungan Rumah / Home Visit',
            'uraian_tindakan'  => 'Melakukan kunjungan bersama wali kelas ke kediaman siswa.',
            'tahap'            => 'home_visit',
            'poin_perubahan'   => 15,
            'tanggal_kegiatan' => '2026-08-15',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('kasus_disiplin_logs', [
            'kasus_disiplin_id' => $kasus->id,
            'judul_kegiatan'    => 'Kunjungan Rumah / Home Visit',
            'poin_perubahan'    => 15,
        ]);
    }

    public function test_upload_dan_hapus_dokumen_bukti_fisik(): void
    {
        Storage::fake('public');

        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
        ]);

        $file = UploadedFile::fake()->create('surat_sp2.pdf', 150, 'application/pdf');

        $res = $this->actingAs($this->guruBk)->post("/disiplin/{$kasus->id}/upload", [
            'judul_dokumen' => 'Scan Surat Pernyataan SP 2',
            'kategori'      => 'surat_pernyataan',
            'file'          => $file,
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('kasus_disiplin_dokumens', [
            'kasus_disiplin_id' => $kasus->id,
            'judul_dokumen'     => 'Scan Surat Pernyataan SP 2',
        ]);

        $dokumen = KasusDisiplinDokumen::where('kasus_disiplin_id', $kasus->id)->first();
        $this->assertNotNull($dokumen);

        // Hapus Dokumen
        $resDel = $this->actingAs($this->guruBk)->delete("/disiplin/{$kasus->id}/dokumen/{$dokumen->id}");
        $resDel->assertRedirect();
        $this->assertDatabaseMissing('kasus_disiplin_dokumens', ['id' => $dokumen->id]);
    }

    public function test_alur_eskalasi_tindak_lanjut_kasus_berjenjang(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'total_alpha'     => 3,
            'status_tahap'    => 'tahap_1_wali_kelas',
            'catatan_wali_kelas' => 'Sudah diberi teguran lisan oleh wali kelas.',
        ]);

        // Eskalasi dari Tahap 1 Wali Kelas -> Tahap 2 Guru BK oleh Wali Kelas
        $res = $this->actingAs($this->waliKelas)->post("/disiplin/{$kasus->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_2_bk',
            'catatan_tindakan'  => 'Pemanggilan orang tua ke ruang BK telah dilakukan.',
            'hasil_musyawarah'  => 'Orang tua bersedia mendampingi ananda.',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('kasus_disiplins', [
            'id'                  => $kasus->id,
            'status_tahap'        => 'tahap_2_bk',
            'catatan_bk'          => 'Pemanggilan orang tua ke ruang BK telah dilakukan.',
            'hasil_musyawarah_bk' => 'Orang tua bersedia mendampingi ananda.',
        ]);

        // Eskalasi dari Tahap 2 BK -> Tahap 3 Wakasis oleh Admin
        $res3 = $this->actingAs($this->admin)->post("/disiplin/{$kasus->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_3_wakasis',
            'catatan_tindakan'  => 'Sidang kesiswaan memutuskan sanksi SP 3.',
            'sanksi_tambahan'   => 'Surat Peringatan 3 & Pembinaan Khusus',
        ]);

        $res3->assertRedirect();
        $this->assertDatabaseHas('kasus_disiplins', [
            'id'             => $kasus->id,
            'status_tahap'   => 'tahap_3_wakasis',
            'sanksi_wakasis' => 'Surat Peringatan 3 & Pembinaan Khusus',
        ]);
    }

    public function test_selesaikan_pembinaan_kasus_disiplin(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'total_alpha'     => 1,
            'status_tahap'    => 'tahap_1_wali_kelas',
        ]);

        $res = $this->actingAs($this->waliKelas)->post("/disiplin/{$kasus->id}/selesaikan");
        $res->assertRedirect();

        $this->assertDatabaseHas('kasus_disiplins', [
            'id'           => $kasus->id,
            'status_tahap' => 'selesai_pembinaan',
        ]);
    }

    public function test_cetak_resume_yuridis_kepsek(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_3_wakasis',
        ]);

        $res = $this->actingAs($this->admin)->get("/disiplin/{$kasus->id}/resume-cetak");
        $res->assertOk();
        $res->assertSee('RESUME REKAM JEJAK & PEMBINAAN KEDISIPLINAN SISWA');
        $res->assertSee($this->siswa->nama);
    }

    public function test_sinkronisasi_otomatis_dari_presensi(): void
    {
        // Buat 3 catatan absensi alpha untuk siswa
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => '2026-08-01',
            'status'       => 'alpha',
        ]);
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => '2026-08-02',
            'status'       => 'alpha',
        ]);
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => '2026-08-03',
            'status'       => 'alpha',
        ]);

        $kasus = KasusDisiplin::syncFromPresensi($this->siswa->id);

        $this->assertEquals(3, $kasus->total_alpha);
        $this->assertEquals('tahap_2_bk', $kasus->status_tahap);
    }

    public function test_wali_kelas_ditolak_akses_kasus_siswa_kelas_lain(): void
    {
        // Buat rombel dan siswa lain
        $guruLain = Guru::create(['nip' => '198901012015011002', 'nama' => 'Guru Lain, S.Pd', 'jabatan' => 'Guru']);
        $rombelLain = Rombel::create([
            'nama_rombel'     => 'XI TKJ 2',
            'tingkat'         => 11,
            'jurusan_id'      => $this->rombel->jurusan_id,
            'tahun_ajaran_id' => $this->ta->id,
            'wali_kelas_id'   => $guruLain->id,
        ]);
        $siswaLain = Siswa::create(['nis' => '9999', 'nama' => 'Siswa Kelas Lain', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id'           => $siswaLain->id,
            'rombel_id'          => $rombelLain->id,
            'tahun_ajaran_id'    => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $kasusLain = KasusDisiplin::create([
            'siswa_id'        => $siswaLain->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_1_wali_kelas',
        ]);

        // Wali kelas mencoba akses kasus siswa kelas lain -> harus 403 Ditolak
        $res = $this->actingAs($this->waliKelas)->get("/disiplin/{$kasusLain->id}");
        $res->assertForbidden();

        // Wali kelas mencoba tindak lanjut kasus siswa kelas lain -> harus 403 Ditolak
        $resTindak = $this->actingAs($this->waliKelas)->post("/disiplin/{$kasusLain->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_2_bk',
            'catatan_tindakan'  => 'Ilegal action',
        ]);
        $resTindak->assertForbidden();
    }

    public function test_eksklusivitas_tahap_per_role(): void
    {
        // 1. Kasus di Tahap 1 (Wali Kelas) -> Guru BK ditolak memproses
        $kasus1 = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_1_wali_kelas',
        ]);
        $resBkOnTahap1 = $this->actingAs($this->guruBk)->post("/disiplin/{$kasus1->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_2_bk',
            'catatan_tindakan'  => 'BK coba tindak tahap 1',
        ]);
        $resBkOnTahap1->assertForbidden();

        // 2. Kasus di Tahap 2 (Guru BK) -> Wali Kelas ditolak memproses
        $kasus2 = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
        ]);
        $resWaliOnTahap2 = $this->actingAs($this->waliKelas)->post("/disiplin/{$kasus2->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_3_wakasis',
            'catatan_tindakan'  => 'Wali kelas coba tindak tahap 2',
        ]);
        $resWaliOnTahap2->assertForbidden();

        // Guru BK berhasil memproses Tahap 2 dan eskalasi ke Tahap 3
        $resBkOnTahap2 = $this->actingAs($this->guruBk)->post("/disiplin/{$kasus2->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_3_wakasis',
            'catatan_tindakan'  => 'BK eskalasi ke kesiswaan',
        ]);
        $resBkOnTahap2->assertRedirect();
    }
}

