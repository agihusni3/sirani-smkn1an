<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AutoBackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:auto-backup {--keep=14 : Jumlah arsip cadangan harian terakhir yang disimpan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pencadangan otomatis database SIRANI SMKN 1 Air Naningan dengan retensi cerdas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Memulai proses pencadangan otomatis database...');

        $dbDriver = config('database.default');
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

        try {
            if ($dbDriver === 'sqlite') {
                $sqlitePath = database_path('database.sqlite');

                if (!File::exists($sqlitePath)) {
                    $this->error("❌ File database SQLite tidak ditemukan pada {$sqlitePath}");
                    return Command::FAILURE;
                }

                $filename = "backup_sirani_auto_{$timestamp}.sqlite";
                $targetPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

                File::copy($sqlitePath, $targetPath);

                $fileSizeBytes = File::size($targetPath);
                $fileSizeMb = round($fileSizeBytes / (1024 * 1024), 2);

                $this->info("✅ Cadangan SQLite berhasil dibuat: {$filename} ({$fileSizeMb} MB)");
                Log::info("Auto-Backup database berhasil dibuat: {$filename} ({$fileSizeMb} MB)");
            } else {
                $this->warn("⚠️ Driver {$dbDriver} belum dikonfigurasi untuk auto-backup SQLite native.");
                return Command::FAILURE;
            }

            // Pruning / Retensi Cadangan Otomatis
            $keep = (int) $this->option('keep');
            $this->pruneOldBackups($backupDir, $keep);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Terjadi kegagalan saat auto-backup: " . $e->getMessage());
            Log::error("Auto-Backup Gagal: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Menghapus file cadangan lama yang melebihi kuota retensi
     */
    protected function pruneOldBackups(string $backupDir, int $keep): void
    {
        $files = File::files($backupDir);

        // Filter file backup sirani
        $backupFiles = [];
        foreach ($files as $file) {
            if (str_starts_with($file->getFilename(), 'backup_sirani_')) {
                $backupFiles[] = [
                    'path' => $file->getPathname(),
                    'time' => $file->getMTime(),
                    'name' => $file->getFilename(),
                ];
            }
        }

        // Urutkan dari yang paling baru ke yang paling lama
        usort($backupFiles, fn($a, $b) => $b['time'] <=> $a['time']);

        if (count($backupFiles) > $keep) {
            $filesToDelete = array_slice($backupFiles, $keep);
            $deletedCount = 0;

            foreach ($filesToDelete as $item) {
                File::delete($item['path']);
                $deletedCount++;
                $this->line("   🗑️ Cadangan lama dibersihkan: {$item['name']}");
            }

            $this->info("🧹 Berhasil membersihkan {$deletedCount} file cadangan lama (Batas Retensi: {$keep} file).");
        }
    }
}
