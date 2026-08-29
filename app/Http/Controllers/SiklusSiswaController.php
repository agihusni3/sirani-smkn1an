<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\TransisiAkademikService;
use Illuminate\Http\Request;
use Exception;

class SiklusSiswaController extends Controller
{
    protected TransisiAkademikService $transisiService;

    public function __construct(TransisiAkademikService $transisiService)
    {
        $this->transisiService = $transisiService;
    }

    public function index(Request $request)
    {
        $search = $request->input('q');
        $rombelId = $request->input('rombel_id');
        $status = $request->input('status');
        $sort = $request->input('sort', 'nama_asc');

        $query = Siswa::with(['siswaRombel.rombel', 'siswaRombel.tahunAjaran']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('siswaRombel.rombel', function ($rq) use ($search) {
                      $rq->where('nama_rombel', 'like', "%{$search}%");
                  });
            });
        }

        if ($rombelId) {
            $query->whereHas('siswaRombel', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        switch ($sort) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nis_asc':
                $query->orderBy('nis', 'asc');
                break;
            case 'nis_desc':
                $query->orderBy('nis', 'desc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $siswas = $query->paginate(20)->withQueryString();

        $rombels = Rombel::withCount(['siswaRombels' => function ($q) {
            $q->where('status_keanggotaan', 'aktif');
        }])->get();

        $tahunAjarans = TahunAjaran::all();
        $allSiswas = Siswa::with(['siswaRombel.rombel'])->where('status', 'aktif')->orderBy('nama')->get();

        return view('siklus_siswa', compact('siswas', 'rombels', 'tahunAjarans', 'allSiswas', 'search', 'rombelId', 'status', 'sort'));
    }

    public function processTransisi(Request $request)
    {
        $aksi = $request->input('aksi') ?? $request->input('jenis');
        
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
        ]);

        if (!in_array($aksi, ['naik', 'naik_kelas', 'tinggal', 'tinggal_kelas', 'lulus', 'pindah', 'keluar', 'mulai_pkl', 'selesai_pkl'])) {
            return redirect()->back()->with('error', 'Jenis transisi akademik tidak valid.');
        }

        try {
            $siswaId = (int) $request->input('siswa_id');
            $siswa = Siswa::findOrFail($siswaId);
            $rombelBaruId = $request->input('rombel_baru_id') ?? $request->input('rombel_tujuan_id');
            $taBaruId = $request->input('tahun_ajaran_baru_id');

            if (in_array($aksi, ['naik', 'naik_kelas'])) {
                if (!$rombelBaruId || !$taBaruId) {
                    return redirect()->back()->with('error', 'Rombel tujuan dan Tahun Ajaran Baru wajib dipilih untuk proses Naik Kelas.');
                }
                $this->transisiService->naikKelas($siswaId, (int) $rombelBaruId, (int) $taBaruId);
                $rombelBaru = Rombel::find($rombelBaruId);
                $msg = "Siswa {$siswa->nama} berhasil dinaikkan ke kelas " . ($rombelBaru->nama_rombel ?? '') . ".";
            } elseif (in_array($aksi, ['tinggal', 'tinggal_kelas'])) {
                if (!$rombelBaruId || !$taBaruId) {
                    return redirect()->back()->with('error', 'Rombel tujuan dan Tahun Ajaran Baru wajib dipilih untuk proses Tinggal Kelas.');
                }
                $this->transisiService->tinggalKelas($siswaId, (int) $rombelBaruId, (int) $taBaruId);
                $rombelBaru = Rombel::find($rombelBaruId);
                $msg = "Siswa {$siswa->nama} berhasil diproses tinggal di kelas " . ($rombelBaru->nama_rombel ?? '') . ".";
            } elseif ($aksi === 'lulus') {
                $this->transisiService->kelulusan($siswaId);
                $msg = "Siswa {$siswa->nama} berhasil diproses LULUS. Status siswa telah diperbarui.";
            } elseif ($aksi === 'pindah') {
                $this->transisiService->pindah($siswaId);
                $msg = "Siswa {$siswa->nama} berhasil diproses PINDAH SEKOLAH.";
            } elseif ($aksi === 'keluar') {
                $this->transisiService->keluar($siswaId);
                $msg = "Siswa {$siswa->nama} berhasil diproses KELUAR.";
            } elseif ($aksi === 'mulai_pkl') {
                $this->transisiService->mulaiPkl($siswaId);
                $msg = "Siswa {$siswa->nama} berhasil ditugaskan PKL. Siswa terbebas dari absensi harian sekolah.";
            } elseif ($aksi === 'selesai_pkl') {
                $this->transisiService->selesaiPkl($siswaId);
                $msg = "Siswa {$siswa->nama} telah menyelesaikan masa PKL dan kembali aktif di sekolah.";
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses transisi: ' . $e->getMessage());
        }
    }

    public function processTransisiMassal(Request $request)
    {
        $request->validate([
            'rombel_asal_id' => 'required|exists:rombels,id',
            'aksi_massal' => 'required|in:naik_kelas,lulus,tinggal_kelas,mulai_pkl,selesai_pkl',
        ]);

        $rombelAsalId = (int) $request->input('rombel_asal_id');
        $aksi = $request->input('aksi_massal');
        $rombelAsal = Rombel::findOrFail($rombelAsalId);
        $excludedSiswaIds = $request->input('exclude_siswa_ids', []);

        try {
            if ($aksi === 'naik_kelas') {
                $request->validate([
                    'rombel_tujuan_id' => 'required|exists:rombels,id',
                    'tahun_ajaran_baru_id' => 'required|exists:tahun_ajarans,id',
                ]);
                $rombelTujuanId = (int) $request->input('rombel_tujuan_id');
                $taBaruId = (int) $request->input('tahun_ajaran_baru_id');
                $rombelTujuan = Rombel::findOrFail($rombelTujuanId);

                $count = $this->transisiService->batchNaikKelas($rombelAsalId, $rombelTujuanId, $taBaruId, $excludedSiswaIds);
                $msg = "Sukses! Sebanyak {$count} siswa dari rombel {$rombelAsal->nama_rombel} berhasil dinaikkan kelas ke {$rombelTujuan->nama_rombel}.";
            } elseif ($aksi === 'lulus') {
                $count = $this->transisiService->batchKelulusan($rombelAsalId, $excludedSiswaIds);
                $msg = "Sukses! Sebanyak {$count} siswa dari rombel {$rombelAsal->nama_rombel} berhasil diproses LULUS.";
            } elseif ($aksi === 'tinggal_kelas') {
                $request->validate([
                    'rombel_tujuan_id' => 'required|exists:rombels,id',
                    'tahun_ajaran_baru_id' => 'required|exists:tahun_ajarans,id',
                ]);
                $rombelTujuanId = (int) $request->input('rombel_tujuan_id');
                $taBaruId = (int) $request->input('tahun_ajaran_baru_id');

                $count = $this->transisiService->batchTinggalKelas($rombelAsalId, $rombelTujuanId, $taBaruId, $excludedSiswaIds);
                $msg = "Sukses! Sebanyak {$count} siswa dari rombel {$rombelAsal->nama_rombel} diproses tinggal kelas di tahun ajaran baru.";
            } elseif ($aksi === 'mulai_pkl') {
                $count = $this->transisiService->batchPkl($rombelAsalId, $excludedSiswaIds);
                $msg = "Sukses! Sebanyak {$count} siswa dari rombel {$rombelAsal->nama_rombel} berhasil ditugaskan PKL (bebas absensi sekolah).";
            } elseif ($aksi === 'selesai_pkl') {
                $count = $this->transisiService->batchSelesaiPkl($rombelAsalId, $excludedSiswaIds);
                $msg = "Sukses! Sebanyak {$count} siswa dari rombel {$rombelAsal->nama_rombel} telah selesai PKL dan kembali aktif.";
            }

            return redirect()->back()->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses aksi massal: ' . $e->getMessage());
        }
    }
}
