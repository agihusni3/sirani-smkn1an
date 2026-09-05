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
  @include('partials.sidebar_web')
  <main class="main-content">
    
    {{-- HEADER BAR --}}
    <div class="panel no-print" style="background:#f2efe7; border:1px solid #c8dfdb; padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md); box-shadow:0 2px 8px rgba(0,0,0,0.02);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:17px; font-weight:900; color:#000000; display:inline-flex; align-items:center; gap:8px;">
            <i class="bi bi-images" style="color:#3368a0; font-size:18px;"></i> Manajemen Hero Stage &amp; Banner Website
          </h1>
          <span style="color:#c8dfdb; font-weight:300;">|</span>
          <span style="font-size:12px; color:#000000; font-weight:600;">
            Pengaturan Headline, Visual Kampus, &amp; Tombol Aksi Beranda Publik
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('admin.banner.create') }}" class="btn btn-sm" style="background:#3368a0; color:#ffffff; font-weight:800; border-radius:6px; font-size:12px; padding:7px 15px; border:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(51,104,160,0.25); text-decoration:none;">
            <i class="bi bi-plus-lg"></i> Tambah Banner Baru
          </a>
          <a href="{{ route('web.beranda') }}" target="_blank" class="btn btn-sm" style="background:#ffffff; border:1px solid #c8dfdb; color:#000000; font-weight:800; border-radius:6px; font-size:12px; padding:7px 14px; text-decoration:none;">
            <i class="bi bi-box-arrow-up-right" style="color:#3368a0;"></i> Lihat di Web
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
      <div class="panel" style="background:#ffffff; border:1px solid #c8dfdb; padding:16px 18px; border-radius:12px;">
        <div style="font-size:11px; font-weight:800; color:#000000; text-transform:uppercase;">Total Banner Terdaftar</div>
        <div style="font-size:22px; font-weight:900; color:#000000; margin-top:4px;">{{ $banners->count() }} Slide</div>
      </div>
      <div class="panel" style="background:#ffffff; border:1px solid #c8dfdb; padding:16px 18px; border-radius:12px;">
        <div style="font-size:11px; font-weight:800; color:#000000; text-transform:uppercase;">Banner Aktif Tampil</div>
        <div style="font-size:22px; font-weight:900; color:#3368a0; margin-top:4px;">{{ $totalAktif }} Slide</div>
      </div>
      <div class="panel" style="background:#ffffff; border:1px solid #c8dfdb; padding:16px 18px; border-radius:12px;">
        <div style="font-size:11px; font-weight:800; color:#000000; text-transform:uppercase;">Mode Presentasi Web</div>
        <div style="font-size:13px; font-weight:900; color:#000000; margin-top:8px;">
          @if($totalAktif > 1)
            <span style="color:#3368a0;"><i class="bi bi-sliders"></i> Multi-Slide Slider Otomatis</span>
          @elseif($totalAktif == 1)
            <span style="color:#66a3bf;"><i class="bi bi-check2-circle"></i> Single Hero Tetap</span>
          @else
            <span style="color:#000000;"><i class="bi bi-exclamation-triangle"></i> Fallback Template Bawaan</span>
          @endif
        </div>
      </div>
    </div>

    {{-- TABEL HERO & BANNER --}}
    <div class="panel" style="background:#ffffff; border:1px solid #c8dfdb; border-radius:12px; overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse; font-size:12.5px; margin:0;">
          <thead>
            <tr style="background:#f2efe7; border-bottom:2px solid #c8dfdb; text-align:left;">
              <th style="padding:10px 14px; width:60px; text-align:center; color:#000000; font-weight:800;">Urutan</th>
              <th style="padding:10px 14px; width:140px; color:#000000; font-weight:800;">Visual Foto</th>
              <th style="padding:10px 14px; color:#000000; font-weight:800;">Headline &amp; Narasi</th>
              <th style="padding:10px 14px; color:#000000; font-weight:800;">Tombol Aksi (CTA)</th>
              <th style="padding:10px 14px; text-align:center; color:#000000; font-weight:800;">Status</th>
              <th style="padding:10px 14px; text-align:center; width:140px; color:#000000; font-weight:800;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($banners as $banner)
              <tr style="border-bottom:1px solid #c8dfdb; vertical-align:middle;">
                {{-- Urutan --}}
                <td style="padding:12px 14px; text-align:center; font-weight:900; color:#000000;">
                  #{{ $banner->urutan }}
                </td>

                {{-- Visual Foto --}}
                <td style="padding:12px 14px;">
                  <div style="width:130px; height:74px; border-radius:8px; overflow:hidden; border:1px solid #c8dfdb; background:#0f172a; position:relative;">
                    <img src="{{ $banner->gambar_url }}" alt="{{ $banner->judul }}" style="width:100%; height:100%; object-fit:cover;">
                    @if(!$banner->gambar)
                      <div style="position:absolute; bottom:2px; right:4px; font-size:9px; background:rgba(0,0,0,0.7); color:#f2efe7; padding:1px 4px; border-radius:3px;">
                        Default Foto
                      </div>
                    @endif
                  </div>
                </td>

                {{-- Headline & Narasi --}}
                <td style="padding:12px 14px;">
                  <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; flex-wrap:wrap;">
                    @if($banner->posisi === 'ppdb_callout')
                      <span style="font-size:10px; font-weight:800; color:#3368a0; background:#c8dfdb; border:1px solid #66a3bf; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        <i class="bi bi-person-check-fill"></i> Callout PPDB
                      </span>
                    @elseif($banner->posisi === 'hero_home')
                      <span style="font-size:10px; font-weight:800; color:#ffffff; background:#3368a0; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        <i class="bi bi-images"></i> Hero Slider
                      </span>
                    @else
                      <span style="font-size:10px; font-weight:800; color:#000000; background:#f2efe7; border:1px solid #c8dfdb; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        {{ $banner->posisi }}
                      </span>
                    @endif

                    @if($banner->badge_text)
                      <span style="font-size:10px; font-weight:800; color:#000000; background:#f2efe7; border:1px solid #c8dfdb; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        {{ $banner->badge_text }}
                      </span>
                    @endif
                    <span style="font-size:10px; font-weight:700; color:#000000; background:#f2efe7; border:1px solid #c8dfdb; padding:2px 8px; border-radius:4px;">
                      <i class="bi bi-text-{{ $banner->posisi_teks ?: 'left' }}"></i> 
                      {{ $banner->posisi_teks === 'center' ? 'Teks Tengah' : ($banner->posisi_teks === 'right' ? 'Teks Kanan' : 'Teks Kiri') }}
                    </span>
                  </div>
                  <div style="font-weight:800; color:#000000; font-size:13.5px; line-height:1.3; margin-bottom:4px;">
                    {{ $banner->judul }}
                  </div>
                  @if($banner->subjudul)
                    <div style="color:#000000; opacity:0.8; font-size:11.5px; line-height:1.4; max-width:550px;">
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
                    @if($banner->tombol_teks_3)
                      <span style="background:#ecfdf5; border:1px solid #a7f3d0; padding:3px 8px; border-radius:4px; color:#065f46; display:inline-flex; align-items:center; gap:4px;">
                        <i class="bi bi-whatsapp"></i> {{ $banner->tombol_teks_3 }}
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
                    <a href="{{ route('admin.banner.edit', $banner->id) }}" class="btn btn-sm" style="background:#ffffff; border:1px solid #c8dfdb; color:#3368a0; padding:5px 10px; font-size:11.5px; border-radius:6px; font-weight:800; text-decoration:none;">
                      <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <form action="{{ route('admin.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus hero banner ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm" style="background:#fff1f2; border:1px solid #fecdd3; color:#e11d48; padding:5px 9px; font-size:11.5px; border-radius:6px; font-weight:700;">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="padding:40px; text-align:center; color:#000000; font-weight:700;">
                  Belum ada banner terdaftar. Klik <strong>Tambah Banner Baru</strong> untuk membuat banner pertama.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- KOTAK PANDUAN GAMBAR --}}
    <div class="panel" style="background:#f2efe7; border:1px solid #c8dfdb; padding:16px 20px; margin-top:16px; border-radius:12px;">
      <div style="display:flex; align-items:flex-start; gap:12px;">
        <i class="bi bi-info-circle-fill" style="color:#3368a0; font-size:20px; margin-top:2px;"></i>
        <div style="font-size:12px; color:#000000; line-height:1.6; font-weight:600;">
          <strong style="color:#000000; font-weight:900;">Tips Manajemen Visual Hero Kampus:</strong>
          Gunakan foto bengkel, gedung sekolah, laboratorium komputer, atau kegiatan siswa dengan rasio <strong>16:9</strong> atau <strong>4:3</strong> (resolusi rekomendasi minimal <strong>1200 &times; 800 piksel</strong>). Sistem mendukung file format JPG, PNG, dan WebP hingga ukuran 5MB.
        </div>
      </div>
    </div>

  </main>
</div>
</body>
</html>
