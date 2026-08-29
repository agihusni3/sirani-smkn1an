<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_hari_inis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->time('jam_masuk_toleransi')->default('07:15:00');
            $table->time('jam_pulang_mulai')->default('15:30:00');
            $table->string('keterangan')->default('Jadwal Reguler');
            $table->string('diubah_oleh')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_hari_inis');
    }
};
