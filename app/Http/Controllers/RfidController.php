<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\JadwalHariIni;
use App\Models\KartuRfid;
use App\Models\PengaturanSekolah;
use App\Models\Pengumuman;
use App\Models\Siswa;
use App\Services\RfidScanService;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RfidController extends Controller
{
    protected RfidScanService $rfidService;

    public function __construct(RfidScanService $rfidService)
    {
        $this->rfidService = $rfidService;
    }

    /**
     * Halaman Manajemen Kartu RFID Terpusat (Admin & Staf TU)
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'siswa'); // 'siswa' atau 'guru'
        if (!in_array($tab, ['siswa', 'guru'])) {
            $tab = 'siswa';
        }

        $search = $request->input('q') ?: $request->input('search');
        $statusFilter = $request->input('status'); // rfid, barcode, atau null
        $rombelId = $request->input('rombel_id');
        $kepegawaian = $request->input('kepegawaian');
        $sort = $request->input('sort', 'nama_asc');

        if ($tab === 'siswa') {
            $query = Siswa::whereIn('status', ['aktif', 'pkl'])->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel.jurusan');
            }, 'kartuRfid']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhereHas('kartuRfid', function ($kq) use ($search) {
                          $kq->where('uid', 'like', "%{$search}%");
                      });
                });
            }

            if ($rombelId) {
                $query->whereHas('siswaRombels', function ($rq) use ($rombelId) {
                    $rq->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
                });
            }

            if ($statusFilter === 'rfid') {
                $query->whereHas('kartuRfid', fn($kq) => $kq->where('status', 'aktif'));
            } elseif ($statusFilter === 'barcode') {
                $query->whereDoesntHave('kartuRfid', fn($kq) => $kq->where('status', 'aktif'));
            }

            // Sorting Siswa
            switch ($sort) {
                case 'terbaru':
                case 'terakhir_input':
                case 'created_desc':
                    $query->orderBy('id', 'desc');
                    break;
                case 'terlama':
                case 'created_asc':
                    $query->orderBy('id', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama', 'desc');
                    break;
                case 'nisn_asc':
                    $query->orderBy('nisn', 'asc');
                    break;
                case 'nisn_desc':
                    $query->orderBy('nisn', 'desc');
                    break;
                case 'nama_asc':
                default:
                    $query->orderBy('nama', 'asc');
                    break;
            }

            $kartus = $query->paginate(15)->withQueryString();
        } else {
            $query = Guru::where('status', 'aktif')->with('kartuRfid');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhereHas('kartuRfid', function ($kq) use ($search) {
                          $kq->where('uid', 'like', "%{$search}%");
                      });
                });
            }

            if ($kepegawaian) {
                $query->where('jenis_kepegawaian', $kepegawaian);
            }

            if ($statusFilter === 'rfid') {
                $query->whereHas('kartuRfid', fn($kq) => $kq->where('status', 'aktif'));
            } elseif ($statusFilter === 'barcode') {
                $query->whereDoesntHave('kartuRfid', fn($kq) => $kq->where('status', 'aktif'));
            }

            // Sorting Guru
            switch ($sort) {
                case 'terbaru':
                case 'terakhir_input':
                case 'created_desc':
                    $query->orderBy('id', 'desc');
                    break;
                case 'terlama':
                case 'created_asc':
                    $query->orderBy('id', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama', 'desc');
                    break;
                case 'nip_asc':
                    $query->orderBy('nip', 'asc');
                    break;
                case 'nip_desc':
                    $query->orderBy('nip', 'desc');
                    break;
                case 'nama_asc':
                default:
                    $query->orderBy('nama', 'asc');
                    break;
            }

            $kartus = $query->paginate(15)->withQueryString();
        }

        // Hitung Statistik Kartu Pintar (Barcode & RFID)
        $statSiswaAktif = Siswa::whereIn('status', ['aktif', 'pkl'])->count();
        $statGuruAktif  = Guru::where('status', 'aktif')->count();
        $statTotalAktif = $statSiswaAktif + $statGuruAktif;
        $statRfidPaired = KartuRfid::where('status', 'aktif')->count();

        // Data Siswa & Guru untuk opsi pendaftaran/pairing
        $rombels = \App\Models\Rombel::orderBy('nama_rombel')->get();
        $allSiswas = Siswa::where('status', 'aktif')->with('siswaRombels.rombel')->orderBy('nama')->get();
        $allGurus = Guru::where('status', 'aktif')->orderBy('nama')->get();

        return view('rfid.index', compact(
            'kartus',
            'tab',
            'search',
            'statusFilter',
            'rombelId',
            'kepegawaian',
            'sort',
            'rombels',
            'statTotalAktif',
            'statSiswaAktif',
            'statGuruAktif',
            'statRfidPaired',
            'allSiswas',
            'allGurus'
        ));
    }

    /**
     * Halaman Cetak Kartu Barcode / RFID Presensi Siswa & Guru Siap Pakai
     */
    public function cetak(Request $request)
    {
        $tab = $request->input('tab', 'siswa'); // 'siswa' atau 'guru'
        $rombelId = $request->input('rombel_id');
        $format = $request->input('format', 'barcode'); // 'barcode' atau 'qr'
        $ids = $request->input('ids'); // array or comma-separated IDs
        $sekolah = PengaturanSekolah::getAktif();
        $rombels = \App\Models\Rombel::orderBy('nama_rombel')->get();

        $selectedIds = [];
        if (!empty($ids)) {
            $selectedIds = is_array($ids) ? $ids : array_filter(explode(',', $ids));
        }

        if ($tab === 'siswa') {
            $siswaQuery = Siswa::whereIn('status', ['aktif', 'pkl'])->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel.jurusan');
            }, 'kartuRfid']);

            if (!empty($selectedIds)) {
                $siswaQuery->whereIn('id', $selectedIds);
            } elseif ($rombelId) {
                $siswaQuery->whereHas('siswaRombels', function ($rq) use ($rombelId) {
                    $rq->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
                });
            }
            $items = $siswaQuery->orderBy('nama')->get();
        } else {
            $guruQuery = Guru::where('status', 'aktif')->with('kartuRfid');
            if (!empty($selectedIds)) {
                $guruQuery->whereIn('id', $selectedIds);
            }
            $items = $guruQuery->orderBy('nama')->get();
        }

        return view('rfid.cetak_kartu', compact(
            'items',
            'tab',
            'rombelId',
            'format',
            'sekolah',
            'rombels',
            'selectedIds'
        ));
    }

    /**
     * Redirect langsung ke Portal Presensi Mandiri Terpadu Siswa & Orang Tua
     */
    public function portalSiswa(?string $nisn = null, Request $request = null)
    {
        $nisn = $nisn ?: ($request ? ($request->input('nisn') ?: $request->input('nis') ?: $request->input('keyword')) : null);

        if (!$nisn) {
            return redirect()->route('portal.ortu.index');
        }

        return redirect()->route('portal.ortu.detail', ['nisn' => $nisn]);
    }

    /**
     * Redirect langsung ke Portal Presensi Mandiri Terpadu Siswa & Orang Tua
     */
    public function kartuDigital(string $identifier, Request $request = null)
    {
        return redirect()->route('portal.ortu.detail', ['nisn' => $identifier]);
    }

    /**
     * Tampilan Kartu Digital Mobile-Friendly untuk Guru & Pegawai
     */
    public function kartuDigitalGuru(int $id)
    {
        $guru = Guru::with('kartuRfid')->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();
        $codeValue = $guru->kartuRfid?->uid ?? ($guru->nip ?: 'GURU-'.$guru->id);

        return view('rfid.kartu_digital_guru', compact('guru', 'sekolah', 'codeValue'));
    }

    /**
     * Mendeteksi Base URL Publik yang aktif (HTTPS Ngrok Tunnel / Domain Publik / APP_URL)
     */
    public static function getPublicBaseUrl(): string
    {
        // 1. Cek apakah ada active ngrok tunnel di local
        try {
            $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $res = curl_exec($ch);
            curl_close($ch);
            if ($res) {
                $data = json_decode($res, true);
                if (!empty($data['tunnels'][0]['public_url'])) {
                    return rtrim($data['tunnels'][0]['public_url'], '/');
                }
            }
        } catch (\Throwable $e) {}

        // 2. Cek Request Host jika bukan localhost
        $host = request()->getHost();
        if ($host && !in_array($host, ['localhost', '127.0.0.1'])) {
            return rtrim(request()->getSchemeAndHttpHost(), '/');
        }

        // 3. Cek APP_URL dari konfigurasi
        $appUrl = config('app.url');
        if ($appUrl && !str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
            return rtrim($appUrl, '/');
        }

        return rtrim(request()->getSchemeAndHttpHost(), '/');
    }

    /**
     * Broadcast Pesan WhatsApp Berisi Barcode / QR Code Presensi
     */
    public function broadcastWa(Request $request, \App\Services\WhatsAppNotificationService $waService)
    {
        @set_time_limit(900); // Izinkan proses background broadcast hingga 15 menit

        $request->validate([
            'tab'             => 'required|in:siswa,ortu,guru,individu_siswa,individu_ortu,individu_guru',
            'rombel_id'       => 'nullable|integer',
            'target_siswa_id' => 'nullable|integer',
            'target_guru_id'  => 'nullable|integer',
        ]);

        $tab = $request->input('tab');
        $rombelId = $request->input('rombel_id');
        $targetSiswaId = $request->input('target_siswa_id');
        $targetGuruId = $request->input('target_guru_id');
        $sekolah = PengaturanSekolah::getAktif();
        $namaSekolah = $sekolah->nama_sekolah ?? 'SMKN 1 AIR NANINGAN';
        $baseUrl = self::getPublicBaseUrl();

        $totalTerkirim = 0;
        $totalTarget = 0;

        // ── 1. PENGIRIMAN INDIVIDU SISWA / ORTU ──
        if ($tab === 'individu_siswa' || $tab === 'individu_ortu') {
            if (!$targetSiswaId) {
                return redirect()->back()->with('error', 'Silakan pilih siswa target yang ingin dikirimi pesan WhatsApp.');
            }

            $s = Siswa::with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel');
            }, 'kartuRfid'])->findOrFail($targetSiswaId);

            $noHp = ($tab === 'individu_siswa') ? $s->no_hp_siswa : $s->no_hp_ortu;
            if (!$noHp) {
                $targetLabel = ($tab === 'individu_siswa') ? 'Siswa' : 'Orang Tua';
                return redirect()->back()->with('error', "Gagal: Nomor WhatsApp {$targetLabel} untuk siswa {$s->nama} belum terisi di data siswa.");
            }

            $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
            $codeVal = $s->kartuRfid?->uid ?? ($s->nisn ?: $s->id);
            $linkPortal = $baseUrl . '/cek-presensi/' . ($s->nisn ?: $s->id);
            $linkQr = $baseUrl . '/qr/siswa/' . $s->id;

            if ($tab === 'individu_siswa') {
                $pesan = "🔔 *PORTAL PRESENSI MANDIRI SISWA & ORTU — {$namaSekolah}*\n\n"
                       . "Yth. *{$s->nama}*:\n"
                       . "🏷️ *NISN:* " . ($s->nisn ?: '-') . "\n"
                       . "🏫 *Kelas:* {$rombelNama}\n\n"
                       . "Berikut akses portal presensi dan QR Code presensi Anda:\n\n"
                       . "📱 *Buka Portal Presensi Mandiri:*\n"
                       . "{$linkPortal}\n\n"
                       . "📥 *Download Gambar QR Code Langsung:*\n"
                       . "{$linkQr}\n\n"
                       . "_Simpan gambar QR di HP atau tunjukkan saat tiba di scanner gerbang sekolah._";
            } else {
                $pesan = "🔔 *PORTAL PRESENSI SISWA & ORANG TUA — {$namaSekolah}*\n\n"
                       . "Yth. Bapak/Ibu Orang Tua / Wali dari:\n"
                       . "👤 *Nama Siswa:* {$s->nama}\n"
                       . "🏷️ *NISN:* " . ($s->nisn ?: '-') . "\n"
                       . "🏫 *Kelas:* {$rombelNama}\n\n"
                       . "Berikut akses portal presensi dan QR Code ananda:\n\n"
                       . "📱 *Buka Portal Presensi Mandiri:*\n"
                       . "{$linkPortal}\n\n"
                       . "📥 *Download Gambar QR Code Langsung:*\n"
                       . "{$linkQr}\n\n"
                       . "_Portal ini dapat digunakan untuk memantau kehadiran, jadwal, dan rekap ananda secara berkala._";
            }

            $res = $waService->kirimDirect($noHp, $pesan, 'KARTU PRESENSI DIGITAL');
            if ($res['success'] ?? false) {
                $targetLabel = ($tab === 'individu_siswa') ? 'Siswa' : 'Orang Tua';
                return redirect()->back()->with('success', "Kartu presensi digital berhasil dikirim ke WhatsApp {$targetLabel} ({$s->nama} - {$noHp}).");
            }

            return redirect()->back()->with('error', "Gagal mengirim WhatsApp ke {$noHp}: " . ($res['message'] ?? 'Error gateway.'));
        }

        // ── 2. PENGIRIMAN INDIVIDU GURU ──
        if ($tab === 'individu_guru') {
            if (!$targetGuruId) {
                return redirect()->back()->with('error', 'Silakan pilih guru/staf target yang ingin dikirimi pesan WhatsApp.');
            }

            $g = Guru::with('kartuRfid')->findOrFail($targetGuruId);
            $noHp = $g->no_hp;
            if (!$noHp) {
                return redirect()->back()->with('error', "Gagal: Nomor WhatsApp untuk guru {$g->nama} belum terisi.");
            }

            $codeVal = $g->kartuRfid?->uid ?? ($g->nip ?: 'GURU-'.$g->id);
            $linkKartu = $baseUrl . '/kartu-digital-guru/' . $g->id;
            $linkQr = $baseUrl . '/qr/guru/' . $g->id;

            $pesan = "🔔 *KARTU PRESENSI DIGITAL GURU & STAF — {$namaSekolah}*\n\n"
                   . "Yth. Bapak/Ibu:\n"
                   . "👤 *Nama:* {$g->nama}\n"
                   . "🏷️ *NIP:* " . ($g->nip ?: '-') . "\n"
                   . "💼 *Jabatan:* {$g->jabatan}\n\n"
                   . "Berikut akses kartu dan QR Code presensi mandiri Anda:\n\n"
                   . "📱 *Buka Kartu Presensi Digital:*\n"
                   . "{$linkKartu}\n\n"
                   . "📥 *Download Gambar QR Code Langsung:*\n"
                   . "{$linkQr}\n\n"
                   . "_Simpan QR di HP untuk melakukan presensi mandiri pada scanner gerbang sekolah._";

            $res = $waService->kirimDirect($noHp, $pesan, 'KARTU PRESENSI GURU');
            if ($res['success'] ?? false) {
                return redirect()->back()->with('success', "Kartu presensi digital berhasil dikirim ke WhatsApp Guru ({$g->nama} - {$noHp}).");
            }

            return redirect()->back()->with('error', "Gagal mengirim WhatsApp ke {$noHp}: " . ($res['message'] ?? 'Error gateway.'));
        }

        // ── 3. PENGIRIMAN MASSAL (BROADCAST) SISWA / ORTU ──
        if ($tab === 'siswa' || $tab === 'ortu') {
            $query = Siswa::whereIn('status', ['aktif', 'pkl'])->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel');
            }, 'kartuRfid']);

            if ($rombelId) {
                $query->whereHas('siswaRombels', function ($rq) use ($rombelId) {
                    $rq->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
                });
            }
            $siswas = $query->get();

            foreach ($siswas as $s) {
                // Pilih nomor sesuai target spesifik: Siswa atau Orang Tua
                $noHp = ($tab === 'siswa') ? $s->no_hp_siswa : $s->no_hp_ortu;
                if (!$noHp) continue;

                // Jeda delay aman 5-7 detik antar nomor untuk mencegah blokir spam WhatsApp
                if ($totalTarget > 0) {
                    sleep(rand(5, 7));
                }

                $totalTarget++;
                $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
                $codeVal = $s->kartuRfid?->uid ?? $s->nisn;
                $linkPortal = $baseUrl . '/cek-presensi/' . ($s->nisn ?: $s->id);
                $linkQr = $baseUrl . '/qr/siswa/' . $s->id;

                if ($tab === 'siswa') {
                    $pesan = "🔔 *PORTAL PRESENSI MANDIRI SISWA & ORTU — {$namaSekolah}*\n\n"
                           . "Yth. *{$s->nama}*:\n"
                           . "🏷️ *NISN:* " . ($s->nisn ?: '-') . "\n"
                           . "🏫 *Kelas:* {$rombelNama}\n\n"
                           . "Berikut akses portal presensi dan QR Code presensi Anda:\n\n"
                           . "📱 *Buka Portal Presensi Mandiri:*\n"
                           . "{$linkPortal}\n\n"
                           . "📥 *Download Gambar QR Code Langsung:*\n"
                           . "{$linkQr}\n\n"
                           . "_Simpan gambar QR di HP atau tunjukkan saat tiba di scanner gerbang sekolah._";
                } else {
                    $pesan = "🔔 *PORTAL PRESENSI SISWA & ORANG TUA — {$namaSekolah}*\n\n"
                           . "Yth. Bapak/Ibu Orang Tua / Wali dari:\n"
                           . "👤 *Nama Siswa:* {$s->nama}\n"
                           . "🏷️ *NISN:* " . ($s->nisn ?: '-') . "\n"
                           . "🏫 *Kelas:* {$rombelNama}\n\n"
                           . "Berikut akses portal presensi dan QR Code ananda:\n\n"
                           . "📱 *Buka Portal Presensi Mandiri:*\n"
                           . "{$linkPortal}\n\n"
                           . "📥 *Download Gambar QR Code Langsung:*\n"
                           . "{$linkQr}\n\n"
                           . "_Portal ini dapat digunakan untuk memantau kehadiran, jadwal, dan rekap ananda secara berkala._";
                }

                $res = $waService->kirimDirect($noHp, $pesan, 'KARTU PRESENSI DIGITAL');
                if ($res['success'] ?? false) {
                    $totalTerkirim++;
                }
            }

            $labelTarget = ($tab === 'siswa') ? 'Siswa' : 'Orang Tua / Wali';
            return redirect()->back()->with('success', "Broadcast QR Code {$labelTarget} berhasil diproses. Terkirim ke {$totalTerkirim} kontak dengan jeda delay aman 5-7 detik.");
        } else {
            // ── 4. PENGIRIMAN MASSAL (BROADCAST) SELURUH GURU ──
            $gurus = Guru::where('status', 'aktif')->with('kartuRfid')->get();

            foreach ($gurus as $g) {
                $noHp = $g->no_hp;
                if (!$noHp) continue;

                // Jeda delay aman 5-7 detik antar nomor
                if ($totalTarget > 0) {
                    sleep(rand(5, 7));
                }

                $totalTarget++;
                $codeVal = $g->kartuRfid?->uid ?? ($g->nip ?: 'GURU-'.$g->id);
                $linkKartu = $baseUrl . '/kartu-digital-guru/' . $g->id;
                $linkQr = $baseUrl . '/qr/guru/' . $g->id;

                $pesan = "🔔 *KARTU PRESENSI DIGITAL GURU & STAF — {$namaSekolah}*\n\n"
                       . "Yth. Bapak/Ibu:\n"
                       . "👤 *Nama:* {$g->nama}\n"
                       . "🏷️ *NIP:* " . ($g->nip ?: '-') . "\n"
                       . "💼 *Jabatan:* {$g->jabatan}\n\n"
                       . "Berikut akses kartu dan QR Code presensi mandiri Anda:\n\n"
                       . "📱 *Buka Kartu Presensi Digital:*\n"
                       . "{$linkKartu}\n\n"
                       . "📥 *Download Gambar QR Code Langsung:*\n"
                       . "{$linkQr}\n\n"
                       . "_Simpan QR di HP untuk melakukan presensi mandiri pada scanner gerbang sekolah._";

                $res = $waService->kirimDirect($noHp, $pesan, 'KARTU PRESENSI GURU');
                if ($res['success'] ?? false) {
                    $totalTerkirim++;
                }
            }

            return redirect()->back()->with('success', "Broadcast QR Code Guru/Staf berhasil diproses. Terkirim ke {$totalTerkirim} kontak dengan jeda delay aman 5-7 detik.");
        }
    }

    /**
     * Kirim Pesan Barcode & Kartu Digital Personal via WhatsApp Gateway
     */
    public function kirimWaPersonal(Request $request, \App\Services\WhatsAppNotificationService $waService): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:siswa,guru',
            'id'   => 'required',
        ]);

        $type = $request->input('type');
        $id = $request->input('id');
        $sekolah = PengaturanSekolah::getAktif();
        $namaSekolah = $sekolah->nama_sekolah ?? 'SMKN 1 AIR NANINGAN';
        $baseUrl = self::getPublicBaseUrl();

        if ($type === 'siswa') {
            $siswa = Siswa::with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel');
            }, 'kartuRfid'])->where('id', $id)->orWhere('nisn', $id)->first();

            if (!$siswa) {
                return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
            }

            $noHp = $siswa->no_hp_siswa ?: $siswa->no_hp_ortu;
            if (!$noHp) {
                return response()->json(['success' => false, 'message' => 'Nomor WhatsApp siswa belum terisi di data siswa (no_hp_siswa / no_hp_ortu).'], 422);
            }

            $rombelNama = $siswa->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
            $codeVal = $siswa->kartuRfid?->uid ?? ($siswa->nisn ?: $siswa->id);
            $linkPortal = $baseUrl . '/cek-presensi/' . ($siswa->nisn ?: $siswa->id);
            $linkQr = $baseUrl . '/qr/siswa/' . $siswa->id;

            $pesan = "🔔 *PORTAL PRESENSI SISWA & ORANG TUA — {$namaSekolah}*\n\n"
                   . "Yth. Orang Tua / Siswa:\n"
                   . "👤 *Nama:* {$siswa->nama}\n"
                   . "🏷️ *NISN:* " . ($siswa->nisn ?: '-') . "\n"
                   . "🏫 *Kelas:* {$rombelNama}\n\n"
                   . "Berikut akses portal presensi dan QR Code presensi Anda:\n\n"
                   . "📱 *Buka Portal Presensi Mandiri:*\n"
                   . "{$linkPortal}\n\n"
                   . "📥 *Download Gambar QR Code Langsung:*\n"
                   . "{$linkQr}\n\n"
                   . "_Simpan gambar QR di HP atau tunjukkan saat tiba di scanner gerbang sekolah._";

            $res = $waService->kirimDirect($noHp, $pesan, 'KARTU PRESENSI DIGITAL');

            return response()->json([
                'success' => $res['success'] ?? false,
                'message' => ($res['success'] ?? false)
                    ? "Kartu Presensi Digital {$siswa->nama} berhasil dikirim ke WhatsApp ({$noHp}) via WA Gateway!"
                    : ($res['message'] ?? 'Gagal mengirim pesan via WhatsApp Gateway.'),
            ]);
        } else {
            $guru = Guru::with('kartuRfid')->find($id);

            if (!$guru) {
                return response()->json(['success' => false, 'message' => 'Data guru tidak ditemukan.'], 404);
            }

            $noHp = $guru->no_hp;
            if (!$noHp) {
                return response()->json(['success' => false, 'message' => 'Nomor WhatsApp guru/pegawai belum terisi.'], 422);
            }

            $codeVal = $guru->kartuRfid?->uid ?? ($guru->nip ?: 'GURU-'.$guru->id);
            $linkKartu = $baseUrl . '/kartu-digital-guru/' . $guru->id;
            $linkQr = $baseUrl . '/qr/guru/' . $guru->id;

            $pesan = "🔔 *KARTU PRESENSI DIGITAL GURU & STAF — {$namaSekolah}*\n\n"
                   . "Yth. Bapak/Ibu:\n"
                   . "👤 *Nama:* {$guru->nama}\n"
                   . "🏷️ *NIP:* " . ($guru->nip ?: '-') . "\n"
                   . "💼 *Jabatan:* {$guru->jabatan}\n\n"
                   . "Berikut akses kartu dan QR Code presensi mandiri Anda:\n\n"
                   . "📱 *Buka Kartu Presensi Digital:*\n"
                   . "{$linkKartu}\n\n"
                   . "📥 *Download Gambar QR Code Langsung:*\n"
                   . "{$linkQr}\n\n"
                   . "_Simpan QR di HP untuk melakukan presensi mandiri pada scanner gerbang sekolah._";

            $res = $waService->kirimDirect($noHp, $pesan, 'KARTU PRESENSI GURU');

            return response()->json([
                'success' => $res['success'] ?? false,
                'message' => ($res['success'] ?? false)
                    ? "Kartu Presensi Digital {$guru->nama} berhasil dikirim ke WhatsApp ({$noHp}) via WA Gateway!"
                    : ($res['message'] ?? 'Gagal mengirim pesan via WhatsApp Gateway.'),
            ]);
        }
    }

    /**
     * Halaman Kios Tap Mandiri / Gerbang RFID
     */
    public function kiosk()
    {
        $jadwal = JadwalHariIni::getJadwalAktif();
        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');
        $libur = HariLibur::where('tanggal', Carbon::today()->toDateString())->first();
        $pengumumanKios = Pengumuman::where('tampilkan_di_kios', true)->latest()->first();

        $today = Carbon::today()->toDateString();

        // Hitung statistik hari ini
        $totalSiswaAktif = Siswa::whereIn('status', ['aktif', 'pkl'])->count();
        $totalKartuRfid = KartuRfid::where('status', 'aktif')->count();
        $totalHadirHariIni = Absensi::where('tanggal', $today)->where('status', 'hadir')->count();
        $totalTerlambatHariIni = Absensi::where('tanggal', $today)->where('status', 'terlambat')->count();
        $totalPulangHariIni = Absensi::where('tanggal', $today)->whereNotNull('jam_pulang')->count();

        // Ambil 5 scan terakhir hari ini
        $initialRecentScans = Absensi::where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->with(['siswa.siswaRombel.rombel', 'guru'])
            ->get();

        return view('rfid.kiosk', compact(
            'jadwal',
            'hariIni',
            'libur',
            'pengumumanKios',
            'totalSiswaAktif',
            'totalKartuRfid',
            'totalHadirHariIni',
            'totalTerlambatHariIni',
            'totalPulangHariIni',
            'initialRecentScans'
        ));
    }


    /**
     * Endpoint API / Web untuk pemrosesan Tap Kartu RFID
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'uid' => 'required|string|min:3',
        ]);

        try {
            $result = $this->rfidService->scanRfid($request->input('uid'), 'kios_rfid');
            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => $e->getMessage(),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pairing / Hubungkan Kartu RFID ke Siswa atau Guru (Admin/Piket)
     */
    public function pair(Request $request): JsonResponse
    {
        $request->validate([
            'uid'           => 'required|string|min:3',
            'pemilik_type'  => 'required|in:siswa,guru',
            'pemilik_id'    => 'required|integer',
        ]);

        try {
            $kartu = KartuRfid::pair(
                $request->input('uid'),
                $request->input('pemilik_type'),
                $request->input('pemilik_id')
            );

            return response()->json([
                'success' => true,
                'message' => "Kartu RFID ({$kartu->uid}) berhasil dipasangkan.",
                'data'    => $kartu,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memasangkan kartu RFID: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Lepas / Nonaktifkan Kartu RFID
     */
    public function unpair(Request $request): JsonResponse
    {
        $request->validate([
            'pemilik_type' => 'required|in:siswa,guru',
            'pemilik_id'   => 'required|integer',
        ]);

        try {
            KartuRfid::where('pemilik_type', $request->input('pemilik_type'))
                ->where('pemilik_id', $request->input('pemilik_id'))
                ->where('status', 'aktif')
                ->update([
                    'status' => 'nonaktif',
                    'tanggal_nonaktif' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Kartu RFID berhasil dinonaktifkan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menonaktifkan kartu: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Generate QR Code PNG server-side (digunakan sebagai link download di pesan WA).
     * Endpoint publik: GET /qr/{type}/{id}.png
     */
    public function generateQrImage(Request $request, string $type, string $id): Response
    {
        abort_unless(in_array($type, ['guru', 'siswa']), 404);

        if ($type === 'guru') {
            $guru = Guru::with('kartuRfid')->findOrFail($id);
            $codeVal = $guru->kartuRfid?->uid ?? ($guru->nip ?: 'GURU-' . $guru->id);
            $filename = 'QR_Guru_' . preg_replace('/[^A-Za-z0-9]/', '_', $guru->nama);
        } else {
            $siswa = Siswa::with('kartuRfid')->where('id', $id)->orWhere('nisn', $id)->firstOrFail();
            $codeVal = $siswa->kartuRfid?->uid ?? ($siswa->nisn ?: $siswa->id);
            $filename = 'QR_Siswa_' . preg_replace('/[^A-Za-z0-9]/', '_', $siswa->nama);
        }

        $options = new QROptions([
            'outputType'    => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'      => QRCode::ECC_H,
            'scale'         => 14,
            'addQuietzone'  => true,
            'quietzoneSize' => 4,
            'imageBase64'   => false,
        ]);

        $qr = (new QRCode($options))->render($codeVal);

        return response($qr, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.png"',
            'Cache-Control'       => 'public, max-age=3600',
        ]);
    }
}
