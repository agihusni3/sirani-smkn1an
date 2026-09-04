<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        $jurusans = Jurusan::all();

        return view('web.jurusan_index', compact('sekolah', 'jurusans'));
    }

    public function show($kode)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $jurusan = Jurusan::where('kode_jurusan', strtoupper($kode))->firstOrFail();
        $semuaJurusan = Jurusan::all();

        return view('web.jurusan_show', compact('sekolah', 'jurusan', 'semuaJurusan'));
    }
}
