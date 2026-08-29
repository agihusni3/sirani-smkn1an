<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\IzinSiswa;
use App\Models\IzinGuru;
use App\Models\Siswa;
use App\Services\IzinSiswaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class IzinSiswaController extends Controller
{
    protected IzinSiswaService $izinService;

    public function __construct(IzinSiswaService $izinService)
    {
        $this->izinService = $izinService;
    }

    public function index()
    {
        $taAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
        $siswas = Siswa::where('status', 'aktif')
            ->with([
                'siswaRombels' => function ($q) use ($taAktif) {
                    if ($taAktif) {
                        $q->where('tahun_ajaran_id', $taAktif->id)->where('status_keanggotaan', 'aktif')->with('rombel');
                    }
                }
            ])
            ->orderBy('nama')
            ->get();

        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $izins = IzinSiswa::with('siswa')->orderBy('tanggal', 'desc')->paginate(10, ['*'], 'page_siswa')->withQueryString();
        $izinGurus = IzinGuru::with('guru')->orderBy('tanggal', 'desc')->paginate(10, ['*'], 'page_guru')->withQueryString();

        return view('izin_siswa', compact('siswas', 'gurus', 'izins', 'izinGurus'));
    }

    public function store(Request $request)
    {
        $kategori = $request->input('kategori', 'siswa');

        if ($kategori === 'guru') {
            $request->validate([
                'guru_id'        => 'required|exists:gurus,id',
                'tanggal'        => 'required|date',
                'jenis'          => 'required|in:sakit,izin,dinas_luar,pulang_cepat,cuti',
                'keterangan'     => 'nullable|string',
                'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            ]);

            try {
                $filePath = null;
                if ($request->hasFile('file_pendukung')) {
                    $filePath = $request->file('file_pendukung')->store('surat_izin/guru', 'public');
                }

                $this->izinService->ajukanIzinGuru(
                    (int) $request->input('guru_id'),
                    $request->input('tanggal'),
                    $request->input('jenis'),
                    $request->input('keterangan'),
                    $filePath
                );

                return redirect()->back()->with('success', 'Perizinan guru berhasil dicatat dan absensi diperbarui.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        // Kategori Siswa
        $request->validate([
            'siswa_id'       => 'required|exists:siswas,id',
            'tanggal'        => 'required|date',
            'jenis'          => 'required|in:sakit,izin,dispensasi,pulang_cepat,pulang_awal',
            'keterangan'     => 'nullable|string',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $filePath = $request->file('file_pendukung')->store('surat_izin/siswa', 'public');
            }

            $this->izinService->ajukanIzin(
                (int) $request->input('siswa_id'),
                $request->input('tanggal'),
                $request->input('jenis'),
                $request->input('keterangan'),
                $filePath
            );

            return redirect()->back()->with('success', 'Perizinan siswa berhasil dicatat dan absensi diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $izin = IzinSiswa::findOrFail($id);
        if ($izin->file_pendukung && Storage::disk('public')->exists($izin->file_pendukung)) {
            Storage::disk('public')->delete($izin->file_pendukung);
        }
        $izin->delete();

        return redirect()->back()->with('success', 'Catatan perizinan siswa berhasil dihapus.');
    }

    public function destroyGuru($id)
    {
        $izin = IzinGuru::findOrFail($id);
        if ($izin->file_pendukung && Storage::disk('public')->exists($izin->file_pendukung)) {
            Storage::disk('public')->delete($izin->file_pendukung);
        }
        $izin->delete();

        return redirect()->back()->with('success', 'Catatan perizinan guru berhasil dihapus.');
    }
}
