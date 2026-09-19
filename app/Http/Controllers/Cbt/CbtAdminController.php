<?php

namespace App\Http\Controllers\Cbt;

use App\Http\Controllers\Controller;
use App\Models\CbtBankSoal;
use App\Models\CbtSoal;
use App\Models\CbtJadwalUjian;
use App\Models\CbtJadwalRombel;
use App\Models\CbtPesertaUjian;
use App\Models\CbtJawabanSiswa;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Jurusan;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CbtAdminController extends Controller
{
    /**
     * Helper identifikasi pengguna guru vs pimpinan/supervisor
     */
    protected function getAuthUserContext()
    {
        $user = Auth::user();
        $isSupervisor = false;
        $guruAktif = null;

        if (!$user) {
            return [$isSupervisor, null];
        }

        if (in_array($user->role, ['admin', 'kepala_sekolah', 'waka_kurikulum']) ||
            ($user->roles && in_array($user->roles, ['admin', 'kepala_sekolah', 'waka_kurikulum']))) {
            $isSupervisor = true;
        }

        if ($user->guru_id) {
            $guruAktif = Guru::find($user->guru_id);
        } else {
            $guruAktif = Guru::where('email', $user->email)->first();
        }

        // Jika bukan supervisor dan guru tidak terhubung, fallback ke guru pertama untuk demo/testing
        if (!$isSupervisor && !$guruAktif) {
            $guruAktif = Guru::first();
        }

        return [$isSupervisor, $guruAktif];
    }

    /**
     * Dashboard Utama CBT (Identik Kejar.id Course Cards)
     */
    public function dashboard()
    {
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        $bankQuery = CbtBankSoal::with(['guru', 'jurusan'])->withCount('soals');
        $jadwalQuery = CbtJadwalUjian::with(['bankSoal', 'rombels.rombel', 'guru']);

        if (!$isSupervisor && $guruAktif) {
            $bankQuery->where(function($q) use ($guruAktif) {
                $q->where('guru_id', $guruAktif->id)->orWhere('is_shared', true);
            });
            $jadwalQuery->where('guru_id', $guruAktif->id);
        }

        $totalBank = (clone $bankQuery)->count();
        $totalJadwal = (clone $jadwalQuery)->count();
        $jadwalAktif = (clone $jadwalQuery)->where('status', 'aktif')->count();
        $totalPeserta = CbtPesertaUjian::count();

        $recentJadwals = $jadwalQuery->orderBy('created_at', 'desc')->take(5)->get();
        $bankCards = $bankQuery->orderBy('updated_at', 'desc')->take(8)->get();

        return view('dcc.cbt.dashboard', compact(
            'totalBank',
            'totalJadwal',
            'jadwalAktif',
            'totalPeserta',
            'recentJadwals',
            'bankCards',
            'isSupervisor',
            'guruAktif'
        ));
    }

    // =========================================================================
    // 1. BANK SOAL
    // =========================================================================
    public function bankSoalIndex(Request $request)
    {
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        $query = CbtBankSoal::with(['guru', 'jurusan'])->withCount('soals');

        if (!$isSupervisor && $guruAktif) {
            $query->where(function($q) use ($guruAktif) {
                $q->where('guru_id', $guruAktif->id)->orWhere('is_shared', true);
            });
        }

        if ($request->filled('mapel')) {
            $query->where('mata_pelajaran', 'like', '%' . $request->mapel . '%');
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $banks = $query->orderBy('created_at', 'desc')->paginate(12);
        $gurus = Guru::orderBy('nama_guru')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();

        return view('dcc.cbt.bank_soal.index', compact('banks', 'gurus', 'jurusans', 'isSupervisor', 'guruAktif'));
    }

    public function bankSoalStore(Request $request)
    {
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        $request->validate([
            'nama_bank'      => 'required|string|max:255',
            'mata_pelajaran' => 'required|string|max:255',
            'tingkat'        => 'required|string',
            'kktp_default'   => 'required|numeric|min:0|max:100',
        ]);

        $guruId = $request->guru_id;
        if (!$isSupervisor || !$guruId) {
            $guruId = $guruAktif ? $guruAktif->id : Guru::first()->id;
        }

        $kode = 'BS-' . strtoupper(Str::slug(substr($request->mata_pelajaran, 0, 4))) . '-' . date('ymd') . '-' . rand(100, 999);

        CbtBankSoal::create([
            'kode_bank'      => $kode,
            'nama_bank'      => $request->nama_bank,
            'mata_pelajaran' => $request->mata_pelajaran,
            'tingkat'        => $request->tingkat,
            'jurusan_id'     => $request->jurusan_id,
            'guru_id'        => $guruId,
            'kktp_default'   => $request->kktp_default,
            'deskripsi'      => $request->deskripsi,
            'is_shared'      => $request->has('is_shared'),
        ]);

        return redirect()->route('admin.cbt.bank.index')->with('success', 'Paket Bank Soal berhasil dibuat.');
    }

    public function bankSoalEdit($id)
    {
        $bank = CbtBankSoal::findOrFail($id);
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        if (!$isSupervisor && $guruAktif && $bank->guru_id !== $guruAktif->id) {
            return redirect()->route('admin.cbt.bank.index')->with('error', 'Anda tidak memiliki hak akses mengubah bank soal ini.');
        }

        $gurus = Guru::orderBy('nama_guru')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();

        return view('dcc.cbt.bank_soal.edit', compact('bank', 'gurus', 'jurusans', 'isSupervisor'));
    }

    public function bankSoalUpdate(Request $request, $id)
    {
        $bank = CbtBankSoal::findOrFail($id);
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        if (!$isSupervisor && $guruAktif && $bank->guru_id !== $guruAktif->id) {
            return redirect()->route('admin.cbt.bank.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'nama_bank'      => 'required|string|max:255',
            'mata_pelajaran' => 'required|string|max:255',
            'tingkat'        => 'required|string',
            'kktp_default'   => 'required|numeric|min:0|max:100',
        ]);

        $bank->update([
            'nama_bank'      => $request->nama_bank,
            'mata_pelajaran' => $request->mata_pelajaran,
            'tingkat'        => $request->tingkat,
            'jurusan_id'     => $request->jurusan_id,
            'kktp_default'   => $request->kktp_default,
            'deskripsi'      => $request->deskripsi,
            'is_shared'      => $request->has('is_shared'),
        ]);

        return redirect()->route('admin.cbt.bank.index')->with('success', 'Paket Bank Soal berhasil diperbarui.');
    }

    public function bankSoalDestroy($id)
    {
        $bank = CbtBankSoal::findOrFail($id);
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        if (!$isSupervisor && $guruAktif && $bank->guru_id !== $guruAktif->id) {
            return redirect()->route('admin.cbt.bank.index')->with('error', 'Akses ditolak.');
        }

        $bank->delete();
        return redirect()->route('admin.cbt.bank.index')->with('success', 'Bank soal beserta seluruh butir soal berhasil dihapus.');
    }

    // =========================================================================
    // 2. BUTIR SOAL & IMPORT CSV
    // =========================================================================
    public function soalIndex($bankId)
    {
        $bank = CbtBankSoal::withCount('soals')->findOrFail($bankId);
        $soals = CbtSoal::where('bank_soal_id', $bankId)->orderBy('nomor_urut', 'asc')->get();

        return view('dcc.cbt.soal.index', compact('bank', 'soals'));
    }

    public function soalCreate($bankId)
    {
        $bank = CbtBankSoal::findOrFail($bankId);
        $nextNomor = CbtSoal::where('bank_soal_id', $bankId)->max('nomor_urut') + 1;

        return view('dcc.cbt.soal.create', compact('bank', 'nextNomor'));
    }

    public function soalStore(Request $request, $bankId)
    {
        $bank = CbtBankSoal::findOrFail($bankId);

        $request->validate([
            'nomor_urut'    => 'required|integer',
            'jenis_soal'    => 'required|in:pg,esai,pg_kompleks,isian',
            'pertanyaan'    => 'required',
            'bobot_nilai'   => 'required|numeric|min:0',
            'media_gambar'  => 'nullable|image|max:3072',
        ]);

        $gambarPath = null;
        if ($request->hasFile('media_gambar')) {
            $file = $request->file('media_gambar');
            $filename = 'soal_' . $bankId . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cbt_soal'), $filename);
            $gambarPath = 'uploads/cbt_soal/' . $filename;
        }

        $kunci = $request->kunci_jawaban;
        if ($request->jenis_soal === 'esai') {
            $kunci = $request->kunci_jawaban_esai;
        }

        CbtSoal::create([
            'bank_soal_id'  => $bankId,
            'nomor_urut'    => $request->nomor_urut,
            'jenis_soal'    => $request->jenis_soal,
            'pertanyaan'    => $request->pertanyaan,
            'media_gambar'  => $gambarPath,
            'opsi_a'        => $request->opsi_a,
            'opsi_b'        => $request->opsi_b,
            'opsi_c'        => $request->opsi_c,
            'opsi_d'        => $request->opsi_d,
            'opsi_e'        => $request->opsi_e,
            'kunci_jawaban' => $kunci,
            'bobot_nilai'   => $request->bobot_nilai,
            'kode_tp'       => $request->kode_tp,
            'pembahasan'    => $request->pembahasan,
        ]);

        return redirect()->route('admin.cbt.soal.index', $bankId)->with('success', 'Butir soal berhasil ditambahkan.');
    }

    public function soalEdit($bankId, $soalId)
    {
        $soal = CbtSoal::where('bank_soal_id', $bankId)->findOrFail($soalId);
        return view('dcc.cbt.soal.edit', compact('soal'));
    }

    public function soalUpdate(Request $request, $bankId, $soalId)
    {
        $soal = CbtSoal::where('bank_soal_id', $bankId)->findOrFail($soalId);

        $request->validate([
            'nomor_urut'    => 'required|integer',
            'jenis_soal'    => 'required|in:pg,esai,pg_kompleks,isian',
            'pertanyaan'    => 'required',
            'bobot_nilai'   => 'required|numeric|min:0',
            'media_gambar'  => 'nullable|image|max:3072',
        ]);

        $gambarPath = $soal->media_gambar;
        if ($request->hasFile('media_gambar')) {
            $file = $request->file('media_gambar');
            $filename = 'soal_' . $bankId . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cbt_soal'), $filename);
            $gambarPath = 'uploads/cbt_soal/' . $filename;
        }

        $kunci = $request->kunci_jawaban;
        if ($request->jenis_soal === 'esai') {
            $kunci = $request->kunci_jawaban_esai;
        }

        $soal->update([
            'nomor_urut'    => $request->nomor_urut,
            'jenis_soal'    => $request->jenis_soal,
            'pertanyaan'    => $request->pertanyaan,
            'media_gambar'  => $gambarPath,
            'opsi_a'        => $request->opsi_a,
            'opsi_b'        => $request->opsi_b,
            'opsi_c'        => $request->opsi_c,
            'opsi_d'        => $request->opsi_d,
            'opsi_e'        => $request->opsi_e,
            'kunci_jawaban' => $kunci,
            'bobot_nilai'   => $request->bobot_nilai,
            'kode_tp'       => $request->kode_tp,
            'pembahasan'    => $request->pembahasan,
        ]);

        return redirect()->route('admin.cbt.soal.index', $bankId)->with('success', 'Butir soal berhasil diperbarui.');
    }

    public function soalDestroy($bankId, $soalId)
    {
        $soal = CbtSoal::where('bank_soal_id', $bankId)->findOrFail($soalId);
        $soal->delete();
        return redirect()->route('admin.cbt.soal.index', $bankId)->with('success', 'Butir soal berhasil dihapus.');
    }

    public function importSoalCsv(Request $request, $bankId)
    {
        $bank = CbtBankSoal::findOrFail($bankId);
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 4096, ',');

        $count = 0;
        $currentNo = CbtSoal::where('bank_soal_id', $bankId)->max('nomor_urut') ?: 0;

        while (($row = fgetcsv($handle, 4096, ',')) !== false) {
            if (empty(array_filter($row))) continue;

            $pertanyaan = $row[0] ?? '';
            $opsiA      = $row[1] ?? '';
            $opsiB      = $row[2] ?? '';
            $opsiC      = $row[3] ?? '';
            $opsiD      = $row[4] ?? '';
            $opsiE      = $row[5] ?? '';
            $kunci      = strtoupper(trim($row[6] ?? 'A'));
            $bobot      = isset($row[7]) && is_numeric($row[7]) ? (float)$row[7] : 1.0;
            $kodeTp     = trim($row[8] ?? '');

            if (empty($pertanyaan)) continue;

            $currentNo++;
            CbtSoal::create([
                'bank_soal_id'  => $bankId,
                'nomor_urut'    => $currentNo,
                'jenis_soal'    => 'pg',
                'pertanyaan'    => $pertanyaan,
                'opsi_a'        => $opsiA,
                'opsi_b'        => $opsiB,
                'opsi_c'        => $opsiC,
                'opsi_d'        => $opsiD,
                'opsi_e'        => $opsiE,
                'kunci_jawaban' => $kunci,
                'bobot_nilai'   => $bobot,
                'kode_tp'       => $kodeTp ?: null,
            ]);
            $count++;
        }

        fclose($handle);
        return redirect()->route('admin.cbt.soal.index', $bankId)->with('success', "Berhasil mengimpor {$count} butir soal dari CSV.");
    }

    // =========================================================================
    // 3. JADWAL & SESI UJIAN
    // =========================================================================
    public function jadwalIndex()
    {
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        $query = CbtJadwalUjian::with(['bankSoal', 'rombels.rombel', 'guru']);

        if (!$isSupervisor && $guruAktif) {
            $query->where('guru_id', $guruAktif->id);
        }

        $jadwals = $query->orderBy('waktu_mulai', 'desc')->paginate(15);
        $banks = CbtBankSoal::withCount('soals')->get();
        $rombels = Rombel::orderBy('nama_rombel')->get();

        return view('dcc.cbt.jadwal.index', compact('jadwals', 'banks', 'rombels', 'isSupervisor', 'guruAktif'));
    }

    public function jadwalCreate()
    {
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        $bankQuery = CbtBankSoal::withCount('soals');
        if (!$isSupervisor && $guruAktif) {
            $bankQuery->where(function($q) use ($guruAktif) {
                $q->where('guru_id', $guruAktif->id)->orWhere('is_shared', true);
            });
        }
        $banks = $bankQuery->get();

        $rombels = Rombel::orderBy('nama_rombel')->get();
        $gurus = Guru::orderBy('nama_guru')->get();
        $token = CbtJadwalUjian::generateToken();

        return view('dcc.cbt.jadwal.create', compact('banks', 'rombels', 'gurus', 'token', 'isSupervisor', 'guruAktif'));
    }

    public function jadwalStore(Request $request)
    {
        [$isSupervisor, $guruAktif] = $this->getAuthUserContext();

        $request->validate([
            'bank_soal_id'   => 'required|exists:cbt_bank_soals,id',
            'nama_ujian'     => 'required|string|max:255',
            'tipe_ujian'     => 'required|in:uh,pts,pas,us,asesmen,remidi',
            'kktp'           => 'required|numeric|min:0|max:100',
            'waktu_mulai'    => 'required|date',
            'waktu_selesai'  => 'required|date|after:waktu_mulai',
            'durasi_menit'   => 'required|integer|min:5',
            'token_ujian'    => 'required|string|max:10',
            'rombels'        => 'required|array|min:1',
        ]);

        $guruId = $request->guru_id;
        if (!$isSupervisor || !$guruId) {
            $guruId = $guruAktif ? $guruAktif->id : Guru::first()->id;
        }

        DB::transaction(function() use ($request, $guruId) {
            $jadwal = CbtJadwalUjian::create([
                'bank_soal_id'    => $request->bank_soal_id,
                'guru_id'         => $guruId,
                'nama_ujian'      => $request->nama_ujian,
                'tipe_ujian'      => $request->tipe_ujian,
                'is_remedial'     => false,
                'kktp'            => $request->kktp,
                'waktu_mulai'     => Carbon::parse($request->waktu_mulai),
                'waktu_selesai'   => Carbon::parse($request->waktu_selesai),
                'durasi_menit'    => $request->durasi_menit,
                'token_ujian'     => strtoupper($request->token_ujian),
                'acak_soal'       => $request->has('acak_soal'),
                'acak_opsi'       => $request->has('acak_opsi'),
                'tampilkan_nilai' => $request->has('tampilkan_nilai'),
                'status'          => 'aktif',
            ]);

            foreach ($request->rombels as $rombelId) {
                CbtJadwalRombel::create([
                    'jadwal_ujian_id' => $jadwal->id,
                    'rombel_id'       => $rombelId,
                ]);

                // Enroll semua siswa di rombel tersebut
                $siswas = Siswa::whereHas('siswaRombels', function($q) use ($rombelId) {
                    $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
                })->get();
                foreach ($siswas as $s) {
                    CbtPesertaUjian::firstOrCreate([
                        'jadwal_ujian_id' => $jadwal->id,
                        'siswa_id'        => $s->id,
                    ], [
                        'rombel_id'       => $rombelId,
                        'status'          => 'belum',
                    ]);
                }
            }
        });

        return redirect()->route('admin.cbt.jadwal.index')->with('success', 'Sesi ujian berhasil dijadwalkan.');
    }

    public function toggleJadwalStatus($id)
    {
        $j = CbtJadwalUjian::findOrFail($id);
        $j->status = ($j->status === 'aktif') ? 'selesai' : 'aktif';
        $j->save();

        return back()->with('success', 'Status ujian berhasil diubah menjadi: ' . strtoupper($j->status));
    }

    public function refreshToken($id)
    {
        $j = CbtJadwalUjian::findOrFail($id);
        $j->token_ujian = CbtJadwalUjian::generateToken();
        $j->save();

        return back()->with('success', 'Token sesi ujian baru berhasil diterbitkan: ' . $j->token_ujian);
    }

    public function jadwalDestroy($id)
    {
        $j = CbtJadwalUjian::findOrFail($id);
        $j->delete();
        return redirect()->route('admin.cbt.jadwal.index')->with('success', 'Sesi ujian berhasil dihapus.');
    }

    // =========================================================================
    // 4. 1-CLICK REMEDIAL GENERATOR
    // =========================================================================
    public function createRemedial($parentJadwalId)
    {
        $parent = CbtJadwalUjian::with(['bankSoal', 'rombels.rombel'])->findOrFail($parentJadwalId);

        // Siswa yang belum tuntas di ujian parent
        $pesertaRemidi = CbtPesertaUjian::with('siswa')
            ->where('jadwal_ujian_id', $parentJadwalId)
            ->where(function($q) use ($parent) {
                $q->where('nilai_akhir', '<', $parent->kktp)
                  ->orWhere('is_tuntas', false)
                  ->orWhereNull('nilai_akhir');
            })
            ->get();

        $tokenBaru = CbtJadwalUjian::generateToken();
        $banks = CbtBankSoal::orderBy('nama_bank')->get();

        return view('dcc.cbt.remedial.create', compact('parent', 'pesertaRemidi', 'tokenBaru', 'banks'));
    }

    public function storeRemedial(Request $request, $parentJadwalId)
    {
        $parent = CbtJadwalUjian::findOrFail($parentJadwalId);

        $request->validate([
            'nama_ujian'       => 'required|string|max:255',
            'bank_soal_id'     => 'required|exists:cbt_bank_soals,id',
            'remedial_policy'  => 'required|in:cap_kktp,nilai_tertinggi,rata_rata,murni',
            'waktu_mulai'      => 'required|date',
            'waktu_selesai'    => 'required|date|after:waktu_mulai',
            'durasi_menit'     => 'required|integer|min:5',
            'token_ujian'      => 'required|string|max:10',
            'siswa_ids'        => 'required|array|min:1',
        ]);

        DB::transaction(function() use ($request, $parent, $parentJadwalId) {
            $jadwalRemidi = CbtJadwalUjian::create([
                'bank_soal_id'     => $request->bank_soal_id,
                'guru_id'          => $parent->guru_id,
                'parent_jadwal_id' => $parentJadwalId,
                'nama_ujian'       => $request->nama_ujian,
                'tipe_ujian'       => 'remidi',
                'is_remedial'      => true,
                'remedial_policy'  => $request->remedial_policy,
                'kktp'             => $parent->kktp,
                'waktu_mulai'      => Carbon::parse($request->waktu_mulai),
                'waktu_selesai'    => Carbon::parse($request->waktu_selesai),
                'durasi_menit'     => $request->durasi_menit,
                'token_ujian'      => strtoupper($request->token_ujian),
                'acak_soal'        => true,
                'acak_opsi'        => true,
                'tampilkan_nilai'  => true,
                'status'           => 'aktif',
            ]);

            // Copy rombels from parent
            foreach ($parent->rombels as $jr) {
                CbtJadwalRombel::firstOrCreate([
                    'jadwal_ujian_id' => $jadwalRemidi->id,
                    'rombel_id'       => $jr->rombel_id,
                ]);
            }

            // Enroll hanya siswa yang dipilih remedial
            foreach ($request->siswa_ids as $siswaId) {
                $siswa = Siswa::find($siswaId);
                if (!$siswa) continue;

                CbtPesertaUjian::create([
                    'jadwal_ujian_id' => $jadwalRemidi->id,
                    'siswa_id'        => $siswaId,
                    'rombel_id'       => $siswa->rombel_id,
                    'status'          => 'belum',
                ]);
            }
        });

        return redirect()->route('admin.cbt.jadwal.index')->with('success', 'Sesi Remedial Otomatis 1-Klik berhasil diterbitkan.');
    }

    // =========================================================================
    // 5. PROCTORING & MONITORING LIVE
    // =========================================================================
    public function proctorIndex(Request $request, $jadwalId)
    {
        $jadwal = CbtJadwalUjian::with(['bankSoal.soals', 'rombels.rombel'])->findOrFail($jadwalId);

        $pesertaQuery = CbtPesertaUjian::with(['siswa.rombel', 'jawabans'])
            ->where('jadwal_ujian_id', $jadwalId);

        if ($request->filled('rombel_id')) {
            $pesertaQuery->where('rombel_id', $request->rombel_id);
        }

        if ($request->filled('status')) {
            $pesertaQuery->where('status', $request->status);
        }

        $pesertas = $pesertaQuery->get();

        $totalSiswa = $pesertas->count();
        $sedangMengerjakan = $pesertas->where('status', 'mengerjakan')->count();
        $selesai = $pesertas->where('status', 'selesai')->count();
        $belum = $pesertas->where('status', 'belum')->count();

        return view('dcc.cbt.proctor.index', compact(
            'jadwal',
            'pesertas',
            'totalSiswa',
            'sedangMengerjakan',
            'selesai',
            'belum'
        ));
    }

    public function proctorResetLogin($pesertaId)
    {
        $peserta = CbtPesertaUjian::findOrFail($pesertaId);
        $peserta->update([
            'status'     => 'belum',
            'ip_address' => null,
            'user_agent' => null,
        ]);

        return back()->with('success', "Login peserta {$peserta->siswa->nama_siswa} berhasil di-reset. Siswa dapat login kembali.");
    }

    public function proctorForceFinish($pesertaId)
    {
        $peserta = CbtPesertaUjian::with('jadwal.bankSoal.soals', 'jawabans')->findOrFail($pesertaId);
        $peserta->update([
            'status'        => 'selesai',
            'waktu_selesai' => Carbon::now(),
        ]);
        $peserta->hitungNilaiOtomatis();

        return back()->with('success', "Pengerjaan ujian {$peserta->siswa->nama_siswa} telah diselesaikan secara paksa dan dinilai otomatis.");
    }

    // =========================================================================
    // 6. CETAK KARTU PESERTA UJIAN
    // =========================================================================
    public function cetakKartu(Request $request)
    {
        $sekolah = PengaturanSekolah::first() ?? new PengaturanSekolah();
        $rombels = Rombel::orderBy('nama_rombel')->get();

        $selectedRombelId = $request->rombel_id ?: ($rombels->first()->id ?? null);
        $rombel = Rombel::find($selectedRombelId);

        $siswas = [];
        if ($selectedRombelId) {
            $siswas = Siswa::whereHas('siswaRombels', function($q) use ($selectedRombelId) {
                $q->where('rombel_id', $selectedRombelId)->where('status_keanggotaan', 'aktif');
            })->orderBy('nama')->get();
        }

        return view('dcc.cbt.cetak.kartu', compact('sekolah', 'rombels', 'selectedRombelId', 'rombel', 'siswas'));
    }

    // =========================================================================
    // 7. REKAP NILAI KKTP & EKSPOR
    // =========================================================================
    public function rekapNilai($jadwalId)
    {
        $jadwal = CbtJadwalUjian::with(['bankSoal', 'rombels.rombel'])->findOrFail($jadwalId);
        $pesertas = CbtPesertaUjian::with(['siswa.rombel', 'jawabans'])
            ->where('jadwal_ujian_id', $jadwalId)
            ->get();

        $totalPeserta = $pesertas->count();
        $selesai = $pesertas->where('status', 'selesai');
        $totalSelesai = $selesai->count();
        $tuntas = $selesai->where('is_tuntas', true)->count();
        $belumTuntas = $selesai->where('is_tuntas', false)->count();

        $rataRata = $totalSelesai > 0 ? round($selesai->avg('nilai_akhir'), 2) : 0;
        $nilaiTertinggi = $totalSelesai > 0 ? $selesai->max('nilai_akhir') : 0;
        $nilaiTerendah = $totalSelesai > 0 ? $selesai->min('nilai_akhir') : 0;

        return view('dcc.cbt.rekap.nilai', compact(
            'jadwal',
            'pesertas',
            'totalPeserta',
            'totalSelesai',
            'tuntas',
            'belumTuntas',
            'rataRata',
            'nilaiTertinggi',
            'nilaiTerendah'
        ));
    }
}
