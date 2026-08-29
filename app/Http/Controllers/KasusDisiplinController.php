<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\KasusDisiplin;
use App\Models\KasusDisiplinDokumen;
use App\Models\KasusDisiplinLog;
use App\Models\KasusDisiplinPelanggaran;
use App\Models\KasusDisiplinReward;
use App\Models\KatalogPelanggaran;
use App\Models\KatalogReward;
use App\Models\NotifikasiOrtu;
use App\Models\PengaturanDisiplin;
use App\Models\PengaturanNotifikasi;
use App\Models\PengaturanSekolah;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\DisiplinNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KasusDisiplinController extends Controller
{
    /**
     * Tampilkan buku catatan kasus & penegakan kedisiplinan berjenjang.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $taAktif = TahunAjaran::where('is_active', true)->first();
        $pengaturanDisiplin = PengaturanDisiplin::getPengaturan();
        KatalogPelanggaran::seedDefault();
        $katalogRewards = KatalogReward::orderBy('kategori')->orderBy('nama_reward')->get();
        $katalogPelanggarans = KatalogPelanggaran::orderBy('kategori')->orderBy('nama_pelanggaran')->get();

        // 1. Auto-sync siswa baru yang memiliki pelanggaran dan belum tercatat di tabel kasus disiplin
        $existingKasusIds = KasusDisiplin::where('is_active', true)->pluck('siswa_id')->toArray();
        $siswaPerluSync = Absensi::where('pemilik_type', 'siswa')
            ->whereIn('status', ['alpha', 'bolos'])
            ->whereNotIn('pemilik_id', $existingKasusIds)
            ->selectRaw('pemilik_id, count(*) as total')
            ->groupBy('pemilik_id')
            ->having('total', '>=', 1)
            ->limit(20)
            ->pluck('pemilik_id');

        foreach ($siswaPerluSync as $siswaId) {
            KasusDisiplin::syncFromPresensi($siswaId);
        }

        // 2. Query Utama Kasus Disiplin
        $query = KasusDisiplin::with(['siswa.siswaRombels.rombel.waliKelas', 'siswa.siswaRombels.rombel.jurusan', 'dokumens', 'rewards', 'pelanggarans'])
            ->where('is_active', true)
            ->forUser($user);

        // Filter Tahap
        $tahapFilter = $request->input('tahap');
        if ($tahapFilter && in_array($tahapFilter, ['tahap_1_wali_kelas', 'tahap_2_bk', 'tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan'])) {
            $query->where('status_tahap', $tahapFilter);
        }

        // Filter Rombel
        $rombelId = $request->input('rombel_id');
        if ($rombelId) {
            $query->whereHas('siswa.siswaRombels', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            });
        }

        // Search Siswa
        $search = $request->input('search');
        if ($search) {
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $kasusList = $query->orderByRaw("CASE status_tahap 
                WHEN 'tahap_4_kepsek' THEN 1 
                WHEN 'tahap_3_wakasis' THEN 2 
                WHEN 'tahap_2_bk' THEN 3 
                WHEN 'tahap_1_wali_kelas' THEN 4 
                ELSE 5 END ASC")
            ->orderBy('total_alpha', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 3. Statistik Hitungan per Tahap (Scoped for user)
        $baseStatQuery = KasusDisiplin::where('is_active', true)->forUser($user);
        $statTahap1    = (clone $baseStatQuery)->where('status_tahap', 'tahap_1_wali_kelas')->count();
        $statTahap2    = (clone $baseStatQuery)->where('status_tahap', 'tahap_2_bk')->count();
        $statTahap3    = (clone $baseStatQuery)->where('status_tahap', 'tahap_3_wakasis')->count();
        $statTahap4    = (clone $baseStatQuery)->where('status_tahap', 'tahap_4_kepsek')->count();
        $statSelesai   = (clone $baseStatQuery)->where('status_tahap', 'selesai_pembinaan')->count();
        $totalKasus    = (clone $baseStatQuery)->count();

        $rombels = Rombel::orderBy('tingkat')->orderBy('nama_rombel')->get();

        if ($user->isWaliKelas() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isWakaKesiswaan() && !$user->isGuruBk()) {
            $rombelIds = $user->guru ? $user->guru->rombels()->pluck('id') : collect();
            $allSiswa = Siswa::where('status', 'aktif')
                ->whereHas('siswaRombels', function ($q) use ($rombelIds) {
                    $q->whereIn('rombel_id', $rombelIds)->where('status_keanggotaan', 'aktif');
                })
                ->orderBy('nama')
                ->get();
        } else {
            $allSiswa = Siswa::where('status', 'aktif')->orderBy('nama')->get();
        }

        return view('disiplin.index', compact(
            'kasusList',
            'statTahap1',
            'statTahap2',
            'statTahap3',
            'statTahap4',
            'statSelesai',
            'totalKasus',
            'rombels',
            'allSiswa',
            'tahapFilter',
            'rombelId',
            'search',
            'user',
            'pengaturanDisiplin',
            'katalogRewards',
            'katalogPelanggarans'
        ));
    }

    /**
     * Helper validasi hak akses perwalian untuk Wali Kelas.
     */
    private function checkAksesKasus($user, KasusDisiplin $kasus): void
    {
        if ($user->isWaliKelas() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isWakaKesiswaan() && !$user->isGuruBk()) {
            $rombelIds = $user->guru ? $user->guru->rombels()->pluck('id')->toArray() : [];
            $siswaRombelId = $kasus->siswa->siswaRombels()->where('status_keanggotaan', 'aktif')->value('rombel_id');
            if (!in_array($siswaRombelId, $rombelIds)) {
                abort(403, 'Akses ditolak: Anda hanya berwenang mengakses data siswa di kelas yang Anda walikan.');
            }
        }
    }

    /**
     * Helper validasi eksklusivitas wewenang tahap per role.
     */
    private function validateWewenangTahap($user, string $currentTahap, ?string $tahapBaru = null): void
    {
        if ($user->isAdmin()) {
            return; // Admin memiliki wewenang penuh
        }

        // 1. Validasi Akses Berdasarkan Status Tahap Saat Ini
        if ($currentTahap === 'tahap_1_wali_kelas') {
            if (!$user->isWaliKelas()) {
                abort(403, 'Akses ditolak: Tahap 1 (Peringatan & Pembinaan Internal) eksklusif wewenang Wali Kelas.');
            }
        } elseif ($currentTahap === 'tahap_2_bk') {
            if (!$user->isGuruBk()) {
                abort(403, 'Akses ditolak: Tahap 2 (Konseling & Panggilan Orang Tua) eksklusif wewenang Guru BK.');
            }
        } elseif ($currentTahap === 'tahap_3_wakasis') {
            if (!$user->isWakaKesiswaan()) {
                abort(403, 'Akses ditolak: Tahap 3 (Sidang Disiplin & Perjanjian SP 2) eksklusif wewenang Waka Kesiswaan.');
            }
        } elseif ($currentTahap === 'tahap_4_kepsek') {
            if (!$user->isKepalaSekolah()) {
                abort(403, 'Akses ditolak: Tahap 4 (Keputusan Akhir & Peringatan Terakhir SP 3) eksklusif wewenang Kepala Sekolah.');
            }
        }

        // 2. Validasi Hak Eskalasi Tahap Baru
        if ($tahapBaru) {
            if ($user->isWaliKelas()) {
                if (!in_array($tahapBaru, ['tahap_1_wali_kelas', 'tahap_2_bk', 'selesai_pembinaan'])) {
                    abort(403, 'Akses ditolak: Wali Kelas hanya berwenang membina di Tahap 1, menyelesaikan, atau mengeskalasikan ke Tahap 2 (Guru BK).');
                }
            } elseif ($user->isGuruBk()) {
                if (!in_array($tahapBaru, ['tahap_2_bk', 'tahap_3_wakasis', 'selesai_pembinaan'])) {
                    abort(403, 'Akses ditolak: Guru BK hanya berwenang membina di Tahap 2, menyelesaikan, atau mengeskalasikan ke Tahap 3 (Waka Kesiswaan).');
                }
            } elseif ($user->isWakaKesiswaan()) {
                if (!in_array($tahapBaru, ['tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan'])) {
                    abort(403, 'Akses ditolak: Waka Kesiswaan berwenang memproses Tahap 3, menyelesaikan, atau meneruskan ke Tahap 4 (Kepala Sekolah).');
                }
            } elseif ($user->isKepalaSekolah()) {
                if (!in_array($tahapBaru, ['tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan'])) {
                    abort(403, 'Akses ditolak: Kepala Sekolah berwenang memproses Keputusan Tahap 4 atau Menyelesaikan Pembinaan.');
                }
            }
        }
    }

    /**
     * Tampilkan halaman Dossier / Berkas Kasus Siswa Lengkap.
     */
    public function show($id)
    {
        $kasus = KasusDisiplin::with([
            'siswa.siswaRombels.rombel.waliKelas',
            'siswa.siswaRombels.rombel.jurusan',
            'logs',
            'dokumens',
            'rewards.katalogReward',
            'pelanggarans.katalogPelanggaran',
            'tahunAjaran'
        ])->findOrFail($id);

        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $siswa = $kasus->siswa;

        // Riwayat Presensi Siswa Semester Berjalan
        $absensiList = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->take(30)
            ->get();

        $totalHadir = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalSemua = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->count();
        $persenKehadiran = $totalSemua > 0 ? round(($totalHadir / $totalSemua) * 100, 1) : 100;

        // Riwayat Notifikasi WhatsApp Siswa & Kasus Disiplin
        $notifikasiList = NotifikasiOrtu::where('siswa_id', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $katalogRewards = KatalogReward::where('is_active', true)->orderBy('kategori')->orderBy('nama_reward')->get();
        $katalogPelanggarans = KatalogPelanggaran::where('is_active', true)->orderBy('kategori')->orderBy('nama_pelanggaran')->get();
        $pengaturanDisiplin = PengaturanDisiplin::getPengaturan();

        return view('disiplin.show', compact('kasus', 'siswa', 'user', 'absensiList', 'persenKehadiran', 'notifikasiList', 'katalogRewards', 'katalogPelanggarans', 'pengaturanDisiplin'));
    }

    /**
     * Input / Daftarkan kasus disiplin siswa baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'     => 'required|exists:siswas,id',
            'status_tahap' => 'required|in:tahap_1_wali_kelas,tahap_2_bk,tahap_3_wakasis,tahap_4_kepsek',
            'catatan'      => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $siswaId = (int) $request->input('siswa_id');
        $statusTahap = $request->input('status_tahap');

        // Validasi Otoritas Role saat Pendaftaran Kasus Baru
        if ($user->isWaliKelas() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isWakaKesiswaan() && !$user->isGuruBk()) {
            $rombelIds = $user->guru ? $user->guru->rombels()->pluck('id')->toArray() : [];
            $siswa = Siswa::findOrFail($siswaId);
            $siswaRombelId = $siswa->siswaRombels()->where('status_keanggotaan', 'aktif')->value('rombel_id');
            if (!in_array($siswaRombelId, $rombelIds)) {
                abort(403, 'Akses ditolak: Anda hanya berwenang mendaftarkan kasus bagi siswa di kelas yang Anda walikan.');
            }
            if ($statusTahap !== 'tahap_1_wali_kelas' && $statusTahap !== 'tahap_2_bk') {
                abort(403, 'Akses ditolak: Wali Kelas hanya berwenang mendaftarkan kasus pada Tahap 1 (Wali Kelas).');
            }
        } elseif ($user->isGuruBk() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isWakaKesiswaan()) {
            if ($statusTahap !== 'tahap_2_bk' && $statusTahap !== 'tahap_1_wali_kelas') {
                abort(403, 'Akses ditolak: Guru BK hanya berwenang mendaftarkan kasus pada Tahap 2 (Guru BK).');
            }
        } elseif ($user->isWakaKesiswaan() && !$user->isAdmin() && !$user->isKepalaSekolah()) {
            if ($statusTahap !== 'tahap_3_wakasis') {
                abort(403, 'Akses ditolak: Waka Kesiswaan berwenang mendaftarkan kasus pada Tahap 3 (Waka Kesiswaan).');
            }
        } elseif ($user->isKepalaSekolah() && !$user->isAdmin()) {
            if ($statusTahap !== 'tahap_4_kepsek') {
                abort(403, 'Akses ditolak: Kepala Sekolah berwenang mendaftarkan kasus pada Tahap 4 (Kepala Sekolah).');
            }
        }

        $kasus = KasusDisiplin::syncFromPresensi($siswaId);

        $kasus->status_tahap      = $statusTahap;
        $kasus->diverifikasi_oleh = $user->name ?? 'Petugas';
        $today                    = Carbon::today()->toDateString();

        if ($kasus->status_tahap === 'tahap_1_wali_kelas') {
            $kasus->catatan_wali_kelas  = $request->input('catatan');
            $kasus->tanggal_tindak_wali = $today;
        } elseif ($kasus->status_tahap === 'tahap_2_bk') {
            $kasus->catatan_bk           = $request->input('catatan');
            $kasus->tanggal_panggilan_bk = $today;
        } elseif ($kasus->status_tahap === 'tahap_3_wakasis') {
            $kasus->catatan_wakasis        = $request->input('catatan');
            $kasus->tanggal_sidang_wakasis = $today;
        } elseif ($kasus->status_tahap === 'tahap_4_kepsek') {
            $kasus->keputusan_kepsek         = $request->input('catatan');
            $kasus->tanggal_keputusan_kepsek = $today;
        }

        $poinAwal = (int) $request->input('poin_awal', 10);
        if ($poinAwal > 0) {
            $kasus->total_poin_pelanggaran += $poinAwal;
        }

        $kasus->save();

        // Rekam log pertama
        KasusDisiplinLog::create([
            'kasus_disiplin_id' => $kasus->id,
            'tahap'             => $kasus->status_tahap,
            'judul_kegiatan'    => $request->input('judul_kegiatan') ?: 'Pendaftaran Kasus Baru',
            'uraian_tindakan'   => $request->input('catatan'),
            'poin_perubahan'    => $poinAwal,
            'petugas_nama'      => $user->name ?? 'Petugas',
            'petugas_role'      => $user->role_display_name ?? 'Staf',
            'tanggal_kegiatan'  => $today,
        ]);

        // Kirim notifikasi WA eskalasi otomatis ke pejabat terkait & orang tua
        DisiplinNotificationService::kirimAlertEskalasi($kasus, $kasus->status_tahap, $request->input('catatan'));

        return redirect()->route('admin.disiplin.show', $kasus->id)
            ->with('success', 'Kasus kedisiplinan siswa berhasil didaftarkan ke buku kasus.');
    }

    /**
     * Catat log interaksi / peristiwa kronologis baru ke dalam timeline siswa.
     */
    public function storeLog(Request $request, $id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);
        $this->validateWewenangTahap($user, $kasus->status_tahap);

        $request->validate([
            'judul_kegiatan'   => 'required|string|max:255',
            'uraian_tindakan'  => 'required|string|max:2000',
            'tahap'            => 'required|string',
            'poin_perubahan'   => 'nullable|integer',
            'tanggal_kegiatan' => 'required|date',
        ]);

        $poin = (int) $request->input('poin_perubahan', 0);

        KasusDisiplinLog::create([
            'kasus_disiplin_id' => $kasus->id,
            'tahap'             => $request->input('tahap'),
            'judul_kegiatan'    => $request->input('judul_kegiatan'),
            'uraian_tindakan'   => $request->input('uraian_tindakan'),
            'poin_perubahan'    => $poin,
            'petugas_nama'      => $user->name ?? 'Petugas',
            'petugas_role'      => $user->role_display_name ?? 'Staf',
            'tanggal_kegiatan'  => $request->input('tanggal_kegiatan'),
        ]);

        // Perbarui akumulasi poin jika ada perubahan
        if ($poin > 0) {
            $kasus->increment('total_poin_pelanggaran', $poin);
        } elseif ($poin < 0) {
            $kasus->increment('total_poin_pemulihan', abs($poin));
        }

        $kasus->touch();

        return back()->with('success', 'Catatan log interaksi berhasil ditambahkan ke timeline kasus.');
    }

    /**
     * Upload berkas bukti fisik digital (Scan Surat Pernyataan / Foto Home Visit / dll).
     */
    public function uploadDokumen(Request $request, $id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);
        $this->validateWewenangTahap($user, $kasus->status_tahap);

        $request->validate([
            'judul_dokumen' => 'required|string|max:255',
            'kategori'      => 'required|in:surat_pernyataan,foto_dokumentasi,berita_acara,surat_dokter,lainnya',
            'file'          => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $path     = $file->storeAs('bukti_disiplin', $fileName, 'public');

            KasusDisiplinDokumen::create([
                'kasus_disiplin_id' => $kasus->id,
                'judul_dokumen'     => $request->input('judul_dokumen'),
                'kategori'          => $request->input('kategori'),
                'file_path'         => $path,
                'file_type'         => $file->getMimeType(),
                'diupload_oleh'     => $user->name ?? 'Petugas',
            ]);

            return back()->with('success', 'Dokumen bukti fisik berhasil diunggah ke brankas digital.');
        }

        return back()->with('error', 'Gagal mengunggah berkas.');
    }

    /**
     * Hapus berkas bukti dokumen.
     */
    public function hapusDokumen($id, $dokumenId)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);
        $this->validateWewenangTahap($user, $kasus->status_tahap);

        $dokumen = KasusDisiplinDokumen::where('kasus_disiplin_id', $id)->findOrFail($dokumenId);

        if (Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();
        return back()->with('success', 'Dokumen bukti berhasil dihapus dari brankas.');
    }

    /**
     * Tindak lanjut & eskalasi kasus disiplin berjenjang.
     */
    public function tindakLanjut(Request $request, $id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $request->validate([
            'status_tahap_baru' => 'required|in:tahap_1_wali_kelas,tahap_2_bk,tahap_3_wakasis,tahap_4_kepsek,selesai_pembinaan',
            'catatan_tindakan'  => 'required|string|max:1000',
            'sanksi_tambahan'   => 'nullable|string|max:500',
        ]);

        $tahapBaru = $request->input('status_tahap_baru');

        // Validasi Wewenang Berjenjang: Tahap 1 (Wali Kelas) -> Tahap 2 (BK) -> Tahap 3 (Wakasis) -> Tahap 4 (Kepsek)
        $this->validateWewenangTahap($user, $kasus->status_tahap, $tahapBaru);

        $catatan   = $request->input('catatan_tindakan');
        $sanksi    = $request->input('sanksi_tambahan');
        $petugas   = $user->name ?? 'Petugas';
        $today     = Carbon::today()->toDateString();

        // Rekam catatan sesuai tahap
        if ($tahapBaru === 'tahap_1_wali_kelas') {
            $kasus->catatan_wali_kelas = $catatan;
            $kasus->tanggal_tindak_wali = $today;
        } elseif ($tahapBaru === 'tahap_2_bk') {
            $kasus->catatan_bk = $catatan;
            $kasus->tanggal_panggilan_bk = $today;
            if ($request->filled('hasil_musyawarah')) {
                $kasus->hasil_musyawarah_bk = $request->input('hasil_musyawarah');
            }
        } elseif ($tahapBaru === 'tahap_3_wakasis') {
            $kasus->catatan_wakasis = $catatan;
            $kasus->sanksi_wakasis  = $sanksi ?: $catatan;
            $kasus->tanggal_sidang_wakasis = $today;
        } elseif ($tahapBaru === 'tahap_4_kepsek') {
            $kasus->keputusan_kepsek = $catatan;
            $kasus->tanggal_keputusan_kepsek = $today;
        } elseif ($tahapBaru === 'selesai_pembinaan') {
            if ($kasus->status_tahap === 'tahap_1_wali_kelas') $kasus->catatan_wali_kelas = ($kasus->catatan_wali_kelas ? $kasus->catatan_wali_kelas . "\n" : '') . "[SELESAI]: " . $catatan;
            if ($kasus->status_tahap === 'tahap_2_bk') $kasus->hasil_musyawarah_bk = $catatan;
            if ($kasus->status_tahap === 'tahap_3_wakasis') $kasus->sanksi_wakasis = $catatan;
            if ($kasus->status_tahap === 'tahap_4_kepsek') $kasus->keputusan_kepsek = $catatan;
        }

        $kasus->status_tahap      = $tahapBaru;
        $kasus->diverifikasi_oleh = $petugas;
        $kasus->save();

        // Rekam ke timeline logs
        KasusDisiplinLog::create([
            'kasus_disiplin_id' => $kasus->id,
            'tahap'             => $tahapBaru,
            'judul_kegiatan'    => 'Tindak Lanjut & Eskalasi: ' . str_replace('_', ' ', strtoupper($tahapBaru)),
            'uraian_tindakan'   => $catatan . ($sanksi ? " (Sanksi: {$sanksi})" : ''),
            'poin_perubahan'    => 0,
            'petugas_nama'      => $petugas,
            'petugas_role'      => auth()->user()->role_display_name ?? 'Staf',
            'tanggal_kegiatan'  => $today,
        ]);

        // Kirim notifikasi WA eskalasi otomatis ke pejabat jenjang berikutnya & orang tua
        DisiplinNotificationService::kirimAlertEskalasi($kasus, $tahapBaru, $catatan);

        return redirect()->route('admin.disiplin.show', $kasus->id)
            ->with('success', "Tindak lanjut kasus siswa {$kasus->siswa->nama} berhasil diperbarui.");
    }

    /**
     * Selesaikan pembinaan kasus siswa.
     */
    public function selesaikan($id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);
        $this->validateWewenangTahap($user, $kasus->status_tahap, 'selesai_pembinaan');

        $kasus->status_tahap = 'selesai_pembinaan';
        $kasus->diverifikasi_oleh = $user->name ?? 'Petugas';
        $kasus->save();

        KasusDisiplinLog::create([
            'kasus_disiplin_id' => $kasus->id,
            'tahap'             => 'selesai_pembinaan',
            'judul_kegiatan'    => 'Penyelesaian Pembinaan Siswa',
            'uraian_tindakan'   => 'Siswa telah menyelesaikan masa pembinaan kedisiplinan dan status tata tertib dinyatakan pulih.',
            'poin_perubahan'    => -20,
            'petugas_nama'      => $user->name ?? 'Petugas',
            'petugas_role'      => $user->role_display_name ?? 'Staf',
            'tanggal_kegiatan'  => Carbon::today()->toDateString(),
        ]);

        return back()->with('success', "Masa pembinaan kedisiplinan {$kasus->siswa->nama} telah ditandai SELESAI.");
    }

    /**
     * Cetak Lembar Dokumen Resmi "Resume Yuridis Rekam Jejak Siswa" (Format A4 untuk Kepala Sekolah).
     */
    public function cetakResume($id)
    {
        $kasus = KasusDisiplin::with([
            'siswa.siswaRombels.rombel.waliKelas',
            'siswa.siswaRombels.rombel.jurusan',
            'logs',
            'dokumens',
            'tahunAjaran'
        ])->findOrFail($id);

        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $siswa   = $kasus->siswa;
        $sekolah = PengaturanSekolah::getAktif();
        $rombel  = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel;
        $wali    = $rombel?->waliKelas;

        // Hitung total alpha & bolos
        $totalAlpha = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('status', 'alpha')->count();
        $totalBolos = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('status', 'bolos')->count();
        $totalTerlambat = Absensi::where('pemilik_type', 'siswa')->where('pemilik_id', $siswa->id)->where('status', 'terlambat')->count();

        return view('disiplin.cetak_resume', compact(
            'kasus',
            'siswa',
            'sekolah',
            'rombel',
            'wali',
            'totalAlpha',
            'totalBolos',
            'totalTerlambat'
        ));
    }

    /**
     * Hapus berkas kasus disiplin.
     */
    public function destroy($id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $nama = $kasus->siswa->nama ?? 'Siswa';
        $kasus->delete();

        return redirect()->route('admin.disiplin.index')
            ->with('success', "Catatan kasus kedisiplinan {$nama} berhasil dihapus.");
    }

    /**
     * Update Pengaturan Global Bobot Poin & Ambang Batas Disiplin (Admin / Wakasis).
     */
    public function updatePengaturanPoin(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isWakaKesiswaan()) {
            abort(403, 'Hanya Administrator atau Waka Kesiswaan yang berwenang mengubah aturan poin.');
        }

        $request->validate([
            'bobot_terlambat'           => 'required|integer|min:0|max:100',
            'bobot_alpha'               => 'required|integer|min:0|max:100',
            'bobot_bolos'               => 'required|integer|min:0|max:100',
            'toleransi_terlambat_piket' => 'required|integer|min:0|max:50',
            'ambang_tahap_1_wali'       => 'required|integer|min:1|max:500',
            'ambang_tahap_2_bk'         => 'required|integer|min:1|max:500',
            'ambang_tahap_3_wakasis'    => 'required|integer|min:1|max:500',
            'ambang_tahap_4_kepsek'     => 'required|integer|min:1|max:500',
            'reward_streak_hari'        => 'required|integer|min:1|max:100',
            'reward_streak_poin'        => 'required|integer|min:1|max:100',
        ]);

        $setting = PengaturanDisiplin::getPengaturan();
        $setting->update($request->only([
            'bobot_terlambat',
            'bobot_alpha',
            'bobot_bolos',
            'toleransi_terlambat_piket',
            'ambang_tahap_1_wali',
            'ambang_tahap_2_bk',
            'ambang_tahap_3_wakasis',
            'ambang_tahap_4_kepsek',
            'reward_streak_hari',
            'reward_streak_poin',
        ]));

        // Sinkronkan juga poin jenis pelanggaran presensi di katalog master
        KatalogPelanggaran::where('nama_pelanggaran', 'like', '%Terlambat%')->where('kategori', 'presensi')->update(['poin_pelanggaran' => (int) $request->input('bobot_terlambat')]);
        KatalogPelanggaran::where('nama_pelanggaran', 'like', '%Alpha%')->where('kategori', 'presensi')->update(['poin_pelanggaran' => (int) $request->input('bobot_alpha')]);
        KatalogPelanggaran::where('nama_pelanggaran', 'like', '%Bolos%')->where('kategori', 'presensi')->update(['poin_pelanggaran' => (int) $request->input('bobot_bolos')]);

        if ($request->boolean('recalculate_now')) {
            $this->hitungUlangSemuaPoin();
            return redirect()->back()->with('success', 'Pengaturan bobot poin berhasil disimpan & seluruh kasus siswa telah dihitung ulang.');
        }

        return redirect()->back()->with('success', 'Pengaturan bobot poin dan ambang batas disiplin berhasil diperbarui.');
    }

    /**
     * Tambah Item Reward Baru ke Katalog (Admin / Wakasis / BK).
     */
    public function storeKatalogReward(Request $request)
    {
        $request->validate([
            'nama_reward'  => 'required|string|max:255',
            'kategori'     => 'required|in:kehadiran,kebersihan,karakter,konseling,prestasi,custom',
            'poin_deduksi' => 'required|integer|min:1|max:200',
            'deskripsi'    => 'nullable|string|max:500',
        ]);

        $created = KatalogReward::create([
            'nama_reward'  => $request->input('nama_reward'),
            'kategori'     => $request->input('kategori'),
            'poin_deduksi' => (int) $request->input('poin_deduksi'),
            'deskripsi'    => $request->input('deskripsi'),
            'is_active'    => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jenis reward baru berhasil ditambahkan ke katalog master.',
                'item'    => $created,
            ]);
        }

        return redirect()->back()->with('success', 'Jenis reward baru berhasil ditambahkan ke katalog master.')->with('open_tab', 'tabKatalog');
    }

    /**
     * Update Item Reward di Katalog.
     */
    public function updateKatalogReward(Request $request, $id)
    {
        $item = KatalogReward::findOrFail($id);
        $request->validate([
            'nama_reward'  => 'required|string|max:255',
            'kategori'     => 'required|in:kehadiran,kebersihan,karakter,konseling,prestasi,custom',
            'poin_deduksi' => 'required|integer|min:1|max:200',
            'deskripsi'    => 'nullable|string|max:500',
            'is_active'    => 'nullable|boolean',
        ]);

        $item->update([
            'nama_reward'  => $request->input('nama_reward'),
            'kategori'     => $request->input('kategori'),
            'poin_deduksi' => (int) $request->input('poin_deduksi'),
            'deskripsi'    => $request->input('deskripsi'),
            'is_active'    => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item reward berhasil diperbarui.',
                'item'    => $item,
            ]);
        }

        return redirect()->back()->with('success', 'Item reward berhasil diperbarui.')->with('open_tab', 'tabKatalog');
    }

    /**
     * Hapus Item Reward dari Katalog.
     */
    public function deleteKatalogReward(Request $request, $id)
    {
        $item = KatalogReward::findOrFail($id);
        $item->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item reward berhasil dihapus dari katalog master.',
            ]);
        }

        return redirect()->back()->with('success', 'Item reward berhasil dihapus dari katalog master.')->with('open_tab', 'tabKatalog');
    }

    /**
     * Berikan Reward & Kurangi Poin Siswa (Dossier Siswa).
     */
    public function storeRewardSiswa(Request $request, $id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $request->validate([
            'nama_tindakan'     => 'required|string|max:255',
            'poin_dikurangi'    => 'required|integer|min:1|max:200',
            'tanggal'           => 'required|date',
            'catatan'           => 'nullable|string|max:500',
            'katalog_reward_id' => 'nullable|exists:katalog_rewards,id',
        ]);

        $poinDipotong = (int) $request->input('poin_dikurangi');
        $namaTindakan = $request->input('nama_tindakan');
        $catatan = $request->input('catatan');
        $petugasNama = $user->name ?? 'Petugas';
        $petugasRole = $user->role_display_name ?? 'Staf';

        // 1. Simpan Riwayat Reward
        KasusDisiplinReward::create([
            'kasus_disiplin_id' => $kasus->id,
            'siswa_id'          => $kasus->siswa_id,
            'katalog_reward_id' => $request->input('katalog_reward_id'),
            'nama_tindakan'     => $namaTindakan,
            'poin_dikurangi'    => $poinDipotong,
            'tanggal'           => $request->input('tanggal'),
            'dicatat_oleh'      => "{$petugasNama} ({$petugasRole})",
            'catatan'           => $catatan,
        ]);

        // 2. Tambah Log Kronologis
        KasusDisiplinLog::create([
            'kasus_disiplin_id' => $kasus->id,
            'tahap'             => $kasus->status_tahap,
            'judul_kegiatan'    => "Pemberian Reward: {$namaTindakan}",
            'uraian_tindakan'   => $catatan ?: "Siswa meraih pengurangan poin sebesar {$poinDipotong} poin atas tindakan positif: {$namaTindakan}",
            'poin_perubahan'    => -$poinDipotong,
            'petugas_nama'      => $petugasNama,
            'petugas_role'      => $petugasRole,
            'tanggal_kegiatan'  => $request->input('tanggal'),
        ]);

        // 3. Sinkronkan dan Recalculate Kasus
        KasusDisiplin::syncFromPresensi($kasus->siswa_id);

        return redirect()->back()->with('success', "Self-Reward berhasil dicatat! Poin pelanggaran berkurang {$poinDipotong} poin.");
    }

    /**
     * Batalkan / Hapus Reward Siswa.
     */
    public function deleteRewardSiswa($id, $rewardId)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $reward = KasusDisiplinReward::where('kasus_disiplin_id', $kasus->id)->findOrFail($rewardId);
        $poinBatal = $reward->poin_dikurangi;
        $reward->delete();

        // Recalculate
        KasusDisiplin::syncFromPresensi($kasus->siswa_id);

        return redirect()->back()->with('success', "Catatan reward dibatalkan ({$poinBatal} poin).");
    }

    /**
     * Hitung ulang seluruh poin dan tahap kasus siswa (Batch Recalculate).
     */
    public function hitungUlangSemuaPoin()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isWakaKesiswaan()) {
            abort(403, 'Akses ditolak.');
        }

        $allKasus = KasusDisiplin::where('is_active', true)->get();
        foreach ($allKasus as $k) {
            KasusDisiplin::syncFromPresensi($k->siswa_id);
        }

        return redirect()->back()->with('success', 'Seluruh data kasus kedisiplinan (' . $allKasus->count() . ' siswa) berhasil dihitung ulang sesuai skema poin aktif.')->with('open_tab', 'tabSimulasi');
    }

    /**
     * Tambah Item Pelanggaran Baru ke Katalog Master (Admin / Wakasis / BK).
     */
    public function storeKatalogPelanggaran(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'kategori'         => 'required|in:presensi,tata_tertib,sikap,berat,custom',
            'poin_pelanggaran' => 'required|integer|min:1|max:200',
            'deskripsi'        => 'nullable|string|max:500',
        ]);

        $created = KatalogPelanggaran::create([
            'nama_pelanggaran' => $request->input('nama_pelanggaran'),
            'kategori'         => $request->input('kategori'),
            'poin_pelanggaran' => (int) $request->input('poin_pelanggaran'),
            'deskripsi'        => $request->input('deskripsi'),
            'is_active'        => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jenis pelanggaran baru berhasil ditambahkan ke katalog master.',
                'item'    => $created,
            ]);
        }

        return redirect()->back()->with('success', 'Jenis pelanggaran baru berhasil ditambahkan ke katalog master.')->with('open_tab', 'tabPelanggaran');
    }

    /**
     * Update Item Pelanggaran di Katalog Master.
     */
    public function updateKatalogPelanggaran(Request $request, $id)
    {
        $item = KatalogPelanggaran::findOrFail($id);
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'kategori'         => 'required|in:presensi,tata_tertib,sikap,berat,custom',
            'poin_pelanggaran' => 'required|integer|min:1|max:200',
            'deskripsi'        => 'nullable|string|max:500',
            'is_active'        => 'nullable|boolean',
        ]);

        $item->update([
            'nama_pelanggaran' => $request->input('nama_pelanggaran'),
            'kategori'         => $request->input('kategori'),
            'poin_pelanggaran' => (int) $request->input('poin_pelanggaran'),
            'deskripsi'        => $request->input('deskripsi'),
            'is_active'        => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item pelanggaran berhasil diperbarui.',
                'item'    => $item,
            ]);
        }

        return redirect()->back()->with('success', 'Item pelanggaran berhasil diperbarui.')->with('open_tab', 'tabPelanggaran');
    }

    /**
     * Hapus Item Pelanggaran dari Katalog Master.
     */
    public function deleteKatalogPelanggaran(Request $request, $id)
    {
        $item = KatalogPelanggaran::findOrFail($id);
        $item->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item pelanggaran berhasil dihapus dari katalog master.',
            ]);
        }

        return redirect()->back()->with('success', 'Item pelanggaran berhasil dihapus dari katalog master.')->with('open_tab', 'tabPelanggaran');
    }

    /**
     * Catat Pelanggaran Manual / Tambahan Siswa (Dossier Siswa).
     */
    public function storePelanggaranSiswa(Request $request, $id)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $request->validate([
            'nama_pelanggaran'       => 'required|string|max:255',
            'poin_ditambah'          => 'required|integer|min:1|max:200',
            'tanggal'                => 'required|date',
            'catatan'                => 'nullable|string|max:500',
            'katalog_pelanggaran_id' => 'nullable|exists:katalog_pelanggarans,id',
        ]);

        $poinPlus = (int) $request->input('poin_ditambah');
        $namaPelanggaran = $request->input('nama_pelanggaran');
        $catatan = $request->input('catatan');
        $petugasNama = $user->name ?? 'Petugas';
        $petugasRole = $user->role_display_name ?? 'Staf';

        // 1. Simpan Riwayat Pelanggaran
        KasusDisiplinPelanggaran::create([
            'kasus_disiplin_id'      => $kasus->id,
            'siswa_id'               => $kasus->siswa_id,
            'katalog_pelanggaran_id' => $request->input('katalog_pelanggaran_id'),
            'nama_pelanggaran'       => $namaPelanggaran,
            'poin_ditambah'          => $poinPlus,
            'tanggal'                => $request->input('tanggal'),
            'dicatat_oleh'           => "{$petugasNama} ({$petugasRole})",
            'catatan'                => $catatan,
        ]);

        // 2. Tambah Log Kronologis
        KasusDisiplinLog::create([
            'kasus_disiplin_id' => $kasus->id,
            'tahap'             => $kasus->status_tahap,
            'judul_kegiatan'    => "Pencatatan Pelanggaran: {$namaPelanggaran}",
            'uraian_tindakan'   => $catatan ?: "Siswa tercatat melakukan pelanggaran: {$namaPelanggaran} (+{$poinPlus} Poin)",
            'poin_perubahan'    => $poinPlus,
            'petugas_nama'      => $petugasNama,
            'petugas_role'      => $petugasRole,
            'tanggal_kegiatan'  => $request->input('tanggal'),
        ]);

        // 3. Sinkronkan dan Recalculate Kasus
        KasusDisiplin::syncFromPresensi($kasus->siswa_id);

        return redirect()->back()->with('success', "Pelanggaran berhasil dicatat! Poin pelanggaran bertambah +{$poinPlus} poin.");
    }

    /**
     * Hapus Catatan Pelanggaran Manual Siswa.
     */
    public function deletePelanggaranSiswa($id, $pelanggaranId)
    {
        $kasus = KasusDisiplin::findOrFail($id);
        $user = auth()->user();
        $this->checkAksesKasus($user, $kasus);

        $pelanggaran = KasusDisiplinPelanggaran::where('kasus_disiplin_id', $kasus->id)->findOrFail($pelanggaranId);
        $poinBatal = $pelanggaran->poin_ditambah;
        $pelanggaran->delete();

        // Recalculate
        KasusDisiplin::syncFromPresensi($kasus->siswa_id);

        return redirect()->back()->with('success', "Catatan pelanggaran dihapus (-{$poinBatal} poin).");
    }
}
