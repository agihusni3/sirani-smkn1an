<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Artikel — {{ $berita->judul }}</title>
  @include('partials.styles')
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_web')
  <main class="main-content">
    
    <div class="panel no-print" style="background:#f2efe7; border:1px solid #c8dfdb; padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md);">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('admin.berita.index') }}" class="btn btn-sm" style="background:#ffffff; border:1px solid #c8dfdb; color:#000000; font-weight:700; padding:6px 12px; border-radius:6px; font-size:12px; text-decoration:none;">
            <i class="bi bi-arrow-left" style="color:#3368a0;"></i> Kembali
          </a>
          <h1 style="margin:0; font-size:16px; font-weight:900; color:#000000;">Edit Artikel Berita</h1>
        </div>
      </div>
    </div>

    @if($errors->any())
      <div class="panel" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm);">
        <ul style="margin:0; padding-left:18px; font-size:12.5px;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="panel" style="background:#ffffff; border:1px solid #c8dfdb; border-radius:var(--r-sm); padding:24px; max-width:850px; box-shadow:0 2px 8px rgba(0,0,0,0.02);">
      <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">Judul Artikel <span style="color:#ef4444;">*</span></label>
          <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" required style="width:100%; padding:9px 12px; font-size:13px; border-radius:6px; border:1px solid #c8dfdb; background:#ffffff; color:#000000; font-weight:600;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">Kategori <span style="color:#ef4444;">*</span></label>
            <select name="kategori" required style="width:100%; padding:9px 12px; font-size:12.5px; border-radius:6px; border:1px solid #c8dfdb; background:#ffffff; color:#000000; font-weight:700;">
              <option value="berita" {{ old('kategori', $berita->kategori) == 'berita' ? 'selected' : '' }}>Berita Kegiatan</option>
              <option value="pengumuman" {{ old('kategori', $berita->kategori) == 'pengumuman' ? 'selected' : '' }}>Pengumuman Sekolah</option>
              <option value="prestasi" {{ old('kategori', $berita->kategori) == 'prestasi' ? 'selected' : '' }}>Prestasi &amp; Juara</option>
              <option value="agenda" {{ old('kategori', $berita->kategori) == 'agenda' ? 'selected' : '' }}>Agenda Kegiatan</option>
            </select>
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">Status Publikasi <span style="color:#ef4444;">*</span></label>
            <select name="status" required style="width:100%; padding:9px 12px; font-size:12.5px; border-radius:6px; border:1px solid #c8dfdb; background:#ffffff; color:#000000; font-weight:700;">
              <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>Tayang (Published)</option>
              <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>Draft (Disimpan Sementara)</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">Ringkasan Singkat</label>
          <input type="text" name="ringkasan" value="{{ old('ringkasan', $berita->ringkasan) }}" style="width:100%; padding:9px 12px; font-size:13px; border-radius:6px; border:1px solid #c8dfdb; background:#ffffff; color:#000000;">
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">Gambar Sampul</label>
          @if($berita->gambar_sampul)
            <div style="margin-bottom:8px;">
              <img src="{{ asset('storage/' . $berita->gambar_sampul) }}" alt="Sampul" style="max-height:120px; border-radius:6px; border:1px solid #c8dfdb;">
            </div>
          @endif
          <input type="file" name="gambar_sampul" accept="image/*" style="font-size:12px; color:#000000;">
        </div>

        <div style="margin-bottom:18px;">
          <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">Isi Konten Artikel <span style="color:#ef4444;">*</span></label>
          <textarea name="konten" rows="10" required style="width:100%; padding:10px 12px; font-size:13px; border-radius:6px; border:1px solid #c8dfdb; background:#ffffff; color:#000000; line-height:1.6;">{{ old('konten', $berita->konten) }}</textarea>
        </div>

        <div style="margin-bottom:18px;">
          <label style="display:inline-flex; align-items:center; gap:8px; font-size:12.5px; font-weight:700; color:#000000; cursor:pointer;">
            <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $berita->is_pinned) ? 'checked' : '' }}>
            Sematkan / Pin artikel ini di posisi teratas
          </label>
        </div>

        <button type="submit" class="btn" style="background:#3368a0; color:#ffffff; padding:9px 24px; font-size:13px; font-weight:800; border-radius:6px; border:none; cursor:pointer; box-shadow:0 2px 6px rgba(51,104,160,0.25);">
          <i class="bi bi-save-fill"></i> Perbarui Artikel
        </button>
      </form>
    </div>

  </main>
</div>
</body>
</html>
