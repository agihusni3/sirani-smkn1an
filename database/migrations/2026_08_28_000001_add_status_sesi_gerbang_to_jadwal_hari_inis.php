<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_hari_inis', function (Blueprint $table) {
            $table->boolean('is_sesi_buka')->default(true)->after('keterangan');
            $table->string('dibuka_oleh', 100)->nullable()->after('is_sesi_buka');
            $table->dateTime('waktu_buka_sesi')->nullable()->after('dibuka_oleh');
            $table->dateTime('waktu_tutup_sesi')->nullable()->after('waktu_buka_sesi');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_hari_inis', function (Blueprint $table) {
            $table->dropColumn(['is_sesi_buka', 'dibuka_oleh', 'waktu_buka_sesi', 'waktu_tutup_sesi']);
        });
    }
};
