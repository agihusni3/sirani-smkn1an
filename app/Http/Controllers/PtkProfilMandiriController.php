<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\ArsipDokumenPtk;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PtkProfilMandiriController extends Controller
{
    /**
     * Tampilkan Portal Mandiri Biodata & Lemari Berkas Digital PTK.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $guru = $user->guru;

        // Jika login sebagai Admin dan akun belum ditautkan ke profil Guru,
        // izinkan admin melihat profil guru tertentu via parameter ?guru_id= atau guru pertama sebagai simulasi
        if (!$guru && $user->isAdmin()) {
            $guruIdParam = $request->get('guru_id');
            if ($guruIdParam) {
                $guru = Guru::with('arsipDokumens')->find($guruIdParam);
            } else {
                $guru = Guru::with('arsipDokumens')->first();
            }
        }

        if (!$guru) {
            return redirect()->route('sirani.index')
                ->with('error', 'Akun Anda belum ditautkan dengan data Guru/PTK. Silakan hubungi staf Tata Usaha.');
        }

        // 1. Dokumen Arsip Digital Pribadi
        $arsips = $guru->arsipDokumens()->latest()->get();

        // 2. Rekapitulasi Presensi Bulan Berjalan
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth   = Carbon::now()->endOfMonth()->toDateString();

        $absensisBulanIni = Absensi::where('pemilik_type', 'guru')
            ->where('pemilik_id', $guru->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->orderBy('tanggal', 'desc')
            ->get();

        $hadir     = $absensisBulanIni->where('status', 'hadir')->count();
        $terlambat = $absensisBulanIni->where('status', 'terlambat')->count();
        $izin      = $absensisBulanIni->whereIn('status', ['izin', 'sakit', 'dispen'])->count();
        $alpha     = $absensisBulanIni->where('status', 'alpha')->count();
        $totalHari = $hadir + $terlambat + $izin + $alpha;
        $persenKehadiran = $totalHari > 0 ? round((($hadir + $terlambat) / $totalHari) * 100, 1) : 100;

        $stats = [
            'hadir'           => $hadir,
            'terlambat'       => $terlambat,
            'izin'            => $izin,
            'alpha'           => $alpha,
            'total_hari'      => $totalHari,
            'persen'          => $persenKehadiran,
            'nama_bulan'      => Carbon::now()->translatedFormat('F Y'),
        ];

        $sekolah = PengaturanSekolah::first();
        $semuaGuru = $user->isAdmin() ? Guru::orderBy('nama')->get() : collect([]);

        return view('ptk.profil_saya', compact('guru', 'arsips', 'stats', 'absensisBulanIni', 'sekolah', 'semuaGuru'));
    }

    /**
     * Unggah Berkas Digital Mandiri oleh PTK ke E-Arsip.
     */
    public function unggahBerkasMandiri(Request $request)
    {
        $user = auth()->user();
        $guru = $user->guru;

        if (!$guru && $user->isAdmin() && $request->has('guru_id')) {
            $guru = Guru::findOrFail($request->guru_id);
        }

        if (!$guru) {
            return back()->with('error', 'Akses ditolak: Akun Anda tidak terhubung ke profil PTK.');
        }

        $request->validate([
            'kategori_berkas' => 'required|in:ktp,kk,sk_cpns,sk_pns,sk_pppk,sk_pangkat_terakhir,sk_kgb_terakhir,ijazah,transkrip,sertifikat_pendidik,kartu_pegawai,lainnya',
            'nama_dokumen'    => 'required|string|max:150',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'file_dokumen'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'file_dokumen.required' => 'Wajib memilih berkas dokumen untuk diunggah.',
            'file_dokumen.mimes'    => 'Format berkas harus PDF, JPG, JPEG, atau PNG.',
            'file_dokumen.max'      => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $file = $request->file('file_dokumen');
        $fileName = 'ptk_' . $guru->id . '_' . $request->kategori_berkas . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('arsip_ptk/' . $guru->id, $fileName, 'public');

        $arsip = ArsipDokumenPtk::create([
            'guru_id'         => $guru->id,
            'kategori_berkas' => $request->kategori_berkas,
            'nama_dokumen'    => $request->nama_dokumen,
            'nomor_dokumen'   => $request->nomor_dokumen,
            'tanggal_dokumen' => $request->tanggal_dokumen,
            'file_path'       => $filePath,
        ]);

        AuditLog::catat('create', 'arsip_ptk_mandiri', "PTK {$guru->nama} mengunggah berkas mandiri: {$request->nama_dokumen}");

        return back()->with('success', "Berkas '{$request->nama_dokumen}' berhasil diunggah ke lemari berkas digital Anda.");
    }

    /**
     * Hapus Dokumen Arsip Pribadi (hanya dokumen milik sendiri atau admin).
     */
    public function hapusBerkasMandiri($id)
    {
        $user = auth()->user();
        $arsip = ArsipDokumenPtk::findOrFail($id);

        // Otorisasi: Pastikan berkas benar milik guru yang sedang login, atau user adalah Admin
        if (!$user->isAdmin() && $user->guru_id !== $arsip->guru_id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus dokumen ini.');
        }

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $namaDok = $arsip->nama_dokumen;
        $arsip->delete();

        AuditLog::catat('delete', 'arsip_ptk_mandiri', "Menghapus berkas digital: {$namaDok}");

        return back()->with('success', "Berkas '{$namaDok}' berhasil dihapus dari lemari berkas.");
    }
}
