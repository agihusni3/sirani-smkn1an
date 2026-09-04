<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('web.profil', compact('sekolah', 'gurus'));
    }
}
