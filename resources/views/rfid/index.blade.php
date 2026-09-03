<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Pusat Manajemen Kartu RFID — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .rfid-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 12px;
    }
    @media (max-width: 600px) {
      .rfid-stat-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    }
    .rfid-stat-card {
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
    .rfid-stat-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      background: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      color: #000000;
      flex-shrink: 0;
    }
    [data-theme="dark"] .rfid-stat-icon {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.15);
      color: #FFFFFF;
    }
    .rfid-stat-num {
      font-size: 19px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: var(--text);
      line-height: 1.1;
    }
    .rfid-stat-label {
      font-size: 11px;
      font-weight: 700;
      color: var(--text-2);
      margin-top: 2px;
    }

    /* Live Card Tester Box */
    .tester-card-wrap {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 10px 14px;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: var(--shadow-sm);
      flex-wrap: wrap;
    }

    /* Segmented Tab Control */
    .rfid-tab-control {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      padding: 4px;
      border-radius: var(--r-md);
      display: inline-flex;
      gap: 4px;
      box-shadow: var(--shadow-sm);
      position: relative;
      transition: all .25s ease;
    }
    .rfid-tab-btn {
      padding: 8px 18px;
      border-radius: 8px;
      font-size: 12.5px;
      font-weight: 800;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      user-select: none;
      -webkit-tap-highlight-color: transparent;
    }
    .rfid-tab-btn:active {
      transform: scale(0.96);
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 768px) {
      .rfid-tab-wrap {
        width: 100% !important;
      }
      .rfid-tab-control {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        padding: 5px !important;
        gap: 6px !important;
        border-radius: 12px !important;
      }
      .rfid-tab-btn {
        width: 100% !important;
        padding: 10px 8px !important;
        font-size: 12.5px !important;
        border-radius: 9px !important;
      }
      .tester-card-wrap {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
      }
      .tester-card-input-wrap {
        width: 100% !important;
        max-width: 100% !important;
      }
      .rfid-table-toolbar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
        padding: 10px 12px !important;
      }
      .rfid-table-title {
        width: 100% !important;
        font-size: 13px !important;
      }
      .rfid-table-form {
        width: 100% !important;
        max-width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .rfid-search-box {
        width: 100% !important;
      }
      .rfid-filter-group {
        width: 100% !important;
        display: flex !important;
        gap: 6px !important;
        align-items: center !important;
      }
      .rfid-filter-group select {
        flex: 1 !important;
        min-width: 0 !important;
        font-size: 11px !important;
        height: 34px !important;
      }
      .rfid-filter-group button, .rfid-filter-group a {
        height: 34px !important;
        flex-shrink: 0 !important;
      }
    /* Searchable Person Selector in Modal */
    .person-select-item {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 10px;
      padding: 9px 12px;
      cursor: pointer;
      border-bottom: 1px solid var(--border-2);
      transition: all .15s ease;
      background: var(--bg-2);
      user-select: none;
    }
    .person-select-item:last-child {
      border-bottom: none;
    }
    .person-select-item:hover {
      background: rgba(0, 0, 0, 0.05);
    }
    [data-theme="dark"] .person-select-item:hover {
      background: rgba(255, 255, 255, 0.08);
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

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-person-vcard-fill" style="color:#000000; font-size:16px;"></i> Kartu Pintar (Barcode &amp; RFID)
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Kompatibel Scanner Barcode USB &amp; Kartu RFID
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($isAdmin || $isStafTu)
            <button type="button" onclick="openModalBroadcastWa()" class="btn btn-sm btn-outline" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;" title="Kirim Barcode Massal ke WhatsApp Siswa / Orang Tua">
              <i class="bi bi-whatsapp" style="color:#22c55e;"></i> Broadcast Barcode WA
            </button>
            <a href="{{ route('rfid.cetak', ['tab' => $tab, 'rombel_id' => $rombelId]) }}" id="btnTopCetakKartu" onclick="return handleTopCetakClick(this, event)" target="_blank" class="btn btn-sm btn-outline" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; text-decoration:none;" title="Cetak Kartu Siswa/Guru Siap Pakai">
              <i class="bi bi-printer-fill"></i> Cetak Kartu Barcode <span id="topSelectedBadge" style="display:none; background:#000000; color:#FFFFFF; border-radius:10px; padding:1px 6px; font-size:10px; font-family:var(--font-mono); margin-left:2px;">0</span>
            </a>
            <button type="button" onclick="openModalTambahKartu('{{ $tab }}')" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-plus-circle-fill"></i> Tambah / Pasang Kartu
            </button>
          @endif
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))<div class="alert-success" style="margin-bottom:12px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:12px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif

    {{-- KPI STAT CARDS --}}
    <div class="rfid-stat-grid">
      <div class="rfid-stat-card">
        <div class="rfid-stat-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
        <div>
          <div class="rfid-stat-num">{{ $statTotalAktif }}</div>
          <div class="rfid-stat-label">Total Pemilik Kartu</div>
        </div>
      </div>

      <div class="rfid-stat-card">
        <div class="rfid-stat-icon"><i class="bi bi-people-fill"></i></div>
        <div>
          <div class="rfid-stat-num">{{ $statSiswaAktif }}</div>
          <div class="rfid-stat-label">Kartu Siswa Aktif</div>
        </div>
      </div>

      <div class="rfid-stat-card">
        <div class="rfid-stat-icon"><i class="bi bi-person-badge-fill"></i></div>
        <div>
          <div class="rfid-stat-num">{{ $statGuruAktif }}</div>
          <div class="rfid-stat-label">Kartu Guru &amp; Staf</div>
        </div>
      </div>

      <div class="rfid-stat-card">
        <div class="rfid-stat-icon" style="background:rgba(34,197,94,0.1); color:#16a34a;"><i class="bi bi-broadcast"></i></div>
        <div>
          <div class="rfid-stat-num" style="color:#16a34a;">{{ $statRfidPaired }}</div>
          <div class="rfid-stat-label">Terpasang RFID Fisik</div>
        </div>
      </div>
    </div>

    {{-- LIVE SENSOR TESTER & QUICK DETECTOR --}}
    <div class="tester-card-wrap">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:32px; height:32px; border-radius:50%; background:#000000; color:#fff; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0;">
          <i class="bi bi-broadcast"></i>
        </div>
        <div>
          <strong style="font-size:12.5px; color:var(--text); display:block;">Sensor Cek Pemilik Kartu (Live Reader)</strong>
          <span style="font-size:11px; color:var(--text-3);">Tempelkan kartu ke USB reader untuk mendeteksi pemiliknya.</span>
        </div>
      </div>

      <div class="tester-card-input-wrap" style="display:flex; align-items:center; gap:6px; flex:1; max-width:340px; min-width:220px;">
        <input type="text" id="testerInput" name="rfid_tester_uid_{{ rand(100,999) }}" placeholder="Tempelkan kartu RFID..."
          autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
          style="width:100%; height:32px; border-radius:var(--r-sm); border:1px solid var(--border-2); background:var(--bg-3); color:var(--text); font-family:var(--font-mono); font-size:11.5px; padding:0 10px; outline:none; text-align:center;" autofocus />
        <button type="button" onclick="testCardUid()" class="btn btn-sm btn-outline" style="height:32px; padding:0 12px; font-weight:800; font-size:11.5px; flex-shrink:0;">
          Periksa
        </button>
      </div>
    </div>

    {{-- TAB SWITCHER: SISWA VS GURU --}}
    <div class="rfid-tab-wrap" style="display:flex; justify-content:flex-start; align-items:center; gap:8px; margin-bottom:12px;">
      <div class="rfid-tab-control">
        <a href="{{ route('rfid.index', ['tab' => 'siswa']) }}"
           class="rfid-tab-btn"
           style="{{ $tab === 'siswa' ? 'background:#000000; color:#FFFFFF; box-shadow:0 2px 6px rgba(0,0,0,0.25);' : 'color:var(--text-2);' }}">
          <i class="bi bi-people-fill"></i>
          <span>Kartu Siswa</span>
          <span style="font-size:10.5px; font-family:var(--font-mono); font-weight:800; padding:1px 6px; border-radius:6px; {{ $tab === 'siswa' ? 'background:rgba(255,255,255,0.25); color:#FFFFFF;' : 'background:rgba(0,0,0,0.06); color:var(--text);' }}">
            {{ $statSiswaAktif }}
          </span>
        </a>
        <a href="{{ route('rfid.index', ['tab' => 'guru']) }}"
           class="rfid-tab-btn"
           style="{{ $tab === 'guru' ? 'background:#000000; color:#FFFFFF; box-shadow:0 2px 6px rgba(0,0,0,0.25);' : 'color:var(--text-2);' }}">
          <i class="bi bi-person-badge-fill"></i>
          <span>Kartu Guru &amp; Pegawai</span>
          <span style="font-size:10.5px; font-family:var(--font-mono); font-weight:800; padding:1px 6px; border-radius:6px; {{ $tab === 'guru' ? 'background:rgba(255,255,255,0.25); color:#FFFFFF;' : 'background:rgba(0,0,0,0.06); color:var(--text);' }}">
            {{ $statGuruAktif }}
          </span>
        </a>
      </div>
    </div>

    {{-- TABEL TERPADU KARTU RFID --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      
      {{-- SELECTION ACTION BAR (MUNCUL DI ATAS CARI & ROMBEL KETIKA ADA PILIHAN) --}}
      <div id="selectionHeaderBar" style="display:none; padding:10px 16px; background:#0F172A; color:#FFFFFF; border-bottom:1px solid rgba(255,255,255,0.1); justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px;">
          <span style="background:#22C55E; color:#FFFFFF; border-radius:50%; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:12px; font-weight:900;">
            <i class="bi bi-check"></i>
          </span>
          <strong style="font-size:13px; font-weight:800;" id="selectedCountTextHeader">0 Siswa Dipilih</strong>
          <span style="font-size:11.5px; color:#94A3B8;">— Siap untuk cetak kartu barcode</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <button type="button" onclick="submitCetakSelected('barcode')" class="btn btn-sm" style="background:#FFFFFF; color:#0F172A; font-weight:900; font-size:12px; height:32px; padding:0 14px; border-radius:6px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(255,255,255,0.2);">
            <i class="bi bi-printer-fill"></i> Cetak Kartu Barcode Terpilih (<span class="selectedCountNum">0</span>)
          </button>
          <button type="button" onclick="clearAllSelections()" class="btn btn-sm" style="background:rgba(255,255,255,0.1); color:#E2E8F0; border:1px solid rgba(255,255,255,0.2); font-size:11.5px; font-weight:700; height:32px; padding:0 10px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="Batalkan Pilihan">
            <i class="bi bi-x-circle-fill"></i> Batal
          </button>
        </div>
      </div>

      {{-- Toolbar & Filter --}}
      <div class="rfid-table-toolbar" style="padding:8px 12px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <div class="rfid-table-title" style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:6px;">
          <i class="bi {{ $tab === 'siswa' ? 'bi-people-fill' : 'bi-person-badge-fill' }}" style="color:#000000;"></i>
          <span>Daftar Kartu RFID {{ $tab === 'siswa' ? 'Siswa' : 'Guru & Pegawai' }}</span>
        </div>

        <form method="GET" action="{{ route('rfid.index') }}" class="rfid-table-form" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; flex:1; justify-content:flex-end; max-width:740px;">
          <input type="hidden" name="tab" value="{{ $tab }}" />

          {{-- Search --}}
          <div class="rfid-search-box" style="position:relative; flex:1.5; min-width:130px;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px;"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="{{ $tab === 'siswa' ? 'Cari UID, nama, NISN...' : 'Cari UID, nama, NIP...' }}" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding-left:28px; padding-right:8px;" />
          </div>

          {{-- Filter Dropdowns & Cari Button Group --}}
          <div class="rfid-filter-group" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
            @if($tab === 'siswa')
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
            @else
              <div style="min-width:110px; flex:1;">
                <select name="kepegawaian" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
                  <option value="">Semua Kepegawaian</option>
                  <option value="PNS" {{ ($kepegawaian ?? '') === 'PNS' ? 'selected' : '' }}>PNS</option>
                  <option value="PPPK" {{ ($kepegawaian ?? '') === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                  <option value="Honorer" {{ ($kepegawaian ?? '') === 'Honorer' ? 'selected' : '' }}>Honorer</option>
                  <option value="GTT" {{ ($kepegawaian ?? '') === 'GTT' ? 'selected' : '' }}>GTT</option>
                  <option value="PTT" {{ ($kepegawaian ?? '') === 'PTT' ? 'selected' : '' }}>PTT</option>
                </select>
              </div>
            @endif

            {{-- Dropdown Urutan / Sort By --}}
            <div style="min-width:130px; flex:1;">
              <select name="sort" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px; font-weight:700;" onchange="this.form.submit()" title="Urutkan Data Kartu">
                <option value="nama_asc" {{ ($sort ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                <option value="nama_desc" {{ ($sort ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                <option value="terbaru" {{ in_array($sort ?? '', ['terbaru', 'terakhir_input', 'created_desc']) ? 'selected' : '' }}>Terakhir Diinput (Terbaru)</option>
                <option value="terlama" {{ in_array($sort ?? '', ['terlama', 'created_asc']) ? 'selected' : '' }}>Pertama Diinput (Terlama)</option>
                @if($tab === 'siswa')
                  <option value="nisn_asc" {{ ($sort ?? '') === 'nisn_asc' ? 'selected' : '' }}>NISN (Urut Naik)</option>
                @else
                  <option value="nip_asc" {{ ($sort ?? '') === 'nip_asc' ? 'selected' : '' }}>NIP (Urut Naik)</option>
                @endif
              </select>
            </div>

            <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; border-radius:var(--r-sm); flex-shrink:0;">
              Cari
            </button>

            @if($search || !empty($rombelId) || !empty($kepegawaian) || (!empty($sort) && $sort !== 'nama_asc'))
              <a href="{{ route('rfid.index', ['tab' => $tab]) }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); border-radius:var(--r-sm); flex-shrink:0;" title="Reset Filter &amp; Urutan">
                Reset
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- Table --}}
      <div class="table-responsive" style="overflow-x:auto; -webkit-overflow-scrolling:touch; width:100%; display:block;">
        <table class="data-table" style="width:100%; min-width:860px; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="width:38px; text-align:center; padding:8px 6px; white-space:nowrap;">
                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#000000; vertical-align:middle;" title="Pilih Semua di Halaman Ini" />
              </th>
              <th style="width:40px; text-align:center; white-space:nowrap;">No</th>
              <th style="min-width:150px; white-space:nowrap;">Kode Scan (Barcode &amp; RFID)</th>
              @if($tab === 'siswa')
                <th style="min-width:200px; white-space:nowrap;">Siswa (Nama &amp; NISN)</th>
                <th style="min-width:160px; white-space:nowrap;">Rombel &amp; Jurusan</th>
                <th style="min-width:150px; white-space:nowrap;">Kontak Orang Tua</th>
              @else
                <th style="min-width:200px; white-space:nowrap;">Guru &amp; Staf (Nama &amp; NIP)</th>
                <th style="min-width:160px; white-space:nowrap;">Jabatan</th>
                <th style="min-width:140px; white-space:nowrap;">Kepegawaian</th>
              @endif
              <th style="text-align:center; width:120px; white-space:nowrap;">Tipe Kredensial</th>
              <th style="width:140px; white-space:nowrap;">Status Kartu</th>
              <th style="width:100px; text-align:center; white-space:nowrap;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($kartus as $idx => $item)
              @php
                $kartuRfid = $item->kartuRfid;
                $hasRfid = $kartuRfid && $kartuRfid->status === 'aktif';
                if ($tab === 'siswa') {
                    $s = $item;
                    $nama = $s->nama;
                    $foto = $s->foto_url;
                    $identitas = 'NISN: ' . ($s->nisn ?: '-');
                    $rombelNama = $s->siswaRombels?->first()?->rombel?->nama_rombel ?? 'Tanpa Rombel';
                    $jurusanNama = $s->siswaRombels?->first()?->rombel?->jurusan?->nama_jurusan ?? '';
                    $codeValue = $kartuRfid?->uid ?? ($s->nisn ?: $s->nis ?: 'SISWA-'.$s->id);
                    $ownerId = $s->id;
                    $noHp = $s->no_hp ?: $s->no_hp_ortu;
                } else {
                    $g = $item;
                    $nama = $g->nama;
                    $foto = $g->foto_url;
                    $identitas = $g->nip ? 'NIP: ' . $g->nip : ($g->label_kepegawaian ?? 'Guru');
                    $jabatan = $g->jabatan ?? 'Guru / Staf';
                    $kepegawaian = $g->label_kepegawaian ?? 'Non-PNS';
                    $codeValue = $kartuRfid?->uid ?? ($g->nip ?: 'GURU-'.$g->id);
                    $ownerId = $g->id;
                    $noHp = $g->no_hp;
                }
              @endphp
              <tr id="row-card-{{ $ownerId }}">
                <td style="text-align:center; vertical-align:middle; padding:8px 6px; white-space:nowrap;">
                  <input type="checkbox" class="card-select-row" value="{{ $ownerId }}" data-nama="{{ $nama }}" onchange="handleRowSelectChange(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#000000; vertical-align:middle;" />
                </td>
                <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12px; vertical-align:middle; white-space:nowrap;">
                  {{ $kartus->firstItem() + $idx }}
                </td>

                {{-- Kode Scan (Barcode / RFID) --}}
                <td style="vertical-align:middle; white-space:nowrap;">
                  <div style="font-family:var(--font-mono); font-size:13px; font-weight:900; color:var(--text); letter-spacing:.04em; display:flex; align-items:center; gap:6px;">
                    @if($hasRfid)
                      <i class="bi bi-broadcast" style="color:#16a34a; font-size:13px;" title="Terpasang RFID Physical"></i>
                    @else
                      <i class="bi bi-qr-code-scan" style="color:var(--text-2); font-size:13px;" title="Barcode / QR Code NISN"></i>
                    @endif
                    <span>{{ $codeValue }}</span>
                  </div>
                </td>

                @if($tab === 'siswa')
                  {{-- Siswa --}}
                  <td style="vertical-align:middle; white-space:nowrap;">
                    <div style="display:flex; align-items:center; gap:10px;">
                      <img src="{{ $foto }}" alt="{{ $nama }}" style="width:34px; height:34px; border-radius:8px; object-fit:cover; border:1px solid var(--border-2); flex-shrink:0;" />
                      <div style="min-width:0;">
                        <strong style="font-size:13px; color:var(--text); display:block; line-height:1.25;">{{ $nama }}</strong>
                        <div style="font-size:11px; font-family:var(--font-mono); color:var(--text-3); margin-top:2px;">
                          NISN: <strong style="color:var(--text);">{{ $s->nisn ?: '-' }}</strong>
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- Rombel & Jurusan --}}
                  <td style="vertical-align:middle; white-space:nowrap;">
                    <div style="font-weight:700; font-size:12.5px; color:var(--text);">{{ $rombelNama }}</div>
                    @if($jurusanNama)
                      <div style="font-size:11px; color:var(--text-3); margin-top:1px;">{{ $jurusanNama }}</div>
                    @endif
                  </td>

                  {{-- Kontak Ortu --}}
                  <td style="vertical-align:middle; white-space:nowrap;">
                    @if($s->no_hp_ortu)
                      <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $s->no_hp_ortu)) }}" target="_blank" style="font-size:11.5px; font-family:var(--font-mono); color:var(--text); text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="Chat WhatsApp Ortu">
                        <i class="bi bi-whatsapp" style="font-size:11px; color:#25D366;"></i> {{ $s->no_hp_ortu }}
                      </a>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                @else
                  {{-- Guru --}}
                  <td style="vertical-align:middle; white-space:nowrap;">
                    <div style="display:flex; align-items:center; gap:10px;">
                      <img src="{{ $foto }}" alt="{{ $nama }}" style="width:34px; height:34px; border-radius:8px; object-fit:cover; border:1px solid var(--border-2); flex-shrink:0;" />
                      <div style="min-width:0;">
                        <strong style="font-size:13px; color:var(--text); display:block; line-height:1.25;">{{ $nama }}</strong>
                        <div style="font-size:11px; font-family:var(--font-mono); color:var(--text-3); margin-top:2px;">
                          {{ $g->nip ? 'NIP: ' . $g->nip : 'Non-NIP' }}
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- Jabatan --}}
                  <td style="vertical-align:middle; white-space:nowrap;">
                    <div style="font-weight:700; font-size:12.5px; color:var(--text);">{{ $jabatan }}</div>
                  </td>

                  {{-- Kepegawaian --}}
                  <td style="vertical-align:middle; white-space:nowrap;">
                    <span style="font-size:11px; font-weight:700; color:var(--text-2); text-transform:uppercase;">
                      {{ $kepegawaian }}
                    </span>
                  </td>
                @endif

                {{-- Tipe Kredensial --}}
                <td style="vertical-align:middle; text-align:center; white-space:nowrap;">
                  @if($hasRfid)
                    <span class="badge" style="background:rgba(34,197,94,0.1); color:#16a34a; border:1px solid rgba(34,197,94,0.3); font-weight:800; font-size:10.5px; display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px;">
                      <i class="bi bi-broadcast"></i> RFID &amp; QR
                    </span>
                  @else
                    <span class="badge" style="background:rgba(0,0,0,0.05); color:var(--text-2); border:1px solid var(--border-2); font-weight:700; font-size:10.5px; display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px;">
                      <i class="bi bi-qr-code-scan"></i> Barcode NISN
                    </span>
                  @endif
                </td>

                {{-- Waktu --}}
                <td style="vertical-align:middle; font-size:11.5px; color:var(--text-2); font-family:var(--font-mono); white-space:nowrap;">
                  {{ $hasRfid ? ($kartuRfid->created_at ? $kartuRfid->created_at->format('d/m/Y H:i') : 'Terpasang') : 'Aktif (Bawaan)' }}
                </td>

                {{-- Aksi --}}
                <td style="vertical-align:middle; text-align:center; white-space:nowrap;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                    {{-- Tombol Lihat Barcode Personal --}}
                    <button type="button"
                      onclick="previewPersonalBarcode('{{ $tab }}', '{{ addslashes($nama) }}', '{{ addslashes($identitas) }}', '{{ addslashes($tab === 'siswa' ? ($rombelNama.($jurusanNama ? ' · '.$jurusanNama : '')) : ($jabatan.' · '.$kepegawaian)) }}', '{{ addslashes($foto) }}', '{{ $codeValue }}', '{{ $ownerId }}', '{{ $noHp }}')"
                      class="btn-icon btn-icon-view" style="width:28px; height:28px;" title="Lihat Barcode &amp; QR Code Personal">
                      <i class="bi bi-qr-code-scan"></i>
                    </button>

                    @if($isAdmin || $isStafTu)
                      <button type="button"
                        onclick="openRfidPairModal('{{ $tab }}', {{ $ownerId }}, '{{ addslashes($nama) }}', '{{ addslashes($identitas) }}', '{{ addslashes($foto) }}', '{{ $kartuRfid?->uid ?? '' }}')"
                        class="btn-icon btn-icon-edit" style="width:28px; height:28px;" title="{{ $hasRfid ? 'Ubah Kartu RFID' : 'Pasang Kartu RFID Baru' }}">
                        <i class="bi {{ $hasRfid ? 'bi-pencil-square' : 'bi-plus-circle' }}"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; padding:40px 12px; color:var(--text-3);">
                  <i class="bi bi-credit-card-2-front" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.35;"></i>
                  <div style="font-weight:700; font-size:13.5px; color:var(--text);">Belum ada data kartu {{ $tab === 'siswa' ? 'siswa' : 'guru' }} yang cocok</div>
                  <p style="font-size:12px; margin-top:3px;">Coba gunakan kata kunci pencarian lain atau klik tombol Reset.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($kartus->hasPages())
        <div style="padding:12px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div style="font-size:12px; color:var(--text-3); font-weight:600;">
            Menampilkan {{ $kartus->firstItem() }} &ndash; {{ $kartus->lastItem() }} dari {{ $kartus->total() }} kartu
          </div>
          <div>
            {{ $kartus->links('partials.pagination') }}
          </div>
        </div>
      @endif

    </div>

  </main>
