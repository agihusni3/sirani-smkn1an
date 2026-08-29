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
        Schema::create('izin_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('jenis', ['sakit', 'izin', 'dinas_luar', 'pulang_cepat', 'cuti'])->default('izin');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('disetujui');
            $table->string('keterangan')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->string('disetujui_oleh')->nullable();
            $table->timestamps();

            $table->index(['guru_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_gurus');
    }
};
