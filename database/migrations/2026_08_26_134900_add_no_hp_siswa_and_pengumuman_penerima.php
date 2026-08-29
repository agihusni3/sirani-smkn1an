<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'no_hp_siswa')) {
                $table->string('no_hp_siswa')->nullable()->after('no_hp_ortu');
            }
        });

        Schema::table('pengumumans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengumumans', 'target_penerima_wa')) {
                $table->string('target_penerima_wa')->default('ortu')->after('kirim_wa'); // 'ortu', 'siswa', 'keduanya'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'no_hp_siswa')) {
                $table->dropColumn('no_hp_siswa');
            }
        });

        Schema::table('pengumumans', function (Blueprint $table) {
            if (Schema::hasColumn('pengumumans', 'target_penerima_wa')) {
                $table->dropColumn('target_penerima_wa');
            }
        });
    }
};
