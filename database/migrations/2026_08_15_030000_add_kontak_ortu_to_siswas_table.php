<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'nama_ortu')) {
                $table->string('nama_ortu')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('siswas', 'no_hp_ortu')) {
                $table->string('no_hp_ortu')->nullable()->after('nama_ortu');
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nama_ortu', 'no_hp_ortu']);
        });
    }
};
