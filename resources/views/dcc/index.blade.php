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

          <p class="module-card-desc">
            Kelola administrasi kelembagaan, sinkronisasi Dapodik kesiswaan dan PTK, pengarsipan e-persuratan dinas, serta loket surat keterangan mandiri siswa.
          </p>
        </div>

        <div class="module-actions">
          @if($canAccessSituan)
            <a href="{{ route('situan.index') }}" class="btn-launch-primary">
              <span>Buka Modul SITUAN</span>
              <i class="bi bi-arrow-right-short" style="font-size: 18px;"></i>
            </a>
          @else
            <div class="btn-module-locked">
              <i class="bi bi-lock-fill"></i>
              <span>Akses Terbatas Modul TU</span>
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

          <p class="module-card-desc">
            Otomasi presensi cerdas gerbang Smart Gate RFID dan barcode, monitoring kehadiran real-time GTK &amp; siswa, serta rekapitulasi laporan absensi digital.
          </p>
        </div>

        <div class="module-actions">
          @if($canAccessSirani)
            <a href="/sirani" class="btn-launch-primary">
              <span>Buka Modul SIRANI</span>
              <i class="bi bi-arrow-right-short" style="font-size: 18px;"></i>
            </a>
          @else
            <div class="btn-module-locked">
              <i class="bi bi-lock-fill"></i>
              <span>Akses Terbatas Modul Presensi</span>
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

          <p class="module-card-desc">
            Portal terpadu pendaftaran calon siswa baru, verifikasi berkas formulir, pelaksanaan ujian seleksi CBT daring, hingga publikasi hasil kelulusan.
          </p>
        </div>

        <div class="module-actions">
          @if($canAccessPpdb)
            <a href="/admin/ppdb" class="btn-launch-primary">
              <span>Buka Modul PPDB</span>
              <i class="bi bi-arrow-right-short" style="font-size: 18px;"></i>
            </a>
          @else
            <div class="btn-module-locked">
              <i class="bi bi-lock-fill"></i>
              <span>Akses Terbatas Panitia PPDB</span>
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

          <p class="module-card-desc">
            Etalase representasi profil resmi sekolah, publikasi rilis berita dan agenda kegiatan, galeri prestasi siswa, serta keterbukaan informasi publik.
          </p>
        </div>

        <div class="module-actions">
          @if($canAccessWeb)
            <a href="/admin/berita" class="btn-launch-primary">
              <span>Buka Modul Web &amp; Humas</span>
              <i class="bi bi-arrow-right-short" style="font-size: 18px;"></i>
            </a>
          @else
            <a href="/" target="_blank" class="btn-launch-primary" title="Buka Portal Website Utama Sekolah">
              <i class="bi bi-globe" style="font-size: 13px;"></i>
              <span>Kunjungi Website Utama</span>
              <i class="bi bi-box-arrow-up-right" style="font-size: 11px;"></i>
            </a>
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
