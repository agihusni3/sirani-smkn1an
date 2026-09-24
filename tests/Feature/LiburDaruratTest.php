<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\HariLiburService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiburDaruratTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_tetapkan_libur_darurat_membatalkan_alpha_dan_reset_poin_disiplin()
    {
        $today = Carbon::today()->toDateString();

        $ta = TahunAjaran::create([
            'nama'      => '2026/2027',
            'semester'  => 'ganjil',
            'is_active' => true,
        ]);

        $jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $rombel = Rombel::create([
            'nama_rombel'     => 'XII RPL 1',
            'tingkat'         => 12,
            'tahun_ajaran_id' => $ta->id,
            'jurusan_id'      => $jurusan->id,
        ]);

        $siswa = Siswa::create([
            'nisn'          => '1234567890',
            'nama'          => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'status'        => 'aktif',
        ]);

        $membership = SiswaRombel::create([
            'siswa_id'           => $siswa->id,
            'rombel_id'          => $rombel->id,
            'tahun_ajaran_id'    => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // 1. Simulasikan bahwa siswa sudah terlanjur dikunci Alpha pukul 09:00 WIB
        $absenAlpha = Absensi::create([
            'pemilik_type'    => 'siswa',
            'pemilik_id'      => $siswa->id,
            'siswa_rombel_id' => $membership->id,
            'tanggal'         => $today,
            'status'          => 'alpha',
            'sumber_absen'    => 'auto_kunci_piket',
            'keterangan'      => 'Terkunci sistem',
        ]);

        // Sinkron poin disiplin: Siswa memiliki 1 Alpha
        KasusDisiplin::syncFromPresensi($siswa->id);
        $kasusSebelum = KasusDisiplin::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($kasusSebelum);
        $this->assertEquals(1, $kasusSebelum->total_alpha);

        // Buat draf notifikasi WA
        NotifikasiOrtu::create([
            'siswa_id'    => $siswa->id,
            'kategori'    => 'alpha',
            'tanggal'     => $today,
            'no_tujuan'   => '08123456789',
            'judul'       => 'Pemberitahuan Alpha',
            'pesan'       => 'Siswa Alpha hari ini',
            'status'      => 'pending',
            'dibuat_oleh' => 'sistem_cron',
        ]);

        $this->assertEquals(1, NotifikasiOrtu::where('siswa_id', $siswa->id)->count());

        // 2. Sekarang pihak sekolah menetapkan Libur Darurat
        $hasil = HariLiburService::tetapkanLiburDarurat(
            'Banjir Bandang & Cuaca Buruk',
            'Akses jalan menuju sekolah terputus',
            'Guru Piket'
        );

        $this->assertTrue($hasil['success']);
        $this->assertEquals(1, $hasil['siswa_alpha_dibatalkan']);
        $this->assertEquals(1, $hasil['notifikasi_dibatalkan']);

        // 3. Verifikasi: HariLibur tercatat
        $this->assertTrue(HariLibur::isLibur($today));
        $libur = HariLibur::where('tanggal_mulai', '<=', $today)->where('tanggal_selesai', '>=', $today)->first();
        $this->assertNotNull($libur);
        $this->assertEquals('Banjir Bandang & Cuaca Buruk', $libur->nama_libur);
        $this->assertEquals('khusus_sekolah', $libur->jenis);

        // 4. Verifikasi: Record Absensi Alpha hari ini terhapus
        $absenCek = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('tanggal', $today)->first();
        $this->assertNull($absenCek);

        // 5. Verifikasi: Poin disiplin siswa kembali ke 0
        $kasusSetelah = KasusDisiplin::where('siswa_id', $siswa->id)->first();
        $this->assertEquals(0, $kasusSetelah->total_alpha);

        // 6. Verifikasi: Notifikasi WA pending terhapus
        $notifCek = NotifikasiOrtu::where('siswa_id', $siswa->id)->where('tanggal', $today)->first();
        $this->assertNull($notifCek);
    }

    public function test_controller_piket_libur_darurat_dan_batal_libur()
    {
        $today = Carbon::today()->toDateString();

        $admin = User::create([
            'name'     => 'Admin Sekolah',
            'email'    => 'admin@smkn1an.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('piket.libur-darurat'), [
            'nama_libur' => 'Rapat Dinas Mendadak',
            'keterangan' => 'Seluruh dewan guru rapat koordinasi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(HariLibur::isLibur($today));
        $libur = HariLibur::where('tanggal_mulai', $today)->first();
        $this->assertNotNull($libur);
        $this->assertEquals('Rapat Dinas Mendadak', $libur->nama_libur);

        // Uji pembatalan libur darurat
        $responseBatal = $this->actingAs($admin)->post(route('piket.batal-libur-darurat', $libur->id));
        $responseBatal->assertRedirect();
        $responseBatal->assertSessionHas('success');

        $this->assertFalse(HariLibur::where('id', $libur->id)->exists());
    }

    public function test_admin_tambah_hari_libur_retroaktif_membersihkan_alpha()
    {
        $today = Carbon::today()->toDateString();

        $ta = TahunAjaran::create([
            'nama'      => '2026/2027',
            'semester'  => 'ganjil',
            'is_active' => true,
        ]);
        $jurusan = \App\Models\Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'TKJ']);
        $rombel = Rombel::create(['nama_rombel' => 'X TKJ 1', 'tingkat' => 10, 'tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id]);
        $siswa = Siswa::create(['nisn' => '9988776655', 'nama' => 'Rian Hidayat', 'jenis_kelamin' => 'L', 'status' => 'aktif']);
        $membership = SiswaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        // Siswa sudah terlanjur alpha
        Absensi::create([
            'pemilik_type'    => 'siswa',
            'pemilik_id'      => $siswa->id,
            'siswa_rombel_id' => $membership->id,
            'tanggal'         => $today,
            'status'          => 'alpha',
            'sumber_absen'    => 'auto_kunci_piket',
        ]);
        KasusDisiplin::syncFromPresensi($siswa->id);

        $admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'sadmin@smkn1an.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        // Admin menambahkan libur manual lewat form Kalender Libur
        $response = $this->actingAs($admin)->post(route('admin.hari-libur.store'), [
            'nama_libur'      => 'Hari Guru Nasional',
            'tanggal_mulai'   => $today,
            'tanggal_selesai' => $today,
            'jenis'           => 'khusus_sekolah',
            'keterangan'      => 'Peringatan Hari Guru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('absensis', [
            'pemilik_id' => $siswa->id,
            'tanggal'    => $today,
            'status'     => 'alpha',
        ]);

        $kasus = KasusDisiplin::where('siswa_id', $siswa->id)->first();
        $this->assertEquals(0, $kasus->total_alpha);
    }
}

