<?php

namespace App\Http\Controllers;

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

        if ($keyword !== '') {
            $cleanPhone = preg_replace('/[^0-9]/', '', $keyword);

            $siswa = Siswa::where(function ($q) use ($keyword, $cleanPhone) {
                $q->where('nis', $keyword)
                  ->orWhere('nisn', $keyword);

                if (!empty($cleanPhone) && strlen($cleanPhone) >= 7) {
                    $phoneTrim = ltrim($cleanPhone, '0');
                    if (str_starts_with($phoneTrim, '62')) {
                        $phoneTrim = substr($phoneTrim, 2);
                    }
                    $q->orWhere('no_hp_ortu', 'LIKE', "%{$cleanPhone}%")
                      ->orWhere('no_hp_ortu', 'LIKE', "%{$phoneTrim}%")
                      ->orWhere('no_hp_siswa', 'LIKE', "%{$cleanPhone}%")
                      ->orWhere('no_hp_siswa', 'LIKE', "%{$phoneTrim}%");
                }
            })->first();

            if ($siswa) {
                // Ambil Rombel aktif dan Wali Kelas
                $siswaRombel = $siswa->siswaRombels()
                    ->where('status_keanggotaan', 'aktif')
                    ->with('rombel.waliKelas', 'rombel.jurusan')
                    ->first();

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

                // Berkas Dossier Karakter & Kedisiplinan Siswa
                $kasusDisiplin = \App\Models\KasusDisiplin::where('siswa_id', $siswa->id)
                    ->where('is_active', true)
                    ->with([
                        'rewards' => fn($q) => $q->orderBy('tanggal', 'desc')->take(6),
                        'pelanggarans' => fn($q) => $q->orderBy('tanggal', 'desc')->take(6),
                    ])
                    ->first();

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
            }
        }

        $pengaturanDisiplin = \App\Models\PengaturanDisiplin::getPengaturan();
        $pengumumans = \App\Models\Pengumuman::forPortal()->latest()->get();

        return view('portal_ortu.index', compact(
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
            'rekapBulananTahunan'
        ));
    }

    /**
     * Akses langsung rekapitulasi kehadiran siswa via URL /presensi-siswa/{nis} atau /cek-presensi/{nis}.
     */
    public function detail($nis, Request $request)
    {
        $request->merge(['keyword' => $nis]);
        return $this->index($request);
    }
}
