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
        Schema::table('pengumumans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengumumans', 'banner_gambar')) {
                $table->string('banner_gambar')->nullable()->after('isi_pesan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumumans', function (Blueprint $table) {
            if (Schema::hasColumn('pengumumans', 'banner_gambar')) {
                $table->dropColumn('banner_gambar');
            }
        });
    }
};
