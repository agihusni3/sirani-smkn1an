<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Master Rombel &amp; Jurusan — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .btn-badge-action {
      height: 36px;
      padding: 0 14px;
      font-size: 13px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border-radius: var(--r-sm);
      text-decoration: none;
      cursor: pointer;
      transition: all .2s;
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
      $isWakaKurikulum = $currentUser && $currentUser->isWakaKurikulum();
      $isStafTu = $currentUser && $currentUser->isStafTu();
      $canManageRombel = $isAdmin || $isWakaKurikulum || $isStafTu;
    @endphp

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-building" style="color:#000000; font-size:16px;"></i> Rombel &amp; Jurusan
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Kelola rombel kelas, jurusan, &amp; wali kelas
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($canManageRombel)
            <button type="button" id="btnToggleTambahRombel" onclick="toggleTambahRombel()" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-plus-circle-fill" id="iconToggleTambahRombel"></i>
              <span id="textToggleTambahRombel">Tambah Rombel</span>
            </button>
            <button type="button" onclick="openModal('modalJurusan')" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
              <i class="bi bi-mortarboard-fill" style="color:#000000;"></i> Jurusan ({{ $jurusans->count() }})
            </button>
            <button type="button" onclick="openModal('modalTahunAjaran')" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
              <i class="bi bi-calendar-range-fill" style="color:#000000;"></i> T.A. ({{ $tahunAjarans->count() }})
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

    @if(isset($errors) && $errors->any())
      <div class="alert-error" style="margin-bottom:12px;">
        @foreach($errors->all() as $err)
          <div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>
        @endforeach
      </div>
    @endif

    {{-- Panel Tambah Rombel (Collapsible / Triggered) --}}
    @if($canManageRombel)
    <div class="panel" id="panelTambahRombel" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom:20px; border-color:var(--border); background:var(--bg-2);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <i class="bi bi-plus-circle-fill" style="color:#000000;"></i>
          <span style="font-weight:800; font-size:14.5px; color:var(--text);">Tambah Rombel Kelas Baru</span>
        </div>
        <button type="button" onclick="toggleTambahRombel(false)" class="btn btn-outline" style="height:30px; width:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form action="/rombel" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; align-items:flex-end;">
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Tahun Ajaran <span style="color:var(--red);">*</span></label>
            <select name="tahun_ajaran_id" required class="input-field" style="width:100%; height:34px; font-size:12px; padding:0 8px;">
              @foreach($tahunAjarans as $ta)
                <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>{{ $ta->nama }} @if($ta->is_active)(Aktif)@endif</option>
              @endforeach
            </select>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Tingkat <span style="color:var(--red);">*</span></label>
            <select name="tingkat" id="rombel_tingkat" required class="input-field" style="width:100%; height:34px; font-size:12px; padding:0 8px;" onchange="generateNamaRombel()">
              <option value="X">X (Sepuluh)</option>
              <option value="XI">XI (Sebelas)</option>
              <option value="XII">XII (Dua Belas)</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Jurusan <span style="color:var(--red);">*</span></label>
            @if($jurusans->isEmpty())
              <button type="button" onclick="openModal('modalJurusan')" class="btn btn-outline" style="width:100%; height:34px; color:var(--red); border-color:var(--red); font-size:11.5px;">
                <i class="bi bi-plus-circle"></i> Tambah Jurusan
              </button>
            @else
              <select name="jurusan_id" id="rombel_jurusan" required class="input-field" style="width:100%; height:34px; font-size:12px; padding:0 8px;" onchange="generateNamaRombel()">
                @foreach($jurusans as $j)
                  <option value="{{ $j->id }}" data-kode="{{ $j->kode_jurusan }}">{{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}</option>
                @endforeach
              </select>
            @endif
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Nama Rombel <span style="color:var(--red);">*</span></label>
            <input type="text" name="nama_rombel" id="rombel_nama_output" required placeholder="Contoh: X RPL 1" class="input-field" style="width:100%; height:34px; font-size:12px; font-family:var(--font-mono); font-weight:800; padding:0 8px;" />
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Wali Kelas (Opsional)</label>
            <select name="wali_kelas_id" class="input-field" style="width:100%; height:34px; font-size:12px; padding:0 8px;">
              <option value="">-- Pilih Wali Kelas --</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->nip ?? 'Non-NIP' }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <button type="submit" class="btn btn-gold" style="width:100%; height:34px; display:inline-flex; align-items:center; justify-content:center; gap:6px; font-weight:800; font-size:12px; border-radius:var(--r-sm);">
              <i class="bi bi-save-fill"></i> Simpan Rombel
            </button>
          </div>
        </div>
      </form>
    </div>
    @endif

    {{-- Panel Daftar Rombel dengan Search & Sort Terpadu --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div class="panel-title" style="padding:8px 12px; margin:0; border-bottom:1px solid var(--border); background:var(--surface); display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:8px;">
        <div style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:13.5px; color:var(--text);">
          <i class="bi bi-building" style="color:#000000;"></i>
          <span>Daftar Rombel Kelas</span>
          <span id="rombelCountBadge" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--text-2); font-size:10.5px; font-weight:700; padding:1px 6px; border-radius:4px;" class="font-mono">
            {{ $rombels->count() }} Rombel
          </span>
        </div>

        {{-- Search Input --}}
        <div style="position:relative; width:100%; max-width:260px;">
          <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px; pointer-events:none;"></i>
          <input type="text" id="searchRombel" onkeyup="filterRombelTable()" placeholder="Cari rombel, tingkat..." class="input-field" style="width:100%; height:32px; padding-left:28px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
        </div>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" id="tableRombel" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th onclick="sortRombelTable(0)" style="cursor:pointer; user-select:none; white-space:nowrap;" title="Klik untuk mengurutkan">
                Rombel <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.7;"></i>
              </th>
              <th onclick="sortRombelTable(1)" style="cursor:pointer; user-select:none; white-space:nowrap;" title="Klik untuk mengurutkan">
                Tingkat <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.7;"></i>
              </th>
              <th onclick="sortRombelTable(2)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                Jurusan <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.7;"></i>
              </th>
              <th onclick="sortRombelTable(3)" style="cursor:pointer; user-select:none;" title="Klik untuk mengurutkan">
                Wali Kelas <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.7;"></i>
              </th>
              <th onclick="sortRombelTable(4)" style="cursor:pointer; user-select:none; white-space:nowrap;" title="Klik untuk mengurutkan">
                Tahun Ajaran <i class="bi bi-arrow-down-up" style="margin-left:4px; font-size:10px; opacity:0.7;"></i>
              </th>
              @if($canManageRombel)
                <th style="text-align:right; white-space:nowrap;">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody id="rombelTableBody">
            @forelse($rombels as $r)
              <tr class="rombel-row">
                <td data-sort="{{ $r->nama_rombel }}" style="white-space:nowrap;">
                  <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800; font-size:13px;">{{ $r->nama_rombel }}</strong>
                </td>
                <td data-sort="{{ $r->tingkat }}" style="white-space:nowrap; font-weight:700; color:var(--text-2); font-size:12px; font-family:var(--font-mono);">
                  Tk. {{ $r->tingkat }}
                </td>
                <td data-sort="{{ $r->jurusan->nama_jurusan ?? '' }}">
                  <div style="font-weight:600; font-size:12.5px; color:var(--text); line-height:1.3;">{{ $r->jurusan->nama_jurusan ?? '-' }}</div>
                  @if($r->jurusan && $r->jurusan->kode_jurusan)
                    <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">Kode: {{ $r->jurusan->kode_jurusan }}</div>
                  @endif
                </td>
                <td data-sort="{{ $r->waliKelas->nama ?? '' }}">
                  @if($r->waliKelas)
                    <div>
                      <strong style="color:var(--text); font-size:12.5px;">{{ $r->waliKelas->nama }}</strong>
                      @if($r->waliKelas->nip)
                        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:1px;">{{ $r->waliKelas->nip }}</div>
                      @endif
                    </div>
                  @else
                    <span style="color:var(--text-3); font-style:italic; font-size:11.5px;">Belum Ditugaskan</span>
                  @endif
                </td>
                <td data-sort="{{ $r->tahunAjaran->nama ?? '' }}" style="white-space:nowrap; font-family:var(--font-mono); font-size:12px; color:var(--text-2);">
                  {{ $r->tahunAjaran->nama ?? '-' }}
                </td>
                @if($canManageRombel)
                <td style="text-align:right; white-space:nowrap;">
                  <div style="display:inline-flex; gap:6px;">
                    <button type="button" onclick="openEditRombel({{ json_encode($r) }})" class="btn-icon btn-icon-edit" data-tooltip="Edit Rombel & Wali Kelas">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    @if($isAdmin)
                      <form action="/rombel/{{ $r->id }}" method="POST" onsubmit="return confirm('Hapus rombel {{ $r->nama_rombel }}?')" style="display:inline; margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-danger" data-tooltip="Hapus Rombel">
                          <i class="bi bi-trash3-fill"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
                @endif
              </tr>
            @empty
              <tr id="emptyRow">
                <td colspan="{{ $canManageRombel ? '6' : '5' }}" style="text-align:center; padding:30px; color:var(--text-3);">Belum ada data rombel kelas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

@if($canManageRombel)
<!-- Modal Edit Rombel -->
<div id="editRombelModal" class="modal-overlay">
  <div class="modal-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <h3 style="font-size:18px; font-weight:800; color:var(--text);"><i class="bi bi-pencil-square" style="color:#000000; margin-right:8px;"></i>Edit Rombel &amp; Wali Kelas</h3>
      <button onclick="closeModal('editRombelModal')" style="background:none; border:none; color:var(--text-3); font-size:20px; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>
    <form id="editRombelForm" method="POST">
      @csrf
      @method('PUT')
      <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="form-group">
          <label>Nama Rombel <span style="color:var(--red);">*</span></label>
          <input type="text" id="edit_nama_rombel" name="nama_rombel" required style="width:100%; height:42px;" />
        </div>
        <div class="form-group">
          <label>Tingkat <span style="color:var(--red);">*</span></label>
          <select id="edit_tingkat" name="tingkat" required style="width:100%; height:42px;">
            <option value="X">X (Sepuluh)</option>
            <option value="XI">XI (Sebelas)</option>
            <option value="XII">XII (Dua Belas)</option>
          </select>
        </div>
        <div class="form-group">
          <label>Wali Kelas Pengampu</label>
          <select id="edit_wali_kelas_id" name="wali_kelas_id" style="width:100%; height:42px;">
            <option value="">-- Tanpa Wali Kelas --</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->nip ?? 'Non-NIP' }})</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px; border-top:1px solid var(--border); padding-top:16px;">
          <button type="button" onclick="closeModal('editRombelModal')" class="btn btn-outline" style="height:40px; padding:0 18px;">Batal</button>
          <button type="submit" class="btn btn-gold" style="height:40px; padding:0 20px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-save-fill"></i>Simpan Perubahan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Kelola Jurusan -->
