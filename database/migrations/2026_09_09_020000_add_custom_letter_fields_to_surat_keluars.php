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
        Schema::table('surat_keluars', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_keluars', 'sifat_surat')) {
                $table->string('sifat_surat', 30)->default('Biasa')->after('perihal');
            }
            if (!Schema::hasColumn('surat_keluars', 'lampiran')) {
                $table->string('lampiran', 100)->default('-')->after('sifat_surat');
            }
            if (!Schema::hasColumn('surat_keluars', 'isi_surat')) {
                $table->longText('isi_surat')->nullable()->after('lampiran');
            }
            if (!Schema::hasColumn('surat_keluars', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan', 100)->default('Kepala Sekolah')->after('penandatangan');
            }
            if (!Schema::hasColumn('surat_keluars', 'nip_penandatangan')) {
                $table->string('nip_penandatangan', 50)->nullable()->after('jabatan_penandatangan');
            }
            if (!Schema::hasColumn('surat_keluars', 'tembusan')) {
                $table->text('tembusan')->nullable()->after('nip_penandatangan');
            }
            if (!Schema::hasColumn('surat_keluars', 'is_nomor_manual')) {
                $table->boolean('is_nomor_manual')->default(false)->after('link_cetak');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn([
                'sifat_surat',
                'lampiran',
                'isi_surat',
                'jabatan_penandatangan',
                'nip_penandatangan',
                'tembusan',
                'is_nomor_manual',
            ]);
        });
    }
};
