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
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('jenis_kepegawaian', 30)->default('pns')->after('jabatan');
            $table->json('hari_mengajar')->nullable()->after('jenis_kepegawaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['jenis_kepegawaian', 'hari_mengajar']);
        });
    }
};
