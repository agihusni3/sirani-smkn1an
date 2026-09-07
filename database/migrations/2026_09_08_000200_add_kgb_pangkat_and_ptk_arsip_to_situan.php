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
        // 1. Tambahkan kolom KGB & Pangkat pada tabel gurus
        Schema::table('gurus', function (Blueprint $table) {
            if (!Schema::hasColumn('gurus', 'golongan_ruang')) {
                $table->string('golongan_ruang', 30)->nullable()->after('status_kepegawaian'); // Misal: III/a, IX PPPK, GTT
            }
            if (!Schema::hasColumn('gurus', 'tmt_kgb_terakhir')) {
                $table->date('tmt_kgb_terakhir')->nullable()->after('golongan_ruang'); // Terhitung Mulai Tanggal KGB
            }
            if (!Schema::hasColumn('gurus', 'tmt_pangkat_terakhir')) {
                $table->date('tmt_pangkat_terakhir')->nullable()->after('tmt_kgb_terakhir');
            }
            if (!Schema::hasColumn('gurus', 'pendidikan_terakhir')) {
                $table->string('pendidikan_terakhir', 50)->nullable()->after('tmt_pangkat_terakhir'); // S1, S2, D3
            }
            if (!Schema::hasColumn('gurus', 'jurusan_pendidikan')) {
                $table->string('jurusan_pendidikan', 100)->nullable()->after('pendidikan_terakhir'); // Pendidikan Teknik Mesin, dll.
            }
        });

        // 2. Lemari Berkas Digital PTK (E-Arsip Kepegawaian)
        Schema::create('arsip_dokumen_ptks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->enum('kategori_berkas', [
                'ktp',
                'kk',
                'sk_cpns',
                'sk_pns',
                'sk_pppk',
                'sk_pangkat_terakhir',
                'sk_kgb_terakhir',
                'ijazah',
                'transkrip',
                'sertifikat_pendidik',
                'kartu_pegawai',
                'lainnya'
            ]);
            $table->string('nama_dokumen', 150);
            $table->string('nomor_dokumen', 100)->nullable();
            $table->date('tanggal_dokumen')->nullable();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_dokumen_ptks');

        Schema::table('gurus', function (Blueprint $table) {
            $columns = [
                'golongan_ruang',
                'tmt_kgb_terakhir',
                'tmt_pangkat_terakhir',
                'pendidikan_terakhir',
                'jurusan_pendidikan'
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('gurus', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
