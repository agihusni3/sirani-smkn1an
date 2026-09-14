<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\PpdbSoalUjian;
use App\Models\PpdbUjianSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PpdbSoalController extends Controller
{
    /**
     * Daftar bank soal untuk setting ujian tertentu
     */
    public function index($settingId)
    {
        $setting = PpdbUjianSetting::with(['soals'])->findOrFail($settingId);
        $soalPg   = $setting->soalPg()->get();
        $soalEsai = $setting->soalEsai()->get();

        return view('ppdb.admin.soal.index', compact('setting', 'soalPg', 'soalEsai'));
    }

    /**
     * Form tambah soal baru
     */
    public function create(Request $request, $settingId)
    {
        $setting = PpdbUjianSetting::findOrFail($settingId);

        // Hitung nomor urut berikutnya secara presisi
        $lastPg   = PpdbSoalUjian::where('ppdb_ujian_setting_id', $settingId)->where('tipe_soal', 'pg')->max('nomor_urut') ?? 0;
        $lastEsai = PpdbSoalUjian::where('ppdb_ujian_setting_id', $settingId)->where('tipe_soal', 'esai')->max('nomor_urut') ?? 0;
        
        $nextPg   = min($lastPg + 1, max(1, (int) $setting->jumlah_soal_pg));
        $nextEsai = $lastEsai > 0 ? $lastEsai + 1 : ((int) $setting->jumlah_soal_pg + 1);

        $type      = $request->query('type', 'pg');
        $nextNomor = ($type === 'esai') ? $nextEsai : $nextPg;

        return view('ppdb.admin.soal.form', compact('setting', 'nextPg', 'nextEsai', 'nextNomor', 'type'));
    }

    /**
     * Simpan soal baru
     */
    public function store(Request $request, $settingId)
    {
        $setting = PpdbUjianSetting::findOrFail($settingId);

        $validated = $request->validate([
            'tipe_soal'     => ['required', 'in:pg,esai'],
            'nomor_urut'    => [
                'required',
                'integer',
                'min:1',
                'max:500',
                Rule::unique('ppdb_soal_ujians', 'nomor_urut')
                    ->where('ppdb_ujian_setting_id', $settingId),
            ],
            'pertanyaan'    => ['required', 'string', 'max:10000'],
            'opsi_a'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_b'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_c'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_d'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_e'        => ['nullable', 'string', 'max:2000'],
            'kunci_jawaban' => ['required_if:tipe_soal,pg', 'nullable', 'in:A,B,C,D,E'],
            'bobot_nilai'   => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'nomor_urut.unique'         => "Nomor urut {$request->nomor_urut} sudah ada di bank soal ujian ini. Gunakan nomor urut lain.",
            'kunci_jawaban.required_if' => 'Kunci jawaban wajib dipilih untuk soal bertipe Pilihan Ganda (PG).',
            'opsi_a.required_if'        => 'Pilihan opsi A wajib diisi untuk soal PG.',
            'opsi_b.required_if'        => 'Pilihan opsi B wajib diisi untuk soal PG.',
            'opsi_c.required_if'        => 'Pilihan opsi C wajib diisi untuk soal PG.',
            'opsi_d.required_if'        => 'Pilihan opsi D wajib diisi untuk soal PG.',
        ]);

        $isPg = ($validated['tipe_soal'] === 'pg');

        DB::transaction(function () use ($settingId, $validated, $isPg) {
            PpdbSoalUjian::create([
                'ppdb_ujian_setting_id' => $settingId,
                'nomor_urut'            => $validated['nomor_urut'],
                'tipe_soal'             => $validated['tipe_soal'],
                'pertanyaan'            => trim($validated['pertanyaan']),
                'opsi_a'                => $isPg ? (trim($validated['opsi_a'] ?? '') ?: null) : null,
                'opsi_b'                => $isPg ? (trim($validated['opsi_b'] ?? '') ?: null) : null,
                'opsi_c'                => $isPg ? (trim($validated['opsi_c'] ?? '') ?: null) : null,
                'opsi_d'                => $isPg ? (trim($validated['opsi_d'] ?? '') ?: null) : null,
                'opsi_e'                => $isPg ? (trim($validated['opsi_e'] ?? '') ?: null) : null,
                'kunci_jawaban'         => $isPg && !empty($validated['kunci_jawaban']) ? strtoupper($validated['kunci_jawaban']) : null,
                'bobot_nilai'           => $validated['bobot_nilai'] ?? 0,
            ]);
        });

        return redirect()->route('admin.ppdb.soal.index', $settingId)
            ->with('success', "Soal no. {$validated['nomor_urut']} berhasil ditambahkan ke bank soal.");
    }

    /**
     * Form edit soal
     */
    public function edit($id)
    {
        $soal    = PpdbSoalUjian::findOrFail($id);
        $setting = $soal->setting;
        
        return view('ppdb.admin.soal.form', compact('soal', 'setting'));
    }

    /**
     * Simpan perubahan soal
     */
    public function update(Request $request, $id)
    {
        $soal = PpdbSoalUjian::findOrFail($id);

        $validated = $request->validate([
            'tipe_soal'     => ['required', 'in:pg,esai'],
            'nomor_urut'    => [
                'required',
                'integer',
                'min:1',
                'max:500',
                Rule::unique('ppdb_soal_ujians', 'nomor_urut')
                    ->where('ppdb_ujian_setting_id', $soal->ppdb_ujian_setting_id)
                    ->ignore($id),
            ],
            'pertanyaan'    => ['required', 'string', 'max:10000'],
            'opsi_a'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_b'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_c'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_d'        => ['required_if:tipe_soal,pg', 'nullable', 'string', 'max:2000'],
            'opsi_e'        => ['nullable', 'string', 'max:2000'],
            'kunci_jawaban' => ['required_if:tipe_soal,pg', 'nullable', 'in:A,B,C,D,E'],
            'bobot_nilai'   => ['nullable', 'numeric', 'min:0', 'max:100'],
        ], [
            'nomor_urut.unique'         => "Nomor urut {$request->nomor_urut} sudah digunakan soal lain.",
            'kunci_jawaban.required_if' => 'Kunci jawaban wajib dipilih untuk soal bertipe Pilihan Ganda (PG).',
            'opsi_a.required_if'        => 'Pilihan opsi A wajib diisi untuk soal PG.',
            'opsi_b.required_if'        => 'Pilihan opsi B wajib diisi untuk soal PG.',
            'opsi_c.required_if'        => 'Pilihan opsi C wajib diisi untuk soal PG.',
            'opsi_d.required_if'        => 'Pilihan opsi D wajib diisi untuk soal PG.',
        ]);

        $isPg = ($validated['tipe_soal'] === 'pg');

        DB::transaction(function () use ($soal, $validated, $isPg) {
            $soal->update([
                'nomor_urut'    => $validated['nomor_urut'],
                'tipe_soal'     => $validated['tipe_soal'],
                'pertanyaan'    => trim($validated['pertanyaan']),
                'opsi_a'        => $isPg ? (trim($validated['opsi_a'] ?? '') ?: null) : null,
                'opsi_b'        => $isPg ? (trim($validated['opsi_b'] ?? '') ?: null) : null,
                'opsi_c'        => $isPg ? (trim($validated['opsi_c'] ?? '') ?: null) : null,
                'opsi_d'        => $isPg ? (trim($validated['opsi_d'] ?? '') ?: null) : null,
                'opsi_e'        => $isPg ? (trim($validated['opsi_e'] ?? '') ?: null) : null,
                'kunci_jawaban' => $isPg && !empty($validated['kunci_jawaban']) ? strtoupper($validated['kunci_jawaban']) : null,
                'bobot_nilai'   => $validated['bobot_nilai'] ?? $soal->bobot_nilai ?? 0,
            ]);
        });

        return redirect()->route('admin.ppdb.soal.index', $soal->ppdb_ujian_setting_id)
            ->with('success', "Soal no. {$soal->nomor_urut} berhasil diperbarui.");
    }

    /**
     * Hapus soal
     */
    public function destroy($id)
    {
        $soal      = PpdbSoalUjian::findOrFail($id);
        $settingId = $soal->ppdb_ujian_setting_id;
        $nomor     = $soal->nomor_urut;
        
        $soal->delete();

        return redirect()->route('admin.ppdb.soal.index', $settingId)
            ->with('success', "Soal no. {$nomor} berhasil dihapus.");
    }
}
