<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\PpdbPendaftar;
use App\Models\PpdbUjianSetting;
use Illuminate\Http\Request;

class PpdbGuruWawancaraController extends Controller
{
    /**
     * Pastikan user yang mengakses adalah Tim Panitia PPDB atau Guru yang ditugaskan sebagai Pewawancara
     */
    protected function authorizeGuru()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        if (!$user->canAccessWawancaraPpdb()) {
            abort(403, 'Akses terbatas. Meja wawancara hanya dapat diakses oleh Panitia PPDB atau Guru yang ditugaskan sebagai Penguji Wawancara.');
        }

        return $user;
    }

    /**
     * Dashboard / Portal Antrean Wawancara Khusus Guru Penguji
     */
    public function index(Request $request)
    {
        $user = $this->authorizeGuru();
        $setting = PpdbUjianSetting::getAktif();
        $jurusans = Jurusan::all();

        $isPanitia = $user->canAccessPpdb();
        // Guru biasa pewawancara dikunci pada tab 'saya' (hanya siswa binaan)
        $tab = $isPanitia ? $request->query('tab', 'saya') : 'saya';
        $cari = $request->query('cari');
        $jurusanId = $request->query('jurusan_id');

        // Statistik Penugasan Khusus Guru Ini
        $totalDitugaskan = PpdbPendaftar::where('pewawancara_id', $user->id)
            ->whereIn('status', ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima'])
            ->count();

        $sudahDiuji = PpdbPendaftar::where('pewawancara_id', $user->id)
            ->whereIn('status', ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima'])
            ->whereNotNull('nilai_wawancara_total')
            ->count();

        $belumDiuji = max(0, $totalDitugaskan - $sudahDiuji);

        // Query Peserta
        $query = PpdbPendaftar::with(['jurusan1', 'jurusan2', 'pewawancara'])
            ->whereIn('status', ['terverifikasi', 'berkas_valid', 'siap_tes', 'diterima']);

        if ($tab === 'saya') {
            $query->where('pewawancara_id', $user->id);
        }

        if ($jurusanId) {
            $query->where(function ($q) use ($jurusanId) {
                $q->where('jurusan_id_1', $jurusanId)
                  ->orWhere('jurusan_id_2', $jurusanId);
            });
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$cari}%")
                  ->orWhere('nisn', 'like', "%{$cari}%");
            });
        }

        // Urutkan yang belum diuji di atas, lalu berdasarkan nomor pendaftaran
        $peserta = $query->orderByRaw('CASE WHEN nilai_wawancara_total IS NULL THEN 0 ELSE 1 END')
            ->orderBy('no_pendaftaran', 'asc')
            ->paginate(20)
            ->withQueryString();

        $ishiharaPlates = PpdbAdminController::getDaftarPlateIshihara();
        $mw = $setting ? $setting->materi_wawancara_aktif : PpdbUjianSetting::getDefaultMateriWawancara();

        return view('ppdb.guru.wawancara', compact(
            'user',
            'setting',
            'jurusans',
            'peserta',
            'tab',
            'cari',
            'jurusanId',
            'totalDitugaskan',
            'sudahDiuji',
            'belumDiuji',
            'ishiharaPlates',
            'mw'
        ));
    }

    /**
     * Simpan Hasil Penilaian Wawancara oleh Guru Penguji
     */
    public function simpanNilai(Request $request, $id)
    {
        $user = $this->authorizeGuru();

        $pendaftar = PpdbPendaftar::findOrFail($id);

        // Validasi penugasan jika bukan panitia PPDB
        if (!$user->canAccessPpdb() && (int)$pendaftar->pewawancara_id !== (int)$user->id) {
            abort(403, 'Akses ditolak. Anda hanya berhak menilai calon siswa yang ditugaskan kepada Anda.');
        }

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

        return redirect()->route('admin.ppdb.wawancara', request()->only(['tab', 'jurusan_id', 'cari']))
            ->with('success', "Penilaian wawancara untuk {$pendaftar->nama_lengkap} berhasil disimpan! Skor: {$pendaftar->nilai_wawancara_total}.");
    }

    /**
     * Cetak Lembar Hasil / Format Wawancara (A4)
     */
    public function cetak($id = null)
    {
        $user = $this->authorizeGuru();

        $pendaftar = null;
        if ($id) {
            $pendaftar = PpdbPendaftar::with(['jurusan1', 'jurusan2', 'pewawancara'])->findOrFail($id);
            if (!$user->canAccessPpdb() && (int)$pendaftar->pewawancara_id !== (int)$user->id) {
                abort(403, 'Akses ditolak. Anda hanya berhak mencetak berkas wawancara calon siswa yang ditugaskan kepada Anda.');
            }
        }

        $jurusans = Jurusan::all();
        $setting = PpdbUjianSetting::getAktif();
        $sekolah = \App\Models\PengaturanSekolah::getAktif();

        return view('ppdb.admin.cetak_instrumen_wawancara', compact(
            'pendaftar',
            'jurusans',
            'setting',
            'sekolah'
        ));
    }

    /**
     * Tampilkan Lembar Piringan Tes Buta Warna Ishihara untuk Pewawancara
     */
    public function tesButaWarna()
    {
        $this->authorizeGuru();
        $plates = PpdbAdminController::getDaftarPlateIshihara();
        $sekolah = \App\Models\PengaturanSekolah::getAktif();
        return view('ppdb.admin.tes_buta_warna', compact('plates', 'sekolah'));
    }
}
