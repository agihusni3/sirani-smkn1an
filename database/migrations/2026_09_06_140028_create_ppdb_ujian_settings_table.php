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
        Schema::create('ppdb_ujian_settings', function (Blueprint $table) {
            $table->id();
            $table->string('judul_ujian')->default('Tes Potensi Akademik & Minat Vokasi PPDB 2026');
            $table->string('tahun_ajaran')->default('2026/2027');
            $table->string('file_pdf_soal')->nullable();
            $table->string('nama_file_asli')->nullable();
            $table->integer('jumlah_soal_pg')->default(30);
            $table->integer('jumlah_soal_esai')->default(5);
            $table->json('kunci_jawaban_pg')->nullable();
            $table->decimal('bobot_pg', 5, 2)->default(70.00);
            $table->decimal('bobot_esai', 5, 2)->default(30.00);
            $table->integer('durasi_menit')->default(60);
            $table->boolean('is_active')->default(false);
            $table->text('petunjuk_ujian')->nullable();
            $table->dateTime('buka_pada')->nullable();
            $table->dateTime('tutup_pada')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppdb_ujian_settings');
    }
};
