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
        $search = $request->input('search') ?: $request->input('q');
        $rombelId = $request->input('rombel_id');
        $status = $request->input('status');
        $statusPkl = $request->input('status_pkl');
        $biometrikStatus = $request->input('biometrik_status');
        $rfidFilter = $request->input('rfid_status');
        $sort = $request->input('sort', 'nama_asc');

        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        $query = Siswa::with(['siswaRombels' => function ($q) {
            $q->where('status_keanggotaan', 'aktif')->with('rombel');
        }, 'kartuRfid']);

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
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nama_ortu', 'like', "%{$search}%")
                  ->orWhere('no_hp_ortu', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($statusPkl) {
            $query->where('status_pkl', $statusPkl);
        }


        if ($rfidFilter === 'ada') {
            $query->whereHas('kartuRfid');
        } elseif ($rfidFilter === 'belum') {
            $query->whereDoesntHave('kartuRfid');
        }

        $tab = $request->input('tab', 'aktif');
        if (!$status) {
            if ($tab === 'alumni') {
                $query->where('status', 'lulus');
            } elseif ($tab === 'semua') {
                // tampilkan seluruh riwayat
            } else {
                $query->whereIn('status', ['aktif', 'pkl']);
            }
        }

        // Sorting
        switch ($sort) {
            case 'terbaru':
            case 'terakhir_input':
            case 'created_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'terlama':
            case 'created_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nisn_asc':
                $query->orderBy('nisn', 'asc');
                break;
            case 'nisn_desc':
                $query->orderBy('nisn', 'desc');
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

        return view('siswa.index', compact('siswas', 'rombels', 'taAktif', 'search', 'rombelId', 'status', 'rfidStatus', 'statusPkl', 'sort', 'tab', 'statTotal', 'statAlumni', 'statPkl', 'statRombel', 'isWaliOnly', 'waliRombel'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        if ($isWaliOnly) {
            if (empty($waliRombelIds)) {
                return redirect()->back()->with('error', 'Akses Ditolak: Anda belum ditugaskan sebagai wali kelas di rombel manapun.');
            }
            if (!in_array($request->input('rombel_id'), $waliRombelIds)) {
                return redirect()->back()->with('error', 'Akses Ditolak: Anda hanya dapat menambahkan siswa ke kelas yang Anda bina.');
            }
        }

        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn',
            'nik'           => 'nullable|string|max:16',
            'nama'          => 'required|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama'         => 'nullable|string|max:50',
            'hobi'          => 'nullable|string|max:100',
            'organisasi_minat' => 'nullable|string|max:100',
            'alamat'        => 'nullable|string',
            'nama_ayah'     => 'nullable|string|max:100',
            'pekerjaan_ayah'=> 'nullable|string|max:100',
            'pendidikan_ayah'=> 'nullable|string|max:50',
            'no_hp_ayah'    => 'nullable|string|max:25',
            'nama_ibu'      => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'pendidikan_ibu'=> 'nullable|string|max:50',
            'no_hp_ibu'     => 'nullable|string|max:25',
            'asal_sekolah'  => 'nullable|string|max:150',
            'rombel_id'     => 'required|exists:rombels,id',
            'nama_ortu'     => 'nullable|string',
            'no_hp_ortu'    => 'nullable|string',
            'no_hp_siswa'   => 'nullable|string',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (!$taAktif) {
            $taAktif = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
        }

        $namaOrtu = $request->input('nama_ortu') ?: ($request->input('nama_ibu') ?: $request->input('nama_ayah') ?: null);
        $noHpOrtu = $request->input('no_hp_ortu') ?: ($request->input('no_hp_ibu') ?: ($request->input('no_hp_ayah') ?: null));

        $siswa = Siswa::create([
            'nisn'          => $request->input('nisn'),
            'nik'           => $request->input('nik') ?: null,
            'nama'          => $request->input('nama'),
            'jenis_kelamin' => $request->input('jenis_kelamin') ?: null,
            'tempat_lahir'  => $request->input('tempat_lahir') ?: null,
            'tanggal_lahir' => $request->input('tanggal_lahir') ?: null,
            'agama'         => $request->input('agama') ?: null,
            'hobi'          => $request->input('hobi') ?: null,
            'organisasi_minat' => $request->input('organisasi_minat') ?: null,
            'alamat'        => $request->input('alamat') ?: null,
            'nama_ayah'     => $request->input('nama_ayah') ?: null,
            'pekerjaan_ayah'=> $request->input('pekerjaan_ayah') ?: null,
            'pendidikan_ayah'=> $request->input('pendidikan_ayah') ?: null,
            'no_hp_ayah'    => $request->input('no_hp_ayah') ?: null,
            'nama_ibu'      => $request->input('nama_ibu') ?: null,
            'pekerjaan_ibu' => $request->input('pekerjaan_ibu') ?: null,
            'pendidikan_ibu'=> $request->input('pendidikan_ibu') ?: null,
            'no_hp_ibu'     => $request->input('no_hp_ibu') ?: null,
            'nama_ortu'     => $namaOrtu,
            'asal_sekolah'  => $request->input('asal_sekolah') ?: null,
            'no_hp_ortu'    => $noHpOrtu,
            'no_hp_siswa'   => $request->input('no_hp_siswa') ?: null,
            'foto'          => $fotoPath,
            'status'        => 'aktif',
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
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        if ($isWaliOnly) {
            $belongsToWali = $siswa->siswaRombels()->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif')->exists();
            if (!$belongsToWali) {
                return redirect()->back()->with('error', 'Akses Ditolak: Anda hanya dapat mengubah data siswa di kelas yang Anda bina.');
            }
            if ($request->filled('rombel_id') && !in_array($request->input('rombel_id'), $waliRombelIds)) {
                return redirect()->back()->with('error', 'Akses Ditolak: Anda tidak dapat memindahkan siswa ke luar kelas binaan Anda.');
            }
        }

        $request->validate([
            'nisn'          => 'required|unique:siswas,nisn,' . $id,
            'nik'           => 'nullable|string|max:16',
            'nama'          => 'required|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama'         => 'nullable|string|max:50',
            'hobi'          => 'nullable|string|max:100',
            'organisasi_minat' => 'nullable|string|max:100',
            'alamat'        => 'nullable|string',
            'nama_ayah'     => 'nullable|string|max:100',
            'pekerjaan_ayah'=> 'nullable|string|max:100',
            'pendidikan_ayah'=> 'nullable|string|max:50',
            'no_hp_ayah'    => 'nullable|string|max:25',
            'nama_ibu'      => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'pendidikan_ibu'=> 'nullable|string|max:50',
            'no_hp_ibu'     => 'nullable|string|max:25',
            'asal_sekolah'  => 'nullable|string|max:150',
            'nama_ortu'     => 'nullable|string',
            'no_hp_ortu'    => 'nullable|string',
            'no_hp_siswa'   => 'nullable|string',
            'status'        => 'required|in:aktif,pkl,lulus,pindah,keluar',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = $siswa->foto;
        if ($request->hasFile('foto')) {
            if ($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
            }
            $fotoPath = $request->file('foto')->store('foto_siswa', 'public');
        }

        $namaOrtu = $request->input('nama_ortu') ?: ($request->input('nama_ibu') ?: $request->input('nama_ayah') ?: $siswa->nama_ortu);
        $noHpOrtu = $request->input('no_hp_ortu') ?: ($request->input('no_hp_ibu') ?: ($request->input('no_hp_ayah') ?: $siswa->no_hp_ortu));

        $siswa->update([
            'nisn'          => $request->input('nisn'),
            'nik'           => $request->input('nik') ?: null,
            'nama'          => $request->input('nama'),
            'jenis_kelamin' => $request->input('jenis_kelamin') ?: null,
            'tempat_lahir'  => $request->input('tempat_lahir') ?: null,
            'tanggal_lahir' => $request->input('tanggal_lahir') ?: null,
            'agama'         => $request->input('agama') ?: null,
            'hobi'          => $request->input('hobi') ?: null,
            'organisasi_minat' => $request->input('organisasi_minat') ?: null,
            'alamat'        => $request->input('alamat') ?: null,
            'nama_ayah'     => $request->input('nama_ayah') ?: null,
            'pekerjaan_ayah'=> $request->input('pekerjaan_ayah') ?: null,
            'pendidikan_ayah'=> $request->input('pendidikan_ayah') ?: null,
            'no_hp_ayah'    => $request->input('no_hp_ayah') ?: null,
            'nama_ibu'      => $request->input('nama_ibu') ?: null,
            'pekerjaan_ibu' => $request->input('pekerjaan_ibu') ?: null,
            'pendidikan_ibu'=> $request->input('pendidikan_ibu') ?: null,
            'no_hp_ibu'     => $request->input('no_hp_ibu') ?: null,
            'nama_ortu'     => $namaOrtu,
            'asal_sekolah'  => $request->input('asal_sekolah') ?: null,
            'no_hp_ortu'    => $noHpOrtu,
            'no_hp_siswa'   => $request->input('no_hp_siswa') ?: null,
            'foto'          => $fotoPath,
            'status'        => $request->input('status'),
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
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isGuruBk();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        if ($isWaliOnly) {
            $belongsToWali = $siswa->siswaRombels()->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif')->exists();
            if (!$belongsToWali) {
                return redirect()->back()->with('error', 'Akses Ditolak: Anda hanya dapat menghapus data siswa di kelas yang Anda bina.');
            }
        }

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

            fputcsv($file, ['No', 'NISN', 'Nama Siswa', 'Nama Ortu / Wali', 'No HP / WhatsApp Ortu', 'No HP / WhatsApp Siswa', 'Rombel Kelas', 'Status Keaktifan'], ';');

            foreach ($siswas as $idx => $s) {
                $sr = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif');
                fputcsv($file, [
                    $idx + 1,
                    $s->nisn ? '="' . $s->nisn . '"' : '-',
                    $s->nama,
                    $s->nama_ortu ?? '-',
                    $s->no_hp_ortu ? '="' . $s->no_hp_ortu . '"' : '-',
                    $s->no_hp_siswa ? '="' . $s->no_hp_siswa . '"' : '-',
                    $sr->rombel->nama_rombel ?? '-',
                    strtoupper($s->status),
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
        $ids = $request->query('ids') ?: $request->input('ids');
        $selectedIds = [];
        if (!empty($ids)) {
            $selectedIds = is_array($ids) ? $ids : array_filter(explode(',', $ids));
        }

        $query = Siswa::with(['siswaRombels.rombel.jurusan'])->orderBy('nama');
        
        $rombel = null;
        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
            if ($isWaliOnly) {
                $rombels = Rombel::whereIn('id', $waliRombelIds)->orderBy('nama_rombel')->get();
            } else {
                $rombels = Rombel::orderBy('nama_rombel')->get();
            }
        } elseif ($isWaliOnly) {
            $effectiveRombelId = $rombelId && in_array($rombelId, $waliRombelIds) ? $rombelId : ($waliRombelIds[0] ?? null);
            if ($effectiveRombelId) {
                $rombel = Rombel::with('jurusan')->find($effectiveRombelId);
                $query->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $effectiveRombelId)->where('status_keanggotaan', 'aktif'));
            }
            $rombels = Rombel::whereIn('id', $waliRombelIds)->orderBy('nama_rombel')->get();
        } else {
            if ($rombelId) {
                $rombel = Rombel::with('jurusan')->find($rombelId);
                $query->whereHas('siswaRombels', fn($q) => $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif'));
            }
            $rombels = Rombel::orderBy('nama_rombel')->get();
        }

        $siswas = $query->get();
        $sekolah = \App\Models\PengaturanSekolah::getAktif();

        return view('siswa.cetak_pdf', compact('siswas', 'rombel', 'rombelId', 'rombels', 'sekolah', 'isWaliOnly', 'selectedIds'));
    }

    /**
     * Unduh Template CSV Format Siswa Resmi (Standar Dapodik).
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_import_siswa_dapodik_smkn1an.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, [
                'NISN',
                'NIK',
                'Nama Lengkap Siswa',
                'Jenis Kelamin (L/P)',
                'Tempat Lahir',
                'Tanggal Lahir (YYYY-MM-DD)',
                'Agama',
                'Hobi Siswa',
                'Organisasi Diminati',
                'Alamat Lengkap',
                'Nama Ayah',
                'Pekerjaan Ayah',
                'Pendidikan Ayah',
                'No HP WA Ayah',
                'Nama Ibu',
                'Pekerjaan Ibu',
                'Pendidikan Ibu',
                'No HP WA Ibu',
                'Nama Orang Tua / Wali',
                'No HP WhatsApp Ortu',
                'No HP WhatsApp Siswa',
                'Asal Sekolah',
                'Nama Kelas (Rombel)',
                'Status (Aktif/PKL/Lulus)'
            ], ';');

            fputcsv($file, [
                '0091234001',
                '1806123456780001',
                'Ahmad Dani Pratama',
                'L',
                'Tanggamus',
                '2009-04-12',
                'Islam',
                'Membaca, Komputer',
                'OSIS, Pramuka',
                'Jl. Raya Air Naningan RT 02 Dusun 01',
                'Bpk. Subagio',
                'Petani / Pekebun',
                'SMA / SMK',
                '081234567890',
                'Ibu Warsini',
                'Ibu Rumah Tangga',
                'SMP',
                '081234567891',
                'Bpk. Subagio',
                '081234567890',
                '081398765432',
                'SMPN 1 Air Naningan',
                'X RPL',
                'Aktif'
            ], ';');

            fputcsv($file, [
                '0091234002',
                '1806123456780002',
                'Siti Rahmawati',
                'P',
                'Pringsewu',
                '2009-08-25',
                'Islam',
                'Memasak, Menjahit',
                'PMR, Rohis',
                'Pekon Datar Lebuay, Air Naningan',
                'Bpk. Karsono',
                'Wiraswasta',
                'SMA / SMK',
                '081234567893',
                'Ibu Maryam',
                'Pedagang',
                'SMP',
                '081234567894',
                'Ibu Maryam',
                '081234567894',
                '',
                'MTs Al-Falah',
                'X APHP',
                'Aktif'
            ], ';');

            fputcsv($file, [
                '0091234003',
                '1806123456780003',
                'Bagus Saputra',
                'L',
                'Tanggamus',
                '2008-11-05',
                'Islam',
                'Otomotif, Olahraga',
                'Paskibra, Futsal',
                'Pekon Way Pring, Pugung',
                'Bpk. Herman',
                'Buruh Harian',
                'SMP',
                '081234567895',
                'Ibu Sumiati',
                'Ibu Rumah Tangga',
                'SD',
                '081234567896',
                'Bpk. Herman',
                '081234567895',
                '',
                'SMPN 2 Air Naningan',
                'X TSM',
                'Aktif'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Data Siswa dari CSV / Excel dengan Parser Cerdas & Multi-Kolom (Mendukung Ekspor Dapodik).
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
            
            // Prioritas NISN lebih tinggi dari NIS biasa
            if (str_contains($cleanName, 'nisn')) {
                $headerMap['nisn'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nik', 'noktp', 'nokk', 'nikpd', 'nomorindukkependudukan'])) {
                $headerMap['nik'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nis', 'noinduk', 'nomorinduk', 'nipd']) && !isset($headerMap['nisn'])) {
                $headerMap['nisn'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['jk', 'jeniskelamin', 'kelamin', 'gender', 'sex'])) {
                $headerMap['jenis_kelamin'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'tempatlahir') || str_contains($cleanName, 'tmplahir')) {
                $headerMap['tempat_lahir'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'tanggallahir') || str_contains($cleanName, 'tgllahir')) {
                $headerMap['tanggal_lahir'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'agama')) {
                $headerMap['agama'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'alamat') || str_contains($cleanName, 'domisili') || str_contains($cleanName, 'tempattinggal') || str_contains($cleanName, 'jalan')) {
                $headerMap['alamat'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'asalsekolah') || str_contains($cleanName, 'sekolahasal') || str_contains($cleanName, 'smp') || str_contains($cleanName, 'mts')) {
                $headerMap['asal_sekolah'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'hobi') || str_contains($cleanName, 'hobby') || str_contains($cleanName, 'kegemaran')) {
                $headerMap['hobi'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'organisasi') || str_contains($cleanName, 'ekskul') || str_contains($cleanName, 'ekstrakurikuler') || (str_contains($cleanName, 'minat') && !str_contains($cleanName, 'jurusan'))) {
                $headerMap['organisasi_minat'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ayah') && (str_contains($cleanName, 'kerja') || str_contains($cleanName, 'profesi') || str_contains($cleanName, 'pekerjaan'))) {
                $headerMap['pekerjaan_ayah'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ayah') && (str_contains($cleanName, 'didik') || str_contains($cleanName, 'pendidikan') || str_contains($cleanName, 'pddk') || str_contains($cleanName, 'ijazah'))) {
                $headerMap['pendidikan_ayah'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ayah') && (str_contains($cleanName, 'hp') || str_contains($cleanName, 'wa') || str_contains($cleanName, 'telepon') || str_contains($cleanName, 'telp') || str_contains($cleanName, 'kontak'))) {
                $headerMap['no_hp_ayah'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ibu') && (str_contains($cleanName, 'kerja') || str_contains($cleanName, 'profesi') || str_contains($cleanName, 'pekerjaan'))) {
                $headerMap['pekerjaan_ibu'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ibu') && (str_contains($cleanName, 'didik') || str_contains($cleanName, 'pendidikan') || str_contains($cleanName, 'pddk') || str_contains($cleanName, 'ijazah'))) {
                $headerMap['pendidikan_ibu'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ibu') && (str_contains($cleanName, 'hp') || str_contains($cleanName, 'wa') || str_contains($cleanName, 'telepon') || str_contains($cleanName, 'telp') || str_contains($cleanName, 'kontak'))) {
                $headerMap['no_hp_ibu'] = $colIdx;
                $hasHeader = true;
            } elseif (
                (str_contains($cleanName, 'ayah') || str_contains($cleanName, 'ibu') || str_contains($cleanName, 'ortu') || str_contains($cleanName, 'wali') || str_contains($cleanName, 'orangtua'))
                && (str_contains($cleanName, 'hp') || str_contains($cleanName, 'whatsapp') || str_contains($cleanName, 'nowa') || str_contains($cleanName, 'telepon') || str_contains($cleanName, 'kontak') || str_contains($cleanName, 'telp'))
            ) {
                if (!isset($headerMap['no_hp_ortu'])) $headerMap['no_hp_ortu'] = $colIdx;
                $hasHeader = true;
            } elseif (
                (str_contains($cleanName, 'siswa') || str_contains($cleanName, 'peserta') || str_contains($cleanName, 'anak'))
                && (str_contains($cleanName, 'hp') || str_contains($cleanName, 'whatsapp') || str_contains($cleanName, 'nowa') || str_contains($cleanName, 'telepon') || str_contains($cleanName, 'kontak') || str_contains($cleanName, 'telp'))
            ) {
                $headerMap['no_hp_siswa'] = $colIdx;
                $hasHeader = true;
            } elseif (
                (str_contains($cleanName, 'nama') || str_contains($cleanName, 'peserta') || str_contains($cleanName, 'siswa'))
                && !str_contains($cleanName, 'ortu') && !str_contains($cleanName, 'wali') && !str_contains($cleanName, 'orangtua')
                && !str_contains($cleanName, 'ayah') && !str_contains($cleanName, 'ibu')
                && !str_contains($cleanName, 'kelas') && !str_contains($cleanName, 'rombel')
                && !str_contains($cleanName, 'sekolah')
                && !str_contains($cleanName, 'hp') && !str_contains($cleanName, 'whatsapp') && !str_contains($cleanName, 'nowa') && !str_contains($cleanName, 'telepon') && !str_contains($cleanName, 'kontak')
            ) {
                $headerMap['nama'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ayah')) {
                $headerMap['nama_ayah'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ibu')) {
                $headerMap['nama_ibu'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'ortu') || str_contains($cleanName, 'wali') || str_contains($cleanName, 'orangtua')) {
                $headerMap['nama_ortu'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'kelas') || str_contains($cleanName, 'rombel')) {
                $headerMap['rombel'] = $colIdx;
                $hasHeader = true;
            } elseif (str_contains($cleanName, 'status') || str_contains($cleanName, 'keaktifan')) {
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

                $nisn         = null;
                $nik          = null;
                $nama         = null;
                $jenisKelamin = null;
                $tempatLahir  = null;
                $tanggalLahir = null;
                $agama        = null;
                $hobi         = null;
                $organisasiMinat = null;
                $alamat       = null;
                $namaAyah     = null;
                $pekerjaanAyah= null;
                $pendidikanAyah= null;
                $noHpAyah     = null;
                $namaIbu      = null;
                $pekerjaanIbu = null;
                $pendidikanIbu= null;
                $noHpIbu      = null;
                $namaOrtu     = null;
                $asalSekolah  = null;
                $noHpOrtu     = null;
                $noHpSiswa    = null;
                $namaRombel   = null;
                $status       = 'aktif';

                if ($hasHeader && isset($headerMap['nama'])) {
                    $nisn         = isset($headerMap['nisn']) ? ($cleanRow[$headerMap['nisn']] ?? null) : null;
                    $nik          = isset($headerMap['nik']) ? ($cleanRow[$headerMap['nik']] ?? null) : null;
                    $nama         = $cleanRow[$headerMap['nama']] ?? null;
                    $jenisKelamin = isset($headerMap['jenis_kelamin']) ? ($cleanRow[$headerMap['jenis_kelamin']] ?? null) : null;
                    $tempatLahir  = isset($headerMap['tempat_lahir']) ? ($cleanRow[$headerMap['tempat_lahir']] ?? null) : null;
                    $tanggalLahir = isset($headerMap['tanggal_lahir']) ? ($cleanRow[$headerMap['tanggal_lahir']] ?? null) : null;
                    $agama        = isset($headerMap['agama']) ? ($cleanRow[$headerMap['agama']] ?? null) : null;
                    $hobi         = isset($headerMap['hobi']) ? ($cleanRow[$headerMap['hobi']] ?? null) : null;
                    $organisasiMinat = isset($headerMap['organisasi_minat']) ? ($cleanRow[$headerMap['organisasi_minat']] ?? null) : null;
                    $alamat       = isset($headerMap['alamat']) ? ($cleanRow[$headerMap['alamat']] ?? null) : null;
                    $namaAyah     = isset($headerMap['nama_ayah']) ? ($cleanRow[$headerMap['nama_ayah']] ?? null) : null;
                    $pekerjaanAyah= isset($headerMap['pekerjaan_ayah']) ? ($cleanRow[$headerMap['pekerjaan_ayah']] ?? null) : null;
                    $pendidikanAyah= isset($headerMap['pendidikan_ayah']) ? ($cleanRow[$headerMap['pendidikan_ayah']] ?? null) : null;
                    $noHpAyah     = isset($headerMap['no_hp_ayah']) ? ($cleanRow[$headerMap['no_hp_ayah']] ?? null) : null;
                    $namaIbu      = isset($headerMap['nama_ibu']) ? ($cleanRow[$headerMap['nama_ibu']] ?? null) : null;
                    $pekerjaanIbu = isset($headerMap['pekerjaan_ibu']) ? ($cleanRow[$headerMap['pekerjaan_ibu']] ?? null) : null;
                    $pendidikanIbu= isset($headerMap['pendidikan_ibu']) ? ($cleanRow[$headerMap['pendidikan_ibu']] ?? null) : null;
                    $noHpIbu      = isset($headerMap['no_hp_ibu']) ? ($cleanRow[$headerMap['no_hp_ibu']] ?? null) : null;
                    $namaOrtu     = isset($headerMap['nama_ortu']) ? ($cleanRow[$headerMap['nama_ortu']] ?? null) : null;
                    $asalSekolah  = isset($headerMap['asal_sekolah']) ? ($cleanRow[$headerMap['asal_sekolah']] ?? null) : null;
                    $noHpOrtu     = isset($headerMap['no_hp_ortu']) ? ($cleanRow[$headerMap['no_hp_ortu']] ?? null) : null;
                    $noHpSiswa    = isset($headerMap['no_hp_siswa']) ? ($cleanRow[$headerMap['no_hp_siswa']] ?? null) : null;
                    $namaRombel   = isset($headerMap['rombel']) ? ($cleanRow[$headerMap['rombel']] ?? null) : null;
                    $status       = isset($headerMap['status']) ? ($cleanRow[$headerMap['status']] ?? null) : null;
                } else {
                    // Positional default parsing
                    $nisn        = !empty($cleanRow[0]) ? $cleanRow[0] : null;
                    $nama        = !empty($cleanRow[1]) ? $cleanRow[1] : null;
                    $namaOrtu    = !empty($cleanRow[2]) ? $cleanRow[2] : null;
                    $noHpOrtu    = !empty($cleanRow[3]) ? $cleanRow[3] : null;
                    $noHpSiswa   = !empty($cleanRow[4]) && (str_starts_with($cleanRow[4], '08') || str_starts_with($cleanRow[4], '62') || str_starts_with($cleanRow[4], '8')) ? $cleanRow[4] : null;
                    $namaRombel  = !empty($cleanRow[4]) && !$noHpSiswa ? $cleanRow[4] : (!empty($cleanRow[5]) ? $cleanRow[5] : null);
                    $status      = !empty($cleanRow[6]) ? $cleanRow[6] : (!empty($cleanRow[5]) && !$noHpSiswa ? $cleanRow[5] : 'aktif');
                }

                if (empty($nisn) || empty($nama)) {
                    continue;
                }

                // Normalisasi Jenis Kelamin
                if (!empty($jenisKelamin)) {
                    $jkUpper = strtoupper(trim((string)$jenisKelamin));
                    if (str_starts_with($jkUpper, 'L') || $jkUpper === 'PRIA' || $jkUpper === 'LAKI-LAKI') {
                        $jenisKelamin = 'L';
                    } elseif (str_starts_with($jkUpper, 'P') || $jkUpper === 'WANITA' || $jkUpper === 'PEREMPUAN') {
                        $jenisKelamin = 'P';
                    } else {
                        $jenisKelamin = null;
                    }
                }

                // Normalisasi Tanggal Lahir
                if (!empty($tanggalLahir)) {
                    $tglParsed = null;
                    try {
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahir)) {
                            $tglParsed = $tanggalLahir;
                        } elseif (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $tanggalLahir, $matches)) {
                            $tglParsed = sprintf('%04d-%02d-%02d', (int)$matches[3], (int)$matches[2], (int)$matches[1]);
                        } else {
                            $tglParsed = \Carbon\Carbon::parse($tanggalLahir)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $tglParsed = null;
                    }
                    $tanggalLahir = $tglParsed;
                }

                // Normalisasi Nama Ortu
                if (empty($namaOrtu)) {
                    $namaOrtu = $namaIbu ?: $namaAyah;
                }

                // Sanitasi Status
                $validStatuses = ['aktif', 'pkl', 'lulus', 'pindah', 'keluar'];
                $status = in_array(strtolower($status ?? ''), $validStatuses) ? strtolower($status) : 'aktif';

                // Format No HP cerdas (buang strip/spasi, normalisasi 62/8 -> 08)
                $cleanPhoneHelper = function($ph) {
                    if (empty($ph)) return null;
                    if (preg_match('/(ga ada|nggak|nggk|tidak ada|belum|none|hapal)/i', $ph)) return null;
                    $dig = preg_replace('/[^0-9]/', '', $ph);
                    if (empty($dig) || strlen($dig) < 7) return null;
                    if (str_starts_with($dig, '62')) $dig = '0' . substr($dig, 2);
                    elseif (str_starts_with($dig, '8')) $dig = '0' . $dig;
                    return $dig;
                };
                $noHpOrtu = $cleanPhoneHelper($noHpOrtu) ?: ($cleanPhoneHelper($noHpIbu) ?: $cleanPhoneHelper($noHpAyah));
                $noHpSiswa = $cleanPhoneHelper($noHpSiswa);
                $noHpAyah = $cleanPhoneHelper($noHpAyah);
                $noHpIbu = $cleanPhoneHelper($noHpIbu);

                // Cari siswa berdasarkan NISN
                $cleanNisnCheck = preg_replace('/[^0-9]/', '', (string)$nisn);
                $existingSiswa = Siswa::where('nisn', $nisn)
                    ->orWhere('nisn', ltrim($cleanNisnCheck, '0'))
                    ->orWhere('nisn', str_pad($cleanNisnCheck, 10, '0', STR_PAD_LEFT))
                    ->first();

                if ($existingSiswa) {
                    $existingSiswa->update([
                        'nisn'          => $nisn,
                        'nik'           => $nik ?: $existingSiswa->nik,
                        'nama'          => $nama,
                        'jenis_kelamin' => $jenisKelamin ?: $existingSiswa->jenis_kelamin,
                        'tempat_lahir'  => $tempatLahir ?: $existingSiswa->tempat_lahir,
                        'tanggal_lahir' => $tanggalLahir ?: $existingSiswa->tanggal_lahir,
                        'agama'         => $agama ?: $existingSiswa->agama,
                        'hobi'          => $hobi ?: $existingSiswa->hobi,
                        'organisasi_minat' => $organisasiMinat ?: $existingSiswa->organisasi_minat,
                        'alamat'        => $alamat ?: $existingSiswa->alamat,
                        'nama_ayah'     => $namaAyah ?: $existingSiswa->nama_ayah,
                        'pekerjaan_ayah'=> $pekerjaanAyah ?: $existingSiswa->pekerjaan_ayah,
                        'pendidikan_ayah'=> $pendidikanAyah ?: $existingSiswa->pendidikan_ayah,
                        'no_hp_ayah'    => $noHpAyah ?: $existingSiswa->no_hp_ayah,
                        'nama_ibu'      => $namaIbu ?: $existingSiswa->nama_ibu,
                        'pekerjaan_ibu' => $pekerjaanIbu ?: $existingSiswa->pekerjaan_ibu,
                        'pendidikan_ibu'=> $pendidikanIbu ?: $existingSiswa->pendidikan_ibu,
                        'no_hp_ibu'     => $noHpIbu ?: $existingSiswa->no_hp_ibu,
                        'nama_ortu'     => $namaOrtu ?: $existingSiswa->nama_ortu,
                        'asal_sekolah'  => $asalSekolah ?: $existingSiswa->asal_sekolah,
                        'no_hp_ortu'    => $noHpOrtu ?: $existingSiswa->no_hp_ortu,
                        'no_hp_siswa'   => $noHpSiswa ?: $existingSiswa->no_hp_siswa,
                        'status'        => $status,
                    ]);
                    $siswa = $existingSiswa;
                } else {
                    $siswa = Siswa::create([
                        'nisn'          => $nisn,
                        'nik'           => $nik ?: null,
                        'nama'          => $nama,
                        'jenis_kelamin' => $jenisKelamin ?: null,
                        'tempat_lahir'  => $tempatLahir ?: null,
                        'tanggal_lahir' => $tanggalLahir ?: null,
                        'agama'         => $agama ?: null,
                        'hobi'          => $hobi ?: null,
                        'organisasi_minat' => $organisasiMinat ?: null,
                        'alamat'        => $alamat ?: null,
                        'nama_ayah'     => $namaAyah ?: null,
                        'pekerjaan_ayah'=> $pekerjaanAyah ?: null,
                        'pendidikan_ayah'=> $pendidikanAyah ?: null,
                        'no_hp_ayah'    => $noHpAyah ?: null,
                        'nama_ibu'      => $namaIbu ?: null,
                        'pekerjaan_ibu' => $pekerjaanIbu ?: null,
                        'pendidikan_ibu'=> $pendidikanIbu ?: null,
                        'no_hp_ibu'     => $noHpIbu ?: null,
                        'nama_ortu'     => $namaOrtu ?: null,
                        'asal_sekolah'  => $asalSekolah ?: null,
                        'no_hp_ortu'    => $noHpOrtu ?: null,
                        'no_hp_siswa'   => $noHpSiswa ?: null,
                        'status'        => $status,
                    ]);
                }

                if ($namaRombel) {
                    $targetRombelId = null;
                    if (isset($rombelMap[$namaRombel])) {
                        $targetRombelId = $rombelMap[$namaRombel];
                    } else {
                        $normalizedKey = strtolower(str_replace(' ', '', $namaRombel));
                        $targetRombelId = $rombelLowerMap[$normalizedKey] ?? null;
                        if (!$targetRombelId) {
                            $strippedKey = preg_replace('/[0-9]+$/', '', $normalizedKey);
                            $targetRombelId = $rombelLowerMap[$strippedKey] ?? null;
                        }
                    }

                    if ($targetRombelId) {
                        SiswaRombel::updateOrCreate(
                            [
                                'siswa_id' => $siswa->id,
                                'tahun_ajaran_id' => $taAktif->id,
                            ],
                            [
                                'rombel_id' => $targetRombelId,
                                'status_keanggotaan' => 'aktif',
                            ]
                        );
                    }
                }

                $imported++;
            }
        });

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data siswa.");
    }
}
