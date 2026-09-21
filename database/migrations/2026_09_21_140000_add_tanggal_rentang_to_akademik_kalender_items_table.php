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
        Schema::table('akademik_kalender_items', function (Blueprint $table) {
            if (!Schema::hasColumn('akademik_kalender_items', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable()->after('minggu_ke_semester');
            }
            if (!Schema::hasColumn('akademik_kalender_items', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akademik_kalender_items', function (Blueprint $table) {
            if (Schema::hasColumn('akademik_kalender_items', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }
            if (Schema::hasColumn('akademik_kalender_items', 'tanggal_mulai')) {
                $table->dropColumn('tanggal_mulai');
            }
        });
    }
};
