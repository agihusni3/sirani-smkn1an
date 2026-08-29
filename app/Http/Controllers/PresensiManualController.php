<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalHariIni;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PresensiManualController extends Controller
{
    /**
     * Halaman Presensi Manual (Lupa Kartu RFID) — Admin.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $taAktif = TahunAjaran::where('is_active', true)->first();

        // Data siswa aktif beserta rombel & kartu RFID
        $semuaSiswa = Siswa::where('status', 'aktif')
            ->with([
                'siswaRombels' => function ($q) use ($taAktif) {
                    if ($taAktif) {
                        $q->where('tahun_ajaran_id', $taAktif->id)
                          ->where('status_keanggotaan', 'aktif')
                          ->with('rombel');
                    }
                }
            ])
            ->get();

        // Data guru aktif
        $semuaGuru = Guru::where('status', 'aktif')->get();

        // Jadwal hari ini
        $jadwalHariIni = JadwalHariIni::getJadwalAktif($today);

        // Riwayat presensi manual hari ini
        $presensiManualHariIni = Absensi::with(['siswa', 'guru', 'siswaRombel.rombel'])
            ->where('tanggal', $today)
            ->where('sumber_absen', 'manual_lupa_kartu')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('presensi_manual.index', compact(
            'today',
            'semuaSiswa',
            'semuaGuru',
            'jadwalHariIni',
            'presensiManualHariIni'
        ));
    }

    /**
     * Simpan presensi manual (lupa kartu) — dipakai oleh Admin dan Guru Piket.
     */
    public static function prosesPresensiManual(Request $request, ?string $pencatat = null): array
    {
        $request->validate([
            'kategori'   => 'required|in:siswa,guru',
            'pemilik_id' => 'required|integer',
            'sesi'       => 'required|in:masuk,pulang',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kategori  = $request->input('kategori');
        $pemilikId = $request->input('pemilik_id');
        $sesi      = $request->input('sesi');
        $keterangan = $request->input('keterangan') ?? 'Presensi Manual / Terkendala Face ID';
        $today     = Carbon::today()->toDateString();
        $now       = Carbon::now();

        // Ambil jadwal hari ini
        $jadwal = JadwalHariIni::getJadwalAktif($today);

        // Cari atau buat record absensi hari ini
        $absensi = Absensi::where('pemilik_type', $kategori)
            ->where('pemilik_id', $pemilikId)
            ->where('tanggal', $today)
            ->first();

        if ($sesi === 'masuk') {
            if ($absensi && $absensi->jam_masuk) {
                return [
                    'success' => false,
                    'message' => 'Sudah ada catatan absen masuk hari ini untuk yang bersangkutan.',
                ];
            }

            // Evaluasi status berdasarkan toleransi jam masuk
            $batasJamMasuk = Carbon::parse($today . ' ' . $jadwal->jam_masuk_toleransi);
            $status = $now->lte($batasJamMasuk) ? 'hadir' : 'terlambat';

            // Cari siswa_rombel_id jika siswa
            $siswaRombelId = null;
            if ($kategori === 'siswa') {
                $taAktif = TahunAjaran::where('is_active', true)->first();
                if ($taAktif) {
                    $membership = SiswaRombel::where('siswa_id', $pemilikId)
                        ->where('tahun_ajaran_id', $taAktif->id)
                        ->where('status_keanggotaan', 'aktif')
                        ->first();
                    $siswaRombelId = $membership?->id;
                }
            }

            Absensi::updateOrCreate(
                [
                    'pemilik_type' => $kategori,
                    'pemilik_id'   => $pemilikId,
                    'tanggal'      => $today,
                ],
                [
                    'siswa_rombel_id' => $siswaRombelId,
                    'jam_masuk'       => $now->toTimeString(),
                    'status'          => $status,
                    'sumber_absen'    => 'manual_lupa_kartu',
                ]
            );

            $nama = $kategori === 'siswa'
                ? (Siswa::find($pemilikId)?->nama ?? '-')
                : (Guru::find($pemilikId)?->nama ?? '-');

            return [
                'success' => true,
                'message' => "Presensi masuk berhasil dicatat untuk {$nama} (Status: " . strtoupper($status) . "). Sumber: Manual (Lupa Kartu). Dicatat oleh: {$pencatat}.",
            ];
        }

        // sesi === 'pulang'
        if (!$absensi || !$absensi->jam_masuk) {
            return [
                'success' => false,
                'message' => 'Belum ada catatan absen masuk hari ini. Silakan catat absen masuk terlebih dahulu.',
            ];
        }

        if ($absensi->jam_pulang) {
            return [
                'success' => false,
                'message' => 'Sudah ada catatan absen pulang hari ini untuk yang bersangkutan.',
            ];
        }

        $absensi->update([
            'jam_pulang'   => $now->toTimeString(),
            'sumber_absen' => 'manual_lupa_kartu',
        ]);

        $nama = $kategori === 'siswa'
            ? (Siswa::find($pemilikId)?->nama ?? '-')
            : (Guru::find($pemilikId)?->nama ?? '-');

        return [
            'success' => true,
            'message' => "Presensi pulang berhasil dicatat untuk {$nama} (Jam Pulang: {$now->format('H:i')} WIB). Dicatat oleh: {$pencatat}.",
        ];
    }

    /**
     * Store presensi manual oleh Admin.
     */
    public function store(Request $request)
    {
        $pencatat = auth()->user()->name ?? 'Administrator';
        $result = self::prosesPresensiManual($request, $pencatat);

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }
}
