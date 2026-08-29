<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with(['user', 'rombels']);

        $search = $request->input('q');
        $kategori = $request->input('kategori'); // wali_kelas, bk, pimpinan, staf, guru
        $kepegawaian = $request->input('kepegawaian'); // pns, pppk, honor, tendik
        $status = $request->input('status'); // aktif, nonaktif
        $biometrikStatus = $request->input('face_id', $request->input('rfid')); // Filter Face ID: ada, belum

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($kepegawaian) {
            $query->where('jenis_kepegawaian', $kepegawaian);
        }

        if ($biometrikStatus === 'ada') {
            $query->whereNotNull('face_embedding');
        } elseif ($biometrikStatus === 'belum') {
            $query->whereNull('face_embedding');
        }

        if ($kategori === 'wali_kelas') {
            $query->whereHas('rombels');
        } elseif ($kategori === 'bk') {
            $query->where(function($q) {
                $q->where('jabatan', 'like', '%BK%')->orWhere('jabatan', 'like', '%Bimbingan%');
            });
        } elseif ($kategori === 'pimpinan') {
            $query->where(function($q) {
                $q->where('jabatan', 'like', '%Kepala%')
                  ->orWhere('jabatan', 'like', '%Wakil%')
                  ->orWhere('jabatan', 'like', '%Waka%')
                  ->orWhere('jabatan', 'like', '%Kaprogli%');
            });
        } elseif ($kategori === 'staf') {
            $query->where(function($q) {
                $q->where('jabatan', 'like', '%Tata Usaha%')
                  ->orWhere('jabatan', 'like', '%Operator%')
                  ->orWhere('jabatan', 'like', '%Staf%')
                  ->orWhere('jabatan', 'like', '%Pustakawan%');
            });
        }

        // Sorting
        $sort = $request->input('sort', 'nama_asc');
        switch ($sort) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nip_asc':
                $query->orderBy('nip', 'asc');
                break;
            case 'nip_desc':
                $query->orderBy('nip', 'desc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $gurus = $query->paginate(20)->withQueryString();

        // Statistik Cepat Terpadu
        $statTotal = Guru::count();
        $statWali = Guru::whereHas('rombels')->count();
        $statAkun = Guru::whereHas('user')->count();
        $rfidStatus = $biometrikStatus;

        return view('guru.index', compact('gurus', 'statTotal', 'statWali', 'statAkun', 'search', 'kategori', 'kepegawaian', 'status', 'rfidStatus', 'sort'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|unique:gurus,nip',
            'nama' => 'required|string',
            'jabatan' => 'required|string',
            'jenis_kepegawaian' => 'nullable|in:pns,pppk,honor,tendik',
            'hari_mengajar' => 'nullable|array',
            'no_hp' => 'nullable|string|max:25',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'email_akun' => 'nullable|email|unique:users,email',
            'password_akun' => 'nullable|string|min:6',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_guru', 'public');
        }

        $guru = Guru::create([
            'nip' => $request->input('nip') ?: null,
            'nama' => $request->input('nama'),
            'jabatan' => $request->input('jabatan'),
            'jenis_kepegawaian' => $request->input('jenis_kepegawaian', 'pns'),
            'hari_mengajar' => $request->input('hari_mengajar') ?: null,
            'no_hp' => $request->input('no_hp') ?: null,
            'foto' => $fotoPath,
            'status' => 'aktif',
        ]);

        if ($request->filled('email_akun') && $request->filled('password_akun')) {
            $role = ($request->input('jabatan') === 'Kepala Sekolah') ? 'kepala_sekolah' : 'guru';
            User::create([
                'name' => $guru->nama,
                'email' => $request->input('email_akun'),
                'password' => Hash::make($request->input('password_akun')),
                'guru_id' => $guru->id,
                'role' => $role,
            ]);
        }

        return redirect()->back()->with('success', 'Data guru/pegawai dan pengaturan akses berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $request->validate([
            'nip' => 'nullable|unique:gurus,nip,' . $id,
            'nama' => 'required|string',
            'jabatan' => 'required|string',
            'jenis_kepegawaian' => 'nullable|in:pns,pppk,honor,tendik',
            'hari_mengajar' => 'nullable|array',
            'no_hp' => 'nullable|string|max:25',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = $guru->foto;
        if ($request->hasFile('foto')) {
            if ($guru->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($guru->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($guru->foto);
            }
            $fotoPath = $request->file('foto')->store('foto_guru', 'public');
        }

        $guru->update([
            'nip' => $request->input('nip') ?: null,
            'nama' => $request->input('nama'),
            'jabatan' => $request->input('jabatan'),
            'jenis_kepegawaian' => $request->input('jenis_kepegawaian', 'pns'),
            'hari_mengajar' => $request->input('hari_mengajar') ?: null,
            'no_hp' => $request->input('no_hp') ?: null,
            'foto' => $fotoPath,
            'status' => $request->input('status'),
        ]);

        return redirect()->back()->with('success', 'Data guru/pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $nama = $guru->nama;
        if ($guru->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($guru->foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($guru->foto);
        }
        $guru->delete();

        return redirect()->back()->with('success', "Data guru {$nama} berhasil dihapus.");
    }

    public function export()
    {
        $gurus = Guru::orderBy('nama')->get();
        $csvFileName = 'data_guru_smkn1an_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($gurus) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fwrite($file, "sep=;\n");

            fputcsv($file, ['No', 'NIP', 'Nama Guru / Pegawai', 'Jabatan / Peran', 'No HP / WhatsApp', 'Status Kepegawaian', 'Biometrik Wajah AI'], ';');

            foreach ($gurus as $idx => $g) {
                fputcsv($file, [
                    $idx + 1,
                    $g->nip ? '="' . $g->nip . '"' : '-',
                    $g->nama,
                    $g->jabatan,
                    $g->no_hp ? '="' . $g->no_hp . '"' : '-',
                    strtoupper($g->status),
                    $g->face_embedding ? 'Terdaftar AI' : 'Belum Rekam',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cetak Daftar Guru & Tenaga Kependidikan Format A4 Resmi ber-KOP Dinas.
     */
    public function cetakPdf(Request $request)
    {
        $gurus = Guru::with(['user'])->orderBy('nama')->get();
        $sekolah = \App\Models\PengaturanSekolah::getAktif();

        return view('guru.cetak_pdf', compact('gurus', 'sekolah'));
    }

    /**
     * Unduh Template CSV Format Guru Resmi.
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_import_guru_smkn1an.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, ['Nama Lengkap & Gelar', 'NIP', 'Jabatan / Tugas', 'Status Kepegawaian (PNS/PPPK/Honor/Tendik)', 'No WhatsApp / HP', 'Status Keaktifan (Aktif/Nonaktif)'], ';');
            fputcsv($file, ['Budi Santoso, S.Pd', '198501012010011005', 'Guru Matematika', 'PNS', '081234567890', 'Aktif'], ';');
            fputcsv($file, ['Siska Widyawati, S.Pd', '198902052023212024', 'Guru PPKn', 'PPPK', '081373310855', 'Aktif'], ';');
            fputcsv($file, ['Rian Kurniawan, S.Pd', '', 'Guru Penjaskes', 'Honor', '081272001006', 'Aktif'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Data Guru & Pegawai dari CSV / Excel dengan Parser Cerdas & Fleksibel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $path = $request->file('file')->getRealPath();
        $content = file_get_contents($path);

        if (empty(trim($content))) {
            return redirect()->back()->with('error', 'File CSV yang Anda unggah kosong.');
        }

        // Bersihkan UTF-8 BOM
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return redirect()->back()->with('error', 'File CSV tidak berisi data.');
        }

        // Hapus directive sep= jika ada
        if (str_starts_with(strtolower(trim($lines[0])), 'sep=')) {
            array_shift($lines);
        }

        if (empty($lines)) {
            return redirect()->back()->with('error', 'File CSV tidak berisi baris data.');
        }

        // Deteksi delimiter (; atau , atau tab \t)
        $sample = $lines[0];
        $delimiter = ';';
        if (substr_count($sample, ';') >= substr_count($sample, ',') && substr_count($sample, ';') > 0) {
            $delimiter = ';';
        } elseif (substr_count($sample, ',') > substr_count($sample, ';')) {
            $delimiter = ',';
        } elseif (substr_count($sample, "\t") > 0) {
            $delimiter = "\t";
        }

        // Parse baris pertama sebagai calon header
        $firstRow = str_getcsv($lines[0], $delimiter);
        $headerMap = [];
        $hasHeader = false;

        foreach ($firstRow as $colIdx => $colName) {
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colName)));
            if (in_array($cleanName, ['nama', 'namaguru', 'namalengkap', 'namadanlengkap', 'namapegawai'])) {
                $headerMap['nama'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nip', 'nipguru', 'noinduk', 'nippegawai', 'nuptk'])) {
                $headerMap['nip'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['jabatan', 'tugas', 'penugasan', 'mapel', 'gurumapel'])) {
                $headerMap['jabatan'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nohp', 'hp', 'wa', 'nowa', 'telepon', 'kontak', 'nohpwa', 'nomorhp'])) {
                $headerMap['no_hp'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['status', 'keaktifan', 'statuskeaktifan'])) {
                $headerMap['status'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['statuskepegawaian', 'jeniskepegawaian', 'kepegawaian', 'pnsgtt', 'golongan'])) {
                $headerMap['jenis_kepegawaian'] = $colIdx;
                $hasHeader = true;
            }
        }

        $startIndex = $hasHeader ? 1 : 0;
        $imported = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($lines, $startIndex, $delimiter, $hasHeader, $headerMap, &$imported) {
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

                $nama = null;
                $nip = null;
                $jabatan = null;
                $noHp = null;
                $status = 'aktif';
                $jenisKepegawaian = 'pns';

                if ($hasHeader && isset($headerMap['nama'])) {
                    $nama = $cleanRow[$headerMap['nama']] ?? null;
                    $nip = isset($headerMap['nip']) ? ($cleanRow[$headerMap['nip']] ?? null) : null;
                    $jabatan = isset($headerMap['jabatan']) ? ($cleanRow[$headerMap['jabatan']] ?? null) : null;
                    $noHp = isset($headerMap['no_hp']) ? ($cleanRow[$headerMap['no_hp']] ?? null) : null;
                    $status = isset($headerMap['status']) ? ($cleanRow[$headerMap['status']] ?? null) : null;
                    $jenisKepegawaian = isset($headerMap['jenis_kepegawaian']) ? ($cleanRow[$headerMap['jenis_kepegawaian']] ?? null) : null;
                } else {
                    // Smart Heuristic Parser
                    $textCols = [];
                    foreach ($cleanRow as $idx => $cell) {
                        if (empty($cell)) continue;

                        $digitsOnly = preg_replace('/[^0-9]/', '', $cell);
                        $lower = strtolower($cell);

                        // 1. Cek Status Keaktifan
                        if (in_array($lower, ['aktif', 'nonaktif'])) {
                            $status = $lower;
                            continue;
                        }

                        // 2. Cek Jenis Kepegawaian
                        if (in_array($lower, ['pns', 'pppk', 'p3k', 'honor', 'gtt', 'tendik', 'tu', 'staf'])) {
                            $jenisKepegawaian = ($lower === 'p3k') ? 'pppk' : (($lower === 'gtt') ? 'honor' : (($lower === 'tu' || $lower === 'staf') ? 'tendik' : $lower));
                            continue;
                        }

                        // 3. Cek NIP (15 - 22 digit)
                        if (strlen($digitsOnly) >= 15 && strlen($digitsOnly) <= 22) {
                            $nip = $digitsOnly;
                            continue;
                        }

                        // 4. Cek No HP / WA (8 - 14 digit)
                        if ((str_starts_with($digitsOnly, '08') || str_starts_with($digitsOnly, '628') || str_starts_with($digitsOnly, '8')) && strlen($digitsOnly) >= 9 && strlen($digitsOnly) <= 14) {
                            $noHp = str_starts_with($digitsOnly, '8') ? ('0' . $digitsOnly) : $digitsOnly;
                            continue;
                        }

                        // 5. Teks Kolom (Nama / Jabatan)
                        $textCols[] = $cell;
                    }

                    if (count($textCols) >= 2) {
                        if (is_numeric($textCols[0]) && count($textCols) >= 3) {
                            array_shift($textCols);
                        }
                        $nama = $textCols[0] ?? null;
                        $jabatan = $textCols[1] ?? 'Guru Mata Pelajaran';
                    } elseif (count($textCols) === 1) {
                        $nama = $textCols[0];
                        $jabatan = 'Guru Mata Pelajaran';
                    }
                }

                if (empty($nama)) continue;

                // Jika kolom jabatan ternyata adalah gelar akademik (misal: S.Kom., S.Pd., M.Pd.), gabungkan ke Nama
                $gelarPattern = '/^(s\.pd|s\.kom|s\.t|s\.tp|m\.pd|m\.kom|s\.e|s\.ag|s\.sos|s\.si|m\.m|m\.si|drs|dra|ir|gr)\.?$/i';
                if ($jabatan && preg_match($gelarPattern, str_replace(' ', '', strtolower($jabatan)))) {
                    if (!str_contains(strtolower($nama), strtolower($jabatan))) {
                        $nama = rtrim($nama, ',. ') . ', ' . $jabatan;
                    }
                    $jabatan = 'Guru Mata Pelajaran';
                }

                // Sanitasi Status Keaktifan
                $status = in_array(strtolower($status ?? ''), ['aktif', 'nonaktif']) ? strtolower($status) : 'aktif';

                // Deteksi otomatis status kepegawaian jika NIP 18 digit
                if ($nip && strlen($nip) === 18) {
                    if (str_contains(substr($nip, 8, 6), '202') || str_contains(substr($nip, 8, 6), '2023') || str_contains(substr($nip, 8, 6), '2024')) {
                        $jenisKepegawaian = 'pppk';
                    } else {
                        $jenisKepegawaian = 'pns';
                    }
                }

                // Sanitasi Jenis Kepegawaian
                $jenisKepegawaian = in_array(strtolower($jenisKepegawaian ?? ''), ['pns', 'pppk', 'honor', 'tendik']) ? strtolower($jenisKepegawaian) : 'pns';

                if (empty($jabatan)) {
                    $jabatan = 'Guru Mata Pelajaran';
                }

                // Format No HP
                if ($noHp && str_starts_with($noHp, '8')) {
                    $noHp = '0' . $noHp;
                }

                // Cari guru berdasarkan NIP terlebih dahulu untuk menghindari UNIQUE constraint violation
                $existingGuru = null;
                if (!empty($nip)) {
                    $existingGuru = Guru::where('nip', $nip)->first();
                }
                if (!$existingGuru && !empty($nama)) {
                    $existingGuru = Guru::where('nama', $nama)->first();
                }

                if ($existingGuru) {
                    $existingGuru->update([
                        'nama'              => $nama,
                        'nip'               => $nip ?: $existingGuru->nip,
                        'jabatan'           => $jabatan,
                        'status'            => $status,
                        'no_hp'             => $noHp ?: $existingGuru->no_hp,
                        'jenis_kepegawaian' => $jenisKepegawaian,
                    ]);
                } else {
                    Guru::create([
                        'nama'              => $nama,
                        'nip'               => $nip ?: null,
                        'jabatan'           => $jabatan,
                        'status'            => $status,
                        'no_hp'             => $noHp ?: null,
                        'jenis_kepegawaian' => $jenisKepegawaian,
                    ]);
                }

                $imported++;
            }
        });

        return redirect()->back()->with('success', "Berhasil memproses dan mengimpor {$imported} data guru/pegawai.");
    }

    /**
     * Buat atau perbarui akun login (Email & Password) untuk Guru / Kepala Sekolah.
     */
    public function storeAkun(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $userId = $guru->user ? $guru->user->id : null;
        $request->validate([
            'email'    => 'required|email|unique:users,email,' . ($userId ?? 'NULL') . ',id',
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'role'     => 'nullable|in:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,guru_bk,wali_kelas,guru_piket,staf_tu,guru',
        ]);

        $defaultRole = ($guru->jabatan === 'Kepala Sekolah') ? 'kepala_sekolah' : 'guru';
        $role = $request->input('role') ?: $defaultRole;

        if ($guru->user) {
            $updateData = [
                'name'  => $guru->nama,
                'email' => $request->input('email'),
                'role'  => $role,
            ];
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->input('password'));
            }
            $guru->user->update($updateData);

            return redirect()->back()->with('success', "Akun login untuk {$guru->nama} berhasil diperbarui.");
        }

        User::create([
            'name'     => $guru->nama,
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'guru_id'  => $guru->id,
            'role'     => $role,
        ]);

        return redirect()->back()->with('success', "Akun login baru untuk {$guru->nama} berhasil dibuat.");
    }

    /**
     * Hapus akun login guru.
     */
    public function destroyAkun($id)
    {
        $guru = Guru::findOrFail($id);
        if ($guru->user) {
            $guru->user->delete();
        }

        return redirect()->back()->with('success', "Akun login untuk {$guru->nama} berhasil dihapus.");
    }
}
