<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDataSiswaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sirani:reset-siswa-data {--force : Paksa reset tanpa dialog konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan seluruh data siswa, keanggotaan rombel, riwayat presensi siswa, notifikasi, dan buku kasus';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('PERINGATAN: Seluruh data master siswa, keanggotaan kelas, absensi siswa, antrean notifikasi, dan buku kasus akan DIHAPUS. Lanjutkan?')) {
            $this->info('Operasi dibatalkan.');
            return 0;
        }

        $this->warn('Memulai pengosongan data siswa & riwayat terkait...');

        Schema::disableForeignKeyConstraints();

        $cleanTable = function ($tableName, $label, $whereCallback = null) {
            if (!Schema::hasTable($tableName)) {
                return;
            }

            try {
                if ($whereCallback) {
                    $query = DB::table($tableName);
                    $whereCallback($query);
                    $count = $query->count();
                    $query->delete();
                } else {
                    $count = DB::table($tableName)->count();
                    DB::table($tableName)->delete();
                    if (DB::getDriverName() === 'sqlite') {
                        DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$tableName}'");
                    } elseif (DB::getDriverName() === 'mysql') {
                        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");
                    }
                }
                $this->line(" ✔ {$label}: {$count} baris dibersihkan.");
            } catch (\Throwable $e) {
                $this->error(" ✖ Gagal membersihkan {$tableName}: " . $e->getMessage());
            }
        };

        // 1. PPDB reference safe
        if (Schema::hasTable('ppdb_pendaftars')) {
            DB::table('ppdb_pendaftars')->update(['siswa_id' => null]);
            $this->line(" ✔ Referensi siswa_id pada PPDB dilepas.");
        }

        // 2. Buku Kasus & Disiplin
        $cleanTable('kasus_disiplin_logs', 'Log Kasus Disiplin');
        $cleanTable('kasus_disiplin_dokumens', 'Dokumen Kasus Disiplin');
        $cleanTable('kasus_disiplin_pelanggarans', 'Pelanggaran Siswa');
        $cleanTable('kasus_disiplin_rewards', 'Reward Siswa');
        $cleanTable('kasus_disiplins', 'Buku Kasus Disiplin');

        // 3. Notifikasi Ortu
        $cleanTable('notifikasi_ortus', 'Antrean & Riwayat Notifikasi WhatsApp');

        // 4. Presensi & Izin Siswa
        $cleanTable('absensis', 'Presensi Siswa', function ($q) {
            $q->where('pemilik_type', 'siswa');
        });
        $cleanTable('izin_siswas', 'Surat Izin Siswa');

        // 5. Kartu RFID & Berkas Siswa
        $cleanTable('kartu_rfids', 'Kartu RFID Siswa', function ($q) {
            $q->where('tipe_pemilik', 'siswa');
        });
        $cleanTable('arsip_dokumen_siswas', 'Arsip Dokumen Siswa');
        $cleanTable('pelayanan_surats', 'Pelayanan Surat Siswa');

        // 6. Rombel & Siswa
        $cleanTable('siswa_rombels', 'Keanggotaan Rombel Siswa');
        $cleanTable('siswas', 'Master Data Siswa');

        Schema::enableForeignKeyConstraints();

        $this->info(' Pengosongan data selesai. Sistem siap untuk impor data siswa baru.');

        return 0;
    }
}
