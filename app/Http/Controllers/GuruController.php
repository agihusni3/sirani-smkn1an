<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use App\Models\SertifikatGuru;
use App\Models\PengaturanSekolah;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with(['user', 'rombels.tahunAjaran', 'kartuRfid', 'sertifikats']);

        $search = $request->input('q');
        $kategori = $request->input('kategori'); // wali_kelas, bk, pimpinan, staf, guru
        $kepegawaian = $request->input('kepegawaian') ?: $request->input('jenis'); // pns, pppk, honor, tendik
        $status = $request->input('status'); // aktif, nonaktif
        $rfidFilter = $request->input('rfid'); // Filter RFID: ada, belum
        $sertifikasi = $request->input('sertifikasi'); // sudah, belum
        $ptk = $request->input('ptk'); // filter jenis ptk

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('mapel_diampu', 'like', "%{$search}%")
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

        if ($sertifikasi) {
            $query->where('status_sertifikasi', $sertifikasi);
        }

        if ($ptk) {
            $query->where('jenis_ptk', $ptk);
        }

        if ($rfidFilter === 'ada') {
            $query->whereHas('kartuRfid');
        } elseif ($rfidFilter === 'belum') {
            $query->whereDoesntHave('kartuRfid');
        }

        if ($kategori === 'wali_kelas') {
            $query->whereHas('rombels');
        } elseif ($kategori === 'bk') {
            $query->where(function($q) {
                $q->where('jabatan', 'like', '%BK%')->orWhere('jabatan', 'like', '%Bimbingan%')->orWhere('jenis_ptk', 'Guru BK');
            });
        } elseif ($kategori === 'pimpinan') {
            $query->where(function($q) {
                $q->where('jabatan', 'like', '%Kepala%')
                  ->orWhere('jabatan', 'like', '%Wakil%')
                  ->orWhere('jabatan', 'like', '%Waka%')
                  ->orWhere('jabatan', 'like', '%Kaprogli%')
                  ->orWhere('tugas_tambahan', 'like', '%Kepala%')
                  ->orWhere('tugas_tambahan', 'like', '%Waka%');
            });
        } elseif ($kategori === 'staf') {
            $query->where(function($q) {
                $q->where('jabatan', 'like', '%Tata Usaha%')
                  ->orWhere('jabatan', 'like', '%Operator%')
                  ->orWhere('jabatan', 'like', '%Staf%')
                  ->orWhere('jabatan', 'like', '%Pustakawan%')
                  ->orWhere('jenis_kepegawaian', 'tendik');
            });
        }

        // Sorting (Default: Hirarki Jabatan Organisasi)
        $sort = $request->input('sort', 'hirarki');
        switch ($sort) {
            case 'nama_asc':
                $query->orderBy('nama', 'asc');
                break;
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
            case 'hirarki':
            default:
                $query->orderByRaw("
                    CASE 
                        WHEN jabatan LIKE '%Kepala Sekolah%' OR tugas_tambahan LIKE '%Kepala Sekolah%' THEN 1
                        WHEN jabatan LIKE '%Waka%' OR jabatan LIKE '%Wakil%' OR tugas_tambahan LIKE '%Waka%' THEN 2
                        WHEN jabatan LIKE '%Kaprog%' OR jabatan LIKE '%Ketua%' OR tugas_tambahan LIKE '%Kaprog%' THEN 3
                        WHEN jabatan LIKE '%BK%' OR jabatan LIKE '%Bimbingan%' OR jenis_ptk = 'Guru BK' THEN 4
                        WHEN jabatan LIKE '%Wali Kelas%' OR tugas_tambahan LIKE '%Wali Kelas%' THEN 5
                        WHEN jabatan LIKE '%Guru%' THEN 6
                        WHEN jabatan LIKE '%Tata Usaha%' OR jabatan LIKE '%TU%' OR jabatan LIKE '%Staf%' OR jabatan LIKE '%Operator%' OR jabatan LIKE '%Tendik%' THEN 7
                        ELSE 8
                    END ASC, nama ASC
                ");
                break;
        }

        $gurus = $query->paginate(20)->withQueryString();

        // Statistik Cepat Terpadu
        $statTotal = Guru::count();
        $statWali = Guru::whereHas('rombels')->count();
        $statAkun = Guru::whereHas('user')->count();
        $statSertifikasi = Guru::where('status_sertifikasi', 'sudah')->count();
        $statTotalSertifikat = SertifikatGuru::count();
        $rfidStatus = $rfidFilter;

        return view('guru.index', compact(
            'gurus',
            'statTotal',
            'statWali',
            'statAkun',
            'statSertifikasi',
            'statTotalSertifikat',
            'search',
            'kategori',
            'kepegawaian',
            'status',
            'sertifikasi',
            'ptk',
            'rfidStatus',
            'sort'
        ));
    }

    /**
     * Resolusi cerdas jabatan agar guru tidak perlu menginput hal yang sama berulang kali.
     */
    protected function resolveJabatan(Request $request, ?string $existingJabatan = null): string
    {
        $inputJabatan = trim($request->input('jabatan', ''));
        $tugasTambahan = trim($request->input('tugas_tambahan', ''));
        $mapelDiampu = trim($request->input('mapel_diampu', ''));
        $jenisPtk = trim($request->input('jenis_ptk', ''));

        // 1. Kepala Sekolah selalu jabatan utama
        if ($tugasTambahan) {
            $lowerTugas = strtolower($tugasTambahan);
            if (str_contains($lowerTugas, 'kepala sekolah') || str_contains($lowerTugas, 'kepsek')) {
                return 'Kepala Sekolah';
            }
        }
        if ($jenisPtk === 'Kepala Sekolah') {
            return 'Kepala Sekolah';
        }

        // 2. Jika guru BK
        if ($jenisPtk === 'Guru BK' || str_contains(strtolower($inputJabatan), 'bk') || str_contains(strtolower($inputJabatan), 'konseling')) {
            return 'Guru Bimbingan Konseling';
        }

        // 3. Jika staf administrasi / TU / laboran / perpustakaan
        if ($jenisPtk && (str_contains($jenisPtk, 'Administrasi') || str_contains($jenisPtk, 'TU'))) {
            return 'Tenaga Administrasi Sekolah (TU)';
        }
        if ($jenisPtk && str_contains($jenisPtk, 'Laboran')) {
            return 'Laboran / Toolman Bengkel';
        }
        if ($jenisPtk && str_contains($jenisPtk, 'Perpustakaan')) {
            return 'Tenaga Perpustakaan';
        }

        // 4. Jika ada mata pelajaran yang diampu, jadikan tugas utama Guru [Mapel]
        if ($mapelDiampu) {
            $firstMapel = trim(explode(',', $mapelDiampu)[0]);
            return str_starts_with(strtolower($firstMapel), 'guru') ? $firstMapel : 'Guru ' . $firstMapel;
        }

        // 5. Jika tidak ada mapel tapi ada tugas tambahan (misal: Waka tanpa mapel, Kepala Bengkel)
        if ($tugasTambahan) {
            return trim(explode(',', $tugasTambahan)[0]);
        }

        // 7. Jika jenis_ptk tersedia
        if ($jenisPtk) {
            return $jenisPtk;
        }

        // 8. Fallback ke input jabatan manual / eksisting
        return $inputJabatan ?: ($existingJabatan ?: 'Guru Mata Pelajaran');
    }

    /**
     * Sinkronisasi data GTK (Guru) ketika Role Akun diubah atau dibuat.
     */
    protected function syncRoleToGuruData(Guru $guru, string $newRole, ?string $oldRole = null): void
    {
        // 1. KEPALA SEKOLAH
        if ($newRole === 'kepala_sekolah') {
            $guru->jabatan = 'Kepala Sekolah';
            $guru->tugas_tambahan = 'Kepala Sekolah';
            $guru->jenis_ptk = 'Kepala Sekolah';
            $guru->save();

            // Sinkronkan data Kepala Sekolah ke Pengaturan Sekolah (Kop Surat & Profil Dinas)
            try {
                $sekolah = PengaturanSekolah::getAktif();
                $sekolah->update([
                    'nama_kepala_sekolah' => $guru->nama_lengkap_gelar ?: $guru->nama,
                    'nip_kepala_sekolah'  => $guru->nip ?: null,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Gagal sinkron PengaturanSekolah saat role kepsek: ' . $e->getMessage());
            }

            // Turunkan akun Kepala Sekolah lain jika ada (karena Kepsek aktif hanya 1 orang)
            $otherKepseks = User::where('role', 'kepala_sekolah')
                ->where('guru_id', '!=', $guru->id)
                ->get();
            foreach ($otherKepseks as $oldKepsekUser) {
                $oldKepsekUser->update(['role' => 'guru']);
                if ($oldKepsekUser->guru && $oldKepsekUser->guru->jabatan === 'Kepala Sekolah') {
                    $demotedGuru = $oldKepsekUser->guru;
                    $demotedGuru->tugas_tambahan = null;
                    if ($demotedGuru->mapel_diampu) {
                        $firstMpl = trim(explode(',', $demotedGuru->mapel_diampu)[0]);
                        $demotedGuru->jabatan = str_starts_with(strtolower($firstMpl), 'guru') ? $firstMpl : 'Guru ' . $firstMpl;
                        $demotedGuru->jenis_ptk = 'Guru Normatif / Adaptif';
                    } else {
                        $demotedGuru->jabatan = 'Guru Mata Pelajaran';
                        $demotedGuru->jenis_ptk = 'Guru Normatif / Adaptif';
                    }
                    $demotedGuru->save();
                }
            }
        } elseif ($oldRole === 'kepala_sekolah' && $newRole !== 'kepala_sekolah') {
            // Jika sebelumnya Kepsek lalu diganti role lain, reset status jabatan Kepsek-nya
            if ($guru->jabatan === 'Kepala Sekolah') {
                $guru->tugas_tambahan = null;
                if ($guru->mapel_diampu) {
                    $firstMpl = trim(explode(',', $guru->mapel_diampu)[0]);
                    $guru->jabatan = str_starts_with(strtolower($firstMpl), 'guru') ? $firstMpl : 'Guru ' . $firstMpl;
                    $guru->jenis_ptk = 'Guru Normatif / Adaptif';
                } else {
                    $guru->jabatan = 'Guru Mata Pelajaran';
                    $guru->jenis_ptk = 'Guru Normatif / Adaptif';
                }
                $guru->save();
            }
        }

        // 2. WAKA KESISWAAN
        if ($newRole === 'waka_kesiswaan') {
            $tgs = array_filter(array_map('trim', explode(',', $guru->tugas_tambahan ?? '')));
            if (!in_array('Waka Kesiswaan', $tgs) && !in_array('Wakil Kepala Sekolah Bidang Kesiswaan', $tgs)) {
                $tgs[] = 'Waka Kesiswaan';
                $guru->tugas_tambahan = implode(', ', $tgs);
                $guru->save();
            }
        }

        // 3. WAKA KURIKULUM
        if ($newRole === 'waka_kurikulum') {
            $tgs = array_filter(array_map('trim', explode(',', $guru->tugas_tambahan ?? '')));
            if (!in_array('Waka Kurikulum', $tgs) && !in_array('Wakil Kepala Sekolah Bidang Kurikulum', $tgs)) {
                $tgs[] = 'Waka Kurikulum';
                $guru->tugas_tambahan = implode(', ', $tgs);
                $guru->save();
            }
        }

        // 4. WAKA SARPRAS (Sarana & Prasarana)
        if ($newRole === 'waka_sarpras') {
            $tgs = array_filter(array_map('trim', explode(',', $guru->tugas_tambahan ?? '')));
            if (!in_array('Waka Sarpras', $tgs) && !in_array('Wakil Kepala Sekolah Bidang Sarana Prasarana', $tgs)) {
                $tgs[] = 'Waka Sarpras';
                $guru->tugas_tambahan = implode(', ', $tgs);
                $guru->save();
            }
        }

        // 5. WAKA HUBIN (Hubungan Industri / DUDI)
        if ($newRole === 'waka_hubin') {
            $tgs = array_filter(array_map('trim', explode(',', $guru->tugas_tambahan ?? '')));
            if (!in_array('Waka Hubin', $tgs) && !in_array('Wakil Kepala Sekolah Bidang Hubungan Industri', $tgs)) {
                $tgs[] = 'Waka Hubin';
                $guru->tugas_tambahan = implode(', ', $tgs);
                $guru->save();
            }
        }

        // 6. KEPALA PROGRAM KEAHLIAN (Kaprog / Jurusan)
        if ($newRole === 'kaprog') {
            $tgs = array_filter(array_map('trim', explode(',', $guru->tugas_tambahan ?? '')));
            $hasKaprog = false;
            foreach ($tgs as $t) {
                if (str_contains(strtolower($t), 'kaprog') || str_contains(strtolower($t), 'kepala program') || str_contains(strtolower($t), 'ketua jurusan')) {
                    $hasKaprog = true;
                    break;
                }
            }
            if (!$hasKaprog) {
                $tgs[] = 'Kepala Program Keahlian';
                $guru->tugas_tambahan = implode(', ', $tgs);
                $guru->save();
            }
        }

        // 7. KEPALA BENGKEL / LABORAN / TOOLMAN
        if ($newRole === 'kepala_bengkel') {
            $tgs = array_filter(array_map('trim', explode(',', $guru->tugas_tambahan ?? '')));
            $hasKabeng = false;
            foreach ($tgs as $t) {
                if (str_contains(strtolower($t), 'kepala bengkel') || str_contains(strtolower($t), 'kabeng') || str_contains(strtolower($t), 'laboran') || str_contains(strtolower($t), 'toolman')) {
                    $hasKabeng = true;
                    break;
                }
            }
            if (!$hasKabeng) {
                $tgs[] = 'Kepala Bengkel';
                $guru->tugas_tambahan = implode(', ', $tgs);
                $guru->save();
            }
        }

        // 8. PUSTAKAWAN
        if ($newRole === 'pustakawan') {
            $guru->jenis_ptk = 'Tenaga Perpustakaan';
            if (empty($guru->mapel_diampu)) {
                $guru->jabatan = 'Tenaga Perpustakaan';
            }
            $guru->save();
        }

        // 9. GURU BK
        if ($newRole === 'guru_bk') {
            $guru->jenis_ptk = 'Guru BK';
            if (empty($guru->mapel_diampu)) {
                $guru->jabatan = 'Guru Bimbingan Konseling';
            }
            $guru->save();
        }

        // 10. STAF TU
        if ($newRole === 'staf_tu') {
            $guru->jenis_ptk = 'Tenaga Administrasi Sekolah (TU)';
            $guru->jabatan = 'Tenaga Administrasi Sekolah (TU)';
            $guru->save();
        }
    }

    /**
     * Sinkronisasi User Role & Pengaturan Sekolah ketika Data GTK (Guru) disimpan/diupdate.
     */
    protected function syncGuruDataToUserRole(Guru $guru): void
    {
        $isKepsek = ($guru->jabatan === 'Kepala Sekolah')
            || (str_contains(strtolower($guru->tugas_tambahan ?? ''), 'kepala sekolah'))
            || ($guru->jenis_ptk === 'Kepala Sekolah');

        if ($isKepsek) {
            // Pastikan jika ada akun login, role-nya otomatis kepala_sekolah
            if ($guru->user) {
                if ($guru->user->role !== 'kepala_sekolah') {
                    $guru->user->update(['role' => 'kepala_sekolah']);
                }
            }

            // Sinkronkan ke profil dinas PengaturanSekolah
            try {
                $sekolah = PengaturanSekolah::getAktif();
                $sekolah->update([
                    'nama_kepala_sekolah' => $guru->nama_lengkap_gelar ?: $guru->nama,
                    'nip_kepala_sekolah'  => $guru->nip ?: null,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Gagal sinkron PengaturanSekolah: ' . $e->getMessage());
            }

            // Turunkan akun kepala sekolah lain jika ada
            User::where('role', 'kepala_sekolah')
                ->where('guru_id', '!=', $guru->id)
                ->update(['role' => 'guru']);
        } else {
            // Jika bukan kepsek tapi akunnya masih tercatat kepala_sekolah
            if ($guru->user && $guru->user->role === 'kepala_sekolah') {
                $guru->user->update(['role' => 'guru']);
            }
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'nullable|string|max:25|unique:gurus,nip',
            'nik' => 'nullable|string|max:20',
            'nuptk' => 'nullable|string|max:25',
            'jabatan' => 'nullable|string|max:100',
            'jenis_kepegawaian' => 'nullable|in:pns,pppk,honor,tendik',
            'jenis_ptk' => 'nullable|string|max:60',
            'status_sertifikasi' => 'nullable|in:sudah,belum',
            'jjm' => 'nullable|integer|min:0|max:60',
            'hari_mengajar' => 'nullable|array',
            'no_hp' => 'nullable|string|max:25',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'email_akun' => 'nullable|email|unique:users,email',
            'password_akun' => 'nullable|string|min:4',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_guru', 'public');
        }

        $nama = trim($request->input('nama'));
        $namaLengkap = $request->filled('nama_lengkap') ? trim($request->input('nama_lengkap')) : $nama;
        $jabatan = $this->resolveJabatan($request);

        $guru = Guru::create([
            'nip'                 => $request->input('nip') ?: null,
            'nama'                => $nama,
            'nama_lengkap'        => $namaLengkap,
            'gelar_depan'         => $request->input('gelar_depan') ?: null,
            'gelar_belakang'      => $request->input('gelar_belakang') ?: null,
            'nik'                 => $request->input('nik') ?: null,
            'nuptk'               => $request->input('nuptk') ?: null,
            'tempat_lahir'        => $request->input('tempat_lahir') ?: null,
            'tanggal_lahir'       => $request->input('tanggal_lahir') ?: null,
            'jenis_kelamin'       => $request->input('jenis_kelamin') ?: null,
            'agama'               => $request->input('agama') ?: null,
            'alamat'              => $request->input('alamat') ?: null,
            'id_gtk'              => $request->input('id_gtk') ?: null,
            'jabatan'             => $jabatan,
            'jenis_kepegawaian'   => $request->input('jenis_kepegawaian', 'pns'),
            'jenis_ptk'           => $request->input('jenis_ptk') ?: null,
            'golongan_pangkat'    => $request->input('golongan_pangkat') ?: null,
            'nomor_sk_pengangkatan' => $request->input('nomor_sk_pengangkatan') ?: null,
            'tmt_kerja'           => $request->input('tmt_kerja') ?: null,
            'lembaga_pengangkat'  => $request->input('lembaga_pengangkat') ?: null,
            'pendidikan_terakhir' => $request->input('pendidikan_terakhir') ?: null,
            'jurusan_kuliah'      => $request->input('jurusan_kuliah') ?: null,
            'kampus'              => $request->input('kampus') ?: null,
            'tahun_lulus'         => $request->input('tahun_lulus') ?: null,
            'status_sertifikasi'  => $request->input('status_sertifikasi', 'belum'),
            'nomor_serdik'        => $request->input('nomor_serdik') ?: null,
            'mapel_diampu'        => $request->input('mapel_diampu') ?: null,
            'jjm'                 => $request->input('jjm') ?: null,
            'tugas_tambahan'      => $request->input('tugas_tambahan') ?: null,
            'sk_tugas_tambahan'   => $request->input('sk_tugas_tambahan') ?: null,
            'hari_mengajar'       => $request->input('hari_mengajar') ?: null,
            'no_hp'               => $request->input('no_hp') ?: null,
            'foto'                => $fotoPath,
            'status'              => 'aktif',
        ]);

        if ($request->filled('email_akun') && $request->filled('password_akun')) {
            $contextLower = strtolower($jabatan . ' ' . $request->input('tugas_tambahan', '') . ' ' . $request->input('jenis_ptk', ''));
            $defaultRole = 'guru';
            if (str_contains($contextLower, 'kepala sekolah') && !str_contains($contextLower, 'wakil') && !str_contains($contextLower, 'waka')) {
                $defaultRole = 'kepala_sekolah';
            } elseif (str_contains($contextLower, 'waka sarpras') || str_contains($contextLower, 'sarana prasarana') || str_contains($contextLower, 'sarpras')) {
                $defaultRole = 'waka_sarpras';
            } elseif (str_contains($contextLower, 'waka hubin') || str_contains($contextLower, 'hubungan industri') || str_contains($contextLower, 'hubin')) {
                $defaultRole = 'waka_hubin';
            } elseif (str_contains($contextLower, 'waka kesiswaan') || str_contains($contextLower, 'kesiswaan')) {
                $defaultRole = 'waka_kesiswaan';
            } elseif (str_contains($contextLower, 'waka kurikulum') || str_contains($contextLower, 'kurikulum')) {
                $defaultRole = 'waka_kurikulum';
            } elseif (str_contains($contextLower, 'kaprog') || str_contains($contextLower, 'kepala program') || str_contains($contextLower, 'ketua program') || str_contains($contextLower, 'ketua jurusan')) {
                $defaultRole = 'kaprog';
            } elseif (str_contains($contextLower, 'kepala bengkel') || str_contains($contextLower, 'kabeng') || str_contains($contextLower, 'toolman') || str_contains($contextLower, 'laboran')) {
                $defaultRole = 'kepala_bengkel';
            } elseif (str_contains($contextLower, 'perpustakaan') || str_contains($contextLower, 'pustakawan')) {
                $defaultRole = 'pustakawan';
            } elseif (str_contains($contextLower, 'bimbingan konseling') || str_contains($contextLower, 'bk')) {
                $defaultRole = 'guru_bk';
            } elseif (str_contains($contextLower, 'tata usaha') || str_contains($contextLower, 'tu') || str_contains($contextLower, 'tendik') || str_contains($contextLower, 'administrasi')) {
                $defaultRole = 'staf_tu';
            }

            $role = $request->input('role_akun') ?: ($request->input('role') ?: $defaultRole);

            $user = User::create([
                'name' => $guru->nama,
                'email' => $request->input('email_akun'),
                'password' => Hash::make($request->input('password_akun')),
                'guru_id' => $guru->id,
                'role' => $role,
            ]);

            $this->syncRoleToGuruData($guru, $role, null);
        } else {
            $this->syncGuruDataToUserRole($guru);
        }

        return redirect()->back()->with('success', 'Data guru/pegawai GTK dan pengaturan akses berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'nullable|string|max:25|unique:gurus,nip,' . $id,
            'nik' => 'nullable|string|max:20',
            'nuptk' => 'nullable|string|max:25',
            'jabatan' => 'nullable|string|max:100',
            'jenis_kepegawaian' => 'nullable|in:pns,pppk,honor,tendik',
            'jenis_ptk' => 'nullable|string|max:60',
            'status_sertifikasi' => 'nullable|in:sudah,belum',
            'jjm' => 'nullable|integer|min:0|max:60',
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

        $nama = trim($request->input('nama'));
        $namaLengkap = $request->filled('nama_lengkap') ? trim($request->input('nama_lengkap')) : ($guru->nama_lengkap ?: $nama);
        $jabatan = $this->resolveJabatan($request, $guru->jabatan);

        $guru->update([
            'nip'                 => $request->input('nip') ?: null,
            'nama'                => $nama,
            'nama_lengkap'        => $namaLengkap,
            'gelar_depan'         => $request->input('gelar_depan') ?: null,
            'gelar_belakang'      => $request->input('gelar_belakang') ?: null,
            'nik'                 => $request->input('nik') ?: null,
            'nuptk'               => $request->input('nuptk') ?: null,
            'tempat_lahir'        => $request->input('tempat_lahir') ?: null,
            'tanggal_lahir'       => $request->input('tanggal_lahir') ?: null,
            'jenis_kelamin'       => $request->input('jenis_kelamin') ?: null,
            'agama'               => $request->input('agama') ?: null,
            'alamat'              => $request->input('alamat') ?: null,
            'id_gtk'              => $request->input('id_gtk') ?: null,
            'jabatan'             => $jabatan,
            'jenis_kepegawaian'   => $request->input('jenis_kepegawaian', 'pns'),
            'jenis_ptk'           => $request->input('jenis_ptk') ?: null,
            'golongan_pangkat'    => $request->input('golongan_pangkat') ?: null,
            'nomor_sk_pengangkatan' => $request->input('nomor_sk_pengangkatan') ?: null,
            'tmt_kerja'           => $request->input('tmt_kerja') ?: null,
            'lembaga_pengangkat'  => $request->input('lembaga_pengangkat') ?: null,
            'pendidikan_terakhir' => $request->input('pendidikan_terakhir') ?: null,
            'jurusan_kuliah'      => $request->input('jurusan_kuliah') ?: null,
            'kampus'              => $request->input('kampus') ?: null,
            'tahun_lulus'         => $request->input('tahun_lulus') ?: null,
            'status_sertifikasi'  => $request->input('status_sertifikasi', 'belum'),
            'nomor_serdik'        => $request->input('nomor_serdik') ?: null,
            'mapel_diampu'        => $request->input('mapel_diampu') ?: null,
            'jjm'                 => $request->input('jjm') ?: null,
            'tugas_tambahan'      => $request->input('tugas_tambahan') ?: null,
            'sk_tugas_tambahan'   => $request->input('sk_tugas_tambahan') ?: null,
            'hari_mengajar'       => $request->input('hari_mengajar') ?: null,
            'no_hp'               => $request->input('no_hp') ?: null,
            'foto'                => $fotoPath,
            'status'              => $request->input('status'),
        ]);

        $this->syncGuruDataToUserRole($guru);

        return redirect()->back()->with('success', 'Data guru/pegawai GTK berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $nama = $guru->nama;

        // Bersihkan akun login jika ada agar tidak menjadi orphaned user
        if ($guru->user) {
            AuditLog::catat('delete', 'auth', "Akun login {$guru->user->username} ikut dihapus karena data GTK {$nama} dihapus oleh " . (auth()->user()->name ?? 'Admin'));
            $guru->user->delete();
        }

        if ($guru->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($guru->foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($guru->foto);
        }
        $guru->delete();

        return redirect()->back()->with('success', "Data guru {$nama} dan akun terkait berhasil dihapus.");
    }

    public function export()
    {
        $gurus = Guru::withCount('sertifikats')->orderBy('nama')->get();
        $csvFileName = 'data_gtk_guru_smkn1an_' . date('Y-m-d') . '.csv';

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

            fputcsv($file, [
                'No',
                'Nama Lengkap Beserta Gelar',
                'Nama Lengkap (Tanpa Gelar)',
                'Gelar Depan',
                'Gelar Belakang',
                'NIK',
                'NUPTK',
                'NIP / NI PPPK',
                'Jenis PTK',
                'Status Kepegawaian',
                'Golongan / Pangkat',
                'Pendidikan Terakhir',
                'Program Studi / Jurusan',
                'Perguruan Tinggi / Kampus',
                'Status Sertifikasi',
                'Nomor Serdik',
                'Mata Pelajaran Diampu',
                'JJM per Minggu',
                'Tugas Tambahan',
                'Nomor Handphone / WhatsApp',
                'Jumlah Sertifikat Pelatihan',
                'Status Keaktifan'
            ], ';');

            foreach ($gurus as $idx => $g) {
                fputcsv($file, [
                    $idx + 1,
                    $g->nama_lengkap_gelar,
                    $g->nama_lengkap ?: $g->nama,
                    $g->gelar_depan ?: '-',
                    $g->gelar_belakang ?: '-',
                    $g->nik ? '="' . $g->nik . '"' : '-',
                    $g->nuptk ? '="' . $g->nuptk . '"' : '-',
                    $g->nip ? '="' . $g->nip . '"' : '-',
                    $g->jenis_ptk ?: $g->jabatan,
                    strtoupper($g->jenis_kepegawaian),
                    $g->golongan_pangkat ?: '-',
                    $g->pendidikan_terakhir ?: '-',
                    $g->jurusan_kuliah ?: '-',
                    $g->kampus ?: '-',
                    $g->status_sertifikasi === 'sudah' ? 'Sudah Sertifikasi' : 'Belum Sertifikasi',
                    $g->nomor_serdik ?: '-',
                    $g->mapel_diampu ?: '-',
                    $g->jjm ? ($g->jjm . ' Jam') : '-',
                    $g->tugas_tambahan ?: '-',
                    $g->no_hp ? '="' . $g->no_hp . '"' : '-',
                    $g->sertifikats_count ?? 0,
                    strtoupper($g->status),
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
        $gurus = Guru::with(['user', 'sertifikats'])->orderBy('nama')->get();
        $sekolah = PengaturanSekolah::getAktif();

        return view('guru.cetak_pdf', compact('gurus', 'sekolah'));
    }

    /**
     * Cetak Lembar Biodata Resmi Pendidik & Tenaga Kependidikan (GTK) Format A4.
     * Standar dokumen profil individu untuk verifikasi BKN / Cabang Dinas / Dapodik.
     */
    public function cetakBiodata($id)
    {
        $guru = Guru::with(['user', 'sertifikats', 'kartuRfid'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        return view('guru.biodata_pdf', compact('guru', 'sekolah'));
    }

    /**
     * Unduh Template CSV Format Guru Resmi.
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_import_gtk_smkn1an.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fwrite($file, "sep=;\n");
            fputcsv($file, [
                'Nama Lengkap',
                'Gelar Depan',
                'Gelar Belakang',
                'NIP',
                'NUPTK',
                'NIK',
                'Jenis PTK',
                'Status Kepegawaian (PNS/PPPK/Honor/Tendik)',
                'Golongan Pangkat',
                'Pendidikan Terakhir',
                'Jurusan Kuliah',
                'Perguruan Tinggi',
                'Status Sertifikasi (Sudah/Belum)',
                'Mata Pelajaran Diampu',
                'JJM per Minggu',
                'Tugas Tambahan',
                'No WhatsApp / HP',
                'Status Keaktifan (Aktif/Nonaktif)'
            ], ';');

            fputcsv($file, ['Budi Santoso', 'Drs.', 'M.Kom.', '198501012010011005', '1234567890123456', '1806010101850001', 'Guru Kejuruan', 'PNS', 'Penata Tk.I (III/d)', 'S2', 'Magister Komputer', 'Universitas Lampung', 'Sudah', 'Pemrograman Web & RPL', '24', 'Kepala Bengkel RPL', '081234567890', 'Aktif'], ';');
            fputcsv($file, ['Siska Widyawati', '', 'S.Pd.', '198902052023212024', '9876543210987654', '1806020589000002', 'Guru Normatif / Adaptif', 'PPPK', 'Golongan IX', 'S1', 'Pendidikan Matematika', 'Universitas Negeri Yogyakarta', 'Sudah', 'Matematika', '24', 'Wali Kelas X RPL 1', '081373310855', 'Aktif'], ';');
            fputcsv($file, ['Rian Kurniawan', '', 'S.T.', '', '', '1806031295000003', 'Laboran / Toolman', 'Honor', '-', 'S1', 'Teknik Otomotif', 'Politeknik Negeri Lampung', 'Belum', 'Praktikum TSM', '18', 'Toolman Bengkel TSM', '081272001006', 'Aktif'], ';');
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
            } elseif (in_array($cleanName, ['gelardepan', 'gelarawal'])) {
                $headerMap['gelar_depan'] = $colIdx;
            } elseif (in_array($cleanName, ['gelarbelakang', 'gelarakhir'])) {
                $headerMap['gelar_belakang'] = $colIdx;
            } elseif (in_array($cleanName, ['nip', 'nipguru', 'noinduk', 'nippegawai'])) {
                $headerMap['nip'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['nuptk', 'nonuptk'])) {
                $headerMap['nuptk'] = $colIdx;
            } elseif (in_array($cleanName, ['nik', 'noktp', 'nonik'])) {
                $headerMap['nik'] = $colIdx;
            } elseif (in_array($cleanName, ['jabatan', 'tugas', 'penugasan'])) {
                $headerMap['jabatan'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['jenisptk', 'ptk', 'peran'])) {
                $headerMap['jenis_ptk'] = $colIdx;
            } elseif (in_array($cleanName, ['mapel', 'gurumapel', 'mapeldiampu', 'matapelajaran'])) {
                $headerMap['mapel_diampu'] = $colIdx;
            } elseif (in_array($cleanName, ['jjm', 'jam', 'jamngajar', 'jammengajar'])) {
                $headerMap['jjm'] = $colIdx;
            } elseif (in_array($cleanName, ['tugastambahan', 'tugasextra'])) {
                $headerMap['tugas_tambahan'] = $colIdx;
            } elseif (in_array($cleanName, ['pendidikan', 'pendidikanterakhir', 'ijazah'])) {
                $headerMap['pendidikan_terakhir'] = $colIdx;
            } elseif (in_array($cleanName, ['jurusan', 'prodi', 'jurusankuliah', 'programstudi'])) {
                $headerMap['jurusan_kuliah'] = $colIdx;
            } elseif (in_array($cleanName, ['kampus', 'universitas', 'perguruantinggi'])) {
                $headerMap['kampus'] = $colIdx;
            } elseif (in_array($cleanName, ['sertifikasi', 'statussertifikasi'])) {
                $headerMap['status_sertifikasi'] = $colIdx;
            } elseif (in_array($cleanName, ['golongan', 'gol', 'pangkat', 'golonganpangkat'])) {
                $headerMap['golongan_pangkat'] = $colIdx;
            } elseif (in_array($cleanName, ['nohp', 'hp', 'wa', 'nowa', 'telepon', 'kontak', 'nohpwa', 'nomorhp'])) {
                $headerMap['no_hp'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['status', 'keaktifan', 'statuskeaktifan'])) {
                $headerMap['status'] = $colIdx;
                $hasHeader = true;
            } elseif (in_array($cleanName, ['statuskepegawaian', 'jeniskepegawaian', 'kepegawaian', 'pnsgtt'])) {
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
                $namaLengkap = null;
                $gelarDepan = null;
                $gelarBelakang = null;
                $nip = null;
                $nuptk = null;
                $nik = null;
                $jabatan = null;
                $jenisPtk = null;
                $mapelDiampu = null;
                $jjm = null;
                $tugasTambahan = null;
                $pendidikan = null;
                $jurusan = null;
                $kampus = null;
                $statusSertifikasi = 'belum';
                $golongan = null;
                $noHp = null;
                $status = 'aktif';
                $jenisKepegawaian = 'pns';

                if ($hasHeader && isset($headerMap['nama'])) {
                    $nama = $cleanRow[$headerMap['nama']] ?? null;
                    $gelarDepan = isset($headerMap['gelar_depan']) ? ($cleanRow[$headerMap['gelar_depan']] ?? null) : null;
                    $gelarBelakang = isset($headerMap['gelar_belakang']) ? ($cleanRow[$headerMap['gelar_belakang']] ?? null) : null;
                    $nip = isset($headerMap['nip']) ? ($cleanRow[$headerMap['nip']] ?? null) : null;
                    $nuptk = isset($headerMap['nuptk']) ? ($cleanRow[$headerMap['nuptk']] ?? null) : null;
                    $nik = isset($headerMap['nik']) ? ($cleanRow[$headerMap['nik']] ?? null) : null;
                    $jabatan = isset($headerMap['jabatan']) ? ($cleanRow[$headerMap['jabatan']] ?? null) : null;
                    $jenisPtk = isset($headerMap['jenis_ptk']) ? ($cleanRow[$headerMap['jenis_ptk']] ?? null) : null;
                    $mapelDiampu = isset($headerMap['mapel_diampu']) ? ($cleanRow[$headerMap['mapel_diampu']] ?? null) : null;
                    $jjm = isset($headerMap['jjm']) ? (int)preg_replace('/[^0-9]/', '', (string)$cleanRow[$headerMap['jjm']]) : null;
                    $tugasTambahan = isset($headerMap['tugas_tambahan']) ? ($cleanRow[$headerMap['tugas_tambahan']] ?? null) : null;
                    $pendidikan = isset($headerMap['pendidikan_terakhir']) ? ($cleanRow[$headerMap['pendidikan_terakhir']] ?? null) : null;
                    $jurusan = isset($headerMap['jurusan_kuliah']) ? ($cleanRow[$headerMap['jurusan_kuliah']] ?? null) : null;
                    $kampus = isset($headerMap['kampus']) ? ($cleanRow[$headerMap['kampus']] ?? null) : null;
                    $statusSertifikasiRaw = isset($headerMap['status_sertifikasi']) ? strtolower($cleanRow[$headerMap['status_sertifikasi']] ?? '') : '';
                    $statusSertifikasi = (str_contains($statusSertifikasiRaw, 'sudah') || str_contains($statusSertifikasiRaw, 'ya')) ? 'sudah' : 'belum';
                    $golongan = isset($headerMap['golongan_pangkat']) ? ($cleanRow[$headerMap['golongan_pangkat']] ?? null) : null;
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

                        // 3. Cek NIP (15 - 22 digit) atau NUPTK (16 digit) atau NIK (16 digit)
                        if (strlen($digitsOnly) === 18) {
                            $nip = $digitsOnly;
                            continue;
                        } elseif (strlen($digitsOnly) === 16) {
                            if (!$nuptk) {
                                $nuptk = $digitsOnly;
                            } else {
                                $nik = $digitsOnly;
                            }
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

                // Cari guru berdasarkan NIP, NUPTK, NIK, atau Nama
                $existingGuru = null;
                if (!empty($nip)) {
                    $existingGuru = Guru::where('nip', $nip)->first();
                }
                if (!$existingGuru && !empty($nuptk)) {
                    $existingGuru = Guru::where('nuptk', $nuptk)->first();
                }
                if (!$existingGuru && !empty($nik)) {
                    $existingGuru = Guru::where('nik', $nik)->first();
                }
                if (!$existingGuru && !empty($nama)) {
                    $existingGuru = Guru::where('nama', $nama)->first();
                }

                $payload = [
                    'nama'                => $nama,
                    'nama_lengkap'        => $namaLengkap ?: $nama,
                    'gelar_depan'         => $gelarDepan,
                    'gelar_belakang'      => $gelarBelakang,
                    'nip'                 => $nip,
                    'nuptk'               => $nuptk,
                    'nik'                 => $nik,
                    'jabatan'             => $jabatan,
                    'jenis_ptk'           => $jenisPtk ?: ($existingGuru->jenis_ptk ?? null),
                    'mapel_diampu'        => $mapelDiampu ?: ($existingGuru->mapel_diampu ?? null),
                    'jjm'                 => $jjm ?: ($existingGuru->jjm ?? null),
                    'tugas_tambahan'      => $tugasTambahan ?: ($existingGuru->tugas_tambahan ?? null),
                    'pendidikan_terakhir' => $pendidikan ?: ($existingGuru->pendidikan_terakhir ?? null),
                    'jurusan_kuliah'      => $jurusan ?: ($existingGuru->jurusan_kuliah ?? null),
                    'kampus'              => $kampus ?: ($existingGuru->kampus ?? null),
                    'status_sertifikasi'  => $statusSertifikasi ?: ($existingGuru->status_sertifikasi ?? 'belum'),
                    'golongan_pangkat'    => $golongan ?: ($existingGuru->golongan_pangkat ?? null),
                    'status'              => $status,
                    'no_hp'               => $noHp ?: ($existingGuru->no_hp ?? null),
                    'jenis_kepegawaian'   => $jenisKepegawaian,
                ];

                if ($existingGuru) {
                    $existingGuru->update(array_filter($payload, fn($v) => !is_null($v)));
                } else {
                    Guru::create($payload);
                }

                $imported++;
            }
        });

        // Sinkronkan data Kepala Sekolah jika terdeteksi dari hasil impor
        $kepsekGuru = Guru::where('jabatan', 'Kepala Sekolah')
            ->orWhere('tugas_tambahan', 'like', '%Kepala Sekolah%')
            ->first();
        if ($kepsekGuru) {
            $this->syncGuruDataToUserRole($kepsekGuru);
        }

        AuditLog::catat('import', 'guru', "Berhasil mengimpor {$imported} data GTK dari berkas CSV oleh " . (auth()->user()->name ?? 'Admin'));

        return redirect()->back()->with('success', "Berhasil memproses dan mengimpor {$imported} data guru/pegawai GTK.");
    }

    /**
     * Buat atau perbarui akun login (Nickname, Username & Password) untuk Guru / Staf.
     */
    public function storeAkun(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        // Jika username/nickname tidak diisi, buat otomatis dari input / NIP / nama guru
        $usernameInput = $request->input('username') ?: $request->input('nickname');
        if (empty($usernameInput)) {
            if ($request->filled('email')) {
                $usernameInput = explode('@', $request->input('email'))[0];
            } elseif (!empty($guru->nip)) {
                $usernameInput = $guru->nip;
            } else {
                $usernameInput = Str::slug($guru->nama, '');
            }
        }
        $username = strtolower(trim(preg_replace('/\s+/', '', (string)$usernameInput)));
        $request->merge(['username' => $username]);

        $userId = $guru->user ? $guru->user->id : null;
        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . ($userId ?? 'NULL') . ',id',
            'email'    => 'nullable|email|max:255|unique:users,email,' . ($userId ?? 'NULL') . ',id',
            'password' => $userId ? 'nullable|min:4' : 'required|min:4',
            'role'     => 'nullable|in:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,guru_piket,staf_tu,guru,humas,panitia_ppdb',
            'roles'    => 'nullable|array',
            'roles.*'  => 'in:admin,kepala_sekolah,waka_kesiswaan,waka_kurikulum,waka_sarpras,waka_hubin,kaprog,kepala_bengkel,pustakawan,guru_bk,wali_kelas,guru_piket,staf_tu,guru,humas,panitia_ppdb',
        ], [
            'username.required' => 'Nickname / Username login wajib diisi.',
            'username.unique'   => 'Nickname / Username ini sudah digunakan oleh akun lain.',
            'email.unique'      => 'Email ini sudah terdaftar pada akun lain.',
            'password.required' => 'Kata sandi wajib diisi (minimal 4 karakter).',
            'password.min'      => 'Kata sandi minimal 4 karakter.',
        ]);

        $defaultRole = ($guru->jabatan === 'Kepala Sekolah') ? 'kepala_sekolah' : 'guru';
        $role = $request->input('role') ?: $defaultRole;
        $email = $request->filled('email') ? trim($request->input('email')) : ($username . '@sirani.local');

        $rolesInput = (array) $request->input('roles', []);
        $rolesList = array_values(array_diff(array_unique(array_filter(array_merge([$role], $rolesInput))), ['guru_piket', 'wali_kelas']));

        $oldRole = $guru->user ? $guru->user->role : null;

        // Proteksi self-lockout: Admin tidak boleh mencabut akses Admin dari akunnya sendiri
        if ($guru->user && auth()->id() === $guru->user->id && $role !== 'admin' && !in_array('admin', $rolesList)) {
            return redirect()->back()->with('error', 'Anda tidak dapat mencabut hak akses Admin dari akun Anda sendiri demi mencegah akun terkunci (lockout).');
        }

        if ($guru->user) {
            $updateData = [
                'name'     => $guru->nama,
                'username' => $username,
                'email'    => $email,
                'role'     => $role,
                'roles'    => $rolesList,
            ];
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->input('password'));
            }
            $guru->user->update($updateData);

            foreach ($rolesList as $r) {
                $this->syncRoleToGuruData($guru, $r, ($r === $role ? $oldRole : null));
            }

            AuditLog::catat('update', 'auth', "Akun login untuk {$guru->nama} diperbarui (Role Utama: {$role}, Multi-Role: " . implode(', ', $rolesList) . ", Username: {$username}) oleh " . (auth()->user()->name ?? 'Admin'));

            return redirect()->back()->with('success', "Akun login untuk {$guru->nama} berhasil diperbarui (Peran: " . ucfirst(str_replace('_', ' ', $role)) . (count($rolesList) > 1 ? " + " . (count($rolesList) - 1) . " Peran Tambahan" : "") . ", Nickname: {$username}).");
        }

        User::create([
            'name'     => $guru->nama,
            'username' => $username,
            'email'    => $email,
            'password' => Hash::make($request->input('password')),
            'guru_id'  => $guru->id,
            'role'     => $role,
            'roles'    => $rolesList,
        ]);

        foreach ($rolesList as $r) {
            $this->syncRoleToGuruData($guru, $r, null);
        }

        AuditLog::catat('create', 'auth', "Akun login baru untuk {$guru->nama} dibuat (Role Utama: {$role}, Multi-Role: " . implode(', ', $rolesList) . ", Username: {$username}) oleh " . (auth()->user()->name ?? 'Admin'));

        return redirect()->back()->with('success', "Akun login baru untuk {$guru->nama} berhasil dibuat (Peran: " . ucfirst(str_replace('_', ' ', $role)) . (count($rolesList) > 1 ? " + " . (count($rolesList) - 1) . " Peran Tambahan" : "") . ", Nickname: {$username}).");
    }

    /**
     * Hapus akun login guru.
     */
    public function destroyAkun($id)
    {
        $guru = Guru::findOrFail($id);
        if ($guru->user) {
            $username = $guru->user->username;
            $guru->user->delete();
            AuditLog::catat('delete', 'auth', "Akun login {$username} milik {$guru->nama} dihapus oleh " . (auth()->user()->name ?? 'Admin'));
        }

        return redirect()->back()->with('success', "Akun login untuk {$guru->nama} berhasil dihapus.");
    }

    /**
     * Tambahkan sertifikat pelatihan guru.
     */
    public function storeSertifikat(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama_pelatihan'  => 'required|string|max:255',
            'penyelenggara'   => 'required|string|max:200',
            'tahun'           => 'required|string|max:10',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nama_pelatihan.required' => 'Nama pelatihan / kegiatan wajib diisi.',
            'penyelenggara.required'  => 'Nama lembaga / instansi penyelenggara wajib diisi.',
            'tahun.required'          => 'Tahun pelaksanaan pelatihan wajib diisi.',
            'file_sertifikat.mimes'   => 'Format berkas sertifikat harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_sertifikat.max'     => 'Ukuran berkas sertifikat maksimal 5 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_sertifikat')) {
            $filePath = $request->file('file_sertifikat')->store('sertifikat_guru', 'public');
        }

        SertifikatGuru::create([
            'guru_id'         => $guru->id,
            'nama_pelatihan'  => $request->input('nama_pelatihan'),
            'penyelenggara'   => $request->input('penyelenggara'),
            'tahun'           => $request->input('tahun'),
            'file_sertifikat' => $filePath,
        ]);

        return redirect()->back()->with('success', "Sertifikat pelatihan \"{$request->input('nama_pelatihan')}\" berhasil ditambahkan ke portofolio {$guru->nama}.");
    }

    /**
     * Hapus sertifikat pelatihan guru.
     */
    public function destroySertifikat($id, $sertifikatId)
    {
        $sertifikat = SertifikatGuru::where('guru_id', $id)->findOrFail($sertifikatId);
        $namaPelatihan = $sertifikat->nama_pelatihan;

        if ($sertifikat->file_sertifikat && \Illuminate\Support\Facades\Storage::disk('public')->exists($sertifikat->file_sertifikat)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($sertifikat->file_sertifikat);
        }

        $sertifikat->delete();

        return redirect()->back()->with('success', "Sertifikat \"{$namaPelatihan}\" berhasil dihapus dari portofolio.");
    }
}
