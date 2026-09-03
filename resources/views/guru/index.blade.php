<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Master Data Guru &amp; Pegawai — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .guru-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 12px;
    }
    .guru-stat-card {
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
    .guru-stat-card:hover {
      border-color: #000000;
    }
    .guru-stat-icon {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }
    .guru-stat-val {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
      color: #000000;
    }
    .guru-stat-lbl {
      font-size: 11.5px;
      color: var(--text-3);
      font-weight: 600;
      margin-top: 2px;
    }

    .guru-form-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px 24px;
      margin-bottom: 22px;
    }
    @media (max-width: 992px) {
      .guru-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 768px) {
      .guru-stat-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
      }
      .guru-stat-card {
        padding: 8px 10px !important;
        gap: 10px !important;
      }
      .guru-stat-val {
        font-size: 18px !important;
      }
      .guru-stat-icon {
        width: 32px !important;
        height: 32px !important;
        font-size: 15px !important;
      }

      /* Toolbar Mobile Responsive */
      .guru-table-toolbar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .guru-table-title {
        width: 100% !important;
      }
      .guru-table-form {
        width: 100% !important;
        max-width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
      }
      .guru-search-box {
        width: 100% !important;
      }
      .guru-filter-group {
        width: 100% !important;
        display: flex !important;
        gap: 4px !important;
      }
      .guru-filter-group select {
        font-size: 10.5px !important;
      }
    }
    @media (max-width: 640px) {
      .guru-form-grid { grid-template-columns: 1fr; }
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

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-person-badge-fill" style="color:#000000; font-size:16px;"></i> Data Guru &amp; Pegawai
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Total: <strong style="color:#000000;">{{ $statTotal }}</strong> Personel
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($isAdmin || $isStafTu)
            <button type="button" id="btnToggleTambahGuru" onclick="toggleTambahGuru()" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px; cursor:pointer;">
              <i class="bi bi-person-plus-fill" id="iconToggleTambahGuru"></i>
              <span id="textToggleTambahGuru">Tambah Guru</span>
            </button>
            <button type="button" onclick="openModal('importGuruModal')" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
              <i class="bi bi-file-earmark-arrow-up-fill" style="color:#000000;"></i> Import CSV
            </button>
          @endif
          <a href="/guru/export" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; text-decoration:none; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;" title="Unduh CSV Kompatibel Excel">
            <i class="bi bi-file-earmark-excel-fill" style="color:#000000;"></i> Excel
          </a>
          <a href="/guru/cetak-pdf" target="_blank" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; text-decoration:none; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px;" title="Cetak Format A4 Kop Dinas">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#000000;"></i> PDF
          </a>
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))<div class="alert-success" style="margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:16px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- KPI STAT CARDS --}}
    <div class="guru-stat-grid">
      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statTotal }}</div>
          <div class="guru-stat-lbl">Total Guru &amp; Pegawai</div>
        </div>
      </div>

      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statWali }}</div>
          <div class="guru-stat-lbl">Ditugaskan Wali Kelas</div>
        </div>
      </div>


      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statAkun }}</div>
          <div class="guru-stat-lbl">Memiliki Akun Login</div>
        </div>
      </div>
    </div>

    @if($isAdmin || $isStafTu)
    <!-- Form Tambah Guru (Collapsible / Triggered) -->
    <div class="panel" id="panelTambahGuru" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom: 20px; border-color: var(--border); background: var(--bg-2);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="stat-icon" style="width:36px; height:36px; border-radius:8px; background:rgba(0,0,0,0.06); color:#000000; display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <span style="font-weight:800; font-size:15px; color:var(--text);">Form Tambah Guru &amp; Tenaga Kependidikan</span>
            <div style="font-size:12px; color:var(--text-3);">Lengkapi profil tenaga pendidik/kependidikan.</div>
          </div>
        </div>
        <button type="button" onclick="toggleTambahGuru(false)" class="btn btn-outline" style="height:32px; width:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form id="formTambahGuru" action="/guru" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Grid Input 4 Kolom Seimbang -->
        <div class="guru-form-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:14px;">
          
          {{-- Baris 1: Kolom 1 (NIP) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              NIP (Opsional)
            </label>
            <input type="text" name="nip" id="tambah_guru_nip" placeholder="Nomor NIP (Contoh: 19850101...)" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 1: Kolom 2 (Nama Lengkap) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Nama Lengkap &amp; Gelar <span style="color:var(--red);">*</span>
            </label>
            <input type="text" name="nama" id="tambah_guru_nama" required placeholder="Contoh: Drs. Sugeng Wardoyo, M.Pd" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 1: Kolom 3 (Jabatan) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Jabatan / Penugasan <span style="color:var(--red);">*</span>
            </label>
            <input type="text" name="jabatan" id="tambah_guru_jabatan" required placeholder="Contoh: Guru Matematika / Guru BK / TU" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 1: Kolom 4 (Jenis Kepegawaian) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              Status Kepegawaian <span style="color:var(--red);">*</span>
            </label>
            <select name="jenis_kepegawaian" id="tambah_guru_jenis_kepegawaian" onchange="toggleHariMengajar('tambah', this.value)" style="width:100%; height:40px;" class="input-field">
              <option value="pns">PNS (Pegawai Negeri Sipil)</option>
              <option value="pppk">PPPK (P3K)</option>
              <option value="honor">Guru Honor (GTT)</option>
              <option value="tendik">Tenaga Kependidikan (TU/Staf)</option>
            </select>
          </div>

          {{-- Baris 2: Kolom 1 (No WhatsApp) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2);">
              No. WhatsApp / HP
            </label>
            <input type="text" name="no_hp" id="tambah_guru_no_hp" placeholder="Contoh: 081277112233" style="width:100%; height:40px;" />
          </div>

          {{-- Baris 2: Kolom 2 (Foto Profil) --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="margin-bottom:6px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-2); display:flex; justify-content:space-between;">
              <span>Foto Profil</span>
              <span style="color:#000000; font-size:11px; text-transform:none; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
            </label>
            <div style="display:flex; align-items:center; gap:10px;">
              <div id="tambah_guru_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid var(--border-2); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                <i class="bi bi-person-fill" style="color:var(--text-3); font-size:20px;"></i>
              </div>
              <input type="file" name="foto" id="inputFotoGuruTambah" accept="image/*" onchange="initPhotoCrop(this, 'tambah_guru_foto_preview', '1:1', 'Potong Foto Profil Guru')" style="flex:1; height:40px;" />
            </div>
          </div>

          {{-- Baris 3: Jadwal Hari Mengajar (Centang Hari Aktif) --}}
          <div id="tambah_hari_mengajar_box" style="grid-column: 1 / -1; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:12px 14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <label style="font-weight:800; font-size:12px; color:var(--text); margin:0;">
                <i class="bi bi-calendar-check-fill" style="color:#000000; margin-right:4px;"></i> Jadwal Hari Wajib Mengajar / Hadir:
              </label>
              <span style="font-size:11px; color:var(--text-3);">Centang hari di mana guru honorer wajib hadir di sekolah</span>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
              @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; cursor:pointer; background:var(--bg-card); padding:5px 12px; border-radius:6px; border:1px solid var(--border);">
                  <input type="checkbox" name="hari_mengajar[]" value="{{ $hari }}" {{ in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']) ? 'checked' : '' }} /> {{ $hari }}
                </label>
              @endforeach
            </div>
            <div style="font-size:11px; color:var(--text-3); margin-top:6px;">
              💡 <em>Guru honor yang tidak hadir di luar jadwal yang dicentang <strong>TIDAK AKAN dialpha</strong> oleh sistem. Jika hadir di luar jadwal, absensi tetap diterima &amp; tercatat sah.</em>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:14px;">
          <button type="button" onclick="toggleTambahGuru(false)" class="btn btn-outline">Batal</button>
          <button type="submit" class="btn btn-gold"><i class="bi bi-check2-circle"></i> Simpan Data Guru</button>
        </div>
      </form>
    </div>
    @endif

    <!-- Tabel Daftar Guru & Toolbar Terpadu -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      {{-- Header & Toolbar Terpadu --}}
      <div class="guru-table-toolbar" style="padding:8px 12px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <div class="guru-table-title" style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:6px;">
          <i class="bi bi-people-fill" style="color:#000000;"></i>
          <span>Daftar Guru &amp; Pegawai</span>
        </div>

        <form method="GET" action="{{ route('guru.index') }}" class="guru-table-form" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; flex:1; justify-content:flex-end; max-width:640px;">
          <div class="guru-search-box" style="position:relative; flex:1.5; min-width:130px;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px;"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, NIP, jabatan..." class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding-left:28px; padding-right:8px;" />
          </div>

          <div class="guru-filter-group" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; flex:2;">
            <div style="min-width:110px; flex:1;">
              <select name="jenis" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
                <option value="">Kepegawaian</option>
                <option value="pns" {{ ($jenis ?? '') === 'pns' ? 'selected' : '' }}>PNS</option>
                <option value="pppk" {{ ($jenis ?? '') === 'pppk' ? 'selected' : '' }}>PPPK</option>
                <option value="honor" {{ ($jenis ?? '') === 'honor' ? 'selected' : '' }}>Honor (GTT)</option>
                <option value="tendik" {{ ($jenis ?? '') === 'tendik' ? 'selected' : '' }}>Tendik/TU</option>
              </select>
            </div>

            <div style="min-width:90px; flex:1;">
              <select name="status" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ ($status ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
              </select>
            </div>

            <div style="min-width:105px; flex:1;">
              <select name="sort" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
                <option value="hirarki" {{ ($sort ?? 'hirarki') === 'hirarki' ? 'selected' : '' }}>Hirarki</option>
                <option value="nama_asc" {{ ($sort ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                <option value="nip_asc" {{ ($sort ?? '') === 'nip_asc' ? 'selected' : '' }}>NIP (Kecil)</option>
                <option value="terbaru" {{ ($sort ?? '') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
              </select>
            </div>

            <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; border-radius:var(--r-sm); flex-shrink:0;">
              Cari
            </button>

            @if($search || ($jenis ?? '') || ($status ?? ''))
              <a href="{{ route('guru.index') }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); border-radius:var(--r-sm); flex-shrink:0;" title="Reset Filter">
                Reset
              </a>
            @endif
          </div>
        </form>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="width:36px; text-align:center;">No</th>
              <th>Guru &amp; Identitas</th>
              <th>Jabatan</th>
              <th>Status Kepegawaian</th>
              <th>Kontak WhatsApp</th>
              <th style="text-align:center;">Kartu RFID</th>
              <th>Akun Login</th>
              <th style="text-align:center;">Status</th>
              <th style="width:80px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($gurus as $idx => $g)
              @php
                $cleanHp = preg_replace('/[^0-9]/', '', $g->no_hp ?? '');
                if (str_starts_with($cleanHp, '0')) { $cleanHp = '62' . substr($cleanHp, 1); }
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12px; vertical-align:middle;">
                  {{ $gurus->firstItem() + $idx }}
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar-circle avatar-md">
                      <img src="{{ $g->foto_url }}" alt="{{ $g->nama }}" class="avatar-img" />
                    </div>
                    <div style="min-width:0;">
                      <div style="font-weight:800; font-size:13px; color:var(--text); line-height:1.25;">{{ $g->nama }}</div>
                      <div style="font-size:11px; font-family:var(--font-mono); color:var(--text-3); margin-top:2px;">
                        {{ $g->nip ? 'NIP: ' . $g->nip : 'Non-NIP' }}
                      </div>
                      @if($g->rombelWali)
                        <span class="badge" style="background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); font-size:9.5px; font-weight:700; margin-top:3px; display:inline-block;">
                          Wali Kelas: {{ $g->rombelWali->nama_rombel }}
                        </span>
                      @endif
                    </div>
                  </div>
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="font-size:12.5px; font-weight:700; color:var(--text);">{{ $g->jabatan }}</div>
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <span style="font-size:11.5px; font-weight:700; color:var(--text-2); text-transform:uppercase; letter-spacing:0.3px;">
                    {{ $g->label_kepegawaian }}
                  </span>
                </td>
                <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                  @if($g->no_hp)
                    <a href="https://wa.me/{{ $cleanHp }}" target="_blank" style="font-size:12px; font-weight:700; font-family:var(--font-mono); text-decoration:none; display:inline-block; color:var(--text); white-space:nowrap; transition:color .15s ease;" onmouseover="this.style.color='#25D366'" onmouseout="this.style.color='var(--text)'" title="Chat WhatsApp">
                      {{ $g->no_hp }}
                    </a>
                  @else
                    <span style="color:var(--text-3); font-size:12px;">-</span>
                  @endif
                </td>

                {{-- Kartu RFID --}}
                <td style="vertical-align:middle; text-align:center; padding:10px 12px; white-space:nowrap;">
                  @php $kartu = $g->kartuRfid; @endphp
                  @if($kartu)
                    @if($isAdmin || $isStafTu)
                      <button type="button"
                        onclick="openRfidPairModal('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian }}', '{{ $g->foto_url }}', '{{ $kartu->uid }}')"
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
                    @if($isAdmin || $isStafTu)
                      <button type="button"
                        onclick="openRfidPairModal('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian }}', '{{ $g->foto_url }}', '')"
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
                <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                  @if($g->user)
                    @if($isAdmin || $isStafTu)
                      <button type="button" onclick="openAkunModal({{ json_encode($g) }})" style="background:transparent; border:none; padding:2px 0; font-size:12px; font-weight:700; color:var(--text); cursor:pointer; white-space:nowrap; text-align:left;" title="Klik untuk atur akun (Nickname: {{ $g->user->username }})">
                        <span style="text-transform:capitalize; display:block;">{{ str_replace('_', ' ', $g->user->role) }}</span>
                        <span style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono); font-weight:600;">{{ $g->user->username ?: ($g->user->email ? explode('@', $g->user->email)[0] : '-') }}</span>
                      </button>
                    @else
                      <div>
                        <span style="font-size:12px; font-weight:700; color:var(--text); text-transform:capitalize; display:block;">
                          {{ str_replace('_', ' ', $g->user->role) }}
                        </span>
                        <span style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono);">
                          {{ $g->user->username ?: ($g->user->email ? explode('@', $g->user->email)[0] : '-') }}
                        </span>
                      </div>
                    @endif
                  @else
                    @if($isAdmin || $isStafTu)
                      <button type="button" onclick="openAkunModal({{ json_encode($g) }})" style="background:transparent; border:none; padding:4px 0; font-size:11.5px; font-weight:800; color:var(--text-2); cursor:pointer; white-space:nowrap;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-2)'" title="Buat Akun Login dengan Nickname/Username">
                        + Buat Akun
                      </button>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  @endif
                </td>
                <td style="vertical-align:middle; text-align:center; padding:10px 8px; white-space:nowrap;">
                  <span style="font-weight:800; font-size:11px; text-transform:uppercase; color:{{ $g->status === 'aktif' ? 'var(--text)' : 'var(--text-3)' }};">
                    {{ $g->status }}
                  </span>
                </td>
                <td style="vertical-align:middle; text-align:center; padding:10px 8px; white-space:nowrap;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                    <a href="{{ route('kartu.digital.guru', ['id' => $g->id]) }}" target="_blank"
                       class="btn-icon btn-icon-view"
                       style="width:30px; height:30px; text-decoration:none;"
                       title="Lihat Barcode &amp; Kartu Digital Guru">
                       <i class="bi bi-qr-code-scan"></i>
                    </a>
                    <button type="button" onclick="openEditGuru({{ json_encode($g) }})" class="btn-icon btn-icon-edit" style="width:30px; height:30px;" title="Edit Data Guru">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <form action="/guru/{{ $g->id }}" method="POST" onsubmit="return confirm('Hapus data guru {{ $g->nama }}?')" style="display:inline; margin:0;">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn-icon btn-icon-danger" style="width:30px; height:30px;" title="Hapus Guru">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align:center; padding:36px; color:var(--text-3);">
                  <i class="bi bi-person-x" style="font-size:32px; opacity:0.4;"></i>
                  <div style="font-weight:700; margin-top:8px; font-size:14px; color:var(--text);">Tidak ada data guru yang cocok</div>
                  <p style="font-size:12px; margin-top:4px;">Coba gunakan kata kunci pencarian lain atau klik Reset.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- PAGINATION CONTROLS (20 PER HALAMAN) --}}
      @if($gurus->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:center;">
          {{ $gurus->links() }}
        </div>
      @endif
    </div>
  </main>
</div>

<!-- Modal Edit Guru -->
<div id="editGuruModal" class="modal-overlay">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-pencil-square" style="color:#000000;"></i> Edit Data Guru / Pegawai
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('editGuruModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form id="editGuruForm" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">NIP (Opsional)</label>
          <input type="text" id="edit_guru_nip" name="nip" class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Lengkap &amp; Gelar <span style="color:var(--red);">*</span></label>
          <input type="text" id="edit_guru_nama" name="nama" required class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Jabatan / Penugasan <span style="color:var(--red);">*</span></label>
          <input type="text" id="edit_guru_jabatan" name="jabatan" required class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">No. WhatsApp / HP</label>
          <input type="text" id="edit_guru_no_hp" name="no_hp" class="input-field" style="width:100%;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Status Kepegawaian <span style="color:var(--red);">*</span></label>
          <select id="edit_guru_jenis_kepegawaian" name="jenis_kepegawaian" onchange="toggleHariMengajar('edit', this.value)" class="input-field" style="width:100%;">
            <option value="pns">PNS (Pegawai Negeri Sipil)</option>
            <option value="pppk">PPPK (P3K)</option>
            <option value="honor">Guru Honor (GTT)</option>
            <option value="tendik">Tenaga Kependidikan (TU/Staf)</option>
          </select>
        </div>

        <div id="edit_hari_mengajar_box" style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:10px 12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:4px;">
            <label style="font-weight:800; font-size:11.5px; color:var(--text); margin:0;">
              <i class="bi bi-calendar-check-fill" style="color:#000000; margin-right:4px;"></i> Jadwal Hari Wajib Mengajar / Hadir:
            </label>
            <span style="font-size:10.5px; color:var(--text-3);">Centang hari wajib hadir</span>
          </div>
          <div style="display:flex; flex-wrap:wrap; gap:8px;">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
              <label style="display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:700; cursor:pointer; background:var(--bg-card); padding:4px 8px; border-radius:6px; border:1px solid var(--border);">
                <input type="checkbox" name="hari_mengajar[]" value="{{ $hari }}" class="edit-hari-cb" data-hari="{{ $hari }}" /> {{ $hari }}
              </label>
            @endforeach
          </div>
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Status Keaktifan</label>
          <select id="edit_guru_status" name="status" class="input-field" style="width:100%;">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:flex; justify-content:space-between; margin-bottom:4px;">
            <span>Ganti Foto Profil</span>
            <span style="color:#000000; font-size:11px; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
          </label>
          <div style="display:flex; align-items:center; gap:10px;">
            <div id="edit_guru_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid rgba(0,0,0,0.15); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
              <img id="edit_guru_foto_img" src="/img/user-default.png" style="width:100%; height:100%; object-fit:cover;" />
            </div>
            <input type="file" name="foto" id="inputFotoGuruEdit" accept="image/*" onchange="initPhotoCrop(this, 'edit_guru_foto_img', '1:1', 'Potong Foto Profil Guru')" class="input-field" style="flex:1;" />
          </div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:18px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('editGuruModal')">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Atur Akun Login Guru / Kepala Sekolah -->
<div id="akunGuruModal" class="modal-overlay">
  <div class="modal-card" style="max-width:480px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-person-lock" style="color:#000000;"></i> Atur Akun Login Pengguna
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('akunGuruModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border-radius:var(--r-sm); padding:10px 14px; margin-bottom:16px;">
      <div style="font-weight:800; font-size:13.5px; color:var(--text);" id="akun_guru_nama_display">-</div>
      <div style="font-size:11.5px; color:var(--text-3);" id="akun_guru_jabatan_display">-</div>
    </div>

    <form id="akunGuruForm" method="POST">
      @csrf
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nickname / Username Login <span style="color:var(--red);">*</span></label>
          <input type="text" id="akun_username" name="username" required class="input-field" placeholder="Contoh: sugeng, agihusni, atau NIP" style="width:100%;" />
          <span style="font-size:10.5px; color:var(--text-3); margin-top:2px; display:block;">Bisa berupa nama panggilan (contoh: agihusni, sugeng, budi), inisial, atau NIP tanpa spasi. <strong>Tidak wajib menggunakan email</strong>.</span>
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Email (Opsional / Tidak Wajib)</label>
          <input type="email" id="akun_email" name="email" class="input-field" placeholder="Boleh dikosongkan (tidak wajib)" style="width:100%;" />
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Kata Sandi (Password)</label>
          <input type="password" id="akun_password" name="password" class="input-field" placeholder="Minimal 4 karakter" style="width:100%;" />
          <span style="font-size:10.5px; color:var(--text-3); margin-top:2px; display:block;" id="akun_password_hint">Biarkan kosong jika tidak ingin mengubah password lama.</span>
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Hak Akses / Peran Sistem <span style="color:var(--red);">*</span></label>
          <select id="akun_role" name="role" required class="input-field" style="width:100%;">
            <option value="admin">Administrator Sistem (Akses Penuh)</option>
            <option value="kepala_sekolah">Kepala Sekolah (Dasbor Eksekutif &amp; Pengesahan Kasus)</option>
            <option value="waka_kesiswaan">Waka Kesiswaan (Disiplin Tahap 3, Transisi PKL, Pengawasan)</option>
            <option value="waka_kurikulum">Waka Kurikulum (Jadwal &amp; Laporan Jam Mengajar)</option>
            <option value="guru_bk">Guru BK / Konseling (Penanganan Kasus Tahap 2 &amp; Panggilan Ortu)</option>
            <option value="wali_kelas">Wali Kelas (Monitoring Kelas Binaan &amp; Pembinaan Tahap 1)</option>
            <option value="guru_piket">Guru Piket (Operasional Meja Piket &amp; Perizinan Siswa)</option>
            <option value="staf_tu">Staf Tata Usaha / Kepegawaian (Data Master Siswa/Guru)</option>
            <option value="guru">Guru Mata Pelajaran (Presensi &amp; Jadwal Mengajar)</option>
          </select>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
          <div id="akunDeleteBtnContainer"></div>
          <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-outline" onclick="closeModal('akunGuruModal')">Batal</button>
            <button type="submit" class="btn btn-gold">Simpan Akun</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Import Guru CSV -->
<div id="importGuruModal" class="modal-overlay">
  <div class="modal-card" style="max-width:520px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-file-earmark-arrow-up-fill" style="color:#000000;"></i> Import Data Guru &amp; Pegawai
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('importGuruModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--surface); border:1px solid var(--border-2); border-radius:12px; padding:12px 14px; margin-bottom:16px; font-size:12px; line-height:1.5;">
      <div style="font-weight:800; color:#000000; display:flex; align-items:center; gap:6px; margin-bottom:4px;">
        <i class="bi bi-info-circle-fill"></i> Format Kolom CSV Fleksibel
      </div>
      <div style="color:var(--text-2);">
        Sistem otomatis mengenali format kolom (Nama, NIP, Jabatan/Mapel, Status Kepegawaian, dan No WhatsApp).
      </div>
      <div style="margin-top:8px;">
        <a href="{{ route('guru.template-csv') }}" class="btn btn-sm btn-outline-mono" style="font-weight:800; font-size:11.5px; display:inline-flex; align-items:center; gap:6px; background:var(--bg-2); text-decoration:none;">
          <i class="bi bi-download" style="color:#000000;"></i> Unduh Contoh Template CSV Guru
        </a>
      </div>
    </div>

    <form action="/guru/import" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom:16px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:6px;">Pilih File CSV / Excel (.csv) <span style="color:var(--red);">*</span></label>
        <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="input-field" style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal('importGuruModal')">Batal</button>
        <button type="submit" class="btn btn-gold"><i class="bi bi-cloud-arrow-up-fill"></i> Mulai Proses Import</button>
      </div>
    </form>
  </div>
</div>

@include('partials.crop_modal')

<script>
  function toggleHariMengajar(context, val) {
    const box = document.getElementById(context + '_hari_mengajar_box');
    if (box) {
      if (val === 'honor') {
        box.style.display = 'block';
      } else {
        box.style.display = 'block';
      }
    }
  }

  function toggleTambahGuru(forceState) {
    const panel = document.getElementById('panelTambahGuru');
    const text = document.getElementById('textToggleTambahGuru');
    const isHidden = (panel.style.display === 'none' || panel.style.display === '');
    const show = (forceState !== undefined) ? forceState : isHidden;
    
    panel.style.display = show ? 'block' : 'none';
    if (text) {
      text.innerText = show ? 'Tutup Form' : 'Tambah Guru';
    }
    if (show) {
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function openEditGuru(guru) {
    document.getElementById('edit_guru_nip').value = guru.nip || '';
    document.getElementById('edit_guru_nama').value = guru.nama || '';
    document.getElementById('edit_guru_jabatan').value = guru.jabatan || '';
    document.getElementById('edit_guru_jenis_kepegawaian').value = guru.jenis_kepegawaian || 'pns';
    document.getElementById('edit_guru_no_hp').value = guru.no_hp || '';
    document.getElementById('edit_guru_status').value = guru.status || 'aktif';
    
    const imgPreview = document.getElementById('edit_guru_foto_img');
    if (imgPreview) {
      imgPreview.src = guru.foto_url || '/img/user-default.png';
    }

    // Set hari mengajar checkboxes
    const hariList = (guru.hari_mengajar && Array.isArray(guru.hari_mengajar)) ? guru.hari_mengajar : ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    document.querySelectorAll('.edit-hari-cb').forEach(cb => {
      cb.checked = hariList.includes(cb.getAttribute('data-hari'));
    });

    document.getElementById('editGuruForm').action = '/guru/' + guru.id;
    openModal('editGuruModal');
  }

  function openAkunModal(guru) {
    document.getElementById('akun_guru_nama_display').innerText = guru.nama;
    document.getElementById('akun_guru_jabatan_display').innerText = guru.jabatan || 'Guru / Pegawai';
    
    const form = document.getElementById('akunGuruForm');
    const delContainer = document.getElementById('akunDeleteBtnContainer');
    
    if (guru.user) {
      document.getElementById('akun_username').value = guru.user.username || (guru.user.email ? guru.user.email.split('@')[0] : (guru.nip || ''));
      document.getElementById('akun_email').value = guru.user.email || '';
      document.getElementById('akun_role').value = guru.user.role || 'guru';
      document.getElementById('akun_password').value = '';
      document.getElementById('akun_password_hint').style.display = 'block';
      form.action = '/guru/' + guru.id + '/akun';
      delContainer.innerHTML = `
        <form action="/guru/${guru.id}/akun" method="POST" onsubmit="return confirm('Hapus akun login guru ini?')" style="display:inline;">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i> Hapus Akun</button>
        </form>
      `;
    } else {
      let cleanName = (guru.nama || '').toLowerCase().replace(/[^a-z0-9]/g, ' ').trim();
      let defaultUsername = guru.nip ? guru.nip : (cleanName.split(/\s+/)[0] || 'guru');
      
      document.getElementById('akun_username').value = defaultUsername;
      document.getElementById('akun_email').value = '';

      const jab = (guru.jabatan || '').toLowerCase();
      let defaultRole = 'guru';
      if (jab.includes('kepala sekolah') && !jab.includes('wakil') && !jab.includes('waka')) defaultRole = 'kepala_sekolah';
      else if (jab.includes('waka kesiswaan') || jab.includes('kesiswaan')) defaultRole = 'waka_kesiswaan';
      else if (jab.includes('waka kurikulum') || jab.includes('kurikulum')) defaultRole = 'waka_kurikulum';
      else if (jab.includes('bk') || jab.includes('bimbingan')) defaultRole = 'guru_bk';
      else if (jab.includes('tata usaha') || jab.includes('tu') || jab.includes('staf') || jab.includes('administrasi')) defaultRole = 'staf_tu';
      else if (jab.includes('piket')) defaultRole = 'guru_piket';
      else if (guru.rombels && guru.rombels.length > 0) defaultRole = 'wali_kelas';

      document.getElementById('akun_role').value = defaultRole;
      document.getElementById('akun_password').value = '';
      document.getElementById('akun_password_hint').style.display = 'none';
      form.action = '/guru/' + guru.id + '/akun';
      delContainer.innerHTML = '';
    }
    
    openModal('akunGuruModal');
  }

  function openModal(id) { document.getElementById(id).classList.add('active'); }
  function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>

@if($isAdmin || $isStafTu)
  @include('partials.rfid_pair_modal')
@endif
</body>
</html>
