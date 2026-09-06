<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Penataan Rombel &amp; Jurusan — SITUAN SMKN 1 Air Naningan</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/situan-app.css') }}?v={{ filemtime(public_path('css/situan-app.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/rombel.css') }}?v={{ filemtime(public_path('css/rombel.css')) }}">
</head>
<body class="situan-body">
<div class="situan-layout">
  @include('partials.sidebar_situan')

  <main class="situan-main">
    <div class="situan-content">
      @php
        $currentUser = auth()->user();
        $isAdmin = $currentUser && $currentUser->isAdmin();
        $isWakaKurikulum = $currentUser && $currentUser->isWakaKurikulum();
        $isStafTu = $currentUser && $currentUser->isStafTu();
        $canManageRombel = $isAdmin || $isWakaKurikulum || $isStafTu;
      @endphp

      {{-- Page Header --}}
      <div class="situan-page-header">
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
              <span style="color:#0284c7; font-weight:800;">Rombel &amp; Jurusan</span>
            </div>
            <h1 class="situan-page-title" style="margin-top:2px; font-size:20px;">Penataan Rombel &amp; Jurusan</h1>
          </div>
        </div>

        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          @if($canManageRombel)
            <button type="button" id="btnToggleTambahRombel" onclick="toggleTambahRombel()" class="situan-btn-primary">
              <i class="bi bi-plus-circle-fill" id="iconToggleTambahRombel"></i>
              <span id="textToggleTambahRombel">Tambah Rombel</span>
            </button>
            <button type="button" onclick="openModal('modalJurusan')" class="situan-btn-outline">
              <i class="bi bi-mortarboard-fill" style="color:#0284c7;"></i> Jurusan ({{ $jurusans->count() }})
            </button>
            <button type="button" onclick="openModal('modalTahunAjaran')" class="situan-btn-outline">
              <i class="bi bi-calendar-range-fill" style="color:#6366f1;"></i> T.A. ({{ $tahunAjarans->count() }})
            </button>
          @endif
          @include('partials.header_actions')
        </div>
      </div>

      {{-- Flash Feedback Alerts --}}
      @if(session('success'))
        <div style="margin-bottom:16px; padding:12px 16px; border-radius:10px; background:rgba(16,185,129,0.1); border:1.5px solid #10b981; color:#065f46; font-weight:800; font-size:13px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-check-circle-fill" style="font-size:16px;"></i> {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div style="margin-bottom:16px; padding:12px 16px; border-radius:10px; background:rgba(239,68,68,0.1); border:1.5px solid #ef4444; color:#991b1b; font-weight:800; font-size:13px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-exclamation-triangle-fill" style="font-size:16px;"></i> {{ session('error') }}
        </div>
      @endif

      @if(isset($errors) && $errors->any())
        <div style="margin-bottom:16px; padding:12px 16px; border-radius:10px; background:rgba(239,68,68,0.1); border:1.5px solid #ef4444; color:#991b1b; font-weight:700; font-size:13px;">
          @foreach($errors->all() as $err)
            <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;"><i class="bi bi-x-circle-fill"></i> {{ $err }}</div>
          @endforeach
        </div>
      @endif

      {{-- Metric Strip Rombel --}}
      <div class="situan-metric-strip" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 20px;">
        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(2, 132, 199, 0.12); color:#0284c7;">
            <i class="bi bi-building"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ $rombels->count() }}</div>
            <div class="situan-metric-lbl">Total Rombel Aktif</div>
          </div>
        </div>

        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(16, 185, 129, 0.12); color:#10b981;">
            <i class="bi bi-1-circle-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ $rombels->where('tingkat', 'X')->count() }}</div>
            <div class="situan-metric-lbl">Tingkat X (Sepuluh)</div>
          </div>
        </div>

        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(217, 119, 6, 0.12); color:#d97706;">
            <i class="bi bi-2-circle-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ $rombels->where('tingkat', 'XI')->count() }}</div>
            <div class="situan-metric-lbl">Tingkat XI (Sebelas)</div>
          </div>
        </div>

        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(99, 102, 241, 0.12); color:#6366f1;">
            <i class="bi bi-3-circle-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ $rombels->where('tingkat', 'XII')->count() }}</div>
            <div class="situan-metric-lbl">Tingkat XII (Dua Belas)</div>
          </div>
        </div>

        <div class="situan-metric-card">
          <div class="situan-metric-icon" style="background:rgba(15, 23, 42, 0.08); color:#000000;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <div class="situan-metric-val">{{ $jurusans->count() }}</div>
            <div class="situan-metric-lbl">Program Keahlian</div>
          </div>
        </div>
      </div>

      {{-- Panel Tambah Rombel (Collapsible / Triggered) --}}
      @if($canManageRombel)
      <div class="situan-panel" id="panelTambahRombel" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom:20px;">
        <div class="situan-panel-header">
          <div class="situan-panel-title">
            <i class="bi bi-plus-circle-fill" style="color:#0284c7;"></i>
            <span>Tambah Rombel Kelas Baru</span>
          </div>
          <button type="button" onclick="toggleTambahRombel(false)" class="situan-btn-outline" style="height:30px; width:30px; padding:0; justify-content:center;" title="Tutup Form">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <form action="/rombel" method="POST">
          @csrf
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; align-items:flex-end;">
            <div>
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:#000000; margin-bottom:6px; display:block;">Tahun Ajaran <span style="color:#ef4444;">*</span></label>
              <select name="tahun_ajaran_id" required class="situan-select" style="width:100%;">
                @foreach($tahunAjarans as $ta)
                  <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>{{ $ta->nama }} @if($ta->is_active)(Aktif)@endif</option>
                @endforeach
              </select>
            </div>

            <div>
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:#000000; margin-bottom:6px; display:block;">Tingkat <span style="color:#ef4444;">*</span></label>
              <select name="tingkat" id="rombel_tingkat" required class="situan-select" style="width:100%;" onchange="generateNamaRombel()">
                <option value="X">X (Sepuluh)</option>
                <option value="XI">XI (Sebelas)</option>
                <option value="XII">XII (Dua Belas)</option>
              </select>
            </div>

            <div>
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:#000000; margin-bottom:6px; display:block;">Jurusan <span style="color:#ef4444;">*</span></label>
              @if($jurusans->isEmpty())
                <button type="button" onclick="openModal('modalJurusan')" class="situan-btn-outline" style="width:100%; color:#ef4444 !important; border-color:#ef4444;">
                  <i class="bi bi-plus-circle"></i> Tambah Jurusan
                </button>
              @else
                <select name="jurusan_id" id="rombel_jurusan" required class="situan-select" style="width:100%;" onchange="generateNamaRombel()">
                  @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" data-kode="{{ $j->kode_jurusan }}">{{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}</option>
                  @endforeach
                </select>
              @endif
            </div>

            <div>
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:#000000; margin-bottom:6px; display:block;">Nama Rombel <span style="color:#ef4444;">*</span></label>
              <input type="text" name="nama_rombel" id="rombel_nama_output" required placeholder="Contoh: X RPL 1" class="situan-input-search" style="padding-left:12px; font-family:ui-monospace, monospace; font-weight:800;" />
            </div>

            <div>
              <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:#000000; margin-bottom:6px; display:block;">Wali Kelas (Opsional)</label>
              <select name="wali_kelas_id" class="situan-select" style="width:100%;">
                <option value="">-- Pilih Wali Kelas --</option>
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->nip ?? 'Non-NIP' }})</option>
                @endforeach
              </select>
            </div>

            <div>
              <button type="submit" class="situan-btn-primary" style="width:100%; justify-content:center;">
                <i class="bi bi-save-fill"></i> Simpan Rombel
              </button>
            </div>
          </div>
        </form>
      </div>
      @endif

      {{-- Table Card: Daftar Rombel Kelas --}}
      <div class="situan-table-card">
        {{-- Table Toolbar --}}
        <div style="padding:14px 18px; border-bottom:1.5px solid var(--situan-border); display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px; background:var(--situan-card-bg);">
          <div style="display:flex; align-items:center; gap:8px;">
            <i class="bi bi-building" style="color:#0284c7; font-size:16px;"></i>
            <span style="font-weight:900; font-size:14px; color:#000000;">Daftar Rombongan Belajar</span>
            <span id="rombelCountBadge" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#000000; font-size:11px; font-weight:800; padding:2px 8px; border-radius:6px; font-family:ui-monospace, monospace;">
              {{ $rombels->count() }} Rombel
            </span>
          </div>

          {{-- Search Input --}}
          <div class="situan-search-wrap" style="max-width:300px;">
            <i class="bi bi-search"></i>
            <input type="text" id="searchRombel" onkeyup="filterRombelTable()" placeholder="Cari rombel, tingkat, wali kelas..." class="situan-input-search" />
          </div>
        </div>

        <div class="situan-table-wrap">
          <table class="situan-table" id="tableRombel">
            <thead>
              <tr>
                <th onclick="sortRombelTable(0)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                  Rombel <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.6;"></i>
                </th>
                <th onclick="sortRombelTable(1)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                  Tingkat <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.6;"></i>
                </th>
                <th onclick="sortRombelTable(2)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                  Jurusan / Program Keahlian <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.6;"></i>
                </th>
                <th onclick="sortRombelTable(3)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                  Wali Kelas <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.6;"></i>
                </th>
                <th onclick="sortRombelTable(4)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                  Tahun Ajaran <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.6;"></i>
                </th>
                @if($canManageRombel)
                  <th style="text-align:right;">Aksi</th>
                @endif
              </tr>
            </thead>
            <tbody id="rombelTableBody">
              @forelse($rombels as $r)
                <tr class="rombel-row">
                  <td data-sort="{{ $r->nama_rombel }}" style="white-space:nowrap;">
                    <div class="situan-badge-rombel font-mono" style="font-size:13px; font-weight:900;">{{ $r->nama_rombel }}</div>
                  </td>
                  <td data-sort="{{ $r->tingkat }}" style="white-space:nowrap; font-weight:800; color:#000000; font-size:12.5px;">
                    Kelas {{ $r->tingkat }}
                  </td>
                  <td data-sort="{{ $r->jurusan->nama_jurusan ?? '' }}">
                    <div style="font-weight:800; font-size:13px; color:#000000; line-height:1.3;">{{ $r->jurusan->nama_jurusan ?? '-' }}</div>
                    @if($r->jurusan && $r->jurusan->kode_jurusan)
                      <div class="situan-badge-jurusan" style="font-family:ui-monospace, monospace;">Kode: {{ $r->jurusan->kode_jurusan }}</div>
                    @endif
                  </td>
                  <td data-sort="{{ $r->waliKelas->nama ?? '' }}">
                    @if($r->waliKelas)
                      <div>
                        <strong style="color:#000000; font-size:13px; font-weight:800;">{{ $r->waliKelas->nama }}</strong>
                        @if($r->waliKelas->nip)
                          <div style="font-size:11px; color:#475569; font-family:ui-monospace, monospace; margin-top:2px;">NIP: {{ $r->waliKelas->nip }}</div>
                        @endif
                      </div>
                    @else
                      <span style="color:#64748b; font-style:italic; font-size:12px; font-weight:600;">Belum Ditugaskan</span>
                    @endif
                  </td>
                  <td data-sort="{{ $r->tahunAjaran->nama ?? '' }}" style="white-space:nowrap; font-size:12px; font-weight:700; color:#000000;">
                    <span style="background:#f1f5f9; border:1px solid #cbd5e1; padding:3px 8px; border-radius:6px;">{{ $r->tahunAjaran->nama ?? '-' }}</span>
                  </td>
                  @if($canManageRombel)
                  <td style="text-align:right; white-space:nowrap;">
                    <div style="display:inline-flex; gap:6px;">
                      <button type="button" onclick="openEditRombel({{ json_encode($r) }})" class="situan-action-icon-btn" title="Edit Rombel &amp; Wali Kelas">
                        <i class="bi bi-pencil-square" style="color:#0284c7;"></i>
                      </button>
                      @if($isAdmin)
                        <form action="/rombel/{{ $r->id }}" method="POST" onsubmit="return confirm('Hapus rombel {{ $r->nama_rombel }}?')" style="display:inline; margin:0;">
                          @csrf @method('DELETE')
                          <button type="submit" class="situan-action-icon-btn" title="Hapus Rombel">
                            <i class="bi bi-trash3-fill" style="color:#ef4444;"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                  @endif
                </tr>
              @empty
                <tr id="emptyRow">
                  <td colspan="{{ $canManageRombel ? '6' : '5' }}" style="text-align:center; padding:36px; color:#64748b; font-size:13px; font-weight:600;">
                    Belum ada data rombel kelas tersimpan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

