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
          <span class="pulse-dot"></span>
          <span>{{ $salam }} · SMKN 1 Air Naningan</span>
        </div>

        <h1 class="cockpit-title">
          Selamat Bertugas, {{ auth()->user()?->name ?? 'Administrator' }}
        </h1>
        
        <p class="cockpit-desc">
          Pusat kendali ekosistem digital terpadu SMKN 1 Air Naningan. Akses data pokok kelembagaan, presensi gerbang, seleksi PPDB, dan publikasi institusi dalam satu sistem terintegrasi.
        </p>
      </div>

      {{-- Right: Sleek Live Clock & Status Card --}}
      <div class="cockpit-hero-right">
        <div class="cockpit-status-card">
          <div class="cockpit-status-top">
            <span class="pulse-dot" style="background:#10b981;"></span>
            <span>STATUS SERVER NORMAL</span>
          </div>
          <div id="portalLiveClock" class="cockpit-live-clock">--:--:-- WIB</div>
          <div class="cockpit-status-sub">Digital Command Center · Online</div>
        </div>
      </div>
    </div>

    {{-- Minimalist Quick Telemetry Bar --}}
    <div class="dcc-stat-strip">
      <div class="dcc-stat-card">
        <div class="dcc-stat-icon"><i class="bi bi-buildings"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">SITUAN · DATA POKOK</div>
          <div class="dcc-stat-val">{{ number_format($totalSiswa) }} <span class="dcc-stat-unit">Siswa</span></div>
          <div class="dcc-stat-sub">{{ $totalGuru }} PTK Aktif · {{ $totalRombel }} Rombel</div>
        </div>
      </div>

      <div class="dcc-stat-card">
        <div class="dcc-stat-icon"><i class="bi bi-fingerprint"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">SIRANI · PRESENSI HARI INI</div>
          <div class="dcc-stat-val">{{ $persenSiswaHadir }}% <span class="dcc-stat-unit">Kehadiran</span></div>
          <div class="dcc-stat-sub">{{ $siswaHadirToday }} Siswa Hadir · Gerbang {{ $isGerbangAktif ? 'Buka' : 'Tutup' }}</div>
        </div>
      </div>

      <div class="dcc-stat-card">
        <div class="dcc-stat-icon"><i class="bi bi-person-plus"></i></div>
        <div class="dcc-stat-body">
          <div class="dcc-stat-label">PPDB 2026 · PENDAFTARAN</div>
          <div class="dcc-stat-val">{{ number_format($totalPendaftar) }} <span class="dcc-stat-unit">Calon</span></div>
          <div class="dcc-stat-sub">{{ $ppdbMenunggu }} Menunggu · {{ $ppdbDiterima }} Diterima</div>
        </div>
      </div>

      <div class="dcc-stat-card">
        <div class="dcc-stat-icon"><i class="bi bi-globe2"></i></div>
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
        <span>Modul Sistem Aktif</span>
      </h2>
      <span class="portal-section-badge">4 Modul Ekosistem Terpadu</span>
    </div>

    <div class="portal-active-grid">

      {{-- Card 1: SITUAN — SMKN 1 AN --}}
      <div class="module-card card-situan {{ $canAccessSituan ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo">
              <i class="bi bi-buildings"></i>
            </div>
          </div>

          <h3 class="module-card-name">SITUAN</h3>
          <p class="module-card-subtitle">Sistem Informasi Tata Usaha SMKN 1 Air Naningan</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Pendidik &amp; Tendik (PTK)</span>
              <span class="kpi-val">{{ $totalGuru }}</span>
              <span class="kpi-sub">Guru &amp; Pegawai Aktif</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Peserta Didik (Siswa)</span>
              <span class="kpi-val">{{ $totalSiswa }}</span>
              <span class="kpi-sub">Siswa Terdaftar Aktif</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Rombel &amp; Jurusan</span>
              <span class="kpi-val">{{ $totalRombel }}</span>
              <span class="kpi-sub">Kelas / 3 Jurusan</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Tahun Ajaran Aktif</span>
              <span class="kpi-val" style="font-size:13px;">
                {{ $tahunAjaranAktif ? $tahunAjaranAktif->nama : '2025/2026' }}
              </span>
              <span class="kpi-sub">{{ $tahunAjaranAktif ? ucfirst($tahunAjaranAktif->semester ?? 'Aktif') : 'Semester Berjalan' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessSituan)
            <a href="{{ route('situan.index') }}" class="btn-launch-primary">
              Buka Modul SITUAN
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/guru" class="btn-launch-secondary" title="Kelola Master Guru &amp; Pegawai">
                Data PTK
              </a>
              <a href="/siswa" class="btn-launch-secondary" title="Kelola Master Siswa {{ auth()->user() && auth()->user()->isWaliKelas() && !auth()->user()->isAdmin() ? '(Kelas Binaan)' : '' }}">
                Data Siswa
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Staf Tata Usaha, Pimpinan, dan Wali Kelas">
              Akses Terbatas (Khusus Staf TU/Pimpinan)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked">Data PTK Terkunci</span>
              <span class="btn-launch-secondary btn-locked">Data Siswa Terkunci</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 2: SIRANI --}}
      <div class="module-card card-sirani {{ $canAccessSirani ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo">
              <i class="bi bi-fingerprint"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessSirani)
                <span class="access-badge allowed">Izin Aktif</span>
              @else
                <span class="access-badge locked">Akses Terbatas</span>
              @endif
              <span class="module-live-pill">
                <span class="pulse-dot"></span> Aktif
              </span>
            </div>
          </div>

          <h3 class="module-card-name">SIRANI</h3>
          <p class="module-card-subtitle">Sistem Informasi Responsif Absensi</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Hadir Siswa Hari Ini</span>
              <span class="kpi-val">{{ $persenSiswaHadir }}%</span>
              <span class="kpi-sub">{{ $siswaHadirToday }} / {{ $totalSiswa }} Siswa</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Hadir Guru &amp; Pegawai</span>
              <span class="kpi-val">{{ $guruHadirToday }}</span>
              <span class="kpi-sub">Dari {{ $totalGuru }} Guru Aktif</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Kasus Disiplin Aktif</span>
              <span class="kpi-val">{{ $kasusDisiplinAktif }}</span>
              <span class="kpi-sub">Dalam Pembinaan</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Smart Gate Gerbang</span>
              <span class="kpi-val" style="font-size:14px;">
                {{ $isGerbangAktif ? 'ONLINE' : 'STANDBY' }}
              </span>
              <span class="kpi-sub">{{ $isGerbangAktif ? 'Sesi Presensi Buka' : 'Di Luar Jam Sesi' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessSirani)
            <a href="/dashboard" class="btn-launch-primary">
              Buka Modul SIRANI
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/smart-gate" target="_blank" class="btn-launch-secondary">
                Smart Gate
              </a>
              <a href="/laporan" class="btn-launch-secondary">
                Laporan
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Anda tidak memiliki wewenang untuk membuka Modul SIRANI">
              Akses Terbatas (Khusus Pendidik/Staf)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked">Smart Gate Terkunci</span>
              <span class="btn-launch-secondary btn-locked">Laporan Terkunci</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 2: PPDB ONLINE 2026 --}}
      <div class="module-card card-ppdb {{ $canAccessPpdb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo">
              <i class="bi bi-mortarboard"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessPpdb)
                <span class="access-badge allowed">Izin Aktif</span>
              @else
                <span class="access-badge locked">Butuh Panitia</span>
              @endif
              <span class="module-live-pill">
                <span class="pulse-dot"></span> Aktif 2026
              </span>
            </div>
          </div>

          <h3 class="module-card-name">PPDB ONLINE 2026</h3>
          <p class="module-card-subtitle">Penerimaan Peserta Didik Baru Terpadu</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Pendaftar</span>
              <span class="kpi-val">{{ $totalPendaftar }}</span>
              <span class="kpi-sub">+{{ $ppdbToday }} Pendaftar Hari Ini</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Menunggu Verifikasi</span>
              <span class="kpi-val">{{ $ppdbMenunggu }}</span>
              <span class="kpi-sub">Perlu Dicek Panitia</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Lolos / Diterima</span>
              <span class="kpi-val">{{ $ppdbDiterima }}</span>
              <span class="kpi-sub">Calon Siswa Resmi</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Ditolak / Perbaikan</span>
              <span class="kpi-val">{{ $ppdbDitolak }}</span>
              <span class="kpi-sub">Berkas Tidak Sesuai</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessPpdb)
            <a href="/admin/ppdb" class="btn-launch-primary">
              Kelola PPDB 2026
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/admin/ppdb?status=menunggu" class="btn-launch-secondary">
                Verifikasi Berkas
              </a>
              <a href="/ppdb" target="_blank" class="btn-launch-secondary">
                Form Publik
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Panitia PPDB, Waka Kesiswaan, & Pimpinan">
              Akses Terbatas (Khusus Panitia PPDB)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked">Verifikasi Terkunci</span>
              <a href="/ppdb" target="_blank" class="btn-launch-secondary">
                Form Publik
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 3: WEB PROFIL & HUMAS --}}
      <div class="module-card card-web {{ $canAccessWeb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo">
              <i class="bi bi-globe"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessWeb)
                <span class="access-badge allowed">Izin Aktif</span>
              @else
                <span class="access-badge locked">Butuh Humas</span>
              @endif
              <span class="module-live-pill">
                <span class="pulse-dot"></span> Publik
              </span>
            </div>
          </div>

          <h3 class="module-card-name">WEB PROFIL &amp; HUMAS</h3>
          <p class="module-card-subtitle">Etalase Publik &amp; Manajemen Publikasi</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Berita &amp; Rilis</span>
              <span class="kpi-val">{{ $totalBerita }}</span>
              <span class="kpi-sub">Artikel Terpublikasi</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Pengunjung Web</span>
              <span class="kpi-val">{{ number_format($todayVisitors) }}</span>
              <span class="kpi-sub">{{ number_format($todayUniqueVisitors) }} Unik Hari Ini</span>
            </div>
            <div class="kpi-item" style="grid-column: span 2; margin-top:6px;">
              <span class="kpi-label">Berita Terakhir</span>
              <span style="font-size:12.5px; font-weight:800; color:#000000; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; margin-top:2px;">
                {{ $beritaTerbaru ? $beritaTerbaru->judul : 'Belum ada rilis berita' }}
              </span>
              <span class="kpi-sub">{{ $beritaTerbaru ? \Carbon\Carbon::parse($beritaTerbaru->created_at)->diffForHumans() : '-' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessWeb)
            <a href="/admin/berita" class="btn-launch-primary">
              Kelola Berita &amp; Rilis
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/admin/banner" class="btn-launch-secondary">
                Banner Hero
              </a>
              @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.statistik.web') }}" class="btn-launch-secondary" style="font-weight:800; border-color:#000000;" title="Hanya Administrator yang dapat melihat grafik pengunjung">
                  Grafik Pengunjung
                </a>
              @else
                <a href="/" target="_blank" class="btn-launch-secondary">
                  Lihat Website
                </a>
              @endif
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Tim Humas, Webmaster, & Pimpinan">
              Akses Terbatas (Khusus Tim Humas)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked">Banner Terkunci</span>
              <a href="/" target="_blank" class="btn-launch-secondary">
                Lihat Website
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
          <span>Roadmap Modul DCC SMKN 1 AN</span>
        </h2>
        <span style="font-size:12px; color:#000000; font-weight:600;">
          Modul yang telah dipetakan dalam arsitektur digital sekolah dan siap diaktifkan secara bertahap.
        </span>
      </div>
      <span class="portal-section-badge">5 Modul Rancang Bangun</span>
    </div>

    <div class="portal-roadmap-grid">
      @foreach($futureModules as $mod)
        <div class="roadmap-card">
          <div class="roadmap-top">
            <div class="roadmap-icon-wrap">
              <i class="bi {{ $mod['icon'] }}"></i>
            </div>
            <span class="roadmap-badge">{{ $mod['badge'] }}</span>
          </div>
          <h4 class="roadmap-title">{{ $mod['name'] }}</h4>
          <div class="roadmap-sub">{{ $mod['subtitle'] }}</div>
          <p class="roadmap-desc">{{ $mod['description'] }}</p>
          <div class="roadmap-lead">
            <span>PIC: {{ $mod['lead'] }}</span>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Section 3: System Status & Quick Links --}}
    <div class="system-strip">
      <div class="system-strip-left">
        <div style="display:inline-flex; align-items:center; gap:7px; font-weight:800; color:#000000;">
          <span class="pulse-dot" style="background:#10b981;"></span> Infrastruktur Stabil
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
          Audit Log
        </a>
        <a href="/backup" class="system-strip-link" title="Manajemen Backup Database">
          Backup DB
        </a>
        <a href="/pengaturan-sekolah" class="system-strip-link" title="Profil Identitas Sekolah">
          Profil SMKN 1
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
      el.textContent = now.toLocaleString('id-ID', options) + ' WIB';
    }
    updatePortalClock();
    setInterval(updatePortalClock, 1000);
  </script>

</body>
</html>
