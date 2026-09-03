<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\PengaturanSekolah;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PeringkatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isWaliKelas = $user && $user->isWaliKelas() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isGuruBk() && !$user->isWakaKesiswaan();
        $waliRombel = $isWaliKelas ? $this->getWaliRombel($user) : null;

        $kategori = $request->input('kategori', 'siswa');
        $periode = $request->input('periode', 'semester'); // semester, bulan, kustom
        $semesterTipe = $request->input('semester', 'ganjil'); // ganjil (Jul - Des), genap (Jan - Jun)
        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        // Tahun Ajaran
        $taAktif = TahunAjaran::where('is_active', true)->first() ?: TahunAjaran::latest()->first();
        $tahunAjaranId = $request->input('tahun_ajaran_id', $taAktif?->id);
        $taPilihan = TahunAjaran::find($tahunAjaranId) ?: $taAktif;

        // Tentukan Rentang Tanggal Berdasarkan Periode
        $yearStart = $taPilihan ? (int)substr($taPilihan->nama, 0, 4) : (int)date('Y');
        $yearEnd = $taPilihan ? (int)substr($taPilihan->nama, 5, 4) : ($yearStart + 1);

        if ($periode === 'semester') {
            if ($semesterTipe === 'ganjil') {
                $startDate = Carbon::create($yearStart, 7, 1)->toDateString();
                $endDate = Carbon::create($yearStart, 12, 31)->toDateString();
                $periodeLabel = "Semester Ganjil T.A. " . ($taPilihan->nama ?? "{$yearStart}/{$yearEnd}");
            } else {
                $startDate = Carbon::create($yearEnd, 1, 1)->toDateString();
                $endDate = Carbon::create($yearEnd, 6, 30)->toDateString();
                $periodeLabel = "Semester Genap T.A. " . ($taPilihan->nama ?? "{$yearStart}/{$yearEnd}");
            }
        } elseif ($periode === 'bulan') {
            $cMonth = Carbon::createFromFormat('Y-m', $bulan);
            $startDate = $cMonth->copy()->startOfMonth()->toDateString();
            $endDate = $cMonth->copy()->endOfMonth()->toDateString();
            $periodeLabel = "Bulan " . $cMonth->translatedFormat('F Y');
        } else { // kustom
            $startDate = $tanggalMulai ?: Carbon::today()->startOfMonth()->toDateString();
            $endDate = $tanggalSelesai ?: Carbon::today()->toDateString();
            $periodeLabel = Carbon::parse($startDate)->translatedFormat('d M Y') . " s/d " . Carbon::parse($endDate)->translatedFormat('d M Y');
        }

        // Jangan menghitung tanggal di masa depan jika semester belum selesai
        $effectiveEndDate = Carbon::parse($endDate)->gt(Carbon::today()) ? Carbon::today()->toDateString() : $endDate;

        // Rombel filter
        $rombels = Rombel::orderBy('nama_rombel')->get();
        if ($isWaliKelas && $waliRombel) {
            $rombelId = $waliRombel->id;
        } else {
            $rombelId = $request->input('rombel_id');
        }

        $sekolah = PengaturanSekolah::getAktif();
        $semuaTa = TahunAjaran::orderBy('nama', 'desc')->get();

        // ── Hitung Peringkat Berdasarkan Kategori ──
        if ($kategori === 'siswa') {
            $fullLeaderboard = $this->calculateSiswaLeaderboard($startDate, $effectiveEndDate, $rombelId, $taPilihan);
        } else {
            $fullLeaderboard = $this->calculateGuruLeaderboard($startDate, $effectiveEndDate);
        }

        $top1 = $fullLeaderboard->firstWhere('rank', 1);
        $top2 = $fullLeaderboard->firstWhere('rank', 2);
        $top3 = $fullLeaderboard->firstWhere('rank', 3);
        $totalRanked = $fullLeaderboard->count();

        // Paginasi 10 Data per Halaman untuk Tabel
        $perPage = 10;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $total = $fullLeaderboard->count();
        $sliced = $fullLeaderboard->slice(($page - 1) * $perPage, $perPage)->values();

        $leaderboard = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return view('peringkat.index', compact(
            'kategori', 'periode', 'semesterTipe', 'bulan', 'tanggalMulai', 'tanggalSelesai',
            'taAktif', 'semuaTa', 'taPilihan', 'rombels', 'rombelId', 'isWaliKelas', 'waliRombel',
            'periodeLabel', 'startDate', 'effectiveEndDate', 'leaderboard', 'top1', 'top2', 'top3', 'totalRanked', 'sekolah'
        ));
    }

    private function calculateSiswaLeaderboard($startDate, $endDate, $rombelId = null, $taPilihan = null)
    {
        $siswaQuery = Siswa::with(['siswaRombels.rombel']);

        if ($rombelId) {
            $siswaQuery->whereHas('siswaRombels', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            });
        } else {
            $siswaQuery->where('status', 'aktif');
        }

        $siswas = $siswaQuery->get();
        $toleransiMasukSec = (7 * 3600) + (15 * 60); // 07:15:00 = 26100 detik

        // Streaming agregasi jutaan record dengan memory footprint minimal
        $statsByPemilik = [];
        $cursor = DB::table('absensis')
            ->where('pemilik_type', 'siswa')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('pemilik_id', 'status', 'jam_masuk', 'jam_pulang')
            ->cursor();

        foreach ($cursor as $rec) {
            $pid = $rec->pemilik_id;
            if (!isset($statsByPemilik[$pid])) {
                $statsByPemilik[$pid] = [
                    'hadir_tepat' => 0,
                    'terlambat' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'dispensasi' => 0,
                    'alpha' => 0,
                    'bolos' => 0,
                    'total_record' => 0,
                    'total_masuk_sec' => 0,
                    'count_masuk' => 0,
                    'earliest_sec' => PHP_INT_MAX,
                    'total_terlambat_sec' => 0,
                    'total_durasi_sec' => 0,
                ];
            }

            $st = $rec->status;
            $statsByPemilik[$pid]['total_record']++;

            if ($st === 'hadir') $statsByPemilik[$pid]['hadir_tepat']++;
            elseif ($st === 'terlambat') $statsByPemilik[$pid]['terlambat']++;
            elseif ($st === 'izin') $statsByPemilik[$pid]['izin']++;
            elseif ($st === 'sakit') $statsByPemilik[$pid]['sakit']++;
            elseif ($st === 'dispensasi') $statsByPemilik[$pid]['dispensasi']++;
            elseif ($st === 'alpha' || $st === 'alfa') $statsByPemilik[$pid]['alpha']++;
            elseif ($st === 'bolos') $statsByPemilik[$pid]['bolos']++;

            if (!empty($rec->jam_masuk)) {
                $mSec = $this->timeToSeconds($rec->jam_masuk);
                if ($mSec > 0) {
                    $statsByPemilik[$pid]['total_masuk_sec'] += $mSec;
                    $statsByPemilik[$pid]['count_masuk']++;
                    if ($mSec < $statsByPemilik[$pid]['earliest_sec']) {
                        $statsByPemilik[$pid]['earliest_sec'] = $mSec;
                    }

                    if ($st === 'terlambat' || $mSec > $toleransiMasukSec) {
                        $statsByPemilik[$pid]['total_terlambat_sec'] += max(0, $mSec - $toleransiMasukSec);
                    }
                }
            }

            if (!empty($rec->jam_masuk) && !empty($rec->jam_pulang)) {
                $mSec = $this->timeToSeconds($rec->jam_masuk);
                $pSec = $this->timeToSeconds($rec->jam_pulang);
                if ($pSec > $mSec) {
                    $statsByPemilik[$pid]['total_durasi_sec'] += ($pSec - $mSec);
                }
            }
        }

        $result = [];

        foreach ($siswas as $s) {
            $st = $statsByPemilik[$s->id] ?? null;

            $hadirTepat            = $st['hadir_tepat'] ?? 0;
            $terlambat             = $st['terlambat'] ?? 0;
            $dispensasi            = $st['dispensasi'] ?? 0;
            $izin                  = $st['izin'] ?? 0;
            $sakit                 = $st['sakit'] ?? 0;
            $alpha                 = $st['alpha'] ?? 0;
            $bolos                 = $st['bolos'] ?? 0;
            $totalRecord           = $st['total_record'] ?? 0;
            $totalMasukSec         = $st['total_masuk_sec'] ?? 0;
            $countMasuk            = $st['count_masuk'] ?? 0;
            $earliestSec           = $st['earliest_sec'] ?? PHP_INT_MAX;
            $totalTerlambatSec     = $st['total_terlambat_sec'] ?? 0;
            $totalDurasiSekolahSec = $st['total_durasi_sec'] ?? 0;

            $totalHadir = $hadirTepat + $terlambat;
            $totalHariEfektif = max(1, $totalRecord);

            $persenKehadiran = $totalHariEfektif > 0 
                ? round((($totalHadir + $dispensasi) / $totalHariEfektif) * 100, 1) 
                : 0;

            $persenKetepatan = $totalHadir > 0 
                ? round(($hadirTepat / $totalHadir) * 100, 1) 
                : 0;

            $avgMasukSec = $countMasuk > 0 ? (int)round($totalMasukSec / $countMasuk) : 0;
            $avgMasukStr = $avgMasukSec > 0 ? $this->secondsToHMS($avgMasukSec) . ' WIB' : '-';
            $terpagiStr = ($earliestSec !== PHP_INT_MAX && $earliestSec > 0) ? $this->secondsToHMS($earliestSec) . ' WIB' : '-';
            $totalTerlambatStr = $this->formatSecondsToDetailedText($totalTerlambatSec);
            $totalDurasiSekolahStr = $this->formatSecondsToDetailedText($totalDurasiSekolahSec);

            $avgDurasiHarianSec = $countMasuk > 0 ? (int)round($totalDurasiSekolahSec / $countMasuk) : 0;
            $avgDurasiHarianStr = $this->formatSecondsToDetailedText($avgDurasiHarianSec);

            // Rumus Skor Disiplin
            $skorDisiplin = ($hadirTepat * 100) + ($terlambat * 60) + ($dispensasi * 80) + ($izin * 40) + ($sakit * 40) - ($alpha * 50) - ($bolos * 80);

            // Predikat Disiplin
            if ($totalRecord > 0 && $alpha === 0 && $bolos === 0 && $terlambat === 0 && $persenKehadiran >= 98) {
                $predikat = 'Bintang Teladan (Sempurna)';
                $predikatBadge = 'gold';
            } elseif ($persenKehadiran >= 95 && $alpha === 0 && $bolos === 0) {
                $predikat = 'Sangat Disiplin';
                $predikatBadge = 'green';
            } elseif ($persenKehadiran >= 85 && ($alpha + $bolos) <= 2) {
                $predikat = 'Disiplin Baik';
                $predikatBadge = 'blue';
            } elseif ($persenKehadiran >= 75) {
                $predikat = 'Cukup Disiplin';
                $predikatBadge = 'amber';
            } else {
                $predikat = 'Perlu Pembinaan BK';
                $predikatBadge = 'red';
            }

            // Ambil info rombel terkini
            $activeRombel = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif');
            $rombelNama = $activeRombel && $activeRombel->rombel ? $activeRombel->rombel->nama_rombel : '-';

            $result[] = [
                'id' => $s->id,
                'nama' => $s->nama,
                'nisn' => $s->nisn,
                'ident' => $s->nisn,
                'rombel' => $rombelNama,
                'sub' => $rombelNama,
                'foto' => $s->foto ?? null,
                'hadir_tepat' => $hadirTepat,
                'terlambat' => $terlambat,
                'dispensasi' => $dispensasi,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'bolos' => $bolos,
                'total_hadir' => $totalHadir,
                'total_record' => $totalRecord,
                'persen_kehadiran' => $persenKehadiran,
                'persen_ketepatan' => $persenKetepatan,
                'skor_disiplin' => $skorDisiplin,
                'predikat' => $predikat,
                'predikat_badge' => $predikatBadge,
                'avg_masuk_sec' => $avgMasukSec,
                'avg_masuk_str' => $avgMasukStr,
                'terpagi_sec' => $earliestSec,
                'terpagi_str' => $terpagiStr,
                'total_terlambat_sec' => $totalTerlambatSec,
                'total_terlambat_str' => $totalTerlambatStr,
                'total_durasi_sec' => $totalDurasiSekolahSec,
                'total_durasi_str' => $totalDurasiSekolahStr,
                'total_durasi_sekolah_str' => $totalDurasiSekolahStr,
                'avg_durasi_harian_sec' => $avgDurasiHarianSec,
                'avg_durasi_harian_str' => $avgDurasiHarianStr,
            ];
        }

        // Sorting Leaderboard
        usort($result, function ($a, $b) {
            if ($b['persen_kehadiran'] != $a['persen_kehadiran']) {
                return $b['persen_kehadiran'] <=> $a['persen_kehadiran'];
            }
            if ($b['persen_ketepatan'] != $a['persen_ketepatan']) {
                return $b['persen_ketepatan'] <=> $a['persen_ketepatan'];
            }
            if ($a['avg_masuk_sec'] != $b['avg_masuk_sec']) {
                $aTime = $a['avg_masuk_sec'] > 0 ? $a['avg_masuk_sec'] : PHP_INT_MAX;
                $bTime = $b['avg_masuk_sec'] > 0 ? $b['avg_masuk_sec'] : PHP_INT_MAX;
                return $aTime <=> $bTime;
            }
            if ($a['total_terlambat_sec'] != $b['total_terlambat_sec']) {
                return $a['total_terlambat_sec'] <=> $b['total_terlambat_sec'];
            }
            if ($b['total_durasi_sec'] != $a['total_durasi_sec']) {
                return $b['total_durasi_sec'] <=> $a['total_durasi_sec'];
            }
            if ($a['alpha'] != $b['alpha']) {
                return $a['alpha'] <=> $b['alpha'];
            }
            return $b['skor_disiplin'] <=> $a['skor_disiplin'];
        });

        // Berikan nomor peringkat
        foreach ($result as $i => &$item) {
            $item['rank'] = $i + 1;
        }

        return collect($result);
    }

    private function calculateGuruLeaderboard($startDate, $endDate)
    {
        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $toleransiMasukSec = (7 * 3600) + (15 * 60);

        $statsByPemilik = [];
        $cursor = DB::table('absensis')
            ->where('pemilik_type', 'guru')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('pemilik_id', 'status', 'jam_masuk', 'jam_pulang')
            ->cursor();

        foreach ($cursor as $rec) {
            $pid = $rec->pemilik_id;
            if (!isset($statsByPemilik[$pid])) {
                $statsByPemilik[$pid] = [
                    'hadir_tepat' => 0,
                    'terlambat' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'dinas_luar' => 0,
                    'alpha' => 0,
                    'total_record' => 0,
                    'total_masuk_sec' => 0,
                    'count_masuk' => 0,
                    'earliest_sec' => PHP_INT_MAX,
                    'total_terlambat_sec' => 0,
                    'total_durasi_sec' => 0,
                ];
            }

            $st = $rec->status;
            $statsByPemilik[$pid]['total_record']++;

            if ($st === 'hadir') $statsByPemilik[$pid]['hadir_tepat']++;
            elseif ($st === 'terlambat') $statsByPemilik[$pid]['terlambat']++;
            elseif ($st === 'izin') $statsByPemilik[$pid]['izin']++;
            elseif ($st === 'sakit') $statsByPemilik[$pid]['sakit']++;
            elseif ($st === 'dinas_luar' || $st === 'dispensasi') $statsByPemilik[$pid]['dinas_luar']++;
            elseif ($st === 'alpha' || $st === 'alfa') $statsByPemilik[$pid]['alpha']++;

            if (!empty($rec->jam_masuk)) {
                $mSec = $this->timeToSeconds($rec->jam_masuk);
                if ($mSec > 0) {
                    $statsByPemilik[$pid]['total_masuk_sec'] += $mSec;
                    $statsByPemilik[$pid]['count_masuk']++;
                    if ($mSec < $statsByPemilik[$pid]['earliest_sec']) {
                        $statsByPemilik[$pid]['earliest_sec'] = $mSec;
                    }

                    if ($st === 'terlambat' || $mSec > $toleransiMasukSec) {
                        $statsByPemilik[$pid]['total_terlambat_sec'] += max(0, $mSec - $toleransiMasukSec);
                    }
                }
            }

            if (!empty($rec->jam_masuk) && !empty($rec->jam_pulang)) {
                $mSec = $this->timeToSeconds($rec->jam_masuk);
                $pSec = $this->timeToSeconds($rec->jam_pulang);
                if ($pSec > $mSec) {
                    $statsByPemilik[$pid]['total_durasi_sec'] += ($pSec - $mSec);
                }
            }
        }

        $result = [];

        foreach ($gurus as $g) {
            $st = $statsByPemilik[$g->id] ?? null;

            $hadirTepat            = $st['hadir_tepat'] ?? 0;
            $terlambat             = $st['terlambat'] ?? 0;
            $izin                  = $st['izin'] ?? 0;
            $sakit                 = $st['sakit'] ?? 0;
            $dinasLuar             = $st['dinas_luar'] ?? 0;
            $alpha                 = $st['alpha'] ?? 0;
            $totalRecord           = $st['total_record'] ?? 0;
            $totalMasukSec         = $st['total_masuk_sec'] ?? 0;
            $countMasuk            = $st['count_masuk'] ?? 0;
            $earliestSec           = $st['earliest_sec'] ?? PHP_INT_MAX;
            $totalTerlambatSec     = $st['total_terlambat_sec'] ?? 0;
            $totalDurasiSekolahSec = $st['total_durasi_sec'] ?? 0;

            $totalHadir = $hadirTepat + $terlambat;
            $totalHariEfektif = max(1, $totalRecord);

            $persenKehadiran = $totalHariEfektif > 0 
                ? round((($totalHadir + $dinasLuar) / $totalHariEfektif) * 100, 1) 
                : 0;

            $persenKetepatan = $totalHadir > 0 
                ? round(($hadirTepat / $totalHadir) * 100, 1) 
                : 0;

            $avgMasukSec = $countMasuk > 0 ? (int)round($totalMasukSec / $countMasuk) : 0;
            $avgMasukStr = $avgMasukSec > 0 ? $this->secondsToHMS($avgMasukSec) . ' WIB' : '-';
            $terpagiStr = ($earliestSec !== PHP_INT_MAX && $earliestSec > 0) ? $this->secondsToHMS($earliestSec) . ' WIB' : '-';
            $totalTerlambatStr = $this->formatSecondsToDetailedText($totalTerlambatSec);
            $totalDurasiSekolahStr = $this->formatSecondsToDetailedText($totalDurasiSekolahSec);

            $avgDurasiHarianSec = $countMasuk > 0 ? (int)round($totalDurasiSekolahSec / $countMasuk) : 0;
            $avgDurasiHarianStr = $this->formatSecondsToDetailedText($avgDurasiHarianSec);

            $skorDisiplin = ($hadirTepat * 100) + ($terlambat * 60) + ($dinasLuar * 90) + ($izin * 40) + ($sakit * 40) - ($alpha * 60);

            if ($totalRecord > 0 && $alpha === 0 && $terlambat === 0 && $persenKehadiran >= 98) {
                $predikat = 'Guru Teladan Utama (100%)';
                $predikatBadge = 'gold';
            } elseif ($persenKehadiran >= 95 && $alpha === 0) {
                $predikat = 'Guru Sangat Disiplin';
                $predikatBadge = 'green';
            } elseif ($persenKehadiran >= 85) {
                $predikat = 'Guru Disiplin Baik';
                $predikatBadge = 'blue';
            } elseif ($persenKehadiran >= 75) {
                $predikat = 'Cukup Disiplin';
                $predikatBadge = 'amber';
            } else {
                $predikat = 'Perlu Peningkatan Disiplin';
                $predikatBadge = 'red';
            }

            $result[] = [
                'id' => $g->id,
                'nama' => $g->nama,
                'nip' => $g->nip,
                'ident' => $g->nip ?? $g->nama,
                'jabatan' => $g->jabatan ?? 'Guru / Tenaga Pengajar',
                'sub' => $g->jabatan ?? 'Guru / Tenaga Pengajar',
                'foto' => $g->foto ?? null,
                'hadir_tepat' => $hadirTepat,
                'terlambat' => $terlambat,
                'dinas_luar' => $dinasLuar,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total_hadir' => $totalHadir,
                'total_record' => $totalRecord,
                'persen_kehadiran' => $persenKehadiran,
                'persen_ketepatan' => $persenKetepatan,
                'skor_disiplin' => $skorDisiplin,
                'predikat' => $predikat,
                'predikat_badge' => $predikatBadge,
                'avg_masuk_sec' => $avgMasukSec,
                'avg_masuk_str' => $avgMasukStr,
                'terpagi_sec' => $earliestSec,
                'terpagi_str' => $terpagiStr,
                'total_terlambat_sec' => $totalTerlambatSec,
                'total_terlambat_str' => $totalTerlambatStr,
                'total_durasi_sec' => $totalDurasiSekolahSec,
                'total_durasi_str' => $totalDurasiSekolahStr,
                'total_durasi_sekolah_str' => $totalDurasiSekolahStr,
                'avg_durasi_harian_sec' => $avgDurasiHarianSec,
                'avg_durasi_harian_str' => $avgDurasiHarianStr,
            ];
        }

        usort($result, function ($a, $b) {
            if ($b['persen_kehadiran'] != $a['persen_kehadiran']) {
                return $b['persen_kehadiran'] <=> $a['persen_kehadiran'];
            }
            if ($b['persen_ketepatan'] != $a['persen_ketepatan']) {
                return $b['persen_ketepatan'] <=> $a['persen_ketepatan'];
            }
            if ($a['avg_masuk_sec'] != $b['avg_masuk_sec']) {
                $aTime = $a['avg_masuk_sec'] > 0 ? $a['avg_masuk_sec'] : PHP_INT_MAX;
                $bTime = $b['avg_masuk_sec'] > 0 ? $b['avg_masuk_sec'] : PHP_INT_MAX;
                return $aTime <=> $bTime;
            }
            if ($a['total_terlambat_sec'] != $b['total_terlambat_sec']) {
                return $a['total_terlambat_sec'] <=> $b['total_terlambat_sec'];
            }
            if ($b['total_durasi_sec'] != $a['total_durasi_sec']) {
                return $b['total_durasi_sec'] <=> $a['total_durasi_sec'];
            }
            if ($a['alpha'] != $b['alpha']) {
                return $a['alpha'] <=> $b['alpha'];
            }
            return $b['skor_disiplin'] <=> $a['skor_disiplin'];
        });

        foreach ($result as $i => &$item) {
            $item['rank'] = $i + 1;
        }

        return collect($result);
    }

    public function cetakPiagamSiswa($id, Request $request)
    {
        $siswa = Siswa::with(['siswaRombels.rombel.jurusan', 'siswaRombels.rombel.waliKelas'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();
        
        $rank = $request->input('rank', 1);
        $predikat = $request->input('predikat', 'Siswa Teladan Kehadiran');
        $periode = $request->input('periode', 'Semester Ganjil 2026/2027');
        $persen = $request->input('persen', 100);
        $avgMasuk = $request->input('avg_masuk', '-');
        $durasi = $request->input('durasi', '-');

        $activeRombel = $siswa->siswaRombels->firstWhere('status_keanggotaan', 'aktif');
        $rombel = $activeRombel?->rombel;

        return view('peringkat.piagam', [
            'tipe' => 'siswa',
            'person' => $siswa,
            'nama' => $siswa->nama,
            'nomorInduk' => "NISN: " . ($siswa->nisn ?: '-'),
            'instansi' => "Kelas: " . ($rombel?->nama_rombel ?? '-'),
            'rank' => $rank,
            'predikat' => $predikat,
            'periode' => $periode,
            'persen' => $persen,
            'avgMasuk' => $avgMasuk,
            'durasi' => $durasi,
            'sekolah' => $sekolah,
            'tanggalCetak' => Carbon::today()->translatedFormat('d F Y'),
        ]);
    }

    public function cetakPiagamGuru($id, Request $request)
    {
        $guru = Guru::findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        $rank = $request->input('rank', 1);
        $predikat = $request->input('predikat', 'Guru Teladan Disiplin');
        $periode = $request->input('periode', 'Semester Ganjil 2026/2027');
        $persen = $request->input('persen', 100);
        $avgMasuk = $request->input('avg_masuk', '-');
        $durasi = $request->input('durasi', '-');

        return view('peringkat.piagam', [
            'tipe' => 'guru',
            'person' => $guru,
            'nama' => $guru->nama,
            'nomorInduk' => "NIP: " . ($guru->nip ?: 'Non-NIP'),
            'instansi' => "Jabatan: " . ($guru->jabatan ?: 'Guru / Tenaga Pendidik'),
            'rank' => $rank,
            'predikat' => $predikat,
            'periode' => $periode,
            'persen' => $persen,
            'avgMasuk' => $avgMasuk,
            'durasi' => $durasi,
            'sekolah' => $sekolah,
            'tanggalCetak' => Carbon::today()->translatedFormat('d F Y'),
        ]);
    }

    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template_gambar' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $sekolah = PengaturanSekolah::getAktif();

        if ($request->hasFile('template_gambar')) {
            if ($sekolah->template_piagam && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->template_piagam)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sekolah->template_piagam);
            }
            $path = $request->file('template_gambar')->store('piagam', 'public');
            $sekolah->update(['template_piagam' => $path]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template gambar piagam berhasil diunggah!',
                    'url' => asset('storage/' . $path),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Template piagam berhasil diperbarui!');
    }

    public function saveTemplateConfig(Request $request)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $config = $request->input('config', []);
        
        $sekolah->update([
            'template_piagam_config' => is_array($config) ? json_encode($config) : $config,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi tata letak piagam berhasil disimpan!',
        ]);
    }

    public function resetTemplate(Request $request)
    {
        $sekolah = PengaturanSekolah::getAktif();
        if ($sekolah->template_piagam && \Illuminate\Support\Facades\Storage::disk('public')->exists($sekolah->template_piagam)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($sekolah->template_piagam);
        }
        $sekolah->update([
            'template_piagam' => null,
            'template_piagam_config' => null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template piagam berhasil di-reset.',
            ]);
        }

        return redirect()->back()->with('success', 'Template piagam berhasil di-reset.');
    }

    public function exportCsv(Request $request)
    {
        $kategori = $request->input('kategori', 'siswa');
        $periode = $request->input('periode', 'semester');
        $semesterTipe = $request->input('semester', 'ganjil');
        $bulan = $request->input('bulan', Carbon::today()->format('Y-m'));
        $rombelId = $request->input('rombel_id');

        $taAktif = TahunAjaran::where('is_active', true)->first() ?: TahunAjaran::latest()->first();
        $tahunAjaranId = $request->input('tahun_ajaran_id', $taAktif?->id);
        $taPilihan = TahunAjaran::find($tahunAjaranId) ?: $taAktif;

        $yearStart = $taPilihan ? (int)substr($taPilihan->nama, 0, 4) : (int)date('Y');
        $yearEnd = $taPilihan ? (int)substr($taPilihan->nama, 5, 4) : ($yearStart + 1);

        if ($periode === 'semester') {
            if ($semesterTipe === 'ganjil') {
                $startDate = Carbon::create($yearStart, 7, 1)->toDateString();
                $endDate = Carbon::create($yearStart, 12, 31)->toDateString();
                $periodeName = "Semester_Ganjil_{$yearStart}_{$yearEnd}";
            } else {
                $startDate = Carbon::create($yearEnd, 1, 1)->toDateString();
                $endDate = Carbon::create($yearEnd, 6, 30)->toDateString();
                $periodeName = "Semester_Genap_{$yearStart}_{$yearEnd}";
            }
        } elseif ($periode === 'bulan') {
            $cMonth = Carbon::createFromFormat('Y-m', $bulan);
            $startDate = $cMonth->copy()->startOfMonth()->toDateString();
            $endDate = $cMonth->copy()->endOfMonth()->toDateString();
            $periodeName = "Bulan_" . $cMonth->format('Y_m');
        } else {
            $startDate = $request->input('tanggal_mulai', Carbon::today()->startOfMonth()->toDateString());
            $endDate = $request->input('tanggal_selesai', Carbon::today()->toDateString());
            $periodeName = "Rentang_{$startDate}_sd_{$endDate}";
        }

        $effectiveEndDate = Carbon::parse($endDate)->gt(Carbon::today()) ? Carbon::today()->toDateString() : $endDate;

        if ($kategori === 'siswa') {
            $data = $this->calculateSiswaLeaderboard($startDate, $effectiveEndDate, $rombelId, $taPilihan);
            $filename = "Peringkat_Presisi_Siswa_{$periodeName}.csv";
        } else {
            $data = $this->calculateGuruLeaderboard($startDate, $effectiveEndDate);
            $filename = "Peringkat_Presisi_Guru_{$periodeName}.csv";
        }

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($data, $kategori) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fwrite($file, "sep=;\n");

            fputcsv($file, [
                'Peringkat',
                $kategori === 'siswa' ? 'NISN' : 'NIP',
                'Nama Lengkap',
                $kategori === 'siswa' ? 'Rombel / Kelas' : 'Jabatan',
                'Hadir Tepat Waktu',
                'Terlambat',
                'Izin / Sakit / Dispensasi',
                'Alpha / Bolos',
                'Total Kehadiran',
                'Persentase Kehadiran (%)',
                'Ketepatan Waktu (%)',
                'Rata-rata Waktu Kedatangan (WIB)',
                'Rekor Masuk Terpagi (WIB)',
                'Akumulasi Waktu Terlambat',
                'Total Akumulasi Durasi di Sekolah',
                'Rata-rata Durasi di Sekolah / Hari',
                'Skor Disiplin',
                'Predikat Teladan'
            ], ';');

            foreach ($data as $d) {
                fputcsv($file, [
                    $d['rank'],
                    '="' . $d['ident'] . '"',
                    $d['nama'],
                    $d['sub'],
                    $d['hadir_tepat'],
                    $d['terlambat'],
                    $d['izin'] + $d['sakit'] + $d['dispensasi'],
                    $d['alpha'] + ($d['bolos'] ?? 0),
                    $d['total_hadir'],
                    $d['persen_kehadiran'] . '%',
                    $d['persen_ketepatan'] . '%',
                    $d['avg_masuk_str'],
                    $d['terpagi_str'],
                    $d['total_terlambat_str'],
                    $d['total_durasi_str'],
                    $d['avg_durasi_harian_str'],
                    $d['skor_disiplin'],
                    $d['predikat']
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function timeToSeconds(?string $time): int
    {
        if (empty($time)) return 0;
        $parts = explode(':', $time);
        $h = isset($parts[0]) ? (int)$parts[0] : 0;
        $m = isset($parts[1]) ? (int)$parts[1] : 0;
        $s = isset($parts[2]) ? (int)$parts[2] : 0;
        return ($h * 3600) + ($m * 60) + $s;
    }

    private function secondsToHMS(int $seconds): string
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    private function formatSecondsToDetailedText(int $seconds): string
    {
        if ($seconds <= 0) return '0 Jam 0 Menit 0 Detik';
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;

        $parts = [];
        $parts[] = "{$h} Jam";
        $parts[] = "{$m} Menit";
        $parts[] = "{$s} Detik";

        return implode(' ', $parts);
    }

    private function getWaliRombel($user)
    {
        if (!$user || !$user->guru) return null;
        return Rombel::where('wali_kelas_id', $user->guru->id)->first();
    }
}
