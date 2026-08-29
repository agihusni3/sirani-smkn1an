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
        // 1. Tabel Konfigurasi Global Bobot Poin & Ambang Batas
        Schema::create('pengaturan_disiplins', function (Blueprint $table) {
            $table->id();
            $table->integer('bobot_terlambat')->default(3);
            $table->integer('bobot_alpha')->default(10);
            $table->integer('bobot_bolos')->default(15);
            $table->integer('toleransi_terlambat_piket')->default(2);
            $table->integer('ambang_tahap_1_wali')->default(10);
            $table->integer('ambang_tahap_2_bk')->default(30);
            $table->integer('ambang_tahap_3_wakasis')->default(50);
            $table->integer('ambang_tahap_4_kepsek')->default(75);
            $table->integer('reward_streak_hari')->default(14);
            $table->integer('reward_streak_poin')->default(5);
            $table->timestamps();
        });

        // 2. Tabel Master Katalog Self-Reward & Aksi Pemulihan
        Schema::create('katalog_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('nama_reward');
            $table->string('kategori')->default('karakter'); // kebersihan, prestasi, karakter, konseling, kehadiran
            $table->integer('poin_deduksi')->default(5);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Pencatatan Riwayat Reward & Pengurangan Poin Siswa
        Schema::create('kasus_disiplin_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_disiplin_id')->constrained('kasus_disiplins')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('katalog_reward_id')->nullable()->constrained('katalog_rewards')->onDelete('set null');
            $table->string('nama_tindakan');
            $table->integer('poin_dikurangi')->default(5);
            $table->date('tanggal');
            $table->string('dicatat_oleh')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasus_disiplin_rewards');
        Schema::dropIfExists('katalog_rewards');
        Schema::dropIfExists('pengaturan_disiplins');
    }
};
