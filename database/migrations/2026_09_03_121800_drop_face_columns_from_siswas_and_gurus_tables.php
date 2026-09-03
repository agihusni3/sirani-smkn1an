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
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'face_embedding')) {
                $table->dropColumn('face_embedding');
            }
            if (Schema::hasColumn('siswas', 'face_registered_at')) {
                $table->dropColumn('face_registered_at');
            }
        });

        Schema::table('gurus', function (Blueprint $table) {
            if (Schema::hasColumn('gurus', 'face_embedding')) {
                $table->dropColumn('face_embedding');
            }
            if (Schema::hasColumn('gurus', 'face_registered_at')) {
                $table->dropColumn('face_registered_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->text('face_embedding')->nullable();
            $table->timestamp('face_registered_at')->nullable();
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->text('face_embedding')->nullable();
            $table->timestamp('face_registered_at')->nullable();
        });
    }
};
