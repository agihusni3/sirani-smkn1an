<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hapus permanen seluruh data siswa, riwayat presensi/absensi siswa,
     * keanggotaan rombel, dan seluruh rekaman akademik/disiplin terkait siswa.
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
                    $driver = DB::getDriverName();
                    if ($driver === 'sqlite') {
                        DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$tableName}'");
                    } elseif ($driver === 'mysql') {
                        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan jika tabel tidak memiliki sequence/auto-increment
            }
        };

        // 1. Putuskan referensi siswa_id pada PPDB agar pendaftar PPDB tetap aman
        if (Schema::hasTable('ppdb_pendaftars')) {
            try {
                DB::table('ppdb_pendaftars')->update(['siswa_id' => null]);
            } catch (\Throwable $e) {}
        }

        // 2. Kosongkan Riwayat Akademik Terkait Siswa
        $cleanTable('akademik_kehadiran_kbms');
        $cleanTable('akademik_asesmen_hasils');
        $cleanTable('akademik_nilais');
        $cleanTable('akademik_legerss');
        $cleanTable('akademik_pkl_siswas');
        $cleanTable('akademik_p5bk_nilais');

        // 3. Kosongkan Buku Kasus & Catatan Kedisiplinan Siswa
        $cleanTable('kasus_disiplin_dokumens');
        $cleanTable('kasus_disiplin_logs');
        $cleanTable('kasus_disiplin_rewards');
        $cleanTable('kasus_disiplin_pelanggarans');
        $cleanTable('kasus_disiplins');

        // 4. Kosongkan Notifikasi Ortu & Pelayanan Surat Siswa
        $cleanTable('notifikasi_ortus');
        if (Schema::hasTable('pelayanan_surats') && Schema::hasColumn('pelayanan_surats', 'siswa_id')) {
            $cleanTable('pelayanan_surats', function ($q) {
                $q->whereNotNull('siswa_id');
            });
        }

        // 5. Kosongkan Presensi Siswa & Izin Siswa (Presensi Guru/PTK tetap aman)
        $cleanTable('absensis', function ($q) {
            $q->where('pemilik_type', 'siswa')
              ->orWhere('pemilik_type', 'App\Models\Siswa')
              ->orWhereNotNull('siswa_rombel_id');
        });
        $cleanTable('izin_siswas');

        // 6. Kosongkan Kartu RFID Siswa & Arsip Siswa
        if (Schema::hasTable('kartu_rfids')) {
            $cleanTable('kartu_rfids', function ($q) {
                $q->whereIn('pemilik_type', ['siswa', 'App\Models\Siswa']);
            });
        }
        $cleanTable('arsip_dokumen_siswas');

        // 7. Kosongkan Keanggotaan Rombel & Master Data Siswa
        $cleanTable('siswa_rombels');
        $cleanTable('siswas');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operasi pembersihan bersifat permanen
    }
};
