<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('surat_keluars', 'kode_verifikasi_qr')) {
            Schema::table('surat_keluars', function (Blueprint $table) {
                $table->string('kode_verifikasi_qr', 64)->nullable()->unique()->after('nomor_surat_lengkap');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('surat_keluars', 'kode_verifikasi_qr')) {
            Schema::table('surat_keluars', function (Blueprint $table) {
                $table->dropColumn('kode_verifikasi_qr');
            });
        }
    }
};
