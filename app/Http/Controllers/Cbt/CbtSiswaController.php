<?php

namespace App\Http\Controllers\Cbt;

use App\Http\Controllers\Controller;
use App\Models\CbtJadwalUjian;
use App\Models\CbtPesertaUjian;
use App\Models\CbtJawabanSiswa;
use App\Models\CbtLogAktivitas;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CbtSiswaController extends Controller
{
    /**
     * Session Key untuk autentikasi Siswa di CBT Portal
     */
    protected const SESSION_SISWA = 'cbt_siswa_id';

    protected function getSiswaAktif(): ?Siswa
    {
        $siswaId = Session::get(self::SESSION_SISWA);
        if (!$siswaId) {
            return null;
        }
        return Siswa::with('siswaRombels.rombel')->find($siswaId);
    }

    public function loginForm()
    {
        if ($this->getSiswaAktif()) {
            return redirect()->route('cbt.siswa.dashboard');
        }

        $sekolah = PengaturanSekolah::first() ?? new PengaturanSekolah();
        return view('dcc.cbt.siswa.login', compact('sekolah'));
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
        ]);

        $nisn = trim($request->nisn);
        $siswa = Siswa::where('nisn', $nisn)->first();

        if (!$siswa) {
            return back()->with('error', 'NISN tidak terdaftar di sistem sekolah. Periksa kembali NISN Anda.');
        }

        // Cek password_cbt jika diset
        if (!empty($siswa->password_cbt) && $request->filled('password')) {
            if ($siswa->password_cbt !== trim($request->password)) {
                return back()->with('error', 'Password CBT Anda salah.');
            }
        }

        Session::put(self::SESSION_SISWA, $siswa->id);

        return redirect()->route('cbt.siswa.dashboard')->with('success', "Selamat datang, {$siswa->nama_siswa}!");
    }

    public function logout()
    {
        Session::forget(self::SESSION_SISWA);
        return redirect()->route('cbt.siswa.login')->with('success', 'Anda telah berhasil keluar dari portal ujian.');
    }

    public function dashboard()
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) {
            return redirect()->route('cbt.siswa.login');
        }

        $sekolah = PengaturanSekolah::first() ?? new PengaturanSekolah();
        $now = Carbon::now();

        // Cari jadwal ujian aktif untuk rombel siswa ini
        $jadwalAktif = CbtJadwalUjian::with(['bankSoal', 'rombels'])
            ->where('status', 'aktif')
            ->whereHas('rombels', function($q) use ($siswa) {
                $q->where('rombel_id', $siswa->rombel_id);
            })
            ->where('waktu_mulai', '<=', $now)
            ->where('waktu_selesai', '>=', $now)
            ->get();

        // Riwayat ujian siswa
        $riwayatUjian = CbtPesertaUjian::with(['jadwal.bankSoal'])
            ->where('siswa_id', $siswa->id)
            ->where('status', 'selesai')
            ->orderBy('waktu_selesai', 'desc')
            ->get();

        return view('dcc.cbt.siswa.dashboard', compact('siswa', 'sekolah', 'jadwalAktif', 'riwayatUjian'));
    }

    public function verifikasiToken(Request $request, $jadwalId)
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) {
            return redirect()->route('cbt.siswa.login');
        }

        $request->validate([
            'token_ujian' => 'required|string',
        ]);

        $jadwal = CbtJadwalUjian::with('bankSoal.soals')->findOrFail($jadwalId);

        // Validasi waktu
        $now = Carbon::now();
        if ($now->lt($jadwal->waktu_mulai) || $now->gt($jadwal->waktu_selesai)) {
            return back()->with('error', 'Sesi ujian ini sedang ditutup atau belum dimulai.');
        }

        // Validasi Token
        if (strtoupper(trim($request->token_ujian)) !== strtoupper(trim($jadwal->token_ujian))) {
            return back()->with('error', 'Token Ujian tidak valid. Mintalah token sesi aktif kepada pengawas ruangan.');
        }

        // Validasi / Cari Peserta Ujian
        $peserta = CbtPesertaUjian::firstOrCreate([
            'jadwal_ujian_id' => $jadwal->id,
            'siswa_id'        => $siswa->id,
        ], [
            'rombel_id'       => $siswa->rombel_id,
            'status'          => 'belum',
        ]);

        if ($peserta->status === 'selesai') {
            return redirect()->route('cbt.siswa.hasil', $jadwal->id)->with('info', 'Anda telah menyelesaikan ujian ini.');
        }

        // Mulai pengerjaan
        if ($peserta->status === 'belum') {
            $peserta->update([
                'status'       => 'mengerjakan',
                'waktu_mulai'  => $now,
                'token_used'   => strtoupper($request->token_ujian),
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ]);

            // Buat row jawaban untuk tiap butir soal
            foreach ($jadwal->bankSoal->soals as $soal) {
                CbtJawabanSiswa::firstOrCreate([
                    'peserta_ujian_id' => $peserta->id,
                    'soal_id'          => $soal->id,
                ], [
                    'jawaban_siswa'    => null,
                    'is_ragu'          => false,
                ]);
            }

            // Log event mulai
            CbtLogAktivitas::create([
                'peserta_ujian_id' => $peserta->id,
                'tipe_event'       => 'login',
                'detail'           => "Siswa memulai pengerjaan dari IP: {$request->ip()}",
                'waktu_catat'      => $now,
            ]);
        }

        return redirect()->route('cbt.siswa.lembar_ujian', $jadwal->id);
    }

    public function lembarUjian($jadwalId)
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) {
            return redirect()->route('cbt.siswa.login');
        }

        $jadwal = CbtJadwalUjian::with(['bankSoal.soals'])->findOrFail($jadwalId);
        $peserta = CbtPesertaUjian::where('jadwal_ujian_id', $jadwalId)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        if ($peserta->status === 'selesai') {
            return redirect()->route('cbt.siswa.hasil', $jadwalId);
        }

        // Hitung sisa waktu berdasarkan durasi_menit
        $waktuMulai = Carbon::parse($peserta->waktu_mulai);
        $batasWaktuMaks = $waktuMulai->copy()->addMinutes($jadwal->durasi_menit);
        
        // Juga jangan melebihi waktu_selesai jadwal
        if ($batasWaktuMaks->gt($jadwal->waktu_selesai)) {
            $batasWaktuMaks = Carbon::parse($jadwal->waktu_selesai);
        }

        $sisaDetik = Carbon::now()->diffInSeconds($batasWaktuMaks, false);

        if ($sisaDetik <= 0) {
            $peserta->update([
                'status'        => 'selesai',
                'waktu_selesai' => Carbon::now(),
            ]);
            $peserta->hitungNilaiOtomatis();
            return redirect()->route('cbt.siswa.hasil', $jadwalId)->with('info', 'Waktu ujian telah berakhir.');
        }

        // Ambil soal (acak atau terurut sesuai setting)
        $soalsQuery = $jadwal->bankSoal->soals();
        if ($jadwal->acak_soal) {
            // Pengacakan konsisten per peserta berdasarkan ID peserta
            $soals = $soalsQuery->get()->shuffle($peserta->id);
        } else {
            $soals = $soalsQuery->orderBy('nomor_urut', 'asc')->get();
        }

        $jawabans = CbtJawabanSiswa::where('peserta_ujian_id', $peserta->id)->get()->keyBy('soal_id');

        return view('dcc.cbt.siswa.lembar_ujian', compact(
            'siswa',
            'jadwal',
            'peserta',
            'soals',
            'jawabans',
            'sisaDetik'
        ));
    }

    public function autosaveJawaban(Request $request)
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'soal_id' => 'required|exists:cbt_soals,id',
            'jawaban' => 'nullable|string',
            'is_ragu' => 'nullable|boolean',
        ]);

        $peserta = CbtPesertaUjian::where('siswa_id', $siswa->id)
            ->where('status', 'mengerjakan')
            ->latest('id')
            ->first();

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak aktif'], 403);
        }

        $jawaban = CbtJawabanSiswa::updateOrCreate([
            'peserta_ujian_id' => $peserta->id,
            'soal_id'          => $request->soal_id,
        ], [
            'jawaban_siswa'    => $request->jawaban,
            'is_ragu'          => (bool)$request->is_ragu,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'soal_id' => $request->soal_id,
        ]);
    }

    public function logAktivitas(Request $request)
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) return response()->json(['success' => false], 401);

        $peserta = CbtPesertaUjian::where('siswa_id', $siswa->id)
            ->where('status', 'mengerjakan')
            ->latest('id')
            ->first();

        if (!$peserta) return response()->json(['success' => false], 403);

        $tipe = $request->tipe_event ?: 'unknown';
        $detail = $request->detail ?: 'Aktivitas tercatat';

        if ($tipe === 'tab_switch') {
            $peserta->increment('jumlah_pelanggaran');
        }

        CbtLogAktivitas::create([
            'peserta_ujian_id' => $peserta->id,
            'tipe_event'       => $tipe,
            'detail'           => $detail,
            'waktu_catat'      => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function selesaiUjian(Request $request, $jadwalId)
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) {
            return redirect()->route('cbt.siswa.login');
        }

        $peserta = CbtPesertaUjian::where('jadwal_ujian_id', $jadwalId)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        $peserta->update([
            'status'        => 'selesai',
            'waktu_selesai' => Carbon::now(),
        ]);

        $peserta->hitungNilaiOtomatis();

        CbtLogAktivitas::create([
            'peserta_ujian_id' => $peserta->id,
            'tipe_event'       => 'finish',
            'detail'           => 'Siswa menyelesaikan ujian secara manual.',
            'waktu_catat'      => Carbon::now(),
        ]);

        return redirect()->route('cbt.siswa.hasil', $jadwalId)->with('success', 'Ujian telah selesai dikerjakan.');
    }

    public function hasilUjian($jadwalId)
    {
        $siswa = $this->getSiswaAktif();
        if (!$siswa) {
            return redirect()->route('cbt.siswa.login');
        }

        $jadwal = CbtJadwalUjian::with('bankSoal')->findOrFail($jadwalId);
        $peserta = CbtPesertaUjian::where('jadwal_ujian_id', $jadwalId)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        $sekolah = PengaturanSekolah::first() ?? new PengaturanSekolah();

        return view('dcc.cbt.siswa.hasil', compact('siswa', 'peserta', 'jadwal', 'sekolah'));
    }
}
