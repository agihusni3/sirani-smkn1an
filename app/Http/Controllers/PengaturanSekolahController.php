<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanSekolahController extends Controller
{
    /**
     * Tampilkan form profil sekolah & kop surat.
     */
    public function index()
    {
        $sekolah = PengaturanSekolah::getAktif();
        return view('pengaturan_sekolah.index', compact('sekolah'));
    }

    /**
     * Simpan perubahan profil sekolah & kop surat.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_instansi_atas' => 'required|string|max:255',
            'nama_dinas'         => 'required|string|max:255',
            'nama_sekolah'       => 'required|string|max:255',
            'npsn'               => 'nullable|string|max:50',
            'alamat'             => 'nullable|string|max:500',
            'desa_kelurahan'     => 'nullable|string|max:100',
            'kecamatan'          => 'nullable|string|max:100',
            'kabupaten'          => 'nullable|string|max:100',
            'provinsi'           => 'nullable|string|max:100',
            'kode_pos'           => 'nullable|string|max:10',
            'telepon'            => 'nullable|string|max:50',
            'email'              => 'nullable|email|max:100',
            'website'            => 'nullable|string|max:100',
            'nama_kepala_sekolah'=> 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:100',
            'logo_provinsi'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:4096',
            'logo_sekolah'       => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:4096',
        ]);

        $sekolah = PengaturanSekolah::getAktif();
        $data = $request->except(['logo_provinsi', 'logo_sekolah', '_token']);

        $data['npsn']           = $data['npsn'] ?? '';
        $data['desa_kelurahan'] = $data['desa_kelurahan'] ?? '';
        $data['kecamatan']      = $data['kecamatan'] ?? '';
        $data['kabupaten']      = $data['kabupaten'] ?? '';
        $data['provinsi']       = $data['provinsi'] ?? '';
        $data['kode_pos']       = $data['kode_pos'] ?? '';
        $data['email']          = $data['email'] ?? '';
        $data['website']        = $data['website'] ?? '';

        if ($request->hasFile('logo_provinsi')) {
            if ($sekolah->logo_provinsi && Storage::disk('public')->exists($sekolah->logo_provinsi)) {
                Storage::disk('public')->delete($sekolah->logo_provinsi);
            }
            $data['logo_provinsi'] = $request->file('logo_provinsi')->store('sekolah', 'public');
        }

        if ($request->hasFile('logo_sekolah')) {
            if ($sekolah->logo_sekolah && Storage::disk('public')->exists($sekolah->logo_sekolah)) {
                Storage::disk('public')->delete($sekolah->logo_sekolah);
            }
            $data['logo_sekolah'] = $request->file('logo_sekolah')->store('sekolah', 'public');
        }

        $sekolah->update($data);

        return redirect()->route('admin.pengaturan-sekolah.index')
            ->with('success', 'Profil sekolah dan format kop dinas berhasil diperbarui!');
    }
}
