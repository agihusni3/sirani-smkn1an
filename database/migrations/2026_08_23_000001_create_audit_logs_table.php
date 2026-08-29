<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi', 30); // create, update, delete, transisi, koreksi, scan, login, logout
            $table->string('modul', 50); // siswa, guru, absensi, rfid, siklus, izin, disiplin, auth, dll
            $table->string('target_type', 80)->nullable(); // nama model / kelas
            $table->unsignedBigInteger('target_id')->nullable(); // PK record yang berubah
            $table->text('deskripsi'); // teks ringkas yang bisa dibaca manusia
            $table->json('data_lama')->nullable(); // snapshot sebelum perubahan
            $table->json('data_baru')->nullable(); // snapshot sesudah perubahan
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index(['modul', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
