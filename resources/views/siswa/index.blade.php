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
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
      margin-bottom: 20px;
    }
    .siswa-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all .2s ease;
    }
    .siswa-stat-card:hover {
      border-color: var(--border-2);
      transform: translateY(-2px);
    }
    .siswa-stat-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }
    .siswa-stat-val {
      font-size: 22px;
      font-weight: 900;
      font-family: var(--font);
      line-height: 1.1;
      color: var(--text);
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
      color: var(--gold);
      background: var(--bg-2);
      border-color: var(--border);
      border-bottom: 2px solid var(--gold);
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
    @endphp
    
    {{-- HEADER --}}
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-people-fill" style="color:var(--gold);"></i> Master Data Siswa &amp; Alumni
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          @if(!empty($isWaliOnly) && $waliRombel)
            Menampilkan data peserta didik khusus rombel binaan Anda: <strong style="color:var(--gold);">{{ $waliRombel->nama_rombel }}</strong>
          @else
            Kelola profil peserta didik aktif, direktori lulusan/alumni, kontak orang tua, dan pembinaan karakter.
          @endif
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        @if($isAdmin || $isStafTu)
        <button type="button" id="btnToggleTambahSiswa" onclick="toggleTambahSiswa()" class="btn btn-gold" style="height:38px; padding:0 16px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-person-plus-fill"></i> <span id="textToggleTambahSiswa">+ Tambah Siswa</span>
        </button>
        <button type="button" onclick="openModal('importModal')" class="btn btn-outline" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:700;">
          <i class="bi bi-file-earmark-arrow-up-fill" style="margin-right:4px;"></i>Import CSV
        </button>
        @endif
        <a href="/siswa/export" class="btn btn-outline" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:700; text-decoration:none; color:var(--green); border-color:var(--green);" title="Unduh CSV Kompatibel Excel">
          <i class="bi bi-file-earmark-excel-fill" style="margin-right:4px;"></i>Export Excel
        </a>
        <a href="/siswa/cetak-pdf" target="_blank" class="btn btn-outline" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:700; text-decoration:none; color:var(--text);" title="Cetak Format A4 Kop Dinas">
          <i class="bi bi-file-earmark-pdf-fill" style="margin-right:4px; color:var(--gold);"></i>Cetak PDF
        </a>
      </div>
    </header>

    @if(session('success'))<div class="alert-success" style="margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:16px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- KPI STAT CARDS --}}
    <div class="siswa-stat-grid">
      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ $statTotal }}</div>
          <div class="siswa-stat-lbl">Total Siswa Aktif</div>
        </div>
      </div>

      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ $statAlumni }}</div>
          <div class="siswa-stat-lbl">Direktori Alumni / Lulus</div>
        </div>
      </div>

      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-camera-video-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ \App\Models\Siswa::whereNotNull('face_embedding')->count() }}</div>
          <div class="siswa-stat-lbl">Face ID Terdaftar</div>
        </div>
      </div>

      <div class="siswa-stat-card">
        <div class="siswa-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-briefcase-fill"></i>
        </div>
        <div>
          <div class="siswa-stat-val">{{ $statPkl }}</div>
          <div class="siswa-stat-lbl">Sedang Praktik Kerja (PKL)</div>
        </div>
      </div>
    </div>

    @if($isAdmin)
    <!-- Form Tambah Siswa (Collapsible / Triggered) -->
    <div class="panel" id="panelTambahSiswa" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom: 20px; border-color: rgba(234, 179, 8, 0.4); background: linear-gradient(180deg, rgba(234, 179, 8, 0.04) 0%, var(--panel) 100%);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="stat-icon" style="width:36px; height:36px; border-radius:8px; background:rgba(234, 179, 8, 0.15); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <span style="font-weight:800; font-size:15px; color:var(--text);">Form Tambah Siswa Baru</span>
            <div style="font-size:12px; color:var(--text-3);">Lengkapi data siswa untuk presensi Face ID.</div>
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
          {{-- Baris 1: Kolom 1 (NIS) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Nomor Induk Siswa (NIS) <span style="color:var(--red);">*</span>
            </label>
            <input type="text" name="nis" required placeholder="Contoh: 10245" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 1: Kolom 2 (NISN) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              NISN (Nasional)
            </label>
            <input type="text" name="nisn" placeholder="Contoh: 0071234567" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 1: Kolom 3 (Nama Lengkap) --}}
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
              <option value="">Pilih Rombel...</option>
              @foreach($rombels as $r)
                <option value="{{ $r->id }}">{{ $r->nama_rombel }} ({{ $r->jurusan->nama_jurusan ?? 'Umum' }})</option>
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
          </div>

          {{-- Baris 3: Kolom 2 (Foto) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2); display:flex; justify-content:space-between;">
              <span>Foto Profil</span>
              <span style="color:var(--gold); font-size:11px; text-transform:none;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
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
        <i class="bi bi-person-check-fill"></i> Peserta Didik Aktif
        <span class="badge" style="background:rgba(202,138,4,0.15); color:var(--gold); font-size:10.5px; padding:2px 7px; border-radius:10px; font-weight:800;">{{ $statTotal }}</span>
      </a>
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'alumni'])) }}" class="tab-btn {{ $tab === 'alumni' ? 'active' : '' }}">
        <i class="bi bi-mortarboard-fill"></i> Direktori Alumni / Lulusan
        <span class="badge" style="background:rgba(34,197,94,0.15); color:#16A34A; font-size:10.5px; padding:2px 7px; border-radius:10px; font-weight:800;">{{ $statAlumni }}</span>
      </a>
      <a href="{{ route('siswa.index', array_merge(request()->except('tab', 'page'), ['tab' => 'semua'])) }}" class="tab-btn {{ $tab === 'semua' ? 'active' : '' }}">
        <i class="bi bi-collection-fill"></i> Semua Riwayat Siswa
      </a>
    </div>

    <!-- Tabel Daftar Siswa & Toolbar Terpadu -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      {{-- Header Tabel --}}
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-mortarboard-fill" style="color:var(--gold);"></i>
          <span>Daftar Peserta Didik
            @if($tab === 'alumni')<span style="font-size:11px; font-weight:600; color:var(--text-3); margin-left:4px;">— Alumni / Lulusan</span>@endif
            @if($tab === 'semua')<span style="font-size:11px; font-weight:600; color:var(--text-3); margin-left:4px;">— Semua Riwayat</span>@endif
          </span>
        </div>
        <div>
          @if($isAdmin || $isStafTu)
            <button type="button" onclick="toggleTambahSiswa(true)" class="btn btn-gold" style="padding:6px 14px; font-size:12px; font-weight:800; border-radius:var(--r-sm);">
              <i class="bi bi-plus-lg"></i> Tambah Siswa
            </button>
          @endif
        </div>
      </div>

      {{-- Toolbar Search & Filter Terpadu --}}
      <div style="padding:12px 18px; border-bottom:1px solid var(--border); background:var(--surface);">
        <form method="GET" action="{{ route('siswa.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
          <input type="hidden" name="tab" value="{{ $tab }}" />

          <div style="flex:1.8; min-width:200px;">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama siswa, NIS, wali murid..." class="input-field" style="width:100%; height:36px; font-size:12.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px;" />
          </div>

          <div style="min-width:140px;">
            <select name="rombel_id" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Rombel</option>
              @foreach($rombels as $r)
                <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
              @endforeach
            </select>
          </div>

          <div style="min-width:130px;">
            <select name="status_pkl" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Status PKL</option>
              <option value="aktif_pkl" {{ $statusPkl === 'aktif_pkl' ? 'selected' : '' }}>Sedang PKL</option>
              <option value="belum_pkl" {{ $statusPkl === 'belum_pkl' ? 'selected' : '' }}>Belum PKL</option>
              <option value="selesai_pkl" {{ $statusPkl === 'selesai_pkl' ? 'selected' : '' }}>Selesai PKL</option>
            </select>
          </div>

          <div style="min-width:130px;">
            <select name="face_id" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Status Face ID</option>
              <option value="ada" {{ $rfidStatus === 'ada' ? 'selected' : '' }}>Terdaftar Face ID</option>
              <option value="belum" {{ $rfidStatus === 'belum' ? 'selected' : '' }}>Belum Face ID</option>
            </select>
          </div>

          <div style="min-width:130px;">
            <select name="sort" class="input-field" style="width:100%; height:36px; font-size:12px; font-weight:700; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="nama_asc" {{ $sort === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
              <option value="nama_desc" {{ $sort === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
              <option value="nis_asc" {{ $sort === 'nis_asc' ? 'selected' : '' }}>NIS (Terkecil)</option>
              <option value="nis_desc" {{ $sort === 'nis_desc' ? 'selected' : '' }}>NIS (Terbesar)</option>
              <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Data Terbaru</option>
            </select>
          </div>

          <button type="submit" class="btn btn-outline" style="height:36px; padding:0 12px; font-size:12px; font-weight:700;">
            <i class="bi bi-search"></i> Cari
          </button>

          @if($search || $rombelId || $statusPkl || $rfidStatus || ($sort && $sort !== 'nama_asc'))
            <a href="{{ route('siswa.index', ['tab' => $tab]) }}" class="btn btn-outline" style="height:36px; padding:0 10px; font-size:12px; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              Reset
            </a>
          @endif
        </form>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="background:var(--bg-3);">
              <th style="width:40px; text-align:center; padding:12px 8px;">No</th>
              <th style="min-width:240px; padding:12px 14px;">Siswa &amp; Identitas</th>
              <th style="min-width:150px; padding:12px 14px;">Kelas / Rombel</th>
              <th style="min-width:170px; padding:12px 14px;">Kontak Orang Tua</th>
              <th style="min-width:150px; padding:12px 14px;">Face ID Biometrik</th>
              <th style="width:80px; text-align:center; padding:12px 8px;">Status</th>
              <th style="width:110px; text-align:center; padding:12px 8px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($siswas as $idx => $s)
              @php
                $rombelAktif = $s->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel 
                  ?? $s->siswaRombels->first()?->rombel;
                $namaRombel   = $rombelAktif ? $rombelAktif->nama_rombel : '-';
                $namaJurusan  = $rombelAktif?->jurusan?->nama_jurusan ?? null;
                $cleanHpOrtu  = preg_replace('/[^0-9]/', '', $s->no_hp_ortu ?? '');
                if (str_starts_with($cleanHpOrtu, '0')) { $cleanHpOrtu = '62' . substr($cleanHpOrtu, 1); }
              @endphp
              <tr style="border-bottom:1px solid var(--border);">

                {{-- No. Urut --}}
                <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px; vertical-align:middle;">
                  {{ $siswas->firstItem() + $idx }}
                </td>

                {{-- Siswa & Identitas --}}
                <td style="vertical-align:middle; padding:12px 14px;">
                  <div style="display:flex; align-items:center; gap:12px;">
                    <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}"
                         style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid rgba(202,138,4,0.3); flex-shrink:0;" />
                    <div style="min-width:0;">
                      <div style="font-weight:800; font-size:13.5px; color:var(--text); line-height:1.25;">{{ $s->nama }}</div>
                      <div style="font-size:11.5px; font-family:var(--font-mono); color:var(--text-3); margin-top:2px;">
                        NIS: {{ $s->nis }}@if($s->nisn) &nbsp;·&nbsp; NISN: {{ $s->nisn }}@endif
                      </div>
                      @if($s->nama_ortu)
                        <div style="font-size:11px; color:var(--text-3); margin-top:3px;">
                          Wali: {{ $s->nama_ortu }}
                        </div>
                      @endif
                    </div>
                  </div>
                </td>

                {{-- Kelas / Rombel --}}
                <td style="vertical-align:middle; padding:12px 14px;">
                  @if($rombelAktif)
                    <div style="font-size:13px; font-weight:700; color:var(--text);">{{ $namaRombel }}</div>
                    @if($namaJurusan)
                      <div style="font-size:10.5px; color:var(--text-3); margin-top:3px;">{{ $namaJurusan }}</div>
                    @endif
                  @else
                    <span style="color:var(--text-3); font-size:12px;">-</span>
                  @endif
                </td>

                {{-- Kontak Orang Tua --}}
                <td style="vertical-align:middle; padding:12px 14px;">
                  @if($s->no_hp_ortu)
                    <a href="https://wa.me/{{ $cleanHpOrtu }}" target="_blank"
                       style="color:#16A34A; font-size:12px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px; background:rgba(22,163,74,0.08); padding:4px 10px; border-radius:6px; border:1px solid rgba(22,163,74,0.25);"
                       title="Chat WhatsApp Wali Murid">
                      <i class="bi bi-whatsapp"></i> {{ $s->no_hp_ortu }}
                    </a>
                  @else
                    <span style="color:var(--text-3); font-size:12px;">-</span>
                  @endif
                  @if($s->no_hp_siswa)
                    <div style="margin-top:4px;">
                      <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $s->no_hp_siswa)) }}" target="_blank"
                         style="color:var(--text-3); font-size:11px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;"
                         title="WA Siswa Pribadi">
                        <i class="bi bi-phone"></i> {{ $s->no_hp_siswa }}
                      </a>
                    </div>
                  @endif
                </td>

                {{-- Face ID --}}
                <td style="vertical-align:middle; padding:12px 14px;">
                  @if($s->face_embedding)
                    <div style="display:flex; align-items:center; gap:5px;">
                      <button type="button"
                        onclick="openViewFaceModal('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}', 'NIS: {{ $s->nis }}', '{{ $s->foto_url }}', '{{ $s->face_registered_at?->translatedFormat('d F Y, H:i') ?? 'Terdaftar Aktif' }}')"
                        class="btn btn-sm"
                        style="padding:4px 9px; font-size:11px; font-weight:700; background:var(--bg-3); color:var(--text-2); border:1px solid var(--border-2); border-radius:6px; display:inline-flex; align-items:center; gap:4px;"
                        title="Lihat Status Biometrik">
                        <i class="bi bi-shield-check" style="color:#16A34A;"></i> Terdaftar
                      </button>
                      @if($isAdmin || $isStafTu)
                        <button type="button"
                          onclick="quickDeleteFace('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}')"
                          class="btn btn-sm btn-outline"
                          style="padding:4px 7px; font-size:11px; color:#EF4444; border-color:rgba(239,68,68,0.3); border-radius:6px;"
                          title="Hapus Face ID">
                          <i class="bi bi-trash3-fill"></i>
                        </button>
                      @endif
                    </div>
                  @else
                    @if($isAdmin || $isStafTu)
                      <button type="button"
                        onclick="openFaceEnrollModal('siswa', {{ $s->id }}, '{{ addslashes($s->nama) }}', 'NIS: {{ $s->nis }}', '{{ $s->foto_url }}')"
                        class="btn btn-sm btn-outline"
                        style="padding:4px 9px; font-size:11px; font-weight:700; color:var(--gold); border-color:var(--gold); border-radius:6px; display:inline-flex; align-items:center; gap:4px;"
                        title="Daftarkan Biometrik Wajah">
                        <i class="bi bi-camera-fill"></i> Rekam Wajah
                      </button>
                    @else
                      <span class="badge" style="background:var(--bg-3); color:var(--text-3); font-size:10.5px; font-weight:600; padding:4px 8px; border-radius:6px;">Belum Rekam</span>
                    @endif
                  @endif
                </td>

                {{-- Status --}}
                <td style="vertical-align:middle; text-align:center; padding:12px 8px;">
                  @if($s->status === 'aktif')
                    <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; font-weight:800; font-size:10px;">AKTIF</span>
                  @elseif($s->status === 'lulus')
                    <span class="badge" style="background:rgba(202,138,4,0.15); color:#CA8A04; font-weight:800; font-size:10px; border:1px solid rgba(202,138,4,0.3);">LULUS</span>
                  @else
                    <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-size:10px;">{{ strtoupper($s->status) }}</span>
                  @endif
                </td>

                {{-- Aksi --}}
                <td style="vertical-align:middle; text-align:center; padding:12px 8px;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                    <a href="/siswa/{{ $s->id }}/surat-bebas-masalah" target="_blank"
                       class="btn-icon btn-icon-edit"
                       style="text-decoration:none;"
                       title="Cetak Surat Bebas Masalah">
                      <i class="bi bi-file-earmark-check-fill"></i>
                    </a>
                    @if($isAdmin || $isStafTu)
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
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:center;">
          {{ $siswas->links() }}
        </div>
      @endif
    </div>
  </main>