</div>

{{-- MODAL TAMBAH KARTU BARU (PILIH SISWA/GURU SEARCHABLE) --}}
@if($isAdmin || $isStafTu)
<div id="modalTambahKartuWrap" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
  <div class="panel" style="max-width:480px; width:92%; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); padding:20px 24px; box-shadow:0 20px 50px rgba(0,0,0,0.5); position:relative; max-height:90vh; overflow-y:auto;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:14px;">
      <div style="font-weight:900; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
        <i class="bi bi-plus-circle-fill" style="color:#000000; font-size:18px;"></i>
        <span>Pasang Kartu RFID Baru</span>
      </div>
      <button type="button" onclick="closeModalTambahKartu()" style="background:transparent; border:none; color:var(--text-3); font-size:18px; cursor:pointer;" title="Tutup Modal">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <form onsubmit="submitTambahKartuBaru(event)">
      <div style="margin-bottom:12px;">
        <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">Tipe Pemilik:</label>
        <select id="tambah_tipe" class="input-field" style="width:100%; height:36px; font-size:12.5px;" onchange="toggleSelectPemilik(this.value)">
          <option value="siswa">Peserta Didik (Siswa)</option>
          <option value="guru">Pendidik &amp; Tenaga Kependidikan (Guru/Staf)</option>
        </select>
      </div>

      <input type="hidden" id="tambah_siswa_id" value="" />
      <input type="hidden" id="tambah_guru_id" value="" />

      {{-- Search & Select Person Box --}}
      <div style="margin-bottom:14px;">
        <label id="labelTambahPerson" style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">
          Pilih Siswa:
        </label>

        {{-- Banner Pemilik Terpilih --}}
        <div id="selectedPersonBanner" style="display:none; align-items:center; justify-content:space-between; background:var(--surface); border:1.5px solid #000000; border-radius:8px; padding:8px 12px; margin-bottom:6px;">
          <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
            <img id="bannerFoto" src="/img/user-default.png" style="width:32px; height:32px; border-radius:6px; object-fit:cover; border:1px solid rgba(0,0,0,0.15); flex-shrink:0;" />
            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              <strong id="bannerNama" style="color:var(--text); font-size:13px; display:block; line-height:1.2;"></strong>
              <span id="bannerMeta" style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);"></span>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline" onclick="resetPersonSelection()" style="height:26px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); flex-shrink:0;">
            Ganti
          </button>
        </div>

        {{-- Kotak Pencarian & Daftar Pilihan --}}
        <div id="searchPersonArea">
          <div style="position:relative; margin-bottom:6px;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:12px;"></i>
            <input type="text" id="tambahSearchInput" placeholder="Ketik nama, NIS, atau rombel..." oninput="filterModalPersonList(this.value)" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
              style="width:100%; height:34px; padding-left:32px; padding-right:10px; font-size:12px; border-radius:var(--r-sm); background:var(--bg-3); border:1px solid var(--border-2); color:var(--text); outline:none;" />
          </div>

          <div id="tambahPersonListContainer" style="max-height:180px; overflow-y:auto; border:1px solid var(--border-2); border-radius:var(--r-sm); background:var(--bg-2);">
            {{-- Siswa List --}}
            @foreach($allSiswas as $s)
              @php $rNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Tanpa Rombel'; @endphp
              <div class="person-select-item item-siswa"
                   data-id="{{ $s->id }}"
                   data-nama="{{ strtolower($s->nama) }}"
                   data-nisn="{{ strtolower($s->nisn ?? '') }}"
                   data-rombel="{{ strtolower($rNama) }}"
                   onclick="selectModalPerson('siswa', '{{ $s->id }}', '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }} &bull; {{ $rNama }}', '{{ $s->foto_url }}')">
                <div style="width:28px; height:28px; border-radius:6px; background:rgba(0,0,0,0.06); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:var(--text); flex-shrink:0;">
                  {{ strtoupper(substr($s->nama, 0, 1)) }}
                </div>
                <div style="min-width:0; flex:1;">
                  <strong style="font-size:12.5px; color:var(--text); display:block; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $s->nama }}</strong>
                  <span style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px; display:block;">NISN: {{ $s->nisn ?: '-' }} &bull; {{ $rNama }}</span>
                </div>
                <i class="bi bi-chevron-right" style="color:var(--text-3); font-size:11px; opacity:0.6; flex-shrink:0;"></i>
              </div>
            @endforeach

            {{-- Guru List --}}
            @foreach($allGurus as $g)
              <div class="person-select-item item-guru"
                   style="display:none;"
                   data-id="{{ $g->id }}"
                   data-nama="{{ strtolower($g->nama) }}"
                   data-nip="{{ strtolower($g->nip ?? '') }}"
                   data-jabatan="{{ strtolower($g->jabatan ?? '') }}"
                   onclick="selectModalPerson('guru', '{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: '.$g->nip : $g->label_kepegawaian }} &bull; {{ $g->jabatan }}', '{{ $g->foto_url }}')">
                <div style="width:28px; height:28px; border-radius:6px; background:rgba(0,0,0,0.06); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:var(--text); flex-shrink:0;">
                  {{ strtoupper(substr($g->nama, 0, 1)) }}
                </div>
                <div style="min-width:0; flex:1;">
                  <strong style="font-size:12.5px; color:var(--text); display:block; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $g->nama }}</strong>
                  <span style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px; display:block;">{{ $g->nip ? 'NIP: '.$g->nip : $g->label_kepegawaian }} &bull; {{ $g->jabatan }}</span>
                </div>
                <i class="bi bi-chevron-right" style="color:var(--text-3); font-size:11px; opacity:0.6; flex-shrink:0;"></i>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">Kode UID Kartu RFID:</label>
        <input type="text" id="tambah_uid" placeholder="Tempelkan kartu atau ketik UID..." required
          autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
          style="width:100%; height:40px; border-radius:var(--r-sm); border:1.5px solid #000000; background:var(--bg-3); color:var(--text); font-family:var(--font-mono); font-size:14px; font-weight:700; padding:0 12px; letter-spacing:.05em; outline:none; text-align:center;" />
        <small style="color:var(--text-3); font-size:11px; margin-top:4px; display:block;">
          <i class="bi bi-info-circle"></i> Tempelkan kartu pada USB scanner.
        </small>
      </div>

      <div id="tambahKartuAlert" style="display:none; padding:10px; border-radius:var(--r-sm); font-size:12px; font-weight:700; margin-bottom:14px;"></div>

      <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" onclick="closeModalTambahKartu()" class="btn btn-outline">Batal</button>
        <button type="submit" id="btnSubmitTambahKartu" class="btn btn-gold"><i class="bi bi-check2-circle"></i> Pasangkan Kartu</button>
      </div>
    </form>

  </div>
