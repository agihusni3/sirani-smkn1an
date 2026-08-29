<style>
  /* ── Modern Elegant Sidebar Design Tokens ── */
  .nav-group {
    margin-bottom: 22px;
  }
  .nav-label {
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-3);
    margin-bottom: 8px;
    padding: 0 12px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: var(--r-sm);
    font-size: 13px;
    font-weight: 600;
    color: var(--text-2);
    text-decoration: none;
    transition: all 0.15s ease;
    margin-bottom: 2px;
  }
  .nav-item:hover {
    background: var(--surface);
    color: var(--text);
  }
  .nav-item.active {
    background: #000000 !important;
    color: #FFFFFF !important;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
  }
  .nav-item.active .nav-icon,
  .nav-item.active i {
    color: #FFFFFF !important;
  }
  .nav-item.active .nav-text {
    color: #FFFFFF !important;
  }
  .nav-left-part {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
  }
  .nav-icon {
    font-size: 16px;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s ease;
  }
  .nav-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .nav-count-badge {
    font-size: 10.5px;
    font-family: var(--font-mono);
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 6px;
    background: rgba(0, 0, 0, 0.06);
    color: #000000;
    border: 1px solid rgba(0, 0, 0, 0.12);
  }
  .nav-item.active .nav-count-badge {
    background: #FFFFFF !important;
    color: #000000 !important;
    border-color: #FFFFFF !important;
  }

  /* ── Mobile Bottom Navigation Bar ── */
  .mobile-bottom-nav {
    display: none;
  }
  @media (max-width: 1024px) {
    .mobile-bottom-nav {
      display: flex;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      height: 60px;
      background: var(--bg-2);
      border-top: 1px solid var(--border-2);
      z-index: 1030;
      align-items: center;
      justify-content: space-around;
      padding-bottom: env(safe-area-inset-bottom, 0px);
      box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
    }
    .mobile-nav-link {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      flex: 1;
      height: 100%;
      color: var(--text-2);
      text-decoration: none;
      font-size: 10.5px;
      font-weight: 700;
      gap: 3px;
      transition: color 0.15s ease;
      user-select: none;
      background: none;
      border: none;
      cursor: pointer;
    }
    .mobile-nav-link i {
      font-size: 18px;
      line-height: 1;
    }
    .mobile-nav-link.active, .mobile-nav-link:active {
      color: #000000;
      font-weight: 900;
    }
    /* Add bottom padding to main content to not overlap bottom bar */
    .main-content {
      padding-bottom: 80px !important;
    }
  }
</style>

@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $isWakasis = $user ? $user->isWakaKesiswaan() : false;
  $isWakaKurikulum = $user ? $user->isWakaKurikulum() : false;
  $isBK = $user ? $user->isGuruBk() : false;
  $isWali = $user ? $user->isWaliKelas() : false;
  $isStafTu = $user ? $user->isStafTu() : false;
  $isGuruPiket = $user ? $user->isGuruPiket() : false;
  $isGuru = $user ? $user->isGuru() : false;
  $isPiketHariIni = $user && $user->guru ? \App\Models\JadwalPiket::isGuruPiketHariIni($user->guru->id) : false;
@endphp

{{-- Mobile Top Bar --}}
<div class="mobile-topbar no-print">
  <div style="display:flex; align-items:center; gap:10px;">
    <div style="width:34px; height:34px; border-radius:8px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; padding:3px;">
      <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div>
      <span style="font-weight:900; font-size:16px; letter-spacing:-0.02em; display:block; line-height:1.1;">SIRANI</span>
      <span style="font-size:10px; color:var(--text-3); font-weight:600;">SMKN 1 Air Naningan</span>
    </div>
  </div>
  <button type="button" class="mobile-hamburger-btn" id="mobileMenuToggle" aria-label="Buka Menu">
    <i class="bi bi-list"></i>
  </button>
</div>

{{-- Backdrop Overlay for Mobile Drawer --}}
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