<div id="modalJurusan" class="modal-overlay">
  <div class="modal-card" style="max-width: 600px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <h3 style="font-size:17px; font-weight:800; color:var(--text);"><i class="bi bi-mortarboard-fill" style="color:#000000; margin-right:8px;"></i>Kelola Program Keahlian / Jurusan</h3>
      <button onclick="closeModal('modalJurusan')" style="background:none; border:none; color:var(--text-3); font-size:20px; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Form Tambah Jurusan --}}
    <form action="{{ route('jurusan.store') }}" method="POST" style="margin-bottom:20px; background:var(--bg-3); padding:14px; border-radius:var(--r-sm);">
      @csrf
      <div style="font-size:12px; font-weight:700; color:#000000; margin-bottom:8px; text-transform:uppercase;">+ Tambah Jurusan Baru</div>
      <div style="display:grid; grid-template-columns: 120px 1fr auto; gap:10px; align-items:flex-end;">
        <div>
          <label style="font-size:11px; font-weight:700;">Kode (Singkatan)</label>
          <input type="text" name="kode_jurusan" placeholder="Contoh: RPL" required style="width:100%; height:38px; text-transform:uppercase; font-family:var(--font-mono); font-weight:800;" />
        </div>
        <div>
          <label style="font-size:11px; font-weight:700;">Nama Lengkap Jurusan</label>
          <input type="text" name="nama_jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" required style="width:100%; height:38px;" />
        </div>
        <div>
          <button type="submit" class="btn btn-gold" style="height:38px; font-size:12px; font-weight:800;"><i class="bi bi-plus-lg"></i> Tambah</button>
        </div>
      </div>
    </form>

    {{-- Daftar Jurusan --}}
    <div style="max-height: 240px; overflow-y:auto; border:1px solid var(--border); border-radius:var(--r-sm);">
      <table style="width:100%; margin:0; font-size:13px;">
        <thead>
          <tr style="background:var(--bg-3);">
            <th style="padding:8px 12px;">Kode</th>
            <th style="padding:8px 12px;">Nama Jurusan</th>
            <th style="padding:8px 12px; text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jurusans as $j)
            <tr>
              <td style="padding:8px 12px; font-family:var(--font-mono); font-weight:800; color:#000000;">{{ $j->kode_jurusan }}</td>
              <td style="padding:8px 12px; font-weight:600;">{{ $j->nama_jurusan }}</td>
              <td style="padding:8px 12px; text-align:right;">
                <form action="{{ route('jurusan.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jurusan {{ $j->kode_jurusan }}?')" style="display:inline;">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-icon btn-icon-danger" style="width:28px; height:28px;" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center; padding:16px; color:var(--text-3);">Belum ada jurusan tersimpan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Kelola Tahun Ajaran -->
