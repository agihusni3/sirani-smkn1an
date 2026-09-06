<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\PengaturanSekolah;
use App\Models\PpdbPendaftar;
use App\Models\PpdbUjianPeserta;
use App\Models\PpdbUjianSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PpdbUjianController extends Controller
{
    /**
     * Halaman Konfirmasi & Petunjuk Pra-Ujian
     */
    public function konfirmasi($nomor)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'ujianPeserta'])
            ->where('no_pendaftaran', $nomor)
            ->firstOrFail();

        // Validasi kelayakan peserta: Harus sudah terverifikasi berkasnya
        $statusValid = in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'diterima', 'siap_tes']);
        if (!$statusValid) {
            return redirect()->route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran])
                ->with('error', 'Berkas pendaftaran Anda belum terverifikasi oleh Panitia. Anda belum dapat mengikuti ujian seleksi.');
        }

        $setting = PpdbUjianSetting::getAktif();
        if (!$setting || !$setting->is_active) {
            return redirect()->route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran])
                ->with('warning', 'Sesi Ujian Seleksi Tes Tertulis PPDB saat ini belum dibuka atau sedang ditutup oleh Panitia.');
        }

        $peserta = $pendaftar->ujianPeserta;
        if ($peserta && in_array($peserta->status_pengerjaan, ['selesai', 'selesai_menunggu_koreksi', 'selesai_dinilai'])) {
            return redirect()->route('ppdb.ujian.selesai', ['nomor' => $pendaftar->no_pendaftaran]);
        }

        return view('ppdb.ujian.konfirmasi', compact('sekolah', 'pendaftar', 'setting', 'peserta'));
    }

    /**
     * Ruang Ujian CBT Split-Screen (PDF Naskah + Lembar Jawab ABC & Esai)
     */
    public function kerjakan($nomor)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'ujianPeserta'])
            ->where('no_pendaftaran', $nomor)
            ->firstOrFail();

        $statusValid = in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'diterima', 'siap_tes']);
        if (!$statusValid) {
            return redirect()->route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran])
                ->with('error', 'Akses ditolak: Berkas Anda belum terverifikasi.');
        }

        $setting = PpdbUjianSetting::getAktif();
        if (!$setting || !$setting->is_active) {
            return redirect()->route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran])
                ->with('warning', 'Sesi ujian belum aktif.');
        }

        // Dapatkan atau buat entri peserta ujian
        $peserta = PpdbUjianPeserta::firstOrCreate(
            ['ppdb_pendaftar_id' => $pendaftar->id],
            [
                'ppdb_ujian_setting_id' => $setting->id,
                'waktu_mulai'           => now(),
                'status_pengerjaan'     => 'sedang_mengerjakan',
                'jawaban_pg'            => [],
                'jawaban_esai'          => [],
            ]
        );

        if (in_array($peserta->status_pengerjaan, ['selesai', 'selesai_menunggu_koreksi', 'selesai_dinilai'])) {
            return redirect()->route('ppdb.ujian.selesai', ['nomor' => $pendaftar->no_pendaftaran]);
        }

        if (!$peserta->waktu_mulai) {
            $peserta->waktu_mulai = now();
            $peserta->status_pengerjaan = 'sedang_mengerjakan';
            $peserta->save();
        }

        // Hitung sisa waktu ujian (detik)
        $durasiMenit = (int) ($setting->durasi_menit ?: 60);
        $batasWaktu = Carbon::parse($peserta->waktu_mulai)->addMinutes($durasiMenit);
        $sisaDetik = max(0, now()->diffInSeconds($batasWaktu, false));

        if ($sisaDetik <= 0) {
            // Waktu habis otomatis, selesaikan ujian
            return $this->autoSelesai($pendaftar, $peserta, $setting);
        }

        $jumlahPg = (int) ($setting->jumlah_soal_pg ?: 30);
        $jumlahEsai = (int) ($setting->jumlah_soal_esai ?: 5);

        return view('ppdb.ujian.kerjakan', compact(
            'sekolah',
            'pendaftar',
            'setting',
            'peserta',
            'sisaDetik',
            'jumlahPg',
            'jumlahEsai'
        ));
    }

    /**
     * Simpan Draft Jawaban PG dan Esai secara Berkala via AJAX
     */
    public function simpanDraft(Request $request, $nomor)
    {
        $pendaftar = PpdbPendaftar::where('no_pendaftaran', $nomor)->firstOrFail();
        $peserta = PpdbUjianPeserta::where('ppdb_pendaftar_id', $pendaftar->id)->firstOrFail();

        if (in_array($peserta->status_pengerjaan, ['selesai', 'selesai_menunggu_koreksi', 'selesai_dinilai'])) {
            return response()->json(['status' => 'already_finished', 'message' => 'Ujian telah selesai dikirim.'], 400);
        }

        $jawabanPg = $request->input('jawaban_pg', []);
        $jawabanEsai = $request->input('jawaban_esai', []);

        $peserta->jawaban_pg = $jawabanPg;
        $peserta->jawaban_esai = $jawabanEsai;
        $peserta->save();

        return response()->json([
            'status'    => 'success',
            'message'   => 'Draft jawaban berhasil disimpan otomatis.',
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Submit Akhir Jawaban Ujian oleh Peserta
     */
    public function selesai(Request $request, $nomor)
    {
        $pendaftar = PpdbPendaftar::where('no_pendaftaran', $nomor)->firstOrFail();
        $peserta = PpdbUjianPeserta::where('ppdb_pendaftar_id', $pendaftar->id)->firstOrFail();
        $setting = PpdbUjianSetting::getAktif();

        if (in_array($peserta->status_pengerjaan, ['selesai', 'selesai_menunggu_koreksi', 'selesai_dinilai'])) {
            return redirect()->route('ppdb.ujian.selesai', ['nomor' => $pendaftar->no_pendaftaran]);
        }

        $jawabanPg = $request->input('jawaban_pg', $peserta->jawaban_pg ?: []);
        $jawabanEsai = $request->input('jawaban_esai', $peserta->jawaban_esai ?: []);

        // Koreksi otomatis 30 soal Pilihan Ganda
        $koreksi = $setting ? $setting->koreksiPg($jawabanPg) : [
            'benar' => 0, 'salah' => 0, 'kosong' => 0, 'skor_pg' => 0.00
        ];

        $peserta->jawaban_pg = $jawabanPg;
        $peserta->jawaban_esai = $jawabanEsai;
        $peserta->jumlah_pg_benar = $koreksi['benar'];
        $peserta->jumlah_pg_salah = $koreksi['salah'];
        $peserta->jumlah_pg_kosong = $koreksi['kosong'];
        $peserta->nilai_pg = $koreksi['skor_pg'];
        $peserta->waktu_selesai = now();
        $peserta->status_pengerjaan = 'selesai_menunggu_koreksi';
        $peserta->sinkronNilaiKePendaftar();

        return redirect()->route('ppdb.ujian.selesai', ['nomor' => $pendaftar->no_pendaftaran])
            ->with('success', 'Ujian Anda telah berhasil dikirim dan tersimpan di sistem!');
    }

    /**
     * Halaman Bukti Selesai Ujian
     */
    public function halamanSelesai($nomor)
    {
        $sekolah = PengaturanSekolah::getAktif();
        $pendaftar = PpdbPendaftar::with(['jurusanPilihan1', 'jurusanPilihan2', 'ujianPeserta.setting'])
            ->where('no_pendaftaran', $nomor)
            ->firstOrFail();

        $peserta = $pendaftar->ujianPeserta;

        return view('ppdb.ujian.selesai', compact('sekolah', 'pendaftar', 'peserta'));
    }

    /**
     * Helper internal auto-selesai saat waktu habis
     */
    protected function autoSelesai(PpdbPendaftar $pendaftar, PpdbUjianPeserta $peserta, PpdbUjianSetting $setting)
    {
        $jawabanPg = $peserta->jawaban_pg ?: [];
        $koreksi = $setting->koreksiPg($jawabanPg);

        $peserta->jumlah_pg_benar = $koreksi['benar'];
        $peserta->jumlah_pg_salah = $koreksi['salah'];
        $peserta->jumlah_pg_kosong = $koreksi['kosong'];
        $peserta->nilai_pg = $koreksi['skor_pg'];
        $peserta->waktu_selesai = now();
        $peserta->status_pengerjaan = 'selesai_menunggu_koreksi';
        $peserta->sinkronNilaiKePendaftar();

        return redirect()->route('ppdb.ujian.selesai', ['nomor' => $pendaftar->no_pendaftaran])
            ->with('warning', 'Waktu ujian telah habis. Seluruh jawaban Anda telah dikirim dan dikoreksi secara otomatis.');
    }
}
