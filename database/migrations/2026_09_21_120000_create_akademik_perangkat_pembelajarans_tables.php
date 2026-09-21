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
        // 1. Master Map / Folder Perangkat Ajar Guru
        Schema::create('akademik_perangkat_ajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('akademik_mata_pelajarans')->onDelete('cascade');
            $table->foreignId('distribusi_id')->nullable()->constrained('akademik_distribusi_mengajars')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->unsignedTinyInteger('semester')->default(1); // 1 = Ganjil, 2 = Genap
            $table->enum('tingkat', ['X', 'XI', 'XII'])->default('X');
            $table->string('fase', 10)->default('E'); // E untuk Kelas X, F untuk Kelas XI-XII
            $table->enum('status', ['draft', 'diajukan', 'perlu_revisi', 'disahkan'])->default('draft');
            $table->text('catatan_supervisi')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_pengesahan')->nullable();
            $table->string('qr_token_pengesahan', 64)->nullable()->unique();
            $table->unsignedSmallInteger('rpe_pekan_efektif')->default(18);
            $table->unsignedSmallInteger('rpe_pekan_cadangan')->default(2);
            $table->string('file_kaldik_rpe')->nullable();
            $table->string('file_cover_pengesahan')->nullable();
            $table->timestamps();

            $table->index(['guru_id', 'mata_pelajaran_id', 'tingkat', 'semester'], 'idx_guru_perangkat');
        });

        // 2. Butir Alur Tujuan Pembelajaran (ATP) & Target Materi
        Schema::create('akademik_atp_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perangkat_id')->constrained('akademik_perangkat_ajars')->onDelete('cascade');
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->string('kode_tp', 30); // misal "TP 1.1", "TP 1.2"
            $table->string('elemen_cp')->nullable(); // misal "Berpikir Komputasional", "Analisis Data"
            $table->text('tujuan_pembelajaran');
            $table->text('materi_pokok');
            $table->unsignedSmallInteger('alokasi_jp')->default(4);
            $table->string('profil_pancasila')->nullable(); // misal "Mandiri, Bernalar Kritis, Gotong Royong"
            $table->unsignedTinyInteger('semester')->default(1);
            $table->text('asesmen_rencana')->nullable();
            $table->timestamps();

            $table->index(['perangkat_id', 'urutan']);
        });

        // 3. Modul Ajar (MA) / RPP Merdeka & Bahan Ajar Praktik
        Schema::create('akademik_modul_ajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perangkat_id')->constrained('akademik_perangkat_ajars')->onDelete('cascade');
            $table->foreignId('atp_item_id')->nullable()->constrained('akademik_atp_items')->nullOnDelete();
            $table->string('judul_modul');
            $table->unsignedSmallInteger('pertemuan_ke_mulai')->default(1);
            $table->unsignedSmallInteger('pertemuan_ke_selesai')->default(2);
            $table->unsignedSmallInteger('alokasi_jp')->default(4);
            $table->string('model_pembelajaran')->nullable(); // PjBL, Problem-Based Learning, Teaching Factory, dll
            $table->string('metode_pembelajaran')->nullable(); // Praktik Bengkel, Diskusi, Studi Kasus
            $table->text('pemahaman_bermakna')->nullable();
            $table->text('pertanyaan_pemantik')->nullable();
            $table->text('kegiatan_pendahuluan')->nullable();
            $table->text('kegiatan_inti')->nullable();
            $table->text('kegiatan_penutup')->nullable();
            $table->text('refleksi_guru_siswa')->nullable();
            $table->string('file_modul_pdf')->nullable();
            $table->string('file_lkpd_pdf')->nullable();
            $table->string('file_jobsheet_praktik')->nullable();
            $table->string('link_media_pembelajaran', 500)->nullable();
            $table->timestamps();

            $table->index(['perangkat_id', 'atp_item_id']);
        });

        // 4. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)
        Schema::create('akademik_kktp_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perangkat_id')->constrained('akademik_perangkat_ajars')->onDelete('cascade');
            $table->foreignId('atp_item_id')->nullable()->constrained('akademik_atp_items')->nullOnDelete();
            $table->enum('pendekatan', ['interval_nilai', 'rubrik', 'deskripsi'])->default('interval_nilai');
            $table->text('keterangan_tuntas')->nullable();
            $table->text('keterangan_remedial')->nullable();
            $table->json('skala_kriteria')->nullable();
            $table->timestamps();

            $table->index(['perangkat_id', 'atp_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akademik_kktp_items');
        Schema::dropIfExists('akademik_modul_ajars');
        Schema::dropIfExists('akademik_atp_items');
        Schema::dropIfExists('akademik_perangkat_ajars');
    }
};
