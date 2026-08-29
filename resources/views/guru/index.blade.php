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
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
      margin-bottom: 20px;
    }
    .guru-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all .2s ease;
    }
    .guru-stat-card:hover {
      border-color: var(--border-2);
      transform: translateY(-2px);
    }
    .guru-stat-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }
    .guru-stat-val {
      font-size: 22px;
      font-weight: 900;
      font-family: var(--font);
      line-height: 1.1;
      color: var(--text);
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
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-person-badge-fill" style="color:var(--gold);"></i> Master Data Guru &amp; Pegawai
        </h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          Kelola data pendidik, tenaga kependidikan, nomor WhatsApp, dan hak akses login.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <button type="button" id="btnToggleTambahGuru" onclick="toggleTambahGuru()" class="btn btn-gold" style="height:38px; padding:0 16px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-person-plus-fill"></i> <span id="textToggleTambahGuru">+ Tambah Guru</span>
        </button>
        <button type="button" onclick="openModal('importGuruModal')" class="btn btn-outline" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:700;">
          <i class="bi bi-file-earmark-arrow-up-fill" style="margin-right:4px;"></i>Import CSV
        </button>
        <a href="/guru/export" class="btn btn-outline" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:700; text-decoration:none; color:var(--green); border-color:var(--green);" title="Unduh CSV Kompatibel Excel">
          <i class="bi bi-file-earmark-excel-fill" style="margin-right:4px;"></i>Export Excel
        </a>
        <a href="/guru/cetak-pdf" target="_blank" class="btn btn-outline" style="height:38px; padding:0 14px; font-size:12.5px; font-weight:700; text-decoration:none; color:var(--text);" title="Cetak Format A4 Kop Dinas">
          <i class="bi bi-file-earmark-pdf-fill" style="margin-right:4px; color:var(--gold);"></i>Cetak PDF
        </a>
      </div>
    </header>

    @if(session('success'))<div class="alert-success" style="margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:16px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- KPI STAT CARDS --}}
    <div class="guru-stat-grid">
      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statTotal }}</div>
          <div class="guru-stat-lbl">Total Guru &amp; Pegawai</div>
        </div>
      </div>

      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statWali }}</div>
          <div class="guru-stat-lbl">Ditugaskan Wali Kelas</div>
        </div>
      </div>

      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-camera-video-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ \App\Models\Guru::whereNotNull('face_embedding')->count() }}</div>
          <div class="guru-stat-lbl">Face ID Terdaftar</div>
        </div>
      </div>

      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--gold);">
          <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statAkun }}</div>
          <div class="guru-stat-lbl">Memiliki Akun Login</div>
        </div>
      </div>
    </div>

    <!-- Form Tambah Guru (Collapsible / Triggered) -->
    <div class="panel" id="panelTambahGuru" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom: 20px; border-color: rgba(234, 179, 8, 0.4); background: linear-gradient(180deg, rgba(234, 179, 8, 0.04) 0%, var(--panel) 100%);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="stat-icon" style="width:36px; height:36px; border-radius:8px; background:rgba(234, 179, 8, 0.15); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <span style="font-weight:800; font-size:15px; color:var(--text);">Form Tambah Guru &amp; Tenaga Kependidikan</span>
            <div style="font-size:12px; color:var(--text-3);">Lengkapi profil tenaga pendidik/kependidikan untuk presensi Face ID.</div>
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
              <span style="color:var(--gold); font-size:11px; text-transform:none;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
            </label>
            <div style="display:flex; align-items:center; gap:10px;">
              <div id="tambah_guru_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid var(--border-2); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                <i class="bi bi-person-fill" style="color:var(--text-3); font-size:20px;"></i>
              </div>
              <input type="file" name="foto" id="inputFotoGuruTambah" accept="image/*" onchange="initPhotoCrop(this, 'tambah_guru_foto_preview', '1:1', 'Potong Foto Profil Guru')" style="flex:1; height:40px;" />
            </div>
          </div>

          {{-- Baris 3: Jadwal Hari Mengajar (Centang Hari Aktif) --}}
          <div id="tambah_hari_mengajar_box" style="grid-column: 1 / -1; background:rgba(202,138,4,0.06); border:1px solid rgba(202,138,4,0.25); border-radius:var(--r-md); padding:12px 14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <label style="font-weight:800; font-size:12px; color:var(--text); margin:0;">
                <i class="bi bi-calendar-check-fill" style="color:var(--gold); margin-right:4px;"></i> Jadwal Hari Wajib Mengajar / Hadir:
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

    <!-- Tabel Daftar Guru & Toolbar Terpadu -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      {{-- Header Tabel --}}
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-people-fill" style="color:var(--gold);"></i>
          <span>Daftar Guru &amp; Tenaga Kependidikan</span>
        </div>
        <div>
          <button type="button" onclick="toggleTambahGuru(true)" class="btn btn-gold" style="padding:6px 14px; font-size:12px; font-weight:800; border-radius:var(--r-sm);">
            <i class="bi bi-plus-lg"></i> Tambah Guru
          </button>
        </div>
      </div>

      {{-- Toolbar Search & Filter Terpadu --}}
      <div style="padding:12px 18px; border-bottom:1px solid var(--border); background:var(--surface);">
        <form method="GET" action="{{ route('guru.index') }}" style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
          <div style="flex:1.8; min-width:200px;">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama guru, NIP, jabatan..." class="input-field" style="width:100%; height:36px; font-size:12.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px;" />
          </div>

          <div style="min-width:140px;">
            <select name="kepegawaian" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Pegawai</option>
              <option value="pns" {{ ($kepegawaian ?? '') === 'pns' ? 'selected' : '' }}>PNS</option>
              <option value="pppk" {{ ($kepegawaian ?? '') === 'pppk' ? 'selected' : '' }}>PPPK</option>
              <option value="honor" {{ ($kepegawaian ?? '') === 'honor' ? 'selected' : '' }}>Guru Honor (GTT)</option>
              <option value="tendik" {{ ($kepegawaian ?? '') === 'tendik' ? 'selected' : '' }}>Tendik / TU</option>
            </select>
          </div>

          <div style="min-width:140px;">
            <select name="kategori" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Jabatan</option>
              <option value="wali_kelas" {{ $kategori === 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
              <option value="bk" {{ $kategori === 'bk' ? 'selected' : '' }}>Guru BK</option>
              <option value="pimpinan" {{ $kategori === 'pimpinan' ? 'selected' : '' }}>Pimpinan / Waka</option>
              <option value="staf" {{ $kategori === 'staf' ? 'selected' : '' }}>Tata Usaha / Staf</option>
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
              <option value="nip_asc" {{ $sort === 'nip_asc' ? 'selected' : '' }}>NIP (Terkecil)</option>
              <option value="nip_desc" {{ $sort === 'nip_desc' ? 'selected' : '' }}>NIP (Terbesar)</option>
              <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Data Terbaru</option>
            </select>
          </div>

          <button type="submit" class="btn btn-outline" style="height:36px; padding:0 12px; font-size:12px; font-weight:700;">
            <i class="bi bi-search"></i> Cari
          </button>

          @if($search || $kategori || ($kepegawaian ?? '') || $rfidStatus || $status || ($sort && $sort !== 'nama_asc'))
            <a href="{{ route('guru.index') }}" class="btn btn-outline" style="height:36px; padding:0 10px; font-size:12px; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              Reset
            </a>
          @endif
        </form>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr style="background:var(--bg-3);">
              <th style="width:45px; text-align:center; padding:12px 8px;">No</th>
              <th style="min-width:240px; padding:12px 12px;">Guru &amp; Identitas</th>
              <th style="min-width:160px; padding:12px 12px;">Jabatan &amp; Tugas</th>
              <th style="min-width:120px; padding:12px 12px;">Status Pegawai</th>
              <th style="min-width:140px; padding:12px 12px;">Kontak WhatsApp</th>
              <th style="min-width:140px; padding:12px 12px;">Face ID</th>
              <th style="min-width:140px; padding:12px 12px;">Akun Sistem</th>
              <th style="width:85px; text-align:center; padding:12px 8px;">Status</th>
              <th style="width:90px; text-align:center; padding:12px 8px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($gurus as $idx => $g)
              @php
                $cleanHp = preg_replace('/[^0-9]/', '', $g->no_hp ?? '');
                if (str_starts_with($cleanHp, '0')) { $cleanHp = '62' . substr($cleanHp, 1); }
              @endphp
              <tr style="border-bottom:1px solid var(--border);">
                <td style="text-align:center; font-weight:700; color:var(--text); font-size:12px; vertical-align:middle;">
                  {{ $gurus->firstItem() + $idx }}
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="display:flex; align-items:center; gap:10px;">
                    <img src="{{ $g->foto_url }}" alt="{{ $g->nama }}" style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid rgba(202,138,4,0.3); flex-shrink:0;" />
                    <div style="min-width:0;">
                      <div style="font-weight:800; font-size:13px; color:var(--text); line-height:1.25;">{{ $g->nama }}</div>
                      <div style="font-size:11px; font-family:var(--font-mono); color:var(--text); margin-top:2px;">
                        {{ $g->nip ? 'NIP: ' . $g->nip : 'Non-NIP' }}
                      </div>
                      @if($g->rombelWali)
                        <span class="badge" style="background:rgba(202,138,4,0.12); color:var(--gold); border:1px solid rgba(202,138,4,0.3); font-size:9.5px; font-weight:800; margin-top:3px; display:inline-block;">
                          Wali Kelas: {{ $g->rombelWali->nama_rombel }}
                        </span>
                      @endif
                    </div>
                  </div>
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="font-size:12.5px; font-weight:800; color:var(--text);">{{ $g->jabatan }}</div>
                  @if($g->isHonor() && !empty($g->hari_mengajar))
                    <div style="font-size:10.5px; color:var(--text); margin-top:2px;">
                      Jadwal: {{ implode(', ', $g->hari_mengajar) }}
                    </div>
                  @endif
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <span class="badge {{ $g->jenis_kepegawaian === 'pns' ? 'badge-pns' : ($g->jenis_kepegawaian === 'pppk' ? 'badge-pppk' : 'badge-honor') }}" style="font-weight:800; font-size:10.5px;">
                    {{ $g->label_kepegawaian }}
                  </span>
                </td>
                <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                  @if($g->no_hp)
                    <a href="https://wa.me/{{ $cleanHp }}" target="_blank" style="color:#16A34A; font-size:11px; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:4px; background:rgba(22,163,74,0.1); padding:0 10px; height:30px; border-radius:6px; border:1px solid rgba(22,163,74,0.25); white-space:nowrap;" title="Chat WhatsApp">
                      <i class="bi bi-whatsapp"></i> {{ $g->no_hp }}
                    </a>
                  @else
                    <span style="color:var(--text); font-size:12px;">-</span>
                  @endif
                </td>
                <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                  @if($g->face_embedding)
                    <div style="display:inline-flex; align-items:center; gap:4px; white-space:nowrap;">
                      <button type="button" onclick="openViewFaceModal('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian }}', '{{ $g->foto_url }}', '{{ $g->face_registered_at?->translatedFormat('d F Y, H:i') ?? 'Terdaftar Aktif' }}')" class="btn btn-sm" style="padding:0 9px; height:30px; font-size:11px; font-weight:800; background:rgba(16,185,129,0.12); color:#10B981; border:1px solid rgba(16,185,129,0.28); border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;" title="Lihat Status Biometrik">
                        <i class="bi bi-eye-fill"></i> Terdaftar
                      </button>
                      <button type="button" onclick="quickDeleteFace('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}')" class="btn btn-sm btn-outline" style="width:30px; height:30px; padding:0; font-size:11px; color:#EF4444; border-color:rgba(239,68,68,0.3); border-radius:6px; display:inline-flex; align-items:center; justify-content:center;" title="Hapus Face ID">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </div>
                  @else
                    <button type="button" onclick="openFaceEnrollModal('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian }}', '{{ $g->foto_url }}')" class="btn btn-sm btn-outline" style="padding:0 9px; height:30px; font-size:11px; font-weight:800; color:var(--gold); border-color:var(--gold); border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;" title="Daftarkan Wajah">
                      <i class="bi bi-camera-fill"></i> Rekam Wajah
                    </button>
                  @endif
                </td>
                <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                  @if($g->user)
                    <button type="button" onclick="openAkunModal({{ json_encode($g) }})" class="btn btn-sm btn-outline" style="padding:0 10px; height:30px; font-size:11px; font-weight:800; border-radius:6px; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; background:var(--bg-2); border-color:var(--border-2); color:var(--text);" title="Klik untuk lihat &amp; atur akun (Email: {{ $g->user->email }})">
                      <i class="bi bi-person-badge-fill" style="color:var(--gold);"></i>
                      <span>Lihat Akun</span>
                      <span style="background:rgba(147,51,234,0.12); color:#9333EA; font-size:9px; font-weight:800; text-transform:uppercase; border:1px solid rgba(147,51,234,0.28); padding:1px 5px; border-radius:4px; line-height:1;">
                        {{ str_replace('_', ' ', $g->user->role) }}
                      </span>
                    </button>
                  @else
                    <button type="button" onclick="openAkunModal({{ json_encode($g) }})" class="btn btn-sm btn-outline" style="padding:0 10px; height:30px; font-size:11px; font-weight:800; color:var(--gold); border-color:var(--gold); border-radius:6px; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;" title="Buat Akun Login Sistem">
                      <i class="bi bi-person-plus-fill"></i> + Buat Akun
                    </button>
                  @endif
                </td>
                <td style="vertical-align:middle; text-align:center; padding:10px 8px; white-space:nowrap;">
                  @if($g->status === 'aktif')
                    <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; font-weight:800; font-size:10px;">AKTIF</span>
                  @else
                    <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-weight:800; font-size:10px;">NONAKTIF</span>
                  @endif
                </td>
                <td style="vertical-align:middle; text-align:center; padding:10px 8px; white-space:nowrap;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
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
        <i class="bi bi-pencil-square" style="color:var(--gold);"></i> Edit Data Guru / Pegawai
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

        <div id="edit_hari_mengajar_box" style="background:rgba(202,138,4,0.06); border:1px solid rgba(202,138,4,0.25); border-radius:var(--r-md); padding:10px 12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:4px;">
            <label style="font-weight:800; font-size:11.5px; color:var(--text); margin:0;">
              <i class="bi bi-calendar-check-fill" style="color:var(--gold); margin-right:4px;"></i> Jadwal Hari Wajib Mengajar / Hadir:
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
            <span style="color:var(--gold); font-size:11px;"><i class="bi bi-crop"></i> Auto-Crop Aktif</span>
          </label>
          <div style="display:flex; align-items:center; gap:10px;">
            <div id="edit_guru_foto_preview" style="width:40px; height:40px; border-radius:50%; border:1.5px solid var(--gold); background:var(--bg-3); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
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
        <i class="bi bi-person-lock" style="color:var(--gold);"></i> Atur Akun Login Pengguna
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
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Email Akun Login <span style="color:var(--red);">*</span></label>
          <input type="email" id="akun_email" name="email" required class="input-field" style="width:100%;" />
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Kata Sandi (Password)</label>
          <input type="password" id="akun_password" name="password" class="input-field" placeholder="Minimal 6 karakter" style="width:100%;" />
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
        <i class="bi bi-file-earmark-arrow-up-fill" style="color:var(--gold);"></i> Import Data Guru &amp; Pegawai
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('importGuruModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--surface); border:1px solid var(--border-2); border-radius:12px; padding:12px 14px; margin-bottom:16px; font-size:12px; line-height:1.5;">
      <div style="font-weight:800; color:var(--gold); display:flex; align-items:center; gap:6px; margin-bottom:4px;">
        <i class="bi bi-info-circle-fill"></i> Format Kolom CSV Fleksibel
      </div>
      <div style="color:var(--text-2);">
        Sistem otomatis mengenali format kolom (Nama, NIP, Jabatan/Mapel, Status Kepegawaian, dan No WhatsApp).
      </div>
      <div style="margin-top:8px;">
        <a href="{{ route('guru.template-csv') }}" class="btn btn-sm btn-outline" style="font-weight:700; font-size:11.5px; display:inline-flex; align-items:center; gap:6px; background:var(--bg-2);">
          <i class="bi bi-download" style="color:var(--gold);"></i> Unduh Contoh Template CSV Guru
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
    text.innerText = show ? 'Tutup Form' : '+ Tambah Guru';
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
      document.getElementById('akun_email').value = guru.user.email;
      document.getElementById('akun_role').value = guru.user.role;
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
      let defaultEmail = guru.nama.toLowerCase().replace(/[^a-z0-9]/g, '.').replace(/\.+/g, '.').replace(/^\.|\.$/g, '') + '@smkn1airnaningan.sch.id';
      document.getElementById('akun_email').value = defaultEmail;

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

@include('partials.face_enroll_modal')
</body>
</html>
