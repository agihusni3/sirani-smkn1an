<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dasbor Publikasi Berita &amp; Humas — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .humas-hero-bar {
      background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 45%, #0284c7 100%);
      border-radius: 16px;
      padding: 22px 24px;
      color: #ffffff;
      margin-bottom: 20px;
      box-shadow: 0 10px 25px rgba(2, 132, 199, 0.18);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .humas-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
      margin-bottom: 20px;
    }
    .humas-stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 16px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all 0.15s ease;
    }
    .humas-stat-card:hover {
      border-color: #0284c7;
      box-shadow: 0 4px 14px rgba(0,0,0,0.05);
    }
    .humas-stat-val {
      font-size: 24px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_web')
  
  <main class="main-content">
    
    {{-- HUMAS HERO HEADER --}}
    <div class="humas-hero-bar no-print">
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.18); color:#e0f2fe; font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; margin-bottom:8px; border:1px solid rgba(255,255,255,0.25);">
          <i class="bi bi-globe-americas"></i> WORKSPACE RESMI HUMAS &amp; WEBSITE
        </div>
        <h1 style="margin:0 0 6px; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
          Pusat Publikasi Berita &amp; Informasi Sekolah
        </h1>
        <p style="margin:0; font-size:13px; color:#bae6fd; max-width:640px;">
          Kelola rilis berita resmi, pengumuman agenda vokasi, artikel prestasi, dan etalase hero banner utama SMKN 1 Air Naningan.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('admin.portal') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:#ffffff; font-weight:800; border-radius:8px; font-size:12px; padding:8px 14px; border:1px solid rgba(255,255,255,0.25); text-decoration:none; display:inline-flex; align-items:center; gap:6px;" title="Buka Digital Command Center SMKN 1 AN">
          <i class="bi bi-command" style="color:#7dd3fc;"></i> DCC SMKN 1 AN
        </a>
        <a href="{{ route('admin.berita.create') }}" class="btn btn-sm" style="background:#ffffff; color:#0369a1; font-weight:900; border-radius:8px; font-size:12px; padding:8px 16px; border:none; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
          <i class="bi bi-plus-lg"></i> Tulis Berita Baru
        </a>
      </div>
    </div>

    @if(session('success'))
      <div class="panel" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; margin-bottom:16px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="panel" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; margin-bottom:16px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> {{ session('error') }}
      </div>
    @endif

    {{-- STATISTIK HUMAS --}}
    <div class="humas-stat-grid">
      <div class="humas-stat-card">
        <div style="width:42px; height:42px; border-radius:10px; background:rgba(14,165,233,0.12); color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-newspaper"></i>
        </div>
        <div>
          <div class="humas-stat-val" style="color:#0284c7;">{{ $counts['total'] ?? $beritas->total() }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Total Artikel &amp; Rilis</div>
        </div>
      </div>

      <div class="humas-stat-card">
        <div style="width:42px; height:42px; border-radius:10px; background:rgba(16,185,129,0.12); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
          <div class="humas-stat-val" style="color:#10b981;">{{ $counts['published'] ?? 0 }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Artikel Tayang Publik</div>
        </div>
      </div>

      <div class="humas-stat-card">
        <div style="width:42px; height:42px; border-radius:10px; background:rgba(245,158,11,0.12); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-images"></i>
        </div>
        <div>
          <div class="humas-stat-val" style="color:#d97706;">{{ $counts['banners'] ?? 0 }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Hero Banner Aktif</div>
        </div>
      </div>

      <div class="humas-stat-card">
        <div style="width:42px; height:42px; border-radius:10px; background:rgba(99,102,241,0.12); color:#6366f1; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-eye-fill"></i>
        </div>
        <div>
          <div class="humas-stat-val" style="color:#6366f1;">{{ number_format($counts['views'] ?? 0) }}</div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">Total Dibaca Publik</div>
        </div>
      </div>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:12px 18px; margin-bottom:16px; border-radius:12px;">
      <form method="GET" action="{{ route('admin.berita.index') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;">
        <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; flex:1;">
          <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari judul artikel atau kata kunci..." style="min-width:280px; flex:1; height:38px; padding:0 14px; font-size:12.5px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
          
          <select name="kategori" onchange="this.form.submit()" style="height:38px; padding:0 12px; font-size:12px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
            <option value="">-- Semua Kategori --</option>
            <option value="berita" {{ request('kategori') == 'berita' ? 'selected' : '' }}>Berita</option>
            <option value="pengumuman" {{ request('kategori') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
            <option value="prestasi" {{ request('kategori') == 'prestasi' ? 'selected' : '' }}>Prestasi</option>
            <option value="agenda" {{ request('kategori') == 'agenda' ? 'selected' : '' }}>Agenda</option>
          </select>

          <button type="submit" class="btn btn-sm" style="background:#0284c7; color:#ffffff; height:38px; padding:0 16px; font-size:12.5px; font-weight:800; border-radius:8px; border:none; cursor:pointer;">
            <i class="bi bi-search"></i> Cari
          </button>

          @if(request()->hasAny(['cari', 'kategori']))
            <a href="{{ route('admin.berita.index') }}" class="btn btn-sm" style="height:38px; padding:0 12px; display:inline-flex; align-items:center; justify-content:center; background:var(--surface); border:1px solid var(--border); color:var(--text); font-size:12px; border-radius:8px; text-decoration:none;">
              Reset Filter
            </a>
          @endif
        </div>

        <a href="{{ route('web.berita.index') }}" target="_blank" class="btn btn-sm" style="height:38px; padding:0 14px; display:inline-flex; align-items:center; gap:6px; background:var(--surface); border:1px solid var(--border); color:var(--text); font-size:12px; font-weight:700; border-radius:8px; text-decoration:none;">
          <i class="bi bi-box-arrow-up-right"></i> Lihat Kabar Sekolah di Web
        </a>
      </form>
    </div>

    {{-- TABEL DATA BERITA --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; font-size:12.5px; margin:0;">
          <thead>
            <tr style="background:var(--surface); border-bottom:2px solid var(--border); text-align:left;">
              <th style="padding:12px 14px; width:70px;">Sampul</th>
              <th style="padding:12px 14px;">Judul Artikel</th>
              <th style="padding:12px 14px;">Kategori</th>
              <th style="padding:12px 14px;">Penulis &amp; Tanggal</th>
              <th style="padding:12px 14px; text-align:center;">Status</th>
              <th style="padding:12px 14px; text-align:center;">Dilihat</th>
              <th style="padding:12px 14px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($beritas as $b)
              <tr style="border-bottom:1px solid var(--border); vertical-align:middle;">
                <td style="padding:10px 14px;">
                  @if($b->gambar_sampul)
                    <img src="{{ asset('storage/' . $b->gambar_sampul) }}" alt="Sampul" style="width:60px; height:42px; object-fit:cover; border-radius:6px; border:1px solid var(--border);">
                  @else
                    <div style="width:60px; height:42px; background:var(--surface); border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:16px; border:1px solid var(--border);">
                      <i class="bi bi-image"></i>
                    </div>
                  @endif
                </td>
                <td style="padding:10px 14px;">
                  <div style="font-weight:800; color:var(--text); font-size:13px; line-height:1.35;">{{ $b->judul }}</div>
                  @if($b->ringkasan)
                    <div style="font-size:11px; color:var(--text-3); margin-top:3px; max-width:480px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                      {{ $b->ringkasan }}
                    </div>
                  @endif
                </td>
                <td style="padding:10px 14px;">
                  <span style="font-size:10.5px; font-weight:800; padding:2px 8px; border-radius:6px; background:rgba(14,165,233,0.1); color:#0284c7; text-transform:uppercase;">
                    {{ $b->kategori }}
                  </span>
                </td>
                <td style="padding:10px 14px; font-size:11.5px; color:var(--text-2);">
                  <div>{{ $b->penulis->name ?? 'Admin Humas' }}</div>
                  <div style="color:var(--text-3); font-size:10.5px; margin-top:2px;">
                    {{ $b->tanggal_publikasi ? \Carbon\Carbon::parse($b->tanggal_publikasi)->format('d M Y, H:i') : $b->created_at->format('d M Y') }}
                  </div>
                </td>
                <td style="padding:10px 14px; text-align:center;">
                  @if($b->is_published)
                    <span style="font-size:10.5px; font-weight:800; padding:3px 8px; border-radius:12px; background:#dcfce7; color:#15803d;">
                      Tayang
                    </span>
                  @else
                    <span style="font-size:10.5px; font-weight:800; padding:3px 8px; border-radius:12px; background:#f1f5f9; color:#64748b;">
                      Draf
                    </span>
                  @endif
                </td>
                <td style="padding:10px 14px; text-align:center; font-family:var(--font-mono); font-size:11px; color:var(--text-2);">
                  {{ $b->views_count ?? 0 }}
                </td>
                <td style="padding:10px 14px; text-align:center;">
                  <div style="display:inline-flex; gap:6px;">
                    <a href="{{ route('admin.berita.edit', $b->id) }}" class="btn btn-sm btn-outline" style="padding:5px 10px; font-size:11.5px; border-radius:6px;" title="Edit Artikel">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.berita.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini secara permanen?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline" style="padding:5px 10px; font-size:11.5px; color:#ef4444; border-color:#fca5a5; border-radius:6px;" title="Hapus Artikel">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:40px 16px; color:var(--text-3);">
                  <i class="bi bi-journal-x" style="font-size:32px; display:block; margin-bottom:8px; opacity:0.6;"></i>
                  Belum ada artikel berita yang dipublikasikan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- PAGINATION --}}
      @if($beritas->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:center;">
          {{ $beritas->links() }}
        </div>
      @endif
    </div>

  </main>
</div>
</body>
</html>
