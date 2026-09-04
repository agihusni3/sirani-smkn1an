<?php

namespace App\Console\Commands;

use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Console\Command;

class SyncSiswaMasterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sirani:sync-siswa {--file= : Path ke file CSV jika custom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi otomatis master data siswa SMKN 1 Air Naningan ke database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $customFile = $this->option('file');
        $csvPath = $customFile ? base_path($customFile) : base_path('database/data/siswa_master.csv');

        if (!file_exists($csvPath)) {
            $this->error("File master siswa tidak ditemukan di: {$csvPath}");
            return Command::FAILURE;
        }

        $this->info("Memulai sinkronisasi data siswa dari: {$csvPath}...");

        $taAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::first();
        if (!$taAktif) {
            $taAktif = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        }

        $rombelMap = Rombel::pluck('id', 'nama_rombel')->toArray();
        $rombelLowerMap = [];
        foreach ($rombelMap as $namaR => $idR) {
            $rombelLowerMap[strtolower(str_replace(' ', '', $namaR))] = $idR;
        }

        $content = file_get_contents($csvPath);
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            $this->warn("File CSV kosong.");
            return Command::SUCCESS;
        }

        // Delimiter detection
        $sample = $lines[0];
        $delimiter = str_contains($sample, ';') ? ';' : ',';

        // Lewati baris header jika ada
        $firstRow = str_getcsv($lines[0], $delimiter);
        $cleanFirstCol = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$firstRow[0])));
        $startIndex = in_array($cleanFirstCol, ['nisn', 'nis', 'nama', 'no']) ? 1 : 0;

        $synced = 0;
        $created = 0;
        $updated = 0;

        for ($i = $startIndex; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $row = str_getcsv($line, $delimiter);
            if (count($row) < 2) continue;

            $nisn        = trim($row[0] ?? '');
            $nama        = trim($row[1] ?? '');
            $namaOrtu    = trim($row[2] ?? '');
            $noHpOrtu    = trim($row[3] ?? '');
            $noHpSiswa   = trim($row[4] ?? '');
            $namaRombel  = trim($row[5] ?? '');
            $status      = trim($row[6] ?? 'aktif');

            if (empty($nisn) || empty($nama)) continue;

            $cleanNisn = preg_replace('/[^0-9]/', '', $nisn);
            $cleanNisnCheck = !empty($cleanNisn) ? ltrim($cleanNisn, '0') : $nisn;

            // Cari siswa by NISN
            $siswa = Siswa::where('nisn', $nisn)
                ->orWhere('nisn', $cleanNisnCheck)
                ->orWhere('nisn', str_pad($cleanNisn, 10, '0', STR_PAD_LEFT))
                ->first();

            $data = [
                'nisn'        => $nisn,
                'nama'        => $nama,
                'nama_ortu'   => $namaOrtu ?: ($siswa?->nama_ortu ?? 'Wali Siswa'),
                'no_hp_ortu'  => $noHpOrtu ?: ($siswa?->no_hp_ortu ?? null),
                'no_hp_siswa' => $noHpSiswa ?: ($siswa?->no_hp_siswa ?? null),
                'status'      => strtolower($status) ?: 'aktif',
            ];

            if ($siswa) {
                $siswa->update($data);
                $updated++;
            } else {
                $siswa = Siswa::create($data);
                $created++;
            }

            // Assign Rombel
            if (!empty($namaRombel)) {
                $targetRombelId = $rombelMap[$namaRombel] 
                    ?? $rombelLowerMap[strtolower(str_replace(' ', '', $namaRombel))] 
                    ?? null;

                if ($targetRombelId) {
                    SiswaRombel::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran_id' => $taAktif->id,
                        ],
                        [
                            'rombel_id' => $targetRombelId,
                            'status_keanggotaan' => 'aktif',
                        ]
                    );
                }
            }

            $synced++;
        }

        $this->info("✔ Sinkronisasi selesai!");
        $this->line("Total data diproses : {$synced} siswa");
        $this->line("Data baru ditambahkan : {$created}");
        $this->line("Data diperbarui       : {$updated}");

        return Command::SUCCESS;
    }
}