@if($canManageRombel)
<!-- Modal Edit Rombel -->
<div id="editRombelModal" class="modal-overlay">
  <div class="modal-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1.5px solid var(--situan-border); padding-bottom:12px;">
      <h3 style="font-size:17px; font-weight:900; color:#000000; margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-pencil-square" style="color:#0284c7;"></i> Edit Rombel &amp; Wali Kelas
      </h3>
      <button onclick="closeModal('editRombelModal')" style="background:none; border:none; color:#64748b; font-size:20px; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>
    <form id="editRombelForm" method="POST">
      @csrf
      @method('PUT')
      <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="form-group">
          <label style="font-size:12px; font-weight:800; color:#000000; display:block; margin-bottom:6px;">Nama Rombel <span style="color:#ef4444;">*</span></label>
          <input type="text" id="edit_nama_rombel" name="nama_rombel" required class="situan-input-search" style="height:40px; padding-left:12px; font-weight:800;" />
        </div>
        <div class="form-group">
          <label style="font-size:12px; font-weight:800; color:#000000; display:block; margin-bottom:6px;">Tingkat <span style="color:#ef4444;">*</span></label>
          <select id="edit_tingkat" name="tingkat" required class="situan-select" style="width:100%; height:40px;">
            <option value="X">X (Sepuluh)</option>
            <option value="XI">XI (Sebelas)</option>
            <option value="XII">XII (Dua Belas)</option>
          </select>
        </div>
        <div class="form-group">
          <label style="font-size:12px; font-weight:800; color:#000000; display:block; margin-bottom:6px;">Wali Kelas Pengampu</label>
          <select id="edit_wali_kelas_id" name="wali_kelas_id" class="situan-select" style="width:100%; height:40px;">
            <option value="">-- Tanpa Wali Kelas --</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->nip ?? 'Non-NIP' }})</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px; border-top:1.5px solid var(--situan-border); padding-top:16px;">
          <button type="button" onclick="closeModal('editRombelModal')" class="situan-btn-outline" style="height:40px;">Batal</button>
          <button type="submit" class="situan-btn-primary" style="height:40px; padding:0 20px;">
            <i class="bi bi-save-fill"></i> Simpan Perubahan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Kelola Jurusan -->
