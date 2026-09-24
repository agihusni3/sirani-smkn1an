<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

use App\Models\Absensi;
use App\Models\IzinSiswa;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PortalOrtuController extends Controller
{
    /**
     * Tampilkan portal cek kehadiran mandiri siswa & orang tua.
     */
    public function index(Request $request)
    {
        $keyword = trim($request->get('keyword') ?: $request->get('nis') ?: $request->get('nisn') ?: '');
        $periode = $request->get('periode', 'harian');
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::today()->startOfWeek()->toDateString());
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::today()->endOfWeek()->toDateString());
        $bulanSelected = $request->get('bulan', Carbon::today()->format('Y-m'));
        $tahunSelected = $request->get('tahun', Carbon::today()->format('Y'));

        $siswa = null;
        $rombel = null;
        $waliKelas = null;
        $todayAbsensi = null;
        $kasusDisiplin = null;
        $rincianPelanggaran = collect();
        $katalogRewardsList = collect();
        $absensis = collect();
        $izins = collect();
        $rekapBulananTahunan = [];
        $rekapMingguanBulanan = [];
        $periodeText = '';
        $serverNotifs = [];
        $koreksiTerbaru = null;
        $stats = [
            'hadir'     => 0,
            'terlambat' => 0,
            'izin'      => 0,
            'sakit'     => 0,
            'alpha'     => 0,
            'bolos'     => 0,
            'total'     => 0,
            'persen'    => 100,
            'predikat'  => 'Sangat Baik',
        ];

        $modeAkses = $request->get('mode') ?: $request->get('role') ?: null;
        $codeValue = '';

        if ($keyword !== '') {
            $cleanKeyword = trim($keyword);
            $digitsOnly = preg_replace('/[^0-9]/', '', $cleanKeyword);
            $candidateNisns = array_unique(array_filter([
                $cleanKeyword,
                $digitsOnly,
                $digitsOnly !== '' ? ltrim($digitsOnly, '0') : null,
                $digitsOnly !== '' ? str_pad($digitsOnly, 10, '0', STR_PAD_LEFT) : null,
            ]));

            // Pencarian siswa berdasarkan NISN (fleksibel awalan 0 atau 10 digit NISN standar)
            $siswa = Siswa::whereIn('nisn', $candidateNisns)
                ->orWhere('id', $cleanKeyword)
                ->with('kartuRfid')
                ->first();

            // Default mode jika belum terdeteksi
            $modeAkses = $modeAkses ?: 'ortu';


            if ($siswa) {
                $siswa->loadMissing('kartuRfid');
                $codeValue = $siswa->kartuRfid?->uid ?? $siswa->nisn;
                // Ambil Rombel aktif dan Wali Kelas
                $siswaRombel = $siswa->siswaRombels()
                    ->where('status_keanggotaan', 'aktif')
                    ->with('rombel.waliKelas', 'rombel.jurusan')
                    ->first();

                // Fallback untuk alumni atau siswa transisi: ambil riwayat rombel terakhir yang diduduki
                if (!$siswaRombel) {
                    $siswaRombel = $siswa->siswaRombels()
                        ->with('rombel.waliKelas', 'rombel.jurusan')
                        ->latest('id')
                        ->first();
                }

                $rombel = $siswaRombel?->rombel;
                $waliKelas = $rombel?->waliKelas;

                // Absensi Hari Ini
                $todayAbsensi = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $siswa->id)
                    ->where('tanggal', Carbon::today()->toDateString())
                    ->first();

                // Tentukan rentang tanggal berdasarkan periode yang dipilih
                if ($periode === 'harian') {
                    $startDate = $tanggal;
                    $endDate = $tanggal;
                    $periodeText = 'Harian: ' . format_tanggal_indo($tanggal);
                } elseif ($periode === 'mingguan') {
                    $startDate = $tanggalMulai;
                    $endDate = $tanggalSelesai;
                    $periodeText = 'Mingguan: ' . Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y');
                } elseif ($periode === 'tahunan') {
                    $startDate = "{$tahunSelected}-01-01";
                    $endDate = "{$tahunSelected}-12-31";
                    $periodeText = "Tahunan: Tahun {$tahunSelected}";
                } else {
                    // Bulanan
                    try {
                        $startOfMonth = Carbon::createFromFormat('Y-m', $bulanSelected)->startOfMonth();
                        $endOfMonth = Carbon::createFromFormat('Y-m', $bulanSelected)->endOfMonth();
                    } catch (\Exception $e) {
                        $bulanSelected = Carbon::today()->format('Y-m');
                        $startOfMonth = Carbon::today()->startOfMonth();
                        $endOfMonth = Carbon::today()->endOfMonth();
                    }
                    $startDate = $startOfMonth->toDateString();
                    $endDate = $endOfMonth->toDateString();
                    $periodeText = 'Bulanan: ' . Carbon::parse($startOfMonth)->locale('id')->translatedFormat('F Y');
                }

                $absensis = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $siswa->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->orderBy('tanggal', 'desc')
                    ->get();

                // Hitung Statistik Periode
                $hadir = $absensis->where('status', 'hadir')->count();
                $terlambat = $absensis->where('status', 'terlambat')->count();
                $izin = $absensis->whereIn('status', ['izin', 'dispen', 'dispensasi'])->count();
                $sakit = $absensis->where('status', 'sakit')->count();
                $alpha = $absensis->whereIn('status', ['alpha', 'alfa'])->count();
                $bolos = $absensis->where('status', 'bolos')->count();
                $totalHari = $hadir + $terlambat + $izin + $sakit + $alpha + $bolos;

                $persen = $totalHari > 0 ? round((($hadir + $terlambat) / $totalHari) * 100, 1) : 100;

                if ($persen >= 90) {
                    $predikat = 'Sangat Baik';
                } elseif ($persen >= 80) {
                    $predikat = 'Baik';
                } elseif ($persen >= 70) {
                    $predikat = 'Cukup';
                } else {
                    $predikat = 'Perlu Perhatian';
                }

                $stats = [
                    'hadir'     => $hadir,
                    'terlambat' => $terlambat,
                    'izin'      => $izin,
                    'sakit'     => $sakit,
                    'alpha'     => $alpha,
                    'bolos'     => $bolos,
                    'total'     => $totalHari,
                    'persen'    => $persen,
                    'predikat'  => $predikat,
                ];

                // Riwayat Surat Izin Siswa
                $izins = IzinSiswa::where('siswa_id', $siswa->id)
                    ->orderBy('tanggal', 'desc')
                    ->take(10)
                    ->get();

                // Berkas Dossier Karakter & Riwayat Kasus Kedisiplinan Siswa
                try {
                    $kasusDisiplin = \App\Models\KasusDisiplin::syncFromPresensi($siswa->id);
                    $kasusDisiplin->loadMissing([
                        'rewards' => fn($q) => $q->orderBy('tanggal', 'desc')->take(20),
                        'pelanggarans' => fn($q) => $q->orderBy('tanggal', 'desc')->take(20),
                    ]);
                } catch (\Throwable $e) {
                    $kasusDisiplin = \App\Models\KasusDisiplin::where('siswa_id', $siswa->id)
                        ->with([
                            'rewards' => fn($q) => $q->orderBy('tanggal', 'desc')->take(20),
                            'pelanggarans' => fn($q) => $q->orderBy('tanggal', 'desc')->take(20),
                        ])
                        ->first();
                }

                // Susun Kronologis Detail Rincian Pelanggaran & Presensi Terkait Disiplin
                $pengaturanDisiplin = \App\Models\PengaturanDisiplin::getPengaturan();
                $absensiPelanggaran = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $siswa->id)
                    ->whereIn('status', ['bolos', 'alpha', 'terlambat'])
                    ->orderBy('tanggal', 'desc')
                    ->take(50)
                    ->get();

                $rincianPelanggaran = collect();
                foreach ($absensiPelanggaran as $ab) {
                    $poin = match($ab->status) {
                        'bolos'     => (int)($pengaturanDisiplin->bobot_bolos ?? 15),
                        'alpha'     => (int)($pengaturanDisiplin->bobot_alpha ?? 10),
                        'terlambat' => (int)($pengaturanDisiplin->bobot_terlambat ?? 3),
                        default     => 0,
                    };
                    $label = match($ab->status) {
                        'bolos'     => 'Bolos Jam Pelajaran',
                        'alpha'     => 'Alpha (Tidak Hadir)',
                        'terlambat' => 'Terlambat Masuk',
                        default     => ucfirst($ab->status),
                    };
                    $rincianPelanggaran->push((object)[
                        'id'         => 'abs-' . $ab->id,
                        'tipe'       => 'presensi',
                        'status'     => $ab->status,
                        'judul'      => $label,
                        'poin'       => $poin,
                        'tanggal'    => $ab->tanggal,
                        'jam'        => $ab->status === 'terlambat' ? ($ab->jam_masuk ? substr($ab->jam_masuk, 0, 5) : null) : ($ab->jam_pulang ? substr($ab->jam_pulang, 0, 5) : null),
                        'sumber'     => $ab->sumber_absen_label,
                        'keterangan' => $ab->keterangan,
                    ]);
                }

                if ($kasusDisiplin && $kasusDisiplin->pelanggarans) {
                    foreach ($kasusDisiplin->pelanggarans as $pel) {
                        $tglStr = $pel->tanggal ? \Carbon\Carbon::parse($pel->tanggal)->toDateString() : ($pel->created_at ? $pel->created_at->toDateString() : null);
                        $rincianPelanggaran->push((object)[
                            'id'         => 'pel-' . $pel->id,
                            'tipe'       => 'tata_tertib',
                            'status'     => 'tata_tertib',
                            'judul'      => $pel->nama_pelanggaran,
                            'poin'       => (int)$pel->poin_ditambah,
                            'tanggal'    => $tglStr,
                            'jam'        => null,
                            'sumber'     => $pel->dicatat_oleh ?: 'Tim Ketertiban',
                            'keterangan' => $pel->catatan,
                        ]);
                    }
                }
                $rincianPelanggaran = $rincianPelanggaran->sortByDesc('tanggal')->values();

                // Daftar Panduan Reward / Pemulihan Poin Aktif
                $katalogRewardsList = \App\Models\KatalogReward::where('is_active', true)
                    ->orderBy('poin_deduksi', 'desc')
                    ->take(6)
                    ->get();

                // Rekapitulasi Jumlah per Bulan untuk Laporan Tahunan
                $rekapBulananTahunan = [];
                if ($periode === 'tahunan') {
                    for ($m = 1; $m <= 12; $m++) {
                        $mCarbon = Carbon::create((int)$tahunSelected, $m, 1);
                        $mStart = $mCarbon->copy()->startOfMonth()->toDateString();
                        $mEnd = $mCarbon->copy()->endOfMonth()->toDateString();

                        $mAbs = $absensis->whereBetween('tanggal', [$mStart, $mEnd]);
                        $mHadir = $mAbs->where('status', 'hadir')->count();
                        $mTelat = $mAbs->where('status', 'terlambat')->count();
                        $mIzin = $mAbs->whereIn('status', ['izin', 'dispen', 'dispensasi'])->count();
                        $mSakit = $mAbs->where('status', 'sakit')->count();
                        $mAlpha = $mAbs->whereIn('status', ['alpha', 'alfa'])->count();
                        $mBolos = $mAbs->where('status', 'bolos')->count();
                        $mTotal = $mHadir + $mTelat + $mIzin + $mSakit + $mAlpha + $mBolos;
                        $mPersen = $mTotal > 0 ? round((($mHadir + $mTelat) / $mTotal) * 100, 1) : null;

                        $rekapBulananTahunan[] = [
                            'bulan_num'  => $m,
                            'bulan_nama' => $mCarbon->locale('id')->translatedFormat('F'),
                            'hadir'      => $mHadir,
                            'terlambat'  => $mTelat,
                            'izin'       => $mIzin,
                            'sakit'      => $mSakit,
                            'alpha'      => $mAlpha,
                            'bolos'      => $mBolos,
                            'total'      => $mTotal,
                            'persen'     => $mPersen,
                        ];
                    }
                }

                // Rekapitulasi Jumlah per Minggu untuk Laporan Bulanan
                $rekapMingguanBulanan = [];
                if ($periode === 'bulanan') {
                    try {
                        $startOfMonth = Carbon::createFromFormat('Y-m', $bulanSelected)->startOfMonth();
                        $endOfMonth = Carbon::createFromFormat('Y-m', $bulanSelected)->endOfMonth();
                    } catch (\Exception $e) {
                        $startOfMonth = Carbon::today()->startOfMonth();
                        $endOfMonth = Carbon::today()->endOfMonth();
                    }

                    $daysInMonth = $startOfMonth->daysInMonth;
                    $monthShort = $startOfMonth->locale('id')->translatedFormat('M');
                    $weekRanges = [
                        ['num' => 1, 'start' => 1,  'end' => min(7, $daysInMonth)],
                        ['num' => 2, 'start' => 8,  'end' => min(14, $daysInMonth)],
                        ['num' => 3, 'start' => 15, 'end' => min(21, $daysInMonth)],
                        ['num' => 4, 'start' => 22, 'end' => min(28, $daysInMonth)],
                    ];
                    if ($daysInMonth > 28) {
                        $weekRanges[] = ['num' => 5, 'start' => 29, 'end' => $daysInMonth];
                    }

                    foreach ($weekRanges as $wr) {
                        $wStart = $startOfMonth->copy()->day($wr['start'])->toDateString();
                        $wEnd   = $startOfMonth->copy()->day($wr['end'])->toDateString();

                        $wAbs = $absensis->whereBetween('tanggal', [$wStart, $wEnd]);
                        $wHadir = $wAbs->where('status', 'hadir')->count();
                        $wTelat = $wAbs->where('status', 'terlambat')->count();
                        $wIzin = $wAbs->whereIn('status', ['izin', 'dispen', 'dispensasi'])->count();
                        $wSakit = $wAbs->where('status', 'sakit')->count();
                        $wAlpha = $wAbs->whereIn('status', ['alpha', 'alfa'])->count();
                        $wBolos = $wAbs->where('status', 'bolos')->count();
                        $wTotal = $wHadir + $wTelat + $wIzin + $wSakit + $wAlpha + $wBolos;
                        $wPersen = $wTotal > 0 ? round((($wHadir + $wTelat) / $wTotal) * 100, 1) : null;

                        $startStr = str_pad($wr['start'], 2, '0', STR_PAD_LEFT);
                        $endStr   = str_pad($wr['end'], 2, '0', STR_PAD_LEFT);

                        $rekapMingguanBulanan[] = [
                            'minggu_num'  => $wr['num'],
                            'minggu_nama' => "Minggu Ke-{$wr['num']}",
                            'rentang'     => "{$startStr} - {$endStr} {$monthShort}",
                            'hadir'       => $wHadir,
                            'terlambat'   => $wTelat,
                            'izin'        => $wIzin,
                            'sakit'       => $wSakit,
                            'alpha'       => $wAlpha,
                            'bolos'       => $wBolos,
                            'total'       => $wTotal,
                            'persen'      => $wPersen,
                        ];
                    }
                }

                // Rekapitulasi Riwayat Notifikasi & Koreksi Terpadu untuk Portal Orang Tua
                $serverNotifs = [];
                $koreksiTerbaru = null;

                // 1. Ambil dari tabel NotifikasiOrtu khusus hari ini (reset jika berganti hari)
                $dbNotifs = \App\Models\NotifikasiOrtu::where('siswa_id', $siswa->id)
                    ->whereDate('created_at', Carbon::today())
                    ->orderBy('created_at', 'desc')
                    ->take(25)
                    ->get();

                foreach ($dbNotifs as $dn) {
                    $isKoreksi = str_contains(strtolower($dn->kategori ?? ''), 'koreksi') || str_contains(strtolower($dn->judul ?? ''), 'koreksi');
                    $serverNotifs[] = [
                        'id'       => 'db-notif-' . $dn->id,
                        'title'    => $dn->judul ?: 'Pemberitahuan Presensi',
                        'body'     => $dn->pesan ?: '',
                        'time'     => $dn->created_at ? $dn->created_at->timestamp * 1000 : now()->timestamp * 1000,
                        'is_read'  => false,
                        'tipe'     => $isKoreksi ? 'koreksi' : 'notifikasi',
                        'kategori' => $dn->kategori,
                        'tanggal'  => $dn->tanggal ? $dn->tanggal->format('Y-m-d') : null,
                        'url'      => '/presensi-siswa/' . ($siswa->nisn ?: $siswa->id),
                    ];
                }

                // 2. Ambil catatan Absensi hasil koreksi guru piket / intervensi khusus hari ini
                $koreksiAbsensis = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $siswa->id)
                    ->whereDate('updated_at', Carbon::today())
                    ->where(function($q) {
                        $q->whereIn('sumber_absen', ['koreksi_piket_manual', 'interfensi_titip_kartu', 'manual_izin_piket'])
                          ->orWhere('keterangan', 'LIKE', '%koreksi%')
                          ->orWhere('keterangan', 'LIKE', '%dikoreksi%')
                          ->orWhere('keterangan', 'LIKE', '%intervensi%');
                    })
                    ->orderBy('updated_at', 'desc')
                    ->take(15)
                    ->get();

                foreach ($koreksiAbsensis as $ka) {
                    $labelStatus = match($ka->status) {
                        'hadir'     => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'sakit'     => 'Sakit',
                        'izin'      => 'Izin',
                        'dispen', 'dispensasi' => 'Dispensasi',
                        'alpha'     => 'Alpha',
                        'bolos'     => 'Bolos',
                        default     => ucfirst($ka->status),
                    };
                    $dateId = $ka->tanggal ? Carbon::parse($ka->tanggal)->translatedFormat('d M Y') : '';
                    $notifId = 'koreksi-abs-' . $ka->id . '-' . strtotime($ka->updated_at ?: $ka->tanggal);

                    $alreadyInDb = collect($serverNotifs)->contains(function($item) use ($ka) {
                        return ($item['tanggal'] ?? '') === $ka->tanggal && ($item['tipe'] ?? '') === 'koreksi';
                    });

                    if (!$alreadyInDb) {
                        $serverNotifs[] = [
                            'id'       => $notifId,
                            'title'    => "Koreksi Presensi: {$siswa->nama} ({$labelStatus})",
                            'body'     => "Data presensi ananda untuk tanggal {$dateId} telah disesuaikan menjadi {$labelStatus}." . ($ka->keterangan ? " Catatan: {$ka->keterangan}" : ""),
                            'time'     => ($ka->updated_at ? $ka->updated_at->timestamp : strtotime($ka->tanggal . ' 12:00:00')) * 1000,
                            'is_read'  => false,
                            'tipe'     => 'koreksi',
                            'kategori' => 'koreksi_presensi',
                            'tanggal'  => $ka->tanggal,
                            'url'      => '/presensi-siswa/' . ($siswa->nisn ?: $siswa->id),
                        ];
                    }

                    if (!$koreksiTerbaru && Carbon::parse($ka->updated_at ?: $ka->tanggal)->isToday()) {
                        $koreksiTerbaru = $ka;
                    }
                }

                // Urutkan notifikasi berdasarkan waktu descending
                usort($serverNotifs, fn($a, $b) => ($b['time'] <=> $a['time']));
            }
        }

        $pengaturanDisiplin = \App\Models\PengaturanDisiplin::getPengaturan();
        $pengumumans = \App\Models\Pengumuman::forPortal()->latest()->get();

        return view('sirani.portal_ortu.index', compact(
            'keyword',
            'periode',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'bulanSelected',
            'tahunSelected',
            'periodeText',
            'siswa',
            'rombel',
            'waliKelas',
            'todayAbsensi',
            'absensis',
            'izins',
            'stats',
            'pengumumans',
            'kasusDisiplin',
            'pengaturanDisiplin',
            'rincianPelanggaran',
            'katalogRewardsList',
            'rekapBulananTahunan',
            'rekapMingguanBulanan',
            'modeAkses',
            'codeValue',
            'serverNotifs',
            'koreksiTerbaru'
        ));
    }

    /**
     * Akses langsung rekapitulasi kehadiran siswa via URL /presensi-siswa/{nis} atau /cek-presensi/{nis}.
     */
    public function detail($nis, ?Request $request = null)
    {
        $req = $request ?: request();
        $req->merge(['keyword' => $nis]);
        return $this->index($req);
    }

    /**
     * API Real-Time Polling Notifikasi Terbaru Portal Orang Tua
     */
    public function getRecentNotifications(Request $request)
    {
        $keyword = trim($request->input('keyword') ?: $request->input('nisn') ?: '');
        if (!$keyword) {
            return response()->json(['status' => 'error', 'message' => 'NISN/Keyword tidak disertakan'], 400);
        }

        $cleanKeyword = preg_replace('/[^a-zA-Z0-9]/', '', $keyword);
        $digitsOnly = preg_replace('/[^0-9]/', '', $cleanKeyword);
        $candidateNisns = array_unique(array_filter([
            $cleanKeyword,
            $digitsOnly,
            $digitsOnly !== '' ? ltrim($digitsOnly, '0') : null,
            $digitsOnly !== '' ? str_pad($digitsOnly, 10, '0', STR_PAD_LEFT) : null,
        ]));

        $siswa = Siswa::whereIn('nisn', $candidateNisns)
            ->orWhere('nis', $cleanKeyword)
            ->orWhere('id', $cleanKeyword)
            ->first();

        if (!$siswa) {
            return response()->json(['status' => 'error', 'message' => 'Siswa tidak ditemukan'], 404);
        }

        $since = $request->input('since');
        if ($since && is_numeric($since)) {
            $sinceCarbon = Carbon::createFromTimestampMs((int)$since)->addSecond();
        } else {
            $sinceCarbon = Carbon::now()->subMinutes(5);
        }

        $dbNotifs = \App\Models\NotifikasiOrtu::where('siswa_id', $siswa->id)
            ->whereDate('created_at', Carbon::today())
            ->where('created_at', '>', $sinceCarbon)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $koreksiAbsensis = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->whereDate('updated_at', Carbon::today())
            ->where('updated_at', '>', $sinceCarbon)
            ->where(function($q) {
                $q->whereIn('sumber_absen', ['koreksi_piket_manual', 'interfensi_titip_kartu', 'manual_izin_piket'])
                  ->orWhere('keterangan', 'LIKE', '%koreksi%')
                  ->orWhere('keterangan', 'LIKE', '%dikoreksi%')
                  ->orWhere('keterangan', 'LIKE', '%intervensi%');
            })
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $notifs = [];
        foreach ($dbNotifs as $dn) {
            $isKoreksi = str_contains(strtolower($dn->kategori ?? ''), 'koreksi') || str_contains(strtolower($dn->judul ?? ''), 'koreksi');
            $notifs[] = [
                'id'       => 'db-notif-' . $dn->id,
                'title'    => $dn->judul ?: 'Pemberitahuan Presensi',
                'body'     => $dn->pesan ?: '',
                'time'     => $dn->created_at ? $dn->created_at->timestamp * 1000 : now()->timestamp * 1000,
                'is_read'  => false,
                'tipe'     => $isKoreksi ? 'koreksi' : 'notifikasi',
                'kategori' => $dn->kategori,
                'tanggal'  => $dn->tanggal ? $dn->tanggal->format('Y-m-d') : null,
                'url'      => '/presensi-siswa/' . ($siswa->nisn ?: $siswa->id),
            ];
        }

        foreach ($koreksiAbsensis as $ka) {
            $labelStatus = match($ka->status) {
                'hadir'     => 'Hadir',
                'terlambat' => 'Terlambat',
                'sakit'     => 'Sakit',
                'izin'      => 'Izin',
                'dispen', 'dispensasi' => 'Dispensasi',
                'alpha'     => 'Alpha',
                'bolos'     => 'Bolos',
                default     => ucfirst($ka->status),
            };
            $dateId = $ka->tanggal ? Carbon::parse($ka->tanggal)->translatedFormat('d M Y') : '';
            $notifs[] = [
                'id'       => 'koreksi-abs-' . $ka->id . '-' . strtotime($ka->updated_at ?: $ka->tanggal),
                'title'    => "Koreksi Presensi: {$siswa->nama} ({$labelStatus})",
                'body'     => "Data kehadiran ananda tanggal {$dateId} diperbarui menjadi {$labelStatus}." . ($ka->keterangan ? " Catatan: {$ka->keterangan}" : ""),
                'time'     => ($ka->updated_at ? $ka->updated_at->timestamp : now()->timestamp) * 1000,
                'is_read'  => false,
                'tipe'     => 'koreksi',
                'kategori' => 'koreksi_presensi',
                'tanggal'  => $ka->tanggal,
                'url'      => '/presensi-siswa/' . ($siswa->nisn ?: $siswa->id),
            ];
        }

        return response()->json([
            'status' => 'success',
            'notifs' => $notifs,
            'count'  => count($notifs),
        ]);
    }
}

