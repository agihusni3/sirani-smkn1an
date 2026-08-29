<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_ortus', function (Blueprint $table) {
            $table->string('status_validasi_kepsek', 50)->default('menunggu_validasi')->after('status_pembinaan');
            $table->string('nama_kepsek_validasi')->nullable()->after('status_validasi_kepsek');
            $table->timestamp('waktu_validasi_kepsek')->nullable()->after('nama_kepsek_validasi');
            $table->text('catatan_kepala_sekolah')->nullable()->after('waktu_validasi_kepsek');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_ortus', function (Blueprint $table) {
            $table->dropColumn([
                'status_validasi_kepsek',
                'nama_kepsek_validasi',
                'waktu_validasi_kepsek',
                'catatan_kepala_sekolah',
            ]);
        });
    }
};
