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
            $table->json('materi_wawancara')->nullable()->after('petunjuk_ujian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_ujian_settings', function (Blueprint $table) {
            $table->dropColumn('materi_wawancara');
        });
    }
};
