<?php

namespace App\Console\Commands;

use App\Services\DisiplinNotificationService;
use Illuminate\Console\Command;

class IngatkanPembinaanDisiplinCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'disiplin:ingatkan-pembinaan';

    /**
     * The console command description.
     */
    protected $description = 'Mengirim pengingat WhatsApp harian otomatis (1x sehari) ke pejabat penanggung jawab kasus disiplin yang belum ditindaklanjuti';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan kasus kedisiplinan siswa yang belum ditindaklanjuti...');

        $countSent = DisiplinNotificationService::kirimPengingatHarianBelumDitangani();

        $this->info("Pengecekan selesai. {$countSent} pesan pengingat WhatsApp harian berhasil dikirimkan ke pejabat terkait.");

        return 0;
    }
}
