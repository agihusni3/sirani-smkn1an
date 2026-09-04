<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\KasusDisiplin;
use App\Models\NotifikasiOrtu;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user   = auth()->user();
        $role   = $user?->role ?: ($user?->fresh()?->role ?? '');

        // ── HAK AKSES BERDASARKAN ROLE ────────────────────────────────────────
        // Role yang boleh akses presensi guru
        $canAccessGuru = in_array($role, ['admin', 'kepala_sekolah', 'waka_kesiswaan']);

        // Wali Kelas: hanya bisa lihat rombel binaan sendiri
        $waliRombel = $this->getWaliRombel($user);
        $isWaliKelas = ($role === 'wali_kelas') || ($waliRombel !== null && !in_array($role, ['admin', 'kepala_sekolah', 'waka_kesiswaan', 'waka_kurikulum', 'guru_bk', 'guru_piket', 'staf_tu']));
        $waliRombelId    = null;
        $waliRombelNama  = null;
        if ($isWaliKelas && $waliRombel) {
            $waliRombelId   = $waliRombel->id;
            $waliRombelNama = $waliRombel->nama_rombel;
        }
        // ─────────────────────────────────────────────────────────────────────

        $kategori = $request->input('kategori', 'siswa');

        // Blokir akses ke presensi guru jika tidak punya hak
        if ($kategori === 'guru' && !$canAccessGuru) {
            $kategori = 'siswa'; // redirect ke siswa
        }

        $periode = $request->input('periode', 'harian');

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::today()->startOfWeek()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::today()->endOfWeek()->toDateString());
        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));
        $tahun = $request->input('tahun', Carbon::today()->format('Y'));

        // Untuk wali_kelas: paksa rombel_id ke rombel binaan (atau -1 jika belum ada rombel), abaikan input dari request
        if ($isWaliKelas) {
            $rombelId = $waliRombelId ?: -1;
            $kategori = 'siswa';
        } else {
            $rombelId = $request->input('rombel_id');
        }

        $siswaId = $request->input('siswa_id');
        $guruId = $request->input('guru_id');

        // Untuk wali_kelas di mode individu: hanya siswa dari rombel binaannya
        if ($isWaliKelas && $siswaId) {
            if ($waliRombelId) {
                $allowed = \App\Models\Siswa::whereHas('siswaRombels', function ($q) use ($waliRombelId) {
                    $q->where('rombel_id', $waliRombelId)->where('status_keanggotaan', 'aktif');
                })->pluck('id')->contains($siswaId);
                if (!$allowed) {
                    $siswaId = null;
                }
            } else {
                $siswaId = null;
            }
        }

        // Penentuan Rentang Tanggal berdasarkan Periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
            $periodeText = "Harian: " . Carbon::parse($tanggal)->translatedFormat('l, d F Y');
        } elseif ($periode === 'mingguan') {
            $startDate = $tanggalMulai;
            $endDate = $tanggalSelesai;
            $periodeText = "Mingguan: " . Carbon::parse($startDate)->translatedFormat('d M Y') . " s/d " . Carbon::parse($endDate)->translatedFormat('d M Y');
        } elseif ($periode === 'bulanan') {
            $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
            $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();
            $periodeText = "Bulanan: " . Carbon::parse($bulan . '-01')->translatedFormat('F Y');
        } elseif ($periode === 'tahunan') {
            $startDate = "{$tahun}-01-01";
            $endDate = "{$tahun}-12-31";
            $periodeText = "Tahunan: Tahun {$tahun}";
        } else { // Periode 'individu'
            $startDate = $tanggalMulai;
            $endDate = $tanggalSelesai;
            $periodeText = "Individu: " . Carbon::parse($startDate)->translatedFormat('d M Y') . " s/d " . Carbon::parse($endDate)->translatedFormat('d M Y');
        }

        // ── AUTO-EVALUASI OTOMATIS (ON-ACCESS EVALUATION) ──
        if ($kategori === 'siswa') {
            if ($periode === 'harian') {
                \App\Services\EvaluasiPresensiService::evaluasiOtomatisJikaWaktunya($tanggal);
                // Sinkronkan status Alpha otomatis setelah pukul 10:00 jika hari aktif sekolah
                if ($tanggal === Carbon::today()->toDateString() && now()->format('H:i') >= '10:00' && !\App\Models\HariLibur::isLibur($tanggal)) {
                    \Illuminate\Support\Facades\Artisan::call('piket:kunci-alpha', ['tanggal' => $tanggal]);
                }
            } else {
                \App\Services\EvaluasiPresensiService::evaluasiOtomatisJikaWaktunya(Carbon::today()->toDateString());
            }
        }

        // ── QUERY UTAMA ────────────────────────────────────────────────────────
        $baseQuery = Absensi::where('pemilik_type', $kategori)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kategori === 'siswa') {
            $baseQuery->whereHas('siswa', fn($q) => $q->where('status', 'aktif'));
            if ($rombelId) {
                $baseQuery->where(function ($q) use ($rombelId) {
                    $q->whereHas('siswaRombel', fn($sq) => $sq->where('rombel_id', $rombelId))
                      ->orWhereHas('siswa.siswaRombels', fn($sq) => $sq->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
                });
            }
            if ($siswaId) $baseQuery->where('pemilik_id', $siswaId);
        } else {
            $baseQuery->whereHas('guru', fn($q) => $q->where('status', 'aktif'));
            if ($guruId) $baseQuery->where('pemilik_id', $guruId);
        }

        // Paginasi 20 per halaman untuk tabel rincian harian & individu
        $laporans = (clone $baseQuery)
            ->with($kategori === 'siswa'
                ? ['siswa:id,nis,nama', 'siswaRombel.rombel:id,nama_rombel']
                : ['guru:id,nip,nama,jabatan'])
            ->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'asc')
            ->paginate(20)->withQueryString();

        // Statistik ringkas (tanpa load semua baris)
        $statsRaw = (clone $baseQuery)->selectRaw("
            COUNT(*) as total_record,
            SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
            SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as total_terlambat,
            SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
            SUM(CASE WHEN status IN ('sakit','izin','dispen','dispensasi','cuti','dinas_luar') THEN 1 ELSE 0 END) as total_izin,
            SUM(CASE WHEN status IN ('alpha','alfa') THEN 1 ELSE 0 END) as total_alpha,
            SUM(CASE WHEN status = 'bolos' THEN 1 ELSE 0 END) as total_bolos
        ")->first();

        // Preload data guru (untuk tabel rincian harian)
        $guruMap = collect();
        if ($kategori === 'guru') {
            $guruIds = (clone $baseQuery)->pluck('pemilik_id')->unique()->values();
            $guruMap = $guruIds->isNotEmpty() ? Guru::whereIn('id', $guruIds)->select('id','nip','nama','jabatan')->get()->keyBy('id') : collect();
        }

        // Data Izin untuk keterangan detail harian & individu
        $izinMap = collect();
        $selectedIndividu = null;
        if ($kategori === 'siswa') {
            if ($periode === 'harian') {
                $izinMap = IzinSiswa::where('tanggal', $tanggal)->get()->keyBy('siswa_id');
            } elseif ($periode === 'individu' && $siswaId) {
                $selectedIndividu = Siswa::with('siswaRombels.rombel')->find($siswaId);
                $izinMap = IzinSiswa::where('siswa_id', $siswaId)
                    ->whereBetween('tanggal', [$startDate, $endDate])->get()->keyBy('tanggal');
            }
        } elseif ($kategori === 'guru' && $periode === 'individu' && $guruId) {
            $selectedIndividu = Guru::find($guruId);
        }

        // ── REKAP AGREGAT: SQL GROUP BY (jauh lebih cepat dari PHP aggregate) ──
        $rekapData = collect();
        if (in_array($periode, ['mingguan', 'bulanan', 'tahunan'])) {
            // Ambil agregat per pemilik_id langsung dari DB (hindari load 690K baris ke PHP)
            $aggrMap = (clone $baseQuery)
                ->selectRaw("
                    pemilik_id,
                    SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END)                       as total_hadir,
                    SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END)                   as total_telat,
                    SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END)                       as total_sakit,
                    SUM(CASE WHEN status IN ('izin','dispen','dinas_luar','dispensasi') THEN 1 ELSE 0 END) as total_izin,
                    SUM(CASE WHEN status IN ('alpha','alfa') THEN 1 ELSE 0 END)             as total_alpha,
                    SUM(CASE WHEN status = 'bolos' THEN 1 ELSE 0 END)                       as total_bolos,
                    COUNT(*) as total_hari
                ")
                ->groupBy('pemilik_id')
                ->get()
                ->keyBy('pemilik_id');

            if ($kategori === 'siswa') {
                $siswaQuery = Siswa::where('status', 'aktif')
                    ->with(['siswaRombels' => fn($q) => $q->where('status_keanggotaan', 'aktif')->with('rombel:id,nama_rombel,tingkat')]);
                if ($rombelId) {
                    $siswaQuery->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
                }
                $daftarSiswa = $this->sortSiswaByTingkatAndRombel($siswaQuery->get());

                $rekapCollection = $daftarSiswa->values()->map(function ($s) use ($aggrMap) {
                    $a = $aggrMap->get($s->id);
                    $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? '-';
                    return (object) [
                        'id'          => $s->id,
                        'nisn'        => $s->nisn,
                        'nama'        => $s->nama,
                        'rombel'      => $rombelNama,
                        'total_hadir' => (int)($a->total_hadir ?? 0),
                        'total_telat' => (int)($a->total_telat ?? 0),
                        'total_sakit' => (int)($a->total_sakit ?? 0),
                        'total_izin'  => (int)($a->total_izin ?? 0),
                        'total_alpha' => (int)($a->total_alpha ?? 0),
                        'total_bolos' => (int)($a->total_bolos ?? 0),
                        'total_hari'  => (int)($a->total_hari ?? 0),
                    ];
                });
            } else {
                $daftarGuru = Guru::where('status', 'aktif')->orderBy('nama')->get();

                $rekapCollection = $daftarGuru->values()->map(function ($g) use ($aggrMap) {
                    $a = $aggrMap->get($g->id);
                    return (object) [
                        'id'                => $g->id,
                        'nip'               => $g->nip ?? '-',
                        'nama'              => $g->nama,
                        'jabatan'           => $g->jabatan ?? '-',
                        'jenis_kepegawaian' => $g->jenis_kepegawaian ?? 'pns',
                        'label_kepegawaian' => $g->label_kepegawaian,
                        'hari_mengajar'     => $g->getHariMengajarList(),
                        'total_hadir'       => (int)($a->total_hadir ?? 0),
                        'total_telat'       => (int)($a->total_telat ?? 0),
                        'total_sakit'       => (int)($a->total_sakit ?? 0),
                        'total_izin'        => (int)($a->total_izin ?? 0),
                        'total_alpha'       => (int)($a->total_alpha ?? 0),
                        'total_bolos'       => (int)($a->total_bolos ?? 0),
                        'total_hari'        => (int)($a->total_hari ?? 0),
                    ];
                });
            }

            // Paginasi 20 item per halaman untuk Rekap Agregat
            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = $rekapCollection->slice(($page - 1) * $perPage, $perPage)->values();
            $rekapData = new LengthAwarePaginator(
                $currentItems,
                $rekapCollection->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        // Statistik dari SQL ringkas (tidak load semua rows)
        $totalRecord    = (int)($statsRaw->total_record ?? 0);
        $totalHadir     = (int)($statsRaw->total_hadir ?? 0);
        $totalTerlambat = (int)($statsRaw->total_terlambat ?? 0);
        $totalIzin      = (int)($statsRaw->total_izin ?? 0);
        $totalAlpha     = (int)($statsRaw->total_alpha ?? 0);
        $totalBolos     = (int)($statsRaw->total_bolos ?? 0);

        // Harmonisasikan perhitungan Harian Hari Ini dengan Meja Piket
        if ($periode === 'harian' && $tanggal === Carbon::today()->toDateString()) {
            if ($kategori === 'siswa') {
                if ($rombelId) {
                    $totalTarget = Siswa::where('status', 'aktif')
                        ->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'))
                        ->count();
                } elseif ($siswaId) {
                    $totalTarget = 1;
                } else {
                    $totalTarget = Siswa::where('status', 'aktif')->count();
                }
            } else {
                $totalTarget = $guruId ? 1 : Guru::where('status', 'aktif')->count();
            }

            if ($totalRecord < $totalTarget) {
                $unscanned = max(0, $totalTarget - ($totalHadir + $totalTerlambat + $totalIzin + $totalBolos));
                $totalAlpha = max($totalAlpha, $unscanned);
                $totalRecord = $totalTarget;
            }
        }

        $persentase     = $totalRecord > 0 ? round((($totalHadir + $totalTerlambat) / $totalRecord) * 100, 1) : 0;

        // Data Master untuk Pilihan Dropdown
        $taAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
        // Wali Kelas: rombel hanya rombel binaannya
        if ($isWaliKelas && $waliRombelId) {
            $rombels = \App\Models\Rombel::where('id', $waliRombelId)->get();
            // Siswa hanya dari rombel binaan
            $siswasRaw = Siswa::where('status', 'aktif')
                ->whereHas('siswaRombels', function ($q) use ($waliRombelId) {
                    $q->where('rombel_id', $waliRombelId)->where('status_keanggotaan', 'aktif');
                })
                ->with(['siswaRombels' => function ($q) use ($taAktif) {
                    if ($taAktif) {
                        $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')->with('rombel');
                    }
                }])
                ->get();
            $siswas = $this->sortSiswaByTingkatAndRombel($siswasRaw);
        } else {
            $rombels = $this->sortRombelByTingkat(Rombel::all());
            $siswasRaw = Siswa::where('status', 'aktif')
                ->select('id', 'nisn', 'nama')
                ->with(['siswaRombels' => function ($q) use ($taAktif) {
                    if ($taAktif) {
                        $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')
                          ->select('id', 'siswa_id', 'rombel_id')->with('rombel:id,nama_rombel,tingkat');
                    }
                }])
                ->get();
            $siswas = $this->sortSiswaByTingkatAndRombel($siswasRaw);
        }
        $gurus = $canAccessGuru ? Guru::where('status', 'aktif')->orderBy('nama')->get() : collect();

        $rombel = $rombelId ? Rombel::find($rombelId) : null;

        return view('laporan.index', compact(
            'laporans',
            'rekapData',
            'guruMap',
            'izinMap',
            'selectedIndividu',
            'rombels',
            'rombel',
            'siswas',
            'gurus',
            'kategori',
            'periode',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'bulan',
            'tahun',
            'rombelId',
            'siswaId',
            'guruId',
            'periodeText',
            'totalRecord',
            'totalHadir',
            'totalTerlambat',
            'totalIzin',
            'totalAlpha',
            'totalBolos',
            'persentase',
            'canAccessGuru',
            'isWaliKelas',
            'waliRombelId',
            'waliRombelNama'
        ));
    }

    /**
     * Export laporan presensi ke file CSV.
     */
    public function exportCsv(Request $request)
    {
        $user   = auth()->user();
        $role   = $user->role ?: ($user->fresh()?->role ?? '');

        $canAccessGuru = in_array($role, ['admin', 'kepala_sekolah', 'waka_kesiswaan']);
        $waliRombel = $this->getWaliRombel($user);
        $isWaliKelas = ($role === 'wali_kelas') || ($waliRombel !== null && !in_array($role, ['admin', 'kepala_sekolah', 'waka_kesiswaan', 'waka_kurikulum', 'guru_bk', 'guru_piket', 'staf_tu']));
        $waliRombelId  = null;
        if ($isWaliKelas && $waliRombel) {
            $waliRombelId = $waliRombel->id;
        }

        $kategori = $request->input('kategori', 'siswa');
        if ($kategori === 'guru' && !$canAccessGuru) {
            $kategori = 'siswa';
        }

        $periode  = $request->input('periode', 'harian');
        $tanggal  = $request->input('tanggal', Carbon::today()->toDateString());
        $tanggalMulai   = $request->input('tanggal_mulai', Carbon::today()->startOfWeek()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::today()->endOfWeek()->toDateString());
        $bulan  = $request->input('bulan', Carbon::today()->format('Y-m'));
        $tahun  = $request->input('tahun', Carbon::today()->format('Y'));

        if ($isWaliKelas) {
            $rombelId = $waliRombelId ?: -1;
            $kategori = 'siswa';
        } else {
            $rombelId = $request->input('rombel_id');
        }

        $siswaId  = $request->input('siswa_id');
        $guruId   = $request->input('guru_id');

        if ($isWaliKelas && $siswaId) {
            if ($waliRombelId) {
                $allowed = \App\Models\Siswa::whereHas('siswaRombels', function ($q) use ($waliRombelId) {
                    $q->where('rombel_id', $waliRombelId)->where('status_keanggotaan', 'aktif');
                })->pluck('id')->contains($siswaId);
                if (!$allowed) {
                    $siswaId = null;
                }
            } else {
                $siswaId = null;
            }
        }

        if ($periode === 'harian')        { $startDate = $tanggal; $endDate = $tanggal; }
        elseif ($periode === 'bulanan')   { $startDate = Carbon::parse($bulan.'-01')->startOfMonth()->toDateString(); $endDate = Carbon::parse($bulan.'-01')->endOfMonth()->toDateString(); }
        elseif ($periode === 'tahunan')   { $startDate = "{$tahun}-01-01"; $endDate = "{$tahun}-12-31"; }
        else                              { $startDate = $tanggalMulai; $endDate = $tanggalSelesai; }

        $query = Absensi::where('pemilik_type', $kategori)->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kategori === 'siswa') {
            $query->whereHas('siswa', fn($q) => $q->where('status', 'aktif'));
            $query->with(['siswa.siswaRombels.rombel', 'siswaRombel.siswa', 'siswaRombel.rombel']);
            if ($rombelId) {
                $query->where(function ($q) use ($rombelId) {
                    $q->whereHas('siswaRombel', function ($sq) use ($rombelId) {
                        $sq->where('rombel_id', $rombelId);
                    })->orWhereHas('siswa.siswaRombels', function ($sq) use ($rombelId) {
                        $sq->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
                    });
                });
            }
            if ($siswaId)  $query->where('pemilik_id', $siswaId);
        } else {
            $query->whereHas('guru', fn($q) => $q->where('status', 'aktif'));
            $query->with('guru');
            if ($guruId) $query->where('pemilik_id', $guruId);
        }

        $laporans = $query->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'asc')->get();

        $fileName = "laporan_{$kategori}_{$periode}_" . date('Y-m-d') . '.csv';
        $headers  = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function () use ($laporans, $kategori, $periode, $rombelId, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fwrite($file, "sep=;\n");

            // ── FORMAT REKAP AGREGAT: MINGGUAN / BULANAN / TAHUNAN ──
            if (in_array($periode, ['mingguan', 'bulanan', 'tahunan'])) {
                if ($kategori === 'siswa') {
                    fputcsv($file, ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Hadir', 'Telat', 'Sakit', 'Izin', 'Alpha', 'Bolos', 'Total Hari'], ';');
                    $siswaQuery = Siswa::where('status', 'aktif')
                        ->with(['siswaRombels' => function ($q) {
                            $q->where('status_keanggotaan', 'aktif')->with('rombel:id,nama_rombel,tingkat');
                        }]);
                    if ($rombelId) {
                        $siswaQuery->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
                    }
                    $daftarSiswa = $this->sortSiswaByTingkatAndRombel($siswaQuery->get());
                    $absensiBySiswa = $laporans->groupBy('pemilik_id');

                    foreach ($daftarSiswa as $i => $s) {
                        $absenSiswa = $absensiBySiswa->get($s->id, collect());
                        $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? '-';
                        fputcsv($file, [
                            $i + 1,
                            $s->nisn ? '="' . $s->nisn . '"' : '-',
                            $s->nama,
                            $rombelNama,
                            $absenSiswa->where('status', 'hadir')->count(),
                            $absenSiswa->where('status', 'terlambat')->count(),
                            $absenSiswa->where('status', 'sakit')->count(),
                            $absenSiswa->whereIn('status', ['izin', 'dispen', 'dispensasi', 'cuti', 'dinas_luar'])->count(),
                            $absenSiswa->whereIn('status', ['alpha', 'alfa'])->count(),
                            $absenSiswa->where('status', 'bolos')->count(),
                            $absenSiswa->count(),
                        ], ';');
                    }
                } else {
                    fputcsv($file, ['No', 'NIP', 'Nama Guru/Pegawai', 'Jabatan', 'Hadir', 'Telat', 'Sakit', 'Izin', 'Alpha', 'Bolos', 'Total Hari'], ';');
                    $daftarGuru = Guru::where('status', 'aktif')->orderBy('nama')->get();
                    $absensiByGuru = $laporans->groupBy('pemilik_id');

                    foreach ($daftarGuru as $i => $g) {
                        $absenGuru = $absensiByGuru->get($g->id, collect());
                        fputcsv($file, [
                            $i + 1,
                            $g->nip ? '="' . $g->nip . '"' : '-',
                            $g->nama,
                            $g->jabatan ?? '-',
                            $absenGuru->where('status', 'hadir')->count(),
                            $absenGuru->where('status', 'terlambat')->count(),
                            $absenGuru->where('status', 'sakit')->count(),
                            $absenGuru->whereIn('status', ['izin', 'dispen', 'dinas_luar', 'dispensasi'])->count(),
                            $absenGuru->whereIn('status', ['alpha', 'alfa'])->count(),
                            $absenGuru->where('status', 'bolos')->count(),
                            $absenGuru->count(),
                        ], ';');
                    }
                }
            } else {
                // ── FORMAT RINCIAN: HARIAN & PER INDIVIDU ──
                if ($kategori === 'siswa') {
                    fputcsv($file, ['No', 'Tanggal', 'NISN', 'Nama Siswa', 'Kelas', 'Status', 'Jam Masuk', 'Jam Pulang', 'Keterangan/Sumber'], ';');
                    foreach ($laporans as $i => $lap) {
                        $nisn = $lap->siswa->nisn ?? ($lap->siswaRombel->siswa->nisn ?? '-');
                        $nama = $lap->siswa->nama ?? ($lap->siswaRombel->siswa->nama ?? '-');
                        $rombel = $lap->siswaRombel->rombel->nama_rombel ?? ($lap->siswa->siswaRombels->first()?->rombel?->nama_rombel ?? '-');
                        fputcsv($file, [
                            $i + 1,
                            $lap->tanggal,
                            $nisn !== '-' ? '="' . $nisn . '"' : '-',
                            $nama,
                            $rombel,
                            strtoupper($lap->status),
                            $lap->jam_masuk ?? '-',
                            $lap->jam_pulang ?? '-',
                            $lap->sumber_absen_label,
                        ], ';');
                    }
                } else {
                    $guruIds = $laporans->pluck('pemilik_id')->unique()->values();
                    $guruMap = Guru::whereIn('id', $guruIds)->get()->keyBy('id');
                    fputcsv($file, ['No', 'Tanggal', 'NIP', 'Nama Guru/Pegawai', 'Jabatan', 'Status', 'Jam Masuk', 'Jam Pulang', 'Keterangan/Sumber'], ';');
                    foreach ($laporans as $i => $lap) {
                        $guru = $lap->guru ?? $guruMap->get($lap->pemilik_id);
                        fputcsv($file, [
                            $i + 1,
                            $lap->tanggal,
                            $guru && $guru->nip ? '="' . $guru->nip . '"' : '-',
                            $guru->nama ?? '-',
                            $guru->jabatan ?? '-',
                            strtoupper($lap->status),
                            $lap->jam_masuk ?? '-',
                            $lap->jam_pulang ?? '-',
                            $lap->sumber_absen_label,
                        ], ';');
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cetak Laporan Rekapitulasi Presensi Format A4 Resmi ber-KOP Dinas.
     */
    public function cetakPdf(Request $request)
    {
        $user   = auth()->user();
        $role   = $user->role ?: ($user->fresh()?->role ?? '');

        $canAccessGuru = in_array($role, ['admin', 'kepala_sekolah', 'waka_kesiswaan']);
        $waliRombel = $this->getWaliRombel($user);
        $isWaliKelas = ($role === 'wali_kelas') || ($waliRombel !== null && !in_array($role, ['admin', 'kepala_sekolah', 'waka_kesiswaan', 'waka_kurikulum', 'guru_bk', 'guru_piket', 'staf_tu']));
        $waliRombelId  = null;
        if ($isWaliKelas && $waliRombel) {
            $waliRombelId = $waliRombel->id;
        }

        $kategori = $request->input('kategori', 'siswa');
        if ($kategori === 'guru' && !$canAccessGuru) {
            $kategori = 'siswa';
        }

        $periode = $request->input('periode', 'harian');

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::today()->startOfWeek()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', Carbon::today()->endOfWeek()->toDateString());
        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));
        $tahun = $request->input('tahun', Carbon::today()->format('Y'));

        if ($isWaliKelas) {
            $rombelId = $waliRombelId ?: -1;
            $kategori = 'siswa';
        } else {
            $rombelId = $request->input('rombel_id');
        }

        $siswaId = $request->input('siswa_id');
        $guruId = $request->input('guru_id');

        if ($isWaliKelas && $siswaId) {
            if ($waliRombelId) {
                $allowed = \App\Models\Siswa::whereHas('siswaRombels', function ($q) use ($waliRombelId) {
                    $q->where('rombel_id', $waliRombelId)->where('status_keanggotaan', 'aktif');
                })->pluck('id')->contains($siswaId);
                if (!$allowed) {
                    $siswaId = null;
                }
            } else {
                $siswaId = null;
            }
        }

        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
            $periodeText = "Harian : " . Carbon::parse($tanggal)->translatedFormat('l, d F Y');
        } elseif ($periode === 'mingguan') {
            $startDate = $tanggalMulai;
            $endDate = $tanggalSelesai;
            $periodeText = "Mingguan : " . Carbon::parse($startDate)->translatedFormat('d M Y') . " s/d " . Carbon::parse($endDate)->translatedFormat('d M Y');
        } elseif ($periode === 'bulanan') {
            $startDate = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
            $endDate = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();
            $periodeText = "Bulan : " . Carbon::parse($bulan . '-01')->translatedFormat('F Y');
        } elseif ($periode === 'tahunan') {
            $startDate = "{$tahun}-01-01";
            $endDate = "{$tahun}-12-31";
            $periodeText = "Tahun : Tahun {$tahun}";
        } else {
            $startDate = $tanggalMulai;
            $endDate = $tanggalSelesai;
            $periodeText = "Individu : " . Carbon::parse($startDate)->translatedFormat('d M Y') . " s/d " . Carbon::parse($endDate)->translatedFormat('d M Y');
        }

        $query = Absensi::where('pemilik_type', $kategori)->whereBetween('tanggal', [$startDate, $endDate]);

        $rombel = null;
        if ($rombelId) {
            $rombel = Rombel::with(['jurusan', 'waliKelas'])->find($rombelId);
        }

        if ($kategori === 'siswa') {
            $query->whereHas('siswa', fn($q) => $q->where('status', 'aktif'));
            $query->with(['siswa.siswaRombels.rombel', 'siswaRombel.siswa', 'siswaRombel.rombel']);
            if ($rombelId) {
                $query->where(function ($q) use ($rombelId) {
                    $q->whereHas('siswaRombel', function ($sq) use ($rombelId) {
                        $sq->where('rombel_id', $rombelId);
                    })->orWhereHas('siswa.siswaRombels', function ($sq) use ($rombelId) {
                        $sq->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
                    });
                });
            }
            if ($siswaId)  $query->where('pemilik_id', $siswaId);
        } else {
            $query->whereHas('guru', fn($q) => $q->where('status', 'aktif'));
            $query->with('guru');
            if ($guruId) $query->where('pemilik_id', $guruId);
        }

        $laporans = $query->orderBy('tanggal', 'asc')->orderBy('jam_masuk', 'asc')->get();

        $rekapData = collect();
        if (in_array($periode, ['mingguan', 'bulanan', 'tahunan'])) {
            if ($kategori === 'siswa') {
                $siswaQuery = Siswa::where('status', 'aktif')
                    ->with(['siswaRombels' => function ($q) {
                        $q->where('status_keanggotaan', 'aktif')->with('rombel:id,nama_rombel,tingkat');
                    }]);
                if ($rombelId) {
                    $siswaQuery->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
                }
                $daftarSiswa = $this->sortSiswaByTingkatAndRombel($siswaQuery->get());
                $absensiBySiswa = $laporans->groupBy('pemilik_id');

                $rekapData = $daftarSiswa->map(function ($s, $idx) use ($absensiBySiswa) {
                    $absenSiswa = $absensiBySiswa->get($s->id, collect());
                    $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? '-';

                    $h = $absenSiswa->where('status', 'hadir')->count();
                    $t = $absenSiswa->where('status', 'terlambat')->count();
                    $sakit = $absenSiswa->where('status', 'sakit')->count();
                    $i = $absenSiswa->whereIn('status', ['izin', 'dispen', 'dispensasi', 'cuti', 'dinas_luar'])->count();
                    $a = $absenSiswa->whereIn('status', ['alpha', 'alfa'])->count();
                    $b = $absenSiswa->where('status', 'bolos')->count();
                    $totMasuk = $h + $t;
                    $totPertemuan = $totMasuk + $sakit + $i + $a + $b;
                    $persen = $totPertemuan > 0 ? round(($totMasuk / $totPertemuan) * 100, 1) : 0;

                    return (object) [
                        'no'          => $idx + 1,
                        'id'          => $s->id,
                        'nisn'        => $s->nisn,
                        'nama'        => $s->nama,
                        'rombel'      => $rombelNama,
                        'hadir'       => $h,
                        'terlambat'   => $t,
                        'sakit'       => $sakit,
                        'izin'        => $i,
                        'alpha'       => $a,
                        'bolos'       => $b,
                        'total_masuk' => $totMasuk,
                        'total_hari'  => $totPertemuan,
                        'persen'      => $persen,
                    ];
                });
            } else {
                $daftarGuru = Guru::where('status', 'aktif')->orderBy('nama')->get();
                $absensiByGuru = $laporans->groupBy('pemilik_id');

                $rekapData = $daftarGuru->map(function ($g, $idx) use ($absensiByGuru) {
                    $absenGuru = $absensiByGuru->get($g->id, collect());
                    $h = $absenGuru->where('status', 'hadir')->count();
                    $t = $absenGuru->where('status', 'terlambat')->count();
                    $sakit = $absenGuru->where('status', 'sakit')->count();
                    $i = $absenGuru->whereIn('status', ['izin', 'dispen', 'dispensasi', 'cuti', 'dinas_luar'])->count();
                    $a = $absenGuru->whereIn('status', ['alpha', 'alfa'])->count();
                    $b = $absenGuru->where('status', 'bolos')->count();
                    $totMasuk = $h + $t;
                    $totPertemuan = $totMasuk + $sakit + $i + $a + $b;
                    $persen = $totPertemuan > 0 ? round(($totMasuk / $totPertemuan) * 100, 1) : 0;

                    return (object) [
                        'no'          => $idx + 1,
                        'id'          => $g->id,
                        'nip'         => $g->nip ?? '-',
                        'nama'        => $g->nama,
                        'jabatan'     => $g->jabatan ?? '-',
                        'hadir'       => $h,
                        'terlambat'   => $t,
                        'sakit'       => $sakit,
                        'izin'        => $i,
                        'alpha'       => $a,
                        'bolos'       => $b,
                        'total_masuk' => $totMasuk,
                        'total_hari'  => $totPertemuan,
                        'persen'      => $persen,
                    ];
                });
            }
        }

        $sekolah = \App\Models\PengaturanSekolah::getAktif();

        return view('laporan.cetak_pdf', compact(
            'kategori',
            'periode',
            'periodeText',
            'startDate',
            'endDate',
            'rombel',
            'laporans',
            'rekapData',
            'sekolah'
        ));
    }

    public function update(Request $request, $id)
    {
        $absensi = Absensi::findOrFail($id);
        $user    = auth()->user();
        $today   = \Carbon\Carbon::today()->toDateString();

        // 1. Batasan Waktu: Hanya data presensi pada hari ini yang dapat dikoreksi
        if ($absensi->tanggal !== $today) {
            return redirect()->back()->with('error', 'Koreksi presensi hanya diizinkan untuk data absensi pada hari ini (' . \Carbon\Carbon::today()->translatedFormat('d F Y') . '). Catatan hari sebelumnya tidak dapat diubah.');
        }

        // 2. Hak Akses: Hanya Guru Piket yang terjadwal bertugas hari ini (atau Admin) yang berhak mengoreksi
        $isAuthorized = $user && (
            $user->isAdmin() || 
            ($user->guru && \App\Models\JadwalPiket::isGuruPiketHariIni($user->guru->id, $today))
        );

        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Guru Piket yang terjadwal bertugas pada hari ini yang berwenang melakukan koreksi presensi.');
        }

        $request->validate([
            'status'     => 'required|in:hadir,terlambat,alpha,sakit,izin,dispen,bolos',
            'jam_masuk'  => 'nullable',
            'jam_pulang' => 'nullable',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $status = $request->input('status');
        $keterangan = $request->input('keterangan');

        // Normalisasi Jam Masuk & Pulang sesuai status baru
        $jamMasuk = $request->input('jam_masuk');
        $jamPulang = $request->input('jam_pulang');

        if (in_array($status, ['alpha', 'sakit', 'izin', 'dispen'])) {
            // Untuk tidak hadir / izin / sakit / dispen / alpha, jika jam tidak diisi spesifik, set null
            if (empty($jamMasuk)) $jamMasuk = null;
            if (empty($jamPulang)) $jamPulang = null;
        } elseif ($status === 'hadir') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
            if (empty($jamPulang)) $jamPulang = $absensi->jam_pulang ?: '15:30:00';
        } elseif ($status === 'terlambat') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:25:00';
            if (empty($jamPulang)) $jamPulang = $absensi->jam_pulang ?: '15:30:00';
        } elseif ($status === 'bolos') {
            if (empty($jamMasuk)) $jamMasuk = $absensi->jam_masuk ?: '07:10:00';
            $jamPulang = null; // bolos tidak tap pulang
        }

        $absensi->update([
            'status'      => $status,
            'jam_masuk'   => $jamMasuk,
            'jam_pulang'  => $jamPulang,
            'sumber_absen'=> 'koreksi_piket_manual',
            'keterangan'  => $keterangan,
        ]);

        // SINKRONISASI UNTUK SISWA
        if ($absensi->pemilik_type === 'siswa') {
            $siswaId = $absensi->pemilik_id ?: ($absensi->siswaRombel?->siswa_id);

            if ($siswaId) {
                // 1. Sinkronisasi IzinSiswa
                if (in_array($status, ['izin', 'sakit', 'dispen'])) {
                    IzinSiswa::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'tanggal'  => $absensi->tanggal,
                        ],
                        [
                            'jenis'          => $status,
                            'status'         => 'disetujui',
                            'keterangan'     => $keterangan ?: 'Koreksi perizinan manual oleh guru piket',
                            'disetujui_oleh' => auth()->user()?->name ?? 'Guru Piket / Admin',
                        ]
                    );
                } else {
                    // Jika diubah menjadi hadir / terlambat / alpha / bolos, bersihkan data IzinSiswa tanggal tsb
                    IzinSiswa::where('siswa_id', $siswaId)
                        ->where('tanggal', $absensi->tanggal)
                        ->delete();
                }

                // 2. Sinkronisasi Buku Kasus & Disiplin
                KasusDisiplin::syncFromPresensi($siswaId);

                // 3. Sinkronisasi Notifikasi WhatsApp
                if (!in_array($status, ['alpha', 'terlambat', 'bolos'])) {
                    NotifikasiOrtu::where('siswa_id', $siswaId)
                        ->where('tanggal', $absensi->tanggal)
                        ->where('status', 'pending')
                        ->whereIn('kategori', ['alpha', 'terlambat', 'bolos', 'panggilan_ortu'])
                        ->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Catatan absensi berhasil dikoreksi dan seluruh modul terkait (Perizinan, Buku Kasus & Peringatan Dasbor) telah tersinkronkan.');
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $user    = auth()->user();
        $today   = \Carbon\Carbon::today()->toDateString();

        // 1. Batasan Waktu: Hanya data presensi pada hari ini yang dapat dihapus
        if ($absensi->tanggal !== $today) {
            return redirect()->back()->with('error', 'Penghapusan presensi hanya diizinkan untuk data absensi pada hari ini (' . \Carbon\Carbon::today()->translatedFormat('d F Y') . ').');
        }

        // 2. Hak Akses: Hanya Guru Piket yang terjadwal bertugas hari ini (atau Admin) yang berhak
        $isAuthorized = $user && (
            $user->isAdmin() || 
            ($user->guru && \App\Models\JadwalPiket::isGuruPiketHariIni($user->guru->id, $today))
        );

        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak: Hanya Guru Piket yang terjadwal bertugas pada hari ini yang berwenang menghapus catatan presensi.');
        }

        $siswaId = $absensi->pemilik_type === 'siswa' ? ($absensi->pemilik_id ?: ($absensi->siswaRombel?->siswa_id)) : null;
        $tanggal = $absensi->tanggal;

        $absensi->delete();

        if ($siswaId) {
            IzinSiswa::where('siswa_id', $siswaId)->where('tanggal', $tanggal)->delete();
            KasusDisiplin::syncFromPresensi($siswaId);
        }

        return redirect()->back()->with('success', 'Catatan absensi berhasil dihapus dan seluruh modul terkait telah disinkronkan.');
    }

    /**
     * Resolusi rombel binaan untuk pengguna ber-role wali_kelas.
     */
    private function getWaliRombel($user): ?\App\Models\Rombel
    {
        if (!$user) return null;

        $guruId = $user->guru_id;
        if (!$guruId && $user->email) {
            $guru = \App\Models\Guru::where('email', $user->email)->first();
            if (!$guru && $user->name) {
                $guru = \App\Models\Guru::where('nama', $user->name)->first();
            }
            if ($guru) {
                $guruId = $guru->id;
            }
        }

        if ($guruId) {
            $rombel = \App\Models\Rombel::where('wali_kelas_id', $guruId)->first();
            if ($rombel) return $rombel;

            $currentGuru = \App\Models\Guru::find($guruId);
            if ($currentGuru) {
                $rombel = $currentGuru->rombels()->first();
                if ($rombel) return $rombel;
            }
        }

        if (str_contains($user->email ?? '', 'walikelas')) {
            $rombel = \App\Models\Rombel::all()->first(function ($r) use ($user) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $r->nama_rombel));
                return str_contains($user->email, $slug);
            });
            if ($rombel) return $rombel;
        }

        return null;
    }

    /**
     * Urutkan koleksi siswa berdasarkan:
     * 1. Tingkat Angkatan (X -> XI -> XII)
     * 2. Nama Rombel (X APHP 1, X RPL 1, X TSM 1, dst secara natural)
     * 3. Nama Siswa (Alfabetis A-Z)
     */
    private function sortSiswaByTingkatAndRombel($collection)
    {
        $tingkatMap = ['X' => 10, 'XI' => 11, 'XII' => 12];
        return $collection->sort(function ($a, $b) use ($tingkatMap) {
            $rombelA = is_object($a) && property_exists($a, 'rombel')
                ? $a->rombel
                : ($a->siswaRombels->first()?->rombel?->nama_rombel ?? 'ZZZ');
            $rombelB = is_object($b) && property_exists($b, 'rombel')
                ? $b->rombel
                : ($b->siswaRombels->first()?->rombel?->nama_rombel ?? 'ZZZ');

            $tA = 99;
            $tB = 99;

            if (preg_match('/^(XII|XI|X)\b/i', (string)$rombelA, $mA)) {
                $tA = $tingkatMap[strtoupper($mA[1])] ?? 99;
            }
            if (preg_match('/^(XII|XI|X)\b/i', (string)$rombelB, $mB)) {
                $tB = $tingkatMap[strtoupper($mB[1])] ?? 99;
            }

            if ($tA !== $tB) {
                return $tA <=> $tB;
            }

            $cmpRombel = strnatcasecmp((string)$rombelA, (string)$rombelB);
            if ($cmpRombel !== 0) {
                return $cmpRombel;
            }

            $namaA = is_object($a) ? ($a->nama ?? '') : '';
            $namaB = is_object($b) ? ($b->nama ?? '') : '';
            return strcasecmp((string)$namaA, (string)$namaB);
        })->values();
    }

    /**
     * Urutkan koleksi Rombel berdasarkan Tingkat Angkatan (X -> XI -> XII) lalu nama rombel
     */
    private function sortRombelByTingkat($rombels)
    {
        $tingkatMap = ['X' => 10, 'XI' => 11, 'XII' => 12];
        return $rombels->sort(function ($a, $b) use ($tingkatMap) {
            $tA = $tingkatMap[strtoupper($a->tingkat ?? '')] ?? 99;
            $tB = $tingkatMap[strtoupper($b->tingkat ?? '')] ?? 99;
            if ($tA !== $tB) {
                return $tA <=> $tB;
            }
            return strnatcasecmp($a->nama_rombel ?? '', $b->nama_rombel ?? '');
        })->values();
    }
}
