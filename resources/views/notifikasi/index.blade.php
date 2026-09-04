<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pusat Notifikasi WhatsApp — SIRANI SMKN 1 AN</title>
  @include('partials.styles')
  <style>
    /* KPI STAT CARDS */
    .notif-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 12px;
    }
    .notif-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 14px;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all .15s ease;
      box-shadow: var(--shadow-sm);
      text-decoration: none;
      color: inherit;
    }
    .notif-stat-card:hover {
      border-color: #000000;
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }
    .notif-stat-card.active-card {
      border-color: #000000;
      background: var(--bg-3);
      box-shadow: var(--shadow-md);
    }
    .notif-stat-icon {
      width: 34px;
      height: 34px;
      border-radius: 8px;
      background: var(--bg-3);
      border: 1px solid var(--border);
      color: #000000;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      flex-shrink: 0;
    }
    .notif-stat-val {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: #000000;
      line-height: 1.1;
    }
    .notif-stat-lbl {
      font-size: 11px;
      color: var(--text-2);
      font-weight: 700;
      margin-top: 1px;
    }

    /* TAB NAVIGASI STATUS */
    .notif-tabs-bar {
      display: flex;
      gap: 4px;
      overflow-x: auto;
      flex-wrap: nowrap;
      margin-bottom: 12px;
      border-bottom: 1.5px solid var(--border);
      padding-bottom: 0px;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
    }
    .notif-tabs-bar::-webkit-scrollbar {
      display: none;
    }
    .notif-tab-item {
      background: transparent;
      border: none;
      border-bottom: 2.5px solid transparent;
      margin-bottom: -1.5px;
      padding: 6px 12px;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-2);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      white-space: nowrap;
      flex-shrink: 0;
      text-decoration: none;
      transition: all .15s ease;
    }
    .notif-tab-item:hover {
      color: #000000;
    }
    .notif-tab-item.active {
      background: transparent;
      color: #000000;
      font-weight: 900;
      border-bottom-color: #000000;
    }

    /* BATCH ACTION BAR */
    .batch-bar {
      display: none;
      align-items: center;
      justify-content: space-between;
      background: var(--bg-3);
      border: 1.5px solid #000000;
      border-radius: var(--r-sm);
      padding: 10px 16px;
      margin-bottom: 14px;
      animation: fadeIn .2s ease;
    }
    .batch-bar.show { display: flex; }

    /* ══ TIMELINE ACTIVITY FEED CARDS ══ */
    .feed-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 24px;
    }
    .feed-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 14px 16px;
      box-shadow: var(--shadow-sm);
      transition: all .15s ease;
      position: relative;
    }
    .feed-card:hover {
      border-color: var(--border);
      box-shadow: var(--shadow-md);
    }
    .feed-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--border);
      flex-wrap: wrap;
    }
    .feed-student-info {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }
    .feed-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--surface);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      font-size: 12px;
      color: var(--text);
      flex-shrink: 0;
    }
    .feed-student-name {
      font-size: 13.5px;
      font-weight: 800;
      color: var(--text);
      line-height: 1.2;
    }
    .feed-student-sub {
      font-size: 11px;
      color: var(--text-3);
      font-family: var(--font-mono);
      margin-top: 1px;
    }
    .feed-meta-right {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .feed-time {
      font-size: 11px;
      color: var(--text-3);
      font-family: var(--font-mono);
      font-weight: 700;
    }

    .feed-card-body {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 10px 12px;
      margin-bottom: 10px;
    }
    .feed-recipient-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 6px;
      padding-bottom: 6px;
      border-bottom: 1px dashed var(--border);
      flex-wrap: wrap;
    }
    .feed-recipient-name {
      font-size: 11.5px;
      font-weight: 800;
      color: var(--text);
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .feed-wa-pill {
      font-size: 11px;
      font-family: var(--font-mono);
      font-weight: 800;
      color: #16A34A;
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      padding: 2px 8px;
      border-radius: 4px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      transition: all .15s;
    }
    .feed-wa-pill:hover {
      border-color: #16A34A;
      background: rgba(22, 163, 74, 0.08);
    }
    .feed-message-text {
      font-size: 12px;
      color: var(--text-2);
      line-height: 1.5;
      white-space: pre-wrap;
      max-height: 72px;
      overflow: hidden;
      position: relative;
    }
    .feed-expand-btn {
      font-size: 11px;
      font-weight: 800;
      color: var(--text);
      text-decoration: underline;
      cursor: pointer;
      margin-top: 4px;
      display: inline-block;
    }

    .feed-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .feed-status-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .feed-actions {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* CATEGORY PILLS */
    .pill-kategori {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: .02em;
    }
    .pill-eskalasi { background: rgba(59,130,246,0.12); color: #2563EB; border: 1px solid rgba(59,130,246,0.3); }
    .pill-disiplin-ortu { background: rgba(249,115,22,0.12); color: #EA580C; border: 1px solid rgba(249,115,22,0.3); }
    .pill-pengingat { background: rgba(234,179,8,0.12); color: #CA8A04; border: 1px solid rgba(234,179,8,0.3); }
    .pill-panggilan { background: rgba(239,68,68,0.12); color: #DC2626; border: 1px solid rgba(239,68,68,0.3); }
    .pill-presensi { background: var(--bg-3); color: var(--text); border: 1px solid var(--border); }

    @media (max-width: 768px) {
      .notif-stat-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
      }
      .notif-stat-card {
        padding: 8px 10px !important;
      }
      .notif-stat-val {
        font-size: 18px !important;
      }
      .notif-stat-icon {
        width: 28px !important;
        height: 28px !important;
        font-size: 13px !important;
      }
      .feed-card {
        padding: 12px;
      }
      .feed-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
      }
      .feed-meta-right {
        width: 100%;
        justify-content: space-between;
      }
      .feed-card-footer {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
      }
      .feed-actions {
        width: 100%;
        justify-content: flex-end;
      }
    }
  </style>
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
            <i class="bi bi-whatsapp" style="color:#000000; font-size:16px;"></i> Notifikasi WhatsApp
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Feed aktivitas notifikasi presensi &amp; disiplin
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGuruPiket() || auth()->user()->isPiketHariIni()))
            <form action="{{ route('notifikasi.bersihkan-kadaluarsa') }}" method="POST" onsubmit="return confirm('Bersihkan semua draf notifikasi yang sudah lewat hari atau kehadiran normal (masuk/pulang) dari antrean?');" style="display:inline; margin:0;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11px; font-weight:700; color:var(--text-2); border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px; cursor:pointer;" title="Batalkan otomatis draf usang atau rutin masuk/pulang agar antrean tetap bersih">
                <i class="bi bi-stars"></i> Bersihkan Draf Usang
              </button>
            </form>
            <button type="button" class="btn btn-sm btn-outline" onclick="openModal('modalPengaturan')" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-gear-fill"></i> Pengaturan Gateway
            </button>
          @endif

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

    {{-- SUB-NAVIGASI MODUL WHATSAPP: NOTIFIKASI OTOMATIS vs BROADCAST PENGUMUMAN --}}
    <div style="display:flex; gap:6px; margin-bottom:12px; border-bottom:1px solid var(--border); padding-bottom:8px; overflow-x:auto; flex-wrap:nowrap; -webkit-overflow-scrolling:touch; scrollbar-width:none;">
      <a href="/notifikasi" class="btn btn-sm btn-gold" style="font-weight:800; font-size:11.5px; border-radius:16px; padding:4px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; flex-shrink:0;">
        <i class="bi bi-chat-text-fill"></i> Notifikasi Presensi &amp; Disiplin
      </a>
      <a href="/pengumuman" class="btn btn-sm btn-outline" style="font-weight:700; font-size:11.5px; border-radius:16px; padding:4px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; flex-shrink:0; color:var(--text-2);">
        <i class="bi bi-megaphone-fill"></i> Broadcast &amp; Pengumuman Sekolah
      </a>
    </div>

    {{-- KPI STAT CARDS --}}
    <div class="notif-stat-grid">
      {{-- Pending Verifikasi --}}
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}" class="notif-stat-card {{ $status === 'pending' ? 'active-card' : '' }}">
        <div class="notif-stat-icon">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <div class="notif-stat-val">{{ $statPending }}</div>
          <div class="notif-stat-lbl">Antrean Pending Verifikasi</div>
        </div>
      </a>

      {{-- Terkirim --}}
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'terkirim'])) }}" class="notif-stat-card {{ $status === 'terkirim' ? 'active-card' : '' }}">
        <div class="notif-stat-icon">
          <i class="bi bi-send-check"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <div class="notif-stat-val">{{ $statTerkirim }}</div>
          <div class="notif-stat-lbl">Notifikasi Terkirim</div>
        </div>
      </a>

      {{-- Gagal Kirim --}}
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'gagal'])) }}" class="notif-stat-card {{ $status === 'gagal' ? 'active-card' : '' }}">
        <div class="notif-stat-icon">
          <i class="bi bi-exclamation-circle"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <div class="notif-stat-val">{{ $statGagal }}</div>
          <div class="notif-stat-lbl">Gagal Kirim (Perlu Koreksi)</div>
        </div>
      </a>
    </div>

    {{-- TABS STATUS FILTER --}}
    <div class="notif-tabs-bar">
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'semua'])) }}" class="notif-tab-item {{ $status === 'semua' ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i> Semua Riwayat ({{ $statSemua }})
      </a>
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}" class="notif-tab-item {{ $status === 'pending' ? 'active' : '' }}">
        <i class="bi bi-inbox-fill"></i> Antrean Pending ({{ $statPending }})
      </a>
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'terkirim'])) }}" class="notif-tab-item {{ $status === 'terkirim' ? 'active' : '' }}">
        <i class="bi bi-check2-all"></i> Terkirim ({{ $statTerkirim }})
      </a>
      <a href="{{ route('notifikasi.index', array_merge(request()->except(['status', 'page']), ['status' => 'gagal'])) }}" class="notif-tab-item {{ $status === 'gagal' ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle-fill"></i> Gagal ({{ $statGagal }})
      </a>
    </div>

    {{-- FILTER TOOLBAR --}}
    <div class="panel" style="padding:10px 12px; margin-bottom:14px; background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm);">
      <form method="GET" action="{{ route('notifikasi.index') }}" id="filterForm" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
        <input type="hidden" name="status" value="{{ $status }}" />

        <div style="flex:1.5; min-width:160px;">
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari siswa, NISN, guru, ortu, No WA..." class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 10px;" />
        </div>

        <div style="min-width:140px; flex:1;">
          <select name="kategori" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <option value="eskalasi_disiplin_internal" {{ $kategori === 'eskalasi_disiplin_internal' ? 'selected' : '' }}>Eskalasi Pejabat</option>
            <option value="pemberitahuan_disiplin_ortu" {{ $kategori === 'pemberitahuan_disiplin_ortu' ? 'selected' : '' }}>Disiplin Ortu</option>
            <option value="pengingat_disiplin_harian" {{ str_starts_with($kategori ?? '', 'pengingat_disiplin') ? 'selected' : '' }}>Pengingat Harian</option>
            <option value="panggilan_ortu" {{ $kategori === 'panggilan_ortu' ? 'selected' : '' }}>Panggilan Ortu</option>
            <option value="alpha" {{ $kategori === 'alpha' ? 'selected' : '' }}>Alpha</option>
            <option value="terlambat" {{ $kategori === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            <option value="bolos" {{ $kategori === 'bolos' ? 'selected' : '' }}>Bolos</option>
            <option value="izin" {{ $kategori === 'izin' ? 'selected' : '' }}>Izin / Sakit</option>
          </select>
        </div>

        <div style="min-width:110px;">
          <select name="rombel_id" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
            @endforeach
          </select>
        </div>

        <div style="width:125px;">
          <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="this.form.submit()" title="Filter Tanggal Kirim" />
        </div>

        <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; border-radius:var(--r-sm);">
          <i class="bi bi-funnel-fill"></i> Filter
        </button>

        @if($tanggal || $kategori || $rombelId || $search || ($status !== 'semua' && $status !== 'pending'))
          <a href="{{ route('notifikasi.index') }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); border-radius:var(--r-sm);" title="Reset Filter">
            Reset
          </a>
        @endif
      </form>
    </div>

    {{-- BATCH ACTION BAR --}}
    <form id="formBatchAction" method="POST" action="{{ route('notifikasi.batch-approve') }}">
      @csrf
      <div class="batch-bar" id="batchBar">
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:18px;">☑️</span>
          <span style="font-weight:800; font-size:13.5px; color:var(--text);" id="selectedCountText">0 pesan dipilih</span>
        </div>
        <div style="display:flex; gap:8px;">
          <button type="submit" class="btn btn-sm btn-gold" style="background:#22C55E; color:#fff; border:none; font-weight:800;" onclick="document.getElementById('formBatchAction').action='{{ route('notifikasi.batch-approve') }}'; return confirm('Kirim dan setujui semua notifikasi terpilih?')">
            <i class="bi bi-send-check-fill"></i> Setujui &amp; Kirim Terpilih
          </button>
          <button type="button" class="btn btn-sm btn-danger" onclick="submitBatchReject()">
            <i class="bi bi-x-circle-fill"></i> Batalkan Terpilih
          </button>
        </div>
      </div>

      {{-- ══ TABEL DATA NOTIFIKASI RAPI & RINGKAS ══ --}}
      <div class="panel" style="padding:0; overflow:hidden; border-radius:var(--r-md); border:1px solid var(--border); background:var(--bg-2); box-shadow:var(--shadow-sm); margin-bottom:20px;">
        <div style="padding:10px 16px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:var(--surface); flex-wrap:wrap; gap:8px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:800; color:var(--text); display:inline-flex; align-items:center; gap:8px; cursor:pointer; margin:0;">
              <input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)" style="cursor:pointer; width:15px; height:15px; accent-color:#000000;" />
              <span>Pilih Semua di Halaman Ini ({{ $notifikasis->count() }} dari {{ $notifikasis->total() }} Pesan)</span>
            </label>
          </div>
          <div style="font-size:11.5px; color:var(--text-3); font-weight:700;">
            Halaman {{ $notifikasis->currentPage() }} dari {{ $notifikasis->lastPage() }}
          </div>
        </div>

        <div class="table-responsive" style="overflow-x:auto; -webkit-overflow-scrolling:touch; width:100%;">
          <table class="data-table" style="width:100%; min-width:980px; border-collapse:collapse; margin:0;">
            <thead>
              <tr style="background:var(--bg-3); border-bottom:1.5px solid var(--border); font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-2);">
                <th style="width:36px; text-align:center; padding:10px 6px;"></th>
                <th style="width:40px; text-align:center; padding:10px 6px;">No</th>
                <th style="min-width:180px; padding:10px 12px;">Siswa &amp; Kelas</th>
                <th style="min-width:110px; padding:10px 10px;">Kategori</th>
                <th style="min-width:160px; padding:10px 12px;">Penerima (Wali Murid)</th>
                <th style="min-width:240px; padding:10px 12px;">Isi Pesan WhatsApp</th>
                <th style="min-width:120px; padding:10px 10px;">Waktu Presensi</th>
                <th style="min-width:120px; padding:10px 10px;">Status Antrean</th>
                <th style="width:130px; text-align:center; padding:10px 12px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($notifikasis as $idx => $notif)
                @php
                  $isTargetGuru = str_contains($notif->kategori, 'eskalasi') || str_contains($notif->kategori, 'pengingat') || $notif->kategori === 'peringatan_wali_kelas';
                  $cleanNo = preg_replace('/[^0-9]/', '', $notif->no_tujuan ?? '');
                  if (str_starts_with($cleanNo, '0')) $cleanNo = '62' . substr($cleanNo, 1);
                  $cleanNamaOrtu = str_replace([' (Fallback Wakasis)', '((', '))'], ['', '(', ')'], $notif->nama_ortu ?: ($isTargetGuru ? 'Pejabat Sekolah' : 'Orang Tua Siswa'));
                  $namaSiswa = $notif->siswa->nama ?? ($cleanNamaOrtu ?: 'Pemberitahuan');
                  $rombelNama = $notif->siswa?->siswaRombels?->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? '-';
                  $nisn = $notif->siswa->nisn ?? '-';
                @endphp
                <tr style="border-bottom:1px solid var(--border); transition:background 0.15s ease;" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                  {{-- Checkbox --}}
                  <td style="text-align:center; padding:8px 6px; vertical-align:middle;">
                    <input type="checkbox" name="ids[]" value="{{ $notif->id }}" class="notif-item-check" onchange="updateBatchBar()" style="cursor:pointer; width:15px; height:15px; accent-color:#000000;" />
                  </td>

                  {{-- No --}}
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-family:var(--font-mono); font-size:11.5px; vertical-align:middle; padding:8px 6px;">
                    {{ $notifikasis->firstItem() + $idx }}
                  </td>

                  {{-- Siswa & Kelas --}}
                  <td style="padding:8px 12px; vertical-align:middle;">
                    <div style="display:flex; align-items:center; gap:8px;">
                      <div style="width:28px; height:28px; border-radius:50%; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:11px; color:var(--text); flex-shrink:0;">
                        {{ substr($namaSiswa, 0, 1) }}
                      </div>
                      <div style="min-width:0;">
                        <div style="font-size:12.5px; font-weight:800; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $namaSiswa }}">{{ $namaSiswa }}</div>
                        <div style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $nisn }} · <strong style="color:var(--text-2);">{{ $rombelNama }}</strong></div>
                      </div>
                    </div>
                  </td>

                  {{-- Kategori --}}
                  <td style="padding:8px 10px; vertical-align:middle; white-space:nowrap;">
                    @if(str_contains($notif->kategori, 'eskalasi'))
                      <span class="pill-kategori pill-eskalasi" style="font-size:10.5px; padding:2px 7px;"><i class="bi bi-bell-fill"></i> Eskalasi</span>
                    @elseif(str_contains($notif->kategori, 'pemberitahuan_disiplin'))
                      <span class="pill-kategori pill-disiplin-ortu" style="font-size:10.5px; padding:2px 7px;"><i class="bi bi-megaphone-fill"></i> Disiplin</span>
                    @elseif(str_contains($notif->kategori, 'pengingat_disiplin'))
                      <span class="pill-kategori pill-pengingat" style="font-size:10.5px; padding:2px 7px;"><i class="bi bi-alarm-fill"></i> Pengingat</span>
                    @elseif($notif->kategori === 'panggilan_ortu')
                      <span class="pill-kategori pill-panggilan" style="font-size:10.5px; padding:2px 7px;"><i class="bi bi-telephone-outbound-fill"></i> Panggilan</span>
                    @elseif($notif->kategori === 'alpha')
                      <span class="pill-kategori" style="background:rgba(239,68,68,0.1); color:#DC2626; border:1px solid rgba(239,68,68,0.3); font-size:10.5px; padding:2px 7px;"><i class="bi bi-x-circle-fill"></i> Alpha</span>
                    @elseif($notif->kategori === 'terlambat')
                      <span class="pill-kategori" style="background:rgba(245,158,11,0.1); color:#D97706; border:1px solid rgba(245,158,11,0.3); font-size:10.5px; padding:2px 7px;"><i class="bi bi-clock-history"></i> Terlambat</span>
                    @elseif($notif->kategori === 'bolos')
                      <span class="pill-kategori" style="background:rgba(239,68,68,0.1); color:#DC2626; border:1px solid rgba(239,68,68,0.3); font-size:10.5px; padding:2px 7px;"><i class="bi bi-door-open-fill"></i> Bolos</span>
                    @else
                      <span class="pill-kategori pill-presensi" style="font-size:10.5px; padding:2px 7px;"><i class="bi bi-info-circle-fill"></i> {{ ucwords(str_replace('_', ' ', $notif->kategori)) }}</span>
                    @endif
                  </td>

                  {{-- Penerima & Kontak --}}
                  <td style="padding:8px 12px; vertical-align:middle;">
                    <div style="font-size:12px; font-weight:800; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $cleanNamaOrtu }}">{{ $cleanNamaOrtu }}</div>
                    <div style="margin-top:2px;">
                      @if($notif->no_tujuan && $notif->no_tujuan !== '-')
                        <a href="https://wa.me/{{ $cleanNo }}" target="_blank" style="font-size:10.5px; color:#16A34A; font-weight:800; font-family:var(--font-mono); text-decoration:none; display:inline-flex; align-items:center; gap:3px;" title="Chat WhatsApp Langsung">
                          <i class="bi bi-whatsapp"></i> {{ $notif->no_tujuan }}
                        </a>
                      @elseif($notif->siswa?->no_hp_siswa)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $notif->siswa->no_hp_siswa)) }}" target="_blank" style="font-size:10.5px; color:#2563EB; font-weight:700; font-family:var(--font-mono); text-decoration:none; display:inline-flex; align-items:center; gap:3px;" title="No HP Siswa (Ortu Kosong)">
                          <i class="bi bi-phone"></i> {{ $notif->siswa->no_hp_siswa }}
                        </a>
                      @else
                        <span style="font-size:10.5px; color:var(--text-3); font-weight:600;"><i class="bi bi-x-circle"></i> Tanpa WA</span>
                      @endif
                    </div>
                  </td>

                  {{-- Isi Pesan WhatsApp --}}
                  <td style="padding:8px 12px; vertical-align:middle; max-width:280px;">
                    <div style="font-size:11.5px; color:var(--text); line-height:1.4; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $notif->pesan }}">
                      {{ Str::limit($notif->pesan, 65) }}
                    </div>
                    <a href="javascript:void(0)" onclick="previewPesanModal({{ json_encode($notif) }})" style="font-size:11px; color:#25D366; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:3px; margin-top:2px;">
                      <i class="bi bi-eye-fill"></i> Tinjau &amp; Edit Pesan
                    </a>
                  </td>

                  {{-- Waktu Presensi --}}
                  <td style="padding:8px 10px; vertical-align:middle; white-space:nowrap;">
                    <div style="font-size:11.5px; font-weight:700; color:var(--text);">{{ $notif->created_at->translatedFormat('d M Y') }}</div>
                    <div style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono); font-weight:700;">{{ $notif->created_at->format('H:i:s') }} WIB</div>
                  </td>

                  {{-- Status Antrean --}}
                  <td style="padding:8px 10px; vertical-align:middle; white-space:nowrap;">
                    @if($notif->status === 'terkirim')
                      <span class="badge" style="background:rgba(34,197,94,0.1); color:#16A34A; border:1px solid rgba(34,197,94,0.3); font-weight:800; font-size:10.5px; padding:2px 7px;">
                        <i class="bi bi-check2-all"></i> Terkirim
                      </span>
                    @elseif($notif->status === 'pending')
                      <span class="badge" style="background:rgba(234,179,8,0.12); color:#CA8A04; border:1px solid rgba(234,179,8,0.3); font-weight:800; font-size:10.5px; padding:2px 7px;">
                        <i class="bi bi-hourglass-split"></i> Pending
                      </span>
                    @elseif($notif->status === 'dibatalkan')
                      <span class="badge" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--text-3); font-size:10.5px; font-weight:700; padding:2px 7px;">
                        Dibatalkan
                      </span>
                    @else
                      <span class="badge" style="background:rgba(239,68,68,0.1); color:#DC2626; border:1px solid rgba(239,68,68,0.3); font-weight:800; font-size:10.5px; padding:2px 7px;">
                        <i class="bi bi-x-circle-fill"></i> Gagal
                      </span>
                    @endif
                  </td>

                  {{-- Aksi --}}
                  <td style="padding:8px 12px; vertical-align:middle; text-align:center; white-space:nowrap;">
                    @if($notif->status === 'pending')
                      <div style="display:inline-flex; align-items:center; gap:4px;">
                        <button type="button" class="btn btn-sm btn-gold" style="background:#22C55E; color:#fff; border:none; font-size:11px; padding:0 8px; height:28px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:3px;" onclick="approveDirect({{ $notif->id }})" title="Setujui &amp; Kirim Sekarang">
                          <i class="bi bi-send-fill"></i> Kirim
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" style="color:#DC2626; border-color:rgba(239,68,68,0.3); font-size:11px; padding:0 6px; height:28px; border-radius:6px;" onclick="rejectDirect({{ $notif->id }})" title="Batalkan Pesan">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </div>
                    @elseif($notif->status === 'gagal')
                      <div style="display:inline-flex; align-items:center; gap:4px;">
                        <button type="button" class="btn btn-sm btn-gold" style="font-size:11px; padding:0 8px; height:28px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:3px;" onclick="approveDirect({{ $notif->id }})" title="Kirim Ulang">
                          <i class="bi bi-arrow-repeat"></i> Ulang
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 6px; height:28px; border-radius:6px;" onclick="previewPesanModal({{ json_encode($notif) }})" title="Detail">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    @else
                      <div style="display:inline-flex; align-items:center; gap:4px;">
                        @if($notif->siswa_id)
                          <a href="{{ route('surat.cetak', ['siswa_id' => $notif->siswa_id, 'kategori' => ($notif->kategori === 'panggilan_ortu' ? 'panggilan_ortu' : 'berita_acara')]) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 6px; height:28px; border-radius:6px;" title="Cetak Surat Fisik">
                            <i class="bi bi-printer-fill"></i>
                          </a>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 8px; height:28px; border-radius:6px; font-weight:700;" onclick="previewPesanModal({{ json_encode($notif) }})" title="Lihat Pesan">
                          <i class="bi bi-eye"></i> Detail
                        </button>
                      </div>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" style="text-align:center; padding:48px 20px; color:var(--text-3); background:var(--bg-2);">
                    <i class="bi bi-chat-square-dots" style="font-size:36px; opacity:0.35;"></i>
                    <div style="font-weight:800; margin-top:8px; font-size:14px; color:var(--text);">Tidak ada riwayat notifikasi WhatsApp</div>
                    <p style="font-size:12px; color:var(--text-2); margin-top:2px;">Notifikasi baru akan otomatis masuk ke antrean saat terjadi ketidakhadiran atau pelanggaran disiplin.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($notifikasis->hasPages())
        <div style="display:flex; justify-content:center; margin-bottom:24px;">
          {{ $notifikasis->appends(request()->query())->links() }}
        </div>
      @endif
    </form>

  </main>
