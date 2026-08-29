<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_ortus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->string('kategori', 50)->default('terlambat');
            $table->date('tanggal');
            $table->string('no_tujuan');
            $table->string('nama_ortu')->nullable();
            $table->string('judul');
            $table->text('pesan');
            $table->enum('status', ['pending', 'diverifikasi', 'terkirim', 'gagal', 'dibatalkan'])->default('pending');
            $table->string('dibuat_oleh')->default('sistem');
            $table->string('diverifikasi_oleh')->nullable();
            $table->timestamp('waktu_verifikasi')->nullable();
            $table->timestamp('waktu_kirim')->nullable();
            $table->text('catatan_error')->nullable();
            $table->timestamps();

            $table->index(['tanggal', 'kategori', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_ortus');
    }
};
