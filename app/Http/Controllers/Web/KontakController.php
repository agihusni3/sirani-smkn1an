<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        return view('web.kontak', compact('sekolah'));
    }
}
