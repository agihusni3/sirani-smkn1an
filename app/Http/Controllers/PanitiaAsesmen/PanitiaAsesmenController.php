<?php

namespace App\Http\Controllers\PanitiaAsesmen;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikAsesmenPanitia;
use App\Models\AkademikAsesmenPeriode;
use App\Models\AkademikMataPelajaran;
use App\Models\AuditLog;
use App\Models\PengaturanSekolah;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PanitiaAsesmenController extends Controller
{
    /**
     * Dapatkan event asesmen aktif saat ini atau yang dipilih.
     */
    protected function getActivePeriode($periodeId = null)
    {
        if ($periodeId) {
            return AkademikAsesmenPeriode::with(['panitias.guru', 'tahunAjaran'])->findOrFail($periodeId);
        }

        return AkademikAsesmenPeriode::with(['panitias.guru', 'tahunAjaran'])
            ->where('status', 'aktif')
            ->latest('tanggal_mulai')
            ->first()
            ?? AkademikAsesmenPeriode::with(['panitias.guru', 'tahunAjaran'])
                ->latest('tanggal_mulai')
                ->first();
    }

    public function dashboard(Request $request)
    {
        $periode = $this->getActivePeriode($request->periode_id);
        $periodes = AkademikAsesmenPeriode::orderByDesc('tanggal_mulai')->get();
        $user = auth()->user();

        // Posisi panitia user saat ini
        $myPanitia = null;
        if ($periode && $user->guru_id) {
            $myPanitia = $periode->panitias->where('guru_id', $user->guru_id)->first();
        }

        // Statistik Ujian
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalMapel = AkademikMataPelajaran::count();
        $totalUjian = AkademikAsesmenOnline::whereIn('jenis', ['pts', 'pas', 'tugas'])->count();
        $ujianAktif = AkademikAsesmenOnline::where('is_active', true)->count();
        $totalHasil = AkademikAsesmenHasil::count();

        // Soal asesmen terbaru
        $recentAsesmens = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.guru'])
            ->latest()
            ->take(5)
            ->get();

        return view('panitia_asesmen.dashboard', compact(
            'periode',
            'periodes',
            'myPanitia',
            'totalSiswa',
            'totalMapel',
            'totalUjian',
            'ujianAktif',
            'totalHasil',
            'recentAsesmens'
        ));
    }

    public function controlRoom(Request $request)
    {
        $periode = $this->getActivePeriode($request->periode_id);
        $asesmens = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.guru', 'distribusi.rombel'])
            ->withCount(['soals', 'hasils'])
            ->latest()
            ->paginate(15);

        // Token harian dari cache / session (default 6 huruf unik acak)
        $currentToken = cache()->get('cbt_active_token', 'SMK1AN');

        return view('panitia_asesmen.control_room', compact('periode', 'asesmens', 'currentToken'));
    }

    public function generateToken(Request $request)
    {
        $newToken = strtoupper(Str::random(6));
        cache()->put('cbt_active_token', $newToken, now()->addHours(6));

        AuditLog::catat('generate_token_cbt', 'panitia_asesmen', "Merilis token ujian CBT baru: {$newToken}");

        return redirect()->back()->with('success', "Token Ujian CBT berhasil diperbarui: {$newToken} (Berlaku 6 Jam)");
    }

    public function toggleUjianStatus($id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $asesmen->is_active = !$asesmen->is_active;
        $asesmen->save();

        $statusText = $asesmen->is_active ? 'DIBAWAHKAN / DIBUKA' : 'DITUTUP';
        AuditLog::catat('toggle_ujian_cbt', 'panitia_asesmen', "Mengubah status ujian {$asesmen->judul} menjadi {$statusText}");

        return redirect()->back()->with('success', "Status ujian '{$asesmen->judul}' berhasil diubah: {$statusText}!");
    }

    public function resetSiswaSession(Request $request, $hasilId)
    {
        $hasil = AkademikAsesmenHasil::with('siswa')->findOrFail($hasilId);
        
        // Reset status pengerjaan jika siswa terlogout paksa
        $hasil->status = 'mengerjakan';
        $hasil->save();

        AuditLog::catat('reset_login_cbt', 'panitia_asesmen', "Reset sesi login ujian siswa: {$hasil->siswa->nama}");

        return redirect()->back()->with('success', "Sesi pengerjaan {$hasil->siswa->nama} berhasil di-reset. Siswa dapat login kembali.");
    }

    public function administrasi(Request $request)
    {
        $periode = $this->getActivePeriode($request->periode_id);
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();
        $mapels = AkademikMataPelajaran::orderBy('nama_mapel')->get();

        return view('panitia_asesmen.administrasi', compact('periode', 'rombels', 'mapels'));
    }

    public function cetakKartu(Request $request)
    {
        $periode = $this->getActivePeriode($request->periode_id);
        $rombelId = $request->rombel_id;

        $siswas = Siswa::where('status', 'aktif')
            ->when($rombelId, fn($q) => $q->where('rombel_id', $rombelId))
            ->orderBy('nama')
            ->get();

        $sekolah = PengaturanSekolah::getAktif();
        $rombel = $rombelId ? Rombel::find($rombelId) : null;

        return view('panitia_asesmen.cetak_kartu', compact('periode', 'siswas', 'sekolah', 'rombel'));
    }

    public function cetakDaftarHadir(Request $request)
    {
        $periode = $this->getActivePeriode($request->periode_id);
        $rombelId = $request->rombel_id;
        $ruang = $request->ruang ?? 'Ruang 01';
        $sesi = $request->sesi ?? 'Sesi 1 (07:30 - 09:30)';
        $mapelNama = $request->mapel_nama ?? 'Semua Mata Pelajaran';

        $siswas = Siswa::where('status', 'aktif')
            ->when($rombelId, fn($q) => $q->where('rombel_id', $rombelId))
            ->orderBy('nama')
            ->get();

        $sekolah = PengaturanSekolah::getAktif();
        $rombel = $rombelId ? Rombel::find($rombelId) : null;

        return view('panitia_asesmen.cetak_daftar_hadir', compact('periode', 'siswas', 'sekolah', 'rombel', 'ruang', 'sesi', 'mapelNama'));
    }

    public function cetakBeritaAcara(Request $request)
    {
        $periode = $this->getActivePeriode($request->periode_id);
        $ruang = $request->ruang ?? 'Ruang 01';
        $sesi = $request->sesi ?? 'Sesi 1 (07:30 - 09:30)';
        $mapelNama = $request->mapel_nama ?? 'Mata Pelajaran Asesmen';
        $sekolah = PengaturanSekolah::getAktif();

        return view('panitia_asesmen.cetak_berita_acara', compact('periode', 'sekolah', 'ruang', 'sesi', 'mapelNama'));
    }
}
