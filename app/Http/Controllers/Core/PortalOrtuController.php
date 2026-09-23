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
        $periode = $request->get('periode', 'bulanan');
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
        $absensis = collect();
        $izins = collect();
        $rekapBulananTahunan = [];
        $periodeText = '';
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
            // Pencarian siswa hanya berdasarkan NISN saja
            $siswa = Siswa::where('nisn', $keyword)
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
                    $periodeText = 'Harian: ' . Carbon::parse($tanggal)->translatedFormat('l, d F Y');
                } elseif ($periode === 'mingguan') {
                    $startDate = $tanggalMulai;
                    $endDate = $tanggalSelesai;
                    $periodeText = 'Mingguan: ' . Carbon::parse($startDate)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($endDate)->translatedFormat('d M Y');
                } elseif ($periode === 'tahunan') {
                    $startDate = "{$tahunSelected}-01-01";
                    $endDate = "{$tahunSelected}-12-31";
                    $periodeText = "Tahunan: Tahun {$tahunSelected}";
                } else {
                    // Bulanan (default)
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
                    $periodeText = 'Bulanan: ' . Carbon::parse($startOfMonth)->translatedFormat('F Y');
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
                        'rewards' => fn($q) => $q->orderBy('tanggal', 'desc')->take(10),
                        'pelanggarans' => fn($q) => $q->orderBy('tanggal', 'desc')->take(10),
                    ]);
                } catch (\Throwable $e) {
                    $kasusDisiplin = \App\Models\KasusDisiplin::where('siswa_id', $siswa->id)
                        ->with([
                            'rewards' => fn($q) => $q->orderBy('tanggal', 'desc')->take(10),
                            'pelanggarans' => fn($q) => $q->orderBy('tanggal', 'desc')->take(10),
                        ])
                        ->first();
                }

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
                            'bulan_nama' => $mCarbon->translatedFormat('F'),
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

                // Rekapitulasi Riwayat Notifikasi & Koreksi Terpadu untuk Portal Orang Tua
                $serverNotifs = [];
                $koreksiTerbaru = null;

                // 1. Ambil dari tabel NotifikasiOrtu
                $dbNotifs = \App\Models\NotifikasiOrtu::where('siswa_id', $siswa->id)
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

                // 2. Ambil catatan Absensi hasil koreksi guru piket / intervensi
                $koreksiAbsensis = Absensi::where('pemilik_type', 'siswa')
                    ->where('pemilik_id', $siswa->id)
                    ->where(function($q) {
                        $q->whereIn('sumber_absen', ['koreksi_piket_manual', 'interfensi_titip_kartu', 'manual_izin_piket'])
                          ->orWhere('keterangan', 'LIKE', '%koreksi%')
                          ->orWhere('keterangan', 'LIKE', '%dikoreksi%')
                          ->orWhere('keterangan', 'LIKE', '%intervensi%');
                    })
                    ->orderBy('tanggal', 'desc')
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

                    if (!$koreksiTerbaru && Carbon::parse($ka->updated_at ?: $ka->tanggal)->gte(Carbon::today()->subDays(7))) {
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
            'rekapBulananTahunan',
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
        $siswa = Siswa::where('nisn', $cleanKeyword)
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
            ->where('created_at', '>', $sinceCarbon)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $koreksiAbsensis = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
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

