<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\PpdbPendaftar;
use App\Models\PpdbUjianPeserta;
use App\Models\PpdbUjianSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbSeleksiCbtTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin PPDB',
            'email' => 'admin.ppdb@smkn1airnaningan.sch.id',
            'role' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kuota' => 2,
            'is_aktif' => true,
        ]);
    }

    protected function buatPendaftar($no, $nisn, $nama, $status = 'terverifikasi', $nilaiRapor = 85.00)
    {
        return PpdbPendaftar::create([
            'no_pendaftaran' => $no,
            'nisn' => $nisn,
            'nama_lengkap' => $nama,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Tanggamus',
            'tanggal_lahir' => '2010-01-01',
            'asal_sekolah' => 'SMPN 1 Air Naningan',
            'alamat' => 'Air Naningan RT 01',
            'no_hp_ortu' => '08123456789',
            'jurusan_id_1' => $this->jurusan->id,
            'status' => $status,
            'nilai_rata_rata' => $nilaiRapor,
        ]);
    }

    public function test_admin_bisa_mengakses_portal_seleksi_cbt()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ppdb.seleksi'));

        $response->assertStatus(200);
        $response->assertSee('Seleksi Terpadu Masuk Calon Siswa Baru');
        $response->assertSee('Kunci Jawaban Resmi');
    }

    public function test_admin_bisa_memperbarui_pengaturan_dan_kunci_jawaban_cbt()
    {
        $kunci = [];
        for ($i = 1; $i <= 30; $i++) {
            $kunci[(string)$i] = ($i % 2 === 0) ? 'B' : 'A';
        }

        $payload = [
            'judul_ujian' => 'Tes Potensi Akademik & Minat Bakat PPDB 2026',
            'durasi_menit' => 90,
            'is_active' => 1,
            'bobot_pg' => 70,
            'bobot_esai' => 30,
            'petunjuk' => 'Kerjakan dengan teliti dan jujur.',
            'kunci_jawaban_pg' => $kunci,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.ppdb.seleksi.setting'), $payload);

        $response->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'pengaturan']));
        $this->assertDatabaseHas('ppdb_ujian_settings', [
            'judul_ujian' => 'Tes Potensi Akademik & Minat Bakat PPDB 2026',
            'durasi_menit' => 90,
        ]);
    }

    public function test_admin_bisa_menjadwalkan_sesi_ujian_secara_massal()
    {
        $p1 = $this->buatPendaftar('PPDB-2026-TEST1', '1234567890', 'Calon Siswa Satu');
        $p2 = $this->buatPendaftar('PPDB-2026-TEST2', '1234567891', 'Calon Siswa Dua');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.ppdb.seleksi.jadwalkan'), [
                'pendaftar_ids' => [$p1->id, $p2->id],
                'jadwal_tes_tanggal' => '2026-06-15',
                'jadwal_tes_sesi' => 'Sesi 1 (08.00 - 09.30)',
                'jadwal_tes_ruang' => 'Lab Multimedia 1',
            ]);

        $response->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'penjadwalan']));
        $p1->refresh();
        $this->assertEquals('2026-06-15', \Carbon\Carbon::parse($p1->jadwal_tes_tanggal)->format('Y-m-d'));
        $this->assertEquals('Lab Multimedia 1', $p1->jadwal_tes_ruang);
    }

    public function test_calon_siswa_bisa_mengakses_halaman_ujian_dan_mengirim_jawaban()
    {
        // Siapkan Setting Ujian
        $kunci = [];
        for ($i = 1; $i <= 30; $i++) {
            $kunci[(string)$i] = 'A';
        }
        $setting = PpdbUjianSetting::firstOrCreate(['id' => 1], [
            'judul_ujian' => 'Ujian Seleksi PPDB 2026',
            'durasi_menit' => 60,
            'is_active' => true,
            'jumlah_soal_pg' => 30,
            'jumlah_soal_esai' => 5,
            'kunci_jawaban_pg' => $kunci,
            'bobot_pg' => 70,
            'bobot_esai' => 30,
        ]);

        $pendaftar = $this->buatPendaftar('PPDB-2026-UJI01', '9988776655', 'Peserta CBT Mandiri');

        // Akses Konfirmasi
        $resKonfirmasi = $this->get(route('ppdb.ujian.konfirmasi', $pendaftar->no_pendaftaran));
        $resKonfirmasi->assertStatus(200);
        $resKonfirmasi->assertSee('Portal Ujian Seleksi Masuk PPDB 2026');

        // Akses Kerjakan
        $resKerjakan = $this->get(route('ppdb.ujian.kerjakan', $pendaftar->no_pendaftaran));
        $resKerjakan->assertStatus(200);

        // Kirim Jawaban (Jawab 30 PG dengan 'A' sehingga skor PG = 100)
        $jawabanPg = [];
        for ($i = 1; $i <= 30; $i++) {
            $jawabanPg[(string)$i] = 'A';
        }
        $jawabanEsai = [
            '31' => 'Jawaban esai nomor 31 mengenai motivasi kejuruan.',
            '32' => 'Jawaban esai nomor 32.',
            '33' => 'Jawaban esai nomor 33.',
            '34' => 'Jawaban esai nomor 34.',
            '35' => 'Jawaban esai nomor 35.',
        ];

        $resSubmit = $this->post(route('ppdb.ujian.selesai', $pendaftar->no_pendaftaran), [
            'jawaban_pg' => $jawabanPg,
            'jawaban_esai' => $jawabanEsai,
        ]);

        $resSubmit->assertRedirect(route('ppdb.ujian.selesai', ['nomor' => $pendaftar->no_pendaftaran]));

        // Cek data peserta ujian di DB
        $pesertaUjian = PpdbUjianPeserta::where('ppdb_pendaftar_id', $pendaftar->id)->first();
        $this->assertNotNull($pesertaUjian);
        $this->assertEquals(30, $pesertaUjian->jumlah_pg_benar);
        $this->assertEquals(0, $pesertaUjian->jumlah_pg_salah);
        $this->assertEquals(70.00, $pesertaUjian->nilai_pg);
        $this->assertEquals('selesai_menunggu_koreksi', $pesertaUjian->status_pengerjaan);

        // Akses Halaman Tanda Terima Selesai
        $resSelesai = $this->get(route('ppdb.ujian.selesai_view', $pendaftar->no_pendaftaran));
        $resSelesai->assertStatus(200);
        $resSelesai->assertSee('LEMBAR JAWABAN RESMI DITERIMA');
    }

    public function test_admin_bisa_menilai_esai_dan_wawancara_kemudian_kalkulasi_kelulusan()
    {
        $setting = PpdbUjianSetting::firstOrCreate(['id' => 1], [
            'judul_ujian' => 'Ujian Seleksi PPDB 2026',
            'durasi_menit' => 60,
            'is_active' => true,
            'jumlah_soal_pg' => 30,
            'jumlah_soal_esai' => 5,
            'bobot_pg' => 70,
            'bobot_esai' => 30,
        ]);

        // Buat Pendaftar 1 (Nilai Rapor 90)
        $p1 = $this->buatPendaftar('PPDB-2026-SISWA1', '1111111111', 'Siswa Juara 1', 'terverifikasi', 90.00);

        $u1 = PpdbUjianPeserta::create([
            'ppdb_pendaftar_id' => $p1->id,
            'ppdb_ujian_setting_id' => $setting->id,
            'status_pengerjaan' => 'selesai_menunggu_koreksi',
            'jumlah_pg_benar' => 28,
            'jumlah_pg_salah' => 2,
            'nilai_pg' => 93.33,
            'jawaban_pg' => [],
            'jawaban_esai' => [
                '31' => 'Esai 1',
                '32' => 'Esai 2',
                '33' => 'Esai 3',
                '34' => 'Esai 4',
                '35' => 'Esai 5',
            ],
        ]);

        // Buat Pendaftar 2 (Nilai Rapor 75)
        $p2 = $this->buatPendaftar('PPDB-2026-SISWA2', '2222222222', 'Siswa Urutan 2', 'terverifikasi', 75.00);

        $u2 = PpdbUjianPeserta::create([
            'ppdb_pendaftar_id' => $p2->id,
            'ppdb_ujian_setting_id' => $setting->id,
            'status_pengerjaan' => 'selesai_menunggu_koreksi',
            'jumlah_pg_benar' => 20,
            'jumlah_pg_salah' => 10,
            'nilai_pg' => 66.67,
            'jawaban_pg' => [],
            'jawaban_esai' => [
                '31' => 'Esai 1',
                '32' => 'Esai 2',
                '33' => 'Esai 3',
                '34' => 'Esai 4',
                '35' => 'Esai 5',
            ],
        ]);

        // 1. Admin nilai esai p1
        $resEsai = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.nilai_esai', $p1->id), [
            'nilai_esai_31' => 10,
            'nilai_esai_32' => 10,
            'nilai_esai_33' => 9,
            'nilai_esai_34' => 9,
            'nilai_esai_35' => 10,
            'catatan_koreksi_esai' => 'Sangat memuaskan',
        ]);
        $resEsai->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'tertulis']));
        $u1->refresh();
        $this->assertEquals(48.00, $u1->nilai_esai);
        $this->assertEquals('selesai_dinilai', $u1->status_pengerjaan);

        // 2. Admin input wawancara p1
        $resWawancara = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.nilai_wawancara', $p1->id), [
            'nilai_wawancara_motivasi' => 90,
            'nilai_wawancara_karakter' => 90,
            'nilai_wawancara_kejuruan' => 95,
            'nilai_wawancara_ortu' => 90,
            'catatan_wawancara' => 'Sangat siap dan berbakat di bidang coding.',
        ]);
        $resWawancara->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'wawancara']));
        $p1->refresh();
        $this->assertGreaterThan(90, $p1->nilai_wawancara_total);

        // Berikan juga nilai wawancara untuk p2
        $p2->update([
            'nilai_tes_tertulis' => 60.00,
            'nilai_wawancara_motivasi' => 70,
            'nilai_wawancara_karakter' => 70,
            'nilai_wawancara_kejuruan' => 70,
            'nilai_wawancara_ortu' => 70,
            'nilai_wawancara_total' => 70.00,
        ]);

        // 3. Jalankan kalkulasi kelulusan
        $resKalkulasi = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.kalkulasi'));
        $resKalkulasi->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'leaderboard']));

        $p1->refresh();
        $p2->refresh();

        $this->assertNotNull($p1->nilai_akhir);
        $this->assertNotNull($p2->nilai_akhir);
        $this->assertGreaterThan($p2->nilai_akhir, $p1->nilai_akhir);

        // Peringkat jurusan
        $this->assertEquals(1, $p1->peringkat_jurusan);
        $this->assertEquals(2, $p2->peringkat_jurusan);
    }
}
