<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom resource_key ke akademik_mata_pelajarans.
     *
     * resource_key = kode unik ruangan/sumber daya yang dibutuhkan mapel ini.
     * Contoh:
     *   NULL         = tidak butuh ruangan khusus (kelas biasa)
     *   'LAB_KOMPUTER' = butuh lab komputer (hanya 1 di sekolah)
     *   'LAB_IPA'      = butuh lab IPA
     *   'AULA'         = butuh aula
     *
     * Jika 2 kelas berbeda punya mapel dengan resource_key yang sama,
     * jadwal mereka TIDAK BOLEH bentrok di jam yang sama.
     */
    public function up(): void
    {
        Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
            if (!Schema::hasColumn('akademik_mata_pelajarans', 'resource_key')) {
                // Kode resource yang dibutuhkan mapel ini (nullable = kelas biasa)
                $table->string('resource_key', 50)->nullable()->after('jenis')
                    ->comment('Kode ruangan/resource terbatas, mis: LAB_KOMPUTER, LAB_IPA, AULA');
            }
        });

        // Tambah juga ke tabel slot jadwal agar bisa di-query tanpa join
        Schema::table('akademik_jadwal_pelajarans', function (Blueprint $table) {
            if (!Schema::hasColumn('akademik_jadwal_pelajarans', 'resource_key')) {
                $table->string('resource_key', 50)->nullable()->after('kegiatan_khusus')
                    ->comment('Copy dari mapel.resource_key untuk query cepat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
            $table->dropColumn('resource_key');
        });
        Schema::table('akademik_jadwal_pelajarans', function (Blueprint $table) {
            $table->dropColumn('resource_key');
        });
    }
};
