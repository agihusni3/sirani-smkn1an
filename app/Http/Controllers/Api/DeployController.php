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

        // ── ACTION: Audit Data Siswa (tanpa deploy) ──
        if ($request->input('action') === 'audit_siswa') {
            return $this->auditSiswa();
        }

        // ── ACTION: Fix Data Siswa (jalankan perbaikan langsung di produksi) ──
        if ($request->input('action') === 'fix_siswa') {
            return $this->fixSiswa();
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

        // 2. Jalankan composer install jika paket penting (DomPDF/PhpWord/WebPush) belum terpasang di vendor
        $needsComposer = !class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) 
            || !class_exists(\PhpOffice\PhpWord\PhpWord::class)
            || !class_exists(\Minishlink\WebPush\WebPush::class);
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

        // 4. Jalankan Seeder jika diminta
        if ($request->has('run_seed') || $request->has('seed')) {
            $seedClass = $request->input('seed') ?: $request->query('seed') ?: 'DatabaseSeeder';
            try {
                Artisan::call('db:seed', ['--class' => $seedClass, '--force' => true]);
                $logs['seed'] = trim(Artisan::output());
            } catch (\Throwable $e) {
                $logs['seed_error'] = $e->getMessage();
            }
        }

        // 4b. Bersihkan rekaman absensi & buku kasus jika diminta secara eksplisit
        if ($request->has('clear_absensi') || $request->has('clear_data')) {
            try {
                Artisan::call('sirani:clear-absensi', [
                    '--force'      => true,
                    '--with-kasus' => true,
                ]);
                $logs['clear_absensi'] = trim(Artisan::output());
            } catch (\Throwable $e) {
                $logs['clear_absensi_error'] = $e->getMessage();
            }
        }

        // Pastikan symlink storage terhubung untuk aset foto
        try {
            Artisan::call('storage:link');
        } catch (\Throwable $e) {
            // Abaikan jika symlink sudah ada
        }

        // 5. Bersihkan & Segarkan Cache Laravel
        try {
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $logs['cache'] = 'Cache Laravel berhasil disegarkan dan dikompilasi.';
        } catch (\Throwable $e) {
            $logs['cache_error'] = $e->getMessage();
        }

        // 6. Diagnostik Database & Siswa
        try {
            $targetKeyword = $request->input('keyword') ?: $request->query('keyword') ?: '104137013';
            $searchedSiswa = \App\Models\Siswa::where('nisn', $targetKeyword)
                ->orWhere('id', $targetKeyword)
                ->first(['id', 'nisn', 'nama', 'status']);

            $logs['db_diagnostics'] = [
                'total_siswas'   => \App\Models\Siswa::count(),
                'total_rombels'  => \App\Models\Rombel::count(),
                'total_absensis' => \App\Models\Absensi::count(),
                'total_gurus'    => \App\Models\Guru::count(),
                'searched_siswa' => $searchedSiswa,
                'sample_nisn'    => \App\Models\Siswa::limit(5)->pluck('nisn', 'nama'),
            ];
        } catch (\Throwable $e) {
            $logs['db_diagnostics_error'] = $e->getMessage();
        }

        // 7. Ambil informasi commit terbaru
        exec(sprintf('cd %s && git -c safe.directory=* log -1 --pretty=format:"%%h - %%s (%%cr)" 2>&1', escapeshellarg($basePath)), $commitOut);
        $latestCommit = !empty($commitOut) ? implode(' ', $commitOut) : 'Unknown';

        Log::info('Deploy webhook sukses dieksekusi: ' . $latestCommit);

        // Cek ekstensi jika diminta
        $logs['php_zip'] = class_exists(\ZipArchive::class);
        $logs['php_xml'] = class_exists(\DOMDocument::class);
        $logs['php_gd']  = extension_loaded('gd');

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

    /**
     * Audit Data Siswa langsung di database produksi (MySQL).
     */
    public function auditSiswa()
    {
        try {
            $allSiswas = \DB::table('siswas')->get();
            $totalSiswa = $allSiswas->count();
            $totalAktif = $allSiswas->where('status', 'aktif')->count();
            $totalNonAktif = $totalSiswa - $totalAktif;

            $nisnNull = [];
            $nisnNonNumeric = [];
            $nisnBukan10Digit = [];
            $nisnMap = [];
            $namaMap = [];
            $allCaps = [];
            $tanpaHpOrtuCount = 0;
            $tanpaNikCount = 0;
            $tanpaFotoCount = 0;

            foreach ($allSiswas as $s) {
                $nisn = trim((string)($s->nisn ?? ''));
                $nama = trim((string)($s->nama ?? ''));
                $hpOrtu = trim((string)($s->no_hp_ortu ?? ''));
                $nik = trim((string)($s->nik ?? ''));
                $foto = trim((string)($s->foto ?? ''));

                // NISN check
                if ($nisn === '') {
                    $nisnNull[] = [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'status' => $s->status,
                        'nik' => $s->nik ?? null,
                    ];
                } else {
                    if (!ctype_digit($nisn)) {
                        $nisnNonNumeric[] = [
                            'id' => $s->id,
                            'nama' => $s->nama,
                            'nisn' => $nisn,
                            'status' => $s->status,
                        ];
                    }
                    if (strlen($nisn) !== 10) {
                        $nisnBukan10Digit[] = [
                            'id' => $s->id,
                            'nama' => $s->nama,
                            'nisn' => $nisn,
                            'length' => strlen($nisn),
                            'status' => $s->status,
                        ];
                    }
                    $nisnMap[$nisn][] = [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'status' => $s->status,
                    ];
                }

                // Nama check
                $namaLower = mb_strtolower($nama);
                $namaMap[$namaLower][] = [
                    'id' => $s->id,
                    'nama' => $s->nama,
                    'nisn' => $nisn,
                    'status' => $s->status,
                ];

                // ALL CAPS check
                if (preg_match('/[A-Za-z]/', $nama) && $nama === mb_strtoupper($nama)) {
                    $allCaps[] = [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'nisn' => $nisn,
                    ];
                }

                // Kelengkapan
                if ($s->status === 'aktif') {
                    if ($hpOrtu === '' || str_contains(strtolower($hpOrtu), 'gaada') || $hpOrtu === '-' || strlen(preg_replace('/[^0-9]/', '', $hpOrtu)) < 9) {
                        $tanpaHpOrtuCount++;
                    }
                }

                if ($nik === '') {
                    $tanpaNikCount++;
                }

                if ($foto === '') {
                    $tanpaFotoCount++;
                }
            }

            // Duplikat NISN
            $nisnDuplikat = [];
            foreach ($nisnMap as $nisnVal => $list) {
                if (count($list) > 1) {
                    $nisnDuplikat[] = [
                        'nisn' => $nisnVal,
                        'count' => count($list),
                        'siswas' => $list,
                    ];
                }
            }

            // Duplikat Nama
            $namaDuplikat = [];
            foreach ($namaMap as $n => $list) {
                if (count($list) > 1) {
                    $namaDuplikat[] = [
                        'nama' => $list[0]['nama'],
                        'count' => count($list),
                        'siswas' => $list,
                    ];
                }
            }

            // Siswa aktif tanpa kelas
            $activeSiswaIds = $allSiswas->where('status', 'aktif')->pluck('id')->toArray();
            $assignedSiswaIds = \DB::table('siswa_rombels')
                ->whereIn('siswa_id', $activeSiswaIds)
                ->where('status_keanggotaan', 'aktif')
                ->pluck('siswa_id')
                ->unique()
                ->toArray();
            $unassignedIds = array_diff($activeSiswaIds, $assignedSiswaIds);

            $tanpaKelas = [];
            if (!empty($unassignedIds)) {
                $tanpaKelas = $allSiswas->whereIn('id', $unassignedIds)->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'nisn' => $s->nisn,
                    ];
                })->values();
            }

            return response()->json([
                'status' => 'success',
                'database' => \DB::connection()->getDatabaseName(),
                'driver' => \DB::connection()->getDriverName(),
                'summary' => [
                    'total_siswa' => $totalSiswa,
                    'total_aktif' => $totalAktif,
                    'total_non_aktif' => $totalNonAktif,
                    'nisn_null_count' => count($nisnNull),
                    'nisn_non_numeric_count' => count($nisnNonNumeric),
                    'nisn_bukan_10_digit_count' => count($nisnBukan10Digit),
                    'nisn_duplikat_count' => count($nisnDuplikat),
                    'nama_duplikat_count' => count($namaDuplikat),
                    'nama_all_caps_count' => count($allCaps),
                    'aktif_tanpa_hp_ortu_count' => $tanpaHpOrtuCount,
                    'tanpa_nik_count' => $tanpaNikCount,
                    'tanpa_foto_count' => $tanpaFotoCount,
                    'aktif_tanpa_kelas_count' => count($tanpaKelas),
                ],
                'details' => [
                    'nisn_null' => $nisnNull,
                    'nisn_non_numeric' => $nisnNonNumeric,
                    'nisn_bukan_10_digit' => $nisnBukan10Digit,
                    'nisn_duplikat' => $nisnDuplikat,
                    'nama_duplikat' => $namaDuplikat,
                    'nama_all_caps' => array_slice($allCaps, 0, 30),
                    'aktif_tanpa_kelas' => $tanpaKelas,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Fix Data Siswa (format nama & bersihkan field kontak kotor).
     */
    public function fixSiswa()
    {
        try {
            $updatedNames = 0;
            $updatedPhones = 0;
            $allSiswas = \DB::table('siswas')->get();

            foreach ($allSiswas as $s) {
                $updates = [];
                if (class_exists(\App\Support\NamaFormatter::class)) {
                    $formattedName = \App\Support\NamaFormatter::format($s->nama);
                    if ($formattedName !== $s->nama) {
                        $updates['nama'] = $formattedName;
                        $updatedNames++;
                    }
                }

                $hp = trim((string)($s->no_hp_ortu ?? ''));
                if (in_array(strtolower($hp), ['gaada', '-', 'tidak ada', '0'])) {
                    $updates['no_hp_ortu'] = null;
                    $updatedPhones++;
                }

                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    \DB::table('siswas')->where('id', $s->id)->update($updates);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil merapikan data siswa produksi: {$updatedNames} nama diformat, {$updatedPhones} kontak diperbaiki.",
                'updated_names' => $updatedNames,
                'updated_phones' => $updatedPhones,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

