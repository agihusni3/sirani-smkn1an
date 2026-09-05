<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pusat Kendali Ekosistem Digital — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    :root {
      --portal-card-bg: var(--surface);
      --portal-card-border: var(--border-2);
      --portal-hover-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    [data-theme="dark"] {
      --portal-card-bg: #1e293b;
      --portal-card-border: rgba(255,255,255,0.08);
      --portal-hover-shadow: 0 16px 36px rgba(0,0,0,0.4);
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: var(--font-sans);
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .portal-topbar {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(8px);
      padding: 12px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .portal-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text);
    }

    .portal-brand-logo {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--bg-2);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .portal-brand-title {
      font-size: 16px;
      font-weight: 900;
      letter-spacing: -0.02em;
      line-height: 1.15;
    }

    .portal-brand-subtitle {
      font-size: 11px;
      color: var(--text-3);
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .portal-container {
      max-width: 1280px;
      width: 100%;
      margin: 0 auto;
      padding: 24px 20px 48px;
      flex: 1;
      box-sizing: border-box;
    }

    /* Hero Banner */
    .portal-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f2744 100%);
      border-radius: 20px;
      padding: 28px 32px;
      color: #ffffff;
      box-shadow: 0 12px 30px rgba(15,23,42,0.15);
      position: relative;
      overflow: hidden;
      margin-bottom: 24px;
      border: 1px solid rgba(255,255,255,0.1);
    }

    .portal-hero::after {
      content: '';
      position: absolute;
      right: -30px;
      bottom: -40px;
      width: 240px;
      height: 240px;
      background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, rgba(56,189,248,0) 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .portal-hero-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(4px);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 800;
      color: #38bdf8;
      margin-bottom: 12px;
      border: 1px solid rgba(56,189,248,0.25);
    }

    .portal-hero-title {
      font-size: 24px;
      font-weight: 900;
      line-height: 1.25;
      margin: 0 0 8px;
      letter-spacing: -0.02em;
    }

    .portal-hero-desc {
      font-size: 13.5px;
      color: #cbd5e1;
      max-width: 780px;
      line-height: 1.5;
      margin: 0;
    }

    .portal-live-clock {
      font-family: var(--font-mono);
      font-size: 12px;
      font-weight: 700;
      color: #94a3b8;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 14px;
      background: rgba(0,0,0,0.25);
      padding: 4px 12px;
      border-radius: 8px;
    }

    /* Section Headers */
    .portal-section-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 28px 0 16px;
    }

    .portal-section-title {
      font-size: 16px;
      font-weight: 900;
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      letter-spacing: -0.01em;
    }

    .portal-section-badge {
      font-size: 11px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 6px;
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      color: var(--text-2);
    }

    /* Grid active modules */
    .portal-active-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .module-card {
      background: var(--portal-card-bg);
      border: 1px solid var(--portal-card-border);
      border-radius: 18px;
      padding: 22px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      overflow: hidden;
    }

    .module-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--portal-hover-shadow);
      border-color: rgba(37,99,235,0.4);
    }

    .module-card-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .module-card-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
    }

    .module-status-pill {
      font-size: 10.5px;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .module-card-name {
      font-size: 17px;
      font-weight: 900;
      margin: 0 0 4px;
      letter-spacing: -0.01em;
      color: var(--text);
    }

    .module-card-subtitle {
      font-size: 12px;
      color: var(--text-3);
      font-weight: 600;
      margin: 0 0 16px;
    }

    .kpi-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 18px;
    }

    .kpi-item {
      display: flex;
      flex-direction: column;
    }

    .kpi-label {
      font-size: 10.5px;
      font-weight: 700;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-bottom: 2px;
    }

    .kpi-val {
      font-size: 18px;
      font-weight: 900;
      color: var(--text);
      font-family: var(--font-mono);
      line-height: 1.1;
    }

    .kpi-sub {
      font-size: 10px;
      color: var(--text-3);
      margin-top: 2px;
    }

    .module-actions {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-top: auto;
    }

    .btn-module-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      height: 40px;
      border-radius: 10px;
      font-size: 12.5px;
      font-weight: 800;
      text-decoration: none;
      transition: all 0.15s ease;
      color: #ffffff;
      border: none;
      cursor: pointer;
    }

    .btn-module-secondary-row {
      display: flex;
      gap: 6px;
    }

    .btn-module-secondary {
      flex: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      height: 32px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 700;
      text-decoration: none;
      background: var(--surface);
      border: 1px solid var(--border-2);
      color: var(--text-2);
      transition: all 0.15s ease;
    }

    .btn-module-secondary:hover {
      background: var(--bg-3);
      color: var(--text);
      border-color: var(--border);
    }

    /* Roadmap Grid */
    .portal-roadmap-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 16px;
    }

    .roadmap-card {
      background: var(--portal-card-bg);
      border: 1px solid var(--portal-card-border);
      border-radius: 16px;
      padding: 18px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      opacity: 0.95;
      transition: all 0.2s ease;
    }

    .roadmap-card:hover {
      border-color: var(--border);
      transform: translateY(-2px);
      opacity: 1;
    }

    .roadmap-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .roadmap-icon-wrap {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .roadmap-badge {
      font-size: 10px;
      font-weight: 800;
      padding: 2px 7px;
      border-radius: 12px;
      background: rgba(245,158,11,0.12);
      color: #d97706;
      border: 1px solid rgba(245,158,11,0.3);
    }

    .roadmap-title {
      font-size: 14.5px;
      font-weight: 900;
      margin: 0 0 2px;
      color: var(--text);
    }

    .roadmap-sub {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-3);
      margin: 0 0 10px;
    }

    .roadmap-desc {
      font-size: 11.5px;
      line-height: 1.45;
      color: var(--text-2);
      margin: 0 0 12px;
      flex: 1;
    }

    .roadmap-lead {
      font-size: 10.5px;
      font-weight: 700;
      color: var(--text-3);
      background: var(--bg-2);
      padding: 6px 10px;
      border-radius: 8px;
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 5px;
    }

    /* System Quick Strip */
    .system-strip {
      margin-top: 32px;
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }

    .system-strip-left {
      display: flex;
      align-items: center;
      gap: 18px;
      font-size: 12px;
      color: var(--text-2);
      flex-wrap: wrap;
    }

    .system-indicator {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-weight: 700;
    }

    .dot-live {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 8px #10b981;
    }

    .system-strip-links {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .system-strip-link {
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-2);
      text-decoration: none;
      padding: 5px 10px;
      border-radius: 6px;
      background: var(--surface);
      border: 1px solid var(--border);
      transition: all 0.15s ease;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .system-strip-link:hover {
      color: var(--text);
      border-color: var(--border-2);
    }

    @media (max-width: 1024px) {
      .portal-active-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .portal-topbar {
        padding: 10px 14px;
      }
      .portal-container {
        padding: 16px 12px 40px;
      }
      .portal-hero {
        padding: 20px;
      }
      .portal-hero-title {
        font-size: 20px;
      }
      .portal-roadmap-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  {{-- Universal Top Navigation Bar --}}
  <header class="portal-topbar">
    <a href="{{ route('admin.portal') }}" class="portal-brand">
      <div class="portal-brand-logo">
        <img src="/img/logo.png" alt="SMKN 1 AN" style="width:100%; height:100%; object-fit:contain;" />
      </div>
      <div>
        <div class="portal-brand-title">EKOSISTEM DIGITAL</div>
        <div class="portal-brand-subtitle">SMKN 1 AIR NANINGAN · PUSAT KENDALI</div>
      </div>
    </a>

    <div style="display:flex; align-items:center; gap:10px;">
      @include('partials.header_actions')
    </div>
  </header>

  <main class="portal-container">

    {{-- Hero Command Center Banner --}}
    <div class="portal-hero">
      <div class="portal-hero-pill">
        <i class="bi bi-cpu-fill"></i> PUSAT KENDALI MODULAR TERPADU
      </div>
      <h1 class="portal-hero-title">
        Selamat Bertugas, {{ auth()->user()?->name ?? 'Administrator' }}
      </h1>
      <p class="portal-hero-desc">
        Pintu gerbang manajemen terpadu SMKN 1 Air Naningan. Pilih modul operasional untuk mengelola absensi cerdas (SIRANI), seleksi siswa baru (PPDB 2026), maupun publikasi informasi publik (Web Profil).
      </p>
      <div class="portal-live-clock" id="portalLiveClock">
        <i class="bi bi-clock-history"></i> Memuat waktu sistem...
      </div>
    </div>

    {{-- Section 1: Modul Operasional Aktif --}}
    <div class="portal-section-head">
      <h2 class="portal-section-title">
        <i class="bi bi-boxes" style="color:#2563eb;"></i> Modul Sistem Aktif
      </h2>
      <span class="portal-section-badge">3 Modul Siap Digunakan</span>
    </div>

    <div class="portal-active-grid">
      
      {{-- Card 1: SIRANI --}}
      <div class="module-card">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
              <i class="bi bi-fingerprint"></i>
            </div>
            <span class="module-status-pill" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.25);">
              <span class="dot-live" style="background:#10b981; box-shadow:0 0 6px #10b981;"></span> Aktif
            </span>
          </div>

          <h3 class="module-card-name">SIRANI</h3>
          <p class="module-card-subtitle">Sistem Absensi &amp; Ketertiban Siswa/Guru</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Hadir Siswa Hari Ini</span>
              <span class="kpi-val" style="color:#10b981;">{{ $persenSiswaHadir }}%</span>
              <span class="kpi-sub">{{ $siswaHadirToday }} / {{ $totalSiswa }} Siswa</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Hadir Guru &amp; Pegawai</span>
              <span class="kpi-val" style="color:#2563eb;">{{ $guruHadirToday }}</span>
              <span class="kpi-sub">Dari {{ $totalGuru }} Guru Aktif</span>
            </div>
            <div class="kpi-item" style="margin-top:4px;">
              <span class="kpi-label">Kasus Disiplin Aktif</span>
              <span class="kpi-val" style="color:{{ $kasusDisiplinAktif > 0 ? '#ef4444' : '#10b981' }};">{{ $kasusDisiplinAktif }}</span>
              <span class="kpi-sub">Dalam Pembinaan</span>
            </div>
            <div class="kpi-item" style="margin-top:4px;">
              <span class="kpi-label">Smart Gate Gerbang</span>
              <span class="kpi-val" style="font-size:13px; color:{{ $isGerbangAktif ? '#10b981' : '#64748b' }};">
                {{ $isGerbangAktif ? 'ONLINE' : 'STANDBY' }}
              </span>
              <span class="kpi-sub">{{ $isGerbangAktif ? 'Sesi Presensi Buka' : 'Di Luar Jam Sesi' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          <a href="/dashboard" class="btn-module-primary" style="background:#10b981;">
            <i class="bi bi-speedometer2"></i> Buka Modul SIRANI
          </a>
          <div class="btn-module-secondary-row">
            <a href="/smart-gate" target="_blank" class="btn-module-secondary">
              <i class="bi bi-upc-scan"></i> Smart Gate
            </a>
            <a href="/laporan" class="btn-module-secondary">
              <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>
          </div>
        </div>
      </div>

      {{-- Card 2: PPDB ONLINE 2026 --}}
      <div class="module-card">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon" style="background:rgba(217,119,6,0.12); color:#d97706;">
              <i class="bi bi-person-badge-fill"></i>
            </div>
            <span class="module-status-pill" style="background:rgba(217,119,6,0.1); color:#d97706; border:1px solid rgba(217,119,6,0.25);">
              <span class="dot-live" style="background:#d97706; box-shadow:0 0 6px #d97706;"></span> Aktif 2026
            </span>
          </div>

          <h3 class="module-card-name">PPDB ONLINE 2026</h3>
          <p class="module-card-subtitle">Penerimaan Peserta Didik Baru Terpadu</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Pendaftar</span>
              <span class="kpi-val" style="color:#d97706;">{{ $totalPendaftar }}</span>
              <span class="kpi-sub">+{{ $ppdbToday }} Pendaftar Hari Ini</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Menunggu Verifikasi</span>
              <span class="kpi-val" style="color:#ef4444;">{{ $ppdbMenunggu }}</span>
              <span class="kpi-sub">Perlu Dicek Panitia</span>
            </div>
            <div class="kpi-item" style="margin-top:4px;">
              <span class="kpi-label">Lolos / Diterima</span>
              <span class="kpi-val" style="color:#10b981;">{{ $ppdbDiterima }}</span>
              <span class="kpi-sub">Calon Siswa Resmi</span>
            </div>
            <div class="kpi-item" style="margin-top:4px;">
              <span class="kpi-label">Ditolak / Perbaikan</span>
              <span class="kpi-val" style="color:#64748b;">{{ $ppdbDitolak }}</span>
              <span class="kpi-sub">Berkas Tidak Sesuai</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          <a href="/admin/ppdb" class="btn-module-primary" style="background:#d97706;">
            <i class="bi bi-people-fill"></i> Kelola PPDB 2026
          </a>
          <div class="btn-module-secondary-row">
            <a href="/admin/ppdb?status=menunggu" class="btn-module-secondary">
              <i class="bi bi-clock"></i> Verifikasi Berkas
            </a>
            <a href="/ppdb" target="_blank" class="btn-module-secondary">
              <i class="bi bi-box-arrow-up-right"></i> Halaman Publik
            </a>
          </div>
        </div>
      </div>

      {{-- Card 3: WEB PROFIL & CMS HUMAS --}}
      <div class="module-card">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon" style="background:rgba(99,102,241,0.12); color:#6366f1;">
              <i class="bi bi-globe-americas"></i>
            </div>
            <span class="module-status-pill" style="background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.25);">
              <span class="dot-live" style="background:#6366f1; box-shadow:0 0 6px #6366f1;"></span> Publik
            </span>
          </div>

          <h3 class="module-card-name">WEB PROFIL &amp; HUMAS</h3>
          <p class="module-card-subtitle">Etalase Publik &amp; Manajemen Publikasi</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Berita &amp; Rilis</span>
              <span class="kpi-val" style="color:#6366f1;">{{ $totalBerita }}</span>
              <span class="kpi-sub">Artikel Terpublikasi</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Hero Banner Aktif</span>
              <span class="kpi-val" style="color:#0ea5e9;">{{ $totalBannerAktif }}</span>
              <span class="kpi-sub">Slide Halaman Depan</span>
            </div>
            <div class="kpi-item" style="grid-column: span 2; margin-top:4px;">
              <span class="kpi-label">Berita Terakhir</span>
              <span style="font-size:12px; font-weight:800; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; margin-top:2px;">
                {{ $beritaTerbaru ? $beritaTerbaru->judul : 'Belum ada rilis berita' }}
              </span>
              <span class="kpi-sub">{{ $beritaTerbaru ? \Carbon\Carbon::parse($beritaTerbaru->created_at)->diffForHumans() : '-' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          <a href="/admin/berita" class="btn-module-primary" style="background:#6366f1;">
            <i class="bi bi-newspaper"></i> Kelola Berita &amp; Rilis
          </a>
          <div class="btn-module-secondary-row">
            <a href="/admin/banner" class="btn-module-secondary">
              <i class="bi bi-images"></i> Kelola Banner
            </a>
            <a href="/" target="_blank" class="btn-module-secondary">
              <i class="bi bi-box-arrow-up-right"></i> Lihat Website
            </a>
          </div>
        </div>
      </div>

    </div>

    {{-- Section 2: Roadmap Modul Masa Depan --}}
    <div class="portal-section-head">
      <div>
        <h2 class="portal-section-title">
          <i class="bi bi-diagram-3-fill" style="color:#d97706;"></i> Roadmap Ekosistem Digital SMKN 1 AN
        </h2>
        <span style="font-size:12px; color:var(--text-3); font-weight:600;">
          Modul yang telah dipetakan dalam arsitektur digital sekolah dan siap diaktifkan secara bertahap.
        </span>
      </div>
      <span class="portal-section-badge">5 Modul Rancang Bangun</span>
    </div>

    <div class="portal-roadmap-grid">
      @foreach($futureModules as $mod)
        <div class="roadmap-card">
          <div>
            <div class="roadmap-top">
              <div class="roadmap-icon-wrap" style="background:rgba(0,0,0,0.05); color:{{ $mod['color'] }};">
                <i class="bi {{ $mod['icon'] }}"></i>
              </div>
              <span class="roadmap-badge">{{ $mod['badge'] }}</span>
            </div>
            <h4 class="roadmap-title">{{ $mod['name'] }}</h4>
            <div class="roadmap-sub">{{ $mod['subtitle'] }}</div>
            <p class="roadmap-desc">{{ $mod['description'] }}</p>
          </div>
          <div class="roadmap-lead">
            <i class="bi bi-person-check-fill" style="color:{{ $mod['color'] }}; font-size:11px;"></i>
            <span>PIC: {{ $mod['lead'] }}</span>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Section 3: System Status & Quick Links --}}
    <div class="system-strip">
      <div class="system-strip-left">
        <div class="system-indicator">
          <span class="dot-live"></span> Sistem Operasional Normal
        </div>
        <div>·</div>
        <div>Framework: <strong>Laravel v{{ app()->version() }}</strong></div>
        <div>·</div>
        <div>PHP: <strong>v{{ PHP_VERSION }}</strong></div>
        <div>·</div>
        <div>Zona: <strong>Asia/Jakarta (WIB)</strong></div>
      </div>

      <div class="system-strip-links">
        <a href="/audit" class="system-strip-link" title="Audit Trail Log Keamanan">
          <i class="bi bi-shield-check" style="color:#64748b;"></i> Audit Log
        </a>
        <a href="/backup" class="system-strip-link" title="Manajemen Backup Database">
          <i class="bi bi-database-check" style="color:#0284c7;"></i> Backup DB
        </a>
        <a href="/pengaturan-sekolah" class="system-strip-link" title="Profil Identitas Sekolah">
          <i class="bi bi-building-gear" style="color:#10b981;"></i> Profil SMKN 1
        </a>
      </div>
    </div>

  </main>

  <script>
    // Live Clock WIB Script
    function updatePortalClock() {
      const el = document.getElementById('portalLiveClock');
      if (!el) return;
      const now = new Date();
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Jakarta'
      };
      el.innerHTML = '<i class="bi bi-clock-history"></i> ' + now.toLocaleString('id-ID', options) + ' WIB';
    }
    updatePortalClock();
    setInterval(updatePortalClock, 1000);
  </script>

</body>
</html>
