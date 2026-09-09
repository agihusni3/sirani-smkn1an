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
        Schema::create('ppdb_absensi_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_pendaftar_id')->constrained('ppdb_pendaftars')->cascadeOnDelete();
            $table->foreignId('ppdb_ujian_setting_id')->nullable()->constrained('ppdb_ujian_settings')->nullOnDelete();
            $table->string('no_pendaftaran')->index();
            $table->date('jadwal_tanggal');
            $table->string('sesi_ujian')->nullable();
            $table->string('ruang_ujian')->nullable();
            $table->dateTime('waktu_hadir');
            $table->string('status_kehadiran')->default('hadir'); // hadir, terlambat, izin
            $table->string('metode_presensi')->default('barcode_scanner'); // barcode_scanner, kamera_qr, manual_panitia
            $table->foreignId('petugas_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Cegah duplikasi presensi untuk pendaftar di tanggal ujian yang sama
            $table->unique(['ppdb_pendaftar_id', 'jadwal_tanggal'], 'ppdb_absensi_unique_pendaftar_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppdb_absensi_ujians');
    }
};
