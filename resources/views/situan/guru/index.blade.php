<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data PTK (Pendidik &amp; Tenaga Kependidikan) — SITUAN SMKN 1 Air Naningan</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/situan-app.css') }}?v={{ filemtime(public_path('css/situan-app.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/guru.css') }}?v={{ filemtime(public_path('css/guru.css')) }}">
  <style>
    .gtk-tab-nav {
      display: flex;
      gap: 6px;
      border-bottom: 2px solid var(--border);
      margin-bottom: 18px;
      overflow-x: auto;
      padding-bottom: 2px;
    }
    .gtk-tab-btn {
      background: none;
      border: none;
      padding: 8px 16px;
      font-size: 12.5px;
      font-weight: 800;
      color: var(--text-3);
      border-radius: 6px 6px 0 0;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
      transition: all .15s ease;
    }
    .gtk-tab-btn:hover {
      color: var(--text);
      background: var(--bg-3);
    }
    .gtk-tab-btn.active {
      color: #000000;
      border-bottom: 2.5px solid #000000;
      background: rgba(0, 0, 0, 0.04);
    }
    .badge-sertifikasi-sudah {
      background: #ecfdf5;
      color: #059669;
      border: 1px solid rgba(5, 150, 105, 0.3);
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    /* Modal Card Statis Tanpa Scroll Vertikal */
    .modal-card-static {
      width: 100% !important;
      max-width: 880px !important;
      height: 475px !important;
      max-height: 94vh !important;
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden !important;
      padding: 16px 22px !important;
      box-sizing: border-box !important;
    }
    .modal-card-static form {
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 0;
      overflow: hidden;
    }
    .gtk-tab-body-scroll {
      flex: 1;
      min-height: 0;
      overflow: hidden !important;
      padding-right: 0;
      padding-bottom: 0;
    }
    .modal-footer-static {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 8px;
      margin-top: auto;
      padding-top: 10px;
      border-top: 1px solid var(--border);
      flex-shrink: 0;
      background: var(--bg-2);
    }
    @media (max-width: 768px) {
      .modal-card-static {
        height: 94vh !important;
        max-height: 94vh !important;
        padding: 14px 16px !important;
      }
      .gtk-tab-body-scroll {
        overflow-y: auto !important;
      }
    }
    .badge-sertifikasi-belum {
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #cbd5e1;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .btn-sertifikat-portofolio {
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid rgba(37, 99, 235, 0.25);
      border-radius: 6px;
      padding: 4px 10px;
      font-size: 11.5px;
      font-weight: 800;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      text-decoration: none;
      transition: all .15s ease;
    }
    .btn-sertifikat-portofolio:hover {
      background: #2563eb;
      color: #ffffff;
    }
  </style>
</head>
<body class="situan-body">
<div class="situan-layout">
  @include('partials.sidebar_situan')
  <main class="situan-main">
    <div class="situan-content">
      @php
        $currentUser = auth()->user();
        $isAdmin = $currentUser && $currentUser->isAdmin();
        $isStafTu = $currentUser && $currentUser->isStafTu();
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
              <span style="color:#0284c7; font-weight:800;">PTK (Pendidik &amp; Tendik)</span>
            </div>
            <h1 class="situan-page-title" style="margin-top:2px; font-size:20px;">Data PTK &amp; Tenaga Kependidikan</h1>
          </div>
        </div>

        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          @if($isAdmin || $isStafTu)
            <button type="button" id="btnToggleTambahGuru" onclick="toggleTambahGuru()" class="situan-btn-primary">
              <i class="bi bi-person-plus-fill" id="iconToggleTambahGuru"></i>
              <span id="textToggleTambahGuru">Tambah GTK</span>
            </button>
            <button type="button" onclick="openModal('importGuruModal')" class="situan-btn-outline">
              <i class="bi bi-file-earmark-arrow-up-fill" style="color:#0284c7;"></i> Import CSV
            </button>
          @endif
          <a href="/guru/export" class="situan-btn-outline" title="Unduh CSV Lengkap Kompatibel Excel">
            <i class="bi bi-file-earmark-excel-fill" style="color:#10b981;"></i> Export CSV
          </a>
          <a href="/guru/cetak-pdf" target="_blank" class="situan-btn-outline" title="Cetak Format A4 Kop Dinas">
            <i class="bi bi-file-earmark-pdf-fill" style="color:#ef4444;"></i> Cetak PDF
          </a>
          @include('partials.header_actions')
        </div>
      </div>

    @if(session('success'))<div class="alert-success" style="margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:16px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- KPI STAT CARDS --}}
    <div class="guru-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));">
      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val">{{ $statTotal }}</div>
          <div class="guru-stat-lbl">Total GTK Guru &amp; Tendik</div>
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
        <div class="guru-stat-icon" style="background:rgba(5, 150, 105, 0.08); border:1px solid rgba(5, 150, 105, 0.25); color:#059669;">
          <i class="bi bi-award-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val" style="color:#059669;">{{ $statSertifikasi }}</div>
          <div class="guru-stat-lbl">Sudah Sertifikasi Pendidik</div>
        </div>
      </div>

      <div class="guru-stat-card">
        <div class="guru-stat-icon" style="background:rgba(37, 99, 235, 0.08); border:1px solid rgba(37, 99, 235, 0.25); color:#2563eb;">
          <i class="bi bi-file-earmark-check-fill"></i>
        </div>
        <div>
          <div class="guru-stat-val" style="color:#2563eb;">{{ $statTotalSertifikat }}</div>
          <div class="guru-stat-lbl">Portofolio Pelatihan Guru</div>
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
    <!-- Form Tambah Guru (Collapsible / Tabbed Bento) -->
    <div class="panel" id="panelTambahGuru" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom: 20px; border-color: var(--border); background: var(--bg-2);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="stat-icon" style="width:36px; height:36px; border-radius:8px; background:rgba(0,0,0,0.06); color:#000000; display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <span style="font-weight:800; font-size:15px; color:var(--text);">Form Tambah Pendidik &amp; Tenaga Kependidikan (GTK)</span>
            <div style="font-size:12px; color:var(--text-3);">Standar Data Pokok Pendidik (Dapodik) SMKN 1 Air Naningan.</div>
          </div>
        </div>
        <button type="button" onclick="toggleTambahGuru(false)" class="btn btn-outline" style="height:32px; width:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form id="formTambahGuru" action="/guru" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- TAB NAVIGATION --}}
        <div class="gtk-tab-nav">
          <button type="button" class="gtk-tab-btn active" onclick="switchGtkTab('tambah', 'tab_tambah_identitas', this)">
            <i class="bi bi-person-lines-fill"></i> 1. Biodata Utama
          </button>
          <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tambah', 'tab_tambah_kepegawaian', this)">
            <i class="bi bi-briefcase-fill"></i> 2. Kepegawaian &amp; Legalitas
          </button>
          <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tambah', 'tab_tambah_kualifikasi', this)">
            <i class="bi bi-mortarboard-fill"></i> 3. Kualifikasi &amp; Sertifikasi
          </button>
          <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('tambah', 'tab_tambah_tugas', this)">
            <i class="bi bi-calendar-range-fill"></i> 4. Tugas &amp; Beban Mengajar (JJM)
          </button>
        </div>

        {{-- TAB 1: BIODATA UTAMA --}}
        <div id="tab_tambah_identitas" class="gtk-tab-content">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Nama Lengkap (Tanpa Gelar) <span style="color:var(--red);">*</span></label>
              <input type="text" name="nama" required placeholder="Contoh: Sugeng Wardoyo" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Gelar Depan</label>
              <input type="text" name="gelar_depan" placeholder="Contoh: Drs. / Dr. / Ir." style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Gelar Belakang</label>
              <input type="text" name="gelar_belakang" placeholder="Contoh: S.Pd. / M.Kom. / Gr." style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">NIK (16 Digit KTP)</label>
              <input type="text" name="nik" maxlength="16" placeholder="16 Digit NIK Kependudukan" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Jenis Kelamin</label>
              <select name="jenis_kelamin" style="width:100%; height:38px;" class="input-field">
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" placeholder="Kota / Kabupaten Kelahiran" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Agama</label>
              <select name="agama" style="width:100%; height:38px;" class="input-field">
                <option value="Islam">Islam</option>
                <option value="Kristen Protestan">Kristen Protestan</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Khonghucu">Khonghucu</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">No. WhatsApp / HP</label>
              <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                <label style="margin:0; font-weight:700; font-size:12px; color:var(--text-2);">Foto Profil Guru</label>
                <span style="font-size:10.5px; font-weight:700; color:var(--gold, #ca8a04); display:inline-flex; align-items:center; gap:3px;">
                  <i class="bi bi-crop"></i> Auto-Crop Aktif
                </span>
              </div>
              <input type="file" id="tambah_guru_foto_input" name="foto" accept="image/*" style="display:none;" onchange="handleGuruPhotoSelect(this, 'tambah_guru_foto_img', 'tambah_guru_foto_text', 'Potong Foto Profil GTK')" />
              
              <div onclick="document.getElementById('tambah_guru_foto_input').click()" 
                   style="display:flex; align-items:center; gap:10px; height:38px; padding:3px 10px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); cursor:pointer; transition:all 0.15s ease;"
                   onmouseover="this.style.borderColor='var(--text)'; this.style.background='var(--surface)';"
                   onmouseout="this.style.borderColor='var(--border-2)'; this.style.background='var(--bg-3)';">
                <div style="width:28px; height:28px; border-radius:50%; border:1.5px solid var(--border-2); background:var(--bg-1); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                  <img id="tambah_guru_foto_img" src="/img/user-default.png" style="width:100%; height:100%; object-fit:cover;" />
                </div>
                <div style="display:flex; align-items:center; gap:6px; flex:1; min-width:0;">
                  <span class="btn btn-sm btn-outline" style="height:26px; padding:0 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; pointer-events:none; flex-shrink:0; background:var(--surface);">
                    <i class="bi bi-camera-fill"></i> Pilih Foto
                  </span>
                  <span id="tambah_guru_foto_text" style="font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1;">
                    Upload &amp; potong foto
                  </span>
                </div>
                <i class="bi bi-crop" style="font-size:13px; color:var(--text-3); flex-shrink:0;" title="Fitur crop foto otomatis"></i>
              </div>
            </div>
            <div class="form-group" style="grid-column: 1 / -1; margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Alamat Domisili Tempat Tinggal</label>
              <textarea name="alamat" rows="2" placeholder="Nama Jalan, RT/RW, Dusun/Pekon, Kecamatan, Kabupaten..." style="width:100%; font-size:12px; padding:8px;"></textarea>
            </div>
          </div>
        </div>

        {{-- TAB 2: KEPEGAWAIAN & LEGALITAS --}}
        <div id="tab_tambah_kepegawaian" class="gtk-tab-content" style="display:none;">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">NIP / NI PPPK (Khusus ASN)</label>
              <input type="text" name="nip" placeholder="Nomor NIP 18 Digit" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">NUPTK (16 Digit Pendidik)</label>
              <input type="text" name="nuptk" maxlength="16" placeholder="Nomor Unik Pendidik 16 Digit" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Jenis PTK (Opsional)</label>
              <select name="jenis_ptk" style="width:100%; height:38px;" class="input-field">
                <option value="">-- Pilih Jenis PTK (Opsional) --</option>
                <option value="Guru Kejuruan / Produktif">Guru Kejuruan / Produktif</option>
                <option value="Guru Normatif / Adaptif">Guru Normatif / Adaptif (Umum)</option>
                <option value="Guru BK">Guru BK (Bimbingan Konseling)</option>
                <option value="Kepala Sekolah">Kepala Sekolah</option>
                <option value="Tenaga Administrasi Sekolah (TU)">Tenaga Administrasi Sekolah (TU)</option>
                <option value="Laboran / Toolman Bengkel">Laboran / Toolman Bengkel</option>
                <option value="Tenaga Perpustakaan">Tenaga Perpustakaan (Pustakawan)</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Status Kepegawaian <span style="color:var(--red);">*</span></label>
              <select name="jenis_kepegawaian" onchange="toggleHariMengajar('tambah', this.value)" style="width:100%; height:38px;" class="input-field">
                <option value="pns">PNS (Pegawai Negeri Sipil)</option>
                <option value="pppk">PPPK (P3K)</option>
                <option value="honor">Guru Honor Daerah / GTT</option>
                <option value="tendik">Tenaga Honorer Sekolah / PTT</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Golongan / Pangkat (Khusus ASN)</label>
              <input type="text" name="golongan_pangkat" placeholder="Contoh: Penata Tk.I (III/d) / Golongan IX" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Nomor SK Pengangkatan Pegawai (Dinas / Pemda)</label>
              <input type="text" name="nomor_sk_pengangkatan" placeholder="Nomor SK Pengangkatan Resmi dari Pemerintah/Dinas" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">TMT Kerja (Terhitung Mulai Tanggal)</label>
              <input type="date" name="tmt_kerja" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Lembaga Pengangkat</label>
              <select name="lembaga_pengangkat" style="width:100%; height:38px;" class="input-field">
                <option value="Pemerintah Provinsi Lampung / Dinas Pendidikan">Pemerintah Provinsi Lampung / Dinas Pendidikan</option>
                <option value="Kepala Sekolah">Kepala Sekolah (SK Tugas Mandiri)</option>
              </select>
            </div>
          </div>
        </div>

        {{-- TAB 3: KUALIFIKASI & SERTIFIKASI --}}
        <div id="tab_tambah_kualifikasi" class="gtk-tab-content" style="display:none;">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Pendidikan Terakhir</label>
              <select name="pendidikan_terakhir" style="width:100%; height:38px;" class="input-field">
                <option value="S1">S1 / D4 (Sarjana / Diploma IV)</option>
                <option value="S2">S2 (Magister)</option>
                <option value="S3">S3 (Doktor)</option>
                <option value="D3">D3 (Diploma III)</option>
                <option value="SMA / SMK">SMA / SMK (Staf Pendukung)</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Program Studi / Jurusan Kuliah</label>
              <input type="text" name="jurusan_kuliah" placeholder="Contoh: Pendidikan Ilmu Komputer / Teknik Otomotif" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Nama Perguruan Tinggi / Kampus</label>
              <input type="text" name="kampus" placeholder="Contoh: Universitas Lampung / UNY / Polinela" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Tahun Kelulusan Kuliah</label>
              <input type="text" name="tahun_lulus" maxlength="4" placeholder="Contoh: 2012" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Status Sertifikasi Pendidik</label>
              <select name="status_sertifikasi" style="width:100%; height:38px;" class="input-field">
                <option value="belum">Belum Sertifikasi</option>
                <option value="sudah">Sudah Sertifikasi Pendidik (Gr.)</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:5px; font-weight:700; font-size:12px; color:var(--text-2);">Nomor Sertifikat Pendidik (Serdik)</label>
              <input type="text" name="nomor_serdik" placeholder="Nomor Registrasi Serdik" style="width:100%; height:38px;" />
            </div>
          </div>
        </div>

        {{-- TAB 4: TUGAS & JJM --}}
        <div id="tab_tambah_tugas" class="gtk-tab-content" style="display:none;">
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:2px; font-weight:700; font-size:12px; color:var(--text-2);">Mata Pelajaran Pokok yang Diampu</label>
              <span style="font-size:10.5px; color:var(--text-3); display:block; margin-bottom:5px;">Pisahkan dengan koma jika &gt; 1 (Otomatis menjadi jabatan mengajar guru)</span>
              <input type="text" name="mapel_diampu" placeholder="Contoh: Pemrograman Web, Basis Data, PBO" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:2px; font-weight:700; font-size:12px; color:var(--text-2);">Jumlah Jam Mengajar (JJM) / Minggu</label>
              <span style="font-size:10.5px; color:var(--text-3); display:block; margin-bottom:5px;">Total jam tatap muka mingguan seluruh mapel</span>
              <input type="number" name="jjm" min="0" max="60" placeholder="Contoh: 24 (Jam)" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:2px; font-weight:700; font-size:12px; color:var(--text-2);">Tugas Tambahan di Sekolah</label>
              <span style="font-size:10.5px; color:var(--text-3); display:block; margin-bottom:5px;">Misal: Waka Kurikulum, Kepala Bengkel RPL, Pembina OSIS</span>
              <input type="text" name="tugas_tambahan" placeholder="Contoh: Waka Kurikulum, Kepala Bengkel RPL" style="width:100%; height:38px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="margin-bottom:2px; font-weight:700; font-size:12px; color:var(--text-2);">SK Pembagian Tugas Mengajar &amp; JJM (Semester)</label>
              <span style="font-size:10.5px; color:var(--text-3); display:block; margin-bottom:5px;">Nomor SK Beban Kerja Mengajar dari Kepala Sekolah</span>
              <input type="text" name="sk_tugas_tambahan" placeholder="Contoh: 800/012/SMK.01/2026" style="width:100%; height:38px;" />
            </div>

            {{-- Jadwal Hari Mengajar (Centang Hari Aktif) --}}
            <div id="tambah_hari_mengajar_box" style="grid-column: 1 / -1; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:12px 14px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
                <label style="font-weight:800; font-size:12px; color:var(--text); margin:0;">
                  <i class="bi bi-calendar-check-fill" style="color:#000000; margin-right:4px;"></i> Jadwal Hari Wajib Hadir / Mengajar:
                </label>
                <span style="font-size:11px; color:var(--text-3);">Khusus guru honorer, hanya wajib hadir pada hari yang dicentang</span>
              </div>
              <div style="display:flex; flex-wrap:wrap; gap:10px;">
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                  <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; cursor:pointer; background:var(--bg-card); padding:5px 12px; border-radius:6px; border:1px solid var(--border);">
                    <input type="checkbox" name="hari_mengajar[]" value="{{ $hari }}" {{ in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']) ? 'checked' : '' }} /> {{ $hari }}
                  </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:14px; margin-top:14px;">
          <div style="font-size:11.5px; color:var(--text-3);">
            💡 <em>Data GTK otomatis tersinkronisasi untuk absensi RFID, profil web, dan cetak laporan resmi.</em>
          </div>
          <div style="display:flex; gap:8px;">
            <button type="button" onclick="toggleTambahGuru(false)" class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-gold"><i class="bi bi-check2-circle"></i> Simpan Data GTK</button>
          </div>
        </div>
      </form>
    </div>
    @endif

    <!-- Tabel Daftar Guru & Toolbar Terpadu -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      {{-- Header & Toolbar Terpadu --}}
      <div class="guru-table-toolbar" style="padding:10px 14px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div class="guru-table-title" style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:6px;">
          <i class="bi bi-people-fill" style="color:#000000;"></i>
          <span>Daftar Pendidik &amp; Tenaga Kependidikan</span>
        </div>

        <form method="GET" action="{{ route('guru.index') }}" class="guru-table-form" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; flex:1; justify-content:flex-end;">
          <div class="guru-search-box" style="position:relative; min-width:160px; flex:1.5;">
            <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:11px;"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, NIP, NUPTK, mapel..." class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding-left:28px; padding-right:8px;" />
          </div>

          <div style="min-width:115px;">
            <select name="kepegawaian" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
              <option value="">Semua Status</option>
              <option value="pns" {{ ($kepegawaian ?? '') === 'pns' ? 'selected' : '' }}>PNS</option>
              <option value="pppk" {{ ($kepegawaian ?? '') === 'pppk' ? 'selected' : '' }}>PPPK</option>
              <option value="honor" {{ ($kepegawaian ?? '') === 'honor' ? 'selected' : '' }}>Honor (GTT)</option>
              <option value="tendik" {{ ($kepegawaian ?? '') === 'tendik' ? 'selected' : '' }}>Tendik/TU</option>
            </select>
          </div>

          <div style="min-width:120px;">
            <select name="sertifikasi" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
              <option value="">Sertifikasi</option>
              <option value="sudah" {{ ($sertifikasi ?? '') === 'sudah' ? 'selected' : '' }}>Sudah Sertifikasi</option>
              <option value="belum" {{ ($sertifikasi ?? '') === 'belum' ? 'selected' : '' }}>Belum Sertifikasi</option>
            </select>
          </div>

          <div style="min-width:90px;">
            <select name="status" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
              <option value="">Keaktifan</option>
              <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
              <option value="nonaktif" {{ ($status ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>

          <div style="min-width:100px;">
            <select name="sort" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
              <option value="hirarki" {{ ($sort ?? 'hirarki') === 'hirarki' ? 'selected' : '' }}>Hirarki</option>
              <option value="nama_asc" {{ ($sort ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
              <option value="terbaru" {{ ($sort ?? '') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            </select>
          </div>

          <button type="submit" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; border-radius:var(--r-sm); flex-shrink:0;">
            Cari
          </button>

          @if($search || ($kepegawaian ?? '') || ($status ?? '') || ($sertifikasi ?? ''))
            <a href="{{ route('guru.index') }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); border-radius:var(--r-sm); flex-shrink:0;" title="Reset Filter">
              Reset
            </a>
          @endif
        </form>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" style="width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="width:36px; text-align:center;">No</th>
              <th>Pendidik &amp; Legalitas</th>
              <th>Penugasan &amp; Mapel</th>
              <th>Kepegawaian &amp; Kualifikasi</th>
              <th style="text-align:center;">Sertifikat Pelatihan</th>
              <th>Akun Akses</th>
              <th style="text-align:center;">Status</th>
              <th style="width:80px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($gurus as $idx => $g)
              @php
                $cleanHp = preg_replace('/[^0-9]/', '', $g->no_hp ?? '');
                if (str_starts_with($cleanHp, '0')) { $cleanHp = '62' . substr($cleanHp, 1); }
                $kartu = $g->kartuRfid;
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text); font-family:var(--font-mono); font-size:12px; vertical-align:middle;">
                  {{ $gurus->firstItem() + $idx }}
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="display:flex; align-items:flex-start; gap:11px;">
                    <div class="avatar-circle avatar-md" style="margin-top:2px; flex-shrink:0;">
                      <img src="{{ $g->foto_url }}" alt="{{ $g->nama }}" class="avatar-img" />
                    </div>
                    <div style="min-width:0; flex:1;">
                      <div style="font-weight:800; font-size:13.5px; color:var(--text); line-height:1.3;">
                        {{ $g->nama_lengkap_gelar }}
                      </div>
                      <div style="font-size:11px; font-family:var(--font-mono); color:var(--text-3); margin-top:2px;">
                        {{ $g->nip ? 'NIP: ' . $g->nip : 'Non-NIP' }}
                        @if($g->nuptk)
                          <span style="color:var(--border-2);">|</span> NUPTK: {{ $g->nuptk }}
                        @endif
                      </div>

                      {{-- Badges Meta: WhatsApp, Kartu RFID, & Tugas Tambahan --}}
                      <div style="display:flex; flex-wrap:wrap; align-items:center; gap:5px; margin-top:5px;">
                        @if($g->no_hp)
                          <a href="https://wa.me/{{ $cleanHp }}" target="_blank" 
                             style="font-size:10.5px; font-weight:700; font-family:var(--font-mono); text-decoration:none; display:inline-flex; align-items:center; gap:3px; color:#15803d; background:#dcfce7; border:1px solid rgba(22,163,74,0.3); border-radius:4px; padding:1px 6px; transition:all .15s ease;"
                             title="Kirim Pesan WhatsApp: {{ $g->no_hp }}">
                            <i class="bi bi-whatsapp"></i> {{ $g->no_hp }}
                          </a>
                        @endif

                        @if($kartu)
                          @if($isAdmin || $isStafTu)
                            <button type="button"
                              onclick="openRfidPairModal('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian }}', '{{ $g->foto_url }}', '{{ $kartu->uid }}')"
                              style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.3); border-radius:4px; padding:1px 6px; font-size:10.5px; font-weight:700; color:#065f46; cursor:pointer; font-family:var(--font-mono); display:inline-flex; align-items:center; gap:3px; transition:all .15s ease;"
                              title="Kartu RFID: {{ $kartu->uid }} (Klik untuk ubah / lepas)">
                              <i class="bi bi-broadcast" style="color:#10b981;"></i> {{ $kartu->uid }}
                            </button>
                          @else
                            <span style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.3); border-radius:4px; padding:1px 6px; font-size:10.5px; font-weight:700; color:#065f46; font-family:var(--font-mono); display:inline-flex; align-items:center; gap:3px;">
                              <i class="bi bi-broadcast" style="color:#10b981;"></i> {{ $kartu->uid }}
                            </span>
                          @endif
                        @else
                          @if($isAdmin || $isStafTu)
                            <button type="button"
                              onclick="openRfidPairModal('guru', {{ $g->id }}, '{{ addslashes($g->nama) }}', '{{ $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian }}', '{{ $g->foto_url }}', '')"
                              style="background:var(--bg-3); border:1px dashed var(--border-2); border-radius:4px; padding:1px 6px; font-size:10px; font-weight:700; color:var(--text-3); cursor:pointer; display:inline-flex; align-items:center; gap:3px; transition:all .15s ease;"
                              title="Daftarkan Kartu RFID Guru">
                              <i class="bi bi-plus-circle"></i> Pasang RFID
                            </button>
                          @endif
                        @endif

                        @if(!empty($g->list_tugas_tambahan))
                          @foreach($g->list_tugas_tambahan as $tgs)
                            <span style="font-size:10px; font-weight:700; color:#b45309; background:#fef3c7; border:1px solid rgba(217,119,6,0.25); border-radius:4px; padding:1px 5px; display:inline-flex; align-items:center; gap:3px;" title="{{ $g->sk_tugas_tambahan ? 'SK: ' . $g->sk_tugas_tambahan : 'Tugas Tambahan' }}">
                              <i class="bi bi-star-fill" style="font-size:8px;"></i> {{ $tgs }}
                            </span>
                          @endforeach
                        @elseif($g->rombelWali)
                          <span style="font-size:10px; font-weight:700; color:var(--text-2); background:var(--bg-3); border:1px solid var(--border); border-radius:4px; padding:1px 5px; display:inline-flex; align-items:center; gap:3px;">
                            <i class="bi bi-mortarboard-fill" style="font-size:9px;"></i> Wali: {{ $g->rombelWali->nama_rombel }}
                          </span>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="font-size:12.5px; font-weight:700; color:var(--text);">{{ $g->jabatan }}</div>
                  @if(!empty($g->list_mapel))
                    <div style="display:flex; flex-wrap:wrap; gap:3px; margin-top:4px; align-items:center;">
                      @foreach($g->list_mapel as $mpl)
                        <span style="font-size:10px; font-weight:700; background:rgba(37,99,235,0.08); color:#2563eb; border:1px solid rgba(37,99,235,0.2); padding:1px 5px; border-radius:4px; display:inline-flex; align-items:center; gap:3px;">
                          <i class="bi bi-book-half" style="font-size:8.5px;"></i> {{ $mpl }}
                        </span>
                      @endforeach
                      @if($g->jjm)
                        <span style="font-size:9.5px; font-weight:800; color:var(--text-3); background:var(--bg-3); padding:1px 5px; border-radius:4px; border:1px solid var(--border);" title="Total Jam Mengajar per Minggu">
                          {{ $g->jjm }} JP
                        </span>
                      @endif
                    </div>
                  @elseif($g->mapel_diampu)
                    <div style="font-size:11px; color:var(--text-2); margin-top:2px;">
                      Mapel: <strong>{{ $g->mapel_diampu }}</strong> {{ $g->jjm ? '(' . $g->jjm . ' JP)' : '' }}
                    </div>
                  @endif
                </td>
                <td style="vertical-align:middle; padding:12px 12px;">
                  <div style="display:flex; flex-direction:column; gap:3px;">
                    <div>
                      <span style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase;">
                        {{ $g->label_kepegawaian }}
                      </span>
                      @if($g->golongan_pangkat)
                        <span style="font-size:11px; color:var(--text-3);">({{ $g->golongan_pangkat }})</span>
                      @endif
                    </div>
                    <div>
                      @if($g->status_sertifikasi === 'sudah')
                        <span class="badge-sertifikasi-sudah" title="Nomor Serdik: {{ $g->nomor_serdik ?: 'Terdaftar' }}">
                          <i class="bi bi-patch-check-fill"></i> Sertifikasi
                        </span>
                      @else
                        <span class="badge-sertifikasi-belum">
                          Belum Sertifikasi
                        </span>
                      @endif
                    </div>
                    @if($g->pendidikan_terakhir)
                      <div style="font-size:10.5px; color:var(--text-3);">
                        {{ $g->pendidikan_terakhir }} {{ $g->jurusan_kuliah ? '- ' . $g->jurusan_kuliah : '' }}
                      </div>
                    @endif
                  </div>
                </td>

                {{-- Kolom Sertifikat Pelatihan --}}
                <td style="vertical-align:middle; text-align:center; padding:10px 12px; white-space:nowrap;">
                  <button type="button" onclick="openPortofolioModal({{ json_encode($g) }}, {{ json_encode($g->sertifikats) }})" class="btn-sertifikat-portofolio" title="Buka &amp; Kelola Portofolio Sertifikat Pelatihan Guru">
                    <i class="bi bi-award"></i>
                    <span>{{ $g->sertifikats->count() }} Sertifikat</span>
                  </button>
                </td>

                {{-- Kolom Akun Akses SIRANI --}}
                <td style="vertical-align:middle; padding:10px 12px; white-space:nowrap;">
                  @if($g->user)
                    @if($isAdmin)
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
                    @if($isAdmin)
                      <button type="button" onclick="openAkunModal({{ json_encode($g) }})" style="background:transparent; border:none; padding:4px 0; font-size:11.5px; font-weight:800; color:var(--text-2); cursor:pointer; white-space:nowrap;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-2)'" title="Buat Akun Login dengan Nickname/Username">
                        + Buat Akun
                      </button>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  @endif
                </td>

                {{-- Status Keaktifan --}}
                <td style="vertical-align:middle; text-align:center; padding:10px 8px; white-space:nowrap;">
                  @if($g->status === 'aktif')
                    <span class="table-status-pill aktif"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                  @elseif($g->status === 'cuti')
                    <span class="table-status-pill izin"><i class="bi bi-calendar-event"></i> Cuti</span>
                  @else
                    <span class="table-status-pill netral"><i class="bi bi-dash-circle-fill"></i> {{ ucfirst($g->status) }}</span>
                  @endif
                </td>

                {{-- Aksi --}}
                <td style="vertical-align:middle; text-align:center; padding:10px 8px; white-space:nowrap;">
                  <div style="display:flex; gap:4px; justify-content:center; align-items:center;">
                    <a href="{{ route('guru.biodata.cetak', ['id' => $g->id]) }}" target="_blank"
                       class="btn-icon"
                       style="width:30px; height:30px; text-decoration:none; color:#0284c7; background:rgba(2,132,199,0.08); border:1px solid rgba(2,132,199,0.25); border-radius:6px; display:inline-flex; align-items:center; justify-content:center; transition:all .15s ease;"
                       title="Cetak Lembar Biodata GTK Resmi (PDF/A4 Format Dapodik/BKN)">
                       <i class="bi bi-file-earmark-person-fill"></i>
                    </a>
                    <a href="{{ route('kartu.digital.guru', ['id' => $g->id]) }}" target="_blank"
                       class="btn-icon btn-icon-view"
                       style="width:30px; height:30px; text-decoration:none;"
                       title="Lihat Barcode &amp; Kartu Digital Guru">
                       <i class="bi bi-qr-code-scan"></i>
                    </a>
                    <button type="button" onclick="openEditGuru({{ json_encode($g) }})" class="btn-icon btn-icon-edit" style="width:30px; height:30px;" title="Edit Data GTK Lengkap">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <form action="/guru/{{ $g->id }}" method="POST" onsubmit="return confirm('Hapus data GTK {{ $g->nama }}?')" style="display:inline; margin:0;">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn-icon btn-icon-danger" style="width:30px; height:30px;" title="Hapus GTK">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; padding:36px; color:var(--text-3);">
                  <i class="bi bi-person-x" style="font-size:32px; opacity:0.4;"></i>
                  <div style="font-weight:700; margin-top:8px; font-size:14px; color:var(--text);">Tidak ada data GTK yang cocok</div>
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
    </div>
  </main>
</div>

<!-- Modal Portofolio Sertifikat Pelatihan Guru -->
<div id="portofolioModal" class="modal-overlay">
  <div class="modal-card modal-card-static" style="max-width:740px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid var(--border); flex-shrink:0;">
      <div style="display:flex; align-items:center; gap:8px;">
        <div style="width:36px; height:36px; border-radius:8px; background:rgba(37, 99, 235, 0.1); color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-award-fill"></i>
        </div>
        <div>
          <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">
            Portofolio Sertifikat Pelatihan Guru
          </h3>
          <div style="font-size:12px; color:var(--text-3);" id="portofolio_guru_subtitle">
            Kelola arsip pelatihan, workshop, dan diklat peningkatan skill.
          </div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('portofolioModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="gtk-tab-body-scroll" style="overflow-y: auto !important; padding-right: 6px;">
      {{-- Form Tambah Sertifikat Baru --}}
      <div style="background:var(--bg-3); border:1px solid var(--border); border-radius:var(--r-md); padding:14px; margin-bottom:18px;">
        <div style="font-weight:800; font-size:13px; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-plus-circle-fill" style="color:#2563eb;"></i> Unggah Sertifikat Pelatihan Baru
        </div>
        <form id="formTambahSertifikat" method="POST" enctype="multipart/form-data">
          @csrf
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:10px;">
            <div>
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Nama Pelatihan / Workshop <span style="color:var(--red);">*</span></label>
              <input type="text" name="nama_pelatihan" required placeholder="Contoh: Diklat Kurikulum Merdeka PMM" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Lembaga Penyelenggara <span style="color:var(--red);">*</span></label>
              <input type="text" name="penyelenggara" required placeholder="Contoh: Kemendikbudristek / BBPPMPV" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Tahun Pelaksanaan <span style="color:var(--red);">*</span></label>
              <input type="text" name="tahun" maxlength="10" required placeholder="Contoh: 2025" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Unggah File Dokumen (PDF/JPG)</label>
              <input type="file" name="file_sertifikat" accept=".pdf,image/*" style="width:100%; height:34px; font-size:11.5px;" />
            </div>
          </div>
          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-sm btn-gold" style="font-size:11.5px; font-weight:800; height:32px;">
              <i class="bi bi-cloud-arrow-up-fill"></i> Simpan Sertifikat
            </button>
          </div>
        </form>
      </div>

      {{-- Tabel Riwayat Sertifikat --}}
      <div style="font-weight:800; font-size:13px; color:var(--text); margin-bottom:8px; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-collection-fill" style="color:var(--text-3);"></i> Berkas Sertifikat yang Dimiliki (<span id="portofolio_count_display">0</span>)
      </div>
      <div style="border:1px solid var(--border); border-radius:var(--r-sm);">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
          <thead>
            <tr style="background:var(--bg-3); border-bottom:1px solid var(--border); text-align:left;">
              <th style="padding:8px 10px; width:30px;">No</th>
              <th style="padding:8px 10px;">Nama Pelatihan / Workshop</th>
              <th style="padding:8px 10px;">Penyelenggara</th>
              <th style="padding:8px 10px; width:70px; text-align:center;">Tahun</th>
              <th style="padding:8px 10px; width:90px; text-align:center;">Berkas</th>
              <th style="padding:8px 10px; width:50px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody id="portofolio_table_body">
            <tr>
              <td colspan="6" style="text-align:center; padding:18px; color:var(--text-3);">Memuat data sertifikat...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal-footer-static">
      <button type="button" class="btn btn-outline" onclick="closeModal('portofolioModal')">Tutup</button>
    </div>
  </div>
</div>

<!-- Modal Edit Guru / GTK Lengkap -->
<div id="editGuruModal" class="modal-overlay">
  <div class="modal-card modal-card-static" style="max-width:880px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border); flex-shrink:0;">
      <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-pencil-square" style="color:#000000;"></i> Edit Data GTK Guru / Tenaga Kependidikan
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('editGuruModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <form id="editGuruForm" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      
      {{-- TAB NAVIGATION EDIT --}}
      <div class="gtk-tab-nav" style="flex-shrink:0; margin-bottom:12px;">
        <button type="button" class="gtk-tab-btn active" onclick="switchGtkTab('edit', 'tab_edit_identitas', this)">
          1. Biodata
        </button>
        <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('edit', 'tab_edit_kepegawaian', this)">
          2. Kepegawaian
        </button>
        <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('edit', 'tab_edit_kualifikasi', this)">
          3. Kualifikasi
        </button>
        <button type="button" class="gtk-tab-btn" onclick="switchGtkTab('edit', 'tab_edit_tugas', this)">
          4. Tugas &amp; JJM
        </button>
      </div>

      {{-- SCROLLABLE TAB BODY WRAPPER --}}
      <div class="gtk-tab-body-scroll">
        {{-- TAB 1 EDIT: BIODATA --}}
        <div id="tab_edit_identitas" class="gtk-tab-content">
          <div style="display:grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Nama Lengkap (Tanpa Gelar) <span style="color:var(--red);">*</span></label>
              <input type="text" id="edit_guru_nama" name="nama" required class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Gelar Depan</label>
              <input type="text" id="edit_guru_gelar_depan" name="gelar_depan" placeholder="Drs. / Ir." class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Gelar Belakang</label>
              <input type="text" id="edit_guru_gelar_belakang" name="gelar_belakang" placeholder="S.Pd. / M.T." class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">NIK (16 Digit)</label>
              <input type="text" id="edit_guru_nik" name="nik" maxlength="16" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1.2fr 1.1fr 1.1fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Jenis Kelamin</label>
              <select id="edit_guru_jenis_kelamin" name="jenis_kelamin" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Tempat Lahir</label>
              <input type="text" id="edit_guru_tempat_lahir" name="tempat_lahir" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Tanggal Lahir</label>
              <input type="date" id="edit_guru_tanggal_lahir" name="tanggal_lahir" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Agama</label>
              <select id="edit_guru_agama" name="agama" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="Islam">Islam</option>
                <option value="Kristen Protestan">Kristen Protestan</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Khonghucu">Khonghucu</option>
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1.3fr 1fr 2fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">No. WhatsApp / HP</label>
              <input type="text" id="edit_guru_no_hp" name="no_hp" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Status Keaktifan</label>
              <select id="edit_guru_status" name="status" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="aktif">Aktif</option>
                <option value="cuti">Cuti</option>
                <option value="nonaktif">Nonaktif</option>
              </select>
            </div>
            <div>
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                <label class="form-label" style="font-weight:700; font-size:11.5px; margin:0;">Ganti Foto Profil</label>
                <span style="font-size:10px; font-weight:700; color:var(--gold, #ca8a04); display:inline-flex; align-items:center; gap:3px;">
                  <i class="bi bi-crop"></i> Auto-Crop Aktif
                </span>
              </div>
              <input type="file" id="edit_guru_foto_input" name="foto" accept="image/*" style="display:none;" 
                onchange="handleGuruPhotoSelect(this, 'edit_guru_foto_img', 'edit_guru_foto_text', 'Potong Foto Profil Guru')" />
              
              <div onclick="document.getElementById('edit_guru_foto_input').click()" 
                   style="display:flex; align-items:center; gap:8px; height:34px; padding:3px 10px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); cursor:pointer; transition:all 0.15s ease;"
                   onmouseover="this.style.borderColor='var(--text)'; this.style.background='var(--surface)';"
                   onmouseout="this.style.borderColor='var(--border-2)'; this.style.background='var(--bg-3)';">
                <div style="position:relative; width:26px; height:26px; border-radius:50%; border:1.5px solid var(--border-2); background:var(--bg-1); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                  <img id="edit_guru_foto_img" src="/img/user-default.png" style="width:100%; height:100%; object-fit:cover;" />
                </div>
                <div style="display:flex; align-items:center; gap:6px; flex:1; min-width:0;">
                  <span class="btn btn-sm btn-outline" style="height:24px; padding:0 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; pointer-events:none; flex-shrink:0; background:var(--surface);">
                    <i class="bi bi-camera-fill"></i> Pilih Foto
                  </span>
                  <span id="edit_guru_foto_text" style="font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex:1;">
                    Ganti &amp; potong foto
                  </span>
                </div>
                <i class="bi bi-crop" style="font-size:12px; color:var(--text-3); flex-shrink:0;" title="Fitur crop foto otomatis"></i>
              </div>
            </div>
          </div>

          <div>
            <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Alamat Domisili</label>
            <input type="text" id="edit_guru_alamat" name="alamat" placeholder="Alamat lengkap tempat tinggal" class="input-field" style="width:100%; height:34px; font-size:12px;" />
          </div>
        </div>

        {{-- TAB 2 EDIT: KEPEGAWAIAN --}}
        <div id="tab_edit_kepegawaian" class="gtk-tab-content" style="display:none;">
          <div style="display:grid; grid-template-columns: 1fr 1fr 1.4fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">NIP / NI PPPK</label>
              <input type="text" id="edit_guru_nip" name="nip" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">NUPTK (16 Digit)</label>
              <input type="text" id="edit_guru_nuptk" name="nuptk" maxlength="16" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Jenis PTK (Opsional)</label>
              <select id="edit_guru_jenis_ptk" name="jenis_ptk" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="">-- Pilih Jenis PTK (Opsional) --</option>
                <option value="Guru Kejuruan / Produktif">Guru Kejuruan / Produktif</option>
                <option value="Guru Normatif / Adaptif">Guru Normatif / Adaptif (Umum)</option>
                <option value="Guru BK">Guru BK (Bimbingan Konseling)</option>
                <option value="Kepala Sekolah">Kepala Sekolah</option>
                <option value="Tenaga Administrasi Sekolah (TU)">Tenaga Administrasi Sekolah (TU)</option>
                <option value="Laboran / Toolman Bengkel">Laboran / Toolman Bengkel</option>
                <option value="Tenaga Perpustakaan">Tenaga Perpustakaan (Pustakawan)</option>
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1.1fr 0.9fr 1.4fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Status Kepegawaian <span style="color:var(--red);">*</span></label>
              <select id="edit_guru_jenis_kepegawaian" name="jenis_kepegawaian" onchange="toggleHariMengajar('edit', this.value)" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="pns">PNS (Pegawai Negeri Sipil)</option>
                <option value="pppk">PPPK (P3K)</option>
                <option value="honor">Guru Honor Daerah / GTT</option>
                <option value="tendik">Tenaga Honorer Sekolah / PTT</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Golongan / Pangkat</label>
              <input type="text" id="edit_guru_golongan_pangkat" name="golongan_pangkat" placeholder="Contoh: III/a" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Nomor SK Pengangkatan Pegawai (Dinas / Pemda)</label>
              <input type="text" id="edit_guru_nomor_sk_pengangkatan" name="nomor_sk_pengangkatan" placeholder="Nomor SK Pengangkatan" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1.3fr; gap:10px 12px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">TMT Kerja</label>
              <input type="date" id="edit_guru_tmt_kerja" name="tmt_kerja" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Lembaga Pengangkat</label>
              <select id="edit_guru_lembaga_pengangkat" name="lembaga_pengangkat" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="Pemerintah Provinsi Lampung / Dinas Pendidikan">Pemerintah Provinsi Lampung / Dinas Pendidikan</option>
                <option value="Kepala Sekolah">Kepala Sekolah (SK Tugas Mandiri)</option>
              </select>
              <input type="hidden" id="edit_guru_jabatan" name="jabatan" />
            </div>
          </div>
        </div>

        {{-- TAB 3 EDIT: KUALIFIKASI --}}
        <div id="tab_edit_kualifikasi" class="gtk-tab-content" style="display:none;">
          <div style="display:grid; grid-template-columns: 1fr 1.3fr 1.3fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Pendidikan Terakhir</label>
              <select id="edit_guru_pendidikan_terakhir" name="pendidikan_terakhir" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="S1">S1 / D4 (Sarjana)</option>
                <option value="S2">S2 (Magister)</option>
                <option value="S3">S3 (Doktor)</option>
                <option value="D3">D3 (Diploma III)</option>
                <option value="SMA / SMK">SMA / SMK</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Program Studi / Jurusan</label>
              <input type="text" id="edit_guru_jurusan_kuliah" name="jurusan_kuliah" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Perguruan Tinggi / Kampus</label>
              <input type="text" id="edit_guru_kampus" name="kampus" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 0.8fr 1.3fr 1.3fr; gap:10px 12px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Tahun Lulus</label>
              <input type="text" id="edit_guru_tahun_lulus" name="tahun_lulus" maxlength="4" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Status Sertifikasi Pendidik</label>
              <select id="edit_guru_status_sertifikasi" name="status_sertifikasi" class="input-field" style="width:100%; height:34px; font-size:12px;">
                <option value="belum">Belum Sertifikasi</option>
                <option value="sudah">Sudah Sertifikasi Pendidik (Gr.)</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:3px;">Nomor Serdik (Jika Ada)</label>
              <input type="text" id="edit_guru_nomor_serdik" name="nomor_serdik" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
          </div>
        </div>

        {{-- TAB 4 EDIT: TUGAS & JJM --}}
        <div id="tab_edit_tugas" class="gtk-tab-content" style="display:none;">
          <div style="display:grid; grid-template-columns: 1.4fr 0.8fr 1.4fr 1.4fr; gap:10px 12px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:2px;">Mata Pelajaran Pokok Diampu</label>
              <span style="font-size:9.5px; color:var(--text-3); display:block; margin-bottom:3px;">Otomatis menjadi jabatan mengajar</span>
              <input type="text" id="edit_guru_mapel_diampu" name="mapel_diampu" placeholder="Contoh: Web, Basis Data" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:2px;">JJM (Jam / Minggu)</label>
              <span style="font-size:9.5px; color:var(--text-3); display:block; margin-bottom:3px;">Total jam tatap muka</span>
              <input type="number" id="edit_guru_jjm" name="jjm" min="0" max="60" placeholder="24" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:2px;">Tugas Tambahan</label>
              <span style="font-size:9.5px; color:var(--text-3); display:block; margin-bottom:3px;">Misal: Waka, Kepala Bengkel</span>
              <input type="text" id="edit_guru_tugas_tambahan" name="tugas_tambahan" placeholder="Kepala Bengkel, Wali Kelas" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
            <div>
              <label class="form-label" style="font-weight:700; font-size:11.5px; margin-bottom:2px;">SK Tugas Mengajar &amp; JJM</label>
              <span style="font-size:9.5px; color:var(--text-3); display:block; margin-bottom:3px;">SK Semester dari Kepala Sekolah</span>
              <input type="text" id="edit_guru_sk_tugas_tambahan" name="sk_tugas_tambahan" placeholder="800/012/SMK.01/2026" class="input-field" style="width:100%; height:34px; font-size:12px;" />
            </div>
          </div>

          <div id="edit_hari_mengajar_box" style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:8px 12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:4px;">
              <label style="font-weight:800; font-size:11.5px; color:var(--text); margin:0;">
                <i class="bi bi-calendar-check-fill" style="color:#000000; margin-right:4px;"></i> Jadwal Hari Wajib Mengajar / Hadir:
              </label>
              <span style="font-size:10px; color:var(--text-3);">Centang hari wajib hadir</span>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
              @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                <label style="display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:700; cursor:pointer; background:var(--bg-card); padding:3px 8px; border-radius:6px; border:1px solid var(--border);">
                  <input type="checkbox" name="hari_mengajar[]" value="{{ $hari }}" class="edit-hari-cb" data-hari="{{ $hari }}" /> {{ $hari }}
                </label>
              @endforeach
            </div>
          </div>
        </div>
      </div> {{-- end gtk-tab-body-scroll --}}

      <div class="modal-footer-static" style="display:flex; justify-content:space-between; align-items:center;">
        <a id="btn_edit_cetak_biodata" href="#" target="_blank" class="btn btn-outline" style="text-decoration:none; font-size:12px; font-weight:700; color:#0284c7; border-color:rgba(2,132,199,0.3); background:rgba(2,132,199,0.06); display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-file-earmark-person-fill"></i> Cetak Biodata GTK
        </a>
        <div style="display:flex; gap:8px;">
          <button type="button" class="btn btn-outline" onclick="closeModal('editGuruModal')">Batal</button>
          <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
        </div>
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
          <span style="font-size:10.5px; color:var(--text-3); margin-top:2px; display:block;">Bisa berupa nama panggilan, inisial, atau NIP tanpa spasi. <strong>Tidak wajib menggunakan email</strong>.</span>
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
            <option value="waka_sarpras">Waka Sarpras (Aset, Inventaris Bengkel/Lab, Laporan Kerusakan)</option>
            <option value="waka_hubin">Waka Hubin / Industri (Kemitraan DUDI, Magang &amp; Bursa Kerja Khusus)</option>
            <option value="kaprog">Kepala Program Keahlian (Monitoring Praktik &amp; Bengkel Jurusan)</option>
            <option value="kepala_bengkel">Kepala Bengkel / Laboran / Toolman (Perawatan Mesin &amp; BHP)</option>
            <option value="pustakawan">Tenaga Perpustakaan (Sirkulasi Buku &amp; Literasi)</option>
            <option value="guru_bk">Guru BK / Konseling (Penanganan Kasus Tahap 2 &amp; Panggilan Ortu)</option>
            <option value="wali_kelas">Wali Kelas (Monitoring Kelas Binaan &amp; Pembinaan Tahap 1)</option>
            <option value="staf_tu">Staf Tata Usaha / Kepegawaian (Data Master Siswa/Guru)</option>
            <option value="humas">Tim Humas &amp; Web (Pengelola Banner &amp; Publikasi Berita)</option>
            <option value="panitia_ppdb">Panitia PPDB 2026 (Verifikasi Dokumen Pendaftar Baru)</option>
            <option value="guru">Guru Mata Pelajaran (Presensi &amp; Jadwal Mengajar)</option>
          </select>
        </div>

        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <span>Peran Tambahan / Multi-Role (Opsional)</span>
            <span style="font-size:10.5px; font-weight:600; color:var(--text-3);">Centang peran ganda</span>
          </label>
          <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 12px; max-height:130px; overflow-y:auto; display:grid; grid-template-columns:1fr 1fr; gap:6px 12px;">
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="waka_sarpras" class="akun-sub-role" /> Waka Sarpras
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="waka_hubin" class="akun-sub-role" /> Waka Hubin
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="waka_kurikulum" class="akun-sub-role" /> Waka Kurikulum
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="waka_kesiswaan" class="akun-sub-role" /> Waka Kesiswaan
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="kaprog" class="akun-sub-role" /> Kaprog Jurusan
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="guru_bk" class="akun-sub-role" /> Guru BK
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="kepala_bengkel" class="akun-sub-role" /> Kepala Bengkel
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="pustakawan" class="akun-sub-role" /> Pustakawan
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="humas" class="akun-sub-role" /> Tim Humas &amp; Web
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="panitia_ppdb" class="akun-sub-role" /> Panitia PPDB
            </label>
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; cursor:pointer;">
              <input type="checkbox" name="roles[]" value="staf_tu" class="akun-sub-role" /> Staf TU
            </label>
          </div>
          <div style="margin-top:6px; font-size:11px; color:var(--text-3); line-height:1.4; display:flex; flex-direction:column; gap:4px;">
            <div style="display:flex; align-items:flex-start; gap:6px;">
              <i class="bi bi-info-circle-fill" style="color:var(--primary); font-size:12px; margin-top:1px;"></i>
              <span><strong>Wali Kelas:</strong> Hak akses otomatis terintegrasi pada akun Guru jika terdaftar membina kelas di menu <a href="/rombel" target="_blank" style="color:var(--primary); font-weight:700; text-decoration:underline;">Rombongan Belajar</a> tanpa perlu switch mode.</span>
            </div>
            <div style="display:flex; align-items:flex-start; gap:6px;">
              <i class="bi bi-info-circle-fill" style="color:var(--primary); font-size:12px; margin-top:1px;"></i>
              <span><strong>Guru Piket:</strong> Hak akses operasional meja piket aktif otomatis pada hari penugasan guru di menu <a href="/jadwal-piket" target="_blank" style="color:var(--primary); font-weight:700; text-decoration:underline;">Jadwal Petugas Piket</a> tanpa perlu ganti akun.</span>
            </div>
          </div>
          <div style="font-size:10.5px; color:var(--text-3); margin-top:4px;">
            💡 Guru dengan multi-peran dapat berpindah mode kerja secara mandiri melalui <strong>Role Switcher</strong> di bilah atas.
          </div>
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

<!-- Modal Import Guru CSV Dapodik -->
<div id="importGuruModal" class="modal-overlay">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-file-earmark-arrow-up-fill" style="color:#000000;"></i> Import Data GTK dari Dapodik / CSV
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('importGuruModal')"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--surface); border:1px solid var(--border-2); border-radius:12px; padding:12px 14px; margin-bottom:16px; font-size:12px; line-height:1.5;">
      <div style="font-weight:800; color:#000000; display:flex; align-items:center; gap:6px; margin-bottom:4px;">
        <i class="bi bi-info-circle-fill"></i> Parser Cerdas Standar Dapodik
      </div>
      <div style="color:var(--text-2);">
        Sistem otomatis mengenali kolom Nama, Gelar, NIP, NUPTK, NIK, Jenis PTK, Status Kepegawaian, Golongan, Pendidikan, Mapel Diampu, JJM, dan No WhatsApp.
      </div>
      <div style="margin-top:8px;">
        <a href="{{ route('guru.template-csv') }}" class="btn btn-sm btn-outline-mono" style="font-weight:800; font-size:11.5px; display:inline-flex; align-items:center; gap:6px; background:var(--bg-2); text-decoration:none;">
          <i class="bi bi-download" style="color:#000000;"></i> Unduh Template CSV GTK Lengkap
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
        <button type="submit" class="btn btn-gold"><i class="bi bi-cloud-arrow-up-fill"></i> Mulai Import GTK</button>
      </div>
    </form>
  </div>
</div>

<script>
  function switchGtkTab(scope, tabId, btnElement) {
    const parent = btnElement.closest('.panel, .modal-card');
    parent.querySelectorAll('.gtk-tab-btn').forEach(b => b.classList.remove('active'));
    parent.querySelectorAll('.gtk-tab-content').forEach(c => c.style.display = 'none');
    
    btnElement.classList.add('active');
    const target = document.getElementById(tabId);
    if (target) {
      target.style.display = 'block';
      const scrollBody = parent.querySelector('.gtk-tab-body-scroll');
      if (scrollBody) scrollBody.scrollTop = 0;
    }
  }

  function toggleHariMengajar(context, val) {
    const box = document.getElementById(context + '_hari_mengajar_box');
    if (box) {
      box.style.display = 'block';
    }
  }

  function toggleTambahGuru(forceState) {
    const panel = document.getElementById('panelTambahGuru');
    const text = document.getElementById('textToggleTambahGuru');
    const isHidden = (panel.style.display === 'none' || panel.style.display === '');
    const show = (forceState !== undefined) ? forceState : isHidden;
    
    panel.style.display = show ? 'block' : 'none';
    if (text) {
      text.innerText = show ? 'Tutup Form' : 'Tambah GTK';
    }
    if (show) {
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function openEditGuru(guru) {
    document.getElementById('edit_guru_nama').value = guru.nama_lengkap || guru.nama || '';
    document.getElementById('edit_guru_gelar_depan').value = guru.gelar_depan || '';
    document.getElementById('edit_guru_gelar_belakang').value = guru.gelar_belakang || '';
    document.getElementById('edit_guru_nik').value = guru.nik || '';
    document.getElementById('edit_guru_nip').value = guru.nip || '';
    document.getElementById('edit_guru_nuptk').value = guru.nuptk || '';
    document.getElementById('edit_guru_tempat_lahir').value = guru.tempat_lahir || '';
    document.getElementById('edit_guru_tanggal_lahir').value = guru.tanggal_lahir ? guru.tanggal_lahir.substring(0, 10) : '';
    document.getElementById('edit_guru_jenis_kelamin').value = guru.jenis_kelamin || '';
    document.getElementById('edit_guru_agama').value = guru.agama || 'Islam';
    document.getElementById('edit_guru_no_hp').value = guru.no_hp || '';
    document.getElementById('edit_guru_status').value = guru.status || 'aktif';
    document.getElementById('edit_guru_alamat').value = guru.alamat || '';

    document.getElementById('edit_guru_jabatan').value = guru.jabatan || '';
    document.getElementById('edit_guru_jenis_kepegawaian').value = guru.jenis_kepegawaian || 'pns';
    document.getElementById('edit_guru_jenis_ptk').value = guru.jenis_ptk || '';
    document.getElementById('edit_guru_golongan_pangkat').value = guru.golongan_pangkat || '';
    document.getElementById('edit_guru_nomor_sk_pengangkatan').value = guru.nomor_sk_pengangkatan || '';
    document.getElementById('edit_guru_tmt_kerja').value = guru.tmt_kerja ? guru.tmt_kerja.substring(0, 10) : '';
    document.getElementById('edit_guru_lembaga_pengangkat').value = guru.lembaga_pengangkat || 'Pemerintah Provinsi Lampung / Dinas Pendidikan';

    document.getElementById('edit_guru_pendidikan_terakhir').value = guru.pendidikan_terakhir || 'S1';
    document.getElementById('edit_guru_jurusan_kuliah').value = guru.jurusan_kuliah || '';
    document.getElementById('edit_guru_kampus').value = guru.kampus || '';
    document.getElementById('edit_guru_tahun_lulus').value = guru.tahun_lulus || '';
    document.getElementById('edit_guru_status_sertifikasi').value = guru.status_sertifikasi || 'belum';
    document.getElementById('edit_guru_nomor_serdik').value = guru.nomor_serdik || '';

    document.getElementById('edit_guru_mapel_diampu').value = guru.mapel_diampu || '';
    document.getElementById('edit_guru_jjm').value = guru.jjm || '';
    document.getElementById('edit_guru_tugas_tambahan').value = guru.tugas_tambahan || '';
    document.getElementById('edit_guru_sk_tugas_tambahan').value = guru.sk_tugas_tambahan || '';

    const imgPreview = document.getElementById('edit_guru_foto_img');
    if (imgPreview) {
      imgPreview.src = guru.foto_url || '/img/user-default.png';
    }
    const editFotoInput = document.getElementById('edit_guru_foto_input');
    if (editFotoInput) editFotoInput.value = '';
    const editFotoText = document.getElementById('edit_guru_foto_text');
    if (editFotoText) {
      editFotoText.innerText = 'Ganti & potong foto';
      editFotoText.style.color = 'var(--text-3)';
      editFotoText.style.fontWeight = 'normal';
    }

    const btnCetakBiodata = document.getElementById('btn_edit_cetak_biodata');
    if (btnCetakBiodata) {
      btnCetakBiodata.href = '/guru/' + guru.id + '/biodata';
    }

    // Set hari mengajar checkboxes
    const hariList = (guru.hari_mengajar && Array.isArray(guru.hari_mengajar)) ? guru.hari_mengajar : ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    document.querySelectorAll('.edit-hari-cb').forEach(cb => {
      cb.checked = hariList.includes(cb.getAttribute('data-hari'));
    });

    // Reset ke tab 1 (Biodata)
    const firstEditTabBtn = document.querySelector('#editGuruModal .gtk-tab-btn');
    if (firstEditTabBtn) switchGtkTab('edit', 'tab_edit_identitas', firstEditTabBtn);

    document.getElementById('editGuruForm').action = '/guru/' + guru.id;
    openModal('editGuruModal');
  }

  function openPortofolioModal(guru, sertifikats) {
    document.getElementById('portofolio_guru_subtitle').innerText = 'Guru: ' + (guru.nama_lengkap_gelar || guru.nama) + ' (' + (guru.nip ? 'NIP: ' + guru.nip : (guru.nuptk ? 'NUPTK: ' + guru.nuptk : guru.label_kepegawaian)) + ')';
    document.getElementById('formTambahSertifikat').action = '/guru/' + guru.id + '/sertifikat';
    
    const countDisplay = document.getElementById('portofolio_count_display');
    const tableBody = document.getElementById('portofolio_table_body');
    
    const items = sertifikats || [];
    countDisplay.innerText = items.length;

    if (items.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" style="text-align:center; padding:24px; color:var(--text-3);">
            <i class="bi bi-file-earmark-x" style="font-size:24px; opacity:0.5;"></i>
            <div style="font-weight:700; margin-top:4px;">Belum ada sertifikat pelatihan yang diunggah.</div>
            <div style="font-size:11px;">Gunakan form di atas untuk menambahkan sertifikat baru.</div>
          </td>
        </tr>
      `;
    } else {
      let html = '';
      items.forEach((s, i) => {
        const fileLink = s.file_sertifikat ? 
          `<a href="/storage/${s.file_sertifikat}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:2px 8px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-file-earmark-arrow-down"></i> Unduh</a>` : 
          `<span style="color:var(--text-3); font-size:11px;">Tidak ada</span>`;
        
        html += `
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:8px 10px; text-align:center; font-family:var(--font-mono);">${i + 1}</td>
            <td style="padding:8px 10px; font-weight:700; color:var(--text);">${s.nama_pelatihan}</td>
            <td style="padding:8px 10px; color:var(--text-2);">${s.penyelenggara}</td>
            <td style="padding:8px 10px; text-align:center; font-family:var(--font-mono); font-weight:700;">${s.tahun}</td>
            <td style="padding:8px 10px; text-align:center;">${fileLink}</td>
            <td style="padding:8px 10px; text-align:center;">
              <form action="/guru/${guru.id}/sertifikat/${s.id}" method="POST" onsubmit="return confirm('Hapus sertifikat ${s.nama_pelatihan}?')" style="display:inline; margin:0;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn-icon btn-icon-danger" style="width:26px; height:26px;" title="Hapus Sertifikat">
                  <i class="bi bi-trash3-fill" style="font-size:11px;"></i>
                </button>
              </form>
            </td>
          </tr>
        `;
      });
      tableBody.innerHTML = html;
    }

    openModal('portofolioModal');
  }

  function openAkunModal(guru) {
    document.getElementById('akun_guru_nama_display').innerText = guru.nama_lengkap_gelar || guru.nama;
    document.getElementById('akun_guru_jabatan_display').innerText = guru.jabatan || 'Guru / Pegawai';
    
    const form = document.getElementById('akunGuruForm');
    const delContainer = document.getElementById('akunDeleteBtnContainer');
    
    // Reset semua checkbox peran tambahan
    document.querySelectorAll('.akun-sub-role').forEach(cb => cb.checked = false);

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

      // Centang peran tambahan yang tersimpan
      if (Array.isArray(guru.user.roles)) {
        guru.user.roles.forEach(r => {
          const cb = document.querySelector('.akun-sub-role[value="' + r + '"]');
          if (cb) cb.checked = true;
        });
      }
    } else {
      let cleanName = (guru.nama || '').toLowerCase().replace(/[^a-z0-9]/g, ' ').trim();
      let defaultUsername = guru.nip ? guru.nip : (cleanName.split(/\s+/)[0] || 'guru');
      
      document.getElementById('akun_username').value = defaultUsername;
      document.getElementById('akun_email').value = '';

      const jab = ((guru.jabatan || '') + ' ' + (guru.tugas_tambahan || '') + ' ' + (guru.jenis_ptk || '')).toLowerCase();
      let defaultRole = 'guru';
      if (jab.includes('kepala sekolah') && !jab.includes('wakil') && !jab.includes('waka')) defaultRole = 'kepala_sekolah';
      else if (jab.includes('waka sarpras') || jab.includes('sarana prasarana') || jab.includes('sarpras')) defaultRole = 'waka_sarpras';
      else if (jab.includes('waka hubin') || jab.includes('hubungan industri') || jab.includes('hubin')) defaultRole = 'waka_hubin';
      else if (jab.includes('waka kesiswaan') || jab.includes('kesiswaan')) defaultRole = 'waka_kesiswaan';
      else if (jab.includes('waka kurikulum') || jab.includes('kurikulum')) defaultRole = 'waka_kurikulum';
      else if (jab.includes('kaprog') || jab.includes('kepala program') || jab.includes('ketua jurusan') || jab.includes('ketua program')) defaultRole = 'kaprog';
      else if (jab.includes('kepala bengkel') || jab.includes('kabeng') || jab.includes('toolman') || jab.includes('laboran')) defaultRole = 'kepala_bengkel';
      else if (jab.includes('perpustakaan') || jab.includes('pustakawan')) defaultRole = 'pustakawan';
      else if (jab.includes('bk') || jab.includes('bimbingan')) defaultRole = 'guru_bk';
      else if (jab.includes('tata usaha') || jab.includes('tu') || jab.includes('staf') || jab.includes('administrasi')) defaultRole = 'staf_tu';

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

  function handleGuruPhotoSelect(input, previewId, labelId, title) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const label = document.getElementById(labelId);
    if (label) {
      label.innerText = file.name;
      label.style.color = 'var(--text)';
      label.style.fontWeight = '600';
    }
    if (typeof initPhotoCrop === 'function') {
      initPhotoCrop(input, previewId, '1:1', title);
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const editInput = document.getElementById('edit_guru_foto_input');
    if (editInput) {
      editInput.addEventListener('crop:success', function(e) {
        const label = document.getElementById('edit_guru_foto_text');
        if (label) {
          label.innerText = '✓ Foto dicrop (' + (e.detail?.name || 'siap simpan') + ')';
          label.style.color = '#10b981';
          label.style.fontWeight = '700';
        }
      });
    }

    const tambahInput = document.getElementById('tambah_guru_foto_input');
    if (tambahInput) {
      tambahInput.addEventListener('crop:success', function(e) {
        const label = document.getElementById('tambah_guru_foto_text');
        if (label) {
          label.innerText = '✓ Foto dicrop (' + (e.detail?.name || 'siap simpan') + ')';
          label.style.color = '#10b981';
          label.style.fontWeight = '700';
        }
      });
    }
  });
</script>

@include('partials.crop_modal')

@if($isAdmin || $isStafTu)
  @include('partials.rfid_pair_modal')
@endif
</body>
</html>
