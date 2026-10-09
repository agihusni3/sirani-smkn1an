<?php

namespace App\Http\Controllers\Situan;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\SituanRapat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SituanRapatController extends Controller
{
    public function index(Request $request)
    {
        $query = SituanRapat::with(['pimpinan', 'notulis'])->latest('tanggal_rapat');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul_rapat', 'like', "%{$q}%")
                    ->orWhere('nomor_surat', 'like', "%{$q}%")
                    ->orWhere('agenda', 'like', "%{$q}%")
                    ->orWhere('pimpinan_nama', 'like', "%{$q}%");
            });
        }

        if ($request->filled('tipe') && $request->tipe !== 'semua') {
            $query->where('tipe_rapat', $request->tipe);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->bulan);
            $query->whereYear('tanggal_rapat', $bulan->year)
                  ->whereMonth('tanggal_rapat', $bulan->month);
        }

        $rapats = $query->paginate(15)->withQueryString();

        // Statistik KPI
        $now = Carbon::now();
        $totalRapat = SituanRapat::count();
        $rapatBulanIni = SituanRapat::whereYear('tanggal_rapat', $now->year)
            ->whereMonth('tanggal_rapat', $now->month)
            ->count();
        $rapatSelesai = SituanRapat::where('status', 'selesai')->count();
        $rapatDijadwalkan = SituanRapat::where('status', 'dijadwalkan')->count();

        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $sekolah = PengaturanSekolah::getAktif();

        return view('situan.rapat.index', compact(
            'rapats',
            'totalRapat',
            'rapatBulanIni',
            'rapatSelesai',
            'rapatDijadwalkan',
            'gurus',
            'sekolah'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_rapat'       => 'required|string|max:255',
            'tipe_rapat'        => 'required|string',
            'tanggal_rapat'     => 'required|date',
            'jam_mulai'         => 'required|string',
            'jam_selesai'       => 'nullable|string',
            'tempat'            => 'required|string|max:255',
            'nomor_surat'       => 'nullable|string|max:100',
            'agenda'            => 'nullable|string',
            'pimpinan_rapat_id' => 'nullable|exists:gurus,id',
            'pimpinan_nama'     => 'nullable|string|max:150',
            'pimpinan_jabatan'  => 'nullable|string|max:100',
            'notulis_id'        => 'nullable|exists:gurus,id',
            'notulis_nama'      => 'nullable|string|max:150',
            'peserta_tipe'      => 'required|string',
            'peserta_ids'       => 'nullable|array',
            'peserta_custom'    => 'nullable|string',
            'susunan_acara'     => 'nullable|string',
        ]);

        // Auto-resolve pimpinan nama dan jabatan jika dipilih dari guru
        if (!empty($validated['pimpinan_rapat_id'])) {
            $pimpinan = Guru::find($validated['pimpinan_rapat_id']);
            if ($pimpinan) {
                $validated['pimpinan_nama'] = $pimpinan->nama;
                $validated['pimpinan_jabatan'] = $pimpinan->jabatan ?: 'Pimpinan Rapat';
            }
        }

        // Auto-resolve notulis nama jika dipilih dari guru
        if (!empty($validated['notulis_id'])) {
            $notulis = Guru::find($validated['notulis_id']);
            if ($notulis) {
                $validated['notulis_nama'] = $notulis->nama;
            }
        }

        // Auto nomor surat jika kosong
        if (empty($validated['nomor_surat'])) {
            $tahun = Carbon::parse($validated['tanggal_rapat'])->year;
            $countThisYear = SituanRapat::whereYear('tanggal_rapat', $tahun)->count() + 1;
            $paddedNo = str_pad($countThisYear, 3, '0', STR_PAD_LEFT);
            $validated['nomor_surat'] = "421.5/{$paddedNo}/V.01/DP.2/{$tahun}";
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'dijadwalkan';

        $rapat = SituanRapat::create($validated);

        AuditLog::catat('buat_rapat', 'situan', "Membuat agenda rapat: {$rapat->judul_rapat} (Tgl: {$rapat->tanggal_rapat->format('d/m/Y')})");

        return redirect()->route('situan.rapat.show', $rapat->id)
            ->with('success', 'Agenda rapat berhasil dibuat! Anda dapat langsung mencetak paket administrasi atau mengisi notula.');
    }

    public function show($id)
    {
        $rapat = SituanRapat::with(['pimpinan', 'notulis', 'creator'])->findOrFail($id);
        $pesertas = $rapat->getPesertaList();
        $gurus = Guru::where('status', 'aktif')->orderBy('nama')->get();
        $sekolah = PengaturanSekolah::getAktif();

        return view('situan.rapat.show', compact('rapat', 'pesertas', 'gurus', 'sekolah'));
    }

    public function update(Request $request, $id)
    {
        $rapat = SituanRapat::findOrFail($id);

        $validated = $request->validate([
            'judul_rapat'       => 'required|string|max:255',
            'tipe_rapat'        => 'required|string',
            'tanggal_rapat'     => 'required|date',
            'jam_mulai'         => 'required|string',
            'jam_selesai'       => 'nullable|string',
            'tempat'            => 'required|string|max:255',
            'nomor_surat'       => 'nullable|string|max:100',
            'agenda'            => 'nullable|string',
            'pimpinan_rapat_id' => 'nullable|exists:gurus,id',
            'pimpinan_nama'     => 'nullable|string|max:150',
            'pimpinan_jabatan'  => 'nullable|string|max:100',
            'notulis_id'        => 'nullable|exists:gurus,id',
            'notulis_nama'      => 'nullable|string|max:150',
            'peserta_tipe'      => 'required|string',
            'peserta_ids'       => 'nullable|array',
            'peserta_custom'    => 'nullable|string',
            'susunan_acara'     => 'nullable|string',
            'status'            => 'required|in:dijadwalkan,selesai,dibatalkan',
        ]);

        if (!empty($validated['pimpinan_rapat_id'])) {
            $pimpinan = Guru::find($validated['pimpinan_rapat_id']);
            if ($pimpinan) {
                $validated['pimpinan_nama'] = $pimpinan->nama;
                $validated['pimpinan_jabatan'] = $pimpinan->jabatan ?: 'Pimpinan Rapat';
            }
        }

        if (!empty($validated['notulis_id'])) {
            $notulis = Guru::find($validated['notulis_id']);
            if ($notulis) {
                $validated['notulis_nama'] = $notulis->nama;
            }
        }

        $rapat->update($validated);

        AuditLog::catat('update_rapat', 'situan', "Memperbarui agenda rapat: {$rapat->judul_rapat}");

        return redirect()->back()->with('success', 'Data agenda rapat berhasil diperbarui.');
    }

    public function updateNotula(Request $request, $id)
    {
        $rapat = SituanRapat::findOrFail($id);

        $validated = $request->validate([
            'jalannya_rapat'     => 'nullable|string',
            'keputusan_rapat'    => 'nullable|string',
            'berita_acara'       => 'nullable|string',
            'jumlah_hadir'       => 'nullable|integer|min:0',
            'jumlah_tidak_hadir' => 'nullable|integer|min:0',
            'status'             => 'required|in:dijadwalkan,selesai,dibatalkan',
        ]);

        $rapat->update($validated);

        AuditLog::catat('simpan_notula', 'situan', "Menyimpan notula & hasil rapat: {$rapat->judul_rapat}");

        return redirect()->back()->with('success', 'Notula & hasil keputusan rapat berhasil disimpan!');
    }

    public function destroy($id)
    {
        $rapat = SituanRapat::findOrFail($id);
        $judul = $rapat->judul_rapat;
        $rapat->delete();

        AuditLog::catat('hapus_rapat', 'situan', "Menghapus agenda rapat: {$judul}");

        return redirect()->route('situan.rapat.index')->with('success', 'Agenda rapat berhasil dihapus.');
    }

    // ── METODE CETAK DOKUMEN RESMI A4 KOP DINAS ──

    public function cetakUndangan($id)
    {
        $rapat = SituanRapat::with(['pimpinan', 'notulis'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();
        $kepsek = Guru::where('jabatan', 'like', '%kepala sekolah%')->first()
            ?? Guru::where('status', 'aktif')->first();

        return view('situan.rapat.cetak_undangan', compact('rapat', 'sekolah', 'kepsek'));
    }

    public function cetakDaftarHadir($id)
    {
        $rapat = SituanRapat::with(['pimpinan', 'notulis'])->findOrFail($id);
        $pesertas = $rapat->getPesertaList();
        $sekolah = PengaturanSekolah::getAktif();

        return view('situan.rapat.cetak_daftar_hadir', compact('rapat', 'pesertas', 'sekolah'));
    }

    public function cetakNotula($id)
    {
        $rapat = SituanRapat::with(['pimpinan', 'notulis'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        return view('situan.rapat.cetak_notula', compact('rapat', 'sekolah'));
    }

    public function cetakBeritaAcara($id)
    {
        $rapat = SituanRapat::with(['pimpinan', 'notulis'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();
        $pesertas = $rapat->getPesertaList();

        return view('situan.rapat.cetak_berita_acara', compact('rapat', 'sekolah', 'pesertas'));
    }

    public function cetakPaket($id)
    {
        $rapat = SituanRapat::with(['pimpinan', 'notulis'])->findOrFail($id);
        $pesertas = $rapat->getPesertaList();
        $sekolah = PengaturanSekolah::getAktif();
        $kepsek = Guru::where('jabatan', 'like', '%kepala sekolah%')->first()
            ?? Guru::where('status', 'aktif')->first();

        return view('situan.rapat.cetak_paket', compact('rapat', 'pesertas', 'sekolah', 'kepsek'));
    }
}
