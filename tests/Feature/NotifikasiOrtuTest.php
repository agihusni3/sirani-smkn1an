<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\NotifikasiDraftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiOrtuTest extends TestCase
{
    use RefreshDatabase;

    public function test_presensi_terlambat_membuat_draf_notifikasi_pending()
    {
        $siswa = Siswa::create([
            'nis'        => '1001',
            'nama'       => 'Andi Pratama',
            'no_hp_ortu' => '081234567890',
            'status'     => 'aktif',
        ]);

        $absensi = Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id'   => $siswa->id,
            'tanggal'      => '2026-08-17',
            'jam_masuk'    => '07:25:00',
            'status'       => 'terlambat',
            'keterangan'   => 'Terlambat 10 menit',
        ]);

        $draft = NotifikasiDraftService::buatDraft($siswa, 'terlambat', [
            'tanggal'   => '2026-08-17',
            'jam_masuk' => '07:25:00',
        ]);

        $this->assertNotNull($draft);
        $this->assertEquals('pending', $draft->status);
        $this->assertEquals('terlambat', $draft->kategori);
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id' => $siswa->id,
            'kategori' => 'terlambat',
            'status'   => 'pending',
        ]);
    }

    public function test_izin_siswa_membuat_draf_notifikasi_pending()
    {
        $siswa = Siswa::create([
            'nis'        => '1002',
            'nama'       => 'Budi Santoso',
            'no_hp_ortu' => '081298765432',
            'status'     => 'aktif',
        ]);

        $draft = NotifikasiDraftService::buatDraft($siswa, 'izin', [
            'tanggal'    => '2026-08-17',
            'keterangan' => 'Acara Keluarga',
        ]);

        $this->assertNotNull($draft);
        $this->assertEquals('pending', $draft->status);
        $this->assertEquals('izin', $draft->kategori);
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id' => $siswa->id,
            'kategori' => 'izin',
            'status'   => 'pending',
        ]);
    }

    public function test_evaluasi_alpha_membuat_draf_notifikasi_alpha()
    {
        $siswa = Siswa::create([
            'nis'        => '1003',
            'nama'       => 'Citra Dewi',
            'no_hp_ortu' => '081355667788',
            'status'     => 'aktif',
        ]);

        $draft = NotifikasiDraftService::buatDraft($siswa, 'alpha', [
            'tanggal' => '2026-08-17',
        ]);

        $this->assertNotNull($draft);
        $this->assertEquals('pending', $draft->status);
        $this->assertEquals('alpha', $draft->kategori);
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id' => $siswa->id,
            'kategori' => 'alpha',
            'status'   => 'pending',
        ]);
    }

    public function test_anti_duplikasi_tidak_membuat_draf_ganda_di_hari_yang_sama()
    {
        $siswa = Siswa::create([
            'nis'   => '1004',
            'nama'  => 'Doni Pratama',
            'status'=> 'aktif',
        ]);

        $draft1 = NotifikasiDraftService::buatDraft($siswa, 'terlambat', ['tanggal' => '2026-08-17']);
        $draft2 = NotifikasiDraftService::buatDraft($siswa, 'terlambat', ['tanggal' => '2026-08-17']);

        $this->assertEquals($draft1->id, $draft2->id);
        $this->assertEquals(1, NotifikasiOrtu::where('siswa_id', $siswa->id)->count());
    }

    public function test_petugas_bisa_verifikasi_dan_kirim_notifikasi()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = Siswa::create([
            'nis'        => '1005',
            'nama'       => 'Eka Putri',
            'no_hp_ortu' => '081211223344',
            'status'     => 'aktif',
        ]);

        $notif = NotifikasiOrtu::create([
            'siswa_id'    => $siswa->id,
            'kategori'    => 'terlambat',
            'tanggal'     => '2026-08-17',
            'no_tujuan'   => '081211223344',
            'judul'       => 'Terlambat',
            'pesan'       => 'Pesan terlambat',
            'status'      => 'pending',
            'dibuat_oleh' => 'sistem',
        ]);

        $response = $this->actingAs($admin)->post("/notifikasi/{$notif->id}/approve");

        $response->assertRedirect();
        $notifFresh = $notif->fresh();
        $this->assertEquals('terkirim', $notifFresh->status);
        $this->assertNotNull($notifFresh->waktu_verifikasi);
    }

    public function test_petugas_bisa_batalkan_draf_notifikasi()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = Siswa::create([
            'nis'    => '1006',
            'nama'   => 'Farhan',
            'status' => 'aktif',
        ]);

        $notif = NotifikasiOrtu::create([
            'siswa_id'    => $siswa->id,
            'kategori'    => 'terlambat',
            'tanggal'     => '2026-08-17',
            'no_tujuan'   => '081211223344',
            'judul'       => 'Terlambat',
            'pesan'       => 'Pesan terlambat',
            'status'      => 'pending',
            'dibuat_oleh' => 'sistem',
        ]);

        $response = $this->actingAs($admin)->post("/notifikasi/{$notif->id}/reject");

        $response->assertRedirect();
        $notifFresh = $notif->fresh();
        $this->assertEquals('dibatalkan', $notifFresh->status);
    }

    public function test_batch_approve_notifikasi_massal()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa1 = Siswa::create(['nis' => '1007', 'nama' => 'Gita', 'no_hp_ortu' => '0812000001', 'status' => 'aktif']);
        $siswa2 = Siswa::create(['nis' => '1008', 'nama' => 'Hadi', 'no_hp_ortu' => '0812000002', 'status' => 'aktif']);

        $n1 = NotifikasiOrtu::create([
            'siswa_id' => $siswa1->id, 'kategori' => 'alpha', 'tanggal' => '2026-08-17',
            'no_tujuan' => '0812000001', 'judul' => 'Alpha', 'pesan' => 'Pesan 1', 'status' => 'pending',
        ]);
        $n2 = NotifikasiOrtu::create([
            'siswa_id' => $siswa2->id, 'kategori' => 'alpha', 'tanggal' => '2026-08-17',
            'no_tujuan' => '0812000002', 'judul' => 'Alpha', 'pesan' => 'Pesan 2', 'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post('/notifikasi/batch-approve', [
            'ids' => [$n1->id, $n2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('terkirim', $n1->fresh()->status);
        $this->assertEquals('terkirim', $n2->fresh()->status);
    }

    public function test_admin_bisa_update_pengaturan_gateway()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/notifikasi/pengaturan', [
            'wa_provider'                => 'fonnte',
            'wa_api_token'               => 'sample_token_fonnte_123',
            'wa_endpoint_url'            => 'https://api.fonnte.com/send',
            'is_active'                  => '1',
            'ambang_batas_alpha'         => 3,
            'hitung_bolos_bersama_alpha' => '1',
            'auto_notif_wali_kelas'      => '1',
            'template_terlambat'         => 'Template Terlambat Baru',
            'template_alpha'             => 'Template Alpha Baru',
            'template_izin'              => 'Template Izin Baru',
            'template_sakit'             => 'Template Sakit Baru',
            'template_bolos'             => 'Template Bolos Baru',
            'template_wali_kelas'        => 'Template Wali Baru',
        ]);

        $response->assertRedirect();
        $setting = PengaturanNotifikasi::first();
        $this->assertEquals('fonnte', $setting->wa_provider);
        $this->assertEquals('sample_token_fonnte_123', $setting->wa_api_token);
        $this->assertTrue($setting->is_active);
    }

    public function test_guru_piket_bisa_akses_dan_kelola_notifikasi_penuh()
    {
        $guruPiketUser = User::factory()->create(['role' => 'guru_piket']);
        $siswa = Siswa::create(['nis' => '1099', 'nama' => 'Rian', 'no_hp_ortu' => '081234567899', 'status' => 'aktif']);
        $notif = NotifikasiOrtu::create([
            'siswa_id' => $siswa->id, 'kategori' => 'terlambat', 'tanggal' => '2026-08-17',
            'no_tujuan' => '081234567899', 'judul' => 'Terlambat', 'pesan' => 'Pesan Terlambat', 'status' => 'pending',
        ]);

        // 1. Akses halaman
        $this->actingAs($guruPiketUser)->get('/notifikasi')->assertStatus(200);

        // 2. Approve
        $this->actingAs($guruPiketUser)->post("/notifikasi/{$notif->id}/approve")->assertRedirect();
        $this->assertEquals('terkirim', $notif->fresh()->status);

        // 3. Update pengaturan
        $this->actingAs($guruPiketUser)->post('/notifikasi/pengaturan', [
            'wa_provider'                => 'fonnte',
            'wa_api_token'               => 'token_piket_123',
            'wa_endpoint_url'            => 'https://api.fonnte.com/send',
            'is_active'                  => '1',
            'ambang_batas_alpha'         => 3,
            'hitung_bolos_bersama_alpha' => '1',
            'auto_notif_wali_kelas'      => '1',
            'template_terlambat'         => 'Template Terlambat',
            'template_alpha'             => 'Template Alpha',
            'template_izin'              => 'Template Izin',
            'template_sakit'             => 'Template Sakit',
            'template_bolos'             => 'Template Bolos',
            'template_wali_kelas'        => 'Template Wali',
        ])->assertRedirect();
        $this->assertEquals('token_piket_123', PengaturanNotifikasi::first()->wa_api_token);
    }
}
