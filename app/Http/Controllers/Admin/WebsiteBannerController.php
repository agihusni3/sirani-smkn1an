<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\WebsiteBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteBannerController extends Controller
{
    /**
     * Tampilkan daftar hero & banner website.
     */
    public function index()
    {
        $banners = WebsiteBanner::orderBy('urutan', 'asc')->get();
        $totalAktif = $banners->where('is_active', true)->count();

        return view('admin.banner.index', compact('banners', 'totalAktif'));
    }

    /**
     * Form tambah banner baru.
     */
    public function create()
    {
        $nextUrutan = (WebsiteBanner::max('urutan') ?? 0) + 1;
        return view('admin.banner.create', compact('nextUrutan'));
    }

    /**
     * Simpan banner baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'posisi'         => 'required|string|in:hero_home,top_bar,popup_modal,ppdb_callout',
            'posisi_teks'    => 'nullable|string|in:left,center,right',
            'judul'          => 'required|string|max:255',
            'subjudul'       => 'nullable|string|max:600',
            'badge_text'     => 'nullable|string|max:100',
            'tag_overlay'    => 'nullable|string|max:255',
            'tombol_teks_1'  => 'nullable|string|max:60',
            'tombol_url_1'   => 'nullable|string|max:255',
            'tombol_teks_2'  => 'nullable|string|max:60',
            'tombol_url_2'   => 'nullable|string|max:255',
            'tombol_teks_3'  => 'nullable|string|max:60',
            'tombol_url_3'   => 'nullable|string|max:255',
            'urutan'         => 'required|integer|min:1',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        $validated['posisi_teks'] = $request->input('posisi_teks', 'left');

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $banner = WebsiteBanner::create($validated);

        // Audit Trail Web Humas
        AuditLog::catat(
            'create',
            'web_humas',
            "Menambahkan banner website baru: '{$banner->judul}' (Posisi: {$banner->posisi})",
            null,
            ['id' => $banner->id, 'judul' => $banner->judul, 'posisi' => $banner->posisi, 'is_active' => $banner->is_active],
            $banner
        );

        return redirect()->route('admin.banner.index')
            ->with('success', 'Banner Website baru berhasil dipublikasikan!');
    }

    /**
     * Form edit banner.
     */
    public function edit(WebsiteBanner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    /**
     * Simpan perubahan banner.
     */
    public function update(Request $request, WebsiteBanner $banner)
    {
        $oldData = ['judul' => $banner->judul, 'posisi' => $banner->posisi, 'is_active' => $banner->is_active, 'urutan' => $banner->urutan];

        $validated = $request->validate([
            'posisi'         => 'required|string|in:hero_home,top_bar,popup_modal,ppdb_callout',
            'posisi_teks'    => 'nullable|string|in:left,center,right',
            'judul'          => 'required|string|max:255',
            'subjudul'       => 'nullable|string|max:600',
            'badge_text'     => 'nullable|string|max:100',
            'tag_overlay'    => 'nullable|string|max:255',
            'tombol_teks_1'  => 'nullable|string|max:60',
            'tombol_url_1'   => 'nullable|string|max:255',
            'tombol_teks_2'  => 'nullable|string|max:60',
            'tombol_url_2'   => 'nullable|string|max:255',
            'tombol_teks_3'  => 'nullable|string|max:60',
            'tombol_url_3'   => 'nullable|string|max:255',
            'urutan'         => 'required|integer|min:1',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        $validated['posisi_teks'] = $request->input('posisi_teks', 'left');

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada di disk
            if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
                Storage::disk('public')->delete($banner->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);

        // Audit Trail Web Humas
        AuditLog::catat(
            'update',
            'web_humas',
            "Memperbarui banner website: '{$banner->judul}'",
            $oldData,
            ['id' => $banner->id, 'judul' => $banner->judul, 'posisi' => $banner->posisi, 'is_active' => $banner->is_active],
            $banner
        );

        return redirect()->route('admin.banner.index')
            ->with('success', 'Banner Website berhasil diperbarui!');
    }

    /**
     * Hapus banner.
     */
    public function destroy(WebsiteBanner $banner)
    {
        $oldData = ['id' => $banner->id, 'judul' => $banner->judul, 'posisi' => $banner->posisi];
        $judul = $banner->judul;

        if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
            Storage::disk('public')->delete($banner->gambar);
        }

        $banner->delete();

        // Audit Trail Web Humas
        AuditLog::catat(
            'delete',
            'web_humas',
            "Menghapus banner website: '{$judul}'",
            $oldData,
            null,
            $banner
        );

        return redirect()->route('admin.banner.index')
            ->with('success', 'Hero Banner berhasil dihapus.');
    }

    /**
     * Toggle cepat status aktif/nonaktif.
     */
    public function toggle(WebsiteBanner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();

        $status = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';

        // Audit Trail Web Humas
        AuditLog::catat(
            'update',
            'web_humas',
            "Mengubah status tayang banner '{$banner->judul}' menjadi {$status}",
            null,
            ['id' => $banner->id, 'judul' => $banner->judul, 'is_active' => $banner->is_active],
            $banner
        );

        return redirect()->route('admin.banner.index')
            ->with('success', "Banner '{$banner->judul}' berhasil {$status}.");
    }
}
