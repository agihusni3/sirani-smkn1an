<?php

namespace App\Http\Controllers;

use App\Models\ArsipDokumenPtk;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\SuratKeluar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SituanKepegawaianController extends Controller
{
    /**
     * Tampilkan Radar Kenaikan Gaji Berkala (KGB) & Pangkat PTK.
     */
    public function radarKgbIndex()
    {
        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $today = Carbon::today();

        // Olah radar jatuh tempo KGB (2 tahun sekali) & Pangkat (4 tahun sekali)
        $radarKgb = $gurus->map(function ($guru) use ($today) {
            $tmtKgb = $guru->tmt_kgb_terakhir ? Carbon::parse($guru->tmt_kgb_terakhir) : null;
            $tmtPangkat = $guru->tmt_pangkat_terakhir ? Carbon::parse($guru->tmt_pangkat_terakhir) : null;

            // Jatuh tempo KGB berikutnya: TMT KGB + 2 Tahun
            $nextKgb = $tmtKgb ? (clone $tmtKgb)->addYears(2) : null;
            $daysToKgb = $nextKgb ? $today->diffInDays($nextKgb, false) : null;

            // Jatuh tempo Pangkat berikutnya: TMT Pangkat + 4 Tahun
            $nextPangkat = $tmtPangkat ? (clone $tmtPangkat)->addYears(4) : null;
            $daysToPangkat = $nextPangkat ? $today->diffInDays($nextPangkat, false) : null;

            // Status KGB
            $statusKgb = 'belum_set'; // jika data belum ada
            if ($nextKgb) {
                if ($daysToKgb <= 0) {
                    $statusKgb = 'jatuh_tempo'; // 🔴 Sudah lewat / bulan ini
                } elseif ($daysToKgb <= 60) {
                    $statusKgb = 'segera'; // 🟡 Mendekati dalam 60 hari
                } else {
                    $statusKgb = 'aman'; // 🟢 Masih panjang
                }
            }

            return [
                'guru'          => $guru,
                'tmtKgb'        => $tmtKgb,
                'nextKgb'       => $nextKgb,
                'daysToKgb'     => $daysToKgb,
                'statusKgb'     => $statusKgb,
                'tmtPangkat'    => $tmtPangkat,
                'nextPangkat'   => $nextPangkat,
                'daysToPangkat' => $daysToPangkat,
            ];
        });

        $countJatuhTempo = $radarKgb->where('statusKgb', 'jatuh_tempo')->count();
        $countSegera = $radarKgb->where('statusKgb', 'segera')->count();
        $countAman = $radarKgb->where('statusKgb', 'aman')->count();

        return view('situan.kepegawaian.radar_kgb', compact(
            'radarKgb',
            'countJatuhTempo',
            'countSegera',
            'countAman',
            'gurus'
        ));
    }

    /**
     * Update TMT KGB & Pangkat Guru secara cepat dari radar.
     */
    public function updateTmtGuru(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'golongan_ruang'      => 'nullable|string|max:30',
            'tmt_kgb_terakhir'    => 'nullable|date',
            'tmt_pangkat_terakhir'=> 'nullable|date',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'jurusan_pendidikan'  => 'nullable|string|max:100',
        ]);

        $guru->update([
            'golongan_ruang'      => $request->golongan_ruang,
            'tmt_kgb_terakhir'    => $request->tmt_kgb_terakhir,
            'tmt_pangkat_terakhir'=> $request->tmt_pangkat_terakhir,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'jurusan_pendidikan'  => $request->jurusan_pendidikan,
        ]);

        AuditLog::catat('update', 'situan_kepegawaian', "Memperbarui TMT KGB/Pangkat untuk {$guru->nama}");

        return back()->with('success', "Data KGB & Pangkat {$guru->nama} berhasil disimpan.");
    }

    /**
     * Draf Cetak Surat Pengantar Pengusulan KGB ke Dinas Pendidikan Provinsi.
     */
    public function cetakPengantarKgb($id)
    {
        $guru = Guru::findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();

        // Ambil dan sinkronisasi ke buku agenda surat keluar resmi SITUAN
        $suratKeluar = SuratKeluar::syncPengantarKgb($guru, now());
        $suratKeluar->ensureKodeVerifikasi();
        $verifyUrl = route('situan.verifikasi-surat', $suratKeluar->kode_verifikasi_qr);

        $generator = [
            'nomor_agenda'        => $suratKeluar->nomor_agenda,
            'tahun_agenda'        => $suratKeluar->tahun_agenda,
            'nomor_surat_lengkap' => $suratKeluar->nomor_surat_lengkap,
        ];

        return view('situan.kepegawaian.cetak_pengantar_kgb', compact('guru', 'sekolah', 'generator', 'suratKeluar', 'verifyUrl'));
    }

    /**
     * Tampilkan Lemari Berkas Digital PTK (E-Arsip).
     */
    public function arsipPtkIndex($guruId)
    {
        $guru = Guru::with('arsipDokumens')->findOrFail($guruId);
        $arsips = $guru->arsipDokumens()->latest()->get();

        return view('situan.kepegawaian.arsip_ptk', compact('guru', 'arsips'));
    }

    /**
     * Unggah Dokumen Arsip Digital PTK.
     */
    public function arsipPtkStore(Request $request, $guruId)
    {
        $guru = Guru::findOrFail($guruId);

        $request->validate([
            'kategori_berkas' => 'required|in:ktp,kk,sk_cpns,sk_pns,sk_pppk,sk_pangkat_terakhir,sk_kgb_terakhir,ijazah,transkrip,sertifikat_pendidik,sertifikat_pelatihan,kartu_pegawai,lainnya',
            'nama_dokumen'    => 'required|string|max:150',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'file_dokumen'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file_dokumen');
        $fileName = 'ptk_' . $guru->id . '_' . $request->kategori_berkas . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('arsip_ptk/' . $guru->id, $fileName, 'public');

        ArsipDokumenPtk::create([
            'guru_id'         => $guru->id,
            'kategori_berkas' => $request->kategori_berkas,
            'nama_dokumen'    => $request->nama_dokumen,
            'nomor_dokumen'   => $request->nomor_dokumen,
            'tanggal_dokumen' => $request->tanggal_dokumen,
            'file_path'       => $filePath,
        ]);

        if ($request->kategori_berkas === 'sertifikat_pelatihan') {
            \App\Models\SertifikatGuru::create([
                'guru_id'         => $guru->id,
                'nama_pelatihan'  => $request->nama_dokumen,
                'penyelenggara'   => 'Kementerian / Lembaga Pelatihan',
                'tahun'           => $request->tanggal_dokumen ? date('Y', strtotime($request->tanggal_dokumen)) : date('Y'),
                'file_sertifikat' => $filePath,
            ]);
        }

        AuditLog::catat('create', 'situan_arsip_ptk', "Mengunggah arsip digital {$request->nama_dokumen} untuk {$guru->nama}");

        return back()->with('success', "Dokumen {$request->nama_dokumen} berhasil diarsipkan.");
    }

    /**
     * Hapus Dokumen Arsip Digital PTK.
     */
    public function arsipPtkDestroy($id)
    {
        $arsip = ArsipDokumenPtk::findOrFail($id);
        $guruId = $arsip->guru_id;

        if ($arsip->kategori_berkas === 'sertifikat_pelatihan') {
            \App\Models\SertifikatGuru::where('guru_id', $arsip->guru_id)
                ->where('file_sertifikat', $arsip->file_path)
                ->delete();
        }

        if ($arsip->file_path && Storage::disk('public')->exists($arsip->file_path)) {
            Storage::disk('public')->delete($arsip->file_path);
        }

        $arsip->delete();

        AuditLog::catat('delete', 'situan_arsip_ptk', "Menghapus arsip digital ID {$id}");

        return back()->with('success', 'Dokumen berhasil dihapus dari arsip.');
    }
}
