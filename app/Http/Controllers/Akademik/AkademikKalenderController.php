<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikKalender;
use App\Models\AkademikKalenderItem;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AkademikKalenderController extends Controller
{
    /**
     * Tampilkan halaman Kalender Pendidikan & Penetapan RPE
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $canManage = $user && ($user->isAdmin() || $user->isWakaKurikulum() || $user->hasAvailableRole('waka_kurikulum'));

        $tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get();
        $taAktif = TahunAjaran::where('is_active', true)->first() ?: $tahunAjarans->first();

        $tahunAjaranId = (int) $request->input('tahun_ajaran_id', $taAktif?->id ?? 0);
        $semester = (int) $request->input('semester', 1);

        $selectedTa = $tahunAjarans->firstWhere('id', $tahunAjaranId) ?: $taAktif;

        $kalender = null;
        $itemsByMonth = collect([]);

        $calendarMonths = [];
        if ($selectedTa) {
            $kalender = AkademikKalender::with('items')
                ->where('tahun_ajaran_id', $selectedTa->id)
                ->where('semester', $semester)
                ->first();

            if ($kalender) {
                $itemsByMonth = $kalender->items->groupBy('bulan');
            }

            $tahunAwal = $selectedTa->tahun_awal;
            $monthsConfig = $semester == 1 ? [
                ['name' => 'Juli', 'm' => 7, 'year' => $tahunAwal],
                ['name' => 'Agustus', 'm' => 8, 'year' => $tahunAwal],
                ['name' => 'September', 'm' => 9, 'year' => $tahunAwal],
                ['name' => 'Oktober', 'm' => 10, 'year' => $tahunAwal],
                ['name' => 'November', 'm' => 11, 'year' => $tahunAwal],
                ['name' => 'Desember', 'm' => 12, 'year' => $tahunAwal],
            ] : [
                ['name' => 'Januari', 'm' => 1, 'year' => $tahunAwal + 1],
                ['name' => 'Februari', 'm' => 2, 'year' => $tahunAwal + 1],
                ['name' => 'Maret', 'm' => 3, 'year' => $tahunAwal + 1],
                ['name' => 'April', 'm' => 4, 'year' => $tahunAwal + 1],
                ['name' => 'Mei', 'm' => 5, 'year' => $tahunAwal + 1],
                ['name' => 'Juni', 'm' => 6, 'year' => $tahunAwal + 1],
            ];

            foreach ($monthsConfig as $mc) {
                $firstDay = \Carbon\Carbon::createFromDate($mc['year'], $mc['m'], 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startDayOfWeek = $firstDay->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)

                $monthKaldikItems = $kalender ? $kalender->items->where('bulan', $mc['name'])->values() : collect([]);
                $totalPekanBulan = $monthKaldikItems->count() ?: 4;

                $weeksGrid = [];
                $currentDay = 1;

                // Baris pertama (minggu ke-1)
                $firstWeekDays = [];
                for ($pad = 1; $pad < $startDayOfWeek; $pad++) {
                    $firstWeekDays[] = null;
                }
                while (count($firstWeekDays) < 7 && $currentDay <= $daysInMonth) {
                    $firstWeekDays[] = $currentDay++;
                }
                $weeksGrid[] = $firstWeekDays;

                // Baris-baris berikutnya
                while ($currentDay <= $daysInMonth) {
                    $weekDays = [];
                    while (count($weekDays) < 7 && $currentDay <= $daysInMonth) {
                        $weekDays[] = $currentDay++;
                    }
                    while (count($weekDays) < 7) {
                        $weekDays[] = null;
                    }
                    $weeksGrid[] = $weekDays;
                }

                $weeksData = [];
                foreach ($weeksGrid as $wIdx => $days) {
                    $kaldikIndex = min($wIdx, $totalPekanBulan - 1);
                    $kItem = $monthKaldikItems->get($kaldikIndex);

                    $validDays = array_filter($days);
                    $dateRange = '';
                    if (!empty($validDays)) {
                        $minD = min($validDays);
                        $maxD = max($validDays);
                        $dateRange = ($minD == $maxD) ? "{$minD} {$mc['name']}" : "{$minD} - {$maxD} {$mc['name']}";
                    }

                    $daysData = [];
                    foreach ($days as $dayIdx => $dayNum) {
                        if ($dayNum === null) {
                            $daysData[] = null;
                            continue;
                        }

                        $dateStr = sprintf('%04d-%02d-%02d', $mc['year'], $mc['m'], $dayNum);

                        // Cek apakah ada agenda spesifik tanggal yang meng-cover tanggal ini
                        $specificItem = $kalender ? $kalender->items->first(function ($it) use ($dateStr) {
                            return $it->isDateCovered($dateStr);
                        }) : null;

                        $resolvedItem = $specificItem;
                        if (!$resolvedItem && $kItem && !$kItem->tanggal_mulai) {
                            $resolvedItem = $kItem;
                        }

                        $daysData[] = [
                            'day_num' => $dayNum,
                            'date_str' => $dateStr,
                            'is_weekend' => ($dayIdx >= 5),
                            'agenda_item' => $resolvedItem,
                            'has_specific_date' => (bool) $specificItem,
                        ];
                    }

                    $weeksData[] = [
                        'week_number' => $wIdx + 1,
                        'kaldik_item' => $kItem,
                        'days' => $days,
                        'days_data' => $daysData,
                        'date_range' => $dateRange,
                    ];
                }

                $calendarMonths[] = [
                    'name' => $mc['name'],
                    'year' => $mc['year'],
                    'month_num' => $mc['m'],
                    'days_in_month' => $daysInMonth,
                    'kaldik_items' => $monthKaldikItems,
                    'weeks' => $weeksData,
                    'efektif_count' => $monthKaldikItems->where('jenis', 'efektif')->count(),
                    'non_efektif_count' => $monthKaldikItems->where('jenis', 'non_efektif')->count(),
                ];
            }
        }

        return view('dcc.akademik.kalender.index', [
            'tahunAjarans' => $tahunAjarans,
            'selectedTa' => $selectedTa,
            'semester' => $semester,
            'kalender' => $kalender,
            'itemsByMonth' => $itemsByMonth,
            'calendarMonths' => $calendarMonths,
            'canManage' => $canManage,
        ]);
    }

    /**
     * Generate template default standar SMK (1 klik)
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
        ]);

        $kalender = AkademikKalender::generateDefaultSemester(
            (int) $validated['tahun_ajaran_id'],
            (int) $validated['semester'],
            auth()->id()
        );

        return redirect()->route('akademik.kalender.index', [
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'semester' => $validated['semester'],
        ])->with('success', 'Berhasil membuat Template Kalender Pendidikan & Penetapan RPE standar SMK.');
    }

    /**
     * Update butir pekan kalender (efektif/non-efektif, kategori, agenda, rentang tanggal)
     */
    public function updateItem(Request $request, $id)
    {
        $item = AkademikKalenderItem::with('kalender')->findOrFail($id);

        if ($item->kalender->is_locked && !(auth()->user()->isAdmin())) {
            return back()->with('error', 'Kalender Pendidikan telah dikunci resmi oleh Waka Kurikulum.');
        }

        $validated = $request->validate([
            'jenis' => 'required|in:efektif,non_efektif',
            'kategori' => 'required|string|max:30',
            'keterangan' => 'nullable|string|max:255',
            'warna' => 'nullable|string|max:30',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'is_single_day' => 'nullable',
        ]);

        $tglMulai = $validated['tanggal_mulai'] ?? null;
        $tglSelesai = $validated['tanggal_selesai'] ?? null;
        if ($tglMulai) {
            if (!empty($request->is_single_day) || empty($tglSelesai) || $tglSelesai < $tglMulai) {
                $tglSelesai = $tglMulai;
            }
        } else {
            $tglMulai = null;
            $tglSelesai = null;
        }

        // Tetapkan warna default sesuai kategori jika tidak diisi
        $warna = $validated['warna'] ?? match ($validated['kategori']) {
            'mpls' => '#f59e0b',
            'sts' => '#8b5cf6',
            'sas', 'sat' => '#ec4899',
            'ukk' => '#f97316',
            'rapor' => '#10b981',
            'libur' => '#ef4444',
            'pkl' => '#06b6d4',
            default => ($validated['jenis'] === 'efektif' ? '#2563eb' : '#64748b'),
        };

        $item->update([
            'tanggal_mulai' => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'jenis' => $validated['jenis'],
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'],
            'warna' => $warna,
        ]);

        $item->kalender->hitungUlangPekan();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pekan berhasil diperbarui.',
                'pekan_efektif' => $item->kalender->pekan_efektif,
                'pekan_cadangan' => $item->kalender->pekan_cadangan,
            ]);
        }

        $rentangText = $item->formatRentangTanggal() ? " ({$item->formatRentangTanggal()})" : "";
        return back()->with('success', "Pekan ke-{$item->minggu_ke} ({$item->bulan}){$rentangText} berhasil diperbarui.");
    }

    /**
     * Kunci / Buka Kalender Pendidikan sebagai acuan resmi
     */
    public function toggleLock(Request $request, $id)
    {
        $kalender = AkademikKalender::findOrFail($id);
        $kalender->is_locked = !$kalender->is_locked;
        $kalender->save();

        $status = $kalender->is_locked ? 'DIKUNCI sebagai Acuan Resmi Sekolah' : 'DIBUKA untuk penyesuaian';
        return back()->with('success', "Kalender Pendidikan berhasil {$status}.");
    }

    /**
     * Update catatan kebijakan kurikulum
     */
    public function updateCatatan(Request $request, $id)
    {
        $kalender = AkademikKalender::findOrFail($id);

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        $kalender->update([
            'catatan' => $validated['catatan'],
        ]);

        return back()->with('success', 'Catatan kebijakan kurikulum berhasil disimpan.');
    }

    /**
     * Tambah atau petakan agenda baru ke kalender (CREATE/MAP presisi tanggal & 1 hari)
     */
    public function storeAgenda(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'is_single_day' => 'nullable',
            'bulan' => 'nullable|string|max:20',
            'minggu_ke' => 'nullable|integer|min:1|max:5',
            'jenis' => 'required|in:efektif,non_efektif',
            'kategori' => 'required|string|max:30',
            'keterangan' => 'required|string|max:255',
            'warna' => 'nullable|string|max:30',
        ]);

        $kalender = AkademikKalender::firstOrCreate(
            ['tahun_ajaran_id' => $validated['tahun_ajaran_id'], 'semester' => $validated['semester']],
            [
                'total_pekan' => 25,
                'pekan_efektif' => 18,
                'pekan_cadangan' => 7,
                'is_locked' => false,
                'created_by' => auth()->id(),
            ]
        );

        if ($kalender->is_locked && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Kalender telah dikunci resmi oleh Waka Kurikulum.');
        }

        $namaBulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $tglMulai = $validated['tanggal_mulai'] ?? null;
        $tglSelesai = $validated['tanggal_selesai'] ?? null;
        $isSingleDay = !empty($request->is_single_day);

        if ($tglMulai) {
            if ($isSingleDay || empty($tglSelesai) || $tglSelesai < $tglMulai) {
                $tglSelesai = $tglMulai;
            }

            $startCarbon = \Carbon\Carbon::parse($tglMulai);
            $bulan = $namaBulanIndo[(int)$startCarbon->month] ?? ($validated['bulan'] ?? 'Juli');
            $firstDay = \Carbon\Carbon::createFromDate($startCarbon->year, $startCarbon->month, 1);
            $startDayOfWeek = $firstDay->dayOfWeekIso;
            $mingguKe = min(5, max(1, (int) ceil(($startCarbon->day + $startDayOfWeek - 1) / 7)));
        } else {
            $bulan = $validated['bulan'] ?? 'Juli';
            $mingguKe = (int)($validated['minggu_ke'] ?? 1);
        }

        $warna = $validated['warna'] ?? match ($validated['kategori']) {
            'mpls' => '#f59e0b',
            'sts' => '#8b5cf6',
            'sas', 'sat' => '#ec4899',
            'ukk' => '#f97316',
            'rapor' => '#10b981',
            'libur' => '#ef4444',
            'pkl' => '#06b6d4',
            default => ($validated['jenis'] === 'efektif' ? '#2563eb' : '#64748b'),
        };

        // Cari apakah item untuk bulan dan minggu_ke sudah ada
        $existingItem = AkademikKalenderItem::where('akademik_kalender_id', $kalender->id)
            ->where('bulan', $bulan)
            ->where('minggu_ke', $mingguKe)
            ->first();

        // Jika item sudah ada dan merupakan KBM standar tanpa tanggal khusus, timpa item tersebut
        if ($existingItem && ($existingItem->kategori === 'kbm' || !$existingItem->tanggal_mulai)) {
            $existingItem->update([
                'tanggal_mulai' => $tglMulai,
                'tanggal_selesai' => $tglSelesai,
                'jenis' => $validated['jenis'],
                'kategori' => $validated['kategori'],
                'keterangan' => $validated['keterangan'],
                'warna' => $warna,
            ]);
            $savedItem = $existingItem;
        } else {
            $globalWeek = $existingItem ? $existingItem->minggu_ke_semester : ($kalender->items()->count() + 1);
            $savedItem = AkademikKalenderItem::create([
                'akademik_kalender_id' => $kalender->id,
                'bulan' => $bulan,
                'minggu_ke' => $mingguKe,
                'minggu_ke_semester' => $globalWeek,
                'tanggal_mulai' => $tglMulai,
                'tanggal_selesai' => $tglSelesai,
                'jenis' => $validated['jenis'],
                'kategori' => $validated['kategori'],
                'keterangan' => $validated['keterangan'],
                'warna' => $warna,
            ]);
        }

        $kalender->hitungUlangPekan();

        $rentangText = $savedItem->formatRentangTanggal() ? " ({$savedItem->formatRentangTanggal()})" : "";
        return back()->with('success', "Agenda \"{$validated['keterangan']}\"{$rentangText} berhasil dipetakan ke Pekan {$mingguKe} {$bulan}.");
    }

    /**
     * Reset pekan menjadi KBM Efektif normal (Hapus agenda khusus)
     */
    public function resetItem(Request $request, $id)
    {
        $item = AkademikKalenderItem::with('kalender')->findOrFail($id);

        if ($item->kalender->is_locked && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Kalender telah dikunci resmi.');
        }

        $item->update([
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
            'jenis' => 'efektif',
            'kategori' => 'kbm',
            'keterangan' => "KBM Efektif Pekan {$item->minggu_ke}",
            'warna' => '#2563eb',
        ]);

        $item->kalender->hitungUlangPekan();

        return back()->with('success', "Pekan ke-{$item->minggu_ke} ({$item->bulan}) berhasil direset menjadi KBM Efektif normal.");
    }

    /**
     * Hapus butir pekan dari kalender
     */
    public function destroyItem(Request $request, $id)
    {
        $item = AkademikKalenderItem::with('kalender')->findOrFail($id);
        $kalender = $item->kalender;

        if ($kalender->is_locked && !auth()->user()->isAdmin()) {
            return back()->with('error', 'Kalender telah dikunci resmi.');
        }

        $item->delete();

        // Nomor urut ulang minggu_ke_semester
        $seq = 1;
        foreach ($kalender->items()->orderBy('id')->get() as $it) {
            $it->update(['minggu_ke_semester' => $seq++]);
        }

        $kalender->hitungUlangPekan();

        return back()->with('success', 'Butir pekan berhasil dihapus.');
    }
}
