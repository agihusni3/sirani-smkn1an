<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasOtomatisTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_wali_kelas_tidak_perlu_switch_role(): void
    {
        $ta = TahunAjaran::create([
            'nama'      => '2025/2026 Ganjil',
            'is_active' => true,
        ]);

        $guru = Guru::create([
            'nama'    => 'Budi Santoso, S.Pd',
            'jabatan' => 'Guru Matematika',
            'status'  => 'aktif',
        ]);

        $user = User::create([
            'name'     => $guru->nama,
            'email'    => 'budi@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'guru',
            'guru_id'  => $guru->id,
        ]);

        $jurusan = \App\Models\Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $rombel = Rombel::create([
            'nama_rombel'     => 'XII RPL 1',
            'tingkat'         => '12',
            'jurusan_id'      => $jurusan->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id'   => $guru->id,
        ]);

        // 1. Wali kelas tidak boleh muncul sebagai mode terpisah di switcher
        $this->assertNotContains('wali_kelas', $user->getAvailableRoles());

        // 2. Akun guru otomatis berstatus Wali Kelas
        $this->assertTrue($user->isWaliKelas());
        $this->assertEquals([$rombel->id], $user->getWaliRombelIds());

        // 3. Langsung dapat membuka dasbor utama dan melihat widget kelas binaan
        $res = $this->actingAs($user)->get('/sirani');
        $res->assertOk();
        $res->assertSee('Ringkasan Kelas Binaan Anda:');
        $res->assertSee('XII RPL 1');

        // 4. Berwenang mengakses Buku Disiplin tanpa switch role
        $resDisiplin = $this->actingAs($user)->get('/disiplin');
        $resDisiplin->assertOk();
    }

    public function test_guru_biasa_bukan_wali_kelas(): void
    {
        $guru = Guru::create([
            'nama'    => 'Siti Aminah, S.Pd',
            'jabatan' => 'Guru Bahasa Inggris',
            'status'  => 'aktif',
        ]);

        $user = User::create([
            'name'     => $guru->nama,
            'email'    => 'siti@smkn1airnaningan.sch.id',
            'password' => bcrypt('password123'),
            'role'     => 'guru',
            'guru_id'  => $guru->id,
        ]);

        $this->assertFalse($user->isWaliKelas());
        $this->assertEmpty($user->getWaliRombelIds());
    }
}
