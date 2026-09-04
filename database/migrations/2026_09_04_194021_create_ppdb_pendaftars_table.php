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
        Schema::create('ppdb_pendaftars', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran', 30)->unique();
            $table->string('nisn', 10)->index();
            $table->string('nik', 16)->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama')->default('Islam');
            $table->string('asal_sekolah');
            $table->string('tahun_lulus', 4)->default('2026');
            $table->text('alamat');
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_ortu')->nullable();
            $table->string('no_hp_ortu'); // WhatsApp Orang Tua
            $table->string('no_hp_siswa')->nullable();
            
            // Pilihan Jurusan
            $table->foreignId('jurusan_id_1')->constrained('jurusans')->cascadeOnDelete();
            $table->foreignId('jurusan_id_2')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('jalur_pendaftaran')->default('reguler'); // reguler, prestasi, zonasi, afirmasi
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            
            // Dokumen Unggahan
            $table->string('berkas_foto')->nullable();
            $table->string('berkas_kk')->nullable();
            $table->string('berkas_ijazah_skl')->nullable();
            
            // Status Seleksi Panitia
            $table->enum('status', ['menunggu_verifikasi', 'terverifikasi', 'diterima', 'cadangan', 'ditolak'])
                  ->default('menunggu_verifikasi')->index();
            $table->foreignId('jurusan_diterima_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->text('catatan_panitia')->nullable();
            $table->string('diverifikasi_oleh')->nullable();
            $table->timestamp('diverifikasi_pada')->nullable();
            
            // Integrasi ke SIRANI (Saat dimutasi ke Siswa Aktif)
            $table->foreignId('siswa_id')->nullable()->constrained('siswas')->nullOnDelete();
            $table->timestamp('dimutasi_pada')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppdb_pendaftars');
    }
};
