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
        $jurusans = Jurusan::all();

        $query = AkademikMataPelajaran::with(['tahunAjaran', 'jurusan', 'gurus', 'distribusiMengajars.guru', 'distribusiMengajars.rombel']);

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } elseif ($ta) {
            $query->where('tahun_ajaran_id', $ta->id);
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
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_mapel', 'like', "%{$s}%")
                  ->orWhere('kode_mapel', 'like', "%{$s}%");
            });
        }

        $mapels = $query->orderBy('jenis')->orderBy('nama_mapel')->paginate(20)->withQueryString();
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        return view('dcc.akademik.matpel.index', compact('mapels', 'ta', 'jurusans', 'tahunAjarans'));
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

        // Determine fase based on tingkat if not explicitly set
        $fase = $request->fase ?? 'E';
        if (str_contains($tingkatStr, 'XI') || str_contains($tingkatStr, 'XII')) {
            $fase = 'F';
        }
        if (str_contains($tingkatStr, 'X') && !str_contains($tingkatStr, 'XI') && !str_contains($tingkatStr, 'XII')) {
            $fase = 'E';
        }

        $request->validate([
            'kode_mapel' => 'required|string|max:20',
            'nama_mapel' => 'required|string|max:255',
            'jenis' => 'required|in:umum,kejuruan,pilihan,p5bk,pkl',
            'jumlah_jam_per_minggu' => 'required|integer|min:1|max:20',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'deskripsi_cp' => 'nullable|string',
            'resource_key' => 'nullable|string|max:50',
        ]);

        AkademikMataPelajaran::create([
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'jurusan_id' => in_array($request->jenis, ['kejuruan', 'pilihan']) ? $request->jurusan_id : null,
            'kode_mapel' => strtoupper(trim($request->kode_mapel)),
            'nama_mapel' => trim($request->nama_mapel),
            'jenis' => $request->jenis,
            'fase' => $fase,
            'tingkat' => $tingkatStr,
            'jumlah_jam_per_minggu' => $request->jumlah_jam_per_minggu,
            'deskripsi_cp' => $request->deskripsi_cp,
            'resource_key' => $request->resource_key ?: null,
            'is_active' => true,
        ]);

        return redirect()->route('akademik.matpel.index')
            ->with('success', 'Mata Pelajaran berhasil ditambahkan ke kurikulum.');
    }

    public function update(Request $request, $id)
    {
        $mapel = AkademikMataPelajaran::findOrFail($id);

        $tingkatStr = $mapel->tingkat ?? 'X,XI,XII';
        if ($request->has('tingkats') && is_array($request->tingkats) && count($request->tingkats) > 0) {
            $tingkatStr = implode(',', $request->tingkats);
        } elseif ($request->filled('tingkat')) {
            $tingkatStr = $request->tingkat;
        }

        $fase = $request->fase ?? $mapel->fase;
        if (str_contains($tingkatStr, 'XI') || str_contains($tingkatStr, 'XII')) {
            $fase = 'F';
        }
        if (str_contains($tingkatStr, 'X') && !str_contains($tingkatStr, 'XI') && !str_contains($tingkatStr, 'XII')) {
            $fase = 'E';
        }

        $request->validate([
            'kode_mapel' => 'required|string|max:20',
            'nama_mapel' => 'required|string|max:255',
            'jenis' => 'required|in:umum,kejuruan,pilihan,p5bk,pkl',
            'jumlah_jam_per_minggu' => 'required|integer|min:1|max:20',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'deskripsi_cp' => 'nullable|string',
            'resource_key' => 'nullable|string|max:50',
        ]);

        $mapel->update([
            'jurusan_id' => in_array($request->jenis, ['kejuruan', 'pilihan']) ? $request->jurusan_id : null,
            'kode_mapel' => strtoupper(trim($request->kode_mapel)),
            'nama_mapel' => trim($request->nama_mapel),
            'jenis' => $request->jenis,
            'fase' => $fase,
            'tingkat' => $tingkatStr,
            'jumlah_jam_per_minggu' => $request->jumlah_jam_per_minggu,
            'deskripsi_cp' => $request->deskripsi_cp,
            'resource_key' => $request->resource_key ?: null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($mapel->is_active ?? true),
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
