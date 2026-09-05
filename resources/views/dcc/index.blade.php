<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Digital Command Center — SMKN 1 AN</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-portal.css') }}?v={{ filemtime(public_path('css/admin-portal.css')) }}">
</head>
<body>

  {{-- Universal Top Navigation Bar --}}
  <header class="portal-topbar">
    <a href="{{ route('admin.portal') }}" class="portal-brand">
      <div class="portal-brand-logo-wrap">
        <div class="portal-brand-logo">
          <img src="/img/logo.png" alt="SMKN 1 AN" style="width:100%; height:100%; object-fit:contain;" />
        </div>
      </div>
      <div>
        <div class="portal-brand-title">
          <span>DCC SMKN 1 AN</span>
        </div>
        <div class="portal-brand-subtitle">Digital Command Center · SMKN 1 Air Naningan</div>
      </div>
    </a>

    <div class="portal-top-right">
      @include('partials.header_actions')
    </div>
  </header>
 
  <main class="portal-container">
    @php
      $hour = (int) now()->timezone('Asia/Jakarta')->format('H');
      $salam = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    @endphp

    {{-- Hero Mission Control Banner (Minimalist Modern) --}}
    <div class="cockpit-hero">
      <div class="cockpit-hero-left">
        <div class="cockpit-salam-badge">
          <i class="bi bi-clock-history"></i>
          <span>{{ $salam }} · SMKN 1 Air Naningan</span>
        </div>

        <h1 class="cockpit-title">
          Selamat Bertugas, {{ auth()->user()?->name ?? 'Administrator' }}
        </h1>
        
        <p class="cockpit-desc">
          Pusat kendali ekosistem digital terpadu SMKN 1 Air Naningan. Akses data pokok kelembagaan, presensi gerbang, seleksi PPDB, dan publikasi institusi dalam satu sistem terintegrasi.
        </p>

        <div class="cockpit-meta-row">
          <div class="cockpit-meta-chip" id="portalLiveClock">
            <i class="bi bi-calendar3"></i> Memuat waktu sistem...
          </div>
          <div class="cockpit-meta-chip">
            <i class="bi bi-shield-check"></i> {{ auth()->user()?->role_display_name ?? 'Super Administrator' }}
          </div>
        </div>

        <div class="cockpit-action-row">
          <a href="#modul-aktif" class="cockpit-action-btn primary">
            <i class="bi bi-grid-fill"></i> Buka Modul Sistem
          </a>
          <a href="{{ route('audit.index') }}" class="cockpit-action-btn secondary">
            <i class="bi bi-shield-shaded"></i> Audit Telemetri
          </a>
          @if($canAccessSirani)
            <a href="{{ route('dashboard') }}" class="cockpit-action-btn secondary">
              <i class="bi bi-fingerprint"></i> Presensi Gerbang
            </a>
          @endif
        </div>
      </div>

      {{-- Right: Clean Framed Showcase Image --}}
      <div class="cockpit-showcase-frame">
        <img src="/images/web/dcc_command_center_banner.jpg" alt="DCC SMKN 1 AN Command Center" class="cockpit-showcase-img" onerror="this.src='/images/web/hero_kampus.jpg';" />
        <div class="cockpit-showcase-caption">
          <i class="bi bi-cpu"></i> Smart Command Center · SMKN 1 Air Naningan
        </div>
      </div>
    </div>

    {{-- Minimalist Quick Telemetry Bar --}}
    <div class="dcc-stat-strip">
      <div class="dcc-stat-card">
        <div class="dcc-stat-icon situan"><i class="bi bi-buildings"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">SITUAN · DATA POKOK</div>
          <div class="dcc-stat-val">{{ number_format($totalSiswa) }} <span class="dcc-stat-unit">Siswa</span></div>
          <div class="dcc-stat-sub">{{ $totalGuru }} PTK Aktif · {{ $totalRombel }} Rombel</div>
        </div>
      </div>

      <div class="dcc-stat-card">
        <div class="dcc-stat-icon sirani"><i class="bi bi-fingerprint"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">SIRANI · PRESENSI HARI INI</div>
          <div class="dcc-stat-val">{{ $persenSiswaHadir }}% <span class="dcc-stat-unit">Kehadiran</span></div>
          <div class="dcc-stat-sub">{{ $siswaHadirToday }} Siswa Hadir · Gerbang {{ $isGerbangAktif ? 'Buka' : 'Tutup' }}</div>
        </div>
      </div>

      <div class="dcc-stat-card">
        <div class="dcc-stat-icon ppdb"><i class="bi bi-person-plus"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">PPDB 2026 · PENDAFTARAN</div>
          <div class="dcc-stat-val">{{ number_format($totalPendaftar) }} <span class="dcc-stat-unit">Calon</span></div>
          <div class="dcc-stat-sub">{{ $ppdbMenunggu }} Menunggu · {{ $ppdbDiterima }} Diterima</div>
        </div>
      </div>

      <div class="dcc-stat-card">
        <div class="dcc-stat-icon web"><i class="bi bi-globe2"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">HUMAS · PUBLIKASI</div>
          <div class="dcc-stat-val">{{ $totalBerita }} <span class="dcc-stat-unit">Rilis</span></div>
          <div class="dcc-stat-sub">{{ $totalBannerAktif }} Banner Aktif Beranda</div>
        </div>
      </div>
    </div>

    {{-- Section 1: Modul Operasional Aktif --}}
    <div class="portal-section-head" id="modul-aktif">
      <h2 class="portal-section-title">
        <div class="portal-section-title-icon" style="background:rgba(37,99,235,0.12); color:#2563eb;">
          <i class="bi bi-boxes"></i>
        </div>
        <span>Modul Sistem Aktif</span>
      </h2>
      <span class="portal-section-badge">4 Modul Ekosistem Terpadu</span>
    </div>

    <div class="portal-active-grid">

      {{-- Card 1: SITUAN — SMKN 1 AN --}}
      <div class="module-card card-situan {{ $canAccessSituan ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(2,132,199,0.2), rgba(3,105,161,0.08)); color:#0284c7; border:1px solid rgba(2,132,199,0.25);">
              <i class="bi bi-buildings-fill"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessSituan)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Akses Terbatas</span>
              @endif
              <span class="module-live-pill" style="background:rgba(2,132,199,0.1); color:#0284c7; border:1px solid rgba(2,132,199,0.25);">
                <span class="pulse-dot" style="background:#0284c7; box-shadow:0 0 0 0 rgba(2,132,199,0.6);"></span> Data Induk
              </span>
            </div>
          </div>

          <h3 class="module-card-name">SITUAN</h3>
          <p class="module-card-subtitle">Sistem Informasi Tata Usaha SMKN 1 Air Naningan</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Pendidik &amp; Tendik (PTK)</span>
              <span class="kpi-val" style="color:#0284c7;">{{ $totalGuru }}</span>
              <span class="kpi-sub">Guru &amp; Pegawai Aktif</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Peserta Didik (Siswa)</span>
              <span class="kpi-val" style="color:#10b981;">{{ $totalSiswa }}</span>
              <span class="kpi-sub">Siswa Terdaftar Aktif</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Rombel &amp; Jurusan</span>
              <span class="kpi-val" style="color:#d97706;">{{ $totalRombel }}</span>
              <span class="kpi-sub">Kelas / 3 Jurusan</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Tahun Ajaran Aktif</span>
              <span class="kpi-val" style="font-size:13px; color:#6366f1;">
                {{ $tahunAjaranAktif ? $tahunAjaranAktif->nama : '2025/2026' }}
              </span>
              <span class="kpi-sub">{{ $tahunAjaranAktif ? ucfirst($tahunAjaranAktif->semester ?? 'Aktif') : 'Semester Berjalan' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessSituan)
            <a href="{{ route('situan.index') }}" class="btn-launch-primary" style="background:linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
              <i class="bi bi-buildings-fill"></i> Buka Modul SITUAN <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/guru" class="btn-launch-secondary" title="Kelola Master Guru &amp; Pegawai">
                <i class="bi bi-person-badge-fill" style="color:#0284c7;"></i> Data PTK
              </a>
              <a href="/siswa" class="btn-launch-secondary" title="Kelola Master Siswa {{ auth()->user() && auth()->user()->isWaliKelas() && !auth()->user()->isAdmin() ? '(Kelas Binaan)' : '' }}">
                <i class="bi bi-people-fill" style="color:#10b981;"></i> Data Siswa
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Staf Tata Usaha, Pimpinan, dan Wali Kelas">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Staf TU/Pimpinan)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Data PTK Terkunci</span>
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Data Siswa Terkunci</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 2: SIRANI --}}
      <div class="module-card card-sirani {{ $canAccessSirani ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.08)); color:#10b981; border:1px solid rgba(16,185,129,0.25);">
              <i class="bi bi-fingerprint"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessSirani)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Akses Terbatas</span>
              @endif
              <span class="module-live-pill" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.25);">
                <span class="pulse-dot"></span> Aktif
              </span>
            </div>
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
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Kasus Disiplin Aktif</span>
              <span class="kpi-val" style="color:{{ $kasusDisiplinAktif > 0 ? '#ef4444' : '#10b981' }};">{{ $kasusDisiplinAktif }}</span>
              <span class="kpi-sub">Dalam Pembinaan</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Smart Gate Gerbang</span>
              <span class="kpi-val" style="font-size:14px; color:{{ $isGerbangAktif ? '#10b981' : '#64748b' }};">
                {{ $isGerbangAktif ? 'ONLINE' : 'STANDBY' }}
              </span>
              <span class="kpi-sub">{{ $isGerbangAktif ? 'Sesi Presensi Buka' : 'Di Luar Jam Sesi' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessSirani)
            <a href="/dashboard" class="btn-launch-primary" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%);">
              <i class="bi bi-speedometer2"></i> Buka Modul SIRANI <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/smart-gate" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-upc-scan" style="color:#10b981;"></i> Smart Gate
              </a>
              <a href="/laporan" class="btn-launch-secondary">
                <i class="bi bi-file-earmark-bar-graph" style="color:#0284c7;"></i> Laporan
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Anda tidak memiliki wewenang untuk membuka Modul SIRANI">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Pendidik/Staf)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Smart Gate Terkunci</span>
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Laporan Terkunci</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 2: PPDB ONLINE 2026 --}}
      <div class="module-card card-ppdb {{ $canAccessPpdb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.08)); color:#d97706; border:1px solid rgba(245,158,11,0.25);">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessPpdb)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Butuh Panitia</span>
              @endif
              <span class="module-live-pill" style="background:rgba(245,158,11,0.1); color:#d97706; border:1px solid rgba(245,158,11,0.25);">
                <span class="pulse-dot" style="background:#d97706; box-shadow:0 0 0 0 rgba(217,119,6,0.6);"></span> Aktif 2026
              </span>
            </div>
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
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Lolos / Diterima</span>
              <span class="kpi-val" style="color:#10b981;">{{ $ppdbDiterima }}</span>
              <span class="kpi-sub">Calon Siswa Resmi</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Ditolak / Perbaikan</span>
              <span class="kpi-val" style="color:#64748b;">{{ $ppdbDitolak }}</span>
              <span class="kpi-sub">Berkas Tidak Sesuai</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessPpdb)
            <a href="/admin/ppdb" class="btn-launch-primary" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
              <i class="bi bi-people-fill"></i> Kelola PPDB 2026 <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/admin/ppdb?status=menunggu" class="btn-launch-secondary">
                <i class="bi bi-clock" style="color:#d97706;"></i> Verifikasi Berkas
              </a>
              <a href="/ppdb" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#2563eb;"></i> Form Publik
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Panitia PPDB, Waka Kesiswaan, & Pimpinan">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Panitia PPDB)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Verifikasi Terkunci</span>
              <a href="/ppdb" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#2563eb;"></i> Form Publik
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 3: WEB PROFIL & HUMAS --}}
      <div class="module-card card-web {{ $canAccessWeb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(99,102,241,0.2), rgba(79,70,229,0.08)); color:#6366f1; border:1px solid rgba(99,102,241,0.25);">
              <i class="bi bi-globe-americas"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessWeb)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Butuh Humas</span>
              @endif
              <span class="module-live-pill" style="background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.25);">
                <span class="pulse-dot" style="background:#6366f1; box-shadow:0 0 0 0 rgba(99,102,241,0.6);"></span> Publik
              </span>
            </div>
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
            <div class="kpi-item" style="grid-column: span 2; margin-top:6px;">
              <span class="kpi-label">Berita Terakhir</span>
              <span style="font-size:12.5px; font-weight:800; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; margin-top:2px;">
                {{ $beritaTerbaru ? $beritaTerbaru->judul : 'Belum ada rilis berita' }}
              </span>
              <span class="kpi-sub">{{ $beritaTerbaru ? \Carbon\Carbon::parse($beritaTerbaru->created_at)->diffForHumans() : '-' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessWeb)
            <a href="/admin/berita" class="btn-launch-primary" style="background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
              <i class="bi bi-newspaper"></i> Kelola Berita &amp; Rilis <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/admin/banner" class="btn-launch-secondary">
                <i class="bi bi-images" style="color:#0ea5e9;"></i> Banner Hero
              </a>
              <a href="/" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#10b981;"></i> Lihat Website
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Tim Humas, Webmaster, & Pimpinan">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Tim Humas)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Banner Terkunci</span>
              <a href="/" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#10b981;"></i> Lihat Website
              </a>
            </div>
          @endif
        </div>
      </div>

    </div>

    {{-- Section 2: Roadmap Modul Masa Depan --}}
    <div class="portal-section-head">
      <div>
        <h2 class="portal-section-title">
          <div class="portal-section-title-icon" style="background:rgba(245,158,11,0.12); color:#d97706;">
            <i class="bi bi-diagram-3-fill"></i>
          </div>
          <span>Roadmap Modul DCC SMKN 1 AN</span>
        </h2>
        <span style="font-size:12px; color:var(--dcc-text-sub); font-weight:600;">
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
              <div class="roadmap-icon-wrap" style="background:rgba(0,0,0,0.03); color:{{ $mod['color'] }};">
                <i class="bi {{ $mod['icon'] }}"></i>
              </div>
              <span class="roadmap-badge">{{ $mod['badge'] }}</span>
            </div>
            <h4 class="roadmap-title">{{ $mod['name'] }}</h4>
            <div class="roadmap-sub">{{ $mod['subtitle'] }}</div>
            <p class="roadmap-desc">{{ $mod['description'] }}</p>
          </div>
          
          <div>
            <div class="roadmap-progress-wrap">
              <div style="display:flex; justify-content:space-between; font-size:9.5px; font-weight:700; color:var(--dcc-text-sub); margin-bottom:4px;">
                <span>Tahap Desain</span>
                <span style="color:{{ $mod['color'] }};">Fase 1</span>
              </div>
              <div class="roadmap-progress-bar">
                <div class="roadmap-progress-fill" style="width: 35%; background:{{ $mod['color'] }};"></div>
              </div>
            </div>

            <div class="roadmap-lead">
              <i class="bi bi-person-check-fill" style="color:{{ $mod['color'] }}; font-size:11px;"></i>
              <span>PIC: {{ $mod['lead'] }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Section 3: System Status & Quick Links --}}
    <div class="system-strip">
      <div class="system-strip-left">
        <div style="display:inline-flex; align-items:center; gap:7px; font-weight:800; color:#10b981;">
          <span class="pulse-dot"></span> Infrastruktur Stabil
        </div>
        <div style="opacity:0.4;">|</div>
        <div>Core Engine: <strong>Laravel v{{ app()->version() }}</strong></div>
        <div style="opacity:0.4;">|</div>
        <div>Runtime: <strong>PHP v{{ PHP_VERSION }}</strong></div>
        <div style="opacity:0.4;">|</div>
        <div>Zona Waktu: <strong>Asia/Jakarta (WIB)</strong></div>
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
    // Live Clock WIB Script Digital Monospace
    function updatePortalClock() {
      const el = document.getElementById('portalLiveClock');
      if (!el) return;
      const now = new Date();
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Jakarta'
      };
      el.innerHTML = '<i class="bi bi-clock-history" style="color:#38bdf8;"></i> ' + now.toLocaleString('id-ID', options) + ' WIB';
    }
    updatePortalClock();
    setInterval(updatePortalClock, 1000);
  </script>

</body>
</html>