</div>

{{-- HIDDEN FORM UNTUK APPROVE / REJECT SATUAN --}}
<form id="formSingleAction" method="POST" action="" style="display:none;">
  @csrf
</form>

{{-- MODAL PRATINJAU PESAN WHATSAPP --}}
<div class="modal-overlay" id="modalPreview">
  <div class="modal-card" style="max-width:520px; padding:22px; background:#111B21; border:1px solid #2A3942; border-radius:14px; box-shadow:0 16px 40px rgba(0,0,0,0.7);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid #2A3942; padding-bottom:12px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:50%; background:#25D366; display:flex; align-items:center; justify-content:center; color:#FFFFFF; font-size:20px; box-shadow:0 2px 10px rgba(37,211,102,0.3);"> 
          <i class="bi bi-whatsapp"></i>
        </div>
        <div>
          <div style="font-weight:800; font-size:14.5px; color:#E9EDEF;" id="modalPreviewNama">-</div>
          <div style="font-size:11px; color:#8696A0; font-weight:700; letter-spacing:0.5px;" id="modalPreviewTujuan">-</div>
        </div>
      </div>
      <button type="button" class="btn btn-sm" onclick="closeModal('modalPreview')" style="background:transparent; color:#8696A0; border:none; font-size:20px; cursor:pointer; padding:4px;"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Field Input No HP Tujuan jika belum terisi / ingin diubah --}}
    <div style="margin-bottom:12px; background:#202C33; padding:8px 12px; border-radius:8px; border:1px solid #3B4A54; display:flex; align-items:center; gap:8px;">
      <span style="font-size:12px; color:#8696A0; font-weight:700; white-space:nowrap;"><i class="bi bi-telephone"></i> No. WA:</span>
      <input type="text" id="modalPreviewInputPhone" style="background:transparent !important; border:none !important; color:#25D366 !important; font-size:14px !important; font-weight:800 !important; font-family:monospace !important; width:100% !important; outline:none !important; height:28px !important; line-height:28px !important; padding:0 !important; box-shadow:none !important;" placeholder="08xxxxxxxxxx" oninput="updateModalDirectWa()" />
      <span id="phoneNoticeBadge" style="font-size:10px; padding:2px 8px; border-radius:4px; font-weight:800; display:none; white-space:nowrap;"></span>
    </div>

    <div style="margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
      <span style="font-size:11.5px; color:#8696A0; font-weight:600;"><i class="bi bi-pencil-square"></i> Isi Pesan WhatsApp (Dapat Diedit):</span>
      <span style="font-size:10.5px; color:#8696A0;" id="charCountPreview">0 karakter</span>
    </div>

    <textarea id="modalPreviewPesan" rows="9" style="width:100% !important; background:#0B141A !important; color:#E9EDEF !important; border:1px solid #2A3942 !important; border-radius:10px !important; padding:12px !important; font-size:13px !important; line-height:1.6 !important; font-family:system-ui, -apple-system, sans-serif !important; resize:vertical !important; outline:none !important;" placeholder="Tulis isi pesan..."></textarea>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; border-top:1px solid #2A3942; padding-top:14px;">
      <button type="button" class="btn btn-outline" onclick="closeModal('modalPreview')" style="color:#8696A0; border-color:#2A3942; background:transparent; font-size:12.5px;">Tutup</button>
      
      <div style="display:flex; gap:8px;" id="modalActionButtons">
        <a href="#" target="_blank" class="btn" id="btnModalDirectWa" style="background:#25D366; color:#FFFFFF; border:none; font-weight:800; text-decoration:none; padding:8px 18px; border-radius:6px; display:inline-flex; align-items:center; gap:7px; font-size:13px; box-shadow:0 2px 10px rgba(37,211,102,0.35);">
          <i class="bi bi-whatsapp"></i> Chat WhatsApp (Kirim Pesan)
        </a>
      </div>
    </div>
  </div>
