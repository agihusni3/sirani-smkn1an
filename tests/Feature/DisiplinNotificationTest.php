<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\KasusDisiplin;
use App\Models\Jurusan;
use App\Models\NotifikasiOrtu;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisiplinNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Guru $waliGuru;
    protected Guru $bkGuru;
    protected Guru $wakasisGuru;
    protected Siswa $siswa;
    protected Rombel $rombel;
    protected TahunAjaran $ta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ta = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'RPL', 'nama_jurusan' => 'Rekayasa Perangkat Lunak']);

        $this->waliGuru = Guru::create([
            'nip'     => '198501012010011001',
            'nama'    => 'Budi Wali, S.Pd',
            'jabatan' => 'Wali Kelas',
            'no_hp'   => '081234567801',
        ]);

        $this->bkGuru = Guru::create([
            'nip'     => '198602022010022002',
            'nama'    => 'Dewi BK, S.Psi',
            'jabatan' => 'Guru Bimbingan Konseling',
            'no_hp'   => '081234567802',
        ]);

        $this->wakasisGuru = Guru::create([
            'nip'     => '198003032005011003',
            'nama'    => 'Drs. Hendra Wakasis',
            'jabatan' => 'Waka Kesiswaan',
            'no_hp'   => '081234567803',
        ]);

        $this->rombel = Rombel::create([
            'nama_rombel'     => 'X RPL 1',
            'tingkat'         => 10,
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $this->ta->id,
            'wali_kelas_id'   => $this->waliGuru->id,
        ]);

        $this->siswa = Siswa::create([
            'nis'            => '9010',
            'nama'           => 'Rian Siswa Uji',
            'status'         => 'aktif',
            'nama_ortu'      => 'Bpk. Ahmad Rian',
            'no_hp_ortu'     => '081299990001',
        ]);

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
    }

    public function test_notifikasi_wa_terkirim_saat_kasus_didaftarkan(): void
    {
        $res = $this->actingAs($this->admin)->post('/disiplin', [
            'siswa_id'     => $this->siswa->id,
            'status_tahap' => 'tahap_1_wali_kelas',
            'catatan'      => 'Siswa sering datang terlambat.',
        ]);

        $res->assertRedirect();

        // Cek bahwa pesan ke Wali Kelas terbuat dan terkirim
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id'  => $this->siswa->id,
            'no_tujuan' => $this->waliGuru->no_hp,
            'kategori'  => 'eskalasi_disiplin_internal',
        ]);

        // Cek bahwa pemberitahuan ke Orang Tua terbuat
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id'  => $this->siswa->id,
            'no_tujuan' => $this->siswa->no_hp_ortu,
            'kategori'  => 'pemberitahuan_disiplin_ortu',
        ]);
    }

    public function test_notifikasi_wa_terkirim_ke_guru_bk_saat_eskalasi_ke_tahap_2(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_1_wali_kelas',
        ]);

        $res = $this->actingAs($this->admin)->post("/disiplin/{$kasus->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_2_bk',
            'catatan_tindakan'  => 'Pelimpahan kasus ke BK untuk panggilan orang tua.',
        ]);

        $res->assertRedirect();

        // Cek bahwa notifikasi otomatis terkirim ke Guru BK
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id'  => $this->siswa->id,
            'no_tujuan' => $this->bkGuru->no_hp,
            'kategori'  => 'eskalasi_disiplin_internal',
        ]);
    }

    public function test_notifikasi_wa_terkirim_ke_wakasis_saat_eskalasi_ke_tahap_3(): void
    {
        $kasus = KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
        ]);

        $res = $this->actingAs($this->admin)->post("/disiplin/{$kasus->id}/tindak-lanjut", [
            'status_tahap_baru' => 'tahap_3_wakasis',
            'catatan_tindakan'  => 'Siswa melanggar SP 2 BK, diajukan sidang kesiswaan.',
            'sanksi_tambahan'   => 'Skorsing 3 hari',
        ]);

        $res->assertRedirect();

        // Cek bahwa notifikasi otomatis terkirim ke Waka Kesiswaan
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id'  => $this->siswa->id,
            'no_tujuan' => $this->wakasisGuru->no_hp,
            'kategori'  => 'eskalasi_disiplin_internal',
        ]);
    }

    public function test_command_pengingat_harian_mengirim_pesan_ke_kasus_belum_ditangani(): void
    {
        // Buat kasus di Tahap 2 BK yang belum ditangani (tanggal_panggilan_bk kosong)
        KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
            'catatan_bk'      => null,
            'tanggal_panggilan_bk' => null,
            'hasil_musyawarah_bk'  => null,
        ]);

        $this->artisan('disiplin:ingatkan-pembinaan')
            ->expectsOutputToContain('Pengecekan selesai')
            ->assertExitCode(0);

        // Pengingat terkirim ke Guru BK
        $this->assertDatabaseHas('notifikasi_ortus', [
            'siswa_id'  => $this->siswa->id,
            'no_tujuan' => $this->bkGuru->no_hp,
            'kategori'  => 'pengingat_disiplin_harian_tahap_2_bk',
        ]);
    }

    public function test_pengingat_harian_otomatis_berhenti_saat_kasus_sudah_ditangani(): void
    {
        // Kasus sudah ditangani oleh BK (tanggal_panggilan_bk & hasil_musyawarah terisi)
        KasusDisiplin::create([
            'siswa_id'        => $this->siswa->id,
            'tahun_ajaran_id' => $this->ta->id,
            'status_tahap'    => 'tahap_2_bk',
            'tanggal_panggilan_bk' => '2026-08-17',
            'hasil_musyawarah_bk'  => 'Orang tua telah hadir dan menandatangani komitmen.',
        ]);

        $this->artisan('disiplin:ingatkan-pembinaan')
            ->expectsOutputToContain('0 pesan pengingat')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('notifikasi_ortus', [
            'siswa_id' => $this->siswa->id,
            'kategori' => 'pengingat_disiplin_harian_tahap_2_bk',
        ]);
    }
}
