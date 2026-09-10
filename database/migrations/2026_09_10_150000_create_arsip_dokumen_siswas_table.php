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
        Schema::create('arsip_dokumen_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('pelayanan_surat_id')->nullable()->constrained('pelayanan_surats')->onDelete('set null');
            $table->foreignId('ppdb_pendaftar_id')->nullable()->constrained('ppdb_pendaftars')->onDelete('set null');
            $table->string('kategori_berkas', 50); // ijazah_smp, akta_kelahiran, kartu_keluarga, ktp_kia, kip_pip_pkh, piagam_prestasi, rapor_berkala, berkas_pkl, surat_keterangan, lainnya
            $table->string('nama_dokumen', 150);
            $table->string('nomor_dokumen', 100)->nullable();
            $table->date('tanggal_dokumen')->nullable();
            $table->string('file_path');
            $table->unsignedInteger('file_size')->nullable(); // dalam bytes
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['siswa_id', 'kategori_berkas']);
            $table->index('tanggal_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_dokumen_siswas');
    }
};