<div id="modalJurusan" class="modal-overlay">
  <div class="modal-card" style="max-width: 620px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1.5px solid var(--situan-border); padding-bottom:12px;">
      <h3 style="font-size:17px; font-weight:900; color:#000000; margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-mortarboard-fill" style="color:#0284c7;"></i> Kelola Program Keahlian / Jurusan
      </h3>
      <button onclick="closeModal('modalJurusan')" style="background:none; border:none; color:#64748b; font-size:20px; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Form Tambah Jurusan --}}
    <form action="{{ route('jurusan.store') }}" method="POST" style="margin-bottom:20px; background:#f8fafc; padding:16px; border-radius:10px; border:1.5px solid var(--situan-border);">
      @csrf
      <div style="font-size:12px; font-weight:800; color:#000000; margin-bottom:10px; text-transform:uppercase;">+ Tambah Jurusan Baru</div>
      <div style="display:grid; grid-template-columns: 120px 1fr auto; gap:10px; align-items:flex-end;">
        <div>
          <label style="font-size:11px; font-weight:800; color:#000000; margin-bottom:4px; display:block;">Kode</label>
          <input type="text" name="kode_jurusan" placeholder="Contoh: RPL" required class="situan-input-search" style="height:38px; text-transform:uppercase; font-family:ui-monospace, monospace; font-weight:900; padding-left:10px;" />
        </div>
        <div>
          <label style="font-size:11px; font-weight:800; color:#000000; margin-bottom:4px; display:block;">Nama Lengkap Jurusan</label>
          <input type="text" name="nama_jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" required class="situan-input-search" style="height:38px; padding-left:10px; font-weight:700;" />
        </div>
        <div>
          <button type="submit" class="situan-btn-primary" style="height:38px;"><i class="bi bi-plus-lg"></i> Tambah</button>
        </div>
      </div>
    </form>

    {{-- Daftar Jurusan --}}
    <div style="max-height: 260px; overflow-y:auto; border:1.5px solid var(--situan-border); border-radius:10px;">
      <table class="situan-table" style="width:100%; margin:0;">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Jurusan</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jurusans as $j)
            <tr>
              <td style="font-family:ui-monospace, monospace; font-weight:900; color:#000000;">{{ $j->kode_jurusan }}</td>
              <td style="font-weight:700; color:#000000;">{{ $j->nama_jurusan }}</td>
              <td style="text-align:right;">
                <form action="{{ route('jurusan.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jurusan {{ $j->kode_jurusan }}?')" style="display:inline;">
                  @csrf @method('DELETE')
                  <button type="submit" class="situan-action-icon-btn" style="color:#ef4444;" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center; padding:18px; color:#64748b; font-size:12.5px;">Belum ada jurusan tersimpan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Kelola Tahun Ajaran -->
