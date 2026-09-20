<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikPklSiswa;
use App\Models\AkademikPklTempat;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AkademikPklController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();

        $tempats = AkademikPklTempat::withCount('siswaPkls')->latest()->get();
        
        $querySiswa = AkademikPklSiswa::with(['siswa.rombel', 'pklTempat', 'guruPembimbing', 'tahunAjaran'])
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($request->filled('status')) {
            $querySiswa->where('status', $request->status);
        }

        if ($request->filled('tempat_id')) {
            $querySiswa->where('pkl_tempat_id', $request->tempat_id);
        }

        $siswaPkls = $querySiswa->latest()->paginate(20)->withQueryString();

        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        // Siswa kelas XI atau XII
        $siswas = Siswa::whereIn('status', ['aktif', 'pkl'])
            ->whereHas('rombels', fn($q) => $q->whereIn('tingkat', ['XI', 'XII']))
            ->orderBy('nama')
            ->get();

        return view('dcc.akademik.pkl.index', compact('tempats', 'siswaPkls', 'gurus', 'siswas', 'ta'));
    }

    public function storeTempat(Request $request)
    {
        $request->validate([
            'nama_dudi' => 'required|string|max:255',
            'bidang_usaha' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'nama_pembimbing_dudi' => 'nullable|string|max:100',
            'kontak_dudi' => 'nullable|string|max:20',
        ]);

        AkademikPklTempat::create($request->all());

        return redirect()->back()->with('success', 'Mitra DU/DI berhasil ditambahkan.');
    }

    public function storePenempatan(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'pkl_tempat_id' => 'required|exists:akademik_pkl_tempats,id',
            'guru_pembimbing_id' => 'required|exists:gurus,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status' => 'required|in:belum_berangkat,aktif,selesai',
        ]);

        AkademikPklSiswa::updateOrCreate(
            ['siswa_id' => $request->siswa_id, 'tahun_ajaran_id' => $request->tahun_ajaran_id],
            $request->all()
        );

        return redirect()->back()->with('success', 'Data penempatan PKL siswa berhasil disimpan.');
    }

    public function updateNilai(Request $request, $id)
    {
        $pklSiswa = AkademikPklSiswa::findOrFail($id);

        $request->validate([
            'nilai_pkl' => 'required|numeric|min:0|max:100',
            'predikat_pkl' => 'nullable|string|max:5',
            'catatan' => 'nullable|string',
            'status' => 'required|in:belum_berangkat,aktif,selesai',
        ]);

        $predikat = 'D';
        $nilai = floatval($request->nilai_pkl);
        if ($nilai >= 85) $predikat = 'A';
        elseif ($nilai >= 75) $predikat = 'B';
        elseif ($nilai >= 65) $predikat = 'C';

        $pklSiswa->update([
            'nilai_pkl' => $nilai,
            'predikat_pkl' => $request->predikat_pkl ?: $predikat,
            'catatan' => $request->catatan,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Nilai PKL berhasil diperbarui.');
    }

    public function destroySiswa($id)
    {
        $pklSiswa = AkademikPklSiswa::findOrFail($id);
        $pklSiswa->delete();

        return redirect()->back()->with('success', 'Penempatan PKL berhasil dihapus.');
    }
}
