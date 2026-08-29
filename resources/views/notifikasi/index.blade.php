<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifikasi WhatsApp — SIRANI SMKN 1 AN</title>
  @include('partials.styles')
  <style>
    /* VIEW SWITCHER & LAYOUT */
    .view-toggle-btn {
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      border: 1px solid var(--border);
      background: var(--bg-2);
      color: var(--text-2);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
    }
    .view-toggle-btn:hover {
      color: var(--text);
      border-color: var(--border-2);
    }
    .view-toggle-btn.active {
      background: var(--text);
      color: var(--bg);
      border-color: var(--text);
      font-weight: 800;
    }

    /* KPI STAT CARDS */
    .notif-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
      margin-bottom: 20px;
    }
    .notif-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 16px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all .2s ease;
    }
    .notif-stat-card:hover {
      border-color: var(--border-2);
      transform: translateY(-2px);
    }
    .notif-stat-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: var(--bg-3);
      border: 1px solid var(--border);
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
    }
    .notif-stat-val {
      font-size: 24px;
      font-weight: 900;
      font-family: var(--font-mono);
      color: var(--text);
      line-height: 1.1;
    }
    .notif-stat-lbl {
      font-size: 12px;
      color: var(--text-2);
      font-weight: 700;
      margin-top: 2px;
    }

    /* TAB NAVIGASI */
    .notif-tabs-bar {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 16px;
      border-bottom: 2px solid var(--border);
      padding-bottom: 2px;
    }
    .notif-tab-item {
      background: transparent;
      border: none;
      border-bottom: 2.5px solid transparent;
      margin-bottom: -4px;
      padding: 8px 16px;
      font-size: 13px;
      font-weight: 700;
      color: var(--text-2);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
    }
    .notif-tab-item:hover {
      color: var(--text);
    }
    .notif-tab-item.active {
      background: transparent;
      color: var(--text);
      font-weight: 800;
      border-bottom-color: var(--text);
    }

    /* BATCH ACTION BAR */
    .batch-bar {
      display: none;
      align-items: center;
      justify-content: space-between;
      background: linear-gradient(135deg, rgba(202,138,4,0.12), rgba(34,197,94,0.12));
      border: 1.5px solid var(--gold);
      border-radius: var(--r-sm);
      padding: 10px 16px;
      margin-bottom: 14px;
      animation: fadeIn .2s ease;
    }
    .batch-bar.show { display: flex; }

    /* CHAT SPLIT VIEW */
    .chat-split-container {
      display: grid;
      grid-template-columns: 380px 1fr;
      gap: 20px;
      min-height: 580px;
    }
    @media (max-width: 992px) {
      .chat-split-container { grid-template-columns: 1fr; }
    }
    .chat-list-pane {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      max-height: 680px;
    }
    .chat-list-scroll {
      overflow-y: auto;
      flex: 1;
    }
    .chat-list-item {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: all .15s ease;
      display: flex;
      gap: 10px;
      align-items: flex-start;
    }
    .chat-list-item:hover {
      background: var(--bg-3);
    }
    .chat-list-item.selected {
      background: rgba(202,138,4,0.08);
      border-left: 3px solid var(--gold);
    }

    .chat-preview-pane {
      background: #0B141A;
      border: 1px solid #2A3942;
      border-radius: var(--r-md);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      box-shadow: var(--shadow-md);
    }
    .chat-preview-header {
      background: #202C33;
      padding: 12px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #2A3942;
      color: #E9EDEF;
    }
    .chat-preview-body {
      padding: 24px;
      flex: 1;
      overflow-y: auto;
      background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
      background-size: 20px 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .wa-bubble-green {
      background: #005C4B;
      color: #E9EDEF;
      border-radius: 12px 12px 2px 12px;
      padding: 14px 18px;
      max-width: 520px;
      font-size: 13.5px;
      line-height: 1.6;
      white-space: pre-wrap;
      box-shadow: 0 2px 6px rgba(0,0,0,0.4);
      position: relative;
    }
    .wa-bubble-time {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 4px;
      font-size: 10.5px;
      color: rgba(233,237,239,0.7);
      margin-top: 8px;
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
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    
    {{-- HEADER --}}
    <header class="header" style="margin-bottom: 20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-whatsapp" style="color:#22C55E;"></i> Meja Notifikasi WhatsApp
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          Monitoring pengiriman pesan otomatis kedisiplinan, alert pejabat kesiswaan, dan presensi siswa.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:10px;">
        {{-- VIEW SWITCHER --}}
        <div style="display:flex; background:var(--bg-3); padding:3px; border-radius:24px; border:1px solid var(--border);">
          <button type="button" class="view-toggle-btn {{ request('view_mode', 'table') === 'table' ? 'active' : '' }}" onclick="switchViewMode('table')">
            <i class="bi bi-table"></i> Tabel
          </button>
          <button type="button" class="view-toggle-btn {{ request('view_mode') === 'chat' ? 'active' : '' }}" onclick="switchViewMode('chat')">
            <i class="bi bi-chat-dots-fill"></i> Mode Chat
          </button>
        </div>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGuruPiket() || auth()->user()->isPiketHariIni()))
          <button type="button" class="btn btn-outline" onclick="openModal('modalPengaturan')">
            <i class="bi bi-gear-fill"></i> Pengaturan
          </button>
        @endif

        @include('partials.header_actions')
      </div>
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:16px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:16px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- SUB-NAVIGASI MODUL WHATSAPP: NOTIFIKASI OTOMATIS vs BROADCAST PENGUMUMAN --}}
    <div style="display:flex; gap:10px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px; flex-wrap:wrap;">
      <a href="/notifikasi" class="btn" style="font-weight:800; font-size:12.5px; border-radius:20px; padding:7px 18px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:var(--text); color:var(--bg); border:1px solid var(--text);">
        <i class="bi bi-chat-text-fill"></i> Notifikasi Presensi &amp; Disiplin
      </a>
      <a href="/pengumuman" class="btn btn-outline" style="font-weight:700; font-size:12.5px; border-radius:20px; padding:7px 18px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; color:var(--text-2);">
        <i class="bi bi-megaphone"></i> Broadcast &amp; Pengumuman Sekolah
      </a>
    </div>

    {{-- KPI STAT CARDS — Klikable --}}
    <div class="notif-stat-grid">

      {{-- Pending Verifikasi --}}
      <div class="notif-stat-card notif-kpi-card" id="kpiNotifPending"
           onclick="showNotifTable('pending', this)"
           style="cursor:pointer; user-select:none; transition:all .2s;">
        <div class="notif-stat-icon">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <div class="notif-stat-val">{{ $statPending }}</div>
          <div class="notif-stat-lbl">Antrean Pending Verifikasi</div>
        </div>
      </div>

      {{-- Terkirim --}}
      <div class="notif-stat-card notif-kpi-card" id="kpiNotifTerkirim"
           onclick="showNotifTable('terkirim', this)"
           style="cursor:pointer; user-select:none; transition:all .2s;">
        <div class="notif-stat-icon">
          <i class="bi bi-send-check"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <div class="notif-stat-val">{{ $statTerkirim }}</div>
          <div class="notif-stat-lbl">Notifikasi Terkirim</div>
        </div>
      </div>

      {{-- Gagal Kirim --}}
      <div class="notif-stat-card notif-kpi-card" id="kpiNotifGagal"
           onclick="showNotifTable('gagal', this)"
           style="cursor:pointer; user-select:none; transition:all .2s;">
        <div class="notif-stat-icon">
          <i class="bi bi-exclamation-circle"></i>
        </div>
        <div style="flex:1; min-width:0;">
          <div class="notif-stat-val">{{ $statGagal }}</div>
          <div class="notif-stat-lbl">Gagal Kirim (Perlu Koreksi)</div>
        </div>
      </div>

    </div>

    {{-- TABS STATUS FILTER --}}
    <div class="notif-tabs-bar">
      <button type="button" class="notif-tab-item {{ $status === 'semua' ? 'active' : '' }}" onclick="showNotifTable('semua', null, this)">
        <i class="bi bi-grid-fill"></i> Semua Riwayat ({{ $statSemua }})
      </button>
      <button type="button" class="notif-tab-item {{ $status === 'pending' ? 'active' : '' }}" onclick="showNotifTable('pending', null, this)">
        <i class="bi bi-inbox-fill"></i> Antrean Pending ({{ $statPending }})
      </button>
      <button type="button" class="notif-tab-item {{ $status === 'terkirim' ? 'active' : '' }}" onclick="showNotifTable('terkirim', null, this)">
        <i class="bi bi-check2-all"></i> Terkirim ({{ $statTerkirim }})
      </button>
      <button type="button" class="notif-tab-item {{ $status === 'gagal' ? 'active' : '' }}" onclick="showNotifTable('gagal', null, this)">
        <i class="bi bi-exclamation-triangle-fill"></i> Gagal ({{ $statGagal }})
      </button>
    </div>

    {{-- FILTER TOOLBAR --}}
    <div class="panel" style="padding:14px 16px; margin-bottom:18px;">
      <form method="GET" action="{{ url()->current() }}" id="filterForm" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
        <input type="hidden" name="status" id="filterStatusInput" value="{{ $status }}" />
        <input type="hidden" name="view_mode" id="filterViewModeInput" value="{{ request('view_mode', 'table') }}" />

        <div style="flex:1.5; min-width:200px;">
          <input type="text" name="q" value="{{ $search }}" placeholder="Cari siswa, NIS, nama guru/ortu, No WA..." class="input-field" style="width:100%; height:38px; font-size:12.5px;" />
        </div>

        <div style="min-width:180px;">
          <select name="kategori" class="input-field" style="width:100%; height:38px; font-size:12.5px;" onchange="this.form.submit()">
            <option value="">Semua Kategori Pesan</option>
            <option value="eskalasi_disiplin_internal" {{ $kategori === 'eskalasi_disiplin_internal' ? 'selected' : '' }}>Eskalasi Guru / Pejabat</option>
            <option value="pemberitahuan_disiplin_ortu" {{ $kategori === 'pemberitahuan_disiplin_ortu' ? 'selected' : '' }}>Pemberitahuan Disiplin Ortu</option>
            <option value="pengingat_disiplin_harian" {{ str_starts_with($kategori ?? '', 'pengingat_disiplin') ? 'selected' : '' }}>Pengingat Harian Kasus</option>
            <option value="panggilan_ortu" {{ $kategori === 'panggilan_ortu' ? 'selected' : '' }}>Panggilan Orang Tua</option>
            <option value="alpha" {{ $kategori === 'alpha' ? 'selected' : '' }}>Alpha</option>
            <option value="terlambat" {{ $kategori === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            <option value="bolos" {{ $kategori === 'bolos' ? 'selected' : '' }}>Bolos</option>
            <option value="izin" {{ $kategori === 'izin' ? 'selected' : '' }}>Izin / Sakit</option>
          </select>
        </div>

        <div style="min-width:150px;">
          <select name="rombel_id" class="input-field" style="width:100%; height:38px; font-size:12.5px;" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
            @endforeach
          </select>
        </div>

        <div style="width:150px;">
          <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field" style="width:100%; height:38px; font-size:12.5px;" onchange="this.form.submit()" title="Filter Tanggal Kirim" />
        </div>

        <button type="submit" class="btn btn-outline" style="height:38px; padding:0 14px;">
          <i class="bi bi-funnel-fill"></i> Filter
        </button>

        @if($tanggal || $kategori || $rombelId || $search || ($status !== 'semua' && $status !== 'pending'))
          <a href="{{ url()->current() }}?view_mode={{ request('view_mode', 'table') }}" class="btn btn-outline" style="height:38px; padding:0 12px; color:var(--red); border-color:var(--red);" title="Reset Filter">
            <i class="bi bi-x-circle"></i> Reset
          </a>
        @endif
      </form>
    </div>

    {{-- KONTEN UTAMA BERDASARKAN VIEW MODE --}}
    @if(request('view_mode') === 'chat')
      
      {{-- MODE 2: WHATSAPP CHAT SPLIT VIEW --}}
      <div class="chat-split-container">
        
        {{-- LIST PANE (KIRI) --}}
        <div class="chat-list-pane">
          <div style="padding:12px 14px; background:var(--bg-3); border-bottom:1px solid var(--border); font-weight:800; font-size:12px; color:var(--text-2); display:flex; justify-content:space-between;">
            <span>DAFTAR PESAN ({{ $notifikasis->count() }})</span>
            <span>STATUS</span>
          </div>

          <div class="chat-list-scroll">
            @forelse($notifikasis as $idx => $n)
              @php
                $isTargetGuru = str_contains($n->kategori, 'eskalasi') || str_contains($n->kategori, 'pengingat') || $n->kategori === 'peringatan_wali_kelas';
              @endphp
              <div class="chat-list-item {{ $idx === 0 ? 'selected' : '' }}" onclick="selectChatItem({{ json_encode($n) }}, this)">
                <div style="width:36px; height:36px; border-radius:50%; background:{{ $isTargetGuru ? 'rgba(59,130,246,0.15)' : 'rgba(202,138,4,0.15)' }}; color:{{ $isTargetGuru ? '#2563EB' : '#CA8A04' }}; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
                  <i class="bi {{ $isTargetGuru ? 'bi-person-badge' : 'bi-person' }}"></i>
                </div>

                <div style="flex:1; min-width:0;">
                  <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:2px;">
                    <strong style="font-size:12.5px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                      {{ $n->siswa->nama ?? 'Pesan Manual' }}
                    </strong>
                    <span style="font-size:10px; color:var(--text-3); font-family:var(--font-mono);">
                      {{ $n->created_at->format('H:i') }}
                    </span>
                  </div>

                  <div style="font-size:11px; color:{{ $isTargetGuru ? '#2563EB' : 'var(--text-2)' }}; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $n->nama_ortu ?: ($isTargetGuru ? 'Pejabat Sekolah' : 'Orang Tua Siswa') }}
                  </div>

                  <div style="font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px;">
                    {{ Str::limit($n->pesan, 45) }}
                  </div>
                </div>

                <div>
                  @if($n->status === 'terkirim')
                    <span style="color:#22C55E; font-size:14px;" title="Terkirim"><i class="bi bi-check2-all"></i></span>
                  @elseif($n->status === 'gagal')
                    <span style="color:#EF4444; font-size:13px;" title="Gagal"><i class="bi bi-exclamation-circle-fill"></i></span>
                  @else
                    <span style="color:#CA8A04; font-size:12px;" title="Pending"><i class="bi bi-hourglass-split"></i></span>
                  @endif
                </div>
              </div>
            @empty
              <div style="text-align:center; padding:40px; color:var(--text-3);">
                <i class="bi bi-chat-square-dots" style="font-size:32px; opacity:0.5;"></i>
                <div style="font-weight:700; margin-top:8px;">Tidak ada pesan yang cocok</div>
              </div>
            @endforelse
          </div>
        </div>

        {{-- PREVIEW PANE (KANAN) --}}
        <div class="chat-preview-pane" id="chatPreviewPane">
          @if($notifikasis->count() > 0)
            @php $first = $notifikasis->first(); @endphp
            <div class="chat-preview-header" id="chatHeader">
              <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:38px; height:38px; border-radius:50%; background:#2A3942; display:flex; align-items:center; justify-content:center; font-size:18px; color:#22C55E;">
                  <i class="bi bi-whatsapp"></i>
                </div>
                <div>
                  <div style="font-weight:800; font-size:14px;" id="chatTargetName">{{ $first->nama_ortu ?: 'Penerima Pesan' }}</div>
                  <div style="font-size:11.5px; color:#8696A0;" id="chatTargetPhone">
                    <i class="bi bi-telephone-fill"></i> {{ $first->no_tujuan ?: '-' }} • Siswa: <strong style="color:#E9EDEF;">{{ $first->siswa->nama ?? '-' }}</strong>
                  </div>
                </div>
              </div>

              <div style="display:flex; gap:8px;" id="chatHeaderActions">
                @if($first->status === 'pending')
                  <form action="{{ route('notifikasi.approve', $first->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-gold" style="background:#22C55E; color:#fff; border:none; font-weight:800;">
                      <i class="bi bi-send-fill"></i> Setujui &amp; Kirim
                    </button>
                  </form>
                  <form action="{{ route('notifikasi.reject', $first->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Batalkan draf pesan ini?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="border-color:#EF4444; color:#EF4444;">
                      <i class="bi bi-x-circle"></i> Tolak
                    </button>
                  </form>
                @endif
                @if($first->no_tujuan)
                  <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $first->no_tujuan) }}" target="_blank" class="btn btn-sm btn-outline" style="border-color:#22C55E; color:#22C55E; font-size:11px;" id="btnWaDirect">
                    <i class="bi bi-box-arrow-up-right"></i> Buka WhatsApp Web
                  </a>
                @endif
              </div>
            </div>

            <div class="chat-preview-body">
              <div class="wa-bubble-green" id="chatBubbleText">{{ $first->pesan }}<div class="wa-bubble-time" id="chatBubbleTime">
                  <span>{{ $first->created_at->translatedFormat('d F Y - H:i') }} WIB</span>
                  <i class="bi bi-check2-all" style="color:#53BDEB; font-size:13px;"></i>
                </div>
              </div>
            </div>
          @else
            <div style="display:flex; justify-content:center; align-items:center; height:100%; color:#8696A0;">
              Pilih pesan di sebelah kiri untuk melihat percakapan WhatsApp.
            </div>
          @endif
        </div>

      </div>

    @else

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

        {{-- MODE 1: TABEL KOMPAK (DEFAULT) --}}
        <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;" id="notifTableSection">
          {{-- Header panel tabel --}}
          <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="font-size:15px; font-weight:800; color:var(--text); display:flex; align-items:center; gap:8px;">
              <i id="notifTableIcon" class="bi bi-chat-text-fill" style="color:var(--gold);"></i>
              <span id="notifTableTitle">Semua Riwayat Notifikasi WhatsApp</span>
            </div>
            <button type="button" onclick="closeNotifTable()" style="background:none; border:1px solid var(--border); border-radius:6px; padding:4px 10px; color:var(--text-3); cursor:pointer; font-size:12px; display:inline-flex; align-items:center; gap:4px; transition:all .15s;"
                    onmouseover="this.style.borderColor='var(--gold)'; this.style.color='var(--gold)';"
                    onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-3)';">
              <i class="bi bi-x-lg"></i> Tutup
            </button>
          </div>
          <div class="table-responsive" style="overflow-x:auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width:36px; text-align:center;">
                    <input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)" style="cursor:pointer;" />
                  </th>
                  <th>Siswa &amp; Rombel</th>
                  <th>Target Penerima &amp; WhatsApp</th>
                  <th>Kategori Pesan</th>
                  <th>Status Pengiriman</th>
                  <th>Verifikator</th>
                  <th style="width:210px; text-align:center;">Aksi Cepat Relevan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($notifikasis as $idx => $notif)
                  @php
                    $isTargetGuru = str_contains($notif->kategori, 'eskalasi') || str_contains($notif->kategori, 'pengingat') || $notif->kategori === 'peringatan_wali_kelas';
                    $cleanNo = preg_replace('/[^0-9]/', '', $notif->no_tujuan ?? '');
                    if (str_starts_with($cleanNo, '0')) $cleanNo = '62' . substr($cleanNo, 1);
                    $cleanNamaOrtu = str_replace([' (Fallback Wakasis)', '((', '))'], ['', '(', ')'], $notif->nama_ortu ?: ($isTargetGuru ? 'Pejabat Sekolah' : 'Orang Tua Siswa'));
                  @endphp
                  <tr style="border-bottom:1px solid var(--border);">
                    <td style="text-align:center; vertical-align:middle; padding:10px 8px;">
                      <input type="checkbox" name="ids[]" value="{{ $notif->id }}" class="notif-item-check" onchange="updateBatchBar()" style="cursor:pointer;" />
                    </td>
                    <td style="vertical-align:middle; padding:10px 12px;">
                      <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px; height:36px; border-radius:50%; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:800; color:var(--text); flex-shrink:0; font-size:12px;">
                          {{ substr($notif->siswa->nama ?? 'S', 0, 1) }}
                        </div>
                        <div>
                          <strong style="color:var(--text); font-size:13px; display:block;">{{ $notif->siswa->nama ?? 'Pemberitahuan Umum' }}</strong>
                          <div style="font-size:11px; color:var(--text); font-family:var(--font-mono); margin-top:2px;">
                            NIS: {{ $notif->siswa->nis ?? '-' }} · <strong style="color:var(--text);">{{ $notif->siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel?->nama_rombel ?? '-' }}</strong>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td style="vertical-align:middle; padding:10px 12px;">
                      <div>
                        <div style="font-size:12.5px; font-weight:800; color:var(--text); display:flex; align-items:center; gap:5px;">
                          <i class="bi {{ $isTargetGuru ? 'bi-person-badge-fill' : 'bi-person-fill' }}"></i>
                          <span>{{ $cleanNamaOrtu }}</span>
                        </div>

                        <div style="font-size:11px; font-family:var(--font-mono); margin-top:3px; white-space:nowrap;">
                          @if($notif->no_tujuan)
                            <a href="https://wa.me/{{ $cleanNo }}" target="_blank" style="color:var(--text); font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:4px; background:var(--bg-2); padding:2px 8px; border-radius:4px; border:1px solid var(--border-2);" title="Buka WhatsApp Langsung">
                              <i class="bi bi-whatsapp" style="color:var(--text);"></i> {{ $notif->no_tujuan }}
                            </a>
                          @else
                            <span style="color:var(--text); font-size:10.5px; font-weight:800;"><i class="bi bi-x-circle"></i> Tanpa No HP</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                      @if(str_contains($notif->kategori, 'eskalasi'))
                        <span class="pill-kategori pill-eskalasi" style="color:var(--text); font-weight:800; background:var(--bg-2); border:1px solid var(--border-2);"><i class="bi bi-bell-fill"></i> Eskalasi Disiplin</span>
                      @elseif(str_contains($notif->kategori, 'pemberitahuan_disiplin'))
                        <span class="pill-kategori pill-disiplin-ortu" style="color:var(--text); font-weight:800; background:var(--bg-2); border:1px solid var(--border-2);"><i class="bi bi-megaphone-fill"></i> Disiplin Ortu</span>
                      @elseif(str_contains($notif->kategori, 'pengingat_disiplin'))
                        <span class="pill-kategori pill-pengingat" style="color:var(--text); font-weight:800; background:var(--bg-2); border:1px solid var(--border-2);"><i class="bi bi-alarm-fill"></i> Pengingat Harian</span>
                      @elseif($notif->kategori === 'panggilan_ortu')
                        <span class="pill-kategori pill-panggilan" style="color:var(--text); font-weight:800; background:var(--bg-2); border:1px solid var(--border-2);"><i class="bi bi-telephone-outbound-fill"></i> Panggilan Ortu</span>
                      @elseif($notif->kategori === 'alpha')
                        <span class="pill-kategori" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:800;"><i class="bi bi-x-circle-fill"></i> Alpha</span>
                      @elseif($notif->kategori === 'terlambat')
                        <span class="pill-kategori" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:800;"><i class="bi bi-clock-history"></i> Terlambat</span>
                      @elseif($notif->kategori === 'bolos')
                        <span class="pill-kategori" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:800;"><i class="bi bi-door-open-fill"></i> Bolos</span>
                      @else
                        <span class="pill-kategori pill-presensi" style="color:var(--text); font-weight:800; background:var(--bg-2); border:1px solid var(--border-2);"><i class="bi bi-info-circle-fill"></i> {{ ucwords(str_replace('_', ' ', $notif->kategori)) }}</span>
                      @endif
                      <div style="font-size:10.5px; color:var(--text); font-weight:700; margin-top:3px; font-family:var(--font-mono);">{{ $notif->created_at->translatedFormat('d M Y') }}</div>
                    </td>
                    <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                      @if($notif->status === 'terkirim')
                        <span class="badge" style="background:var(--bg-2); color:var(--text); border:1px solid var(--border-2); font-weight:800; font-size:10.5px;">
                          <i class="bi bi-check2-all"></i> Terkirim
                        </span>
                        <div style="font-size:10.5px; color:var(--text); font-weight:700; margin-top:3px; font-family:var(--font-mono);">
                          {{ $notif->waktu_kirim ? $notif->waktu_kirim->format('H:i') . ' WIB' : $notif->created_at->format('H:i') . ' WIB' }}
                        </div>
                      @elseif($notif->status === 'pending')
                        <span class="badge" style="background:var(--bg-2); color:var(--text); border:1px solid var(--border-2); font-weight:800; font-size:10.5px;">
                          <i class="bi bi-hourglass-split"></i> Pending
                        </span>
                      @elseif($notif->status === 'dibatalkan')
                        <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-size:10.5px; font-weight:800;">
                          Dibatalkan
                        </span>
                      @else
                        <span class="badge" style="background:var(--bg-2); color:var(--text); border:1px solid var(--border-2); font-weight:800; font-size:10.5px;">
                          <i class="bi bi-x-circle-fill"></i> Gagal
                        </span>
                        @if($notif->catatan_error)
                          <div style="font-size:10px; color:var(--text); margin-top:2px; max-width:180px; word-break:break-word;">
                            {{ Str::limit($notif->catatan_error, 45) }}
                          </div>
                        @endif
                      @endif
                    </td>
                    <td style="vertical-align:middle; padding:10px 12px; font-size:11.5px; color:var(--text); white-space:nowrap;">
                      <div style="font-weight:800;">{{ $notif->diverifikasi_oleh ?: 'Sistem Otomatis' }}</div>
                      <div style="font-size:10.5px; font-family:var(--font-mono); font-weight:700; margin-top:2px;">{{ $notif->created_at->format('d/m H:i') }}</div>
                    </td>

                    {{-- AKSI CEPAT RELEVAN & KONTEKSTUAL --}}
                    <td style="vertical-align:middle; text-align:center; padding:10px 12px; white-space:nowrap;">
                      <div style="display:flex; gap:6px; justify-content:center; align-items:center; white-space:nowrap;">
                        
                        {{-- 1. JIKA STATUS PENDING: TOMBOL KIRIM & TOLAK --}}
                        @if($notif->status === 'pending')
                          <button type="button" class="btn btn-sm btn-gold" style="background:#22C55E; color:#fff; border:none; font-size:11px; padding:0 10px; height:30px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;" onclick="approveDirect({{ $notif->id }})" title="Setujui &amp; Kirim Sekarang">
                            <i class="bi bi-send-fill"></i> Kirim
                          </button>

                          <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 8px; height:30px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center;" onclick="previewPesanModal({{ json_encode($notif) }})" title="Pratinjau Pesan">
                            <i class="bi bi-eye"></i>
                          </button>

                          <button type="button" class="btn btn-sm btn-danger" style="font-size:11px; padding:0 8px; height:30px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center;" onclick="rejectDirect({{ $notif->id }})" title="Tolak / Batalkan Pesan">
                            <i class="bi bi-x-lg"></i>
                          </button>
                        
                        {{-- 2. JIKA STATUS TERKIRIM: BACA, CHAT WA, SURAT --}}
                        @elseif($notif->status === 'terkirim')
                          <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 10px; height:30px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap; background:var(--bg-2); border-color:var(--border-2); color:var(--text);" onclick="previewPesanModal({{ json_encode($notif) }})" title="Pratinjau Isi Pesan">
                            <i class="bi bi-eye-fill" style="color:var(--gold);"></i> Baca
                          </button>

                          @if($notif->siswa_id)
                            <a href="{{ route('surat.cetak', ['siswa_id' => $notif->siswa_id, 'kategori' => ($notif->kategori === 'panggilan_ortu' ? 'panggilan_ortu' : 'berita_acara')]) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 10px; height:30px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap; background:var(--bg-2); border-color:var(--border-2); color:var(--text);" title="Cetak Surat Fisik A4">
                              <i class="bi bi-printer-fill" style="color:var(--gold);"></i> Surat
                            </a>
                          @endif

                        {{-- 3. JIKA STATUS GAGAL: RETRY / KIRIM ULANG --}}
                        @elseif($notif->status === 'gagal')
                          <button type="button" class="btn btn-sm btn-gold" style="font-size:11px; padding:0 10px; height:30px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;" onclick="approveDirect({{ $notif->id }})" title="Kirim Ulang Pesan">
                            <i class="bi bi-arrow-repeat"></i> Kirim Ulang
                          </button>

                          <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:0 8px; height:30px; border-radius:6px; color:var(--red); display:inline-flex; align-items:center; justify-content:center;" onclick="previewPesanModal({{ json_encode($notif) }})" title="Lihat Catatan Error">
                            <i class="bi bi-exclamation-triangle"></i>
                          </button>

                        {{-- 4. JIKA STATUS DIBATALKAN --}}
                        @else
                          <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px;" onclick="previewPesanModal({{ json_encode($notif) }})" title="Pratinjau">
                            <i class="bi bi-eye"></i> Baca
                          </button>
                        @endif

                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-3);">
                      <i class="bi bi-chat-left-dots" style="font-size:36px; opacity:0.4;"></i>
                      <div style="font-weight:700; margin-top:8px; font-size:14px; color:var(--text);">Tidak ada antrean / riwayat notifikasi WhatsApp</div>
                      <p style="font-size:12px; margin-top:4px;">Semua pesan telah diproses atau filter pencarian tidak menemukan hasil.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($notifikasis->hasPages())
            <div style="padding:14px; border-top:1px solid var(--border); display:flex; justify-content:center;">
              {{ $notifikasis->links() }}
            </div>
          @endif
        </div>
      </form>

    @endif

  </main>