{{-- Sidebar Navigation: Structured & Role-Based with Icons --}}
<aside class="sidebar" id="appSidebar">
  <div class="brand" style="margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
    <div class="brand-left" style="display:flex; align-items:center; gap:10px;">
      <div style="width:36px; height:36px; border-radius:8px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; padding:4px;">
        <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
      </div>
      <div class="brand-text">
        <div style="font-weight:900; font-size:17px; letter-spacing:-0.03em;">SIRANI</div>
        <div style="font-size:11px; color:var(--text-3); font-weight:600;">{{ $user ? $user->role_display_name : 'SMKN 1 Air Naningan' }}</div>
      </div>
    </div>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- 1. UTAMA --}}
  <div class="nav-group">
    <div class="nav-label">Navigasi Utama</div>
    <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-grid-1x2-fill nav-icon"></i>
        <span class="nav-text">Dasbor Utama</span>
      </div>
    </a>
    @if($isAdmin || $isWakasis || $isGuruPiket || $isPiketHariIni)
      <a href="/piket" class="nav-item {{ request()->is('piket*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-person-badge-fill nav-icon"></i>
          <span class="nav-text">Meja Piket Harian</span>
        </div>
      </a>
    @endif
    @if($isAdmin)
      <a href="/" class="nav-item {{ request()->is('kios-wajah*') || request()->is('/') ? 'active' : '' }}" target="_blank">
        <div class="nav-left-part">
          <i class="bi bi-camera-video-fill nav-icon"></i>
          <span class="nav-text">Smart Gate Kiosk</span>
        </div>
        <span class="nav-count-badge">Live</span>
      </a>
      <a href="/cek-presensi" class="nav-item {{ request()->is('cek-presensi*') ? 'active' : '' }}" target="_blank">
        <div class="nav-left-part">
          <i class="bi bi-search nav-icon"></i>
          <span class="nav-text">Portal Siswa &amp; Ortu</span>
        </div>
      </a>
    @endif
  </div>

  {{-- 2. PRESENSI & KESISWAAN --}}
  @if($isAdmin || $isKepsek || $isWakasis || $isWakaKurikulum || $isBK || $isWali || $isStafTu || $isGuruPiket || $isPiketHariIni)
    <div class="nav-group">
      <div class="nav-label">Presensi &amp; Kesiswaan</div>
      @if($isAdmin || $isKepsek || $isWakasis || $isBK || $isWali || $isStafTu || $isGuruPiket)
        <a href="/laporan" class="nav-item {{ request()->is('laporan*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-bar-chart-line-fill nav-icon"></i>
            <span class="nav-text">Rekap Presensi</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isGuruPiket || $isPiketHariIni)
        <a href="/izin-siswa" class="nav-item {{ request()->is('izin*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-file-earmark-check-fill nav-icon"></i>
            <span class="nav-text">Perizinan Siswa</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isKepsek || $isWakasis || $isBK || $isWali)
        <a href="/disiplin" class="nav-item {{ request()->is('disiplin*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-journals nav-icon"></i>
            <span class="nav-text">Buku Kasus Disiplin</span>
          </div>
          @php
            $sidebarDisiplinCount = \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', '!=', 'selesai_pembinaan')->forUser($user)->count();
          @endphp
          @if($sidebarDisiplinCount > 0)
            <span class="nav-count-badge">{{ $sidebarDisiplinCount }}</span>
          @endif
        </a>
      @endif

      @if(!$isWali && !$isGuru)
        <a href="/peringkat" class="nav-item {{ request()->is('peringkat*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-trophy-fill nav-icon"></i>
            <span class="nav-text">Peringkat Kehadiran</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isWakasis || $isBK)
        <a href="/surat" class="nav-item {{ request()->is('surat*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-envelope-paper-fill nav-icon"></i>
            <span class="nav-text">Surat Panggilan Ortu</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isKepsek || $isWakasis || $isWakaKurikulum || $isWali)
        <a href="/notifikasi" class="nav-item {{ request()->is('notifikasi*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-bell-fill nav-icon"></i>
            <span class="nav-text">Pusat Notifikasi WA</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isKepsek || $isWakasis || $isWakaKurikulum)
        <a href="/pengumuman" class="nav-item {{ request()->is('pengumuman*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-megaphone-fill nav-icon"></i>
            <span class="nav-text">Pengumuman Sekolah</span>
          </div>
        </a>
      @endif
    </div>
  @endif

  {{-- 3. JADWAL & OPERASIONAL --}}
  @if($isAdmin || $isKepsek || $isWakasis || $isWakaKurikulum || $isStafTu || $isGuru)
    <div class="nav-group">
      <div class="nav-label">Jadwal &amp; Operasional</div>
      <a href="/jadwal-sekolah" class="nav-item {{ request()->is('jadwal-sekolah*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-clock-history nav-icon"></i>
          <span class="nav-text">Jam Sekolah &amp; Sesi</span>
        </div>
      </a>
      <a href="/jadwal-piket" class="nav-item {{ request()->is('jadwal-piket*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-calendar2-check-fill nav-icon"></i>
          <span class="nav-text">Jadwal Petugas Piket</span>
        </div>
      </a>
      <a href="/hari-libur" class="nav-item {{ request()->is('hari-libur*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-calendar2-week-fill nav-icon"></i>
          <span class="nav-text">Kalender Hari Libur</span>
        </div>
      </a>
    </div>
  @endif

  {{-- 4. MASTER DATA --}}
  @if($isAdmin || $isKepsek || $isWakasis || $isBK || $isWali || $isStafTu)
    <div class="nav-group">
      <div class="nav-label">Master Data</div>
      <a href="/siswa" class="nav-item {{ request()->is('siswa*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-people-fill nav-icon"></i>
          <span class="nav-text">Data Siswa</span>
        </div>
      </a>
      @if($isAdmin || $isWakasis || $isStafTu || $isWakaKurikulum)
        <a href="/siklus-siswa" class="nav-item {{ request()->is('siklus-siswa*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-arrow-repeat nav-icon"></i>
            <span class="nav-text">Siklus Akademik Siswa</span>
          </div>
        </a>
      @endif
      @if($isAdmin || $isStafTu)
        <a href="/guru" class="nav-item {{ request()->is('guru*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-person-badge-fill nav-icon"></i>
            <span class="nav-text">Data Guru &amp; Pegawai</span>
          </div>
        </a>
      @endif
      @if(!$isWali)
        <a href="/rombel" class="nav-item {{ request()->is('rombel*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-building nav-icon"></i>
            <span class="nav-text">Rombongan Belajar</span>
          </div>
        </a>
      @endif
    </div>
  @endif

  {{-- 5. SISTEM --}}
  @if($isAdmin)
    <div class="nav-group">
      <div class="nav-label">Sistem &amp; Pengaturan</div>
      <a href="/pengaturan-sekolah" class="nav-item {{ request()->is('pengaturan-sekolah*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-bank2 nav-icon"></i>
          <span class="nav-text">Profil &amp; Kop Surat</span>
        </div>
      </a>
      <a href="/backup" class="nav-item {{ request()->is('backup*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-database-down nav-icon"></i>
          <span class="nav-text">Pencadangan Data</span>
        </div>
      </a>
      <a href="/audit" class="nav-item {{ request()->is('audit*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-shield-lock-fill nav-icon"></i>
          <span class="nav-text">Audit Trail</span>
        </div>
      </a>
    </div>
  @endif
