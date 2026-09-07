<?php

use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update Profil Sekolah Resmi
        DB::table('pengaturan_sekolahs')->update([
            'nama_kepala_sekolah' => 'Aprida, S.Si.',
            'nip_kepala_sekolah'  => '197904172008012019',
        ]);

        // 2. Update Data PTK Guru
        $guru = Guru::where('nip', '197904172008012019')
            ->orWhere('nama', 'like', '%Aprida%')
            ->orWhere('id', 1)
            ->first();

        if ($guru) {
            $guru->update([
                'nama'           => 'Aprida, S.Si.',
                'nip'            => '197904172008012019',
                'jabatan'        => 'Kepala Sekolah',
                'tugas_tambahan' => 'Kepala Sekolah',
                'jenis_ptk'      => 'Kepala Sekolah',
            ]);

            // 3. Update Akun Login Pengguna
            if ($guru->user) {
                $guru->user->update([
                    'role'  => 'kepala_sekolah',
                    'roles' => ['kepala_sekolah', 'guru'],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
