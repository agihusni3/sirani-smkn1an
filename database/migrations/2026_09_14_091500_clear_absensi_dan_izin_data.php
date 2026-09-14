<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('absensis')) {
            DB::table('absensis')->truncate();
        }

        if (Schema::hasTable('izin_siswas')) {
            DB::table('izin_siswas')->truncate();
        }

        if (Schema::hasTable('izin_gurus')) {
            DB::table('izin_gurus')->truncate();
        }

        if (Schema::hasTable('notifikasi_ortus')) {
            DB::table('notifikasi_ortus')->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operasi truncate tidak dapat di-rollback otomatis
    }
};
