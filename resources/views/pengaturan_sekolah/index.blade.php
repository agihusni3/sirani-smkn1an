<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengaturan Profil Sekolah & Kop Dinas — SMKN 1 Air Naningan</title>
  @include('partials.styles')
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-bank2" style="color:#000000; font-size:16px;"></i> Pengaturan Profil Sekolah &amp; Kop Dinas
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Identitas instansi, kop dinas, &amp; tanda tangan
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          @include('partials.header_actions')
        </div>
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
    @endif

    {{-- LIVE KOP PREVIEW --}}
    <div class="panel" style="margin-bottom:24px;">
      <div style="font-size:12px; font-weight:800; color:#000000; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:14px;">
        <i class="bi bi-eye-fill"></i> Pratinjau Kop Surat Resmi Instansi
      </div>
      
      <div style="border:1.5px dashed var(--border); border-radius:12px; padding:20px; background:var(--bg);">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; text-align:center;">
          {{-- LOGO KIRI: PROVINSI LAMPUNG --}}
          <div style="width:70px; height:70px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
            <img src="/img/logo_prov_lampung.png" alt="Logo Provinsi Lampung" onerror="this.onerror=null; this.src='/img/logo_prov_lampung.svg'" style="max-width:100%; max-height:100%; object-fit:contain;" />
          </div>

          {{-- TEKS KOP DINAS --}}
          <div style="flex:1;">
            <div style="font-size:13px; font-weight:800; letter-spacing:0.5px; color:var(--text); text-transform:uppercase;">
              {{ $sekolah->nama_instansi_atas }}
            </div>
            <div style="font-size:14px; font-weight:900; letter-spacing:0.5px; color:var(--text); text-transform:uppercase;">
              {{ $sekolah->nama_dinas }}
            </div>
            <div style="font-size:17px; font-weight:900; letter-spacing:0.5px; color:var(--text); text-transform:uppercase; margin:2px 0;">
              {{ $sekolah->nama_sekolah }}
            </div>
            <div style="font-size:11px; color:var(--text-2); line-height:1.4; margin-top:2px;">
              {{ $sekolah->alamat_lengkap ?? $sekolah->alamat }}<br />
              Email: {{ $sekolah->email }} · Website: {{ $sekolah->website }}
            </div>
          </div>

          {{-- LOGO KANAN: SMKN 1 AIR NANINGAN --}}
          <div style="width:70px; height:70px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
            @if($sekolah->logo_sekolah)
              <img src="{{ asset('storage/'.$sekolah->logo_sekolah) }}" alt="Logo Sekolah" style="max-width:100%; max-height:100%; object-fit:contain;" />
            @else
              <img src="/img/logo.png" alt="Logo Sekolah" onerror="this.style.display='none'" style="max-width:100%; max-height:100%; object-fit:contain;" />
            @endif
          </div>
        </div>
        <div style="border-bottom:3px solid var(--text); border-top:1px solid var(--text); height:3px; margin-top:14px;"></div>
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
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Instansi Atas / Pemerintah Provinsi</label>
            <input type="text" name="nama_instansi_atas" class="form-control" value="{{ old('nama_instansi_atas', $sekolah->nama_instansi_atas) }}" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Nama Dinas Pembina</label>
            <input type="text" name="nama_dinas" class="form-control" value="{{ old('nama_dinas', $sekolah->nama_dinas) }}" required style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Nama Resmi Sekolah</label>
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

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:16px;">
          <div style="grid-column:1 / -1;">
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Jalan / Alamat Lengkap</label>
            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $sekolah->alamat) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px;" />
          </div>

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
          <i class="bi bi-person-badge-fill" style="color:#000000; margin-right:6px;"></i> 3. Pimpinan Sekolah &amp; Logo Instansi
        </h3>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:20px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">Nama Kepala Sekolah (Lengkap Gelar)</label>
            <input type="text" name="nama_kepala_sekolah" class="form-control" value="{{ old('nama_kepala_sekolah', $sekolah->nama_kepala_sekolah) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-weight:700;" placeholder="Drs. H. Ahmad Sudrajat, M.Pd." />
          </div>

          <div>
            <label style="display:block; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">NIP Kepala Sekolah</label>
            <input type="text" name="nip_kepala_sekolah" class="form-control" value="{{ old('nip_kepala_sekolah', $sekolah->nip_kepala_sekolah) }}" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); font-size:13px; font-family:var(--font-mono);" placeholder="19750510 200003 1 005" />
          </div>

          <div style="grid-column:1 / -1;">
            <label style="display:flex; justify-content:space-between; font-size:12px; font-weight:800; color:var(--text-2); margin-bottom:6px;">
              <span>Ganti Logo Sekolah (PNG / JPG Transparan)</span>
              <span style="color:#000000; font-size:11px; font-weight:800;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
            </label>
            <div style="display:flex; align-items:center; gap:12px;">
              <div id="logo_preview_wrap" style="width:48px; height:48px; border-radius:10px; border:1.5px solid rgba(0,0,0,0.15); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; padding:3px;">
                <img id="logo_preview_img" src="{{ $sekolah->logo_url ?? '/img/logo.png' }}" style="width:100%; height:100%; object-fit:contain;" />
              </div>
              <div style="flex:1;">
                <input type="file" name="logo_sekolah" id="inputLogoSekolah" accept="image/png,image/jpeg,image/webp" onchange="initPhotoCrop(this, 'logo_preview_img', '1:1', 'Sesuaikan & Potong Logo Sekolah')" style="font-size:12.5px; color:var(--text-2); width:100%;" />
                <div style="font-size:11.5px; color:var(--text-3); margin-top:4px;">Disarankan rasio 1:1 format PNG transparan, ukuran maksimal 2 MB.</div>
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
  </main>
</div>

@include('partials.crop_modal')

</body>
</html>
