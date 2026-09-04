<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BeritaSekolah;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use App\Models\WebsiteBanner;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        
        $stats = [
            'total_siswa' => Siswa::where('status', 'aktif')->count(),
            'total_guru' => Guru::where('status', 'aktif')->count(),
            'total_jurusan' => Jurusan::count(),
            'persentase_kelulusan' => 100, // Standar SMK Vokasi
        ];

        $jurusans = Jurusan::all();
        $beritas = BeritaSekolah::published()
            ->latest('tanggal_publikasi')
            ->take(4)
            ->get();

        $heroBanners = WebsiteBanner::active()
            ->hero()
            ->orderBy('urutan')
            ->get();

        return view('web.index', compact('sekolah', 'stats', 'jurusans', 'beritas', 'heroBanners'));
    }
}