</div>

{{-- HIDDEN FORM UNTUK APPROVE / REJECT SATUAN --}}
<form id="formSingleAction" method="POST" action="" style="display:none;">
  @csrf
</form>

{{-- MODAL PRATINJAU PESAN (MODAL VIEW) --}}
<div class="modal-overlay" id="modalPreview">
  <div class="modal-card" style="max-width:500px; padding:20px; background:#0B141A; border:1px solid #2A3942; color:#E9EDEF;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid #2A3942; padding-bottom:10px;">
      <div style="display:flex; align-items:center; gap:8px;">
        <i class="bi bi-whatsapp" style="color:#22C55E; font-size:18px;"></i>
        <strong style="font-size:14px;" id="modalTargetLabel">Pratinjau Pesan WhatsApp</strong>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalPreview')" style="color:#8696A0; border-color:#2A3942;"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:#202C33; border-radius:8px; padding:10px 12px; margin-bottom:14px; font-size:11.5px;">
      <div>Penerima: <strong style="color:#E9EDEF;" id="modalPenerimaNama">-</strong></div>
      <div>No. WhatsApp: <strong style="color:#22C55E;" id="modalPenerimaNo">-</strong></div>
      <div>Kategori: <span style="color:#CA8A04;" id="modalKategoriLabel">-</span></div>
    </div>

    <div class="wa-bubble-green" id="modalBubbleText" style="max-width:100%; margin:0 auto;">
      ...
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:18px; border-top:1px solid #2A3942; padding-top:12px;">
      <button type="button" class="btn btn-outline" onclick="closeModal('modalPreview')" style="color:#8696A0; border-color:#2A3942;">Tutup</button>
      
      <div style="display:flex; gap:8px;" id="modalActionButtons">
        <a href="#" target="_blank" class="btn btn-gold" id="btnModalDirectWa" style="background:#22C55E; color:#fff; border:none;">
          <i class="bi bi-whatsapp"></i> Chat di WhatsApp
        </a>
      </div>
    </div>
  </div>
