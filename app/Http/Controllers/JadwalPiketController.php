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

        // Parameter Tanggal Laporan Kehadiran Petugas Piket
        $filterTanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $carbonTgl     = Carbon::parse($filterTanggal);
        $hariPilihan   = JadwalPiket::getHariIndonesia($carbonTgl);

        // Petugas piket untuk hari pilihan
        $petugasPiket = JadwalPiket::where('hari', $hariPilihan)
            ->with('guru')
            ->get();

        // Ambil data absensi guru piket pada tanggal tersebut
        $guruIds = $petugasPiket->pluck('guru_id');
        $absensiMap = Absensi::where('pemilik_type', 'guru')
            ->whereIn('pemilik_id', $guruIds)
            ->where('tanggal', $filterTanggal)
            ->get()
            ->keyBy('pemilik_id');

        // Status sesi smart gate pada tanggal tersebut
        $jadwalHarian = JadwalHariIni::where('tanggal', $filterTanggal)->first();

        // Hitung ringkasan statistik
        $totalTugas      = $petugasPiket->count();
        $totalHadir      = 0;
        $totalTerlambat  = 0;
        $totalBelumHadir = 0;

        foreach ($petugasPiket as $p) {
            $abs = $absensiMap->get($p->guru_id);
            if ($abs) {
                $totalHadir++;
                if ($abs->status === 'terlambat') {
                    $totalTerlambat++;
                }
            } else {
                $totalBelumHadir++;
            }
        }

        // ═══════════════════════════════════════════════════════
        // REKAP SISWA BELUM ABSEN PULANG
        // Siswa yang sudah absen masuk (jam_masuk ada) tapi
        // belum absen pulang (jam_pulang NULL) pada tanggal filter
        // ═══════════════════════════════════════════════════════
        $siswaBelumPulangRaw = Absensi::with([
                'siswaRombel.siswa',
                'siswaRombel.rombel.jurusan',
            ])
            ->where('pemilik_type', 'siswa')
            ->where('tanggal', $filterTanggal)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_pulang')
            ->whereIn('status', ['hadir', 'terlambat'])
            ->get();

        // Kelompokkan per nama rombel, urutkan abjad
        $siswaBelumPulangGrouped = $siswaBelumPulangRaw
            ->filter(fn($a) => $a->siswaRombel && $a->siswaRombel->rombel)
            ->sortBy(fn($a) => $a->siswaRombel->rombel->nama_rombel)
            ->groupBy(fn($a) => $a->siswaRombel->rombel->nama_rombel ?? 'Tanpa Kelas');

        $totalSiswaBelumPulang = $siswaBelumPulangRaw->count();

        return view('jadwal_piket.index', compact(
            'gurus',
            'hariList',
            'jadwalGrouped',
            'hariHariIni',
            'filterTanggal',
            'carbonTgl',
            'hariPilihan',
            'petugasPiket',
            'absensiMap',
            'jadwalHarian',
            'totalTugas',
            'totalHadir',
            'totalTerlambat',
            'totalBelumHadir',
            'siswaBelumPulangGrouped',
            'totalSiswaBelumPulang'
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
