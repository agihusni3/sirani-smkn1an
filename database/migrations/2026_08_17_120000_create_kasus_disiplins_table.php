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
        Schema::create('kasus_disiplins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->onDelete('set null');
            
            // Statistik Pelanggaran
            $table->integer('total_alpha')->default(0);
            $table->integer('total_bolos')->default(0);
            $table->integer('total_terlambat')->default(0);
            
            // Tahap Penanganan Berjenjang
            $table->enum('status_tahap', [
                'tahap_1_wali_kelas',
                'tahap_2_bk',
                'tahap_3_wakasis',
                'tahap_4_kepsek',
                'selesai_pembinaan'
            ])->default('tahap_1_wali_kelas');
            
            // Tahap 1: Wali Kelas
            $table->text('catatan_wali_kelas')->nullable();
            $table->date('tanggal_tindak_wali')->nullable();
            
            // Tahap 2: Guru BK
            $table->text('catatan_bk')->nullable();
            $table->date('tanggal_panggilan_bk')->nullable();
            $table->text('hasil_musyawarah_bk')->nullable();
            
            // Tahap 3: Waka Kesiswaan
            $table->text('catatan_wakasis')->nullable();
            $table->text('sanksi_wakasis')->nullable();
            $table->date('tanggal_sidang_wakasis')->nullable();
            
            // Tahap 4: Kepala Sekolah
            $table->text('keputusan_kepsek')->nullable();
            $table->date('tanggal_keputusan_kepsek')->nullable();
            
            $table->string('diverifikasi_oleh')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['siswa_id', 'status_tahap']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasus_disiplins');
    }
};
