<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\SituanRapat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SituanRapatTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $guru;

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

        $this->user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->guru = Guru::create([
            'nama' => 'Drs. H. Ahmad Fauzi, M.Pd.',
            'nip' => '197501012000031001',
            'jabatan' => 'Kepala Sekolah',
            'jenis_ptk' => 'Guru',
            'status' => 'aktif',
        ]);
    }

    public function test_can_access_situan_rapat_index()
    {
        $response = $this->actingAs($this->user)->get(route('situan.rapat.index'));
        $response->assertStatus(200);
        $response->assertSee('Administrasi Rapat & Notula Dinas');
    }

    public function test_can_create_situan_rapat()
    {
        $payload = [
            'judul_rapat' => 'Rapat Pleno Pembagian Tugas Semester Ganjil',
            'tipe_rapat' => 'dinas',
            'tanggal_rapat' => now()->addDays(2)->format('Y-m-d'),
            'jam_mulai' => '08:30',
            'jam_selesai' => '12:00',
            'tempat' => 'Ruang Guru SMKN 1 Air Naningan',
            'peserta_tipe' => 'semua_gtk',
            'pimpinan_rapat_id' => $this->guru->id,
            'agenda' => 'Pembagian tugas mengajar dan jadwal piket',
        ];

        $response = $this->actingAs($this->user)->post(route('situan.rapat.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('situan_rapats', [
            'judul_rapat' => 'Rapat Pleno Pembagian Tugas Semester Ganjil',
            'pimpinan_nama' => 'Drs. H. Ahmad Fauzi, M.Pd.',
        ]);
    }

    public function test_can_update_notula_and_access_cetak_templates()
    {
        $rapat = SituanRapat::create([
            'judul_rapat' => 'Rapat Koordinasi Penilaian Akhir Semester',
            'tipe_rapat' => 'dinas',
            'tanggal_rapat' => now()->format('Y-m-d'),
            'jam_mulai' => '09:00',
            'tempat' => 'Aula Sekolah',
            'peserta_tipe' => 'semua_gtk',
            'pimpinan_rapat_id' => $this->guru->id,
            'pimpinan_nama' => $this->guru->nama,
            'pimpinan_jabatan' => $this->guru->jabatan,
            'status' => 'dijadwalkan',
            'nomor_surat' => '421.5/001/V.01/DP.2/2026',
        ]);

        // Simpan Notula
        $notulaPayload = [
            'status' => 'selesai',
            'jumlah_hadir' => 45,
            'jumlah_tidak_hadir' => 2,
            'jalannya_acara' => 'Acara dibuka dengan doa bersama dilanjutkan pemaparan kisi-kisi soal ujian.',
            'hasil_keputusan' => 'Seluruh naskah soal PAS harus diunggah maksimal H-3 pelaksanaan.',
            'tindak_lanjut' => 'Panitia menyiapkan akun token ujian siswa.',
            'catatan_khusus' => 'Bagi guru yang bertugas mengawas dilarang meninggalkan ruangan.',
        ];

        $updateResponse = $this->actingAs($this->user)
            ->post(route('situan.rapat.notula', $rapat->id), $notulaPayload);
        $updateResponse->assertRedirect();

        $this->assertDatabaseHas('situan_rapats', [
            'id' => $rapat->id,
            'status' => 'selesai',
            'jumlah_hadir' => 45,
        ]);

        // Uji Semua Template Cetak Dokumen A4
        $this->actingAs($this->user)
            ->get(route('situan.rapat.cetak.undangan', $rapat->id))
            ->assertStatus(200)
            ->assertSee('Surat Undangan Rapat Kedinasan Resmi');

        $this->actingAs($this->user)
            ->get(route('situan.rapat.cetak.daftar-hadir', $rapat->id))
            ->assertStatus(200)
            ->assertSee('DAFTAR HADIR PESERTA RAPAT');

        $this->actingAs($this->user)
            ->get(route('situan.rapat.cetak.notula', $rapat->id))
            ->assertStatus(200)
            ->assertSee('NOTULA RAPAT DINAS');

        $this->actingAs($this->user)
            ->get(route('situan.rapat.cetak.berita-acara', $rapat->id))
            ->assertStatus(200)
            ->assertSee('BERITA ACARA');

        $this->actingAs($this->user)
            ->get(route('situan.rapat.cetak.paket', $rapat->id))
            ->assertStatus(200)
            ->assertSee('DAFTAR HADIR PESERTA RAPAT');
    }
}
