<?php

namespace Tests\Feature;

use App\Models\AkademikAsesmenPanitia;
use App\Models\AkademikAsesmenPeriode;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepanitiaanAsesmenTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $wakaKurikulumUser;
    protected $wakaKurikulumGuru;
    protected $guruAnggota;
    protected $guruUser;
    protected $ta;

    protected function setUp(): void
    {
        parent::setUp();

        PengaturanSekolah::create([
            'nama_sekolah' => 'SMK Negeri 1 Air Naningan',
            'npsn' => '69896000',
            'alamat' => 'Jl. Tanggamus KM 5',
            'desa_kelurahan' => 'Air Naningan',
            'kecamatan' => 'Air Naningan',
            'kabupaten_kota' => 'Tanggamus',
            'provinsi' => 'Lampung',
            'status_aktif' => true,
        ]);

        $this->ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Guru Waka Kurikulum
        $this->wakaKurikulumGuru = Guru::create([
            'nama' => 'Budi Santoso, S.Pd., M.M.',
            'nip' => '198001012005011005',
            'jabatan' => 'Wakil Kepala Sekolah Bidang Kurikulum',
            'jenis_ptk' => 'Guru',
            'status' => 'aktif',
        ]);

        $this->wakaKurikulumUser = User::factory()->create([
            'role' => 'waka_kurikulum',
            'guru_id' => $this->wakaKurikulumGuru->id,
        ]);

        // Guru Biasa
        $this->guruAnggota = Guru::create([
            'nama' => 'Siti Aminah, S.Kom.',
            'nip' => '199205122019032015',
            'jabatan' => 'Guru Mata Pelajaran TJKT',
            'jenis_ptk' => 'Guru',
            'status' => 'aktif',
        ]);

        $this->guruUser = User::factory()->create([
            'role' => 'guru',
            'guru_id' => $this->guruAnggota->id,
        ]);
    }

    public function test_waka_kurikulum_can_create_event_and_assign_committee()
    {
        // 1. Waka Kurikulum membuat event asesmen
        $response = $this->actingAs($this->wakaKurikulumUser)
            ->post(route('akademik.kepanitiaan.store'), [
                'tahun_ajaran_id' => $this->ta->id,
                'semester' => 1,
                'nama_event' => 'Sumatif Akhir Semester (SAS) Ganjil 2026/2027',
                'jenis_asesmen' => 'pas',
                'tanggal_mulai' => now()->format('Y-m-d'),
                'tanggal_selesai' => now()->addDays(7)->format('Y-m-d'),
                'sk_nomor' => '421.5/012/SK-PANITIA/V.01/DP.2/2026',
                'keterangan' => 'Pelaksanaan CBT di Lab Komputer 1-3',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('akademik_asesmen_periodes', [
            'nama_event' => 'Sumatif Akhir Semester (SAS) Ganjil 2026/2027',
            'sk_nomor' => '421.5/012/SK-PANITIA/V.01/DP.2/2026',
        ]);

        $periode = AkademikAsesmenPeriode::first();

        // 2. Waka Kurikulum menetapkan guru menjadi Proktor Utama CBT
        $assignResponse = $this->actingAs($this->wakaKurikulumUser)
            ->post(route('akademik.kepanitiaan.add-panitia', $periode->id), [
                'guru_id' => $this->guruAnggota->id,
                'peran' => 'proktor_utama',
                'tugas_khusus' => 'Proktor CBT Lab Komputer 1',
            ]);

        $assignResponse->assertRedirect();

        $this->assertDatabaseHas('akademik_asesmen_panitias', [
            'periode_id' => $periode->id,
            'guru_id' => $this->guruAnggota->id,
            'peran' => 'proktor_utama',
        ]);

        // 3. Guru yang ditunjuk otomatis memiliki peran 'panitia_asesmen'
        $availableRoles = $this->guruUser->getAvailableRoles();
        $this->assertContains('panitia_asesmen', $availableRoles);

        // 4. Set active role menjadi panitia_asesmen dan akses workspace panitia
        $this->guruUser->setActiveRole('panitia_asesmen');

        $workspaceResponse = $this->actingAs($this->guruUser)
            ->get(route('panitia-asesmen.dashboard'));
        $workspaceResponse->assertStatus(200);
        $workspaceResponse->assertSee('Pusat Komando Operasional Asesmen');

        // 5. Panitia dapat merilis token ujian baru
        $tokenResponse = $this->actingAs($this->guruUser)
            ->post(route('panitia-asesmen.generate-token'));
        $tokenResponse->assertRedirect();
        $this->assertNotNull(cache()->get('cbt_active_token'));

        // 6. Akses seluruh template dokumen resmi
        $this->actingAs($this->wakaKurikulumUser)
            ->get(route('akademik.kepanitiaan.cetak-sk', $periode->id))
            ->assertStatus(200)
            ->assertSee('SURAT KEPUTUSAN KEPALA SMK NEGERI 1 AIR NANINGAN');

        $this->actingAs($this->guruUser)
            ->get(route('panitia-asesmen.cetak-kartu', ['periode_id' => $periode->id]))
            ->assertStatus(200);

        $this->actingAs($this->guruUser)
            ->get(route('panitia-asesmen.cetak-daftar-hadir', ['periode_id' => $periode->id]))
            ->assertStatus(200)
            ->assertSee('DAFTAR HADIR PESERTA ASESMEN');

        $this->actingAs($this->guruUser)
            ->get(route('panitia-asesmen.cetak-berita-acara', ['periode_id' => $periode->id]))
            ->assertStatus(200)
            ->assertSee('BERITA ACARA PELAKSANAAN');
    }
}
