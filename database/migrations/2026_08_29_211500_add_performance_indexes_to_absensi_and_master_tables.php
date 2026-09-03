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
            Schema::table('absensis', function (Blueprint $table) {
                $table->index('tanggal', 'idx_absensis_tanggal');
                $table->index(['pemilik_type', 'tanggal', 'status'], 'idx_absensis_type_tgl_status');
                $table->index(['pemilik_type', 'status', 'pemilik_id'], 'idx_absensis_type_status_id');
                $table->index(['pemilik_type', 'pemilik_id', 'tanggal'], 'idx_absensis_type_id_tgl');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('siswa_rombels', function (Blueprint $table) {
                $table->index(['status_keanggotaan', 'rombel_id', 'siswa_id'], 'idx_siswa_rombels_aktif');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('siswas', function (Blueprint $table) {
                $table->index('status', 'idx_siswas_status');
            });
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('idx_absensis_tanggal');
            $table->dropIndex('idx_absensis_type_tgl_status');
            $table->dropIndex('idx_absensis_type_status_id');
            $table->dropIndex('idx_absensis_type_id_tgl');
        });

        Schema::table('siswa_rombels', function (Blueprint $table) {
            $table->dropIndex('idx_siswa_rombels_aktif');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndex('idx_siswas_status');
        });
    }
};