</div>

{{-- MODAL PENGATURAN GATEWAY & TEMPLATE --}}
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGuruPiket() || auth()->user()->isPiketHariIni()))
<div class="modal-overlay" id="modalPengaturan">
  <div class="modal-card" style="max-width:640px; width:100%; padding:22px 20px; max-height:92vh; overflow-y:auto; border-radius:var(--r-lg); background:var(--bg-2); border:1px solid var(--border);">
    
    {{-- Header Modal --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:8px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:16px; color:#000000;">
          <i class="bi bi-gear-wide-connected"></i>
        </div>
        <div>
          <h3 style="font-size:15.5px; font-weight:900; color:var(--text); margin:0; line-height:1.2;">
            Pengaturan Gateway WhatsApp
          </h3>
          <span style="font-size:11px; color:var(--text-3);">Konfigurasi provider pengiriman &amp; draf pesan</span>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalPengaturan')" style="border-radius:6px; width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <form action="{{ route('notifikasi.pengaturan.update') }}" method="POST">
      @csrf

      {{-- SEKSI 1: KONFIGURASI MESIN GATEWAY --}}
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-sm); padding:14px; margin-bottom:14px;">
        <div style="font-weight:800; font-size:12.5px; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-hdd-network-fill" style="color:#000000;"></i>
          <span>Mesin Pengirim Pesan (Gateway)</span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:10px; margin-bottom:10px;">
          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">Provider Layanan</label>
            <select name="wa_provider" class="input-field" style="width:100%; height:36px; font-size:12px;">
              <option value="simulasi" {{ $setting->wa_provider === 'simulasi' ? 'selected' : '' }}>Mode Simulasi (Aman / Log Saja)</option>
              <option value="fonnte" {{ $setting->wa_provider === 'fonnte' ? 'selected' : '' }}>Fonnte.com (Rekomendasi)</option>
              <option value="wablas" {{ $setting->wa_provider === 'wablas' ? 'selected' : '' }}>Wablas.com</option>
              <option value="generic_api" {{ $setting->wa_provider === 'generic_api' ? 'selected' : '' }}>Custom REST API</option>
            </select>
          </div>

          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">Status Operasional</label>
            <select name="is_active" class="input-field" style="width:100%; height:36px; font-size:12px;">
              <option value="1" {{ $setting->is_active ? 'selected' : '' }}>🟢 Aktif (Live Dispatch)</option>
              <option value="0" {{ !$setting->is_active ? 'selected' : '' }}>🔴 Nonaktif (Simpan Draf Saja)</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:10px;">
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">API Token / Secret Key</label>
          <input type="text" name="wa_api_token" class="input-field" value="{{ $setting->wa_api_token }}" placeholder="Masukkan token API WhatsApp..." style="width:100%; height:36px; font-size:12px; font-family:var(--font-mono);" />
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">Endpoint URL Gateway (Opsional)</label>
          <input type="text" name="wa_endpoint_url" class="input-field" value="{{ $setting->wa_endpoint_url }}" placeholder="https://api.fonnte.com/send" style="width:100%; height:36px; font-size:12px; font-family:var(--font-mono);" />
        </div>
      </div>

      {{-- SEKSI 2: FILTER KATEGORI NOTIFIKASI (ANTI-ANTREAN MEMBENGKAK) --}}
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-sm); padding:14px; margin-bottom:14px;">
        <div style="font-weight:800; font-size:12.5px; color:var(--text); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-funnel-fill" style="color:#000000;"></i>
          <span>Kategori Notifikasi yang Diizinkan (Anti-Spam Antrean)</span>
        </div>
        <p style="font-size:11px; color:var(--text-3); margin-top:0; margin-bottom:12px;">
          Pilih hanya kejadian yang membutuhkan perhatian orang tua. Nonaktifkan kehadiran normal agar tidak membanjiri antrean harian.
        </p>

        <div style="display:grid; grid-template-columns:1fr; gap:8px;">
          <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--text); cursor:pointer;">
            <input type="checkbox" name="notif_alpha_aktif" value="1" {{ ($setting->notif_alpha_aktif ?? true) ? 'checked' : '' }} style="width:16px; height:16px;" />
            <span>❌ Siswa Alpha (Tanpa Keterangan) <span style="font-size:10.5px; color:#DC2626; font-weight:600;">(Prioritas Utama)</span></span>
          </label>

          <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--text); cursor:pointer;">
            <input type="checkbox" name="notif_bolos_aktif" value="1" {{ ($setting->notif_bolos_aktif ?? true) ? 'checked' : '' }} style="width:16px; height:16px;" />
            <span>🚫 Siswa Bolos / Pulang Sebelum Waktu <span style="font-size:10.5px; color:#DC2626; font-weight:600;">(Prioritas Utama)</span></span>
          </label>

          <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--text); cursor:pointer;">
            <input type="checkbox" name="notif_terlambat_aktif" value="1" {{ ($setting->notif_terlambat_aktif ?? true) ? 'checked' : '' }} style="width:16px; height:16px;" />
            <span>⚠️ Siswa Terlambat Datang Sekolah</span>
          </label>

          <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--text); cursor:pointer;">
            <input type="checkbox" name="notif_panggilan_aktif" value="1" {{ ($setting->notif_panggilan_aktif ?? true) ? 'checked' : '' }} style="width:16px; height:16px;" />
            <span>🚨 Surat Panggilan Orang Tua (Akumulasi Alpha)</span>
          </label>

          <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--text); cursor:pointer;">
            <input type="checkbox" name="notif_izin_aktif" value="1" {{ ($setting->notif_izin_aktif ?? true) ? 'checked' : '' }} style="width:16px; height:16px;" />
            <span>📋 Konfirmasi Surat Izin &amp; Sakit Resmi</span>
          </label>

          <div style="border-top:1px dashed var(--border); margin:4px 0;"></div>

          <label style="display:flex; align-items:flex-start; gap:8px; font-size:12px; font-weight:600; color:var(--text-2); cursor:pointer;">
            <input type="checkbox" name="notif_masuk_aktif" value="1" {{ ($setting->notif_masuk_aktif ?? false) ? 'checked' : '' }} style="width:16px; height:16px; margin-top:2px;" />
            <div>
              <span>🟢 Siswa Masuk Tepat Waktu (Kehadiran Normal)</span>
              <div style="font-size:10.5px; color:var(--text-3); font-weight:400;">Nonaktifkan agar tidak menumpuk ratusan antrean harian saat siswa scan pagi.</div>
            </div>
          </label>

          <label style="display:flex; align-items:flex-start; gap:8px; font-size:12px; font-weight:600; color:var(--text-2); cursor:pointer;">
            <input type="checkbox" name="notif_pulang_aktif" value="1" {{ ($setting->notif_pulang_aktif ?? false) ? 'checked' : '' }} style="width:16px; height:16px; margin-top:2px;" />
            <div>
              <span>🔵 Siswa Pulang Sekolah Normal</span>
              <div style="font-size:10.5px; color:var(--text-3); font-weight:400;">Nonaktifkan agar tidak menumpuk ratusan antrean harian saat siswa scan jam pulang.</div>
            </div>
          </label>
        </div>
      </div>

      {{-- SEKSI 3: ATURAN AMBANG BATAS PELANGGARAN --}}
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-sm); padding:14px; margin-bottom:14px;">
        <div style="font-weight:800; font-size:12.5px; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-shield-exclamation" style="color:#000000;"></i>
          <span>Ketentuan Otomasi Panggilan Ortu</span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:10px;">
          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">Ambang Batas Alpha (Panggilan)</label>
            <div style="display:flex; align-items:center; gap:6px;">
              <input type="number" name="ambang_batas_alpha" class="input-field" value="{{ $setting->ambang_batas_alpha ?? 3 }}" min="1" max="10" style="width:70px; height:36px; text-align:center; font-weight:800; font-size:13px;" />
              <span style="font-size:11.5px; color:var(--text-2); font-weight:600;">Kali ketidakhadiran</span>
            </div>
          </div>

          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">Hitung Bolos Bersama Alpha?</label>
            <select name="hitung_bolos_bersama_alpha" class="input-field" style="width:100%; height:36px; font-size:12px;">
              <option value="1" {{ $setting->hitung_bolos_bersama_alpha ? 'selected' : '' }}>Ya (Akumulasi Alpha + Bolos)</option>
              <option value="0" {{ !$setting->hitung_bolos_bersama_alpha ? 'selected' : '' }}>Tidak (Hanya Alpha Murni)</option>
            </select>
          </div>
        </div>
      </div>

      {{-- SEKSI 3: TEMPLATE PESAN --}}
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-sm); padding:14px; margin-bottom:16px;">
        <div style="font-weight:800; font-size:12.5px; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-chat-quote-fill" style="color:#000000;"></i>
          <span>Template Format Pesan Otomatis</span>
        </div>

        <div style="margin-bottom:12px;">
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">
            Template: Alpha / Tidak Hadir Tanpa Keterangan
          </label>
          <textarea name="template_alpha" class="input-field" rows="3" style="width:100%; font-size:12px; line-height:1.5; padding:8px 10px; font-family:var(--font-mono); resize:vertical;">{{ $setting->template_alpha }}</textarea>
        </div>

        <div style="margin-bottom:12px;">
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">
            Template: Keterlambatan Hadir
          </label>
          <textarea name="template_terlambat" class="input-field" rows="3" style="width:100%; font-size:12px; line-height:1.5; padding:8px 10px; font-family:var(--font-mono); resize:vertical;">{{ $setting->template_terlambat }}</textarea>
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px; color:var(--text-2);">
            Template: Peringatan Eskalasi Wali Kelas
          </label>
          <textarea name="template_wali_kelas" class="input-field" rows="3" style="width:100%; font-size:12px; line-height:1.5; padding:8px 10px; font-family:var(--font-mono); resize:vertical;">{{ $setting->template_wali_kelas }}</textarea>
        </div>

        <div style="font-size:10.5px; color:var(--text-3); margin-top:8px; line-height:1.4;">
          <i class="bi bi-info-circle"></i> Variabel dinamis: <code>{nama_siswa}</code>, <code>{kelas}</code>, <code>{tanggal}</code>, <code>{waktu}</code>, <code>{nama_wali_kelas}</code>
        </div>
      </div>

      <div style="display:none;">
        <input type="hidden" name="template_izin" value="{{ $setting->template_izin }}" />
        <input type="hidden" name="template_sakit" value="{{ $setting->template_sakit }}" />
        <input type="hidden" name="template_bolos" value="{{ $setting->template_bolos }}" />
      </div>

      {{-- Footer Aksi --}}
      <div style="display:flex; justify-content:flex-end; gap:8px; padding-top:12px; border-top:1px solid var(--border);">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalPengaturan')" style="padding:6px 14px; font-size:12px; font-weight:700; border-radius:6px;">
          Batal
        </button>
        <button type="submit" class="btn btn-gold" style="background:#000000; color:#FFFFFF; border:none; padding:6px 16px; font-size:12px; font-weight:800; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
          <i class="bi bi-check-circle-fill"></i> Simpan Pengaturan
        </button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- SCRIPT INTERAKSI --}}