</div>

{{-- MODAL PENGATURAN GATEWAY & TEMPLATE --}}
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGuruPiket() || auth()->user()->isPiketHariIni()))
<div class="modal-overlay" id="modalPengaturan">
  <div class="modal-card" style="max-width:680px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
      <h3 style="font-size:18px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-gear-fill" style="color:var(--gold);"></i> Pengaturan Gateway &amp; Template WhatsApp
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalPengaturan')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('notifikasi.pengaturan.update') }}" method="POST">
      @csrf
      <div style="background:var(--bg-3); border-radius:var(--r-sm); padding:14px; margin-bottom:16px;">
        <div style="font-weight:800; font-size:13px; color:var(--gold); margin-bottom:10px;">
          <i class="bi bi-hdd-network"></i> Konfigurasi Mesin Gateway WhatsApp
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:10px;">
          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Provider Gateway</label>
            <select name="wa_provider" class="input-field" style="width:100%;">
              <option value="simulasi" {{ $setting->wa_provider === 'simulasi' ? 'selected' : '' }}>Mode Simulasi (Aman / Tanpa Kuota)</option>
              <option value="fonnte" {{ $setting->wa_provider === 'fonnte' ? 'selected' : '' }}>Fonnte (Rekomendasi)</option>
              <option value="wablas" {{ $setting->wa_provider === 'wablas' ? 'selected' : '' }}>Wablas</option>
              <option value="generic_api" {{ $setting->wa_provider === 'generic_api' ? 'selected' : '' }}>Custom REST API</option>
            </select>
          </div>

          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Status Gateway</label>
            <select name="is_active" class="input-field" style="width:100%;">
              <option value="1" {{ $setting->is_active ? 'selected' : '' }}>🟢 Aktif (Live Dispatch)</option>
              <option value="0" {{ !$setting->is_active ? 'selected' : '' }}>🔴 Nonaktif / Simulasi</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom:10px;">
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">API Token / API Key</label>
          <input type="text" name="wa_api_token" class="input-field" value="{{ $setting->wa_api_token }}" placeholder="Token dari dashboard provider..." style="width:100%;" />
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Endpoint URL (Opsional)</label>
          <input type="text" name="wa_endpoint_url" class="input-field" value="{{ $setting->wa_endpoint_url }}" placeholder="https://api.fonnte.com/send" style="width:100%;" />
        </div>
      </div>

      <div style="background:var(--bg-3); border-radius:var(--r-sm); padding:14px; margin-bottom:16px;">
        <div style="font-weight:800; font-size:13px; color:var(--gold); margin-bottom:10px;">
          <i class="bi bi-sliders"></i> Ketentuan Batas Pelanggaran (Panggilan Otomatis)
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Ambang Batas Alpha (Panggilan Ortu)</label>
            <input type="number" name="ambang_batas_alpha" class="input-field" value="{{ $setting->ambang_batas_alpha ?? 3 }}" min="1" max="10" style="width:100%;" />
          </div>

          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; display:block; margin-bottom:4px;">Hitung Bolos Bersama Alpha?</label>
            <select name="hitung_bolos_bersama_alpha" class="input-field" style="width:100%;">
              <option value="1" {{ $setting->hitung_bolos_bersama_alpha ? 'selected' : '' }}>Ya (Alpha + Bolos)</option>
              <option value="0" {{ !$setting->hitung_bolos_bersama_alpha ? 'selected' : '' }}>Hanya Alpha Saja</option>
            </select>
          </div>
        </div>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Template Pesan: Alpha (Ketidakhadiran)</label>
        <textarea name="template_alpha" class="input-field" rows="3" style="width:100%;">{{ $setting->template_alpha }}</textarea>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Template Pesan: Keterlambatan</label>
        <textarea name="template_terlambat" class="input-field" rows="3" style="width:100%;">{{ $setting->template_terlambat }}</textarea>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Template Pesan: Peringatan Wali Kelas</label>
        <textarea name="template_wali_kelas" class="input-field" rows="3" style="width:100%;">{{ $setting->template_wali_kelas }}</textarea>
      </div>

      <div style="display:none;">
        <input type="hidden" name="template_izin" value="{{ $setting->template_izin }}" />
        <input type="hidden" name="template_sakit" value="{{ $setting->template_sakit }}" />
        <input type="hidden" name="template_bolos" value="{{ $setting->template_bolos }}" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:18px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalPengaturan')">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Pengaturan</button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- SCRIPT INTERAKSI --}}
