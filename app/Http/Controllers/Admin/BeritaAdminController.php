<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeritaSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeritaAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = BeritaSekolah::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cari')) {
            $query->where('judul', 'like', '%' . $request->cari . '%');
        }

        $beritas = $query->latest('tanggal_publikasi')->paginate(15);

        return view('admin.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'kategori' => 'required|in:berita,pengumuman,prestasi,agenda',
            'ringkasan' => 'nullable|string|max:300',
            'konten' => 'required|string',
            'gambar_sampul' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status' => 'required|in:draft,published',
            'is_pinned' => 'boolean',
        ]);

        if ($request->hasFile('gambar_sampul')) {
            $validated['gambar_sampul'] = $request->file('gambar_sampul')->store('berita', 'public');
        }

        $validated['author_name'] = auth()->user()->nama ?? auth()->user()->name ?? 'Admin Humas';
        $validated['tanggal_publikasi'] = now();
        $validated['is_pinned'] = $request->has('is_pinned');

        BeritaSekolah::create($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita / Pengumuman berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $berita = BeritaSekolah::findOrFail($id);
        return view('admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $berita = BeritaSekolah::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'kategori' => 'required|in:berita,pengumuman,prestasi,agenda',
            'ringkasan' => 'nullable|string|max:300',
            'konten' => 'required|string',
            'gambar_sampul' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status' => 'required|in:draft,published',
            'is_pinned' => 'boolean',
        ]);

        if ($request->hasFile('gambar_sampul')) {
            $validated['gambar_sampul'] = $request->file('gambar_sampul')->store('berita', 'public');
        }

        $validated['is_pinned'] = $request->has('is_pinned');
        $berita->update($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $berita = BeritaSekolah::findOrFail($id);
        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}
