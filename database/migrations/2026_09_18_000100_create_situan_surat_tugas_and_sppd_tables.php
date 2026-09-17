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
        // 1. Tabel Utama Surat Perintah Tugas (SPT)
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_keluar_id')->nullable()->constrained('surat_keluars')->nullOnDelete();
            $table->string('nomor_surat_tugas')->index();
            $table->string('kode_klasifikasi', 20)->default('094'); // 094 (Perjalanan Dinas) atau 424 (Tenaga Pengajar)
            $table->text('dasar_penugasan')->nullable(); // cth: Surat Undangan Disdikbud / Program Kerja Sekolah
            $table->text('maksud_tugas'); // Perihal / Uraian tugas kedinasan
            $table->string('tempat_berangkat')->default('Air Naningan, Tanggamus');
            $table->string('tempat_tujuan'); // Kota / Instansi tujuan (cth: Bandar Lampung)
            $table->string('lokasi_spesifik')->nullable(); // cth: Hotel Horison / BPMP Prov. Lampung
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('lama_hari')->default(1);
            $table->string('alat_transportasi')->nullable(); // Kendaraan Dinas, Kendaraan Pribadi, Angkutan Umum
            $table->string('sumber_anggaran')->nullable(); // BOS Reguler, BOPD Provinsi Lampung, Mandiri / Panitia
            $table->string('pejabat_penandatangan')->default('Kepala Sekolah');
            $table->string('nama_pejabat')->nullable();
            $table->string('nip_pejabat')->nullable();
            $table->string('pangkat_pejabat')->nullable();
            $table->string('jabatan_pejabat')->nullable();
            $table->enum('status', ['draf', 'disetujui', 'selesai', 'dibatalkan'])->default('disetujui');
            $table->string('kode_verifikasi_qr', 64)->unique();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Tabel Personil / Anggota Rombongan Tugas (Dukungan Multi-Personil)
        Schema::create('surat_tugas_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_tugas_id')->constrained('surat_tugas')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('peran')->default('Ketua Rombongan'); // Ketua Rombongan, Anggota, Pendamping
            $table->string('keterangan')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        // 3. Tabel Surat Perintah Perjalanan Dinas (SPPD) & Visum Lembar I & II
        Schema::create('sppds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_tugas_id')->constrained('surat_tugas')->cascadeOnDelete();
            $table->foreignId('surat_tugas_anggota_id')->nullable()->constrained('surat_tugas_anggotas')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->string('nomor_sppd')->index();
            $table->string('nama_pelaksana');
            $table->string('nip_pelaksana')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('tingkat_biaya')->default('Tingkat C'); // Tingkat C / Tingkat D
            $table->text('maksud_perjalanan');
            $table->string('alat_angkut')->nullable();
            $table->string('tempat_berangkat')->default('Air Naningan, Tanggamus');
            $table->string('tempat_tujuan');
            $table->integer('lama_perjalanan')->default(1);
            $table->date('tanggal_berangkat');
            $table->date('tanggal_harus_kembali');
            $table->string('instansi_pembeban_anggaran')->default('SMK Negeri 1 Air Naningan');
            $table->string('mata_anggaran')->nullable(); // Kode Rekening Anggaran
            $table->text('keterangan_lain')->nullable();
            $table->enum('status', ['terbit', 'selesai', 'batal'])->default('terbit');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppds');
        Schema::dropIfExists('surat_tugas_anggotas');
        Schema::dropIfExists('surat_tugas');
    }
};
