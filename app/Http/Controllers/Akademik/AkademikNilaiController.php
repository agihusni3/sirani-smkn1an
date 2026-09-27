<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenOnline;
use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikLeger;
use App\Models\AkademikNilai;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AkademikNilaiController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        $guruId = $user?->guru_id;

        $query = AkademikDistribusiMengajar::with([
                'mataPelajaran', 
                'rombel' => fn($q) => $q->withCount(['siswas' => fn($sq) => $sq->whereIn('status', ['aktif', 'pkl'])]), 
                'guru'
            ])
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->orderBy('mata_pelajaran_id')
            ->orderBy('semester')
            ->orderBy('rombel_id');

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $query->where('guru_id', $guruId);
        }

        $distribusis = $query->get();
        $groupedByMapel = $distribusis->groupBy('mata_pelajaran_id');

        return view('akademik.nilai.index', compact('distribusis', 'groupedByMapel', 'ta'));
    }

    protected function authorizeDistribusi(AkademikDistribusiMengajar $distribusi): void
    {
        $user = auth()->user();
        if ($user->isAdmin() || $user->isWakaKurikulum()) {
            return;
        }

        if ($user->guru_id && $distribusi->guru_id === $user->guru_id) {
            return;
        }

        abort(403, 'Akses Ditolak: Anda hanya memiliki hak akses untuk menginput nilai pada mata pelajaran dan rombel yang Anda ampu.');
    }

    public function inputNilai(Request $request, $distribusiId)
    {
        $distribusi = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel', 'guru', 'tahunAjaran'])
            ->findOrFail($distribusiId);
        $this->authorizeDistribusi($distribusi);

        $siswas = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $distribusi->rombel_id))
            ->whereIn('status', ['aktif', 'pkl'])
            ->orderBy('nama')
            ->get();

        // Ambil nilai yang sudah ada
        $existingNilai = AkademikNilai::where('distribusi_id', $distribusi->id)
            ->where('semester', $distribusi->semester)
            ->get()
            ->groupBy('siswa_id');

        $namaPenilaians = [
            'Formatif 1 (Tugas/Kuis)',
            'Formatif 2 (Praktik/Proyek)',
            'Formatif 3 (Diskusi/Portofolio)',
            'Sumatif Tengah Semester (STS)',
            'Sumatif Akhir Semester (SAS)',
        ];

        return view('akademik.nilai.input', compact('distribusi', 'siswas', 'existingNilai', 'namaPenilaians'));
    }

    public function storeNilai(Request $request, $distribusiId)
    {
        $distribusi = AkademikDistribusiMengajar::findOrFail($distribusiId);
        $this->authorizeDistribusi($distribusi);
        $nilaiData = $request->input('nilai', []); // [siswa_id => [penilaian_key => score]]
        $deskripsiData = $request->input('deskripsi', []);

        DB::beginTransaction();
        try {
            foreach ($nilaiData as $siswaId => $penilaians) {
                $formatifScores = [];
                $sumatifScores = [];

                foreach ($penilaians as $nama => $skor) {
                    if ($skor === null || $skor === '') continue;

                    $skorNum = floatval($skor);
                    $jenis = str_contains(strtolower($nama), 'sumatif') ? 'sumatif' : 'formatif';

                    AkademikNilai::updateOrCreate(
                        [
                            'distribusi_id' => $distribusi->id,
                            'siswa_id' => $siswaId,
                            'semester' => $distribusi->semester,
                            'nama_penilaian' => $nama,
                        ],
                        [
                            'jenis_penilaian' => $jenis,
                            'nilai' => $skorNum,
                            'deskripsi_capaian' => $deskripsiData[$siswaId] ?? null,
                        ]
                    );

                    if ($jenis === 'formatif') {
                        $formatifScores[] = $skorNum;
                    } else {
                        $sumatifScores[] = $skorNum;
                    }
                }

                // Kalkulasi Leger Otomatis
                $formatifAvg = count($formatifScores) > 0 ? array_sum($formatifScores) / count($formatifScores) : 0;
                $sumatifAvg = count($sumatifScores) > 0 ? array_sum($sumatifScores) / count($sumatifScores) : 0;
                
                // Bobot Kurikulum Merdeka SMK: 50% Formatif + 50% Sumatif (atau rata-rata)
                $nilaiAkhir = ($formatifAvg > 0 && $sumatifAvg > 0)
                    ? round(($formatifAvg * 0.5) + ($sumatifAvg * 0.5), 2)
                    : round(max($formatifAvg, $sumatifAvg), 2);

                $predikat = 'D';
                if ($nilaiAkhir >= 85) $predikat = 'A';
                elseif ($nilaiAkhir >= 75) $predikat = 'B';
                elseif ($nilaiAkhir >= 65) $predikat = 'C';

                $statusLulus = $nilaiAkhir >= 70;

                AkademikLeger::updateOrCreate(
                    [
                        'distribusi_id' => $distribusi->id,
                        'siswa_id' => $siswaId,
                        'semester' => $distribusi->semester,
                    ],
                    [
                        'nilai_formatif_avg' => round($formatifAvg, 2),
                        'nilai_sumatif_avg' => round($sumatifAvg, 2),
                        'nilai_akhir' => $nilaiAkhir,
                        'predikat' => $predikat,
                        'deskripsi_rapor' => $deskripsiData[$siswaId] ?? 'Menunjukkan penguasaan kompetensi yang memadai.',
                        'status_lulus' => $statusLulus,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('akademik.nilai.input', $distribusi->id)
                ->with('success', 'Nilai dan leger capaian siswa berhasil disimpan dan dikalkulasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    public function syncFromCBT(Request $request, $distribusiId)
    {
        $distribusi = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel'])->findOrFail($distribusiId);
        $this->authorizeDistribusi($distribusi);

        $siswas = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $distribusi->rombel_id))
            ->whereIn('status', ['aktif', 'pkl'])
            ->pluck('id');

        // Cari semua asesmen yang berkaitan dengan mata pelajaran ini pada semester dan tahun ajaran aktif
        $asesmens = AkademikAsesmenOnline::where('semester', $distribusi->semester)
            ->whereHas('distribusi', function($q) use ($distribusi) {
                $q->where('mata_pelajaran_id', $distribusi->mata_pelajaran_id)
                  ->where('tahun_ajaran_id', $distribusi->tahun_ajaran_id);
            })
            ->get();

        $syncedCount = 0;
        foreach ($asesmens as $asesmen) {
            $targetRombels = $asesmen->target_rombel_ids ?? [$asesmen->distribusi?->rombel_id];
            $targetRombels = array_map('intval', (array) $targetRombels);

            if (!empty($targetRombels) && !in_array((int) $distribusi->rombel_id, $targetRombels)) {
                continue;
            }

            $hasils = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)
                ->whereIn('siswa_id', $siswas)
                ->where('is_selesai', true)
                ->get();

            foreach ($hasils as $h) {
                self::syncSingleAsesmenToNilai($asesmen, $h->siswa_id, $h->nilai);
                $syncedCount++;
            }
        }

        if ($syncedCount === 0) {
            return redirect()->back()->with('warning', 'Tidak ditemukan hasil tes asesmen CBT yang selesai untuk mata pelajaran dan rombel ini.');
        }

        return redirect()->back()->with('success', "Berhasil menyinkronkan {$syncedCount} nilai dari hasil tes asesmen CBT ke Buku Nilai & Leger.");
    }

    public static function resolveNamaPenilaian(AkademikAsesmenOnline $asesmen): array
    {
        $jenis = strtolower(trim($asesmen->jenis ?? ''));
        $judul = strtolower(trim($asesmen->judul ?? ''));

        if ($jenis === 'pts' || str_contains($judul, 'tengah semester') || str_contains($judul, 'sts') || str_contains($judul, 'pts')) {
            return [
                'nama' => 'Sumatif Tengah Semester (STS)',
                'jenis' => 'sumatif'
            ];
        }

        if ($jenis === 'pas' || str_contains($judul, 'akhir semester') || str_contains($judul, 'sas') || str_contains($judul, 'pas')) {
            return [
                'nama' => 'Sumatif Akhir Semester (SAS)',
                'jenis' => 'sumatif'
            ];
        }

        if (str_contains($judul, 'formatif 2') || str_contains($judul, 'praktik') || str_contains($judul, 'proyek')) {
            return [
                'nama' => 'Formatif 2 (Praktik/Proyek)',
                'jenis' => 'formatif'
            ];
        }

        if (str_contains($judul, 'formatif 3') || str_contains($judul, 'diskusi') || str_contains($judul, 'portofolio')) {
            return [
                'nama' => 'Formatif 3 (Diskusi/Portofolio)',
                'jenis' => 'formatif'
            ];
        }

        return [
            'nama' => 'Formatif 1 (Tugas/Kuis)',
            'jenis' => 'formatif'
        ];
    }

    public static function syncSingleAsesmenToNilai(AkademikAsesmenOnline $asesmen, $siswaId, $nilaiAkhir): void
    {
        try {
            $mapping = self::resolveNamaPenilaian($asesmen);
            $namaPenilaian = $mapping['nama'];
            $jenisPenilaian = $mapping['jenis'];

            $siswa = Siswa::with('rombels')->find($siswaId);
            $studentRombelId = $siswa?->rombels?->first()?->id;

            $distribusi = null;
            if ($asesmen->distribusi) {
                $distribusi = AkademikDistribusiMengajar::where('mata_pelajaran_id', $asesmen->distribusi->mata_pelajaran_id)
                    ->when($studentRombelId, fn($q) => $q->where('rombel_id', $studentRombelId))
                    ->where('tahun_ajaran_id', $asesmen->distribusi->tahun_ajaran_id)
                    ->where('semester', $asesmen->semester)
                    ->first();
            }

            if (!$distribusi) {
                $distribusi = $asesmen->distribusi;
            }

            if (!$distribusi) {
                return;
            }

            AkademikNilai::updateOrCreate(
                [
                    'distribusi_id' => $distribusi->id,
                    'siswa_id' => $siswaId,
                    'semester' => $asesmen->semester,
                    'nama_penilaian' => $namaPenilaian,
                ],
                [
                    'jenis_penilaian' => $jenisPenilaian,
                    'nilai' => $nilaiAkhir,
                    'deskripsi_capaian' => "Hasil asesmen CBT ({$asesmen->judul}): Skor {$nilaiAkhir}",
                ]
            );

            self::kalkulasiLegerSiswa($distribusi, $siswaId);
        } catch (\Throwable $e) {
            Log::error("Gagal syncSingleAsesmenToNilai: " . $e->getMessage());
        }
    }

    public static function kalkulasiLegerSiswa(AkademikDistribusiMengajar $distribusi, $siswaId): void
    {
        $allNilais = AkademikNilai::where('distribusi_id', $distribusi->id)
            ->where('siswa_id', $siswaId)
            ->where('semester', $distribusi->semester)
            ->get();

        $formatifScores = $allNilais->where('jenis_penilaian', 'formatif')->pluck('nilai')->map(fn($v) => floatval($v))->all();
        $sumatifScores = $allNilais->where('jenis_penilaian', 'sumatif')->pluck('nilai')->map(fn($v) => floatval($v))->all();

        $formatifAvg = count($formatifScores) > 0 ? array_sum($formatifScores) / count($formatifScores) : 0;
        $sumatifAvg = count($sumatifScores) > 0 ? array_sum($sumatifScores) / count($sumatifScores) : 0;

        $nilaiAkhir = ($formatifAvg > 0 && $sumatifAvg > 0)
            ? round(($formatifAvg * 0.5) + ($sumatifAvg * 0.5), 2)
            : round(max($formatifAvg, $sumatifAvg), 2);

        $predikat = 'D';
        if ($nilaiAkhir >= 85) $predikat = 'A';
        elseif ($nilaiAkhir >= 75) $predikat = 'B';
        elseif ($nilaiAkhir >= 65) $predikat = 'C';

        $passingGrade = $distribusi->mataPelajaran?->passing_grade ?? 75;
        $statusLulus = $nilaiAkhir >= $passingGrade;

        AkademikLeger::updateOrCreate(
            [
                'distribusi_id' => $distribusi->id,
                'siswa_id' => $siswaId,
                'semester' => $distribusi->semester,
            ],
            [
                'nilai_formatif_avg' => round($formatifAvg, 2),
                'nilai_sumatif_avg' => round($sumatifAvg, 2),
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
                'deskripsi_rapor' => 'Capaian kompetensi berdasarkan asesmen formatif & sumatif.',
                'status_lulus' => $statusLulus,
            ]
        );
    }

    public function leger(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();
        $selectedRombelId = $request->get('rombel_id', $rombels->first()?->id);
        $semester = $request->get('semester', 1);

        $selectedRombel = Rombel::find($selectedRombelId);
        $distribusis = AkademikDistribusiMengajar::with('mataPelajaran')
            ->where('rombel_id', $selectedRombelId)
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get();

        $siswas = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $selectedRombelId))
            ->whereIn('status', ['aktif', 'pkl'])
            ->orderBy('nama')
            ->get();

        // Ambil semua leger untuk rombel ini
        $legers = AkademikLeger::whereIn('distribusi_id', $distribusis->pluck('id'))
            ->where('semester', $semester)
            ->get()
            ->groupBy('siswa_id');

        return view('akademik.nilai.leger', compact(
            'rombels', 'selectedRombel', 'distribusis', 'siswas', 'legers', 'semester', 'ta'
        ));
    }
}
