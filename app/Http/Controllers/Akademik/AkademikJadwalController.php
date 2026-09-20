<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikGuruPiket;
use App\Models\AkademikJadwalPelajaran;
use App\Models\AkademikJadwalWaktu;
use App\Models\AkademikMataPelajaran;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AkademikJadwalController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        if ($request->filled('tahun_ajaran_id')) {
            $ta = TahunAjaran::find($request->tahun_ajaran_id) ?? $ta;
        }

        $semester = (int) $request->get('semester', 1);
        $tab = $request->get('tab', 'roster'); // roster, formulasi, distribusi, piket
        $hariFilter = $request->get('hari', ''); // SENIN, SELASA, etc. or all

        // Master Data
        $gurus = Guru::where('status', 'aktif')->orderBy('kode_nomor')->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();
        $mapels = AkademikMataPelajaran::where('is_active', true)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->orderBy('nama_mapel')->get();

        // 1. Matriks Roster Jadwal Mingguan
        $jadwalQuery = AkademikJadwalPelajaran::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($hariFilter) {
            $jadwalQuery->where('hari', strtoupper($hariFilter));
        }

        $allSlots = $jadwalQuery->get();

        // Group slots by [hari][jam_ke][rombel_id] for lightning-fast matrix lookup
        $slotsMatrix = [];
        foreach ($allSlots as $s) {
            $slotsMatrix[$s->hari][$s->jam_ke][$s->rombel_id] = $s;
        }

        // 2. Data Guru Piket
        $guruPikets = AkademikGuruPiket::with('wakaPiket')
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get()
            ->keyBy('hari');

        // 3. Data Distribusi Mengajar (Beban JJM) - Dikelompokkan 1 Mapel 1 Guru Jadi 1 Baris
        $distribusiQuery = AkademikDistribusiMengajar::with(['guru', 'mataPelajaran', 'rombel', 'tahunAjaran'])
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id));

        if ($request->filled('guru_id')) {
            $distribusiQuery->where('guru_id', $request->guru_id);
        }
        if ($request->filled('rombel_id')) {
            $distribusiQuery->where('rombel_id', $request->rombel_id);
        }
        if ($request->filled('mata_pelajaran_id')) {
            $distribusiQuery->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        $allRawDistribusi = $distribusiQuery->orderBy('mata_pelajaran_id')->orderBy('guru_id')->get();

        // Gabungkan rombel jika 1 mapel diampu oleh guru yang sama
        $groupedDistribusi = $allRawDistribusi->groupBy(function ($item) {
            return $item->guru_id . '_' . $item->mata_pelajaran_id . '_' . $item->semester;
        })->map(function ($items) {
            $first = $items->first();
            return (object) [
                'id' => $first->id,
                'ids' => $items->pluck('id')->toArray(),
                'ids_string' => $items->pluck('id')->implode(','),
                'guru_id' => $first->guru_id,
                'guru' => $first->guru,
                'mata_pelajaran_id' => $first->mata_pelajaran_id,
                'mataPelajaran' => $first->mataPelajaran,
                'semester' => $first->semester,
                'total_jam_per_minggu' => $first->total_jam_per_minggu,
                'total_jam_akumulasi' => $items->sum('total_jam_per_minggu'),
                'rombels' => $items->pluck('rombel')->filter()->unique('id')->values(),
                'rombel_ids' => $items->pluck('rombel_id')->toArray(),
                'catatan' => $first->catatan,
            ];
        })->values();

        // Paginate manual collection agar pagination tetap rapi
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 25;
        $currentPageItems = $groupedDistribusi->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $distribusis = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $groupedDistribusi->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Rekap JJM (Jam Jabatan Mengajar) per Guru
        $rekapJjm = AkademikDistribusiMengajar::where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->selectRaw('guru_id, SUM(total_jam_per_minggu) as total_jam, COUNT(id) as total_rombel')
            ->groupBy('guru_id')
            ->with('guru')
            ->get();

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        // Definisi Jam & Pukul Resmi SMKN 1 Air Naningan (Dinamis per TA & Semester)
        $defaultSchedule = AkademikJadwalWaktu::defaultSchedule();
        $customWaktus = AkademikJadwalWaktu::where('tahun_ajaran_id', $ta?->id)
            ->where('semester', $semester)
            ->orderBy('hari')->orderBy('urutan')->orderBy('jam_ke')
            ->get();

        // jadwalWaktu: hanya jam KBM [hari][jam_ke] => pukul (untuk roster matriks)
        $jadwalWaktu = $defaultSchedule;
        foreach ($customWaktus->where('tipe', 'jam') as $w) {
            $jadwalWaktu[$w->hari][$w->jam_ke] = $w->pukul;
        }

        // jadwalWaktuFull: semua baris per hari termasuk istirahat (untuk tab Atur Pukul)
        $defaultFull = AkademikJadwalWaktu::defaultScheduleFull();
        $jadwalWaktuFull = $defaultFull;

        // Override dengan data custom yang tersimpan di DB
        if ($customWaktus->isNotEmpty()) {
            foreach (array_keys($defaultFull) as $hari) {
                $dbRows = $customWaktus->where('hari', $hari)->sortBy('urutan');
                if ($dbRows->count() >= count($defaultFull[$hari])) {
                    // Ada data custom lengkap — gunakan dari DB
                    $jadwalWaktuFull[$hari] = $dbRows->map(fn($r) => [
                        'tipe'   => $r->tipe ?? 'jam',
                        'jam_ke' => $r->jam_ke,
                        'urutan' => $r->urutan,
                        'label'  => $r->label ?? ($r->tipe === 'istirahat' ? 'Istirahat' : 'Jam ' . $r->jam_ke),
                        'pukul'  => $r->pukul,
                    ])->values()->toArray();
                } else {
                    // Data parsial — merge default dengan override dari DB
                    foreach ($jadwalWaktuFull[$hari] as &$row) {
                        $dbRow = $customWaktus->where('hari', $hari)->where('jam_ke', $row['jam_ke'])->first();
                        if ($dbRow) {
                            $row['pukul'] = $dbRow->pukul;
                            if ($dbRow->label) $row['label'] = $dbRow->label;
                        }
                    }
                    unset($row);
                }
            }
        }

        return view('dcc.akademik.jadwal.index', compact(
            'distribusis', 'ta', 'gurus', 'rombels', 'mapels', 'semester', 'rekapJjm',
            'tahunAjarans', 'tab', 'hariFilter', 'slotsMatrix', 'guruPikets',
            'jadwalWaktu', 'jadwalWaktuFull'
        ));
    }

    /**
     * Formulasi Cepat Blok Jam (Batch Block Scheduler)
     * Mengisi jadwal rentang jam sekaligus dengan proteksi deteksi bentrok otomatis!
     */
    public function storeBlok(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
            'hari' => 'required|in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU',
            'rombel_id' => 'required|exists:rombels,id',
            'guru_id' => 'nullable|exists:gurus,id',
            'mata_pelajaran_id' => 'nullable|exists:akademik_mata_pelajarans,id',
            'jam_mulai' => 'required|integer|min:0|max:12',
            'jam_selesai' => 'required|integer|min:0|max:12|gte:jam_mulai',
            'kegiatan_khusus' => 'nullable|string',
        ]);

        $taId = $request->tahun_ajaran_id;
        $semester = $request->semester;
        $hari = strtoupper($request->hari);
        $rombelId = $request->rombel_id;
        $guruId = $request->guru_id;
        $mapelId = $request->mata_pelajaran_id;
        $jamMulai = (int) $request->jam_mulai;
        $jamSelesai = (int) $request->jam_selesai;
        $kegiatan = $request->kegiatan_khusus;
        $force = $request->boolean('force');

        $guru = $guruId ? Guru::find($guruId) : null;
        $mapel = $mapelId ? AkademikMataPelajaran::find($mapelId) : null;
        $rombel = Rombel::find($rombelId);

        // 1. DETEKSI BENTROK GURU (Conflict Detection)
        if ($guruId && !$force) {
            $conflicts = AkademikJadwalPelajaran::with('rombel')
                ->where('tahun_ajaran_id', $taId)
                ->where('semester', $semester)
                ->where('hari', $hari)
                ->where('guru_id', $guruId)
                ->where('rombel_id', '!=', $rombelId)
                ->whereBetween('jam_ke', [$jamMulai, $jamSelesai])
                ->get();

            if ($conflicts->isNotEmpty()) {
                $conflictList = $conflicts->map(function ($c) {
                    return "Jam Ke-{$c->jam_ke} di {$c->rombel?->nama_rombel}";
                })->implode(', ');

                return redirect()->back()
                    ->withInput()
                    ->with('error', "⚠️ BENTROK JADWAL! Guru {$guru->nama} sudah dijadwalkan mengajar pada {$hari} ({$conflictList}). Silakan sesuaikan jam atau ganti pengampu.");
            }
        }

        // 2. TERAPKAN BLOK JADWAL
        $appliedCount = 0;
        for ($j = $jamMulai; $j <= $jamSelesai; $j++) {
            AkademikJadwalPelajaran::updateOrCreate(
                [
                    'tahun_ajaran_id' => $taId,
                    'semester' => $semester,
                    'hari' => $hari,
                    'jam_ke' => $j,
                    'rombel_id' => $rombelId,
                ],
                [
                    'guru_id' => $guruId,
                    'mata_pelajaran_id' => $mapelId,
                    'kode_guru' => $guru?->kode_nomor ? (string)$guru->kode_nomor : null,
                    'singkatan_mapel' => $mapel?->singkatan_mapel ?? $mapel?->nama_mapel,
                    'kegiatan_khusus' => $kegiatan,
                    'is_locked' => $request->boolean('is_locked'),
                ]
            );
            $appliedCount++;
        }

        $label = $mapel ? $mapel->nama_mapel : ($kegiatan ?? 'Kegiatan');
        return redirect()->route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester, 'hari' => $hari])
            ->with('success', "Berhasil menjadwalkan {$label} untuk {$rombel->nama_rombel} pada {$hari} (Jam {$jamMulai} s/d {$jamSelesai} - {$appliedCount} JP).");
    }

    /**
     * Simpan / Perbarui 1 Slot Jadwal secara langsung
     */
    public function storeSlot(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
            'hari' => 'required|in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU',
            'jam_ke' => 'required|integer|min:0|max:12',
            'rombel_id' => 'required|exists:rombels,id',
            'guru_id' => 'nullable|exists:gurus,id',
            'mata_pelajaran_id' => 'nullable|exists:akademik_mata_pelajarans,id',
            'kegiatan_khusus' => 'nullable|string',
        ]);

        $guru = $request->guru_id ? Guru::find($request->guru_id) : null;
        $mapel = $request->mata_pelajaran_id ? AkademikMataPelajaran::find($request->mata_pelajaran_id) : null;

        // Cek Bentrok jika ada guru
        if ($request->guru_id && !$request->boolean('force')) {
            $conflict = AkademikJadwalPelajaran::with('rombel')
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('semester', $request->semester)
                ->where('hari', strtoupper($request->hari))
                ->where('guru_id', $request->guru_id)
                ->where('rombel_id', '!=', $request->rombel_id)
                ->where('jam_ke', $request->jam_ke)
                ->first();

            if ($conflict) {
                return redirect()->back()
                    ->with('error', "⚠️ BENTROK! Guru {$guru->nama} sudah terjadwal di {$conflict->rombel?->nama_rombel} pada {$request->hari} Jam Ke-{$request->jam_ke}.");
            }
        }

        AkademikJadwalPelajaran::updateOrCreate(
            [
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'semester' => $request->semester,
                'hari' => strtoupper($request->hari),
                'jam_ke' => $request->jam_ke,
                'rombel_id' => $request->rombel_id,
            ],
            [
                'guru_id' => $request->guru_id,
                'mata_pelajaran_id' => $request->mata_pelajaran_id,
                'kode_guru' => $guru?->kode_nomor ? (string)$guru->kode_nomor : null,
                'singkatan_mapel' => $mapel?->singkatan_mapel ?? $mapel?->nama_mapel,
                'kegiatan_khusus' => $request->kegiatan_khusus,
                'is_locked' => $request->boolean('is_locked'),
            ]
        );

        return redirect()->back()->with('success', 'Slot jadwal berhasil diperbarui.');
    }

    /**
     * Hapus / Kosongkan Slot Jadwal
     */
    public function destroySlot($id)
    {
        $slot = AkademikJadwalPelajaran::findOrFail($id);
        $slot->delete();

        return redirect()->back()->with('success', 'Slot jadwal berhasil dikosongkan.');
    }

    /**
     * Kunci / Buka Kunci Slot Jadwal (Keep / Lock Feature)
     * Slot yang dikunci (is_locked = true) tidak akan diubah atau ditimpa oleh penyusun otomatis!
     */
    public function toggleLock(Request $request, $id)
    {
        $slot = AkademikJadwalPelajaran::findOrFail($id);
        $slot->is_locked = !$slot->is_locked;
        $slot->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_locked' => $slot->is_locked,
                'message' => $slot->is_locked ? 'Slot berhasil dikunci (di-keep).' : 'Kunci slot dibuka (unlocked).'
            ]);
        }

        $label = $slot->is_locked ? '🔒 Slot jadwal berhasil dikunci (di-keep agar tidak digeser oleh sistem otomatis).' : '🔓 Kunci slot jadwal berhasil dibuka.';
        return redirect()->back()->with('success', $label);
    }

    /**
     * Otomatisasi Jadwal 1-Klik (Auto-Scheduler Engine)
     * Mengambil alokasi distribusi mengajar dan menyusun jadwal otomatis tanpa bentrok.
     * Mempertahankan slot yang berstatus DIKUNCI (is_locked = true).
     */
    public function autoGenerate(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        $taId = $request->get('tahun_ajaran_id', $ta?->id ?? 1);
        $semester = (int) $request->get('semester', 1);
        $mode = $request->get('mode', 'regenerate_unlocked'); // 'fill_empty' or 'regenerate_unlocked'

        $hariList = $request->get('hari_list', ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT']);
        $selectedRombelIds = $request->get('rombel_ids');

        // Jika tidak pilih rombel, ambil rombel non-PKL (Kelas X dan XI)
        if (empty($selectedRombelIds) || !is_array($selectedRombelIds)) {
            $selectedRombelIds = Rombel::where('is_active', true)
                ->where(function($q) {
                    $q->where('tingkat', '10')->orWhere('tingkat', '11')
                      ->orWhere('tingkat', 'X')->orWhere('tingkat', 'XI');
                })
                ->pluck('id')
                ->toArray();
        }

        // 1. Jika mode 'regenerate_unlocked', bersihkan slot yang TIDAK DIKUNCI
        // PERTAHANKAN slot yang is_locked = true (DI-KEEP) dan kegiatan khusus (Upacara/Apel/PKL)
        if ($mode === 'regenerate_unlocked') {
            AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
                ->where('semester', $semester)
                ->whereIn('hari', $hariList)
                ->whereIn('rombel_id', $selectedRombelIds)
                ->where('is_locked', false)
                ->whereNull('kegiatan_khusus')
                ->delete();
        }

        // 2. Petakan status keterisian (Occupancy Matrix)
        $existingSlots = AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
            ->where('semester', $semester)
            ->get();

        $occupiedSlots = [];
        $guruBusy = [];
        $rombelSubjectsScheduled = [];

        foreach ($existingSlots as $slot) {
            $occupiedSlots[$slot->hari][$slot->jam_ke][$slot->rombel_id] = true;
            if ($slot->guru_id) {
                $guruBusy[$slot->hari][$slot->jam_ke][$slot->guru_id] = true;
            }
            if ($slot->rombel_id && $slot->mata_pelajaran_id) {
                $rombelSubjectsScheduled[$slot->rombel_id][$slot->mata_pelajaran_id] =
                    ($rombelSubjectsScheduled[$slot->rombel_id][$slot->mata_pelajaran_id] ?? 0) + 1;
            }
        }

        // 3. Ambil daftar alokasi mengajar yang harus dijadwalkan
        $distribusis = AkademikDistribusiMengajar::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('tahun_ajaran_id', $taId)
            ->where('semester', $semester)
            ->whereIn('rombel_id', $selectedRombelIds)
            ->get();

        // Urutkan mapel berjam besar dulu (4 JP, 3 JP, 2 JP) agar penempatan blok lebih efisien
        $distribusis = $distribusis->sortByDesc('total_jam_per_minggu');

        // Definisi Sesi Alami (Blok jam KBM tanpa memotong waktu istirahat)
        $sesiAlami = [
            'SENIN' => [
                [1, 2, 3],       // Pagi (sebelum Istirahat 1)
                [4, 5, 6, 7],    // Siang (antara Istirahat 1 & 2)
                [8, 9, 10, 11],  // Sore (setelah Istirahat 2)
            ],
            'SELASA' => [
                [1, 2, 3, 4],
                [5, 6, 7],
                [8, 9, 10, 11],
            ],
            'RABU' => [
                [1, 2, 3, 4],
                [5, 6, 7],
                [8, 9, 10, 11],
            ],
            'KAMIS' => [
                [1, 2, 3, 4],
                [5, 6, 7],
                [8, 9, 10, 11],
            ],
            'JUMAT' => [
                [1, 2, 3],       // Sebelum Istirahat
                [4, 5],          // Setelah Istirahat
            ],
        ];

        $placedCount = 0;
        $unresolvedList = [];

        foreach ($distribusis as $dist) {
            $rombelId = $dist->rombel_id;
            $mapelId = $dist->mata_pelajaran_id;
            $guruId = $dist->guru_id;
            $totalJp = (int) $dist->total_jam_per_minggu;

            $alreadyScheduled = $rombelSubjectsScheduled[$rombelId][$mapelId] ?? 0;
            $neededJp = max(0, $totalJp - $alreadyScheduled);

            if ($neededJp <= 0) {
                continue;
            }

            $remaining = $neededJp;

            while ($remaining > 0) {
                $blockSize = min($remaining, 4); // Blok maksimal 4 JP sekali pertemuan
                $found = false;

                foreach ($hariList as $hari) {
                    if (!isset($sesiAlami[$hari])) continue;

                    // Hindari duplikasi mapel di hari yang sama jika belum perlu
                    $alreadyInDay = false;
                    foreach ($sesiAlami[$hari] as $sesiCheck) {
                        foreach ($sesiCheck as $jCheck) {
                            $chk = $existingSlots->first(function($s) use ($hari, $jCheck, $rombelId, $mapelId) {
                                return $s->hari === $hari && $s->jam_ke === $jCheck && $s->rombel_id === $rombelId && $s->mata_pelajaran_id === $mapelId;
                            });
                            if ($chk) {
                                $alreadyInDay = true;
                                break 2;
                            }
                        }
                    }
                    if ($alreadyInDay && count($hariList) > 1 && $remaining == $neededJp) {
                        continue;
                    }

                    // Cari rentang jam berurutan
                    foreach ($sesiAlami[$hari] as $sesi) {
                        $maxOffset = count($sesi) - $blockSize;
                        if ($maxOffset < 0) continue;

                        for ($offset = 0; $offset <= $maxOffset; $offset++) {
                            $candidateJams = array_slice($sesi, $offset, $blockSize);

                            // Periksa bentrok rombel & guru
                            $canPlace = true;
                            foreach ($candidateJams as $jam) {
                                if (!empty($occupiedSlots[$hari][$jam][$rombelId])) {
                                    $canPlace = false;
                                    break;
                                }
                                if ($guruId && !empty($guruBusy[$hari][$jam][$guruId])) {
                                    $canPlace = false;
                                    break;
                                }
                            }

                            if ($canPlace) {
                                foreach ($candidateJams as $jam) {
                                    AkademikJadwalPelajaran::updateOrCreate(
                                        [
                                            'tahun_ajaran_id' => $taId,
                                            'semester' => $semester,
                                            'hari' => $hari,
                                            'jam_ke' => $jam,
                                            'rombel_id' => $rombelId,
                                        ],
                                        [
                                            'guru_id' => $guruId,
                                            'mata_pelajaran_id' => $mapelId,
                                            'distribusi_id' => $dist->id,
                                            'kode_guru' => $dist->guru?->kode_nomor ? (string)$dist->guru->kode_nomor : null,
                                            'singkatan_mapel' => $dist->mataPelajaran?->singkatan_mapel ?? $dist->mataPelajaran?->nama_mapel,
                                            'is_locked' => false,
                                        ]
                                    );

                                    $occupiedSlots[$hari][$jam][$rombelId] = true;
                                    if ($guruId) {
                                        $guruBusy[$hari][$jam][$guruId] = true;
                                    }
                                    $placedCount++;
                                }

                                $remaining -= $blockSize;
                                $found = true;
                                break 2; // keluar dari offset & sesi hari ini
                            }
                        }
                    }
                }

                if (!$found) {
                    // Coba pecah jadi blok yang lebih kecil (misal 2 JP atau 1 JP)
                    if ($blockSize > 2) {
                        $remaining = $remaining - 2; // coba pecah 2 JP
                        // Taruh 2 JP dulu
                        $blockSize = 2;
                    } elseif ($blockSize > 1) {
                        $remaining = $remaining - 1;
                        $blockSize = 1;
                    } else {
                        $unresolvedList[] = "{$dist->mataPelajaran?->nama_mapel} ({$dist->rombel?->nama_rombel}) sisa {$remaining} JP";
                        break;
                    }
                }
            }
        }

        $lockedCount = AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
            ->where('semester', $semester)
            ->where('is_locked', true)
            ->count();

        $msg = "✨ Berhasil menyusun jadwal otomatis! Sebanyak {$placedCount} slot pelajaran berhasil ditempatkan tanpa bentrok.";
        if ($lockedCount > 0) {
            $msg .= " Sebanyak {$lockedCount} slot yang dikunci (keep) tetap dipertahankan.";
        }
        if (!empty($unresolvedList)) {
            $msg .= " Catatan: " . count($unresolvedList) . " alokasi jam perlu penempatan manual karena slot penuh.";
        }

        return redirect()->route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester])
            ->with('success', $msg);
    }

    /**
     * Hapus / Bersihkan Jadwal yang ada di Tabel
     */
    public function clearJadwal(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
            'hari' => 'nullable|string',
        ]);

        $taId = $request->tahun_ajaran_id;
        $semester = $request->semester;

        $query = AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
            ->where('semester', $semester);

        if ($request->filled('hari') && $request->hari !== 'ALL') {
            $query->where('hari', strtoupper($request->hari));
        }

        if ($request->has('rombel_ids') && is_array($request->rombel_ids) && count($request->rombel_ids) > 0) {
            $query->whereIn('rombel_id', $request->rombel_ids);
        }

        // Proteksi slot yang dikunci (keep 🔒)
        $keepLocked = $request->boolean('keep_locked', true);
        if ($keepLocked) {
            $query->where('is_locked', false);
        }

        $count = $query->count();
        $query->delete();

        $hariLabel = ($request->hari && $request->hari !== 'ALL') ? "Hari " . strtoupper($request->hari) : "Seluruh Hari";
        $lockedMsg = $keepLocked ? " (Slot yang dikunci 🔒 tetap aman dipertahankan)" : " (Termasuk slot yang dikunci)";

        return redirect()->route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester])
            ->with('success', "Berhasil menghapus {$count} slot jadwal pelajaran untuk {$hariLabel}{$lockedMsg}.");
    }

    /**
     * Atur & Simpan Pukul (Jam + Istirahat) oleh Waka Kurikulum
     * Mendukung simpan semua baris termasuk istirahat yang labelnya bisa diubah
     */
    public function updatePukul(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
        ]);

        $taId = $request->tahun_ajaran_id;
        $semester = $request->semester;

        // ============================
        // OPSI 1: Reset ke default resmi (semua data custom dihapus)
        // ============================
        if ($request->boolean('reset_default')) {
            AkademikJadwalWaktu::where('tahun_ajaran_id', $taId)
                ->where('semester', $semester)
                ->delete();

            // Simpan ulang berdasarkan defaultScheduleFull (termasuk istirahat)
            $defaultFull = AkademikJadwalWaktu::defaultScheduleFull();
            foreach ($defaultFull as $hari => $rows) {
                foreach ($rows as $row) {
                    AkademikJadwalWaktu::create([
                        'tahun_ajaran_id' => $taId,
                        'semester'        => $semester,
                        'hari'            => $hari,
                        'jam_ke'          => $row['jam_ke'],
                        'pukul'           => $row['pukul'],
                        'tipe'            => $row['tipe'],
                        'label'           => $row['label'],
                        'urutan'          => $row['urutan'],
                    ]);
                    // Sinkronkan pukul ke tabel jadwal pelajaran (hanya jam KBM)
                    if ($row['tipe'] === 'jam') {
                        AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
                            ->where('semester', $semester)
                            ->where('hari', $hari)
                            ->where('jam_ke', $row['jam_ke'])
                            ->update(['pukul' => $row['pukul']]);
                    }
                }
            }
            return redirect()->back()->with('success', '✅ Jadwal waktu berhasil direset ke standar resmi SMKN 1 Air Naningan (termasuk jam istirahat).');
        }

        // ============================
        // OPSI 2: Bulk save dari form Atur Pukul (rows[] per hari)
        // rows = array of {hari, jam_ke, pukul, tipe, label, urutan}
        // ============================
        if ($request->has('rows') && is_array($request->rows)) {
            // Hapus dulu semua data custom hari & semester ini
            AkademikJadwalWaktu::where('tahun_ajaran_id', $taId)
                ->where('semester', $semester)
                ->delete();

            $count = 0;
            foreach ($request->rows as $row) {
                $hari   = strtoupper($row['hari'] ?? '');
                $jamKe  = (int) ($row['jam_ke'] ?? 0);
                $pukul  = trim($row['pukul'] ?? '');
                $tipe   = $row['tipe'] ?? 'jam';
                $label  = trim($row['label'] ?? '');
                $urutan = (int) ($row['urutan'] ?? $count);

                if (empty($hari) || empty($pukul)) continue;

                AkademikJadwalWaktu::create([
                    'tahun_ajaran_id' => $taId,
                    'semester'        => $semester,
                    'hari'            => $hari,
                    'jam_ke'          => $jamKe,
                    'pukul'           => $pukul,
                    'tipe'            => $tipe,
                    'label'           => $label ?: null,
                    'urutan'          => $urutan,
                ]);

                // Sinkronkan pukul ke slot jadwal pelajaran (hanya jam KBM)
                if ($tipe === 'jam' && $jamKe >= 0) {
                    AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
                        ->where('semester', $semester)
                        ->where('hari', $hari)
                        ->where('jam_ke', $jamKe)
                        ->update(['pukul' => $pukul]);
                }
                $count++;
            }

            return redirect()->route('akademik.jadwal.index', ['tab' => 'pukul', 'semester' => $semester])
                ->with('success', "✅ Berhasil menyimpan {$count} baris konfigurasi waktu KBM (termasuk istirahat).");
        }

        // ============================
        // OPSI 3: Legacy waktu_data[hari][jam] = pukul (backward compat)
        // ============================
        if ($request->has('waktu_data') && is_array($request->waktu_data)) {
            $count = 0;
            foreach ($request->waktu_data as $hari => $jams) {
                foreach ($jams as $jam => $pukul) {
                    if (!empty($pukul)) {
                        AkademikJadwalWaktu::updateOrCreate(
                            ['tahun_ajaran_id' => $taId, 'semester' => $semester, 'hari' => strtoupper($hari), 'jam_ke' => (int) $jam],
                            ['pukul' => trim($pukul), 'tipe' => 'jam']
                        );
                        AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
                            ->where('semester', $semester)
                            ->where('hari', strtoupper($hari))
                            ->where('jam_ke', (int) $jam)
                            ->update(['pukul' => trim($pukul)]);
                        $count++;
                    }
                }
            }
            return redirect()->back()->with('success', "✅ Konfigurasi pukul KBM berhasil disimpan ({$count} slot).");
        }

        return redirect()->back()->with('error', 'Tidak ada data pukul yang diubah.');
    }

    /**
     * Simpan Petugas Guru Piket Harian
     */
    public function storePiket(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester' => 'required|in:1,2',
            'hari' => 'required|in:SENIN,SELASA,RABU,KAMIS,JUMAT,SABTU',
            'waka_piket_id' => 'nullable|exists:gurus,id',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:gurus,id',
            'catatan' => 'nullable|string',
        ]);

        AkademikGuruPiket::updateOrCreate(
            [
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'semester' => $request->semester,
                'hari' => strtoupper($request->hari),
            ],
            [
                'waka_piket_id' => $request->waka_piket_id,
                'guru_ids' => $request->guru_ids ?? [],
                'catatan' => $request->catatan,
            ]
        );

        return redirect()->back()->with('success', "Petugas piket untuk hari {$request->hari} berhasil disimpan.");
    }

    /**
     * AJAX Live Conflict Checker untuk Wakakur
     */
    public function checkConflict(Request $request)
    {
        $guruId = $request->guru_id;
        $hari = strtoupper($request->hari);
        $rombelId = $request->rombel_id;
        $jamMulai = (int) $request->jam_mulai;
        $jamSelesai = (int) $request->jam_selesai;
        $taId = $request->tahun_ajaran_id;
        $semester = $request->semester ?? 1;

        if (!$guruId || !$hari) {
            return response()->json(['conflict' => false]);
        }

        $conflicts = AkademikJadwalPelajaran::with('rombel')
            ->where('tahun_ajaran_id', $taId)
            ->where('semester', $semester)
            ->where('hari', $hari)
            ->where('guru_id', $guruId)
            ->where('rombel_id', '!=', $rombelId)
            ->whereBetween('jam_ke', [$jamMulai, $jamSelesai])
            ->get();

        if ($conflicts->isNotEmpty()) {
            $details = $conflicts->map(function ($c) {
                return "Jam Ke-{$c->jam_ke} ({$c->rombel?->nama_rombel})";
            })->implode(', ');

            $guru = Guru::find($guruId);
            return response()->json([
                'conflict' => true,
                'message' => "⚠️ PERINGATAN: Guru {$guru?->nama} sudah terjadwal di: {$details}",
            ]);
        }

        return response()->json(['conflict' => false]);
    }

    /**
     * Tampilan Cetak Resmi Format Wakakur SMKN 1 Air Naningan
     */
    public function cetak(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        if ($request->filled('tahun_ajaran_id')) {
            $ta = TahunAjaran::find($request->tahun_ajaran_id) ?? $ta;
        }

        $semester = (int) $request->get('semester', 1);

        $gurus = Guru::where('status', 'aktif')->orderBy('kode_nomor')->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();
        $mapels = AkademikMataPelajaran::where('is_active', true)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->orderBy('kode_mapel')->get();

        $allSlots = AkademikJadwalPelajaran::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get();

        $slotsMatrix = [];
        foreach ($allSlots as $s) {
            $slotsMatrix[$s->hari][$s->jam_ke][$s->rombel_id] = $s;
        }

        $guruPikets = AkademikGuruPiket::with('wakaPiket')
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get()
            ->keyBy('hari');

        return view('dcc.akademik.jadwal.cetak', compact(
            'ta', 'semester', 'gurus', 'rombels', 'mapels', 'slotsMatrix', 'guruPikets'
        ));
    }

    /**
     * Menyimpan Distribusi Mengajar (Beban JJM)
     */
    public function store(Request $request)
    {
        // Support array of gurus (guru_ids) or single guru_id
        if ($request->has('guru_id') && !$request->has('guru_ids')) {
            $request->merge(['guru_ids' => [$request->guru_id]]);
        }
        if ($request->has('guru_ids') && !is_array($request->guru_ids)) {
            $request->merge(['guru_ids' => [$request->guru_ids]]);
        }

        // Support array of rombels (rombel_ids) or single rombel_id
        if ($request->has('rombel_id') && !$request->has('rombel_ids')) {
            $request->merge(['rombel_ids' => [$request->rombel_id]]);
        }

        // Support semester_opsi ('1', '2', 'both') or array semesters
        if ($request->filled('semester_opsi')) {
            if ($request->semester_opsi === 'both') {
                $request->merge(['semesters' => [1, 2]]);
            } else {
                $request->merge(['semesters' => [(int) $request->semester_opsi]]);
            }
        }

        // Support array of semesters (semesters) or single semester
        if ($request->has('semester') && !$request->has('semesters')) {
            $request->merge(['semesters' => [$request->semester]]);
        }
        if (!$request->has('semesters') || empty($request->semesters)) {
            $request->merge(['semesters' => [1]]);
        }

        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'guru_ids' => 'required|array|min:1',
            'guru_ids.*' => 'exists:gurus,id',
            'mata_pelajaran_id' => 'required|exists:akademik_mata_pelajarans,id',
            'rombel_ids' => 'required|array|min:1',
            'rombel_ids.*' => 'exists:rombels,id',
            'semesters' => 'required|array|min:1',
            'semesters.*' => 'in:1,2',
            'total_jam_per_minggu' => 'required|integer|min:1|max:20',
            'catatan' => 'nullable|string',
        ]);

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($request->guru_ids as $guruId) {
            foreach ($request->semesters as $sem) {
                foreach ($request->rombel_ids as $rombelId) {
                    $exists = AkademikDistribusiMengajar::where('guru_id', $guruId)
                        ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                        ->where('rombel_id', $rombelId)
                        ->where('semester', $sem)
                        ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                        ->exists();

                    if (!$exists) {
                        AkademikDistribusiMengajar::create([
                            'tahun_ajaran_id' => $request->tahun_ajaran_id,
                            'guru_id' => $guruId,
                            'mata_pelajaran_id' => $request->mata_pelajaran_id,
                            'rombel_id' => $rombelId,
                            'semester' => $sem,
                            'total_jam_per_minggu' => $request->total_jam_per_minggu,
                            'catatan' => $request->catatan,
                        ]);
                        $createdCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            }
        }

        if ($createdCount === 0 && $skippedCount > 0) {
            return redirect()->route('akademik.jadwal.index', ['tab' => 'distribusi'])
                ->with('error', 'Semua kombinasi rombel dan semester yang dipilih sudah terdaftar sebelumnya.');
        }

        $msg = "Distribusi mengajar berhasil ditambahkan ({$createdCount} jadwal dibuat).";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} jadwal dilewati karena sudah ada sebelumnya).";
        }

        return redirect()->route('akademik.jadwal.index', ['tab' => 'distribusi'])->with('success', $msg);
    }

    public function update(Request $request, $id)
    {
        $distribusi = AkademikDistribusiMengajar::findOrFail($id);
        $taId = $distribusi->tahun_ajaran_id;

        if ($request->filled('semester_opsi')) {
            $request->merge(['semester' => (int) $request->semester_opsi]);
        }

        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'mata_pelajaran_id' => 'required|exists:akademik_mata_pelajarans,id',
            'semester' => 'required|in:1,2',
            'total_jam_per_minggu' => 'required|integer|min:1|max:20',
            'catatan' => 'nullable|string',
        ]);

        // Support rombel_ids array or single rombel_id
        $rombelIds = $request->rombel_ids ?? ($request->rombel_id ? [$request->rombel_id] : [$distribusi->rombel_id]);

        $targetIds = $request->filled('ids_string') ? explode(',', $request->ids_string) : [$id];

        // Hapus alokasi lama dalam grup ini
        AkademikDistribusiMengajar::whereIn('id', $targetIds)->delete();

        // Buat alokasi baru untuk rombel-rombel yang dipilih
        foreach ($rombelIds as $rId) {
            AkademikDistribusiMengajar::updateOrCreate(
                [
                    'tahun_ajaran_id' => $taId,
                    'guru_id' => $request->guru_id,
                    'mata_pelajaran_id' => $request->mata_pelajaran_id,
                    'rombel_id' => $rId,
                    'semester' => $request->semester,
                ],
                [
                    'total_jam_per_minggu' => $request->total_jam_per_minggu,
                    'catatan' => $request->catatan,
                ]
            );
        }

        return redirect()->route('akademik.jadwal.index', ['tab' => 'distribusi'])
            ->with('success', 'Alokasi pengampu berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $ids = $request->filled('ids') ? explode(',', $request->ids) : [$id];
        AkademikDistribusiMengajar::whereIn('id', $ids)->delete();

        return redirect()->route('akademik.jadwal.index', ['tab' => 'distribusi'])
            ->with('success', 'Alokasi pengampu berhasil dihapus.');
    }
}
