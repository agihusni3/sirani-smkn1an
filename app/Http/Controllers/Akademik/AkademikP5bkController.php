<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikP5bkNilai;
use App\Models\AkademikP5bkProyek;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikP5bkController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();

        $query = AkademikP5bkProyek::with(['rombel', 'tahunAjaran'])->withCount('nilais')
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($request->filled('rombel_id')) {
            $query->where('rombel_id', $request->rombel_id);
        }

        $proyeks = $query->latest()->paginate(15)->withQueryString();

        $temas = [
            'Gaya Hidup Berkelanjutan',
            'Kearifan Lokal',
            'Bhinneka Tunggal Ika',
            'Bangunlah Jiwa dan Raganya',
            'Suara Demokrasi',
            'Rekayasa dan Teknologi',
            'Kewirausahaan',
            'Kebekerjaan (Khas SMK)',
        ];

        return view('dcc.akademik.p5bk.index', compact('proyeks', 'rombels', 'ta', 'temas'));
    }

    public function storeProyek(Request $request)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
            'nama_proyek' => 'required|string|max:255',
            'tema' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
        ]);

        AkademikP5bkProyek::create($request->all());

        return redirect()->back()->with('success', 'Proyek P5BK berhasil dibuat.');
    }

    public function penilaian($id)
    {
        $proyek = AkademikP5bkProyek::with(['rombel', 'tahunAjaran'])->findOrFail($id);

        $siswas = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $proyek->rombel_id))
            ->whereIn('status', ['aktif', 'pkl'])
            ->orderBy('nama')
            ->get();

        $existingNilai = AkademikP5bkNilai::where('p5bk_proyek_id', $proyek->id)
            ->get()
            ->keyBy('siswa_id');

        return view('dcc.akademik.p5bk.penilaian', compact('proyek', 'siswas', 'existingNilai'));
    }

    public function storeNilai(Request $request, $id)
    {
        $proyek = AkademikP5bkProyek::findOrFail($id);
        $dimensiData = $request->input('dimensi', []); // [siswa_id => [dimensi_name => 1..4]]
        $catatanData = $request->input('catatan', []);

        DB::beginTransaction();
        try {
            foreach ($dimensiData as $siswaId => $vals) {
                AkademikP5bkNilai::updateOrCreate(
                    [
                        'p5bk_proyek_id' => $proyek->id,
                        'siswa_id' => $siswaId,
                    ],
                    [
                        'beriman_bertaqwa' => $vals['beriman'] ?? 3,
                        'berkebhinekaan_global' => $vals['kebhinekaan'] ?? 3,
                        'bergotong_royong' => $vals['gotong_royong'] ?? 3,
                        'mandiri' => $vals['mandiri'] ?? 3,
                        'bernalar_kritis' => $vals['nalar_kritis'] ?? 3,
                        'kreatif' => $vals['kreatif'] ?? 3,
                        'nilai_budaya_kerja' => $vals['budaya_kerja'] ?? 3,
                        'catatan' => $catatanData[$siswaId] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('akademik.p5bk.penilaian', $proyek->id)
                ->with('success', 'Penilaian 6 Dimensi P5BK berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    public function destroyProyek($id)
    {
        $proyek = AkademikP5bkProyek::findOrFail($id);
        $proyek->delete();

        return redirect()->back()->with('success', 'Proyek P5BK berhasil dihapus.');
    }
}
