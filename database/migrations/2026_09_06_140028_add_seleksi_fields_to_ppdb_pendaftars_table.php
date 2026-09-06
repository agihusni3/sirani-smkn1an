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
            $table->date('jadwal_tes_tanggal')->nullable()->after('status');
            $table->string('jadwal_tes_sesi')->nullable()->after('jadwal_tes_tanggal');
            $table->string('jadwal_tes_ruang')->nullable()->after('jadwal_tes_sesi');
            
            $table->decimal('nilai_tes_tertulis', 5, 2)->nullable()->after('jadwal_tes_ruang');
            $table->text('catatan_tes_tertulis')->nullable()->after('nilai_tes_tertulis');
            
            $table->decimal('nilai_wawancara_motivasi', 5, 2)->nullable()->after('catatan_tes_tertulis');
            $table->decimal('nilai_wawancara_karakter', 5, 2)->nullable()->after('nilai_wawancara_motivasi');
            $table->decimal('nilai_wawancara_kejuruan', 5, 2)->nullable()->after('nilai_wawancara_karakter');
            $table->decimal('nilai_wawancara_ortu', 5, 2)->nullable()->after('nilai_wawancara_kejuruan');
            $table->decimal('nilai_wawancara_total', 5, 2)->nullable()->after('nilai_wawancara_ortu');
            $table->text('catatan_wawancara')->nullable()->after('nilai_wawancara_total');
            $table->foreignId('pewawancara_id')->nullable()->after('catatan_wawancara')->constrained('users')->nullOnDelete();
            $table->dateTime('diwawancara_pada')->nullable()->after('pewawancara_id');

            $table->decimal('nilai_akhir', 5, 2)->nullable()->after('diwawancara_pada');
            $table->integer('peringkat_jurusan')->nullable()->after('nilai_akhir');
            $table->foreignId('rekomendasi_jurusan_id')->nullable()->after('peringkat_jurusan')->constrained('jurusans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppdb_pendaftars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rekomendasi_jurusan_id');
            $table->dropConstrainedForeignId('pewawancara_id');
            $table->dropColumn([
                'jadwal_tes_tanggal',
                'jadwal_tes_sesi',
                'jadwal_tes_ruang',
                'nilai_tes_tertulis',
                'catatan_tes_tertulis',
                'nilai_wawancara_motivasi',
                'nilai_wawancara_karakter',
                'nilai_wawancara_kejuruan',
                'nilai_wawancara_ortu',
                'nilai_wawancara_total',
                'catatan_wawancara',
                'diwawancara_pada',
                'nilai_akhir',
                'peringkat_jurusan',
            ]);
        });
    }
};
