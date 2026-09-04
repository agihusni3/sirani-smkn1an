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
            // Master Identitas
            $table->string('nama_lengkap')->nullable()->after('nama');
            $table->string('gelar_depan', 50)->nullable()->after('nama_lengkap');
            $table->string('gelar_belakang', 50)->nullable()->after('gelar_depan');
            $table->string('nik', 20)->nullable()->after('gelar_belakang');
            $table->string('nuptk', 25)->nullable()->after('nik');
            $table->string('tempat_lahir', 100)->nullable()->after('nuptk');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->string('agama', 30)->nullable()->after('jenis_kelamin');
            $table->text('alamat')->nullable()->after('agama');
            $table->string('id_gtk', 60)->nullable()->after('alamat');

            // Status Kepegawaian & Legalitas (Khusus SMK Negeri)
            $table->string('jenis_ptk', 60)->nullable()->after('jenis_kepegawaian'); // Guru Produktif, Guru Normatif/Adaptif, Guru BK, TU, Laboran/Toolman, Pustakawan
            $table->string('golongan_pangkat', 60)->nullable()->after('jenis_ptk'); // Penata Muda (III/a), Golongan IX (PPPK), dll.
            $table->string('nomor_sk_pengangkatan', 100)->nullable()->after('golongan_pangkat');
            $table->date('tmt_kerja')->nullable()->after('nomor_sk_pengangkatan');
            $table->string('lembaga_pengangkat', 100)->nullable()->after('tmt_kerja'); // Pemprov Lampung / Dinas Pendidikan, Kepala Sekolah

            // Kualifikasi & Kompetensi (Kesesuaian SNP)
            $table->string('pendidikan_terakhir', 40)->nullable()->after('lembaga_pengangkat');
            $table->string('jurusan_kuliah', 100)->nullable()->after('pendidikan_terakhir');
            $table->string('kampus', 120)->nullable()->after('jurusan_kuliah');
            $table->string('tahun_lulus', 10)->nullable()->after('kampus');
            $table->enum('status_sertifikasi', ['sudah', 'belum'])->default('belum')->after('tahun_lulus');
            $table->string('nomor_serdik', 80)->nullable()->after('status_sertifikasi');

            // Tugas & Beban Kerja (JJM)
            $table->string('mapel_diampu', 120)->nullable()->after('nomor_serdik');
            $table->unsignedSmallInteger('jjm')->nullable()->after('mapel_diampu');
            $table->string('tugas_tambahan', 100)->nullable()->after('jjm');
            $table->string('sk_tugas_tambahan', 100)->nullable()->after('tugas_tambahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn([
                'nama_lengkap',
                'gelar_depan',
                'gelar_belakang',
                'nik',
                'nuptk',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'agama',
                'alamat',
                'id_gtk',
                'jenis_ptk',
                'golongan_pangkat',
                'nomor_sk_pengangkatan',
                'tmt_kerja',
                'lembaga_pengangkat',
                'pendidikan_terakhir',
                'jurusan_kuliah',
                'kampus',
                'tahun_lulus',
                'status_sertifikasi',
                'nomor_serdik',
                'mapel_diampu',
                'jjm',
                'tugas_tambahan',
                'sk_tugas_tambahan',
            ]);
        });
    }
};
