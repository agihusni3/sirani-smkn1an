<?php

namespace Tests\Feature;

use App\Console\Commands\EvaluasiAlphaCommand;
use App\Models\Absensi;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\FaceScanService;
use App\Services\TransisiAkademikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SiswaPklTest extends TestCase
{
    use RefreshDatabase;

    protected TahunAjaran $ta;
    protected \App\Models\Jurusan $jurusan;
    protected Rombel $rombel;
    protected TransisiAkademikService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $this->jurusan = \App\Models\Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer Jaringan']);
        $this->rombel = Rombel::create([
            'nama_rombel' => 'XI TKJ 1',
            'tingkat' => 'XI',
            'jurusan_id' => $this->jurusan->id,
            'tahun_ajaran_id' => $this->ta->id
        ]);
        $this->service = new TransisiAkademikService();
    }

    public function test_siswa_bisa_ditugaskan_pkl_dan_selesai_pkl_individu(): void
    {
        $siswa = Siswa::create(['nis' => '11001', 'nama' => 'Budi PKL', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // 1. Mulai PKL
        $this->service->mulaiPkl($siswa->id);
        $this->assertEquals('pkl', $siswa->fresh()->status);

        // 2. Selesai PKL
        $this->service->selesaiPkl($siswa->id);
        $this->assertEquals('aktif', $siswa->fresh()->status);
    }

    public function test_batch_penugasan_pkl_massal_dan_selesai_massal(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $s = Siswa::create(['nis' => "1100{$i}", 'nama' => "Siswa {$i}", 'status' => 'aktif']);
            SiswaRombel::create([
                'siswa_id' => $s->id,
                'rombel_id' => $this->rombel->id,
                'tahun_ajaran_id' => $this->ta->id,
                'status_keanggotaan' => 'aktif',
            ]);
        }

        // Batch PKL
        $countPkl = $this->service->batchPkl($this->rombel->id);
        $this->assertEquals(3, $countPkl);
        $this->assertEquals(3, Siswa::where('status', 'pkl')->count());

        // Batch Selesai PKL
        $countSelesai = $this->service->batchSelesaiPkl($this->rombel->id);
        $this->assertEquals(3, $countSelesai);
        $this->assertEquals(3, Siswa::where('status', 'aktif')->count());
    }

    public function test_siswa_pkl_terbebas_dari_evaluasi_alpha_otomatis(): void
    {
        // Siswa 1: Aktif di sekolah (tidak hadir -> harus kena Alpha)
        $siswaAktif = Siswa::create(['nis' => '11010', 'nama' => 'Siswa Reguler', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id' => $siswaAktif->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Siswa 2: Sedang PKL di DUDI (tidak hadir di sekolah -> TIDAK BOLEH kena Alpha)
        $siswaPkl = Siswa::create(['nis' => '11011', 'nama' => 'Siswa Sedang PKL', 'status' => 'pkl']);
        SiswaRombel::create([
            'siswa_id' => $siswaPkl->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Paksa ke hari kerja (Senin) agar command tidak di-skip karena weekend
        Carbon::setTestNow(Carbon::parse('next monday'));
        $today = Carbon::today()->toDateString();
        $this->artisan("absensi:evaluasi-alpha {$today}")->assertExitCode(0);

        // Siswa Aktif tercatat Alpha
        $this->assertDatabaseHas('absensis', [
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswaAktif->id,
            'tanggal' => $today,
            'status' => 'alpha',
        ]);

        // Siswa PKL TIDAK memiliki record Alpha
        $this->assertDatabaseMissing('absensis', [
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswaPkl->id,
            'tanggal' => $today,
        ]);

        Carbon::setTestNow(); // reset
    }

    public function test_scan_wajah_siswa_pkl_tercatat_berhasil(): void
    {
        $siswa = Siswa::create(['nis' => '11020', 'nama' => 'Ahmad PKL', 'status' => 'pkl']);
        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $faceService = new FaceScanService();
        $res = $faceService->scanPerson('siswa', $siswa->id, 'face_kiosk');

        $this->assertEquals('success', $res['status']);
        $this->assertEquals('jam_masuk', $res['type']);
    }

    public function test_web_transisi_pkl_endpoint_berhasil(): void
    {
        $user = User::factory()->create();
        $siswa = Siswa::create(['nis' => '11030', 'nama' => 'Dedi PKL Web', 'status' => 'aktif']);
        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $this->rombel->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        // Transisi web mulai PKL
        $res = $this->actingAs($user)->post('/siklus-siswa/transisi', [
            'siswa_id' => $siswa->id,
            'jenis' => 'mulai_pkl',
        ]);
        $res->assertRedirect();
        $this->assertEquals('pkl', $siswa->fresh()->status);

        // Transisi web selesai PKL
        $res2 = $this->actingAs($user)->post('/siklus-siswa/transisi', [
            'siswa_id' => $siswa->id,
            'jenis' => 'selesai_pkl',
        ]);
        $res2->assertRedirect();
        $this->assertEquals('aktif', $siswa->fresh()->status);
    }
}
