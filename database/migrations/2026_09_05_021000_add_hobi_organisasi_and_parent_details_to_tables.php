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
            $table->string('hobi')->nullable()->after('asal_sekolah');
            $table->string('organisasi_minat')->nullable()->after('hobi');
            
            // Detail Ayah
            $table->string('pekerjaan_ayah')->nullable()->after('nama_ayah');
            $table->string('pendidikan_ayah')->nullable()->after('pekerjaan_ayah');
            $table->string('no_hp_ayah')->nullable()->after('pendidikan_ayah');
            
            // Detail Ibu
            $table->string('pekerjaan_ibu')->nullable()->after('nama_ibu');
            $table->string('pendidikan_ibu')->nullable()->after('pekerjaan_ibu');
            $table->string('no_hp_ibu')->nullable()->after('pendidikan_ibu');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('hobi')->nullable()->after('asal_sekolah');
            $table->string('organisasi_minat')->nullable()->after('hobi');
            
            // Detail Ayah
            $table->string('pekerjaan_ayah')->nullable()->after('nama_ayah');
            $table->string('pendidikan_ayah')->nullable()->after('pekerjaan_ayah');
            $table->string('no_hp_ayah')->nullable()->after('pendidikan_ayah');
            
            // Detail Ibu
            $table->string('pekerjaan_ibu')->nullable()->after('nama_ibu');
            $table->string('pendidikan_ibu')->nullable()->after('pekerjaan_ibu');
            $table->string('no_hp_ibu')->nullable()->after('pendidikan_ibu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_pendaftars', function (Blueprint $table) {
            $table->dropColumn([
                'hobi',
                'organisasi_minat',
                'pekerjaan_ayah',
                'pendidikan_ayah',
                'no_hp_ayah',
                'pekerjaan_ibu',
                'pendidikan_ibu',
                'no_hp_ibu',
            ]);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'hobi',
                'organisasi_minat',
                'pekerjaan_ayah',
                'pendidikan_ayah',
                'no_hp_ayah',
                'pekerjaan_ibu',
                'pendidikan_ibu',
                'no_hp_ibu',
            ]);
        });
    }
};
