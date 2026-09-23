<?php

namespace App\Http\Controllers\Situan;

use App\Http\Controllers\Controller;

use App\Models\SuratTugas;
use App\Models\SuratTugasAnggota;
use App\Models\Sppd;
use App\Models\SuratKeluar;
use App\Models\KlasifikasiSurat;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\ArsipDokumenPtk;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SituanSuratTugasController extends Controller
{
    /**
     * Tampilan Indeks Daftar Surat Tugas & SPPD Terpadu.
     */
    public function index(Request $request)
    {
        $thisYear = (int) ($request->get('tahun') ?: now()->year);
        $q = trim($request->get('q', ''));
        $status = $request->get('status');

        $query = SuratTugas::with(['suratKeluar', 'anggotas.guru', 'sppds'])
            ->whereYear('tanggal_mulai', $thisYear);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_surat_tugas', 'like', "%{$q}%")
                    ->orWhere('maksud_tugas', 'like', "%{$q}%")
                    ->orWhere('tempat_tujuan', 'like', "%{$q}%")
                    ->orWhere('lokasi_spesifik', 'like', "%{$q}%")
                    ->orWhereHas('anggotas', function ($aq) use ($q) {
                        $aq->where('nama', 'like', "%{$q}%")
                           ->orWhere('nip', 'like', "%{$q}%");
                    });
            });
        }

        if ($status && in_array($status, ['draf', 'disetujui', 'selesai', 'dibatalkan'])) {
            $query->where('status', $status);
        }

        $suratTugasList = $query->orderBy('tanggal_mulai', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistik
        $totalTugas = SuratTugas::whereYear('tanggal_mulai', $thisYear)->count();
        $totalPersonil = SuratTugasAnggota::whereHas('suratTugas', function ($q) use ($thisYear) {
            $q->whereYear('tanggal_mulai', $thisYear);
        })->count();
        $totalSppd = Sppd::whereYear('tanggal_berangkat', $thisYear)->count();

        // Data Master Guru untuk Form Modal (Auto-fill)
        $gurus = Guru::where('status', 'aktif')
            ->select(['id', 'nama', 'gelar_depan', 'gelar_belakang', 'nip', 'jabatan', 'golongan_pangkat', 'golongan_ruang'])
            ->orderBy('nama')
            ->get()
            ->map(function ($g) {
                $pangkatGrup = trim(($g->golongan_pangkat ?? '') . ' ' . ($g->golongan_ruang ? '(' . $g->golongan_ruang . ')' : ''));
                return [
                    'id'               => $g->id,
                    'nama'             => $g->nama_lengkap_gelar ?: $g->nama,
                    'nip'              => $g->nip ?: '-',
                    'jabatan'          => $g->jabatan ?: 'Guru Mata Pelajaran',
                    'pangkat_golongan' => $pangkatGrup ?: '-',
                ];
            });

        // Pratinjau nomor berikutnya
        $previewNomor = SuratKeluar::generateNomorSurat('094');

        return view('situan.tugas.index', compact(
            'suratTugasList',
            'thisYear',
            'q',
            'status',
            'totalTugas',
            'totalPersonil',
            'totalSppd',
            'gurus',
            'previewNomor'
        ));
    }

    /**
     * Simpan Surat Tugas Baru & Generate SPPD Otomatis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'maksud_tugas'      => 'required|string|max:1000',
            'tempat_berangkat'  => 'required|string|max:255',
            'tempat_tujuan'     => 'required|string|max:255',
            'lokasi_spesifik'   => 'nullable|string|max:255',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'anggotas'          => 'required|array|min:1',
            'anggotas.*.nama'   => 'required|string|max:255',
        ], [
            'maksud_tugas.required'      => 'Maksud tugas / perihal dinas wajib diisi.',
            'tempat_tujuan.required'     => 'Tempat/Kota tujuan wajib diisi.',
            'tanggal_mulai.required'     => 'Tanggal mulai tugas wajib ditentukan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'anggotas.required'          => 'Minimal harus ada 1 personil yang ditugaskan.',
        ]);

        return DB::transaction(function () use ($request) {
            $tglMulai = Carbon::parse($request->tanggal_mulai);
            $tglSelesai = Carbon::parse($request->tanggal_selesai);
            $lamaHari = $tglMulai->diffInDays($tglSelesai) + 1;

            // 1. Dapatkan Data Kepala Sekolah / Penandatangan
            $sekolah = PengaturanSekolah::getAktif();
            $kepsek = Guru::where('jabatan', 'like', '%Kepala Sekolah%')
                ->orWhere('tugas_tambahan', 'like', '%Kepala Sekolah%')
                ->orWhere('nama', 'like', '%Aprida%')
                ->first();

            $namaKepsek = $kepsek ? ($kepsek->nama_lengkap_gelar ?: $kepsek->nama) : ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.');
            $nipKepsek  = $kepsek && !empty($kepsek->nip) ? $kepsek->nip : ($sekolah->nip_kepala_sekolah ?: '197904172008012019');
            $pangkatKepsek = $kepsek ? ($kepsek->golongan_pangkat . ' ' . ($kepsek->golongan_ruang ? '(' . $kepsek->golongan_ruang . ')' : '')) : 'Pembina Tk. I (IV/b)';

            // 2. Kunci & Terbitkan Nomor Surat Keluar Resmi
            $kodeKlasifikasi = $request->get('kode_klasifikasi', '094'); // 094: Perjalanan Dinas / Perjalanan Tugas
            $gen = SuratKeluar::generateNomorSurat($kodeKlasifikasi, $tglMulai);
            $klasifikasi = KlasifikasiSurat::where('kode', $kodeKlasifikasi)->first();

            $firstAnggotaNama = $request->anggotas[0]['nama'] ?? 'Personil';
            $countAnggota = count($request->anggotas);
            $ringkasanPenerima = $countAnggota > 1 ? "{$firstAnggotaNama} dkk ({$countAnggota} Orang)" : $firstAnggotaNama;

            $suratKeluar = SuratKeluar::create([
                'nomor_agenda'        => $gen['nomor_agenda'],
                'tahun_agenda'        => $gen['tahun_agenda'],
                'klasifikasi_id'      => $klasifikasi?->id,
                'kode_klasifikasi'    => $kodeKlasifikasi,
                'nomor_surat_lengkap' => $gen['nomor_surat_lengkap'],
                'tujuan_surat'        => $ringkasanPenerima,
                'perihal'             => "Surat Perintah Tugas: " . Str::limit($request->maksud_tugas, 120),
                'tanggal_surat'       => $tglMulai->toDateString(),
                'penandatangan'       => 'Kepala Sekolah',
                'jabatan_penandatangan' => 'Kepala SMK Negeri 1 Air Naningan',
                'nip_penandatangan'   => $nipKepsek,
                'jenis_surat'         => 'surat_tugas',
                'sumber_modul'        => 'situan_tugas',
                'kategori_surat'      => 'Surat Tugas & SPPD',
                'created_by'          => auth()->id(),
            ]);

            // 3. Simpan Data Surat Perintah Tugas
            $suratTugas = SuratTugas::create([
                'surat_keluar_id'       => $suratKeluar->id,
                'nomor_surat_tugas'     => $gen['nomor_surat_lengkap'],
                'kode_klasifikasi'      => $kodeKlasifikasi,
                'dasar_penugasan'       => $request->dasar_penugasan ?: 'Program Kerja Sekolah & Kalender Pendidikan SMK Negeri 1 Air Naningan',
                'maksud_tugas'          => $request->maksud_tugas,
                'tempat_berangkat'      => $request->tempat_berangkat ?: 'Air Naningan, Tanggamus',
                'tempat_tujuan'         => $request->tempat_tujuan,
                'lokasi_spesifik'       => $request->lokasi_spesifik,
                'tanggal_mulai'         => $tglMulai->toDateString(),
                'tanggal_selesai'       => $tglSelesai->toDateString(),
                'lama_hari'             => $lamaHari,
                'alat_transportasi'     => $request->alat_transportasi ?: 'Kendaraan Dinas / Pribadi',
                'sumber_anggaran'       => $request->sumber_anggaran ?: 'BOS Reguler SMK Negeri 1 Air Naningan',
                'pejabat_penandatangan' => 'Kepala Sekolah',
                'nama_pejabat'          => $namaKepsek,
                'nip_pejabat'           => $nipKepsek,
                'pangkat_pejabat'       => $pangkatKepsek,
                'jabatan_pejabat'       => 'Kepala SMK Negeri 1 Air Naningan',
                'status'                => 'disetujui',
                'catatan'               => $request->catatan,
                'created_by'            => auth()->id(),
            ]);

            // Sinkronkan link cetak di SuratKeluar
            $suratKeluar->update([
                'link_cetak' => route('situan.surat-tugas.cetak-surat', $suratTugas->id),
            ]);

            // 4. Simpan Personil & Terbitkan SPPD Jika Diaktifkan
            $terbitkanSppd = $request->boolean('terbitkan_sppd', true);
            $tingkatBiaya = $request->get('tingkat_biaya', 'Tingkat C');
            $mataAnggaran = $request->get('mata_anggaran', 'BOS Reguler / BOPD');

            $nextUrutSppd = Sppd::whereYear('created_at', $tglMulai->year)->count();

            foreach ($request->anggotas as $idx => $item) {
                $guruId = !empty($item['guru_id']) ? (int)$item['guru_id'] : null;
                $peran = $idx === 0 ? ($item['peran'] ?? 'Ketua Rombongan') : ($item['peran'] ?? 'Anggota');

                $anggota = SuratTugasAnggota::create([
                    'surat_tugas_id'   => $suratTugas->id,
                    'guru_id'          => $guruId,
                    'nama'             => $item['nama'],
                    'nip'              => $item['nip'] ?? null,
                    'pangkat_golongan' => $item['pangkat_golongan'] ?? null,
                    'jabatan'          => $item['jabatan'] ?? null,
                    'peran'            => $peran,
                    'keterangan'       => $item['keterangan'] ?? null,
                    'urutan'           => $idx + 1,
                ]);

                // Jika terbitkan SPPD
                if ($terbitkanSppd) {
                    $nextUrutSppd++;
                    $padSppd = str_pad((string)$nextUrutSppd, 3, '0', STR_PAD_LEFT);
                    $romawiMonth = SuratKeluar::romawiBulan((int)$tglMulai->format('n'));
                    $nomorSppd = "094/SPPD.{$padSppd}/SMKN1AN/{$romawiMonth}/{$tglMulai->year}";

                    Sppd::create([
                        'surat_tugas_id'             => $suratTugas->id,
                        'surat_tugas_anggota_id'     => $anggota->id,
                        'guru_id'                    => $guruId,
                        'nomor_sppd'                 => $nomorSppd,
                        'nama_pelaksana'             => $item['nama'],
                        'nip_pelaksana'              => $item['nip'] ?? null,
                        'pangkat_golongan'           => $item['pangkat_golongan'] ?? null,
                        'jabatan'                    => $item['jabatan'] ?? null,
                        'tingkat_biaya'              => $tingkatBiaya,
                        'maksud_perjalanan'          => $request->maksud_tugas,
                        'alat_angkut'                => $request->alat_transportasi ?: 'Kendaraan Dinas / Pribadi',
                        'tempat_berangkat'           => $request->tempat_berangkat ?: 'Air Naningan, Tanggamus',
                        'tempat_tujuan'              => $request->tempat_tujuan . ($request->lokasi_spesifik ? " ({$request->lokasi_spesifik})" : ''),
                        'lama_perjalanan'            => $lamaHari,
                        'tanggal_berangkat'          => $tglMulai->toDateString(),
                        'tanggal_harus_kembali'      => $tglSelesai->toDateString(),
                        'instansi_pembeban_anggaran' => 'SMK Negeri 1 Air Naningan',
                        'mata_anggaran'              => $mataAnggaran,
                        'keterangan_lain'            => $request->catatan,
                        'status'                     => 'terbit',
                        'created_by'                 => auth()->id(),
                    ]);
                }

                // Otomatis sinkron arsip digital PTK jika personil adalah Guru/Pegawai sekolah
                if ($guruId) {
                    try {
                        ArsipDokumenPtk::create([
                            'guru_id'         => $guruId,
                            'kategori_berkas' => 'Surat Tugas & SPPD',
                            'nama_dokumen'    => 'Surat Tugas: ' . Str::limit($request->maksud_tugas, 80),
                            'nomor_dokumen'   => $suratTugas->nomor_surat_tugas,
                            'tanggal_dokumen' => $tglMulai->toDateString(),
                        ]);
                    } catch (\Throwable $e) {
                        // Abaikan jika tabel belum ada atau skip
                    }
                }
            }

            AuditLog::catat(
                'create',
                'situan_surat_tugas',
                "Menerbitkan Surat Tugas No. {$suratTugas->nomor_surat_tugas} ({$ringkasanPenerima}) tujuan {$request->tempat_tujuan}"
            );

            return redirect()->route('situan.surat-tugas.index')
                ->with('success', "Surat Perintah Tugas No. {$suratTugas->nomor_surat_tugas} berhasil diterbitkan" . ($terbitkanSppd ? " beserta lembar SPPD!" : "!"));
        });
    }

    /**
     * Hapus Surat Tugas beserta anggotanya dan nomor agenda terkait.
     */
    public function destroy($id)
    {
        $suratTugas = SuratTugas::with(['suratKeluar', 'sppds', 'anggotas'])->findOrFail($id);
        $nomor = $suratTugas->nomor_surat_tugas;

        DB::transaction(function () use ($suratTugas) {
            if ($suratTugas->suratKeluar) {
                $suratTugas->suratKeluar->delete();
            }
            $suratTugas->delete();
        });

        AuditLog::catat('delete', 'situan_surat_tugas', "Menghapus Surat Perintah Tugas No. {$nomor}");

        return redirect()->route('situan.surat-tugas.index')
            ->with('success', "Surat Perintah Tugas No. {$nomor} berhasil dihapus.");
    }

    /**
     * Cetak Lembar Surat Perintah Tugas (SPT) Resmi Format A4.
     */
    public function cetakSuratTugas($id)
    {
        $suratTugas = SuratTugas::with(['suratKeluar', 'anggotas.guru'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        // Data Penandatangan
        $namaKepsek = $suratTugas->nama_pejabat ?: ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.');
        $nipKepsek  = $suratTugas->nip_pejabat ?: ($sekolah->nip_kepala_sekolah ?: '197904172008012019');
        $pangkatKepsek = $suratTugas->pangkat_pejabat ?: 'Pembina Tk. I (IV/b)';

        $verifyUrl = route('situan.verifikasi-surat', $suratTugas->kode_verifikasi_qr);
        $qrImage = $this->generateQrImage($verifyUrl);

        return view('situan.tugas.cetak_surat_tugas', compact(
            'suratTugas',
            'sekolah',
            'namaKepsek',
            'nipKepsek',
            'pangkatKepsek',
            'verifyUrl',
            'qrImage'
        ));
    }

    /**
     * Cetak Lembar SPPD (Lembar I Perincian & Lembar II Visum Tiba/Berangkat).
     */
    public function cetakSppd(Request $request, $id, $sppdId = null)
    {
        $suratTugas = SuratTugas::with(['suratKeluar', 'anggotas.guru', 'sppds'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        // Jika sppdId spesifik diberikan, ambil satu. Jika tidak, ambil yang pertama
        $sppd = $sppdId 
            ? Sppd::where('surat_tugas_id', $suratTugas->id)->findOrFail($sppdId)
            : $suratTugas->sppds()->firstOrFail();

        $namaKepsek = $suratTugas->nama_pejabat ?: ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.');
        $nipKepsek  = $suratTugas->nip_pejabat ?: ($sekolah->nip_kepala_sekolah ?: '197904172008012019');
        $pangkatKepsek = $suratTugas->pangkat_pejabat ?: 'Pembina Tk. I (IV/b)';

        $verifyUrl = route('situan.verifikasi-surat', $suratTugas->kode_verifikasi_qr);
        $qrImage = $this->generateQrImage($verifyUrl);

        return view('situan.tugas.cetak_sppd', compact(
            'suratTugas',
            'sppd',
            'sekolah',
            'namaKepsek',
            'nipKepsek',
            'pangkatKepsek',
            'verifyUrl',
            'qrImage'
        ));
    }

    /**
     * Cetak Paket Lengkap 3-in-1 (Surat Tugas + SPPD Lembar I + SPPD Lembar II Visum) Sekali Klik.
     */
    public function cetakPaket(Request $request, $id)
    {
        $suratTugas = SuratTugas::with(['suratKeluar', 'anggotas.guru', 'sppds'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        $namaKepsek = $suratTugas->nama_pejabat ?: ($sekolah->nama_kepala_sekolah ?: 'Aprida, S.Si.');
        $nipKepsek  = $suratTugas->nip_pejabat ?: ($sekolah->nip_kepala_sekolah ?: '197904172008012019');
        $pangkatKepsek = $suratTugas->pangkat_pejabat ?: 'Pembina Tk. I (IV/b)';

        $verifyUrl = route('situan.verifikasi-surat', $suratTugas->kode_verifikasi_qr);
        $qrImage = $this->generateQrImage($verifyUrl);

        return view('situan.tugas.cetak_paket', compact(
            'suratTugas',
            'sekolah',
            'namaKepsek',
            'nipKepsek',
            'pangkatKepsek',
            'verifyUrl',
            'qrImage'
        ));
    }

    /**
     * Generator QR Code Base64 Helper.
     */
    private function generateQrImage(string $text): ?string
    {
        try {
            $qrOptions = new \chillerlan\QRCode\QROptions([
                'outputType'  => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'    => \chillerlan\QRCode\QRCode::ECC_M,
                'scale'       => 4,
                'imageBase64' => true,
            ]);
            return (new \chillerlan\QRCode\QRCode($qrOptions))->render($text);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
