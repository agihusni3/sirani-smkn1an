<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    protected WhatsAppNotificationService $waService;

    public function __construct(WhatsAppNotificationService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Tampilkan Meja Verifikasi Notifikasi & Riwayat Pengiriman.
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $user  = auth()->user();

        // Restriksi jika login sebagai Wali Kelas (hanya melihat kelas binaannya)
        $isWaliKelas   = $user && $user->isWaliKelas() && !$user->isAdmin() && $user->guru;
        $waliRombelIds = $isWaliKelas ? $user->guru->rombels()->pluck('id')->toArray() : [];

        // Base Query scoped to role
        $baseQuery = NotifikasiOrtu::with(['siswa.siswaRombels.rombel']);
        if ($isWaliKelas) {
            $baseQuery->whereHas('siswa.siswaRombels', function ($q) use ($waliRombelIds) {
                $q->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif');
            });
        }

        // Applied Filters from Request
        $status    = $request->input('status');
        $kategori  = $request->input('kategori');
        $tanggal   = $request->input('tanggal'); // Nullable agar riwayat lintas tanggal terlihat
        $rombelId  = $request->input('rombel_id');
        $search    = $request->input('q');

        // Apply filters to filterQuery for synchronized counts and table
        $filterQuery = clone $baseQuery;

        if ($tanggal) {
            $filterQuery->whereDate('tanggal', $tanggal);
        }

        if ($kategori) {
            $filterQuery->where('kategori', $kategori);
        }

        if ($rombelId) {
            $filterQuery->whereHas('siswa.siswaRombels', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            });
        }

        if ($search) {
            $filterQuery->where(function ($q) use ($search) {
                $q->where('no_tujuan', 'like', "%{$search}%")
                  ->orWhere('nama_ortu', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('siswa', function ($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        // Calculate KPI Counts from $filterQuery (Synced 100% with Role and Active Filters)
        $statPending    = (clone $filterQuery)->where('status', 'pending')->count();
        $statTerkirim   = (clone $filterQuery)->where('status', 'terkirim')->count();
        $statDibatalkan = (clone $filterQuery)->where('status', 'dibatalkan')->count();
        $statGagal      = (clone $filterQuery)->where('status', 'gagal')->count();
        $statSemua      = (clone $filterQuery)->count();

        // Default status tab logic:
        if (!$status) {
            $status = $statPending > 0 ? 'pending' : 'semua';
        }

        // Table Query
        $tableQuery = clone $filterQuery;
        if ($status !== 'semua') {
            $tableQuery->where('status', $status);
        }

        $notifikasis = $tableQuery->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        $rombels = Rombel::orderBy('nama_rombel')->get();
        if ($isWaliKelas) {
            $rombels = Rombel::whereIn('id', $waliRombelIds)->orderBy('nama_rombel')->get();
        }
        $setting = PengaturanNotifikasi::getPengaturan();

        $siswaQuery = Siswa::where('status', 'aktif')->with(['siswaRombels.rombel']);
        if ($isWaliKelas) {
            $siswaQuery->whereHas('siswaRombels', function ($q) use ($waliRombelIds) {
                $q->whereIn('rombel_id', $waliRombelIds)->where('status_keanggotaan', 'aktif');
            });
        }
        $siswasPembinaan = $siswaQuery->orderBy('nama')->get();

        return view('notifikasi.index', compact(
            'notifikasis',
            'status',
            'kategori',
            'tanggal',
            'rombelId',
            'search',
            'statPending',
            'statTerkirim',
            'statDibatalkan',
            'statGagal',
            'statSemua',
            'rombels',
            'setting',
            'siswasPembinaan'
        ));
    }

    /**
     * Dapatkan nama petugas yang memverifikasi.
     */
    private function getPetugasNama(): string
    {
        if (auth()->check()) {
            return auth()->user()->name . (auth()->user()->isAdmin() ? ' (Admin)' : ' (Wali Kelas)');
        }
        return session('guru_piket_nama', 'Guru Piket') . ' (Guru Piket)';
    }

    /**
     * Verifikasi & kirim satu notifikasi.
     */
    public function approve(Request $request, int $id)
    {
        $notifikasi = NotifikasiOrtu::findOrFail($id);

        $petugas = $this->getPetugasNama();
        $notifikasi->update([
            'status'            => 'diverifikasi',
            'diverifikasi_oleh' => $petugas,
            'waktu_verifikasi'  => now(),
        ]);

        $result = $this->waService->kirim($notifikasi);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message'] . " (Diverifikasi oleh {$petugas})"
        );
    }

    /**
     * Verifikasi & kirim banyak notifikasi sekaligus (Batch Approve).
     */
    public function batchApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu notifikasi untuk diverifikasi.');
        }

        $petugas = $this->getPetugasNama();
        $successCount = 0;
        $failCount = 0;

        $notifikasis = NotifikasiOrtu::whereIn('id', $ids)->where('status', 'pending')->get();
        foreach ($notifikasis as $notif) {
            $notif->update([
                'status'            => 'diverifikasi',
                'diverifikasi_oleh' => $petugas,
                'waktu_verifikasi'  => now(),
            ]);

            $res = $this->waService->kirim($notif);
            if ($res['success']) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return back()->with('success', "Berhasil memverifikasi {$successCount} notifikasi. (Gagal: {$failCount})");
    }

    /**
     * Tolak / Batalkan draf notifikasi.
     */
    public function reject(Request $request, int $id)
    {
        $notifikasi = NotifikasiOrtu::findOrFail($id);
        $petugas = $this->getPetugasNama();

        $notifikasi->update([
            'status'            => 'dibatalkan',
            'diverifikasi_oleh' => $petugas,
            'waktu_verifikasi'  => now(),
            'catatan_error'     => 'Dibatalkan oleh petugas: ' . $petugas,
        ]);

        return back()->with('success', "Draf notifikasi berhasil dibatalkan.");
    }

    /**
     * Batalkan massal draf notifikasi.
     */
    public function batchReject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu draf notifikasi untuk dibatalkan.');
        }

        $petugas = $this->getPetugasNama();
        NotifikasiOrtu::whereIn('id', $ids)->where('status', 'pending')->update([
            'status'            => 'dibatalkan',
            'diverifikasi_oleh' => $petugas,
            'waktu_verifikasi'  => now(),
            'catatan_error'     => 'Dibatalkan massal oleh: ' . $petugas,
        ]);

        return back()->with('success', count($ids) . " draf notifikasi berhasil dibatalkan.");
    }

    /**
     * Simpan pengaturan gateway WhatsApp & kustomisasi template pesan.
     */
    public function updatePengaturan(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isGuruPiket())) {
            abort(403, 'Akses ditolak. Anda tidak berhak mengubah ketentuan pelanggaran dan pengaturan notifikasi.');
        }

        $request->validate([
            'wa_provider'                => 'required|in:fonnte,wablas,generic_api,simulasi',
            'wa_api_token'               => 'nullable|string|max:255',
            'wa_endpoint_url'            => 'nullable|url|max:255',
            'ambang_batas_alpha'         => 'required|integer|min:1|max:10',
            'template_terlambat'         => 'required|string',
            'template_alpha'             => 'required|string',
            'template_izin'              => 'required|string',
            'template_sakit'             => 'required|string',
            'template_bolos'             => 'required|string',
            'template_wali_kelas'        => 'required|string',
        ]);

        $setting = PengaturanNotifikasi::getPengaturan();
        $setting->update([
            'wa_provider'                => $request->input('wa_provider'),
            'wa_api_token'               => $request->input('wa_api_token'),
            'wa_endpoint_url'            => $request->input('wa_endpoint_url'),
            'is_active'                  => $request->boolean('is_active'),
            'ambang_batas_alpha'         => (int) $request->input('ambang_batas_alpha', 3),
            'hitung_bolos_bersama_alpha' => $request->boolean('hitung_bolos_bersama_alpha'),
            'auto_notif_wali_kelas'      => $request->boolean('auto_notif_wali_kelas'),
            'template_terlambat'         => $request->input('template_terlambat'),
            'template_alpha'             => $request->input('template_alpha'),
            'template_izin'              => $request->input('template_izin'),
            'template_sakit'             => $request->input('template_sakit'),
            'template_bolos'             => $request->input('template_bolos'),
            'template_wali_kelas'        => $request->input('template_wali_kelas'),
        ]);

        return back()->with('success', 'Ketentuan Pelanggaran & Template Notifikasi Otomatis berhasil disimpan.');
    }

    /**
     * Test kirim pesan WhatsApp simulasi/live.
     */
    public function testKirim(Request $request)
    {
        $request->validate([
            'no_tujuan' => 'required|string|min:8',
            'pesan'     => 'required|string',
        ]);

        $dummySiswa = Siswa::first() ?? Siswa::create([
            'nis'   => '999999',
            'nama'  => 'Siswa Percobaan',
            'status'=> 'aktif',
        ]);

        $notif = NotifikasiOrtu::create([
            'siswa_id'          => $dummySiswa->id,
            'kategori'          => 'pengumuman',
            'tanggal'           => Carbon::today()->toDateString(),
            'no_tujuan'         => $request->input('no_tujuan'),
            'nama_ortu'         => 'Wali Siswa Percobaan',
            'judul'             => 'Test Pesan SIRANI SMKN 1 AN',
            'pesan'             => $request->input('pesan'),
            'status'            => 'diverifikasi',
            'dibuat_oleh'       => 'test_admin',
            'diverifikasi_oleh' => $this->getPetugasNama(),
            'waktu_verifikasi'  => now(),
        ]);

        $result = $this->waService->kirim($notif);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

}

