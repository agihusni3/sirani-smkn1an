<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BeritaSekolah;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;

class BeritaWebController extends Controller
{
    public function index(Request $request)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $query = BeritaSekolah::published();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->cari . '%')
                  ->orWhere('konten', 'like', '%' . $request->cari . '%');
            });
        }

        $beritas = $query->latest('tanggal_publikasi')->paginate(9);

        return view('web.berita_index', compact('sekolah', 'beritas'));
    }

    public function show($slug)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $berita = BeritaSekolah::published()->where('slug', $slug)->firstOrFail();
        
        // Naikkan view count
        $berita->increment('views');

        $beritaTerkait = BeritaSekolah::published()
            ->where('id', '!=', $berita->id)
            ->where('kategori', $berita->kategori)
            ->latest('tanggal_publikasi')
            ->take(3)
            ->get();

        return view('web.berita_show', compact('sekolah', 'berita', 'beritaTerkait'));
    }
}
