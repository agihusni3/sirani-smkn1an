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
        Schema::table('website_banners', function (Blueprint $table) {
            $table->string('tombol_teks_3')->nullable()->after('tombol_url_2');
            $table->string('tombol_url_3')->nullable()->after('tombol_teks_3');
        });

        // Set default tombol 3 untuk banner PPDB
        DB::table('website_banners')
            ->where('posisi', 'ppdb_callout')
            ->update([
                'tombol_teks_3' => 'Tanya Panitia PPDB',
                'tombol_url_3' => 'https://wa.me/6281234567890?text=Halo%20Panitia%20PPDB%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20bertanya%20seputar%20pendaftaran',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_banners', function (Blueprint $table) {
            $table->dropColumn(['tombol_teks_3', 'tombol_url_3']);
        });
    }
};
