<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        \App\Services\EvaluasiPresensiService::evaluasiOtomatisJikaWaktunya($today);

        $taAktif = TahunAjaran::where('is_active', true)->first();

        // ── 1. DATA SISWA ──
        $totalSiswaActive = Siswa::where('status', 'aktif')->count();
        $totalSiswaPkl    = Siswa::where('status', 'aktif')->where('status_pkl', 'aktif_pkl')->count();
        $siswaHadir       = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'hadir')->count();
        $siswaTerlambat   = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'terlambat')->count();
        $siswaIzin        = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->whereIn('status', ['sakit', 'izin', 'dispen'])->count();
        $siswaAlpha       = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'alpha')->count();
        $persenSekolah    = $totalSiswaActive > 0 ? min(100.0, round((($siswaHadir + $siswaTerlambat) / $totalSiswaActive) * 100, 1)) : 0;

        // Semua siswa aktif beserta rombelnya (hanya kolom yg dibutuhkan JS agar JSON ringan)
        $semuaSiswaList = Siswa::where('status', 'aktif')
            ->select('id', 'nis', 'nama', 'status_pkl')
            ->with(['siswaRombels' => function ($q) use ($taAktif) {
                if ($taAktif) {
                    $q->where('tahun_ajaran_id', $taAktif->id)
                      ->where('status_keanggotaan', 'aktif')
                      ->select('id', 'siswa_id', 'rombel_id')
                      ->with(['rombel:id,nama_rombel']);
                }
            }])
            ->get();

        // Daftar absensi siswa hari ini
        $absensiSiswaHariIni = Absensi::with(['siswa', 'siswaRombel.rombel'])
            ->where('pemilik_type', 'siswa')
            ->where('tanggal', $today)
            ->orderBy('jam_masuk', 'asc')
            ->get();

        // ── 2. DATA GURU & PEGAWAI ──
        $totalGuruActive  = Guru::where('status', 'aktif')->count();
        $guruHadir        = Absensi::where('pemilik_type', 'guru')->where('tanggal', $today)->where('status', 'hadir')->count();
        $guruTerlambat    = Absensi::where('pemilik_type', 'guru')->where('tanggal', $today)->where('status', 'terlambat')->count();
        $guruIzin         = Absensi::where('pemilik_type', 'guru')->where('tanggal', $today)->whereIn('status', ['sakit', 'izin', 'dispen'])->count();
        $guruTotalScan    = Absensi::where('pemilik_type', 'guru')->where('tanggal', $today)->count();
        $guruBelumHadir   = max(0, $totalGuruActive - $guruTotalScan);
        $persenGuru       = $totalGuruActive > 0 ? min(100.0, round(($guruTotalScan / $totalGuruActive) * 100, 1)) : 0;

        // Semua guru aktif (hanya kolom yg dibutuhkan JS)
        $semuaGuruList = Guru::where('status', 'aktif')->select('id', 'nip', 'nama', 'jabatan')->get();

        // Daftar absensi guru hari ini
        $absensiGuruHariIni = Absensi::with('guru')
            ->where('pemilik_type', 'guru')
            ->where('tanggal', $today)
            ->orderBy('jam_masuk', 'asc')
            ->get();

        // ── 3. SUMMARY PER ROMBEL (BATCH OPTIMIZED & FULLY SYNCED) ──
        $allActiveSiswas = Siswa::where('status', 'aktif')
            ->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif');
            }])->get();

        $siswaByRombel = $allActiveSiswas->groupBy(function ($s) {
            return $s->siswaRombels->first()?->rombel_id;
        });

        $todayAbsensis = Absensi::where('pemilik_type', 'siswa')
            ->where('tanggal', $today)
            ->get()
            ->keyBy('pemilik_id');

        $rombelSummary = Rombel::all()->map(function ($rombel) use ($siswaByRombel, $todayAbsensis) {
            $siswas = $siswaByRombel->get($rombel->id, collect());
            $totalSiswa = $siswas->count();
            
            $hadir = 0; $terlambat = 0; $izin = 0; $alpha = 0;
            foreach ($siswas as $s) {
                $abs = $todayAbsensis->get($s->id);
                if ($abs) {
                    if ($abs->status === 'hadir') $hadir++;
                    elseif ($abs->status === 'terlambat') $terlambat++;
                    elseif (in_array($abs->status, ['sakit', 'izin', 'dispen'])) $izin++;
                    elseif ($abs->status === 'alpha') $alpha++;
                }
            }
            $persen = $totalSiswa > 0 ? round((($hadir + $terlambat) / $totalSiswa) * 100, 1) : 0;

            return (object) [
                'nama_rombel' => $rombel->nama_rombel,
                'total_siswa' => $totalSiswa,
                'hadir'       => $hadir,
                'terlambat'   => $terlambat,
                'izin'        => $izin,
                'alpha'       => $alpha,
                'persentase'  => $persen,
            ];
        });

        // ── 4. JADWAL & STATUS LIBUR HARI INI ──
        $jadwalHariIni   = \App\Models\JadwalHariIni::getJadwalAktif($today);
        $hariLiburAktif  = \App\Models\HariLibur::getLiburHariIni($today);
        $isLiburHariIni  = \App\Models\HariLibur::isLibur($today);

        // ── 5. SISWA MENCAPAI AMBANG BATAS PELANGGARAN (EARLY WARNING ADMIN) ──
        $settingNotif = \App\Models\PengaturanNotifikasi::getPengaturan();
        $ambangAlpha = $settingNotif->ambang_batas_alpha ?? 3;
        $hitungBolos = $settingNotif->hitung_bolos_bersama_alpha ?? true;

        $kasusViolations = \App\Models\KasusDisiplin::where('is_active', true)
            ->where(function ($q) use ($ambangAlpha, $hitungBolos) {
                if ($hitungBolos) {
                    $q->whereRaw('(total_alpha + total_bolos) >= ?', [$ambangAlpha]);
                } else {
                    $q->where('total_alpha', '>=', $ambangAlpha);
                }
            })
            ->with(['siswa.siswaRombels' => function ($q) use ($taAktif) {
                if ($taAktif) {
                    $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')->with(['rombel.waliKelas']);
                }
            }])
            ->limit(50)
            ->get();

        $siswaWajibPanggilan = $kasusViolations->map(function ($k) {
            $s = $k->siswa;
            if (!$s) return null;
            $sr = $s->siswaRombels->first();
            $rombel = $sr?->rombel;
            return [
                'id'               => $s->id,
                'nama'             => $s->nama,
                'nisn'             => $s->nisn,
                'rombel'           => $rombel->nama_rombel ?? '-',
                'wali_kelas'       => $rombel?->waliKelas?->nama ?? '-',
                'total_alpha'      => (int)$k->total_alpha,
                'total_bolos'      => (int)$k->total_bolos,
                'total_pelanggaran'=> (int)($k->total_alpha + $k->total_bolos),
                'link_portal'      => url('/portal-siswa/' . ($s->nisn ?: $s->id)),
                'link_surat'       => url('/surat/cetak?siswa_id=' . $s->id . '&kategori=panggilan_ortu'),
            ];
        })->filter()->values();

        // ── 6. TREN KEHADIRAN 30 HARI (1 BULAN TERAKHIR) ──
        $startDate = Carbon::today()->subDays(29)->toDateString();
        $endDate   = $today;

        $trenAbsensi = Absensi::where('pemilik_type', 'siswa')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw("tanggal,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat,
                SUM(CASE WHEN status IN ('sakit', 'izin') THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha,
                SUM(CASE WHEN status = 'bolos' THEN 1 ELSE 0 END) as bolos")
            ->groupBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $chartLabels = [];
        $chartHadir = [];
        $chartTerlambat = [];
        $chartIzin = [];
        $chartAlpha = [];
        $chartPersentase = [];

        for ($i = 29; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->toDateString();
            $label = $tgl->translatedFormat('d M');
            $chartLabels[] = $label;

            $record = $trenAbsensi->get($tglStr);
            $h = (int) ($record->hadir ?? 0);
            $t = (int) ($record->terlambat ?? 0);
            $iz = (int) ($record->izin ?? 0);
            $a = (int) ($record->alpha ?? 0) + (int) ($record->bolos ?? 0);

            $chartHadir[] = $h;
            $chartTerlambat[] = $t;
            $chartIzin[] = $iz;
            $chartAlpha[] = $a;

            $persenHari = $totalSiswaActive > 0 ? min(100.0, round((($h + $t) / $totalSiswaActive) * 100, 1)) : 0;
            $chartPersentase[] = $persenHari;
        }

        // ── 7. TREN KEHADIRAN GURU & PEGAWAI 30 HARI ──
        $trenAbsensiGuru = Absensi::where('pemilik_type', 'guru')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw("tanggal,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat,
                SUM(CASE WHEN status IN ('sakit', 'izin', 'dispen') THEN 1 ELSE 0 END) as izin")
            ->groupBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $chartGuruPersentase = [];
        $chartGuruHadir = [];
        $chartGuruTerlambat = [];
        $chartGuruIzin = [];

        for ($i = 29; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->toDateString();

            $recordG = $trenAbsensiGuru->get($tglStr);
            $gh = (int) ($recordG->hadir ?? 0);
            $gt = (int) ($recordG->terlambat ?? 0);
            $giz = (int) ($recordG->izin ?? 0);

            $chartGuruHadir[] = $gh;
            $chartGuruTerlambat[] = $gt;
            $chartGuruIzin[] = $giz;

            $persenHariGuru = $totalGuruActive > 0 ? min(100.0, round((($gh + $gt) / $totalGuruActive) * 100, 1)) : 0;
            $chartGuruPersentase[] = $persenHariGuru;
        }

        // ── 7. DISTRIBUSI KEDISIPLINAN PER JURUSAN ──
        $jurusanList = \App\Models\Jurusan::with('rombels')->get();

        $jurusanLabels = [];
        $jurusanPersen = [];
        $jurusanHadir  = [];
        $jurusanAlpha  = [];

        foreach ($jurusanList as $jur) {
            $totalSisInJur = 0;
            $hadirJur = 0;
            $alphaJur = 0;

            foreach ($jur->rombels as $r) {
                $siswasInRombel = $siswaByRombel->get($r->id, collect());
                $totalSisInJur += $siswasInRombel->count();

                foreach ($siswasInRombel as $s) {
                    $ab = $todayAbsensis->get($s->id);
                    if ($ab) {
                        if (in_array($ab->status, ['hadir', 'terlambat'])) $hadirJur++;
                        elseif (in_array($ab->status, ['alpha', 'bolos'])) $alphaJur++;
                    }
                }
            }

            if ($totalSisInJur === 0) continue;

            $persenJur = round(($hadirJur / max(1, $totalSisInJur)) * 100, 1);

            $jurusanLabels[] = $jur->kode_jurusan ?: $jur->nama_jurusan;
            $jurusanPersen[] = $persenJur;
            $jurusanHadir[]  = $hadirJur;
            $jurusanAlpha[]  = $alphaJur;
        }

        $siswaBolos = $todayAbsensis->where('status', 'bolos')->count();

        // Data Donut Chart Komposisi Kehadiran Hari Ini
        $donutSiswaLabels = ['Hadir Tepat Waktu', 'Terlambat', 'Sakit / Izin', 'Alpha', 'Bolos'];
        $donutSiswaValues = [$siswaHadir, $siswaTerlambat, $siswaIzin, $siswaAlpha, $siswaBolos];

        $donutGuruLabels = ['Hadir Tepat Waktu', 'Terlambat', 'Izin / Dinas', 'Belum Hadir'];
        $donutGuruValues = [$guruHadir, $guruTerlambat, $guruIzin, $guruBelumHadir];

        // Data Bar Chart Komparasi per Kelas
        $chartRombelLabels = $rombelSummary->pluck('nama_rombel')->toArray();
        $chartRombelHadir  = $rombelSummary->pluck('hadir')->toArray();
        $chartRombelTelat  = $rombelSummary->pluck('terlambat')->toArray();
        $chartRombelIzin   = $rombelSummary->pluck('izin')->toArray();
        $chartRombelAlpha  = $rombelSummary->pluck('alpha')->toArray();

        // ── 8. DATA KHUSUS PER PERAN (ROLE-SPECIFIC SUITES) ──
        $currentUser = auth()->user();
        $currentGuru = $currentUser ? ($currentUser->guru ?? \App\Models\Guru::where('user_id', $currentUser->id)->orWhere('email', $currentUser->email)->first()) : null;

        // A. Wali Kelas Specific Data
        $waliRombel = null;
        $waliTotalSiswa = 0;
        $waliHadir = 0; $waliTerlambat = 0; $waliIzin = 0; $waliAlpha = 0; $waliBolos = 0; $waliPersen = 0;
        $waliSiswaBermasalahHariIni = collect();
        $waliKasusTahap1List = collect();

        if ($currentUser && $currentUser->isWaliKelas()) {
            $waliRombelIds = [];
            if ($currentGuru) {
                $waliRombelIds = $currentGuru->rombels()->pluck('id')->toArray();
                $waliRombel = $currentGuru->rombels()->first();
            }
            if (empty($waliRombelIds) && str_contains($currentUser->email, 'walikelas')) {
                $foundRombel = Rombel::all()->first(function ($r) use ($currentUser) {
                    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $r->nama_rombel));
                    return str_contains($currentUser->email, $slug);
                });
                if ($foundRombel) {
                    $waliRombelIds = [$foundRombel->id];
                    $waliRombel = $foundRombel;
                }
            }

            if (!empty($waliRombelIds)) {
                $waliSiswaCollection = $allActiveSiswas->filter(function ($s) use ($waliRombelIds) {
                    return in_array($s->siswaRombels->first()?->rombel_id, $waliRombelIds);
                });
                $waliTotalSiswa = $waliSiswaCollection->count();
                $waliSiswaIds = $waliSiswaCollection->pluck('id')->toArray();

                $waliTodayAbsen = Absensi::where('pemilik_type', 'siswa')
                    ->where('tanggal', $today)
                    ->whereIn('pemilik_id', $waliSiswaIds)
                    ->get()
                    ->keyBy('pemilik_id');

                foreach ($waliSiswaCollection as $ws) {
                    $ab = $waliTodayAbsen->get($ws->id);
                    if ($ab) {
                        if ($ab->status === 'hadir') $waliHadir++;
                        elseif ($ab->status === 'terlambat') {
                            $waliTerlambat++;
                            $waliSiswaBermasalahHariIni->push((object)[
                                'siswa' => $ws,
                                'status' => 'terlambat',
                                'jam' => $ab->jam_masuk,
                                'keterangan' => $ab->keterangan ?? 'Terlambat scan masuk'
                            ]);
                        }
                        elseif (in_array($ab->status, ['sakit', 'izin', 'dispen'])) $waliIzin++;
                        elseif ($ab->status === 'alpha') {
                            $waliAlpha++;
                            $waliSiswaBermasalahHariIni->push((object)[
                                'siswa' => $ws,
                                'status' => 'alpha',
                                'jam' => null,
                                'keterangan' => $ab->keterangan ?? 'Tidak hadir tanpa keterangan'
                            ]);
                        }
                        elseif ($ab->status === 'bolos') {
                            $waliBolos++;
                            $waliSiswaBermasalahHariIni->push((object)[
                                'siswa' => $ws,
                                'status' => 'bolos',
                                'jam' => $ab->jam_masuk,
                                'keterangan' => $ab->keterangan ?? 'Tidak tap pulang tanpa izin'
                            ]);
                        }
                    } else {
                        $waliSiswaBermasalahHariIni->push((object)[
                            'siswa' => $ws,
                            'status' => 'belum_hadir',
                            'jam' => null,
                            'keterangan' => 'Belum tap kartu di gerbang'
                        ]);
                    }
                }
                $waliPersen = $waliTotalSiswa > 0 ? round((($waliHadir + $waliTerlambat) / $waliTotalSiswa) * 100, 1) : 0;

                $waliKasusTahap1List = \App\Models\KasusDisiplin::with('siswa')
                    ->where('is_active', true)
                    ->whereIn('siswa_id', $waliSiswaIds)
                    ->orderBy('total_poin_pelanggaran', 'desc')
                    ->get();

                // Batasi tabel absensi siswa di dashboard hanya untuk kelas binaan wali kelas
                $absensiSiswaHariIni = $absensiSiswaHariIni->filter(function ($ab) use ($waliRombelIds) {
                    return in_array($ab->siswaRombel?->rombel_id, $waliRombelIds);
                });

                // Batasi banner peringatan kesiswaan (ambang batas pelanggaran) hanya untuk siswa wali kelas tersebut
                $siswaWajibPanggilan = $siswaWajibPanggilan->filter(function ($item) use ($waliSiswaIds) {
                    return in_array($item['id'], $waliSiswaIds);
                })->values();

                // Jika user murni Wali Kelas (bukan Eksekutif / Wakasis / Wakakur / Admin)
                // Batasi seluruh statistik siswa di dasbor (KPI Strip, Grafik 30 Hari, Donut Chart) HANYA untuk kelas binaan wali kelas tersebut.
                if (!($currentUser->isAdmin() || $currentUser->isKepalaSekolah() || $currentUser->isWakaKesiswaan() || $currentUser->isWakaKurikulum())) {
                    $totalSiswaActive = $waliTotalSiswa;
                    $totalSiswaPkl    = $waliSiswaCollection->where('status_pkl', 'aktif_pkl')->count();
                    $siswaHadir       = $waliHadir;
                    $siswaTerlambat   = $waliTerlambat;
                    $siswaIzin        = $waliIzin;
                    $siswaAlpha       = $waliAlpha;
                    $siswaBolos       = $waliBolos;
                    $persenSekolah    = $waliPersen;

                    $donutSiswaValues = [$waliHadir, $waliTerlambat, $waliIzin, $waliAlpha, $waliBolos];

                    // Hitung ulang grafik tren 30 hari khusus untuk siswa wali kelas ini
                    $trenAbsensiWali = Absensi::where('pemilik_type', 'siswa')
                        ->whereIn('pemilik_id', $waliSiswaIds)
                        ->whereBetween('tanggal', [$startDate, $endDate])
                        ->selectRaw("tanggal,
                            SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                            SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat,
                            SUM(CASE WHEN status IN ('sakit', 'izin') THEN 1 ELSE 0 END) as izin,
                            SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha,
                            SUM(CASE WHEN status = 'bolos' THEN 1 ELSE 0 END) as bolos")
                        ->groupBy('tanggal')
                        ->get()
                        ->keyBy('tanggal');

                    $chartHadir = [];
                    $chartTerlambat = [];
                    $chartIzin = [];
                    $chartAlpha = [];
                    $chartPersentase = [];

                    for ($i = 29; $i >= 0; $i--) {
                        $tgl = Carbon::today()->subDays($i);
                        $tglStr = $tgl->toDateString();

                        $record = $trenAbsensiWali->get($tglStr);
                        $h = (int) ($record->hadir ?? 0);
                        $t = (int) ($record->terlambat ?? 0);
                        $iz = (int) ($record->izin ?? 0);
                        $a = (int) ($record->alpha ?? 0) + (int) ($record->bolos ?? 0);

                        $chartHadir[] = $h;
                        $chartTerlambat[] = $t;
                        $chartIzin[] = $iz;
                        $chartAlpha[] = $a;

                        $persenHari = $waliTotalSiswa > 0 ? min(100.0, round((($h + $t) / $waliTotalSiswa) * 100, 1)) : 0;
                        $chartPersentase[] = $persenHari;
                    }

                    // Filter perbandingan rombel hanya untuk rombel binaan wali kelas
                    $rombelSummary = $rombelSummary->filter(function ($item) use ($waliRombel) {
                        return $item->nama_rombel === ($waliRombel->nama_rombel ?? '');
                    })->values();

                    $chartRombelLabels = $rombelSummary->pluck('nama_rombel')->toArray();
                    $chartRombelHadir  = $rombelSummary->pluck('hadir')->toArray();
                    $chartRombelTelat  = $rombelSummary->pluck('terlambat')->toArray();
                    $chartRombelIzin   = $rombelSummary->pluck('izin')->toArray();
                    $chartRombelAlpha  = $rombelSummary->pluck('alpha')->toArray();
                }
            } else {
                $siswaWajibPanggilan = collect();
                if (!($currentUser->isAdmin() || $currentUser->isKepalaSekolah() || $currentUser->isWakaKesiswaan() || $currentUser->isWakaKurikulum())) {
                    $totalSiswaActive = 0;
                    $totalSiswaPkl    = 0;
                    $siswaHadir       = 0;
                    $siswaTerlambat   = 0;
                    $siswaIzin        = 0;
                    $siswaAlpha       = 0;
                    $siswaBolos       = 0;
                    $persenSekolah    = 0;
                    $donutSiswaValues = [0, 0, 0, 0, 0];
                    $chartHadir       = array_fill(0, 30, 0);
                    $chartTerlambat   = array_fill(0, 30, 0);
                    $chartIzin        = array_fill(0, 30, 0);
                    $chartAlpha       = array_fill(0, 30, 0);
                    $chartPersentase  = array_fill(0, 30, 0);
                    $rombelSummary    = collect();
                    $chartRombelLabels= [];
                    $chartRombelHadir = [];
                    $chartRombelTelat = [];
                    $chartRombelIzin  = [];
                    $chartRombelAlpha = [];
                }
            }
        }

        // ─── DETEKSI WALI KELAS YANG SEDANG BERTUGAS PIKET HARI INI ───
        // Jika seorang Wali Kelas terdaftar bertugas piket pada hari ini,
        // tampilkan dasbor operasional Guru Piket (skala sekolah),
        // namun tetap sertakan ringkasan kelas binaannya.
        $isWaliSedangPiket = false;
        if ($currentUser && $currentUser->isWaliKelas() && $currentGuru) {
            $isWaliSedangPiket = \App\Models\JadwalPiket::isGuruPiketHariIni($currentGuru->id, $today);
        }

        // Jika Wali Kelas sedang bertugas piket: override statistik KPI ke skala sekolah penuh
        if ($isWaliSedangPiket) {
            $totalSiswaActive = Siswa::where('status', 'aktif')->count();
            $totalSiswaPkl    = Siswa::where('status', 'aktif')->where('status_pkl', 'aktif_pkl')->count();
            $siswaHadir       = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'hadir')->count();
            $siswaTerlambat   = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'terlambat')->count();
            $siswaIzin        = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->whereIn('status', ['sakit', 'izin', 'dispen'])->count();
            $siswaAlpha       = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'alpha')->count();
            $siswaBolos       = Absensi::where('pemilik_type', 'siswa')->where('tanggal', $today)->where('status', 'bolos')->count();
            $persenSekolah    = $totalSiswaActive > 0 ? min(100.0, round((($siswaHadir + $siswaTerlambat) / $totalSiswaActive) * 100, 1)) : 0;
            $donutSiswaValues = [$siswaHadir, $siswaTerlambat, $siswaIzin, $siswaAlpha, $siswaBolos];

            // Hitung ulang grafik tren 30 hari skala sekolah
            $trenAbsensiSekolah = Absensi::where('pemilik_type', 'siswa')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->selectRaw("tanggal,
                    SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat,
                    SUM(CASE WHEN status IN ('sakit', 'izin') THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha,
                    SUM(CASE WHEN status = 'bolos' THEN 1 ELSE 0 END) as bolos")
                ->groupBy('tanggal')
                ->get()
                ->keyBy('tanggal');

            $chartHadir = []; $chartTerlambat = []; $chartIzin = []; $chartAlpha = []; $chartPersentase = [];
            $totalSiswaActiveForChart = $totalSiswaActive;
            for ($i = 29; $i >= 0; $i--) {
                $tgl = Carbon::today()->subDays($i);
                $tglStr = $tgl->toDateString();
                $record = $trenAbsensiSekolah->get($tglStr);
                $h = (int) ($record->hadir ?? 0);
                $t = (int) ($record->terlambat ?? 0);
                $iz = (int) ($record->izin ?? 0);
                $a = (int) ($record->alpha ?? 0) + (int) ($record->bolos ?? 0);
                $chartHadir[] = $h;
                $chartTerlambat[] = $t;
                $chartIzin[] = $iz;
                $chartAlpha[] = $a;
                $chartPersentase[] = $totalSiswaActiveForChart > 0 ? min(100.0, round((($h + $t) / $totalSiswaActiveForChart) * 100, 1)) : 0;
            }

            // Reset filter rombel ke semua rombel (skala sekolah)
            $chartRombelLabels = $rombelSummary->pluck('nama_rombel')->toArray();
            $chartRombelHadir  = $rombelSummary->pluck('hadir')->toArray();
            $chartRombelTelat  = $rombelSummary->pluck('terlambat')->toArray();
            $chartRombelIzin   = $rombelSummary->pluck('izin')->toArray();
            $chartRombelAlpha  = $rombelSummary->pluck('alpha')->toArray();
        }

        // B. Guru BK Specific Data
        $bkKasusTahap2List = collect();
        $bkTotalKasusAktif = 0;
        $bkTotalDokumen = 0;
        $bkSiswaPelanggarTinggi = collect();

        if ($currentUser && ($currentUser->isGuruBk() || $currentUser->isAdmin())) {
            $bkKasusTahap2List = \App\Models\KasusDisiplin::with(['siswa.siswaRombels.rombel'])
                ->where('is_active', true)
                ->where('status_tahap', 'tahap_2_bk')
                ->orderBy('total_poin_pelanggaran', 'desc')
                ->get();

            $bkTotalKasusAktif = \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', '!=', 'selesai_pembinaan')->count();
            $bkTotalDokumen = \App\Models\KasusDisiplinDokumen::count();

            $bkSiswaPelanggarTinggi = \App\Models\KasusDisiplin::with(['siswa.siswaRombels.rombel'])
                ->where('is_active', true)
                ->orderBy('total_poin_pelanggaran', 'desc')
                ->limit(6)
                ->get();
        }

        // C. Waka Kesiswaan Specific Data
        $wakasisKasusTahap3List = collect();
        $wakasisPendingNotif = 0;

        if ($currentUser && ($currentUser->isWakaKesiswaan() || $currentUser->isAdmin())) {
            $wakasisKasusTahap3List = \App\Models\KasusDisiplin::with(['siswa.siswaRombels.rombel'])
                ->where('is_active', true)
                ->where('status_tahap', 'tahap_3_wakasis')
                ->orderBy('total_poin_pelanggaran', 'desc')
                ->get();

            $wakasisPendingNotif = \App\Models\NotifikasiOrtu::where('status', 'pending')
                ->where('tanggal', $today)
                ->count();
        }

        // Leaderboard Siswa Teladan Bulan Ini (Untuk Wakasis & Eksekutif)
        $bulanSekarang = Carbon::today()->format('Y-m');
        $topSiswaTeladan = Absensi::selectRaw('pemilik_id, count(*) as total_hadir')
            ->where('pemilik_type', 'siswa')
            ->where('tanggal', 'like', "{$bulanSekarang}%")
            ->where('status', 'hadir')
            ->groupBy('pemilik_id')
            ->orderBy('total_hadir', 'desc')
            ->limit(5)
            ->with(['siswa.siswaRombels.rombel'])
            ->get();

        // D. Kepala Sekolah Specific Data
        $kepsekKasusTahap4List = collect();
        $kepsekStageCounts = (object)[
            'tahap_1' => \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', 'tahap_1_wali_kelas')->count(),
            'tahap_2' => \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', 'tahap_2_bk')->count(),
            'tahap_3' => \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', 'tahap_3_wakasis')->count(),
            'tahap_4' => \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', 'tahap_4_kepsek')->count(),
        ];

        if ($currentUser && ($currentUser->isKepalaSekolah() || $currentUser->isAdmin())) {
            $kepsekKasusTahap4List = \App\Models\KasusDisiplin::with(['siswa.siswaRombels.rombel'])
                ->where('is_active', true)
                ->where('status_tahap', 'tahap_4_kepsek')
                ->orderBy('total_poin_pelanggaran', 'desc')
                ->get();
        }

        // E. Guru / Guru Piket Specific Data
        $guruPresensiPribadi = null;
        $guruTotalHadirBulanIni = 0;
        $isPiketHariIni = false;
        $jadwalPiketHariIni = \App\Models\JadwalPiket::getJadwalHariIni($today);
        $totalIzinSiswaHariIni = \App\Models\IzinSiswa::where('tanggal', $today)->count();

        if ($currentGuru) {
            $guruPresensiPribadi = Absensi::where('pemilik_type', 'guru')
                ->where('pemilik_id', $currentGuru->id)
                ->where('tanggal', $today)
                ->first();

            $bulanIni = Carbon::today()->format('Y-m');
            $guruTotalHadirBulanIni = Absensi::where('pemilik_type', 'guru')
                ->where('pemilik_id', $currentGuru->id)
                ->where('tanggal', 'like', "{$bulanIni}%")
                ->whereIn('status', ['hadir', 'terlambat'])
                ->count();

            $isPiketHariIni = \App\Models\JadwalPiket::isGuruPiketHariIni($currentGuru->id, $today);
        }

        // Data Khusus Operasional Guru Piket
        $piketSiswaTerlambatList = $absensiSiswaHariIni->where('status', 'terlambat')->values();
        $piketIzinList = \App\Models\IzinSiswa::where('tanggal', $today)
            ->with(['siswa.siswaRombels.rombel'])
            ->orderBy('created_at', 'desc')
            ->get();
        $piketBelumHadirCount = max(0, $totalSiswaActive - ($siswaHadir + $siswaTerlambat + $siswaIzin + $totalSiswaPkl));

        // Siswa yang sudah absen masuk tapi belum scan pulang (hadir, terlambat, atau bolos)
        $siswaBelumPulangCount = Absensi::where('pemilik_type', 'siswa')
            ->where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->whereIn('status', ['hadir', 'terlambat', 'bolos'])
            ->count();

        // Cek apakah sudah melewati jam tutup gerbang (17:00:00)
        $jamTutupGerbang = $jadwalHariIni->jam_tutup_gerbang ?? '17:00:00';
        $sudahLewatJamTutup = now()->format('H:i:s') >= $jamTutupGerbang;


        $piketRecentLiveFeed = $absensiSiswaHariIni->sortByDesc(function ($item) {
            return $item->jam_pulang ?: $item->jam_masuk;
        })->take(8)->values();

        // ── LAPORAN GURU PIKET HARI INI (UNTUK KEPALA SEKOLAH & EKSEKUTIF) ──
        $hariHariIni = \App\Models\JadwalPiket::getHariIndonesia();
        $petugasPiketHariIniList = \App\Models\JadwalPiket::where('hari', $hariHariIni)
            ->with(['guru.user'])
            ->get();
        $guruPiketIds = $petugasPiketHariIniList->pluck('guru_id');
        $absensiPiketMap = Absensi::where('pemilik_type', 'guru')
            ->whereIn('pemilik_id', $guruPiketIds)
            ->where('tanggal', $today)
            ->get()
            ->keyBy('pemilik_id');

        $piketTotalTugas = $petugasPiketHariIniList->count();
        $piketTotalHadir = 0;
        $piketTotalTerlambat = 0;
        $piketTotalBelumHadir = 0;

        foreach ($petugasPiketHariIniList as $p) {
            $abs = $absensiPiketMap->get($p->guru_id);
            if ($abs) {
                $piketTotalHadir++;
                if ($abs->status === 'terlambat') {
                    $piketTotalTerlambat++;
                }
            } else {
                $piketTotalBelumHadir++;
            }
        }

        $jadwalHarianSesi = \App\Models\JadwalHariIni::where('tanggal', $today)->first();
        $upcomingHolidays = \App\Models\HariLibur::where('tanggal_selesai', '>=', $today)
            ->orderBy('tanggal_mulai', 'asc')
            ->limit(4)
            ->get();

        return view('dashboard', compact(
            'today',
            'taAktif',
            'jadwalHariIni',
            'totalSiswaActive',
            'totalSiswaPkl',
            'siswaHadir',
            'siswaTerlambat',
            'siswaIzin',
            'siswaAlpha',
            'siswaBolos',
            'persenSekolah',
            'semuaSiswaList',
            'absensiSiswaHariIni',
            'totalGuruActive',
            'guruHadir',
            'guruTerlambat',
            'guruIzin',
            'guruBelumHadir',
            'guruTotalScan',
            'persenGuru',
            'semuaGuruList',
            'absensiGuruHariIni',
            'rombelSummary',
            'siswaWajibPanggilan',
            'chartLabels',
            'chartHadir',
            'chartTerlambat',
            'chartIzin',
            'chartAlpha',
            'chartPersentase',
            'chartGuruPersentase',
            'jurusanLabels',
            'jurusanPersen',
            'jurusanHadir',
            'jurusanAlpha',
            'donutSiswaLabels',
            'donutSiswaValues',
            'donutGuruLabels',
            'donutGuruValues',
            'chartRombelLabels',
            'chartRombelHadir',
            'chartRombelTelat',
            'chartRombelIzin',
            'chartRombelAlpha',
            'hariLiburAktif',
            'isLiburHariIni',
            'upcomingHolidays',
            'currentUser',
            'currentGuru',
            'waliRombel',
            'waliTotalSiswa',
            'waliHadir',
            'waliTerlambat',
            'waliIzin',
            'waliAlpha',
            'waliBolos',
            'waliPersen',
            'waliSiswaBermasalahHariIni',
            'waliKasusTahap1List',
            'bkKasusTahap2List',
            'bkTotalKasusAktif',
            'bkTotalDokumen',
            'bkSiswaPelanggarTinggi',
            'wakasisKasusTahap3List',
            'wakasisPendingNotif',
            'kepsekKasusTahap4List',
            'kepsekStageCounts',
            'guruPresensiPribadi',
            'guruTotalHadirBulanIni',
            'isPiketHariIni',
            'jadwalPiketHariIni',
            'totalIzinSiswaHariIni',
            'piketSiswaTerlambatList',
            'piketIzinList',
            'piketBelumHadirCount',
            'piketRecentLiveFeed',
            'hariHariIni',
            'petugasPiketHariIniList',
            'absensiPiketMap',
            'piketTotalTugas',
            'piketTotalHadir',
            'piketTotalTerlambat',
            'piketTotalBelumHadir',
            'jadwalHarianSesi',
            'topSiswaTeladan',
            'siswaBelumPulangCount',
            'sudahLewatJamTutup',
            'isWaliSedangPiket'
        ));
    }



    /**
     * Halaman Pengaturan Jam Operasional Sekolah (Admin).
     */
    public function jadwalSekolah()
    {
        $today = Carbon::today()->toDateString();
        $jadwalHariIni = JadwalHariIni::getJadwalAktif($today);
        $taAktif = TahunAjaran::where('is_active', true)->first();

        // Riwayat 7 hari terakhir
        $riwayatJadwal = JadwalHariIni::orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();

        return view('jadwal_sekolah.index', compact('today', 'jadwalHariIni', 'taAktif', 'riwayatJadwal'));
    }

    /**
     * Update jadwal masuk dan pulang oleh Admin.
     */
    public function updateJadwal(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'jam_masuk_toleransi' => 'required|date_format:H:i',
            'jam_pulang_mulai'    => 'required|date_format:H:i',
            'jam_tutup_gerbang'   => 'nullable|date_format:H:i',
            'keterangan'          => 'nullable|string|max:255',
        ]);

        $today = Carbon::today()->toDateString();
        $adminNama = auth()->user()->name ?? 'Administrator';

        $jadwal = JadwalHariIni::getJadwalAktif($today);
        $jamTutup = $request->filled('jam_tutup_gerbang') ? ($request->input('jam_tutup_gerbang') . ':00') : ($jadwal->jam_tutup_gerbang ?? '17:00:00');


        $jadwal->update([
            'jam_masuk_toleransi' => $request->input('jam_masuk_toleransi') . ':00',
            'jam_pulang_mulai'    => $request->input('jam_pulang_mulai') . ':00',
            'jam_tutup_gerbang'   => $jamTutup,
            'keterangan'          => $request->input('keterangan') ?: 'Jadwal Reguler',
            'diubah_oleh'         => $adminNama . ' (Admin)',
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal jam masuk, jam pulang, & batas tutup gerbang hari ini berhasil diperbarui!');
    }
}
