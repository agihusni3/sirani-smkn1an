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
        Schema::table('ppdb_pendaftars', function (Blueprint $table) {
            $table->string('penerima_pip', 10)->default('Tidak')->after('organisasi_minat');
            $table->string('nomor_pip', 50)->nullable()->after('penerima_pip');
            $table->string('berkas_pip')->nullable()->after('berkas_kip');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('penerima_pip', 10)->default('Tidak')->after('organisasi_minat');
            $table->string('nomor_pip', 50)->nullable()->after('penerima_pip');
            $table->string('berkas_pip')->nullable()->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_pendaftars', function (Blueprint $table) {
            $table->dropColumn(['penerima_pip', 'nomor_pip', 'berkas_pip']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['penerima_pip', 'nomor_pip', 'berkas_pip']);
        });
    }
};
