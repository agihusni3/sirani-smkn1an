<?php

namespace App\Http\Controllers;

use App\Models\ArsipDokumenPtk;
use App\Models\ArsipDokumenSiswa;
use App\Models\ArsipSekolah;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\PpdbPendaftar;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SituanEKabinetController extends Controller
{
    /**
     * Akses E-Kabinet Siswa
     */
    public function siswa(Request $request)
    {
        $request->merge(['tab' => 'siswa']);
        return $this->index($request);
    }

    /**
     * Akses E-Kabinet PTK
     */
    public function ptk(Request $request)
    {
        $request->merge(['tab' => 'ptk']);
        return $this->index($request);
    }

    /**
     * Akses E-Kabinet Lembaga
     */
    public function lembaga(Request $request)
    {
        $request->merge(['tab' => 'lembaga']);
        return $this->index($request);
    }

    /**
     * Akses E-Kabinet MoU
     */
    public function mou(Request $request)
    {
        $request->merge(['tab' => 'mou']);
        return $this->index($request);
    }

    /**
     * Tampilkan Dasbor Lemari Berkas Digital Terpusat (E-Kabinet SITUAN).
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'ptk'); // 'ptk', 'siswa', 'lembaga', 'mou', 'kelengkapan'

        // 1. Metrik Ringkasan Tiap Kabinet
        $totalArsipSiswa = ArsipDokumenSiswa::count();
        $totalSiswaWithArsip = ArsipDokumenSiswa::distinct('siswa_id')->count('siswa_id');

        $totalArsipPtk = ArsipDokumenPtk::count();
        $totalGuruWithArsip = ArsipDokumenPtk::distinct('guru_id')->count('guru_id');

        $totalArsipLembaga = ArsipSekolah::where('kategori_arsip', '!=', 'mou_industri')->count();
        $totalArsipSekolah = ArsipSekolah::count();

        $totalArsipMou = ArsipSekolah::where('kategori_arsip', 'mou_industri')->count();
        $totalMouAktif = ArsipSekolah::where('kategori_arsip', 'mou_industri')
            ->where(function ($q) {
                $q->whereNull('tanggal_berakhir')->orWhere('tanggal_berakhir', '>=', now()->toDateString());
            })->count();
        $totalMouExpired = ArsipSekolah::where('kategori_arsip', 'mou_industri')
            ->whereNotNull('tanggal_berakhir')
            ->where('tanggal_berakhir', '<', now()->toDateString())
            ->count();

        // 2. Daftar Guru Aktif untuk Dropdown Filter & Upload
        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip', 'jabatan', 'golongan_ruang']);

        // 3. Query Arsip Siswa (Tab Siswa)
        $rombels = Rombel::orderBy('nama_rombel')->get(['id', 'nama_rombel']);
        $kamusKategoriSiswa = ArsipDokumenSiswa::getKamusKategori();

        $arsipSiswaQuery = ArsipDokumenSiswa::with(['siswa.siswaRombels.rombel', 'pengunggah', 'pelayananSurat'])->latest();

        if ($request->filled('siswa_id')) {
            $arsipSiswaQuery->where('siswa_id', $request->siswa_id);
        }

        if ($request->filled('rombel_id')) {
            $rombelId = $request->rombel_id;
            $arsipSiswaQuery->whereHas('siswa.siswaRombels', function ($rQuery) use ($rombelId) {
                $rQuery->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            });
        }

        if ($request->filled('kategori_siswa')) {
            $arsipSiswaQuery->where('kategori_berkas', $request->kategori_siswa);
        }

        if ($request->filled('q_siswa')) {
            $qSiswa = $request->q_siswa;
            $arsipSiswaQuery->where(function ($query) use ($qSiswa) {
                $query->where('nama_dokumen', 'like', "%{$qSiswa}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$qSiswa}%")
                    ->orWhereHas('siswa', function ($sQuery) use ($qSiswa) {
                        $sQuery->where('nama', 'like', "%{$qSiswa}%")
                            ->orWhere('nisn', 'like', "%{$qSiswa}%");
                    });
            });
        }

        $arsipSiswas = $arsipSiswaQuery->paginate(15, ['*'], 'page_siswa')->withQueryString();

        // Cari siswa spesifik jika difilter untuk info header
        $selectedSiswa = $request->filled('siswa_id') ? Siswa::with('siswaRombels.rombel')->find($request->siswa_id) : null;

        // 4. Query Arsip PTK (Tab PTK)
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

        // 5. Query Arsip Lembaga (Tab Lembaga - Tanpa MoU Industri)
        $arsipLembagaQuery = ArsipSekolah::with('pengunggah')
            ->where('kategori_arsip', '!=', 'mou_industri')
            ->latest();

        if ($request->filled('kategori_lembaga')) {
            $arsipLembagaQuery->where('kategori_arsip', $request->kategori_lembaga);
        }

        if ($request->filled('q_lembaga')) {
            $qLembaga = $request->q_lembaga;
            $arsipLembagaQuery->where(function ($query) use ($qLembaga) {
                $query->where('nama_arsip', 'like', "%{$qLembaga}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$qLembaga}%");
            });
        }

        $arsipLembagas = $arsipLembagaQuery->paginate(15, ['*'], 'page_lembaga')->withQueryString();

        // 6. Query Arsip MoU (Tab MoU - Khusus Kerjasama & DUDI)
        $arsipMouQuery = ArsipSekolah::with('pengunggah')
            ->where('kategori_arsip', 'mou_industri')
            ->latest();

        if ($request->filled('status_mou')) {
            if ($request->status_mou === 'aktif') {
                $arsipMouQuery->where(function ($q) {
                    $q->whereNull('tanggal_berakhir')->orWhere('tanggal_berakhir', '>=', now()->toDateString());
                });
            } elseif ($request->status_mou === 'kedaluwarsa') {
                $arsipMouQuery->whereNotNull('tanggal_berakhir')
                    ->where('tanggal_berakhir', '<', now()->toDateString());
            }
        }

        if ($request->filled('q_mou')) {
            $qMou = $request->q_mou;
            $arsipMouQuery->where(function ($query) use ($qMou) {
                $query->where('nama_arsip', 'like', "%{$qMou}%")
                    ->orWhere('mitra_instansi', 'like', "%{$qMou}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$qMou}%");
            });
        }

        $arsipMous = $arsipMouQuery->paginate(15, ['*'], 'page_mou')->withQueryString();

        // 7. Radar Kelengkapan Berkas PTK (Tab Kelengkapan)
        $radarKelengkapan = Guru::where('status', 'aktif')
            ->with(['arsipDokumens' => function ($q) {
                $q->select('id', 'guru_id', 'kategori_berkas');
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip', 'jabatan', 'golongan_ruang']);

        // Data pendaftar PPDB yang dapat disinkronkan
        $ppdbReadyCount = PpdbPendaftar::whereNotNull('siswa_id')
            ->where(function ($q) {
                $q->whereNotNull('berkas_kk')
                    ->orWhereNotNull('berkas_ijazah_skl')
                    ->orWhereNotNull('berkas_akta')
                    ->orWhereNotNull('berkas_kip')
                    ->orWhereNotNull('berkas_ktp_ortu');
            })->count();

        // Daftar seluruh siswa aktif untuk modal upload berkas siswa
        $allSiswaAktif = Siswa::whereIn('status', ['aktif', 'pkl'])
            ->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel');
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'nisn']);

        return view('situan.ekabinet.index', compact(
            'activeTab',
            'totalArsipPtk',
            'totalGuruWithArsip',
            'totalArsipSiswa',
            'totalSiswaWithArsip',
            'totalArsipLembaga',
            'totalArsipSekolah',
            'totalArsipMou',
            'totalMouAktif',
            'totalMouExpired',
            'gurus',
            'rombels',
            'kamusKategoriSiswa',
            'arsipPtks',
            'arsipSiswas',
            'selectedSiswa',
            'allSiswaAktif',
            'arsipLembagas',
            'arsipMous',
            'radarKelengkapan',
            'ppdbReadyCount'
        ));
    }

    /**
     * Unggah Dokumen Berkas PTK Langsung dari E-Kabinet.
     */
    public function storePtk(Request $request)
    {
        $request->validate([
            'guru_id'         => 'required|exists:gurus,id',
            'kategori_berkas' => 'required|in:ktp,kk,sk_cpns,sk_pns,sk_pppk,sk_pangkat_terakhir,sk_kgb_terakhir,ijazah,transkrip,sertifikat_pendidik,sertifikat_pelatihan,kartu_pegawai,lainnya',
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

        // Jika kategori berkas adalah sertifikat pelatihan, sinkronkan juga ke SertifikatGuru
        if ($request->kategori_berkas === 'sertifikat_pelatihan') {
            \App\Models\SertifikatGuru::create([
                'guru_id'         => $guru->id,
                'nama_pelatihan'  => $request->nama_dokumen,
                'penyelenggara'   => 'Kementerian / Lembaga Pelatihan',
                'tahun'           => $request->tanggal_dokumen ? date('Y', strtotime($request->tanggal_dokumen)) : date('Y'),
                'file_sertifikat' => $filePath,
            ]);
        }

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

        // Jika sertifikat pelatihan, sinkronkan hapus di SertifikatGuru
        if ($arsip->kategori_berkas === 'sertifikat_pelatihan') {
            \App\Models\SertifikatGuru::where('guru_id', $arsip->guru_id)
                ->where('file_sertifikat', $arsip->file_path)
                ->delete();
        }

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $arsip->delete();

        AuditLog::catat('delete', 'situan_ekabinet_ptk', "Menghapus berkas digital {$namaDok} milik {$guruNama}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'ptk'])
            ->with('success', "Dokumen {$namaDok} berhasil dihapus dari E-Kabinet.");
    }

    /**
     * Unggah Dokumen Berkas Siswa ke E-Kabinet.
     */
    public function storeSiswa(Request $request)
    {
        $request->validate([
            'siswa_id'        => 'required|exists:siswas,id',
            'kategori_berkas' => 'required|string|max:50',
            'nama_dokumen'    => 'required|string|max:150',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'file_dokumen'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'keterangan'      => 'nullable|string|max:500',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $file = $request->file('file_dokumen');
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $fileName = 'siswa_' . $siswa->id . '_' . $request->kategori_berkas . '_' . time() . '.' . $extension;
        $filePath = $file->storeAs('arsip_siswa/' . $siswa->id, $fileName, 'public');

        ArsipDokumenSiswa::create([
            'siswa_id'        => $siswa->id,
            'kategori_berkas' => $request->kategori_berkas,
            'nama_dokumen'    => $request->nama_dokumen,
            'nomor_dokumen'   => $request->nomor_dokumen,
            'tanggal_dokumen' => $request->tanggal_dokumen,
            'file_path'       => $filePath,
            'file_size'       => $fileSize,
            'keterangan'      => $request->keterangan,
            'created_by'      => auth()->id(),
        ]);

        AuditLog::catat('create', 'situan_ekabinet_siswa', "Mengunggah berkas {$request->nama_dokumen} untuk siswa {$siswa->nama} (NISN: {$siswa->nisn}) via E-Kabinet");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $siswa->id])
            ->with('success', "Berkas digital untuk siswa {$siswa->nama} berhasil disimpan ke E-Kabinet.");
    }

    /**
     * Hapus Dokumen Berkas Siswa dari E-Kabinet.
     */
    public function destroySiswa($id)
    {
        $arsip = ArsipDokumenSiswa::findOrFail($id);
        $namaDok = $arsip->nama_dokumen;
        $siswaNama = $arsip->siswa?->nama ?? 'Siswa';
        $siswaId = $arsip->siswa_id;

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $arsip->delete();

        AuditLog::catat('delete', 'situan_ekabinet_siswa', "Menghapus berkas digital {$namaDok} milik siswa {$siswaNama}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $siswaId])
            ->with('success', "Berkas {$namaDok} berhasil dihapus dari lemari arsip siswa.");
    }

    /**
     * Sinkronisasi Otomatis Berkas Pendaftar PPDB ke E-Kabinet Siswa.
     */
    public function syncFromPpdb(Request $request)
    {
        // Temukan pendaftar PPDB yang memiliki relasi siswa_id atau match NISN
        $pendaftars = PpdbPendaftar::where(function ($q) {
            $q->whereNotNull('siswa_id')
                ->orWhereIn('status', ['diterima', 'lulus', 'aktif']);
        })->get();

        $syncedCount = 0;

        foreach ($pendaftars as $p) {
            // Cari model siswa
            $siswa = null;
            if ($p->siswa_id) {
                $siswa = Siswa::find($p->siswa_id);
            }
            if (!$siswa && $p->nisn) {
                $siswa = Siswa::where('nisn', $p->nisn)->first();
            }
            if (!$siswa && $p->nama_lengkap) {
                $siswa = Siswa::where('nama', $p->nama_lengkap)->first();
            }

            if (!$siswa) continue;

            // Mapping file PPDB ke kategori berkas
            $fieldMapping = [
                'berkas_kk'         => ['kategori' => 'kartu_keluarga', 'nama' => 'Kartu Keluarga (PPDB)'],
                'berkas_ijazah_skl' => ['kategori' => 'ijazah_smp',     'nama' => 'Ijazah / SKL SMP (PPDB)'],
                'berkas_akta'       => ['kategori' => 'akta_kelahiran', 'nama' => 'Akta Kelahiran (PPDB)'],
                'berkas_ktp_ortu'   => ['kategori' => 'ktp_kia',        'nama' => 'KTP Orang Tua / Wali (PPDB)'],
                'berkas_kip'        => ['kategori' => 'kip_pip_pkh',    'nama' => 'Kartu KIP / PIP (PPDB)'],
            ];

            foreach ($fieldMapping as $field => $cfg) {
                $path = $p->{$field};
                if (!empty($path)) {
                    // Cek apakah sudah terarsip
                    $exists = ArsipDokumenSiswa::where('siswa_id', $siswa->id)
                        ->where(function ($q) use ($path, $cfg) {
                            $q->where('file_path', $path)
                                ->orWhere('kategori_berkas', $cfg['kategori']);
                        })->exists();

                    if (!$exists) {
                        $size = null;
                        try {
                            if (Storage::disk('public')->exists($path)) {
                                $size = Storage::disk('public')->size($path);
                            }
                        } catch (\Throwable $e) {
                            $size = null;
                        }

                        ArsipDokumenSiswa::create([
                            'siswa_id'          => $siswa->id,
                            'ppdb_pendaftar_id' => $p->id,
                            'kategori_berkas'   => $cfg['kategori'],
                            'nama_dokumen'      => $cfg['nama'],
                            'nomor_dokumen'     => $p->no_pendaftaran,
                            'tanggal_dokumen'   => $p->created_at?->toDateString(),
                            'file_path'         => $path,
                            'file_size'         => $size,
                            'keterangan'        => 'Sinkronisasi berkas otomatis dari PPDB No: ' . $p->no_pendaftaran,
                            'created_by'        => auth()->id(),
                        ]);
                        $syncedCount++;
                    }
                }
            }
        }

        AuditLog::catat('sync', 'situan_ekabinet_siswa', "Menyinkronkan {$syncedCount} berkas dari modul PPDB ke E-Kabinet Siswa");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'siswa'])
            ->with('success', "Berhasil menyinkronkan {$syncedCount} berkas pendaftaran dari PPDB ke Lemari Berkas Siswa.");
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

    /**
     * Unggah Dokumen Perjanjian Kerjasama / MoU Kemitraan DUDI Industri.
     */
    public function storeMou(Request $request)
    {
        $request->validate([
            'nama_arsip'       => 'required|string|max:200',
            'mitra_instansi'   => 'required|string|max:150',
            'nomor_dokumen'    => 'nullable|string|max:100',
            'tanggal_dokumen'  => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'file_dokumen'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'keterangan'       => 'nullable|string|max:500',
        ]);

        $file = $request->file('file_dokumen');
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $fileName = 'mou_' . time() . '_' . uniqid() . '.' . $extension;
        $filePath = $file->storeAs('arsip_sekolah/mou_industri', $fileName, 'public');

        ArsipSekolah::create([
            'kategori_arsip'        => 'mou_industri',
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

        AuditLog::catat('create', 'situan_ekabinet_mou', "Mengarsipkan MoU Kerjasama: {$request->nama_arsip} dengan {$request->mitra_instansi}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'mou'])
            ->with('success', "Dokumen MoU Kerjasama dengan {$request->mitra_instansi} berhasil disimpan ke E-Kabinet MoU.");
    }

    /**
     * Hapus Dokumen MoU dari E-Kabinet MoU.
     */
    public function destroyMou($id)
    {
        $arsip = ArsipSekolah::where('kategori_arsip', 'mou_industri')->findOrFail($id);
        $namaArsip = $arsip->nama_arsip;

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $arsip->delete();

        AuditLog::catat('delete', 'situan_ekabinet_mou', "Menghapus dokumen MoU Kerjasama: {$namaArsip}");

        return redirect()->route('situan.ekabinet.index', ['tab' => 'mou'])
            ->with('success', "Dokumen MoU {$namaArsip} berhasil dihapus dari E-Kabinet MoU.");
    }
}
