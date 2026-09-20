<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('akademik_jadwal_waktus')) {
            Schema::create('akademik_jadwal_waktus', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
                $table->tinyInteger('semester')->default(1);
                $table->string('hari', 15); // SENIN, SELASA, RABU, KAMIS, JUMAT, SABTU
                $table->tinyInteger('jam_ke'); // 0 s/d 12
                $table->string('pukul', 30); // Contoh: 07.15 - 08.15
                $table->string('keterangan', 50)->nullable();
                $table->timestamps();

                $table->unique(['tahun_ajaran_id', 'semester', 'hari', 'jam_ke'], 'jadwal_waktu_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_jadwal_waktus');
    }
};
