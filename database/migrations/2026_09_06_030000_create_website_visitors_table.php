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
        Schema::create('website_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_hash', 64)->index();
            $table->string('url', 255)->index();
            $table->string('route_name', 100)->nullable();
            $table->string('device_type', 20)->default('desktop')->index(); // mobile, desktop, tablet
            $table->string('browser', 50)->nullable();
            $table->string('referer', 255)->nullable();
            $table->date('visited_date')->index();
            $table->timestamps();

            $table->index(['visited_date', 'ip_hash']);
            $table->index(['visited_date', 'device_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_visitors');
    }
};
