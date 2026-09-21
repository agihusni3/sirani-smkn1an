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
        if (!Schema::hasTable('akademik_kalenders')) {
            Schema::create('akademik_kalenders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
                $table->unsignedTinyInteger('semester')->default(1); // 1: Ganjil, 2: Genap
                $table->unsignedSmallInteger('total_pekan')->default(25);
                $table->unsignedSmallInteger('pekan_efektif')->default(18);
                $table->unsignedSmallInteger('pekan_cadangan')->default(2);
                $table->text('catatan')->nullable();
                $table->boolean('is_locked')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['tahun_ajaran_id', 'semester'], 'akademik_kalender_ta_smt_unique');
            });
        }

        if (!Schema::hasTable('akademik_kalender_items')) {
            Schema::create('akademik_kalender_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('akademik_kalender_id')->constrained('akademik_kalenders')->cascadeOnDelete();
                $table->string('bulan', 20); // Juli, Agustus, dst.
                $table->unsignedTinyInteger('minggu_ke')->default(1); // 1 s/d 5 dalam bulan bersangkutan
                $table->unsignedTinyInteger('minggu_ke_semester')->default(1); // 1 s/d 26 urut sepanjang semester
                $table->string('jenis', 20)->default('efektif'); // 'efektif' / 'non_efektif'
                $table->string('kategori', 30)->default('kbm'); // 'kbm', 'mpls', 'sts', 'sas', 'sat', 'ukk', 'rapor', 'libur', 'pkl', 'lainnya'
                $table->string('keterangan')->nullable();
                $table->string('warna', 30)->nullable();
                $table->timestamps();

                $table->index(['akademik_kalender_id', 'minggu_ke_semester'], 'kalender_item_smt_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akademik_kalender_items');
        Schema::dropIfExists('akademik_kalenders');
    }
};
