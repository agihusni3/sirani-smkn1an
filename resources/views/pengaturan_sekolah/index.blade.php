<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengaturan Profil Sekolah & Kop Dinas — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/situan-app.css') }}?v={{ filemtime(public_path('css/situan-app.css')) }}">
</head>
<body class="situan-body">
<div class="situan-layout">
  @include('partials.sidebar_situan')
  <main class="situan-main">
    <div class="situan-content">
      <div class="situan-page-header no-print">
        <div style="display:flex; align-items:center; gap:12px;">
          <button type="button" class="situan-mobile-menu-btn" onclick="window.toggleSituanSidebar()" aria-label="Buka Menu" style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:6px 10px; font-size:17px; cursor:pointer; color:#000000;">
            <i class="bi bi-list"></i>
          </button>
          <div>
            <div class="situan-breadcrumb">
              <a href="{{ route('admin.portal') }}" style="display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-grid-fill" style="color:#0284c7; font-size:12px;"></i> DCC</a>
              <span class="sep">/</span>
              <a href="{{ route('situan.index') }}">SITUAN</a>
              <span class="sep">/</span>
              <span style="color:#0284c7; font-weight:800;">Profil Sekolah</span>
            </div>
            <h1 class="situan-page-title" style="margin-top:2px; font-size:20px;">Pengaturan Profil Sekolah &amp; Kop Dinas</h1>
          </div>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          @include('partials.header_actions')
        </div>
      </div>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:12px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:12px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif    {{-- LIVE KOP PREVIEW --}}
    <div class="panel" style="margin-bottom:24px;">
      <div style="font-size:12px; font-weight:800; color:#000000; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:14px;">
        <i class="bi bi-eye-fill"></i> Pratinjau Kop Surat Resmi Instansi (Sumber Acuan Seluruh Dokumen)
      </div>
      
      <div style="border:1.5px dashed var(--border); border-radius:12px; padding:20px 20px 8px 20px; background:#ffffff; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        @include('partials.kop_surat', ['sekolah' => $sekolah])
      </div>
    </div>

    {{-- FORM EDIT PENGATURAN --}}
    <form method="POST" action="{{ route('admin.pengaturan-sekolah.update') }}" enctype="multipart/form-data">
      @csrf
      
      <div class="panel" style="margin-bottom:24px;">
        <h3 style="font-size:15px; font-weight:800; margin-bottom:18px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:10px;">
          <i class="bi bi-building" style="color:#000000; margin-right:6px;"></i> 1. Identitas Dinas &amp; Nama Sekolah
        </h3>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Instansi Atas / Pemerintah Provinsi <span style="color:#ef4444;">*</span></label>
            <input type="text" name="nama_instansi_atas" class="form-control" value="{{ old('nama_instansi_atas', $sekolah->nama_instansi_atas) }}" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Nama Dinas Pembina <span style="color:#ef4444;">*</span></label>
            <input type="text" name="nama_dinas" class="form-control" value="{{ old('nama_dinas', $sekolah->nama_dinas) }}" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Nama Resmi Sekolah <span style="color:#ef4444;">*</span></label>
            <input type="text" name="nama_sekolah" class="form-control" value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-weight:700;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">NPSN Sekolah</label>
            <input type="text" name="npsn" class="form-control" value="{{ old('npsn', $sekolah->npsn) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-family:var(--font-mono);" />
          </div>
        </div>

        <h3 style="font-size:15px; font-weight:800; margin:24px 0 18px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:10px;">
          <i class="bi bi-geo-alt-fill" style="color:#000000; margin-right:6px;"></i> 2. Alamat &amp; Kontak Sekolah
        </h3>

        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Jalan / Alamat Lengkap</label>
          <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $sekolah->alamat) }}" placeholder="Jl. Makam Baturuguk, Pekon Karang Sari" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Kecamatan</label>
            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $sekolah->kecamatan) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Kabupaten / Kota</label>
            <input type="text" name="kabupaten" class="form-control" value="{{ old('kabupaten', $sekolah->kabupaten) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Provinsi</label>
            <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $sekolah->provinsi) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Kode Pos</label>
            <input type="text" name="kode_pos" class="form-control" value="{{ old('kode_pos', $sekolah->kode_pos) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-family:var(--font-mono);" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Email Resmi</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $sekolah->email) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Website Resmi</label>
            <input type="text" name="website" class="form-control" value="{{ old('website', $sekolah->website) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>
        </div>

        <h3 style="font-size:15px; font-weight:800; margin:24px 0 18px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:10px;">
          <i class="bi bi-person-badge-fill" style="color:#000000; margin-right:6px;"></i> 3. Pimpinan Sekolah
        </h3>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:24px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Nama Kepala Sekolah (Lengkap Gelar)</label>
            <input type="text" name="nama_kepala_sekolah" class="form-control" value="{{ old('nama_kepala_sekolah', $sekolah->nama_kepala_sekolah) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-weight:700;" placeholder="Aprida, S.Si." />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">NIP Kepala Sekolah</label>
            <input type="text" name="nip_kepala_sekolah" class="form-control" value="{{ old('nip_kepala_sekolah', $sekolah->nip_kepala_sekolah) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-family:var(--font-mono);" placeholder="197904172008012019" />
          </div>
        </div>

        <h3 style="font-size:15px; font-weight:800; margin:24px 0 18px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:10px;">
          <i class="bi bi-images" style="color:#000000; margin-right:6px;"></i> 4. Logo Resmi Kop Surat Kedinasan
        </h3>

        {{-- GRID SIMETRIS 2 LOGO (KIRI: PROVINSI LAMPUNG, KANAN: SMKN 1 AIR NANINGAN) --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px; margin-bottom:24px;">
          
          {{-- KARTU LOGO 1: PROVINSI LAMPUNG (KIRI KOP) --}}
          <div style="background:var(--bg-2); border:1.5px solid var(--border-2); border-radius:14px; padding:18px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.03);">
            <div>
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-size:12.5px; font-weight:800; color:var(--text);">Logo Pemprov Lampung (Kiri Kop)</span>
                <span style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
                  <i class="bi bi-shield-fill-check"></i> Sisi Kiri
                </span>
              </div>

              <div style="display:flex; align-items:center; gap:16px;">
                <div id="logo_prov_preview_wrap" style="width:72px; height:72px; border-radius:12px; border:1.5px solid rgba(0,0,0,0.12); background:#ffffff; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; padding:6px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                  <img id="logo_prov_preview_img" src="{{ $sekolah->logo_provinsi_url }}" alt="Logo Provinsi" style="max-width:100%; max-height:100%; object-fit:contain;" onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='/img/logo_prov_lampung.png';}else{this.src='/lampung.png';}" />
                </div>
                <div style="flex:1; min-width:0;">
                  <input type="file" name="logo_provinsi" id="inputLogoProvinsi" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;" onchange="handleLogoPreview(this, 'logo_prov_preview_img', 'nama_file_prov')" />
                  <label for="inputLogoProvinsi" class="btn btn-sm" style="background:#0284c7; color:#ffffff; font-weight:800; font-size:12px; padding:8px 16px; border-radius:8px; border:none; display:inline-flex; align-items:center; gap:7px; cursor:pointer; box-shadow:0 2px 6px rgba(2,132,199,0.25); transition:all 0.15s ease;">
                    <i class="bi bi-cloud-arrow-up-fill" style="font-size:14px;"></i> Unggah Logo Provinsi
                  </label>
                  <div id="nama_file_prov" style="font-size:11px; color:var(--text-3); margin-top:6px; line-height:1.4;">
                    Format PNG transparan / SVG resmi (Maks. 4 MB).
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- KARTU LOGO 2: SMKN 1 AIR NANINGAN (KANAN KOP) --}}
          <div style="background:var(--bg-2); border:1.5px solid var(--border-2); border-radius:14px; padding:18px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.03);">
            <div>
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-size:12.5px; font-weight:800; color:var(--text);">Logo SMKN 1 Air Naningan (Kanan Kop)</span>
                <span style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:11px; font-weight:800; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
                  <i class="bi bi-patch-check-fill"></i> Sisi Kanan
                </span>
              </div>

              <div style="display:flex; align-items:center; gap:16px;">
                <div id="logo_preview_wrap" style="width:72px; height:72px; border-radius:12px; border:1.5px solid rgba(0,0,0,0.12); background:#ffffff; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; padding:6px; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                  <img id="logo_preview_img" src="{{ $sekolah->logo_sekolah_url }}" alt="Logo Sekolah" style="max-width:100%; max-height:100%; object-fit:contain;" onerror="this.src='/img/logo.png';" />
                </div>
                <div style="flex:1; min-width:0;">
                  <input type="file" name="logo_sekolah" id="inputLogoSekolah" accept="image/png,image/jpeg,image/webp" style="display:none;" onchange="handleLogoPreview(this, 'logo_preview_img', 'nama_file_sekolah')" />
                  <label for="inputLogoSekolah" class="btn btn-sm" style="background:#10b981; color:#ffffff; font-weight:800; font-size:12px; padding:8px 16px; border-radius:8px; border:none; display:inline-flex; align-items:center; gap:7px; cursor:pointer; box-shadow:0 2px 6px rgba(16,185,129,0.25); transition:all 0.15s ease;">
                    <i class="bi bi-cloud-arrow-up-fill" style="font-size:14px;"></i> Unggah Logo Sekolah
                  </label>
                  <div id="nama_file_sekolah" style="font-size:11px; color:var(--text-3); margin-top:6px; line-height:1.4;">
                    Format PNG transparan rasio 1:1 sekolah (Maks. 4 MB).
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div style="display:flex; justify-content:flex-end; padding-top:16px; border-top:1px solid var(--border);">
          <button type="submit" class="btn btn-gold" style="padding:10px 24px; font-size:13.5px; font-weight:800; display:inline-flex; align-items:center; gap:8px;">
            <i class="bi bi-floppy-fill"></i> Simpan Profil &amp; Kop Surat
          </button>
        </div>
      </div>
    </form>
    </div>
  </main>
</div>

@include('partials.crop_modal')

<script>
  function handleLogoPreview(input, previewImgId, labelInfoId) {
    if (input.files && input.files[0]) {
      const file = input.files[0];
      const labelEl = document.getElementById(labelInfoId);
      if (labelEl) {
        labelEl.innerHTML = `<span style="color:#16a34a; font-weight:700;"><i class="bi bi-check-circle-fill"></i> Terpilih:</span> ` + file.name + ` (${(file.size / 1024).toFixed(1)} KB)`;
      }
      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.getElementById(previewImgId);
        if (img) img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }
</script>

</body>
</html>
