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
        $modeUjians = \App\Models\ModeUjian::orderBy('tanggal_mulai', 'desc')->get();
        $modeUjianAktif = \App\Models\ModeUjian::getModeAktif();

        return view('sirani.jadwal_piket.index', compact(
            'gurus',
            'hariList',
            'jadwalGrouped',
            'hariHariIni',
            'modeUjians',
            'modeUjianAktif'
        ));
    }

    /**
     * Simpan / Perbarui pengaturan Mode Ujian (STS / SAS dsb)
     */
    public function simpanModeUjian(Request $request)
    {
        $request->validate([
            'nama_ujian'                 => 'required|string|max:100',
            'tipe'                       => 'required|in:STS,SAS,SAT,USBK,Lainnya',
            'tanggal_mulai'              => 'required|date',
            'tanggal_selesai'            => 'required|date|after_or_equal:tanggal_mulai',
            'jam_pulang_mulai'           => 'required',
            'panitia_guru_ids'           => 'nullable|array',
            'panitia_guru_ids.*'         => 'exists:gurus,id',
            'nonaktifkan_piket_reguler'  => 'nullable|boolean',
            'is_active'                  => 'nullable|boolean',
            'keterangan'                 => 'nullable|string|max:255',
        ]);

        $id = $request->input('id');
        $jamPulang = $request->input('jam_pulang_mulai');
        if (strlen($jamPulang) === 5) {
            $jamPulang .= ':00';
        }

        $data = [
            'nama_ujian'                => $request->input('nama_ujian'),
            'tipe'                      => $request->input('tipe'),
            'tanggal_mulai'             => $request->input('tanggal_mulai'),
            'tanggal_selesai'           => $request->input('tanggal_selesai'),
            'jam_pulang_mulai'          => $jamPulang,
            'panitia_guru_ids'          => array_map('intval', $request->input('panitia_guru_ids', [])),
            'nonaktifkan_piket_reguler' => $request->boolean('nonaktifkan_piket_reguler', true),
            'is_active'                 => $request->boolean('is_active', true),
            'keterangan'                => $request->input('keterangan'),
        ];

        if ($id) {
            $mode = \App\Models\ModeUjian::findOrFail($id);
            $mode->update($data);
            $msg = "Pengaturan Mode Ujian {$mode->nama_ujian} berhasil diperbarui.";
        } else {
            $mode = \App\Models\ModeUjian::create($data);
            $msg = "Mode Ujian {$mode->nama_ujian} berhasil ditambahkan dan diaktifkan.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Hapus pengaturan Mode Ujian
     */
    public function hapusModeUjian($id)
    {
        $mode = \App\Models\ModeUjian::findOrFail($id);
        $nama = $mode->nama_ujian;
        $mode->delete();

        return back()->with('success', "Mode Ujian {$nama} berhasil dihapus.");
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
