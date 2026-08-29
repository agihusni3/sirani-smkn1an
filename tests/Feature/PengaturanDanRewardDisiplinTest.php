<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinReward;
use App\Models\KatalogReward;
use App\Models\PengaturanDisiplin;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanDanRewardDisiplinTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guruBk;
    private Siswa $siswa;
    private TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create([
            'nama'      => '2026/2027 Ganjil',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name'     => 'Administrator Disiplin',
            'email'    => 'admin_disiplin@smk.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $guru = Guru::create(['nama' => 'Guru BK Hebat', 'status' => 'aktif', 'jabatan' => 'Guru BK']);
        $this->guruBk = User::create([
            'name'     => 'Guru BK User',
            'email'    => 'bk@smk.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'guru_bk',
            'guru_id'  => $guru->id,
        ]);

        $this->siswa = Siswa::create([
            'nis'    => '9901',
            'nama'   => 'Siswa Disiplin Test',
            'status' => 'aktif',
        ]);
    }

    public function test_admin_bisa_mengubah_aturan_bobot_poin_dan_ambang_batas()
    {
        $response = $this->actingAs($this->admin)->post('/disiplin/pengaturan-poin', [
            'bobot_terlambat'           => 5,
            'bobot_alpha'               => 15,
            'bobot_bolos'               => 20,
            'toleransi_terlambat_piket' => 3,
            'ambang_tahap_1_wali'       => 15,
            'ambang_tahap_2_bk'         => 45,
            'ambang_tahap_3_wakasis'    => 75,
            'ambang_tahap_4_kepsek'     => 100,
            'reward_streak_hari'        => 20,
            'reward_streak_poin'        => 10,
        ]);

        $response->assertSessionHas('success');
        $setting = PengaturanDisiplin::getPengaturan();
        $this->assertEquals(5, $setting->bobot_terlambat);
        $this->assertEquals(15, $setting->bobot_alpha);
        $this->assertEquals(20, $setting->bobot_bolos);
        $this->assertEquals(3, $setting->toleransi_terlambat_piket);
        $this->assertEquals(15, $setting->ambang_tahap_1_wali);
    }

    public function test_admin_dan_bk_bisa_kelola_katalog_reward_master()
    {
        // 1. Tambah Reward Baru
        $response = $this->actingAs($this->admin)->post('/disiplin/katalog-reward', [
            'nama_reward'  => 'Juara 1 LKS Web Tech',
            'kategori'     => 'prestasi',
            'poin_deduksi' => 25,
            'deskripsi'    => 'Mewakili sekolah tingkat provinsi',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('katalog_rewards', [
            'nama_reward'  => 'Juara 1 LKS Web Tech',
            'poin_deduksi' => 25,
        ]);

        $katalog = KatalogReward::where('nama_reward', 'Juara 1 LKS Web Tech')->first();

        // 2. Update Reward
        $responseEdit = $this->actingAs($this->admin)->put("/disiplin/katalog-reward/{$katalog->id}", [
            'nama_reward'  => 'Juara 1 LKS Web Tech Nasional',
            'kategori'     => 'prestasi',
            'poin_deduksi' => 30,
            'deskripsi'    => 'Tingkat Nasional',
            'is_active'    => 1,
        ]);
        $responseEdit->assertSessionHas('success');
        $this->assertDatabaseHas('katalog_rewards', ['nama_reward' => 'Juara 1 LKS Web Tech Nasional', 'poin_deduksi' => 30]);

        // 3. Delete Reward
        $responseDel = $this->actingAs($this->admin)->delete("/disiplin/katalog-reward/{$katalog->id}");
        $responseDel->assertSessionHas('success');
        $this->assertDatabaseMissing('katalog_rewards', ['id' => $katalog->id]);
    }

    public function test_petugas_bisa_memberikan_self_reward_dan_mengurangi_poin_siswa()
    {
        // Buat absensi siswa melanggar: 3x Alpha (30 poin default)
        for ($i = 0; $i < 3; $i++) {
            Absensi::create([
                'pemilik_type' => 'siswa',
                'pemilik_id'   => $this->siswa->id,
                'tanggal'      => Carbon::today()->subDays($i)->toDateString(),
                'status'       => 'alpha',
            ]);
        }

        $kasus = KasusDisiplin::syncFromPresensi($this->siswa->id);
        $this->assertEquals(30, $kasus->total_poin_pelanggaran);
        $this->assertEquals(30, $kasus->poin_bersih);
        $this->assertEquals('tahap_2_bk', $kasus->status_tahap);

        // Berikan Self-Reward: Bakti Sosial (-10 Poin)
        $responseReward = $this->actingAs($this->guruBk)->post("/disiplin/{$kasus->id}/reward", [
            'nama_tindakan'  => 'Membersihkan Bengkel Otomotif',
            'poin_dikurangi' => 10,
            'tanggal'        => Carbon::today()->toDateString(),
            'catatan'        => 'Siswa rajin dan menunjukkan perbaikan sikap.',
        ]);

        $responseReward->assertSessionHas('success');

        $kasusFresh = $kasus->fresh();
        $this->assertEquals(10, $kasusFresh->total_poin_pemulihan);
        $this->assertEquals(20, $kasusFresh->poin_bersih); // 30 - 10 = 20
        $this->assertEquals('tahap_1_wali_kelas', $kasusFresh->status_tahap); // Turun tahap ke Tahap 1 (10-29 poin)

        // Berikan Reward Lagi hingga 0 poin
        $this->actingAs($this->guruBk)->post("/disiplin/{$kasus->id}/reward", [
            'nama_tindakan'  => 'Penyelesaian Tugas Resume Karakter',
            'poin_dikurangi' => 20,
            'tanggal'        => Carbon::today()->toDateString(),
        ]);

        $kasusTuntas = $kasus->fresh();
        $this->assertEquals(0, $kasusTuntas->poin_bersih);
        $this->assertEquals('selesai_pembinaan', $kasusTuntas->status_tahap);
    }

    public function test_batch_recalculate_memperbarui_seluruh_kasus_siswa()
    {
        // Buat 2x terlambat
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->toDateString(),
            'status'       => 'terlambat',
        ]);
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $this->siswa->id,
            'tanggal'      => Carbon::today()->subDay()->toDateString(),
            'status'       => 'terlambat',
        ]);

        // Dengan toleransi 2x, poin terlambat yang dihitung = max(0, 2 - 2) * 3 = 0
        KasusDisiplin::syncFromPresensi($this->siswa->id);
        $kasus = KasusDisiplin::where('siswa_id', $this->siswa->id)->first();
        $this->assertEquals(0, $kasus->poin_bersih);
        $this->assertEquals('selesai_pembinaan', $kasus->status_tahap);

        // Ubah toleransi jadi 0x dan bobot telat 5 poin
        $setting = PengaturanDisiplin::getPengaturan();
        $setting->update(['toleransi_terlambat_piket' => 0, 'bobot_terlambat' => 5]);

        // Jalankan Batch Recalculate
        $response = $this->actingAs($this->admin)->post('/disiplin/recalculate');
        $response->assertSessionHas('success');

        $kasusUpdated = $kasus->fresh();
        // 2x telat * 5 poin = 10 poin -> masuk Tahap 1: Wali Kelas
        $this->assertEquals(10, $kasusUpdated->poin_bersih);
        $this->assertEquals('tahap_1_wali_kelas', $kasusUpdated->status_tahap);
    }

    public function test_admin_dan_bk_bisa_kelola_katalog_pelanggaran_master()
    {
        // 1. Tambah Pelanggaran Baru ke Master
        $response = $this->actingAs($this->admin)->post('/disiplin/katalog-pelanggaran', [
            'nama_pelanggaran' => 'Vape di Belakang Kelas',
            'kategori'         => 'berat',
            'poin_pelanggaran' => 35,
            'deskripsi'        => 'Membawa rokok elektrik ke sekolah',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('katalog_pelanggarans', [
            'nama_pelanggaran' => 'Vape di Belakang Kelas',
            'poin_pelanggaran' => 35,
        ]);

        $katalog = \App\Models\KatalogPelanggaran::where('nama_pelanggaran', 'Vape di Belakang Kelas')->first();

        // 2. Update Pelanggaran
        $responseEdit = $this->actingAs($this->admin)->put("/disiplin/katalog-pelanggaran/{$katalog->id}", [
            'nama_pelanggaran' => 'Vape & Merokok di Sekolah',
            'kategori'         => 'berat',
            'poin_pelanggaran' => 40,
            'deskripsi'        => 'Membawa rokok elektrik atau tembakau',
            'is_active'        => 1,
        ]);
        $responseEdit->assertSessionHas('success');
        $this->assertDatabaseHas('katalog_pelanggarans', ['nama_pelanggaran' => 'Vape & Merokok di Sekolah', 'poin_pelanggaran' => 40]);

        // 3. Delete Pelanggaran
        $responseDel = $this->actingAs($this->admin)->delete("/disiplin/katalog-pelanggaran/{$katalog->id}");
        $responseDel->assertSessionHas('success');
        $this->assertDatabaseMissing('katalog_pelanggarans', ['id' => $katalog->id]);
    }

    public function test_petugas_bisa_mencatat_pelanggaran_manual_dan_menambah_poin_siswa()
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'selesai_pembinaan',
        ]);

        // Catat Pelanggaran Manual: Merokok (+25 Poin)
        $response = $this->actingAs($this->guruBk)->post("/disiplin/{$kasus->id}/pelanggaran", [
            'nama_pelanggaran' => 'Merokok di Kantin Sekolah',
            'poin_ditambah'    => 25,
            'tanggal'          => Carbon::today()->toDateString(),
            'catatan'          => 'Tertangkap saat jam istirahat pertama.',
        ]);

        $response->assertSessionHas('success');

        $kasusFresh = $kasus->fresh();
        $this->assertEquals(25, $kasusFresh->total_poin_pelanggaran);
        $this->assertEquals(25, $kasusFresh->poin_bersih);
        $this->assertEquals('tahap_1_wali_kelas', $kasusFresh->status_tahap);

        // Hapus catatan pelanggaran
        $pelanggaran = \App\Models\KasusDisiplinPelanggaran::where('kasus_disiplin_id', $kasus->id)->first();
        $responseHapus = $this->actingAs($this->guruBk)->delete("/disiplin/{$kasus->id}/pelanggaran/{$pelanggaran->id}");
        $responseHapus->assertSessionHas('success');

        $kasusNol = $kasus->fresh();
        $this->assertEquals(0, $kasusNol->poin_bersih);
        $this->assertEquals('selesai_pembinaan', $kasusNol->status_tahap);
    }
}
