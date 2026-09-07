<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Control Center — SMKN 1 AN</title>
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
        <div class="portal-brand-subtitle">Data Control Center (Pusat Kontrol Data) · SMKN 1 Air Naningan</div>
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
          Pusat kontrol dan kendali data ekosistem digital SMKN 1 Air Naningan. Akses data pokok kelembagaan, presensi gerbang, seleksi PPDB, dan publikasi institusi dalam satu sistem terpadu.
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
          <div class="cockpit-status-sub">Data Control Center · Online</div>
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
              <span class="kpi-label">Peserta Didik (Siswa)</span>
              <span class="kpi-val">{{ $totalSiswa }}</span>
              <span class="kpi-sub">Siswa Aktif</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Pendidik &amp; Tendik</span>
              <span class="kpi-val">{{ $totalGuru }}</span>
              <span class="kpi-sub">Guru &amp; Pegawai</span>
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
          </div>

          <h3 class="module-card-name">SIRANI</h3>
          <p class="module-card-subtitle">Sistem Informasi Responsif Absensi</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Kehadiran Siswa</span>
              <span class="kpi-val">{{ $persenSiswaHadir }}%</span>
              <span class="kpi-sub">{{ $siswaHadirToday }} / {{ $totalSiswa }} Hadir</span>
            </div>
            <div class="kpi-item">
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

      {{-- Card 3: PPDB ONLINE 2026 --}}
      <div class="module-card card-ppdb {{ $canAccessPpdb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo">
              <i class="bi bi-mortarboard"></i>
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
              <span class="kpi-label">Lolos / Diterima</span>
              <span class="kpi-val" style="color:#10b981;">{{ $ppdbDiterima }}</span>
              <span class="kpi-sub">Calon Siswa Resmi</span>
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

      {{-- Card 4: WEB PROFIL & HUMAS --}}
      <div class="module-card card-web {{ $canAccessWeb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo">
              <i class="bi bi-globe"></i>
            </div>
          </div>

          <h3 class="module-card-name">WEB PROFIL &amp; HUMAS</h3>
          <p class="module-card-subtitle">Etalase Publik &amp; Manajemen Publikasi</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Berita</span>
              <span class="kpi-val">{{ $totalBerita }}</span>
              <span class="kpi-sub">Artikel Terpublikasi</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Pengunjung Web</span>
              <span class="kpi-val">{{ number_format($todayVisitors) }}</span>
              <span class="kpi-sub">{{ number_format($todayUniqueVisitors) }} Unik Hari Ini</span>
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

    {{-- Clean Minimalist Institutional Footer --}}
    <footer class="portal-footer">
      <div class="portal-footer-text">
        <span>&copy; {{ date('Y') }} SMKN 1 Air Naningan</span>
        <span class="portal-footer-dot">&middot;</span>
        <span>Data Control Center (DCC)</span>
        <span class="portal-footer-dot">&middot;</span>
        <span>Pusat Kontrol Data Ekosistem Terpadu</span>
      </div>
    </footer>

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
