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
        Schema::table('kasus_disiplin_dokumens', function (Blueprint $table) {
            $table->string('tahap', 50)->nullable()->default('tahap_1_wali_kelas')->after('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kasus_disiplin_dokumens', function (Blueprint $table) {
            $table->dropColumn('tahap');
        });
    }
};
