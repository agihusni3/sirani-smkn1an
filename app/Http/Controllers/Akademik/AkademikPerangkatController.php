<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAtpItem;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikKktpItem;
use App\Models\AkademikMataPelajaran;
use App\Models\AkademikModulAjar;
use App\Models\AkademikPerangkatAjar;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AkademikPerangkatController extends Controller
{
    /**
     * Tampilan Utama: Daftar Perangkat Ajar & Meja Supervisi Kurikulum
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isGuru = $user->isGuru();
        $isAdminOrWaka = $user->isAdmin() || $user->isWakaKurikulum() || $user->isKepalaSekolah();

        $tahunAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::latest('id')->first();
        $semester = (int) $request->get('semester', 1);

        // Jika user adalah Guru (bukan admin mutlak):
        $guruId = null;
        if ($user->guru_id) {
            $guruId = $user->guru_id;
        } elseif ($request->filled('guru_id') && $isAdminOrWaka) {
            $guruId = $request->guru_id;
        }

        // Query Perangkat
        $query = AkademikPerangkatAjar::with(['guru', 'mataPelajaran', 'distribusiMengajar.rombel', 'tahunAjaran', 'atpItems', 'modulAjars', 'kktpItems', 'validator'])
            ->where('semester', $semester);

        if ($guruId && !$isAdminOrWaka) {
            $query->where('guru_id', $guruId);
        } elseif ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->whereHas('mataPelajaran', fn($m) => $m->where('nama_mapel', 'like', "%{$q}%")->orWhere('kode_mapel', 'like', "%{$q}%"))
                  ->orWhereHas('guru', fn($g) => $g->where('nama', 'like', "%{$q}%"));
            });
        }

        $perangkats = $query->orderBy('tingkat')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Ambil daftar distribusi mengajar aktif milik guru yang bersangkutan (untuk pembuatan cepat)
        $myDistribusis = collect([]);
        if ($user->guru_id) {
            $myDistribusis = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel'])
                ->where('guru_id', $user->guru_id)
                ->where('semester', $semester)
                ->get();
        }

        // Statistik monitoring untuk Wakakur / Kepsek
        $stats = [
            'total' => AkademikPerangkatAjar::where('semester', $semester)->count(),
            'disahkan' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'disahkan')->count(),
            'diajukan' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'diajukan')->count(),
            'revisi' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'perlu_revisi')->count(),
            'draft' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'draft')->count(),
        ];

        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $mapels = AkademikMataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();

        return view('dcc.akademik.perangkat.index', compact(
            'perangkats',
            'myDistribusis',
            'tahunAktif',
            'semester',
            'stats',
            'gurus',
            'mapels',
            'isAdminOrWaka',
            'isGuru'
        ));
    }

    /**
     * Buat Folder Perangkat Ajar Baru
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'mata_pelajaran_id' => 'required|exists:akademik_mata_pelajarans,id',
            'distribusi_id' => 'nullable|exists:akademik_distribusi_mengajars,id',
            'tingkat' => 'required|in:X,XI,XII',
            'semester' => 'required|in:1,2',
            'fase' => 'nullable|string',
            'rpe_pekan_efektif' => 'nullable|integer|min:1|max:26',
            'rpe_pekan_cadangan' => 'nullable|integer|min:0|max:10',
        ]);

        // Proteksi guru hanya bisa buat milik sendiri kecuali Admin/Wakakur
        if ($user->isGuru() && !$user->isAdmin() && !$user->isWakaKurikulum() && $user->guru_id != $validated['guru_id']) {
            return back()->with('error', 'Anda hanya dapat membuat perangkat ajar untuk akun guru Anda sendiri.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::latest('id')->first();
        $mapel = AkademikMataPelajaran::findOrFail($validated['mata_pelajaran_id']);

        // Tentukan fase otomatis jika kosong: Kelas X = Fase E, XI & XII = Fase F
        $fase = $validated['fase'] ?? ($validated['tingkat'] === 'X' ? 'E' : 'F');

        // Cek duplikasi
        $exists = AkademikPerangkatAjar::where('guru_id', $validated['guru_id'])
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->where('tingkat', $validated['tingkat'])
            ->where('semester', $validated['semester'])
            ->first();

        if ($exists) {
            return redirect()->route('akademik.perangkat.show', $exists->id)
                ->with('info', 'Folder perangkat ajar untuk mapel dan tingkat ini sudah ada. Mengalihkan ke dokumen terkait.');
        }

        $perangkat = AkademikPerangkatAjar::create([
            'guru_id' => $validated['guru_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'distribusi_id' => $validated['distribusi_id'] ?? null,
            'tahun_ajaran_id' => $tahunAktif?->id,
            'semester' => $validated['semester'],
            'tingkat' => $validated['tingkat'],
            'fase' => $fase,
            'status' => 'draft',
            'rpe_pekan_efektif' => $validated['rpe_pekan_efektif'] ?? 18,
            'rpe_pekan_cadangan' => $validated['rpe_pekan_cadangan'] ?? 2,
        ]);

        return redirect()->route('akademik.perangkat.show', $perangkat->id)
            ->with('success', 'Folder Perangkat Ajar Kurikulum Merdeka berhasil dibuat! Silakan lengkapi ATP dan Modul Ajar.');
    }

    /**
     * Detail Lembar Perangkat Ajar (5 Komponen Terpadu)
     */
    public function show($id)
    {
        $perangkat = AkademikPerangkatAjar::with([
            'guru',
            'mataPelajaran',
            'distribusiMengajar.rombel',
            'tahunAjaran',
            'atpItems.modulAjars',
            'modulAjars',
            'kktpItems',
            'validator'
        ])->findOrFail($id);

        $user = auth()->user();
        $isAdminOrWaka = $user->isAdmin() || $user->isWakaKurikulum() || $user->isKepalaSekolah();
        $isOwner = $user->guru_id && $user->guru_id === $perangkat->guru_id;

        // Hitung kelengkapan
        $kelengkapan = $perangkat->kelengkapan;
        $statusBadge = $perangkat->status_badge;

        return view('dcc.akademik.perangkat.show', compact(
            'perangkat',
            'kelengkapan',
            'statusBadge',
            'isAdminOrWaka',
            'isOwner'
        ));
    }

    /**
     * Update Informasi Umum / RPE
     */
    public function updateInfo(Request $request, $id)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);

        $validated = $request->validate([
            'rpe_pekan_efektif' => 'required|integer|min:1|max:30',
            'rpe_pekan_cadangan' => 'required|integer|min:0|max:10',
            'catatan_guru' => 'nullable|string',
            'file_kaldik' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $data = [
            'rpe_pekan_efektif' => $validated['rpe_pekan_efektif'],
            'rpe_pekan_cadangan' => $validated['rpe_pekan_cadangan'],
            'catatan_guru' => $validated['catatan_guru'],
        ];

        if ($request->hasFile('file_kaldik')) {
            if ($perangkat->file_kaldik_rpe && Storage::disk('public')->exists($perangkat->file_kaldik_rpe)) {
                Storage::disk('public')->delete($perangkat->file_kaldik_rpe);
            }
            $data['file_kaldik_rpe'] = $request->file('file_kaldik')->store('perangkat_ajar/kaldik', 'public');
        }

        $perangkat->update($data);

        return back()->with('success', 'Rincian Pekan Efektif (RPE) & Catatan berhasil diperbarui.');
    }

    /**
     * Tambah / Edit Butir Alur Tujuan Pembelajaran (ATP)
     */
    public function storeAtp(Request $request, $id)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);

        $validated = $request->validate([
            'atp_id' => 'nullable|exists:akademik_atp_items,id',
            'kode_tp' => 'required|string|max:30',
            'urutan' => 'required|integer|min:1',
            'elemen_cp' => 'nullable|string|max:150',
            'tujuan_pembelajaran' => 'required|string',
            'materi_pokok' => 'required|string',
            'alokasi_jp' => 'required|integer|min:1|max:40',
            'profil_pancasila' => 'nullable|string|max:255',
            'asesmen_rencana' => 'nullable|string',
        ]);

        if (!empty($validated['atp_id'])) {
            $atp = AkademikAtpItem::where('perangkat_id', $perangkat->id)->findOrFail($validated['atp_id']);
            $atp->update($validated);
            $msg = 'Butir Tujuan Pembelajaran (ATP) berhasil diperbarui.';
        } else {
            $validated['perangkat_id'] = $perangkat->id;
            $validated['semester'] = $perangkat->semester;
            AkademikAtpItem::create($validated);
            $msg = 'Butir Tujuan Pembelajaran (ATP) baru berhasil ditambahkan.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Hapus Butir ATP
     */
    public function destroyAtp($id, $atpId)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);
        $atp = AkademikAtpItem::where('perangkat_id', $perangkat->id)->findOrFail($atpId);
        $atp->delete();

        return back()->with('success', 'Butir Tujuan Pembelajaran (ATP) berhasil dihapus.');
    }

    /**
     * Tambah / Update Modul Ajar (MA) / RPP Merdeka
     */
    public function storeModul(Request $request, $id)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);

        $validated = $request->validate([
            'modul_id' => 'nullable|exists:akademik_modul_ajars,id',
            'atp_item_id' => 'nullable|exists:akademik_atp_items,id',
            'judul_modul' => 'required|string|max:255',
            'pertemuan_ke_mulai' => 'required|integer|min:1',
            'pertemuan_ke_selesai' => 'required|integer|min:1',
            'alokasi_jp' => 'required|integer|min:1|max:40',
            'model_pembelajaran' => 'nullable|string|max:150',
            'metode_pembelajaran' => 'nullable|string|max:150',
            'pemahaman_bermakna' => 'nullable|string',
            'pertanyaan_pemantik' => 'nullable|string',
            'kegiatan_pendahuluan' => 'nullable|string',
            'kegiatan_inti' => 'nullable|string',
            'kegiatan_penutup' => 'nullable|string',
            'refleksi_guru_siswa' => 'nullable|string',
            'file_modul' => 'nullable|file|mimes:pdf,docx,doc|max:10240',
            'file_lkpd' => 'nullable|file|mimes:pdf,docx,doc|max:10240',
            'file_jobsheet' => 'nullable|file|mimes:pdf,docx,doc|max:10240',
            'link_media_pembelajaran' => 'nullable|url|max:500',
        ]);

        $data = $validated;
        unset($data['file_modul'], $data['file_lkpd'], $data['file_jobsheet']);

        if ($request->hasFile('file_modul')) {
            $data['file_modul_pdf'] = $request->file('file_modul')->store('perangkat_ajar/modul', 'public');
        }
        if ($request->hasFile('file_lkpd')) {
            $data['file_lkpd_pdf'] = $request->file('file_lkpd')->store('perangkat_ajar/lkpd', 'public');
        }
        if ($request->hasFile('file_jobsheet')) {
            $data['file_jobsheet_praktik'] = $request->file('file_jobsheet')->store('perangkat_ajar/jobsheet', 'public');
        }

        if (!empty($validated['modul_id'])) {
            $modul = AkademikModulAjar::where('perangkat_id', $perangkat->id)->findOrFail($validated['modul_id']);
            $modul->update($data);
            $msg = 'Modul Ajar / RPP Merdeka berhasil diperbarui.';
        } else {
            $data['perangkat_id'] = $perangkat->id;
            AkademikModulAjar::create($data);
            $msg = 'Modul Ajar / RPP Merdeka baru berhasil disimpan.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Hapus Modul Ajar
     */
    public function destroyModul($id, $modulId)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);
        $modul = AkademikModulAjar::where('perangkat_id', $perangkat->id)->findOrFail($modulId);

        if ($modul->file_modul_pdf && Storage::disk('public')->exists($modul->file_modul_pdf)) {
            Storage::disk('public')->delete($modul->file_modul_pdf);
        }
        if ($modul->file_lkpd_pdf && Storage::disk('public')->exists($modul->file_lkpd_pdf)) {
            Storage::disk('public')->delete($modul->file_lkpd_pdf);
        }
        if ($modul->file_jobsheet_praktik && Storage::disk('public')->exists($modul->file_jobsheet_praktik)) {
            Storage::disk('public')->delete($modul->file_jobsheet_praktik);
        }

        $modul->delete();
        return back()->with('success', 'Modul Ajar berhasil dihapus.');
    }

    /**
     * Simpan Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)
     */
    public function storeKktp(Request $request, $id)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);

        $validated = $request->validate([
            'atp_item_id' => 'required|exists:akademik_atp_items,id',
            'pendekatan' => 'required|in:interval_nilai,rubrik,deskripsi',
            'keterangan_tuntas' => 'nullable|string',
            'keterangan_remedial' => 'nullable|string',
            'skala_kriteria' => 'nullable|string', // JSON atau text deskripsi
        ]);

        $kktp = AkademikKktpItem::updateOrCreate(
            [
                'perangkat_id' => $perangkat->id,
                'atp_item_id' => $validated['atp_item_id'],
            ],
            [
                'pendekatan' => $validated['pendekatan'],
                'keterangan_tuntas' => $validated['keterangan_tuntas'],
                'keterangan_remedial' => $validated['keterangan_remedial'],
                'skala_kriteria' => !empty($validated['skala_kriteria']) ? json_decode($validated['skala_kriteria'], true) : null,
            ]
        );

        return back()->with('success', 'Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) berhasil disimpan.');
    }

    /**
     * Ajukan Perangkat ke Waka Kurikulum / Kepala Sekolah
     */
    public function ajukan($id)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);
        $user = auth()->user();

        // Validasi kepemilikan
        if ($user->isGuru() && !$user->isAdmin() && $user->guru_id != $perangkat->guru_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengajukan perangkat ajar ini.');
        }

        if ($perangkat->atpItems()->count() == 0) {
            return back()->with('error', 'Gagal mengajukan: Alur Tujuan Pembelajaran (ATP) belum diisi.');
        }

        $perangkat->update([
            'status' => 'diajukan',
        ]);

        return back()->with('success', 'Perangkat pembelajaran berhasil diajukan untuk supervisi Waka Kurikulum & Kepala Sekolah!');
    }

    /**
     * Tindakan Supervisi & Telaah Kurikulum (Waka Kurikulum / Kepala Sekolah)
     */
    public function supervisiAction(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isWakaKurikulum() && !$user->isKepalaSekolah()) {
            abort(403, 'Akses terbatas untuk Waka Kurikulum dan Kepala Sekolah.');
        }

        $perangkat = AkademikPerangkatAjar::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:disahkan,perlu_revisi,draft',
            'catatan_supervisi' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'catatan_supervisi' => $validated['catatan_supervisi'],
        ];

        if ($validated['status'] === 'disahkan') {
            $updateData['disahkan_oleh'] = $user->id;
            $updateData['tanggal_pengesahan'] = now();
            if (empty($perangkat->qr_token_pengesahan)) {
                $updateData['qr_token_pengesahan'] = 'PERANGKAT-' . strtoupper(Str::random(10)) . '-' . $perangkat->id;
            }
        }

        $perangkat->update($updateData);

        return back()->with('success', 'Hasil supervisi dan status perangkat ajar berhasil diperbarui.');
    }

    /**
     * Cetak Lembar Pengesahan Resmi Standar Dinas Pendidikan Ber-QR Code
     */
    public function cetakPengesahan($id)
    {
        $perangkat = AkademikPerangkatAjar::with([
            'guru',
            'mataPelajaran',
            'distribusiMengajar.rombel',
            'tahunAjaran',
            'validator',
            'atpItems',
            'modulAjars'
        ])->findOrFail($id);

        $sekolah = PengaturanSekolah::first() ?? new PengaturanSekolah([
            'nama_sekolah' => 'SMK NEGERI 1 AIR NANINGAN',
            'npsn' => '69896425',
            'alamat' => 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung',
            'nama_kepala_sekolah' => 'Aprida, S.Pd., M.M.',
            'nip_kepala_sekolah' => '19750412 200501 2 007',
        ]);

        // Pastikan QR token ada
        $qrToken = $perangkat->generateQrToken();

        return view('dcc.akademik.perangkat.cetak_pengesahan', compact(
            'perangkat',
            'sekolah',
            'qrToken'
        ));
    }

    /**
     * Hapus Dokumen Perangkat
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $perangkat = AkademikPerangkatAjar::findOrFail($id);

        if (!$user->isAdmin() && !$user->isWakaKurikulum() && $user->guru_id != $perangkat->guru_id) {
            abort(403);
        }

        $perangkat->delete();
        return redirect()->route('akademik.perangkat.index')->with('success', 'Folder Perangkat Pembelajaran berhasil dihapus.');
    }

    /**
     * Helper resolver konteks perangkat pembelajaran per guru / mata pelajaran
     */
    private function resolvePerangkatContext(Request $request)
    {
        $user = auth()->user();
        $isGuru = $user->isGuru();
        $isAdminOrWaka = $user->isAdmin() || $user->isWakaKurikulum() || $user->isKepalaSekolah();

        $tahunAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::latest('id')->first();
        $semester = (int) $request->get('semester', 1);

        $guruId = null;
        if ($user->guru_id) {
            $guruId = $user->guru_id;
        } elseif ($request->filled('guru_id') && $isAdminOrWaka) {
            $guruId = $request->guru_id;
        }

        // Query Perangkats yang tersedia untuk dropdown switcher
        $perangkatsQuery = AkademikPerangkatAjar::with(['guru', 'mataPelajaran', 'tahunAjaran'])
            ->where('semester', $semester);

        if ($guruId && !$isAdminOrWaka) {
            $perangkatsQuery->where('guru_id', $guruId);
        } elseif ($request->filled('guru_id')) {
            $perangkatsQuery->where('guru_id', $request->guru_id);
        }

        $perangkatsList = $perangkatsQuery->orderBy('tingkat')->orderBy('id', 'desc')->get();

        // Cari active perangkat
        $activePerangkat = null;
        if ($request->filled('perangkat_id')) {
            $activePerangkat = AkademikPerangkatAjar::with(['guru', 'mataPelajaran', 'distribusiMengajar.rombel', 'tahunAjaran', 'atpItems', 'modulAjars', 'kktpItems', 'validator'])
                ->find($request->perangkat_id);
        }

        if (!$activePerangkat && $perangkatsList->isNotEmpty()) {
            $activePerangkat = AkademikPerangkatAjar::with(['guru', 'mataPelajaran', 'distribusiMengajar.rombel', 'tahunAjaran', 'atpItems', 'modulAjars', 'kktpItems', 'validator'])
                ->find($perangkatsList->first()->id);
        }

        // Ambil daftar distribusi mengajar aktif milik guru (untuk pembuatan cepat jika belum ada)
        $myDistribusis = collect([]);
        if ($user->guru_id) {
            $myDistribusis = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel'])
                ->where('guru_id', $user->guru_id)
                ->where('semester', $semester)
                ->get();
        }

        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $mapels = AkademikMataPelajaran::where('is_active', true)->orderBy('nama_mapel')->get();

        return [
            'user' => $user,
            'isGuru' => $isGuru,
            'isAdminOrWaka' => $isAdminOrWaka,
            'tahunAktif' => $tahunAktif,
            'semester' => $semester,
            'perangkatsList' => $perangkatsList,
            'activePerangkat' => $activePerangkat,
            'myDistribusis' => $myDistribusis,
            'gurus' => $gurus,
            'mapels' => $mapels,
        ];
    }

    /**
     * Menu 1: Capaian Pembelajaran (CP) Resmi Kurikulum Merdeka
     */
    public function cp(Request $request)
    {
        $ctx = $this->resolvePerangkatContext($request);
        
        $selectedMapelId = $request->get('mapel_id', $ctx['activePerangkat']?->mata_pelajaran_id);
        $selectedMapel = null;
        if ($selectedMapelId) {
            $selectedMapel = AkademikMataPelajaran::find($selectedMapelId);
        }
        if (!$selectedMapel && $ctx['mapels']->isNotEmpty()) {
            $selectedMapel = $ctx['mapels']->first();
        }

        return view('dcc.akademik.perangkat.cp', array_merge($ctx, [
            'selectedMapel' => $selectedMapel,
        ]));
    }

    /**
     * Menu 2: Tujuan & Alur Pembelajaran (TP & ATP)
     */
    public function atp(Request $request)
    {
        $ctx = $this->resolvePerangkatContext($request);
        $atpItems = $ctx['activePerangkat']?->atpItems()->orderBy('urutan')->get() ?? collect([]);
        $totalJp = $atpItems->sum('alokasi_jp');

        return view('dcc.akademik.perangkat.atp', array_merge($ctx, [
            'atpItems' => $atpItems,
            'totalJp' => $totalJp,
        ]));
    }

    /**
     * Menu 3: Program Tahunan (Prota) & Program Semester (Promes)
     */
    public function protaPromes(Request $request)
    {
        $ctx = $this->resolvePerangkatContext($request);
        $atpItems = $ctx['activePerangkat']?->atpItems()->orderBy('urutan')->get() ?? collect([]);
        
        $rpePekanEfektif = $ctx['activePerangkat']?->rpe_pekan_efektif ?? 18;
        $rpeCadangan = $ctx['activePerangkat']?->rpe_pekan_cadangan ?? 2;
        $totalPekan = $rpePekanEfektif + $rpeCadangan;

        $jamPerMinggu = $ctx['activePerangkat']?->distribusiMengajar?->total_jam_per_minggu ?? 4;
        $totalJpSemester = $rpePekanEfektif * $jamPerMinggu;

        return view('dcc.akademik.perangkat.prota_promes', array_merge($ctx, [
            'atpItems' => $atpItems,
            'rpePekanEfektif' => $rpePekanEfektif,
            'rpeCadangan' => $rpeCadangan,
            'totalPekan' => $totalPekan,
            'jamPerMinggu' => $jamPerMinggu,
            'totalJpSemester' => $totalJpSemester,
        ]));
    }

    /**
     * Menu 4: Modul Ajar (RPP Merdeka), LKPD & Bahan Ajar Praktik
     */
    public function modulAjar(Request $request)
    {
        $ctx = $this->resolvePerangkatContext($request);
        $modulAjars = $ctx['activePerangkat']?->modulAjars()->with('atpItem')->orderBy('pertemuan_ke_mulai')->get() ?? collect([]);
        $atpItems = $ctx['activePerangkat']?->atpItems()->orderBy('urutan')->get() ?? collect([]);

        return view('dcc.akademik.perangkat.modul_ajar', array_merge($ctx, [
            'modulAjars' => $modulAjars,
            'atpItems' => $atpItems,
        ]));
    }

    /**
     * Menu 5: Kriteria Ketuntasan Tujuan Pembelajaran (KKTP)
     */
    public function kktp(Request $request)
    {
        $ctx = $this->resolvePerangkatContext($request);
        $kktpItems = $ctx['activePerangkat']?->kktpItems()->with('atpItem')->get() ?? collect([]);
        $atpItems = $ctx['activePerangkat']?->atpItems()->orderBy('urutan')->get() ?? collect([]);

        return view('dcc.akademik.perangkat.kktp', array_merge($ctx, [
            'kktpItems' => $kktpItems,
            'atpItems' => $atpItems,
        ]));
    }

    /**
     * Menu 6: Meja Supervisi, Telaah & Validasi Pengesahan Resmi
     */
    public function supervisiMeja(Request $request)
    {
        $user = auth()->user();
        $isGuru = $user->isGuru();
        $isAdminOrWaka = $user->isAdmin() || $user->isWakaKurikulum() || $user->isKepalaSekolah();

        $tahunAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::latest('id')->first();
        $semester = (int) $request->get('semester', 1);

        $query = AkademikPerangkatAjar::with(['guru', 'mataPelajaran', 'distribusiMengajar.rombel', 'tahunAjaran', 'atpItems', 'modulAjars', 'kktpItems', 'validator'])
            ->where('semester', $semester);

        if ($user->guru_id && !$isAdminOrWaka) {
            $query->where('guru_id', $user->guru_id);
        } elseif ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perangkats = $query->orderBy('status', 'asc')->orderBy('tingkat')->paginate(20)->withQueryString();

        $stats = [
            'total' => AkademikPerangkatAjar::where('semester', $semester)->count(),
            'disahkan' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'disahkan')->count(),
            'diajukan' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'diajukan')->count(),
            'revisi' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'perlu_revisi')->count(),
            'draft' => AkademikPerangkatAjar::where('semester', $semester)->where('status', 'draft')->count(),
        ];

        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();

        return view('dcc.akademik.perangkat.supervisi', compact(
            'perangkats', 'stats', 'semester', 'tahunAktif', 'gurus', 'isAdminOrWaka', 'isGuru'
        ));
    }
}
