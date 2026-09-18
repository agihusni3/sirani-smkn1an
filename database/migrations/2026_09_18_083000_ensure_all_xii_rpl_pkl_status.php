<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rombel = Rombel::where('nama_rombel', 'like', '%XII%RPL%')->first();
        if ($rombel) {
            $siswaIds = DB::table('siswa_rombels')
                ->where('rombel_id', $rombel->id)
                ->where('status_keanggotaan', 'aktif')
                ->pluck('siswa_id');

            if ($siswaIds->isNotEmpty()) {
                Siswa::whereIn('id', $siswaIds)->update(['status' => 'pkl']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
