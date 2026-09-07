<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $klasifikasis = [
            ['kode' => '005', 'nama' => 'Undangan Kedinasan', 'kategori' => 'Umum', 'uraian' => 'Surat undangan pertemuan, rapat dinas, dan upacara'],
            ['kode' => '420', 'nama' => 'Pendidikan (Umum)', 'kategori' => 'Pendidikan', 'uraian' => 'Urusan pendidikan secara umum'],
            ['kode' => '421.3', 'nama' => 'Sekolah Menengah Kejuruan (SMK)', 'kategori' => 'Pendidikan', 'uraian' => 'Pembinaan kelembagaan SMK, izin operasional, profil, dan akreditasi'],
            ['kode' => '421.5', 'nama' => 'Asesmen & Ujian Sekolah', 'kategori' => 'Pendidikan', 'uraian' => 'Penyelenggaraan Asesmen Nasional (ANBK), Uji Kompetensi Keahlian (UKK), dan Sumatif Akhir'],
            ['kode' => '422.1', 'nama' => 'Penerimaan Siswa Baru (PPDB)', 'kategori' => 'Kesiswaan', 'uraian' => 'Surat, pengumuman, dan berkas terkait PPDB'],
            ['kode' => '422.2', 'nama' => 'Kesiswaan & Ekstrakurikuler', 'kategori' => 'Kesiswaan', 'uraian' => 'Kegiatan OSIS, ekstrakurikuler, lomba siswa, dan pembinaan kesiswaan'],
            ['kode' => '422.3', 'nama' => 'Beasiswa & Bantuan PIP', 'kategori' => 'Kesiswaan', 'uraian' => 'Pengurusan beasiswa PIP, KIP, dan bantuan pendidikan peserta didik'],
            ['kode' => '422.4', 'nama' => 'Surat Keterangan & Mutasi Siswa', 'kategori' => 'Kesiswaan', 'uraian' => 'Surat keterangan siswa aktif, mutasi masuk/keluar, dan SKL'],
            ['kode' => '423', 'nama' => 'Kurikulum & Kalender Pendidikan', 'kategori' => 'Kurikulum', 'uraian' => 'Struktur kurikulum merdeka, kalender akademik, dan jadwal KBM'],
            ['kode' => '424', 'nama' => 'Tenaga Pengajar (Guru & Pegawai)', 'kategori' => 'Kepegawaian', 'uraian' => 'Surat tugas dinas, pelatihan, workshop, dan diklat PTK'],
            ['kode' => '425', 'nama' => 'Sarana & Prasarana Sekolah', 'kategori' => 'Sarpras', 'uraian' => 'Inventaris aset, bantuan gedung, perlengkapan lab bengkel, dan logistik'],
            ['kode' => '427', 'nama' => 'Hubungan Industri (Hubin) & PKL', 'kategori' => 'Hubin', 'uraian' => 'MoU DUDI, surat pengantar PKL/Prakerin industri, dan teaching factory'],
            ['kode' => '800', 'nama' => 'Kepegawaian (Umum)', 'kategori' => 'Kepegawaian', 'uraian' => 'Administrasi kenaikan pangkat, KGB, izin cuti, dan pensiun'],
            ['kode' => '821.2', 'nama' => 'Kenaikan Gaji Berkala (KGB)', 'kategori' => 'Kepegawaian', 'uraian' => 'Pengusulan KGB guru dan pegawai ke Dinas Pendidikan'],
            ['kode' => '823', 'nama' => 'Kenaikan Pangkat PTK', 'kategori' => 'Kepegawaian', 'uraian' => 'Pengusulan berkas kenaikan pangkat guru dan tenaga kependidikan'],
        ];

        foreach ($klasifikasis as $item) {
            DB::table('klasifikasi_surats')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('klasifikasi_surats')->truncate();
    }
};
