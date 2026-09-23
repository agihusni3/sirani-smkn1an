<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Hapus riwayat absensi siswa dan buku kasus siswa:
     * - Absensi harian siswa (absensi guru/PTK tetap aman)
     * - Izin siswa (izin_siswas)
     * - Presensi KBM kelas (akademik_kehadiran_kbms)
     * - Notifikasi orang tua terkait presensi/kasus (notifikasi_ortus)
     * - Seluruh buku kasus kedisiplinan siswa (kasus_disiplins + pelanggaran, reward, dokumen, log)
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $clean = function (string $table, ?\Closure $where = null): void {
            if (!Schema::hasTable($table)) {
                return;
            }
            try {
                $q = DB::table($table);
                if ($where) {
                    $where($q);
                }
                $q->delete();

                // Reset Auto-Increment bila seluruh isi tabel dihapus
                $driver = DB::getDriverName();
                if (!$where) {
                    if ($driver === 'mysql') {
                        DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                    } elseif ($driver === 'sqlite') {
                        DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$table}'");
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan jika tabel tidak mendukung reset sequence
            }
        };

        // 1. Hapus Riwayat Absensi Siswa (Presensi Guru/PTK tetap utuh & aman)
        $clean('absensis', function ($q) {
            $q->where(function ($sub) {
                $sub->where('pemilik_type', 'siswa')
                    ->orWhere('pemilik_type', 'App\\Models\\Siswa')
                    ->orWhereNotNull('siswa_rombel_id');
            });
        });

        // 2. Hapus Riwayat Izin Siswa
        $clean('izin_siswas');

        // 3. Hapus Kehadiran KBM di kelas
        $clean('akademik_kehadiran_kbms');

        // 4. Hapus Riwayat Notifikasi Orang Tua
        $clean('notifikasi_ortus');

        // 5. Hapus Seluruh Buku Kasus & Catatan Kedisiplinan Siswa
        $clean('kasus_disiplin_dokumens');
        $clean('kasus_disiplin_logs');
        $clean('kasus_disiplin_rewards');
        $clean('kasus_disiplin_pelanggarans');
        $clean('kasus_disiplins');

        // 6. Bersihkan Berkas Fisik Bukti Dokumen Kasus & Surat Izin (jika ada)
        try {
            if (Storage::disk('public')->exists('kasus_disiplin')) {
                Storage::disk('public')->deleteDirectory('kasus_disiplin');
            }
            if (Storage::disk('public')->exists('dokumen_kasus')) {
                Storage::disk('public')->deleteDirectory('dokumen_kasus');
            }
        } catch (\Throwable $e) {
            // Abaikan jika ada permission error filesystem
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pembersihan bersifat permanen
    }
};
