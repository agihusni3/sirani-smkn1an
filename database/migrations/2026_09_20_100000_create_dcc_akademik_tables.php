<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── 1. Mata Pelajaran ──────────────────────────────────────────────
        Schema::create('akademik_mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->string('kode_mapel', 20);
            $table->string('nama_mapel');
            $table->enum('jenis', ['umum', 'kejuruan', 'pilihan', 'p5bk', 'pkl'])->default('umum');
            $table->enum('fase', ['E', 'F'])->default('E'); // E=X, F=XI-XII
            $table->string('tingkat', 50)->default('X,XI,XII'); // X, XI, XII atau kombinasi
            $table->unsignedTinyInteger('jumlah_jam_per_minggu')->default(2);
            $table->text('deskripsi_cp')->nullable(); // ringkasan capaian pembelajaran
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─── 2. Distribusi Mengajar ──────────────────────────────────────────
        Schema::create('akademik_distribusi_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('akademik_mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->tinyInteger('semester'); // 1 atau 2
            $table->unsignedTinyInteger('total_jam_per_minggu')->default(2);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['guru_id', 'mata_pelajaran_id', 'rombel_id', 'semester', 'tahun_ajaran_id'], 'distribusi_unique');
        });

        // ─── 3. Jurnal KBM Harian ────────────────────────────────────────────
        Schema::create('akademik_jurnal_kbms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('akademik_distribusi_mengajars')->cascadeOnDelete();
            $table->date('tanggal');
            $table->unsignedSmallInteger('pertemuan_ke')->default(1);
            $table->text('materi_ajar'); // topik/materi yang diajarkan
            $table->string('metode_pembelajaran')->nullable(); // ceramah, diskusi, praktik, dll
            $table->text('catatan_guru')->nullable();
            $table->text('refleksi')->nullable();
            $table->boolean('is_published')->default(false); // bisa jadi draft
            $table->timestamps();
        });

        // ─── 4. Kehadiran Siswa per Sesi KBM ────────────────────────────────
        Schema::create('akademik_kehadiran_kbms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained('akademik_jurnal_kbms')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->default('hadir');
            $table->string('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['jurnal_id', 'siswa_id']);
        });

        // ─── 5. Nilai Siswa (Formatif & Sumatif) ────────────────────────────
        Schema::create('akademik_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('akademik_distribusi_mengajars')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->tinyInteger('semester');
            $table->enum('jenis_penilaian', ['diagnostik', 'formatif', 'sumatif'])->default('formatif');
            $table->string('nama_penilaian'); // "Ulangan Harian 1", "STS Ganjil", dll
            $table->decimal('nilai', 5, 2)->default(0); // 0.00 – 100.00
            $table->text('deskripsi_capaian')->nullable();
            $table->timestamps();
        });

        // ─── 6. Leger (Nilai Akhir Per Semester) ────────────────────────────
        Schema::create('akademik_legerss', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('akademik_distribusi_mengajars')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->tinyInteger('semester');
            $table->decimal('nilai_formatif_avg', 5, 2)->default(0);
            $table->decimal('nilai_sumatif_avg', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->string('predikat', 5)->nullable(); // A/B/C/D
            $table->text('deskripsi_rapor')->nullable();
            $table->boolean('status_lulus')->default(true);
            $table->boolean('is_locked')->default(false); // dikunci setelah rapor dicetak
            $table->timestamps();
            $table->unique(['distribusi_id', 'siswa_id', 'semester']);
        });

        // ─── 7. PKL Tempat / DU-DI ──────────────────────────────────────────
        Schema::create('akademik_pkl_tempats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dudi');
            $table->string('bidang_usaha')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('nama_pembimbing_dudi')->nullable();
            $table->string('kontak_dudi', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // ─── 8. PKL Penempatan Siswa ─────────────────────────────────────────
        Schema::create('akademik_pkl_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('pkl_tempat_id')->constrained('akademik_pkl_tempats')->cascadeOnDelete();
            $table->foreignId('guru_pembimbing_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['belum_berangkat', 'aktif', 'selesai'])->default('belum_berangkat');
            $table->decimal('nilai_pkl', 5, 2)->nullable();
            $table->string('predikat_pkl', 5)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // ─── 9. P5BK Proyek ──────────────────────────────────────────────────
        Schema::create('akademik_p5bk_proyeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->tinyInteger('semester');
            $table->string('nama_proyek');
            $table->string('tema')->nullable(); // Kebekerjaan, Kewirausahaan, dll
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });

        // ─── 10. P5BK Nilai Per Siswa ────────────────────────────────────────
        Schema::create('akademik_p5bk_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p5bk_proyek_id')->constrained('akademik_p5bk_proyeks')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            // 6 Dimensi Profil Pelajar Pancasila (1-4: BB/MB/BSH/SB)
            $table->tinyInteger('beriman_bertaqwa')->default(1);
            $table->tinyInteger('berkebhinekaan_global')->default(1);
            $table->tinyInteger('bergotong_royong')->default(1);
            $table->tinyInteger('mandiri')->default(1);
            $table->tinyInteger('bernalar_kritis')->default(1);
            $table->tinyInteger('kreatif')->default(1);
            $table->tinyInteger('nilai_budaya_kerja')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['p5bk_proyek_id', 'siswa_id']);
        });

        // ─── 11. Asesmen Online — Header ─────────────────────────────────────
        Schema::create('akademik_asesmen_onlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained('akademik_distribusi_mengajars')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['kuis', 'ulangan_harian', 'pts', 'pas', 'tugas'])->default('kuis');
            $table->tinyInteger('semester');
            $table->unsignedSmallInteger('durasi_menit')->default(60);
            $table->datetime('dibuka_pada')->nullable();
            $table->datetime('ditutup_pada')->nullable();
            $table->boolean('acak_soal')->default(true);
            $table->boolean('tampilkan_nilai')->default(true); // tampilkan nilai setelah selesai
            $table->boolean('is_active')->default(false);
            $table->unsignedSmallInteger('passing_grade')->default(70);
            $table->timestamps();
        });

        // ─── 12. Asesmen Online — Butir Soal ─────────────────────────────────
        Schema::create('akademik_asesmen_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesmen_id')->constrained('akademik_asesmen_onlines')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor')->default(1);
            $table->text('pertanyaan');
            $table->string('tipe', 20)->default('pilihan_ganda'); // pilihan_ganda / essay / benar_salah
            $table->text('gambar_url')->nullable();
            // Untuk pilihan ganda
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->string('kunci_jawaban', 5)->nullable(); // A/B/C/D/E atau true/false
            $table->text('pembahasan')->nullable();
            $table->unsignedTinyInteger('bobot')->default(1);
            $table->timestamps();
        });

        // ─── 13. Asesmen Online — Hasil Siswa ────────────────────────────────
        Schema::create('akademik_asesmen_hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesmen_id')->constrained('akademik_asesmen_onlines')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->json('jawaban')->nullable(); // {soal_id: jawaban, ...}
            $table->decimal('nilai', 5, 2)->nullable();
            $table->boolean('is_selesai')->default(false);
            $table->datetime('mulai_pada')->nullable();
            $table->datetime('selesai_pada')->nullable();
            $table->unsignedSmallInteger('durasi_detik')->nullable(); // waktu pengerjaan
            $table->timestamps();
            $table->unique(['asesmen_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_asesmen_hasils');
        Schema::dropIfExists('akademik_asesmen_soals');
        Schema::dropIfExists('akademik_asesmen_onlines');
        Schema::dropIfExists('akademik_p5bk_nilais');
        Schema::dropIfExists('akademik_p5bk_proyeks');
        Schema::dropIfExists('akademik_pkl_siswas');
        Schema::dropIfExists('akademik_pkl_tempats');
        Schema::dropIfExists('akademik_legerss');
        Schema::dropIfExists('akademik_nilais');
        Schema::dropIfExists('akademik_kehadiran_kbms');
        Schema::dropIfExists('akademik_jurnal_kbms');
        Schema::dropIfExists('akademik_distribusi_mengajars');
        Schema::dropIfExists('akademik_mata_pelajarans');
    }
};
