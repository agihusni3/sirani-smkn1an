<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('akademik_mata_pelajarans', 'tingkat')) {
            Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
                $table->string('tingkat', 50)->default('X,XI,XII')->after('fase');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('akademik_mata_pelajarans', 'tingkat')) {
            Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
                $table->dropColumn('tingkat');
            });
        }
    }
};
