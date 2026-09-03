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
        try {
            \DB::statement('DROP INDEX IF EXISTS siswas_nis_unique;');
        } catch (\Throwable $e) {}

        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'nis')) {
                $table->dropColumn('nis');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'nis')) {
                $table->string('nis')->nullable();
            }
        });
    }
};
