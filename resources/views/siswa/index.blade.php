<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Master Data Siswa — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .siswa-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 12px;
    }
    .siswa-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 14px;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all .15s ease;
      box-shadow: var(--shadow-sm);
    }
    .siswa-stat-card:hover {
      border-color: #000000;
    }
    .siswa-stat-icon {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }
    .siswa-stat-val {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
      color: #000000;
    }
    .siswa-stat-lbl {
      font-size: 11.5px;
      color: var(--text-3);
      font-weight: 600;
      margin-top: 2px;
    }

    .siswa-form-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px 24px;
      margin-bottom: 22px;
    }
    @media (max-width: 992px) {
      .siswa-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
      .siswa-stat-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
      }
      .siswa-stat-card {
        padding: 8px 10px !important;
        gap: 10px !important;
      }
      .siswa-stat-val {
        font-size: 18px !important;
      }
      .siswa-stat-icon {
        width: 32px !important;
        height: 32px !important;
        font-size: 15px !important;
      }

      /* Toolbar Mobile Responsive */
      .siswa-table-toolbar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .siswa-table-title {
        width: 100% !important;
      }
      .siswa-table-form {
        width: 100% !important;
        max-width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .siswa-search-box {
        width: 100% !important;
      }
      .siswa-filter-group {
        width: 100% !important;
        display: flex !important;
        gap: 4px !important;
      }
      .siswa-filter-group select {
        font-size: 10.5px !important;
      }
    }
    @media (max-width: 640px) {
      .siswa-form-grid { grid-template-columns: 1fr; }
    }

    /* Tab Navigation */
    .tab-nav {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 2px;
      overflow-x: auto;
    }
    .tab-btn {
      padding: 9px 16px;
      border-radius: 8px 8px 0 0;
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      color: var(--text-2);
      background: transparent;
      border: 1px solid transparent;
      border-bottom: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
      white-space: nowrap;
    }
    .tab-btn:hover {
      color: var(--text);
      background: rgba(255,255,255,0.03);
    }
    .tab-btn.active {
      color: #000000;
      background: var(--bg-2);
      border-color: var(--border);
      border-bottom: 2px solid #000000;
      font-weight: 800;
    }

    /* Modern Hover Tooltip */
    [data-tooltip] {
      position: relative;
    }
    [data-tooltip]::before {
      content: attr(data-tooltip);
      position: absolute;
      bottom: calc(100% + 8px);
      left: 50%;
      transform: translateX(-50%) translateY(4px);
      background: #0f172a;
      color: #f8fafc;
      font-size: 11px;
      font-weight: 700;
      padding: 5px 9px;
      border-radius: 6px;
      white-space: nowrap;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity .15s ease, transform .15s ease;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
      border: 1px solid rgba(255,255,255,0.15);
      z-index: 1000;
    }
    [data-tooltip]::after {
      content: '';
      position: absolute;
      bottom: calc(100% + 2px);
      left: 50%;
      transform: translateX(-50%) translateY(4px);
      border: 4px solid transparent;
      border-top-color: #0f172a;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity .15s ease, transform .15s ease;
      z-index: 1000;
    }
    [data-tooltip]:hover::before,
    [data-tooltip]:hover::after {
      opacity: 1;
      visibility: visible;
      transform: translateX(-50%) translateY(0);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    @php
      $currentUser = auth()->user();
      $isAdmin = $currentUser && $currentUser->isAdmin();
      $isStafTu = $currentUser && $currentUser->isStafTu();
      $isWali = $currentUser && $currentUser->isWaliKelas();
      $canManageSiswa = $isAdmin || $isStafTu || $isWali;
    @endphp
    
    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-people-fill" style="color:#000000; font-size:16px;"></i> Data Siswa &amp; Alumni
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            @if(!empty($isWaliOnly) && $waliRombel)
              Rombel Binaan: <strong style="color:#000000;">{{ $waliRombel->nama_rombel }}</strong>
            @else
              Total: <strong style="color:#000000;">{{ $statTotal }}</strong> Siswa Aktif
            @endif
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($canManageSiswa)
            <button type="button" id="btnToggleTambahSiswa" onclick="toggleTambahSiswa()" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-person-plus-fill" id="iconToggleTambahSiswa"></i>
              <span id="textToggleTambahSiswa">Tambah Siswa</span>
            </button>
            <button type="button" onclick="openModal('importModal')" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
              <i class="bi bi-file-earmark-arrow-up-fill" style="color:#000000;"></i> Import CSV
            </button>
          @endif
          <a href="/siswa/export" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; text-decoration:none; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;" title="Unduh CSV Kompatibel Excel">
            <i class="bi bi-file-earmark-excel-fill" style="color:#000000;"></i> Excel
          </a>
          <a href="/siswa/cetak-pdf{{ !empty($rombelId) ? '?rombel_id='.$rombelId : '' }}" id="btnTopCetakPdf" onclick="return handleTopCetakPdfClick(this, event)" target="_blank" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; text-decoration:none; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;" title="Cetak Format A4 Kop Dinas">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#000000;"></i> PDF <span id="topSelectedBadge" style="display:none; background:#000000; color:#FFFFFF; border-radius:10px; padding:1px 6px; font-size:10px; font-family:var(--font-mono); margin-left:2px;">0</span>
          </a>
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))<div class="alert-success" style="margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:16px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- KPI STAT CARDS --}}
    <div class="siswa-stat-grid">
      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ $statTotal }}</div>
          <div class="siswa-stat-lbl">Total Siswa Aktif</div>
        </div>
      </div>

      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ $statAlumni }}</div>
          <div class="siswa-stat-lbl">Direktori Alumni / Lulus</div>
        </div>
      </div>


      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-briefcase-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ $statPkl }}</div>
          <div class="siswa-stat-lbl">Sedang Praktik Kerja (PKL)</div>
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
        
        <!-- Grid Input 2 Kolom Seimbang -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:14px; margin-bottom:14px;">
          {{-- Baris 1: Kolom 1 (NISN) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              NISN (Nomor Induk Siswa Nasional) <span style="color:var(--red);">*</span>
            </label>
            <input type="text" name="nisn" required placeholder="Contoh: 0071234567" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 1: Kolom 2 (Nama Lengkap) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Nama Lengkap Siswa <span style="color:var(--red);">*</span>
            </label>
            <input type="text" name="nama" required placeholder="Nama lengkap sesuai ijazah..." style="width:100%; height:40px;" />
          </div>

          {{-- Baris 2: Kolom 1 (Rombel) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Kelas / Rombel <span style="color:var(--red);">*</span>
            </label>
            <select name="rombel_id" required style="width:100%; height:40px;">
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

          {{-- Baris 2: Kolom 2 (Nama Orang Tua) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Nama Orang Tua / Wali
            </label>
            <input type="text" name="nama_ortu" placeholder="Nama ayah / ibu / wali..." style="width:100%; height:40px;" />
          </div>

          {{-- Baris 2: Kolom 3 (No WhatsApp Orang Tua) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              No. WhatsApp Orang Tua
            </label>
            <input type="text" name="no_hp_ortu" placeholder="08123456789" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 3: Kolom 1 (No WhatsApp Siswa) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              No. WhatsApp Siswa (Pribadi)
            </label>
            <input type="text" name="no_hp_siswa" placeholder="08987654321 (Opsional)" style="width:100%; height:40px;" />
          </div>          {{-- Baris 3: Kolom 2 (Foto) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2); display:flex; justify-content:space-between;">
              <span>Foto Profil</span>
              <span style="color:#000000; font-size:11px; text-transform:none; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
            </label>
            <div style="display:flex; align-items:center; gap:10px;">
              <div id="tambah_siswa_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid var(--border-2); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                <i class="bi bi-person-fill" style="color:var(--text-3); font-size:20px;"></i>
              </div>
              <input type="file" name="foto" id="inputFotoSiswaTambah" accept="image/*" onchange="initPhotoCrop(this, 'tambah_siswa_foto_preview', '1:1', 'Potong Foto Profil Siswa')" style="flex:1; height:40px;" />
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

    {{-- TAB NAV: AKTIF vs ALUMNI vs SEMUA --}}
    <div class="tab-nav">
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'aktif'])) }}" class="tab-btn {{ $tab === 'aktif' ? 'active' : '' }}">
        <i class="bi bi-person-check-fill"></i> Peserta Didik Aktif <span style="color:#000000; font-size:12px; font-weight:800; margin-left:4px;">{{ $statTotal }}</span>
      </a>
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'alumni'])) }}" class="tab-btn {{ $tab === 'alumni' ? 'active' : '' }}">
        <i class="bi bi-mortarboard-fill"></i> Direktori Alumni / Lulusan <span style="color:#000000; font-size:12px; font-weight:800; margin-left:4px;">{{ $statAlumni }}</span>
      </a>
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'semua'])) }}" class="tab-btn {{ $tab === 'semua' ? 'active' : '' }}">
        <i class="bi bi-collection-fill"></i> Semua Riwayat Siswa
      </a>
    </div>

    <!-- Tabel Daftar Siswa & Toolbar Terpadu -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      
      {{-- SELECTION ACTION BAR (MUNCUL DI ATAS CARI & ROMBEL KETIKA ADA PILIHAN) --}}
      <div id="selectionHeaderBar" style="display:none; padding:10px 16px; background:#0F172A; color:#FFFFFF; border-bottom:1px solid rgba(255,255,255,0.1); justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
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
          <button type="button" onclick="submitCetakBarcodeSelected()" class="btn btn-sm" style="background:#2563EB; color:#FFFFFF; font-weight:900; font-size:12px; height:32px; padding:0 14px; border-radius:6px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-printer-fill"></i> Cetak Barcode Terpilih (<span class="selectedCountNum">0</span>)
          </button>
          <button type="button" onclick="clearAllSelections()" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:700; color:#E2E8F0; border-color:rgba(255,255,255,0.2); border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="Batalkan Pilihan">
            <i class="bi bi-x-circle-fill"></i> Batal
          </button>
        </div>
      </div>

      {{-- Header & Toolbar Terpadu --}}
      <div class="siswa-table-toolbar" style="padding:8px 12px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <div class="siswa-table-title" style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:6px;">
          <i class="bi bi-mortarboard-fill" style="color:#000000;"></i>
          <span>Daftar Peserta Didik
            @if($tab === 'alumni')<span style="font-size:11px; font-weight:600; color:var(--text-3); margin-left:4px;">(Alumni)</span>@endif
          </span>
        </div>

        <form method="GET" action="{{ route('siswa.index') }}" class="siswa-table-form" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; flex:1; justify-content:flex-end; max-width:720px;">
          <input type="hidden" name="tab" value="{{ $tab }}" />

          <div class="siswa-search-box" style="position:relative; flex:1.5; min-width:130px;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px;"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, NISN..." class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding-left:28px; padding-right:8px;" />
          </div>

          <div class="siswa-filter-group" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
            <div style="min-width:110px; flex:1;">
              <select name="rombel_id" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
                <option value="">Semua Rombel</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}" {{ ($rombelId ?? '') == $r->id ? 'selected' : '' }}>
                    {{ $r->nama_rombel }}
                  </option>
                @endforeach
              </select>
            </div>

            <div style="min-width:95px; flex:1;">
              <select name="status" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="lulus" {{ ($status ?? '') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                <option value="pindah" {{ ($status ?? '') === 'pindah' ? 'selected' : '' }}>Pindah</option>
                <option value="keluar" {{ ($status ?? '') === 'keluar' ? 'selected' : '' }}>Keluar/DO</option>
              </select>
            </div>

            {{-- Dropdown Urutan / Sort By --}}
            <div style="min-width:130px; flex:1;">
              <select name="sort" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px; font-weight:700;" onchange="this.form.submit()" title="Urutkan Data Siswa">
                <option value="nama_asc" {{ ($sort ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                <option value="nama_desc" {{ ($sort ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                <option value="terbaru" {{ in_array($sort ?? '', ['terbaru', 'terakhir_input', 'created_desc']) ? 'selected' : '' }}>Terakhir Diinput (Terbaru)</option>
                <option value="terlama" {{ in_array($sort ?? '', ['terlama', 'created_asc']) ? 'selected' : '' }}>Pertama Diinput (Terlama)</option>
                <option value="nisn_asc" {{ ($sort ?? '') === 'nisn_asc' ? 'selected' : '' }}>NISN (Urut Naik)</option>
              </select>
            </div>

            <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; border-radius:var(--r-sm); flex-shrink:0;">
              Cari
            </button>

            @if($search || !empty($rombelId) || !empty($status) || (!empty($sort) && $sort !== 'nama_asc'))
              <a href="{{ route('siswa.index', ['tab' => $tab]) }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); border-radius:var(--r-sm); flex-shrink:0;" title="Reset Filter &amp; Urutan">
                Reset
              </a>
            @endif
          </div>
        </form>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:38px; text-align:center; padding:8px 6px; white-space:nowrap;">
                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#000000; vertical-align:middle;" title="Pilih Semua di Halaman Ini" />
              </th>
              <th style="width:36px; text-align:center;">No</th>
              <th>Siswa</th>
              <th>Rombel &amp; Jurusan</th>
              <th>Kontak Orang Tua &amp; Siswa</th>
              <th style="text-align:center;">Kartu RFID</th>
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
              @endphp
              <tr id="row-siswa-{{ $s->id }}">
                <td style="text-align:center; vertical-align:middle; padding:8px 6px; white-space:nowrap;">
                  <input type="checkbox" class="siswa-select-row" value="{{ $s->id }}" data-nama="{{ $s->nama }}" onchange="handleRowSelectChange(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#000000; vertical-align:middle;" />
                </td>
                <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12px;">
                  {{ $siswas->firstItem() + $idx }}
                </td>
                
                {{-- Siswa (Avatar + Nama + NISN) --}}
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar-circle avatar-md">
                      <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="avatar-img" />
                    </div>
                    <div>
                      <strong style="color:var(--text); font-size:13.5px; display:block;">{{ $s->nama }}</strong>
                      <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">
                        NISN: <strong style="color:var(--text);">{{ $s->nisn ?: '-' }}</strong>
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
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $s->no_hp_ortu)) }}" target="_blank" style="color:var(--text); text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:color .15s ease;" onmouseover="this.style.color='#25D366'" onmouseout="this.style.color='var(--text)'" title="Chat WhatsApp Wali Murid">
                          <i class="bi bi-whatsapp" style="font-size:10.5px; color:#25D366;"></i> {{ $s->no_hp_ortu }}
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


                {{-- Kartu RFID --}}
                <td style="vertical-align:middle; text-align:center; padding:12px 8px; white-space:nowrap;">
                  @php $kartu = $s->kartuRfid; @endphp
                  @if($kartu)
                    @if($canManageSiswa)
                      <button type="button"
                        onclick="openRfidPairModal('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }}', '{{ $s->foto_url }}', '{{ $kartu->uid }}')"
                        style="background:transparent; border:none; padding:4px 0; font-size:12px; font-weight:700; color:var(--text); cursor:pointer; font-family:var(--font-mono); white-space:nowrap;"
                        title="Klik untuk Ubah / Lepas Kartu RFID">
                        {{ $kartu->uid }}
                      </button>
                    @else
                      <span style="font-size:11.5px; font-weight:700; color:var(--text); font-family:var(--font-mono);">
                        {{ $kartu->uid }}
                      </span>
                    @endif
                  @else
                    @if($canManageSiswa)
                      <button type="button"
                        onclick="openRfidPairModal('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }}', '{{ $s->foto_url }}', '')"
                        style="background:transparent; border:none; padding:4px 0; font-size:11.5px; font-weight:800; color:var(--text-2); cursor:pointer; white-space:nowrap;"
                        onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-2)'"
                        title="Daftarkan Kartu RFID">
                        + RFID
                      </button>
                    @else
                      <span style="font-size:11px; font-weight:600; color:var(--text-3);">-</span>
                    @endif
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
                <td colspan="9" style="text-align:center; padding:48px; color:var(--text-3);">
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
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:center;">
          {{ $siswas->links() }}
        </div>
      @endif
    </div>
  </main>
</div>

@if($canManageSiswa)
<!-- Modal Edit Siswa -->
<div id="editModal" class="modal-overlay">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-pencil-square" style="color:#000000;"></i> Edit Data Siswa
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('editModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form id="editForm" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">NISN (Nomor Induk Siswa Nasional) <span style="color:var(--red);">*</span></label>
          <input type="text" id="edit_nisn" name="nisn" required class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Lengkap Siswa <span style="color:var(--red);">*</span></label>
          <input type="text" id="edit_nama" name="nama" required class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Rombel / Kelas <span style="color:var(--red);">*</span></label>
          <select id="edit_rombel_id" name="rombel_id" required class="input-field" style="width:100%;">
            <option value="">-- Pilih Rombel --</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}">{{ $r->nama_rombel }} ({{ $r->jurusan->kode_jurusan ?? '' }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Orang Tua / Wali</label>
          <input type="text" id="edit_nama_ortu" name="nama_ortu" class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">No. WhatsApp Orang Tua</label>
          <input type="text" id="edit_no_hp_ortu" name="no_hp_ortu" class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">No. WhatsApp Siswa (Pribadi)</label>
          <input type="text" id="edit_no_hp_siswa" name="no_hp_siswa" placeholder="08987654321 (Opsional)" class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Status Keaktifan</label>
          <select id="edit_status" name="status" class="input-field" style="width:100%;">
            <option value="aktif">Aktif</option>
            <option value="lulus">Lulus</option>
            <option value="pindah">Pindah</option>
            <option value="keluar">Keluar / DO</option>
          </select>
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:flex; justify-content:space-between; margin-bottom:4px;">
            <span>Ganti Foto Profil</span>
            <span style="color:#000000; font-size:11px; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
          </label>
          <div style="display:flex; align-items:center; gap:10px;">
            <div id="edit_siswa_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid rgba(0,0,0,0.15); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
              <img id="edit_siswa_foto_img" src="/img/user-default.png" style="width:100%; height:100%; object-fit:cover;" />
            </div>
            <input type="file" name="foto" id="inputFotoSiswaEdit" accept="image/*" onchange="initPhotoCrop(this, 'edit_siswa_foto_img', '1:1', 'Potong Foto Profil Siswa')" class="input-field" style="flex:1;" />
          </div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:18px;">
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
        Import Data Siswa (CSV / Excel)
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('importModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:14px; margin-bottom:16px; font-size:12px; line-height:1.5;">
      <div style="font-weight:800; color:var(--text); margin-bottom:4px;">
        Format Kolom CSV Otomatis
      </div>
      <div style="color:var(--text-2); font-size:11.5px; margin-bottom:10px;">
        Sistem otomatis mengenali format kolom (NISN, Nama, Nama Ortu, No WA Ortu, No WA Siswa, dan Kelas).
      </div>
      <div>
        <a href="{{ route('siswa.template-csv') }}" class="btn btn-sm btn-outline" style="font-weight:800; font-size:11.5px; display:inline-flex; align-items:center; gap:6px; background:var(--surface); text-decoration:none; color:var(--text);">
          Unduh Contoh Template CSV Siswa
        </a>
      </div>
    </div>

    <form action="/siswa/import" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:6px;">Pilih File CSV / Excel (.csv) <span style="color:var(--red);">*</span></label>
        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="input-field" style="width:100%; height:40px; padding:6px 10px; font-size:12.5px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
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
    document.getElementById('edit_nisn').value = siswa.nisn || '';
    document.getElementById('edit_nama').value = siswa.nama || '';
    document.getElementById('edit_nama_ortu').value = siswa.nama_ortu || '';
    document.getElementById('edit_no_hp_ortu').value = siswa.no_hp_ortu || '';
    document.getElementById('edit_no_hp_siswa').value = siswa.no_hp_siswa || '';
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
