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
            if (!Schema::hasColumn('ppdb_pendaftars', 'desa_kelurahan')) {
                $table->string('desa_kelurahan', 100)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('ppdb_pendaftars', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('desa_kelurahan');
            }
            if (!Schema::hasColumn('ppdb_pendaftars', 'kabupaten')) {
                $table->string('kabupaten', 100)->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('ppdb_pendaftars', 'provinsi')) {
                $table->string('provinsi', 100)->nullable()->after('kabupaten');
            }
        });

        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'desa_kelurahan')) {
                $table->string('desa_kelurahan', 100)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('siswas', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('desa_kelurahan');
            }
            if (!Schema::hasColumn('siswas', 'kabupaten')) {
                $table->string('kabupaten', 100)->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('siswas', 'provinsi')) {
                $table->string('provinsi', 100)->nullable()->after('kabupaten');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_pendaftars', function (Blueprint $table) {
            $table->dropColumn(['desa_kelurahan', 'kecamatan', 'kabupaten', 'provinsi']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['desa_kelurahan', 'kecamatan', 'kabupaten', 'provinsi']);
        });
    }
};
