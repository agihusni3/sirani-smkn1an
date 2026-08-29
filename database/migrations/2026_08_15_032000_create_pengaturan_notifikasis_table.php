<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('wa_provider')->default('simulasi'); // fonnte, wablas, generic_api, simulasi
            $table->string('wa_api_token')->nullable();
            $table->string('wa_endpoint_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('template_terlambat')->nullable();
            $table->text('template_alpha')->nullable();
            $table->text('template_izin')->nullable();
            $table->text('template_sakit')->nullable();
            $table->text('template_bolos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_notifikasis');
    }
};
