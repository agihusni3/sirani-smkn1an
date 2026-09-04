<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Hero &amp; Banner Website — SIRANI</title>
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
            <i class="bi bi-images" style="color:#f59e0b; font-size:18px;"></i> Manajemen Hero Stage &amp; Banner Website
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:12px; color:var(--text-3);">
            Pengaturan Headline, Visual Kampus, &amp; Tombol Aksi Beranda Publik
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('admin.banner.create') }}" class="btn btn-sm btn-primary" style="font-weight:700; border-radius:6px; font-size:12px; padding:6px 14px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-plus-lg"></i> Tambah Banner Baru
          </a>
          <a href="{{ route('web.beranda') }}" target="_blank" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); font-weight:700; border-radius:6px; font-size:12px; padding:6px 14px;">
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

    {{-- KARTU STATISTIK RINGKAS --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:16px;">
      <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:14px 18px; border-radius:var(--r-sm);">
        <div style="font-size:11px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Total Banner Terdaftar</div>
        <div style="font-size:22px; font-weight:900; color:var(--text); margin-top:4px;">{{ $banners->count() }} Slide</div>
      </div>
      <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:14px 18px; border-radius:var(--r-sm);">
        <div style="font-size:11px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Banner Aktif Tampil</div>
        <div style="font-size:22px; font-weight:900; color:#10b981; margin-top:4px;">{{ $totalAktif }} Slide</div>
      </div>
      <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); padding:14px 18px; border-radius:var(--r-sm);">
        <div style="font-size:11px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Mode Presentasi Web</div>
        <div style="font-size:13px; font-weight:800; color:var(--text); margin-top:8px;">
          @if($totalAktif > 1)
            <span style="color:#0284c7;"><i class="bi bi-sliders"></i> Multi-Slide Slider Otomatis</span>
          @elseif($totalAktif == 1)
            <span style="color:#059669;"><i class="bi bi-check2-circle"></i> Single Hero Tetap</span>
          @else
            <span style="color:#d97706;"><i class="bi bi-exclamation-triangle"></i> Fallback Template Bawaan</span>
          @endif
        </div>
      </div>
    </div>

    {{-- TABEL HERO & BANNER --}}
    <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; font-size:12.5px; margin:0;">
          <thead>
            <tr style="background:var(--surface); border-bottom:2px solid var(--border); text-align:left;">
              <th style="padding:10px 14px; width:60px; text-align:center;">Urutan</th>
              <th style="padding:10px 14px; width:140px;">Visual Foto</th>
              <th style="padding:10px 14px;">Headline &amp; Narasi</th>
              <th style="padding:10px 14px;">Tombol Aksi (CTA)</th>
              <th style="padding:10px 14px; text-align:center;">Status</th>
              <th style="padding:10px 14px; text-align:center; width:140px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($banners as $banner)
              <tr style="border-bottom:1px solid var(--border); vertical-align:middle;">
                {{-- Urutan --}}
                <td style="padding:12px 14px; text-align:center; font-weight:800; color:var(--text-3);">
                  #{{ $banner->urutan }}
                </td>

                {{-- Visual Foto --}}
                <td style="padding:12px 14px;">
                  <div style="width:130px; height:74px; border-radius:8px; overflow:hidden; border:1px solid var(--border); background:#0f172a; position:relative;">
                    <img src="{{ $banner->gambar_url }}" alt="{{ $banner->judul }}" style="width:100%; height:100%; object-fit:cover;">
                    @if(!$banner->gambar)
                      <div style="position:absolute; bottom:2px; right:4px; font-size:9px; background:rgba(0,0,0,0.7); color:#93c5fd; padding:1px 4px; border-radius:3px;">
                        Default Foto
                      </div>
                    @endif
                  </div>
                </td>

                {{-- Headline & Narasi --}}
                <td style="padding:12px 14px;">
                  <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; flex-wrap:wrap;">
                    @if($banner->posisi === 'ppdb_callout')
                      <span style="font-size:10px; font-weight:800; color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        <i class="bi bi-person-check-fill"></i> Callout PPDB
                      </span>
                    @elseif($banner->posisi === 'hero_home')
                      <span style="font-size:10px; font-weight:800; color:#047857; background:#d1fae5; border:1px solid #a7f3d0; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        <i class="bi bi-images"></i> Hero Slider
                      </span>
                    @else
                      <span style="font-size:10px; font-weight:800; color:#64748b; background:#f1f5f9; border:1px solid #e2e8f0; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        {{ $banner->posisi }}
                      </span>
                    @endif

                    @if($banner->badge_text)
                      <span style="font-size:10px; font-weight:800; color:#b45309; background:#fef3c7; border:1px solid #fde68a; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        {{ $banner->badge_text }}
                      </span>
                    @endif
                    <span style="font-size:10px; font-weight:700; color:#475569; background:#f1f5f9; border:1px solid #cbd5e1; padding:2px 8px; border-radius:4px;">
                      <i class="bi bi-text-{{ $banner->posisi_teks ?: 'left' }}"></i> 
                      {{ $banner->posisi_teks === 'center' ? 'Teks Tengah' : ($banner->posisi_teks === 'right' ? 'Teks Kanan' : 'Teks Kiri') }}
                    </span>
                  </div>
                  <div style="font-weight:800; color:var(--text); font-size:13.5px; line-height:1.3; margin-bottom:4px;">
                    {{ $banner->judul }}
                  </div>
                  @if($banner->subjudul)
                    <div style="color:var(--text-3); font-size:11.5px; line-height:1.4; max-width:550px;">
                      {{ Str::limit($banner->subjudul, 120) }}
                    </div>
                  @endif
                  @if($banner->tag_overlay)
                    <div style="margin-top:4px; font-size:10.5px; color:var(--text-muted); font-style:italic;">
                      <i class="bi bi-geo-alt"></i> {{ $banner->tag_overlay }}
                    </div>
                  @endif
                </td>

                {{-- Tombol Aksi --}}
                <td style="padding:12px 14px;">
                  <div style="display:flex; flex-direction:column; gap:4px; font-size:11px;">
                    @if($banner->tombol_teks_1)
                      <span style="background:var(--surface); border:1px solid var(--border); padding:3px 8px; border-radius:4px; color:var(--text); display:inline-flex; align-items:center; gap:4px;">
                        <i class="bi bi-link-45deg"></i> {{ $banner->tombol_teks_1 }} &rarr; <span style="color:var(--text-3);">{{ $banner->tombol_url_1 }}</span>
                      </span>
                    @endif
                    @if($banner->tombol_teks_2)
                      <span style="background:var(--surface); border:1px solid var(--border); padding:3px 8px; border-radius:4px; color:var(--text); display:inline-flex; align-items:center; gap:4px;">
                        <i class="bi bi-link-45deg"></i> {{ $banner->tombol_teks_2 }} &rarr; <span style="color:var(--text-3);">{{ $banner->tombol_url_2 }}</span>
                      </span>
                    @endif
                  </div>
                </td>

                {{-- Status --}}
                <td style="padding:12px 14px; text-align:center;">
                  <form action="{{ route('admin.banner.toggle', $banner->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @if($banner->is_active)
                      <button type="submit" title="Klik untuk nonaktifkan" class="btn btn-sm" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:800; font-size:11px; padding:4px 10px; border-radius:20px;">
                        <i class="bi bi-check-circle-fill"></i> AKTIF
                      </button>
                    @else
                      <button type="submit" title="Klik untuk aktifkan" class="btn btn-sm" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-weight:800; font-size:11px; padding:4px 10px; border-radius:20px;">
                        <i class="bi bi-dash-circle-fill"></i> NONAKTIF
                      </button>
                    @endif
                  </form>
                </td>

                {{-- Aksi --}}
                <td style="padding:12px 14px; text-align:center;">
                  <div style="display:inline-flex; gap:6px;">
                    <a href="{{ route('admin.banner.edit', $banner->id) }}" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); padding:5px 9px; font-size:11.5px; border-radius:5px; font-weight:700;">
                      <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <form action="{{ route('admin.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus hero banner ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm" style="background:#fff1f2; border:1px solid #fecdd3; color:#e11d48; padding:5px 9px; font-size:11.5px; border-radius:5px; font-weight:700;">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="padding:40px; text-align:center; color:var(--text-3);">
                  Belum ada banner terdaftar. Klik <strong>Tambah Banner Baru</strong> untuk membuat banner pertama.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- KOTAK PANDUAN GAMBAR --}}
    <div class="panel" style="background:var(--surface); border:1px solid var(--border); padding:14px 18px; margin-top:16px; border-radius:var(--r-sm);">
      <div style="display:flex; align-items:flex-start; gap:12px;">
        <i class="bi bi-info-circle-fill" style="color:#0ea5e9; font-size:18px; margin-top:2px;"></i>
        <div style="font-size:12px; color:var(--text-3); line-height:1.6;">
          <strong style="color:var(--text);">Tips Manajemen Visual Hero Kampus:</strong>
          Gunakan foto bengkel, gedung sekolah, laboratorium komputer, atau kegiatan siswa dengan rasio <strong>16:9</strong> atau <strong>4:3</strong> (resolusi rekomendasi minimal <strong>1200 &times; 800 piksel</strong>). Sistem mendukung file format JPG, PNG, dan WebP hingga ukuran 5MB.
        </div>
      </div>
    </div>

  </main>
</div>
</body>
</html>
