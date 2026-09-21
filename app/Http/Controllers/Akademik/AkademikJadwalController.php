<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikGuruPiket;
use App\Models\AkademikJadwalPelajaran;
use App\Models\AkademikJadwalWaktu;
use App\Models\AkademikMasterTugasTambahan;
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
        if ($tab === 'jadwal') {
            $tab = 'roster';
        }
        $hariFilter = $request->get('hari', ''); // SENIN, SELASA, etc. or all

        // Master Data
        $gurus = Guru::where('status', 'aktif')->orderBy('kode_nomor')->orderBy('nama')->get();
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();
        $mapels = AkademikMataPelajaran::where(function($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->when($ta, function($q) use ($ta) {
                $q->where(function($sub) use ($ta) {
                    $sub->where('tahun_ajaran_id', $ta->id)
                        ->orWhereNull('tahun_ajaran_id');
                });
            })
            ->orderBy('kode_mapel')
            ->orderBy('nama_mapel')
            ->get();

        if ($mapels->isEmpty()) {
            $mapels = AkademikMataPelajaran::orderBy('kode_mapel')->orderBy('nama_mapel')->get();
        }

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

        // 4. Matriks Distribusi: [mata_pelajaran_id][rombel_id] => AkademikDistribusiMengajar
        $allDistribusiList = AkademikDistribusiMengajar::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get();

        $matrixDistribusi = [];
        foreach ($allDistribusiList as $dist) {
            $matrixDistribusi[$dist->mata_pelajaran_id][$dist->rombel_id] = $dist;
        }

        // 5. Rekap Beban Mengajar & Tugas Tambahan Seluruh Guru (Standar Permendikbud 15/2018 & Dapodik)
        $rekapBebanGuru = $gurus->map(function ($guru) use ($allDistribusiList) {
            $guruDists = $allDistribusiList->where('guru_id', $guru->id);
            $jtmMurni = $guruDists->sum('total_jam_per_minggu');
            $tugasTambahan = $guru->tugas_tambahan_list;
            $eqTugasTambahan = $guru->total_ekuivalen_tugas_tambahan;
            $totalEkuivalen = $jtmMurni + $eqTugasTambahan;

            $status = 'kurang';
            if ($totalEkuivalen >= 24 && $totalEkuivalen <= 40) {
                $status = 'memenuhi';
            } elseif ($totalEkuivalen > 40) {
                $status = 'lebih';
            }

            return (object) [
                'guru' => $guru,
                'jtm_murni' => $jtmMurni,
                'total_rombel' => $guruDists->pluck('rombel_id')->unique()->count(),
                'tugas_tambahan' => $tugasTambahan,
                'ekuivalen_tugas' => $eqTugasTambahan,
                'total_ekuivalen' => $totalEkuivalen,
                'status' => $status,
                'mapels' => $guruDists->pluck('mataPelajaran.nama_mapel')->filter()->unique()->values(),
            ];
        })->sortByDesc('total_ekuivalen')->values();

        $masterTugasTambahan = AkademikMasterTugasTambahan::where('is_active', true)->orderBy('urutan')->orderBy('nama_tugas')->get();

        return view('dcc.akademik.jadwal.index', compact(
            'distribusis', 'ta', 'gurus', 'rombels', 'mapels', 'semester', 'rekapJjm',
            'tahunAjarans', 'tab', 'hariFilter', 'slotsMatrix', 'guruPikets',
            'jadwalWaktu', 'jadwalWaktuFull', 'matrixDistribusi', 'rekapBebanGuru',
            'masterTugasTambahan'
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

        // 2. DETEKSI BENTROK RESOURCE / LAB (misal: Lab Komputer hanya 1)
        if ($mapel && $mapel->resource_key && !$force) {
            $resourceLabel = \App\Models\AkademikMataPelajaran::RESOURCES[$mapel->resource_key] ?? $mapel->resource_key;
            $resourceConflicts = AkademikJadwalPelajaran::with('rombel', 'mataPelajaran')
                ->where('tahun_ajaran_id', $taId)
                ->where('semester', $semester)
                ->where('hari', $hari)
                ->where('resource_key', $mapel->resource_key)
                ->where('rombel_id', '!=', $rombelId)
                ->whereBetween('jam_ke', [$jamMulai, $jamSelesai])
                ->get();

            if ($resourceConflicts->isNotEmpty()) {
                $conflictDetail = $resourceConflicts->map(function ($c) {
                    return "{$c->rombel?->nama_rombel} Jam Ke-{$c->jam_ke} ({$c->singkatan_mapel})";
                })->unique()->implode(', ');

                return redirect()->back()
                    ->withInput()
                    ->with('error', "🏫 BENTROK RUANGAN! {$resourceLabel} sudah dipakai oleh: {$conflictDetail} pada {$hari}. Ubah jadwal kelas lain dulu, atau geser jam mapel ini.");
            }
        }

        // 3. TERAPKAN BLOK JADWAL
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
                    'resource_key' => $mapel?->resource_key,  // simpan untuk cek cepat
                ]
            );
            $appliedCount++;
        }

        $resourceInfo = ($mapel && $mapel->resource_key)
            ? ' [' . (\App\Models\AkademikMataPelajaran::RESOURCES[$mapel->resource_key] ?? $mapel->resource_key) . ']'
            : '';
        $label = $mapel ? $mapel->nama_mapel : ($kegiatan ?? 'Kegiatan');
        return redirect()->route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester, 'hari' => $hari])
            ->with('success', "Berhasil menjadwalkan {$label}{$resourceInfo} untuk {$rombel->nama_rombel} pada {$hari} (Jam {$jamMulai} s/d {$jamSelesai} - {$appliedCount} JP).");
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
                    ->with('error', "⚠️ BENTROK GURU! Guru {$guru->nama} sudah terjadwal di {$conflict->rombel?->nama_rombel} pada {$request->hari} Jam Ke-{$request->jam_ke}.");
            }
        }

        // Cek Bentrok Ruangan / Lab (misal: Lab Komputer hanya ada 1)
        if ($mapel && $mapel->resource_key && !$request->boolean('force')) {
            $resConflict = AkademikJadwalPelajaran::with('rombel')
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('semester', $request->semester)
                ->where('hari', strtoupper($request->hari))
                ->where('resource_key', $mapel->resource_key)
                ->where('rombel_id', '!=', $request->rombel_id)
                ->where('jam_ke', $request->jam_ke)
                ->first();

            if ($resConflict) {
                $resLabel = \App\Models\AkademikMataPelajaran::RESOURCES[$mapel->resource_key] ?? $mapel->resource_key;
                return redirect()->back()
                    ->with('error', "🏫 BENTROK RUANGAN! {$resLabel} sedang digunakan oleh {$resConflict->rombel?->nama_rombel} ({$resConflict->singkatan_mapel}) pada {$request->hari} Jam Ke-{$request->jam_ke}.");
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
                'resource_key' => $mapel?->resource_key,
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
        $resourceBusy = [];
        $rombelSubjectsScheduled = [];

        foreach ($existingSlots as $slot) {
            $occupiedSlots[$slot->hari][$slot->jam_ke][$slot->rombel_id] = true;
            if ($slot->guru_id) {
                $guruBusy[$slot->hari][$slot->jam_ke][$slot->guru_id] = true;
            }
            if ($slot->resource_key) {
                $resourceBusy[$slot->hari][$slot->jam_ke][$slot->resource_key] = true;
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

                            // Periksa bentrok rombel, guru, dan ruangan/lab (misal: Lab Komputer hanya 1)
                            $canPlace = true;
                            $mapelResource = $dist->mataPelajaran?->resource_key;

                            foreach ($candidateJams as $jam) {
                                if (!empty($occupiedSlots[$hari][$jam][$rombelId])) {
                                    $canPlace = false;
                                    break;
                                }
                                if ($guruId && !empty($guruBusy[$hari][$jam][$guruId])) {
                                    $canPlace = false;
                                    break;
                                }
                                if ($mapelResource && !empty($resourceBusy[$hari][$jam][$mapelResource])) {
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
                                            'resource_key' => $mapelResource,
                                        ]
                                    );

                                    $occupiedSlots[$hari][$jam][$rombelId] = true;
                                    if ($guruId) {
                                        $guruBusy[$hari][$jam][$guruId] = true;
                                    }
                                    if ($mapelResource) {
                                        $resourceBusy[$hari][$jam][$mapelResource] = true;
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

        $piket = AkademikGuruPiket::updateOrCreate(
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

        $piket->syncDayToSirani();

        return redirect()->back()->with('success', "Petugas piket untuk hari {$request->hari} berhasil disimpan dan otomatis disinkronkan ke Meja Piket SIRANI.");
    }

    /**
     * Sinkronkan Seluruh Jadwal Piket ke SIRANI secara manual
     */
    public function syncPiketToSirani(Request $request)
    {
        $taId = $request->input('tahun_ajaran_id');
        $semester = $request->input('semester');

        $count = AkademikGuruPiket::syncAllToSirani($taId, $semester);

        return redirect()->back()->with('success', "Berhasil menyinkronkan seluruh jadwal piket ({$count} hari) ke Meja Piket SIRANI & Smart Gate.");
    }

    /**
     * AJAX Live Conflict Checker untuk Wakakur
     */
    public function checkConflict(Request $request)
    {
        $guruId = $request->guru_id;
        $mapelId = $request->mata_pelajaran_id;
        $hari = strtoupper($request->hari ?? '');
        $rombelId = $request->rombel_id;
        $jamMulai = (int) $request->jam_mulai;
        $jamSelesai = (int) $request->jam_selesai;
        $taId = $request->tahun_ajaran_id;
        $semester = $request->semester ?? 1;

        if (!$hari) {
            return response()->json(['conflict' => false]);
        }

        // 1. Cek bentrok guru (1 guru tidak boleh mengajar di 2 kelas di jam yang sama)
        if ($guruId) {
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
                    'type' => 'guru',
                    'message' => "⚠️ PERINGATAN BENTROK GURU: {$guru?->nama} sudah terjadwal di: {$details}",
                ]);
            }
        }

        // 2. Cek bentrok Ruangan / Lab (misal Lab Komputer hanya ada 1 di sekolah)
        if ($mapelId) {
            $mapel = AkademikMataPelajaran::find($mapelId);
            if ($mapel && $mapel->resource_key) {
                $resLabel = \App\Models\AkademikMataPelajaran::RESOURCES[$mapel->resource_key] ?? $mapel->resource_key;
                $resConflicts = AkademikJadwalPelajaran::with('rombel')
                    ->where('tahun_ajaran_id', $taId)
                    ->where('semester', $semester)
                    ->where('hari', $hari)
                    ->where('resource_key', $mapel->resource_key)
                    ->where('rombel_id', '!=', $rombelId)
                    ->whereBetween('jam_ke', [$jamMulai, $jamSelesai])
                    ->get();

                if ($resConflicts->isNotEmpty()) {
                    $details = $resConflicts->map(function ($c) {
                        return "{$c->rombel?->nama_rombel} Jam Ke-{$c->jam_ke} ({$c->singkatan_mapel})";
                    })->unique()->implode(', ');

                    return response()->json([
                        'conflict' => true,
                        'type' => 'ruangan',
                        'message' => "🏫 PERINGATAN BENTROK RUANGAN: {$resLabel} sudah dipakai oleh: {$details}! Pilih jam lain atau geser jadwal kelas lain.",
                    ]);
                }
            }
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
        $mapels = AkademikMataPelajaran::where(function($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->when($ta, function($q) use ($ta) {
                $q->where(function($sub) use ($ta) {
                    $sub->where('tahun_ajaran_id', $ta->id)
                        ->orWhereNull('tahun_ajaran_id');
                });
            })
            ->orderBy('kode_mapel')
            ->orderBy('nama_mapel')
            ->get();

        if ($mapels->isEmpty()) {
            $mapels = AkademikMataPelajaran::orderBy('kode_mapel')->orderBy('nama_mapel')->get();
        }

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
     * Cetak Lampiran SK Pembagian Tugas Mengajar & Tugas Tambahan Resmi
     * Format Surat Keputusan Kepala SMKN 1 Air Naningan (Standar Kemdikbudristek & Dapodik)
     */
    public function cetakSk(?Request $request = null)
    {
        $request = $request ?? request() ?? new Request();
        $ta = TahunAjaran::where('is_active', true)->first();
        if ($request->filled('tahun_ajaran_id')) {
            $ta = TahunAjaran::find($request->tahun_ajaran_id) ?? $ta;
        }
        $semester = (int) $request->get('semester', 1);

        $sekolah = \App\Models\PengaturanSekolah::first();
        $gurus = Guru::where('status', 'aktif')->orderBy('kode_nomor')->orderBy('nama')->get();
        $rombels = Rombel::with('waliKelas')->orderBy('tingkat')->orderBy('nama_rombel')->get();
        $mapels = AkademikMataPelajaran::where(function($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->when($ta, function($q) use ($ta) {
                $q->where(function($sub) use ($ta) {
                    $sub->where('tahun_ajaran_id', $ta->id)
                        ->orWhereNull('tahun_ajaran_id');
                });
            })
            ->orderBy('kode_mapel')
            ->orderBy('nama_mapel')
            ->get();

        if ($mapels->isEmpty()) {
            $mapels = AkademikMataPelajaran::orderBy('kode_mapel')->orderBy('nama_mapel')->get();
        }

        $allDistribusi = AkademikDistribusiMengajar::with(['mataPelajaran', 'rombel'])
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get();

        // Rekap per guru untuk tabel SK
        $dataGuruSk = $gurus->map(function ($guru) use ($allDistribusi, $rombels) {
            $dists = $allDistribusi->where('guru_id', $guru->id);
            $jtmMurni = $dists->sum('total_jam_per_minggu');
            $tugasTambahan = $guru->tugas_tambahan_list;
            $eqTugas = $guru->total_ekuivalen_tugas_tambahan;
            $totalEkuivalen = $jtmMurni + $eqTugas;

            // Rincian per mapel & kelas
            $rincianMapel = $dists->groupBy('mata_pelajaran_id')->map(function ($items) {
                $first = $items->first();
                return [
                    'nama_mapel' => $first->mataPelajaran?->nama_mapel,
                    'kode_mapel' => $first->mataPelajaran?->kode_mapel,
                    'kelas_list' => $items->pluck('rombel.nama_rombel')->implode(', '),
                    'jam_per_rombel' => $first->total_jam_per_minggu,
                    'total_jam' => $items->sum('total_jam_per_minggu'),
                ];
            })->values();

            return (object) [
                'guru' => $guru,
                'jtm_murni' => $jtmMurni,
                'rincian_mapel' => $rincianMapel,
                'tugas_tambahan' => $tugasTambahan,
                'ekuivalen_tugas' => $eqTugas,
                'total_ekuivalen' => $totalEkuivalen,
            ];
        });

        // Daftar Wali Kelas
        $daftarWali = $rombels->map(function ($r) {
            return (object) [
                'rombel' => $r->nama_rombel,
                'tingkat' => $r->tingkat,
                'wali' => $r->waliKelas,
            ];
        });

        return view('dcc.akademik.jadwal.cetak_sk', compact(
            'ta', 'semester', 'sekolah', 'dataGuruSk', 'daftarWali', 'rombels'
        ));
    }

    /**
     * Cetak Jadwal Pelajaran per Rombel / Kelas (Untuk Dinding Kelas)
     */
    public function cetakKelas(?Request $request = null)
    {
        $request = $request ?? request() ?? new Request();
        $ta = TahunAjaran::where('is_active', true)->first();
        if ($request->filled('tahun_ajaran_id')) {
            $ta = TahunAjaran::find($request->tahun_ajaran_id) ?? $ta;
        }
        $semester = (int) $request->get('semester', 1);

        $sekolah = \App\Models\PengaturanSekolah::first();
        $rombels = Rombel::with('waliKelas')->orderBy('tingkat')->orderBy('nama_rombel')->get();
        $selectedRombelId = $request->get('rombel_id');

        $activeRombels = $selectedRombelId
            ? $rombels->where('id', $selectedRombelId)
            : $rombels;

        $allSlots = AkademikJadwalPelajaran::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('semester', $semester)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get();

        $slotsMatrix = [];
        foreach ($allSlots as $s) {
            $slotsMatrix[$s->rombel_id][$s->hari][$s->jam_ke] = $s;
        }

        $jadwalWaktu = AkademikJadwalWaktu::defaultSchedule();
        $customWaktus = AkademikJadwalWaktu::where('tahun_ajaran_id', $ta?->id)
            ->where('semester', $semester)->where('tipe', 'jam')->get();
        foreach ($customWaktus as $w) {
            $jadwalWaktu[$w->hari][$w->jam_ke] = $w->pukul;
        }

        return view('dcc.akademik.jadwal.cetak_kelas', compact(
            'ta', 'semester', 'sekolah', 'rombels', 'activeRombels', 'slotsMatrix', 'jadwalWaktu', 'selectedRombelId'
        ));
    }

    /**
     * Cetak Jadwal Pemakaian Ruang Khusus / Lab Komputer
     */
    public function cetakLab(?Request $request = null)
    {
        $request = $request ?? request() ?? new Request();
        $ta = TahunAjaran::where('is_active', true)->first();
        if ($request->filled('tahun_ajaran_id')) {
            $ta = TahunAjaran::find($request->tahun_ajaran_id) ?? $ta;
        }
        $semester = (int) $request->get('semester', 1);
        $resourceLabel = \App\Models\AkademikMataPelajaran::RESOURCES[$resourceKey] ?? 'Lab Komputer';

        $sekolah = \App\Models\PengaturanSekolah::first();
        $allSlots = AkademikJadwalPelajaran::with(['guru', 'mataPelajaran', 'rombel'])
            ->where('semester', $semester)
            ->where('resource_key', $resourceKey)
            ->when($ta, fn($q) => $q->where('tahun_ajaran_id', $ta->id))
            ->get();

        $labMatrix = [];
        foreach ($allSlots as $s) {
            $labMatrix[$s->hari][$s->jam_ke] = $s;
        }

        $jadwalWaktu = AkademikJadwalWaktu::defaultSchedule();
        $customWaktus = AkademikJadwalWaktu::where('tahun_ajaran_id', $ta?->id)
            ->where('semester', $semester)->where('tipe', 'jam')->get();
        foreach ($customWaktus as $w) {
            $jadwalWaktu[$w->hari][$w->jam_ke] = $w->pukul;
        }

        return view('dcc.akademik.jadwal.cetak_lab', compact(
            'ta', 'semester', 'sekolah', 'resourceKey', 'resourceLabel', 'labMatrix', 'jadwalWaktu'
        ));
    }

    /**
     * AJAX: Ambil Alokasi Distribusi Mengajar dari Rombel Tertentu
     * Mempermudah form pembuatan jadwal: otomatis mengisi guru & mengunci sisa JP!
     */
    public function getRombelAlokasi($rombelId, ?Request $request = null)
    {
        $request = $request ?? request() ?? new Request();
        $ta = TahunAjaran::where('is_active', true)->first();
        $taId = $request->get('tahun_ajaran_id', $ta?->id ?? 1);
        $semester = (int) $request->get('semester', 1);

        $distribusis = AkademikDistribusiMengajar::with(['mataPelajaran', 'guru'])
            ->where('tahun_ajaran_id', $taId)
            ->where('semester', $semester)
            ->where('rombel_id', $rombelId)
            ->get();

        $data = $distribusis->map(function ($d) use ($taId, $semester, $rombelId) {
            $scheduledCount = AkademikJadwalPelajaran::where('tahun_ajaran_id', $taId)
                ->where('semester', $semester)
                ->where('rombel_id', $rombelId)
                ->where('mata_pelajaran_id', $d->mata_pelajaran_id)
                ->where('guru_id', $d->guru_id)
                ->count();

            $sisa = max(0, (int) $d->total_jam_per_minggu - $scheduledCount);

            return [
                'distribusi_id' => $d->id,
                'mata_pelajaran_id' => $d->mata_pelajaran_id,
                'nama_mapel' => $d->mataPelajaran?->nama_mapel,
                'singkatan_mapel' => $d->mataPelajaran?->singkatan_mapel ?? $d->mataPelajaran?->kode_mapel,
                'resource_key' => $d->mataPelajaran?->resource_key,
                'resource_label' => $d->mataPelajaran?->resource_label,
                'guru_id' => $d->guru_id,
                'nama_guru' => $d->guru?->nama,
                'kode_guru' => $d->guru?->kode_nomor,
                'total_jam' => (int) $d->total_jam_per_minggu,
                'jam_terjadwal' => $scheduledCount,
                'sisa_jam' => $sisa,
                'is_lengkap' => $sisa === 0,
            ];
        });

        return response()->json([
            'success' => true,
            'rombel_id' => $rombelId,
            'alokasi' => $data,
        ]);
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

    /**
     * Update Tugas Tambahan Guru (Waka, Kaprog, Ka. Bengkel/Lab, Pembina, Koordinator P5, dll.)
     * Langsung dari modul Wakakur / Pembagian Tugas
     */
    public function updateTugasTambahan(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'tugas_tambahan' => 'nullable|string|max:255',
            'sk_tugas_tambahan' => 'nullable|string|max:150',
        ]);

        $guru = Guru::findOrFail($request->guru_id);
        $guru->tugas_tambahan = $request->filled('tugas_tambahan') ? trim($request->tugas_tambahan) : null;
        $guru->sk_tugas_tambahan = $request->filled('sk_tugas_tambahan') ? trim($request->sk_tugas_tambahan) : null;
        $guru->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tugas tambahan {$guru->nama} berhasil disimpan.",
                'guru' => [
                    'id' => $guru->id,
                    'nama' => $guru->nama,
                    'tugas_tambahan' => $guru->tugas_tambahan,
                    'tugas_tambahan_list' => $guru->tugas_tambahan_list,
                    'total_ekuivalen' => $guru->total_ekuivalen_tugas_tambahan,
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', "Tugas tambahan untuk {$guru->nama} berhasil diperbarui.");
    }

    /**
     * Master Tugas Tambahan CRUD (Daftar & Ekuivalensi Jam Dinamis)
     */
    public function getMasterTugas()
    {
        $items = AkademikMasterTugasTambahan::orderBy('urutan')->orderBy('nama_tugas')->get();
        return response()->json($items);
    }

    public function storeMasterTugas(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:150',
            'ekuivalensi_jam' => 'required|integer|min:1|max:40',
            'kategori' => 'nullable|string|max:100',
        ]);

        $item = AkademikMasterTugasTambahan::create([
            'nama_tugas' => trim($request->nama_tugas),
            'ekuivalensi_jam' => (int) $request->ekuivalensi_jam,
            'kategori' => $request->kategori ?: 'Tugas Tambahan',
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tugas '{$item->nama_tugas}' (+{$item->ekuivalensi_jam} JP) berhasil ditambahkan.",
                'data' => $item,
            ]);
        }

        return redirect()->back()->with('success', 'Master tugas tambahan berhasil ditambahkan.');
    }

    public function updateMasterTugas(Request $request, $id)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:150',
            'ekuivalensi_jam' => 'required|integer|min:1|max:40',
            'kategori' => 'nullable|string|max:100',
        ]);

        $item = AkademikMasterTugasTambahan::findOrFail($id);
        $item->update([
            'nama_tugas' => trim($request->nama_tugas),
            'ekuivalensi_jam' => (int) $request->ekuivalensi_jam,
            'kategori' => $request->kategori ?: $item->kategori,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tugas '{$item->nama_tugas}' berhasil diperbarui.",
                'data' => $item,
            ]);
        }

        return redirect()->back()->with('success', 'Master tugas tambahan berhasil diperbarui.');
    }

    public function destroyMasterTugas(Request $request, $id)
    {
        $item = AkademikMasterTugasTambahan::findOrFail($id);
        $nama = $item->nama_tugas;
        $item->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tugas '{$nama}' berhasil dihapus dari daftar master.",
            ]);
        }

        return redirect()->back()->with('success', "Tugas '{$nama}' berhasil dihapus.");
    }
}
