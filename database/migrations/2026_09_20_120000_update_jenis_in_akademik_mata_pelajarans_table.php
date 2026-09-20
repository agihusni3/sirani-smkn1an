<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alter jenis column to allow string up to 30 chars for maximum compatibility across sqlite and mysql
        Schema::table('akademik_mata_pelajarans', function (Blueprint $table) {
            $table->string('jenis', 30)->default('umum')->change();
        });
    }

    public function down(): void
    {
        // No-op
    }
};
