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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi_pesan');
            $table->string('kategori')->default('umum'); // umum, kedisiplinan, kegiatan, akademik, darurat
            $table->string('target_tipe')->default('semua'); // semua, tingkat, rombel, jurusan, alumni
            $table->string('target_id')->nullable(); // id rombel / id jurusan / 10,11,12
            $table->string('target_nama')->nullable(); // misal 'Kelas XII TSM 1' atau 'Tingkat XI'
            $table->boolean('kirim_wa')->default(true);
            $table->boolean('tampil_portal')->default(true);
            $table->boolean('tampil_kios')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('total_target')->default(0);
            $table->integer('total_terkirim')->default(0);
            $table->string('status_pengiriman')->default('draft'); // draft, proses, selesai, gagal
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
