<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikMataPelajaran;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AkademikMatpelController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();

        $selectedTaId = $request->input('tahun_ajaran_id', $ta?->id);
        $selectedTa = $tahunAjarans->firstWhere('id', $selectedTaId) ?? $ta;

        $query = AkademikMataPelajaran::with(['tahunAjaran', 'jurusan']);

        if ($selectedTaId) {
            $query->where('tahun_ajaran_id', $selectedTaId);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('fase')) {
            $query->where('fase', $request->fase);
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', 'like', '%' . $request->tingkat . '%');
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_mapel', 'like', "%{$s}%")
                  ->orWhere('kode_mapel', 'like', "%{$s}%");
            });
        }

        // Statistik Cepat (KPI) Kurikulum untuk Tahun Ajaran terpilih (1 Query Agregat Cepat)
        $statQuery = AkademikMataPelajaran::query();
        if ($selectedTaId) {
            $statQuery->where('tahun_ajaran_id', $selectedTaId);
        }

        $statAgg = $statQuery->selectRaw("
            COUNT(*) as total_mapel,
            COALESCE(SUM(jumlah_jam_per_minggu), 0) as total_jp,
            SUM(CASE WHEN jenis IN ('kejuruan', 'pilihan') THEN 1 ELSE 0 END) as total_kejuruan,
            SUM(CASE WHEN resource_key IS NOT NULL AND resource_key != '' THEN 1 ELSE 0 END) as total_lab
        ")->first();

        $stats = [
            'total_mapel'    => (int) ($statAgg->total_mapel ?? 0),
            'total_jp'       => (int) ($statAgg->total_jp ?? 0),
            'total_kejuruan' => (int) ($statAgg->total_kejuruan ?? 0),
            'total_lab'      => (int) ($statAgg->total_lab ?? 0),
        ];

        $mapels = $query->orderBy('jenis')->orderBy('kode_mapel')->paginate(25)->withQueryString();

        return view('akademik.matpel.index', compact(
            'mapels', 'ta', 'selectedTa', 'selectedTaId', 'jurusans', 'tahunAjarans', 'stats'
        ));
    }

    public function store(Request $request)
    {
        // Format tingkat
        $tingkatStr = 'X,XI,XII';
        if ($request->has('tingkats') && is_array($request->tingkats) && count($request->tingkats) > 0) {
            $tingkatStr = implode(',', $request->tingkats);
        } elseif ($request->filled('tingkat')) {
            $tingkatStr = $request->tingkat;
        }

        // Tentukan fase otomatis sesuai tingkat
        $fase = $request->fase ?? 'E';
        if (str_contains($tingkatStr, 'XI') || str_contains($tingkatStr, 'XII')) {
            $fase = 'F';
        }
        if (str_contains($tingkatStr, 'X') && !str_contains($tingkatStr, 'XI') && !str_contains($tingkatStr, 'XII')) {
            $fase = 'E';
        }
        if (str_contains($tingkatStr, 'X') && (str_contains($tingkatStr, 'XI') || str_contains($tingkatStr, 'XII'))) {
            $fase = 'E,F';
        }

        $request->validate([
            'kode_mapel'            => 'required|string|max:20',
            'nama_mapel'            => 'required|string|max:255',
            'jenis'                 => 'required|in:umum,kejuruan,pilihan,p5bk,pkl',
            'jumlah_jam_per_minggu' => 'required|integer|min:1|max:20',
            'tahun_ajaran_id'       => 'required|exists:tahun_ajarans,id',
            'jurusan_id'            => 'nullable|exists:jurusans,id',
            'deskripsi_cp'          => 'nullable|string',
            'resource_key'          => 'nullable|string|max:50',
        ]);

        AkademikMataPelajaran::create([
            'tahun_ajaran_id'       => $request->tahun_ajaran_id,
            'jurusan_id'            => in_array($request->jenis, ['kejuruan', 'pilihan']) ? $request->jurusan_id : null,
            'kode_mapel'            => strtoupper(trim($request->kode_mapel)),
            'nama_mapel'            => trim($request->nama_mapel),
            'jenis'                 => $request->jenis,
            'fase'                  => $fase,
            'tingkat'               => $tingkatStr,
            'jumlah_jam_per_minggu' => $request->jumlah_jam_per_minggu,
            'deskripsi_cp'          => $request->deskripsi_cp,
            'resource_key'          => $request->resource_key ?: null,
            'is_active'             => true,
        ]);

        return redirect()->route('akademik.matpel.index', ['tahun_ajaran_id' => $request->tahun_ajaran_id])
            ->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $mapel = AkademikMataPelajaran::findOrFail($id);

        $tingkatStr = 'X,XI,XII';
        if ($request->has('tingkats') && is_array($request->tingkats) && count($request->tingkats) > 0) {
            $tingkatStr = implode(',', $request->tingkats);
        } elseif ($request->filled('tingkat')) {
            $tingkatStr = $request->tingkat;
        } elseif (!empty($mapel->tingkat)) {
            $tingkatStr = $mapel->tingkat;
        }

        $fase = $request->fase ?? 'E';
        if (str_contains($tingkatStr, 'XI') || str_contains($tingkatStr, 'XII')) {
            $fase = 'F';
        }
        if (str_contains($tingkatStr, 'X') && !str_contains($tingkatStr, 'XI') && !str_contains($tingkatStr, 'XII')) {
            $fase = 'E';
        }
        if (str_contains($tingkatStr, 'X') && (str_contains($tingkatStr, 'XI') || str_contains($tingkatStr, 'XII'))) {
            $fase = 'E,F';
        }

        $request->validate([
            'kode_mapel'            => 'required|string|max:20',
            'nama_mapel'            => 'required|string|max:255',
            'jenis'                 => 'required|in:umum,kejuruan,pilihan,p5bk,pkl',
            'jumlah_jam_per_minggu' => 'required|integer|min:1|max:20',
            'jurusan_id'            => 'nullable|exists:jurusans,id',
            'deskripsi_cp'          => 'nullable|string',
            'resource_key'          => 'nullable|string|max:50',
        ]);

        $mapel->update([
            'jurusan_id'            => in_array($request->jenis, ['kejuruan', 'pilihan']) ? $request->jurusan_id : null,
            'kode_mapel'            => strtoupper(trim($request->kode_mapel)),
            'nama_mapel'            => trim($request->nama_mapel),
            'jenis'                 => $request->jenis,
            'fase'                  => $fase,
            'tingkat'               => $tingkatStr,
            'jumlah_jam_per_minggu' => $request->jumlah_jam_per_minggu,
            'deskripsi_cp'          => $request->deskripsi_cp,
            'resource_key'          => $request->resource_key ?: null,
            'is_active'             => $request->has('is_active') ? $request->boolean('is_active') : ($mapel->is_active ?? true),
        ]);

        // Sinkronkan juga resource_key ke jadwal yang sudah di-generate untuk mapel ini
        \App\Models\AkademikJadwalPelajaran::where('mata_pelajaran_id', $mapel->id)
            ->update(['resource_key' => $request->resource_key ?: null]);

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mapel = AkademikMataPelajaran::findOrFail($id);
        $mapel->delete();

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}

