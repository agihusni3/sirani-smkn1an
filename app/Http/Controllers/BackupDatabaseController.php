<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\NotifikasiOrtu;
use App\Models\Rombel;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabaseController extends Controller
{
    /**
     * Tampilkan status database dan panel unduh cadangan.
     */
    public function index()
    {
        $dbDriver = config('database.default');
        $dbName = config("database.connections.{$dbDriver}.database");

        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalRombel = Rombel::count();
        $totalAbsensi = Absensi::count();
        $totalNotifikasi = NotifikasiOrtu::count();

        $fileSize = '-';
        if ($dbDriver === 'sqlite' && File::exists($dbName)) {
            $bytes = File::size($dbName);
            $fileSize = round($bytes / (1024 * 1024), 2) . ' MB (' . number_format($bytes) . ' bytes)';
        }

        // Ambil daftar file backup tersimpan
        $backupDir = storage_path('app/backups');
        $storedBackups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $f) {
                if (str_starts_with($f->getFilename(), 'backup_sirani_')) {
                    $storedBackups[] = [
                        'filename' => $f->getFilename(),
                        'size'     => round($f->getSize() / (1024 * 1024), 2) . ' MB',
                        'time'     => Carbon::createFromTimestamp($f->getMTime())->translatedFormat('d F Y, H:i:s') . ' WIB',
                        'raw_time' => $f->getMTime(),
                    ];
                }
            }
            usort($storedBackups, fn($a, $b) => $b['raw_time'] <=> $a['raw_time']);
        }

        return view('backup.index', compact(
            'dbDriver',
            'dbName',
            'totalSiswa',
            'totalGuru',
            'totalRombel',
            'totalAbsensi',
            'totalNotifikasi',
            'fileSize',
            'storedBackups'
        ));
    }

    /**
     * Jalankan proses auto-backup secara manual melalui web.
     */
    public function triggerAutoBackup()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:auto-backup', ['--keep' => 14]);
            return back()->with('success', 'Auto-Backup database berhasil dieksekusi dan diarsipkan ke server.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengeksekusi auto-backup: ' . $e->getMessage());
        }
    }

    /**
     * Unduh file cadangan tersimpan tertentu.
     */
    public function downloadSaved(string $filename)
    {
        // Validasi path traversal
        $filename = basename($filename);
        $filePath = storage_path("app/backups/{$filename}");

        if (!File::exists($filePath)) {
            return back()->with('error', 'File cadangan tidak ditemukan.');
        }

        return response()->download($filePath, $filename);
    }

    /**
     * Pulihkan database langsung dari file cadangan yang tersimpan di server.
     */
    public function restoreSaved(string $filename)
    {
        $filename = basename($filename);
        $filePath = storage_path("app/backups/{$filename}");

        if (!File::exists($filePath)) {
            return back()->with('error', 'File cadangan tidak ditemukan.');
        }

        try {
            $sqlitePath = database_path('database.sqlite');

            // Snapshot keselamatan sebelum restore
            if (File::exists($sqlitePath)) {
                $safetySnapshot = storage_path('app/backup_pre_restore_' . time() . '.sqlite');
                File::copy($sqlitePath, $safetySnapshot);
            }

            DB::disconnect();
            File::copy($filePath, $sqlitePath);
            DB::purge();
            DB::reconnect();

            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            return back()->with('success', "Database berhasil dipulihkan dari arsip: {$filename}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Hapus file cadangan tersimpan.
     */
    public function deleteSaved(string $filename)
    {
        $filename = basename($filename);
        $filePath = storage_path("app/backups/{$filename}");

        if (File::exists($filePath)) {
            File::delete($filePath);
            return back()->with('success', "Arsip cadangan {$filename} berhasil dihapus.");
        }

        return back()->with('error', 'File cadangan tidak ditemukan.');
    }

    /**
     * Unduh file cadangan database secara instan.
     */
    public function download()
    {
        $dbDriver = config('database.default');
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

        if ($dbDriver === 'sqlite') {
            $sqlitePath = database_path('database.sqlite');
            if (File::exists($sqlitePath)) {
                $filename = "backup_sirani_smkn1an_{$timestamp}.sqlite";
                return response()->download($sqlitePath, $filename, [
                    'Content-Type' => 'application/x-sqlite3',
                ]);
            }
        }

        // Fallback generic SQL backup
        $filename = "backup_sirani_smkn1an_{$timestamp}.sql";
        $tables = ['pengaturan_sekolahs', 'tahun_ajarans', 'jurusans', 'gurus', 'rombels', 'siswas', 'siswa_rombels', 'kartu_rfids', 'jadwal_pikets', 'jadwal_hari_inis', 'absensis', 'izin_siswas', 'notifikasi_ortus', 'pengaturan_notifikasis', 'users'];

        $sqlDump = "-- CADANGAN DATABASE SIRANI SMKN 1 AIR NANINGAN\n";
        $sqlDump .= "-- Waktu Ekspor: " . Carbon::now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $rows = DB::table($table)->get();
                $sqlDump .= "-- Data Tabel: {$table} (" . count($rows) . " baris)\n";
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $keys = implode('`, `', array_keys($rowArray));
                    $values = array_map(function ($val) {
                        return is_null($val) ? 'NULL' : "'" . addslashes((string)$val) . "'";
                    }, array_values($rowArray));
                    $valuesStr = implode(', ', $values);
                    $sqlDump .= "INSERT INTO `{$table}` (`{$keys}`) VALUES ({$valuesStr});\n";
                }
                $sqlDump .= "\n";
            }
        }

        return response($sqlDump, 200, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Pulihkan database dari file cadangan (.sqlite atau .sql).
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:512000', // Maks 500MB
        ]);

        try {
            $file = $request->file('backup_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $dbDriver = config('database.default');

            if ($dbDriver === 'sqlite') {
                $sqlitePath = database_path('database.sqlite');

                // Buat snapshot darurat sebelum ditimpa
                if (File::exists($sqlitePath)) {
                    $safetySnapshot = storage_path('app/backup_pre_restore_' . time() . '.sqlite');
                    File::copy($sqlitePath, $safetySnapshot);
                }

                if ($extension === 'sqlite' || $extension === 'db') {
                    // Timpa file database.sqlite langsung
                    File::copy($file->getRealPath(), $sqlitePath);
                } elseif ($extension === 'sql') {
                    // Impor script SQL ke SQLite
                    $sqlContent = File::get($file->getRealPath());
                    DB::unprepared($sqlContent);
                } else {
                    return back()->with('error', 'Format file tidak didukung. Harap upload file berekstensi .sqlite atau .sql');
                }

                \Illuminate\Support\Facades\Artisan::call('view:clear');
                \Illuminate\Support\Facades\Artisan::call('cache:clear');

                return back()->with('success', 'Database berhasil dipulihkan (Restore Sukses) dari file: ' . $file->getClientOriginalName());
            }

            return back()->with('error', 'Fitur restore otomatis saat ini dikonfigurasi untuk driver SQLite.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }
}
