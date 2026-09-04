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
        Schema::create('berita_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->default('berita'); // berita, pengumuman, prestasi, agenda
            $table->text('ringkasan')->nullable();
            $table->longText('konten');
            $table->string('gambar_sampul')->nullable();
            $table->string('penulis')->default('Humas SMKN 1 Air Naningan');
            $table->unsignedInteger('dilihat')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_sekolahs');
    }
};
