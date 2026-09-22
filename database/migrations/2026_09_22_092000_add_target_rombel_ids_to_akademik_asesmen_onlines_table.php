<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            if (!Schema::hasColumn('akademik_asesmen_onlines', 'target_rombel_ids')) {
                $table->json('target_rombel_ids')->nullable()->after('target_tipe');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akademik_asesmen_onlines', function (Blueprint $table) {
            if (Schema::hasColumn('akademik_asesmen_onlines', 'target_rombel_ids')) {
                $table->dropColumn('target_rombel_ids');
            }
        });
    }
};
