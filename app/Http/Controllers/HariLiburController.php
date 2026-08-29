<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    /**
     * Tampilkan kalender hari libur & daftar tanggal merah.
     */
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', Carbon::today()->month);
        $tahun = (int) $request->input('tahun', Carbon::today()->year);

        if ($bulan < 1 || $bulan > 12) $bulan = Carbon::today()->month;
        if ($tahun < 2020 || $tahun > 2040) $tahun = Carbon::today()->year;

        $taAktif = TahunAjaran::where('is_active', true)->first();

        // 1. Data Libur di Bulan Terpilih
        $liburBulanIni = HariLibur::getLiburBulan($bulan, $tahun);

        // 2. Semua Data Libur Tahun Terpilih untuk Tabel
        $semuaLiburTahun = HariLibur::whereYear('tanggal_mulai', $tahun)
            ->orWhereYear('tanggal_selesai', $tahun)
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // 3. Bangun Matriks Kalender Bulanan (Senin - Minggu)
        $firstDayOfMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $daysInMonth     = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek  = $firstDayOfMonth->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)

        $calendarDays = [];

        // Padding hari dari bulan sebelumnya
        $prevMonthLastDay = $firstDayOfMonth->copy()->subDay();
        for ($i = $startDayOfWeek - 1; $i > 0; $i--) {
            $d = $prevMonthLastDay->copy()->subDays($i - 1);
            $calendarDays[] = [
                'day'          => $d->day,
                'date'         => $d->toDateString(),
                'isCurrent'    => false,
                'isWeekend'    => $d->isSaturday() || $d->isSunday(),
                'isToday'      => $d->isToday(),
                'holiday'      => HariLibur::getLiburHariIni($d->toDateString()),
            ];
        }

        // Hari-hari di bulan aktif
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $d = Carbon::createFromDate($tahun, $bulan, $day);
            $dateStr = $d->toDateString();
            $calendarDays[] = [
                'day'          => $day,
                'date'         => $dateStr,
                'isCurrent'    => true,
                'isWeekend'    => $d->isSaturday() || $d->isSunday(),
                'isToday'      => $d->isToday(),
                'holiday'      => HariLibur::getLiburHariIni($dateStr),
            ];
        }

        // Padding hari untuk bulan berikutnya agar genap kelipatan 7
        $remaining = 7 - (count($calendarDays) % 7);
        if ($remaining < 7 && $remaining > 0) {
            $nextMonthFirst = Carbon::createFromDate($tahun, $bulan, $daysInMonth)->addDay();
            for ($i = 0; $i < $remaining; $i++) {
                $d = $nextMonthFirst->copy()->addDays($i);
                $calendarDays[] = [
                    'day'          => $d->day,
                    'date'         => $d->toDateString(),
                    'isCurrent'    => false,
                    'isWeekend'    => $d->isSaturday() || $d->isSunday(),
                    'isToday'      => $d->isToday(),
                    'holiday'      => HariLibur::getLiburHariIni($d->toDateString()),
                ];
            }
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');

        return view('hari_libur.index', compact(
            'bulan',
            'tahun',
            'namaBulan',
            'taAktif',
            'liburBulanIni',
            'semuaLiburTahun',
            'calendarDays'
        ));
    }

    /**
     * Tambah tanggal / rentang hari libur baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_libur'      => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jenis'           => 'required|in:libur_nasional,cuti_bersama,libur_semester,khusus_sekolah',
            'keterangan'      => 'nullable|string|max:255',
        ]);

        $tglMulai   = $request->input('tanggal_mulai');
        $tglSelesai = $request->input('tanggal_selesai') ?: $tglMulai;
        $petugas    = auth()->user()->name ?? 'Administrator';

        HariLibur::create([
            'nama_libur'      => $request->input('nama_libur'),
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'jenis'           => $request->input('jenis'),
            'keterangan'      => $request->input('keterangan'),
            'created_by'      => $petugas,
        ]);

        return redirect()->route('admin.hari-libur.index', [
            'bulan' => Carbon::parse($tglMulai)->month,
            'tahun' => Carbon::parse($tglMulai)->year,
        ])->with('success', 'Hari libur "' . $request->input('nama_libur') . '" berhasil ditambahkan ke kalender!');
    }

    /**
     * Hapus jadwal hari libur.
     */
    public function destroy($id)
    {
        $libur = HariLibur::findOrFail($id);
        $nama = $libur->nama_libur;
        $libur->delete();

        return back()->with('success', "Hari libur \"{$nama}\" berhasil dihapus dari kalender.");
    }

    /**
     * Isi otomatis template hari libur nasional standar.
     */
    public function isiPreset(Request $request)
    {
        $tahun = (int) $request->input('tahun', Carbon::today()->year);
        $petugas = auth()->user()->name ?? 'Administrator';

        $presets = [
            ['nama' => 'Tahun Baru Masehi',              'mulai' => "{$tahun}-01-01", 'selesai' => "{$tahun}-01-01", 'jenis' => 'libur_nasional'],
            ['nama' => 'Hari Buruh Internasional',        'mulai' => "{$tahun}-05-01", 'selesai' => "{$tahun}-05-01", 'jenis' => 'libur_nasional'],
            ['nama' => 'Hari Lahir Pancasila',            'mulai' => "{$tahun}-06-01", 'selesai' => "{$tahun}-06-01", 'jenis' => 'libur_nasional'],
            ['nama' => 'Libur Akhir Semester Genap',      'mulai' => "{$tahun}-06-22", 'selesai' => "{$tahun}-07-10", 'jenis' => 'libur_semester'],
            ['nama' => 'Hari Kemerdekaan Republik Indonesia', 'mulai' => "{$tahun}-08-17", 'selesai' => "{$tahun}-08-17", 'jenis' => 'libur_nasional'],
            ['nama' => 'Hari Guru Nasional',             'mulai' => "{$tahun}-11-25", 'selesai' => "{$tahun}-11-25", 'jenis' => 'khusus_sekolah'],
            ['nama' => 'Libur Semester Ganjil',          'mulai' => "{$tahun}-12-21", 'selesai' => "{$tahun}-12-31", 'jenis' => 'libur_semester'],
        ];

        $count = 0;
        foreach ($presets as $p) {
            $exists = HariLibur::where('tanggal_mulai', $p['mulai'])
                ->where('nama_libur', $p['nama'])
                ->exists();

            if (!$exists) {
                HariLibur::create([
                    'nama_libur'      => $p['nama'],
                    'tanggal_mulai'   => $p['mulai'],
                    'tanggal_selesai' => $p['selesai'],
                    'jenis'           => $p['jenis'],
                    'keterangan'      => 'Jadwal libur standar kalender pendidikan',
                    'created_by'      => $petugas,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.hari-libur.index', ['tahun' => $tahun])
            ->with('success', "Berhasil menambahkan {$count} hari libur nasional & kalender pendidikan tahun {$tahun}.");
    }
}