<script>
  function switchViewMode(mode) {
    const url = new URL(window.location.href);
    url.searchParams.set('view_mode', mode);
    window.location.href = url.toString();
  }

  // ─── Notif KPI Card & Tab Click Logic ───────────────────────
  var notifKpiConfig = {
    'semua'    : { title: 'Semua Riwayat Notifikasi', icon: 'bi-grid-fill', color: 'var(--gold)', cardId: null },
    'pending'  : { title: 'Antrean Pending Verifikasi', icon: 'bi-hourglass-split', color: '#CA8A04', cardId: 'kpiNotifPending' },
    'terkirim' : { title: 'Terkirim Hari Ini', icon: 'bi-send-check-fill', color: '#16A34A', cardId: 'kpiNotifTerkirim' },
    'gagal'    : { title: 'Gagal Kirim (Perlu Koreksi)', icon: 'bi-exclamation-octagon-fill', color: '#DC2626', cardId: 'kpiNotifGagal' },
  };
  var _notifActiveFilter = null;

  function showNotifTable(status, cardEl, tabEl) {
    var cfg = notifKpiConfig[status] || notifKpiConfig['semua'];
    var panel = document.getElementById('notifTableSection');

    // Toggle: klik kartu yang sama → tutup
    if (_notifActiveFilter === status && !tabEl) {
      closeNotifTable();
      return;
    }
    _notifActiveFilter = status;

    // Reset semua highlight KPI cards
    document.querySelectorAll('.notif-kpi-card').forEach(function(c) {
      c.style.outline   = '2px solid transparent';
      c.style.transform = 'translateY(0)';
      c.style.boxShadow = '';
    });
    // Highlight card yang diklik
    if (cardEl) {
      cardEl.style.outline   = '2px solid ' + cfg.color;
      cardEl.style.transform = 'translateY(-3px)';
      cardEl.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
    }
    // Sync KPI card highlight jika klik dari tab
    if (tabEl && cfg.cardId) {
      var kpiCard = document.getElementById(cfg.cardId);
      if (kpiCard) {
        kpiCard.style.outline   = '2px solid ' + cfg.color;
        kpiCard.style.transform = 'translateY(-3px)';
      }
    }

    // Update tab aktif
    document.querySelectorAll('.notif-tab-item').forEach(function(t) {
      t.classList.remove('active');
    });
    if (tabEl) { tabEl.classList.add('active'); }

    // Update ikon & judul header tabel
    var icon = document.getElementById('notifTableIcon');
    var title = document.getElementById('notifTableTitle');
    if (icon) { icon.className = 'bi ' + cfg.icon; icon.style.color = cfg.color; }
    if (title) { title.textContent = cfg.title; }

    // Reveal panel dengan animasi
    if (panel) {
      panel.style.display = 'block';
      panel.style.opacity = '0';
      panel.style.transform = 'translateY(8px)';
      requestAnimationFrame(function() {
        panel.style.transition = 'opacity .3s ease, transform .3s ease';
        panel.style.opacity   = '1';
        panel.style.transform = 'translateY(0)';
      });
      // Scroll ke panel
      setTimeout(function() {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 100);
    }

    // Submit filter jika perlu filter data dari server (status berubah)
    var currentStatus = document.getElementById('filterStatusInput');
    if (currentStatus && currentStatus.value !== status) {
      currentStatus.value = status;
      // Delay untuk memastikan animasi terjadi dulu
      setTimeout(function() {
        document.getElementById('filterForm').submit();
      }, 250);
    }
  }

  function closeNotifTable() {
    var panel = document.getElementById('notifTableSection');
    if (panel) {
      panel.style.transition = 'opacity .25s ease, transform .25s ease';
      panel.style.opacity   = '0';
      panel.style.transform = 'translateY(8px)';
      setTimeout(function() { panel.style.display = 'none'; }, 260);
    }
    _notifActiveFilter = null;
    document.querySelectorAll('.notif-kpi-card').forEach(function(c) {
      c.style.outline   = '2px solid transparent';
      c.style.transform = 'translateY(0)';
      c.style.boxShadow = '';
    });
  }

  // Sembunyikan tabel saat load (kecuali jika ada filter/status aktif dari URL)
  (function initNotifTable() {
    var panel = document.getElementById('notifTableSection');
    if (!panel) return;
    var urlParams = new URLSearchParams(window.location.search);
    var hasFilter = urlParams.get('status') || urlParams.get('q') || urlParams.get('kategori') || urlParams.get('tanggal') || urlParams.get('rombel_id');
    if (hasFilter) {
      // Ada filter aktif dari URL → tampilkan tabel & highlight KPI yang sesuai
      panel.style.display = 'block';
      var activeStatus = urlParams.get('status') || 'semua';
      _notifActiveFilter = activeStatus;
      var cfg = notifKpiConfig[activeStatus] || notifKpiConfig['semua'];
      if (cfg.cardId) {
        var card = document.getElementById(cfg.cardId);
        if (card) {
          card.style.outline = '2px solid ' + cfg.color;
          card.style.transform = 'translateY(-3px)';
        }
      }
      var icon = document.getElementById('notifTableIcon');
      var title = document.getElementById('notifTableTitle');
      if (icon) { icon.className = 'bi ' + cfg.icon; icon.style.color = cfg.color; }
      if (title) { title.textContent = cfg.title; }
    } else {
      // Tidak ada filter → sembunyikan tabel
      panel.style.display = 'none';
    }
  })();

  // Legacy alias
  function filterTabStatus(status) {
    showNotifTable(status, null);
  }

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

  function selectChatItem(notif, el) {
    document.querySelectorAll('.chat-list-item').forEach(item => item.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('chatTargetName').innerText = notif.nama_ortu || (notif.siswa ? notif.siswa.nama : 'Penerima');
    document.getElementById('chatTargetPhone').innerHTML = `<i class="bi bi-telephone-fill"></i> ${notif.no_tujuan || '-'} • Siswa: <strong style="color:#E9EDEF;">${notif.siswa ? notif.siswa.nama : '-'}</strong>`;
    document.getElementById('chatBubbleText').innerHTML = `${notif.pesan}<div class="wa-bubble-time"><span>${notif.created_at || ''}</span><i class="bi bi-check2-all" style="color:#53BDEB;"></i></div>`;
    
    if (notif.no_tujuan) {
      let clean = notif.no_tujuan.replace(/[^0-9]/g, '');
      if (clean.startsWith('0')) clean = '62' + clean.substr(1);
      const btn = document.getElementById('btnWaDirect');
      if (btn) btn.href = 'https://wa.me/' + clean;
    }
  }

  function previewPesanModal(notif) {
    document.getElementById('modalPenerimaNama').innerText = notif.nama_ortu || (notif.siswa ? notif.siswa.nama : '-');
    document.getElementById('modalPenerimaNo').innerText = notif.no_tujuan || '(Tidak ada nomor)';
    document.getElementById('modalKategoriLabel').innerText = (notif.kategori || '').replace(/_/g, ' ').toUpperCase();
    document.getElementById('modalBubbleText').innerText = notif.pesan || '';

    let clean = (notif.no_tujuan || '').replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) clean = '62' + clean.substr(1);
    document.getElementById('btnModalDirectWa').href = clean ? 'https://wa.me/' + clean : '#';

    openModal('modalPreview');
  }

  function openModal(id) { document.getElementById(id).classList.add('active'); }
  function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>

</body>
</html>
