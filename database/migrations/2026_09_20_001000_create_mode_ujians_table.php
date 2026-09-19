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
        Schema::create('mode_ujians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ujian');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_masuk_toleransi')->default('07:15:00');
            $table->time('jam_pulang_mulai')->default('11:30:00');
            $table->time('jam_tutup_gerbang')->default('17:00:00');
            $table->json('panitia_guru_ids')->nullable();
            $table->boolean('nonaktifkan_piket_reguler')->default(true);
            $table->boolean('is_aktif')->default(true);
            $table->string('keterangan')->nullable();
            $table->string('diubah_oleh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mode_ujians');
    }
};
