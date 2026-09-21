<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikJurnalKbm;
use App\Models\AkademikKehadiranKbm;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikJurnalController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        $guruId = $user?->guru_id;

        $query = AkademikJurnalKbm::with([
            'distribusi.guru',
            'distribusi.mataPelajaran',
            'distribusi.rombel',
            'kehadirans'
        ]);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('guru_id')) {
            $query->whereHas('distribusi', fn($q) => $q->where('guru_id', $request->guru_id));
        } elseif ($guruId && !$user->isAdmin() && !$user->isWakaKurikulum()) {
            // Guru biasa otomatis difilter ke jurnalnya sendiri
            $query->whereHas('distribusi', fn($q) => $q->where('guru_id', $guruId));
        }

        if ($request->filled('rombel_id')) {
            $query->whereHas('distribusi', fn($q) => $q->where('rombel_id', $request->rombel_id));
        }

        $jurnals = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();

        // Statistik hari ini
        $today = Carbon::today()->toDateString();
        $totalJurnalHariIni = AkademikJurnalKbm::whereDate('tanggal', $today)->count();

        return view('dcc.akademik.jurnal.index', compact(
            'jurnals', 'ta', 'gurus', 'rombels', 'totalJurnalHariIni', 'today'
        ));
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

        $selectedDistribusi = null;
        $siswas = collect();
        $pertemuanKe = 1;
        $perangkatAjar = null;
        $atpList = collect();
        $modulList = collect();

        if ($request->filled('distribusi_id')) {
            $selectedDistribusi = AkademikDistribusiMengajar::with(['rombel', 'mataPelajaran'])->find($request->distribusi_id);
            if ($selectedDistribusi) {
                $siswas = Siswa::whereHas('rombels', fn($q) => $q->where('rombels.id', $selectedDistribusi->rombel_id))
                    ->whereIn('status', ['aktif', 'pkl'])
                    ->orderBy('nama')
                    ->get();

                $lastPertemuan = AkademikJurnalKbm::where('distribusi_id', $selectedDistribusi->id)->max('pertemuan_ke');
                $pertemuanKe = ($lastPertemuan ?? 0) + 1;

                // Cari dokumen Perangkat Pembelajaran yang cocok
                $perangkatAjar = \App\Models\AkademikPerangkatAjar::with(['atpItems', 'modulAjars'])
                    ->where('guru_id', $selectedDistribusi->guru_id)
                    ->where('mata_pelajaran_id', $selectedDistribusi->mata_pelajaran_id)
                    ->where('tingkat', $selectedDistribusi->rombel?->tingkat ?? 'X')
                    ->where('semester', $selectedDistribusi->semester ?? 1)
                    ->first();

                if ($perangkatAjar) {
                    $atpList = $perangkatAjar->atpItems;
                    $modulList = $perangkatAjar->modulAjars;
                }
            }
        }

        return view('dcc.akademik.jurnal.create', compact('distribusis', 'selectedDistribusi', 'siswas', 'pertemuanKe', 'perangkatAjar', 'atpList', 'modulList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distribusi_id' => 'required|exists:akademik_distribusi_mengajars,id',
            'tanggal' => 'required|date',
            'pertemuan_ke' => 'required|integer|min:1',
            'materi_ajar' => 'required|string',
            'metode_pembelajaran' => 'nullable|string|max:100',
            'catatan_guru' => 'nullable|string',
            'refleksi' => 'nullable|string',
            'kehadiran' => 'required|array', // [siswa_id => status]
        ]);

        DB::beginTransaction();
        try {
            $jurnal = AkademikJurnalKbm::create([
                'distribusi_id' => $request->distribusi_id,
                'tanggal' => $request->tanggal,
                'pertemuan_ke' => $request->pertemuan_ke,
                'materi_ajar' => $request->materi_ajar,
                'metode_pembelajaran' => $request->metode_pembelajaran,
                'catatan_guru' => $request->catatan_guru,
                'refleksi' => $request->refleksi,
                'is_published' => true,
            ]);

            foreach ($request->kehadiran as $siswaId => $status) {
                AkademikKehadiranKbm::create([
                    'jurnal_id' => $jurnal->id,
                    'siswa_id' => $siswaId,
                    'status' => in_array($status, ['hadir', 'izin', 'sakit', 'alfa']) ? $status : 'hadir',
                    'keterangan' => $request->keterangan[$siswaId] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('akademik.jurnal.index')
                ->with('success', 'Jurnal KBM dan presensi siswa sesi pertemuan ke-' . $request->pertemuan_ke . ' berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $jurnal = AkademikJurnalKbm::with([
            'distribusi.guru',
            'distribusi.mataPelajaran',
            'distribusi.rombel',
            'kehadirans.siswa'
        ])->findOrFail($id);

        $rekap = [
            'total' => $jurnal->kehadirans->count(),
            'hadir' => $jurnal->kehadirans->where('status', 'hadir')->count(),
            'izin' => $jurnal->kehadirans->where('status', 'izin')->count(),
            'sakit' => $jurnal->kehadirans->where('status', 'sakit')->count(),
            'alfa' => $jurnal->kehadirans->where('status', 'alfa')->count(),
        ];

        return view('dcc.akademik.jurnal.show', compact('jurnal', 'rekap'));
    }

    public function destroy($id)
    {
        $jurnal = AkademikJurnalKbm::findOrFail($id);
        $jurnal->delete();

        return redirect()->route('akademik.jurnal.index')
            ->with('success', 'Jurnal KBM berhasil dihapus.');
    }
}
