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
        Schema::create('situan_rapats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable();
            $table->string('judul_rapat');
            $table->string('tipe_rapat')->default('dinas_guru');
            $table->date('tanggal_rapat');
            $table->string('jam_mulai')->default('08:00');
            $table->string('jam_selesai')->nullable()->default('Selesai');
            $table->string('tempat')->default('Ruang Pertemuan / Ruang Guru SMKN 1 Air Naningan');
            $table->foreignId('pimpinan_rapat_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('pimpinan_nama')->nullable();
            $table->string('pimpinan_jabatan')->nullable();
            $table->foreignId('notulis_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('notulis_nama')->nullable();
            $table->text('agenda')->nullable();
            $table->string('peserta_tipe')->default('semua_gtk');
            $table->json('peserta_ids')->nullable();
            $table->text('peserta_custom')->nullable();
            $table->text('susunan_acara')->nullable();
            $table->longText('jalannya_rapat')->nullable();
            $table->text('keputusan_rapat')->nullable();
            $table->longText('berita_acara')->nullable();
            $table->string('status')->default('dijadwalkan');
            $table->integer('jumlah_hadir')->nullable();
            $table->integer('jumlah_tidak_hadir')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal_rapat', 'tipe_rapat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('situan_rapats');
    }
};
