<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAbsensiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sirani:clear-absensi 
                            {--force : Paksa hapus tanpa konfirmasi}
                            {--with-kasus : Ikut kosongkan seluruh rekaman buku kasus disiplin & poin pembinaan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan data tabel absensi, izin siswa & guru, notifikasi ortu, serta buku kasus disiplin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin mengosongkan seluruh data absensi dan izin' . ($this->option('with-kasus') ? ' beserta buku kasus' : '') . '?')) {
            $this->info('Operasi dibatalkan.');
            return 0;
        }

        $this->info('Mengosongkan data absensi...');

        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('absensis')) {
            DB::table('absensis')->truncate();
            $this->line('✔ Tabel absensis telah dikosongkan.');
        }

        if (Schema::hasTable('izin_siswas')) {
            DB::table('izin_siswas')->truncate();
            $this->line('✔ Tabel izin_siswas telah dikosongkan.');
        }

        if (Schema::hasTable('izin_gurus')) {
            DB::table('izin_gurus')->truncate();
            $this->line('✔ Tabel izin_gurus telah dikosongkan.');
        }

        if (Schema::hasTable('akademik_kehadiran_kbms')) {
            DB::table('akademik_kehadiran_kbms')->truncate();
            $this->line('✔ Tabel akademik_kehadiran_kbms telah dikosongkan.');
        }

        if (Schema::hasTable('notifikasi_ortus')) {
            DB::table('notifikasi_ortus')->truncate();
            $this->line('✔ Tabel notifikasi_ortus telah dikosongkan.');
        }

        if ($this->option('with-kasus')) {
            $this->info('Mengosongkan rekaman buku kasus disiplin...');

            $kasusTables = [
                'kasus_disiplin_dokumens',
                'kasus_disiplin_logs',
                'kasus_disiplin_pelanggarans',
                'kasus_disiplin_rewards',
                'kasus_disiplins',
            ];

            foreach ($kasusTables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line("✔ Tabel {$table} telah dikosongkan.");
                }
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Semua data absensi' . ($this->option('with-kasus') ? ' dan buku kasus' : '') . ' berhasil di-nol kan!');
        return 0;
    }
}
