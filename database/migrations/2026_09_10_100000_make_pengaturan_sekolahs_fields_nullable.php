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
            $table->string('npsn')->nullable()->change();
            $table->string('desa_kelurahan')->nullable()->change();
            $table->string('kecamatan')->nullable()->change();
            $table->string('kabupaten')->nullable()->change();
            $table->string('provinsi')->nullable()->change();
            $table->string('kode_pos', 10)->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('website')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_sekolahs', function (Blueprint $table) {
            $table->string('npsn')->default('70011825')->change();
            $table->string('desa_kelurahan')->default('Air Naningan')->change();
            $table->string('kecamatan')->default('Air Naningan')->change();
            $table->string('kabupaten')->default('Kab. Tanggamus')->change();
            $table->string('provinsi')->default('Lampung')->change();
            $table->string('kode_pos', 10)->default('35379')->change();
            $table->string('email')->default('smkn1airnaningan@gmail.com')->change();
            $table->string('website')->default('smkn1airnaningan.sch.id')->change();
        });
    }
};
