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
        // 1. Klasifikasi Surat Standar Dinas Pendidikan
        Schema::create('klasifikasi_surats', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // Contoh: 421.3, 422.1, 800
            $table->string('nama', 150);          // Contoh: Pembinaan SMK, Kesiswaan
            $table->string('kategori', 50)->default('Pendidikan'); // Pendidikan, Umum, Kepegawaian
            $table->text('uraian')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Buku Agenda Surat Masuk
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_agenda')->index(); // Auto urut tahunan
            $table->year('tahun_agenda')->index();
            $table->string('nomor_surat_asal', 100);
            $table->string('pengirim', 150); // Instansi / Organisasi pengirim
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->string('perihal', 255);
            $table->enum('tingkat_urgensi', ['biasa', 'penting', 'segera', 'rahasia'])->default('biasa');
            $table->string('file_lampiran')->nullable(); // Path PDF / gambar scan
            $table->enum('status_disposisi', ['menunggu', 'didisposisi', 'selesai'])->default('menunggu');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 3. Lembar Disposisi Elektronik Kepala Sekolah
        Schema::create('disposisi_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->onDelete('cascade');
            $table->foreignId('pemberi_user_id')->nullable()->constrained('users')->onDelete('set null'); // Kepsek
            $table->foreignId('penerima_user_id')->constrained('users')->onDelete('cascade'); // Pejabat/Guru tujuan
            $table->json('instruksi_flags')->nullable(); // Array: ['tindak_lanjuti', 'hadiri', 'pelajari', 'arsipkan']
            $table->text('catatan_kepsek')->nullable();
            $table->date('batas_waktu')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_selesai')->default(false);
            $table->timestamp('selesai_at')->nullable();
            $table->text('laporan_tindak_lanjut')->nullable();
            $table->string('file_tindak_lanjut')->nullable();
            $table->timestamps();
        });

        // 4. Buku Agenda Surat Keluar & Generator Nomor
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_agenda')->index();
            $table->year('tahun_agenda')->index();
            $table->foreignId('klasifikasi_id')->nullable()->constrained('klasifikasi_surats')->onDelete('set null');
            $table->string('kode_klasifikasi', 30); // Caching kode misal 421.3
            $table->string('nomor_surat_lengkap', 120)->unique(); // Misal: 025/421.3/SMKN1AN/IX/2026
            $table->string('tujuan_surat', 200); // Kepada Yth...
            $table->string('perihal', 255);
            $table->date('tanggal_surat');
            $table->string('penandatangan', 150)->default('Kepala Sekolah');
            $table->enum('jenis_surat', ['umum', 'suket_siswa', 'surat_tugas', 'rekomendasi_mutasi', 'sk_kepsek', 'lainnya'])->default('umum');
            $table->string('file_arsip')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 5. Buku Register SK (Surat Keputusan) Kepala Sekolah
        Schema::create('buku_sk_kepseks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_urut_sk')->index();
            $table->year('tahun_sk')->index();
            $table->string('nomor_sk_lengkap', 120)->unique();
            $table->string('tentang_sk', 255); // Judul SK
            $table->date('tanggal_ditetapkan');
            $table->string('kategori_sk', 80)->default('Umum'); // KBM, Panitia PPDB, Kelulusan, dll.
            $table->string('file_dokumen')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Pelayanan Surat Kesiswaan Mandiri Ber-QR Code
        Schema::create('pelayanan_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_keluar_id')->nullable()->constrained('surat_keluars')->onDelete('set null');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->enum('jenis_pelayanan', [
                'suket_aktif',
                'suket_berkelakuan_baik',
                'suket_mutasi_keluar',
                'suket_skl',
                'suket_pengantar_pkl'
            ]);
            $table->string('keperluan', 255); // Misal: Pengurusan BPJS Kesehatan, Beasiswa PIP, Tunjangan Gaji
            $table->string('kode_verifikasi_qr', 64)->unique(); // Hash unik anti-pemalsuan
            $table->boolean('is_valid')->default(true);
            $table->json('payload_snapshot')->nullable(); // Snapshot data siswa saat surat dicetak
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelayanan_surats');
        Schema::dropIfExists('buku_sk_kepseks');
        Schema::dropIfExists('surat_keluars');
        Schema::dropIfExists('disposisi_surats');
        Schema::dropIfExists('surat_masuks');
        Schema::dropIfExists('klasifikasi_surats');
    }
};