</div>

{{-- MODAL BROADCAST WA BARCODE --}}
<div id="modalBroadcastWaWrap" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
  <div class="panel" style="max-width:440px; width:92%; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); padding:20px 24px; box-shadow:0 20px 50px rgba(0,0,0,0.5); position:relative;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:14px;">
      <div style="font-weight:900; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
        <i class="bi bi-whatsapp" style="color:#22c55e; font-size:18px;"></i>
        <span>Broadcast Barcode Presensi ke WA</span>
      </div>
      <button type="button" onclick="closeModalBroadcastWa()" style="background:transparent; border:none; color:var(--text-3); font-size:18px; cursor:pointer;" title="Tutup Modal">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <form action="{{ route('rfid.broadcast.wa') }}" method="POST" onsubmit="return confirm('Kirimkan broadcast WhatsApp berisi barcode presensi ke kontak sasaran?')">
      @csrf
      <div style="margin-bottom:12px;">
        <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">Sasaran Penerima:</label>
        <select name="tab" id="wa_broadcast_tab" class="input-field" style="width:100%; height:36px; font-size:12.5px;" onchange="toggleWaTarget(this.value)">
          <optgroup label="Broadcast Massal">
            <option value="siswa">Seluruh Siswa (Nomor HP Siswa)</option>
            <option value="ortu">Seluruh Orang Tua (Nomor HP Ortu)</option>
            <option value="guru">Seluruh Guru &amp; Pegawai</option>
          </optgroup>
          <optgroup label="Kirim Satuan (Individu)">
            <option value="individu_siswa">1 Siswa Tertentu (Kirim ke HP Siswa)</option>
            <option value="individu_ortu">1 Orang Tua Tertentu (Kirim ke HP Ortu)</option>
            <option value="individu_guru">1 Guru / Pegawai Tertentu</option>
          </optgroup>
        </select>
      </div>

      {{-- Filter Rombel (Khusus Broadcast Siswa/Ortu) --}}
      <div id="waRombelFilterGroup" style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">Pilih Rombel / Kelas:</label>
        <select name="rombel_id" class="input-field" style="width:100%; height:36px; font-size:12.5px;">
          <option value="">-- Kirim ke Semua Kelas (Seluruh Siswa) --</option>
          @foreach($rombels as $r)
            <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
          @endforeach
        </select>
      </div>

      {{-- Pilih Siswa Individu via Live Search --}}
      <div id="waIndividuSiswaGroup" style="margin-bottom:14px; display:none;">
        <label id="waLabelSiswaTarget" style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">
          Cari &amp; Pilih Siswa:
        </label>
        <input type="hidden" name="target_siswa_id" id="wa_target_siswa_id" />

        {{-- Banner Siswa Terpilih --}}
        <div id="waSelectedSiswaBanner" style="display:none; align-items:center; justify-content:space-between; background:var(--surface); border:1.5px solid #000000; border-radius:8px; padding:8px 12px; margin-bottom:6px;">
          <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
            <img id="waBannerSiswaFoto" src="/img/user-default.png" style="width:32px; height:32px; border-radius:6px; object-fit:cover; border:1px solid rgba(0,0,0,0.15); flex-shrink:0;" />
            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              <strong id="waBannerSiswaNama" style="color:var(--text); font-size:13px; display:block; line-height:1.2;"></strong>
              <span id="waBannerSiswaMeta" style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);"></span>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline" onclick="resetWaSiswaSelection()" style="height:26px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); flex-shrink:0;">
            Ganti
          </button>
        </div>

        {{-- Area Pencarian Siswa --}}
        <div id="waSiswaSearchArea">
          <div style="position:relative; margin-bottom:6px;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:12px;"></i>
            <input type="text" id="waSiswaSearchInput" placeholder="Ketik nama, NISN, atau kelas..." oninput="filterWaSiswaList(this.value)" autocomplete="off" spellcheck="false"
              style="width:100%; height:34px; padding-left:32px; padding-right:10px; font-size:12px; border-radius:var(--r-sm); background:var(--bg-3); border:1px solid var(--border-2); color:var(--text); outline:none;" />
          </div>

          <div id="waSiswaListContainer" style="max-height:160px; overflow-y:auto; border:1px solid var(--border-2); border-radius:var(--r-sm); background:var(--bg-2);">
            @foreach($allSiswas as $s)
              @php
                $rNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Tanpa Rombel';
                $hpSiswa = $s->no_hp_siswa ?: '-';
                $hpOrtu = $s->no_hp_ortu ?: '-';
              @endphp
              <div class="person-select-item wa-item-siswa"
                   data-id="{{ $s->id }}"
                   data-nama="{{ strtolower($s->nama) }}"
                   data-nisn="{{ strtolower($s->nisn ?? '') }}"
                   data-rombel="{{ strtolower($rNama) }}"
                   onclick="selectWaSiswa('{{ $s->id }}', '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }} · {{ $rNama }} · HP: {{ $s->no_hp_siswa ?: '(kosong)' }} / Ortu: {{ $s->no_hp_ortu ?: '(kosong)' }}', '{{ $s->foto_url }}')">
                <div style="width:28px; height:28px; border-radius:6px; background:rgba(0,0,0,0.06); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:var(--text); flex-shrink:0;">
                  {{ strtoupper(substr($s->nama, 0, 1)) }}
                </div>
                <div style="min-width:0; flex:1;">
                  <strong style="font-size:12.5px; color:var(--text); display:block; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $s->nama }}</strong>
                  <span style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px; display:block;">
                    NISN: {{ $s->nisn ?: '-' }} &bull; {{ $rNama }} &bull; HP Siswa: {{ $hpSiswa }} &bull; Ortu: {{ $hpOrtu }}
                  </span>
                </div>
                <i class="bi bi-chevron-right" style="color:var(--text-3); font-size:11px; opacity:0.6; flex-shrink:0;"></i>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Pilih Guru Individu via Live Search --}}
      <div id="waIndividuGuruGroup" style="margin-bottom:14px; display:none;">
        <label style="display:block; font-size:12px; font-weight:800; color:var(--text); margin-bottom:5px;">
          Cari &amp; Pilih Guru / Staf:
        </label>
        <input type="hidden" name="target_guru_id" id="wa_target_guru_id" />

        {{-- Banner Guru Terpilih --}}
        <div id="waSelectedGuruBanner" style="display:none; align-items:center; justify-content:space-between; background:var(--surface); border:1.5px solid #000000; border-radius:8px; padding:8px 12px; margin-bottom:6px;">
          <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
            <img id="waBannerGuruFoto" src="/img/user-default.png" style="width:32px; height:32px; border-radius:6px; object-fit:cover; border:1px solid rgba(0,0,0,0.15); flex-shrink:0;" />
            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              <strong id="waBannerGuruNama" style="color:var(--text); font-size:13px; display:block; line-height:1.2;"></strong>
              <span id="waBannerGuruMeta" style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);"></span>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline" onclick="resetWaGuruSelection()" style="height:26px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); flex-shrink:0;">
            Ganti
          </button>
        </div>

        {{-- Area Pencarian Guru --}}
        <div id="waGuruSearchArea">
          <div style="position:relative; margin-bottom:6px;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:12px;"></i>
            <input type="text" id="waGuruSearchInput" placeholder="Ketik nama, NIP, atau jabatan..." oninput="filterWaGuruList(this.value)" autocomplete="off" spellcheck="false"
              style="width:100%; height:34px; padding-left:32px; padding-right:10px; font-size:12px; border-radius:var(--r-sm); background:var(--bg-3); border:1px solid var(--border-2); color:var(--text); outline:none;" />
          </div>

          <div id="waGuruListContainer" style="max-height:160px; overflow-y:auto; border:1px solid var(--border-2); border-radius:var(--r-sm); background:var(--bg-2);">
            @foreach($allGurus as $g)
              <div class="person-select-item wa-item-guru"
                   data-id="{{ $g->id }}"
                   data-nama="{{ strtolower($g->nama) }}"
                   data-nip="{{ strtolower($g->nip ?? '') }}"
                   data-jabatan="{{ strtolower($g->jabatan ?? '') }}"
                   onclick="selectWaGuru('{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: '.$g->nip : $g->label_kepegawaian }} · {{ $g->jabatan }} · HP: {{ $g->no_hp ?: '(kosong)' }}', '{{ $g->foto_url }}')">
                <div style="width:28px; height:28px; border-radius:6px; background:rgba(0,0,0,0.06); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:var(--text); flex-shrink:0;">
                  {{ strtoupper(substr($g->nama, 0, 1)) }}
                </div>
                <div style="min-width:0; flex:1;">
                  <strong style="font-size:12.5px; color:var(--text); display:block; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $g->nama }}</strong>
                  <span style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px; display:block;">
                    {{ $g->nip ? 'NIP: '.$g->nip : $g->label_kepegawaian }} &bull; {{ $g->jabatan }} &bull; HP: {{ $g->no_hp ?: '-' }}
                  </span>
                </div>
                <i class="bi bi-chevron-right" style="color:var(--text-3); font-size:11px; opacity:0.6; flex-shrink:0;"></i>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div style="background:var(--surface); border:1px solid var(--border-2); border-radius:8px; padding:10px 12px; font-size:11.5px; color:var(--text-2); margin-bottom:16px; line-height:1.4;">
        <strong style="color:var(--text); display:block; margin-bottom:2px;">
          <i class="bi bi-info-circle-fill" style="color:var(--primary);"></i> Informasi Pengiriman Aman:
        </strong>
        Pesan dikirim berurutan dengan <strong>jeda aman 5–7 detik antar nomor</strong> untuk melindungi nomor WA dari pemblokiran spam. Setiap penerima akan mendapatkan pesan personal berisi <strong>Nama, NISN/NIP, Link Kartu Digital</strong>, dan <strong>Link Gambar QR Code</strong>.
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" onclick="closeModalBroadcastWa()" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-gold">
          <i class="bi bi-send-fill"></i> Kirim Broadcast Sekarang
        </button>
      </div>
    </form>

  </div>
