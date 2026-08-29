<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\FaceScanService;
use App\Services\TransisiAkademikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class TransisiAkademikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Login as Administrator for web routes
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@smkn1airnaningan.sch.id',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($admin);
    }

    /**
     * 0. Pengujian Akses Halaman Siklus Siswa (GET /siklus-siswa)
     */
    public function test_halaman_siklus_siswa_dapat_diakses_dengan_sukses(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '1000', 'nama' => 'Siswa Test', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'tahun_ajaran_id' => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $response = $this->get('/siklus-siswa');
        $response->assertStatus(200);
        $response->assertSee('Siklus Akademik Siswa');
        $response->assertSee('Aksi Massal per Kelas');
    }

    /**
     * 1. Pengujian Kenaikan Kelas (Tingkat X ke XI)
     */
    public function test_siswa_bisa_naik_kelas(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $taOld = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);
        $taNew = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);

        $rombel1 = Rombel::create(['tahun_ajaran_id' => $taOld->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL', 'tingkat' => 'X']);
        $rombel2 = Rombel::create(['tahun_ajaran_id' => $taNew->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XI RPL', 'tingkat' => 'XI']);

        $siswa = Siswa::create(['nis' => '1001', 'nama' => 'Budi Pertiwi', 'status' => 'aktif']);

        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel1->id,
            'tahun_ajaran_id' => $taOld->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $service = new TransisiAkademikService();
        $newSr = $service->naikKelas($siswa->id, $rombel2->id, $taNew->id);

        $this->assertEquals('aktif', $newSr->status_keanggotaan);
        $this->assertEquals($rombel2->id, $newSr->rombel_id);

        // Keanggotaan lama harus berstatus 'naik' dan tidak terhapus
        $oldSr = SiswaRombel::where('siswa_id', $siswa->id)->where('rombel_id', $rombel1->id)->first();
        $this->assertNotNull($oldSr);
        $this->assertEquals('naik', $oldSr->status_keanggotaan);
    }

    /**
     * 2. Pengujian Tinggal Kelas (Tingkat X tetap di X pada Tahun Ajaran Baru)
     */
    public function test_siswa_bisa_tinggal_kelas(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'TSM', 'nama_jurusan' => 'Teknik Sepeda Motor']);
        $taOld = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);
        $taNew = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);

        $rombelOld = Rombel::create(['tahun_ajaran_id' => $taOld->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TSM', 'tingkat' => 'X']);
        $rombelNew = Rombel::create(['tahun_ajaran_id' => $taNew->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TSM', 'tingkat' => 'X']);

        $siswa = Siswa::create(['nis' => '1002', 'nama' => 'Rian Hidayat', 'status' => 'aktif']);

        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombelOld->id,
            'tahun_ajaran_id' => $taOld->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $service = new TransisiAkademikService();
        $newSr = $service->tinggalKelas($siswa->id, $rombelNew->id, $taNew->id);

        $this->assertEquals('aktif', $newSr->status_keanggotaan);
        $this->assertEquals($taNew->id, $newSr->tahun_ajaran_id);

        // Keanggotaan lama berstatus 'tinggal'
        $oldSr = SiswaRombel::where('siswa_id', $siswa->id)->where('rombel_id', $rombelOld->id)->first();
        $this->assertEquals('tinggal', $oldSr->status_keanggotaan);
    }

    /**
     * 3. Pengujian Kelulusan Siswa (Terminal Lulus & RFID Otomatis Nonaktif)
     */
    public function test_siswa_lulus_nonaktifkan_rfid_dan_tidak_bisa_scan(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'APHP', 'nama_jurusan' => 'Agribisnis Pengolahan']);
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XII APHP', 'tingkat' => 'XII']);
        $siswa = Siswa::create(['nis' => '1003', 'nama' => 'Siswa Alumni Sukses', 'status' => 'aktif']);

        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'tahun_ajaran_id' => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Eksekusi Kelulusan
        $transisiService = new TransisiAkademikService();
        $transisiService->lulus($siswa->id);

        // Verifikasi Status Siswa
        $this->assertEquals('lulus', $siswa->fresh()->status);

        // Uji Kios Scan Biometrik Wajah: harus ditolak dengan Exception
        $this->expectException(Exception::class);
        $scanService = new FaceScanService();
        $scanService->scanPerson('siswa', $siswa->id);
    }

    /**
     * 4. Pengujian Siswa Pindah Sekolah
     */
    public function test_siswa_pindah(): void
    {
        $siswa = Siswa::create(['nis' => '1004', 'nama' => 'Ahmad Pindah', 'status' => 'aktif']);

        $transisiService = new TransisiAkademikService();
        $transisiService->pindah($siswa->id);

        $this->assertEquals('pindah', $siswa->fresh()->status);
    }

    /**
     * 5. Pengujian Siswa Keluar
     */
    public function test_siswa_keluar(): void
    {
        $siswa = Siswa::create(['nis' => '1005', 'nama' => 'Dimas Keluar', 'status' => 'aktif']);

        $transisiService = new TransisiAkademikService();
        $transisiService->keluar($siswa->id);

        $this->assertEquals('keluar', $siswa->fresh()->status);
    }

    /**
     * 6. Validasi Proteksi: Siswa Non-Aktif (Lulus/Pindah/Keluar) Tidak Bisa Dinaikkan Kelas
     */
    public function test_siswa_nonaktif_tidak_bisa_dinaikkan_kelas(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XI RPL', 'tingkat' => 'XI']);
        
        $siswaLulus = Siswa::create(['nis' => '1006', 'nama' => 'Siswa Sudah Lulus', 'status' => 'lulus']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Hanya siswa aktif yang dapat dinaikkan kelas.');

        $service = new TransisiAkademikService();
        $service->naikKelas($siswaLulus->id, $rombel->id, $ta->id);
    }

    /**
     * 7. Pengujian Endpoint Web Transisi POST (UI Form Submission)
     */
    public function test_web_transisi_endpoint_berhasil(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $taOld = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);
        $taNew = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);

        $rombelOld = Rombel::create(['tahun_ajaran_id' => $taOld->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL', 'tingkat' => 'X']);
        $rombelNew = Rombel::create(['tahun_ajaran_id' => $taNew->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XI RPL', 'tingkat' => 'XI']);

        $siswa = Siswa::create(['nis' => '1007', 'nama' => 'Putri Ayu', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombelOld->id,
            'tahun_ajaran_id' => $taOld->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Simulasikan submit form transisi naik kelas dari web
        $response = $this->post('/siklus-siswa/transisi', [
            'siswa_id' => $siswa->id,
            'jenis' => 'naik_kelas',
            'rombel_tujuan_id' => $rombelNew->id,
            'tahun_ajaran_baru_id' => $taNew->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan keanggotaan baru aktif di database
        $this->assertDatabaseHas('siswa_rombels', [
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombelNew->id,
            'status_keanggotaan' => 'aktif',
        ]);
    }

    /**
     * 8. Pengujian Multi-Tahun Perjalanan Akademik (X -> XI -> XII -> Lulus)
     * Memastikan seluruh 3 tahun perjalanan tersimpan utuh dan tidak ada data tertimpa
     */
    public function test_histori_keanggotaan_tersimpan_secara_lengkap_multi_tahun(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $ta1 = TahunAjaran::create(['nama' => '2024/2025', 'is_active' => false]);
        $ta2 = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);
        $ta3 = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);

        $rombel10 = Rombel::create(['tahun_ajaran_id' => $ta1->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL', 'tingkat' => 'X']);
        $rombel11 = Rombel::create(['tahun_ajaran_id' => $ta2->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XI RPL', 'tingkat' => 'XI']);
        $rombel12 = Rombel::create(['tahun_ajaran_id' => $ta3->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XII RPL', 'tingkat' => 'XII']);

        $siswa = Siswa::create(['nis' => '1008', 'nama' => 'Siti Khodijah', 'status' => 'aktif']);

        // Tahun 1: Kelas X
        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel10->id,
            'tahun_ajaran_id' => $ta1->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $service = new TransisiAkademikService();

        // Tahun 2: Naik ke Kelas XI
        $service->naikKelas($siswa->id, $rombel11->id, $ta2->id);

        // Tahun 3: Naik ke Kelas XII
        $service->naikKelas($siswa->id, $rombel12->id, $ta3->id);

        // Akhir Tahun 3: Lulus
        $service->kelulusan($siswa->id);

        // Verifikasi Total Histori Rombel Siswa Harus Tepat 3 Baris
        $allHistori = SiswaRombel::where('siswa_id', $siswa->id)->get();
        $this->assertCount(3, $allHistori);

        // Status Tiap Tahun:
        // Tahun 1 -> naik
        $this->assertEquals('naik', $allHistori->firstWhere('rombel_id', $rombel10->id)->status_keanggotaan);
        // Tahun 2 -> naik
        $this->assertEquals('naik', $allHistori->firstWhere('rombel_id', $rombel11->id)->status_keanggotaan);
        // Tahun 3 -> lulus
        $this->assertEquals('lulus', $allHistori->firstWhere('rombel_id', $rombel12->id)->status_keanggotaan);

        // Status Akhir Siswa
        $this->assertEquals('lulus', $siswa->fresh()->status);
    }

    /**
     * 9. Pengujian Kenaikan Kelas Massal Seluruh Siswa di Rombel
     */
    public function test_batch_naik_kelas_massal_satu_rombel(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);
        $ta1 = TahunAjaran::create(['nama' => '2025/2026', 'is_active' => false]);
        $ta2 = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);

        $rombelAsal = Rombel::create(['tahun_ajaran_id' => $ta1->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X RPL', 'tingkat' => 'X']);
        $rombelTujuan = Rombel::create(['tahun_ajaran_id' => $ta2->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XI RPL', 'tingkat' => 'XI']);

        // Buat 5 Siswa dalam rombel asal
        for ($i = 1; $i <= 5; $i++) {
            $s = Siswa::create(['nis' => "202400{$i}", 'nama' => "Siswa {$i}", 'status' => 'aktif']);
            SiswaRombel::create([
                'siswa_id' => $s->id,
                'rombel_id' => $rombelAsal->id,
                'tahun_ajaran_id' => $ta1->id,
                'status_keanggotaan' => 'aktif',
            ]);
        }

        // Eksekusi Massal via POST endpoint
        $response = $this->post('/siklus-siswa/transisi-massal', [
            'rombel_asal_id' => $rombelAsal->id,
            'aksi_massal' => 'naik_kelas',
            'rombel_tujuan_id' => $rombelTujuan->id,
            'tahun_ajaran_baru_id' => $ta2->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan seluruh 5 siswa kini aktif di rombel tujuan
        $this->assertEquals(5, SiswaRombel::where('rombel_id', $rombelTujuan->id)->where('status_keanggotaan', 'aktif')->count());
        // Seluruh 5 keanggotaan lama harus berstatus 'naik'
        $this->assertEquals(5, SiswaRombel::where('rombel_id', $rombelAsal->id)->where('status_keanggotaan', 'naik')->count());
    }

    /**
     * 10. Pengujian Kelulusan Massal Seluruh Siswa di Rombel XII
     */
    public function test_batch_kelulusan_massal_satu_rombel(): void
    {
        $jurusan = Jurusan::create(['kode_jurusan' => 'TSM', 'nama_jurusan' => 'Teknik Sepeda Motor']);
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $rombelXII = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'XII TSM', 'tingkat' => 'XII']);

        // Buat 4 Siswa aktif
        for ($i = 1; $i <= 4; $i++) {
            $s = Siswa::create(['nis' => "202300{$i}", 'nama' => "Calon Alumni {$i}", 'status' => 'aktif']);
            SiswaRombel::create([
                'siswa_id' => $s->id,
                'rombel_id' => $rombelXII->id,
                'tahun_ajaran_id' => $ta->id,
                'status_keanggotaan' => 'aktif',
            ]);
        }

        // Eksekusi Kelulusan Massal via POST endpoint
        $response = $this->post('/siklus-siswa/transisi-massal', [
            'rombel_asal_id' => $rombelXII->id,
            'aksi_massal' => 'lulus',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Seluruh 4 siswa berstatus 'lulus'
        $this->assertEquals(4, Siswa::where('status', 'lulus')->count());
    }
}

