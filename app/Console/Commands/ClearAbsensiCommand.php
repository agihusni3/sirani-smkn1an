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
    protected $signature = 'sirani:clear-absensi {--force : Paksa hapus tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan data tabel absensi, izin siswa & guru, serta notifikasi ortu';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin mengosongkan seluruh data absensi dan izin?')) {
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

        if (Schema::hasTable('notifikasi_ortus')) {
            DB::table('notifikasi_ortus')->truncate();
            $this->line('✔ Tabel notifikasi_ortus telah dikosongkan.');
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Semua data absensi berhasil di-nol kan!');
        return 0;
    }
}
