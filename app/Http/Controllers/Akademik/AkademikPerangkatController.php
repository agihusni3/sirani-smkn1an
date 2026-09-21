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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

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
     * Simpan / Perbarui Capaian Pembelajaran (CP) oleh Guru Pengampu
     */
    public function storeCp(Request $request, $id)
    {
        $perangkat = AkademikPerangkatAjar::findOrFail($id);
        $user = auth()->user();

        // Validasi hak akses: guru pemilik atau admin / wakakurikulum
        if ($user->isGuru() && !$user->isAdmin() && !$user->isWakaKurikulum() && $user->guru_id != $perangkat->guru_id) {
            return back()->with('error', 'Anda hanya dapat mengedit Capaian Pembelajaran pada perangkat milik akun guru Anda.');
        }

        $validated = $request->validate([
            'capaian_pembelajaran' => 'required|string',
            'rasional_tujuan' => 'nullable|string',
            'elemen_cp' => 'nullable|array',
            'elemen_cp.*.nama' => 'nullable|string',
            'elemen_cp.*.deskripsi' => 'nullable|string',
        ]);

        // Filter elemen CP yang tidak kosong
        $elemenList = [];
        if (!empty($validated['elemen_cp'])) {
            foreach ($validated['elemen_cp'] as $elem) {
                $nama = trim($elem['nama'] ?? '');
                $deskripsi = trim($elem['deskripsi'] ?? '');
                if ($nama !== '' || $deskripsi !== '') {
                    $elemenList[] = [
                        'nama' => $nama,
                        'deskripsi' => $deskripsi,
                    ];
                }
            }
        }

        $perangkat->update([
            'capaian_pembelajaran' => $validated['capaian_pembelajaran'],
            'rasional_tujuan' => $validated['rasional_tujuan'] ?? null,
            'elemen_cp' => $elemenList,
        ]);

        return back()->with('success', 'Capaian Pembelajaran (CP) dan Elemen Kompetensi berhasil disimpan oleh Guru!');
    }

    /**
     * Salin Template Resmi SK BSKAP 032/2024 ke Dokumen Guru
     */
    public function copyTemplateCp(Request $request, $id)
    {
        $perangkat = AkademikPerangkatAjar::with('mataPelajaran')->findOrFail($id);
        $user = auth()->user();

        if ($user->isGuru() && !$user->isAdmin() && !$user->isWakaKurikulum() && $user->guru_id != $perangkat->guru_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses pada dokumen perangkat ajar ini.');
        }

        $mapel = $perangkat->mataPelajaran;
        if (!$mapel) {
            return back()->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        // Ambil acuan CP master sesuai fase
        $cpText = '';
        if ($perangkat->fase === 'E' && !empty($mapel->capaian_pembelajaran_fase_e)) {
            $cpText = $mapel->capaian_pembelajaran_fase_e;
        } elseif ($perangkat->fase === 'F' && !empty($mapel->capaian_pembelajaran_fase_f)) {
            $cpText = $mapel->capaian_pembelajaran_fase_f;
        } else {
            $cpText = $mapel->deskripsi_cp ?? $mapel->capaian_pembelajaran_fase_e ?? $mapel->capaian_pembelajaran_fase_f ?? '';
        }

        $elemenList = [];
        if (!empty($mapel->elemen_cp)) {
            $elemenList = is_array($mapel->elemen_cp) ? $mapel->elemen_cp : (json_decode($mapel->elemen_cp, true) ?? []);
        }

        $perangkat->update([
            'capaian_pembelajaran' => $cpText ?: 'Peserta didik mampu menguasai capaian kompetensi mata pelajaran ' . $mapel->nama_mapel . ' sesuai Kurikulum Merdeka Fase ' . $perangkat->fase . '.',
            'elemen_cp' => $elemenList,
        ]);

        return back()->with('success', 'Template Capaian Pembelajaran standar resmi berhasil disalin ke dokumen Anda. Silakan disesuaikan jika diperlukan.');
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
        $ctx             = $this->resolvePerangkatContext($request);
        $activePerangkat = $ctx['activePerangkat'];
        $user            = $ctx['user'];
        $isAdminOrWaka   = $ctx['isAdminOrWaka'];
        $semester        = $ctx['semester'];

        // ─── Override $mapels: Guru hanya melihat mapel yang ditugaskan ───
        if ($user->guru_id && !$isAdminOrWaka) {
            // Kumpulkan mata_pelajaran_id dari perangkat yang dimiliki guru ini
            $mapelIdsPerangkat = AkademikPerangkatAjar::where('guru_id', $user->guru_id)
                ->where('semester', $semester)
                ->pluck('mata_pelajaran_id');

            // Kumpulkan juga dari distribusi mengajar aktif semester ini
            $mapelIdsDist = AkademikDistribusiMengajar::where('guru_id', $user->guru_id)
                ->where('semester', $semester)
                ->pluck('mata_pelajaran_id');

            // Gabung & deduplikasi
            $assignedMapelIds = $mapelIdsPerangkat->merge($mapelIdsDist)->unique()->values();

            $mapels = AkademikMataPelajaran::where('is_active', true)
                ->whereIn('id', $assignedMapelIds)
                ->orderBy('nama_mapel')
                ->get();
        } else {
            // Admin / Wakakur tetap melihat semua mapel aktif
            $mapels = $ctx['mapels'];
        }

        // Override context mapels
        $ctx['mapels'] = $mapels;

        $selectedMapelId = $request->get('mapel_id', $activePerangkat?->mata_pelajaran_id);
        $selectedMapel   = null;
        if ($selectedMapelId) {
            $selectedMapel = AkademikMataPelajaran::find($selectedMapelId);
        }
        if (!$selectedMapel && $activePerangkat) {
            $selectedMapel = $activePerangkat->mataPelajaran;
        }
        if (!$selectedMapel && $mapels->isNotEmpty()) {
            $selectedMapel = $mapels->first();
        }

        $isOwner = $user->guru_id && $activePerangkat && $user->guru_id === $activePerangkat->guru_id;
        $canEdit = $isAdminOrWaka || $isOwner;

        // Acuan standar nasional SK BSKAP 032/2024
        $templateCpText = '';
        if ($activePerangkat && $activePerangkat->fase === 'E') {
            $templateCpText = $selectedMapel?->capaian_pembelajaran_fase_e;
        } elseif ($activePerangkat && $activePerangkat->fase === 'F') {
            $templateCpText = $selectedMapel?->capaian_pembelajaran_fase_f;
        }
        if (empty($templateCpText)) {
            $templateCpText = $selectedMapel?->deskripsi_cp ?? '';
        }

        $templateElemenCp = [];
        if ($selectedMapel && !empty($selectedMapel->elemen_cp)) {
            $templateElemenCp = is_array($selectedMapel->elemen_cp) ? $selectedMapel->elemen_cp : (json_decode($selectedMapel->elemen_cp, true) ?? []);
        }

        return view('dcc.akademik.perangkat.cp', array_merge($ctx, [
            'selectedMapel'    => $selectedMapel,
            'isOwner'          => $isOwner,
            'canEdit'          => $canEdit,
            'templateCpText'   => $templateCpText,
            'templateElemenCp' => $templateElemenCp,
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

    /**
     * ============================================================
     * EXPORT PDF — Seluruh Dokumen Perangkat Pembelajaran (5 Bab)
     * ============================================================
     */
    /**
     * Dapatkan path dan base64 logo untuk ekspor PDF & Word berstandar SITUAN
     */
    private function resolveKopLogos($sekolah): array
    {
        $storageProv = $sekolah->logo_provinsi ? storage_path('app/public/' . $sekolah->logo_provinsi) : null;
        $provPath = ($storageProv && file_exists($storageProv)) ? $storageProv : (
            file_exists(public_path('img/logo_prov_lampung.png')) ? public_path('img/logo_prov_lampung.png') : (
                file_exists(public_path('lampung.png')) ? public_path('lampung.png') : null
            )
        );

        $storageSekolah = $sekolah->logo_sekolah ? storage_path('app/public/' . $sekolah->logo_sekolah) : null;
        $sekolahPath = ($storageSekolah && file_exists($storageSekolah)) ? $storageSekolah : (
            file_exists(public_path('img/logo.png')) ? public_path('img/logo.png') : (
                file_exists(public_path('logo.png')) ? public_path('logo.png') : null
            )
        );

        $logoProvBase64 = ($provPath && file_exists($provPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($provPath)) : null;
        $logoSekolahBase64 = ($sekolahPath && file_exists($sekolahPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sekolahPath)) : null;

        return [
            'provPath'          => $provPath,
            'sekolahPath'       => $sekolahPath,
            'logoProvBase64'    => $logoProvBase64,
            'logoSekolahBase64' => $logoSekolahBase64,
        ];
    }

    /**
     * Helper: Tambahkan Kop Surat resmi dinas SITUAN pada dokumen Word (PhpWord)
     */
    private function addWordKopSurat($section, $sekolah, $provPath, $sekolahPath, $compact = false)
    {
        $table = $section->addTable([
            'borderSize' => 0,
            'cellMargin' => 0,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'width' => 100 * 50,
            'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
        ]);
        $table->addRow();

        // Logo Kiri: Provinsi
        $cell1 = $table->addCell(1300, ['valign' => 'center']);
        if ($provPath && file_exists($provPath)) {
            $cell1->addImage($provPath, [
                'width' => $compact ? 44 : 50,
                'height' => $compact ? 55 : 62,
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            ]);
        }

        // Teks Tengah: Kop Instansi
        $cell2 = $table->addCell(7000, ['valign' => 'center']);
        $cell2->addText(
            strtoupper($sekolah->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG'),
            ['bold' => true, 'size' => $compact ? 9.5 : 10.5, 'name' => 'Times New Roman'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 10]
        );
        $cell2->addText(
            strtoupper($sekolah->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN'),
            ['bold' => true, 'size' => $compact ? 10.5 : 11.5, 'name' => 'Times New Roman'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 10]
        );
        $cell2->addText(
            strtoupper($sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN'),
            ['bold' => true, 'size' => $compact ? 12.5 : 14.5, 'name' => 'Times New Roman'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 20]
        );
        $alamat = ($sekolah->alamat ?: 'Jl. Makam Baturuguk, Pekon Karang Sari') . ', Kec. ' . ($sekolah->kecamatan ?: 'Air Naningan') . ', Kab. ' . ($sekolah->kabupaten ?: 'Tanggamus') . ', ' . ($sekolah->provinsi ?: 'Lampung') . ' ' . ($sekolah->kode_pos ?: '35379');
        $cell2->addText(
            $alamat,
            ['italic' => true, 'size' => $compact ? 7 : 7.5, 'name' => 'Times New Roman'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 10]
        );
        $kontak = 'NPSN: ' . ($sekolah->npsn ?: '70011825') . ' | Website: ' . ($sekolah->website ?: 'smkn1airnaningan.sch.id') . ' | Email: ' . ($sekolah->email ?: 'smkn1airnaningan@gmail.com');
        $cell2->addText(
            $kontak,
            ['size' => $compact ? 6.5 : 7, 'name' => 'Times New Roman'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0]
        );

        // Logo Kanan: Sekolah
        $cell3 = $table->addCell(1300, ['valign' => 'center']);
        if ($sekolahPath && file_exists($sekolahPath)) {
            $cell3->addImage($sekolahPath, [
                'width' => $compact ? 44 : 50,
                'height' => $compact ? 50 : 56,
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            ]);
        }

        // Garis Ganda Pembatas Kop Surat
        $lineTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 0, 'width' => 100 * 50, 'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT]);
        $lineTable->addRow(20);
        $lineTable->addCell(9600, [
            'borderTopSize' => 18,
            'borderTopColor' => '000000',
            'borderBottomSize' => 6,
            'borderBottomColor' => '000000',
        ]);
        $section->addTextBreak(1);
    }

    /**
     * ================================================================
     * EXPORT PDF — Dokumen Capaian Pembelajaran (CP) A4 (Format SITUAN)
     * ================================================================
     */
    public function exportCpPdf($id)
    {
        $perangkat = AkademikPerangkatAjar::with([
            'guru', 'mataPelajaran', 'distribusiMengajar.rombel',
            'tahunAjaran', 'validator'
        ])->findOrFail($id);

        $sekolah = PengaturanSekolah::getAktif();
        $logos = $this->resolveKopLogos($sekolah);
        $logoProvBase64 = $logos['logoProvBase64'];
        $logoSekolahBase64 = $logos['logoSekolahBase64'];

        $activeCpText = $perangkat->resolved_cp;
        $activeElemen = $perangkat->resolved_elemen_cp;

        $pdf = Pdf::loadView('dcc.akademik.perangkat.export.pdf_cp', compact(
            'perangkat', 'sekolah', 'logoProvBase64', 'logoSekolahBase64',
            'activeCpText', 'activeElemen'
        ))->setPaper('a4', 'portrait');

        $filename = 'CP-' . Str::slug($perangkat->mataPelajaran?->nama_mapel ?? 'mapel') . '-Kelas' . $perangkat->tingkat . '-Fase' . $perangkat->fase . '-Smt' . $perangkat->semester . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * ================================================================
     * EXPORT DOCX — Dokumen Capaian Pembelajaran (CP) A4 (Format SITUAN)
     * ================================================================
     */
    public function exportCpDocx($id)
    {
        $perangkat = AkademikPerangkatAjar::with([
            'guru', 'mataPelajaran', 'distribusiMengajar.rombel',
            'tahunAjaran', 'validator'
        ])->findOrFail($id);

        $sekolah = PengaturanSekolah::getAktif();
        $logos = $this->resolveKopLogos($sekolah);

        $phpWord = new PhpWord();
        $phpWord->getSettings()->setUpdateFields(true);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        $sectionStyle = [
            'paperSize'    => 'A4',
            'marginTop'    => 1134, // 2 cm
            'marginBottom' => 1134, // 2 cm
            'marginLeft'   => 1418, // 2.5 cm
            'marginRight'  => 1134, // 2 cm
        ];
        $section = $phpWord->addSection($sectionStyle);

        // 1. KOP SURAT SITUAN
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], false);

        // 2. JUDUL DOKUMEN
        $section->addText('CAPAIAN PEMBELAJARAN (CP)', ['bold' => true, 'size' => 13, 'underline' => 'single'], ['alignment' => Jc::CENTER, 'spaceAfter' => 30]);
        $section->addText('KURIKULUM MERDEKA TAHUN AJARAN ' . ($perangkat->tahunAjaran?->nama ?? '2026/2027'), ['bold' => true, 'size' => 11], ['alignment' => Jc::CENTER, 'spaceAfter' => 100]);

        // 3. TABEL IDENTITAS
        $tblIdentitas = $section->addTable(['borderSize' => 6, 'borderColor' => 'CCCCCC', 'cellMargin' => 60, 'width' => 100 * 50, 'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT]);
        $tblIdentitas->addRow();
        $tblIdentitas->addCell(2200, ['bgColor' => 'F1F5F9'])->addText('Mata Pelajaran', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(4000)->addText($perangkat->mataPelajaran?->nama_mapel . ' (' . $perangkat->mataPelajaran?->kode_mapel . ')', ['size' => 10]);
        $tblIdentitas->addCell(1600, ['bgColor' => 'F1F5F9'])->addText('Fase / Kelas', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(1800)->addText('Fase ' . $perangkat->fase . ' / Kls ' . $perangkat->tingkat, ['size' => 10]);

        $tblIdentitas->addRow();
        $tblIdentitas->addCell(2200, ['bgColor' => 'F1F5F9'])->addText('Guru Pengampu', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(4000)->addText($perangkat->guru?->nama ?? '-', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(1600, ['bgColor' => 'F1F5F9'])->addText('Semester', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(1800)->addText($perangkat->semester == 1 ? '1 (Ganjil)' : '2 (Genap)', ['size' => 10]);

        $tblIdentitas->addRow();
        $tblIdentitas->addCell(2200, ['bgColor' => 'F1F5F9'])->addText('NIP / NUPTK', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(4000)->addText($perangkat->guru?->nip ?? '-', ['size' => 10]);
        $tblIdentitas->addCell(1600, ['bgColor' => 'F1F5F9'])->addText('Status', ['bold' => true, 'size' => 10]);
        $tblIdentitas->addCell(1800)->addText(ucfirst($perangkat->status ?? 'Draft'), ['bold' => true, 'size' => 10]);

        $section->addTextBreak(1);

        // 4. CAPAIAN PEMBELAJARAN
        $section->addText('A. Capaian Pembelajaran Fase ' . $perangkat->fase, ['bold' => true, 'size' => 11], ['spaceBefore' => 60, 'spaceAfter' => 40]);
        $cpParagraph = preg_replace('/\s*\n\s*/', ' ', $perangkat->resolved_cp);
        $section->addText($cpParagraph ?: '—', ['size' => 10.5], ['alignment' => Jc::BOTH, 'spaceAfter' => 60]);

        // 5. RASIONAL (jika ada)
        if (!empty($perangkat->rasional_tujuan)) {
            $section->addText('B. Rasional & Tujuan Mata Pelajaran', ['bold' => true, 'size' => 11], ['spaceBefore' => 60, 'spaceAfter' => 40]);
            $rasionalParagraph = preg_replace('/\s*\n\s*/', ' ', $perangkat->rasional_tujuan);
            $section->addText($rasionalParagraph, ['size' => 10.5], ['alignment' => Jc::BOTH, 'spaceAfter' => 60]);
        }

        // 6. ELEMEN KOMPETENSI CP
        $sectionLabel = !empty($perangkat->rasional_tujuan) ? 'C.' : 'B.';
        $section->addText($sectionLabel . ' Elemen Kompetensi Capaian Pembelajaran', ['bold' => true, 'size' => 11], ['spaceBefore' => 60, 'spaceAfter' => 40]);

        $elemenList = !empty($perangkat->elemen_cp) && is_array($perangkat->elemen_cp) && count($perangkat->elemen_cp) > 0
            ? $perangkat->elemen_cp
            : $perangkat->resolved_elemen_cp;

        if (!empty($elemenList) && count($elemenList) > 0) {
            $tblElem = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50, 'width' => 100 * 50, 'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT]);
            $tblElem->addRow();
            $tblElem->addCell(500, ['bgColor' => 'DCE8F5'])->addText('No', ['bold' => true, 'size' => 9.5], ['alignment' => Jc::CENTER]);
            $tblElem->addCell(2500, ['bgColor' => 'DCE8F5'])->addText('Nama Elemen CP', ['bold' => true, 'size' => 9.5], ['alignment' => Jc::CENTER]);
            $tblElem->addCell(6600, ['bgColor' => 'DCE8F5'])->addText('Deskripsi Capaian Pembelajaran Elemen', ['bold' => true, 'size' => 9.5], ['alignment' => Jc::CENTER]);

            foreach ($elemenList as $idx => $elem) {
                $tblElem->addRow();
                $namaEl = is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem;
                $deskEl = is_array($elem) ? ($elem['deskripsi'] ?? $elem['capaian'] ?? '-') : '-';
                $tblElem->addCell(500)->addText((string)($idx + 1), ['size' => 9.5], ['alignment' => Jc::CENTER]);
                $tblElem->addCell(2500)->addText($namaEl, ['bold' => true, 'size' => 9.5]);
                $tblElem->addCell(6600)->addText($deskEl, ['size' => 9.5], ['alignment' => Jc::BOTH]);
            }
        }

        $section->addTextBreak(2);

        // 7. LEMBAR PENGESAHAN & TTD
        $tblTtd = $section->addTable(['borderSize' => 0, 'cellMargin' => 0, 'width' => 100 * 50, 'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT]);
        $tblTtd->addRow();
        $cellKepsek = $tblTtd->addCell(5000);
        $cellKepsek->addText('Mengetahui,', ['size' => 10]);
        $cellKepsek->addText('Kepala SMK Negeri 1 Air Naningan', ['size' => 10]);
        $cellKepsek->addTextBreak(3);
        $cellKepsek->addText($sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.', ['bold' => true, 'underline' => 'single', 'size' => 10]);
        $cellKepsek->addText('NIP. ' . ($sekolah->nip_kepala_sekolah ?? '197904172008012019'), ['size' => 10]);

        $cellGuru = $tblTtd->addCell(5000);
        $cellGuru->addText('Air Naningan, ' . \Carbon\Carbon::parse($perangkat->tanggal_pengesahan ?? now())->translatedFormat('d F Y'), ['size' => 10]);
        $cellGuru->addText('Guru Pengampu Mata Pelajaran,', ['size' => 10]);
        $cellGuru->addTextBreak(3);
        $cellGuru->addText($perangkat->guru?->nama ?? '-', ['bold' => true, 'underline' => 'single', 'size' => 10]);
        $cellGuru->addText('NIP. ' . ($perangkat->guru?->nip ?? '-'), ['size' => 10]);

        $filename = 'CP-' . Str::slug($perangkat->mataPelajaran?->nama_mapel ?? 'mapel') . '-Kelas' . $perangkat->tingkat . '-Fase' . $perangkat->fase . '-Smt' . $perangkat->semester . '.docx';

        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpPath);

        return Response::download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    /**
     * ================================================================
     * EXPORT PDF — Seluruh Dokumen Perangkat Pembelajaran A4
     * ================================================================
     */
    public function exportPdf($id)
    {
        $perangkat = AkademikPerangkatAjar::with([
            'guru', 'mataPelajaran', 'distribusiMengajar.rombel',
            'tahunAjaran', 'atpItems', 'modulAjars', 'kktpItems.atpItem', 'validator'
        ])->findOrFail($id);

        $sekolah = PengaturanSekolah::getAktif();
        $logos   = $this->resolveKopLogos($sekolah);
        $logoProvBase64    = $logos['logoProvBase64'];
        $logoSekolahBase64 = $logos['logoSekolahBase64'];

        $qrToken    = $perangkat->generateQrToken();
        $atpItems   = $perangkat->atpItems()->orderBy('urutan')->get();
        $modulAjars = $perangkat->modulAjars()->with('atpItem')->orderBy('pertemuan_ke_mulai')->get();
        $kktpItems  = $perangkat->kktpItems()->with('atpItem')->get();

        $pdf = Pdf::loadView('dcc.akademik.perangkat.export.pdf_lengkap', compact(
            'perangkat', 'sekolah', 'qrToken', 'atpItems', 'modulAjars', 'kktpItems',
            'logoProvBase64', 'logoSekolahBase64'
        ))->setPaper('a4', 'portrait');

        $filename = 'Perangkat-' . Str::slug($perangkat->mataPelajaran?->nama_mapel ?? 'mapel') . '-Kelas' . $perangkat->tingkat . '-Smt' . $perangkat->semester . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * ================================================================
     * EXPORT DOCX — Seluruh Dokumen Perangkat Pembelajaran A4 (PhpWord)
     * ================================================================
     */
    public function exportDocx($id)
    {
        $perangkat = AkademikPerangkatAjar::with([
            'guru', 'mataPelajaran', 'distribusiMengajar.rombel',
            'tahunAjaran', 'atpItems', 'modulAjars', 'kktpItems.atpItem', 'validator'
        ])->findOrFail($id);

        $sekolah    = PengaturanSekolah::getAktif();
        $logos      = $this->resolveKopLogos($sekolah);
        $namaSekolah= $sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN';
        $atpItems   = $perangkat->atpItems()->orderBy('urutan')->get();
        $modulAjars = $perangkat->modulAjars()->with('atpItem')->orderBy('pertemuan_ke_mulai')->get();
        $kktpItems  = $perangkat->kktpItems()->with('atpItem')->get();

        // ---- Inisialisasi PhpWord ----
        $phpWord = new PhpWord();
        $phpWord->getSettings()->setUpdateFields(true);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // Page layout A4
        $sectionStyle = [
            'marginTop'    => 1134,
            'marginBottom' => 1134,
            'marginLeft'   => 1418,
            'marginRight'  => 1134,
            'paperSize'    => 'A4',
        ];

        // Helper: Judul Section
        $addJudul = function ($section, $text) {
            $section->addText($text, ['bold' => true, 'size' => 13, 'underline' => 'single'], ['alignment' => Jc::CENTER]);
            $section->addTextBreak(1);
        };

        // Helper: Sub-judul
        $addSub = function ($section, $text) {
            $section->addText($text, ['bold' => true, 'size' => 11.5], ['spaceBefore' => 100, 'spaceAfter' => 50]);
        };

        // Helper: Paragraf teks biasa
        $addPara = function ($section, $text) {
            $cleanText = preg_replace('/\s*\n\s*/', ' ', $text);
            $section->addText($cleanText ?: '—', ['size' => 11], ['alignment' => Jc::BOTH, 'spaceBefore' => 30, 'spaceAfter' => 30]);
        };

        // ---- HALAMAN SAMPUL ----
        $section = $phpWord->addSection($sectionStyle);
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], false);

        $section->addTextBreak(2);
        $section->addText('PERANGKAT PEMBELAJARAN', ['bold' => true, 'size' => 18], ['alignment' => Jc::CENTER]);
        $section->addText('Kurikulum Merdeka (Kepmendikbudristek No. 12/2024)', ['size' => 12], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);
        $section->addText($perangkat->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran', ['bold' => true, 'size' => 16], ['alignment' => Jc::CENTER]);
        $section->addText('Kelas ' . $perangkat->tingkat . ' / Fase ' . $perangkat->fase, ['size' => 13], ['alignment' => Jc::CENTER]);
        $section->addText('Semester ' . ($perangkat->semester == 1 ? 'Ganjil' : 'Genap'), ['size' => 12], ['alignment' => Jc::CENTER]);
        $section->addText('Tahun Ajaran ' . ($perangkat->tahunAjaran?->nama ?? date('Y') . '/' . (date('Y') + 1)), ['size' => 12], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(3);
        $section->addText('Guru Pengampu : ' . ($perangkat->guru?->nama ?? '-'), ['bold' => true, 'size' => 12], ['alignment' => Jc::CENTER]);
        $section->addText('NIP : ' . ($perangkat->guru?->nip ?? '-'), ['size' => 12], ['alignment' => Jc::CENTER]);

        // ---- BAB I: CP ----
        $section = $phpWord->addSection($sectionStyle);
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], true);
        $addJudul($section, 'BAB I — CAPAIAN PEMBELAJARAN (CP)');

        $addSub($section, 'A. Identitas Mata Pelajaran');
        $section->addText('Mata Pelajaran : ' . $perangkat->mataPelajaran?->nama_mapel, ['size' => 11]);
        $section->addText('Kode Mapel     : ' . $perangkat->mataPelajaran?->kode_mapel, ['size' => 11]);
        $section->addText('Kelas / Fase   : ' . $perangkat->tingkat . ' / Fase ' . $perangkat->fase, ['size' => 11]);
        $section->addTextBreak(1);

        $addSub($section, 'B. Capaian Pembelajaran');
        $addPara($section, $perangkat->capaian_pembelajaran ?? $perangkat->mataPelajaran?->capaian_pembelajaran_fase_e ?? '—');

        if (!empty($perangkat->rasional_tujuan)) {
            $addSub($section, 'C. Rasional & Tujuan Pembelajaran');
            $addPara($section, $perangkat->rasional_tujuan);
        }

        $elemenCp = is_array($perangkat->elemen_cp) ? $perangkat->elemen_cp : (json_decode($perangkat->elemen_cp ?? '[]', true) ?? []);
        if (count($elemenCp) > 0) {
            $addSub($section, 'D. Elemen Kompetensi CP');
            $tblElemen = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            $tblElemen->addRow(); $tblElemen->addCell(2000)->addText('Nama Elemen', ['bold' => true]); $tblElemen->addCell(6000)->addText('Deskripsi', ['bold' => true]);
            foreach ($elemenCp as $el) {
                $tblElemen->addRow();
                $tblElemen->addCell(2000)->addText($el['nama'] ?? '-', ['bold' => true]);
                $tblElemen->addCell(6000)->addText($el['deskripsi'] ?? '-');
            }
        }

        // ---- BAB II: ATP ----
        $section = $phpWord->addSection($sectionStyle);
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], true);
        $addJudul($section, 'BAB II — ALUR TUJUAN PEMBELAJARAN (ATP)');
        $addSub($section, $perangkat->mataPelajaran?->nama_mapel . ' | Kelas ' . $perangkat->tingkat . ' Semester ' . ($perangkat->semester == 1 ? 'Ganjil' : 'Genap'));

        if ($atpItems->count() > 0) {
            $tblAtp = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            $tblAtp->addRow();
            foreach (['No', 'Kode TP', 'Tujuan Pembelajaran', 'Materi Pokok', 'JP'] as $h) {
                $tblAtp->addCell(null)->addText($h, ['bold' => true]);
            }
            $no = 1;
            foreach ($atpItems as $atp) {
                $tblAtp->addRow();
                $tblAtp->addCell(400)->addText($no++);
                $tblAtp->addCell(900)->addText($atp->kode_tp);
                $tblAtp->addCell(3500)->addText($atp->tujuan_pembelajaran);
                $tblAtp->addCell(2500)->addText($atp->materi_pokok);
                $tblAtp->addCell(500)->addText($atp->alokasi_jp);
            }
            $tblAtp->addRow();
            $cell = $tblAtp->addCell(7300, ['gridSpan' => 4]);
            $cell->addText('Total JP:', ['bold' => true], ['alignment' => Jc::RIGHT]);
            $tblAtp->addCell(500)->addText($atpItems->sum('alokasi_jp'), ['bold' => true]);
        } else {
            $section->addText('Belum ada butir ATP.', ['italic' => true, 'color' => '666666']);
        }

        // ---- BAB III: PROTA/PROMES ----
        $section = $phpWord->addSection($sectionStyle);
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], true);
        $addJudul($section, 'BAB III — PROGRAM TAHUNAN & PROGRAM SEMESTER');

        $rpeEfektif  = $perangkat->rpe_pekan_efektif ?? 18;
        $rpeCadangan = $perangkat->rpe_pekan_cadangan ?? 2;
        $jamMinggu   = $perangkat->distribusiMengajar?->total_jam_per_minggu ?? 4;
        $totalJpSmt  = $rpeEfektif * $jamMinggu;

        $addSub($section, 'A. Rincian Pekan Efektif (RPE)');
        $section->addText("Pekan Efektif         : {$rpeEfektif} pekan", ['size' => 11]);
        $section->addText("Pekan Cadangan        : {$rpeCadangan} pekan", ['size' => 11]);
        $section->addText("JP per Minggu         : {$jamMinggu} JP", ['size' => 11]);
        $section->addText("Total JP Tersedia     : {$totalJpSmt} JP", ['bold' => true, 'size' => 11]);
        $section->addText("Total JP Terpakai ATP : " . $atpItems->sum('alokasi_jp') . " JP", ['size' => 11]);
        $section->addTextBreak(1);

        $addSub($section, 'B. Program Semester — Distribusi Materi');
        if ($atpItems->count() > 0) {
            $tblPrm = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            $tblPrm->addRow();
            foreach (['No', 'Materi Pokok', 'JP', 'Ket.'] as $h) { $tblPrm->addCell(null)->addText($h, ['bold' => true]); }
            $no = 1;
            foreach ($atpItems as $atp) {
                $tblPrm->addRow();
                $tblPrm->addCell(400)->addText($no++);
                $tblPrm->addCell(5500)->addText($atp->materi_pokok);
                $tblPrm->addCell(500)->addText($atp->alokasi_jp);
                $tblPrm->addCell(1400)->addText('—');
            }
        }

        // ---- BAB IV: MODUL AJAR ----
        $section = $phpWord->addSection($sectionStyle);
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], true);
        $addJudul($section, 'BAB IV — MODUL AJAR / RPP MERDEKA');

        if ($modulAjars->count() > 0) {
            $mNo = 1;
            foreach ($modulAjars as $modul) {
                $addSub($section, "Modul {$mNo}: {$modul->judul_modul}");
                $section->addText('Pertemuan ke-' . $modul->pertemuan_ke_mulai . ' s.d. ' . $modul->pertemuan_ke_selesai . ' | ' . $modul->alokasi_jp . ' JP', ['size' => 11]);
                $section->addText('Model: ' . ($modul->model_pembelajaran ?? '-') . ' | Metode: ' . ($modul->metode_pembelajaran ?? '-'), ['size' => 11]);
                if ($modul->pemahaman_bermakna) { $section->addText('Pemahaman Bermakna:', ['bold' => true, 'size' => 11]); $addPara($section, $modul->pemahaman_bermakna); }
                if ($modul->pertanyaan_pemantik) { $section->addText('Pertanyaan Pemantik:', ['bold' => true, 'size' => 11]); $addPara($section, $modul->pertanyaan_pemantik); }
                if ($modul->kegiatan_pendahuluan) { $section->addText('Kegiatan Pendahuluan:', ['bold' => true, 'size' => 11]); $addPara($section, $modul->kegiatan_pendahuluan); }
                if ($modul->kegiatan_inti) { $section->addText('Kegiatan Inti:', ['bold' => true, 'size' => 11]); $addPara($section, $modul->kegiatan_inti); }
                if ($modul->kegiatan_penutup) { $section->addText('Kegiatan Penutup:', ['bold' => true, 'size' => 11]); $addPara($section, $modul->kegiatan_penutup); }
                $section->addTextBreak(1);
                $mNo++;
            }
        } else {
            $section->addText('Belum ada Modul Ajar.', ['italic' => true, 'color' => '666666']);
        }

        // ---- BAB V: KKTP ----
        $section = $phpWord->addSection($sectionStyle);
        $this->addWordKopSurat($section, $sekolah, $logos['provPath'], $logos['sekolahPath'], true);
        $addJudul($section, 'BAB V — KRITERIA KETERCAPAIAN TUJUAN PEMBELAJARAN (KKTP)');

        if ($kktpItems->count() > 0) {
            $tblKktp = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            $tblKktp->addRow();
            foreach (['No', 'Tujuan Pembelajaran', 'Pendekatan', 'Kriteria Tuntas', 'Remedial'] as $h) {
                $tblKktp->addCell(null)->addText($h, ['bold' => true]);
            }
            $no = 1;
            foreach ($kktpItems as $kktp) {
                $tblKktp->addRow();
                $tblKktp->addCell(400)->addText($no++);
                $tblKktp->addCell(2800)->addText(($kktp->atpItem?->kode_tp ?? '') . ': ' . Str::limit($kktp->atpItem?->tujuan_pembelajaran, 60));
                $tblKktp->addCell(1200)->addText(str_replace('_', ' ', $kktp->pendekatan));
                $tblKktp->addCell(1800)->addText($kktp->keterangan_tuntas ?? '—');
                $tblKktp->addCell(1600)->addText($kktp->keterangan_remedial ?? '—');
            }
        } else {
            $section->addText('Belum ada KKTP yang diisi.', ['italic' => true, 'color' => '666666']);
        }

        // ---- GENERATE & DOWNLOAD ----
        $filename = 'Perangkat-' . Str::slug($perangkat->mataPelajaran?->nama_mapel ?? 'mapel') . '-Kelas' . $perangkat->tingkat . '-Smt' . $perangkat->semester . '.docx';
        $tmpPath  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpPath);

        return Response::download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
