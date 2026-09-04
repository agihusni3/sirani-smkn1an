<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\PengaturanSekolah;
use App\Models\PpdbPendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PpdbDaftarController extends Controller
{
    /**
     * Halaman Informasi Utama PPDB Online
     */
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        $jurusans = Jurusan::all();
        $totalPendaftar = PpdbPendaftar::count();
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);

        return view('ppdb.index', compact('sekolah', 'jurusans', 'totalPendaftar', 'tahunAjaran'));
    }

    /**
     * Formulir Pendaftaran Siswa Baru
     */
    public function formulir()
    {
        $sekolah = PengaturanSekolah::getAktif();
        $jurusans = Jurusan::all();
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);

        return view('ppdb.formulir', compact('sekolah', 'jurusans', 'tahunAjaran'));
    }

    /**
     * Simpan Data Pendaftaran Calon Siswa Baru
     */
    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|size:10|unique:ppdb_pendaftars,nisn',
            'nik' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'asal_sekolah' => 'required|string|max:150',
            'tahun_lulus' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'alamat_lengkap' => 'required|string',
            'nama_ayah' => 'nullable|string|max:150',
            'nama_ibu' => 'required|string|max:150',
            'no_hp_siswa' => 'nullable|string|max:20',
            'no_hp_ortu' => 'required|string|max:20',
            'jurusan_pilihan_1_id' => 'required|exists:jurusans,id',
            'jurusan_pilihan_2_id' => 'nullable|different:jurusan_pilihan_1_id|exists:jurusans,id',
            'jalur_pendaftaran' => 'required|in:reguler,prestasi,afirmasi',
            'pas_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'scan_kk' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'scan_ijazah_skl' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'scan_ktp_ortu' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'scan_akta' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'scan_kip' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
            'scan_sktm' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:3072',
        ], [
            'nisn.required' => 'NISN wajib diisi (10 digit).',
            'nisn.size' => 'NISN harus tepat 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah terdaftar dalam sistem PPDB. Silakan cek status pendaftaran Anda.',
            'nama_lengkap.required' => 'Nama lengkap calon siswa wajib diisi.',
            'jurusan_pilihan_1_id.required' => 'Pilihan keahlian utama wajib dipilih.',
            'no_hp_ortu.required' => 'Nomor WhatsApp / HP Orang Tua wajib diisi untuk konfirmasi.',
        ]);

        $p = new PpdbPendaftar();
        $p->no_pendaftaran = PpdbPendaftar::generateNomor();
        $p->nisn = $validated['nisn'];
        $p->nik = $validated['nik'] ?? null;
        $p->nama_lengkap = $validated['nama_lengkap'];
        $p->jenis_kelamin = $validated['jenis_kelamin'];
        $p->tempat_lahir = $validated['tempat_lahir'];
        $p->tanggal_lahir = $validated['tanggal_lahir'];
        $p->agama = $validated['agama'] ?? 'Islam';
        $p->asal_sekolah = $validated['asal_sekolah'];
        $p->tahun_lulus = (string) $validated['tahun_lulus'];
        $p->alamat = $validated['alamat_lengkap'];
        $p->nama_ayah = $validated['nama_ayah'] ?? null;
        $p->nama_ibu = $validated['nama_ibu'];
        $p->no_hp_ortu = $validated['no_hp_ortu'];
        $p->no_hp_siswa = $validated['no_hp_siswa'] ?? null;
        $p->jurusan_id_1 = $validated['jurusan_pilihan_1_id'];
        $p->jurusan_id_2 = $validated['jurusan_pilihan_2_id'] ?? null;
        $p->jalur_pendaftaran = $validated['jalur_pendaftaran'];
        $p->status = 'menunggu_verifikasi';

        // Upload berkas jika ada
        if ($request->hasFile('pas_foto')) {
            $p->berkas_foto = $request->file('pas_foto')->store('ppdb/foto', 'public');
        }
        if ($request->hasFile('scan_kk')) {
            $p->berkas_kk = $request->file('scan_kk')->store('ppdb/berkas', 'public');
        }
        if ($request->hasFile('scan_ijazah_skl')) {
            $p->berkas_ijazah_skl = $request->file('scan_ijazah_skl')->store('ppdb/berkas', 'public');
        }
        if ($request->hasFile('scan_ktp_ortu')) {
            $p->berkas_ktp_ortu = $request->file('scan_ktp_ortu')->store('ppdb/berkas', 'public');
        }
        if ($request->hasFile('scan_akta')) {
            $p->berkas_akta = $request->file('scan_akta')->store('ppdb/berkas', 'public');
        }
        if ($request->hasFile('scan_kip')) {
            $p->berkas_kip = $request->file('scan_kip')->store('ppdb/berkas', 'public');
        }
        if ($request->hasFile('scan_sktm')) {
            $p->berkas_sktm = $request->file('scan_sktm')->store('ppdb/berkas', 'public');
        }

        $p->save();

        return redirect()->route('ppdb.sukses', ['nomor' => $p->no_pendaftaran])
            ->with('success', 'Pendaftaran PPDB Berhasil Disimpan!');
    }

    /**
     * Halaman Konfirmasi Sukses Pendaftaran
     */
    public function sukses($nomor)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2'])
            ->where('no_pendaftaran', $nomor)
            ->firstOrFail();

        return view('ppdb.sukses', compact('sekolah', 'pendaftar'));
    }

    /**
     * Cek Status Pendaftaran & Verifikasi Mandiri
     */
    public function status(Request $request)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $pendaftar = null;

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima'])
                ->where('no_pendaftaran', $keyword)
                ->orWhere('nisn', $keyword)
                ->first();
        }

        return view('ppdb.status', compact('sekolah', 'pendaftar'));
    }

    /**
     * Cetak Bukti / Kartu Tanda Peserta PPDB
     */
    public function cetakKartu($nomor)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'jurusanDiterima'])
            ->where('no_pendaftaran', $nomor)
            ->firstOrFail();

        return view('ppdb.kartu_cetak', compact('sekolah', 'pendaftar'));
    }
}