<script>
  function approveDirect(id) {
    if (confirm('Setujui dan kirim pesan notifikasi ini sekarang?')) {
      const form = document.getElementById('formSingleAction');
      form.action = '/notifikasi/' + id + '/approve';
      form.submit();
    }
  }

  function rejectDirect(id) {
    if (confirm('Batalkan draf pesan notifikasi ini?')) {
      const form = document.getElementById('formSingleAction');
      form.action = '/notifikasi/' + id + '/reject';
      form.submit();
    }
  }

  function toggleCheckAll(source) {
    const checkboxes = document.querySelectorAll('.notif-item-check');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateBatchBar();
  }

  function updateBatchBar() {
    const checked = document.querySelectorAll('.notif-item-check:checked');
    const bar = document.getElementById('batchBar');
    const text = document.getElementById('selectedCountText');
    if (checked.length > 0) {
      bar.classList.add('show');
      text.innerText = checked.length + ' pesan dipilih';
    } else {
      bar.classList.remove('show');
      document.getElementById('checkAll').checked = false;
    }
  }

  function submitBatchReject() {
    if (confirm('Batalkan seluruh pesan terpilih?')) {
      const form = document.getElementById('formBatchAction');
      form.action = '{{ route('notifikasi.batch-reject') }}';
      form.submit();
    }
  }

  let currentTargetPhone = '';
  let activeNotif = null;

  const templateConfig = {
    terlambat: {!! json_encode($setting->template_terlambat) !!},
    alpha: {!! json_encode($setting->template_alpha) !!},
    izin: {!! json_encode($setting->template_izin) !!},
    sakit: {!! json_encode($setting->template_sakit) !!},
    bolos: {!! json_encode($setting->template_bolos) !!},
  };

  function formatTanggalIndo(dateStr) {
    if (!dateStr) return '';
    try {
      const clean = String(dateStr).split('T')[0];
      const parts = clean.split('-');
      if (parts.length === 3) {
        const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const d = parseInt(parts[2], 10);
        const m = parseInt(parts[1], 10) - 1;
        const y = parts[0];
        if (!isNaN(d) && m >= 0 && m < 12) {
          return d + ' ' + bulan[m] + ' ' + y;
        }
      }
    } catch (e) {}
    return String(dateStr).split('T')[0];
  }

  function renderTemplateFromConfig(notif) {
    let tpl = templateConfig[notif.kategori] || notif.pesan || '';
    if (!tpl) return notif.pesan || '';

    let nama = notif.siswa ? notif.siswa.nama : (notif.nama_ortu || '-');
    let rombel = '-';
    if (notif.siswa && notif.siswa.siswa_rombels && notif.siswa.siswa_rombels.length > 0) {
      rombel = notif.siswa.siswa_rombels[0].rombel ? notif.siswa.siswa_rombels[0].rombel.nama_rombel : '-';
    }

    let jam = '07:15';
    let matchJam = (notif.pesan || '').match(/pukul\s+([0-9:]{5,8})/i);
    if (matchJam) {
      jam = matchJam[1];
    } else if (notif.created_at) {
      try {
        let cd = new Date(notif.created_at);
        if (!isNaN(cd.getTime())) {
          jam = cd.toLocaleTimeString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
          }).replace(/\./g, ':');
        }
      } catch (e) {}
    }

    let tgl = formatTanggalIndo(notif.tanggal);

    return tpl
      .replace(/{nama_siswa}/g, nama)
      .replace(/{kelas}/g, rombel)
      .replace(/{rombel}/g, rombel)
      .replace(/{jam}/g, jam)
      .replace(/{waktu}/g, jam)
      .replace(/{tanggal}/g, tgl)
      .replace(/{batas_jam}/g, '07:15')
      .replace(/{keterangan}/g, (notif.kategori || '-').toUpperCase())
      .replace(/{nama_ortu}/g, notif.nama_ortu || 'Bapak/Ibu Orang Tua/Wali');
  }

  function previewPesanModal(notif) {
    activeNotif = notif;
    document.getElementById('modalPreviewNama').innerText = notif.nama_ortu || (notif.siswa ? notif.siswa.nama : 'Penerima');
    document.getElementById('modalPreviewTujuan').innerText = (notif.kategori || '').replace(/_/g, ' ').toUpperCase();
    
    const textarea = document.getElementById('modalPreviewPesan');
    let pesanAwal = notif.pesan || '';
    
    // Otomatis ubah pesan jika masih teks lama atau ISO string
    if (pesanAwal.includes('Scan Barcode/RFID') || pesanAwal.includes('telah melakukan presensi') || pesanAwal.includes('.000000Z') || pesanAwal.includes('2026-09-03T')) {
      pesanAwal = renderTemplateFromConfig(notif);
    } else {
      // Bersihkan jika ada format tanggal ISO yang tersisa di teks pesan
      pesanAwal = pesanAwal.replace(/[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9:.]+Z?/g, formatTanggalIndo(notif.tanggal));
    }
    if (textarea) textarea.value = pesanAwal;

    // Ambil nomor target: no_tujuan, atau jika kosong coba dari siswa->no_hp_ortu, lalu siswa->no_hp_siswa
    let rawPhone = (notif.no_tujuan && notif.no_tujuan !== '-') ? notif.no_tujuan : '';
    if (!rawPhone && notif.siswa) {
      if (notif.siswa.no_hp_ortu && notif.siswa.no_hp_ortu !== '-') {
        rawPhone = notif.siswa.no_hp_ortu;
      } else if (notif.siswa.no_hp_siswa && notif.siswa.no_hp_siswa !== '-') {
        rawPhone = notif.siswa.no_hp_siswa;
      }
    }

    const inputPhone = document.getElementById('modalPreviewInputPhone');
    if (inputPhone) inputPhone.value = rawPhone;

    updateModalDirectWa();
    openModal('modalPreview');
  }

  function updateModalDirectWa() {
    const textarea = document.getElementById('modalPreviewPesan');
    const btn = document.getElementById('btnModalDirectWa');
    const counter = document.getElementById('charCountPreview');
    const inputPhone = document.getElementById('modalPreviewInputPhone');
    const badge = document.getElementById('phoneNoticeBadge');
    const msg = textarea ? textarea.value : '';

    if (counter) counter.innerText = msg.length + ' karakter';

    let rawVal = inputPhone ? inputPhone.value.trim() : '';
    let clean = rawVal.replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) clean = '62' + clean.substr(1);

    if (badge) {
      if (!clean) {
        badge.style.display = 'inline-block';
        badge.style.background = '#EF4444';
        badge.style.color = '#FFFFFF';
        badge.innerText = 'No. HP Belum Ada';
      } else {
        badge.style.display = 'none';
      }
    }

    if (clean) {
      btn.href = 'https://wa.me/' + clean + '?text=' + encodeURIComponent(msg);
      btn.style.pointerEvents = 'auto';
      btn.style.opacity = '1';
    } else {
      btn.href = '#';
      btn.style.pointerEvents = 'none';
      btn.style.opacity = '0.4';
    }
  }

  document.getElementById('modalPreviewPesan')?.addEventListener('input', updateModalDirectWa);

  function openModal(id) { document.getElementById(id).classList.add('active'); }
  function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>

</body>
</html>
