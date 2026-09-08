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
        Schema::create('arsip_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori_arsip', [
                'akreditasi',
                'izin_operasional',
                'mou_industri',
                'sertifikat_aset',
                'kurikulum_kosp',
                'pedoman_sop',
                'sk_kelembagaan',
                'lainnya'
            ])->default('lainnya');
            $table->string('nama_arsip', 200);
            $table->string('nomor_dokumen', 100)->nullable();
            $table->string('mitra_instansi', 150)->nullable(); // Khusus MoU DUDI / Lembaga Mitra
            $table->date('tanggal_dokumen')->nullable();
            $table->date('tanggal_berakhir')->nullable(); // Masa berlaku akreditasi / MoU
            $table->string('file_path');
            $table->string('file_type', 20)->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // Bytes
            $table->text('keterangan')->nullable();
            $table->foreignId('diunggah_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_sekolahs');
    }
};
