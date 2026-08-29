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
        // 1. Tambahkan kolom poin pada kasus_disiplins jika belum ada
        Schema::table('kasus_disiplins', function (Blueprint $table) {
            $table->integer('total_poin_pelanggaran')->default(0)->after('total_terlambat');
            $table->integer('total_poin_pemulihan')->default(0)->after('total_poin_pelanggaran');
        });

        // 2. Tabel Timeline Log Kronologis Kasus Siswa (Multi-Event)
        Schema::create('kasus_disiplin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_disiplin_id')->constrained('kasus_disiplins')->onDelete('cascade');
            $table->string('tahap')->default('tahap_1_wali_kelas');
            $table->string('judul_kegiatan');
            $table->text('uraian_tindakan');
            $table->integer('poin_perubahan')->default(0); // misal +15 atau -10
            $table->string('petugas_nama');
            $table->string('petugas_role');
            $table->date('tanggal_kegiatan');
            $table->timestamps();

            $table->index(['kasus_disiplin_id', 'tanggal_kegiatan']);
        });

        // 3. Tabel Brankas Bukti Digital (Upload Scan Surat / Foto)
        Schema::create('kasus_disiplin_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_disiplin_id')->constrained('kasus_disiplins')->onDelete('cascade');
            $table->string('judul_dokumen');
            $table->enum('kategori', [
                'surat_pernyataan',
                'foto_dokumentasi',
                'berita_acara',
                'surat_dokter',
                'lainnya'
            ])->default('surat_pernyataan');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->string('diupload_oleh');
            $table->timestamps();

            $table->index(['kasus_disiplin_id', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasus_disiplin_dokumens');
        Schema::dropIfExists('kasus_disiplin_logs');
        Schema::table('kasus_disiplins', function (Blueprint $table) {
            $table->dropColumn(['total_poin_pelanggaran', 'total_poin_pemulihan']);
        });
    }
};
