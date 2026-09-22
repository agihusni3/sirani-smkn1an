<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            $table->string('status_validasi', 30)->default('draft')->after('is_active'); // draft, siap_diujikan, perlu_revisi
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->foreignId('divalidasi_oleh')->nullable()->after('catatan_validasi')->constrained('users')->nullOnDelete();
            $table->datetime('divalidasi_pada')->nullable()->after('divalidasi_oleh');
            $table->string('target_tipe', 30)->default('rombel')->after('divalidasi_pada'); // rombel, siswa_terpilih
            $table->json('target_siswa_ids')->nullable()->after('target_tipe');
        });
    }

    public function down(): void
    {
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            $table->dropForeign(['divalidasi_oleh']);
            $table->dropColumn([
                'status_validasi',
                'catatan_validasi',
                'divalidasi_oleh',
                'divalidasi_pada',
                'target_tipe',
                'target_siswa_ids',
            ]);
        });
    }
};
