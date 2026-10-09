<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akademik_asesmen_periodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->tinyInteger('semester')->default(1);
            $table->string('nama_event'); // Misal: STS Ganjil 2026/2027, SAS Ganjil 2026/2027
            $table->enum('jenis_asesmen', ['pts', 'pas', 'pat', 'us', 'anbk', 'ukk', 'lainnya'])->default('pas');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('sk_nomor')->nullable(); // Nomor SK Kepanitiaan
            $table->date('sk_tanggal')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('akademik_asesmen_panitias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('akademik_asesmen_periodes')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->enum('peran', [
                'penanggung_jawab', // Kepala Sekolah
                'pengarah',         // Waka Kurikulum
                'ketua',
                'sekretaris',
                'bendahara',
                'proktor_utama',
                'teknisi',
                'koordinator_soal',
                'pengawas',
                'anggota'
            ])->default('anggota');
            $table->string('tugas_khusus')->nullable(); // Misal: Proktor Lab Komputer 1, Penggandaan Naskah
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['periode_id', 'guru_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_asesmen_panitias');
        Schema::dropIfExists('akademik_asesmen_periodes');
    }
};
