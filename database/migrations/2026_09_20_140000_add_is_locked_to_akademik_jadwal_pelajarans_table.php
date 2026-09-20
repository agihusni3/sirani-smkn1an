<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('akademik_jadwal_pelajarans') && !Schema::hasColumn('akademik_jadwal_pelajarans', 'is_locked')) {
            Schema::table('akademik_jadwal_pelajarans', function (Blueprint $table) {
                $table->boolean('is_locked')->default(false)->after('kegiatan_khusus');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('akademik_jadwal_pelajarans') && Schema::hasColumn('akademik_jadwal_pelajarans', 'is_locked')) {
            Schema::table('akademik_jadwal_pelajarans', function (Blueprint $table) {
                $table->dropColumn('is_locked');
            });
        }
    }
};
