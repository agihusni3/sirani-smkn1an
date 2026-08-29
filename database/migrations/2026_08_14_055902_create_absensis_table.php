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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->enum('pemilik_type', ['siswa', 'guru']);
            $table->unsignedBigInteger('pemilik_id');
            $table->foreignId('siswa_rombel_id')->nullable()->constrained('siswa_rombels')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'alpha', 'sakit', 'izin', 'bolos'])->default('hadir');
            $table->string('sumber_absen')->default('rfid');
            $table->timestamps();

            $table->unique(['pemilik_type', 'pemilik_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
