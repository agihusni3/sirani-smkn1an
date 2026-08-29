<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pengumuman;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengumumanController extends Controller
{
    protected WhatsAppNotificationService $waService;

    public function __construct(WhatsAppNotificationService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Tampilkan Halaman Pusat Pengumuman & Broadcast Sekolah.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isWaliOnly = $currentUser && $currentUser->isWaliKelas() && !$currentUser->isAdmin() && !$currentUser->isWakaKesiswaan() && !$currentUser->isWakaKurikulum();
        $waliRombelIds = $isWaliOnly ? $currentUser->getWaliRombelIds() : [];

        $kategori = $request->input('kategori');
        $status = $request->input('status');
        $search = $request->input('q');

        $query = Pengumuman::with('user')->latest();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($status === 'aktif') {
            $query->activeToday();
        } elseif ($status === 'nonaktif') {
            $query->where('is_active', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi_pesan', 'like', "%{$search}%")
                  ->orWhere('target_nama', 'like', "%{$search}%");
            });
        }

        $pengumumans = $query->paginate(15)->withQueryString();

        // Data Pilihan Target
        if ($isWaliOnly) {
            $rombels = Rombel::whereIn('id', $waliRombelIds)->orderBy('nama_rombel')->get();
            $jurusans = collect();
        } else {
            $rombels = Rombel::orderBy('nama_rombel')->get();
            $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        }

        // KPI Stats
        $statTotal = Pengumuman::count();
        $statAktif = Pengumuman::activeToday()->count();
        $statWaTerkirim = Pengumuman::sum('total_terkirim');
        $statPortal = Pengumuman::forPortal()->count();

        return view('pengumuman.index', compact(
            'pengumumans',
            'rombels',
            'jurusans',
            'kategori',
            'status',
            'search',
            'statTotal',
            'statAktif',
            'statWaTerkirim',
            'statPortal',
            'isWaliOnly'
        ));
    }

    /**
     * Simpan Pengumuman Baru & Kirim Broadcast WhatsApp jika dipilih.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'           => 'required|string|max:200',
            'isi_pesan'       => 'required|string',
            'banner_gambar'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'kategori'        => 'required|in:umum,kedisiplinan,kegiatan,akademik,darurat',
            'target_tipe'     => 'required|in:semua,tingkat,rombel,jurusan,alumni',
            'target_id'       => 'nullable|string',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $targetTipe = $request->input('target_tipe');
        $targetId = $request->input('target_id');
        $targetNama = 'Semua Siswa & Wali Murid';

        // Tentukan Target Nama
        if ($targetTipe === 'tingkat') {
            $targetNama = 'Siswa Kelas Tingkat ' . ($targetId ?: 'Semua Tingkat');
        } elseif ($targetTipe === 'rombel') {
            $r = Rombel::find($targetId);
            $targetNama = $r ? 'Rombel ' . $r->nama_rombel : 'Rombel Terpilih';
        } elseif ($targetTipe === 'jurusan') {
            $j = Jurusan::find($targetId);
            $targetNama = $j ? 'Jurusan ' . $j->nama_jurusan : 'Jurusan Terpilih';
        } elseif ($targetTipe === 'alumni') {
            $targetNama = 'Direktori Alumni / Lulusan';
        }

        $bannerPath = null;
        if ($request->hasFile('banner_gambar')) {
            $bannerPath = $request->file('banner_gambar')->store('pengumuman_banner', 'public');
        }

        $kirimWa = $request->has('kirim_wa');
        $targetPenerimaWa = $request->input('target_penerima_wa', 'ortu');
        $tampilPortal = $request->has('tampil_portal');
        $tampilKios = $request->has('tampil_kios');

        $tanggalMulai = $request->input('tanggal_mulai') ?: Carbon::today()->toDateString();
        $tanggalSelesai = $request->input('tanggal_selesai');

        // Ambil target penerima siswa
        $querySiswa = Siswa::query();

        if ($targetTipe === 'alumni') {
            $querySiswa->where('status', 'lulus');
        } else {
            $querySiswa->whereIn('status', ['aktif', 'pkl']);

            if ($targetTipe === 'rombel' && $targetId) {
                $querySiswa->whereHas('siswaRombels', function ($q) use ($targetId) {
                    $q->where('rombel_id', $targetId)->where('status_keanggotaan', 'aktif');
                });
            } elseif ($targetTipe === 'tingkat' && $targetId) {
                $querySiswa->whereHas('siswaRombels.rombel', function ($q) use ($targetId) {
                    $q->where('tingkat', $targetId)->where('status_keanggotaan', 'aktif');
                });
            } elseif ($targetTipe === 'jurusan' && $targetId) {
                $querySiswa->whereHas('siswaRombels.rombel', function ($q) use ($targetId) {
                    $q->where('jurusan_id', $targetId)->where('status_keanggotaan', 'aktif');
                });
            }
        }

        $allSiswas = $querySiswa->get();

        // Hitung total penerima WA sesuai pilihan
        $totalTarget = 0;
        if ($kirimWa) {
            foreach ($allSiswas as $s) {
                if (($targetPenerimaWa === 'ortu' || $targetPenerimaWa === 'keduanya') && !empty($s->no_hp_ortu)) {
                    $totalTarget++;
                }
                if (($targetPenerimaWa === 'siswa' || $targetPenerimaWa === 'keduanya') && !empty($s->no_hp_siswa)) {
                    $totalTarget++;
                }
            }
        }

        $totalTerkirim = 0;

        $pengumuman = Pengumuman::create([
            'judul'              => $request->input('judul'),
            'isi_pesan'          => $request->input('isi_pesan'),
            'banner_gambar'      => $bannerPath,
            'kategori'           => $request->input('kategori'),
            'target_tipe'        => $targetTipe,
            'target_id'          => $targetId,
            'target_nama'        => $targetNama,
            'kirim_wa'           => $kirimWa,
            'target_penerima_wa' => $targetPenerimaWa,
            'tampil_portal'      => $tampilPortal,
            'tampil_kios'        => $tampilKios,
            'is_active'          => true,
            'tanggal_mulai'      => $tanggalMulai,
            'tanggal_selesai'    => $tanggalSelesai,
            'total_target'       => $totalTarget,
            'total_terkirim'     => 0,
            'status_pengiriman'  => $kirimWa ? 'proses' : 'draft',
            'created_by'         => auth()->id(),
        ]);

        // Jika kirim WA aktif, lakukan broadcast pesan
        if ($kirimWa && $totalTarget > 0) {
            $kategoriHeader = match ($pengumuman->kategori) {
                'darurat'      => '*PENGUMUMAN PENTING / DARURAT*',
                'kedisiplinan' => '*PEMBERITAHUAN KEDISIPLINAN*',
                'kegiatan'     => '*INFORMASI KEGIATAN SEKOLAH*',
                'akademik'     => '*INFORMASI AKADEMIK & PEMBELAJARAN*',
                default        => '*PENGUMUMAN RESMI SEKOLAH*',
            };

            foreach ($allSiswas as $s) {
                // 1. Kirim ke Nomor Orang Tua
                if (($targetPenerimaWa === 'ortu' || $targetPenerimaWa === 'keduanya') && !empty($s->no_hp_ortu)) {
                    $pesanWaOrtu = "{$kategoriHeader}\n"
                                 . "*SMK NEGERI 1 AIR NANINGAN*\n\n"
                                 . "Yth. Orang Tua / Wali dari:\n"
                                 . "Nama: *{$s->nama}* (NIS: {$s->nis})\n\n"
                                 . "*{$pengumuman->judul}*\n\n"
                                 . "{$pengumuman->isi_pesan}\n\n"
                                 . "----------------------------------------\n"
                                 . "_Pesan resmi otomatis dari Sistem Informasi Responsif Absensi (SIRANI) SMKN 1 Air Naningan._";

                    $res = $this->waService->kirimDirect($s->no_hp_ortu, $pesanWaOrtu, $pengumuman->judul);
                    if (!empty($res['success'])) {
                        $totalTerkirim++;
                    }
                }

                // 2. Kirim ke Nomor Siswa Sendiri
                if (($targetPenerimaWa === 'siswa' || $targetPenerimaWa === 'keduanya') && !empty($s->no_hp_siswa)) {
                    $pesanWaSiswa = "{$kategoriHeader}\n"
                                  . "*SMK NEGERI 1 AIR NANINGAN*\n\n"
                                  . "Yth. Peserta Didik:\n"
                                  . "Nama: *{$s->nama}* (NIS: {$s->nis})\n\n"
                                  . "*{$pengumuman->judul}*\n\n"
                                  . "{$pengumuman->isi_pesan}\n\n"
                                  . "----------------------------------------\n"
                                  . "_Pesan resmi dari SMKN 1 Air Naningan._";

                    $res = $this->waService->kirimDirect($s->no_hp_siswa, $pesanWaSiswa, $pengumuman->judul);
                    if (!empty($res['success'])) {
                        $totalTerkirim++;
                    }
                }
            }

            $pengumuman->update([
                'total_terkirim'    => $totalTerkirim,
                'status_pengiriman' => 'selesai',
            ]);
        }

        $penerimaLabel = match ($targetPenerimaWa) {
            'siswa'    => 'WhatsApp Siswa Pribadi',
            'keduanya' => 'Orang Tua & Siswa',
            default    => 'Orang Tua / Wali',
        };

        $msg = "Pengumuman berhasil diterbitkan!";
        if ($kirimWa) {
            $msg .= " Broadcast {$penerimaLabel} berhasil dikirimkan ke {$totalTerkirim} dari {$totalTarget} kontak penerima.";
        }

        return redirect()->route('pengumuman.index')->with('success', $msg);
    }

    /**
     * Toggle status aktif / nonaktif pengumuman
     */
    public function toggleStatus($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['is_active' => !$pengumuman->is_active]);

        $statusStr = $pengumuman->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Pengumuman \"{$pengumuman->judul}\" berhasil {$statusStr}.");
    }

    /**
     * Hapus Pengumuman
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        if ($pengumuman->banner_gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($pengumuman->banner_gambar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengumuman->banner_gambar);
        }
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
