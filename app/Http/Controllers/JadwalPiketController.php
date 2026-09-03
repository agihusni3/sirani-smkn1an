<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalHariIni;
use App\Models\JadwalPiket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalPiketController extends Controller
{
    public function index(Request $request)
    {
        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $jadwalGrouped = JadwalPiket::with('guru')
            ->get()
            ->groupBy('hari');

        $hariHariIni = JadwalPiket::getHariIndonesia();

        return view('jadwal_piket.index', compact(
            'gurus',
            'hariList',
            'jadwalGrouped',
            'hariHariIni'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'guru_id' => 'required|exists:gurus,id',
            'keterangan' => 'nullable|string|max:100',
        ]);

        $exists = JadwalPiket::where('hari', $request->input('hari'))
            ->where('guru_id', $request->input('guru_id'))
            ->exists();

        if ($exists) {
            return back()->with('error', 'Guru tersebut sudah terdaftar dalam jadwal piket hari ' . $request->input('hari') . '.');
        }

        JadwalPiket::create([
            'hari' => $request->input('hari'),
            'guru_id' => $request->input('guru_id'),
            'keterangan' => $request->input('keterangan'),
        ]);

        $guru = Guru::find($request->input('guru_id'));
        return back()->with('success', "Berhasil menambahkan {$guru->nama} ke jadwal piket hari {$request->input('hari')}.");
    }

    public function destroy($id)
    {
        $jadwal = JadwalPiket::with('guru')->findOrFail($id);
        $namaGuru = $jadwal->guru->nama ?? 'Guru';
        $hari = $jadwal->hari;
        $jadwal->delete();

        return back()->with('success', "Penugasan piket {$namaGuru} pada hari {$hari} berhasil dihapus.");
    }
}
