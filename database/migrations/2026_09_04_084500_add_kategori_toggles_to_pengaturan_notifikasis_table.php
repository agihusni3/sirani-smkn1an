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
        Schema::table('pengaturan_notifikasis', function (Blueprint $table) {
            $table->boolean('notif_terlambat_aktif')->default(true)->after('auto_notif_wali_kelas');
            $table->boolean('notif_alpha_aktif')->default(true)->after('notif_terlambat_aktif');
            $table->boolean('notif_bolos_aktif')->default(true)->after('notif_alpha_aktif');
            $table->boolean('notif_izin_aktif')->default(true)->after('notif_bolos_aktif');
            $table->boolean('notif_panggilan_aktif')->default(true)->after('notif_izin_aktif');
            $table->boolean('notif_masuk_aktif')->default(false)->after('notif_panggilan_aktif'); // default false agar tidak spam
            $table->boolean('notif_pulang_aktif')->default(false)->after('notif_masuk_aktif'); // default false agar tidak spam
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_notifikasis', function (Blueprint $table) {
            $table->dropColumn([
                'notif_terlambat_aktif',
                'notif_alpha_aktif',
                'notif_bolos_aktif',
                'notif_izin_aktif',
                'notif_panggilan_aktif',
                'notif_masuk_aktif',
                'notif_pulang_aktif',
            ]);
        });
    }
};
