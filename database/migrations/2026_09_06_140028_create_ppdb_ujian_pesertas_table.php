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
        Schema::create('ppdb_ujian_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_pendaftar_id')->constrained('ppdb_pendaftars')->cascadeOnDelete();
            $table->foreignId('ppdb_ujian_setting_id')->nullable()->constrained('ppdb_ujian_settings')->nullOnDelete();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->json('jawaban_pg')->nullable();
            $table->json('jawaban_esai')->nullable();
            $table->integer('jumlah_pg_benar')->default(0);
            $table->integer('jumlah_pg_salah')->default(0);
            $table->integer('jumlah_pg_kosong')->default(0);
            $table->decimal('nilai_pg', 5, 2)->default(0.00);
            $table->json('nilai_per_nomor_esai')->nullable();
            $table->decimal('nilai_esai', 5, 2)->default(0.00);
            $table->decimal('nilai_total_tertulis', 5, 2)->default(0.00);
            $table->string('status_pengerjaan')->default('belum_mulai');
            $table->text('catatan_koreksi_esai')->nullable();
            $table->foreignId('diperiksa_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diperiksa_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppdb_ujian_pesertas');
    }
};
