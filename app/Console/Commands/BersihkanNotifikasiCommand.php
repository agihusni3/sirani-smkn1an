<?php

namespace App\Console\Commands;

use App\Models\NotifikasiOrtu;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BersihkanNotifikasiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sirani:bersihkan-notif {--all : Batalkan seluruh draf pending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan antrean draf notifikasi usang atau kehadiran rutin normal dari database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        $query = NotifikasiOrtu::where('status', 'pending');
        if (!$this->option('all')) {
            $query->where(function ($q) use ($today) {
                $q->whereIn('kategori', ['masuk', 'pulang'])
                  ->orWhereDate('tanggal', '<', $today);
            });
        }

        $affected = $query->update([
            'status'           => 'dibatalkan',
            'catatan_error'    => 'Dibersihkan otomatis: draf kadaluarsa / anomali normal',
            'waktu_verifikasi' => now(),
        ]);

        $this->info("✔ Berhasil membersihkan {$affected} draf notifikasi dari antrean!");
        return Command::SUCCESS;
    }
}
