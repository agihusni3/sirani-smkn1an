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
        Schema::create('pengaturan_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi_atas')->default('PEMERINTAH PROVINSI LAMPUNG');
            $table->string('nama_dinas')->default('DINAS PENDIDIKAN DAN KEBUDAYAAN');
            $table->string('nama_sekolah')->default('SMK NEGERI 1 AIR NANINGAN');
            $table->string('npsn')->default('69888999');
            $table->text('alamat')->nullable();
            $table->string('desa_kelurahan')->default('Air Naningan');
            $table->string('kecamatan')->default('Air Naningan');
            $table->string('kabupaten')->default('Kab. Tanggamus');
            $table->string('provinsi')->default('Lampung');
            $table->string('kode_pos', 10)->default('35379');
            $table->string('telepon')->nullable();
            $table->string('email')->default('smkn1airnaningan@gmail.com');
            $table->string('website')->default('smkn1airnaningan.sch.id');
            $table->string('nama_kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah')->nullable();
            $table->string('logo_sekolah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sekolahs');
    }
};
