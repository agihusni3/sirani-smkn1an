<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AkademikAsesmenPanitia;
use App\Models\AkademikAsesmenPeriode;
use App\Models\AuditLog;
use App\Models\Guru;
use App\Models\PengaturanSekolah;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KepanitiaanAsesmenController extends Controller
{
    public function index(Request $request)
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        
        $query = AkademikAsesmenPeriode::with(['tahunAjaran', 'panitias.guru', 'creator'])
            ->withCount('panitias');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('nama_event', 'like', "%{$q}%")
                    ->orWhere('sk_nomor', 'like', "%{$q}%");
            });
        }

        $periodes = $query->latest()->paginate(10)->withQueryString();
        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();

        return view('akademik.kepanitiaan.index', compact('periodes', 'ta', 'tahunAjarans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'semester'        => 'required|in:1,2',
            'nama_event'      => 'required|string|max:255',
            'jenis_asesmen'   => 'required|in:pts,pas,pat,us,anbk,ukk,lainnya',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'sk_nomor'        => 'nullable|string|max:100',
            'sk_tanggal'      => 'nullable|date',
            'keterangan'      => 'nullable|string',
        ]);

        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        // Auto nomor SK jika kosong
        if (empty($validated['sk_nomor'])) {
            $tahun = Carbon::parse($validated['tanggal_mulai'])->year;
            $count = AkademikAsesmenPeriode::whereYear('tanggal_mulai', $tahun)->count() + 1;
            $pad = str_pad($count, 3, '0', STR_PAD_LEFT);
            $validated['sk_nomor'] = "421.5/{$pad}/SK-PANITIA/V.01/DP.2/{$tahun}";
        }

        $periode = AkademikAsesmenPeriode::create($validated);

        // Auto-assign Waka Kurikulum sebagai Pengarah jika terdata
        $wakaKurikulum = Guru::where('jabatan', 'like', '%kurikulum%')
            ->orWhere('tugas_tambahan', 'like', '%kurikulum%')
            ->first();

        if ($wakaKurikulum) {
            AkademikAsesmenPanitia::create([
                'periode_id'   => $periode->id,
                'guru_id'      => $wakaKurikulum->id,
                'peran'        => 'pengarah',
                'tugas_khusus' => 'Pengarah Teknis & Regulasi Kurikulum',
                'is_active'    => true,
            ]);
        }

        // Auto-assign Kepala Sekolah sebagai Penanggung Jawab
        $kepsek = Guru::where('jabatan', 'like', '%kepala sekolah%')->first();
        if ($kepsek && $kepsek->id !== ($wakaKurikulum?->id)) {
            AkademikAsesmenPanitia::create([
                'periode_id'   => $periode->id,
                'guru_id'      => $kepsek->id,
                'peran'        => 'penanggung_jawab',
                'tugas_khusus' => 'Penanggung Jawab Kebijakan Satuan Pendidikan',
                'is_active'    => true,
            ]);
        }

        AuditLog::catat('buat_event_asesmen', 'akademik', "Menetapkan periode asesmen: {$periode->nama_event}");

        return redirect()->route('akademik.kepanitiaan.show', $periode->id)
            ->with('success', 'Event asesmen berhasil ditetapkan! Silakan susun struktur personalia kepanitiaan.');
    }

    public function show($id)
    {
        $periode = AkademikAsesmenPeriode::with(['tahunAjaran', 'panitias.guru', 'creator'])->findOrFail($id);
        
        // Urutan hierarki peran kepanitiaan
        $urutanPeran = [
            'penanggung_jawab' => 1,
            'pengarah'         => 2,
            'ketua'            => 3,
            'sekretaris'       => 4,
            'bendahara'        => 5,
            'proktor_utama'    => 6,
            'teknisi'          => 7,
            'koordinator_soal' => 8,
            'pengawas'         => 9,
            'anggota'          => 10,
        ];

        $panitias = $periode->panitias->sortBy(function($item) use ($urutanPeran) {
            return $urutanPeran[$item->peran] ?? 99;
        });

        // Guru-guru aktif yang belum masuk kepanitiaan ini
        $assignedGuruIds = $periode->panitias->pluck('guru_id')->toArray();
        $gurus = Guru::where('status', 'aktif')
            ->whereNotIn('id', $assignedGuruIds)
            ->orderBy('nama')
            ->get();

        return view('akademik.kepanitiaan.show', compact('periode', 'panitias', 'gurus'));
    }

    public function addPanitia(Request $request, $id)
    {
        $periode = AkademikAsesmenPeriode::findOrFail($id);

        $validated = $request->validate([
            'guru_id'      => 'required|exists:gurus,id',
            'peran'        => 'required|in:penanggung_jawab,pengarah,ketua,sekretaris,bendahara,proktor_utama,teknisi,koordinator_soal,pengawas,anggota',
            'tugas_khusus' => 'nullable|string|max:255',
        ]);

        $validated['periode_id'] = $periode->id;
        $validated['is_active'] = true;

        $panitia = AkademikAsesmenPanitia::updateOrCreate(
            ['periode_id' => $periode->id, 'guru_id' => $validated['guru_id']],
            $validated
        );

        $guru = Guru::find($validated['guru_id']);
        AuditLog::catat('tambah_panitia_asesmen', 'akademik', "Menambahkan {$guru->nama} sebagai {$panitia->peran_label} pada event {$periode->nama_event}");

        return redirect()->back()->with('success', "Petugas {$guru->nama} berhasil ditetapkan ke dalam kepanitiaan!");
    }

    public function removePanitia($id, $panitiaId)
    {
        $periode = AkademikAsesmenPeriode::findOrFail($id);
        $panitia = AkademikAsesmenPanitia::where('periode_id', $periode->id)->findOrFail($panitiaId);
        
        $namaGuru = $panitia->guru->nama ?? 'Guru';
        $panitia->delete();

        AuditLog::catat('hapus_panitia_asesmen', 'akademik', "Menghapus {$namaGuru} dari kepanitiaan {$periode->nama_event}");

        return redirect()->back()->with('success', "Petugas {$namaGuru} berhasil dihapus dari kepanitiaan.");
    }

    public function toggleStatus(Request $request, $id)
    {
        $periode = AkademikAsesmenPeriode::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        $periode->update(['status' => $validated['status']]);

        AuditLog::catat('ubah_status_event_asesmen', 'akademik', "Mengubah status event {$periode->nama_event} menjadi {$periode->status}");

        return redirect()->back()->with('success', "Status event asesmen berhasil diubah menjadi: {$periode->status}!");
    }

    public function cetakSk($id)
    {
        $periode = AkademikAsesmenPeriode::with(['tahunAjaran', 'panitias.guru'])->findOrFail($id);
        $sekolah = PengaturanSekolah::getAktif();
        $kepsek = Guru::where('jabatan', 'like', '%kepala sekolah%')->first()
            ?? Guru::where('status', 'aktif')->first();

        $urutanPeran = [
            'penanggung_jawab' => 1,
            'pengarah'         => 2,
            'ketua'            => 3,
            'sekretaris'       => 4,
            'bendahara'        => 5,
            'proktor_utama'    => 6,
            'teknisi'          => 7,
            'koordinator_soal' => 8,
            'pengawas'         => 9,
            'anggota'          => 10,
        ];

        $panitias = $periode->panitias->sortBy(function($item) use ($urutanPeran) {
            return $urutanPeran[$item->peran] ?? 99;
        });

        return view('akademik.kepanitiaan.cetak_sk', compact('periode', 'sekolah', 'kepsek', 'panitias'));
    }
}
