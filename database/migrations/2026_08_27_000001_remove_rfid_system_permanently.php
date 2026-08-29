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
        Schema::dropIfExists('kartu_rfids');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('kartu_rfids', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->enum('tipe', ['siswa', 'guru']);
            $table->unsignedBigInteger('pemilik_id')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }
};
