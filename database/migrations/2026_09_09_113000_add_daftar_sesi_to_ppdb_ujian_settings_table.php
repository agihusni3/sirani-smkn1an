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
        Schema::table('ppdb_ujian_settings', function (Blueprint $table) {
            $table->json('daftar_sesi')->nullable()->after('sesi_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_ujian_settings', function (Blueprint $table) {
            $table->dropColumn('daftar_sesi');
        });
    }
};