<div id="modalTahunAjaran" class="modal-overlay">
  <div class="modal-card" style="max-width: 540px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <h3 style="font-size:17px; font-weight:800; color:var(--text);"><i class="bi bi-calendar-range-fill" style="color:#000000; margin-right:8px;"></i>Kelola Tahun Ajaran</h3>
      <button onclick="closeModal('modalTahunAjaran')" style="background:none; border:none; color:var(--text-3); font-size:20px; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Form Tambah Tahun Ajaran --}}
    <form action="/tahun-ajaran" method="POST" style="margin-bottom:20px; background:var(--bg-3); padding:14px; border-radius:var(--r-sm);">
      @csrf
      <div style="font-size:12px; font-weight:700; color:#000000; margin-bottom:8px; text-transform:uppercase;">+ Tambah Tahun Ajaran Baru</div>
      <div style="display:flex; gap:10px; align-items:flex-end;">
        <div style="flex:1;">
          <label style="font-size:11px; font-weight:700;">Nama Tahun Ajaran</label>
          <input type="text" name="nama" placeholder="Contoh: 2026/2027 Ganjil" required style="width:100%; height:38px;" />
        </div>
        <div>
          <button type="submit" class="btn btn-gold" style="height:38px; font-size:12px; font-weight:800;"><i class="bi bi-plus-lg"></i> Tambah</button>
        </div>
      </div>
    </form>

    {{-- Daftar Tahun Ajaran --}}
    <div style="max-height: 220px; overflow-y:auto; border:1px solid var(--border); border-radius:var(--r-sm);">
      <table style="width:100%; margin:0; font-size:13px;">
        <thead>
          <tr style="background:var(--bg-3);">
            <th style="padding:8px 12px;">Tahun Ajaran</th>
            <th style="padding:8px 12px;">Status</th>
            <th style="padding:8px 12px; text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tahunAjarans as $ta)
            <tr>
              <td style="padding:8px 12px; font-weight:700;">{{ $ta->nama }}</td>
              <td style="padding:8px 12px;">
                @if($ta->is_active)
                  <span style="background:rgba(34,197,94,0.15); color:#22C55E; font-size:11px; font-weight:800; padding:2px 8px; border-radius:10px;">Aktif</span>
                @else
                  <span style="color:var(--text-3); font-size:11px;">Tidak Aktif</span>
                @endif
              </td>
              <td style="padding:8px 12px; text-align:right;">
                @if(!$ta->is_active)
                  <form action="/tahun-ajaran/{{ $ta->id }}/aktifkan" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px;">Aktifkan</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center; padding:16px; color:var(--text-3);">Belum ada tahun ajaran.</td></tr>
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
