<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('website_banners')->where('posisi', 'ppdb_callout')->exists();
        if (!$exists) {
            DB::table('website_banners')->insert([
                'posisi' => 'ppdb_callout',
                'posisi_teks' => 'left',
                'judul' => 'Daftar Online Mudah dari Rumah, Siap Cetak Generasi Vokasi Berkarakter',
                'subjudul' => 'Membuka 3 Jalur: Reguler (Nilai Rapor), Prestasi (Piagam Lomba & Tahfidz), dan Afirmasi (KIP / PKH). Bebas uang gedung (SPI), didukung laboratorium bengkel presisi modern, serta terhubung sertifikasi kerja resmi BNSP.',
                'badge_text' => 'GELOMBANG 1 TP 2026/2027 • BEBAS BIAYA PENDAFTARAN (100% GRATIS)',
                'tag_overlay' => 'Bebas Biaya Pendaftaran | Tanpa Uang Gedung (SPI) | Lisensi Sertifikasi BNSP | Penyaluran Kerja & Industri',
                'gambar' => null, // null defaults to public/images/web/ppdb_banner_bg.jpg
                'tombol_teks_1' => 'Isi Formulir PPDB Sekarang',
                'tombol_url_1' => '/ppdb/formulir',
                'tombol_teks_2' => 'Cek Status Seleksi',
                'tombol_url_2' => '/ppdb/status',
                'urutan' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('website_banners')->where('posisi', 'ppdb_callout')->delete();
    }
};
