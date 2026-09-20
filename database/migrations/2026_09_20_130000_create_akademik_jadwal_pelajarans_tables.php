<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kode_nomor guru (1 s/d 24) di tabel gurus jika belum ada
        if (!Schema::hasColumn('gurus', 'kode_nomor')) {
            Schema::table('gurus', function (Blueprint $table) {
                $table->unsignedSmallInteger('kode_nomor')->nullable()->after('id');
            });
        }

        // 2. Tambah singkatan_mapel di tabel akademik_mata_pelajarans jika belum ada
        if (!Schema::hasColumn('akademik_mata_pelajarans', 'singkatan_mapel')) {
            Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
                $table->string('singkatan_mapel', 30)->nullable()->after('nama_mapel');
            });
        }

        // 3. Tabel Slot Jadwal Pelajaran Mingguan
        if (!Schema::hasTable('akademik_jadwal_pelajarans')) {
            Schema::create('akademik_jadwal_pelajarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
                $table->tinyInteger('semester')->default(1);
                $table->string('hari', 15); // SENIN, SELASA, RABU, KAMIS, JUMAT, SABTU
                $table->tinyInteger('jam_ke'); // 0 s/d 12
                $table->string('pukul', 30)->nullable(); // Misal: 07.15 - 08.15
                $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
                $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
                $table->foreignId('mata_pelajaran_id')->nullable()->constrained('akademik_mata_pelajarans')->nullOnDelete();
                $table->foreignId('distribusi_id')->nullable()->constrained('akademik_distribusi_mengajars')->nullOnDelete();
                $table->string('kode_guru', 10)->nullable(); // 1, 2, 3...
                $table->string('singkatan_mapel', 30)->nullable(); // MTK, PJOK, DKK RPL...
                $table->string('kegiatan_khusus')->nullable(); // UPACARA, APEL, ISTIRAHAT, PKL
                $table->string('warna_bg', 20)->nullable();
                $table->timestamps();

                $table->unique(['tahun_ajaran_id', 'semester', 'hari', 'jam_ke', 'rombel_id'], 'slot_jadwal_unique');
            });
        }

        // 4. Tabel Jadwal Guru Piket Harian
        if (!Schema::hasTable('akademik_guru_pikets')) {
            Schema::create('akademik_guru_pikets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
                $table->tinyInteger('semester')->default(1);
                $table->string('hari', 15); // SENIN, SELASA, RABU, KAMIS, JUMAT, SABTU
                $table->foreignId('waka_piket_id')->nullable()->constrained('gurus')->nullOnDelete();
                $table->json('guru_ids')->nullable(); // Array of guru IDs
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['tahun_ajaran_id', 'semester', 'hari'], 'guru_piket_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_guru_pikets');
        Schema::dropIfExists('akademik_jadwal_pelajarans');
        if (Schema::hasColumn('akademik_mata_pelajarans', 'singkatan_mapel')) {
            Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
                $table->dropColumn('singkatan_mapel');
            });
        }
        if (Schema::hasColumn('gurus', 'kode_nomor')) {
            Schema::table('gurus', function (Blueprint $table) {
                $table->dropColumn('kode_nomor');
            });
        }
    }
};
