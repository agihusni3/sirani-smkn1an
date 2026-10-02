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

        // ── ACTION: Audit Barcode Siswa (sinkronisasi barcode & RFID) ──
        if ($request->input('action') === 'audit_barcode') {
            return $this->auditBarcode();
        }

        // ── ACTION: Audit Tanda Baca Petik (') dan Karakter Khusus ──
        if ($request->input('action') === 'audit_tanda_baca') {
            return $this->auditTandaBaca();
        }

        // ── ACTION: Fix Data Siswa (jalankan perbaikan langsung di produksi) ──
        if ($request->input('action') === 'fix_siswa') {
            return $this->fixSiswa();
        }

        // Pengamanan: Jangan izinkan eksekusi shell git pull / build melalui HTTP GET biasa
        // Deploy mutasi server WAJIB menggunakan HTTP POST
        if ($request->isMethod('GET') && !$request->filled('action')) {
            return response()->json([
                'status'      => 'ready',
                'message'     => 'SIRANI Webhook aktif dan terotentikasi. Gunakan HTTP POST untuk memicu deploy server.',
                'server_time' => now()->toDateTimeString(),
            ]);
        }

        Log::info('Deploy webhook diterima & divalidasi dari IP ' . $request->ip());

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

        // 3b. Pastikan SQLite di server menggunakan WAL mode & busy_timeout tinggi
        try {
            if (config('database.default') === 'sqlite') {
                \Illuminate\Support\Facades\DB::statement('PRAGMA journal_mode=WAL;');
                \Illuminate\Support\Facades\DB::statement('PRAGMA busy_timeout=15000;');
                \Illuminate\Support\Facades\DB::statement('PRAGMA synchronous=NORMAL;');
                $logs['sqlite_wal'] = 'SQLite WAL mode dan busy_timeout=15000 berhasil diaktifkan.';
            }
        } catch (\Throwable $e) {
            $logs['sqlite_wal_error'] = $e->getMessage();
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

            // Rombel & Distribusi Siswa
            $rombels = \DB::table('rombels')->get();
            $rombelStats = [];
            foreach ($rombels as $r) {
                $anggotaAktif = \DB::table('siswa_rombels')
                    ->where('rombel_id', $r->id)
                    ->where('status_keanggotaan', 'aktif')
                    ->count();
                $wali = null;
                if (!empty($r->wali_kelas_id ?? null)) {
                    $wali = \DB::table('gurus')->where('id', $r->wali_kelas_id)->value('nama');
                }
                $jurusan = null;
                if (!empty($r->jurusan_id ?? null) && \Illuminate\Support\Facades\Schema::hasTable('jurusans')) {
                    $jurusan = \DB::table('jurusans')->where('id', $r->jurusan_id)->value('nama_jurusan');
                }
                $rombelStats[] = [
                    'id' => $r->id,
                    'nama' => $r->nama_rombel ?? ($r->nama ?? 'Rombel #' . $r->id),
                    'tingkat' => $r->tingkat ?? '-',
                    'jurusan' => $jurusan ?? ($r->jurusan ?? '-'),
                    'wali_kelas' => $wali ?? 'Belum ditentukan',
                    'jumlah_siswa_aktif' => $anggotaAktif,
                ];
            }

            // Absensi
            $totalAbsensi = 0;
            $statusAbsensi = [];
            $rentangAbsensi = null;
            $siswaAktifBelumPernahAbsen = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('absensis')) {
                $totalAbsensi = \DB::table('absensis')->count();
                $statusAbsensi = \DB::table('absensis')
                    ->select('status', \DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();
                $minDate = \DB::table('absensis')->min('tanggal');
                $maxDate = \DB::table('absensis')->max('tanggal');
                $rentangAbsensi = [
                    'pertama' => $minDate,
                    'terakhir' => $maxDate,
                ];
                $siswaPernahAbsen = \DB::table('absensis')
                    ->where('pemilik_type', 'siswa')
                    ->distinct()
                    ->pluck('pemilik_id')
                    ->toArray();
                $siswaAktifBelumPernahAbsen = $allSiswas->where('status', 'aktif')
                    ->whereNotIn('id', $siswaPernahAbsen)
                    ->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama, 'nisn' => $s->nisn])
                    ->values()
                    ->toArray();
            }

            // Guru
            $guruStats = [
                'total_guru' => 0,
                'guru_tanpa_nip' => 0,
            ];
            if (\Illuminate\Support\Facades\Schema::hasTable('gurus')) {
                $guruStats['total_guru'] = \DB::table('gurus')->count();
                $guruStats['guru_tanpa_nip'] = \DB::table('gurus')
                    ->where(function($q) {
                        $q->whereNull('nip')->orWhere('nip', '');
                    })->count();
            }

            // Demografi & Kelengkapan Lainnya
            $jkStats = $allSiswas->groupBy('jenis_kelamin')->map->count()->toArray();
            $agamaStats = $allSiswas->groupBy('agama')->map->count()->toArray();
            $tanpaTglLahir = $allSiswas->filter(fn($s) => empty($s->tanggal_lahir))->count();
            $tanpaTempatLahir = $allSiswas->filter(fn($s) => empty($s->tempat_lahir))->count();
            $tanpaAlamat = $allSiswas->filter(fn($s) => empty($s->alamat))->count();
            $tanpaNamaAyah = $allSiswas->filter(fn($s) => empty($s->nama_ayah))->count();
            $tanpaNamaIbu = $allSiswas->filter(fn($s) => empty($s->nama_ibu))->count();
            $penerimaPipCount = $allSiswas->filter(fn($s) => !empty($s->penerima_pip) && $s->penerima_pip != '0')->count();

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
                    'tanpa_tgl_lahir_count' => $tanpaTglLahir,
                    'tanpa_tempat_lahir_count' => $tanpaTempatLahir,
                    'tanpa_alamat_count' => $tanpaAlamat,
                    'tanpa_nama_ayah_count' => $tanpaNamaAyah,
                    'tanpa_nama_ibu_count' => $tanpaNamaIbu,
                    'penerima_pip_count' => $penerimaPipCount,
                    'total_rombel' => count($rombelStats),
                    'total_absensi' => $totalAbsensi,
                    'siswa_aktif_belum_pernah_absen_count' => count($siswaAktifBelumPernahAbsen),
                    'total_guru' => $guruStats['total_guru'],
                ],
                'details' => [
                    'nisn_null' => $nisnNull,
                    'nisn_non_numeric' => $nisnNonNumeric,
                    'nisn_bukan_10_digit' => $nisnBukan10Digit,
                    'nisn_duplikat' => $nisnDuplikat,
                    'nama_duplikat' => $namaDuplikat,
                    'nama_all_caps_sample' => array_slice($allCaps, 0, 20),
                    'aktif_tanpa_kelas' => $tanpaKelas,
                    'rombel_distribution' => $rombelStats,
                    'demografi' => [
                        'jenis_kelamin' => $jkStats,
                        'agama' => $agamaStats,
                    ],
                    'absensi' => [
                        'total' => $totalAbsensi,
                        'per_status' => $statusAbsensi,
                        'rentang' => $rentangAbsensi,
                        'siswa_aktif_belum_pernah_absen' => array_slice($siswaAktifBelumPernahAbsen, 0, 15),
                    ],
                    'guru' => $guruStats,
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

    /**
     * Audit Barcode & RFID Siswa secara mendalam pada database produksi.
     */
    public function auditBarcode()
    {
        try {
            $hasKartuRfidTable = \Illuminate\Support\Facades\Schema::hasTable('kartu_rfids');
            $allSiswas = \DB::table('siswas')->get();
            $activeSiswas = $allSiswas->where('status', 'aktif');
            $totalActive = $activeSiswas->count();

            $kartuRfids = $hasKartuRfidTable ? \DB::table('kartu_rfids')->get() : collect();

            // 1. Audit Kartu RFID / Barcode Terdaftar di kartu_rfids
            $siswaCards = $kartuRfids->where('pemilik_type', 'siswa');
            $guruCards = $kartuRfids->where('pemilik_type', 'guru');

            $siswaCardsActive = $siswaCards->where('status', 'aktif');
            $siswaCardsNonaktif = $siswaCards->where('status', '!=', 'aktif');

            // Cek orphan cards
            $allSiswaIds = $allSiswas->pluck('id')->toArray();
            $orphanSiswaCards = $siswaCards->whereNotIn('pemilik_id', $allSiswaIds)->values()->toArray();

            // Cek kartu ganda per siswa
            $cardGroupedBySiswa = $siswaCardsActive->groupBy('pemilik_id');
            $siswaWithMultipleCards = [];
            foreach ($cardGroupedBySiswa as $sid => $cards) {
                if ($cards->count() > 1) {
                    $siswaObj = $allSiswas->firstWhere('id', $sid);
                    $siswaWithMultipleCards[] = [
                        'siswa_id' => $sid,
                        'nama' => $siswaObj?->nama ?? 'Unknown',
                        'total_kartu' => $cards->count(),
                        'uids' => $cards->pluck('uid')->toArray(),
                    ];
                }
            }

            // Cek UID duplikat di tabel kartu_rfids
            $uidGroup = $kartuRfids->groupBy('uid');
            $duplicateUids = [];
            foreach ($uidGroup as $uid => $cards) {
                if ($cards->count() > 1) {
                    $duplicateUids[] = [
                        'uid' => $uid,
                        'count' => $cards->count(),
                        'cards' => $cards->toArray(),
                    ];
                }
            }

            // 2. Audit Keselarasan Siswa Aktif vs Barcode / Scan Ready
            $scanReadySiswa = [];
            $unscannableSiswa = [];
            $barcodeTypeDistribution = [
                'registered_rfid_card' => 0,
                'barcode_nisn' => 0,
                'barcode_fallback_id' => 0,
                'unscannable' => 0,
            ];

            $siswaRombelMap = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('siswa_rombels') && \Illuminate\Support\Facades\Schema::hasTable('rombels')) {
                $srList = \DB::table('siswa_rombels')
                    ->where('status_keanggotaan', 'aktif')
                    ->join('rombels', 'siswa_rombels.rombel_id', '=', 'rombels.id')
                    ->select('siswa_rombels.siswa_id', 'rombels.nama_rombel')
                    ->get();
                foreach ($srList as $sr) {
                    $siswaRombelMap[$sr->siswa_id] = $sr->nama_rombel;
                }
            }

            foreach ($activeSiswas as $s) {
                $card = $siswaCardsActive->firstWhere('pemilik_id', $s->id);
                $nisn = trim((string)($s->nisn ?? ''));
                $rombelNama = $siswaRombelMap[$s->id] ?? 'Tanpa Rombel';

                $scannable = false;
                $activeMethod = null;
                $codeToPrint = null;
                $scanSource = null;

                if ($card) {
                    $activeMethod = 'Kartu RFID/Barcode Terdaftar (UID: ' . $card->uid . ')';
                    $codeToPrint = $card->uid;
                    $scanSource = 'kartu_rfids';
                    $barcodeTypeDistribution['registered_rfid_card']++;
                    $scannable = true;
                } elseif ($nisn !== '') {
                    $activeMethod = 'Barcode Standar NISN (' . $nisn . ')';
                    $codeToPrint = $nisn;
                    $scanSource = 'nisn';
                    $barcodeTypeDistribution['barcode_nisn']++;
                    $scannable = true;
                } else {
                    $activeMethod = 'Barcode Fallback ID (SISWA-' . $s->id . ')';
                    $codeToPrint = 'SISWA-' . $s->id;
                    $scanSource = 'fallback_id';
                    $barcodeTypeDistribution['barcode_fallback_id']++;
                    $scannable = true;
                }

                // Cek potensi konflik jika NISN siswa sama dengan UID kartu orang lain
                $conflictCard = $kartuRfids->firstWhere('uid', $nisn);
                $hasConflict = false;
                $conflictDetail = null;
                if ($conflictCard && $conflictCard->pemilik_id != $s->id) {
                    $hasConflict = true;
                    $conflictDetail = "NISN bentrok dengan UID kartu milik " . $conflictCard->pemilik_type . " ID #" . $conflictCard->pemilik_id;
                }

                $info = [
                    'id' => $s->id,
                    'nama' => $s->nama,
                    'nisn' => $nisn ?: null,
                    'rombel' => $rombelNama,
                    'active_method' => $activeMethod,
                    'code_to_print' => $codeToPrint,
                    'scan_source' => $scanSource,
                    'has_rfid_card' => (bool)$card,
                    'has_conflict' => $hasConflict,
                    'conflict_detail' => $conflictDetail,
                ];

                if ($scannable && !$hasConflict) {
                    $scanReadySiswa[] = $info;
                } else {
                    $barcodeTypeDistribution['unscannable']++;
                    $unscannableSiswa[] = $info;
                }
            }

            // 3. Riwayat Absensi & Sumber Scan
            $sumberAbsenStats = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('absensis')) {
                $sumberAbsenStats = \DB::table('absensis')
                    ->where('pemilik_type', 'siswa')
                    ->select('sumber_absen', \DB::raw('count(*) as count'))
                    ->groupBy('sumber_absen')
                    ->pluck('count', 'sumber_absen')
                    ->toArray();
            }

            return response()->json([
                'status' => 'success',
                'summary' => [
                    'total_siswa_aktif' => $totalActive,
                    'total_kartu_di_tabel_rfid' => $kartuRfids->count(),
                    'kartu_siswa_aktif' => $siswaCardsActive->count(),
                    'kartu_siswa_nonaktif' => $siswaCardsNonaktif->count(),
                    'kartu_guru_terdaftar' => $guruCards->count(),
                    'kartu_orphan_count' => count($orphanSiswaCards),
                    'siswa_kartu_ganda_count' => count($siswaWithMultipleCards),
                    'uid_duplikat_count' => count($duplicateUids),
                    'total_siswa_siap_scan' => count($scanReadySiswa),
                    'total_siswa_tidak_bisa_scan' => count($unscannableSiswa),
                    'metode_distribusi' => $barcodeTypeDistribution,
                    'riwayat_sumber_absen' => $sumberAbsenStats,
                ],
                'details' => [
                    'orphan_cards' => $orphanSiswaCards,
                    'siswa_multiple_cards' => $siswaWithMultipleCards,
                    'duplicate_uids' => $duplicateUids,
                    'unscannable_siswa' => $unscannableSiswa,
                    'sample_scan_ready' => array_slice($scanReadySiswa, 0, 15),
                    'sample_kartu_rfid_terdaftar' => $siswaCardsActive->take(10)->values(),
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
     * Audit khusus tanda baca petik (') dan karakter khusus di database produksi.
     */
    public function auditTandaBaca()
    {
        try {
            $quotes = ["'", "’", "‘", "`", '"', "\\"];
            $containsQuote = function (?string $val) use ($quotes) {
                if ($val === null || $val === '') return false;
                foreach ($quotes as $q) {
                    if (str_contains($val, $q)) return true;
                }
                return false;
            };

            // 1. Siswa
            $allSiswas = \DB::table('siswas')->get();
            $siswaHits = [];
            foreach ($allSiswas as $s) {
                $fieldsWithQuote = [];
                $fieldsToCheck = ['nama', 'nisn', 'nik', 'tempat_lahir', 'alamat', 'nama_ayah', 'nama_ibu', 'asal_sekolah'];
                foreach ($fieldsToCheck as $f) {
                    $val = (string)($s->{$f} ?? '');
                    if ($containsQuote($val)) {
                        $fieldsWithQuote[$f] = $val;
                    }
                }

                if (!empty($fieldsWithQuote)) {
                    $siswaHits[] = [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'status' => $s->status,
                        'fields' => $fieldsWithQuote,
                    ];
                }
            }

            // 2. Guru
            $guruHits = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('gurus')) {
                $allGurus = \DB::table('gurus')->get();
                foreach ($allGurus as $g) {
                    $fieldsWithQuote = [];
                    foreach (['nama', 'nip', 'nuptk', 'alamat'] as $f) {
                        $val = (string)($g->{$f} ?? '');
                        if ($containsQuote($val)) {
                            $fieldsWithQuote[$f] = $val;
                        }
                    }
                    if (!empty($fieldsWithQuote)) {
                        $guruHits[] = [
                            'id' => $g->id,
                            'nama' => $g->nama,
                            'fields' => $fieldsWithQuote,
                        ];
                    }
                }
            }

            // 3. Rombels
            $rombelHits = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('rombels')) {
                $rombels = \DB::table('rombels')->get();
                foreach ($rombels as $r) {
                    $namaRombel = (string)($r->nama_rombel ?? ($r->nama ?? ''));
                    if ($containsQuote($namaRombel)) {
                        $rombelHits[] = [
                            'id' => $r->id,
                            'nama_rombel' => $namaRombel,
                        ];
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'summary' => [
                    'total_siswa_dengan_tanda_baca' => count($siswaHits),
                    'total_guru_dengan_tanda_baca' => count($guruHits),
                    'total_rombel_dengan_tanda_baca' => count($rombelHits),
                ],
                'details' => [
                    'siswa' => $siswaHits,
                    'guru' => $guruHits,
                    'rombel' => $rombelHits,
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
}


