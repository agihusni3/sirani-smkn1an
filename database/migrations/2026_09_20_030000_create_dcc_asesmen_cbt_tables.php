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
        Schema::dropIfExists('cbt_log_aktivitas');
        Schema::dropIfExists('cbt_jawaban_siswas');
        Schema::dropIfExists('cbt_peserta_ujians');
        Schema::dropIfExists('cbt_jadwal_rombels');
        Schema::dropIfExists('cbt_jadwal_ujians');
        Schema::dropIfExists('cbt_soals');
        Schema::dropIfExists('cbt_bank_soals');

        // 1. Bank Soal
        Schema::create('cbt_bank_soals', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bank', 50)->unique();
            $table->string('nama_bank');
            $table->string('mata_pelajaran');
            $table->string('tingkat', 20)->default('semua'); // 10, 11, 12, semua
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->decimal('kktp_default', 5, 2)->default(75.00);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->timestamps();

            $table->index(['guru_id', 'mata_pelajaran']);
        });

        // 2. Butir Soal
        Schema::create('cbt_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('cbt_bank_soals')->cascadeOnDelete();
            $table->integer('nomor_urut')->default(1);
            $table->enum('jenis_soal', ['pg', 'esai', 'pg_kompleks', 'isian'])->default('pg');
            $table->longText('pertanyaan');
            $table->string('media_gambar')->nullable();
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->text('kunci_jawaban')->nullable();
            $table->decimal('bobot_nilai', 5, 2)->default(1.00);
            $table->string('kode_tp', 100)->nullable(); // Tujuan Pembelajaran (Kurikulum Merdeka)
            $table->text('pembahasan')->nullable();
            $table->timestamps();

            $table->index(['bank_soal_id', 'nomor_urut']);
        });

        // 3. Jadwal & Sesi Ujian
        Schema::create('cbt_jadwal_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('cbt_bank_soals')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('parent_jadwal_id')->nullable()->constrained('cbt_jadwal_ujians')->nullOnDelete();
            $table->string('nama_ujian');
            $table->enum('tipe_ujian', ['uh', 'pts', 'pas', 'us', 'asesmen', 'remidi'])->default('uh');
            $table->boolean('is_remedial')->default(false);
            $table->enum('remedial_policy', ['cap_kktp', 'nilai_tertinggi', 'rata_rata', 'murni'])->default('cap_kktp');
            $table->decimal('kktp', 5, 2)->default(75.00);
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->integer('durasi_menit')->default(60);
            $table->string('token_ujian', 10);
            $table->boolean('acak_soal')->default(true);
            $table->boolean('acak_opsi')->default(true);
            $table->boolean('tampilkan_nilai')->default(false);
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('aktif');
            $table->timestamps();

            $table->index(['guru_id', 'status']);
            $table->index(['token_ujian', 'status']);
        });

        // 4. Pivot Jadwal Ujian ke Rombel
        Schema::create('cbt_jadwal_rombels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')->constrained('cbt_jadwal_ujians')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['jadwal_ujian_id', 'rombel_id']);
        });

        // 5. Peserta Ujian
        Schema::create('cbt_peserta_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')->constrained('cbt_jadwal_ujians')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', ['belum', 'mengerjakan', 'selesai', 'dibatalkan'])->default('belum');
            $table->decimal('nilai_pg', 5, 2)->nullable();
            $table->decimal('nilai_esai', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->boolean('is_tuntas')->nullable();
            $table->string('token_used', 10)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('jumlah_pelanggaran')->default(0);
            $table->timestamps();

            $table->unique(['jadwal_ujian_id', 'siswa_id']);
            $table->index(['jadwal_ujian_id', 'status']);
        });

        // 6. Lembar Jawaban Siswa
        Schema::create('cbt_jawaban_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_ujian_id')->constrained('cbt_peserta_ujians')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('cbt_soals')->cascadeOnDelete();
            $table->text('jawaban_siswa')->nullable();
            $table->boolean('is_ragu')->default(false);
            $table->boolean('is_benar')->nullable();
            $table->decimal('skor', 5, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['peserta_ujian_id', 'soal_id']);
        });

        // 7. Log Aktivitas / Proctoring Integrity
        Schema::create('cbt_log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_ujian_id')->constrained('cbt_peserta_ujians')->cascadeOnDelete();
            $table->string('tipe_event'); // login, jawab, ragu, tab_switch, timeout, finish
            $table->text('detail')->nullable();
            $table->dateTime('waktu_catat');
            $table->timestamps();

            $table->index(['peserta_ujian_id', 'tipe_event']);
        });

        // 8. Tambah password_cbt ke siswas jika belum ada
        if (!Schema::hasColumn('siswas', 'password_cbt')) {
            Schema::table('siswas', function (Blueprint $table) {
                $table->string('password_cbt', 20)->nullable()->after('nisn');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbt_log_aktivitas');
        Schema::dropIfExists('cbt_jawaban_siswas');
        Schema::dropIfExists('cbt_peserta_ujians');
        Schema::dropIfExists('cbt_jadwal_rombels');
        Schema::dropIfExists('cbt_jadwal_ujians');
        Schema::dropIfExists('cbt_soals');
        Schema::dropIfExists('cbt_bank_soals');

        if (Schema::hasColumn('siswas', 'password_cbt')) {
            Schema::table('siswas', function (Blueprint $table) {
                $table->dropColumn('password_cbt');
            });
        }
    }
};
