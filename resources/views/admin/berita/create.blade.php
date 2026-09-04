<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Artikel Berita — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md);">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('admin.berita.index') }}" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); padding:5px 10px; border-radius:6px; font-size:12px;">
            <i class="bi bi-arrow-left"></i> Kembali
          </a>
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text);">Tambah Berita / Pengumuman Baru</h1>
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

    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:20px; max-width:850px;">
      <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Judul Artikel <span style="color:#ef4444;">*</span></label>
          <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: SMKN 1 Air Naningan Raih Juara LKS Tingkat Provinsi" style="width:100%; padding:8px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Kategori <span style="color:#ef4444;">*</span></label>
            <select name="kategori" required style="width:100%; padding:8px 12px; font-size:12.5px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
              <option value="berita">Berita Kegiatan</option>
              <option value="pengumuman">Pengumuman Sekolah</option>
              <option value="prestasi">Prestasi &amp; Juara</option>
              <option value="agenda">Agenda Kegiatan</option>
            </select>
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Status Publikasi <span style="color:#ef4444;">*</span></label>
            <select name="status" required style="width:100%; padding:8px 12px; font-size:12.5px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
              <option value="published">Tayang (Published)</option>
              <option value="draft">Draft (Disimpan Sementara)</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Ringkasan Singkat (Muncul di Halaman Depan)</label>
          <input type="text" name="ringkasan" value="{{ old('ringkasan') }}" placeholder="Maksimal 1-2 kalimat ringkas..." style="width:100%; padding:8px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Gambar Sampul (Opsional)</label>
          <input type="file" name="gambar_sampul" accept="image/*" style="font-size:12px; color:var(--text-2);">
        </div>

        <div style="margin-bottom:18px;">
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Isi Konten Artikel <span style="color:#ef4444;">*</span></label>
          <textarea name="konten" rows="10" required placeholder="Tuliskan isi berita lengkap di sini..." style="width:100%; padding:10px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text); line-height:1.6;">{{ old('konten') }}</textarea>
        </div>

        <div style="margin-bottom:18px;">
          <label style="display:inline-flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer;">
            <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
            Sematkan / Pin artikel ini di posisi teratas
          </label>
        </div>

        <button type="submit" class="btn btn-primary" style="padding:9px 22px; font-size:13px; font-weight:800; border-radius:6px;">
          <i class="bi bi-send-fill"></i> Simpan &amp; Publikasikan
        </button>
      </form>
    </div>

  </main>
</div>
</body>
</html>
