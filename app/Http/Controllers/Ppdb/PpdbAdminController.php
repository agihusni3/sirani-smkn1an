<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Jurusan;
use App\Models\PpdbPendaftar;
use App\Models\Rombel;
use App\Services\PpdbMutasiService;
use Illuminate\Http\Request;

class PpdbAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima', 'siswa']);

        if ($request->filled('status')) {
            $st = $request->status;
            if ($st === 'menunggu') $st = 'menunggu_verifikasi';
            if ($st === 'berkas_valid') $st = 'terverifikasi';
            $query->where('status', $st);
        }

        if ($request->filled('jurusan_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('jurusan_id_1', $request->jurusan_id)
                  ->orWhere('jurusan_id_2', $request->jurusan_id)
                  ->orWhere('jurusan_diterima_id', $request->jurusan_id);
            });
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_lengkap', 'like', "%{$cari}%")
                  ->orWhere('nisn', 'like', "%{$cari}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$cari}%")
                  ->orWhere('asal_sekolah', 'like', "%{$cari}%");
            });
        }

        $pendaftars = $query->latest()->paginate(20);
        $jurusans = Jurusan::all();
        $rombels = Rombel::where('tingkat', '10')->orWhere('tingkat', 'X')->get();

        $counts = [
            'total' => PpdbPendaftar::count(),
            'menunggu' => PpdbPendaftar::whereIn('status', ['menunggu', 'menunggu_verifikasi', 'draft'])->count(),
            'berkas_valid' => PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid'])->count(),
            'diterima' => PpdbPendaftar::where('status', 'diterima')->count(),
            'ditolak' => PpdbPendaftar::where('status', 'ditolak')->count(),
        ];

        // Statistik Peminatan Jurusan
        $jurusanStats = $jurusans->map(function ($j) {
            $peminat = PpdbPendaftar::where('jurusan_id_1', $j->id)->count();
            $diterima = PpdbPendaftar::where('jurusan_diterima_id', $j->id)->where('status', 'diterima')->count();
            return [
                'id' => $j->id,
                'kode' => $j->kode_jurusan,
                'nama' => $j->nama_jurusan,
                'peminat' => $peminat,
                'diterima' => $diterima,
            ];
        });

        return view('ppdb.admin.index', compact('pendaftars', 'jurusans', 'rombels', 'counts', 'jurusanStats'));
    }

    public function show($id)
    {
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima', 'siswa'])->findOrFail($id);
        $jurusans = Jurusan::all();
        $rombels = Rombel::where('tingkat', '10')->orWhere('tingkat', 'X')->get();

        return view('ppdb.admin.show', compact('pendaftar', 'jurusans', 'rombels'));
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftar = PpdbPendaftar::findOrFail($id);

        $validated = $request->validate([
            'status_pendaftaran' => 'required|in:menunggu,berkas_valid,diterima,ditolak',
            'jurusan_diterima_id' => 'nullable|exists:jurusans,id',
            'catatan' => 'nullable|string',
        ]);

        $st = $validated['status_pendaftaran'];
        if ($st === 'menunggu') $st = 'menunggu_verifikasi';
        if ($st === 'berkas_valid') $st = 'terverifikasi';

        $statusLama = $pendaftar->status;
        $jurusanLama = $pendaftar->jurusan_diterima_id;

        $pendaftar->status = $st;
        if (!empty($validated['jurusan_diterima_id'])) {
            $pendaftar->jurusan_diterima_id = $validated['jurusan_diterima_id'];
        }
        if (isset($validated['catatan'])) {
            $pendaftar->catatan_panitia = $validated['catatan'];
        }
        $pendaftar->diverifikasi_oleh = auth()->user()->nama ?? auth()->user()->name ?? 'Admin';
        $pendaftar->diverifikasi_pada = now();
        $pendaftar->save();

        // Audit Trail PPDB
        AuditLog::catat(
            'update',
            'ppdb',
            "Verifikasi pendaftar PPDB: {$pendaftar->nama_lengkap} ({$pendaftar->no_pendaftaran}) status diubah menjadi " . strtoupper($pendaftar->status),
            ['status' => $statusLama, 'jurusan_diterima_id' => $jurusanLama],
            ['status' => $pendaftar->status, 'jurusan_diterima_id' => $pendaftar->jurusan_diterima_id, 'catatan' => $pendaftar->catatan_panitia],
            $pendaftar
        );

        return back()->with('success', 'Status pendaftar ' . $pendaftar->nama_lengkap . ' berhasil diperbarui.');
    }

    public function mutasi(Request $request, $id, PpdbMutasiService $mutasiService)
    {
        $request->validate([
            'rombel_id' => 'required|exists:rombels,id',
        ]);

        $pendaftar = PpdbPendaftar::findOrFail($id);

        try {
            $siswa = $mutasiService->mutasiKeSiswa($pendaftar, $request->rombel_id);

            // Audit Trail PPDB
            AuditLog::catat(
                'create',
                'ppdb',
                "Migrasi calon siswa PPDB {$pendaftar->nama_lengkap} ({$pendaftar->no_pendaftaran}) menjadi Siswa Aktif SITUAN/SIRANI (NISN: {$siswa->nisn})",
                null,
                ['pendaftar_id' => $pendaftar->id, 'siswa_id' => $siswa->id, 'rombel_id' => $request->rombel_id],
                $pendaftar
            );

            return back()->with('success', "Sukses! Calon siswa {$pendaftar->nama_lengkap} resmi dimutasi menjadi Siswa Aktif SIRANI (NISN: {$siswa->nisn}).");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memutasi siswa: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Riwayat & Audit Trail Khusus Modul PPDB 2026.
     */
    public function log(Request $request)
    {
        $filters = $request->only(['aksi', 'dari', 'sampai', 'cari']);

        $logs = AuditLog::with('user')
            ->ppdb()
            ->filter($filters)
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        $aksiOptions = ['create', 'update', 'delete'];

        $counts = [
            'total'     => AuditLog::ppdb()->count(),
            'hari_ini'  => AuditLog::ppdb()->whereDate('created_at', today())->count(),
            'minggu_ini'=> AuditLog::ppdb()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('ppdb.admin.log', compact('logs', 'filters', 'aksiOptions', 'counts'));
    }
}