</div>

@if($isAdmin)
<!-- Modal Edit Siswa -->
<div id="editModal" class="modal-overlay">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-pencil-square" style="color:var(--gold);"></i> Edit Data Siswa
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('editModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form id="editForm" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">NIS <span style="color:var(--red);">*</span></label>
          <input type="text" id="edit_nis" name="nis" required class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">NISN (Opsional)</label>
          <input type="text" id="edit_nisn" name="nisn" class="input-field" style="width:100%;" />
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
            <span style="color:var(--gold); font-size:11px;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
          </label>
          <div style="display:flex; align-items:center; gap:10px;">
            <div id="edit_siswa_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid var(--gold); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
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
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-file-earmark-arrow-up-fill" style="color:var(--gold);"></i> Import Data Siswa (CSV / Excel)
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('importModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--surface); border:1px solid var(--border-2); border-radius:12px; padding:12px 14px; margin-bottom:16px; font-size:12px; line-height:1.5;">
      <div style="font-weight:800; color:var(--gold); display:flex; align-items:center; gap:6px; margin-bottom:4px;">
        <i class="bi bi-info-circle-fill"></i> Format Kolom CSV Otomatis
      </div>
      <div style="color:var(--text-2);">
        Sistem otomatis mengenali format kolom (NIS, NISN, Nama, Nama Ortu, No WA Ortu, No WA Siswa, dan Kelas).
      </div>
      <div style="margin-top:8px;">
        <a href="{{ route('siswa.template-csv') }}" class="btn btn-sm btn-outline" style="font-weight:700; font-size:11.5px; display:inline-flex; align-items:center; gap:6px; background:var(--bg-2);">
          <i class="bi bi-download" style="color:var(--gold);"></i> Unduh Contoh Template CSV Siswa
        </a>
      </div>
    </div>

    <form action="/siswa/import" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom:16px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:6px;">Pilih File CSV / Excel (.csv) <span style="color:var(--red);">*</span></label>
        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="input-field" style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('importModal')">Batal</button>
        <button type="submit" class="btn btn-gold"><i class="bi bi-cloud-arrow-up-fill"></i> Mulai Proses Import</button>
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
    text.innerText = show ? 'Tutup Form' : '+ Tambah Siswa';
  }

  function openEditModal(siswa) {
    document.getElementById('edit_nis').value = siswa.nis || '';
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
</script>

@include('partials.face_enroll_modal')
</body>
</html>
