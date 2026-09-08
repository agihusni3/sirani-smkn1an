<?php

namespace App\Http\Controllers;

use App\Models\ArsipDokumenPtk;
use App\Models\ArsipSekolah;
use App\Models\AuditLog;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SituanEKabinetController extends Controller
{
    /**
     * Tampilkan Dasbor Lemari Berkas Digital Terpusat (E-Kabinet SITUAN).
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'ptk'); // 'ptk', 'lembaga', 'kelengkapan'

        // 1. Metrik Ringkasan Kabinet
        $totalArsipPtk = ArsipDokumenPtk::count();
        $totalGuruWithArsip = ArsipDokumenPtk::distinct('guru_id')->count('guru_id');
        $totalArsipSekolah = ArsipSekolah::count();
        $totalMouAktif = ArsipSekolah::where('kategori_arsip', 'mou_industri')
            ->where(function ($q) {
                $q->whereNull('tanggal_berakhir')->orWhere('tanggal_berakhir', '>=', now()->toDateString());
            })->count();

        // 2. Daftar Guru Aktif untuk Dropdown Filter & Upload
        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip', 'jabatan', 'golongan_ruang']);

        // 3. Query Arsip PTK (Tab 1)
        $arsipPtkQuery = ArsipDokumenPtk::with('guru')->latest();

        if ($request->filled('guru_id')) {
            $arsipPtkQuery->where('guru_id', $request->guru_id);
        }

        if ($request->filled('kategori_ptk')) {
            $arsipPtkQuery->where('kategori_berkas', $request->kategori_ptk);
        }

        if ($request->filled('q_ptk')) {
            $q = $request->q_ptk;
            $arsipPtkQuery->where(function ($query) use ($q) {
                $query->where('nama_dokumen', 'like', "%{$q}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$q}%")
                    ->orWhereHas('guru', function ($gQuery) use ($q) {
                        $gQuery->where('nama', 'like', "%{$q}%")
                            ->orWhere('nip', 'like', "%{$q}%");
                    });
            });
        }

        $arsipPtks = $arsipPtkQuery->paginate(15, ['*'], 'page_ptk')->withQueryString();

        // 4. Query Arsip Lembaga & MoU Industri (Tab 2)
        $arsipLembagaQuery = ArsipSekolah::with('pengunggah')->latest();

        if ($request->filled('kategori_lembaga')) {
            $arsipLembagaQuery->where('kategori_arsip', $request->kategori_lembaga);
        }

        if ($request->filled('q_lembaga')) {
            $qLembaga = $request->q_lembaga;
            $arsipLembagaQuery->where(function ($query) use ($qLembaga) {
                $query->where('nama_arsip', 'like', "%{$qLembaga}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$qLembaga}%")
                    ->orWhere('mitra_instansi', 'like', "%{$qLembaga}%");
            });
        }

        $arsipLembagas = $arsipLembagaQuery->paginate(15, ['*'], 'page_lembaga')->withQueryString();

        // 5. Radar Kelengkapan Berkas PTK (Tab 3)
        $radarKelengkapan = Guru::where('status', 'aktif')
            ->with(['arsipDokumens' => function ($q) {
                $q->select('id', 'guru_id', 'kategori_berkas');
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip', 'jabatan', 'golongan_ruang']);

        return view('situan.ekabinet.index', compact(
            'activeTab',
            'totalArsipPtk',
            'totalGuruWithArsip',
            'totalArsipSekolah',
            'totalMouAktif',
            'gurus',
            'arsipPtks',
            'arsipLembagas',
            'radarKelengkapan'
        ));
    }

    /**
     * Unggah Dokumen Berkas PTK Langsung dari E-Kabinet.
     */
    public function storePtk(Request $request)
    {
        $request->validate([
            'guru_id'         => 'required|exists:gurus,id',
            'kategori_berkas' => 'required|in:ktp,kk,sk_cpns,sk_pns,sk_pppk,sk_pangkat_terakhir,sk_kgb_terakhir,ijazah,transkrip,sertifikat_pendidik,kartu_pegawai,lainnya',
            'nama_dokumen'    => 'required|string|max:150',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'file_dokumen'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $guru = Guru::findOrFail($request->guru_id);
        $file = $request->file('file_dokumen');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'ptk_' . $guru->id . '_' . $request->kategori_berkas . '_' . time() . '.' . $extension;
        $filePath = $file->storeAs('arsip_ptk/' . $guru->id, $fileName, 'public');

        ArsipDokumenPtk::create([
            'guru_id'         => $guru->id,
            'kategori_berkas' => $request->kategori_berkas,
            'nama_dokumen'    => $request->nama_dokumen,
            'nomor_dokumen'   => $request->nomor_dokumen,
            'tanggal_dokumen' => $request->tanggal_dokumen,
            'file_path'       => $filePath,
        ]);

        AuditLog::catat('create', 'situan_ekabinet_ptk', "Mengunggah berkas {$request->nama_dokumen} untuk {$guru->nama} via E-Kabinet");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'ptk'])
            ->with('success', "Berkas digital untuk {$guru->nama} berhasil diarsipkan ke E-Kabinet.");
    }

    /**
     * Hapus Dokumen Berkas PTK dari E-Kabinet.
     */
    public function destroyPtk($id)
    {
        $arsip = ArsipDokumenPtk::findOrFail($id);
        $namaDok = $arsip->nama_dokumen;
        $guruNama = $arsip->guru?->nama ?? 'PTK';

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $arsip->delete();

        AuditLog::catat('delete', 'situan_ekabinet_ptk', "Menghapus berkas digital {$namaDok} milik {$guruNama}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'ptk'])
            ->with('success', "Dokumen {$namaDok} berhasil dihapus dari E-Kabinet.");
    }

    /**
     * Unggah Dokumen Arsip Lembaga / MoU Kemitraan Sekolah.
     */
    public function storeLembaga(Request $request)
    {
        $request->validate([
            'kategori_arsip'   => 'required|in:akreditasi,izin_operasional,mou_industri,sertifikat_aset,kurikulum_kosp,pedoman_sop,sk_kelembagaan,lainnya',
            'nama_arsip'       => 'required|string|max:200',
            'nomor_dokumen'    => 'nullable|string|max:100',
            'mitra_instansi'   => 'nullable|string|max:150',
            'tanggal_dokumen'  => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'file_dokumen'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'keterangan'       => 'nullable|string|max:500',
        ]);

        $file = $request->file('file_dokumen');
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $fileName = 'sekolah_' . $request->kategori_arsip . '_' . time() . '.' . $extension;
        $filePath = $file->storeAs('arsip_sekolah/' . $request->kategori_arsip, $fileName, 'public');

        ArsipSekolah::create([
            'kategori_arsip'        => $request->kategori_arsip,
            'nama_arsip'            => $request->nama_arsip,
            'nomor_dokumen'         => $request->nomor_dokumen,
            'mitra_instansi'        => $request->mitra_instansi,
            'tanggal_dokumen'       => $request->tanggal_dokumen,
            'tanggal_berakhir'      => $request->tanggal_berakhir,
            'file_path'             => $filePath,
            'file_type'             => $extension,
            'file_size'             => $fileSize,
            'keterangan'            => $request->keterangan,
            'diunggah_oleh_user_id' => auth()->id(),
        ]);

        AuditLog::catat('create', 'situan_ekabinet_lembaga', "Mengarsipkan dokumen lembaga: {$request->nama_arsip}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'lembaga'])
            ->with('success', "Dokumen lembaga {$request->nama_arsip} berhasil disimpan ke E-Kabinet.");
    }

    /**
     * Hapus Dokumen Arsip Lembaga / MoU.
     */
    public function destroyLembaga($id)
    {
        $arsip = ArsipSekolah::findOrFail($id);
        $namaArsip = $arsip->nama_arsip;

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $arsip->delete();

        AuditLog::catat('delete', 'situan_ekabinet_lembaga', "Menghapus dokumen arsip lembaga: {$namaArsip}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'lembaga'])
            ->with('success', "Dokumen {$namaArsip} berhasil dihapus dari E-Kabinet.");
    }
}
