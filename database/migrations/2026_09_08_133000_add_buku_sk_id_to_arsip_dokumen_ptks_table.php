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
        Schema::table('arsip_dokumen_ptks', function (Blueprint $table) {
            $table->foreignId('buku_sk_id')->nullable()->after('guru_id')->constrained('buku_sk_kepseks')->onDelete('cascade');
        });

        // Ubah kolom kategori_berkas menjadi string/varchar agar fleksibel untuk penugasan sekolah tanpa limitasi enum lama
        Schema::table('arsip_dokumen_ptks', function (Blueprint $table) {
            $table->string('kategori_berkas', 60)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip_dokumen_ptks', function (Blueprint $table) {
            $table->dropForeign(['buku_sk_id']);
            $table->dropColumn('buku_sk_id');
        });
    }
};
