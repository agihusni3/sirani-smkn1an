<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index()
    {
        $rombels = Rombel::with(['tahunAjaran', 'jurusan', 'waliKelas'])->get();
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->get();
        $jurusans = Jurusan::all();
        $gurus = \App\Models\Guru::where('status', 'aktif')->orderBy('nama')->get();

        return view('rombel.index', compact('rombels', 'tahunAjarans', 'jurusans', 'gurus'));
    }

    public function storeRombel(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'jurusan_id' => 'required|exists:jurusans,id',
            'nama_rombel' => 'required|string',
            'tingkat' => 'required|in:X,XI,XII',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        Rombel::create($data);
        return redirect()->back()->with('success', 'Rombel berhasil ditambahkan.');
    }

    public function updateRombel(Request $request, $id)
    {
        $rombel = Rombel::findOrFail($id);
        $data = $request->validate([
            'nama_rombel' => 'required|string',
            'tingkat' => 'required|in:X,XI,XII',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        $rombel->update($data);
        return redirect()->back()->with('success', 'Rombel berhasil diperbarui.');
    }

    public function destroyRombel($id)
    {
        $rombel = Rombel::findOrFail($id);
        $rombel->delete();
        return redirect()->back()->with('success', 'Rombel berhasil dihapus.');
    }

    public function storeTahunAjaran(Request $request)
    {
        $request->validate(['nama' => 'required|string']);

        if ($request->boolean('is_active')) {
            TahunAjaran::query()->update(['is_active' => false]);
        }

        TahunAjaran::create([
            'nama' => $request->input('nama'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function setActiveTahunAjaran($id)
    {
        TahunAjaran::query()->update(['is_active' => false]);
        $ta = TahunAjaran::findOrFail($id);
        $ta->update(['is_active' => true]);

        return redirect()->back()->with('success', "Tahun Ajaran {$ta->nama} diaktifkan.");
    }

    public function storeJurusan(Request $request)
    {
        $data = $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required|string|max:100',
        ]);

        $data['kode_jurusan'] = strtoupper(trim($data['kode_jurusan']));
        Jurusan::create($data);

        return redirect()->back()->with('success', "Jurusan {$data['kode_jurusan']} berhasil ditambahkan.");
    }

    public function destroyJurusan($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        if ($jurusan->rombels()->exists()) {
            return redirect()->back()->with('error', "Jurusan {$jurusan->kode_jurusan} tidak dapat dihapus karena masih digunakan oleh Rombel.");
        }

        $jurusan->delete();
        return redirect()->back()->with('success', "Jurusan {$jurusan->kode_jurusan} berhasil dihapus.");
    }
}
