<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Siklus Akademik &amp; Transisi Siswa — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* ─── Custom Page Styling & Typography ─── */
    .page-hero {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 22px 24px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      box-shadow: var(--shadow-sm);
    }
    .page-hero-title {
      font-size: 20px;
      font-weight: 900;
      color: var(--text);
      letter-spacing: -0.02em;
      display: flex;
      align-items: center;
      gap: 10px;
      line-height: 1.2;
    }
    .page-hero-sub {
      font-size: 13px;
      color: var(--text-2);
      margin-top: 4px;
      font-weight: 500;
    }

    /* ─── Segmented Tab Switcher ─── */
    .segmented-control {
      display: inline-flex;
      background: var(--bg-3);
      padding: 4px;
      border-radius: var(--r-sm);
      border: 1px solid var(--border-2);
      gap: 4px;
    }
    .segmented-btn {
      background: transparent;
      border: none;
      color: var(--text-2);
      padding: 7px 16px;
      border-radius: 4px;
      font-family: var(--font);
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .segmented-btn:hover {
      color: var(--text);
      background: rgba(0, 0, 0, 0.05);
    }
    .segmented-btn.active {
      background: #000000 !important;
      color: #FFFFFF !important;
      font-weight: 800;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    .segmented-btn.active i {
      color: #FFFFFF !important;
    }

    /* ─── Step Cards & Containers ─── */
    .step-card {
      background: var(--bg-card);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 16px 18px;
      transition: border-color .2s ease, box-shadow .2s ease;
    }
    .step-card:focus-within, .step-card:hover {
      border-color: #000000;
    }
    .step-badge {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #000000;
      color: #FFFFFF;
      font-size: 11px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
      flex-shrink: 0;
    }
    .step-label {
      font-size: 13px;
      font-weight: 800;
      color: var(--text);
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      letter-spacing: -0.01em;
    }

    /* ─── Tahun Ajaran Card ─── */
    .ta-active-box {
      background: var(--bg-card);
      border: 1.5px solid #000000;
      padding: 16px 18px;
      border-radius: var(--r-md);
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
      box-shadow: var(--shadow-sm);
    }
    .ta-chip {
      background: var(--bg-card);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 6px 12px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s ease;
    }
    .ta-chip.is-active {
      border-color: #000000;
      background: #000000;
      color: #FFFFFF;
    }
    .ta-chip.is-active strong {
      color: #FFFFFF !important;
    }
    .ta-chip:hover {
      border-color: #000000;
    }

    /* ─── Typography Helpers ─── */
    .font-mono { font-family: var(--font-mono); }
    .label-uppercase {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-3);
      font-family: var(--font-mono);
      margin-bottom: 8px;
    }
    .histori-pill {
      font-family: var(--font-mono);
      font-size: 11px;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 4px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    /* ─── Standard Buttons ─── */
    .btn-black {
      background: #000000 !important;
      color: #FFFFFF !important;
      border: 1px solid #000000 !important;
      font-weight: 800;
      transition: all .15s ease;
    }
    .btn-black:hover {
      background: #222222 !important;
      border-color: #222222 !important;
      transform: translateY(-1px);
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 768px) {
      .step-card {
        padding: 10px 12px !important;
      }
      .step-label {
        font-size: 12px !important;
        margin-bottom: 6px !important;
      }
      .step-badge {
        width: 18px !important;
        height: 18px !important;
        font-size: 10px !important;
        margin-right: 6px !important;
      }
      .ta-chip-container {
        display: flex !important;
        gap: 6px !important;
        overflow-x: auto !important;
        flex-wrap: nowrap !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: none !important;
        padding-bottom: 2px !important;
        width: 100% !important;
      }
      .ta-chip-container::-webkit-scrollbar {
        display: none !important;
      }
      .ta-chip-container .btn, .ta-chip-container button, .ta-chip-container span, .ta-chip-container form {
        flex-shrink: 0 !important;
      }

      /* Toolbar Mobile Responsive */
      .siklus-table-toolbar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .siklus-table-title {
        width: 100% !important;
      }
      .siklus-table-form {
        width: 100% !important;
        max-width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .siklus-search-box {
        width: 100% !important;
      }
      .siklus-filter-group {
        width: 100% !important;
        display: flex !important;
        gap: 4px !important;
      }
      .siklus-filter-group select {
        font-size: 10.5px !important;
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
            <i class="bi bi-arrow-repeat" style="color:#000000; font-size:16px;"></i> Siklus Akademik &amp; Transisi Siswa
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Kenaikan kelas, kelulusan, &amp; penugasan PKL
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill" style="margin-right:8px; font-size:16px;"></i>
        <span style="font-weight:700;">{{ session('success') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:8px; font-size:16px;"></i>
        <span style="font-weight:700;">{{ session('error') }}</span>
      </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 1: PENGATURAN PERIODE TAHUN AJARAN -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="margin-bottom:14px; padding:10px 14px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <div style="font-size:13.5px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:6px;">
            <i class="bi bi-calendar-range-fill" style="color:#000000; font-size:14px;"></i>
            <span>Pengaturan Periode Tahun Ajaran</span>
          </div>
          <div style="font-size:11.5px; color:var(--text-3); margin-top:2px;">
            Tahun ajaran aktif saat ini: 
            @php $activeTa = $tahunAjarans->firstWhere('is_active', true); @endphp
            <strong style="color:var(--text); font-family:var(--font-mono); font-size:12px;">{{ $activeTa ? $activeTa->nama : '-' }}</strong>
          </div>
        </div>

        <div class="ta-chip-container" style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          {{-- Tombol Periode Tahun Ajaran --}}
          @foreach($tahunAjarans as $ta)
            @if($ta->is_active)
              <span class="btn" style="background:#000000; color:#FFFFFF; border:1.5px solid #000000; padding:5px 12px; font-size:11.5px; font-weight:800; font-family:var(--font-mono); border-radius:var(--r-sm); display:inline-flex; align-items:center; gap:5px; cursor:default;">
                <span style="background:#22C55E; width:6px; height:6px; border-radius:50%; display:inline-block;"></span>
                {{ $ta->nama }} (Aktif)
              </span>
            @else
              <form action="/tahun-ajaran/{{ $ta->id }}/aktifkan" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Pindahkan tahun ajaran aktif ke {{ $ta->nama }}?')">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding:5px 10px; font-size:11.5px; font-family:var(--font-mono); font-weight:700; border-radius:var(--r-sm);" title="Klik untuk mengaktifkan tahun ajaran ini">
                  {{ $ta->nama }}
                </button>
              </form>
            @endif
          @endforeach

          {{-- Tombol Tambah Periode Baru --}}
          <button type="button" class="btn btn-outline" onclick="openModalTa('modalTambahTa')" style="padding:5px 12px; font-size:11.5px; font-weight:800; border-radius:var(--r-sm); border-color:#000000; color:#000000; display:inline-flex; align-items:center; gap:4px; cursor:pointer;" title="Tambah Tahun Ajaran Baru">
            + Tambah Periode
          </button>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 2: PANEL AKSI TRANSISI AKADEMIK -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="margin-bottom:14px; padding:12px 14px;">
      <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:10px; margin-bottom:14px;">
        <div style="font-size:13.5px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-sliders" style="color:#000000; font-size:15px;"></i>
          <span>Aksi Transisi Akademik Siswa</span>
        </div>

        {{-- Tab Switcher --}}
        <div class="segmented-control">
          <button type="button" id="tabBtnMassal" class="segmented-btn active" onclick="switchTransisiTab('massal')" style="padding:4px 10px; font-size:11.5px;">
            <i class="bi bi-collection-fill"></i> Aksi Massal
          </button>
          <button type="button" id="tabBtnIndividu" class="segmented-btn" onclick="switchTransisiTab('individu')" style="padding:4px 10px; font-size:11.5px;">
            <i class="bi bi-person-fill"></i> Perorangan
          </button>
        </div>
      </div>

      {{-- FORM 1: AKSI MASSAL PER KELAS / ROMBEL --}}
      <div id="panelFormMassal">
        <form action="/siklus-siswa/transisi-massal" method="POST" onsubmit="return confirmTransisiMassal()">
          @csrf
          
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:10px; margin-bottom:10px;">
            <!-- Langkah 1: Pilih Rombel Asal -->
            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">1</span> Rombel / Kelas Asal <span style="color:var(--red); margin-left:3px;">*</span>
              </label>
              <select name="rombel_asal_id" id="massal_rombel_asal" required class="input-field" style="width:100%; height:34px; font-size:12px; font-weight:700;">
                <option value="">-- Pilih Rombel Asal --</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">
                    {{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }}) — [{{ $r->siswa_rombels_count ?? 0 }} Siswa]
                  </option>
                @endforeach
              </select>
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px; font-weight:500;">
                Seluruh siswa aktif di rombel ini akan diproses.
              </div>
            </div>

            <!-- Langkah 2: Jenis Tindakan -->
            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">2</span> Jenis Tindakan Massal <span style="color:var(--red); margin-left:3px;">*</span>
              </label>
              <select name="aksi_massal" id="massal_aksi" required class="input-field" style="width:100%; height:34px; font-size:12px; font-weight:800;" onchange="toggleMassalFields()">
                <option value="naik_kelas">Naik Kelas Massal (Pindah ke Rombel Baru)</option>
                <option value="lulus">Kelulusan Massal (Nonaktifkan Akun Siswa)</option>
                <option value="tinggal_kelas">Tinggal Kelas Massal (Tahun Ajaran Baru)</option>
                <option value="mulai_pkl">Tugaskan PKL Massal (Bebas Evaluasi Alpha)</option>
                <option value="selesai_pkl">Selesai PKL Massal (Kembali Aktif di Sekolah)</option>
              </select>
              <div id="massal_aksi_desc" style="font-size:10.5px; color:var(--text-2); margin-top:4px; font-weight:600;">
                Memindahkan rombel anggota ke tingkat/kelas lanjutan.
              </div>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:10px; margin-bottom:14px;">
            <!-- Langkah 3: Rombel Tujuan -->
            <div class="step-card" id="massal_group_tujuan">
              <label id="massal_label_tujuan" class="step-label">
                <span class="step-badge">3</span> Rombel / Kelas Tujuan <span style="color:var(--red); margin-left:3px;">*</span>
              </label>
              <select name="rombel_tujuan_id" id="massal_rombel_tujuan" class="input-field" style="width:100%; height:34px; font-size:12px; font-weight:700;">
                <option value="">-- Pilih Rombel Tujuan --</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">{{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }})</option>
                @endforeach
              </select>
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px; font-weight:500;">
                Rombel tujuan yang akan menampung anggota baru.
              </div>
            </div>

            <!-- Langkah 4: Tahun Ajaran Baru -->
            <div class="step-card" id="massal_group_ta">
              <label id="massal_label_ta" class="step-label">
                <span class="step-badge">4</span> Berlaku Mulai Tahun Ajaran <span style="color:var(--red); margin-left:3px;">*</span>
              </label>
              <select name="tahun_ajaran_baru_id" id="massal_ta_baru" class="input-field font-mono" style="width:100%; height:34px; font-size:12px; font-weight:700;">
                @foreach($tahunAjarans as $ta)
                  <option value="{{ $ta->id }}" @if($ta->is_active) selected @endif>
                    {{ $ta->nama }} @if($ta->is_active)(Aktif Saat Ini)@endif
                  </option>
                @endforeach
              </select>
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px; font-weight:500;">
                Periode akademik berlakunya status baru ini.
              </div>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-black" style="height:36px; padding:0 18px; display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:800; border-radius:var(--r-sm);">
              <i class="bi bi-arrow-repeat" style="font-size:14px;"></i> Eksekusi Transisi Massal
            </button>
          </div>
        </form>
      </div>

      {{-- FORM 2: AKSI PERORANGAN / INDIVIDU --}}
      <div id="panelFormIndividu" style="display:none;">
        <form action="/siklus-siswa/transisi" method="POST">
          @csrf
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:10px; margin-bottom:10px;">
            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">1</span> Pilih Siswa <span style="color:var(--red); margin-left:3px;">*</span>
              </label>
              <select name="siswa_id" id="select_siswa_id" required class="input-field" style="width:100%; height:34px; font-size:12px; font-weight:700;">
                <option value="">-- Cari / Pilih Siswa --</option>
                @foreach($allSiswas as $s)
                  @php $srAktif = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif'); @endphp
                  <option value="{{ $s->id }}" data-status="{{ $s->status }}">
                    {{ $s->nama }} (NISN: {{ $s->nisn ?: '-' }}) — [{{ $srAktif->rombel->nama_rombel ?? 'Tanpa Rombel' }}] ({{ strtoupper($s->status) }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">2</span> Jenis Transisi <span style="color:var(--red); margin-left:3px;">*</span>
              </label>
              <select name="jenis" id="select_jenis" required class="input-field" style="width:100%; height:34px; font-size:12px; font-weight:800;" onchange="toggleTransisiFields()">
                <option value="naik_kelas">Naik Kelas (Ke Rombel Baru)</option>
                <option value="tinggal_kelas">Tinggal Kelas (Tahun Ajaran Baru)</option>
                <option value="mulai_pkl">Mulai PKL (Bebas Absensi Sekolah)</option>
                <option value="selesai_pkl">Selesai PKL (Kembali Aktif di Sekolah)</option>
                <option value="lulus">Lulus (Nonaktifkan Akun Siswa)</option>
                <option value="pindah">Pindah Sekolah (Nonaktifkan Siswa)</option>
                <option value="keluar">Keluar / Dikeluarkan (Nonaktifkan Siswa)</option>
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:10px; margin-bottom:14px;">
            <div class="step-card" id="group_rombel_tujuan">
              <label id="label_rombel_tujuan" class="step-label">
                <span class="step-badge">3</span> Rombel Tujuan
              </label>
              <select name="rombel_tujuan_id" id="select_rombel_tujuan" class="input-field" style="width:100%; height:34px; font-size:12px; font-weight:700;">
                <option value="">-- Pilih Rombel Tujuan --</option>
                @foreach($rombels as $r)<option value="{{ $r->id }}">{{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }})</option>@endforeach
              </select>
            </div>

            <div class="step-card" id="group_ta_baru">
              <label id="label_ta_baru" class="step-label">
                <span class="step-badge">4</span> Tahun Ajaran Baru
              </label>
              <select name="tahun_ajaran_baru_id" id="select_ta_baru" class="input-field font-mono" style="width:100%; height:34px; font-size:12px; font-weight:700;">
                @foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @if($ta->is_active) selected @endif>{{ $ta->nama }} @if($ta->is_active)(Aktif)@endif</option>@endforeach
              </select>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-black" style="height:36px; padding:0 18px; display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:800; border-radius:var(--r-sm);">
              <i class="bi bi-check2-circle" style="font-size:14px;"></i> Simpan Transisi Siswa
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 3: TABEL HISTORI KEANGGOTAAN ROMBEL & STATUS SISWA -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      
      {{-- Header & Toolbar Terpadu --}}
      <div class="siklus-table-toolbar" style="padding:10px 14px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        
        {{-- Judul --}}
        <div class="siklus-table-title" style="font-weight:800; font-size:13px; color:var(--text); display:flex; align-items:center; gap:6px; flex-shrink:0;">
          <i class="bi bi-people-fill" style="color:#000000;"></i>
          <span>Histori Siswa</span>
          <span style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--text-2); font-size:10.5px; font-weight:700; padding:1px 7px; border-radius:4px; font-family:var(--font-mono);">
            {{ $siswas->total() }}
          </span>
        </div>

        {{-- Form Filter & Search --}}
        <form method="GET" action="{{ route('siklus-siswa.index') }}" style="display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
          
          {{-- Search Box --}}
          <div style="position:relative; min-width:180px; flex:1;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px; pointer-events:none;"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, NIS..." class="input-field"
              style="width:100%; height:33px; font-size:11.5px; padding-left:30px; padding-right:8px; border-radius:6px;" />
          </div>

          {{-- Filter Kelas --}}
          <select name="rombel_id" class="input-field" style="height:33px; font-size:11.5px; padding:0 8px; border-radius:6px; min-width:105px;" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
            @endforeach
          </select>

          {{-- Filter Status --}}
          <select name="status" class="input-field" style="height:33px; font-size:11.5px; padding:0 8px; border-radius:6px; min-width:90px;" onchange="this.form.submit()">
            <option value="">Status</option>
            <option value="aktif"  {{ $status === 'aktif'  ? 'selected' : '' }}>Aktif</option>
            <option value="pkl"    {{ $status === 'pkl'    ? 'selected' : '' }}>PKL</option>
            <option value="lulus"  {{ $status === 'lulus'  ? 'selected' : '' }}>Lulus</option>
            <option value="pindah" {{ $status === 'pindah' ? 'selected' : '' }}>Pindah</option>
            <option value="keluar" {{ $status === 'keluar' ? 'selected' : '' }}>Keluar</option>
          </select>

          {{-- Filter Urutan --}}
          <select name="sort" class="input-field" style="height:33px; font-size:11.5px; padding:0 8px; border-radius:6px; min-width:105px;" onchange="this.form.submit()">
            <option value="nama_asc"  {{ $sort === 'nama_asc'  ? 'selected' : '' }}>Nama (A-Z)</option>
            <option value="nama_desc" {{ $sort === 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
            <option value="nis_asc"   {{ $sort === 'nis_asc'   ? 'selected' : '' }}>NIS (Kecil)</option>
            <option value="terbaru"   {{ $sort === 'terbaru'   ? 'selected' : '' }}>Terbaru</option>
          </select>

          {{-- Tombol Aksi --}}
          <div style="display:flex; gap:5px; align-items:center; flex-shrink:0;">
            <button type="submit" class="btn btn-sm btn-black"
              style="height:33px; padding:0 14px; font-size:11.5px; font-weight:800; border-radius:6px; white-space:nowrap;">
              Filter
            </button>
            @if($search || $rombelId || $status || ($sort && $sort !== 'nama_asc'))
              <a href="{{ route('siklus-siswa.index') }}"
                style="height:33px; padding:0 12px; font-size:11.5px; font-weight:800; border-radius:6px; border:1.5px solid rgba(239,68,68,0.5); color:#ef4444; background:transparent; display:inline-flex; align-items:center; white-space:nowrap; text-decoration:none;"
                title="Reset Filter">
                Reset
              </a>
            @endif
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:44px; text-align:center;">No</th>
              <th style="width:48px; text-align:center;">Foto</th>
              <th style="width:110px; text-align:center;">NISN</th>
              <th style="text-align:left;">Nama Lengkap Siswa</th>
              <th style="width:100px; text-align:center;">Status</th>
              <th style="width:120px; text-align:center;">Rombel Aktif</th>
              <th style="width:110px; text-align:center;">Tahun Ajaran</th>
              <th style="min-width:260px; text-align:left;">Histori Siklus Perjalanan Kelas</th>
              <th style="width:90px; text-align:center;" class="no-print">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($siswas as $idx => $s)
              @php
                $sr = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif');
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">
                  {{ $siswas->firstItem() + $idx }}
                </td>
                <td style="text-align:center; vertical-align:middle;">
                  <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid var(--border-2);" />
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-weight:800; color:var(--text); font-size:12.5px;">
                  {{ $s->nisn ?: '-' }}
                </td>
                <td style="text-align:left;">
                  <strong style="color:var(--text); font-size:13.5px; font-weight:800;">{{ $s->nama }}</strong>
                </td>
                <td style="text-align:center;">
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
                <td style="text-align:center; font-weight:800; color:var(--text); font-size:13px;">
                  {{ $sr->rombel->nama_rombel ?? '-' }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text-2);">
                  {{ $sr->tahunAjaran->nama ?? '-' }}
                </td>
                <td style="text-align:left;">
                  <div style="display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
                    @forelse($s->siswaRombels as $hist)
                      @php
                        $histColor = match($hist->status_keanggotaan) {
                          'aktif' => 'color:#000000; font-weight:800;',
                          'naik' => 'color:var(--text); font-weight:700;',
                          'tinggal' => 'color:var(--text-2); font-weight:600;',
                          'lulus' => 'color:var(--text); font-weight:700;',
                          default => 'color:var(--text-3); font-weight:600;',
                        };
                      @endphp
                      <span style="font-family:var(--font-mono); font-size:12px; {{ $histColor }}">
                        {{ $hist->rombel->nama_rombel ?? 'Rombel' }} <span style="font-size:11px; opacity:0.75;">({{ ucfirst($hist->status_keanggotaan) }})</span>
                      </span>
                      @if(!$loop->last)
                        <span style="color:var(--border-2); font-size:11px; margin:0 2px;">•</span>
                      @endif
                    @empty
                      <span style="color:var(--text-3); font-size:12px;">-</span>
                    @endforelse
                  </div>
                </td>
                <td style="text-align:center;" class="no-print">
                  <div style="display:flex; gap:6px; justify-content:center;">
                    <button type="button" class="btn btn-sm" onclick="pilihSiswaTransisi({{ $s->id }})" style="font-size:13px; padding:4px 6px; font-weight:800; color:var(--text); border:none; background:transparent; box-shadow:none; cursor:pointer;" title="Proses Transisi Siswa Ini">
                      <i class="bi bi-arrow-left-right"></i>
                    </button>
                    <a href="/portal-siswa/{{ $s->nisn ?: $s->id }}" target="_blank" class="btn btn-sm" style="font-size:13px; padding:4px 6px; font-weight:800; color:var(--text); border:none; background:transparent; box-shadow:none; text-decoration:none;" title="Portal Rekap Siswa">
                      <i class="bi bi-person-lines-fill"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align:center; padding:40px; color:var(--text-3);">
                  <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:10px; opacity:.5;"></i>
                  <span style="font-size:13.5px; font-weight:600;">Tidak ada data siswa ditemukan untuk kriteria pencarian ini.</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($siswas->hasPages())
        <div style="padding:16px 20px; border-top:1px solid var(--border-2); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="font-size:12px; color:var(--text-3); font-weight:600;">
            Menampilkan {{ $siswas->firstItem() }} s/d {{ $siswas->lastItem() }} dari {{ $siswas->total() }} data
          </div>
          <div>
            {{ $siswas->links() }}
          </div>
        </div>
      @endif
    </div>
  </main>
</div>

<script>
  function switchTransisiTab(mode) {
    const btnMassal = document.getElementById('tabBtnMassal');
    const btnIndividu = document.getElementById('tabBtnIndividu');
    const panelMassal = document.getElementById('panelFormMassal');
    const panelIndividu = document.getElementById('panelFormIndividu');

    if (mode === 'massal') {
      btnMassal.classList.add('active');
      btnIndividu.classList.remove('active');
      panelMassal.style.display = 'block';
      panelIndividu.style.display = 'none';
    } else {
      btnIndividu.classList.add('active');
      btnMassal.classList.remove('active');
      panelIndividu.style.display = 'block';
      panelMassal.style.display = 'none';
    }
  }

  function toggleMassalFields() {
    const aksi = document.getElementById('massal_aksi').value;
    const selectTujuan = document.getElementById('massal_rombel_tujuan');
    const selectTa = document.getElementById('massal_ta_baru');
    const labelTujuan = document.getElementById('massal_label_tujuan');
    const labelTa = document.getElementById('massal_label_ta');
    const desc = document.getElementById('massal_aksi_desc');

    if (aksi === 'naik_kelas') {
      desc.textContent = 'Memindahkan rombel anggota ke tingkat/kelas lanjutan.';
    } else if (aksi === 'lulus') {
      desc.textContent = 'Menetapkan kelulusan dan otomatis menonaktifkan status siswa.';
    } else if (aksi === 'tinggal_kelas') {
      desc.textContent = 'Mempertahankan tingkat kelas siswa di tahun ajaran baru.';
    } else if (aksi === 'mulai_pkl') {
      desc.textContent = 'Menugaskan PKL (bebas dari evaluasi alpha otomatis gerbang sekolah).';
    } else if (aksi === 'selesai_pkl') {
      desc.textContent = 'Menyelesaikan penugasan PKL (kembali aktif absensi di sekolah).';
    }

    if (['lulus', 'mulai_pkl', 'selesai_pkl'].includes(aksi)) {
      selectTujuan.disabled = true;
      selectTa.disabled = true;
      selectTujuan.style.opacity = '0.4';
      selectTa.style.opacity = '0.4';
      if (labelTujuan) labelTujuan.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
      if (labelTa) labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
    } else {
      selectTujuan.disabled = false;
      selectTa.disabled = false;
      selectTujuan.style.opacity = '1';
      selectTa.style.opacity = '1';
      if (labelTujuan) labelTujuan.innerHTML = '<span class="step-badge">3</span> Rombel / Kelas Tujuan <span style="color:var(--red); margin-left:4px;">*</span>';
      if (labelTa) labelTa.innerHTML = '<span class="step-badge">4</span> Berlaku Mulai Tahun Ajaran <span style="color:var(--red); margin-left:4px;">*</span>';
    }
  }

  function confirmTransisiMassal() {
    const rombelSelect = document.getElementById('massal_rombel_asal');
    const rombelText = rombelSelect.options[rombelSelect.selectedIndex]?.text || '';
    const aksi = document.getElementById('massal_aksi').value;

    let aksiName = 'Naik Kelas';
    if (aksi === 'lulus') aksiName = 'Kelulusan Massal (Nonaktifkan Siswa)';
    if (aksi === 'tinggal_kelas') aksiName = 'Tinggal Kelas Massal';
    if (aksi === 'mulai_pkl') aksiName = 'Penugasan PKL Massal (Bebas Absensi)';
    if (aksi === 'selesai_pkl') aksiName = 'Selesai PKL Massal (Kembali Aktif)';

    return confirm(`Apakah Anda yakin ingin memproses ${aksiName} secara MASSAL untuk seluruh siswa di [${rombelText}]?`);
  }

  function toggleTransisiFields() {
    const jenis = document.getElementById('select_jenis').value;
    const rombelTujuan = document.getElementById('select_rombel_tujuan');
    const taBaru = document.getElementById('select_ta_baru');
    const labelRombel = document.getElementById('label_rombel_tujuan');
    const labelTa = document.getElementById('label_ta_baru');

    const isNoTarget = ['lulus', 'pindah', 'keluar', 'mulai_pkl', 'selesai_pkl'].includes(jenis);

    if (isNoTarget) {
      rombelTujuan.disabled = true;
      taBaru.disabled = true;
      rombelTujuan.style.opacity = '0.4';
      taBaru.style.opacity = '0.4';
      if (labelRombel) labelRombel.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
      if (labelTa) labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
    } else {
      rombelTujuan.disabled = false;
      taBaru.disabled = false;
      rombelTujuan.style.opacity = '1';
      taBaru.style.opacity = '1';
      if (labelRombel) labelRombel.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan';
      if (labelTa) labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran Baru';
    }
  }

  function pilihSiswaTransisi(siswaId) {
    switchTransisiTab('individu');
    const select = document.getElementById('select_siswa_id');
    if (select) {
      select.value = siswaId;
      select.scrollIntoView({ behavior: 'smooth', block: 'center' });
      select.focus();
    }
  }

  function openModalTa(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
  }

  function closeModalTa(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    toggleTransisiFields();
    toggleMassalFields();
  });
</script>

{{-- MODAL TAMBAH TAHUN AJARAN BARU --}}
<div class="modal-overlay" id="modalTambahTa" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-card" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--r-md); max-width:440px; width:90%; padding:24px; box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">
        Tambah Periode Tahun Ajaran
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalTa('modalTambahTa')" style="padding:2px 8px;"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="/tahun-ajaran" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label style="font-size:12px; font-weight:800; color:var(--text-2); display:block; margin-bottom:6px;">Nama Tahun Ajaran</label>
        <input type="text" name="nama" required placeholder="Contoh: 2027/2028" class="input-field font-mono" style="width:100%; height:40px; font-size:13px; font-weight:700; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text);" />
      </div>

      <div style="margin-bottom:18px;">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:12.5px; font-weight:700; color:var(--text);">
          <input type="checkbox" name="is_active" value="1" style="width:16px; height:16px; cursor:pointer; accent-color:#000000;" /> 
          Jadikan langsung sebagai tahun ajaran aktif
        </label>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalTa('modalTambahTa')">Batal</button>
        <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1px solid #000000; font-weight:800; padding:0 16px; height:38px; border-radius:var(--r-sm); cursor:pointer;">
          Simpan Periode Baru
        </button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
