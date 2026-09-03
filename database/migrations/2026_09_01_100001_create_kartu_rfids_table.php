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
        if (!Schema::hasTable('kartu_rfids')) {
            Schema::create('kartu_rfids', function (Blueprint $table) {
                $table->id();
                $table->string('uid')->unique();
                $table->enum('pemilik_type', ['siswa', 'guru']);
                $table->unsignedBigInteger('pemilik_id');
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamp('tanggal_nonaktif')->nullable();
                $table->timestamps();

                $table->index(['pemilik_type', 'pemilik_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_rfids');
    }
};
