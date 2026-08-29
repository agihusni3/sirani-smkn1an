<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->longText('face_embedding')->nullable()->after('foto');
            $table->timestamp('face_registered_at')->nullable()->after('face_embedding');
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->longText('face_embedding')->nullable()->after('foto');
            $table->timestamp('face_registered_at')->nullable()->after('face_embedding');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['face_embedding', 'face_registered_at']);
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['face_embedding', 'face_registered_at']);
        });
    }
};
