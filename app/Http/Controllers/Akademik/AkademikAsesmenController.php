<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikAsesmenSoal;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikNilai;
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
        $distribusis = $distribusiQuery->get();

        $query = AkademikAsesmenOnline::with(['distribusi.guru', 'distribusi.mataPelajaran', 'distribusi.rombel'])
            ->withCount(['soals', 'hasils']);

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $query->whereHas('distribusi', fn($q) => $q->where('guru_id', $guruId));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('distribusi_id')) {
            $query->where('distribusi_id', $request->distribusi_id);
        }

        $asesmens = $query->latest()->paginate(15)->withQueryString();

        return view('dcc.akademik.asesmen.index', compact('asesmens', 'distribusis', 'ta'));
    }

    public function create(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        $guruId = $user?->guru_id;

        $distribusiQuery = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel', 'guru'])
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $distribusiQuery->where('guru_id', $guruId);
        }
        $distribusis = $distribusiQuery->get();

        return view('dcc.akademik.asesmen.create', compact('distribusis', 'ta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distribusi_id' => 'required|exists:akademik_distribusi_mengajars,id',
            'judul' => 'required|string|max:255',
            'jenis' => 'required|in:kuis,ulangan_harian,pts,pas,tugas',
            'durasi_menit' => 'required|integer|min:5|max:300',
            'passing_grade' => 'required|integer|min:0|max:100',
            'dibuka_pada' => 'nullable|date',
            'ditutup_pada' => 'nullable|date|after_or_equal:dibuka_pada',
            'deskripsi' => 'nullable|string',
        ]);

        $distribusi = AkademikDistribusiMengajar::findOrFail($request->distribusi_id);

        $asesmen = AkademikAsesmenOnline::create([
            'distribusi_id' => $request->distribusi_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'jenis' => $request->jenis,
            'semester' => $distribusi->semester,
            'durasi_menit' => $request->durasi_menit,
            'dibuka_pada' => $request->dibuka_pada,
            'ditutup_pada' => $request->ditutup_pada,
            'acak_soal' => $request->has('acak_soal'),
            'tampilkan_nilai' => $request->has('tampilkan_nilai'),
            'is_active' => $request->has('is_active'),
            'passing_grade' => $request->passing_grade,
        ]);

        return redirect()->route('akademik.asesmen.soal', $asesmen->id)
            ->with('success', 'Asesmen online berhasil dibuat. Silakan tambahkan butir soal.');
    }

    public function soal($id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.rombel', 'soals'])->findOrFail($id);
        $totalBobot = $asesmen->soals->sum('bobot');
        $nomorBerikutnya = ($asesmen->soals->max('nomor') ?? 0) + 1;

        return view('dcc.akademik.asesmen.soal', compact('asesmen', 'totalBobot', 'nomorBerikutnya'));
    }

    public function storeSoal(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);

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
        ]);

        $nomor = ($asesmen->soals()->max('nomor') ?? 0) + 1;

        AkademikAsesmenSoal::create([
            'asesmen_id' => $asesmen->id,
            'nomor' => $nomor,
            'pertanyaan' => $request->pertanyaan,
            'tipe' => $request->tipe,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'kunci_jawaban' => strtoupper($request->kunci_jawaban),
            'bobot' => $request->bobot,
            'pembahasan' => $request->pembahasan,
        ]);

        return redirect()->back()->with('success', 'Butir soal no. ' . $nomor . ' berhasil ditambahkan.');
    }

    public function destroySoal($id, $soalId)
    {
        $soal = AkademikAsesmenSoal::where('asesmen_id', $id)->findOrFail($soalId);
        $soal->delete();

        return redirect()->back()->with('success', 'Butir soal berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $asesmen = AkademikAsesmenOnline::findOrFail($id);
        $asesmen->update(['is_active' => !$asesmen->is_active]);

        $statusStr = $asesmen->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Asesmen berhasil {$statusStr}.");
    }

    public function hasil($id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.rombel', 'soals'])->findOrFail($id);

        $hasils = AkademikAsesmenHasil::with('siswa')
            ->where('asesmen_id', $asesmen->id)
            ->get()
            ->keyBy('siswa_id');

        $siswas = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $asesmen->distribusi->rombel_id))
            ->whereIn('status', ['aktif', 'pkl'])
            ->orderBy('nama')
            ->get();

        $totalSiswa = $siswas->count();
        $totalMengerjakan = $hasils->where('is_selesai', true)->count();
        $rataRata = $hasils->where('is_selesai', true)->avg('nilai') ?? 0;
        $tuntas = $hasils->where('is_selesai', true)->where('nilai', '>=', $asesmen->passing_grade)->count();

        return view('dcc.akademik.asesmen.hasil', compact(
            'asesmen', 'siswas', 'hasils', 'totalSiswa', 'totalMengerjakan', 'rataRata', 'tuntas'
        ));
    }

    public function pushToNilai(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::with('distribusi')->findOrFail($id);
        $hasils = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)->where('is_selesai', true)->get();

        if ($hasils->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada siswa yang menyelesaikan asesmen ini.');
        }

        $jenisNilai = in_array($asesmen->jenis, ['pts', 'pas']) ? 'sumatif' : 'formatif';

        foreach ($hasils as $h) {
            AkademikNilai::updateOrCreate(
                [
                    'distribusi_id' => $asesmen->distribusi_id,
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

        return redirect()->back()->with('success', 'Nilai asesmen online berhasil ditransfer ke Buku Nilai & Leger Siswa.');
    }

    /**
     * Tampilan pengerjaan simulasi/interaktif asesmen
     */
    public function kerjakan($id)
    {
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'soals'])->findOrFail($id);

        if (!$asesmen->is_active) {
            return redirect()->route('akademik.asesmen.index')->with('error', 'Asesmen online ini sedang tidak aktif.');
        }

        $user = auth()->user();
        $siswa = null;

        if ($user?->siswa_id) {
            $siswa = Siswa::find($user->siswa_id);
        }

        // Mode preview / simulasi untuk guru dan admin
        if (!$siswa) {
            $rombelId = $asesmen->distribusi?->rombel_id;
            if ($rombelId) {
                $siswa = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombelId))->first();
            }
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
                if ($rombelId) {
                    \App\Models\SiswaRombel::create([
                        'siswa_id' => $siswa->id,
                        'rombel_id' => $rombelId,
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
                ['mulai_pada' => now(), 'is_selesai' => false]
            );
        }

        $soals = $asesmen->soals;
        if ($asesmen->acak_soal) {
            $soals = $soals->shuffle();
        }

        return view('dcc.akademik.asesmen.kerjakan', compact('asesmen', 'soals', 'siswa', 'hasil'));
    }

    public function submitJawaban(Request $request, $id)
    {
        $asesmen = AkademikAsesmenOnline::with('soals')->findOrFail($id);
        $siswaId = $request->input('siswa_id');
        $jawabanInput = $request->input('jawaban', []); // [soal_id => 'A']

        // Pastikan $siswaId benar-benar valid di database
        $siswa = $siswaId ? Siswa::find($siswaId) : null;
        if (!$siswa) {
            $rombelId = $asesmen->distribusi?->rombel_id;
            if ($rombelId) {
                $siswa = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $rombelId))->first();
            }
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
                if ($rombelId) {
                    \App\Models\SiswaRombel::create([
                        'siswa_id' => $siswa->id,
                        'rombel_id' => $rombelId,
                        'tahun_ajaran_id' => $asesmen->distribusi?->tahun_ajaran_id ?? \App\Models\TahunAjaran::where('is_active', 1)->value('id') ?? 14,
                        'status_keanggotaan' => 'aktif',
                    ]);
                }
            }
            $siswaId = $siswa->id;
        }

        $totalBobot = $asesmen->soals->sum('bobot');
        $skorDidapat = 0;

        foreach ($asesmen->soals as $soal) {
            $jawabanUser = strtoupper(trim($jawabanInput[$soal->id] ?? ''));
            if ($jawabanUser === strtoupper(trim($soal->kunci_jawaban))) {
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

        AkademikAsesmenHasil::updateOrCreate(
            ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswaId],
            [
                'jawaban' => $jawabanInput,
                'nilai' => $nilaiAkhir,
                'is_selesai' => true,
                'selesai_pada' => now(),
                'durasi_detik' => $durasi,
            ]
        );

        return redirect()->route('akademik.asesmen.hasil', $asesmen->id)
            ->with('success', "Asesmen telah diselesaikan! Nilai perolehan: {$nilaiAkhir}");
    }
}
