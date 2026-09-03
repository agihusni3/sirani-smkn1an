<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_hari_inis', function (Blueprint $table) {
            $table->time('jam_tutup_gerbang')->default('17:00:00')->after('jam_pulang_mulai');

        });
    }

    public function down(): void
    {
        Schema::table('jadwal_hari_inis', function (Blueprint $table) {
            $table->dropColumn('jam_tutup_gerbang');
        });
    }
};
