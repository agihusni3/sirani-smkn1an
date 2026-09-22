<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenHasil;
use App\Models\AkademikAsesmenOnline;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PortalAsesmenSiswaController extends Controller
{
    /**
     * Halaman Login Portal Asesmen Siswa (NISN & Tanggal Lahir)
     */
    public function login()
    {
        if (session()->has('cbt_siswa_id')) {
            $siswa = Siswa::find(session('cbt_siswa_id'));
            if ($siswa) {
                return redirect()->route('portal.asesmen.dashboard');
            }
            session()->forget(['cbt_siswa_id', 'cbt_siswa_nama', 'cbt_siswa_nisn']);
        }

        return view('dcc.akademik.asesmen.siswa.login');
    }

    /**
     * Proses Validasi Masuk Siswa dengan NISN & Tanggal Lahir
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'tanggal_lahir' => 'required',
        ], [
            'nisn.required' => 'Nomor NISN wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
        ]);

        $nisnClean = trim($request->nisn);
        $nisnWithoutZeros = ltrim($nisnClean, '0');
        $nisnWith10Digits = str_pad($nisnClean, 10, '0', STR_PAD_LEFT);
        
        // Cari siswa berdasarkan NISN (dengan/tanpa leading zero) atau NIS
        $siswa = Siswa::with('rombels')
            ->where(function ($q) use ($nisnClean, $nisnWithoutZeros, $nisnWith10Digits) {
                $q->where('nisn', $nisnClean)
                  ->orWhere('nis', $nisnClean);
                if (!empty($nisnWithoutZeros)) {
                    $q->orWhere('nisn', $nisnWithoutZeros)
                      ->orWhere('nis', $nisnWithoutZeros);
                }
                if (strlen($nisnClean) <= 10) {
                    $q->orWhere('nisn', $nisnWith10Digits);
                }
            })
            ->first();

        if (!$siswa) {
            return back()->withInput()->with('error', 'NISN / NIS tidak ditemukan dalam data siswa aktif SMKN 1 Air Naningan.');
        }

        // Parse dan validasi tanggal lahir format ddmmyyyy (misal: 22101991)
        $rawDob = trim($request->tanggal_lahir);
        $cleanDob = preg_replace('/[^0-9]/', '', $rawDob);

        $inputDate = null;
        if (strlen($cleanDob) === 8) {
            $day = (int) substr($cleanDob, 0, 2);
            $month = (int) substr($cleanDob, 2, 2);
            $year = (int) substr($cleanDob, 4, 4);

            if (checkdate($month, $day, $year)) {
                $inputDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        if (!$inputDate) {
            try {
                $inputDate = Carbon::parse($rawDob)->format('Y-m-d');
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Format tanggal lahir tidak valid. Masukkan 8 digit angka ddmmyyyy (Contoh: 22101991).');
            }
        }

        // Cek apakah tanggal lahir di DB sudah terisi valid
        $rawDbDate = (string) ($siswa->getRawOriginal('tanggal_lahir') ?? '');
        $isDbDateEmpty = empty($rawDbDate)
            || in_array(substr($rawDbDate, 0, 10), ['0000-00-00', '0001-01-01', '1970-01-01'])
            || (int) substr($rawDbDate, 0, 4) < 1980;

        if (!$isDbDateEmpty && $siswa->tanggal_lahir) {
            $dbDate = Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d');
            if ($dbDate !== $inputDate) {
                return back()->withInput()->with('error', 'Kombinasi NISN dan Tanggal Lahir tidak cocok. Pastikan tanggal lahir yang Anda masukkan sesuai format ddmmyyyy (Contoh: 22101991).');
            }
        } else {
            // Jika tanggal lahir di DB masih kosong/belum valid, perbarui otomatis dengan tanggal yang dimasukkan siswa
            $siswa->update(['tanggal_lahir' => $inputDate]);
        }

        // Simpan sesi autentikasi siswa CBT
        session([
            'cbt_siswa_id' => $siswa->id,
            'cbt_siswa_nama' => $siswa->nama,
            'cbt_siswa_nisn' => $siswa->nisn ?: $siswa->nis,
        ]);

        $rombelNama = $siswa->rombels->first()?->nama_rombel ?? 'Umum';

        return redirect()->route('portal.asesmen.dashboard')
            ->with('success', "Selamat datang, {$siswa->nama}! Kelas: {$rombelNama}");
    }

    /**
     * Ruang Asesmen Siswa (Daftar Ujian Aktif Rombel)
     */
    public function dashboard()
    {
        $siswaId = session('cbt_siswa_id');
        if (!$siswaId) {
            return redirect()->route('portal.asesmen.login')
                ->with('info', 'Silakan masukkan NISN dan Tanggal Lahir untuk mengakses ruang ujian.');
        }

        $siswa = Siswa::with(['rombels.jurusan'])->findOrFail($siswaId);
        $rombelIds = $siswa->rombels->pluck('id')->all();

        $now = now();

        // Cari asesmen aktif yang ditugaskan untuk rombel siswa ini
        $asesmens = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'distribusi.guru', 'soals'])
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>=', $now);
            })
            ->where(function ($q) use ($rombelIds) {
                $q->where(function ($sub) use ($rombelIds) {
                    foreach ($rombelIds as $rid) {
                        $sub->orWhereJsonContains('target_rombel_ids', (int) $rid)
                            ->orWhereJsonContains('target_rombel_ids', (string) $rid);
                    }
                })
                ->orWhereHas('distribusi', function ($sub) use ($rombelIds) {
                    $sub->whereIn('rombel_id', $rombelIds);
                });
            })
            ->latest()
            ->get();

        // Filter jika mode siswa_terpilih (Remedial/Susulan)
        $asesmens = $asesmens->filter(function ($a) use ($siswaId) {
            if ($a->target_tipe === 'siswa_terpilih' && !empty($a->target_siswa_ids)) {
                $targetIds = array_map('intval', (array) $a->target_siswa_ids);
                return in_array((int) $siswaId, $targetIds, true);
            }
            return true;
        })->values();

        // Riwayat hasil yang sudah dikerjakan siswa
        $hasils = AkademikAsesmenHasil::where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('asesmen_id');

        return view('dcc.akademik.asesmen.siswa.dashboard', compact('siswa', 'asesmens', 'hasils'));
    }

    /**
     * Konfirmasi Token Ujian Sebelum Membuka Soal
     */
    public function konfirmasiToken(Request $request, $id)
    {
        $siswaId = session('cbt_siswa_id');
        if (!$siswaId) {
            return redirect()->route('portal.asesmen.login');
        }

        $asesmen = AkademikAsesmenOnline::findOrFail($id);

        if (!$asesmen->is_active) {
            return back()->with('error', 'Ujian ini sedang ditutup atau tidak aktif.');
        }

        // Cek token jika asesmen memiliki token ujian
        if (!empty($asesmen->token_ujian)) {
            $inputToken = strtoupper(trim($request->input('token_ujian', '')));
            $correctToken = strtoupper(trim($asesmen->token_ujian));

            if ($inputToken !== $correctToken) {
                return back()->with('error', 'Token ujian yang Anda masukkan tidak valid! Silakan konfirmasi ke Pengawas Ujian.');
            }
        }

        session(["cbt_token_valid_{$id}" => true]);

        return redirect()->route('portal.asesmen.ujian', $id);
    }

    /**
     * Halaman Pengerjaan Ujian CBT Siswa
     */
    public function ujian($id)
    {
        $siswaId = session('cbt_siswa_id');
        if (!$siswaId) {
            return redirect()->route('portal.asesmen.login');
        }

        $siswa = Siswa::with('rombels')->findOrFail($siswaId);
        $asesmen = AkademikAsesmenOnline::with(['distribusi.mataPelajaran', 'soals'])->findOrFail($id);

        if (!$asesmen->is_active) {
            return redirect()->route('portal.asesmen.dashboard')->with('error', 'Asesmen ini sedang tidak aktif.');
        }

        // Verifikasi token jika diperlukan
        if (!empty($asesmen->token_ujian) && !session("cbt_token_valid_{$id}")) {
            return redirect()->route('portal.asesmen.dashboard')->with('error', 'Silakan masukkan token ujian terlebih dahulu.');
        }

        // Buat atau ambil sesi hasil pengerjaan
        $hasil = AkademikAsesmenHasil::firstOrCreate(
            ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswa->id],
            ['mulai_pada' => now(), 'is_selesai' => false, 'jumlah_pelanggaran' => 0, 'status_kejujuran' => 'jujur']
        );

        if ($hasil->is_selesai) {
            return redirect()->route('portal.asesmen.dashboard')->with('info', 'Anda sudah menyelesaikan asesmen ini sebelumnya.');
        }

        $soals = $asesmen->soals;
        if ($asesmen->acak_soal && $soals->isNotEmpty()) {
            $seed = crc32($siswa->id . '_' . $asesmen->id);
            $soalsArray = $soals->all();
            mt_srand($seed);
            shuffle($soalsArray);
            mt_srand();
            $soals = collect($soalsArray);
        }

        // Siapkan permutasi acak opsi jika diaktifkan
        $opsiAcakPerSoal = [];
        foreach ($soals as $s) {
            $opts = [];
            if (!empty($s->opsi_a)) $opts['A'] = $s->opsi_a;
            if (!empty($s->opsi_b)) $opts['B'] = $s->opsi_b;
            if (!empty($s->opsi_c)) $opts['C'] = $s->opsi_c;
            if (!empty($s->opsi_d)) $opts['D'] = $s->opsi_d;
            if (!empty($s->opsi_e)) $opts['E'] = $s->opsi_e;

            if ($asesmen->acak_opsi && count($opts) > 1) {
                $optSeed = crc32($siswa->id . '_' . $s->id);
                mt_srand($optSeed);
                $keys = array_keys($opts);
                shuffle($keys);
                mt_srand();
                $shuffled = [];
                foreach ($keys as $k) {
                    $shuffled[$k] = $opts[$k];
                }
                $opsiAcakPerSoal[$s->id] = $shuffled;
            } else {
                $opsiAcakPerSoal[$s->id] = $opts;
            }
        }

        // Tandai bahwa ini sesi pengerjaan siswa publik
        $isSiswaPortal = true;

        return view('dcc.akademik.asesmen.kerjakan', compact('asesmen', 'soals', 'siswa', 'hasil', 'opsiAcakPerSoal', 'isSiswaPortal'));
    }

    /**
     * Simpan Jawaban & Selesaikan Ujian Siswa
     */
    public function submitJawaban(Request $request, $id)
    {
        $siswaId = session('cbt_siswa_id') ?: $request->input('siswa_id');
        if (!$siswaId) {
            return response()->json(['error' => 'Sesi login tidak valid'], 403);
        }

        $siswa = Siswa::findOrFail($siswaId);
        $asesmen = AkademikAsesmenOnline::with('soals')->findOrFail($id);
        $jawabanInput = $request->input('jawaban', []);

        $totalBobot = $asesmen->soals->sum('bobot');
        $skorDidapat = 0;

        foreach ($asesmen->soals as $soal) {
            $jawabanUser = strtoupper(trim($jawabanInput[$soal->id] ?? ''));
            if ($jawabanUser === strtoupper(trim($soal->kunci_jawaban ?? ''))) {
                $skorDidapat += $soal->bobot;
            }
        }

        $nilaiAkhir = $totalBobot > 0 ? round(($skorDidapat / $totalBobot) * 100, 2) : 0;

        $hasil = AkademikAsesmenHasil::where('asesmen_id', $asesmen->id)
            ->where('siswa_id', $siswaId)
            ->first();

        $durasi = null;
        if ($hasil && $hasil->mulai_pada) {
            $durasi = now()->diffInSeconds($hasil->mulai_pada);
        }

        $violations = $hasil?->jumlah_pelanggaran ?? 0;
        $statusKejujuran = 'jujur';
        if ($violations >= ($asesmen->max_toleransi_keluar ?? 3)) {
            $statusKejujuran = 'terindikasi_curang';
        } elseif ($violations > 0) {
            $statusKejujuran = 'waspada';
        }

        AkademikAsesmenHasil::updateOrCreate(
            ['asesmen_id' => $asesmen->id, 'siswa_id' => $siswaId],
            [
                'jawaban' => $jawabanInput,
                'nilai' => $nilaiAkhir,
                'is_selesai' => true,
                'selesai_pada' => now(),
                'durasi_detik' => $durasi,
                'status_kejujuran' => $statusKejujuran,
            ]
        );

        session()->forget("cbt_token_valid_{$id}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'nilai' => $nilaiAkhir,
                'status_kejujuran' => $statusKejujuran,
                'redirect' => route('portal.asesmen.dashboard'),
            ]);
        }

        $pesan = "Asesmen ({$asesmen->judul}) berhasil diselesaikan!";
        if ($asesmen->tampilkan_nilai) {
            $pesan .= " Nilai Anda: {$nilaiAkhir}";
        }

        return redirect()->route('portal.asesmen.dashboard')->with('success', $pesan);
    }

    /**
     * Logout Sesi Siswa dari Portal Ujian
     */
    public function keluar()
    {
        session()->forget(['cbt_siswa_id', 'cbt_siswa_nama', 'cbt_siswa_nisn']);
        return redirect()->route('portal.asesmen.login')
            ->with('info', 'Anda telah keluar dari Portal Asesmen Siswa.');
    }
}
