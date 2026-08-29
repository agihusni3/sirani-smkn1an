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
        // 1. Tabel Master Katalog Pelanggaran
        Schema::create('katalog_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggaran');
            $table->string('kategori')->default('presensi'); // presensi, tata_tertib, sikap, berat, custom
            $table->integer('poin_pelanggaran')->default(10);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Riwayat Pencatatan Pelanggaran Siswa (Manual / Katalog)
        Schema::create('kasus_disiplin_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_disiplin_id')->constrained('kasus_disiplins')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('katalog_pelanggaran_id')->nullable()->constrained('katalog_pelanggarans')->onDelete('set null');
            $table->string('nama_pelanggaran');
            $table->integer('poin_ditambah')->default(10);
            $table->date('tanggal');
            $table->string('dicatat_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasus_disiplin_pelanggarans');
        Schema::dropIfExists('katalog_pelanggarans');
    }
};
