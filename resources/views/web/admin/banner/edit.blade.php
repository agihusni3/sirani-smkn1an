<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Hero &amp; Banner — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-banner.css') }}?v={{ filemtime(public_path('css/admin-banner.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_web')
  <main class="main-content">
    
    <div class="panel no-print" style="background:#f2efe7; border:1px solid #c8dfdb; padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md);">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('admin.banner.index') }}" class="btn btn-sm" style="background:#ffffff; border:1px solid #c8dfdb; color:#000000; font-weight:700; padding:6px 12px; border-radius:6px; font-size:12px; text-decoration:none;">
            <i class="bi bi-arrow-left" style="color:#3368a0;"></i> Kembali ke Daftar
          </a>
          <h1 style="margin:0; font-size:16px; font-weight:900; color:#000000;">Edit Hero Banner #{{ $banner->id }}</h1>
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

    <div class="panel" style="background:#ffffff; border:1px solid #c8dfdb; border-radius:12px; padding:24px; max-width:860px; box-shadow:0 2px 8px rgba(0,0,0,0.02);">
      <form action="{{ route('admin.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Posisi & Urutan --}}
        <div style="display:grid; grid-template-columns:1fr 140px; gap:16px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:#000000; margin-bottom:5px;">
              Posisi Penempatan <span style="color:#ef4444;">*</span>
            </label>
            <select name="posisi" required style="width:100%; padding:9px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
              <option value="hero_home" {{ old('posisi', $banner->posisi) == 'hero_home' ? 'selected' : '' }}>Hero Utama Beranda Web (Full Photo Slider)</option>
              <option value="ppdb_callout" {{ old('posisi', $banner->posisi) == 'ppdb_callout' ? 'selected' : '' }}>Banner Callout PPDB (Beranda Web)</option>
              <option value="top_bar" {{ old('posisi', $banner->posisi) == 'top_bar' ? 'selected' : '' }}>Top Bar Pengumuman</option>
              <option value="popup_modal" {{ old('posisi', $banner->posisi) == 'popup_modal' ? 'selected' : '' }}>Modal Pengumuman Penting</option>
            </select>
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">
              Urutan Tampil <span style="color:#ef4444;">*</span>
            </label>
            <input type="number" name="urutan" value="{{ old('urutan', $banner->urutan) }}" min="1" required style="width:100%; padding:9px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
          </div>
        </div>

        {{-- Tata Letak Tulisan (Posisi Teks di Hero) --}}
        <div style="margin-bottom:18px; padding:16px; background:var(--surface); border:1px solid var(--border); border-radius:8px;">
          <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:6px;">
            <i class="bi bi-text-paragraph"></i> Tata Letak Tulisan di Dalam Hero Banner <span style="color:#ef4444;">*</span>
          </label>
          <div style="font-size:11.5px; color:var(--text-3); margin-bottom:12px;">
            Atur letak tulisan dan tombol di atas foto agar tidak menutupi objek utama pada foto banner.
          </div>
          <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px;">
            <label style="display:block; cursor:pointer;">
              <input type="radio" name="posisi_teks" value="left" {{ old('posisi_teks', $banner->posisi_teks ?: 'left') === 'left' ? 'checked' : '' }} style="display:none;" class="posisi-teks-radio">
              <div class="posisi-card" style="border:2px solid var(--border); border-radius:8px; padding:12px; text-align:center; transition:all 0.2s;">
                <div style="font-size:22px; color:var(--brand); margin-bottom:4px;"><i class="bi bi-text-left"></i></div>
                <div style="font-weight:700; font-size:12.5px; color:var(--text);">Rata Kiri (Left)</div>
                <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Teks di kiri, foto dominan di kanan</div>
              </div>
            </label>

            <label style="display:block; cursor:pointer;">
              <input type="radio" name="posisi_teks" value="center" {{ old('posisi_teks', $banner->posisi_teks) === 'center' ? 'checked' : '' }} style="display:none;" class="posisi-teks-radio">
              <div class="posisi-card" style="border:2px solid var(--border); border-radius:8px; padding:12px; text-align:center; transition:all 0.2s;">
                <div style="font-size:22px; color:var(--brand); margin-bottom:4px;"><i class="bi bi-text-center"></i></div>
                <div style="font-weight:700; font-size:12.5px; color:var(--text);">Rata Tengah (Center)</div>
                <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Teks terpusat simetris di tengah</div>
              </div>
            </label>

            <label style="display:block; cursor:pointer;">
              <input type="radio" name="posisi_teks" value="right" {{ old('posisi_teks', $banner->posisi_teks) === 'right' ? 'checked' : '' }} style="display:none;" class="posisi-teks-radio">
              <div class="posisi-card" style="border:2px solid var(--border); border-radius:8px; padding:12px; text-align:center; transition:all 0.2s;">
                <div style="font-size:22px; color:var(--brand); margin-bottom:4px;"><i class="bi bi-text-right"></i></div>
                <div style="font-weight:700; font-size:12.5px; color:var(--text);">Rata Kanan (Right)</div>
                <div style="font-size:11px; color:var(--text-3); margin-top:2px;">Teks di kanan, foto dominan di kiri</div>
              </div>
            </label>
          </div>
        </div>

        {{-- Badge Teks & Tag Overlay --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">
              Badge Teks Atas (Opsional)
            </label>
            <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="Contoh: PENDAFTARAN PPDB 2026/2027" style="width:100%; padding:9px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
            <div style="font-size:11px; color:var(--text-3); margin-top:3px;">Lencana kecil berlatar abu/emas di atas judul headline.</div>
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">
              Tag Keterangan / Benefit Pills (Overlay)
            </label>
            <input type="text" name="tag_overlay" value="{{ old('tag_overlay', $banner->tag_overlay) }}" placeholder="Contoh: Bebas Biaya Pendaftaran | Tanpa Uang Gedung | Lisensi BNSP" style="width:100%; padding:9px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
            <div style="font-size:11px; color:var(--text-3); margin-top:3px;">Untuk Banner PPDB: pisahkan poin keunggulan dengan tanda | atau koma.</div>
          </div>
        </div>

        {{-- Judul Utama --}}
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">
            Judul Utama Headline (Hero Title) <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" name="judul" value="{{ old('judul', $banner->judul) }}" required placeholder="Contoh: Menempa Keahlian Teknik, Rekayasa, & Agro-Industri." style="width:100%; padding:10px 12px; font-size:14px; font-weight:700; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
        </div>

        {{-- Narasi / Subjudul --}}
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:4px;">
            Paragraf Pengantar / Narasi Vokasi
          </label>
          <textarea name="subjudul" rows="3" placeholder="Jelaskan secara ringkas keunggulan vokasi, fasilitas bengkel, atau pesan sambutan sekolah..." style="width:100%; padding:10px 12px; font-size:13px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text); resize:vertical;">{{ old('subjudul', $banner->subjudul) }}</textarea>
        </div>

        {{-- Upload / Ganti Foto --}}
        <div style="margin-bottom:20px; padding:16px; background:var(--surface); border:1px solid var(--border); border-radius:8px;">
          <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:6px;">
            <i class="bi bi-camera"></i> Foto Visual Hero Banner
          </label>
          <div style="display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap;">
            <div id="previewContainer" style="width:200px; height:120px; border-radius:8px; overflow:hidden; border:1px solid var(--border); background:#0f172a; display:flex; align-items:center; justify-content:center;">
              <img id="previewImage" src="{{ $banner->gambar_url }}" alt="{{ $banner->judul }}" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <div style="flex:1; min-width:260px;">
              <input type="file" name="gambar" id="gambarInput" accept="image/jpeg,image/png,image/webp" class="form-control" style="width:100%; font-size:12.5px;">
              <div style="font-size:11.5px; color:var(--text-3); line-height:1.5; margin-top:4px;">
                Biarkan kosong jika tidak ingin mengganti foto saat ini.<br>
                Format: <strong>JPG, PNG, WebP</strong> (Maksimal 5MB). Rekomendasi rasio <strong>16:9</strong> atau <strong>4:3</strong>.
              </div>
            </div>
          </div>
        </div>

        {{-- Tombol Aksi (CTA 1, CTA 2, CTA 3) --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:18px;">
          <div style="padding:14px; background:var(--surface); border:1px solid var(--border); border-radius:8px;">
            <div style="font-size:12px; font-weight:800; color:var(--text); margin-bottom:8px;">
              <i class="bi bi-box-arrow-in-up-right"></i> Tombol Aksi 1 (Putih / Primer)
            </div>
            <div style="margin-bottom:8px;">
              <label style="font-size:11px; font-weight:700; color:var(--text-3);">Teks Tombol 1</label>
              <input type="text" name="tombol_teks_1" value="{{ old('tombol_teks_1', $banner->tombol_teks_1) }}" placeholder="Contoh: Isi Formulir PPDB Sekarang" style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); color:var(--text);">
            </div>
            <div>
              <label style="font-size:11px; font-weight:700; color:var(--text-3);">Tujuan URL / Link</label>
              <input type="text" name="tombol_url_1" value="{{ old('tombol_url_1', $banner->tombol_url_1) }}" placeholder="Contoh: /ppdb/formulir atau https://..." style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); color:var(--text);">
            </div>
          </div>

          <div style="padding:14px; background:var(--surface); border:1px solid var(--border); border-radius:8px;">
            <div style="font-size:12px; font-weight:800; color:var(--text); margin-bottom:8px;">
              <i class="bi bi-box-arrow-in-up-right"></i> Tombol Aksi 2 (Kaca / Sekunder)
            </div>
            <div style="margin-bottom:8px;">
              <label style="font-size:11px; font-weight:700; color:var(--text-3);">Teks Tombol 2</label>
              <input type="text" name="tombol_teks_2" value="{{ old('tombol_teks_2', $banner->tombol_teks_2) }}" placeholder="Contoh: Cek Status Seleksi" style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); color:var(--text);">
            </div>
            <div>
              <label style="font-size:11px; font-weight:700; color:var(--text-3);">Tujuan URL / Link</label>
              <input type="text" name="tombol_url_2" value="{{ old('tombol_url_2', $banner->tombol_url_2) }}" placeholder="Contoh: /ppdb/status atau https://..." style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); color:var(--text);">
            </div>
          </div>

          <div style="padding:14px; background:var(--surface); border:1px solid var(--border); border-radius:8px;">
            <div style="font-size:12px; font-weight:800; color:#10b981; margin-bottom:8px;">
              <i class="bi bi-whatsapp"></i> Tombol Aksi 3 (WhatsApp / Bantuan)
            </div>
            <div style="margin-bottom:8px;">
              <label style="font-size:11px; font-weight:700; color:var(--text-3);">Teks Tombol 3</label>
              <input type="text" name="tombol_teks_3" value="{{ old('tombol_teks_3', $banner->tombol_teks_3) }}" placeholder="Contoh: Tanya Panitia PPDB" style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); color:var(--text);">
            </div>
            <div>
              <label style="font-size:11px; font-weight:700; color:var(--text-3);">Tujuan Link WA / URL</label>
              <input type="text" name="tombol_url_3" value="{{ old('tombol_url_3', $banner->tombol_url_3) }}" placeholder="Contoh: https://wa.me/62812... atau link lain" style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); color:var(--text);">
            </div>
          </div>
        </div>

        {{-- Checkbox Status Aktif --}}
        <div style="margin-bottom:24px; display:flex; align-items:center; gap:8px;">
          <input type="checkbox" name="is_active" id="isActiveCheck" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:#10b981;">
          <label for="isActiveCheck" style="font-size:13px; font-weight:700; color:var(--text); cursor:pointer;">
            Aktifkan dan tampilkan banner ini di Beranda Website publik
          </label>
        </div>

        {{-- Tombol Submit --}}
        <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #c8dfdb; padding-top:16px;">
          <a href="{{ route('admin.banner.index') }}" class="btn" style="background:#ffffff; border:1px solid #c8dfdb; color:#000000; padding:8px 18px; border-radius:6px; font-weight:700; font-size:12.5px; text-decoration:none;">
            Batal
          </a>
          <button type="submit" class="btn" style="background:#3368a0; color:#ffffff; font-weight:800; font-size:13px; padding:9px 24px; border-radius:6px; border:none; cursor:pointer; box-shadow:0 2px 6px rgba(51,104,160,0.25);">
            <i class="bi bi-check-lg"></i> Perbarui Banner
          </button>
        </div>
      </form>
    </div>

  </main>
</div>

<script>
  // Live Preview Image on change
  document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(evt) {
        const preview = document.getElementById('previewImage');
        preview.src = evt.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>
</body>
</html>