</aside>

{{-- Mobile Bottom Bar Navigation with Clean Icons --}}
<nav class="mobile-bottom-nav no-print" aria-label="Navigasi Bawah">
  <a href="/dashboard" class="mobile-nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid-1x2-fill"></i>
    <span>Dasbor</span>
  </a>
  @if($isAdmin || $isWakasis || $isGuruPiket || $isPiketHariIni)
    <a href="/piket" class="mobile-nav-link {{ request()->is('piket*') ? 'active' : '' }}">
      <i class="bi bi-person-badge-fill"></i>
      <span>Piket</span>
    </a>
  @endif
  <a href="/laporan" class="mobile-nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-line-fill"></i>
    <span>Rekap</span>
  </a>
  <a href="/kios-wajah" target="_blank" class="mobile-nav-link {{ request()->is('kios-wajah*') || request()->is('/') ? 'active' : '' }}">
    <i class="bi bi-camera-video-fill"></i>
    <span>Kiosk</span>
  </a>
  <button type="button" class="mobile-nav-link" onclick="document.getElementById('mobileMenuToggle')?.click()" aria-label="Buka Menu Lengkap">
    <i class="bi bi-list"></i>
    <span>Lainnya</span>
  </button>
</nav>

<script>
  (function() {
    function initSidebar() {
      const toggleBtn = document.getElementById('mobileMenuToggle');
      const closeBtn = document.getElementById('sidebarCloseBtn');
      const sidebar = document.getElementById('appSidebar');
      const backdrop = document.getElementById('sidebarBackdrop');

      function openSidebar() {
        if (sidebar) sidebar.classList.add('mobile-open');
        if (backdrop) backdrop.classList.add('active');
      }

      function closeSidebar() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (backdrop) backdrop.classList.remove('active');
      }

      if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
      if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
      if (backdrop) backdrop.addEventListener('click', closeSidebar);

      window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
          closeSidebar();
        }
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
      initSidebar();
    }
  })();
</script>
