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
            $table->string('berkas_ktp_ortu')->nullable()->after('berkas_ijazah_skl');
            $table->string('berkas_akta')->nullable()->after('berkas_ktp_ortu');
            $table->string('berkas_kip')->nullable()->after('berkas_akta');
            $table->string('berkas_sktm')->nullable()->after('berkas_kip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_pendaftars', function (Blueprint $table) {
            $table->dropColumn([
                'berkas_ktp_ortu',
                'berkas_akta',
                'berkas_kip',
                'berkas_sktm',
            ]);
        });
    }
};
