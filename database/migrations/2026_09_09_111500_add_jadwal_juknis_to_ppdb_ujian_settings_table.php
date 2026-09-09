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
            $table->date('tanggal_pelaksanaan')->nullable()->after('durasi_menit');
            $table->string('sesi_default')->default('Sesi 1 (08.00 - 10.00 WIB)')->after('tanggal_pelaksanaan');
            $table->string('ruang_default')->default('Lab Komputer SMKN 1 Air Naningan')->after('sesi_default');
            $table->string('gelombang_label')->default('1x Gelombang (Sesuai Juknis Resmi)')->after('ruang_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_ujian_settings', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pelaksanaan', 'sesi_default', 'ruang_default', 'gelombang_label']);
        });
    }
};
