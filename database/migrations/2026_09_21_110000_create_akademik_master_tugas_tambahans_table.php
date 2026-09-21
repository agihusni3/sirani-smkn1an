<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('akademik_master_tugas_tambahans')) {
            Schema::create('akademik_master_tugas_tambahans', function (Blueprint $table) {
                $table->id();
                $table->string('nama_tugas');
                $table->integer('ekuivalensi_jam')->default(2);
                $table->string('kategori')->nullable();
                $table->integer('urutan')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Seed data awal tugas tambahan standar SMK (Permendikbud 15/2018) tanpa icon
        $defaults = [
            ['nama_tugas' => 'Waka Kurikulum', 'ekuivalensi_jam' => 12, 'kategori' => 'Pimpinan', 'urutan' => 1],
            ['nama_tugas' => 'Waka Kesiswaan', 'ekuivalensi_jam' => 12, 'kategori' => 'Pimpinan', 'urutan' => 2],
            ['nama_tugas' => 'Waka Sarpras', 'ekuivalensi_jam' => 12, 'kategori' => 'Pimpinan', 'urutan' => 3],
            ['nama_tugas' => 'Waka Hubin', 'ekuivalensi_jam' => 12, 'kategori' => 'Pimpinan', 'urutan' => 4],
            ['nama_tugas' => 'Kepala Program Keahlian RPL', 'ekuivalensi_jam' => 12, 'kategori' => 'Ketua Program', 'urutan' => 5],
            ['nama_tugas' => 'Kepala Program Keahlian APHP', 'ekuivalensi_jam' => 12, 'kategori' => 'Ketua Program', 'urutan' => 6],
            ['nama_tugas' => 'Kepala Program Keahlian TSM', 'ekuivalensi_jam' => 12, 'kategori' => 'Ketua Program', 'urutan' => 7],
            ['nama_tugas' => 'Kepala Lab Komputer', 'ekuivalensi_jam' => 12, 'kategori' => 'Kepala Lab/Bengkel', 'urutan' => 8],
            ['nama_tugas' => 'Kepala Bengkel Otomotif', 'ekuivalensi_jam' => 12, 'kategori' => 'Kepala Lab/Bengkel', 'urutan' => 9],
            ['nama_tugas' => 'Kepala Lab APHP', 'ekuivalensi_jam' => 12, 'kategori' => 'Kepala Lab/Bengkel', 'urutan' => 10],
            ['nama_tugas' => 'Kepala Perpustakaan Sekolah', 'ekuivalensi_jam' => 12, 'kategori' => 'Perpustakaan', 'urutan' => 11],
            ['nama_tugas' => 'Pembina OSIS', 'ekuivalensi_jam' => 2, 'kategori' => 'Kesiswaan', 'urutan' => 12],
            ['nama_tugas' => 'Pembina Pramuka', 'ekuivalensi_jam' => 2, 'kategori' => 'Kesiswaan', 'urutan' => 13],
            ['nama_tugas' => 'Pembina PMR', 'ekuivalensi_jam' => 2, 'kategori' => 'Kesiswaan', 'urutan' => 14],
            ['nama_tugas' => 'Pembina Rohis', 'ekuivalensi_jam' => 2, 'kategori' => 'Kesiswaan', 'urutan' => 15],
            ['nama_tugas' => 'Koordinator Projek Penguatan Profil Pelajar Pancasila (P5)', 'ekuivalensi_jam' => 2, 'kategori' => 'Kurikulum', 'urutan' => 16],
            ['nama_tugas' => 'Koordinator BKK / PKL', 'ekuivalensi_jam' => 2, 'kategori' => 'Hubinmas / BKK', 'urutan' => 17],
            ['nama_tugas' => 'Guru Piket', 'ekuivalensi_jam' => 1, 'kategori' => 'Operasional', 'urutan' => 18],
        ];

        $now = now();
        foreach ($defaults as $d) {
            $exists = DB::table('akademik_master_tugas_tambahans')->where('nama_tugas', $d['nama_tugas'])->exists();
            if (!$exists) {
                DB::table('akademik_master_tugas_tambahans')->insert(array_merge($d, [
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('akademik_master_tugas_tambahans');
    }
};
