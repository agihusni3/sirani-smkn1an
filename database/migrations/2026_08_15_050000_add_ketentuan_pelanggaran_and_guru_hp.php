<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah no_hp pada tabel gurus
        Schema::table('gurus', function (Blueprint $table) {
            if (!Schema::hasColumn('gurus', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('jabatan');
            }
        });

        // 2. Tambah konfigurasi ambang batas pelanggaran & template notifikasi wali kelas
        Schema::table('pengaturan_notifikasis', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan_notifikasis', 'ambang_batas_alpha')) {
                $table->integer('ambang_batas_alpha')->default(3)->after('is_active');
            }
            if (!Schema::hasColumn('pengaturan_notifikasis', 'hitung_bolos_bersama_alpha')) {
                $table->boolean('hitung_bolos_bersama_alpha')->default(true)->after('ambang_batas_alpha');
            }
            if (!Schema::hasColumn('pengaturan_notifikasis', 'auto_notif_wali_kelas')) {
                $table->boolean('auto_notif_wali_kelas')->default(true)->after('hitung_bolos_bersama_alpha');
            }
            if (!Schema::hasColumn('pengaturan_notifikasis', 'template_wali_kelas')) {
                $table->text('template_wali_kelas')->nullable()->after('template_bolos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            if (Schema::hasColumn('gurus', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });

        Schema::table('pengaturan_notifikasis', function (Blueprint $table) {
            $cols = ['ambang_batas_alpha', 'hitung_bolos_bersama_alpha', 'auto_notif_wali_kelas', 'template_wali_kelas'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('pengaturan_notifikasis', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
