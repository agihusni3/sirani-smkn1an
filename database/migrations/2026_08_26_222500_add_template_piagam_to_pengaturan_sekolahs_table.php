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
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->string('template_piagam')->nullable()->after('logo_sekolah');
            $table->text('template_piagam_config')->nullable()->after('template_piagam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->dropColumn(['template_piagam', 'template_piagam_config']);
        });
    }
};
