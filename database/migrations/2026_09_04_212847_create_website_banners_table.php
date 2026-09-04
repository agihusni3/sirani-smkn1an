<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_banners', function (Blueprint $table) {
            $table->id();
            $table->string('posisi')->default('hero_home'); // hero_home, top_bar, popup_modal
            $table->string('judul');
            $table->text('subjudul')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('gambar')->nullable();
            $table->string('tag_overlay')->nullable();
            $table->string('tombol_teks_1')->nullable();
            $table->string('tombol_url_1')->nullable();
            $table->string('tombol_teks_2')->nullable();
            $table->string('tombol_url_2')->nullable();
            $table->integer('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed banner default agar langsung tampil rapi dan siap dikelola
        DB::table('website_banners')->insert([
            'posisi' => 'hero_home',
            'judul' => 'Menempa Keahlian Teknik, Rekayasa, & Agro-Industri.',
            'subjudul' => 'SMKN 1 Air Naningan mempersiapkan lulusan berkompetensi tinggi yang siap terserap langsung di dunia industri, menguasai pengujian sertifikasi profesi resmi BNSP, serta memiliki mentalitas mandiri wirausaha.',
            'badge_text' => 'PRECISION VOCATIONAL WORKSHOP • TANGGAMUS',
            'tag_overlay' => 'SMKN 1 Air Naningan • Tanggamus, Lampung',
            'tombol_teks_1' => 'Eksplorasi 3 Kejuruan',
            'tombol_url_1' => '/kejuruan',
            'tombol_teks_2' => 'Pendaftaran PPDB 2026/2027',
            'tombol_url_2' => '/ppdb',
            'gambar' => null, // fallback ke images/web/hero_kampus.jpg
            'urutan' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_banners');
    }
};
