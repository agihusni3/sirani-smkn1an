<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        $query = Siswa::with(['siswaRombels.rombel', 'siswaRombels.tahunAjaran']);

        $search = $request->input('q');
        $rombelId = $request->input('rombel_id');
        $statusPkl = $request->input('status_pkl');
        $biometrikStatus = $request->input('face_id', $request->input('rfid')); // Filter Face ID
        $sort = $request->input('sort', 'nama_asc');

        // Batasi hanya untuk rombel binaan jika pengguna adalah Wali Kelas
        if ($isWaliOnly) {
            $query->whereHas('siswaRombels', function ($q) use ($waliRombelIds) {
                $q->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif');
            });
        } elseif ($rombelId) {
            $query->whereHas('siswaRombels', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nama_ortu', 'like', "%{$search}%")
                  ->orWhere('no_hp_ortu', 'like', "%{$search}%");
            });
        }

        if ($statusPkl) {
            $query->where('status_pkl', $statusPkl);
        }

        if ($biometrikStatus === 'ada') {
            $query->whereNotNull('face_embedding');
        } elseif ($biometrikStatus === 'belum') {
            $query->whereNull('face_embedding');
        }

        $tab = $request->input('tab', 'aktif');
        if ($tab === 'alumni') {
            $query->where('status', 'lulus');
        } elseif ($tab === 'semua') {
            // tampilkan seluruh riwayat
        } else {
            $query->whereIn('status', ['aktif', 'pkl']);
        }

        // Sorting
        switch ($sort) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nis_asc':
                $query->orderBy('nis', 'asc');
                break;
            case 'nis_desc':
                $query->orderBy('nis', 'desc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $siswas = $query->paginate(20)->withQueryString();
        $taAktif = TahunAjaran::where('is_active', true)->first();

        // Statistik Cepat & Pilihan Rombel
        if ($isWaliOnly) {
            $rombels = Rombel::whereIn('id', $waliRombelIds)->orderBy('nama_rombel')->get();
            $statTotal = Siswa::whereIn('status', ['aktif', 'pkl'])->whereHas('siswaRombels', function ($q) use ($waliRombelIds) {
                $q->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif');
            })->count();
            $statAlumni = Siswa::where('status', 'lulus')->whereHas('siswaRombels', function ($q) use ($waliRombelIds) {
                $q->whereIn('rombel_id', $waliRombelIds);
            })->count();
            $statPkl = Siswa::where('status', 'aktif')->where('status_pkl', 'aktif_pkl')->whereHas('siswaRombels', function ($q) use ($waliRombelIds) {
                $q->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif');
            })->count();
            $statRombel = count($waliRombelIds);
        } else {
            $rombels = Rombel::orderBy('nama_rombel')->get();
            $statTotal = Siswa::whereIn('status', ['aktif', 'pkl'])->count();
            $statAlumni = Siswa::where('status', 'lulus')->count();
            $statPkl = Siswa::where('status', 'aktif')->where('status_pkl', 'aktif_pkl')->count();
            $statRombel = Rombel::count();
        }

        $waliRombel = $isWaliOnly && !empty($waliRombelIds) ? Rombel::find($waliRombelIds[0]) : null;
        $rfidStatus = $biometrikStatus;

        return view('siswa.index', compact('siswas', 'rombels', 'taAktif', 'search', 'rombelId', 'rfidStatus', 'statusPkl', 'sort', 'tab', 'statTotal', 'statAlumni', 'statPkl', 'statRombel', 'isWaliOnly', 'waliRombel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'         => 'required|unique:siswas,nis',
            'nisn'        => 'nullable|unique:siswas,nisn',
            'nama'        => 'required|string',
            'rombel_id'   => 'required|exists:rombels,id',
            'nama_ortu'   => 'nullable|string',
            'no_hp_ortu'  => 'nullable|string',
            'no_hp_siswa' => 'nullable|string',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (!$taAktif) {
            $taAktif = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
        }

        $siswa = Siswa::create([
            'nis'         => $request->input('nis'),
            'nisn'        => $request->input('nisn') ?: null,
            'nama'        => $request->input('nama'),
            'nama_ortu'   => $request->input('nama_ortu') ?: null,
            'no_hp_ortu'  => $request->input('no_hp_ortu') ?: null,
            'no_hp_siswa' => $request->input('no_hp_siswa') ?: null,
            'foto'        => $fotoPath,
            'status'      => 'aktif',
        ]);

        SiswaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $request->input('rombel_id'),
            'tahun_ajaran_id' => $taAktif->id,
            'status_keanggotaan' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $request->validate([
            'nis'         => 'required|unique:siswas,nis,' . $id,
            'nisn'        => 'nullable|unique:siswas,nisn,' . $id,
            'nama'        => 'required|string',
            'nama_ortu'   => 'nullable|string',
            'no_hp_ortu'  => 'nullable|string',
            'no_hp_siswa' => 'nullable|string',
            'status'      => 'required|in:aktif,pkl,lulus,pindah,keluar',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = $siswa->foto;
        if ($request->hasFile('foto')) {
            if ($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
            }
            $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
        }

        $siswa->update([
            'nis'         => $request->input('nis'),
            'nisn'        => $request->input('nisn') ?: null,
            'nama'        => $request->input('nama'),
            'nama_ortu'   => $request->input('nama_ortu') ?: null,
            'no_hp_ortu'  => $request->input('no_hp_ortu') ?: null,
            'no_hp_siswa' => $request->input('no_hp_siswa') ?: null,
            'foto'        => $fotoPath,
            'status'      => $request->input('status'),
        ]);

        if ($request->filled('rombel_id')) {
            $taAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::first();
            if ($taAktif) {
                SiswaRombel::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $taAktif->id,
                    ],
                    [
                        'rombel_id' => $request->input('rombel_id'),
                        'status_keanggotaan' => 'aktif',
                    ]
                );
            }
        }

        return redirect()->back()->with('success', "Data siswa {$siswa->nama} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $nama = $siswa->nama;
        if ($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
        }
        $siswa->delete();

        return redirect()->back()->with('success', "Data siswa {$nama} berhasil dihapus.");
    }

    public function export(Request $request)
    {
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        $rombelId = $request->query('rombel_id');
        $query = Siswa::with(['siswaRombels.rombel'])->orderBy('nama');

        if ($isWaliOnly) {
            $query->whereHas('siswaRombels', fn($q) => $q->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif'));
        } elseif ($rombelId) {
            $query->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
        }

        $siswas = $query->get();
        $csvFileName = 'data_siswa_smkn1an_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($siswas) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fwrite($file, "sep=;\n");

            fputcsv($file, ['No', 'NIS', 'NISN', 'Nama Siswa', 'Nama Ortu / Wali', 'No HP / WhatsApp Ortu', 'No HP / WhatsApp Siswa', 'Rombel Kelas', 'Status Keaktifan', 'Biometrik Wajah AI'], ';');

            foreach ($siswas as $idx => $s) {
                $sr = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif');
                fputcsv($file, [
                    $idx + 1,
                    '="' . $s->nis . '"',
                    $s->nisn ? '="' . $s->nisn . '"' : '-',
                    $s->nama,
                    $s->nama_ortu ?? '-',
                    $s->no_hp_ortu ? '="' . $s->no_hp_ortu . '"' : '-',
                    $s->no_hp_siswa ? '="' . $s->no_hp_siswa . '"' : '-',
                    $sr->rombel->nama_rombel ?? '-',
                    strtoupper($s->status),
                    $s->face_embedding ? 'Terdaftar AI' : 'Belum Rekam',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cetak Lembar Data Siswa Terdaftar Format A4 Resmi ber-KOP Dinas.
     */
    public function cetakPdf(Request $request)
    {
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        $rombelId = $request->query('rombel_id');
        $query = Siswa::with(['siswaRombels.rombel'])->orderBy('nama');
        
        $rombel = null;
        if ($isWaliOnly) {
            $effectiveRombelId = $rombelId && in_array($rombelId, $waliRombelIds) ? $rombelId : ($waliRombelIds[0] ?? null);
            if ($effectiveRombelId) {
                $rombel = Rombel::with('jurusan')->find($effectiveRombelId);
                $query->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $effectiveRombelId)->where('status_keanggotaan', 'aktif'));
            }
        } elseif ($rombelId) {
            $rombel = Rombel::with('jurusan')->find($rombelId);
            $query->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
        }

        $siswas = $query->get();
        $sekolah = \App\Models\PengaturanSekolah::getAktif();

        return view('siswa.cetak_pdf', compact('siswas', 'rombel', 'sekolah'));
    }

    /**
     * Unduh Template CSV Format Siswa Resmi.
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_import_siswa_smkn1an.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, ['NIS', 'NISN', 'Nama Lengkap Siswa', 'Nama Orang Tua / Wali', 'No HP WhatsApp Ortu', 'No HP WhatsApp Siswa', 'Nama Kelas (Rombel)', 'Status (Aktif/PKL/Lulus)'], ';');
            fputcsv($file, ['2026101', '0091234001', 'Ahmad Dani Pratama', 'Bpk. Subagio', '081234567890', '081398765432', 'X RPL 1', 'Aktif'], ';');
            fputcsv($file, ['2026102', '0091234002', 'Siti Rahmawati', 'Ibu Maryam', '081234567891', '', 'X APHP 1', 'Aktif'], ';');
            fputcsv($file, ['2026103', '0091234003', 'Bagus Saputra', 'Bpk. Herman', '081234567892', '', 'X TSM 1', 'Aktif'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Data Siswa dari CSV / Excel dengan Parser Cerdas & Multi-Kolom.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $path = $request->file('file')->getRealPath();
        $content = file_get_contents($path);

        if (empty(trim($content))) {
            return redirect()->back()->with('error', 'File CSV kosong.');
        }

        // Bersihkan UTF-8 BOM
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return redirect()->back()->with('error', 'File CSV tidak berisi data.');
        }

        if (str_starts_with(strtolower(trim($lines[0])), 'sep=')) {
            array_shift($lines);
        }

        if (empty($lines)) {
            return redirect()->back()->with('error', 'File CSV tidak berisi baris data.');
        }

        // Deteksi delimiter
        $sample = $lines[0];
        $delimiter = ';';
        if (substr_count($sample, ';') >= substr_count($sample, ',') && substr_count($sample, ';') > 0) {
            $delimiter = ';';
        } elseif (substr_count($sample, ',') > substr_count($sample, ';')) {
            $delimiter = ',';
        } elseif (substr_count($sample, "\t") > 0) {
            $delimiter = "\t";
        }

        $firstRow = str_getcsv($lines[0], $delimiter);
        $headerMap = [];
        $hasHeader = false;

        foreach ($firstRow as $colIdx => $colName) {
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colName)));
            if (in_array($cleanName, ['nis', 'nomorinduk', 'nomorinduksiswa', 'noinduk'])) {
                $headerMap['nis'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nisn', 'nomornisn'])) {
                $headerMap['nisn'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nama', 'namasiswa', 'namalengkap', 'namapesertadidik'])) {
                $headerMap['nama'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['namaortu', 'wali', 'namaorangtua', 'namaayah', 'namawali'])) {
                $headerMap['nama_ortu'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nohportu', 'teleponortu', 'waortu', 'nowaortu', 'kontakortu', 'hportu'])) {
                $headerMap['no_hp_ortu'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nohpsiswa', 'teleponsiswa', 'wasiswa', 'nowasiswa', 'hpsiswa', 'kontaksiswa'])) {
                $headerMap['no_hp_siswa'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['kelas', 'rombel', 'namarombel', 'namakelas', 'rombonganbelajar'])) {
                $headerMap['rombel'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['status', 'keaktifan', 'statuskeaktifan', 'statussiswa'])) {
                $headerMap['status'] = $colIdx;
                $hasHeader = true;
            }
        }

        $taAktif = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::first();
        if (!$taAktif) {
            $taAktif = TahunAjaran::create(['nama' => '2026/2027 Ganjil', 'is_active' => true]);
        }

        $startIndex = $hasHeader ? 1 : 0;
        $imported = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($lines, $startIndex, $delimiter, $hasHeader, $headerMap, $taAktif, &$imported) {
            $rombelMap = Rombel::pluck('id', 'nama_rombel')->toArray();
            // Case-insensitive mapping untuk nama rombel (misal "x rpl 1" -> "X RPL 1")
            $rombelLowerMap = [];
            foreach ($rombelMap as $namaR => $idR) {
                $rombelLowerMap[strtolower(str_replace(' ', '', $namaR))] = $idR;
            }

            for ($i = $startIndex; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if (empty($line)) continue;

                $row = str_getcsv($line, $delimiter);
                if (empty($row) || count(array_filter($row)) === 0) continue;

                $cleanRow = array_map(function ($val) {
                    $val = trim((string)$val);
                    if (str_starts_with($val, '="') && str_ends_with($val, '"')) {
                        $val = substr($val, 2, -1);
                    }
                    return trim($val, "'\" \t\n\r\0\x0B");
                }, $row);

                $nis = null;
                $nisn = null;
                $nama = null;
                $namaOrtu = null;
                $noHpOrtu = null;
                $noHpSiswa = null;
                $namaRombel = null;
                $status = 'aktif';

                if ($hasHeader && isset($headerMap['nama'])) {
                    $nis = isset($headerMap['nis']) ? ($cleanRow[$headerMap['nis']] ?? null) : null;
                    $nisn = isset($headerMap['nisn']) ? ($cleanRow[$headerMap['nisn']] ?? null) : null;
                    $nama = $cleanRow[$headerMap['nama']] ?? null;
                    $namaOrtu = isset($headerMap['nama_ortu']) ? ($cleanRow[$headerMap['nama_ortu']] ?? null) : null;
                    $noHpOrtu = isset($headerMap['no_hp_ortu']) ? ($cleanRow[$headerMap['no_hp_ortu']] ?? null) : null;
                    $noHpSiswa = isset($headerMap['no_hp_siswa']) ? ($cleanRow[$headerMap['no_hp_siswa']] ?? null) : null;
                    $namaRombel = isset($headerMap['rombel']) ? ($cleanRow[$headerMap['rombel']] ?? null) : null;
                    $status = isset($headerMap['status']) ? ($cleanRow[$headerMap['status']] ?? null) : null;
                } else {
                    // Positional default parsing
                    $nis = !empty($cleanRow[0]) ? $cleanRow[0] : null;
                    $nisn = !empty($cleanRow[1]) ? $cleanRow[1] : null;
                    $nama = !empty($cleanRow[2]) ? $cleanRow[2] : null;
                    $namaOrtu = !empty($cleanRow[3]) ? $cleanRow[3] : null;
                    $noHpOrtu = !empty($cleanRow[4]) ? $cleanRow[4] : null;
                    $noHpSiswa = !empty($cleanRow[5]) && (str_starts_with($cleanRow[5], '08') || str_starts_with($cleanRow[5], '62') || str_starts_with($cleanRow[5], '8')) ? $cleanRow[5] : null;
                    $namaRombel = !empty($cleanRow[5]) && !$noHpSiswa ? $cleanRow[5] : (!empty($cleanRow[6]) ? $cleanRow[6] : null);
                    $status = !empty($cleanRow[7]) ? $cleanRow[7] : (!empty($cleanRow[6]) && !$noHpSiswa ? $cleanRow[6] : 'aktif');
                }

                if (empty($nis) || empty($nama)) continue;

                // Sanitasi Status
                $validStatuses = ['aktif', 'pkl', 'lulus', 'pindah', 'keluar'];
                $status = in_array(strtolower($status ?? ''), $validStatuses) ? strtolower($status) : 'aktif';

                // Format No HP
                if ($noHpOrtu && str_starts_with($noHpOrtu, '8')) $noHpOrtu = '0' . $noHpOrtu;
                if ($noHpSiswa && str_starts_with($noHpSiswa, '8')) $noHpSiswa = '0' . $noHpSiswa;

                // Cari siswa berdasarkan NIS atau NISN terlebih dahulu untuk menghindari UNIQUE constraint violation
                $existingSiswa = null;
                if (!empty($nis)) {
                    $existingSiswa = Siswa::where('nis', $nis)->first();
                }
                if (!$existingSiswa && !empty($nisn)) {
                    $existingSiswa = Siswa::where('nisn', $nisn)->first();
                }

                if ($existingSiswa) {
                    $existingSiswa->update([
                        'nis'         => $nis,
                        'nisn'        => $nisn ?: $existingSiswa->nisn,
                        'nama'        => $nama,
                        'nama_ortu'   => $namaOrtu ?: $existingSiswa->nama_ortu,
                        'no_hp_ortu'  => $noHpOrtu ?: $existingSiswa->no_hp_ortu,
                        'no_hp_siswa' => $noHpSiswa ?: $existingSiswa->no_hp_siswa,
                        'status'      => $status,
                    ]);
                    $siswa = $existingSiswa;
                } else {
                    $siswa = Siswa::create([
                        'nis'         => $nis,
                        'nisn'        => $nisn ?: null,
                        'nama'        => $nama,
                        'nama_ortu'   => $namaOrtu ?: null,
                        'no_hp_ortu'  => $noHpOrtu ?: null,
                        'no_hp_siswa' => $noHpSiswa ?: null,
                        'status'      => $status,
                    ]);
                }

                if ($namaRombel) {
                    $targetRombelId = null;
                    if (isset($rombelMap[$namaRombel])) {
                        $targetRombelId = $rombelMap[$namaRombel];
                    } else {
                        $normalizedKey = strtolower(str_replace(' ', '', $namaRombel));
                        if (isset($rombelLowerMap[$normalizedKey])) {
                            $targetRombelId = $rombelLowerMap[$normalizedKey];
                        }
                    }

                    if ($targetRombelId) {
                        SiswaRombel::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $taAktif->id],
                            ['rombel_id' => $targetRombelId, 'status_keanggotaan' => in_array($status, ['aktif', 'pkl']) ? 'aktif' : $status]
                        );
                    }
                }

                $imported++;
            }
        });

        return redirect()->back()->with('success', "Proses import selesai. Berhasil memproses {$imported} data siswa.");
    }
}
