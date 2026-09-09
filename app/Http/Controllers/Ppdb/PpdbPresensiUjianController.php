<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSekolah;
use App\Models\PpdbAbsensiUjian;
use App\Models\PpdbPendaftar;
use App\Models\PpdbUjianSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PpdbPresensiUjianController extends Controller
{
    /**
     * Kios Presensi Barcode / QR Ujian PPDB & Monitor Kehadiran Realtime
     */
    public function kios(Request $request)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $setting = PpdbUjianSetting::getAktif();

        $tanggal = $request->input('tanggal', $setting?->tanggal_pelaksanaan ? \Carbon\Carbon::parse($setting->tanggal_pelaksanaan)->format('Y-m-d') : Carbon::today()->toDateString());
        $sesiFilter = $request->input('sesi');
        $ruangFilter = $request->input('ruang');

        // Daftar opsi sesi & ruang yang terdaftar, selalu sinkron dengan waktu resmi panitia
        $daftarSesi = PpdbPendaftar::whereNotNull('jadwal_tes_sesi')
            ->where('jadwal_tes_sesi', '!=', '')
            ->distinct()
            ->pluck('jadwal_tes_sesi')
            ->map(function($s) {
                if (preg_match('/\((.*?)\)/', $s, $m)) return trim($m[1]);
                return trim(preg_replace('/^Sesi\s*\d+\s*[-:]*\s*/i', '', $s));
            })
            ->filter(fn($s) => !empty($s) && $s !== '08.00 - 09.30')
            ->unique()
            ->values()
            ->toArray();

        if ($setting && $setting->waktu_pelaksanaan && !in_array($setting->waktu_pelaksanaan, $daftarSesi)) {
            array_unshift($daftarSesi, $setting->waktu_pelaksanaan);
        }
        if (empty($daftarSesi) && $setting) {
            $daftarSesi = [$setting->waktu_pelaksanaan];
        }

        $daftarRuang = PpdbPendaftar::whereNotNull('jadwal_tes_ruang')
            ->where('jadwal_tes_ruang', '!=', '')
            ->where('jadwal_tes_ruang', '!=', 'Lab Komputer 1')
            ->distinct()
            ->pluck('jadwal_tes_ruang')
            ->toArray();

        if ($setting && $setting->ruang_default && !in_array($setting->ruang_default, $daftarRuang)) {
            array_unshift($daftarRuang, $setting->ruang_default);
        }

        // Query dasar pendaftar yang siap ujian / berjadwal
        $pendaftarQuery = PpdbPendaftar::with(['jurusanPilihan1', 'absensiUjian' => function($q) use ($tanggal) {
            $q->whereDate('jadwal_tanggal', $tanggal);
        }])
        ->whereIn('status', ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima']);

        if ($sesiFilter) {
            $pendaftarQuery->where('jadwal_tes_sesi', $sesiFilter);
        }
        if ($ruangFilter) {
            $pendaftarQuery->where('jadwal_tes_ruang', $ruangFilter);
        }

        // Pendaftar terfilter
        $listPeserta = (clone $pendaftarQuery)->orderBy('nama_lengkap', 'asc')->get();

        // Rekap statistik kehadiran
        $totalTerjadwal = $listPeserta->count();
        $totalHadir = $listPeserta->filter(fn($p) => $p->absensiUjian !== null)->count();
        $totalBelumHadir = max(0, $totalTerjadwal - $totalHadir);
        $persenHadir = $totalTerjadwal > 0 ? round(($totalHadir / $totalTerjadwal) * 100, 1) : 0;

        // Riwayat kehadiran terbaru hari ini (Live Feed)
        $riwayatTerbaru = PpdbAbsensiUjian::with(['pendaftar.jurusanPilihan1', 'petugas'])
            ->whereDate('jadwal_tanggal', $tanggal)
            ->latest('waktu_hadir')
            ->limit(15)
            ->get();

        return view('ppdb.admin.presensi_kios', compact(
            'sekolah',
            'setting',
            'tanggal',
            'sesiFilter',
            'ruangFilter',
            'daftarSesi',
            'daftarRuang',
            'listPeserta',
            'totalTerjadwal',
            'totalHadir',
            'totalBelumHadir',
            'persenHadir',
            'riwayatTerbaru'
        ));
    }

    /**
     * AJAX Endpoint: Pemindaian 2D Barcode (QR Code) & USB Scanner
     */
    public function scan(Request $request)
    {
        $rawCode = trim($request->input('input_code', ''));
        $metode = $request->input('metode', 'barcode_scanner');

        if (empty($rawCode)) {
            return response()->json([
                'status'  => 'empty',
                'message' => 'Kode barcode tidak boleh kosong.',
            ], 422);
        }

        // 1. Ekstraksi nomor pendaftaran jika pemindai membaca URL status / kartu cetak
        $searchKey = $rawCode;
        if (str_contains($rawCode, 'keyword=')) {
            $parts = parse_url($rawCode);
            if (isset($parts['query'])) {
                parse_str($parts['query'], $queryData);
                if (!empty($queryData['keyword'])) {
                    $searchKey = trim($queryData['keyword']);
                }
            }
        } elseif (str_contains($rawCode, '/ujian/')) {
            $segments = explode('/', rtrim($rawCode, '/'));
            $searchKey = end($segments);
        }

        // Normalisasi format singkat misal "1" -> "PPDB-2026-0001"
        $formattedNomor = $searchKey;
        if (is_numeric($searchKey) && strlen($searchKey) <= 5) {
            $formattedNomor = sprintf('PPDB-%s-%04d', date('Y'), (int) $searchKey);
        }

        // 2. Cari Pendaftar di Database
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2'])
            ->where('no_pendaftaran', $searchKey)
            ->orWhere('no_pendaftaran', $formattedNomor)
            ->orWhere('nisn', $searchKey)
            ->first();

        if (!$pendaftar) {
            return response()->json([
                'status'  => 'not_found',
                'code'    => $rawCode,
                'message' => "Data calon siswa dengan kode '{$searchKey}' tidak ditemukan dalam database PPDB.",
            ], 404);
        }

        // 3. Validasi Kelayakan Berkas / Status
        $statusValid = in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima']);
        if (!$statusValid) {
            return response()->json([
                'status'    => 'invalid_status',
                'pendaftar' => $this->formatPendaftarData($pendaftar),
                'message'   => "Status berkas '{$pendaftar->nama_lengkap}' masih '{$pendaftar->status}'. Belum dinyatakan lolos verifikasi berkas.",
            ], 422);
        }

        $today = Carbon::today()->toDateString();
        $setting = PpdbUjianSetting::getAktif();

        // 4. Cek apakah sudah pernah presensi hari ini
        $existing = PpdbAbsensiUjian::where('ppdb_pendaftar_id', $pendaftar->id)
            ->whereDate('jadwal_tanggal', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'status'      => 'already_attended',
                'waktu_hadir' => $existing->waktu_hadir->format('H:i:s'),
                'pendaftar'   => $this->formatPendaftarData($pendaftar, $existing),
                'message'     => "Calon siswa {$pendaftar->nama_lengkap} SUDAH tercatat hadir pada pukul {$existing->waktu_hadir->format('H:i:s')} WIB.",
            ]);
        }

        // 5. Rekam Presensi Ujian Baru
        $sesi = $pendaftar->jadwal_sesi_resmi ?: ($setting ? $setting->waktu_pelaksanaan : '08.00 - 10.00 WIB');
        $ruang = $pendaftar->jadwal_ruang_resmi ?: ($setting ? $setting->ruang_default : 'Lab Komputer SMKN 1 Air Naningan');

        $absensi = PpdbAbsensiUjian::create([
            'ppdb_pendaftar_id'     => $pendaftar->id,
            'ppdb_ujian_setting_id' => $setting?->id,
            'no_pendaftaran'        => $pendaftar->no_pendaftaran,
            'jadwal_tanggal'        => $today,
            'sesi_ujian'            => $sesi,
            'ruang_ujian'           => $ruang,
            'waktu_hadir'           => now(),
            'status_kehadiran'      => 'hadir',
            'metode_presensi'       => $metode,
            'petugas_user_id'       => auth()->id(),
            'catatan'               => 'Presensi sukses via 2D scanner pos ujian',
        ]);

        return response()->json([
            'status'      => 'success',
            'waktu_hadir' => $absensi->waktu_hadir->format('H:i:s'),
            'pendaftar'   => $this->formatPendaftarData($pendaftar, $absensi),
            'message'     => "Presensi Ujian Berhasil! Selamat datang {$pendaftar->nama_lengkap}.",
        ]);
    }

    /**
     * Presensi Manual oleh Panitia
     */
    public function manualHadir(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::findOrFail($id);
        $today = Carbon::today()->toDateString();
        $setting = PpdbUjianSetting::getAktif();

        $absensi = PpdbAbsensiUjian::updateOrCreate(
            [
                'ppdb_pendaftar_id' => $pendaftar->id,
                'jadwal_tanggal'    => $today,
            ],
            [
                'ppdb_ujian_setting_id' => $setting?->id,
                'no_pendaftaran'        => $pendaftar->no_pendaftaran,
                'sesi_ujian'            => $pendaftar->jadwal_sesi_resmi ?: ($setting ? $setting->waktu_pelaksanaan : '08.00 - 10.00 WIB'),
                'ruang_ujian'           => $pendaftar->jadwal_ruang_resmi ?: ($setting ? $setting->ruang_default : 'Lab Komputer SMKN 1 Air Naningan'),
                'waktu_hadir'           => now(),
                'status_kehadiran'      => 'hadir',
                'metode_presensi'       => 'manual_panitia',
                'petugas_user_id'       => auth()->id(),
                'catatan'               => 'Hadir manual disahkan oleh Panitia Pengawas',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Kehadiran {$pendaftar->nama_lengkap} berhasil disahkan secara manual.",
            ]);
        }

        return back()->with('success', "Kehadiran {$pendaftar->nama_lengkap} berhasil dicatat secara manual.");
    }

    /**
     * Batal Presensi / Hapus Entri Presensi
     */
    public function batalHadir($id)
    {
        $absensi = PpdbAbsensiUjian::with('pendaftar')->findOrFail($id);
        $nama = $absensi->pendaftar?->nama_lengkap ?: $absensi->no_pendaftaran;
        $absensi->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Presensi atas nama {$nama} berhasil dibatalkan.",
            ]);
        }

        return back()->with('success', "Presensi ujian atas nama {$nama} berhasil dibatalkan.");
    }

    /**
     * Cetak Berita Acara & Daftar Hadir Ujian Seleksi Format A4 Resmi
     */
    public function cetakDaftarHadir(Request $request)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $setting = PpdbUjianSetting::getAktif();

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $sesiFilter = $request->input('sesi');
        $ruangFilter = $request->input('ruang');

        $query = PpdbPendaftar::with(['jurusanPilihan1', 'absensiUjian' => function($q) use ($tanggal) {
            $q->whereDate('jadwal_tanggal', $tanggal);
        }])
        ->whereIn('status', ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima']);

        if ($sesiFilter) {
            $query->where('jadwal_tes_sesi', $sesiFilter);
        }
        if ($ruangFilter) {
            $query->where('jadwal_tes_ruang', $ruangFilter);
        }

        $pesertas = $query->orderBy('nama_lengkap', 'asc')->get();

        $totalTerjadwal = $pesertas->count();
        $totalHadir = $pesertas->filter(fn($p) => $p->absensiUjian !== null)->count();
        $totalTidakHadir = max(0, $totalTerjadwal - $totalHadir);

        return view('ppdb.admin.cetak_daftar_hadir', compact(
            'sekolah',
            'setting',
            'tanggal',
            'sesiFilter',
            'ruangFilter',
            'pesertas',
            'totalTerjadwal',
            'totalHadir',
            'totalTidakHadir'
        ));
    }

    /**
     * Helper pemformat data pendaftar untuk respon JSON
     */
    protected function formatPendaftarData(PpdbPendaftar $pendaftar, ?PpdbAbsensiUjian $absensi = null): array
    {
        $fotoUrl = ($pendaftar->berkas_foto && file_exists(public_path('storage/' . $pendaftar->berkas_foto)))
            ? asset('storage/' . $pendaftar->berkas_foto)
            : null;

        return [
            'id'             => $pendaftar->id,
            'no_pendaftaran' => $pendaftar->no_pendaftaran,
            'nisn'           => $pendaftar->nisn,
            'nama_lengkap'   => strtoupper($pendaftar->nama_lengkap),
            'jenis_kelamin'  => $pendaftar->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            'asal_sekolah'   => $pendaftar->asal_sekolah,
            'jurusan_1'      => $pendaftar->jurusanPilihan1 ? $pendaftar->jurusanPilihan1->nama_jurusan : '-',
            'jurusan_kode'   => $pendaftar->jurusanPilihan1 ? ($pendaftar->jurusanPilihan1->kode_jurusan ?: $pendaftar->jurusanPilihan1->kode) : '',
            'ruang'          => $pendaftar->jadwal_tes_ruang ?: ($absensi?->ruang_ujian ?: 'Lab Komputer'),
            'sesi'           => $pendaftar->jadwal_tes_sesi ?: ($absensi?->sesi_ujian ?: 'Sesi 1'),
            'foto'           => $fotoUrl,
            'status_berkas'  => $pendaftar->status,
            'waktu_hadir'    => $absensi ? $absensi->waktu_hadir->format('H:i:s') : now()->format('H:i:s'),
        ];
    }
}
