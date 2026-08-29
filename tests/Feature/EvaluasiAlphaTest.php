<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use App\Services\IzinSiswaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EvaluasiAlphaTest extends TestCase
{
    use RefreshDatabase;

    public function test_perizinan_siswa_otomatis_memperbarui_absensi(): void
    {
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $siswa = Siswa::create(['nis' => '2001', 'nama' => 'Siswa Sakit', 'status' => 'aktif']);

        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'tahun_ajaran_id' => $ta->id,
            'status_keanggotaan' => 'aktif',
        ]);

        $today = Carbon::today()->toDateString();
        $service = new IzinSiswaService();
        $service->ajukanIzin($siswa->id, $today, 'sakit', 'Demam tinggi');

        $absensi = Absensi::where('pemilik_id', $siswa->id)->where('tanggal', $today)->first();
        $this->assertNotNull($absensi);
        $this->assertEquals('sakit', $absensi->status);
        $this->assertEquals('manual_izin_piket', $absensi->sumber_absen);
    }

    public function test_command_evaluasi_alpha_otomatis(): void
    {
        // Paksa tanggal ke hari kerja (Senin) agar command tidak di-skip karena weekend
        Carbon::setTestNow(Carbon::parse('next monday'));

        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);

        // Siswa 1: Hadir (sudah ada absensi)
        $siswaHadir = Siswa::create(['nis' => '3001', 'nama' => 'Siswa Rajin', 'status' => 'aktif']);
        $sr1 = SiswaRombel::create(['siswa_id' => $siswaHadir->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        $today = Carbon::today()->toDateString();
        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswaHadir->id,
            'siswa_rombel_id' => $sr1->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'sumber_absen' => 'rfid',
        ]);

        // Siswa 2: Belum scan (harus jadi Alpha)
        $siswaTanpaKeterangan = Siswa::create(['nis' => '3002', 'nama' => 'Siswa Bolos', 'status' => 'aktif']);
        SiswaRombel::create(['siswa_id' => $siswaTanpaKeterangan->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        // Jalankan Command evaluasi alpha
        $this->artisan('absensi:evaluasi-alpha')
            ->assertExitCode(0);

        // Verifikasi Siswa 2 menjadi Alpha otomatis
        $absensiAlpha = Absensi::where('pemilik_id', $siswaTanpaKeterangan->id)->where('tanggal', $today)->first();
        $this->assertNotNull($absensiAlpha);
        $this->assertEquals('alpha', $absensiAlpha->status);
        $this->assertEquals('auto_evaluasi_alpha', $absensiAlpha->sumber_absen);

        Carbon::setTestNow(); // reset
    }

    public function test_admin_dan_guru_piket_bisa_catat_izin_pulang_cepat(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin_test@test.com',
            'password' => bcrypt('password'),
        ]);

        $siswa = Siswa::create(['nis' => '4001', 'nama' => 'Siswa Pulang Cepat', 'status' => 'aktif']);
        $today = Carbon::today()->toDateString();

        // Guru Piket input izin pulang cepat
        $guru = \App\Models\Guru::create([
            'nama' => 'Guru Piket Test',
            'jabatan' => 'Guru Piket',
            'status' => 'aktif',
        ]);

        $izinService = app(\App\Services\IzinSiswaService::class);
        $izin = $izinService->ajukanIzin(
            $siswa->id,
            $today,
            'pulang_cepat',
            'Keperluan keluarga mendesak'
        );

        $this->assertDatabaseHas('izin_siswas', [
            'siswa_id' => $siswa->id,
            'jenis' => 'pulang_awal',
            'status' => 'disetujui',
        ]);
    }

    public function test_command_evaluasi_siswa_bolos_otomatis(): void
    {
        // Paksa tanggal ke hari kerja (Senin) agar command tidak di-skip karena weekend
        Carbon::setTestNow(Carbon::parse('next monday'));

        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $jurusan = Jurusan::create(['kode_jurusan' => 'TKJ', 'nama_jurusan' => 'Teknik Komputer']);
        $rombel = Rombel::create(['tahun_ajaran_id' => $ta->id, 'jurusan_id' => $jurusan->id, 'nama_rombel' => 'X TKJ 1', 'tingkat' => 'X']);
        $today = Carbon::today()->toDateString();

        // Siswa 1: Hadir pagi, tapi tidak tap pulang dan tidak ada izin piket (Bolos)
        $siswaBolos = Siswa::create(['nis' => '5001', 'nama' => 'Siswa Kabur', 'status' => 'aktif']);
        $sr1 = SiswaRombel::create(['siswa_id' => $siswaBolos->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswaBolos->id,
            'siswa_rombel_id' => $sr1->id,
            'tanggal' => $today,
            'jam_masuk' => '07:05:00',
            'jam_pulang' => null,
            'status' => 'hadir',
            'sumber_absen' => 'rfid',
        ]);

        // Siswa 2: Hadir pagi, pulang awal DENGAN izin guru piket
        $siswaIzinPulang = Siswa::create(['nis' => '5002', 'nama' => 'Siswa Izin Pulang', 'status' => 'aktif']);
        $sr2 = SiswaRombel::create(['siswa_id' => $siswaIzinPulang->id, 'rombel_id' => $rombel->id, 'tahun_ajaran_id' => $ta->id, 'status_keanggotaan' => 'aktif']);

        Absensi::create([
            'pemilik_type' => 'siswa',
            'pemilik_id' => $siswaIzinPulang->id,
            'siswa_rombel_id' => $sr2->id,
            'tanggal' => $today,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => null,
            'status' => 'hadir',
            'sumber_absen' => 'rfid',
        ]);

        \App\Models\IzinSiswa::create([
            'siswa_id' => $siswaIzinPulang->id,
            'tanggal' => $today,
            'jenis' => 'pulang_awal',
            'status' => 'disetujui',
            'keterangan' => 'Sakit kepala',
        ]);

        // Jalankan evaluasi
        $this->artisan('absensi:evaluasi-alpha')
            ->assertExitCode(0);

        // Siswa 1 harus menjadi 'bolos'
        $absensi1 = Absensi::where('pemilik_id', $siswaBolos->id)->where('tanggal', $today)->first();
        $this->assertEquals('bolos', $absensi1->status);
        $this->assertEquals('auto_evaluasi_bolos', $absensi1->sumber_absen);

        // Siswa 2 tetap 'hadir' (karena punya izin piket resmi)
        $absensi2 = Absensi::where('pemilik_id', $siswaIzinPulang->id)->where('tanggal', $today)->first();
        $this->assertEquals('hadir', $absensi2->status);

        Carbon::setTestNow(); // reset
    }

    public function test_perizinan_guru_otomatis_memperbarui_absensi(): void
    {
        $guru = \App\Models\Guru::create([
            'nama' => 'Guru Dinas Luar',
            'nip' => '198501012010011001',
            'jabatan' => 'Guru Produktif',
            'status' => 'aktif',
        ]);

        $today = Carbon::today()->toDateString();
        $service = new IzinSiswaService();
        $service->ajukanIzinGuru($guru->id, $today, 'dinas_luar', 'Pelatihan Kurikulum');

        $absensi = Absensi::where('pemilik_type', 'guru')->where('pemilik_id', $guru->id)->where('tanggal', $today)->first();
        $this->assertNotNull($absensi);
        $this->assertEquals('izin', $absensi->status);
        $this->assertEquals('manual_izin_piket', $absensi->sumber_absen);

        $izinGuru = \App\Models\IzinGuru::where('guru_id', $guru->id)->where('tanggal', $today)->first();
        $this->assertNotNull($izinGuru);
        $this->assertEquals('dinas_luar', $izinGuru->jenis);
    }

    public function test_web_perizinan_siswa_dan_guru_dengan_upload_berkas(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = \App\Models\User::create([
            'name' => 'Admin Test',
            'email' => 'admin_izin@smk.sch.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $siswa = Siswa::create(['nis' => '7001', 'nama' => 'Siswa Surat Dokter', 'status' => 'aktif']);
        $guru = \App\Models\Guru::create(['nama' => 'Guru Surat Sakit', 'status' => 'aktif']);

        $today = Carbon::today()->toDateString();
        $fileSiswa = \Illuminate\Http\UploadedFile::fake()->create('surat_dokter.pdf', 100, 'application/pdf');

        // Post Izin Siswa with file
        $response = $this->actingAs($user)->post('/izin-siswa', [
            'kategori' => 'siswa',
            'siswa_id' => $siswa->id,
            'tanggal' => $today,
            'jenis' => 'sakit',
            'keterangan' => 'Dirawat di RS',
            'file_pendukung' => $fileSiswa,
        ]);

        $response->assertSessionHas('success');
        $izinSiswa = \App\Models\IzinSiswa::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($izinSiswa->file_pendukung);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($izinSiswa->file_pendukung);

        // Post Izin Guru with file
        $fileGuru = \Illuminate\Http\UploadedFile::fake()->image('surat_dinas.jpg');
        $responseGuru = $this->actingAs($user)->post('/izin-siswa', [
            'kategori' => 'guru',
            'guru_id' => $guru->id,
            'tanggal' => $today,
            'jenis' => 'dinas_luar',
            'keterangan' => 'Workshop Provinsi',
            'file_pendukung' => $fileGuru,
        ]);

        $responseGuru->assertSessionHas('success');
        $izinGuru = \App\Models\IzinGuru::where('guru_id', $guru->id)->first();
        $this->assertNotNull($izinGuru->file_pendukung);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($izinGuru->file_pendukung);
    }
}
