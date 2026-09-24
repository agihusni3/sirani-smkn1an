<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update master tabel jurusans
        if (Schema::hasTable('jurusans')) {
            DB::table('jurusans')
                ->where('kode_jurusan', 'TSM')
                ->orWhere('nama_jurusan', 'LIKE', '%Bisnis Sepeda Motor%')
                ->update([
                    'nama_jurusan' => 'Teknik Sepeda Motor',
                    'updated_at'   => now(),
                ]);
        }

        // 2. Update ppdb_pendaftars jika ada kolom jurusan1_nama / jurusan2_nama
        if (Schema::hasTable('ppdb_pendaftars')) {
            if (Schema::hasColumn('ppdb_pendaftars', 'jurusan1_nama')) {
                DB::table('ppdb_pendaftars')
                    ->where('jurusan1_nama', 'LIKE', '%Bisnis Sepeda Motor%')
                    ->update(['jurusan1_nama' => 'Teknik Sepeda Motor']);
            }
            if (Schema::hasColumn('ppdb_pendaftars', 'jurusan2_nama')) {
                DB::table('ppdb_pendaftars')
                    ->where('jurusan2_nama', 'LIKE', '%Bisnis Sepeda Motor%')
                    ->update(['jurusan2_nama' => 'Teknik Sepeda Motor']);
            }
        }

        // 3. Update ppdb_ujian_settings jika ada materi/judul
        if (Schema::hasTable('ppdb_ujian_settings')) {
            $settings = DB::table('ppdb_ujian_settings')->get();
            foreach ($settings as $setting) {
                $updated = false;
                $data = [];

                if (isset($setting->materi_wawancara)) {
                    $materi = json_decode($setting->materi_wawancara, true);
                    if (is_array($materi) && isset($materi['kejuruan_tsm'])) {
                        $materi['kejuruan_tsm']['judul'] = 'Kesiapan Kejuruan TSM (Teknik Sepeda Motor)';
                        $data['materi_wawancara'] = json_encode($materi);
                        $updated = true;
                    }
                }

                if ($updated) {
                    DB::table('ppdb_ujian_settings')->where('id', $setting->id)->update($data);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu direverse karena TSM resmi adalah Teknik Sepeda Motor
    }
};