<div id="modalTahunAjaran" class="modal-overlay">
  <div class="modal-card" style="max-width: 560px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1.5px solid var(--situan-border); padding-bottom:12px;">
      <h3 style="font-size:17px; font-weight:900; color:#000000; margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-calendar-range-fill" style="color:#6366f1;"></i> Kelola Tahun Ajaran
      </h3>
      <button onclick="closeModal('modalTahunAjaran')" style="background:none; border:none; color:#64748b; font-size:20px; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Form Tambah Tahun Ajaran --}}
    <form action="/tahun-ajaran" method="POST" style="margin-bottom:20px; background:#f8fafc; padding:16px; border-radius:10px; border:1.5px solid var(--situan-border);">
      @csrf
      <div style="font-size:12px; font-weight:800; color:#000000; margin-bottom:10px; text-transform:uppercase;">+ Tambah Tahun Ajaran Baru</div>
      <div style="display:flex; gap:10px; align-items:flex-end;">
        <div style="flex:1;">
          <label style="font-size:11px; font-weight:800; color:#000000; margin-bottom:4px; display:block;">Nama Tahun Ajaran</label>
          <input type="text" name="nama" placeholder="Contoh: 2026/2027 Ganjil" required class="situan-input-search" style="height:38px; padding-left:10px; font-weight:700;" />
        </div>
        <div>
          <button type="submit" class="situan-btn-primary" style="height:38px;"><i class="bi bi-plus-lg"></i> Tambah</button>
        </div>
      </div>
    </form>

    {{-- Daftar Tahun Ajaran --}}
    <div style="max-height: 240px; overflow-y:auto; border:1.5px solid var(--situan-border); border-radius:10px;">
      <table class="situan-table" style="width:100%; margin:0;">
        <thead>
          <tr>
            <th>Tahun Ajaran</th>
            <th>Status</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tahunAjarans as $ta)
            <tr>
              <td style="font-weight:800; color:#000000;">{{ $ta->nama }}</td>
              <td>
                @if($ta->is_active)
                  <span style="background:rgba(16,185,129,0.12); color:#065f46; border:1px solid #6ee7b7; font-size:11px; font-weight:800; padding:2px 8px; border-radius:10px;">Aktif</span>
                @else
                  <span style="color:#64748b; font-size:11px; font-weight:700;">Tidak Aktif</span>
                @endif
              </td>
              <td style="text-align:right;">
                @if(!$ta->is_active)
                  <form action="/tahun-ajaran/{{ $ta->id }}/aktifkan" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="situan-btn-outline" style="font-size:11px; height:28px; padding:0 8px;">Aktifkan</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center; padding:18px; color:#64748b; font-size:12.5px;">Belum ada tahun ajaran tersimpan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

