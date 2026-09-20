<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikLeger;
use App\Models\AkademikNilai;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikNilaiController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        $guruId = $user?->guru_id;

        $query = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel', 'guru'])
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            $query->where('guru_id', $guruId);
        }

        $distribusis = $query->get();

        return view('dcc.akademik.nilai.index', compact('distribusis', 'ta'));
    }

    public function inputNilai(Request $request, $distribusiId)
    {
        $distribusi = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel', 'guru', 'tahunAjaran'])
            ->findOrFail($distribusiId);

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

        return view('dcc.akademik.nilai.input', compact('distribusi', 'siswas', 'existingNilai', 'namaPenilaians'));
    }

    public function storeNilai(Request $request, $distribusiId)
    {
        $distribusi = AkademikDistribusiMengajar::findOrFail($distribusiId);
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

        return view('dcc.akademik.nilai.leger', compact(
            'rombels', 'selectedRombel', 'distribusis', 'siswas', 'legers', 'semester', 'ta'
        ));
    }
}
