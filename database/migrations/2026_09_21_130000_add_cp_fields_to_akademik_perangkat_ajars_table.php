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
        Schema::table('akademik_perangkat_ajars', function (Blueprint $table) {
            $table->text('capaian_pembelajaran')->nullable()->after('catatan_guru');
            $table->text('rasional_tujuan')->nullable()->after('capaian_pembelajaran');
            $table->json('elemen_cp')->nullable()->after('rasional_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akademik_perangkat_ajars', function (Blueprint $table) {
            $table->dropColumn(['capaian_pembelajaran', 'rasional_tujuan', 'elemen_cp']);
        });
    }
};