</div>
@endif

{{-- MODAL PREVIEW KARTU & QR CODE PERSONAL (PREMIUM DIGITAL PASS) --}}
<div id="modalBarcodePersonalWrap" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.8); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(6px); padding:16px;">
  <div class="panel" style="max-width:420px; width:100%; background:var(--surface); border:1px solid var(--border-2); border-radius:18px; padding:22px; box-shadow:0 25px 60px rgba(0,0,0,0.4); position:relative; text-align:center;">
    
    {{-- Header Modal --}}
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-2); padding-bottom:12px; margin-bottom:14px;">
      <div style="text-align:left;">
        <span id="prevBadgeKategori" style="font-size:9.5px; font-weight:900; text-transform:uppercase; letter-spacing:0.05em; display:block; color:#0f766e;">KARTU PRESENSI RESMI</span>
        <h3 style="font-size:15px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-qr-code-scan" style="color:var(--primary);"></i>
          <span>Kartu &amp; QR Presensi Digital</span>
        </h3>
      </div>
      <button type="button" onclick="closePersonalBarcode()" class="btn btn-sm btn-outline" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;" title="Tutup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    {{-- Profil Pass Card --}}
    <div id="prevPassCard" style="background:linear-gradient(135deg, #042f2e 0%, #0f766e 60%, #14b8a6 100%); border-radius:14px; padding:14px; color:#ffffff; margin-bottom:14px; box-shadow:0 6px 20px rgba(0,0,0,0.15); text-align:left; position:relative; overflow:hidden;">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; border-bottom:1px solid rgba(255,255,255,0.2); padding-bottom:6px;">
        <div style="display:flex; align-items:center; gap:6px;">
          <img src="{{ !empty($sekolah->logo_sekolah) ? asset('storage/'.$sekolah->logo_sekolah) : '/img/logo.png' }}" alt="Logo" style="width:20px; height:20px; object-fit:contain;" onerror="this.src='/img/logo.png';" />
          <span style="font-size:9.5px; font-weight:900; letter-spacing:0.03em; text-transform:uppercase;">{{ $sekolah->nama_sekolah ?? 'SMKN 1 AIR NANINGAN' }}</span>
        </div>
        <span id="prevRolePill" style="font-size:8.5px; font-weight:800; background:#f0fdfa; color:#0f766e; padding:2px 7px; border-radius:8px;">SISWA</span>
      </div>

      <div style="display:flex; gap:12px; align-items:center;">
        <img id="prevFoto" src="/img/user-default.png" style="width:46px; height:46px; border-radius:8px; object-fit:cover; border:1.5px solid rgba(255,255,255,0.6); background:#ffffff; flex-shrink:0;" onerror="this.src='/img/user-default.png'" />
        <div style="min-width:0; flex:1;">
          <div id="prevNama" style="font-size:13.5px; font-weight:900; color:#ffffff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></div>
          <div id="prevIdentitas" style="font-size:11px; font-family:var(--font-mono); color:#e0f2fe; margin-top:2px;"></div>
          <div id="prevMeta" style="font-size:11px; color:#ffffff; opacity:0.9; margin-top:1px;"></div>
        </div>
      </div>
    </div>

    {{-- QR Code Scanner Box --}}
    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:14px; padding:14px; margin-bottom:14px; text-align:center;">
      <div style="font-size:10.5px; font-weight:800; color:var(--text-3); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">
        <i class="bi bi-broadcast-pin"></i> SCANNER GERBANG &amp; KIOSK
      </div>
      
      <div style="background:#ffffff; padding:10px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 4px 14px rgba(0,0,0,0.06); display:inline-flex; align-items:center; justify-content:center;">
        <div id="prevQrBox" style="width:145px; height:145px; display:flex; align-items:center; justify-content:center;"></div>
      </div>

      <div id="prevCodeLabel" style="font-family:var(--font-mono); font-size:13px; font-weight:900; color:var(--text); letter-spacing:.04em; margin-top:8px;"></div>
    </div>

    {{-- Action Buttons --}}
    <div style="display:flex; flex-direction:column; gap:8px;">
      {{-- Tombol 1: Kirim Otomatis via Server WA Gateway --}}
      <button type="button" id="btnKirimWaGateway" onclick="kirimWaGatewayPersonal()" class="btn btn-gold" style="width:100%; font-size:12.5px; font-weight:800; padding:10px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px; cursor:pointer;">
        <i class="bi bi-send-fill"></i> Kirim via WhatsApp Gateway
      </button>

      {{-- Tombol 2 & 3: Buka Link HP & Simpan QR --}}
      <div style="display:flex; gap:8px;">
        <a id="btnBukaLinkHp" href="#" target="_blank" class="btn btn-outline" style="flex:1; font-weight:700; font-size:12px; padding:9px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none;">
          <i class="bi bi-phone"></i> Mode HP
        </a>
        <button type="button" onclick="downloadQrPersonalModal()" class="btn btn-outline" style="flex:1; font-weight:700; font-size:12px; padding:9px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
          <i class="bi bi-download"></i> Simpan QR
        </button>
      </div>
    </div>

  </div>
