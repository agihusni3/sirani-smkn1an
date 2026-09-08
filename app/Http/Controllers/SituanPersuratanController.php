<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BukuSkKepsek;
use App\Models\DisposisiSurat;
use App\Models\KlasifikasiSurat;
use App\Models\PengaturanSekolah;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Models\Guru;
use App\Models\ArsipDokumenPtk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SituanPersuratanController extends Controller
{
    /**
     * Tampilkan Buku Agenda Surat Masuk.
     */
    public function suratMasukIndex(Request $request)
    {
        $user = auth()->user();
        $query = SuratMasuk::with(['disposisis.penerima', 'creator'])->latest('tanggal_diterima');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nomor_surat_asal', 'like', "%{$q}%")
                  ->orWhere('pengirim', 'like', "%{$q}%")
                  ->orWhere('perihal', 'like', "%{$q}%")
                  ->orWhere('nomor_agenda', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_disposisi', $request->status);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_agenda', $request->tahun);
        }

        $suratMasuks = $query->paginate(15)->withQueryString();

        // Statistik Persuratan Masuk
        $thisYear = (int) date('Y');
        $totalTahunIni = SuratMasuk::where('tahun_agenda', $thisYear)->count();
        $menungguDisposisi = SuratMasuk::where('status_disposisi', 'menunggu')->count();
        $didisposisi = SuratMasuk::where('status_disposisi', 'didisposisi')->count();
        $selesaiTindakLanjut = SuratMasuk::where('status_disposisi', 'selesai')->count();

        // Daftar pengguna untuk pilihan target disposisi (Pimpinan, Waka, Guru, Staf TU)
        $pejabatTujuan = User::whereIn('role', [
            'kepala_sekolah', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_hubin',
            'kaprog', 'kepala_bengkel', 'guru_bk', 'guru_piket', 'staf_tu', 'guru'
        ])->orderBy('name')->get(['id', 'name', 'role']);

        $nextAgenda = SuratMasuk::nextNomorAgenda($thisYear);

        return view('situan.persuratan.masuk_index', compact(
            'suratMasuks',
            'totalTahunIni',
            'menungguDisposisi',
            'didisposisi',
            'selesaiTindakLanjut',
            'pejabatTujuan',
            'nextAgenda',
            'thisYear',
            'user'
        ));
    }

    /**
     * Catat Surat Masuk Baru ke Buku Agenda.
     */
    public function suratMasukStore(Request $request)
    {
        $request->validate([
            'nomor_surat_asal' => 'required|string|max:100',
            'pengirim'         => 'required|string|max:150',
            'tanggal_surat'    => 'required|date',
            'tanggal_diterima' => 'required|date',
            'perihal'          => 'required|string|max:255',
            'tingkat_urgensi'  => 'required|in:biasa,penting,segera,rahasia',
            'file_lampiran'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Maks 10MB
        ]);

        $tglDiterima = Carbon::parse($request->tanggal_diterima);
        $tahun = (int) $tglDiterima->format('Y');
        $nextAgenda = SuratMasuk::nextNomorAgenda($tahun);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $fileName = 'surat_masuk_' . $tahun . '_' . $nextAgenda . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('arsip_surat/masuk', $fileName, 'public');
        }

        $surat = SuratMasuk::create([
            'nomor_agenda'     => $nextAgenda,
            'tahun_agenda'     => $tahun,
            'nomor_surat_asal' => $request->nomor_surat_asal,
            'pengirim'         => $request->pengirim,
            'tanggal_surat'    => $request->tanggal_surat,
            'tanggal_diterima' => $request->tanggal_diterima,
            'perihal'          => $request->perihal,
            'tingkat_urgensi'  => $request->tingkat_urgensi,
            'file_lampiran'    => $filePath,
            'status_disposisi' => 'menunggu',
            'created_by'       => auth()->id(),
        ]);

        AuditLog::catat('create', 'situan_surat', "Mencatat Agenda Surat Masuk No. {$nextAgenda}/{$tahun} dari {$surat->pengirim}");

        return redirect()->route('situan.surat-masuk.index')
            ->with('success', "Surat Masuk berhasil dicatat dalam Agenda No. {$nextAgenda}/{$tahun}.");
    }

    /**
     * Proses Disposisi Elektronik oleh Kepala Sekolah.
     */
    public function suratMasukDisposisi(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || (!$user->isKepalaSekolah() && !$user->isAdmin())) {
            abort(403, 'Hanya Kepala Sekolah atau Administrator yang berwenang memberikan disposisi resmi.');
        }

        $surat = SuratMasuk::findOrFail($id);

        $request->validate([
            'penerima_user_id' => 'required|exists:users,id',
            'instruksi_flags'  => 'nullable|array',
            'catatan_kepsek'   => 'nullable|string',
            'batas_waktu'      => 'nullable|date',
        ]);

        $disposisi = DisposisiSurat::create([
            'surat_masuk_id'   => $surat->id,
            'pemberi_user_id'  => $user->id,
            'penerima_user_id' => $request->penerima_user_id,
            'instruksi_flags'  => $request->instruksi_flags ?? ['tindak_lanjuti'],
            'catatan_kepsek'   => $request->catatan_kepsek,
            'batas_waktu'      => $request->batas_waktu,
            'is_read'          => false,
            'is_selesai'       => false,
        ]);

        $surat->update(['status_disposisi' => 'didisposisi']);

        $penerima = User::find($request->penerima_user_id);
        AuditLog::catat('update', 'situan_disposisi', "Memberikan Disposisi Surat No. Agenda {$surat->nomor_agenda} kepada {$penerima?->name}");

        return back()->with('success', "Disposisi elektronik berhasil diteruskan kepada {$penerima?->name}.");
    }

    /**
     * Cetak Lembar Disposisi Format Resmi Kedinasan (Ukuran Standar A5).
     */
    public function disposisiCetakLembar($id)
    {
        $surat = SuratMasuk::with(['disposisis.penerima', 'disposisis.pemberi'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();
        return view('situan.persuratan.cetak_lembar_disposisi', compact('surat', 'sekolah'));
    }

    /**
     * Tampilkan Buku Agenda Surat Keluar & Generator Nomor Otomatis.
     */
    public function suratKeluarIndex(Request $request)
    {
        $user = auth()->user();
        $query = SuratKeluar::with(['klasifikasi', 'creator'])->latest('tanggal_surat');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nomor_surat_lengkap', 'like', "%{$q}%")
                  ->orWhere('tujuan_surat', 'like', "%{$q}%")
                  ->orWhere('perihal', 'like', "%{$q}%")
                  ->orWhere('kode_klasifikasi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('klasifikasi')) {
            $query->where('kode_klasifikasi', $request->klasifikasi);
        }

        $suratKeluars = $query->paginate(15)->withQueryString();

        $klasifikasis = KlasifikasiSurat::where('is_active', true)->orderBy('kode')->get();

        $thisYear = (int) date('Y');
        $totalKeluarTahunIni = SuratKeluar::where('tahun_agenda', $thisYear)->count();

        // Preview nomor berikutnya jika memilih kode klasifikasi default 421.3
        $previewNomor = SuratKeluar::generateNomorSurat('421.3');

        return view('situan.persuratan.keluar_index', compact(
            'suratKeluars',
            'klasifikasis',
            'totalKeluarTahunIni',
            'previewNomor',
            'thisYear',
            'user'
        ));
    }

    /**
     * Catat / Terbitkan Nomor Surat Keluar Baru.
     */
    public function suratKeluarStore(Request $request)
    {
        $request->validate([
            'kode_klasifikasi' => 'required|string|max:30',
            'tujuan_surat'     => 'required|string|max:200',
            'perihal'          => 'required|string|max:255',
            'tanggal_surat'    => 'required|date',
            'penandatangan'    => 'required|string|max:150',
            'jenis_surat'      => 'required|in:umum,suket_siswa,surat_tugas,rekomendasi_mutasi,sk_kepsek,lainnya',
            'file_arsip'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $klasifikasi = KlasifikasiSurat::where('kode', $request->kode_klasifikasi)->first();
        $tanggal = Carbon::parse($request->tanggal_surat);

        // Generate nomor surat berstandar
        $generator = SuratKeluar::generateNomorSurat($request->kode_klasifikasi, $tanggal);

        $filePath = null;
        if ($request->hasFile('file_arsip')) {
            $file = $request->file('file_arsip');
            $fileName = 'surat_keluar_' . $generator['tahun_agenda'] . '_' . $generator['nomor_agenda'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('arsip_surat/keluar', $fileName, 'public');
        }

        $surat = SuratKeluar::create([
            'nomor_agenda'        => $generator['nomor_agenda'],
            'tahun_agenda'        => $generator['tahun_agenda'],
            'klasifikasi_id'      => $klasifikasi?->id,
            'kode_klasifikasi'    => $request->kode_klasifikasi,
            'nomor_surat_lengkap' => $generator['nomor_surat_lengkap'],
            'tujuan_surat'        => $request->tujuan_surat,
            'perihal'             => $request->perihal,
            'tanggal_surat'       => $request->tanggal_surat,
            'penandatangan'       => $request->penandatangan,
            'jenis_surat'         => $request->jenis_surat,
            'file_arsip'          => $filePath,
            'created_by'          => auth()->id(),
        ]);

        AuditLog::catat('create', 'situan_surat_keluar', "Menerbitkan Nomor Surat Keluar: {$surat->nomor_surat_lengkap} ke {$surat->tujuan_surat}");

        return redirect()->route('situan.surat-keluar.index')
            ->with('success', "Nomor Surat Keluar Resmi berhasil diterbitkan: {$surat->nomor_surat_lengkap}");
    }

    /**
     * Tampilkan Buku Register SK Kepala Sekolah.
     */
    public function bukuSkIndex(Request $request)
    {
        $thisYear = (int) date('Y');
        $query = BukuSkKepsek::withCount('distribusiPtks')->latest('tanggal_ditetapkan');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nomor_sk_lengkap', 'like', "%{$q}%")
                  ->orWhere('tentang_sk', 'like', "%{$q}%")
                  ->orWhere('kategori_sk', 'like', "%{$q}%");
            });
        }

        $bukuSks = $query->paginate(15)->withQueryString();
        $totalSk = BukuSkKepsek::where('tahun_sk', $thisYear)->count();
        $nextUrut = BukuSkKepsek::nextNomorUrut($thisYear);
        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get(['id', 'nama', 'nip', 'jabatan']);

        return view('situan.persuratan.sk_index', compact('bukuSks', 'totalSk', 'nextUrut', 'thisYear', 'gurus'));
    }

    /**
     * Catat SK Kepala Sekolah Baru & Distribusikan Otomatis ke E-Kabinet PTK.
     */
    public function bukuSkStore(Request $request)
    {
        $request->validate([
            'tentang_sk'         => 'required|string|max:255',
            'tanggal_ditetapkan' => 'required|date',
            'kategori_sk'        => 'required|string|max:80',
            'file_dokumen'       => 'nullable|file|mimes:pdf|max:15360',
            'distribusi_target'  => 'nullable|in:semua_guru,pilih_guru,tidak_distribusi',
            'guru_ids'           => 'nullable|array',
            'guru_ids.*'         => 'exists:gurus,id',
        ]);

        $tgl = Carbon::parse($request->tanggal_ditetapkan);
        $tahun = (int) $tgl->format('Y');
        $nextUrut = BukuSkKepsek::nextNomorUrut($tahun);
        $romawi = SuratKeluar::romawiBulan((int) $tgl->format('n'));

        // Format standar SK Kepala Sekolah: 421.3/SK.XXX/SMKN1AN/[Bulan]/[Tahun]
        $padUrut = str_pad((string) $nextUrut, 3, '0', STR_PAD_LEFT);
        $nomorSkLengkap = "421.3/SK.{$padUrut}/SMKN1AN/{$romawi}/{$tahun}";

        $filePath = null;
        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $fileName = 'sk_kepsek_' . $tahun . '_' . $padUrut . '_' . time() . '.pdf';
            $filePath = $file->storeAs('arsip_surat/sk', $fileName, 'public');
        }

        $sk = BukuSkKepsek::create([
            'nomor_urut_sk'      => $nextUrut,
            'tahun_sk'           => $tahun,
            'nomor_sk_lengkap'   => $nomorSkLengkap,
            'tentang_sk'         => $request->tentang_sk,
            'tanggal_ditetapkan' => $request->tanggal_ditetapkan,
            'kategori_sk'        => $request->kategori_sk,
            'file_dokumen'       => $filePath,
            'is_active'          => true,
        ]);

        // Distribusi Otomatis ke E-Kabinet Guru Penerima (Smart One-to-Many Linking)
        $targetGuruIds = [];
        $distribusi = $request->distribusi_target ?? 'tidak_distribusi';

        if ($filePath && $distribusi === 'semua_guru') {
            $targetGuruIds = Guru::where('status', 'aktif')->pluck('id')->toArray();
        } elseif ($filePath && $distribusi === 'pilih_guru' && !empty($request->guru_ids)) {
            $targetGuruIds = $request->guru_ids;
        }

        if (!empty($targetGuruIds)) {
            foreach ($targetGuruIds as $gId) {
                ArsipDokumenPtk::create([
                    'guru_id'         => $gId,
                    'buku_sk_id'      => $sk->id,
                    'kategori_berkas' => 'sk_penugasan_sekolah',
                    'nama_dokumen'    => '[SK Kolektif] ' . $sk->tentang_sk,
                    'nomor_dokumen'   => $nomorSkLengkap,
                    'tanggal_dokumen' => $request->tanggal_ditetapkan,
                    'file_path'       => $filePath,
                ]);
            }
        }

        $distribusiInfo = count($targetGuruIds) > 0 ? " dan otomatis didistribusikan ke " . count($targetGuruIds) . " guru." : ".";
        AuditLog::catat('create', 'situan_sk', "Menerbitkan SK Kepala Sekolah No: {$nomorSkLengkap} tentang {$sk->tentang_sk}{$distribusiInfo}");

        return redirect()->route('situan.buku-sk.index')
            ->with('success', "SK Kepala Sekolah berhasil didaftarkan: {$nomorSkLengkap}{$distribusiInfo}");
    }
}
