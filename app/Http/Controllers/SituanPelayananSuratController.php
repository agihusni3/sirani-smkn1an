<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\PelayananSurat;
use App\Models\PengaturanSekolah;
use App\Models\Siswa;
use App\Models\SuratKeluar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SituanPelayananSuratController extends Controller
{
    /**
     * Tampilkan Loket Pelayanan Surat Mandiri Kesiswaan.
     */
    public function index(Request $request)
    {
        $query = PelayananSurat::with(['siswa.siswaRombels.rombel', 'suratKeluar', 'creator'])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('siswa', function ($s) use ($q) {
                $s->where('nama', 'like', "%{$q}%")
                  ->orWhere('nisn', 'like', "%{$q}%")
                  ->orWhere('nis', 'like', "%{$q}%");
            })->orWhere('keperluan', 'like', "%{$q}%")
              ->orWhere('kode_verifikasi_qr', 'like', "%{$q}%");
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_pelayanan', $request->jenis);
        }

        $pelayanans = $query->paginate(15)->withQueryString();

        // Cari siswa untuk autocomplete/select
        $allSiswaAktif = Siswa::whereIn('status', ['aktif', 'pkl'])
            ->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel');
            }])
            ->orderBy('nama')
            ->get(['id', 'nama', 'nisn', 'nis', 'status']);

        $totalSuratDiterbitkan = PelayananSurat::count();
        $totalSuketAktif = PelayananSurat::where('jenis_pelayanan', 'suket_aktif')->count();
        $totalMutasi = PelayananSurat::where('jenis_pelayanan', 'suket_mutasi_keluar')->count();

        return view('situan.pelayanan.index', compact(
            'pelayanans',
            'allSiswaAktif',
            'totalSuratDiterbitkan',
            'totalSuketAktif',
            'totalMutasi'
        ));
    }

    /**
     * Buat dan Terbitkan Surat Keterangan Kesiswaan Otomatis (One-Click).
     */
    public function buatSurat(Request $request)
    {
        $request->validate([
            'siswa_id'        => 'required|exists:siswas,id',
            'jenis_pelayanan' => 'required|in:suket_aktif,suket_berkelakuan_baik,suket_mutasi_keluar,suket_skl,suket_pengantar_pkl',
            'keperluan'       => 'required|string|max:255',
            'tanggal_surat'   => 'required|date',
            'alasan_mutasi'   => 'nullable|string|max:255',
            'sekolah_tujuan'  => 'nullable|string|max:200',
        ]);

        $siswa = Siswa::with(['siswaRombels' => function ($q) {
            $q->where('status_keanggotaan', 'aktif')->with('rombel');
        }])->findOrFail($request->siswa_id);

        $tanggal = Carbon::parse($request->tanggal_surat);
        $kodeKlasifikasi = '422.4'; // Kode standar Surat Keterangan / Mutasi Siswa

        // 1. Generate nomor agenda surat keluar otomatis
        $generator = SuratKeluar::generateNomorSurat($kodeKlasifikasi, $tanggal);

        $namaJenis = match ($request->jenis_pelayanan) {
            'suket_aktif'            => 'Surat Keterangan Siswa Aktif',
            'suket_berkelakuan_baik' => 'Surat Keterangan Berkelakuan Baik',
            'suket_mutasi_keluar'    => 'Surat Rekomendasi Pindah Sekolah',
            'suket_skl'              => 'Surat Keterangan Lulus Sementara',
            'suket_pengantar_pkl'    => 'Surat Pengantar PKL Industri',
            default                  => 'Surat Keterangan Kesiswaan',
        };

        // 2. Simpan ke Buku Agenda Surat Keluar TU
        $suratKeluar = SuratKeluar::create([
            'nomor_agenda'        => $generator['nomor_agenda'],
            'tahun_agenda'        => $generator['tahun_agenda'],
            'kode_klasifikasi'    => $kodeKlasifikasi,
            'nomor_surat_lengkap' => $generator['nomor_surat_lengkap'],
            'tujuan_surat'        => $siswa->nama . ' (NISN: ' . ($siswa->nisn ?: '-') . ')',
            'perihal'             => "{$namaJenis} a.n {$siswa->nama} ({$request->keperluan})",
            'tanggal_surat'       => $request->tanggal_surat,
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'suket_siswa',
            'created_by'          => auth()->id(),
        ]);

        // 3. Simpan snapshot data siswa agar historis akurat
        $rombelAktif = $siswa->siswaRombels->first()?->rombel?->nama_rombel ?? 'Tidak Terdaftar';
        $snapshot = [
            'nama'           => $siswa->nama,
            'nis'            => $siswa->nis,
            'nisn'           => $siswa->nisn,
            'tempat_lahir'   => $siswa->tempat_lahir,
            'tanggal_lahir'  => $siswa->tanggal_lahir ? Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-',
            'jenis_kelamin'  => $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'rombel'         => $rombelAktif,
            'nama_ortu'      => $siswa->nama_ortu ?: ($siswa->nama_ayah ?: ($siswa->nama_ibu ?: '-')),
            'alamat'         => $siswa->alamat ?: '-',
            'keperluan'      => $request->keperluan,
            'alasan_mutasi'  => $request->alasan_mutasi,
            'sekolah_tujuan' => $request->sekolah_tujuan,
        ];

        // 4. Catat ke Pelayanan Surat
        $pelayanan = PelayananSurat::create([
            'surat_keluar_id'    => $suratKeluar->id,
            'siswa_id'           => $siswa->id,
            'jenis_pelayanan'    => $request->jenis_pelayanan,
            'keperluan'          => $request->keperluan,
            'kode_verifikasi_qr' => Str::random(32),
            'is_valid'           => true,
            'payload_snapshot'   => $snapshot,
            'created_by'         => auth()->id(),
        ]);

        AuditLog::catat('create', 'situan_pelayanan_surat', "Menerbitkan {$namaJenis} No. {$suratKeluar->nomor_surat_lengkap} untuk {$siswa->nama}");

        return redirect()->route('situan.pelayanan.cetak', $pelayanan->id)
            ->with('success', "Surat berhasil diterbitkan dengan nomor resmi: {$suratKeluar->nomor_surat_lengkap}");
    }

    /**
     * Tampilan Cetak Resmi A4 Lengkap dengan Kop Surat & QR Code Validasi.
     */
    public function cetakSurat($id)
    {
        $pelayanan = PelayananSurat::with(['siswa', 'suratKeluar', 'creator'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        // Cari data Kepala Sekolah langsung dari Data PTK (Tabel Guru)
        $kepsek = Guru::where('jabatan', 'like', '%Kepala Sekolah%')
            ->orWhere('tugas_tambahan', 'like', '%Kepala Sekolah%')
            ->orWhere('nama', 'like', '%Aprida%')
            ->first();

        $namaKepsek = $kepsek ? ($kepsek->nama_lengkap_gelar ?: $kepsek->nama) : ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.');
        $nipKepsek  = $kepsek && !empty($kepsek->nip) ? $kepsek->nip : ($sekolah->nip_kepala_sekolah ?: '197904172008012019');

        $sekolah->nama_kepala_sekolah = $namaKepsek;
        $sekolah->nip_kepala_sekolah  = $nipKepsek;

        $verifyUrl = route('situan.verifikasi-surat', $pelayanan->kode_verifikasi_qr);
        $qrImage = null;
        try {
            $qrOptions = new \chillerlan\QRCode\QROptions([
                'outputType'  => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'    => \chillerlan\QRCode\QRCode::ECC_M,
                'scale'       => 4,
                'imageBase64' => true,
            ]);
            $qrImage = (new \chillerlan\QRCode\QRCode($qrOptions))->render($verifyUrl);
        } catch (\Throwable $e) {
            // Fallback
            $qrImage = null;
        }

        return view('situan.pelayanan.cetak_surat', compact('pelayanan', 'sekolah', 'verifyUrl', 'qrImage', 'namaKepsek', 'nipKepsek'));
    }

    /**
     * Halaman Publik Verifikasi Keabsahan Dokumen via Scan QR Code.
     * (Dapat diakses oleh instansi luar / bank / dinas tanpa login).
     */
    public function verifikasiSuratPublik($hash)
    {
        $pelayanan = PelayananSurat::with(['siswa', 'suratKeluar'])
            ->where('kode_verifikasi_qr', $hash)
            ->first();

        $suratKeluar = null;
        if ($pelayanan) {
            $suratKeluar = $pelayanan->suratKeluar;
        } else {
            $suratKeluar = SuratKeluar::with(['creator', 'pelayanans.siswa'])
                ->where('kode_verifikasi_qr', $hash)
                ->first();

            if (!$suratKeluar) {
                $suratTugas = \App\Models\SuratTugas::where('kode_verifikasi_qr', $hash)->first();
                if ($suratTugas && $suratTugas->surat_keluar_id) {
                    $suratKeluar = SuratKeluar::with(['creator', 'pelayanans.siswa'])->find($suratTugas->surat_keluar_id);
                }
            }
        }

        $sekolah = PengaturanSekolah::getAktif();

        $kepsek = Guru::where('jabatan', 'like', '%Kepala Sekolah%')
            ->orWhere('tugas_tambahan', 'like', '%Kepala Sekolah%')
            ->orWhere('nama', 'like', '%Aprida%')
            ->first();

        $namaKepsek = $kepsek ? ($kepsek->nama_lengkap_gelar ?: $kepsek->nama) : ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.');
        $nipKepsek  = $kepsek && !empty($kepsek->nip) ? $kepsek->nip : ($sekolah->nip_kepala_sekolah ?: '197904172008012019');

        $sekolah->nama_kepala_sekolah = $namaKepsek;
        $sekolah->nip_kepala_sekolah  = $nipKepsek;

        return view('situan.pelayanan.verifikasi_publik', compact('pelayanan', 'suratKeluar', 'hash', 'sekolah', 'namaKepsek', 'nipKepsek'));
    }

    /**
     * Perbarui data pelayanan surat siswa (Edit/Update).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id'        => 'required|exists:siswas,id',
            'jenis_pelayanan' => 'required|in:suket_aktif,suket_berkelakuan_baik,suket_mutasi_keluar,suket_skl,suket_pengantar_pkl',
            'keperluan'       => 'required|string|max:255',
            'tanggal_surat'   => 'required|date',
            'alasan_mutasi'   => 'nullable|string|max:255',
            'sekolah_tujuan'  => 'nullable|string|max:200',
        ]);

        $pelayanan = PelayananSurat::with(['suratKeluar'])->findOrFail($id);

        $siswa = Siswa::with(['siswaRombels' => function ($q) {
            $q->where('status_keanggotaan', 'aktif')->with('rombel');
        }])->findOrFail($request->siswa_id);

        $namaJenis = match ($request->jenis_pelayanan) {
            'suket_aktif'            => 'Surat Keterangan Siswa Aktif',
            'suket_berkelakuan_baik' => 'Surat Keterangan Berkelakuan Baik',
            'suket_mutasi_keluar'    => 'Surat Rekomendasi Pindah Sekolah',
            'suket_skl'              => 'Surat Keterangan Lulus Sementara',
            'suket_pengantar_pkl'    => 'Surat Pengantar PKL Industri',
            default                  => 'Surat Keterangan Kesiswaan',
        };

        $tanggal = Carbon::parse($request->tanggal_surat);
        $kodeKlasifikasi = '422.4';

        // Perbarui atau sinkronkan Surat Keluar
        if ($pelayanan->suratKeluar) {
            $pelayanan->suratKeluar->update([
                'tujuan_surat'  => $siswa->nama . ' (NISN: ' . ($siswa->nisn ?: '-') . ')',
                'perihal'       => "{$namaJenis} a.n {$siswa->nama} ({$request->keperluan})",
                'tanggal_surat' => $request->tanggal_surat,
            ]);
        } else {
            // Jika belum ada nomor agenda surat keluar (misal data legacy), buatkan otomatis
            $generator = SuratKeluar::generateNomorSurat($kodeKlasifikasi, $tanggal);
            $suratKeluar = SuratKeluar::create([
                'nomor_agenda'        => $generator['nomor_agenda'],
                'tahun_agenda'        => $generator['tahun_agenda'],
                'kode_klasifikasi'    => $kodeKlasifikasi,
                'nomor_surat_lengkap' => $generator['nomor_surat_lengkap'],
                'tujuan_surat'        => $siswa->nama . ' (NISN: ' . ($siswa->nisn ?: '-') . ')',
                'perihal'             => "{$namaJenis} a.n {$siswa->nama} ({$request->keperluan})",
                'tanggal_surat'       => $request->tanggal_surat,
                'penandatangan'       => 'Kepala Sekolah',
                'jenis_surat'         => 'suket_siswa',
                'created_by'          => auth()->id(),
            ]);
            $pelayanan->surat_keluar_id = $suratKeluar->id;
        }

        $rombelAktif = $siswa->siswaRombels->first()?->rombel?->nama_rombel ?? 'Tidak Terdaftar';
        $snapshot = [
            'nama'           => $siswa->nama,
            'nis'            => $siswa->nis,
            'nisn'           => $siswa->nisn,
            'tempat_lahir'   => $siswa->tempat_lahir,
            'tanggal_lahir'  => $siswa->tanggal_lahir ? Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-',
            'jenis_kelamin'  => $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'rombel'         => $rombelAktif,
            'nama_ortu'      => $siswa->nama_ortu ?: ($siswa->nama_ayah ?: ($siswa->nama_ibu ?: '-')),
            'alamat'         => $siswa->alamat ?: '-',
            'keperluan'      => $request->keperluan,
            'alasan_mutasi'  => $request->alasan_mutasi,
            'sekolah_tujuan' => $request->sekolah_tujuan,
        ];

        $pelayanan->update([
            'siswa_id'         => $siswa->id,
            'jenis_pelayanan'  => $request->jenis_pelayanan,
            'keperluan'        => $request->keperluan,
            'payload_snapshot' => $snapshot,
        ]);

        AuditLog::catat('update', 'situan_pelayanan_surat', "Memperbarui data {$namaJenis} untuk {$siswa->nama}");

        return redirect()->route('situan.pelayanan.index')
            ->with('success', "Pelayanan surat untuk {$siswa->nama} berhasil diperbarui.");
    }

    /**
     * Hapus pelayanan surat siswa beserta surat keluar terkait (Delete/Destroy).
     */
    public function destroy($id)
    {
        $pelayanan = PelayananSurat::with('suratKeluar')->findOrFail($id);
        $namaSiswa = $pelayanan->siswa->nama ?? ($pelayanan->payload_snapshot['nama'] ?? 'Siswa');
        $nomorSurat = $pelayanan->suratKeluar?->nomor_surat_lengkap ?? 'Tanpa Nomor Agenda';

        if ($pelayanan->suratKeluar) {
            $suratKeluar = $pelayanan->suratKeluar;
            $pelayanan->delete();
            $suratKeluar->delete();
        } else {
            $pelayanan->delete();
        }

        AuditLog::catat('delete', 'situan_pelayanan_surat', "Menghapus pelayanan surat ({$nomorSurat}) an {$namaSiswa}");

        return redirect()->route('situan.pelayanan.index')
            ->with('success', "Pelayanan surat untuk {$namaSiswa} ({$nomorSurat}) berhasil dihapus.");
    }

    /**
     * Generate nomor agenda surat keluar otomatis jika sebelumnya pending/kosong.
     */
    public function generateNomorSurat($id)
    {
        $pelayanan = PelayananSurat::with(['siswa', 'suratKeluar'])->findOrFail($id);
        if ($pelayanan->surat_keluar_id && $pelayanan->suratKeluar) {
            return redirect()->route('situan.pelayanan.index')
                ->with('info', "Surat ini sudah memiliki nomor agenda resmi: {$pelayanan->suratKeluar->nomor_surat_lengkap}");
        }

        $tanggal = $pelayanan->created_at ? Carbon::parse($pelayanan->created_at) : now();
        $kodeKlasifikasi = '422.4';
        $generator = SuratKeluar::generateNomorSurat($kodeKlasifikasi, $tanggal);

        $namaSiswa = $pelayanan->siswa?->nama ?? ($pelayanan->payload_snapshot['nama'] ?? 'Siswa');
        $nisn = $pelayanan->siswa?->nisn ?? ($pelayanan->payload_snapshot['nisn'] ?? '-');
        $namaJenis = $pelayanan->jenis_label;

        $suratKeluar = SuratKeluar::create([
            'nomor_agenda'        => $generator['nomor_agenda'],
            'tahun_agenda'        => $generator['tahun_agenda'],
            'kode_klasifikasi'    => $kodeKlasifikasi,
            'nomor_surat_lengkap' => $generator['nomor_surat_lengkap'],
            'tujuan_surat'        => "{$namaSiswa} (NISN: {$nisn})",
            'perihal'             => "{$namaJenis} a.n {$namaSiswa} ({$pelayanan->keperluan})",
            'tanggal_surat'       => $tanggal->format('Y-m-d'),
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'suket_siswa',
            'created_by'          => auth()->id(),
        ]);

        $pelayanan->update(['surat_keluar_id' => $suratKeluar->id]);

        AuditLog::catat('create', 'situan_pelayanan_surat', "Generate nomor agenda resmi No. {$suratKeluar->nomor_surat_lengkap} untuk {$namaSiswa}");

        return redirect()->route('situan.pelayanan.index')
            ->with('success', "Nomor agenda resmi berhasil digenerate: {$suratKeluar->nomor_surat_lengkap}");
    }
}
