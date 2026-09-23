<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bersihkan riwayat:
     *  - Absensi siswa (bukan absensi guru/PTK)
     *  - Izin siswa
     *  - Notifikasi orang tua (notifikasi_ortus)
     *  - Push subscription (push_subscriptions) — reset agar HP ortu perlu daftar ulang
     *  - Buku kasus (kasus_disiplins + turunannya)
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $clean = function (string $table, ?\Closure $where = null): void {
            if (! Schema::hasTable($table)) {
                return;
            }
            try {
                $q = DB::table($table);
                if ($where) {
                    $where($q);
                }
                $q->delete();

                // Reset auto-increment
                $driver = DB::getDriverName();
                if (! $where) {
                    if ($driver === 'mysql') {
                        DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                    } elseif ($driver === 'sqlite') {
                        DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$table}'");
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan tabel tanpa auto-increment atau error minor
            }
        };

        // ── 1. Riwayat Absensi Siswa (absensi guru/PTK tetap aman) ──────────
        $clean('absensis', function ($q) {
            $q->where(function ($qq) {
                $qq->where('pemilik_type', 'siswa')
                   ->orWhere('pemilik_type', 'App\Models\Siswa')
                   ->orWhereNotNull('siswa_rombel_id');
            });
        });

        // ── 2. Izin Siswa ────────────────────────────────────────────────────
        $clean('izin_siswas');

        // ── 3. Riwayat Notifikasi Orang Tua ──────────────────────────────────
        $clean('notifikasi_ortus');

        // ── 4. Push Subscription (reset pendaftaran HP) ─────────────────────
        $clean('push_subscriptions');

        // ── 5. Buku Kasus & Kedisiplinan ─────────────────────────────────────
        $clean('kasus_disiplin_dokumens');
        $clean('kasus_disiplin_logs');
        $clean('kasus_disiplin_rewards');
        $clean('kasus_disiplin_pelanggarans');
        $clean('kasus_disiplins');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Operasi pembersihan bersifat permanen (tidak dapat di-rollback).
     */
    public function down(): void
    {
        //
    }
};
