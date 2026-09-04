<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Berita &amp; Pengumuman Sekolah — SIRANI</title>
  @include('partials.styles')
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    
    {{-- HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:17px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:8px;">
            <i class="bi bi-newspaper" style="color:#0ea5e9; font-size:18px;"></i> Berita &amp; Pengumuman Website Sekolah
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:12px; color:var(--text-3);">
            Publikasi Konten Beranda &amp; Portal Informasi Vokasi
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('admin.berita.create') }}" class="btn btn-sm btn-primary" style="font-weight:700; border-radius:6px; font-size:12px; padding:6px 14px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-plus-lg"></i> Tambah Artikel Baru
          </a>
          <a href="{{ route('web.berita.index') }}" target="_blank" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); font-weight:700; border-radius:6px; font-size:12px; padding:6px 14px;">
            <i class="bi bi-box-arrow-up-right"></i> Lihat di Web
          </a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="panel" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif

    {{-- TABEL ARTIKEL --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; font-size:12.5px; margin:0;">
          <thead>
            <tr style="background:var(--surface); border-bottom:2px solid var(--border); text-align:left;">
              <th style="padding:10px 14px;">Sampul</th>
              <th style="padding:10px 14px;">Judul Artikel</th>
              <th style="padding:10px 14px;">Kategori</th>
              <th style="padding:10px 14px;">Penulis</th>
              <th style="padding:10px 14px; text-align:center;">Status</th>
              <th style="padding:10px 14px; text-align:center;">Dilihat</th>
              <th style="padding:10px 14px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($beritas as $b)
              <tr style="border-bottom:1px solid var(--border); vertical-align:middle;">
                <td style="padding:10px 14px; width:70px;">
                  @if($b->gambar_sampul)
                    <img src="{{ asset('storage/' . $b->gambar_sampul) }}" alt="Sampul" style="width:60px; height:42px; object-fit:cover; border-radius:4px; border:1px solid var(--border);">
                  @else
                    <div style="width:60px; height:42px; background:var(--surface); border-radius:4px; display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:16px;">
                      <i class="bi bi-image"></i>
                    </div>
                  @endif
                </td>
                <td style="padding:10px 14px;">
                  <div style="font-weight:800; font-size:13px; color:var(--text);">{{ $b->judul }}</div>
                  <div style="font-size:11px; color:var(--text-3);">
                    {{ $b->tanggal_publikasi ? \Carbon\Carbon::parse($b->tanggal_publikasi)->translatedFormat('d M Y, H:i') : '-' }}
                  </div>
                </td>
                <td style="padding:10px 14px;">
                  <span style="font-size:11px; font-weight:700; text-transform:uppercase; padding:3px 8px; border-radius:4px; background:var(--surface); border:1px solid var(--border);">
                    {{ $b->kategori }}
                  </span>
                </td>
                <td style="padding:10px 14px; color:var(--text-2);">
                  {{ $b->author_name ?? 'Humas' }}
                </td>
                <td style="padding:10px 14px; text-align:center;">
                  @if($b->status == 'published')
                    <span style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:800; font-size:11px;">Tayang</span>
                  @else
                    <span style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-weight:800; font-size:11px;">Draft</span>
                  @endif
                </td>
                <td style="padding:10px 14px; text-align:center; font-family:var(--font-mono); font-weight:700;">
                  {{ $b->views }}
                </td>
                <td style="padding:10px 14px; text-align:center; white-space:nowrap;">
                  <a href="{{ route('admin.berita.edit', $b->id) }}" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); padding:4px 8px; font-size:11px; border-radius:4px;">
                    <i class="bi bi-pencil-square"></i> Edit
                  </a>
                  <form action="{{ route('admin.berita.destroy', $b->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus artikel ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm" style="background:#fee2e2; border:1px solid #fecaca; color:#b91c1c; padding:4px 8px; font-size:11px; border-radius:4px;">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="padding:30px; text-align:center; color:var(--text-3);">
                  Belum ada artikel berita yang dipublikasikan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="padding:12px; border-top:1px solid var(--border);">
        {{ $beritas->links() }}
      </div>
    </div>

  </main>
</div>
</body>
</html>