</div>

@include('partials.rfid_pair_modal')

<script src="/jsbarcode.min.js" defer></script>
<script src="/qrcode.min.js" defer></script>

<script>
  let currentPersonalTarget = null;
  let currentPersonalNama = '';

  function previewPersonalBarcode(type, nama, identitas, meta, foto, codeVal, refId, noHp) {
    currentPersonalTarget = { type: type, id: refId };
    currentPersonalNama = nama;

    const isSiswa = (type === 'siswa');
    const passCard = document.getElementById('prevPassCard');
    const rolePill = document.getElementById('prevRolePill');
    const badgeKategori = document.getElementById('prevBadgeKategori');

    if (passCard && rolePill) {
      if (isSiswa) {
        passCard.style.background = 'linear-gradient(135deg, #042f2e 0%, #0f766e 60%, #14b8a6 100%)';
        rolePill.style.background = '#f0fdfa';
        rolePill.style.color = '#0f766e';
        rolePill.textContent = 'SISWA';
        if (badgeKategori) {
          badgeKategori.style.color = '#0f766e';
          badgeKategori.textContent = 'KARTU PRESENSI SISWA';
        }
      } else {
        passCard.style.background = 'linear-gradient(135deg, #0f2744 0%, #1d4ed8 60%, #0284c7 100%)';
        rolePill.style.background = '#f0f9ff';
        rolePill.style.color = '#0284c7';
        rolePill.textContent = 'GURU & STAF';
        if (badgeKategori) {
          badgeKategori.style.color = '#0284c7';
          badgeKategori.textContent = 'KARTU PRESENSI GURU & STAF';
        }
      }
    }

    document.getElementById('prevNama').textContent = nama;
    document.getElementById('prevIdentitas').textContent = identitas;
    document.getElementById('prevMeta').textContent = meta;
    document.getElementById('prevFoto').src = foto || '/img/user-default.png';
    document.getElementById('prevCodeLabel').textContent = codeVal;

    // Render 2D QR Code
    const qrBox = document.getElementById('prevQrBox');
    qrBox.innerHTML = '';
    new QRCode(qrBox, {
      text: codeVal,
      width: 145,
      height: 145,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.M
    });

    // Setup Links
    const urlHp = (type === 'siswa') ? `/kartu-digital/${refId}` : `/kartu-digital-guru/${refId}`;
    document.getElementById('btnBukaLinkHp').href = urlHp;

    const btnWa = document.getElementById('btnKirimWaDirect');
    const btnGateway = document.getElementById('btnKirimWaGateway');

    if (noHp) {
      const cleanPhone = noHp.replace(/^0/, '62').replace(/[^0-9]/g, '');
      const waMsg = encodeURIComponent(`Halo ${nama},\nBerikut akses Kartu Presensi Digital Anda:\n${window.location.origin}${urlHp}\n\nTunjukkan QR Code pada scanner gerbang sekolah.`);
      if (btnWa) {
        btnWa.href = `https://wa.me/${cleanPhone}?text=${waMsg}`;
        btnWa.style.display = 'inline-flex';
      }
    } else {
      if (btnWa) {
        btnWa.style.display = 'none';
      }
    }
    if (btnGateway) {
      btnGateway.style.display = 'inline-flex';
    }

    const modal = document.getElementById('modalBarcodePersonalWrap');
    if (modal) {
      modal.classList.add('active');
      modal.style.display = 'flex';
      modal.style.opacity = '1';
    }
  }

  function downloadQrPersonalModal() {
    const wrap = document.getElementById('prevQrBox');
    const srcCanvas = wrap ? wrap.querySelector('canvas') : null;
    const srcImg    = wrap ? wrap.querySelector('img')    : null;
    const dlName    = 'QR_Presensi_' + (currentPersonalNama ? currentPersonalNama.replace(/[^A-Za-z0-9]/g, '_') : 'Personal') + '.png';

    if (srcCanvas) {
      const out = document.createElement('canvas');
      const pad = 24;
      out.width  = srcCanvas.width  + pad * 2;
      out.height = srcCanvas.height + pad * 2;
      const ctx = out.getContext('2d');
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, out.width, out.height);
      ctx.drawImage(srcCanvas, pad, pad);
      const link = document.createElement('a');
      link.href = out.toDataURL('image/png');
      link.download = dlName;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } else if (srcImg) {
      const out = document.createElement('canvas');
      const pad = 24;
      out.width  = srcImg.naturalWidth  + pad * 2;
      out.height = srcImg.naturalHeight + pad * 2;
      const ctx = out.getContext('2d');
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, out.width, out.height);
      const img2 = new Image();
      img2.crossOrigin = 'anonymous';
      img2.onload = function () {
        ctx.drawImage(img2, pad, pad);
        const link = document.createElement('a');
        link.href = out.toDataURL('image/png');
        link.download = dlName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      };
      img2.src = srcImg.src;
    } else {
      alert('QR Code sedang disiapkan, silakan coba 1 detik lagi.');
    }
  }

  async function kirimWaGatewayPersonal() {
    if (!currentPersonalTarget) return;
    const btn = document.getElementById('btnKirimWaGateway');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim via Gateway...';

    try {
      const res = await fetch('{{ route("rfid.kirim.wa.personal") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(currentPersonalTarget)
      });

      const json = await res.json();
      if (json.success) {
        alert('✅ ' + json.message);
      } else {
        alert('❌ Gagal: ' + (json.message || 'Terjadi kesalahan'));
      }
    } catch (e) {
      console.error(e);
      alert('❌ Terjadi kesalahan koneksi ke server.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = origHtml;
    }
  }

  function closePersonalBarcode() {
    const modal = document.getElementById('modalBarcodePersonalWrap');
    if (modal) {
      modal.classList.remove('active');
      modal.style.display = 'none';
    }
  }
  function openModalBroadcastWa() {
    const modal = document.getElementById('modalBroadcastWaWrap');
    if (modal) {
      modal.classList.add('active');
      modal.style.display = 'flex';
      modal.style.opacity = '1';
    }
  }

  function closeModalBroadcastWa() {
    const modal = document.getElementById('modalBroadcastWaWrap');
    if (modal) {
      modal.classList.remove('active');
      modal.style.display = 'none';
    }
  }

  function toggleWaTarget(val) {
    const grpRombel = document.getElementById('waRombelFilterGroup');
    const grpSiswa  = document.getElementById('waIndividuSiswaGroup');
    const grpGuru   = document.getElementById('waIndividuGuruGroup');

    if (grpRombel) grpRombel.style.display = (val === 'siswa' || val === 'ortu') ? 'block' : 'none';
    if (grpSiswa)  grpSiswa.style.display  = (val === 'individu_siswa' || val === 'individu_ortu') ? 'block' : 'none';
    if (grpGuru)   grpGuru.style.display   = (val === 'individu_guru') ? 'block' : 'none';

    // Update label siswa/ortu jika berubah
    const lblSiswa = document.getElementById('waLabelSiswaTarget');
    if (lblSiswa) {
      lblSiswa.textContent = (val === 'individu_ortu') ? 'Cari & Pilih Siswa (Kirim ke Nomor Ortu):' : 'Cari & Pilih Siswa (Kirim ke Nomor Siswa):';
    }
  }

  function selectWaSiswa(id, nama, meta, foto) {
    document.getElementById('wa_target_siswa_id').value = id;
    document.getElementById('waBannerSiswaNama').textContent = nama;
    document.getElementById('waBannerSiswaMeta').textContent = meta;
    document.getElementById('waBannerSiswaFoto').src = foto || '/img/user-default.png';
    document.getElementById('waSelectedSiswaBanner').style.display = 'flex';
    document.getElementById('waSiswaSearchArea').style.display = 'none';
  }

  function resetWaSiswaSelection() {
    document.getElementById('wa_target_siswa_id').value = '';
    document.getElementById('waSelectedSiswaBanner').style.display = 'none';
    document.getElementById('waSiswaSearchArea').style.display = 'block';
    const inp = document.getElementById('waSiswaSearchInput');
    if (inp) {
      inp.value = '';
      inp.focus();
    }
    filterWaSiswaList('');
  }

  function filterWaSiswaList(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('#waSiswaListContainer .wa-item-siswa').forEach(el => {
      const match = !q ||
        el.dataset.nama.includes(q) ||
        el.dataset.nisn.includes(q) ||
        el.dataset.rombel.includes(q);
      el.style.display = match ? 'flex' : 'none';
    });
  }

  function selectWaGuru(id, nama, meta, foto) {
    document.getElementById('wa_target_guru_id').value = id;
    document.getElementById('waBannerGuruNama').textContent = nama;
    document.getElementById('waBannerGuruMeta').textContent = meta;
    document.getElementById('waBannerGuruFoto').src = foto || '/img/user-default.png';
    document.getElementById('waSelectedGuruBanner').style.display = 'flex';
    document.getElementById('waGuruSearchArea').style.display = 'none';
  }

  function resetWaGuruSelection() {
    document.getElementById('wa_target_guru_id').value = '';
    document.getElementById('waSelectedGuruBanner').style.display = 'none';
    document.getElementById('waGuruSearchArea').style.display = 'block';
    const inp = document.getElementById('waGuruSearchInput');
    if (inp) {
      inp.value = '';
      inp.focus();
    }
    filterWaGuruList('');
  }

  function filterWaGuruList(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('#waGuruListContainer .wa-item-guru').forEach(el => {
      const match = !q ||
        el.dataset.nama.includes(q) ||
        el.dataset.nip.includes(q) ||
        el.dataset.jabatan.includes(q);
      el.style.display = match ? 'flex' : 'none';
    });
  }

  function openModalTambahKartu(defaultType = 'siswa') {
    const elType = document.getElementById('tambah_tipe');
    if (elType) {
      elType.value = defaultType;
      toggleSelectPemilik(defaultType);
    }
    resetPersonSelection();
    const modal = document.getElementById('modalTambahKartuWrap');
    if (modal) {
      modal.classList.add('active');
      modal.style.display = 'flex';
      modal.style.opacity = '1';
      setTimeout(() => {
        const inp = document.getElementById('tambahSearchInput');
        if (inp) inp.focus();
      }, 150);
    }
  }

  function closeModalTambahKartu() {
    const modal = document.getElementById('modalTambahKartuWrap');
    if (modal) {
      modal.classList.remove('active');
      modal.style.display = 'none';
    }
  }

  function toggleSelectPemilik(type) {
    const isSiswa = (type === 'siswa');
    document.getElementById('labelTambahPerson').textContent = isSiswa ? 'Pilih Siswa:' : 'Pilih Guru / Staf:';
    document.getElementById('tambahSearchInput').placeholder = isSiswa ? 'Ketik nama, NISN, atau rombel...' : 'Ketik nama, NIP, atau jabatan...';
    resetPersonSelection();

    // Toggle item visibility in list
    document.querySelectorAll('.item-siswa').forEach(el => el.style.display = isSiswa ? 'flex' : 'none');
    document.querySelectorAll('.item-guru').forEach(el => el.style.display = isSiswa ? 'none' : 'flex');
  }

  function filterModalPersonList(query) {
    const q = query.toLowerCase().trim();
    const type = document.getElementById('tambah_tipe').value;
    const items = document.querySelectorAll(type === 'siswa' ? '.item-siswa' : '.item-guru');

    items.forEach(el => {
      const nama = el.getAttribute('data-nama') || '';
      const nisn = el.getAttribute('data-nisn') || '';
      const nip = el.getAttribute('data-nip') || '';
      const rombel = el.getAttribute('data-rombel') || '';
      const jabatan = el.getAttribute('data-jabatan') || '';

      const match = !q || nama.includes(q) || nisn.includes(q) || nip.includes(q) || rombel.includes(q) || jabatan.includes(q);
      el.style.display = match ? 'flex' : 'none';
    });
  }

  function selectModalPerson(type, id, nama, meta, fotoUrl) {
    if (type === 'siswa') {
      document.getElementById('tambah_siswa_id').value = id;
      document.getElementById('tambah_guru_id').value = '';
    } else {
      document.getElementById('tambah_guru_id').value = id;
      document.getElementById('tambah_siswa_id').value = '';
    }

    document.getElementById('bannerNama').textContent = nama;
    document.getElementById('bannerMeta').innerHTML = meta;
    document.getElementById('bannerFoto').src = fotoUrl || '/img/user-default.png';

    document.getElementById('searchPersonArea').style.display = 'none';
    document.getElementById('selectedPersonBanner').style.display = 'flex';

    setTimeout(() => document.getElementById('tambah_uid').focus(), 100);
  }

  function resetPersonSelection() {
    document.getElementById('tambah_siswa_id').value = '';
    document.getElementById('tambah_guru_id').value = '';
    document.getElementById('searchPersonArea').style.display = 'block';
    document.getElementById('selectedPersonBanner').style.display = 'none';
    const searchInp = document.getElementById('tambahSearchInput');
    if (searchInp) {
      searchInp.value = '';
      filterModalPersonList('');
      searchInp.focus();
    }
  }

  async function submitTambahKartuBaru(e) {
    e.preventDefault();
    const type = document.getElementById('tambah_tipe').value;
    const pemilikId = (type === 'siswa') ? document.getElementById('tambah_siswa_id').value : document.getElementById('tambah_guru_id').value;
    const uid = document.getElementById('tambah_uid').value.trim();
    const alertBox = document.getElementById('tambahKartuAlert');
    const btn = document.getElementById('btnSubmitTambahKartu');

    if (!pemilikId) {
      alert(type === 'siswa' ? 'Silakan cari & pilih siswa terlebih dahulu.' : 'Silakan cari & pilih guru terlebih dahulu.');
      return;
    }
    if (!uid) return;

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    try {
      const res = await fetch('/api/v1/rfid-pair', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          uid: uid,
          pemilik_type: type,
          pemilik_id: pemilikId,
        })
      });

      const json = await res.json();
      if (json.success) {
        alertBox.style.display = 'block';
        alertBox.style.background = 'var(--green-dim)';
        alertBox.style.color = 'var(--green)';
        alertBox.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${json.message}`;
        setTimeout(() => window.location.reload(), 700);
      } else {
        alertBox.style.display = 'block';
        alertBox.style.background = 'var(--red-dim)';
        alertBox.style.color = 'var(--red)';
        alertBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> ${json.message || 'Gagal menyimpan kartu'}`;
      }
    } catch (err) {
      console.error(err);
      alertBox.style.display = 'block';
      alertBox.style.background = 'var(--red-dim)';
      alertBox.style.color = 'var(--red)';
      alertBox.textContent = 'Terjadi kesalahan jaringan.';
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check2-circle"></i> Pasangkan Kartu';
    }
  }

  // Live Tester Keystroke Buffer
  const testerInput = document.getElementById('testerInput');
  testerInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      testCardUid();
    }
  });

  async function testCardUid() {
    const val = testerInput.value.trim();
    if (!val) return;

    try {
      const res = await fetch('/api/v1/rfid-scan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ uid: val })
      });

      const json = await res.json();
      if (json.data && json.data.nama) {
        alert(`KARTU TERDAFTAR!\n\nNama: ${json.data.nama}\nIdentitas: ${json.data.identitas}\nKelas/Jabatan: ${json.data.rombel_atau_jabatan}\nStatus Kehadiran Hari Ini: ${json.data.status.toUpperCase()}`);
      } else {
        alert(`KARTU BELUM TERDAFTAR!\n\nUID (${val}) belum dipasangkan ke siswa atau guru manapun.`);
      }
      testerInput.value = '';
    } catch (err) {
      console.error(err);
    }
  }

  // ── SELECTION PERSISTENCE HELPER (SESSION STORAGE) ──
  const RFID_STORAGE_KEY = 'rfid_selected_' + '{{ $tab }}';

  function getStoredSelectedIds() {
    try {
      const stored = sessionStorage.getItem(RFID_STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (e) {
      return [];
    }
  }

  function setStoredSelectedIds(ids) {
    try {
      sessionStorage.setItem(RFID_STORAGE_KEY, JSON.stringify(ids));
    } catch (e) {
      console.error(e);
    }
  }

  function getSelectedCardIds() {
    return getStoredSelectedIds();
  }

  function toggleSelectAll(masterCb) {
    const checkboxes = document.querySelectorAll('.card-select-row');
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
    updateSelectedCardsUI();
  }

  function handleRowSelectChange(changedCb) {
    const allCbs = document.querySelectorAll('.card-select-row');
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

    updateSelectedCardsUI();
  }

  function syncCheckboxesFromStorage() {
    const stored = getStoredSelectedIds().map(v => String(v));
    const allCbs = document.querySelectorAll('.card-select-row');
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

    updateSelectedCardsUI();
  }

  function updateSelectedCardsUI() {
    const ids = getSelectedCardIds();
    const headerBar = document.getElementById('selectionHeaderBar');
    const countTextHeader = document.getElementById('selectedCountTextHeader');
    const countNums = document.querySelectorAll('.selectedCountNum');
    const topBadge = document.getElementById('topSelectedBadge');
    const labelType = '{{ $tab === "siswa" ? "Siswa" : "Guru" }}';

    if (ids.length > 0) {
      if (headerBar) headerBar.style.display = 'flex';
      if (countTextHeader) countTextHeader.innerText = ids.length + ' ' + labelType + ' Dipilih';
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
    sessionStorage.removeItem(RFID_STORAGE_KEY);
    const checkboxes = document.querySelectorAll('.card-select-row');
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
    updateSelectedCardsUI();
  }

  function submitCetakSelected(format = 'barcode') {
    const ids = getSelectedCardIds();
    if (ids.length === 0) {
      alert('Silakan pilih minimal 1 siswa / guru untuk dicetak.');
      return;
    }
    const url = '{{ route("rfid.cetak") }}?tab={{ $tab }}&ids=' + ids.join(',') + '&format=' + format;
    window.open(url, '_blank');
  }

  function handleTopCetakClick(anchor, event) {
    const ids = getSelectedCardIds();
    if (ids.length > 0) {
      event.preventDefault();
      submitCetakSelected('barcode');
      return false;
    }
    return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    syncCheckboxesFromStorage();
  });
</script>
</body>
</html>
