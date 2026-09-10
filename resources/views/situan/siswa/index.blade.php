<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Master Data Siswa — SITUAN SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/situan-app.css') }}?v={{ filemtime(public_path('css/situan-app.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/siswa.css') }}?v={{ filemtime(public_path('css/siswa.css')) }}">
</head>
<body class="situan-body">
<div class="situan-layout">
  @php
    $currentUser = auth()->user();
    $isAdmin = $currentUser && $currentUser->isAdmin();
    $isStafTu = $currentUser && $currentUser->isStafTu();
    $isWali = $currentUser && $currentUser->isWaliKelas();
    $canManageSiswa = $isAdmin || $isStafTu || $isWali;
    $useSiraniSidebar = request()->get('from') === 'sirani' || ($isWali && !$isAdmin && !$isStafTu && !request()->has('from_situan'));
  @endphp

  @if($useSiraniSidebar)
    @include('partials.sidebar')
  @else
    @include('partials.sidebar_situan')
  @endif

  <main class="situan-main">
    {{-- Topbar Breadcrumbs --}}
    <div class="situan-topbar no-print">
      <div class="situan-breadcrumb">
        <button type="button" class="situan-mobile-menu-btn" onclick="window.toggleSituanSidebar()" aria-label="Buka Menu" style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:4px 8px; font-size:15px; cursor:pointer; color:#000000; margin-right:4px;">
          <i class="bi bi-list"></i>
        </button>
        <a href="{{ route('admin.portal') }}" style="display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-grid-fill" style="color:#0284c7; font-size:12px;"></i> DCC</a>
        <span class="sep">/</span>
        <a href="{{ route('situan.index') }}">SITUAN</a>
        <span class="sep">/</span>
        <span style="color:#0284c7; font-weight:800;">Data Siswa &amp; Alumni</span>
      </div>
      <div class="situan-topbar-actions">
        <a href="{{ route('situan.index') }}" class="btn-situan btn-situan-outline" style="height:32px; font-size:11px;">
          <i class="bi bi-speedometer2"></i> Dasbor SITUAN
        </a>
        @include('partials.header_actions')
      </div>
    </div>

    <div class="situan-content">
      {{-- SITUAN MODERN PAGE HEADER --}}
      <div class="situan-page-header no-print">
        <div class="situan-page-title-wrap">
          <div class="situan-page-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <div>
            <h1 class="situan-page-title">Data Siswa &amp; Alumni</h1>
            <div class="situan-page-subtitle">
              @if(!empty($isWaliOnly) && $waliRombel)
                Rombel Binaan: <strong style="color:#000000;">{{ $waliRombel->nama_rombel }}</strong> · 
              @endif
              Pangkalan Data Pokok Peserta Didik SMKN 1 Air Naningan · Total: <strong style="color:#000000;">{{ number_format($statTotal) }}</strong> Siswa Aktif
            </div>
          </div>
        </div>

        <div class="situan-action-buttons">
          @if($canManageSiswa)
            <button type="button" id="btnToggleTambahSiswa" onclick="toggleTambahSiswa()" class="btn-situan btn-situan-primary">
              <i class="bi bi-person-plus-fill" id="iconToggleTambahSiswa"></i>
              <span id="textToggleTambahSiswa">Tambah Siswa</span>
            </button>
            <button type="button" onclick="openModal('importModal')" class="btn-situan btn-situan-outline">
              <i class="bi bi-file-earmark-arrow-up-fill"></i> Import CSV
            </button>
          @endif
          <a href="/siswa/export" class="btn-situan btn-situan-outline" title="Unduh CSV Kompatibel Excel">
            <i class="bi bi-file-earmark-excel-fill" style="color:#10b981;"></i> Excel
          </a>
          <a href="/siswa/cetak-pdf{{ !empty($rombelId) ? '?rombel_id='.$rombelId : '' }}" id="btnTopCetakPdf" onclick="return handleTopCetakPdfClick(this, event)" target="_blank" class="btn-situan btn-situan-outline" title="Cetak Format A4 Kop Dinas">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i> PDF <span id="topSelectedBadge" style="display:none; background:#000000; color:#FFFFFF; border-radius:10px; padding:1px 6px; font-size:10px; font-family:monospace; margin-left:2px;">0</span>
          </a>
        </div>
      </div>

      @if(session('success'))<div class="alert-success" style="margin-bottom:16px; border-radius:8px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
      @if(session('error'))<div class="alert-error" style="margin-bottom:16px; border-radius:8px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
      @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px; border-radius:8px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

      {{-- SITUAN MODERN METRIC STRIP --}}
      <div class="situan-metric-strip no-print">
        <div class="situan-metric-card">
          <div class="situan-metric-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ number_format($statTotal) }}</div>
            <div class="situan-metric-lbl">Total Siswa Aktif</div>
          </div>
        </div>

        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(15,118,110,0.1); border-color:rgba(15,118,110,0.25); color:#0f766e;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ number_format($statAlumni) }}</div>
            <div class="situan-metric-lbl">Direktori Alumni / Lulus</div>
          </div>
        </div>

        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(147,51,234,0.1); border-color:rgba(147,51,234,0.25); color:#7e22ce;">
            <i class="bi bi-briefcase-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ number_format($statPkl) }}</div>
            <div class="situan-metric-lbl">Sedang Praktik Kerja (PKL)</div>
          </div>
        </div>
      </div>

    @if($canManageSiswa)
    <!-- Form Tambah Siswa (Collapsible / Triggered) -->
    <div class="panel" id="panelTambahSiswa" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom: 20px; border-color: var(--border); background: var(--bg-2);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="stat-icon" style="width:36px; height:36px; border-radius:8px; background:rgba(0,0,0,0.06); color:#000000; display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <span style="font-weight:800; font-size:15px; color:var(--text);">Form Tambah Siswa Baru</span>
            <div style="font-size:12px; color:var(--text-3);">Lengkapi data siswa untuk presensi sekolah.</div>
          </div>
        </div>
        <button type="button" onclick="toggleTambahSiswa(false)" class="btn btn-outline" style="height:32px; width:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form id="formTambahSiswa" action="/siswa" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Seksi 1: Identitas Pokok & Rombel --}}
        <div style="margin-bottom:16px;">
          <div style="font-size:12px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:var(--brand-blue, #2563eb);"></span> Identitas Pokok & Kelas
          </div>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                NISN <span style="color:var(--red);">*</span>
              </label>
              <input type="text" name="nisn" maxlength="30" required placeholder="Contoh: 0071234567" style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                NIK (KTP / KK)
              </label>
              <input type="text" name="nik" maxlength="16" placeholder="16 digit angka..." style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0; grid-column:span 1;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Nama Lengkap Siswa <span style="color:var(--red);">*</span>
              </label>
              <input type="text" name="nama" required placeholder="Nama lengkap sesuai ijazah/akta..." style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Jenis Kelamin <span style="color:var(--red);">*</span>
              </label>
              <select name="jenis_kelamin" style="width:100%; height:38px;">
                <option value="">-- Pilih L/P --</option>
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Kelas / Rombel <span style="color:var(--red);">*</span>
              </label>
              <select name="rombel_id" required style="width:100%; height:38px;">
                @if(!$isWaliOnly || $rombels->count() > 1)
                  <option value="">Pilih Rombel...</option>
                @endif
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}" {{ ($isWaliOnly && $rombels->count() === 1) ? 'selected' : '' }}>
                    {{ $r->nama_rombel }} ({{ $r->jurusan->nama_jurusan ?? 'Umum' }})
                  </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Seksi 2: Kelahiran & Agama --}}
        <div style="margin-bottom:16px; padding-top:12px; border-top:1px dashed var(--border);">
          <div style="font-size:12px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:var(--brand-emerald, #059669);"></span> Kelahiran & Agama
          </div>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Tempat Lahir
              </label>
              <input type="text" name="tempat_lahir" placeholder="Contoh: Tanggamus" style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Tanggal Lahir
              </label>
              <input type="date" name="tanggal_lahir" style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Agama
              </label>
              <select name="agama" style="width:100%; height:38px;">
                <option value="">-- Pilih Agama --</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Khonghucu">Khonghucu</option>
              </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Hobi Siswa
              </label>
              <input type="text" name="hobi" placeholder="Contoh: Membaca, Olahraga, Musik..." style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Organisasi Diminati
              </label>
              <input type="text" name="organisasi_minat" placeholder="Contoh: OSIS, Pramuka, PMR, Rohis..." style="width:100%; height:38px;" />
            </div>
          </div>
        </div>

        {{-- Seksi 3: Orang Tua (Ayah & Ibu) & Kontak WhatsApp --}}
        <div style="margin-bottom:16px; padding-top:12px; border-top:1px dashed var(--border);">
          <div style="font-size:12px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:var(--brand-amber, #d97706);"></span> Orang Tua (Ayah & Ibu) & Kontak WhatsApp
          </div>
          
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:14px; margin-bottom:12px;">
            {{-- Data Ayah --}}
            <div style="background:var(--surface); border:1px solid var(--border); border-radius:6px; padding:12px;">
              <div style="font-size:11.5px; font-weight:800; color:#2563eb; margin-bottom:8px;">
                <i class="bi bi-person-badge"></i> Data Ayah Kandung
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <div style="grid-column:1 / -1;">
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">Nama Ayah</label>
                  <input type="text" name="nama_ayah" placeholder="Nama ayah..." style="width:100%; height:34px; font-size:12px;" />
                </div>
                <div>
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">Pekerjaan</label>
                  <input type="text" name="pekerjaan_ayah" placeholder="Petani, Wiraswasta, PNS..." style="width:100%; height:34px; font-size:12px;" />
                </div>
                <div>
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">Pendidikan</label>
                  <input type="text" name="pendidikan_ayah" placeholder="SD, SMP, SMA, S1..." style="width:100%; height:34px; font-size:12px;" />
                </div>
                <div style="grid-column:1 / -1;">
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">No HP / WA Ayah</label>
                  <input type="text" name="no_hp_ayah" placeholder="08xxxxxxxxxx" style="width:100%; height:34px; font-size:12px;" />
                </div>
              </div>
            </div>

            {{-- Data Ibu --}}
            <div style="background:var(--surface); border:1px solid var(--border); border-radius:6px; padding:12px;">
              <div style="font-size:11.5px; font-weight:800; color:#db2777; margin-bottom:8px;">
                <i class="bi bi-person-heart"></i> Data Ibu Kandung
              </div>
              <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <div style="grid-column:1 / -1;">
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">Nama Ibu</label>
                  <input type="text" name="nama_ibu" placeholder="Nama ibu..." style="width:100%; height:34px; font-size:12px;" />
                </div>
                <div>
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">Pekerjaan</label>
                  <input type="text" name="pekerjaan_ibu" placeholder="IRT, Petani, Pedagang..." style="width:100%; height:34px; font-size:12px;" />
                </div>
                <div>
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">Pendidikan</label>
                  <input type="text" name="pendidikan_ibu" placeholder="SD, SMP, SMA, S1..." style="width:100%; height:34px; font-size:12px;" />
                </div>
                <div style="grid-column:1 / -1;">
                  <label style="margin-bottom:2px; font-weight:600; font-size:10.5px; color:var(--text-2);">No HP / WA Ibu</label>
                  <input type="text" name="no_hp_ibu" placeholder="08xxxxxxxxxx" style="width:100%; height:34px; font-size:12px;" />
                </div>
              </div>
            </div>
          </div>

          {{-- Kontak Notifikasi & Siswa --}}
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:10px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-2);">
                Nama Wali / Kontak Tambahan
              </label>
              <input type="text" name="nama_ortu" placeholder="Opsional jika diasuh wali..." style="width:100%; height:36px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-2);">
                No. WA Utama Presensi <span style="color:#059669; font-size:10px;">(Notif Sekolah)</span>
              </label>
              <input type="text" name="no_hp_ortu" placeholder="08xxxxxxxxxx" style="width:100%; height:36px;" />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11px; text-transform:uppercase; color:var(--text-2);">
                No. WhatsApp Siswa (Pribadi)
              </label>
              <input type="text" name="no_hp_siswa" placeholder="08xxxxxxxxxx (Opsional)" style="width:100%; height:36px;" />
            </div>
          </div>
        </div>

        {{-- Seksi 4: Domisili, Asal Sekolah & Foto --}}
        <div style="margin-bottom:16px; padding-top:12px; border-top:1px dashed var(--border);">
          <div style="font-size:12px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:#8b5cf6;"></span> Domisili, Asal Sekolah & Foto
          </div>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px;">
            <div class="form-group" style="margin-bottom:0; grid-column:span 1;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Asal Sekolah (SMP / MTs)
              </label>
              <input type="text" name="asal_sekolah" placeholder="Contoh: SMPN 1 Air Naningan" style="width:100%; height:38px;" />
            </div>

            <div class="form-group" style="margin-bottom:0; grid-column:span 1;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2); display:flex; justify-content:space-between;">
                <span>Foto Profil</span>
                <span style="color:#000000; font-size:11px; text-transform:none; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop</span>
              </label>
              <div style="display:flex; align-items:center; gap:8px;">
                <div id="tambah_siswa_foto_preview" style="width:38px; height:38px; border-radius:50%; border:1.5px solid var(--border-2); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                  <i class="bi bi-person-fill" style="color:var(--text-3); font-size:18px;"></i>
                </div>
                <input type="file" name="foto" id="inputFotoSiswaTambah" accept="image/*" onchange="initPhotoCrop(this, 'tambah_siswa_foto_preview', '1:1', 'Potong Foto Profil Siswa')" style="flex:1; height:38px;" />
              </div>
            </div>

            <div class="form-group" style="margin-bottom:0; grid-column:1 / -1;">
              <label style="margin-bottom:4px; font-weight:700; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-2);">
                Alamat Tempat Tinggal Lengkap
              </label>
              <input type="text" name="alamat" placeholder="Jalan, RT/RW, Dusun, Pekon / Desa..." style="width:100%; height:38px;" />
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:14px;">
          <button type="button" onclick="toggleTambahSiswa(false)" class="btn btn-outline">Batal</button>
          <button type="submit" class="btn btn-gold"><i class="bi bi-check2-circle"></i> Simpan Data Siswa</button>
        </div>
      </form>
    </div>
    @endif

    {{-- SITUAN MODERN TABS BAR --}}
    <div class="situan-tabs-bar no-print">
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'aktif'])) }}" class="situan-tab-item {{ $tab === 'aktif' ? 'active' : '' }}">
        <i class="bi bi-person-check-fill"></i>
        <span>Peserta Didik Aktif</span>
        <span class="situan-tab-count">{{ $statTotal }}</span>
      </a>
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'alumni'])) }}" class="situan-tab-item {{ $tab === 'alumni' ? 'active' : '' }}">
        <i class="bi bi-mortarboard-fill"></i>
        <span>Direktori Alumni / Lulusan</span>
        <span class="situan-tab-count">{{ $statAlumni }}</span>
      </a>
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'semua'])) }}" class="situan-tab-item {{ $tab === 'semua' ? 'active' : '' }}">
        <i class="bi bi-collection-fill"></i>
        <span>Semua Riwayat Siswa</span>
      </a>
    </div>

    {{-- SELECTION ACTION BAR (MUNCUL DI ATAS FILTER KETIKA ADA PILIHAN) --}}
    <div id="selectionHeaderBar" style="display:none; padding:10px 16px; background:#0F172A; color:#FFFFFF; border-radius:8px; margin-bottom:14px; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; box-shadow:0 4px 14px rgba(15,23,42,0.15);">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="background:#22C55E; color:#FFFFFF; border-radius:50%; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:900;">
          <i class="bi bi-check"></i>
        </span>
        <strong style="font-size:13px; font-weight:800;" id="selectedCountTextHeader">0 Siswa Dipilih</strong>
        <span style="font-size:11.5px; color:#94A3B8;">— Siap untuk dicetak</span>
      </div>
      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <button type="button" onclick="submitCetakPdfSelected()" class="btn btn-sm" style="background:#FFFFFF; color:#0F172A; font-weight:900; font-size:12px; height:32px; padding:0 14px; border-radius:6px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(255,255,255,0.2);">
          <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF Terpilih (<span class="selectedCountNum">0</span>)
        </button>
        <button type="button" onclick="submitCetakBarcodeSelected()" class="btn btn-sm" style="background:#0284c7; color:#FFFFFF; font-weight:900; font-size:12px; height:32px; padding:0 14px; border-radius:6px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-printer-fill"></i> Cetak Barcode Terpilih (<span class="selectedCountNum">0</span>)
        </button>
        <button type="button" onclick="clearAllSelections()" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:700; color:#E2E8F0; border-color:rgba(255,255,255,0.2); border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="Batalkan Pilihan">
          <i class="bi bi-x-circle-fill"></i> Batal
        </button>
      </div>
    </div>

    {{-- SITUAN MODERN FILTER & TOOLBAR --}}
    <div class="situan-filter-panel no-print">
      <div class="situan-filter-title">
        <i class="bi bi-mortarboard-fill" style="color:#0284c7; font-size:16px;"></i>
        <strong style="font-size:13.5px; color:#000000; white-space:nowrap;">
          Daftar Siswa @if($tab === 'alumni')<span style="font-size:11px; font-weight:600; color:#64748b;">(Alumni)</span>@endif
        </strong>
      </div>

      <form method="GET" action="{{ route('siswa.index') }}" class="situan-filter-form">
        <input type="hidden" name="tab" value="{{ $tab }}" />

        <div class="situan-search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, NISN siswa..." class="situan-input-search" />
        </div>

        <select name="rombel_id" class="situan-select" style="min-width:140px;" onchange="this.form.submit()">
          <option value="">Semua Rombel</option>
          @foreach($rombels as $r)
            <option value="{{ $r->id }}" {{ ($rombelId ?? '') == $r->id ? 'selected' : '' }}>
              {{ $r->nama_rombel }}
            </option>
          @endforeach
        </select>

        <select name="status" class="situan-select" style="min-width:125px;" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
          <option value="lulus" {{ ($status ?? '') === 'lulus' ? 'selected' : '' }}>Lulus</option>
          <option value="pindah" {{ ($status ?? '') === 'pindah' ? 'selected' : '' }}>Pindah</option>
          <option value="keluar" {{ ($status ?? '') === 'keluar' ? 'selected' : '' }}>Keluar/DO</option>
        </select>

        <select name="sort" class="situan-select" style="min-width:130px;" onchange="this.form.submit()" title="Urutkan Data Siswa">
          <option value="nama_asc" {{ ($sort ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
          <option value="nama_desc" {{ ($sort ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
          <option value="terbaru" {{ in_array($sort ?? '', ['terbaru', 'terakhir_input', 'created_desc']) ? 'selected' : '' }}>Terbaru</option>
          <option value="terlama" {{ in_array($sort ?? '', ['terlama', 'created_asc']) ? 'selected' : '' }}>Terlama</option>
          <option value="nisn_asc" {{ ($sort ?? '') === 'nisn_asc' ? 'selected' : '' }}>NISN (Naik)</option>
        </select>

        <button type="submit" class="btn-situan btn-situan-outline" style="height:36px; padding:0 14px; white-space:nowrap; flex-shrink:0;">
          <i class="bi bi-funnel"></i> Cari
        </button>

        @if($search || !empty($rombelId) || !empty($status) || (!empty($sort) && $sort !== 'nama_asc'))
          <a href="{{ route('siswa.index', ['tab' => $tab]) }}" class="btn-situan btn-situan-outline" style="height:36px; padding:0 12px; color:#ef4444 !important; border-color:rgba(239,68,68,0.4); white-space:nowrap; flex-shrink:0;" title="Reset Filter &amp; Urutan">
            <i class="bi bi-x-circle"></i> Reset
          </a>
        @endif
      </form>
    </div>

    <!-- Tabel Daftar Siswa & Data Card SITUAN -->
    <div class="situan-table-card" style="margin-bottom:24px;">
      <div class="situan-table-wrap">
        <table class="situan-table">
          <thead>
            <tr>
              <th style="width:38px; text-align:center; padding:10px 8px; white-space:nowrap;">
                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#0284c7; vertical-align:middle;" title="Pilih Semua di Halaman Ini" />
              </th>
              <th style="width:36px; text-align:center;">No</th>
              <th>Siswa</th>
              <th>Rombel &amp; Jurusan</th>
              <th>Kontak Orang Tua &amp; Siswa</th>
              <th style="text-align:center;">Status</th>
              <th style="width:100px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($siswas as $idx => $s)
              @php
                $rombelNama = ($s->siswaRombels && $s->siswaRombels->first() && $s->siswaRombels->first()->rombel)
                  ? $s->siswaRombels->first()->rombel->nama_rombel
                  : 'Tanpa Rombel';
                $kartu = $s->kartuRfid;
              @endphp
              <tr id="row-siswa-{{ $s->id }}">
                <td style="text-align:center; vertical-align:middle; padding:8px 6px; white-space:nowrap;">
                  <input type="checkbox" class="siswa-select-row" value="{{ $s->id }}" data-nama="{{ $s->nama }}" onchange="handleRowSelectChange(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#000000; vertical-align:middle;" />
                </td>
                <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12px;">
                  {{ $siswas->firstItem() + $idx }}
                </td>
                
                {{-- Siswa (Avatar + Nama + NISN + RFID Chip) --}}
                <td>
                  <div style="display:flex; align-items:flex-start; gap:10px;">
                    <div class="avatar-circle avatar-md" style="margin-top:2px; flex-shrink:0;">
                      <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="avatar-img" />
                    </div>
                    <div style="min-width:0;">
                      <div style="display:flex; align-items:center; gap:6px;">
                        <strong style="color:var(--text); font-size:13.5px;">{{ $s->nama }}</strong>
                        @if($s->jenis_kelamin)
                          <span style="font-size:10px; font-weight:800; padding:1px 5px; border-radius:4px; {{ $s->jenis_kelamin === 'L' ? 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;' : 'background:#fdf2f8; color:#db2777; border:1px solid #fbcfe8;' }}" title="{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}">{{ $s->jenis_kelamin }}</span>
                        @endif
                      </div>
                      <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px; display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        <span>NISN: <strong style="color:var(--text);">{{ $s->nisn ?: '-' }}</strong></span>
                        @if($s->nik)
                          <span style="color:var(--border-2);">•</span>
                          <span title="NIK Kependudukan">NIK: {{ $s->nik }}</span>
                        @endif

                        @if($kartu)
                          @if($canManageSiswa)
                            <button type="button"
                              onclick="openRfidPairModal('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }}', '{{ $s->foto_url }}', '{{ $kartu->uid }}')"
                              style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.3); border-radius:4px; padding:1px 6px; font-size:10px; font-weight:700; color:#065f46; cursor:pointer; font-family:var(--font-mono); display:inline-flex; align-items:center; gap:3px; transition:all .15s ease;"
                              title="Kartu RFID: {{ $kartu->uid }} (Klik untuk ubah / lepas)">
                              <i class="bi bi-broadcast" style="color:#10b981; font-size:9px;"></i> {{ $kartu->uid }}
                            </button>
                          @else
                            <span style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.3); border-radius:4px; padding:1px 6px; font-size:10px; font-weight:700; color:#065f46; font-family:var(--font-mono); display:inline-flex; align-items:center; gap:3px;">
                              <i class="bi bi-broadcast" style="color:#10b981; font-size:9px;"></i> {{ $kartu->uid }}
                            </span>
                          @endif
                        @else
                          @if($canManageSiswa)
                            <button type="button"
                              onclick="openRfidPairModal('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }}', '{{ $s->foto_url }}', '')"
                              style="background:var(--bg-3); border:1px dashed var(--border-2); border-radius:4px; padding:1px 6px; font-size:9.5px; font-weight:700; color:var(--text-3); cursor:pointer; display:inline-flex; align-items:center; gap:2px; transition:all .15s ease;"
                              title="Daftarkan Kartu RFID Siswa">
                              <i class="bi bi-plus"></i> RFID
                            </button>
                          @endif
                        @endif
                      </div>
                    </div>
                  </div>
                </td>

                {{-- Rombel & Jurusan --}}
                <td>
                  <div style="font-weight:700; font-size:13px; color:var(--text);">{{ $rombelNama }}</div>
                  @if($s->siswaRombels && $s->siswaRombels->first() && $s->siswaRombels->first()->rombel && $s->siswaRombels->first()->rombel->jurusan)
                    <div style="font-size:11.5px; color:var(--text-3);">{{ $s->siswaRombels->first()->rombel->jurusan->nama_jurusan }}</div>
                  @endif
                </td>

                {{-- Kontak Orang Tua & Siswa --}}
                <td>
                  @if($s->nama_ortu || $s->no_hp_ortu)
                    <div style="font-size:12px; color:var(--text); font-weight:600; display:flex; align-items:center; gap:4px;">
                      <span style="font-size:10px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Ortu:</span>
                      <span>{{ $s->nama_ortu ?: 'Wali Murid' }}</span>
                    </div>
                    @if($s->no_hp_ortu)
                      <div style="font-size:11.5px; font-family:var(--font-mono); color:var(--text-2); margin-top:2px;">
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $s->no_hp_ortu)) }}" target="_blank" style="color:var(--text); text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:color .15s ease;" onmouseover="this.style.color='#25D366'" onmouseout="this.style.color='var(--text)'" title="Chat WhatsApp Orang Tua">
                          <i class="bi bi-whatsapp" style="color:#25D366; font-size:11px;"></i> {{ $s->no_hp_ortu }}
                        </a>
                      </div>
                    @endif
                  @endif

                  @if($s->no_hp_siswa)
                    <div style="font-size:11.5px; font-family:var(--font-mono); color:var(--text-2); margin-top:{{ ($s->nama_ortu || $s->no_hp_ortu) ? '4px' : '0' }}; {{ ($s->nama_ortu || $s->no_hp_ortu) ? 'padding-top:3px; border-top:1px dashed var(--border-2);' : '' }}">
                      <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $s->no_hp_siswa)) }}" target="_blank" style="color:var(--text); text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:color .15s ease;" onmouseover="this.style.color='#25D366'" onmouseout="this.style.color='var(--text)'" title="Chat WhatsApp Siswa">
                        <span style="font-size:10px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Siswa:</span>
                        <i class="bi bi-phone" style="font-size:10.5px; color:var(--text-3);"></i> {{ $s->no_hp_siswa }}
                      </a>
                    </div>
                  @elseif(!$s->nama_ortu && !$s->no_hp_ortu)
                    <span style="color:var(--text-3); font-size:11.5px;">-</span>
                  @endif
                </td>

                {{-- Status --}}
                <td style="vertical-align:middle; text-align:center; padding:12px 8px; white-space:nowrap;">
                  @if($s->status === 'aktif')
                    <span class="table-status-pill aktif"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                  @elseif($s->status === 'pkl')
                    <span class="table-status-pill pkl"><i class="bi bi-building"></i> PKL</span>
                  @elseif($s->status === 'lulus')
                    <span class="table-status-pill netral"><i class="bi bi-mortarboard-fill"></i> Lulus</span>
                  @elseif($s->status === 'pindah')
                    <span class="table-status-pill netral"><i class="bi bi-box-arrow-right"></i> Pindah</span>
                  @else
                    <span class="table-status-pill belum"><i class="bi bi-dash-circle-fill"></i> {{ ucfirst($s->status) }}</span>
                  @endif
                </td>

                {{-- Aksi --}}
                <td style="vertical-align:middle; text-align:center; padding:12px 8px;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                    <a href="{{ route('kartu.digital', ['nisn' => ($s->nisn ?: $s->id)]) }}" target="_blank"
                       class="btn-icon btn-icon-view"
                       style="text-decoration:none;"
                       title="Lihat Barcode &amp; Kartu Digital Siswa">
                       <i class="bi bi-qr-code-scan"></i>
                    </a>
                    <a href="{{ route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $s->id]) }}" target="_blank"
                       class="btn-icon"
                       style="text-decoration:none; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;"
                       title="Buka Lemari Berkas Digital Siswa (E-Kabinet)">
                       <i class="bi bi-archive-fill"></i>
                    </a>
                    @if($canManageSiswa)
                      <button type="button" onclick="openEditModal({{ json_encode($s) }})" class="btn-icon btn-icon-edit" title="Edit Data Siswa">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <form action="/siswa/{{ $s->id }}" method="POST" onsubmit="return confirm('Hapus data siswa {{ $s->nama }}?')" style="display:inline; margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-danger" title="Hapus Siswa">
                          <i class="bi bi-trash3-fill"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:48px; color:var(--text-3);">
                  <i class="bi bi-person-x" style="font-size:36px; opacity:0.35;"></i>
                  <div style="font-weight:700; margin-top:10px; font-size:14px; color:var(--text);">Tidak ada data siswa yang cocok</div>
                  <p style="font-size:12px; margin-top:4px;">Coba gunakan kata kunci pencarian lain atau klik Reset.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- PAGINATION CONTROLS --}}
      @if($siswas->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--situan-border); display:flex; justify-content:center;">
          {{ $siswas->links() }}
        </div>
      @endif
    </div>
    </div> <!-- close situan-content -->
  </main>
