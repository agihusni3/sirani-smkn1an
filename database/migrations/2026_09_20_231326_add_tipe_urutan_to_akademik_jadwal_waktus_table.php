<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akademik_jadwal_waktus', function (Blueprint $table) {
            // Tipe baris: 'jam' = slot KBM, 'istirahat' = waktu istirahat, 'khusus' = kegiatan khusus (upacara dsb)
            if (!Schema::hasColumn('akademik_jadwal_waktus', 'tipe')) {
                $table->string('tipe', 20)->default('jam')->after('keterangan'); // jam | istirahat | khusus
            }
            // Label tampilan baris istirahat (bisa diubah, misal: "Istirahat Pertama", "Sholat Dzuhur", dll)
            if (!Schema::hasColumn('akademik_jadwal_waktus', 'label')) {
                $table->string('label', 80)->nullable()->after('tipe');
            }
            // Urutan tampil di dalam 1 hari (untuk sorting yang fleksibel)
            if (!Schema::hasColumn('akademik_jadwal_waktus', 'urutan')) {
                $table->smallInteger('urutan')->default(0)->after('label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akademik_jadwal_waktus', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'label', 'urutan']);
        });
    }
};
