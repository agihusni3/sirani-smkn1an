<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\PpdbSoalUjian;
use App\Models\PpdbUjianSetting;
use Illuminate\Http\Request;

class PpdbSoalController extends Controller
{
    /** Daftar bank soal untuk setting ujian tertentu */
    public function index($settingId)
    {
        $setting = PpdbUjianSetting::with(['soals'])->findOrFail($settingId);
        $soalPg   = $setting->soalPg()->get();
        $soalEsai = $setting->soalEsai()->get();

        return view('ppdb.admin.soal.index', compact('setting', 'soalPg', 'soalEsai'));
    }

    /** Form tambah soal baru */
    public function create($settingId)
    {
        $setting = PpdbUjianSetting::findOrFail($settingId);

        // Hitung nomor urut berikutnya
        $lastPg   = PpdbSoalUjian::where('ppdb_ujian_setting_id', $settingId)->where('tipe_soal', 'pg')->max('nomor_urut') ?? 0;
        $lastEsai = PpdbSoalUjian::where('ppdb_ujian_setting_id', $settingId)->where('tipe_soal', 'esai')->max('nomor_urut') ?? 0;
        $nextPg   = min($lastPg + 1, (int) $setting->jumlah_soal_pg);
        $nextEsai = $lastEsai > 0 ? $lastEsai + 1 : (int) $setting->jumlah_soal_pg + 1;

        return view('ppdb.admin.soal.form', compact('setting', 'nextPg', 'nextEsai'));
    }

    /** Simpan soal baru */
    public function store(Request $request, $settingId)
    {
        $setting = PpdbUjianSetting::findOrFail($settingId);

        $validated = $request->validate([
            'tipe_soal'     => 'required|in:pg,esai',
            'nomor_urut'    => 'required|integer|min:1',
            'pertanyaan'    => 'required|string',
            'opsi_a'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_b'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_c'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_d'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_e'        => 'nullable|string',
            'kunci_jawaban' => 'required_if:tipe_soal,pg|nullable|in:A,B,C,D,E',
            'bobot_nilai'   => 'nullable|numeric|min:0',
        ]);

        // Cek duplikat nomor urut
        $exists = PpdbSoalUjian::where('ppdb_ujian_setting_id', $settingId)
                    ->where('nomor_urut', $validated['nomor_urut'])->exists();
        if ($exists) {
            return back()->withInput()->with('error', "Soal nomor {$validated['nomor_urut']} sudah ada. Gunakan nomor lain atau edit soal yang sudah ada.");
        }

        PpdbSoalUjian::create([
            'ppdb_ujian_setting_id' => $settingId,
            'nomor_urut'    => $validated['nomor_urut'],
            'tipe_soal'     => $validated['tipe_soal'],
            'pertanyaan'    => $validated['pertanyaan'],
            'opsi_a'        => $validated['opsi_a'] ?? null,
            'opsi_b'        => $validated['opsi_b'] ?? null,
            'opsi_c'        => $validated['opsi_c'] ?? null,
            'opsi_d'        => $validated['opsi_d'] ?? null,
            'opsi_e'        => $validated['opsi_e'] ?? null,
            'kunci_jawaban' => $validated['kunci_jawaban'] ? strtoupper($validated['kunci_jawaban']) : null,
            'bobot_nilai'   => $validated['bobot_nilai'] ?? 0,
        ]);

        return redirect()->route('admin.ppdb.soal.index', $settingId)
            ->with('success', "Soal no. {$validated['nomor_urut']} berhasil ditambahkan.");
    }

    /** Form edit soal */
    public function edit($id)
    {
        $soal    = PpdbSoalUjian::findOrFail($id);
        $setting = $soal->setting;
        return view('ppdb.admin.soal.form', compact('soal', 'setting'));
    }

    /** Simpan perubahan soal */
    public function update(Request $request, $id)
    {
        $soal = PpdbSoalUjian::findOrFail($id);

        $validated = $request->validate([
            'tipe_soal'     => 'required|in:pg,esai',
            'nomor_urut'    => 'required|integer|min:1',
            'pertanyaan'    => 'required|string',
            'opsi_a'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_b'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_c'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_d'        => 'required_if:tipe_soal,pg|nullable|string',
            'opsi_e'        => 'nullable|string',
            'kunci_jawaban' => 'required_if:tipe_soal,pg|nullable|in:A,B,C,D,E',
            'bobot_nilai'   => 'nullable|numeric|min:0',
        ]);

        // Cek duplikat nomor urut (selain dirinya sendiri)
        $exists = PpdbSoalUjian::where('ppdb_ujian_setting_id', $soal->ppdb_ujian_setting_id)
                    ->where('nomor_urut', $validated['nomor_urut'])
                    ->where('id', '!=', $id)->exists();
        if ($exists) {
            return back()->withInput()->with('error', "Soal nomor {$validated['nomor_urut']} sudah digunakan soal lain.");
        }

        $soal->update([
            'nomor_urut'    => $validated['nomor_urut'],
            'tipe_soal'     => $validated['tipe_soal'],
            'pertanyaan'    => $validated['pertanyaan'],
            'opsi_a'        => $validated['opsi_a'] ?? null,
            'opsi_b'        => $validated['opsi_b'] ?? null,
            'opsi_c'        => $validated['opsi_c'] ?? null,
            'opsi_d'        => $validated['opsi_d'] ?? null,
            'opsi_e'        => $validated['opsi_e'] ?? null,
            'kunci_jawaban' => $validated['kunci_jawaban'] ? strtoupper($validated['kunci_jawaban']) : null,
            'bobot_nilai'   => $validated['bobot_nilai'] ?? 0,
        ]);

        return redirect()->route('admin.ppdb.soal.index', $soal->ppdb_ujian_setting_id)
            ->with('success', "Soal no. {$soal->nomor_urut} berhasil diperbarui.");
    }

    /** Hapus soal */
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