<script>
  function generateNamaRombel() {
    const tingkat = document.getElementById('rombel_tingkat').value;
    const jurusanSelect = document.getElementById('rombel_jurusan');
    if (!jurusanSelect || !jurusanSelect.options || jurusanSelect.options.length === 0) return;
    
    const selectedOpt = jurusanSelect.options[jurusanSelect.selectedIndex];
    const kodeJurusan = selectedOpt ? selectedOpt.getAttribute('data-kode') : 'RPL';

    const output = document.getElementById('rombel_nama_output');
    if (output) {
      output.value = `${tingkat} ${kodeJurusan}`;
    }
  }

  // ── Live Search Filter ──
  function filterRombelTable() {
    const query = document.getElementById('searchRombel').value.toLowerCase();
    const rows = document.querySelectorAll('#rombelTableBody .rombel-row');
    let visibleCount = 0;

    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      if (text.includes(query)) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    const badge = document.getElementById('rombelCountBadge');
    if (badge) {
      badge.textContent = `${visibleCount} Rombel`;
    }
  }

  // ── Table Column Sorting ──
  let sortDirections = {};
  function sortRombelTable(colIndex) {
    const tbody = document.getElementById('rombelTableBody');
    const rows = Array.from(tbody.querySelectorAll('.rombel-row'));
    if (!rows.length) return;

    const isAsc = !sortDirections[colIndex];
    sortDirections = {}; // reset
    sortDirections[colIndex] = isAsc;

    rows.sort((rowA, rowB) => {
      const cellA = rowA.children[colIndex].getAttribute('data-sort') || rowA.children[colIndex].innerText;
      const cellB = rowB.children[colIndex].getAttribute('data-sort') || rowB.children[colIndex].innerText;

      return isAsc ? cellA.localeCompare(cellB, undefined, { numeric: true }) : cellB.localeCompare(cellA, undefined, { numeric: true });
    });

    rows.forEach(row => tbody.appendChild(row));
  }

  function toggleTambahRombel(forceState) {
    const panel = document.getElementById('panelTambahRombel');
    const text = document.getElementById('textToggleTambahRombel');
    if (!panel) return;
    const isHidden = (panel.style.display === 'none' || panel.style.display === '');
    const show = (forceState !== undefined) ? forceState : isHidden;
    
    panel.style.display = show ? 'block' : 'none';
    if (text) {
      text.innerText = show ? 'Tutup Form' : 'Tambah Rombel';
    }
    if (show) {
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function openEditRombel(rombel) {
    document.getElementById('editRombelForm').action = '/rombel/' + rombel.id;
    document.getElementById('edit_nama_rombel').value = rombel.nama_rombel || '';
    document.getElementById('edit_tingkat').value = rombel.tingkat || 'X';
    document.getElementById('edit_wali_kelas_id').value = rombel.wali_kelas_id || '';
    openModal('editRombelModal');
  }

  function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('active');
  }

  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
  }

  document.addEventListener('DOMContentLoaded', () => {
    generateNamaRombel();
  });
</script>
</body>
</html>
