<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>{{ $siswa ? ($modeAkses === 'siswa' ? 'Kartu & Presensi Siswa '.$siswa->nama.' — SIRANI' : 'Rekap Kehadiran '.$siswa->nama.' — Monitoring Absen Mandiri') : 'Monitoring Absen Mandiri — SMKN 1 Air Naningan' }}</title>
  
  {{-- PWA Meta Tags --}}
  <link rel="manifest" href="/manifest.json" />
  <meta name="theme-color" content="#0F172A" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="SIRANI" />
  <link rel="apple-touch-icon" href="/icons/icon-192.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png" />
  
  {{-- Google Fonts: Plus Jakarta Sans & JetBrains Mono --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  
  {{-- QR Code Generator & Canvas Exporter --}}
  <script src="/qrcode.min.js"></script>
  <script src="/html2canvas.min.js"></script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link rel="stylesheet" href="{{ asset('css/portal-ortu.css') }}?v={{ filemtime(public_path('css/portal-ortu.css')) }}">
</head>
<body>

  {{-- TOP NAVIGATION --}}
  <nav class="top-nav">
    <div class="top-nav-inner">
      <a href="/monitoring-absen" class="brand-logo" title="SIRANI - Presensi SMKN 1 Air Naningan">
        <div class="brand-icon">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="brand-text">
          <h1>SIRANI</h1>
          <p>Presensi SMKN 1 AN</p>
        </div>
      </a>

      <div class="nav-actions">
        {{-- Tombol Pengaturan Notifikasi & Suara (Selalu Aktif) --}}
        <button type="button" id="btnNavNotifSettings" onclick="openNotifSettingsModal()" class="nav-action-btn btn-nav-notif" title="Pengaturan Notifikasi & Suara (Selalu Aktif)">
          <span class="nav-icon-wrap">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-active-dot" title="Notifikasi Selalu Aktif"></span>
          </span>
          <span class="nav-btn-text">Suara</span>
        </button>

        {{-- Tombol Pasang Aplikasi / APK --}}
        <button type="button" id="btnPwaInstall" onclick="openApkInstallModal()" class="nav-action-btn btn-pwa-install" title="Pasang Aplikasi SIRANI / Unduh APK">
          <i class="bi bi-phone-fill"></i>
          <span class="nav-btn-text">App</span>
        </button>

        {{-- Link Web Resmi SMK --}}
        <a href="/" class="nav-action-btn btn-portal-sekolah" title="Website Resmi SMKN 1 Air Naningan">
          <i class="bi bi-globe2"></i>
          <span class="nav-btn-text">Web</span>
        </a>

        {{-- Tombol Ganti Siswa (Hanya jika siswa sedang dibuka) --}}
        @if($siswa)
          <button type="button" onclick="logoutSavedStudent()" class="nav-action-btn btn-ganti-nisn" title="Ganti Siswa / Keluar dari Akun">
            <i class="bi bi-arrow-left-right"></i>
            <span class="nav-btn-text">Ganti</span>
          </button>
        @endif
      </div>
    </div>
  </nav>

  {{-- MAIN CONTAINER --}}
  <main class="container">

    @if(!$siswa)
      {{-- SEARCH CONSOLE (HERO SELAMAT DATANG) --}}
      <section class="search-console-card">
        <div class="search-badge">
          Layanan Presensi Mandiri Siswa &amp; Orang Tua
        </div>

        <h2 class="search-title">Monitoring Absen Mandiri</h2>
        <p class="search-desc">
          Pantau catatan absensi, riwayat kehadiran harian, kartu pelajar digital, dan catatan pembinaan siswa SMKN 1 Air Naningan secara real-time.
        </p>

        @if($keyword)
          {{-- JIKA SISWA TIDAK DITEMUKAN: MENGGANTIKAN BOX PENCARIAN --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-lg); padding:32px 20px; text-align:center; margin-bottom:20px;">
            <i class="bi bi-person-x-fill" style="font-size:42px; color:var(--text-3); margin-bottom:10px; display:inline-block;"></i>
            <h3 style="font-size:16.5px; font-weight:800; margin-bottom:6px; color:var(--text);">Data Siswa Tidak Ditemukan</h3>
            <p style="font-size:13px; color:var(--text-2); max-width:480px; margin:0 auto 18px; line-height:1.5;">
              Nomor Induk Siswa Nasional (NISN) <strong>"{{ $keyword }}"</strong> tidak terdaftar pada pangkalan data aktif sekolah. Pastikan nomor yang Anda masukkan sudah sesuai.
            </p>
            <a href="{{ route('portal.ortu.index') }}" class="btn-search" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; padding:9px 22px; border-radius:8px; font-size:13px;">
              <i class="bi bi-arrow-repeat"></i> Masukkan NISN
            </a>
          </div>
        @else
          <div id="savedStudentsContainer" style="display:none;"></div>

          <div class="search-form-box">
            <form method="GET" action="{{ route('portal.ortu.index') }}">
              <div class="search-input-wrap">
                <input
                  type="text"
                  name="keyword"
                  class="search-input"
                  value="{{ $keyword }}"
                  placeholder="Masukkan NISN Anda"
                  autocomplete="off"
                  required
                  autofocus
                />
                <button type="submit" class="btn-search">
                 Masuk Portal Siswa
                </button>
              </div>
              <div class="search-hints-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-top:10px;">
                <span class="search-hint-pill">Pencarian menggunakan <strong>NISN Siswa</strong> yang terdaftar.</span>
                <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--text-2); cursor:pointer; user-select:none; font-weight:600;">
                  <input type="checkbox" id="rememberNisnCheckbox" checked style="accent-color:#0f172a; cursor:pointer; width:15px; height:15px;" />
                  <span>Ingat NISN di perangkat ini</span>
                </label>
              </div>
            </form>
          </div>
        @endif

        {{-- Highlight Layanan --}}
        <div class="portal-features-grid">
          <div class="portal-feature-item">
            <div class="portal-feature-text">
              <strong>Smart Gate Terintegrasi</strong>
              <span>Presensi otomatis tercatat saat siswa tap kartu RFID atau QR Code di gerbang masuk sekolah.</span>
            </div>
          </div>
          <div class="portal-feature-item">
            <div class="portal-feature-text">
              <strong>Rekapitulasi Lengkap</strong>
              <span>Akses pantauan riwayat kehadiran harian, mingguan, bulanan, dan persentase kehadiran transparan.</span>
            </div>
          </div>
          <div class="portal-feature-item">
            <div class="portal-feature-text">
              <strong>Kartu Pelajar &amp; Karakter</strong>
              <span>Transparansi kartu digital mandiri, rekap poin apresiasi, prestasi, dan catatan pembinaan siswa.</span>
            </div>
          </div>
        </div>
      </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         BANNER NOTIFIKASI & PENGATURAN SUARA (Selalu Tampil)
    ══════════════════════════════════════════════════════════ --}}
    {{-- Banner jika izin browser di HP belum diberikan --}}
    <div id="siraniBannerNotif" class="sirani-notif-banner" style="display:none;">
      <div class="sirani-notif-banner-left">
        <div class="sirani-notif-banner-icon"><i class="bi bi-bell-fill"></i></div>
        <div class="sirani-notif-banner-text">
          <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <strong>Notifikasi Presensi Sekolah</strong>
            <span class="badge-notif-wajib"><i class="bi bi-shield-lock-fill"></i> Wajib Aktif</span>
          </div>
          <span>Pemberitahuan tap gerbang otomatis langsung terkirim ke HP saat anak hadir atau pulang.</span>
        </div>
      </div>
      <div class="sirani-notif-banner-right">
        <button type="button" id="btnAktifkanNotif" onclick="sirani_requestPushPermission()" class="btn-notif-aktifkan">
          <i class="bi bi-bell-fill"></i> Izinkan di HP Ini
        </button>
        <button type="button" onclick="openNotifSettingsModal()" class="btn-notif-settings-icon" title="Atur Suara & Nada Dering">
          <i class="bi bi-sliders"></i>
        </button>
      </div>
    </div>

    {{-- Banner Status Notifikasi HP Aktif & Terhubung --}}
    <div id="siraniBannerNotifAktif" class="sirani-notif-banner sirani-notif-aktif" style="display:none;">
      <div class="sirani-notif-banner-left">
        <div class="sirani-notif-banner-icon sirani-notif-icon-green">
          <i class="bi bi-bell-fill"></i>
        </div>
        <div class="sirani-notif-banner-text">
          <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <strong>Notifikasi Selalu Aktif ✓</strong>
            <span class="badge-notif-wajib"><i class="bi bi-shield-lock-fill"></i> Wajib Sekolah</span>
          </div>
          <span>HP ini terhubung & otomatis menerima bunyi pemberitahuan kehadiran siswa.</span>
        </div>
      </div>
      <div class="sirani-notif-banner-right">
        <button type="button" onclick="openNotifSettingsModal()" class="btn-notif-atur-suara">
          <i class="bi bi-music-note-beamed"></i> Atur Suara
        </button>
      </div>
    </div>

    @if($siswa)


      {{-- HASIL DATA SISWA TERPILIH --}}

      {{-- 4 TAB NAVIGASI UTAMA ATAS (Dashboard Menu 2x2 di Mobile, 4 Kolom di Desktop) --}}
      <div class="portal-main-tabs">
        <button type="button" id="btnTabAbsen" onclick="switchPortalMainTab('absen')" class="portal-main-tab active">
          <div class="portal-tab-icon">
            <i class="bi bi-calendar3"></i>
          </div>
          <div class="portal-tab-content">
            <div class="portal-tab-title">Riwayat Absensi</div>
            <div class="portal-tab-sub">Kehadiran Harian</div>
          </div>
        </button>

        <button type="button" id="btnTabDisiplin" onclick="switchPortalMainTab('disiplin')" class="portal-main-tab">
          <div class="portal-tab-icon">
            <i class="bi bi-shield-check"></i>
            @if(isset($kasusDisiplin) && $kasusDisiplin->total_poin_pelanggaran > 0)
              <span class="portal-tab-badge danger">{{ $kasusDisiplin->total_poin_pelanggaran }} pt</span>
            @endif
          </div>
          <div class="portal-tab-content">
            <div class="portal-tab-title">Catatan Disiplin</div>
            <div class="portal-tab-sub">Poin &amp; Pelanggaran</div>
          </div>
        </button>

        <button type="button" id="btnTabKartuQr" onclick="switchPortalMainTab('kartu-qr')" class="portal-main-tab">
          <div class="portal-tab-icon">
            <i class="bi bi-qr-code"></i>
          </div>
          <div class="portal-tab-content">
            <div class="portal-tab-title">Kartu &amp; QR Code</div>
            <div class="portal-tab-sub">Kartu Pelajar Digital</div>
          </div>
        </button>

        <button type="button" id="btnTabPengumuman" onclick="switchPortalMainTab('pengumuman')" class="portal-main-tab">
          <div class="portal-tab-icon">
            <i class="bi bi-megaphone-fill"></i>
            @if(isset($pengumumans) && $pengumumans->count() > 0)
              <span id="badgeTabPengumuman" class="portal-tab-badge info">{{ $pengumumans->count() }}</span>
              <script>
                (function(){
                  try {
                    var lastId = localStorage.getItem('sirani_last_read_pengumuman_id');
                    if (lastId && lastId === '{{ $pengumumans->first()->id ?? 0 }}') {
                      var b = document.getElementById('badgeTabPengumuman');
                      if (b) b.style.display = 'none';
                    }
                  } catch(e){}
                })();
              </script>
            @endif
          </div>
          <div class="portal-tab-content">
            <div class="portal-tab-title">Pengumuman</div>
            <div class="portal-tab-sub">Informasi Sekolah</div>
          </div>
        </button>
      </div>

      {{-- TAB 1: KARTU & QR CODE SCANNER (EMERALD CARD + SCANNER KIOSK) --}}
      <div id="section-kartu-qr" style="display: none;">
        {{-- AREA KARTU DIGITAL SISWA UTUH (DITAMPILKAN & DIUNDUH SECARA IDENTIK) --}}
        <div id="kartuSiswaDigitalArea" style="padding:12px; border-radius:24px; background:var(--bg-2, #0f172a); margin-bottom:14px; transition:background-color .25s ease;">
          {{-- CARD SISWA HIJAU EMERALD SMKN 1 AIR NANINGAN --}}
          <div class="card-siswa-emerald">
            <div class="card-siswa-emerald-header">
              <div class="card-siswa-emerald-logo">
                <div style="width:24px; height:24px; background:rgba(255,255,255,0.25); border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,255,255,0.4);">
                  <i class="bi bi-mortarboard-fill" style="font-size:12px; color:#fff;"></i>
                </div>
                <span>SMK NEGERI 1 AIR NANINGAN</span>
              </div>
              <div class="card-siswa-emerald-badge">{{ $siswa->status === 'lulus' ? 'ALUMNI' : 'SISWA' }}</div>
            </div>
            <div class="card-siswa-emerald-body">
              <div class="card-siswa-emerald-avatar">
                @if($siswa->foto && file_exists(public_path('storage/'.$siswa->foto)))
                  <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" />
                @else
                  {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                @endif
              </div>
              <div class="card-siswa-emerald-info">
                <div class="card-siswa-emerald-name">{{ $siswa->nama }}</div>
                <div class="card-siswa-emerald-nisn">NISN: {{ $siswa->nisn ?: $siswa->nis }}</div>
                <div class="card-siswa-emerald-kelas">
                  @if($siswa->status === 'lulus')
                    Alumni · {{ $rombel->nama_rombel ?? 'Lulusan' }} ({{ $rombel->jurusan->nama_jurusan ?? 'Semua Jurusan' }})
                  @else
                    {{ $rombel->nama_rombel ?? 'X' }} - {{ $rombel->jurusan->nama_jurusan ?? 'Semua Jurusan' }}
                  @endif
                </div>
              </div>
            </div>
          </div>

          {{-- CARD SCANNER GERBANG & KIOSK (PUTIH) --}}
          <div class="card-scanner-kiosk" style="margin-bottom:0;">
            <div class="card-scanner-kiosk-title">SCANNER GERBANG &amp; KIOSK</div>

            {{-- KOTAK QR INTERAKTIF --}}
            <div class="qr-interactive-box" onclick="toggleQrFullscreenZoom(true)" title="Sentuh untuk zoom &amp; maksimalkan kecerahan">
              <div id="scannerQrContainer" class="qr-code-canvas-wrap" style="min-width:190px; min-height:190px; display:flex; align-items:center; justify-content:center;"></div>

              <div class="qr-code-number-label">{{ $codeValue }}</div>
            </div>

            {{-- PETUNJUK TAP TO ZOOM --}}
            <div class="qr-tap-instruction" onclick="toggleQrFullscreenZoom(true)">
              <i class="bi bi-arrows-fullscreen"></i>
              <span>Sentuh gambar QR untuk memperbesar &amp; maksimalkan kecerahan</span>
            </div>
          </div>
        </div>

        {{-- TOMBOL AKSI KARTU --}}
        <div style="display:flex; flex-direction:column; gap:10px;">
          {{-- TOMBOL: SIMPAN GAMBAR KARTU IDENTIK --}}
          <button type="button" id="btnDownloadSiswaCard" onclick="downloadSiswaFullCard()" class="btn-qr-action-download">
            <i class="bi bi-download"></i> Simpan Gambar Kartu ke Galeri HP
          </button>
        </div>
      </div>

      {{-- TAB RIWAYAT ABSENSI & DOSSIER SISWA (DEFAULT LANGSUNG TERBUKA) --}}
      <div id="section-absen-wrap" style="display: block;">
        {{-- DOSSIER DIGITAL DETAIL --}}
        <div class="dossier-card">
          <div class="dossier-header">
            <span class="dossier-header-title" style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px;">
              Profil Lengkap &amp; Kehadiran Hari Ini
            </span>
          </div>

          <div class="dossier-body">
            {{-- Kiri: Biodata Siswa --}}
            <div class="student-info-left">
              <div class="student-avatar">
                @if($siswa->foto)
                  <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" />
                @else
                  {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                @endif
              </div>
              <div>
                <h2 class="student-name">{{ $siswa->nama }}</h2>
                <div style="font-size:12px; color:var(--text-2); display:flex; flex-wrap:wrap; gap:6px; justify-content:center;">
                  <span>NIS: <strong style="font-family:var(--font-mono); color:var(--text);">{{ $siswa->nis }}</strong></span>
                  <span>•</span>
                  <span>NISN: <strong style="font-family:var(--font-mono); color:var(--text);">{{ $siswa->nisn ?: '-' }}</strong></span>
                </div>
                <div class="student-meta-tags">
                  <span class="tag-pill">
                    <i class="bi bi-building"></i> {{ $siswa->status === 'lulus' ? 'Kelas Terakhir: ' : 'Kelas ' }}{{ $rombel->nama_rombel ?? 'Belum Ada Rombel' }}
                  </span>
                  @if($rombel && $rombel->jurusan)
                    <span class="tag-pill">
                      <i class="bi bi-book-half"></i> {{ $rombel->jurusan->nama_jurusan }}
                    </span>
                  @endif
                  @if($siswa->status === 'lulus')
                    <span class="tag-pill" style="color:#0284c7; background:rgba(2,132,199,0.1); border-color:rgba(2,132,199,0.3); font-weight:800;">
                      <i class="bi bi-mortarboard-fill"></i> Alumni / Lulusan
                    </span>
                  @elseif($siswa->status === 'pkl')
                    <span class="tag-pill" style="color:var(--text); font-weight:800;">
                      <i class="bi bi-briefcase"></i> Praktik Kerja (PKL)
                    </span>
                  @elseif($siswa->status === 'pindah')
                    <span class="tag-pill" style="color:#d97706; background:rgba(217,119,6,0.1); border-color:rgba(217,119,6,0.3); font-weight:800;">
                      <i class="bi bi-box-arrow-right"></i> Pindah
                    </span>
                  @elseif($siswa->status === 'keluar')
                    <span class="tag-pill" style="color:#ef4444; background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.3); font-weight:800;">
                      <i class="bi bi-x-circle-fill"></i> Keluar
                    </span>
                  @endif
                  @if($waliKelas)
                    <span class="tag-pill">
                      <i class="bi bi-person"></i> Wali Kelas: {{ $waliKelas->nama }}
                    </span>
                    @if($waliKelas->no_hp)
                      @php
                        $hpWaliClean = preg_replace('/[^0-9]/', '', $waliKelas->no_hp);
                        if (str_starts_with($hpWaliClean, '0')) $hpWaliClean = '62' . substr($hpWaliClean, 1);
                        $pesanWaWali = rawurlencode("Halo Bapak/Ibu Wali Kelas {$waliKelas->nama}, saya orang tua dari {$siswa->nama} (Kelas " . ($rombel->nama_rombel ?? '-') . "). Ingin berkonsultasi mengenai kehadiran/perkembangan belajar ananda.");
                      @endphp
                      <a href="https://wa.me/{{ $hpWaliClean }}?text={{ $pesanWaWali }}" target="_blank" class="tag-pill" style="color:#16A34A; font-weight:800;" title="Konsultasi WhatsApp dengan Wali Kelas">
                        <i class="bi bi-whatsapp"></i> Hubungi Wali Kelas
                      </a>
                    @endif
                  @endif
                  @if($siswa->nama_ortu)
                    <span class="tag-pill">
                      <i class="bi bi-people"></i> Wali Murid: {{ $siswa->nama_ortu }}
                    </span>
                  @endif
                </div>
              </div>
            </div>

            {{-- Kanan: Status Kehadiran Hari Ini --}}
            <div class="today-widget">
              <div class="today-widget-title">
                <span style="font-weight:800; color:var(--text);">Kehadiran Hari Ini ({{ \Carbon\Carbon::today()->translatedFormat('d M Y') }})</span>
                <div>
                  @if($todayAbsensi)
                    @if($todayAbsensi->status === 'hadir')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Hadir Tepat Waktu</span>
                    @elseif($todayAbsensi->status === 'terlambat')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Terlambat</span>
                    @elseif($todayAbsensi->status === 'izin')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Izin</span>
                    @elseif($todayAbsensi->status === 'sakit')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Sakit</span>
                    @elseif($todayAbsensi->status === 'bolos')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Bolos</span>
                    @elseif($todayAbsensi->status === 'alpha')
                      <span style="font-weight:800; font-size:12px; color:var(--text);">Alpha</span>
                    @endif
                  @elseif($siswa->status === 'lulus')
                    <span style="font-weight:800; font-size:12px; color:#0284c7;">Alumni (Telah Lulus)</span>
                  @elseif($siswa->status === 'pkl')
                    <span style="font-weight:800; font-size:12px; color:var(--text);">PKL</span>
                  @else
                    <span style="font-weight:800; font-size:12px; color:var(--text);">Belum Presensi</span>
                  @endif
                </div>
              </div>

              <div class="today-time-grid">
                <div class="today-time-box">
                  <div class="today-time-label">Masuk Gerbang</div>
                  <div class="today-time-val">
                    {{ $todayAbsensi && $todayAbsensi->jam_masuk ? substr($todayAbsensi->jam_masuk, 0, 5).' WIB' : '—' }}
                  </div>
                </div>
                <div class="today-time-box">
                  <div class="today-time-label">Pulang Gerbang</div>
                  <div class="today-time-val">
                    {{ $todayAbsensi && $todayAbsensi->jam_pulang ? substr($todayAbsensi->jam_pulang, 0, 5).' WIB' : '—' }}
                  </div>
                </div>
              </div>
              
              <div style="font-size:11px; color:var(--text-3); text-align:center; margin-top:8px;">
                {{ $todayAbsensi ? ($todayAbsensi->keterangan ?: 'Tervalidasi via Smart Gate RFID/QR SMKN 1 AN') : ($siswa->status === 'lulus' ? 'Siswa telah menyelesaikan studi di SMKN 1 Air Naningan (Status: Alumni)' : ($siswa->status === 'pkl' ? 'Siswa sedang melaksanakan PKL di Industri' : 'Belum ada rekaman presensi di gerbang hari ini')) }}
              </div>
            </div>
          </div>
        </div>

        {{-- RIWAYAT KEHADIRAN TERPADU --}}
        <div id="riwayat-kehadiran" style="scroll-margin-top: 70px;">
        
        {{-- RINGKASAN METRIK KEHADIRAN (RINGKAS & PROPORSIONAL) --}}
        <div class="attendance-summary-card">
          <div class="summary-top-row">
            <div class="summary-main-score">
              <span class="summary-score-val">{{ $stats['persen'] }}%</span>
              <div class="summary-score-meta">
                <strong>Tingkat Kehadiran Siswa</strong>
                <span>{{ $stats['predikat'] }} &middot; Total {{ $stats['total'] }} Hari Efektif</span>
              </div>
            </div>
          </div>
          <div class="summary-pills-row">
            <div class="summary-stat-pill pill-hadir">
              <span class="pill-dot"></span>
              <span class="pill-num">{{ $stats['hadir'] }}</span>
              <span class="pill-lbl">Hadir Tepat</span>
            </div>
            <div class="summary-stat-pill pill-telat">
              <span class="pill-dot"></span>
              <span class="pill-num">{{ $stats['terlambat'] }}</span>
              <span class="pill-lbl">Terlambat</span>
            </div>
            <div class="summary-stat-pill pill-izin">
              <span class="pill-dot"></span>
              <span class="pill-num">{{ $stats['izin'] + $stats['sakit'] }}</span>
              <span class="pill-lbl">Izin/Sakit</span>
            </div>
            <div class="summary-stat-pill pill-alpha">
              <span class="pill-dot"></span>
              <span class="pill-num">{{ $stats['alpha'] + $stats['bolos'] }}</span>
              <span class="pill-lbl">Alpha/Bolos</span>
            </div>
          </div>
        </div>

        {{-- KONTROL PILIHAN PERIODE (RINGKAS & RAMPING) --}}
        <div class="period-control-card">
          <div class="period-tabs">
            <a href="/cek-presensi?keyword={{ $siswa->nisn ?: $siswa->nis }}&periode=harian" class="period-btn {{ $periode === 'harian' ? 'active' : '' }}">
              <i class="bi bi-calendar-day"></i> Harian
            </a>
            <a href="/cek-presensi?keyword={{ $siswa->nisn ?: $siswa->nis }}&periode=mingguan" class="period-btn {{ $periode === 'mingguan' ? 'active' : '' }}">
              <i class="bi bi-calendar-week"></i> Mingguan
            </a>
            <a href="/cek-presensi?keyword={{ $siswa->nisn ?: $siswa->nis }}&periode=bulanan" class="period-btn {{ $periode === 'bulanan' ? 'active' : '' }}">
              <i class="bi bi-calendar-month"></i> Bulanan
            </a>
            <a href="/cek-presensi?keyword={{ $siswa->nisn ?: $siswa->nis }}&periode=tahunan" class="period-btn {{ $periode === 'tahunan' ? 'active' : '' }}">
              <i class="bi bi-calendar3"></i> Tahunan
            </a>
          </div>

          <form method="GET" action="{{ route('portal.ortu.index') }}" class="period-input-wrap">
            <input type="hidden" name="keyword" value="{{ $siswa->nisn ?: $siswa->nis }}" />
            <input type="hidden" name="periode" value="{{ $periode }}" />

            @if($periode === 'harian')
              <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)" />
            @elseif($periode === 'mingguan')
              <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="form-control-pt" />
              <span style="font-size:11.5px; color:var(--text-3); font-weight:700;">s/d</span>
              <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)" />
              <button type="submit" class="btn-search" style="padding:4px 10px; font-size:11.5px; border-radius:6px;">Pilih</button>
            @elseif($periode === 'tahunan')
              <select name="tahun" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)">
                @for($y = date('Y') + 1; $y >= 2024; $y--)
                  <option value="{{ $y }}" {{ $tahunSelected == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
              </select>
            @else
              {{-- Bulanan --}}
              <select name="bulan" class="form-control-pt" onchange="preserveScrollAndSubmit(this.form)">
                @for($i = 0; $i < 12; $i++)
                  @php
                    $m = \Carbon\Carbon::today()->subMonths($i);
                    $val = $m->format('Y-m');
                  @endphp
                  <option value="{{ $val }}" {{ $bulanSelected === $val ? 'selected' : '' }}>
                    {{ $m->translatedFormat('F Y') }}
                  </option>
                @endfor
              </select>
            @endif
          </form>
        </div>

        {{-- TABEL RINCIAN LOG / REKAPITULASI KEHADIRAN --}}
        <div class="panel">
          <div class="panel-title">
            @if($periode === 'tahunan')
              <span><i class="bi bi-calendar3" style="color:var(--gold); margin-right:6px;"></i>Rekap Bulanan (Tahun {{ $tahunSelected }})</span>
              <span style="font-size:11.5px; font-weight:700; color:var(--text-3); font-family:var(--font-mono);">12 Bulan Terdata</span>
            @else
              <span><i class="bi bi-journal-check" style="color:var(--gold); margin-right:6px;"></i>Riwayat Kehadiran ({{ $periodeText }})</span>
              <span style="font-size:11.5px; font-weight:700; color:var(--text-3); font-family:var(--font-mono);">{{ $absensis->count() }} Hari Tercatat</span>
            @endif
          </div>

          <div class="table-wrap">
            @if($periode === 'tahunan')
              {{-- 1. REKAP TAHUNAN (JUMLAH PER BULAN) --}}
              <table>
                <thead>
                  <tr>
                    <th style="width:160px; white-space:nowrap;">Bulan</th>
                    <th style="width:90px; text-align:center; white-space:nowrap;">Hadir</th>
                    <th style="width:90px; text-align:center; white-space:nowrap;">Terlambat</th>
                    <th style="width:110px; text-align:center; white-space:nowrap;">Izin / Sakit</th>
                    <th style="width:110px; text-align:center; white-space:nowrap;">Alpha / Bolos</th>
                    <th style="width:100px; text-align:center; white-space:nowrap;">Total Hari</th>
                    <th style="width:130px; text-align:center; white-space:nowrap;">Kedisiplinan</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $totHadir = 0; $totTelat = 0; $totIzinSakit = 0; $totAlphaBolos = 0; $totSemua = 0;
                  @endphp
                  @foreach($rekapBulananTahunan as $rb)
                    @php
                      $totHadir += $rb['hadir'];
                      $totTelat += $rb['terlambat'];
                      $totIzinSakit += ($rb['izin'] + $rb['sakit']);
                      $totAlphaBolos += ($rb['alpha'] + $rb['bolos']);
                      $totSemua += $rb['total'];
                    @endphp
                    <tr>
                      <td style="white-space:nowrap;">
                        <strong style="color:var(--text); font-size:13px;">{{ $rb['bulan_nama'] }}</strong>
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['hadir'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['terlambat'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['izin'] + $rb['sakit'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['alpha'] + $rb['bolos'] }}
                      </td>
                      <td style="text-align:center; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        {{ $rb['total'] }}
                      </td>
                      <td style="text-align:center; white-space:nowrap; font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text);">
                        @if($rb['total'] > 0)
                          {{ $rb['persen'] }}%
                        @else
                          <span style="color:var(--text-3); font-size:12px;">—</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                  <tr style="background:var(--bg-subtle); font-weight:800; border-top:2px solid var(--border);">
                    <td style="white-space:nowrap; font-weight:900; color:var(--text);">
                      <i class="bi bi-calculator" style="color:var(--gold);"></i> TOTAL 1 TAHUN
                    </td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totHadir }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totTelat }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totIzinSakit }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totAlphaBolos }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:var(--text);">{{ $totSemua }}</td>
                    <td style="text-align:center; font-family:var(--font-mono); font-weight:900; font-size:13.5px; color:var(--text);">
                      {{ $stats['persen'] }}%
                    </td>
                  </tr>
                </tbody>
              </table>

            @else
              {{-- 2. REKAP HARIAN, MINGGUAN & BULANAN (TABEL RINCIAN LOG TANGGAL) --}}
              @if($absensis->count() > 0)
                <div class="mobile-scroll-hint">
                  <i class="bi bi-arrows-expand"></i> Geser tabel ke samping untuk melihat data lengkap
                </div>
                <table>
                  <thead>
                    <tr>
                      <th style="width:170px; white-space:nowrap;">Hari &amp; Tanggal</th>
                      <th style="width:110px; text-align:center; white-space:nowrap;">Jam Masuk</th>
                      <th style="width:110px; text-align:center; white-space:nowrap;">Jam Pulang</th>
                      <th style="width:170px; text-align:center; white-space:nowrap;">Status Kehadiran</th>
                      <th style="min-width:180px;">Keterangan / Alasan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($absensis as $abs)
                      <tr>
                        <td style="white-space:nowrap;">
                          <strong style="color:var(--text); font-size:13px;">{{ \Carbon\Carbon::parse($abs->tanggal)->translatedFormat('l') }}</strong>
                          <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">{{ \Carbon\Carbon::parse($abs->tanggal)->translatedFormat('d M Y') }}</div>
                        </td>
                        <td style="text-align:center; white-space:nowrap;">
                          <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text); white-space:nowrap;">
                            {{ $abs->jam_masuk ? substr($abs->jam_masuk, 0, 5).' WIB' : '—' }}
                          </span>
                        </td>
                        <td style="text-align:center; white-space:nowrap;">
                          <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:var(--text); white-space:nowrap;">
                            {{ $abs->jam_pulang ? substr($abs->jam_pulang, 0, 5).' WIB' : '—' }}
                          </span>
                        </td>
                        <td style="text-align:center; white-space:nowrap;">
                          @if($abs->status === 'hadir')
                            <span style="font-weight:800; font-size:12.5px; color:#16a34a;"><i class="bi bi-check-circle-fill"></i> Hadir Tepat Waktu</span>
                          @elseif($abs->status === 'terlambat')
                            <span style="font-weight:800; font-size:12.5px; color:#d97706;"><i class="bi bi-clock-history"></i> Terlambat</span>
                          @elseif($abs->status === 'izin')
                            <span style="font-weight:800; font-size:12.5px; color:#0284c7;"><i class="bi bi-info-circle-fill"></i> Izin</span>
                          @elseif($abs->status === 'sakit')
                            <span style="font-weight:800; font-size:12.5px; color:#8b5cf6;"><i class="bi bi-heart-pulse-fill"></i> Sakit</span>
                          @elseif($abs->status === 'bolos')
                            <span style="font-weight:800; font-size:12.5px; color:#dc2626;"><i class="bi bi-exclamation-triangle-fill"></i> Bolos</span>
                          @elseif($abs->status === 'alpha')
                            <span style="font-weight:800; font-size:12.5px; color:#ef4444;"><i class="bi bi-x-circle-fill"></i> Alpha</span>
                          @endif
                        </td>
                        <td style="font-size:12.5px; color:var(--text-2); min-width:180px;">
                          @if($abs->keterangan)
                            <span style="font-weight:600; color:var(--text);"><i class="bi bi-chat-left-text-fill" style="color:var(--gold); font-size:11px; margin-right:4px;"></i>{{ $abs->keterangan }}</span>
                          @elseif($abs->status === 'bolos')
                            <span style="color:#991B1B; font-weight:600;"><i class="bi bi-exclamation-triangle-fill"></i> Tidak tap pulang (tanpa izin piket)</span>
                          @elseif($abs->status === 'alpha')
                            <span style="color:#DC2626; font-weight:600;"><i class="bi bi-x-circle-fill"></i> Tidak hadir tanpa keterangan</span>
                          @elseif($abs->status === 'terlambat')
                            <span style="color:var(--amber); font-weight:600;"><i class="bi bi-clock-history"></i> Terlambat masuk gerbang</span>
                          @elseif($abs->status === 'hadir')
                            <span style="color:var(--text-3);"><i class="bi bi-check2"></i> Hadir pembelajaran reguler</span>
                          @else
                            -
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @else
                <div style="text-align:center; padding:32px 16px; color:var(--text-3);">
                  <i class="bi bi-calendar2-check" style="font-size:32px; color:var(--border-2); margin-bottom:8px; display:inline-block;"></i>
                  <div style="font-weight:800; font-size:13.5px; color:var(--text-2); margin-bottom:4px;">Belum Ada Catatan Kehadiran</div>
                  <div style="font-size:12px; color:var(--text-3);">Tidak ada rekaman presensi pada periode {{ $periodeText }}.</div>
                </div>
              @endif
            @endif
          </div>
        </div>
      </div>
      </div>

      {{-- TAB 2: CATATAN KEDISIPLINAN SISWA --}}
      <div id="section-disiplin-wrap" style="display: none;">
        @php
          $poinBersih = $kasusDisiplin ? $kasusDisiplin->poin_bersih : 0;
          $totalPelanggaran = $kasusDisiplin ? $kasusDisiplin->total_poin_pelanggaran : 0;
          $totalPemulihan = $kasusDisiplin ? $kasusDisiplin->total_poin_pemulihan : 0;
          $a1 = $pengaturanDisiplin->ambang_tahap_1_wali ?? 10;
          $a2 = $pengaturanDisiplin->ambang_tahap_2_bk ?? 30;
          $a3 = $pengaturanDisiplin->ambang_tahap_3_wakasis ?? 50;
          $a4 = $pengaturanDisiplin->ambang_tahap_4_kepsek ?? 75;
        @endphp
        <div class="dossier-card" id="portofolio-karakter" style="margin-top: 0px; scroll-margin-top: 70px; display: block;">
          <div class="dossier-header">
            <div>
              <h3 style="font-size:14.5px; font-weight:800; color:var(--text); margin:0;">Catatan Kedisiplinan Siswa</h3>
              <p style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Transparansi poin kedisiplinan, catatan ketidakhadiran, serta apresiasi perilaku positif</p>
            </div>
          <div>
            @if($poinBersih == 0)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Status: Tertib &amp; Bebas Masalah (0 Poin)
              </span>
            @elseif($poinBersih >= $a4)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 4: Penanganan Kepala Sekolah ({{ $poinBersih }} Poin)
              </span>
            @elseif($poinBersih >= $a3)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 3: Pembinaan Kesiswaan ({{ $poinBersih }} Poin)
              </span>
            @elseif($poinBersih >= $a2)
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 2: Bimbingan Konseling BK ({{ $poinBersih }} Poin)
              </span>
            @else
              <span style="font-weight:800; font-size:12px; color:var(--text);">
                Tahap 1: Bimbingan Wali Kelas ({{ $poinBersih }} Poin)
              </span>
            @endif
          </div>
        </div>

        {{-- Body 2 Kolom --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
          
          {{-- Kolom Kiri: Apresiasi & Tindakan Positif --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-size:12.5px; font-weight:800; color:var(--text);">
                Apresiasi &amp; Self-Reward
              </span>
              <span style="font-weight:900; font-size:13px; font-family:var(--font-mono); color:var(--text);">
                -{{ $totalPemulihan }} Poin
              </span>
            </div>

            @if($kasusDisiplin && $kasusDisiplin->rewards->count() > 0)
              <div style="display:flex; flex-direction:column; gap:6px;">
                @foreach($kasusDisiplin->rewards as $rew)
                  <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:8px 10px; display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                    <div style="flex:1; min-width:0;">
                      <strong style="color:var(--text); font-size:11.5px; display:block; line-height:1.3;">{{ $rew->nama_tindakan }}</strong>
                      <div style="font-size:10px; color:var(--text-3); margin-top:2px;">
                        {{ \Carbon\Carbon::parse($rew->tanggal)->translatedFormat('d M Y') }} · {{ $rew->dicatat_oleh }}
                      </div>
                    </div>
                    <span style="font-weight:800; font-size:11px; font-family:var(--font-mono); color:var(--text); flex-shrink:0;">
                      -{{ $rew->poin_dikurangi }} Poin
                    </span>
                  </div>
                @endforeach
              </div>
            @else
              <div style="text-align:center; padding:16px 10px; color:var(--text-3); font-size:11.5px; line-height:1.4;">
                Siswa dapat meraih poin pemulihan dan apresiasi melalui prestasi lomba, hafalan ibadah, bakti sosial, serta kehadiran 100% tepat waktu.
              </div>
            @endif
          </div>

          {{-- Kolom Kanan: Catatan Kedisiplinan & Pelanggaran --}}
          <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-size:12.5px; font-weight:800; color:var(--text);">
                Catatan Kedisiplinan
              </span>
              <span style="font-weight:900; font-size:13px; font-family:var(--font-mono); color:var(--text);">
                +{{ $totalPelanggaran }} Poin
              </span>
            </div>

            @if($kasusDisiplin && ($kasusDisiplin->total_alpha > 0 || $kasusDisiplin->total_bolos > 0 || $kasusDisiplin->total_terlambat > 0 || $kasusDisiplin->pelanggarans->count() > 0))
              <div style="display:flex; flex-direction:column; gap:6px; font-size:11.5px;">
                @if($kasusDisiplin->total_alpha > 0)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text);">Alpha (Tidak Hadir):</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_alpha }}h ({{ $kasusDisiplin->total_alpha * ($pengaturanDisiplin->bobot_alpha ?? 10) }} pt)</strong>
                  </div>
                @endif
                @if($kasusDisiplin->total_bolos > 0)
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text);">Bolos Jam Pelajaran:</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_bolos }}x ({{ $kasusDisiplin->total_bolos * ($pengaturanDisiplin->bobot_bolos ?? 15) }} pt)</strong>
                  </div>
                @endif
                @if($kasusDisiplin->total_terlambat > 0)
                  @php
                    $hLate = max(0, $kasusDisiplin->total_terlambat - ($pengaturanDisiplin->toleransi_terlambat_piket ?? 2));
                  @endphp
                  <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <span style="color:var(--text);">Keterlambatan:</span>
                    <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasusDisiplin->total_terlambat }}x ({{ $hLate * ($pengaturanDisiplin->bobot_terlambat ?? 3) }} pt)</strong>
                  </div>
                @endif
                @foreach($kasusDisiplin->pelanggarans as $pel)
                  <div style="display:flex; justify-content:space-between; align-items:flex-start; background:var(--bg-card); border:1px solid var(--border); padding:6px 10px; border-radius:6px;">
                    <div style="flex:1; min-width:0;">
                      <strong style="color:var(--text); font-size:11px; display:block;">{{ $pel->nama_pelanggaran }}</strong>
                      <div style="font-size:9.5px; color:var(--text-3);">{{ \Carbon\Carbon::parse($pel->tanggal)->translatedFormat('d M Y') }}</div>
                    </div>
                    <span style="color:var(--text); font-weight:800; font-size:11px; font-family:var(--font-mono); flex-shrink:0;">+{{ $pel->poin_ditambah }} pt</span>
                  </div>
                @endforeach
              </div>
            @else
              <div style="text-align:center; padding:16px 10px; color:var(--text-3); font-size:11.5px; line-height:1.4;">
                Tidak ada catatan pelanggaran tata tertib. Pertahankan kedisiplinan belajar ananda!
              </div>
            @endif
          </div>
        </div>

        {{-- Edukasi Kolaboratif Sekolah & Keluarga --}}
        <div style="margin-top:14px; background:var(--bg-subtle); border:1px solid var(--border); border-radius:8px; padding:10px 14px; display:flex; align-items:flex-start; gap:10px;">
          <span style="font-size:11.5px; color:var(--text); line-height:1.45;">
            <strong>Prinsip Pendidikan Positif:</strong> Seluruh catatan ketertiban bersifat edukatif dan dapat dipulihkan melalui perbaikan perilaku, keaktifan ibadah, serta konsistensi hadir tepat waktu di sekolah.
          </span>
        </div>
      </div> {{-- Penutup #portofolio-karakter --}}
      </div> {{-- Penutup #section-disiplin-wrap --}}

      {{-- 4. PENGUMUMAN RESMI SEKOLAH --}}
      <div class="dossier-card" id="section-pengumuman" style="margin-top: 20px; scroll-margin-top: 70px; display: none;">
        <div class="dossier-header">
          <div>
            <h3 style="font-size:14.5px; font-weight:800; color:var(--text); margin:0;">Pengumuman &amp; Informasi Sekolah</h3>
            <p style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Pemberitahuan resmi dari pihak SMKN 1 Air Naningan untuk wali murid</p>
          </div>
          @if(isset($pengumumans) && $pengumumans->count() > 0)
            <span style="font-size:11.5px; font-weight:800; color:var(--text);">
              {{ $pengumumans->count() }} Pengumuman Aktif
            </span>
          @endif
        </div>

        @if(isset($pengumumans) && $pengumumans->count() > 0)
          <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($pengumumans as $p)
              @php $badge = $p->kategori_badge; @endphp
              <div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:14px 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; margin-bottom:4px;">
                  <span style="font-size:11px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.4px;">
                    {{ $badge['label'] }}
                  </span>
                  <span style="font-size:11px; color:var(--text-3); font-weight:600;">
                    <i class="bi bi-clock"></i> {{ $p->created_at->translatedFormat('d M Y') }}
                  </span>
                </div>
                <h4 style="font-size:14px; font-weight:800; color:var(--text); margin:0 0 6px;">{{ $p->judul }}</h4>
                <p style="font-size:12.5px; color:var(--text-2); line-height:1.5; margin:0; white-space:pre-line;">{{ $p->isi_pesan }}</p>
                
                @if($p->banner_url)
                  <div style="position:relative; margin-top:10px; border-radius:8px; overflow:hidden; border:1px solid var(--border); max-width:100%; cursor:zoom-in;" onclick="openImageZoom('{{ $p->banner_url }}', '{{ addslashes($p->judul) }}')">
                    <img src="{{ $p->banner_url }}" alt="{{ $p->judul }}" style="width:100%; max-height:380px; object-fit:contain; background:rgba(0,0,0,0.02); display:block;" />
                    <div style="position:absolute; bottom:8px; right:8px; background:rgba(15,23,42,0.85); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:6px; backdrop-filter:blur(4px); display:flex; align-items:center; gap:5px; pointer-events:none;">
                      <i class="bi bi-arrows-fullscreen"></i> Ketuk untuk Zoom
                    </div>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div style="text-align:center; padding:24px 16px; color:var(--text-3); font-size:12px;">
            <i class="bi bi-megaphone" style="font-size:24px; display:block; margin-bottom:6px; opacity:0.6;"></i>
            Saat ini belum ada pengumuman resmi terbaru dari sekolah.
          </div>
        @endif
      </div>

    @endif

  </main>

  {{-- FOOTER --}}
  <footer class="footer">
    <div style="font-weight:800; color:var(--text); margin-bottom:3px;">SIRANI · Sistem Informasi Responsif Absensi</div>
    <div>SMK Negeri 1 Air Naningan · Layanan pemantauan kehadiran mandiri terpadu</div>
  </footer>

  <script>
    // Preservasi posisi scroll saat pergantian tab atau filter tanggal
    function preserveScrollAndSubmit(form) {
      sessionStorage.setItem('portal_scroll_y', window.scrollY);
      form.submit();
    }

    document.querySelectorAll('.period-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        sessionStorage.setItem('portal_scroll_y', window.scrollY);
      });
    });

    window.addEventListener('beforeunload', function() {
      sessionStorage.setItem('portal_scroll_y', window.scrollY);
    });

    document.addEventListener('DOMContentLoaded', function() {
      const savedY = sessionStorage.getItem('portal_scroll_y');
      if (savedY !== null) {
        window.scrollTo({
          top: parseInt(savedY, 10),
          behavior: 'instant'
        });
        sessionStorage.removeItem('portal_scroll_y');
      }
    });

    function copyLinkPresensi(url) {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() {
          showCopySuccess();
        }).catch(function() {
          fallbackCopy(url);
        });
      } else {
        fallbackCopy(url);
      }
    }

    function fallbackCopy(url) {
      const temp = document.createElement('input');
      temp.value = url;
      document.body.appendChild(temp);
      temp.select();
      document.execCommand('copy');
      document.body.removeChild(temp);
      showCopySuccess();
    }

    function showCopySuccess() {
      const btnText = document.getElementById('copyBtnText');
      if (btnText) {
        const orig = btnText.innerText;
        btnText.innerText = 'Tersalin!';
        setTimeout(function() {
          btnText.innerText = orig;
        }, 2000);
      }
    }

    function resetSiswaTersimpan() {
      window.location.href = '/cek-presensi';
    }

    // ===== MAIN TABS SWITCHER (RIWAYAT ABSENSI, CATATAN KEDISIPLINAN, KARTU & QR, PENGUMUMAN) =====
    function switchPortalMainTab(tabName) {
      const secKartuQr = document.getElementById('section-kartu-qr');
      const secAbsenWrap = document.getElementById('section-absen-wrap');
      const secDisiplinWrap = document.getElementById('section-disiplin-wrap');
      const secPengumuman = document.getElementById('section-pengumuman');
      
      const btnKartuQr = document.getElementById('btnTabKartuQr');
      const btnAbsen = document.getElementById('btnTabAbsen');
      const btnDisiplin = document.getElementById('btnTabDisiplin');
      const btnPengumuman = document.getElementById('btnTabPengumuman');

      if (tabName === 'kartu-qr') {
        if (secKartuQr) secKartuQr.style.display = 'block';
        if (secAbsenWrap) secAbsenWrap.style.display = 'none';
        if (secDisiplinWrap) secDisiplinWrap.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'none';

        if (btnKartuQr) btnKartuQr.classList.add('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnDisiplin) btnDisiplin.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
        if (typeof renderPortalQrCode === 'function') {
          setTimeout(renderPortalQrCode, 50);
        }
      } else if (tabName === 'disiplin') {
        if (secKartuQr) secKartuQr.style.display = 'none';
        if (secAbsenWrap) secAbsenWrap.style.display = 'none';
        if (secDisiplinWrap) secDisiplinWrap.style.display = 'block';
        if (secPengumuman) secPengumuman.style.display = 'none';

        if (btnKartuQr) btnKartuQr.classList.remove('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnDisiplin) btnDisiplin.classList.add('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
      } else if (tabName === 'absen') {
        if (secKartuQr) secKartuQr.style.display = 'none';
        if (secAbsenWrap) secAbsenWrap.style.display = 'block';
        if (secDisiplinWrap) secDisiplinWrap.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'none';

        if (btnKartuQr) btnKartuQr.classList.remove('active');
        if (btnAbsen) btnAbsen.classList.add('active');
        if (btnDisiplin) btnDisiplin.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.remove('active');
      } else if (tabName === 'pengumuman') {
        if (secKartuQr) secKartuQr.style.display = 'none';
        if (secAbsenWrap) secAbsenWrap.style.display = 'none';
        if (secDisiplinWrap) secDisiplinWrap.style.display = 'none';
        if (secPengumuman) secPengumuman.style.display = 'block';

        if (btnKartuQr) btnKartuQr.classList.remove('active');
        if (btnAbsen) btnAbsen.classList.remove('active');
        if (btnDisiplin) btnDisiplin.classList.remove('active');
        if (btnPengumuman) btnPengumuman.classList.add('active');

        // Hilangkan angka notifikasi saat tab pengumuman dibuka
        markPengumumanAsRead();
      }
    }
    window.switchPortalMainTab = switchPortalMainTab;

    function markPengumumanAsRead() {
      const badge = document.getElementById('badgeTabPengumuman') || document.querySelector('#btnTabPengumuman .portal-tab-badge');
      if (badge) {
        badge.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        badge.style.opacity = '0';
        badge.style.transform = 'scale(0.5)';
        setTimeout(function() {
          badge.style.display = 'none';
        }, 250);
      }
      @if(isset($pengumumans) && $pengumumans->count() > 0)
        try {
          const latestId = '{{ $pengumumans->first()->id ?? 0 }}';
          localStorage.setItem('sirani_last_read_pengumuman_id', latestId);
          localStorage.setItem('sirani_last_read_pengumuman_count', '{{ $pengumumans->count() }}');
          localStorage.setItem('sirani_last_seen_popup_pengumuman_id', latestId);
        } catch(e) {}
      @endif
    }
    window.markPengumumanAsRead = markPengumumanAsRead;

    // Kontroler Pop-up Pengumuman Otomatis
    function openPengumumanPopup() {
      const modal = document.getElementById('modalPengumumanPopup');
      if (!modal) return;
      modal.style.display = 'flex';
      try {
        if (typeof sirani_playPreviewSound === 'function') {
          sirani_playPreviewSound('chime');
        }
      } catch(e) {}
    }

    function closePengumumanPopup(markAsRead = true) {
      const modal = document.getElementById('modalPengumumanPopup');
      if (modal) modal.style.display = 'none';
      if (markAsRead) {
        markPengumumanAsRead();
      }
    }

    function handlePengumumanPopupOverlayClick(event) {
      if (event.target && event.target.id === 'modalPengumumanPopup') {
        closePengumumanPopup(true);
      }
    }

    function goToAllPengumumanFromPopup() {
      closePengumumanPopup(true);
      if (typeof switchPortalMainTab === 'function') {
        switchPortalMainTab('pengumuman');
      }
      setTimeout(function() {
        const sec = document.getElementById('section-pengumuman');
        if (sec) sec.scrollIntoView({ behavior: 'smooth' });
      }, 150);
    }

    function checkAutoPopupPengumuman() {
      @if(isset($pengumumans) && $pengumumans->count() > 0)
        try {
          const latestId = '{{ $pengumumans->first()->id ?? 0 }}';
          const lastSeenId = localStorage.getItem('sirani_last_seen_popup_pengumuman_id');
          const lastReadId = localStorage.getItem('sirani_last_read_pengumuman_id');

          if (latestId && latestId !== '0' && lastSeenId !== latestId && lastReadId !== latestId) {
            setTimeout(function() {
              openPengumumanPopup();
            }, 800);
          }
        } catch(e) {}
      @endif
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', checkAutoPopupPengumuman);
    } else {
      checkAutoPopupPengumuman();
    }

    window.openPengumumanPopup = openPengumumanPopup;
    window.closePengumumanPopup = closePengumumanPopup;
    window.handlePengumumanPopupOverlayClick = handlePengumumanPopupOverlayClick;
    window.goToAllPengumumanFromPopup = goToAllPengumumanFromPopup;

    @if($siswa)
    // ===== QR CODE & BARCODE SCANNER KIOSK & AUTO-ZOOM MAX BRIGHTNESS =====
    const KARTU_CODE_VALUE = @json($codeValue);
    let screenWakeLock = null;

    function renderPortalQrCode() {
      const codeVal = KARTU_CODE_VALUE || '{{ $siswa->nisn ?? $siswa->nis }}';
      if (!codeVal) return;

      // 1. Render Normal Scanner QR Code (2D)
      const containerScanner = document.getElementById('scannerQrContainer');
      if (containerScanner) {
        containerScanner.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
          try {
            new QRCode(containerScanner, {
              text: codeVal,
              width: 190,
              height: 190,
              colorDark: '#000000',
              colorLight: '#ffffff',
              correctLevel: (typeof QRCode.CorrectLevel !== 'undefined') ? QRCode.CorrectLevel.M : 0
            });
          } catch(e) {
            console.warn('QR render error:', e);
          }
        }
      }

      // 2. Render Fullscreen Zoomed QR Code (2D)
      const containerZoomed = document.getElementById('zoomedQrContainer');
      if (containerZoomed) {
        containerZoomed.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
          try {
            new QRCode(containerZoomed, {
              text: codeVal,
              width: 240,
              height: 240,
              colorDark: '#000000',
              colorLight: '#ffffff',
              correctLevel: (typeof QRCode.CorrectLevel !== 'undefined') ? QRCode.CorrectLevel.H : 0
            });
          } catch(e) {
            console.warn('QR Zoom render error:', e);
          }
        }
      }
    }

    // AUTO-ZOOM & MAX BRIGHTNESS TOGGLE
    async function toggleQrFullscreenZoom(isOpen) {
      const overlay = document.getElementById('qrZoomOverlay');
      if (!overlay) return;

      if (isOpen) {
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        renderPortalQrCode();

        // Aktifkan Screen WakeLock API agar layar HP tetap menyala terang (tidak mati/redup saat antre scan)
        try {
          if ('wakeLock' in navigator && navigator.wakeLock) {
            screenWakeLock = await navigator.wakeLock.request('screen');
          }
        } catch(err) {
          console.log('WakeLock not supported or denied', err);
        }
      } else {
        overlay.style.display = 'none';
        document.body.style.overflow = '';

        // Lepaskan WakeLock (kecerahan kembali normal)
        if (screenWakeLock !== null) {
          try {
            await screenWakeLock.release();
            screenWakeLock = null;
          } catch(err) {}
        }
      }
    }
    window.toggleQrFullscreenZoom = toggleQrFullscreenZoom;

    // SIMPAN GAMBAR KARTU SISWA UTUH KE GALERI HP
    async function downloadSiswaFullCard() {
      const cardElement = document.getElementById('kartuSiswaDigitalArea');
      if (!cardElement) return;

      const btn = document.getElementById('btnDownloadSiswaCard');
      const origHtml = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Merender Gambar Kartu...';
      }

      try {
        await new Promise(r => setTimeout(r, 80));

        const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
        const bgColor = isDark ? '#0f172a' : '#ffffff';

        const canvas = await html2canvas(cardElement, {
          scale: 3,
          useCORS: true,
          allowTaint: true,
          backgroundColor: bgColor,
          logging: false,
          scrollX: 0,
          scrollY: -window.scrollY
        });

        const safeFilename = 'KARTU_PRESENSI_{{ Str::slug($siswa->nama) }}_{{ $codeValue }}.png';

        if (navigator.canShare && window.File) {
          try {
            canvas.toBlob(async (blob) => {
              if (blob) {
                const file = new File([blob], safeFilename, { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                  await navigator.share({
                    files: [file],
                    title: 'Kartu Presensi Siswa',
                    text: 'Kartu Presensi Digital SMKN 1 Air Naningan - {{ $siswa->nama }}'
                  });
                  return;
                }
              }
              triggerSiswaDownload(canvas, safeFilename);
            }, 'image/png');
            return;
          } catch(e) {
            triggerSiswaDownload(canvas, safeFilename);
            return;
          }
        }

        triggerSiswaDownload(canvas, safeFilename);
      } catch (err) {
        console.error('Gagal render kartu siswa:', err);
        // Fallback
        const containerScanner = document.getElementById('scannerQrContainer');
        const qrCanvasOrImg = containerScanner?.querySelector('canvas') || containerScanner?.querySelector('img');
        if (qrCanvasOrImg) {
          const link = document.createElement('a');
          link.download = 'QR_Presensi_{{ Str::slug($siswa->nama) }}_{{ $codeValue }}.png';
          link.href = (qrCanvasOrImg.tagName === 'CANVAS') ? qrCanvasOrImg.toDataURL('image/png') : qrCanvasOrImg.src;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          alert('Gagal mengunduh kartu. Silakan muat ulang halaman.');
        }
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
      }
    }

    function triggerSiswaDownload(canvas, filename) {
      const link = document.createElement('a');
      link.download = filename;
      link.href = canvas.toDataURL('image/png');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    window.downloadSiswaFullCard = downloadSiswaFullCard;
    window.downloadQrOnly = downloadSiswaFullCard;

    window.renderPortalQrCode = renderPortalQrCode;

    function openKartuDigital() {
      const modal = document.getElementById('kartuDigitalModal');
      if (!modal) return;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      if (!kartuQrRendered) {
        renderKartuQr();
        kartuQrRendered = true;
      }
      // Animasi masuk
      const inner = modal.querySelector('[onclick="event.stopPropagation()"]');
      if (inner) {
        inner.style.opacity = '0';
        inner.style.transform = 'translateY(20px) scale(0.96)';
        requestAnimationFrame(() => {
          inner.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          inner.style.opacity = '1';
          inner.style.transform = 'translateY(0) scale(1)';
        });
      }
    }

    function closeKartuDigital(e) {
      if (e && e.target && !e.target.closest('[onclick="event.stopPropagation()"]') === false) return;
      const modal = document.getElementById('kartuDigitalModal');
      if (!modal) return;
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    let kartuQrRendered = false;
    function renderKartuQr() {
      const codeVal = KARTU_CODE_VALUE || '{{ $siswa->nisn ?? $siswa->nis }}';
      if (!codeVal) return;

      // Render QR Code di Modal Kartu Digital
      const qrContainer = document.getElementById('kartuQrContainer');
      if (qrContainer && typeof QRCode !== 'undefined') {
        try {
          qrContainer.innerHTML = '';
          new QRCode(qrContainer, {
            text: codeVal,
            width: 120,
            height: 120,
            colorDark: '#0f172a',
            colorLight: '#ffffff',
            correctLevel: (typeof QRCode.CorrectLevel !== 'undefined') ? QRCode.CorrectLevel.M : 0
          });
        } catch(e) {
          console.warn('QR render error:', e);
        }
      }
    }

    async function downloadKartuDigital() {
      const card = document.getElementById('kartuDigitalCard');
      if (!card) return;
      try {
        if (typeof html2canvas !== 'undefined') {
          const canvas = await html2canvas(card, { scale: 3, useCORS: true, backgroundColor: null });
          const link = document.createElement('a');
          link.download = 'kartu-pelajar-{{ Str::slug($siswa->nama) }}.png';
          link.href = canvas.toDataURL('image/png');
          link.click();
        } else {
          const printContent = card.outerHTML;
          const printWin = window.open('', '_blank', 'width=400,height=600');
          printWin.document.write(`<html><head><title>Kartu Pelajar Digital</title><style>body{margin:0;padding:16px;background:#1e293b;display:flex;justify-content:center;align-items:center;min-height:100vh;font-family:system-ui,sans-serif;}</style></head><body>${printContent}</body></html>`);
          printWin.document.close();
          printWin.focus();
          setTimeout(() => { printWin.print(); printWin.close(); }, 500);
        }
      } catch(e) {
        alert('Fitur unduh memerlukan koneksi internet untuk library tambahan. Coba gunakan Screenshot layar.');
      }
    }

    async function shareKartuDigital() {
      const card = document.getElementById('kartuDigitalCard');
      if (!card) return;
      try {
        if (navigator.share) {
          const shareUrl = window.location.href;
          await navigator.share({
            title: 'Kartu Pelajar Digital - {{ $siswa->nama }}',
            text: 'Lihat kartu pelajar digital {{ $siswa->nama }} ({{ $siswa->nis }}) di portal SIRANI SMKN 1 Air Naningan.',
            url: shareUrl
          });
        } else {
          const url = window.location.href;
          navigator.clipboard.writeText(url).then(() => {
            alert('Link portal berhasil disalin ke clipboard!');
          });
        }
      } catch(e) {
        console.warn('Share error:', e);
      }
    }

    window.openKartuDigital = openKartuDigital;
    window.closeKartuDigital = closeKartuDigital;
    window.downloadKartuDigital = downloadKartuDigital;
    window.shareKartuDigital = shareKartuDigital;
    @endif

    document.addEventListener('DOMContentLoaded', function() {
      @if($siswa)
        // Render QR Code & Barcode pada kartu scanner gerbang & kiosk
        if (typeof renderPortalQrCode === 'function') {
          renderPortalQrCode();
        }

        // Cek URL params / hash untuk navigasi tab
        const urlParams = new URLSearchParams(window.location.search);
        const hash = window.location.hash;

        if (hash === '#riwayat-kehadiran' || hash === '#absen' || urlParams.has('periode') || urlParams.has('tanggal') || urlParams.has('bulan') || urlParams.has('tahun')) {
          switchPortalMainTab('absen');
        } else if (hash === '#disiplin' || hash === '#catatan-kedisiplinan' || hash === '#portofolio-karakter' || hash === '#section-disiplin-wrap') {
          switchPortalMainTab('disiplin');
        } else if (hash === '#section-pengumuman' || hash === '#pengumuman') {
          switchPortalMainTab('pengumuman');
        } else if (hash === '#kartu-qr' || hash === '#kartu') {
          switchPortalMainTab('kartu-qr');
        } else {
          switchPortalMainTab('absen');
        }

        // Simpan NISN ke localStorage untuk Auto-Login jika tidak dinonaktifkan
        if (localStorage.getItem('sirani_remember_nisn_disabled') !== 'true') {
          localStorage.setItem('sirani_saved_nisn', "{{ $siswa->nisn }}");
        }

        saveSiswaToLocalStorage({
          nisn: "{{ $siswa->nisn }}",
          nis: "{{ $siswa->nis }}",
          nama: "{{ $siswa->nama }}",
          rombel: "{{ $rombel->nama_rombel ?? '' }}",
          foto: "{{ $siswa->foto ? asset('storage/'.$siswa->foto) : '' }}"
        });
      @else
        @if($keyword)
          // Jika keyword dicari tapi tidak ada siswa, bersihkan memori NISN
          localStorage.removeItem('sirani_saved_nisn');
        @else
          // Auto-Login / Auto-Load profil siswa yang tersimpan
          const urlParams = new URLSearchParams(window.location.search);
          const isReset = urlParams.has('reset') || urlParams.has('logout');
          const savedNisn = localStorage.getItem('sirani_saved_nisn');

          if (!isReset && savedNisn && savedNisn.trim() !== '') {
            document.body.style.opacity = '0.5';
            window.location.replace('{{ route("portal.ortu.index") }}?keyword=' + encodeURIComponent(savedNisn.trim()));
            return;
          }

          // Inisialisasi checkbox ingat NISN
          const remCb = document.getElementById('rememberNisnCheckbox');
          if (remCb) {
            if (localStorage.getItem('sirani_remember_nisn_disabled') === 'true') {
              remCb.checked = false;
            }
            remCb.addEventListener('change', function() {
              if (this.checked) {
                localStorage.removeItem('sirani_remember_nisn_disabled');
              } else {
                localStorage.setItem('sirani_remember_nisn_disabled', 'true');
                localStorage.removeItem('sirani_saved_nisn');
              }
            });
          }

          const searchForm = document.querySelector('.search-form-box form');
          if (searchForm) {
            searchForm.addEventListener('submit', function() {
              if (remCb && !remCb.checked) {
                localStorage.setItem('sirani_remember_nisn_disabled', 'true');
                localStorage.removeItem('sirani_saved_nisn');
              } else {
                localStorage.removeItem('sirani_remember_nisn_disabled');
              }
            });
          }

          renderSavedStudents();
        @endif
      @endif
    });

    function logoutSavedStudent() {
      if (confirm('Keluar dari profil siswa ini dan kembali ke menu pencarian NISN?')) {
        localStorage.removeItem('sirani_saved_nisn');
        window.location.href = '{{ route("portal.ortu.index") }}?reset=1';
      }
    }

    window.addEventListener('load', function() {
      @if($siswa)
        if (typeof renderPortalQrCode === 'function') {
          renderPortalQrCode();
        }
      @endif
    });

    // Auto-Remember / LocalStorage Management
    function saveSiswaToLocalStorage(siswaData) {
      const idVal = siswaData ? (siswaData.nisn || siswaData.nis) : null;
      if (!idVal) return;
      let saved = [];
      try {
        saved = JSON.parse(localStorage.getItem('sirani_saved_students') || '[]');
      } catch (e) { saved = []; }

      saved = saved.filter(function(s) { return (s.nisn || s.nis) !== idVal; });
      saved.unshift(siswaData);
      if (saved.length > 5) saved = saved.slice(0, 5);
      localStorage.setItem('sirani_saved_students', JSON.stringify(saved));
    }

    function renderSavedStudents() {
      const container = document.getElementById('savedStudentsContainer');
      if (!container) return;

      let saved = [];
      try {
        saved = JSON.parse(localStorage.getItem('sirani_saved_students') || '[]');
      } catch (e) { saved = []; }

      if (saved.length === 0) {
        container.style.display = 'none';
        return;
      }

      let html = '<div style="background:var(--bg-subtle); border:1px solid var(--border); border-radius:var(--r-md); padding:12px 14px; margin-bottom:18px;">';
      html += '<div style="font-size:11.5px; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center;">';
      html += '<span><i class="bi bi-bookmark-check-fill" style="color:var(--text); margin-right:4px;"></i> Profil Siswa Tersimpan di HP Ini</span>';
      html += '<button type="button" onclick="clearSavedStudents()" style="background:none; border:none; color:var(--text-3); font-size:11px; font-weight:700; cursor:pointer;"><i class="bi bi-trash"></i> Hapus</button>';
      html += '</div>';

      html += '<div style="display:flex; flex-direction:column; gap:6px;">';
      saved.forEach(function(s) {
        const idVal = s.nisn || s.nis;
        html += '<div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--r-sm); padding:8px 12px; display:flex; justify-content:space-between; align-items:center; gap:8px;">';
        html += '<div style="display:flex; align-items:center; gap:10px; min-width:0;">';
        html += '<div style="width:30px; height:40px; aspect-ratio:3/4; border-radius:6px; background:var(--bg-subtle); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:var(--text); flex-shrink:0; overflow:hidden;">';
        if (s.foto) {
          html += '<img src="' + s.foto + '" style="width:100%; height:100%; aspect-ratio:3/4; object-fit:cover; object-position:center 20%;">';
        } else {
          html += s.nama.charAt(0).toUpperCase();
        }
        html += '</div>';
        html += '<div style="min-width:0;">';
        html += '<strong style="color:var(--text); font-size:12.5px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + s.nama + '</strong>';
        html += '<span style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NISN: ' + idVal + (s.rombel ? ' · ' + s.rombel : '') + '</span>';
        html += '</div>';
        html += '</div>';
        html += '<a href="/cek-presensi?keyword=' + idVal + '" class="btn-search" style="padding:4px 12px; height:30px; font-size:11.5px; text-decoration:none; flex-shrink:0;">Buka →</a>';
        html += '</div>';
      });
      html += '</div></div>';

      container.innerHTML = html;
      container.style.display = 'block';
    }

    function clearSavedStudents() {
      if (confirm('Hapus daftar profil siswa yang tersimpan di perangkat ini?')) {
        localStorage.removeItem('sirani_saved_students');
        renderSavedStudents();
      }
    }

    // Service Worker Registration for PWA
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(reg) {
          console.log('SIRANI PWA ServiceWorker ready:', reg.scope);
          // Cek status notifikasi setelah SW ready
          sirani_checkNotifStatus(reg);
        }).catch(function(err) {
          console.log('SIRANI PWA ServiceWorker error:', err);
        });
      });
    }

    // ════════════════════════════════════════════════════════════
    // PUSH NOTIFICATION — SIRANI PORTAL ORANG TUA
    // ════════════════════════════════════════════════════════════
    const SIRANI_PUSH_KEY_URL  = '/api/push-vapid-key';
    const SIRANI_SUB_URL       = '/api/push-subscribe';
    const SIRANI_UNSUB_URL     = '/api/push-unsubscribe';

    /**
     * Ambil NISN dari URL atau localStorage
     */
    function sirani_getNisnAktif() {
      const params = new URLSearchParams(window.location.search);
      return params.get('keyword') || params.get('nisn') || localStorage.getItem('sirani_last_nisn') || '';
    }

    /**
     * Kirim atau perbarui langganan perangkat ke server database
     */
    async function sirani_syncSubscriptionToServer(sub, nisn) {
      if (!sub) return false;
      try {
        const subJson = sub.toJSON();
        const activeNisn = nisn || sirani_getNisnAktif() || null;
        await fetch(SIRANI_SUB_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({
            endpoint : subJson.endpoint,
            p256dh   : subJson.keys?.p256dh,
            auth     : subJson.keys?.auth,
            nisn     : activeNisn,
          }),
        });
        console.log('SIRANI Push Subscription berhasil disinkronkan ke server untuk notifikasi background:', activeNisn);
        return true;
      } catch (err) {
        console.warn('Gagal sinkron push subscription ke server:', err);
        return false;
      }
    }

    /**
     * Periksa status notifikasi & auto-sinkron ke server agar notifikasi background selalu masuk
     */
    function sirani_checkNotifStatus(swReg) {
      if (!('PushManager' in window) || !('Notification' in window)) return;
      const nisn = sirani_getNisnAktif();

      const bannerNotif      = document.getElementById('siraniBannerNotif');
      const bannerNotifAktif = document.getElementById('siraniBannerNotifAktif');

      if (Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(async function(reg) {
          try {
            let sub = await reg.pushManager.getSubscription();

            // Jika permission sudah diizinkan tapi belum ada token, auto-subscribe!
            if (!sub) {
              const keyResp = await fetch(SIRANI_PUSH_KEY_URL);
              const keyData = await keyResp.json();
              if (keyData.publicKey) {
                const applicationServerKey = sirani_urlBase64ToUint8Array(keyData.publicKey);
                sub = await reg.pushManager.subscribe({
                  userVisibleOnly: true,
                  applicationServerKey: applicationServerKey,
                });
              }
            }

            // Selalu daftarkan/perbarui endpoint ke database server agar notifikasi background masuk
            if (sub) {
              await sirani_syncSubscriptionToServer(sub, nisn);

              if (bannerNotifAktif) bannerNotifAktif.style.display = 'flex';
              if (bannerNotif)      bannerNotif.style.display      = 'none';
            }
          } catch(err) {
            console.warn('Auto sync push subscription error:', err);
          }
        });
      } else if (Notification.permission === 'default') {
        // Belum pernah ditanya — tampilkan banner ajakan aktifkan
        if (nisn && !localStorage.getItem('sirani_push_dismissed_' + nisn)) {
          setTimeout(function() {
            if (bannerNotif) bannerNotif.style.display = 'flex';
          }, 1500);
        }
      }
    }

    /**
     * Minta izin notifikasi dan daftarkan perangkat
     */
    async function sirani_requestPushPermission() {
      const btn = document.getElementById('btnAktifkanNotif');
      if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...'; }

      try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
          alert('Izin notifikasi belum diizinkan. Silakan aktifkan izin notifikasi pada bilah alamat / setelan HP Anda.');
          if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-bell-fill"></i> Izinkan di HP Ini'; }
          return;
        }

        // Ambil VAPID Public Key dari server
        const keyResp = await fetch(SIRANI_PUSH_KEY_URL);
        const keyData = await keyResp.json();
        const vapidPublicKey = keyData.publicKey;

        // Convert base64 URL ke Uint8Array
        const applicationServerKey = sirani_urlBase64ToUint8Array(vapidPublicKey);

        const swReg = await navigator.serviceWorker.ready;
        const subscription = await swReg.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: applicationServerKey,
        });

        const nisn = sirani_getNisnAktif();
        await sirani_syncSubscriptionToServer(subscription, nisn);

        // Update UI
        const bannerNotif      = document.getElementById('siraniBannerNotif');
        const bannerNotifAktif = document.getElementById('siraniBannerNotifAktif');
        if (bannerNotif)      bannerNotif.style.display      = 'none';
        if (bannerNotifAktif) bannerNotifAktif.style.display = 'flex';

        // Hapus flag dismiss
        if (nisn) localStorage.removeItem('sirani_push_dismissed_' + nisn);

        alert('Notifikasi Berhasil Diaktifkan!\n\nHP Anda sekarang siap menerima pemberitahuan kehadiran siswa dan pengumuman sekolah langsung di bilah notifikasi.');

      } catch (err) {
        console.error('Gagal subscribe push:', err);
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-bell-fill"></i> Izinkan di HP Ini'; }
        alert('Gagal mengaktifkan notifikasi: ' + err.message);
      }
    }

    /**
     * Cabut langganan notifikasi
     */
    async function sirani_unsubscribePush() {
      try {
        const swReg = await navigator.serviceWorker.ready;
        const sub   = await swReg.pushManager.getSubscription();
        if (sub) {
          const endpoint = sub.endpoint;
          await sub.unsubscribe();
          // Beri tahu server
          await fetch(SIRANI_UNSUB_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ endpoint }),
          });
        }
        const bannerNotif      = document.getElementById('siraniBannerNotif');
        const bannerNotifAktif = document.getElementById('siraniBannerNotifAktif');
        if (bannerNotifAktif) bannerNotifAktif.style.display = 'none';
        if (bannerNotif)      bannerNotif.style.display      = 'flex';
        const nisn = sirani_getNisnAktif();
        if (nisn) localStorage.setItem('sirani_push_dismissed_' + nisn, '1');
      } catch (err) {
        console.error('Gagal unsubscribe push:', err);
      }
    }

    /**
     * Helper: Decode base64url ke Uint8Array untuk VAPID
     */
    function sirani_urlBase64ToUint8Array(base64String) {
      const padding   = '='.repeat((4 - base64String.length % 4) % 4);
      const base64    = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
      const rawData   = window.atob(base64);
      const outputArr = new Uint8Array(rawData.length);
      for (let i = 0; i < rawData.length; ++i) {
        outputArr[i] = rawData.charCodeAt(i);
      }
      return outputArr;
    }


    window.addEventListener('beforeinstallprompt', function(e) {
      e.preventDefault();
      deferredPrompt = e;
      const btn = document.getElementById('btnPwaInstall');
      if (btn) btn.style.display = 'inline-flex';
    });

    function openApkInstallModal() {
      const modal = document.getElementById('modalApkInstall');
      if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
    }

    function closeApkInstallModal() {
      const modal = document.getElementById('modalApkInstall');
      if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
      }
    }

    function handleApkModalOverlayClick(e) {
      if (e.target && e.target.id === 'modalApkInstall') {
        closeApkInstallModal();
      }
    }

    function triggerPwaInstall() {
      openApkInstallModal();
    }

    function triggerPwaInstallFromModal() {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function(choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            closeApkInstallModal();
            const btn = document.getElementById('btnPwaInstall');
            if (btn) btn.style.display = 'none';
          }
          deferredPrompt = null;
        });
      } else {
        const guide = document.getElementById('pwaManualGuide');
        if (guide) {
          guide.style.display = 'block';
          guide.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      }
    }

    function handleDownloadApkFile() {
      fetch('/api/check-apk')
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data && data.exists) {
            window.location.href = '/download-apk';
          } else {
            const notice = document.getElementById('apkNoticeBox');
            if (notice) {
              notice.style.display = 'block';
              notice.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          }
        })
        .catch(function() {
          window.location.href = '/download-apk';
        });
    }

    // Auto-open modal jika URL membawa param ?open_install=1
    document.addEventListener('DOMContentLoaded', function() {
      try {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('open_install') === '1') {
          openApkInstallModal();
          if (urlParams.get('apk_missing') === '1') {
            const notice = document.getElementById('apkNoticeBox');
            if (notice) notice.style.display = 'block';
          }
        }
      } catch(e) {}
    });

    // Image Zoom Lightbox Controller
    let currentZoomScale = 1;
    function openImageZoom(imgUrl, title) {
      const modal = document.getElementById('imageZoomModal');
      const modalImg = document.getElementById('zoomModalImg');
      const modalTitle = document.getElementById('zoomModalTitle');
      const downloadLink = document.getElementById('zoomDownloadLink');

      if (!modal || !modalImg) return;

      modalImg.src = imgUrl;
      if (modalTitle) modalTitle.innerText = title || 'Pengumuman Sekolah';
      if (downloadLink) downloadLink.href = imgUrl;

      currentZoomScale = 1;
      applyZoomScale();

      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeImageZoom() {
      const modal = document.getElementById('imageZoomModal');
      if (modal) modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    function adjustZoom(delta) {
      currentZoomScale = Math.min(Math.max(0.6, currentZoomScale + delta), 3.5);
      applyZoomScale();
    }

    function resetZoom() {
      currentZoomScale = 1;
      applyZoomScale();
    }

    function toggleDoubleZoom(e) {
      e.stopPropagation();
      if (currentZoomScale > 1.2) {
        currentZoomScale = 1;
      } else {
        currentZoomScale = 2;
      }
      applyZoomScale();
    }

    function applyZoomScale() {
      const img = document.getElementById('zoomModalImg');
      const text = document.getElementById('zoomLevelText');
      if (img) {
        img.style.transform = 'scale(' + currentZoomScale + ')';
        img.style.cursor = currentZoomScale > 1.2 ? 'zoom-out' : 'zoom-in';
      }
      if (text) {
        text.innerText = Math.round(currentZoomScale * 100) + '%';
      }
    }

    function handleModalBackdropClick(e) {
      if (e.target.id === 'imageZoomModal' || e.target.id === 'zoomViewport') {
        closeImageZoom();
      }
    }

    window.openImageZoom = openImageZoom;
    window.closeImageZoom = closeImageZoom;
    window.adjustZoom = adjustZoom;
    window.resetZoom = resetZoom;
    window.toggleDoubleZoom = toggleDoubleZoom;
    window.handleModalBackdropClick = handleModalBackdropClick;

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeImageZoom();
        closeQrScanner();
      }
    });
  </script>

  {{-- LIGHTBOX IMAGE ZOOM MODAL --}}
  <div id="imageZoomModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.94); z-index:99999; flex-direction:column; justify-content:space-between; user-select:none; backdrop-filter:blur(6px);" onclick="handleModalBackdropClick(event)">
    {{-- Top Controls Bar --}}
    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(15,23,42,0.85); border-bottom:1px solid rgba(255,255,255,0.12); color:#fff; z-index:10;">
      <div style="font-weight:800; font-size:13px; max-width:55%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" id="zoomModalTitle">
        Pengumuman Sekolah
      </div>
      <div style="display:flex; align-items:center; gap:6px;">
        <button type="button" onclick="adjustZoom(-0.3)" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; cursor:pointer;" title="Perkecil">
          <i class="bi bi-zoom-out"></i>
        </button>
        <button type="button" onclick="resetZoom()" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 8px; border-radius:6px; font-size:11px; font-weight:800; cursor:pointer; min-width:44px;" title="Reset Ukuran" id="zoomLevelText">
          100%
        </button>
        <button type="button" onclick="adjustZoom(0.3)" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; cursor:pointer;" title="Perbesar">
          <i class="bi bi-zoom-in"></i>
        </button>
        <a id="zoomDownloadLink" href="#" target="_blank" style="background:rgba(255,255,255,0.15); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center;" title="Buka Gambar Asli">
          <i class="bi bi-box-arrow-up-right"></i>
        </a>
        <button type="button" onclick="closeImageZoom()" style="background:rgba(220,38,38,0.85); border:none; color:#fff; padding:6px 10px; border-radius:6px; font-size:13px; cursor:pointer; margin-left:4px;" title="Tutup">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    {{-- Image Display Area with Pan & Zoom --}}
    <div style="flex:1; display:flex; align-items:center; justify-content:center; overflow:auto; position:relative; padding:12px; cursor:grab;" id="zoomViewport">
      <img id="zoomModalImg" src="" alt="Pengumuman" style="max-width:96vw; max-height:82vh; object-fit:contain; transition:transform 0.15s ease-out; transform-origin:center center; box-shadow:0 10px 30px rgba(0,0,0,0.5); border-radius:6px;" onclick="toggleDoubleZoom(event)" />
    </div>

    {{-- Bottom Hint Bar --}}
    <div style="padding:10px 16px; background:rgba(15,23,42,0.85); text-align:center; color:rgba(255,255,255,0.75); font-size:11.5px; border-top:1px solid rgba(255,255,255,0.12);">
      <i class="bi bi-info-circle"></i> <strong>Ketuk 2x</strong> pada gambar untuk Zoom · Gunakan tombol <i class="bi bi-zoom-in"></i> / <i class="bi bi-zoom-out"></i> untuk mengatur skala
    </div>
  </div>

  @if($siswa)
  {{-- KARTU DIGITAL SISWA MODAL — REDESIGN PREMIUM --}}
  <div id="kartuDigitalModal" style="display:none; position:fixed; inset:0; z-index:99998; align-items:center; justify-content:center; padding:20px; background:rgba(2,8,23,0.92); backdrop-filter:blur(20px) saturate(1.5);" onclick="closeKartuDigital(event)">
    <div style="width:100%; max-width:400px; position:relative;" onclick="event.stopPropagation()">

      {{-- Close --}}
      <button onclick="closeKartuDigital()" style="position:absolute; top:-18px; right:-10px; z-index:10; background:rgba(255,255,255,0.1); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.2); width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; color:#fff; cursor:pointer; transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
        <i class="bi bi-x-lg"></i>
      </button>

      {{-- === KARTU UTAMA === --}}
      <div id="kartuDigitalCard" style="border-radius:24px; overflow:hidden; box-shadow:0 32px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.08); font-family:var(--font-main); position:relative;">

        {{-- ===== SISI DEPAN KARTU ===== --}}
        {{-- Background gradient utama --}}
        <div style="background:linear-gradient(135deg, #0a0f1e 0%, #0d1f4a 30%, #1a1060 60%, #2d0a4e 100%); padding:0; position:relative; min-height:220px; overflow:hidden;">

          {{-- Holographic stripe horizontal --}}
          <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899, #f59e0b, #10b981, #6366f1); background-size:200% 100%; animation:holoShift 3s linear infinite;"></div>

          {{-- Grid pattern overlay --}}
          <div style="position:absolute; inset:0; opacity:0.04; background-image:linear-gradient(rgba(255,255,255,0.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.8) 1px, transparent 1px); background-size:24px 24px;"></div>

          {{-- Glow orbs dekoratif --}}
          <div style="position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:radial-gradient(circle, rgba(139,92,246,0.25) 0%, transparent 70%);"></div>
          <div style="position:absolute; bottom:-80px; left:-40px; width:220px; height:220px; border-radius:50%; background:radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);"></div>
          <div style="position:absolute; top:50%; right:30px; width:80px; height:80px; border-radius:50%; background:radial-gradient(circle, rgba(236,72,153,0.15) 0%, transparent 70%);"></div>

          {{-- === ROW 1: Logo + Tahun === --}}
          <div style="padding:18px 20px 0; display:flex; align-items:center; justify-content:space-between; position:relative; z-index:2;">
            <div style="display:flex; align-items:center; gap:10px;">
              {{-- Logo badge --}}
              <div style="width:36px; height:36px; background:linear-gradient(135deg,rgba(139,92,246,0.4),rgba(99,102,241,0.3)); border:1px solid rgba(255,255,255,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
                <i class="bi bi-mortarboard-fill" style="color:#c4b5fd; font-size:16px;"></i>
              </div>
              <div>
                <div style="font-size:7.5px; font-weight:800; color:rgba(196,181,253,0.8); letter-spacing:2px; text-transform:uppercase; line-height:1;">KARTU PELAJAR DIGITAL</div>
                <div style="font-size:12px; font-weight:900; color:#fff; line-height:1.3; margin-top:2px;">SMK Negeri 1 Air Naningan</div>
              </div>
            </div>
            {{-- Contactless icon + TA --}}
            <div style="text-align:right;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="opacity:0.6; display:block; margin-left:auto; margin-bottom:3px;">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="rgba(139,92,246,0.3)" stroke="rgba(196,181,253,0.6)" stroke-width="1.5"/>
                <path d="M8 12c0-2.21 1.79-4 4-4s4 1.79 4 4" stroke="rgba(196,181,253,0.8)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                <path d="M6 12c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="rgba(167,139,250,0.5)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
              </svg>
              <div style="font-size:9px; font-weight:800; color:rgba(196,181,253,0.7); line-height:1;">TA {{ date('Y') }}/{{ date('Y')+1 }}</div>
              <div style="font-size:7px; color:rgba(255,255,255,0.3); margin-top:1px; letter-spacing:0.5px;">SIRANI · v2</div>
            </div>
          </div>

          {{-- === ROW 2: Foto + Info === --}}
          <div style="padding:14px 20px 20px; display:flex; gap:16px; align-items:flex-end; position:relative; z-index:2;">
            {{-- Foto --}}
            <div style="flex-shrink:0; position:relative;">
              {{-- Ring glow --}}
              <div style="position:absolute; inset:-3px; border-radius:16px; background:linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899); padding:2px; opacity:0.8;">
                <div style="width:100%; height:100%; background:#0d1f4a; border-radius:14px;"></div>
              </div>
              @if($siswa->foto)
                <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" style="position:relative; z-index:1; width:75px; height:100px; aspect-ratio:3/4; object-fit:cover; object-position:center 20%; border-radius:12px; display:block;" />
              @else
                <div style="position:relative; z-index:1; width:75px; height:100px; aspect-ratio:3/4; background:linear-gradient(135deg,#312e81,#4c1d95,#6d28d9); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:900; color:rgba(255,255,255,0.9); font-family:var(--font-main);">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</div>
              @endif
            </div>

            {{-- Info --}}
            <div style="flex:1; min-width:0; padding-bottom:4px;">
              <div style="font-size:18px; font-weight:900; color:#fff; line-height:1.15; margin-bottom:10px; letter-spacing:-0.3px;">{{ $siswa->nama }}</div>
              <div style="display:flex; flex-direction:column; gap:4px;">
                <div style="display:flex; align-items:center; gap:0;">
                  <span style="font-size:9px; color:rgba(196,181,253,0.6); font-weight:700; text-transform:uppercase; letter-spacing:0.8px; width:32px;">NIS</span>
                  <span style="font-size:11px; color:#e2e8f0; font-weight:800; font-family:var(--font-mono); letter-spacing:1px;">{{ $siswa->nis ?: '—' }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:0;">
                  <span style="font-size:9px; color:rgba(196,181,253,0.6); font-weight:700; text-transform:uppercase; letter-spacing:0.8px; width:32px;">NISN</span>
                  <span style="font-size:11px; color:#e2e8f0; font-weight:800; font-family:var(--font-mono); letter-spacing:1px;">{{ $siswa->nisn ?: '—' }}</span>
                </div>
                <div style="margin-top:4px; display:flex; gap:5px; flex-wrap:wrap;">
                  <span style="background:rgba(99,102,241,0.25); border:1px solid rgba(99,102,241,0.4); color:#c4b5fd; font-size:9.5px; font-weight:800; padding:3px 9px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; backdrop-filter:blur(4px);">
                    <i class="bi bi-building" style="font-size:8px;"></i> {{ $rombel->nama_rombel ?? '—' }}
                  </span>
                  <span style="background:rgba(236,72,153,0.2); border:1px solid rgba(236,72,153,0.35); color:#f9a8d4; font-size:9px; font-weight:700; padding:3px 9px; border-radius:20px; display:inline-flex; align-items:center; gap:4px; backdrop-filter:blur(4px);">
                    <i class="bi bi-tools" style="font-size:8px;"></i> {{ Str::words($rombel->jurusan->nama_jurusan ?? '—', 3, '…') }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {{-- Chip EMV --}}
          <div style="position:absolute; bottom:16px; right:20px; z-index:2;">
            <div style="width:32px; height:24px; background:linear-gradient(135deg,#d4a017,#f5d06b,#b8860b); border-radius:5px; position:relative; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.4);">
              <div style="position:absolute; inset:0; background:repeating-linear-gradient(0deg, transparent, transparent 6px, rgba(0,0,0,0.15) 6px, rgba(0,0,0,0.15) 7px);"></div>
              <div style="position:absolute; left:50%; top:0; bottom:0; width:1px; background:rgba(0,0,0,0.2); transform:translateX(-50%);"></div>
            </div>
          </div>
        </div>

        {{-- ===== BAGIAN BAWAH: BARCODE + QR ===== --}}
        <div style="background:#f8fafc; padding:16px 20px 14px;">
          {{-- Garis dekoratif atas --}}
          <div style="height:3px; background:linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899, #f59e0b); border-radius:2px; margin-bottom:14px; opacity:0.6;"></div>

          {{-- QR Code Section - full width --}}
          <div style="text-align:center;">
            <div style="display:flex; align-items:center; justify-content:center; gap:5px; margin-bottom:8px;">
              <i class="bi bi-qr-code" style="font-size:10px; color:#8b5cf6;"></i>
              <span style="font-size:8.5px; font-weight:800; color:#8b5cf6; text-transform:uppercase; letter-spacing:1.2px;">QR Presensi · ID: {{ $codeValue }}</span>
            </div>
            <div style="background:#fff; border-radius:10px; padding:8px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,0.06); display:inline-block;">
              <div id="kartuQrContainer" style="width:120px; height:120px; display:flex; align-items:center; justify-content:center;"></div>
            </div>
            <div style="font-size:8.5px; color:#94a3b8; text-align:center; margin-top:6px; font-family:var(--font-mono); letter-spacing:2px;">{{ $codeValue }}</div>
          </div>

          {{-- Footer --}}
          <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:5px;">
              <div style="width:5px; height:5px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#ec4899);"></div>
              <span style="font-size:8px; color:#94a3b8; font-weight:700; letter-spacing:0.3px;">SMKN 1 Air Naningan · Tanggamus · Lampung</span>
            </div>
            <span style="font-size:7.5px; color:#cbd5e1; font-family:var(--font-mono); letter-spacing:0.5px;">portal.sirani</span>
          </div>
        </div>
      </div>

      {{-- ===== TOMBOL AKSI ===== --}}
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:14px;">
        <button onclick="downloadKartuDigital()" style="background:#fff; color:#0f172a; border:none; padding:11px 16px; border-radius:14px; font-size:12px; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; font-family:var(--font-main); box-shadow:0 4px 20px rgba(0,0,0,0.4); transition:all .2s; letter-spacing:0.2px;" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.4)'">
          <i class="bi bi-download" style="font-size:13px;"></i> Unduh Kartu
        </button>
        <button onclick="shareKartuDigital()" style="background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2)); color:#e2e8f0; border:1px solid rgba(139,92,246,0.4); padding:11px 16px; border-radius:14px; font-size:12px; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; font-family:var(--font-main); transition:all .2s; backdrop-filter:blur(10px); letter-spacing:0.2px;" onmouseover="this.style.background='linear-gradient(135deg,rgba(99,102,241,0.35),rgba(139,92,246,0.35))';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2))';this.style.transform=''">
          <i class="bi bi-share" style="font-size:13px;"></i> Bagikan
        </button>
      </div>

      {{-- Label kecil --}}
      <div style="text-align:center; margin-top:10px; font-size:10px; color:rgba(255,255,255,0.25); letter-spacing:0.5px; font-family:var(--font-main);">
        Kartu ini merupakan identitas digital resmi siswa SMKN 1 Air Naningan
      </div>
    </div>
  </div>

  {{-- FULLSCREEN AUTO-ZOOM & MAX BRIGHTNESS (PURE WHITE SCREEN) OVERLAY --}}
  <div id="qrZoomOverlay" onclick="toggleQrFullscreenZoom(false)" title="Sentuh untuk minimize &amp; kembalikan kecerahan normal">
    <div class="zoom-overlay-header">
      <div style="font-size:11px; font-weight:800; color:#64748b; letter-spacing:1.5px; text-transform:uppercase;">SMK NEGERI 1 AIR NANINGAN</div>
      <div style="font-size:15px; font-weight:900; color:#0f172a; margin-top:2px; letter-spacing:0.5px;">SCANNER GERBANG &amp; KIOSK</div>
    </div>
    <div class="zoom-overlay-body">
      <div style="background:#ffffff; padding:18px 18px 12px; border-radius:24px; box-shadow:0 16px 50px rgba(0,0,0,0.12); border:2px solid #e2e8f0; display:inline-block; max-width:92vw;">
        <div id="zoomedQrContainer" style="min-width:240px; min-height:240px; display:flex; align-items:center; justify-content:center; margin:0 auto;"></div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; letter-spacing:3px; color:#0f172a; margin-top:10px; text-align:center;">{{ $codeValue }}</div>
      </div>
      <div style="margin-top:14px; text-align:center;">
        <div style="font-size:18px; font-weight:900; color:#0f172a; line-height:1.2;">{{ $siswa->nama }}</div>
        <div style="font-size:13px; font-weight:700; color:#64748b; margin-top:3px;">
          @if($siswa->status === 'lulus')
            Alumni · {{ $rombel->nama_rombel ?? 'Lulusan' }} ({{ $rombel->jurusan->nama_jurusan ?? 'Semua Jurusan' }})
          @else
            {{ $rombel->nama_rombel ?? 'X' }} · {{ $rombel->jurusan->nama_jurusan ?? 'Semua Jurusan' }}
          @endif
        </div>
      </div>
    </div>
    <div class="zoom-overlay-footer">
      <div style="display:inline-flex; align-items:center; gap:8px; background:#0f172a; color:#ffffff; padding:10px 22px; border-radius:30px; font-size:12px; font-weight:800; box-shadow:0 4px 18px rgba(0,0,0,0.25);">
        <i class="bi bi-hand-index-thumb"></i>
        <span>Sentuh layar untuk minimize &amp; kembalikan kecerahan</span>
      </div>
    </div>
  </div>

  @endif

  @if(isset($pengumumans) && $pengumumans->count() > 0)
    @php
      $latestPengumuman = $pengumumans->first();
      $badgeKategori = $latestPengumuman->kategori_badge ?? ['label' => 'Pengumuman Resmi', 'color' => '#2563eb'];
    @endphp
    {{-- MODAL POPUP PENGUMUMAN OTOMATIS SAAT BUKA APLIKASI --}}
    <div id="modalPengumumanPopup" class="sirani-modal-overlay" style="display:none; z-index: 1050;" onclick="handlePengumumanPopupOverlayClick(event)">
      <div class="sirani-modal-container" onclick="event.stopPropagation()" style="max-width: 490px; max-height: 88vh; display: flex; flex-direction: column; border-radius: 22px; overflow: hidden; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);">
        
        {{-- Modal Header --}}
        <div class="sirani-modal-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: space-between;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(59, 130, 246, 0.2); border: 1.5px solid rgba(59, 130, 246, 0.4); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #60a5fa; flex-shrink: 0; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
              <i class="bi bi-megaphone-fill"></i>
            </div>
            <div>
              <div style="font-size: 15px; font-weight: 800; color: #ffffff; line-height: 1.2;">Pengumuman Baru Sekolah</div>
              <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">SMK Negeri 1 Air Naningan</div>
            </div>
          </div>
          <button type="button" onclick="closePengumumanPopup(true)" style="background: rgba(255,255,255,0.12); border: none; width: 34px; height: 34px; border-radius: 50%; color: #ffffff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.15s;" title="Tutup">
            <i class="bi bi-x-lg" style="font-size: 13px;"></i>
          </button>
        </div>

        {{-- Modal Body (Scrollable) --}}
        <div class="sirani-modal-body" style="padding: 20px; overflow-y: auto; -webkit-overflow-scrolling: touch; max-height: calc(88vh - 150px);">
          {{-- Kategori & Tanggal Badge --}}
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
            <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 100px; background: #dbeafe; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">
              <i class="bi bi-info-circle-fill"></i> {{ $badgeKategori['label'] ?? 'PENGUMUMAN RESMI' }}
            </span>
            <span style="font-size: 11.5px; color: var(--text-3, #64748b); font-weight: 600;">
              <i class="bi bi-clock"></i> {{ $latestPengumuman->created_at->translatedFormat('d M Y') }}
            </span>
          </div>

          {{-- Judul Pengumuman --}}
          <h3 style="font-size: 17px; font-weight: 800; color: var(--text, #0f172a); margin: 0 0 12px; line-height: 1.35;">
            {{ $latestPengumuman->judul }}
          </h3>

          {{-- Banner Gambar jika ada --}}
          @if($latestPengumuman->banner_url)
            <div style="margin-bottom: 14px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border, #e2e8f0); cursor: zoom-in;" onclick="openImageZoom('{{ $latestPengumuman->banner_url }}', '{{ addslashes($latestPengumuman->judul) }}')">
              <img src="{{ $latestPengumuman->banner_url }}" alt="{{ $latestPengumuman->judul }}" style="width: 100%; max-height: 240px; object-fit: contain; background: rgba(0,0,0,0.03); display: block;" />
              <div style="background: rgba(15,23,42,0.85); color: #fff; font-size: 10.5px; font-weight: 700; padding: 4px 8px; text-align: center;">
                <i class="bi bi-arrows-fullscreen"></i> Ketuk gambar untuk memperbesar (Zoom)
              </div>
            </div>
          @endif

          {{-- Isi Pesan Pengumuman --}}
          <div style="font-size: 13.5px; color: var(--text-2, #334155); line-height: 1.6; white-space: pre-line; background: var(--bg-subtle, #f8fafc); padding: 14px 16px; border-radius: 14px; border: 1px solid var(--border, #e2e8f0);">
            {{ $latestPengumuman->isi_pesan }}
          </div>

          @if($pengumumans->count() > 1)
            <div style="margin-top: 12px; font-size: 11.5px; color: var(--text-3, #64748b); text-align: center;">
              <i class="bi bi-layers-fill" style="color: #2563eb;"></i> Terdapat total <strong>{{ $pengumumans->count() }} pengumuman aktif</strong> di portal saat ini.
            </div>
          @endif
        </div>

        {{-- Modal Footer Actions --}}
        <div style="padding: 14px 20px; background: var(--bg-subtle, #f8fafc); border-top: 1px solid var(--border, #e2e8f0); display: flex; flex-direction: column; gap: 8px;">
          <button type="button" onclick="closePengumumanPopup(true)" style="width: 100%; padding: 12px 18px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; border: none; border-radius: 12px; font-size: 13.5px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(37,99,235,0.3);">
            <i class="bi bi-check-circle-fill"></i> Sudah Dibaca &amp; Paham
          </button>
          @if($pengumumans->count() > 1)
            <button type="button" onclick="goToAllPengumumanFromPopup()" style="width: 100%; padding: 9px 16px; background: #ffffff; border: 1.5px solid var(--border, #cbd5e1); color: var(--text, #334155); border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
              <i class="bi bi-megaphone"></i> Buka Daftar Semua Pengumuman ({{ $pengumumans->count() }})
            </button>
          @endif
        </div>
      </div>
    </div>
  @endif

  {{-- MODAL INSTAL APLIKASI & DOWNLOAD APK SIRANI --}}
  <div id="modalApkInstall" class="sirani-modal-overlay" style="display:none;" onclick="handleApkModalOverlayClick(event)">
    <div class="sirani-modal-container" onclick="event.stopPropagation()">
      <div class="sirani-modal-header">
        <div class="sirani-modal-app-badge">
          <img src="/icons/icon-192.png" alt="SIRANI Logo" class="sirani-modal-logo">
          <div>
            <div class="sirani-modal-title">SIRANI Mobile</div>
            <div class="sirani-modal-sub">SMK Negeri 1 Air Naningan</div>
          </div>
        </div>
        <button type="button" onclick="closeApkInstallModal()" class="sirani-modal-close" title="Tutup">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="sirani-modal-body">
        {{-- Info Card --}}
        <div class="sirani-app-info-card">
          <div class="sirani-app-info-row">
            <span class="info-label"><i class="bi bi-phone"></i> Nama Aplikasi:</span>
            <span class="info-val">SIRANI</span>
          </div>
          <div class="sirani-app-info-row">
            <span class="info-label"><i class="bi bi-shield-check" style="color:#22c55e;"></i> Ikon di HP:</span>
            <span class="info-val">Logo SMKN 1 Air Naningan</span>
          </div>
          <div class="sirani-app-info-row">
            <span class="info-label"><i class="bi bi-bell-fill" style="color:#3b82f6;"></i> Notifikasi:</span>
            <span class="info-val">Otomatis saat Masuk/Pulang</span>
          </div>
        </div>

        {{-- Opsi 1: Pasang Langsung ke Layar HP (Utama) --}}
        <div class="sirani-install-option active-opt">
          <div class="option-header">
            <div class="option-tag">Rekomendasi Utama · 1 Klik</div>
            <h5>Pasang Langsung ke Layar HP</h5>
            <p>Aplikasi otomatis terpasang ke layar utama HP Android / iPhone Anda tanpa perlu download file besar.</p>
          </div>
          <button type="button" id="btnActionPwaInstall" onclick="triggerPwaInstallFromModal()" class="btn-install-primary">
            <i class="bi bi-download"></i>
            <span>Pasang Sekarang (Instan)</span>
          </button>
        </div>

        {{-- Panduan Manual jika prompt otomatis tidak muncul --}}
        <div id="pwaManualGuide" class="pwa-manual-guide" style="display:none;">
          <div class="guide-title"><i class="bi bi-info-circle-fill" style="color:#3b82f6;"></i> Langkah Pasang Manual di Chrome:</div>
          <div class="guide-steps">
            <div class="guide-step">
              <span class="step-num">1</span>
              <span>Ketuk ikon menu titik tiga (<i class="bi bi-three-dots-vertical"></i>) di pojok kanan atas browser Google Chrome Anda.</span>
            </div>
            <div class="guide-step">
              <span class="step-num">2</span>
              <span>Pilih menu <strong>"Instal aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</span>
            </div>
            <div class="guide-step">
              <span class="step-num">3</span>
              <span>Selesai! Ikon <strong>SIRANI</strong> akan langsung muncul di layar utama HP Anda dengan logo resmi sekolah.</span>
            </div>
          </div>
        </div>

        {{-- Opsi 2: Download File .APK --}}
        <div class="sirani-install-option">
          <div class="option-header">
            <div class="option-tag secondary">Paket File Offline (.APK)</div>
            <h5>Download Paket APK Android</h5>
            <p>Untuk perangkat Android yang membutuhkan file paket installer offline langsung.</p>
          </div>
          <button type="button" onclick="handleDownloadApkFile()" class="btn-install-secondary">
            <i class="bi bi-android2"></i>
            <span>Unduh File .APK</span>
          </button>
          <div id="apkNoticeBox" style="display:none; margin-top:10px; padding:10px 12px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.25); border-radius:10px; font-size:12px; line-height:1.5; color:#1e40af;">
            <i class="bi bi-info-circle-fill" style="color:#2563eb;"></i>
            <strong>Info File APK:</strong> File installer offline (.apk) sedang disiapkan oleh tim IT. Untuk saat ini, silakan gunakan tombol <strong>"Pasang Sekarang (Instan)"</strong> di atas — fitur, icon SIRANI, dan performanya persis sama seperti aplikasi yang dipasang dari APK!
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════════════
       MODAL PENGATURAN SUARA NOTIFIKASI & WAJIB AKTIF
  ══════════════════════════════════════════════════════════ --}}
  <div id="modalNotifSettings" class="sirani-modal-overlay" style="display:none;" onclick="handleNotifSettingsOverlayClick(event)">
    <div class="sirani-modal-container" onclick="event.stopPropagation()" style="max-width: 480px; max-height: 90vh; display: flex; flex-direction: column;">
      
      {{-- Modal Header --}}
      <div class="sirani-modal-header" style="flex-shrink: 0;">
        <div class="sirani-modal-app-badge">
          <div style="width: 42px; height: 42px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 4px 10px rgba(37,99,235,0.15);">
            <i class="bi bi-bell-fill"></i>
          </div>
          <div>
            <div class="sirani-modal-title">Suara &amp; Notifikasi</div>
            <div class="sirani-modal-sub">SIRANI Mobile · SMKN 1 Air Naningan</div>
          </div>
        </div>
        <button type="button" onclick="closeNotifSettingsModal()" class="sirani-modal-close" title="Tutup">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      {{-- Modal Body (Scrollable) --}}
      <div class="sirani-modal-body" style="overflow-y: auto; padding: 20px;">
        
        {{-- Status Box: Selalu Aktif & Wajib Sekolah --}}
        <div class="sirani-notif-status-box">
          <div class="notif-status-left">
            <div class="notif-status-pulse-icon">
              <i class="bi bi-bell-fill"></i>
            </div>
            <div>
              <div class="notif-status-title-row">
                <span class="notif-status-title">Status: Selalu Aktif</span>
                <span class="notif-lock-badge"><i class="bi bi-shield-lock-fill"></i> Wajib Sekolah</span>
              </div>
              <div class="notif-status-desc">
                Notifikasi presensi disetel menyala secara permanen agar Anda menerima info kehadiran ananda tepat waktu.
              </div>
            </div>
          </div>
          <div class="notif-locked-toggle" title="Notifikasi wajib aktif dan tidak dapat dimatikan">
            <div class="toggle-switch-locked"></div>
            <span class="toggle-locked-label"><i class="bi bi-lock-fill"></i> Selalu ON</span>
          </div>
        </div>

        {{-- Section: Pilihan Nada Notifikasi --}}
        <div class="sirani-sound-section-title">
          <i class="bi bi-music-note-beamed" style="color: #2563eb;"></i>
          <span>Pilih Nada Notifikasi Suara</span>
        </div>
        <div class="sirani-sound-section-desc">
          Pilih nada dering yang ingin Anda dengar setiap kali ananda hadir atau pulang sekolah:
        </div>

        <div class="sirani-sound-list" id="soundChoicesList">
          {{-- Sound 1: Chime Klasik --}}
          <div class="sirani-sound-item selected" id="soundCard_chime" onclick="sirani_selectSound('chime')">
            <div class="sound-item-left">
              <div class="sound-radio-circle"></div>
              <i class="bi bi-bell sound-item-icon"></i>
              <div>
                <div class="sound-item-name">Chime Klasik (Ting Tung)</div>
                <div class="sound-item-desc">Dua nada cerah &amp; elegan (Bawaan)</div>
              </div>
            </div>
            <button type="button" class="btn-sound-preview" id="btnPlay_chime" onclick="event.stopPropagation(); sirani_playPreviewSound('chime')">
              <i class="bi bi-play-fill"></i> Tes
            </button>
          </div>

          {{-- Sound 2: Bel Sekolah --}}
          <div class="sirani-sound-item" id="soundCard_bell" onclick="sirani_selectSound('bell')">
            <div class="sound-item-left">
              <div class="sound-radio-circle"></div>
              <i class="bi bi-building sound-item-icon"></i>
              <div>
                <div class="sound-item-name">Bel Sekolah (School Bell)</div>
                <div class="sound-item-desc">Tiga nada khas lonceng sekolah</div>
              </div>
            </div>
            <button type="button" class="btn-sound-preview" id="btnPlay_bell" onclick="event.stopPropagation(); sirani_playPreviewSound('bell')">
              <i class="bi bi-play-fill"></i> Tes
            </button>
          </div>

          {{-- Sound 3: Melodi Lembut --}}
          <div class="sirani-sound-item" id="soundCard_soft" onclick="sirani_selectSound('soft')">
            <div class="sound-item-left">
              <div class="sound-radio-circle"></div>
              <i class="bi bi-soundwave sound-item-icon"></i>
              <div>
                <div class="sound-item-name">Melodi Lembut (Gentle Harp)</div>
                <div class="sound-item-desc">Nada lembut &amp; ramah telinga</div>
              </div>
            </div>
            <button type="button" class="btn-sound-preview" id="btnPlay_soft" onclick="event.stopPropagation(); sirani_playPreviewSound('soft')">
              <i class="bi bi-play-fill"></i> Tes
            </button>
          </div>

          {{-- Sound 4: Alert Modern --}}
          <div class="sirani-sound-item" id="soundCard_alert" onclick="sirani_selectSound('alert')">
            <div class="sound-item-left">
              <div class="sound-radio-circle"></div>
              <i class="bi bi-lightning-charge sound-item-icon"></i>
              <div>
                <div class="sound-item-name">Alert Modern (Double Beep)</div>
                <div class="sound-item-desc">Nada digital singkat, tegas &amp; jelas</div>
              </div>
            </div>
            <button type="button" class="btn-sound-preview" id="btnPlay_alert" onclick="event.stopPropagation(); sirani_playPreviewSound('alert')">
              <i class="bi bi-play-fill"></i> Tes
            </button>
          </div>

          {{-- Sound 5: Custom Sound File --}}
          <div class="sirani-sound-item" id="soundCard_custom" onclick="sirani_selectSound('custom')">
            <div class="sound-item-left">
              <div class="sound-radio-circle"></div>
              <i class="bi bi-folder2-open sound-item-icon" style="color: #9333ea;"></i>
              <div>
                <div class="sound-item-name">Pilih File Suara Sendiri (MP3 / Audio)</div>
                <div class="sound-item-desc" id="customSoundDesc">Pilih file audio apapun dari memori HP Anda</div>
              </div>
            </div>
            <button type="button" class="btn-sound-preview" id="btnPlay_custom" style="display:none;" onclick="event.stopPropagation(); sirani_playPreviewSound('custom')">
              <i class="bi bi-play-fill"></i> Tes
            </button>
          </div>
        </div>

        {{-- Uploader Box for Custom Sound --}}
        <div id="customSoundUploaderBox" class="sirani-custom-sound-box" style="display:none;">
          <div class="custom-sound-header">
            <span class="custom-sound-title"><i class="bi bi-upload"></i> Unggah Audio dari HP</span>
            <span id="customSoundSize" style="font-size: 11px; color:#9333ea;">Maks 5 MB</span>
          </div>
          <div class="custom-sound-info">
            Pilih file audio (MP3, WAV, M4A, OGG) dari penyimpanan HP Anda untuk dijadikan nada notifikasi SIRANI.
          </div>
          <div class="custom-sound-actions">
            <input type="file" id="inputCustomAudioFile" accept="audio/*,.mp3,.wav,.ogg,.m4a,.aac" style="display:none;" onchange="sirani_handleCustomSoundUpload(event)">
            <button type="button" class="btn-upload-sound" onclick="document.getElementById('inputCustomAudioFile').click()">
              <i class="bi bi-folder-symlink-fill"></i> Cari File di HP
            </button>
            <div id="customSoundFileBadge" style="display:none;" class="custom-sound-filename"></div>
            <button type="button" id="btnResetCustomSound" style="display:none; background:none; border:none; color:#ef4444; font-size:11px; font-weight:700; cursor:pointer;" onclick="sirani_removeCustomSound()">
              <i class="bi bi-trash"></i> Hapus
            </button>
          </div>
        </div>

        {{-- Volume Slider --}}
        <div class="sirani-volume-box" style="margin-top: 14px;">
          <div class="volume-header">
            <span class="volume-label"><i class="bi bi-volume-up-fill" style="color: #2563eb;"></i> Volume Notifikasi:</span>
            <span class="volume-val-badge" id="volumeValBadge">85%</span>
          </div>
          <input type="range" min="10" max="100" value="85" class="volume-slider" id="notifVolumeSlider" oninput="sirani_updateVolume(this.value)">
        </div>

        {{-- Action Buttons --}}
        <div class="sirani-notif-modal-actions">
          <button type="button" onclick="sirani_testNotification()" class="btn-test-notif-full">
            <i class="bi bi-bell-fill"></i> Tes Kirim Notifikasi &amp; Suara ke HP
          </button>
          <button type="button" onclick="sirani_saveSoundSettings()" class="btn-save-sound-setting">
            <i class="bi bi-check-lg"></i> Simpan Pilihan Suara
          </button>
        </div>

        {{-- Panduan Nada Dering Android OS --}}
        <div class="android-sound-guide-box">
          <button type="button" class="android-guide-toggle" onclick="toggleAndroidSoundGuide()">
            <span><i class="bi bi-phone"></i> Cara Mengubah Suara di Pengaturan HP Android</span>
            <i class="bi bi-chevron-down" id="guideChevron"></i>
          </button>
          <div id="androidGuideBody" class="android-guide-content" style="display:none;">
            <strong>1. Mengubah Suara &amp; Nada Dering Notifikasi:</strong>
            <ol class="android-guide-steps-list">
              <li>Buka <strong>Pengaturan (Settings) HP</strong> &gt; <strong>Aplikasi</strong>.</li>
              <li>Pilih aplikasi <strong>SIRANI</strong> (atau browser Chrome).</li>
              <li>Ketuk <strong>Pemberitahuan / Notifikasi</strong> &gt; <strong>Kategori Notifikasi</strong>.</li>
              <li>Pilih <strong>Suara / Nada Dering</strong> lalu pilih nada dering HP yang Anda sukai.</li>
            </ol>
            <div style="margin-top:10px; padding-top:10px; border-top:1px dashed #cbd5e1;">
              <strong style="color:#0f172a;"><i class="bi bi-shield-check" style="color:#22c55e;"></i> 2. Agar Selalu Masuk Real-Time Seperti WhatsApp (Aplikasi Ditutup / Layar Mati):</strong>
              <ol class="android-guide-steps-list" style="margin-top:4px;">
                <li>Di <strong>Pengaturan HP &gt; Aplikasi &gt; SIRANI</strong> (atau Chrome).</li>
                <li>Pilih <strong>Penghemat Baterai (Battery Saver)</strong> &gt; Ubah menjadi <strong>"Tidak Ada Pembatasan" (No Restrictions / Unrestricted)</strong>.</li>
                <li>Aktifkan <strong>Mulai Otomatis (Autostart)</strong> jika HP Anda bermerek Xiaomi, Oppo, atau Vivo.</li>
              </ol>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- JAVASCRIPT KONTROLER SUARA & NOTIFIKASI SIRANI -->
  <script>
    let currentPreviewAudio = null;

    const SIRANI_SOUND_MAP = {
      chime: '/sounds/notif-chime.wav',
      bell:  '/sounds/notif-bell.wav',
      soft:  '/sounds/notif-soft.wav',
      alert: '/sounds/notif-alert.wav'
    };

    function sirani_playToneFallback(type, volume) {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const gainNode = ctx.createGain();
        gainNode.gain.setValueAtTime(volume * 0.4, ctx.currentTime);
        gainNode.connect(ctx.destination);

        const now = ctx.currentTime;
        if (type === 'bell') {
          [523.25, 659.25, 783.99].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(freq, now + i * 0.18);
            osc.connect(gainNode);
            osc.start(now + i * 0.18);
            osc.stop(now + i * 0.18 + 0.35);
          });
        } else if (type === 'soft') {
          [440, 554.37, 659.25].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, now + i * 0.14);
            osc.connect(gainNode);
            osc.start(now + i * 0.14);
            osc.stop(now + i * 0.14 + 0.4);
          });
        } else if (type === 'alert') {
          [880, 880].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            osc.type = 'square';
            osc.frequency.setValueAtTime(freq, now + i * 0.12);
            osc.connect(gainNode);
            osc.start(now + i * 0.12);
            osc.stop(now + i * 0.12 + 0.08);
          });
        } else {
          // chime (default)
          [659.25, 880].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, now + i * 0.15);
            osc.connect(gainNode);
            osc.start(now + i * 0.15);
            osc.stop(now + i * 0.15 + 0.35);
          });
        }
      } catch (err) {
        console.warn('Audio tone fallback error:', err);
      }
    }

    function openNotifSettingsModal() {
      const modal = document.getElementById('modalNotifSettings');
      if (!modal) return;
      modal.style.display = 'flex';

      // Load saved preferences
      const savedSound = localStorage.getItem('sirani_sound_choice') || 'chime';
      const savedVol = localStorage.getItem('sirani_sound_volume') || '85';

      sirani_selectSound(savedSound, false);

      const slider = document.getElementById('notifVolumeSlider');
      if (slider) slider.value = savedVol;
      const volBadge = document.getElementById('volumeValBadge');
      if (volBadge) volBadge.textContent = savedVol + '%';

      // Check custom sound
      const customName = localStorage.getItem('sirani_custom_sound_name');
      const customData = localStorage.getItem('sirani_custom_sound_data');
      if (customName && customData) {
        const badge = document.getElementById('customSoundFileBadge');
        if (badge) {
          badge.textContent = '🎵 ' + customName;
          badge.style.display = 'inline-block';
        }
        const desc = document.getElementById('customSoundDesc');
        if (desc) desc.textContent = 'Audio kustom: ' + customName;
        const btnPlay = document.getElementById('btnPlay_custom');
        if (btnPlay) btnPlay.style.display = 'inline-flex';
        const btnReset = document.getElementById('btnResetCustomSound');
        if (btnReset) btnReset.style.display = 'inline-block';
      }
    }

    let currentPreviewSoundKey = null;
    let fallbackTimeout = null;

    function resetAllPreviewButtons() {
      if (fallbackTimeout) {
        clearTimeout(fallbackTimeout);
        fallbackTimeout = null;
      }
      document.querySelectorAll('.btn-sound-preview').forEach(function(btn) {
        btn.classList.remove('playing');
        btn.innerHTML = '<i class="bi bi-play-fill"></i> Tes';
      });
    }

    function closeNotifSettingsModal() {
      const modal = document.getElementById('modalNotifSettings');
      if (modal) modal.style.display = 'none';
      if (currentPreviewAudio) {
        try { currentPreviewAudio.pause(); currentPreviewAudio.currentTime = 0; } catch(e){}
        currentPreviewAudio = null;
      }
      resetAllPreviewButtons();
      currentPreviewSoundKey = null;
    }

    function handleNotifSettingsOverlayClick(event) {
      if (event.target && event.target.id === 'modalNotifSettings') {
        closeNotifSettingsModal();
      }
    }

    let activeSelectedSound = 'chime';

    function sirani_selectSound(soundKey, triggerPreview = false) {
      activeSelectedSound = soundKey;
      document.querySelectorAll('.sirani-sound-item').forEach(el => el.classList.remove('selected'));
      const activeCard = document.getElementById('soundCard_' + soundKey);
      if (activeCard) activeCard.classList.add('selected');

      const uploaderBox = document.getElementById('customSoundUploaderBox');
      if (uploaderBox) {
        uploaderBox.style.display = (soundKey === 'custom') ? 'block' : 'none';
      }

      if (triggerPreview) {
        sirani_playPreviewSound(soundKey);
      }
    }

    function sirani_updateVolume(val) {
      const badge = document.getElementById('volumeValBadge');
      if (badge) badge.textContent = val + '%';
      localStorage.setItem('sirani_sound_volume', val);
    }

    function sirani_playPreviewSound(soundKey) {
      // Jika suara yang sama sedang diputar, tombol berfungsi sebagai STOP
      if (currentPreviewSoundKey === soundKey) {
        if (currentPreviewAudio) {
          try { currentPreviewAudio.pause(); currentPreviewAudio.currentTime = 0; } catch(e){}
          currentPreviewAudio = null;
        }
        resetAllPreviewButtons();
        currentPreviewSoundKey = null;
        return;
      }

      // Hentikan suara sebelumnya bila ada
      if (currentPreviewAudio) {
        try { currentPreviewAudio.pause(); currentPreviewAudio.currentTime = 0; } catch(e){}
        currentPreviewAudio = null;
      }
      resetAllPreviewButtons();

      const activeBtn = document.getElementById('btnPlay_' + soundKey);
      if (activeBtn) {
        activeBtn.classList.add('playing');
        activeBtn.innerHTML = '<i class="bi bi-stop-fill"></i> Stop';
      }
      currentPreviewSoundKey = soundKey;

      const volVal = parseInt(document.getElementById('notifVolumeSlider')?.value || localStorage.getItem('sirani_sound_volume') || 85, 10) / 100;

      function onSoundFinish() {
        resetAllPreviewButtons();
        currentPreviewSoundKey = null;
        currentPreviewAudio = null;
      }

      if (soundKey === 'custom') {
        const customData = localStorage.getItem('sirani_custom_sound_data');
        if (!customData) {
          resetAllPreviewButtons();
          currentPreviewSoundKey = null;
          alert('Silakan pilih file audio dari HP terlebih dahulu melalui tombol "Cari File di HP".');
          document.getElementById('inputCustomAudioFile')?.click();
          return;
        }
        try {
          currentPreviewAudio = new Audio(customData);
          currentPreviewAudio.volume = volVal;
          currentPreviewAudio.onended = onSoundFinish;
          currentPreviewAudio.onerror = function() {
            sirani_playToneFallback('chime', volVal);
            fallbackTimeout = setTimeout(onSoundFinish, 800);
          };
          currentPreviewAudio.play().catch(function() {
            sirani_playToneFallback('chime', volVal);
            fallbackTimeout = setTimeout(onSoundFinish, 800);
          });
        } catch(e) {
          sirani_playToneFallback('chime', volVal);
          fallbackTimeout = setTimeout(onSoundFinish, 800);
        }
        return;
      }

      const soundUrl = SIRANI_SOUND_MAP[soundKey] || SIRANI_SOUND_MAP.chime;
      try {
        currentPreviewAudio = new Audio(soundUrl);
        currentPreviewAudio.volume = volVal;
        currentPreviewAudio.onended = onSoundFinish;
        currentPreviewAudio.onerror = function() {
          sirani_playToneFallback(soundKey, volVal);
          fallbackTimeout = setTimeout(onSoundFinish, 800);
        };
        const playPromise = currentPreviewAudio.play();
        if (playPromise !== undefined) {
          playPromise.catch(function() {
            // Audio file blocked or not found, fallback to Web Audio tone
            sirani_playToneFallback(soundKey, volVal);
            fallbackTimeout = setTimeout(onSoundFinish, 800);
          });
        }
      } catch(e) {
        sirani_playToneFallback(soundKey, volVal);
        fallbackTimeout = setTimeout(onSoundFinish, 800);
      }
    }

    function sirani_handleCustomSoundUpload(event) {
      const file = event.target.files && event.target.files[0];
      if (!file) return;

      // Maksimal 5 MB
      if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file terlalu besar! Silakan pilih file audio di bawah 5 MB.');
        return;
      }

      const reader = new FileReader();
      reader.onload = function(e) {
        const base64Data = e.target.result;
        try {
          localStorage.setItem('sirani_custom_sound_data', base64Data);
          localStorage.setItem('sirani_custom_sound_name', file.name);
          localStorage.setItem('sirani_sound_choice', 'custom');

          const badge = document.getElementById('customSoundFileBadge');
          if (badge) {
            badge.textContent = '🎵 ' + file.name;
            badge.style.display = 'inline-block';
          }
          const desc = document.getElementById('customSoundDesc');
          if (desc) desc.textContent = 'Audio kustom: ' + file.name;
          const btnPlay = document.getElementById('btnPlay_custom');
          if (btnPlay) btnPlay.style.display = 'inline-flex';
          const btnReset = document.getElementById('btnResetCustomSound');
          if (btnReset) btnReset.style.display = 'inline-block';

          sirani_selectSound('custom');
          sirani_playPreviewSound('custom');
        } catch(quotaErr) {
          alert('Penyimpanan browser penuh untuk file ini. Coba pilih file audio dengan ukuran lebih kecil (di bawah 1 MB).');
        }
      };
      reader.readAsDataURL(file);
    }

    function sirani_removeCustomSound() {
      localStorage.removeItem('sirani_custom_sound_data');
      localStorage.removeItem('sirani_custom_sound_name');
      const badge = document.getElementById('customSoundFileBadge');
      if (badge) badge.style.display = 'none';
      const desc = document.getElementById('customSoundDesc');
      if (desc) desc.textContent = 'Pilih file audio apapun dari memori HP Anda';
      const btnPlay = document.getElementById('btnPlay_custom');
      if (btnPlay) btnPlay.style.display = 'none';
      const btnReset = document.getElementById('btnResetCustomSound');
      if (btnReset) btnReset.style.display = 'none';
      sirani_selectSound('chime');
    }

    function sirani_saveSoundSettings() {
      localStorage.setItem('sirani_sound_choice', activeSelectedSound);
      const vol = document.getElementById('notifVolumeSlider')?.value || '85';
      localStorage.setItem('sirani_sound_volume', vol);

      // Sinkronkan ke service worker bila aktif
      if (navigator.serviceWorker && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
          type: 'SIRANI_SET_SOUND_PREFERENCE',
          sound: activeSelectedSound,
          volume: vol
        });
      }

      // Beri feedback notifikasi singkat
      const soundNames = {
        chime: 'Chime Klasik (Ting Tung)',
        bell: 'Bel Sekolah (School Bell)',
        soft: 'Melodi Lembut (Gentle Harp)',
        alert: 'Alert Modern (Double Beep)',
        custom: 'File Audio Pilihan Sendiri'
      };
      const soundTitle = soundNames[activeSelectedSound] || activeSelectedSound;

      alert('Pengaturan Tersimpan!\n\n• Nada: ' + soundTitle + '\n• Volume: ' + vol + '%\n• Status: Selalu Aktif');
      closeNotifSettingsModal();
    }

    function sirani_testNotification() {
      const volVal = parseInt(document.getElementById('notifVolumeSlider')?.value || 85, 10) / 100;
      sirani_playPreviewSound(activeSelectedSound);

      if ('Notification' in window) {
        if (Notification.permission === 'granted') {
          showActualTestNotification();
        } else if (Notification.permission !== 'denied') {
          Notification.requestPermission().then(function(perm) {
            if (perm === 'granted') {
              showActualTestNotification();
            } else {
              alert('Izin notifikasi tidak diizinkan di browser Anda. Silakan izinkan notifikasi pada ikon gembok di bilah alamat browser.');
            }
          });
        } else {
          alert('Notifikasi saat ini diblokir di browser HP Anda. Buka Pengaturan Situs / Ikon Gembok di bilah alamat browser, lalu aktifkan Notifikasi.');
        }
      } else {
        alert('Browser ini tidak mendukung Web Notifications API.');
      }
    }

    function showActualTestNotification() {
      const testTitle = 'SIRANI — Tes Notifikasi & Suara';
      const testOptions = {
        body: 'Tes berhasil! Suara nada dering dan notifikasi presensi siap diterima.',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        vibrate: [250, 100, 250, 100, 350],
        tag: 'sirani-test-' + Date.now()
      };

      if ('serviceWorker' in navigator && navigator.serviceWorker.ready) {
        navigator.serviceWorker.ready.then(function(reg) {
          reg.showNotification(testTitle, testOptions);
        }).catch(function() {
          try { new Notification(testTitle, testOptions); } catch(e){}
        });
      } else {
        try { new Notification(testTitle, testOptions); } catch(e){}
      }
    }

    function toggleAndroidSoundGuide() {
      const body = document.getElementById('androidGuideBody');
      const chev = document.getElementById('guideChevron');
      if (!body) return;
      if (body.style.display === 'none' || body.style.display === '') {
        body.style.display = 'block';
        if (chev) chev.className = 'bi bi-chevron-up';
      } else {
        body.style.display = 'none';
        if (chev) chev.className = 'bi bi-chevron-down';
      }
    }

    // Listener suara saat Service Worker menerima Push Notification
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'SIRANI_PUSH_RECEIVED') {
          const soundChoice = localStorage.getItem('sirani_sound_choice') || 'chime';
          sirani_playPreviewSound(soundChoice);
        }
      });
    }

    // Unlock Web Audio & HTML5 Audio pada interaksi pengguna pertama di mobile browser
    let siraniAudioUnlocked = false;
    function siraniUnlockAudioContext() {
      if (siraniAudioUnlocked) return;
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
          const ctx = new AudioCtx();
          if (ctx.state === 'suspended') {
            ctx.resume();
          }
        }
        siraniAudioUnlocked = true;
      } catch (e) {}
    }
    document.addEventListener('click', siraniUnlockAudioContext, { once: true });
    document.addEventListener('touchstart', siraniUnlockAudioContext, { once: true, passive: true });

    // Expose ke global window
    window.openNotifSettingsModal = openNotifSettingsModal;
    window.closeNotifSettingsModal = closeNotifSettingsModal;
    window.handleNotifSettingsOverlayClick = handleNotifSettingsOverlayClick;
    window.handleNotifModalOverlayClick = handleNotifSettingsOverlayClick;
    window.sirani_selectSound = sirani_selectSound;
    window.sirani_playPreviewSound = sirani_playPreviewSound;
    window.sirani_updateVolume = sirani_updateVolume;
    window.sirani_saveSoundSettings = sirani_saveSoundSettings;
    window.sirani_testNotification = sirani_testNotification;
    window.sirani_handleCustomSoundUpload = sirani_handleCustomSoundUpload;
    window.sirani_removeCustomSound = sirani_removeCustomSound;
    window.toggleAndroidSoundGuide = toggleAndroidSoundGuide;
  </script>
</body>
</html>
