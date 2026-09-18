<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Reset seluruh data siswa, keanggotaan rombel, riwayat presensi siswa,
     * antrean notifikasi ortu, dan buku kasus / kedisiplinan.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $cleanTable = function ($tableName, $whereCallback = null) {
            if (!Schema::hasTable($tableName)) {
                return;
            }

            try {
                if ($whereCallback) {
                    $query = DB::table($tableName);
                    $whereCallback($query);
                    $query->delete();
                } else {
                    DB::table($tableName)->delete();
                    if (DB::getDriverName() === 'sqlite') {
                        DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$tableName}'");
                    } elseif (DB::getDriverName() === 'mysql') {
                        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");
                    }
                }
            } catch (\Throwable $e) {
                // Log atau lanjutkan jika tabel terkendala
            }
        };

        // 1. Putuskan referensi siswa_id pada PPDB agar data pendaftar PPDB tetap aman
        if (Schema::hasTable('ppdb_pendaftars')) {
            DB::table('ppdb_pendaftars')->update(['siswa_id' => null]);
        }

        // 2. Kosongkan Buku Kasus & Catatan Kedisiplinan Siswa
        $cleanTable('kasus_disiplin_logs');
        $cleanTable('kasus_disiplin_dokumens');
        $cleanTable('kasus_disiplin_pelanggarans');
        $cleanTable('kasus_disiplin_rewards');
        $cleanTable('kasus_disiplins');

        // 3. Kosongkan Notifikasi Orang Tua
        $cleanTable('notifikasi_ortus');

        // 4. Kosongkan Data Presensi & Izin Siswa (Presensi Guru/PTK tetap aman)
        $cleanTable('absensis', function ($q) {
            $q->where('pemilik_type', 'siswa');
        });
        $cleanTable('izin_siswas');

        // 5. Kosongkan Kartu RFID Siswa & Berkas Siswa
        $cleanTable('kartu_rfids', function ($q) {
            $q->where('tipe_pemilik', 'siswa');
        });
        $cleanTable('arsip_dokumen_siswas');
        $cleanTable('pelayanan_surats');

        // 6. Kosongkan Keanggotaan Rombel & Master Data Siswa
        $cleanTable('siswa_rombels');
        $cleanTable('siswas');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operasi reset bersifat permanen
    }
};
