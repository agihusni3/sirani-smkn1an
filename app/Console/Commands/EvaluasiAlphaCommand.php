<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\IzinSiswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EvaluasiAlphaCommand extends Command
{
    protected $signature = 'absensi:evaluasi-alpha {tanggal?}';
    protected $description = 'Mengevaluasi siswa aktif yang belum hadir (Alpha) dan siswa bolos (tidak tap pulang tanpa izin)';

    public function handle()
    {
        $tanggal = $this->argument('tanggal') ?? Carbon::today()->toDateString();
        $this->info("Menjalankan evaluasi kehadiran & deteksi bolos otomatis untuk tanggal: {$tanggal}");

        $result = \App\Services\EvaluasiPresensiService::jalankanEvaluasi($tanggal, true);
        
        $this->info($result['message'] ?? 'Evaluasi selesai.');
        return 0;
    }
}
