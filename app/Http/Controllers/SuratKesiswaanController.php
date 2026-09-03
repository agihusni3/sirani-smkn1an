<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuratKesiswaanController extends Controller
{
    /**
     * Tampilkan lembar cetak fisik resmi surat panggilan/pembinaan kesiswaan beserta lampiran rekap absensi.
     */
    public function cetak(Request $request, $id = null)
    {
        $notifikasi = null;
        $siswa = null;

        if ($id) {
            $notifikasi = NotifikasiOrtu::with('siswa.siswaRombels.rombel.waliKelas', 'siswa.siswaRombels.rombel.jurusan')->find($id);
            $siswa = $notifikasi?->siswa;
        }

        if (!$notifikasi && $request->has('notif_id')) {
            $notifikasi = NotifikasiOrtu::with('siswa.siswaRombels.rombel.waliKelas', 'siswa.siswaRombels.rombel.jurusan')->find($request->get('notif_id'));
            if ($notifikasi && !$siswa) {
                $siswa = $notifikasi->siswa;
            }
        }

        if (!$siswa && $request->has('siswa_id')) {
            $siswa = Siswa::with('siswaRombels.rombel.waliKelas', 'siswaRombels.rombel.jurusan')->find($request->get('siswa_id'));
        }

        if (!$siswa && $request->has('nisn')) {
            $siswa = Siswa::with('siswaRombels.rombel.waliKelas', 'siswaRombels.rombel.jurusan')->where('nisn', $request->get('nisn'))->first();
        }

        if (!$siswa) {
            $latestNotif = NotifikasiOrtu::with('siswa.siswaRombels.rombel.waliKelas', 'siswa.siswaRombels.rombel.jurusan')
                ->where('kategori', 'panggilan_ortu')
                ->latest()
                ->first();
            if ($latestNotif && $latestNotif->siswa) {
                $notifikasi = $latestNotif;
                $siswa = $latestNotif->siswa;
            } else {
                $siswa = Siswa::with('siswaRombels.rombel.waliKelas', 'siswaRombels.rombel.jurusan')
                    ->where('status', 'aktif')
                    ->first();
            }
        }

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa untuk pembuatan surat tidak ditemukan.');
        }

        $siswaRombel = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()
            ?? $siswa->siswaRombels->last();
        $rombel = $siswaRombel?->rombel;
        $waliKelas = $rombel?->waliKelas;
        if (!$waliKelas) {
            $waliKelas = \App\Models\Guru::where('status', 'aktif')->whereNotNull('nama')->first();
        }

        $guruBk = \App\Models\User::where('role', 'guru_bk')->with('guru')->first()?->guru
            ?? \App\Models\Guru::where('status', 'aktif')
                ->where(function ($q) {
                    $q->where('jabatan', 'like', '%BK%')
                      ->orWhere('jabatan', 'like', '%Bimbingan%')
                      ->orWhere('jabatan', 'like', '%Konseling%');
                })
                ->first()
            ?? $waliKelas;

        $sekolah = PengaturanSekolah::getAktif();

        // Parameter surat
        $kategori = $request->get('kategori', $notifikasi?->kategori ?? 'panggilan_ortu');
        if ($kategori === 'berita_acara') {
            $judulSurat = 'BERITA ACARA TINDAK LANJUT & PEMBINAAN KESISWAAN';
        } elseif ($kategori === 'panggilan_ortu') {
            $judulSurat = 'SURAT PANGGILAN ORANG TUA / WALI MURID';
        } elseif ($kategori === 'pembinaan') {
            $judulSurat = 'SURAT PERINGATAN KEDISIPLINAN SISWA';
        } else {
            $judulSurat = 'SURAT PEMBERITAHUAN KESISWAAN';
        }

        $bulanRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $bln = $bulanRomawi[Carbon::today()->month] ?? 'VIII';

        $nomorSurat = $request->get('nomor_surat');
        if (!$nomorSurat) {
            if ($kategori === 'berita_acara') {
                $nomorSurat = 'BA-BK/' . str_pad($siswa->id, 3, '0', STR_PAD_LEFT) . '/' . ($rombel->nama_rombel ?? 'SMK') . '/' . $bln . '/' . Carbon::today()->year;
            } else {
                $nomorSurat = '421.5/' . str_pad($siswa->id, 3, '0', STR_PAD_LEFT) . '/SMKN1-AN/KESISWAAN/' . $bln . '/' . Carbon::today()->year;
            }
        }

        $hariTanggal = $request->get('hari_tanggal', Carbon::today()->translatedFormat('l, d F Y'));
        $waktu = $request->get('waktu', '08:30 WIB s.d. Selesai');
        $tempat = $request->get('tempat', 'Ruang Bimbingan & Konseling (BK) SMKN 1 Air Naningan');
        $menghadap = $request->get('menghadap', ($waliKelas ? $waliKelas->nama . ' (Wali Kelas ' . $rombel?->nama_rombel . ')' : 'Wali Kelas') . ' & Guru BK');
        $keperluan = $request->get('keperluan', 'Koordinasi & Pembinaan Kedisiplinan Presensi Belajar Siswa');

        // Parameter Dinamis Khusus Berita Acara
        $namaWaliHadir = $request->get('nama_wali_hadir', $notifikasi?->nama_wali_hadir ?: ($siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua Siswa'));
        $waktuDiskusi = $request->get('waktu_diskusi', $notifikasi?->waktu_diskusi ? $notifikasi->waktu_diskusi->translatedFormat('l, d F Y - H:i') . ' WIB' : Carbon::now()->translatedFormat('l, d F Y - H:i') . ' WIB');
        $catatanHasil = $request->get('catatan_hasil', $notifikasi?->catatan_hasil_diskusi ?: 'Telah dilaksanakan musyawarah pembinaan dan penyampaian komitmen kedisiplinan belajar. Orang tua bersedia mengawasi dan mendampingi ananda agar hadir tepat waktu di sekolah.');
        $statusPembinaan = $request->get('status_pembinaan', $notifikasi?->status_pembinaan ?? 'selesai');

        // Data Lampiran Rekap Absensi Siswa
        $bulanParam = $request->get('bulan');
        $absensiBaseQuery = Absensi::where(function ($q) use ($siswa) {
            $q->where(function ($sq) use ($siswa) {
                $sq->where('pemilik_id', $siswa->id)->where('pemilik_type', 'siswa');
            })->orWhere('siswa_id', $siswa->id);
        });

        if ($bulanParam) {
            try {
                $startOfMonth = Carbon::createFromFormat('Y-m', $bulanParam)->startOfMonth();
                $endOfMonth   = Carbon::createFromFormat('Y-m', $bulanParam)->endOfMonth();
                $absensis = (clone $absensiBaseQuery)
                    ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orderBy('tanggal', 'desc')
                    ->get();
                $periodeTeks = 'Bulan ' . Carbon::createFromFormat('Y-m', $bulanParam)->translatedFormat('F Y');
            } catch (\Exception $e) {
                $absensis = (clone $absensiBaseQuery)->orderBy('tanggal', 'desc')->get();
                $periodeTeks = 'Rekapitulasi Semester Berjalan (TP ' . ($sekolah->tahun_ajaran_aktif ?? '2026/2027') . ')';
            }
        } else {
            // Default: Tampilkan seluruh rekapitulasi semester berjalan yang tersinkron dengan buku kasus disiplin
            $absensis = (clone $absensiBaseQuery)
                ->orderBy('tanggal', 'desc')
                ->get();
            $periodeTeks = 'Rekapitulasi Semester Berjalan (TP ' . ($sekolah->tahun_ajaran_aktif ?? '2026/2027') . ')';
        }

        $hadir     = $absensis->where('status', 'hadir')->count();
        $terlambat = $absensis->where('status', 'terlambat')->count();
        $izin      = $absensis->whereIn('status', ['izin', 'sakit'])->count();
        $alpha     = $absensis->where('status', 'alpha')->count();
        $bolos     = $absensis->where('status', 'bolos')->count();
        $totalHari = $hadir + $terlambat + $izin + $alpha + $bolos;
        $persen    = $totalHari > 0 ? round((($hadir + $terlambat) / $totalHari) * 100, 1) : ($alpha + $bolos > 0 ? 0 : 100);

        $stats = [
            'hadir'     => $hadir,
            'terlambat' => $terlambat,
            'izin'      => $izin,
            'alpha'     => $alpha,
            'bolos'     => $bolos,
            'persen'    => $persen,
        ];

        return view('surat.cetak', compact(
            'sekolah',
            'siswa',
            'rombel',
            'waliKelas',
            'guruBk',
            'notifikasi',
            'judulSurat',
            'nomorSurat',
            'kategori',
            'hariTanggal',
            'waktu',
            'tempat',
            'menghadap',
            'keperluan',
            'namaWaliHadir',
            'waktuDiskusi',
            'catatanHasil',
            'statusPembinaan',
            'absensis',
            'stats',
            'periodeTeks'
        ));
    }

    /**
     * Cetak Surat Bebas Masalah & Resume Kumulatif Presensi dan Disiplin Siswa (Clearance Sheet).
     */
    public function cetakSuratBebasMasalah(Request $request, $siswaId)
    {
        $siswa = Siswa::with([
            'siswaRombels.rombel.waliKelas',
            'siswaRombels.rombel.jurusan',
            'siswaRombels.tahunAjaran'
        ])->findOrFail($siswaId);

        $sekolah = PengaturanSekolah::getAktif();
        $rombelAktif = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel
            ?? $siswa->siswaRombels->last()?->rombel;
        $waliKelas = $rombelAktif?->waliKelas;

        // Ambil semua histori presensi kumulatif
        $absensis = Absensi::where('siswa_id', $siswa->id)->orderBy('tanggal', 'desc')->get();
        $hadir = $absensis->where('status', 'hadir')->count();
        $terlambat = $absensis->where('status', 'terlambat')->count();
        $izin = $absensis->where('status', 'izin')->count();
        $sakit = $absensis->where('status', 'sakit')->count();
        $alpha = $absensis->where('status', 'alpha')->count();
        $bolos = $absensis->where('status', 'bolos')->count();
        $totalPresensi = $hadir + $terlambat + $izin + $sakit + $alpha + $bolos;
        $persenKehadiran = $totalPresensi > 0 ? round((($hadir + $terlambat) / $totalPresensi) * 100, 1) : 100;

        $stats = [
            'hadir'           => $hadir,
            'terlambat'       => $terlambat,
            'izin'            => $izin,
            'sakit'           => $sakit,
            'alpha'           => $alpha,
            'bolos'           => $bolos,
            'total'           => $totalPresensi,
            'persen'          => $persenKehadiran,
        ];

        // Ambil data kedisiplinan
        $kasusList = \App\Models\KasusDisiplin::where('siswa_id', $siswa->id)->get();
        $kasusAktif = $kasusList->where('is_active', true)->where('status_tahap', '!=', 'selesai_pembinaan')->count();
        $totalKasusSelesai = $kasusList->where('status_tahap', 'selesai_pembinaan')->count();
        $isBebasMasalah = ($kasusAktif === 0);

        $nomorSurat = '421.5/' . sprintf('%03d', $siswa->id) . '/SMKN1AN/SKKB/' . date('Y');
        $tanggalSurat = Carbon::now()->translatedFormat('d F Y');

        return view('siswa.surat_bebas_masalah', compact(
            'sekolah',
            'siswa',
            'rombelAktif',
            'waliKelas',
            'stats',
            'kasusList',
            'kasusAktif',
            'totalKasusSelesai',
            'isBebasMasalah',
            'nomorSurat',
            'tanggalSurat'
        ));
    }
}
