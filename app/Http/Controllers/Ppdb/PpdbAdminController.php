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
            'status_pendaftaran' => 'required|in:menunggu,berkas_valid,diterima,ditolak',
            'jurusan_diterima_id' => 'nullable|exists:jurusans,id',
            'catatan' => 'nullable|string',
        ]);

        $st = $validated['status_pendaftaran'];
        if ($st === 'menunggu') $st = 'menunggu_verifikasi';
        if ($st === 'berkas_valid') $st = 'terverifikasi';

        $statusLama = $pendaftar->status;
        $jurusanLama = $pendaftar->jurusan_diterima_id;

        $pendaftar->status = $st;
        if (!empty($validated['jurusan_diterima_id'])) {
            $pendaftar->jurusan_diterima_id = $validated['jurusan_diterima_id'];
        }
        if (isset($validated['catatan'])) {
            $pendaftar->catatan_panitia = $validated['catatan'];
        }
        $pendaftar->diverifikasi_oleh = auth()->user()->nama ?? auth()->user()->name ?? 'Admin';
        $pendaftar->diverifikasi_pada = now();
        $pendaftar->save();

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
            'total_valid'      => PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid', 'diterima', 'siap_tes'])->count(),
            'sudah_jadwal'     => PpdbPendaftar::whereNotNull('jadwal_tes_tanggal')->count(),
            'sudah_pg'         => PpdbUjianPeserta::whereNotNull('waktu_selesai')->count(),
            'menunggu_esai'    => PpdbUjianPeserta::where('status_pengerjaan', 'selesai_menunggu_koreksi')->count(),
            'sudah_wawancara'  => PpdbPendaftar::whereNotNull('nilai_wawancara_total')->count(),
            'siap_dirangking'  => PpdbPendaftar::whereNotNull('nilai_akhir')->count(),
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
     * Simpan Pengaturan Ujian CBT (File PDF Soal, Kunci Jawaban 30 PG, Bobot Esai)
     */
    public function simpanSettingUjian(Request $request)
    {
        $validated = $request->validate([
            'judul_ujian'      => 'required|string|max:255',
            'durasi_menit'     => 'required|integer|min:15|max:180',
            'bobot_pg'         => 'required|numeric|min:10|max:90',
            'bobot_esai'       => 'required|numeric|min:10|max:90',
            'is_active'        => 'nullable|boolean',
            'file_pdf_soal'    => 'nullable|file|mimes:pdf|max:20480',
            'kunci'            => 'nullable|array',
            'petunjuk_ujian'   => 'nullable|string',
        ]);

        $setting = PpdbUjianSetting::getAktif();
        if (!$setting) {
            $setting = new PpdbUjianSetting();
        }

        $setting->judul_ujian = $validated['judul_ujian'];
        $setting->durasi_menit = $validated['durasi_menit'];
        $setting->bobot_pg = $validated['bobot_pg'];
        $setting->bobot_esai = $validated['bobot_esai'];
        $setting->is_active = $request->has('is_active');
        $setting->petunjuk_ujian = $validated['petunjuk_ujian'] ?? null;
        $setting->created_by = auth()->id();

        // Upload PDF Soal jika ada
        if ($request->hasFile('file_pdf_soal')) {
            $file = $request->file('file_pdf_soal');
            $setting->nama_file_asli = $file->getClientOriginalName();
            $path = $file->store('ppdb/soal', 'public');
            $setting->file_pdf_soal = $path;
        }

        // Susun kunci jawaban PG 1..30
        $kunciInput = $request->input('kunci', []);
        $kunciClean = [];
        for ($i = 1; $i <= 30; $i++) {
            $str = (string) $i;
            if (isset($kunciInput[$str])) {
                $kunciClean[$str] = strtoupper(trim($kunciInput[$str]));
            }
        }
        $setting->kunci_jawaban_pg = $kunciClean;
        $setting->jumlah_soal_pg = 30;
        $setting->jumlah_soal_esai = 5;
        $setting->save();

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'pengaturan'])
            ->with('success', 'Pengaturan Naskah Soal PDF dan Kunci Jawaban Ujian berhasil disimpan!');
    }

    /**
     * Jadwalkan Sesi, Ruang, dan Tanggal Ujian Massal
     */
    public function jadwalkanMassal(Request $request)
    {
        $validated = $request->validate([
            'pendaftar_ids'        => 'required|array',
            'pendaftar_ids.*'      => 'exists:ppdb_pendaftars,id',
            'jadwal_tes_tanggal'   => 'required|date',
            'jadwal_tes_sesi'      => 'required|string|max:100',
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
     * Simpan Koreksi dan Nilai 5 Soal Esai Peserta
     */
    public function simpanNilaiEsai(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::with('ujianPeserta')->findOrFail($id);
        $peserta = $pendaftar->ujianPeserta;

        if (!$peserta) {
            return back()->with('error', 'Peserta belum memulai atau mengumpulkan lembar jawaban ujian.');
        }

        $validated = $request->validate([
            'nilai_esai_31'       => 'required|numeric|min:0|max:10',
            'nilai_esai_32'       => 'required|numeric|min:0|max:10',
            'nilai_esai_33'       => 'required|numeric|min:0|max:10',
            'nilai_esai_34'       => 'required|numeric|min:0|max:10',
            'nilai_esai_35'       => 'required|numeric|min:0|max:10',
            'catatan_koreksi_esai'=> 'nullable|string',
        ]);

        $rincianEsai = [
            '31' => (float) $validated['nilai_esai_31'],
            '32' => (float) $validated['nilai_esai_32'],
            '33' => (float) $validated['nilai_esai_33'],
            '34' => (float) $validated['nilai_esai_34'],
            '35' => (float) $validated['nilai_esai_35'],
        ];

        $totalSkorEsai = array_sum($rincianEsai);

        $peserta->nilai_per_nomor_esai = $rincianEsai;
        $peserta->nilai_esai = $totalSkorEsai;
        $peserta->catatan_koreksi_esai = $validated['catatan_koreksi_esai'] ?? null;
        $peserta->diperiksa_oleh = auth()->id();
        $peserta->diperiksa_pada = now();
        $peserta->status_pengerjaan = 'selesai_dinilai';
        $peserta->sinkronNilaiKePendaftar();

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'tertulis'])
            ->with('success', "Koreksi 5 soal esai untuk {$pendaftar->nama_lengkap} berhasil disimpan! Total Nilai Tertulis: {$pendaftar->nilai_tes_tertulis}.");
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
     * Perangkingan Otomatis & Kalkulasi Kelulusan Terbobot
     */
    public function kalkulasiKelulusan(Request $request)
    {
        $pendaftars = PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid', 'diterima', 'siap_tes'])
            ->get();

        foreach ($pendaftars as $p) {
            $p->hitungNilaiWawancara();
            $p->hitungNilaiAkhir();
            $p->save();
        }

        // Perangkingan per jurusan pilihan 1
        $jurusans = Jurusan::all();
        foreach ($jurusans as $j) {
            $ranked = PpdbPendaftar::where('jurusan_id_1', $j->id)
                ->whereNotNull('nilai_akhir')
                ->orderBy('nilai_akhir', 'desc')
                ->get();

            $rank = 1;
            foreach ($ranked as $item) {
                $item->peringkat_jurusan = $rank++;
                $item->save();
            }
        }

        return redirect()->route('admin.ppdb.seleksi', ['tab' => 'leaderboard'])
            ->with('success', 'Kalkulasi nilai akhir dan perangkingan kuota jurusan berhasil diperbarui secara otomatis!');
    }
}
