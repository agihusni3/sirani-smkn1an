<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            if (!Schema::hasColumn('akademik_asesmen_onlines', 'target_jumlah_soal')) {
                $table->integer('target_jumlah_soal')->nullable()->default(10)->after('passing_grade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            if (Schema::hasColumn('akademik_asesmen_onlines', 'target_jumlah_soal')) {
                $table->dropColumn('target_jumlah_soal');
            }
        });
    }
};