</div>

@if($canManageSiswa)
<!-- Modal Edit Siswa -->
<div id="editModal" class="modal-overlay">
  <div class="modal-card" style="max-width:640px; max-height:90vh; overflow-y:auto; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; position:sticky; top:0; background:var(--surface, #ffffff); z-index:10; padding-bottom:8px; border-bottom:1px solid var(--border);">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-pencil-square" style="color:#000000;"></i> Edit Data Siswa (Standar Dapodik)
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('editModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form id="editForm" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div style="display:flex; flex-direction:column; gap:14px;">
        
        {{-- Seksi 1: Identitas Pokok & Rombel --}}
        <div>
          <div style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:var(--brand-blue, #2563eb);"></span> Identitas Pokok & Kelas
          </div>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">NISN <span style="color:var(--red);">*</span></label>
              <input type="text" id="edit_nisn" name="nisn" maxlength="30" required class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">NIK (KTP/KK)</label>
              <input type="text" id="edit_nik" name="nik" maxlength="16" class="input-field" style="width:100%; height:38px;" />
            </div>
            <div style="grid-column:1 / -1;">
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Nama Lengkap Siswa <span style="color:var(--red);">*</span></label>
              <input type="text" id="edit_nama" name="nama" required class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Jenis Kelamin</label>
              <select id="edit_jenis_kelamin" name="jenis_kelamin" class="input-field" style="width:100%; height:38px;">
                <option value="">-- Pilih L/P --</option>
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Kelas / Rombel <span style="color:var(--red);">*</span></label>
              <select id="edit_rombel_id" name="rombel_id" required class="input-field" style="width:100%; height:38px;">
                <option value="">-- Pilih Rombel --</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">{{ $r->nama_rombel }} ({{ $r->jurusan->nama_jurusan ?? '' }})</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Seksi 2: Kelahiran & Agama --}}
        <div style="padding-top:10px; border-top:1px dashed var(--border);">
          <div style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:var(--brand-emerald, #059669);"></span> Kelahiran & Agama
          </div>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Tempat Lahir</label>
              <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Tanggal Lahir</label>
              <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Agama</label>
              <select id="edit_agama" name="agama" class="input-field" style="width:100%; height:38px;">
                <option value="">-- Pilih Agama --</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Khonghucu">Khonghucu</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Hobi Siswa</label>
              <input type="text" id="edit_hobi" name="hobi" placeholder="Membaca, Olahraga, Musik..." class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Organisasi Diminati</label>
              <input type="text" id="edit_organisasi_minat" name="organisasi_minat" placeholder="OSIS, Pramuka, PMR..." class="input-field" style="width:100%; height:38px;" />
            </div>
          </div>
        </div>

        {{-- Seksi 3: Orang Tua (Ayah & Ibu) & Kontak --}}
        <div style="padding-top:10px; border-top:1px dashed var(--border);">
          <div style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:var(--brand-amber, #d97706);"></span> Orang Tua (Ayah & Ibu) & Kontak WhatsApp
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px; margin-bottom:10px;">
            {{-- Ayah --}}
            <div style="background:var(--bg-2); border:1px solid var(--border); border-radius:6px; padding:10px;">
              <div style="font-size:11px; font-weight:800; color:#2563eb; margin-bottom:6px;"><i class="bi bi-person-badge"></i> Ayah Kandung</div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <input type="text" id="edit_nama_ayah" name="nama_ayah" placeholder="Nama ayah..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
                <input type="text" id="edit_pekerjaan_ayah" name="pekerjaan_ayah" placeholder="Pekerjaan ayah..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
                <input type="text" id="edit_pendidikan_ayah" name="pendidikan_ayah" placeholder="Pendidikan ayah..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
                <input type="text" id="edit_no_hp_ayah" name="no_hp_ayah" placeholder="No HP/WA ayah..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
              </div>
            </div>

            {{-- Ibu --}}
            <div style="background:var(--bg-2); border:1px solid var(--border); border-radius:6px; padding:10px;">
              <div style="font-size:11px; font-weight:800; color:#db2777; margin-bottom:6px;"><i class="bi bi-person-heart"></i> Ibu Kandung</div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <input type="text" id="edit_nama_ibu" name="nama_ibu" placeholder="Nama ibu..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
                <input type="text" id="edit_pekerjaan_ibu" name="pekerjaan_ibu" placeholder="Pekerjaan ibu..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
                <input type="text" id="edit_pendidikan_ibu" name="pendidikan_ibu" placeholder="Pendidikan ibu..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
                <input type="text" id="edit_no_hp_ibu" name="no_hp_ibu" placeholder="No HP/WA ibu..." class="input-field" style="width:100%; height:32px; font-size:12px;" />
              </div>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11px; display:block; margin-bottom:4px;">Nama Wali / Kontak</label>
              <input type="text" id="edit_nama_ortu" name="nama_ortu" class="input-field" style="width:100%; height:36px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11px; display:block; margin-bottom:4px;">No. WA Utama Sekolah</label>
              <input type="text" id="edit_no_hp_ortu" name="no_hp_ortu" class="input-field" style="width:100%; height:36px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11px; display:block; margin-bottom:4px;">No. WA Siswa Pribadi</label>
              <input type="text" id="edit_no_hp_siswa" name="no_hp_siswa" placeholder="08xxxxxxxxxx" class="input-field" style="width:100%; height:36px;" />
            </div>
          </div>
        </div>

        {{-- Seksi 4: Domisili, Asal Sekolah, Status & Foto --}}
        <div style="padding-top:10px; border-top:1px dashed var(--border);">
          <div style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
            <span style="width:6px; height:6px; border-radius:50%; background:#8b5cf6;"></span> Domisili, Status & Foto
          </div>
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
            <div style="grid-column:1 / -1;">
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Alamat Tempat Tinggal Lengkap</label>
              <input type="text" id="edit_alamat" name="alamat" class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Asal Sekolah (SMP/MTs)</label>
              <input type="text" id="edit_asal_sekolah" name="asal_sekolah" class="input-field" style="width:100%; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Status Keaktifan</label>
              <select id="edit_status" name="status" class="input-field" style="width:100%; height:38px;">
                <option value="aktif">Aktif</option>
                <option value="pkl">Praktik Kerja Lapangan (PKL)</option>
                <option value="lulus">Lulus</option>
                <option value="pindah">Pindah</option>
                <option value="keluar">Keluar / DO</option>
              </select>
            </div>
            <div style="grid-column:1 / -1;">
              <label class="form-label" style="font-weight:700; font-size:11.5px; display:flex; justify-content:space-between; margin-bottom:4px;">
                <span>Ganti Foto Profil</span>
                <span style="color:#000000; font-size:11px; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
              </label>
              <div style="display:flex; align-items:center; gap:10px;">
                <div id="edit_siswa_foto_preview" style="width:38px; height:38px; border-radius:50%; border:1.5px solid rgba(0,0,0,0.15); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                  <img id="edit_siswa_foto_img" src="/img/user-default.png" style="width:100%; height:100%; object-fit:cover;" />
                </div>
                <input type="file" name="foto" id="inputFotoSiswaEdit" accept="image/*" onchange="initPhotoCrop(this, 'edit_siswa_foto_img', '1:1', 'Potong Foto Profil Siswa')" class="input-field" style="flex:1; height:38px;" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:18px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Import CSV -->
<div id="importModal" class="modal-overlay">
  <div class="modal-card" style="max-width:520px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:16.5px; font-weight:900; color:var(--text); margin:0;">
        Import Data Siswa (CSV / Excel Dapodik)
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('importModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:14px; margin-bottom:16px; font-size:12px; line-height:1.5;">
      <div style="font-weight:800; color:var(--text); margin-bottom:4px;">
        Format Parser Otomatis Standar Dapodik
      </div>
      <div style="color:var(--text-2); font-size:11.5px; margin-bottom:10px;">
        Sistem otomatis mengenali kolom Dapodik (NISN, NIK, Nama, JK, TTL, Agama, Alamat, Ayah, Ibu, No HP Ortu, No HP Siswa, Asal Sekolah, dan Kelas).
      </div>
      <div>
        <a href="{{ route('siswa.template-csv') }}" class="btn btn-sm btn-outline" style="font-weight:800; font-size:11.5px; display:inline-flex; align-items:center; gap:6px; background:var(--surface); text-decoration:none; color:var(--text);">
          <i class="bi bi-file-earmark-arrow-down"></i> Unduh Template CSV Siswa Dapodik
        </a>
      </div>
    </div>

    <form action="/siswa/import" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:6px;">Pilih File CSV / Excel (.csv) <span style="color:var(--red);">*</span></label>
        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="form-control" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('importModal')">Batal</button>
        <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1px solid #000000; font-weight:800; padding:0 16px; height:38px; border-radius:var(--r-sm); cursor:pointer;">
          Mulai Proses Import
        </button>
      </div>
    </form>
  </div>
</div>
@endif

@include('partials.crop_modal')

<script>
  function toggleTambahSiswa(forceState) {
    const panel = document.getElementById('panelTambahSiswa');
    const text = document.getElementById('textToggleTambahSiswa');
    const isHidden = (panel.style.display === 'none' || panel.style.display === '');
    const show = (forceState !== undefined) ? forceState : isHidden;
    
    panel.style.display = show ? 'block' : 'none';
    if (text) {
      text.innerText = show ? 'Tutup Form' : 'Tambah Siswa';
    }
    if (show) {
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function openEditModal(siswa) {
    document.getElementById('edit_nisn').value = siswa.nisn || '';
    document.getElementById('edit_nik').value = siswa.nik || '';
    document.getElementById('edit_nama').value = siswa.nama || '';
    document.getElementById('edit_jenis_kelamin').value = siswa.jenis_kelamin || '';
    document.getElementById('edit_tempat_lahir').value = siswa.tempat_lahir || '';
    document.getElementById('edit_tanggal_lahir').value = siswa.tanggal_lahir ? siswa.tanggal_lahir.substring(0, 10) : '';
    document.getElementById('edit_agama').value = siswa.agama || '';
    document.getElementById('edit_hobi').value = siswa.hobi || '';
    document.getElementById('edit_organisasi_minat').value = siswa.organisasi_minat || '';
    document.getElementById('edit_nama_ayah').value = siswa.nama_ayah || '';
    document.getElementById('edit_pekerjaan_ayah').value = siswa.pekerjaan_ayah || '';
    document.getElementById('edit_pendidikan_ayah').value = siswa.pendidikan_ayah || '';
    document.getElementById('edit_no_hp_ayah').value = siswa.no_hp_ayah || '';
    document.getElementById('edit_nama_ibu').value = siswa.nama_ibu || '';
    document.getElementById('edit_pekerjaan_ibu').value = siswa.pekerjaan_ibu || '';
    document.getElementById('edit_pendidikan_ibu').value = siswa.pendidikan_ibu || '';
    document.getElementById('edit_no_hp_ibu').value = siswa.no_hp_ibu || '';
    document.getElementById('edit_nama_ortu').value = siswa.nama_ortu || '';
    document.getElementById('edit_no_hp_ortu').value = siswa.no_hp_ortu || '';
    document.getElementById('edit_no_hp_siswa').value = siswa.no_hp_siswa || '';
    document.getElementById('edit_alamat').value = siswa.alamat || '';
    document.getElementById('edit_asal_sekolah').value = siswa.asal_sekolah || '';
    document.getElementById('edit_status').value = siswa.status || 'aktif';

    const imgPreview = document.getElementById('edit_siswa_foto_img');
    if (imgPreview) {
      imgPreview.src = siswa.foto_url || '/img/user-default.png';
    }

    let rombelId = '';
    if (siswa.siswa_rombels && siswa.siswa_rombels.length > 0) {
      let activeSr = siswa.siswa_rombels.find(sr => sr.status_keanggotaan === 'aktif');
      if (activeSr) rombelId = activeSr.rombel_id;
    }
    document.getElementById('edit_rombel_id').value = rombelId;

    document.getElementById('editForm').action = '/siswa/' + siswa.id;
    openModal('editModal');
  }

  function openModal(id) { document.getElementById(id).classList.add('active'); }
  function closeModal(id) { document.getElementById(id).classList.remove('active'); }

  // ── SELECTION PERSISTENCE HELPER (SESSION STORAGE) ──
  const SISWA_STORAGE_KEY = 'siswa_selected_ids';

  function getStoredSelectedIds() {
    try {
      const stored = sessionStorage.getItem(SISWA_STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (e) {
      return [];
    }
  }

  function setStoredSelectedIds(ids) {
    try {
      sessionStorage.setItem(SISWA_STORAGE_KEY, JSON.stringify(ids));
    } catch (e) {
      console.error(e);
    }
  }

  function getSelectedSiswaIds() {
    return getStoredSelectedIds();
  }

  function toggleSelectAll(masterCb) {
    const checkboxes = document.querySelectorAll('.siswa-select-row');
    let stored = getStoredSelectedIds();

    checkboxes.forEach(cb => {
      cb.checked = masterCb.checked;
      const tr = cb.closest('tr');
      const val = cb.value;
      if (masterCb.checked) {
        if (!stored.includes(val)) stored.push(val);
        if (tr) tr.style.background = 'rgba(0,0,0,0.03)';
      } else {
        stored = stored.filter(id => String(id) !== String(val));
        if (tr) tr.style.background = '';
      }
    });

    setStoredSelectedIds(stored);
    updateSelectedSiswaUI();
  }

  function handleRowSelectChange(changedCb) {
    const allCbs = document.querySelectorAll('.siswa-select-row');
    let stored = getStoredSelectedIds();

    if (changedCb) {
      const val = changedCb.value;
      const tr = changedCb.closest('tr');
      if (changedCb.checked) {
        if (!stored.includes(val)) stored.push(val);
        if (tr) tr.style.background = 'rgba(0,0,0,0.03)';
      } else {
        stored = stored.filter(id => String(id) !== String(val));
        if (tr) tr.style.background = '';
      }
      setStoredSelectedIds(stored);
    }

    const checkedCountOnPage = Array.from(allCbs).filter(cb => cb.checked).length;
    const masterCb = document.getElementById('selectAllCheckbox');
    if (masterCb) {
      masterCb.checked = (allCbs.length > 0 && allCbs.length === checkedCountOnPage);
      masterCb.indeterminate = (checkedCountOnPage > 0 && checkedCountOnPage < allCbs.length);
    }

    updateSelectedSiswaUI();
  }

  function syncCheckboxesFromStorage() {
    const stored = getStoredSelectedIds().map(v => String(v));
    const allCbs = document.querySelectorAll('.siswa-select-row');
    let checkedCountOnPage = 0;

    allCbs.forEach(cb => {
      const isChecked = stored.includes(String(cb.value));
      cb.checked = isChecked;
      const tr = cb.closest('tr');
      if (tr) {
        tr.style.background = isChecked ? 'rgba(0,0,0,0.03)' : '';
      }
      if (isChecked) checkedCountOnPage++;
    });

    const masterCb = document.getElementById('selectAllCheckbox');
    if (masterCb) {
      masterCb.checked = (allCbs.length > 0 && allCbs.length === checkedCountOnPage);
      masterCb.indeterminate = (checkedCountOnPage > 0 && checkedCountOnPage < allCbs.length);
    }

    updateSelectedSiswaUI();
  }

  function updateSelectedSiswaUI() {
    const ids = getSelectedSiswaIds();
    const headerBar = document.getElementById('selectionHeaderBar');
    const countTextHeader = document.getElementById('selectedCountTextHeader');
    const countNums = document.querySelectorAll('.selectedCountNum');
    const topBadge = document.getElementById('topSelectedBadge');

    if (ids.length > 0) {
      if (headerBar) headerBar.style.display = 'flex';
      if (countTextHeader) countTextHeader.innerText = ids.length + ' Siswa Dipilih';
      countNums.forEach(el => el.innerText = ids.length);
      if (topBadge) {
        topBadge.innerText = ids.length;
        topBadge.style.display = 'inline-block';
      }
    } else {
      if (headerBar) headerBar.style.display = 'none';
      if (topBadge) topBadge.style.display = 'none';
    }
  }

  function clearAllSelections() {
    sessionStorage.removeItem(SISWA_STORAGE_KEY);
    const checkboxes = document.querySelectorAll('.siswa-select-row');
    checkboxes.forEach(cb => {
      cb.checked = false;
      const tr = cb.closest('tr');
      if (tr) tr.style.background = '';
    });
    const masterCb = document.getElementById('selectAllCheckbox');
    if (masterCb) {
      masterCb.checked = false;
      masterCb.indeterminate = false;
    }
    updateSelectedSiswaUI();
  }

  function submitCetakPdfSelected() {
    const ids = getSelectedSiswaIds();
    if (ids.length === 0) {
      alert('Silakan pilih minimal 1 siswa untuk dicetak.');
      return;
    }
    const url = '{{ url("/siswa/cetak-pdf") }}?ids=' + ids.join(',');
    window.open(url, '_blank');
  }

  function submitCetakBarcodeSelected() {
    const ids = getSelectedSiswaIds();
    if (ids.length === 0) {
      alert('Silakan pilih minimal 1 siswa untuk dicetak barcodenya.');
      return;
    }
    const url = '{{ route("rfid.cetak") }}?tab=siswa&ids=' + ids.join(',') + '&format=barcode';
    window.open(url, '_blank');
  }

  function handleTopCetakPdfClick(anchor, event) {
    const ids = getSelectedSiswaIds();
    if (ids.length > 0) {
      event.preventDefault();
      submitCetakPdfSelected();
      return false;
    }
    return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    syncCheckboxesFromStorage();
  });
</script>

@if($canManageSiswa)
  @include('partials.rfid_pair_modal')
@endif
</body>
</html>
