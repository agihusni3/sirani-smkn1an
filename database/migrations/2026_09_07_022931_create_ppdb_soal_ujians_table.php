<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_soal_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_ujian_setting_id')->constrained('ppdb_ujian_settings')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor_urut');          // nomor asli: 1-30 PG, 31-35 Esai
            $table->enum('tipe_soal', ['pg', 'esai']);
            $table->text('pertanyaan');
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->char('kunci_jawaban', 1)->nullable();        // A/B/C/D/E hanya untuk PG
            $table->decimal('bobot_nilai', 5, 2)->default(0);   // bobot per soal
            $table->string('gambar_soal')->nullable();           // opsional
            $table->timestamps();

            $table->unique(['ppdb_ujian_setting_id', 'nomor_urut']);
        });

        // Hapus kolom PDF dari ppdb_ujian_settings (tidak dipakai lagi)
        Schema::table('ppdb_ujian_settings', function (Blueprint $table) {
            $table->dropColumn(['file_pdf_soal', 'nama_file_asli']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_soal_ujians');

        Schema::table('ppdb_ujian_settings', function (Blueprint $table) {
            $table->string('file_pdf_soal')->nullable();
            $table->string('nama_file_asli')->nullable();
        });
    }
};
