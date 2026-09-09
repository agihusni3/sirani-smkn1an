<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Jurusan;
use App\Models\PpdbPendaftar;
use App\Models\PpdbUjianPeserta;
use App\Models\PpdbUjianSetting;
use App\Models\Rombel;
use App\Services\PpdbMutasiService;
use Illuminate\Http\Request;

class PpdbAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima', 'siswa']);

        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === 'menunggu') $st = 'menunggu_verifikasi';
            if ($st === 'berkas_valid') $st = 'terverifikasi';
            $query->where('status', $st);
        }

        if ($request->filled('jurusan_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('jurusan_id_1', $request->jurusan_id)
                  ->orWhere('jurusan_id_2', $request->jurusan_id)
                  ->orWhere('jurusan_diterima_id', $request->jurusan_id);
            });
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('nisn', 'like', "%{$cari}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$cari}%")
                  ->orWhere('asal_sekolah', 'like', "%{$cari}%");
            });
        }

        $pendaftars = $query->latest()->paginate(20);
        $jurusans = Jurusan::all();
        $rombels = Rombel::where('tingkat', '10')->orWhere('tingkat', 'X')->get();

        $counts = [
            'total' => PpdbPendaftar::count(),
            'menunggu' => PpdbPendaftar::whereIn('status', ['menunggu', 'menunggu_verifikasi', 'draft'])->count(),
            'berkas_valid' => PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid'])->count(),
            'diterima' => PpdbPendaftar::where('status', 'diterima')->count(),
            'cadangan' => PpdbPendaftar::where('status', 'cadangan')->count(),
            'ditolak' => PpdbPendaftar::where('status', 'ditolak')->count(),
        ];

        // Statistik Peminatan Jurusan
        $jurusanStats = $jurusans->map(function ($j) {
            $peminat = PpdbPendaftar::where('jurusan_id_1', $j->id)->count();
            $diterima = PpdbPendaftar::where('jurusan_diterima_id', $j->id)->where('status', 'diterima')->count();
            return [
                'id' => $j->id,
                'kode' => $j->kode_jurusan,
                'nama' => $j->nama_jurusan,
                'peminat' => $peminat,
                'diterima' => $diterima,
            ];
        });

        return view('ppdb.admin.index', compact('pendaftars', 'jurusans', 'rombels', 'counts', 'jurusanStats'));
    }

    public function show($id)
    {
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima', 'siswa'])->findOrFail($id);
        $jurusans = Jurusan::all();
        $rombels = Rombel::where('tingkat', '10')->orWhere('tingkat', 'X')->get();

        return view('ppdb.admin.show', compact('pendaftar', 'jurusans', 'rombels'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::findOrFail($id);

        $validated = $request->validate([
            'status_pendaftaran' => 'required|in:menunggu,menunggu_verifikasi,berkas_valid,terverifikasi,diterima,cadangan,ditolak',
            'jurusan_diterima_id' => 'nullable|exists:jurusans,id',
            'catatan' => 'nullable|string',
        ]);

        $st = $validated['status_pendaftaran'];
        if ($st === 'menunggu') $st = 'menunggu_verifikasi';
        if ($st === 'berkas_valid') $st = 'terverifikasi';

        $statusLama = $pendaftar->status;
        $jurusanLama = $pendaftar->jurusan_diterima_id;

        $pendaftar->status = $st;

        // Jika diterima, pastikan jurusan_diterima_id terisi
        if ($st === 'diterima') {
            $pendaftar->jurusan_diterima_id = $validated['jurusan_diterima_id'] ?: ($pendaftar->jurusan_diterima_id ?: $pendaftar->jurusan_id_1);
        } elseif (!empty($validated['jurusan_diterima_id'])) {
            $pendaftar->jurusan_diterima_id = $validated['jurusan_diterima_id'];
        }

        if (isset($validated['catatan'])) {
            $pendaftar->catatan_panitia = $validated['catatan'];
        }
        $pendaftar->diverifikasi_oleh = auth()->user()->nama ?? auth()->user()->name ?? 'Admin';
        $pendaftar->diverifikasi_pada = now();
        $pendaftar->save();

        // Otomatis pastikan jadwal ujian resmi Juknis (1 Gelombang) terisi jika berkas valid/terverifikasi
        if (in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'diterima'])) {
            $pendaftar->pastikanJadwalJuknis();
        }

        // Audit Trail PPDB
        AuditLog::catat(
            'update',
            'ppdb',
            "Verifikasi pendaftar PPDB: {$pendaftar->nama_lengkap} ({$pendaftar->no_pendaftaran}) status diubah menjadi " . strtoupper($pendaftar->status),
            ['status' => $statusLama, 'jurusan_diterima_id' => $jurusanLama],
            ['status' => $pendaftar->status, 'jurusan_diterima_id' => $pendaftar->jurusan_diterima_id, 'catatan' => $pendaftar->catatan_panitia],
            $pendaftar
        );

        return back()->with('success', 'Status pendaftar ' . $pendaftar->nama_lengkap . ' berhasil diperbarui.');
    }

    public function mutasi(Request $request, $id, PpdbMutasiService $mutasiService)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
        ]);

        $pendaftar = PpdbPendaftar::findOrFail($id);

        try {
            $siswa = $mutasiService->mutasiKeSiswa($pendaftar, $request->rombel_id);

            // Audit Trail PPDB
            AuditLog::catat(
                'create',
                'ppdb',
                "Migrasi calon siswa PPDB {$pendaftar->nama_lengkap} ({$pendaftar->no_pendaftaran}) menjadi Siswa Aktif SITUAN/SIRANI (NISN: {$siswa->nisn})",
                null,
                ['pendaftar_id' => $pendaftar->id, 'siswa_id' => $siswa->id, 'rombel_id' => $request->rombel_id],
                $pendaftar
            );

            return back()->with('success', "Sukses! Calon siswa {$pendaftar->nama_lengkap} resmi dimutasi menjadi Siswa Aktif SIRANI (NISN: {$siswa->nisn}).");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memutasi siswa: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Riwayat & Audit Trail Khusus Modul PPDB 2026.
     */
    public function log(Request $request)
    {
        $filters = $request->only(['aksi', 'dari', 'sampai', 'cari']);

        $logs = AuditLog::with('user')
            ->ppdb()
            ->filter($filters)
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        $aksiOptions = ['create', 'update', 'delete'];

        $counts = [
            'total'     => AuditLog::ppdb()->count(),
            'hari_ini'  => AuditLog::ppdb()->whereDate('created_at', today())->count(),
            'minggu_ini'=> AuditLog::ppdb()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('ppdb.admin.log', compact('logs', 'filters', 'aksiOptions', 'counts'));
    }

    /**
     * Halaman Seleksi Terpadu (CBT PDF Soal, Penjadwalan, Koreksi Esai, Wawancara, Leaderboard)
     */
    public function seleksi(Request $request)
    {
        $setting = PpdbUjianSetting::getAktif();
        if (!$setting) {
            $setting = PpdbUjianSetting::create([
                'judul_ujian'      => 'Tes Potensi Akademik & Minat Vokasi PPDB 2026',
                'tahun_ajaran'     => date('Y') . '/' . (date('Y') + 1),
                'jumlah_soal_pg'   => 30,
                'jumlah_soal_esai' => 5,
                'bobot_pg'         => 70.00,
                'bobot_esai'       => 30.00,
                'durasi_menit'     => 60,
                'is_active'        => false,
            ]);
        }

        $jurusans = Jurusan::all();
        $tabAktif = $request->query('tab', 'pengaturan');

        $query = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima', 'ujianPeserta', 'pewawancara']);

        if ($request->filled('jurusan_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('jurusan_id_1', $request->jurusan_id)
                  ->orWhere('jurusan_id_2', $request->jurusan_id)
                  ->orWhere('jurusan_diterima_id', $request->jurusan_id);
            });
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('nisn', 'like', "%{$cari}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$cari}%");
            });
        }

        // Peserta untuk jadwal dan rekapitulasi
        $pesertaUjian = (clone $query)->whereIn('status', ['terverifikasi', 'berkas_valid', 'diterima', 'siap_tes'])
            ->orderBy('no_pendaftaran', 'asc')
            ->get();

        // Peserta untuk leaderboard (diurutkan berdasarkan nilai akhir)
        $leaderboard = (clone $query)->whereNotNull('nilai_akhir')
            ->orderBy('nilai_akhir', 'desc')
            ->get();

        $stats = [
            'total_valid'      => PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid', 'diterima', 'cadangan', 'siap_tes'])->count(),
            'sudah_jadwal'     => PpdbPendaftar::whereNotNull('jadwal_tes_tanggal')->count(),
            'sudah_pg'         => PpdbUjianPeserta::whereNotNull('waktu_selesai')->count(),
            'menunggu_esai'    => PpdbUjianPeserta::where('status_pengerjaan', 'selesai_menunggu_koreksi')->count(),
            'sudah_wawancara'  => PpdbPendaftar::whereNotNull('nilai_wawancara_total')->count(),
            'siap_dirangking'  => PpdbPendaftar::whereNotNull('nilai_akhir')->count(),
            'total_diterima'   => PpdbPendaftar::where('status', 'diterima')->count(),
            'total_cadangan'   => PpdbPendaftar::where('status', 'cadangan')->count(),
        ];

        return view('ppdb.admin.seleksi', compact(
            'setting',
            'jurusans',
            'pesertaUjian',
            'leaderboard',
            'stats',
            'tabAktif'
        ));
    }

    /**
     * Simpan Pengaturan Sesi Ujian CBT PPDB & Manajemen Multi-Sesi
     */
    public function simpanSettingUjian(Request $request)
    {
        $validated = $request->validate([
            'judul_ujian'          => 'required|string|max:255',
            'durasi_menit'         => 'required|integer|min:15|max:180',
            'tanggal_pelaksanaan'  => 'nullable|date',
            'sesi_default'         => 'nullable|string|max:150',
            'ruang_default'        => 'nullable|string|max:150',
            'gelombang_label'      => 'nullable|string|max:150',
            'bobot_pg'             => 'required|numeric|min:10|max:90',
            'bobot_esai'           => 'required|numeric|min:10|max:90',
            'is_active'            => 'nullable|boolean',
            'petunjuk_ujian'       => 'nullable|string',
        ]);

        $setting = PpdbUjianSetting::getAktif();
        if (!$setting) {
            $setting = new PpdbUjianSetting();
        }

        // Parsing multi-sesi fleksibel yang dikonfigurasi admin
        $daftarSesi = [];
        if ($request->has('sesi_nama') && is_array($request->input('sesi_nama'))) {
            $namas = $request->input('sesi_nama');
            $waktus = $request->input('sesi_waktu', []);
            foreach ($namas as $idx => $nama) {
                $namaClean = trim($nama);
                $waktuClean = trim($waktus[$idx] ?? '');
                if ($namaClean !== '') {
                    $label = $waktuClean !== '' ? "{$namaClean} ({$waktuClean})" : $namaClean;
                    $daftarSesi[] = [
                        'nama'  => $namaClean,
                        'waktu' => $waktuClean,
                        'label' => $label,
                    ];
                }
            }
        }

        if (!empty($daftarSesi)) {
            $setting->daftar_sesi = $daftarSesi;
        }

        $defaultSesiCandidate = $validated['sesi_default'] ?? null;
        if (!$defaultSesiCandidate && !empty($daftarSesi)) {
            $defaultSesiCandidate = $daftarSesi[0]['label'];
        }

        $setting->judul_ujian = $validated['judul_ujian'];
        $setting->durasi_menit = $validated['durasi_menit'];
        $setting->tanggal_pelaksanaan = $validated['tanggal_pelaksanaan'] ?? ($setting->tanggal_pelaksanaan ?: now()->toDateString());
        $setting->sesi_default = $defaultSesiCandidate ?: ($setting->sesi_default ?: 'Sesi 1 (08.00 - 10.00 WIB)');
        $setting->ruang_default = $validated['ruang_default'] ?? ($setting->ruang_default ?: 'Lab Komputer SMKN 1 Air Naningan');
        $setting->gelombang_label = $validated['gelombang_label'] ?? ($setting->gelombang_label ?: '1x Gelombang (Sesuai Juknis Resmi)');
        $setting->bobot_pg = $validated['bobot_pg'];
        $setting->bobot_esai = $validated['bobot_esai'];
        $setting->is_active = $request->has('is_active');
        $setting->petunjuk_ujian = $validated['petunjuk_ujian'] ?? null;
        $setting->created_by = auth()->id();
        $setting->save();

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'pengaturan'])
            ->with('success', 'Pengaturan sesi ujian CBT dan daftar sesi berhasil disimpan.');
    }

    /**
     * Tetapkan Jadwal Ujian Serentak (Fleksibel Pilih Sesi atau Bagi Rata)
     */
    public function jadwalkanJuknisSerentak(Request $request)
    {
        $setting = PpdbUjianSetting::getAktif();
        $tanggal = $request->input('jadwal_tes_tanggal') ?: ($setting?->tanggal_pelaksanaan ? $setting->tanggal_pelaksanaan->format('Y-m-d') : now()->toDateString());
        $ruang = $request->input('jadwal_tes_ruang') ?: ($setting?->ruang_default ?: 'Lab Komputer SMKN 1 Air Naningan');
        $sesiPilihan = $request->input('sesi_pilihan', 'default');

        $pendaftars = PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima'])->orderBy('id')->get();
        $sesiOptions = $setting ? $setting->sesi_options : ['Sesi 1 (08.00 - 10.00 WIB)'];
        $count = 0;

        foreach ($pendaftars as $index => $p) {
            $p->jadwal_tes_tanggal = $tanggal;
            $p->jadwal_tes_ruang = $ruang;

            if ($sesiPilihan === 'bagi_rata' && count($sesiOptions) > 1) {
                // Distribusi peserta secara merata ke semua sesi yang tersedia
                $sesiIdx = $index % count($sesiOptions);
                $p->jadwal_tes_sesi = $sesiOptions[$sesiIdx];
            } elseif ($sesiPilihan !== 'default' && !empty($sesiPilihan)) {
                $p->jadwal_tes_sesi = $sesiPilihan;
            } else {
                $p->jadwal_tes_sesi = $setting?->sesi_default ?: ($sesiOptions[0] ?? 'Sesi 1 (08.00 - 10.00 WIB)');
            }

            $p->save();
            $count++;
        }

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'penjadwalan'])
            ->with('success', "Sukses menetapkan jadwal ujian untuk {$count} calon peserta!");
    }

    /**
     * Jadwalkan Sesi, Ruang, dan Tanggal Ujian Massal Tercentang
     */
    public function jadwalkanMassal(Request $request)
    {
        $validated = $request->validate([
            'pendaftar_ids'        => 'required|array',
            'pendaftar_ids.*'      => 'exists:ppdb_pendaftars,id',
            'jadwal_tes_tanggal'   => 'required|date',
            'jadwal_tes_sesi'      => 'required|string|max:150',
            'jadwal_tes_ruang'     => 'required|string|max:150',
        ]);

        $count = 0;
        foreach ($validated['pendaftar_ids'] as $id) {
            $p = PpdbPendaftar::find($id);
            if ($p) {
                $p->jadwal_tes_tanggal = $validated['jadwal_tes_tanggal'];
                $p->jadwal_tes_sesi = $validated['jadwal_tes_sesi'];
                $p->jadwal_tes_ruang = $validated['jadwal_tes_ruang'];
                $p->save();
                $count++;
            }
        }

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'penjadwalan'])
            ->with('success', "Berhasil menjadwalkan {$count} calon peserta ujian seleksi!");
    }

    /**
     * Jadwalkan Sesi, Ruang, dan Tanggal untuk 1 Calon Siswa (Single Edit)
     */
    public function jadwalkanSingle(Request $request, $id)
    {
        $validated = $request->validate([
            'jadwal_tes_tanggal' => 'required|date',
            'jadwal_tes_sesi'    => 'required|string|max:150',
            'jadwal_tes_ruang'   => 'required|string|max:150',
        ]);

        $peserta = PpdbPendaftar::findOrFail($id);
        $peserta->jadwal_tes_tanggal = $validated['jadwal_tes_tanggal'];
        $peserta->jadwal_tes_sesi = $validated['jadwal_tes_sesi'];
        $peserta->jadwal_tes_ruang = $validated['jadwal_tes_ruang'];
        $peserta->save();

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'penjadwalan'])
            ->with('success', "Jadwal untuk {$peserta->nama_lengkap} berhasil diperbarui ({$peserta->jadwal_tes_sesi})!");
    }

    /**
     * Simpan Koreksi dan Nilai 5 Soal Esai Peserta
     */
    public function simpanNilaiEsai(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::with('ujianPeserta')->findOrFail($id);
        $peserta = $pendaftar->ujianPeserta;

        if (!$peserta) {
            return back()->with('error', 'Peserta belum memulai atau mengumpulkan lembar jawaban ujian.');
        }

        $rincianEsai = [];
        $totalSkorEsai = 0;

        if ($request->has('nilai_esai') && is_array($request->input('nilai_esai'))) {
            $inputEsai = $request->input('nilai_esai');
            foreach ($inputEsai as $k => $v) {
                $numVal = min(10, max(0, (float) $v));
                $rincianEsai[(string)$k] = $numVal;
                $totalSkorEsai += $numVal;
            }
        } else {
            for ($i = 31; $i <= 35; $i++) {
                $numVal = min(10, max(0, (float) $request->input("nilai_esai_{$i}", 0)));
                $rincianEsai[(string)$i] = $numVal;
                $totalSkorEsai += $numVal;
            }
        }

        $peserta->nilai_per_nomor_esai = $rincianEsai;
        $peserta->nilai_esai = $totalSkorEsai;
        $peserta->catatan_koreksi_esai = $request->input('catatan_koreksi_esai');
        $peserta->diperiksa_oleh = auth()->id();
        $peserta->diperiksa_pada = now();
        $peserta->status_pengerjaan = 'selesai_dinilai';
        $peserta->sinkronNilaiKePendaftar();

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'tertulis'])
            ->with('success', "Koreksi soal esai untuk {$pendaftar->nama_lengkap} berhasil disimpan! Total Nilai Tertulis: {$pendaftar->nilai_tes_tertulis}.");
    }

    /**
     * Simpan Rubrik Penilaian Wawancara (4 Kriteria)
     */
    public function simpanNilaiWawancara(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::findOrFail($id);

        $validated = $request->validate([
            'nilai_wawancara_motivasi' => 'required|numeric|min:0|max:100',
            'nilai_wawancara_karakter' => 'required|numeric|min:0|max:100',
            'nilai_wawancara_kejuruan' => 'required|numeric|min:0|max:100',
            'nilai_wawancara_ortu'     => 'required|numeric|min:0|max:100',
            'catatan_wawancara'        => 'nullable|string',
        ]);

        $pendaftar->nilai_wawancara_motivasi = $validated['nilai_wawancara_motivasi'];
        $pendaftar->nilai_wawancara_karakter = $validated['nilai_wawancara_karakter'];
        $pendaftar->nilai_wawancara_kejuruan = $validated['nilai_wawancara_kejuruan'];
        $pendaftar->nilai_wawancara_ortu     = $validated['nilai_wawancara_ortu'];
        $pendaftar->catatan_wawancara        = $validated['catatan_wawancara'] ?? null;
        $pendaftar->pewawancara_id           = auth()->id();
        $pendaftar->diwawancara_pada         = now();

        $pendaftar->hitungNilaiWawancara();
        $pendaftar->hitungNilaiAkhir();
        $pendaftar->save();

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'wawancara'])
            ->with('success', "Penilaian wawancara untuk {$pendaftar->nama_lengkap} berhasil disimpan! Skor Wawancara: {$pendaftar->nilai_wawancara_total} (Skor Akhir: {$pendaftar->nilai_akhir}).");
    }

    /**
     * Perangkingan Otomatis & Kalkulasi Kelulusan Terbobot Berdasarkan Kuota
     */
    public function kalkulasiKelulusan(Request $request)
    {
        $validated = $request->validate([
            'kuota_per_rombel' => 'nullable|integer|min:1|max:100',
            'passing_grade'    => 'nullable|numeric|min:0|max:100',
        ]);

        $kuotaPerRombel = (int) ($validated['kuota_per_rombel'] ?? 36);
        $passingGrade   = (float) ($validated['passing_grade'] ?? 50.00);

        // Ambil semua pendaftar dalam proses seleksi
        $pendaftars = PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid', 'diterima', 'cadangan', 'siap_tes'])
            ->get();

        if ($pendaftars->isEmpty()) {
            return redirect()->route('admin.ppdb.seleksi', ['tab' => 'leaderboard'])
                ->with('warning', 'Tidak ada calon siswa dengan berkas terverifikasi yang siap diperingkatkan.');
        }

        // 1. Hitung nilai akhir untuk setiap calon siswa
        foreach ($pendaftars as $p) {
            $p->hitungNilaiWawancara();
            $p->hitungNilaiAkhir();
            $p->save();
        }

        // 2. Siapkan kuota per jurusan berdasarkan jumlah rombel Kelas X aktif
        $jurusans = Jurusan::all();
        $kuotaJurusan = [];
        $diterimaCount = [];

        foreach ($jurusans as $j) {
            $rombelCount = Rombel::where(function ($q) {
                $q->where('tingkat', '10')->orWhere('tingkat', 'X');
            })->where('jurusan_id', $j->id)->count();

            // Default minimal 1 rombel jika belum diset rombel spesifik
            $totalKuota = max($rombelCount, 1) * $kuotaPerRombel;
            $kuotaJurusan[$j->id] = $totalKuota;
            $diterimaCount[$j->id] = 0;
        }

        // 3. TAHAP 1: Alokasi Jurusan Pilihan 1
        $pendingPilihan2 = [];

        foreach ($jurusans as $j) {
            $pelamar = PpdbPendaftar::whereIn('id', $pendaftars->pluck('id'))
                ->where('jurusan_id_1', $j->id)
                ->whereNotNull('nilai_akhir')
                ->orderBy('nilai_akhir', 'desc')
                ->get();

            $rank = 1;
            foreach ($pelamar as $p) {
                // Pertahankan siswa yang sudah resmi dimutasi ke rombel
                if ($p->siswa_id) {
                    $diterimaCount[$j->id]++;
                    $p->peringkat_jurusan = $rank++;
                    $p->status = 'diterima';
                    $p->jurusan_diterima_id = $j->id;
                    $p->save();
                    continue;
                }

                $nilai = (float) $p->nilai_akhir;

                // Lolos jika memenuhi passing grade dan kuota Pilihan 1 masih tersedia
                if ($nilai >= $passingGrade && $diterimaCount[$j->id] < $kuotaJurusan[$j->id]) {
                    $p->peringkat_jurusan = $rank++;
                    $p->status = 'diterima';
                    $p->jurusan_diterima_id = $j->id;
                    $p->save();
                    $diterimaCount[$j->id]++;
                } else {
                    // Masukkan ke antrean evaluasi Pilihan 2
                    $pendingPilihan2[] = $p;
                }
            }
        }

        // 4. TAHAP 2: Alokasi Jurusan Pilihan 2 bagi peserta yang belum lolos Pilihan 1
        usort($pendingPilihan2, function ($a, $b) {
            return $b->nilai_akhir <=> $a->nilai_akhir;
        });

        foreach ($pendingPilihan2 as $p) {
            if ($p->siswa_id) continue;

            $jur2 = $p->jurusan_id_2;
            $nilai = (float) $p->nilai_akhir;

            if ($jur2 && isset($kuotaJurusan[$jur2]) && $diterimaCount[$jur2] < $kuotaJurusan[$jur2] && $nilai >= $passingGrade) {
                // Diterima di Pilihan 2
                $diterimaCount[$jur2]++;
                $p->status = 'diterima';
                $p->jurusan_diterima_id = $jur2;
                $p->peringkat_jurusan = $diterimaCount[$jur2];
                $p->save();
            } elseif ($nilai >= $passingGrade) {
                // Nilai memenuhi standar tapi kuota penuh -> CADANGAN
                $p->status = 'cadangan';
                $p->jurusan_diterima_id = null;
                $p->save();
            } else {
                // Nilai di bawah passing grade -> TIDAK LOLOS
                $p->status = 'ditolak';
                $p->jurusan_diterima_id = null;
                $p->save();
            }
        }

        $totalDiterima = array_sum($diterimaCount);
        AuditLog::catat(
            'update',
            'ppdb',
            "Kalkulasi kelulusan PPDB: {$totalDiterima} siswa DITERIMA, kuota {$kuotaPerRombel}/rombel, passing grade {$passingGrade}",
            null,
            ['kuota_per_rombel' => $kuotaPerRombel, 'passing_grade' => $passingGrade, 'diterima_per_jurusan' => $diterimaCount]
        );

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'leaderboard'])
            ->with('success', "Kalkulasi kelulusan selesai! Sebanyak {$totalDiterima} calon siswa resmi dinyatakan Lulus / Diterima sesuai kuota kejuruan.");
    }

    /**
     * Mutasi Massal Calon Siswa Diterima ke Rombel Kelas X (Auto-mapping Jurusan)
     */
    public function mutasiMassal(Request $request, PpdbMutasiService $mutasiService)
    {
        $request->validate([
            'pendaftar_ids'   => 'required|array|min:1',
            'pendaftar_ids.*' => 'exists:ppdb_pendaftars,id',
        ]);

        $berhasil = 0;
        $gagal    = 0;
        $pesanGagal = [];

        // Ambil semua rombel Kelas X yang punya relasi jurusan_id
        $rombelKelasX = Rombel::where(function ($q) {
            $q->where('tingkat', 'X')->orWhere('tingkat', '10');
        })->get()->keyBy('jurusan_id'); // key = jurusan_id

        foreach ($request->pendaftar_ids as $pid) {
            $pendaftar = PpdbPendaftar::find($pid);
            if (!$pendaftar) { $gagal++; continue; }

            // Pastikan sudah diterima
            if ($pendaftar->status !== 'diterima') {
                $gagal++;
                $pesanGagal[] = "{$pendaftar->nama_lengkap}: status bukan 'diterima'";
                continue;
            }

            // Jika sudah dimutasi, skip
            if ($pendaftar->siswa_id) {
                $gagal++;
                $pesanGagal[] = "{$pendaftar->nama_lengkap}: sudah dimutasi sebelumnya";
                continue;
            }

            // Tentukan rombel berdasarkan jurusan_diterima_id
            $jurusanId = $pendaftar->jurusan_diterima_id ?? $pendaftar->jurusan_id_1;
            $rombel = $rombelKelasX->get($jurusanId);

            if (!$rombel) {
                $gagal++;
                $pesanGagal[] = "{$pendaftar->nama_lengkap}: rombel Kelas X untuk jurusan tidak ditemukan";
                continue;
            }

            try {
                $siswa = $mutasiService->mutasiKeSiswa($pendaftar, $rombel->id);
                AuditLog::catat(
                    'create', 'ppdb',
                    "[MASSAL] Mutasi {$pendaftar->nama_lengkap} ({$pendaftar->no_pendaftaran}) ke {$rombel->nama_rombel}",
                    null,
                    ['pendaftar_id' => $pendaftar->id, 'siswa_id' => $siswa->id, 'rombel_id' => $rombel->id],
                    $pendaftar
                );
                $berhasil++;
            } catch (\Exception $e) {
                $gagal++;
                $pesanGagal[] = "{$pendaftar->nama_lengkap}: " . $e->getMessage();
            }
        }

        $msg = "Mutasi massal selesai: {$berhasil} siswa berhasil dimutasi ke Kelas X.";
        if ($gagal > 0) {
            $msg .= " {$gagal} gagal: " . implode('; ', array_slice($pesanGagal, 0, 3));
            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg);
    }

    /**
     * Koreksi Jurusan Diterima (jika ada kesalahan input)
     */
    public function koreksiJurusan(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::findOrFail($id);

        $validated = $request->validate([
            'jurusan_diterima_id' => 'required|exists:jurusans,id',
            'alasan_koreksi'      => 'nullable|string|max:300',
        ]);

        $jurusanLama = $pendaftar->jurusan_diterima_id;
        $pendaftar->jurusan_diterima_id = $validated['jurusan_diterima_id'];
        $pendaftar->save();

        AuditLog::catat(
            'update', 'ppdb',
            "Koreksi jurusan diterima: {$pendaftar->nama_lengkap} ({$pendaftar->no_pendaftaran}) jurusan_diterima diubah",
            ['jurusan_diterima_id' => $jurusanLama],
            ['jurusan_diterima_id' => $pendaftar->jurusan_diterima_id, 'alasan' => $validated['alasan_koreksi'] ?? null],
            $pendaftar
        );

        $jurusan = Jurusan::find($validated['jurusan_diterima_id']);
        return back()->with('success', "Jurusan yang diterima untuk {$pendaftar->nama_lengkap} berhasil diperbarui menjadi {$jurusan->nama_jurusan}.");
    }
}
