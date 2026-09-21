<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan seluruh mata pelajaran berstatus aktif (is_active = 1)
        // Mencegah mapel hilang dari dropdown distribusi jadwal
        DB::table('akademik_mata_pelajarans')->update(['is_active' => 1]);

        // 2. Pastikan tahun_ajaran_id tidak null dan terhubung ke Tahun Ajaran aktif
        $ta = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::first();
        if ($ta) {
            DB::table('akademik_mata_pelajarans')
                ->whereNull('tahun_ajaran_id')
                ->update(['tahun_ajaran_id' => $ta->id]);
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback
    }
};
