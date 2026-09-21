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
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            $table->text('tujuan_pembelajaran')->nullable()->after('deskripsi');
            $table->string('token_ujian', 10)->nullable()->after('passing_grade');
            $table->boolean('acak_opsi')->default(true)->after('acak_soal');
            $table->boolean('anti_cheat_mode')->default(true)->after('token_ujian');
            $table->boolean('wajib_fullscreen')->default(true)->after('anti_cheat_mode');
            $table->boolean('blokir_copy_paste')->default(true)->after('wajib_fullscreen');
            $table->unsignedTinyInteger('max_toleransi_keluar')->default(3)->after('blokir_copy_paste');
            $table->boolean('tampilkan_pembahasan')->default(false)->after('tampilkan_nilai');
        });

        Schema::table('akademik_asesmen_hasils', function (Blueprint $table) {
            $table->unsignedTinyInteger('jumlah_pelanggaran')->default(0)->after('durasi_detik');
            $table->json('log_pelanggaran')->nullable()->after('jumlah_pelanggaran');
            $table->string('status_kejujuran', 25)->default('jujur')->after('log_pelanggaran');
            $table->text('catatan_pengawas')->nullable()->after('status_kejujuran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akademik_asesmen_hasils', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pelanggaran', 'log_pelanggaran', 'status_kejujuran', 'catatan_pengawas']);
        });

        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            $table->dropColumn([
                'tujuan_pembelajaran', 'token_ujian', 'acak_opsi', 'anti_cheat_mode',
                'wajib_fullscreen', 'blokir_copy_paste', 'max_toleransi_keluar', 'tampilkan_pembahasan'
            ]);
        });
    }
};
