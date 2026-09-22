<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    /**
     * Secret token default untuk validasi request deploy
     */
    protected const DEFAULT_TOKEN = 'sirani_smkn1an_secret_deploy_key_2026';

    /**
     * Endpoint Webhook Deploy Otomatis
     * Menerima trigger dari GitHub Webhook, curl, atau script push lokal.
     */
    public function handle(Request $request)
    {
        $expectedToken = config('app.deploy_token', env('DEPLOY_SECRET_TOKEN', self::DEFAULT_TOKEN));
        $receivedToken = $request->header('X-Deploy-Token')
            ?: $request->query('token')
            ?: $request->input('token');

        // Validasi Token
        if (!$receivedToken || !hash_equals((string) $expectedToken, (string) $receivedToken)) {
            Log::warning('Deploy webhook ditolak: Token tidak valid dari IP ' . $request->ip());
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Token deploy tidak valid.'
            ], 403);
        }

        @set_time_limit(300);
        @ini_set('max_execution_time', '300');

        $basePath = base_path();
        $logs = [];

        // 1. Eksekusi git pull / reset hard ke origin/main dengan safe.directory in-memory (tanpa sentuh ~/.gitconfig)
        $gitCmd = sprintf(
            'chmod -R ug+rwX %s 2>/dev/null; cd %s && git -c safe.directory=* fetch origin 2>&1 && git -c safe.directory=* reset --hard origin/main 2>&1',
            escapeshellarg($basePath),
            escapeshellarg($basePath)
        );
        exec($gitCmd, $gitOutput, $gitStatus);
        $logs['git'] = $gitOutput;

        // 2. Jalankan composer install jika paket penting (DomPDF/PhpWord) belum terpasang di vendor
        $needsComposer = !class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) || !class_exists(\PhpOffice\PhpWord\PhpWord::class);
        if ($needsComposer || $request->has('run_composer')) {
            $composerBin = null;
            $possiblePaths = ['composer', '/usr/local/bin/composer', '/usr/bin/composer'];
            foreach ($possiblePaths as $p) {
                $check = trim((string) @shell_exec("which $p 2>/dev/null"));
                if ($check) {
                    $composerBin = $check;
                    break;
                }
                if (file_exists($p)) {
                    $composerBin = $p;
                    break;
                }
            }

            if ($composerBin) {
                $composerCmd = sprintf(
                    'git config --global --add safe.directory %s 2>/dev/null; cd %s && COMPOSER_HOME=/tmp/.composer %s install --no-dev --prefer-dist --optimize-autoloader --no-interaction --ignore-platform-req=ext-gd 2>&1',
                    escapeshellarg($basePath),
                    escapeshellarg($basePath),
                    escapeshellarg($composerBin)
                );
                exec($composerCmd, $composerOutput, $composerStatus);
                $logs['composer'] = $composerOutput;
                $logs['composer_status'] = $composerStatus;
            } else {
                $logs['composer_error'] = 'Binary composer tidak ditemukan di server Ubuntu';
            }
        }

        // 3. Jalankan Migrasi Database
        try {
            Artisan::call('migrate', ['--force' => true]);
            $logs['migrate'] = trim(Artisan::output());
        } catch (\Throwable $e) {
            $logs['migrate_error'] = $e->getMessage();
        }

        // Pastikan symlink storage terhubung untuk aset foto
        try {
            Artisan::call('storage:link');
        } catch (\Throwable $e) {
            // Abaikan jika symlink sudah ada
        }

        // 3. Bersihkan & Segarkan Cache Laravel
        try {
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $logs['cache'] = 'Cache Laravel berhasil disegarkan dan dikompilasi.';
        } catch (\Throwable $e) {
            $logs['cache_error'] = $e->getMessage();
        }

        // 5. Ambil informasi commit terbaru
        exec(sprintf('cd %s && git -c safe.directory=* log -1 --pretty=format:"%%h - %%s (%%cr)" 2>&1', escapeshellarg($basePath)), $commitOut);
        $latestCommit = !empty($commitOut) ? implode(' ', $commitOut) : 'Unknown';

        Log::info('Deploy webhook sukses dieksekusi: ' . $latestCommit);

        // Cek ekstensi jika diminta
        $logs['php_zip'] = class_exists(\ZipArchive::class);
        $logs['php_xml'] = class_exists(\DOMDocument::class);
        $logs['php_gd']  = extension_loaded('gd');

        // Cek data siswa jika diminta untuk diagnostik
        if ($request->has('check_nisn')) {
            $s = \App\Models\Siswa::where('nisn', $request->check_nisn)
                ->orWhere('nis', $request->check_nisn)
                ->first();
            $logs['student_detail'] = $s ? [
                'id' => $s->id,
                'nama' => $s->nama,
                'nisn' => $s->nisn,
                'tempat_lahir' => $s->tempat_lahir,
                'tanggal_lahir_raw' => $s->getRawOriginal('tanggal_lahir'),
                'tanggal_lahir_formatted' => $s->tanggal_lahir ? (\Carbon\Carbon::parse($s->tanggal_lahir)->format('d-m-Y')) : null,
                'status' => $s->status,
                'rombels' => $s->rombels->pluck('nama_rombel')->all(),
            ] : 'not found';
        }

        // Ambil baris error terakhir jika ada
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            exec('grep -A 10 "production.ERROR" ' . escapeshellarg($logPath) . ' | tail -n 25', $recentErrors);
            $logs['last_error'] = !empty($recentErrors) ? $recentErrors : 'No recent ERROR in log';
        }

        return response()->json([
            'status'        => 'success',
            'message'       => 'Server SIRANI berhasil diperbarui ke commit terbaru!',
            'latest_commit' => $latestCommit,
            'git_status'    => $gitStatus === 0 ? 'OK' : 'Warning',
            'timestamp'     => now()->translatedFormat('d F Y H:i:s T'),
            'details'       => $logs,
        ]);
    }
}
