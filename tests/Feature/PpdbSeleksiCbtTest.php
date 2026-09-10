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

        // Simulasikan presensi barcode di lokasi ujian sebelum membuka soal
        \App\Models\PpdbAbsensiUjian::create([
            'ppdb_pendaftar_id' => $pendaftar->id,
            'no_pendaftaran'    => $pendaftar->no_pendaftaran,
            'jadwal_tanggal'    => \Carbon\Carbon::today()->toDateString(),
            'waktu_hadir'       => now(),
            'status_kehadiran'  => 'hadir',
            'metode_presensi'   => 'barcode_scanner',
        ]);

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
        $this->assertGreaterThan(0, $pesertaUjian->jumlah_pg_benar);
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

    public function test_admin_bisa_menetapkan_jadwal_juknis_serentak_1_gelombang()
    {
        $setting = PpdbUjianSetting::updateOrCreate(['id' => 1], [
            'judul_ujian'          => 'Ujian CBT PPDB 2026',
            'durasi_menit'         => 60,
            'is_active'            => true,
            'tanggal_pelaksanaan'  => '2026-09-15',
            'sesi_default'         => 'Sesi 1 (08.00 - 10.00 WIB)',
            'ruang_default'        => 'Lab Komputer SMKN 1',
            'gelombang_label'      => '1x Gelombang (Sesuai Juknis Resmi)',
        ]);

        $p1 = $this->buatPendaftar('PPDB-JUKNIS-01', '1122334455', 'Peserta Juknis 1', 'terverifikasi');
        $p2 = $this->buatPendaftar('PPDB-JUKNIS-02', '1122334456', 'Peserta Juknis 2', 'berkas_valid');

        $this->assertNull($p1->jadwal_tes_tanggal);
        $this->assertNull($p2->jadwal_tes_tanggal);

        $response = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.jadwalkan_serentak'));
        $response->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'penjadwalan']));
        $response->assertSessionHas('success');

        $p1->refresh();
        $p2->refresh();

        $this->assertEquals('2026-09-15', $p1->jadwal_tes_tanggal->toDateString());
        $this->assertEquals('08.00 - 10.00 WIB', $p1->jadwal_tes_sesi);
        $this->assertEquals('Lab Komputer SMKN 1', $p1->jadwal_tes_ruang);

        $this->assertEquals('2026-09-15', $p2->jadwal_tes_tanggal->toDateString());
    }

    public function test_kartu_cetak_menampilkan_jadwal_pasti_1x_gelombang_juknis()
    {
        $pendaftar = $this->buatPendaftar('PPDB-JUKNIS-03', '1122334457', 'Peserta Kartu Pasti', 'terverifikasi');
        $pendaftar->pastikanJadwalJuknis();

        $response = $this->get(route('ppdb.cetak', ['nomor' => $pendaftar->no_pendaftaran]));
        $response->assertStatus(200);
        $response->assertSee('1x Gelombang (Sesuai Juknis Resmi PPDB)');
        $response->assertDontSee('Sesuai Jadwal Gelombang Panitia PPDB');
        $response->assertSee('Waktu Ujian');
        $response->assertDontSee('Sesi Waktu');
    }

    public function test_admin_bisa_mengatur_jam_pelaksanaan_ujian_secara_fleksibel()
    {
        $payload = [
            'judul_ujian'          => 'Tes PPDB 2026',
            'durasi_menit'         => 60,
            'tanggal_pelaksanaan'  => '2026-09-20',
            'jam_mulai'            => '07:30',
            'jam_selesai'          => '09:30',
            'ruang_default'        => 'Lab Komputer Utama',
            'bobot_pg'             => 70,
            'bobot_esai'           => 30,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.setting'), $payload);
        $response->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'pengaturan']));
        $response->assertSessionHas('success');

        $setting = PpdbUjianSetting::getAktif();
        $this->assertEquals('07:30', $setting->jam_mulai);
        $this->assertEquals('09:30', $setting->jam_selesai);
        $this->assertEquals('07.30 - 09.30 WIB', $setting->waktu_pelaksanaan);
    }

    public function test_admin_bisa_mengubah_jadwal_siswa_secara_single()
    {
        $pendaftar = $this->buatPendaftar('PPDB-SESI-01', '1122334458', 'Peserta Single Sesi', 'terverifikasi');

        $payload = [
            'jadwal_tes_tanggal' => '2026-09-25',
            'jadwal_tes_sesi'    => '10.30 - 12.30 WIB',
            'jadwal_tes_ruang'   => 'Lab Komputer 2',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.jadwalkan_single', $pendaftar->id), $payload);
        $response->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'penjadwalan']));
        $response->assertSessionHas('success');

        $pendaftar->refresh();
        $this->assertEquals('2026-09-25', $pendaftar->jadwal_tes_tanggal->toDateString());
        $this->assertEquals('10.30 - 12.30 WIB', $pendaftar->jadwal_tes_sesi);
        $this->assertEquals('Lab Komputer 2', $pendaftar->jadwal_tes_ruang);
    }

    public function test_admin_bisa_mengakses_cetak_instrumen_rubrik_wawancara()
    {
        $pendaftar = $this->buatPendaftar('PPDB-WCR-01', '1122334499', 'Peserta Rubrik Wawancara', 'terverifikasi');

        // 1. Akses format cetak single peserta
        $resSingle = $this->actingAs($this->admin)->get(route('admin.ppdb.seleksi.cetak_wawancara', $pendaftar->id));
        $resSingle->assertOk();
        $resSingle->assertSee('Instrumen &amp; Rubrik Wawancara Minat Kejuruan PPDB 2026', false);
        $resSingle->assertSee($pendaftar->nama_lengkap);

        // 2. Akses format cetak blank / template panitia
        $resBlank = $this->actingAs($this->admin)->get(route('admin.ppdb.seleksi.cetak_wawancara'));
        $resBlank->assertOk();
        $resBlank->assertSee('Instrumen &amp; Rubrik Wawancara Minat Kejuruan PPDB 2026', false);
        $resBlank->assertSee('Ketua Panitia PPDB 2026');
    }

    public function test_admin_bisa_mengatur_dan_mereset_materi_wawancara_secara_fleksibel()
    {
        // 1. Simpan kustomisasi materi wawancara
        $payload = [
            'materi_wawancara' => [
                'motivasi' => [
                    'pertanyaan_text' => "Pertanyaan Kustom 1\nPertanyaan Kustom 2",
                    'rubrik' => [
                        '85 - 100' => 'Kriteria kustom luar biasa',
                        '70 - 84'  => 'Kriteria kustom sedang',
                        '< 70'     => 'Kriteria kustom kurang',
                    ],
                ],
                'kejuruan_rpl' => [
                    'uji_fisik' => 'Uji buta warna piringan 1-8 dan uji logika coding sederhana',
                    'pertanyaan_text' => "Apakah pernah belajar HTML/CSS?",
                    'rubrik' => [
                        '85 - 100' => 'Paham konsep kustom',
                        '70 - 84'  => 'Biasa saja',
                        '< 70'     => 'Belum tahu sama sekali',
                    ],
                ],
            ],
        ];

        $resSave = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.materi_wawancara'), $payload);
        $resSave->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'wawancara']));
        $resSave->assertSessionHas('success');

        $setting = PpdbUjianSetting::getAktif();
        $mw = $setting->materi_wawancara_aktif;
        $this->assertCount(2, $mw['motivasi']['pertanyaan']);
        $this->assertEquals('Pertanyaan Kustom 1', $mw['motivasi']['pertanyaan'][0]);
        $this->assertEquals('Kriteria kustom luar biasa', $mw['motivasi']['rubrik']['85 - 100']);
        $this->assertEquals('Uji buta warna piringan 1-8 dan uji logika coding sederhana', $mw['kejuruan_rpl']['uji_fisik']);

        // 2. Reset materi wawancara ke template juknis standar
        $resReset = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.reset_materi_wawancara'));
        $resReset->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'wawancara']));
        $resReset->assertSessionHas('success');

        $setting->refresh();
        $this->assertNull($setting->materi_wawancara);
        // Accessor tetap mengembalikan default
        $mwDefault = $setting->materi_wawancara_aktif;
        $this->assertNotEmpty($mwDefault['motivasi']['pertanyaan']);
    }

    public function test_admin_bisa_mengakses_halaman_uji_tes_buta_warna_ishihara()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ppdb.seleksi.tes_buta_warna'));
        $response->assertOk();
        $response->assertSee('PIRINGAN UJI PERSEPSI WARNA ISHIHARA PPDB 2026');
        $response->assertSee('Plat #1');
        $response->assertSee('Plat #8');
        $response->assertSee('Bebas Buta Warna');
    }

    public function test_admin_bisa_mem_plotting_guru_penguji_pra_tes()
    {
        $guru = User::create([
            'name' => 'Budi Santoso, S.Kom.',
            'email' => 'budi.guru@smkn1airnaningan.sch.id',
            'role' => 'guru',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $p1 = $this->buatPendaftar('PPDB2026-001', '1234567891', 'Siswa RPL 1');
        $p2 = $this->buatPendaftar('PPDB2026-002', '1234567892', 'Siswa RPL 2');

        // Admin plot massal per jurusan RPL sebelum tanggal tes
        $res = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.plot_wawancara'), [
            'jurusan_id' => $this->jurusan->id,
            'pewawancara_id' => $guru->id,
        ]);

        $res->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'wawancara']));
        $res->assertSessionHas('success');

        $p1->refresh();
        $p2->refresh();
        $this->assertEquals($guru->id, $p1->pewawancara_id);
        $this->assertEquals($guru->id, $p2->pewawancara_id);

        // Admin ganti guru penguji single
        $guru2 = User::create([
            'name' => 'Dewi Lestari, S.Pd.',
            'email' => 'dewi.guru@smkn1airnaningan.sch.id',
            'role' => 'guru',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $resSingle = $this->actingAs($this->admin)->post(route('admin.ppdb.seleksi.plot_wawancara_single', $p1->id), [
            'pewawancara_id' => $guru2->id,
        ]);

        $resSingle->assertRedirect(route('admin.ppdb.seleksi', ['tab' => 'wawancara']));
        $p1->refresh();
        $this->assertEquals($guru2->id, $p1->pewawancara_id);
    }

    public function test_guru_penguji_bisa_mengakses_portal_wawancara_dan_menginput_nilai()
    {
        $guru = User::create([
            'name' => 'Budi Santoso, S.Kom.',
            'email' => 'budi.penguji@smkn1airnaningan.sch.id',
            'role' => 'guru',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $p = $this->buatPendaftar('PPDB2026-003', '1234567893', 'Calon Siswa Binaan');
        $p->pewawancara_id = $guru->id;
        $p->save();

        // Akses via redirect legacy guru.ppdb.wawancara
        $resLegacy = $this->actingAs($guru)->get(route('guru.ppdb.wawancara'));
        $resLegacy->assertRedirect(route('admin.ppdb.wawancara'));

        // Guru membuka portal wawancara di modul PPDB
        $resPortal = $this->actingAs($guru)->get(route('admin.ppdb.wawancara'));
        $resPortal->assertOk();
        $resPortal->assertSee('Meja Penilaian Wawancara');
        $resPortal->assertSee('Calon Siswa Binaan');

        // Guru menginput nilai wawancara
        $resNilai = $this->actingAs($guru)->post(route('admin.ppdb.wawancara.simpan', $p->id), [
            'nilai_wawancara_motivasi' => 90,
            'nilai_wawancara_karakter' => 88,
            'nilai_wawancara_kejuruan' => 92,
            'nilai_wawancara_ortu'     => 85,
            'catatan_wawancara'        => 'Bebas buta warna, motivasi sangat tinggi.',
        ]);

        $resNilai->assertRedirect();
        $resNilai->assertSessionHas('success');

        $p->refresh();
        $this->assertNotNull($p->nilai_wawancara_total);
        $this->assertEquals($guru->id, $p->pewawancara_id);
        $this->assertNotNull($p->diwawancara_pada);
        $this->assertEquals('Bebas buta warna, motivasi sangat tinggi.', $p->catatan_wawancara);
    }

    public function test_cetak_instrumen_dan_tes_buta_warna_merender_kop_dinas_2_logo_provinsi_lampung()
    {
        $resWawancara = $this->actingAs($this->admin)->get(route('admin.ppdb.seleksi.cetak_wawancara'));
        $resWawancara->assertOk();
        $resWawancara->assertSee('alt="Logo Provinsi Lampung"', false);
        $resWawancara->assertSee('alt="Logo Sekolah"', false);
        $resWawancara->assertSee('PEMERINTAH PROVINSI LAMPUNG');
        $resWawancara->assertSee('DINAS PENDIDIKAN DAN KEBUDAYAAN');

        $resButaWarna = $this->actingAs($this->admin)->get(route('admin.ppdb.seleksi.tes_buta_warna'));
        $resButaWarna->assertOk();
        $resButaWarna->assertSee('alt="Logo Provinsi Lampung"', false);
        $resButaWarna->assertSee('alt="Logo Sekolah"', false);
        $resButaWarna->assertSee('PEMERINTAH PROVINSI LAMPUNG');
        $resButaWarna->assertSee('DINAS PENDIDIKAN DAN KEBUDAYAAN');
    }
}

