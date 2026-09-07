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

        $basePath = base_path();
        $logs = [];

        // 1. Eksekusi git pull / reset hard ke origin/main
        $gitCmd = sprintf(
            'cd %s && git config --global --add safe.directory %s 2>&1 && git fetch origin main 2>&1 && git reset --hard origin/main 2>&1',
            escapeshellarg($basePath),
            escapeshellarg($basePath)
        );
        exec($gitCmd, $gitOutput, $gitStatus);
        $logs['git'] = $gitOutput;

        // 2. Jalankan Migrasi Database
        try {
            Artisan::call('migrate', ['--force' => true]);
            $logs['migrate'] = trim(Artisan::output());
        } catch (\Throwable $e) {
            $logs['migrate_error'] = $e->getMessage();
        }

        // 3. Sinkronisasi Data Siswa jika perintah tersedia
        try {
            if (Artisan::has('sirani:sync-siswa')) {
                Artisan::call('sirani:sync-siswa');
                $logs['sync_siswa'] = trim(Artisan::output());
            }
        } catch (\Throwable $e) {
            $logs['sync_siswa_error'] = $e->getMessage();
        }

        // 4. Bersihkan & Segarkan Cache Laravel
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
        exec(sprintf('cd %s && git log -1 --pretty=format:"%%h - %%s (%%cr)" 2>&1', escapeshellarg($basePath)), $commitOut);
        $latestCommit = !empty($commitOut) ? implode(' ', $commitOut) : 'Unknown';

        Log::info('Deploy webhook sukses dieksekusi: ' . $latestCommit);

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
