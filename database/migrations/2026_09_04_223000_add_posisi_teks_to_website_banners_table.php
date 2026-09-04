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
        Schema::table('website_banners', function (Blueprint $table) {
            $table->string('posisi_teks', 20)->default('left')->after('posisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_banners', function (Blueprint $table) {
            $table->dropColumn('posisi_teks');
        });
    }
};
