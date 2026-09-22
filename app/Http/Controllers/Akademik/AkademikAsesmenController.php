<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikAsesmenSoal;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikMataPelajaran;
use App\Models\AkademikNilai;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikAsesmenController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        $guruId = $user?->guru_id;

        $distribusiQuery = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel', 'guru'])
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $distribusiQuery->where('guru_id', $guruId);
        }
        $rawDistribusi = $distribusiQuery->get();

        // Dapatkan Mapel & Rombel unik yang diampu (tanpa duplikasi semester)
        $filterMapels = $rawDistribusi->pluck('mataPelajaran')->filter()->unique('id')->sortBy('nama_mapel')->values();
        $filterRombels = $rawDistribusi->pluck('rombel')->filter()->unique('id')->sortBy('nama_rombel')->values();

        $query = AkademikAsesmenOnline::with(['distribusi.guru', 'distribusi.mataPelajaran', 'distribusi.rombel'])
            ->withCount(['soals', 'hasils']);

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $query->whereHas('distribusi', fn($q) => $q->where('guru_id', $guruId));
        }

        if ($request->filled('mapel_id')) {
            $query->whereHas('distribusi', fn($q) => $q->where('mata_pelajaran_id', $request->mapel_id));
        }

        if ($request->filled('rombel_id')) {
            $rombelId = (int) $request->rombel_id;
            $query->where(function($q) use ($rombelId) {
                $q->whereHas('distribusi', fn($d) => $d->where('rombel_id', $rombelId))
                  ->orWhereJsonContains('target_rombel_ids', $rombelId);
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $asesmens = $query->latest()->paginate(15)->withQueryString();

        return view('dcc.akademik.asesmen.index', compact('asesmens', 'filterMapels', 'filterRombels', 'ta'));
    }

    public function create(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        $guruId = $user?->guru_id;

        $distribusiQuery = AkademikDistribusiMengajar::with(['mataPelajaran'])
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $distribusiQuery->where('guru_id', $guruId);
        }
        $distribusis = $distribusiQuery->get();

        // Mapel unik yang diampu guru
        $mapels = $distribusis->pluck('mataPelajaran')->filter()->unique('id')->sortBy('nama_mapel')->values();

        return view('dcc.akademik.asesmen.create', compact('mapels', 'ta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:akademik_mata_pelajarans,id',
            'judul' => 'required|string|max:255',
            'jenis' => 'required|in:kuis,ulangan_harian,pts,pas,tugas',
            'target_jumlah_soal' => 'required|integer|min:1|max:200',
            'passing_grade' => 'required|integer|min:0|max:100',
        ]);

        $user = auth()->user();
        $guruId = $user?->guru_id;
        $ta = TahunAjaran::where('is_active', true)->first();

        // Cari record distribusi yang valid untuk guru dan mapel ini
        $distribusi = AkademikDistribusiMengajar::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->when($guruId && !$user->isAdmin() && !$user->isWakaKurikulum(), fn($q) => $q->where('guru_id', $guruId))
            ->first();

        if (!$distribusi) {
            $distribusi = AkademikDistribusiMengajar::where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
                ->firstOrFail();
        }

        $asesmen = AkademikAsesmenOnline::create([
            'distribusi_id' => $distribusi->id,
            'target_rombel_ids' => null,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'target_jumlah_soal' => (int) $request->target_jumlah_soal,
            'passing_grade' => (int) $request->passing_grade,
            'semester' => $distribusi->semester ?? 1,
            'durasi_menit' => 60,
            'target_tipe' => 'rombel',
            'status_validasi' => 'draft',
            'is_active' => false,
            'acak_soal' => true,
            'acak_opsi' => true,
            'anti_cheat_mode' => true,
            'wajib_fullscreen' => true,
            'blokir_copy_paste' => true,
            'max_toleransi_keluar' => 3,
        ]);

        return redirect()->route('akademik.asesmen.soal', $asesmen->id)
            ->with('success', 'Paket asesmen berhasil dibuat! Silakan mulai menyusun butir soal pada langkah ini.');
    }

    protected function authorizeAsesmen(AkademikAsesmenOnline $asesmen): void
    {
        $user = auth()->user();
        if ($user->isAdmin() || $user->isWakaKurikulum() || $user->isKaprog()) {
            return;
        }

        if ($user->guru_id && $asesmen->distribusi?->guru_id === $user->guru_id) {
            return;
        }

        abort(403, 'Akses Ditolak: Anda hanya memiliki hak akses untuk mengelola asesmen pada mata pelajaran yang Anda ampu.');
    }

    public function soal($id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.rombel', 'soals', 'validator'])->findOrFail($id);
        $this->authorizeAsesmen($asesmen);
        $totalBobot = $asesmen->soals->sum('bobot');
        $nomorBerikutnya = ($asesmen->soals->max('nomor') ?? 0) + 1;
        $auditKelayakan = $asesmen->cekKelayakanSoal();

        return view('dcc.akademik.asesmen.soal', compact('asesmen', 'totalBobot', 'nomorBerikutnya', 'auditKelayakan'));
    }

    public function storeSoal(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        $request->validate([
            'pertanyaan' => 'required|string',
            'tipe' => 'required|in:pilihan_ganda,essay,benar_salah',
            'opsi_a' => 'required_if:tipe,pilihan_ganda|nullable|string',
            'opsi_b' => 'required_if:tipe,pilihan_ganda|nullable|string',
            'opsi_c' => 'nullable|string',
            'opsi_d' => 'nullable|string',
            'opsi_e' => 'nullable|string',
            'kunci_jawaban' => 'required|string|max:5',
            'bobot' => 'required|integer|min:1',
            'pembahasan' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'gambar_url' => 'nullable|string',
        ]);

        // Handle unggah file gambar atau URL gambar
        $gambarUrl = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('asesmen_soal', 'public');
            $gambarUrl = '/storage/' . $path;
        } elseif ($request->filled('gambar_url')) {
            $gambarUrl = $request->gambar_url;
        }

        // Validasi Kunci Jawaban harus memiliki teks pilihan yang terisi
        if ($request->tipe === 'pilihan_ganda') {
            $kunci = strtoupper($request->kunci_jawaban);
            $kunciField = 'opsi_' . strtolower($kunci);
            if (empty(trim($request->input($kunciField) ?? ''))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Gagal Menyimpan: Anda memilih Kunci Jawaban '{$kunci}', tetapi teks Pilihan Jawaban {$kunci} masih kosong!");
            }
        }

        $nomor = ($asesmen->soals()->max('nomor') ?? 0) + 1;

        AkademikAsesmenSoal::create([
            'asesmen_id' => $asesmen->id,
            'nomor' => $nomor,
            'pertanyaan' => $request->pertanyaan,
            'tipe' => $request->tipe,
            'gambar_url' => $gambarUrl,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'kunci_jawaban' => strtoupper($request->kunci_jawaban),
            'bobot' => $request->bobot,
            'pembahasan' => $request->pembahasan,
        ]);

        // Auto-update status validasi jika paket sudah memenuhi syarat
        $audit = $asesmen->fresh()->cekKelayakanSoal();
        if ($audit['is_valid'] && $asesmen->status_validasi === 'draft') {
            $asesmen->update(['status_validasi' => 'siap_diujikan']);
        }

        return redirect()->back()->with('success', 'Butir soal no. ' . $nomor . ' berhasil divalidasi dan disimpan.');
    }

    public function destroySoal($id, $soalId)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);
        $soal = AkademikAsesmenSoal::where('asesmen_id', $id)->findOrFail($soalId);
        $soal->delete();

        // Re-check validasi setelah hapus
        $audit = $asesmen->fresh()->cekKelayakanSoal();
        if (!$audit['is_valid'] && $asesmen->status_validasi === 'siap_diujikan') {
            $asesmen->update(['status_validasi' => 'draft', 'is_active' => false]);
        }

        return redirect()->back()->with('success', 'Butir soal berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        // Jika akan diaktifkan, wajib lolos validasi kelayakan soal
        if (!$asesmen->is_active) {
            $audit = $asesmen->cekKelayakanSoal();
            if (!$audit['is_valid']) {
                $firstError = $audit['errors'][0] ?? 'Butir soal belum memenuhi standar kelayakan asesmen.';
                return redirect()->back()->with('error', "Gagal Mengaktifkan: {$firstError}");
            }
            $asesmen->status_validasi = 'siap_diujikan';
        }

        $asesmen->is_active = !$asesmen->is_active;
        $asesmen->save();

        $statusStr = $asesmen->is_active ? 'diaktifkan dan siap diakses siswa' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Asesmen berhasil {$statusStr}.");
    }

    public function validasiSoal(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        $request->validate([
            'status_validasi' => 'required|in:draft,siap_diujikan,perlu_revisi',
            'catatan_validasi' => 'nullable|string',
        ]);

        $audit = $asesmen->cekKelayakanSoal();
        if ($request->status_validasi === 'siap_diujikan' && !$audit['is_valid']) {
            $firstError = $audit['errors'][0] ?? 'Butir soal belum lengkap.';
            return redirect()->back()->with('error', "Validasi Ditolak: {$firstError}");
        }

        $asesmen->update([
            'status_validasi' => $request->status_validasi,
            'catatan_validasi' => $request->catatan_validasi,
            'divalidasi_oleh' => auth()->id(),
            'divalidasi_pada' => now(),
        ]);

        return redirect()->back()->with('success', 'Status validasi & telaah soal berhasil diperbarui.');
    }

    /**
     * Langkah 3: Sesi Penugasan Rombel / Select Siswa Remedial & Pengaturan Ujian
     */
    public function penugasan($id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.rombel', 'soals'])->findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        $audit = $asesmen->cekKelayakanSoal();
        if (!$audit['is_valid']) {
            $firstError = $audit['errors'][0] ?? 'Lengkapi butir soal terlebih dahulu.';
            return redirect()->route('akademik.asesmen.soal', $asesmen->id)
                ->with('error', "Belum Dapat Masuk Penugasan: {$firstError}");
        }

        $user = auth()->user();
        $guruId = $user?->guru_id;
        $ta = TahunAjaran::where('is_active', true)->first();

        // Dapatkan semua rombel yang diajar oleh guru untuk mata pelajaran ini
        $mapelId = $asesmen->distribusi?->mata_pelajaran_id;
        $availableRombels = AkademikDistribusiMengajar::with('rombel')
            ->where('mata_pelajaran_id', $mapelId)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->when($guruId && !$user->isAdmin() && !$user->isWakaKurikulum(), fn($q) => $q->where('guru_id', $guruId))
            ->get()
            ->pluck('rombel')
            ->filter()
            ->unique('id')
            ->sortBy('nama_rombel')
            ->values();

        $currentTargetRombelIds = !empty($asesmen->target_rombel_ids)
            ? array_map('intval', (array) $asesmen->target_rombel_ids)
            : $availableRombels->pluck('id')->all();

        // Siswa dari semua rombel sasaran, dikelompokkan per rombel
        $allRombelIds = $availableRombels->pluck('id')->values();
        $siswasPerRombel = Siswa::whereHas('rombels', fn($q) => $q->whereIn('rombels.id', $allRombelIds))
            ->with(['rombels' => fn($q) => $q->whereIn('rombels.id', $allRombelIds)])
            ->whereIn('status', ['aktif', 'pkl'])
            ->orderBy('nama')
            ->get(['id', 'nama', 'nisn'])
            ->groupBy(fn($s) => $s->rombels->first()?->id);

        return view('dcc.akademik.asesmen.penugasan', compact(
            'asesmen', 'availableRombels', 'currentTargetRombelIds', 'siswasPerRombel'
        ));
    }

    public function storePenugasan(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        $request->validate([
            'target_rombel_ids' => 'required|array|min:1',
            'target_rombel_ids.*' => 'required|exists:rombels,id',
            'durasi_menit' => 'required|integer|min:5|max:300',
            'passing_grade' => 'required|integer|min:0|max:100',
            'max_toleransi_keluar' => 'nullable|integer|min:1|max:10',
            'dibuka_pada' => 'nullable|date',
            'ditutup_pada' => 'nullable|date|after_or_equal:dibuka_pada',
            'token_ujian' => 'nullable|string|max:10',
            'target_tipe' => 'required|in:rombel,siswa_terpilih',
            'target_siswa_ids' => 'nullable|array',
            'aksi' => 'required|in:publish,draft',
        ]);

        $audit = $asesmen->cekKelayakanSoal();
        if (!$audit['is_valid']) {
            $firstError = $audit['errors'][0] ?? 'Butir soal belum lengkap.';
            return redirect()->route('akademik.asesmen.soal', $asesmen->id)
                ->with('error', "Gagal Menyimpan Penugasan: {$firstError}");
        }

        $targetRombelIds = array_map('intval', (array) $request->target_rombel_ids);
        $targetTipe = $request->input('target_tipe', 'rombel');
        $targetSiswaIds = null;
        if ($targetTipe === 'siswa_terpilih') {
            $rawIds = $request->input('target_siswa_ids', []);
            if (empty($rawIds)) {
                return redirect()->back()->withInput()->with('error', 'Anda memilih mode penugasan "Siswa Tertentu", tetapi belum mencentang satu siswa pun!');
            }
            $targetSiswaIds = array_map('intval', (array) $rawIds);
        }

        $isActive = ($request->aksi === 'publish');

        $asesmen->update([
            'target_rombel_ids' => $targetRombelIds,
            'target_tipe' => $targetTipe,
            'target_siswa_ids' => $targetSiswaIds,
            'durasi_menit' => $request->durasi_menit,
            'dibuka_pada' => $request->dibuka_pada,
            'ditutup_pada' => $request->ditutup_pada,
            'token_ujian' => $request->filled('token_ujian') ? strtoupper(trim($request->token_ujian)) : null,
            'passing_grade' => $request->passing_grade,
            'anti_cheat_mode' => $request->boolean('anti_cheat_mode'),
            'wajib_fullscreen' => $request->boolean('wajib_fullscreen'),
            'blokir_copy_paste' => $request->boolean('blokir_copy_paste'),
            'acak_soal' => $request->boolean('acak_soal'),
            'acak_opsi' => $request->boolean('acak_opsi'),
            'tampilkan_nilai' => $request->boolean('tampilkan_nilai'),
            'tampilkan_pembahasan' => $request->boolean('tampilkan_pembahasan'),
            'max_toleransi_keluar' => $request->input('max_toleransi_keluar', 3),
            'status_validasi' => 'siap_diujikan',
            'is_active' => $isActive,
        ]);

        $msg = $isActive 
            ? 'Selamat! Asesmen berhasil diterbitkan dan sesi ujian telah AKTIF untuk siswa di rombel terpilih.' 
            : 'Sesi penugasan asesmen berhasil disimpan sebagai Draft Terjadwal.';

        return redirect()->route('akademik.asesmen.index')->with('success', $msg);
    }

    public function hasil(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.rombel', 'soals'])->findOrFail($id);

        $hasils = AkademikAsesmenHasil::with('siswa')
            ->where('asesmen_id', $asesmen->id)
            ->get()
            ->keyBy('siswa_id');

        $targetRombelIds = $asesmen->target_rombel_ids ?? [$asesmen->distribusi?->rombel_id];
        $targetRombelIds = array_map('intval', (array) $targetRombelIds);

        $targetRombels = Rombel::whereIn('id', $targetRombelIds)->orderBy('nama_rombel')->get();

        $selectedRombelId = $request->get('rombel_id');
        $filterRombelIds = $selectedRombelId ? [(int) $selectedRombelId] : $targetRombelIds;

        $siswas = Siswa::whereHas('rombels', fn($q) => $q->whereIn('rombels.id', $filterRombelIds))
            ->with(['rombels' => fn($q) => $q->whereIn('rombels.id', $targetRombelIds)])
            ->whereIn('status', ['aktif', 'pkl'])
            ->orderBy('nama')
            ->get();

        $totalSiswa = $siswas->count();
        $totalMengerjakan = $hasils->where('is_selesai', true)->count();
        $rataRata = $hasils->where('is_selesai', true)->avg('nilai') ?? 0;
        $tuntas = $hasils->where('is_selesai', true)->where('nilai', '>=', $asesmen->passing_grade)->count();
        $totalMelanggar = $hasils->where('jumlah_pelanggaran', '>', 0)->count();
        $totalCurang = $hasils->where('status_kejujuran', 'terindikasi_curang')->count();

        return view('dcc.akademik.asesmen.hasil', compact(
            'asesmen', 'siswas', 'hasils', 'totalSiswa', 'totalMengerjakan', 'rataRata', 'tuntas', 'totalMelanggar', 'totalCurang', 'targetRombels', 'selectedRombelId'
        ));
    }

    public function pushToNilai(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::with('distribusi')->findOrFail($id);
        $this->authorizeAsesmen($asesmen);
        $hasils = AkademikAsesmenHasil::with('siswa.rombels')->where('asesmen_id', $asesmen->id)->where('is_selesai', true)->get();

        if ($hasils->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada siswa yang menyelesaikan asesmen ini.');
        }

        $jenisNilai = in_array($asesmen->jenis, ['pts', 'pas']) ? 'sumatif' : 'formatif';

        // Petakan distribusi_id per rombel untuk mapel ini
        $mapelId = $asesmen->distribusi->mata_pelajaran_id;
        $distribusisByRombel = AkademikDistribusiMengajar::where('mata_pelajaran_id', $mapelId)
            ->where('tahun_ajaran_id', $asesmen->distribusi->tahun_ajaran_id)
            ->pluck('id', 'rombel_id');

        foreach ($hasils as $h) {
            $studentRombelId = $h->siswa?->rombels?->first()?->id;
            $distId = $distribusisByRombel[$studentRombelId] ?? $asesmen->distribusi_id;

            AkademikNilai::updateOrCreate(
                [
                    'distribusi_id' => $distId,
                    'siswa_id' => $h->siswa_id,
                    'semester' => $asesmen->semester,
                    'nama_penilaian' => $asesmen->judul,
                ],
                [
                    'jenis_penilaian' => $jenisNilai,
                    'nilai' => $h->nilai,
                    'deskripsi_capaian' => "Hasil asesmen online ({$asesmen->judul}): Skor {$h->nilai}",
                ]
            );
        }

        return redirect()->back()->with('success', "Nilai dari {$hasils->count()} siswa berhasil ditransfer ke Buku Nilai & Leger Siswa untuk masing-masing rombel.");
    }

    /**
     * Tampilan pengerjaan simulasi/interaktif asesmen dengan CBT Anti-Kecurangan
     */
    public function kerjakan(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'soals'])->findOrFail($id);

        if (!$asesmen->is_active) {
            return redirect()->route('akademik.asesmen.index')->with('error', 'Asesmen online ini sedang tidak aktif.');
        }

        $user = auth()->user();
        $siswa = null;

        $targetRombelIds = $asesmen->target_rombel_ids ?? [$asesmen->distribusi?->rombel_id];
        $targetRombelIds = array_map('intval', (array) $targetRombelIds);

        if ($user?->siswa_id) {
            $siswa = Siswa::with('rombels')->find($user->siswa_id);

            // Validasi apakah rombel siswa termasuk dalam rombel sasaran
            if ($siswa) {
                $studentRombelIds = $siswa->rombels->pluck('id')->all();
                if (!empty($targetRombelIds) && empty(array_intersect($studentRombelIds, $targetRombelIds))) {
                    return redirect()->route('akademik.asesmen.index')
                        ->with('error', 'Akses Ditolak: Asesmen ini tidak ditugaskan untuk rombel kelas Anda.');
                }
            }

            // Cek batasan penugasan jika mode adalah siswa terpilih (Remedial / Susulan)
            if ($asesmen->target_tipe === 'siswa_terpilih' && !empty($asesmen->target_siswa_ids)) {
                $targetSiswaIds = array_map('intval', (array) $asesmen->target_siswa_ids);
                if (!in_array((int) $siswa->id, $targetSiswaIds)) {
                    return redirect()->route('akademik.asesmen.index')
                        ->with('error', 'Akses Ditolak: Anda tidak terdaftar dalam sesi penugasan ini (Sesi Khusus Remedial / Susulan).');
                }
            }
        }

        // Mode preview / simulasi untuk guru dan admin
        if (!$siswa) {
            $siswa = Siswa::whereHas('rombels', fn($q) => $q->whereIn('rombels.id', $targetRombelIds))->first();
            if (!$siswa) {
                $siswa = Siswa::first();
            }
            if (!$siswa) {
                $siswa = Siswa::create([
                    'nisn' => '0099887766',
                    'nama' => 'Siswa Simulasi CBT',
                    'status' => 'aktif',
                    'jenis_kelamin' => 'L',
                ]);
                $primaryRombel = $targetRombelIds[0] ?? null;
                if ($primaryRombel) {
                    \App\Models\SiswaRombel::create([
                        'siswa_id' => $siswa->id,
                        'rombel_id' => $primaryRombel,
                        'tahun_ajaran_id' => $asesmen->distribusi?->tahun_ajaran_id ?? \App\Models\TahunAjaran::where('is_active', 1)->value('id') ?? 14,
                        'status_keanggotaan' => 'aktif',
                    ]);
                }
            }
        }

        $hasil = null;
        if ($siswa) {
            $hasil = AkademikAsesmenHasil::firstOrCreate(
                ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswa->id],
                ['mulai_pada' => now(), 'is_selesai' => false, 'jumlah_pelanggaran' => 0, 'status_kejujuran' => 'jujur']
            );
        }

        $soals = $asesmen->soals;
        if ($asesmen->acak_soal && $soals->isNotEmpty()) {
            $seed = crc32(($siswa?->id ?? 1) . '_' . $asesmen->id);
            $soalsArray = $soals->all();
            mt_srand($seed);
            shuffle($soalsArray);
            mt_srand();
            $soals = collect($soalsArray);
        }

        // Jika acak opsi aktif, siapkan permutasi opsi per butir soal untuk sesi siswa ini
        $opsiAcakPerSoal = [];
        foreach ($soals as $s) {
            $opts = [];
            if (!empty($s->opsi_a)) $opts['A'] = $s->opsi_a;
            if (!empty($s->opsi_b)) $opts['B'] = $s->opsi_b;
            if (!empty($s->opsi_c)) $opts['C'] = $s->opsi_c;
            if (!empty($s->opsi_d)) $opts['D'] = $s->opsi_d;
            if (!empty($s->opsi_e)) $opts['E'] = $s->opsi_e;

            if ($asesmen->acak_opsi && count($opts) > 1) {
                $optSeed = crc32(($siswa?->id ?? 1) . '_' . $s->id);
                mt_srand($optSeed);
                $keys = array_keys($opts);
                shuffle($keys);
                mt_srand();

                $shuffled = [];
                foreach ($keys as $k) {
                    $shuffled[$k] = $opts[$k];
                }
                $opsiAcakPerSoal[$s->id] = $shuffled;
            } else {
                $opsiAcakPerSoal[$s->id] = $opts;
            }
        }

        return view('dcc.akademik.asesmen.kerjakan', compact('asesmen', 'soals', 'siswa', 'hasil', 'opsiAcakPerSoal'));
    }

    public function submitJawaban(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::with('soals')->findOrFail($id);
        $siswaId = $request->input('siswa_id');
        $jawabanInput = $request->input('jawaban', []); // [soal_id => 'A']

        // Pastikan $siswaId benar-benar valid di database
        $siswa = $siswaId ? Siswa::find($siswaId) : null;
        if (!$siswa) {
            $siswa = Siswa::first();
            $siswaId = $siswa?->id;
        }

        $totalBobot = $asesmen->soals->sum('bobot');
        $skorDidapat = 0;

        foreach ($asesmen->soals as $soal) {
            $jawabanUser = strtoupper(trim($jawabanInput[$soal->id] ?? ''));
            if ($jawabanUser === strtoupper(trim($soal->kunci_jawaban ?? ''))) {
                $skorDidapat += $soal->bobot;
            }
        }

        $nilaiAkhir = $totalBobot > 0 ? round(($skorDidapat / $totalBobot) * 100, 2) : 0;

        $hasil = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)
            ->where('siswa_id', $siswaId)
            ->first();

        $durasi = null;
        if ($hasil && $hasil->mulai_pada) {
            $durasi = now()->diffInSeconds($hasil->mulai_pada);
        }

        $violations = $hasil?->jumlah_pelanggaran ?? 0;
        $statusKejujuran = 'jujur';
        if ($violations >= ($asesmen->max_toleransi_keluar ?? 3)) {
            $statusKejujuran = 'terindikasi_curang';
        } elseif ($violations > 0) {
            $statusKejujuran = 'waspada';
        }

        AkademikAsesmenHasil::updateOrCreate(
            ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswaId],
            [
                'jawaban' => $jawabanInput,
                'nilai' => $nilaiAkhir,
                'is_selesai' => true,
                'selesai_pada' => now(),
                'durasi_detik' => $durasi,
                'status_kejujuran' => $statusKejujuran,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'nilai' => $nilaiAkhir,
                'status_kejujuran' => $statusKejujuran,
                'redirect' => route('akademik.asesmen.hasil', $asesmen->id)
            ]);
        }

        return redirect()->route('akademik.asesmen.hasil', $asesmen->id)
            ->with('success', "Asesmen telah diselesaikan! Nilai perolehan: {$nilaiAkhir} (Integritas: " . strtoupper($statusKejujuran) . ")");
    }

    public function refreshToken($id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        $tokenBaru = AkademikAsesmenOnline::buatToken();
        $asesmen->update(['token_ujian' => $tokenBaru]);

        return redirect()->back()->with('success', "Token Ujian baru berhasil diperbarui: {$tokenBaru}");
    }

    public function resetSiswa($id, $siswaId)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $this->authorizeAsesmen($asesmen);

        $hasil = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($hasil) {
            $hasil->update([
                'jumlah_pelanggaran' => 0,
                'status_kejujuran' => 'jujur',
                'is_selesai' => false,
                'selesai_pada' => null,
                'catatan_pengawas' => 'Sesi ujian dan status integritas di-reset oleh pengawas pada ' . now()->isoFormat('D MMM Y, HH:mm'),
            ]);
        }

        return redirect()->back()->with('success', 'Sesi pengerjaan siswa berhasil di-reset. Siswa dapat melanjutkan kembali pengerjaan.');
    }

    public function logPelanggaran(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $siswaId = $request->input('siswa_id');
        $tipe = $request->input('tipe', 'pindah_tab');
        $keterangan = $request->input('keterangan', 'Terdeteksi keluar dari layar CBT');

        $hasil = AkademikAsesmenHasil::firstOrCreate(
            ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswaId],
            ['mulai_pada' => now(), 'is_selesai' => false, 'jumlah_pelanggaran' => 0, 'status_kejujuran' => 'jujur']
        );

        $violations = ($hasil->jumlah_pelanggaran ?? 0) + 1;
        $logs = is_array($hasil->log_pelanggaran) ? $hasil->log_pelanggaran : [];
        $logs[] = [
            'waktu' => now()->format('H:i:s'),
            'tipe' => $tipe,
            'keterangan' => $keterangan,
        ];

        $maxTol = $asesmen->max_toleransi_keluar ?? 3;
        $statusKejujuran = 'jujur';
        if ($violations >= $maxTol) {
            $statusKejujuran = 'terindikasi_curang';
        } elseif ($violations > 0) {
            $statusKejujuran = 'waspada';
        }

        $hasil->update([
            'jumlah_pelanggaran' => $violations,
            'log_pelanggaran' => $logs,
            'status_kejujuran' => $statusKejujuran,
        ]);

        return response()->json([
            'success' => true,
            'violations' => $violations,
            'max_toleransi' => $maxTol,
            'status_kejujuran' => $statusKejujuran,
            'is_locked' => $violations >= $maxTol,
        ]);
    }

    public function autosaveJawaban(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $siswaId = $request->input('siswa_id');
        $jawabanInput = $request->input('jawaban', []);

        $hasil = AkademikAsesmenHasil::firstOrCreate(
            ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswaId],
            ['mulai_pada' => now(), 'is_selesai' => false, 'jumlah_pelanggaran' => 0, 'status_kejujuran' => 'jujur']
        );

        $current = is_array($hasil->jawaban) ? $hasil->jawaban : [];
        $merged = array_merge($current, $jawabanInput);

        $hasil->update([
            'jawaban' => $merged,
        ]);

        return response()->json(['success' => true, 'saved_at' => now()->format('H:i:s')]);
    }
}
