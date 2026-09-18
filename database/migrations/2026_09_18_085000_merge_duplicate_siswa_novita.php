<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus duplikat siswa Novita Yuli Yanti (mempertahankan entri utama ID 417)
        $novitas = Siswa::where('nama', 'Novita Yuli Yanti')->orderBy('id')->get();
        if ($novitas->count() > 1) {
            $master = $novitas->first(); // ID 417
            $duplicates = $novitas->slice(1); // ID 418 dst

            foreach ($duplicates as $dup) {
                // Hapus relasi rombel dan kasus disiplin duplikat
                DB::table('siswa_rombels')->where('siswa_id', $dup->id)->delete();
                DB::table('kasus_disiplins')->where('siswa_id', $dup->id)->delete();
                $dup->delete();
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
