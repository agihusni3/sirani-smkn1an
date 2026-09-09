

@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $isWakasis = $user ? $user->isWakaKesiswaan() : false;
  $isWakaKurikulum = $user ? $user->isWakaKurikulum() : false;
  $isWakaSarpras = $user ? $user->isWakaSarpras() : false;
  $isWakaHubin = $user ? $user->isWakaHubin() : false;
  $isKaprog = $user ? $user->isKaprog() : false;
  $isKepalaBengkel = $user ? $user->isKepalaBengkel() : false;
  $isPustakawan = $user ? $user->isPustakawan() : false;
  $isPimpinan = $user ? $user->isPimpinan() : false;
  $isBK = $user ? $user->isGuruBk() : false;
  $isWali = $user ? $user->isWaliKelas() : false;
  $isStafTu = $user ? $user->isStafTu() : false;
  $isGuruPiket = $user ? $user->isGuruPiket() : false;
  $isGuru = $user ? $user->isGuru() : false;
  $isHumas = $user ? $user->isHumas() : false;
  $isPanitiaPpdb = $user ? $user->isPanitiaPpdb() : false;
  $isPiketHariIni = ($user && !$isPimpinan && $user->guru) ? \App\Models\JadwalPiket::isGuruPiketHariIni($user->guru->id) : false;
@endphp

{{-- Mobile Top Bar with Integrated Actions (Theme + Account + Menu) --}}
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
  <div style="display:flex; align-items:center; gap:6px;">
    @include('partials.header_actions')
    <button type="button" class="mobile-hamburger-btn" id="mobileMenuToggle" onclick="window.toggleSmknSidebar(event)" aria-label="Buka Menu">
      <i class="bi bi-list"></i>
    </button>
  </div>
</div>

{{-- Backdrop Overlay for Mobile Drawer --}}
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="window.closeSmknSidebar()"></div>

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
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" onclick="window.closeSmknSidebar()" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- Tombol Navigasi Kembali ke DCC SMKN 1 AN --}}
  <div style="margin-bottom:16px;">
    <a href="{{ route('admin.portal') }}" class="btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:8px 12px; font-size:11.5px; font-weight:800; background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color:#ffffff; border:1px solid rgba(255,255,255,0.12); border-radius:var(--r-sm); text-decoration:none; box-shadow:0 3px 10px rgba(0,0,0,0.12); box-sizing:border-box;" title="Kembali ke Data Control Center (DCC) SMKN 1 AN">
      <i class="bi bi-command" style="color:#38bdf8; font-size:13.5px;"></i>
      <span>DCC SMKN 1 AN</span>
    </a>
  </div>

  {{-- 1. NAVIGASI UTAMA --}}
  <div class="nav-group">
    <div class="nav-label">Navigasi Utama</div>
    <a href="/sirani" class="nav-item {{ (request()->is('sirani*') || request()->is('dashboard*')) ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-grid-1x2-fill nav-icon"></i>
        <span class="nav-text">Dasbor Utama</span>
      </div>
    </a>
    @if($user && $user->guru)
      <a href="javascript:void(0)" onclick="window.openModalKartuGuruSaya()" class="nav-item" style="color:var(--text); font-weight:700; cursor:pointer;" title="Buka Kartu & QR Presensi Guru">
        <div class="nav-left-part">
          <i class="bi bi-qr-code-scan nav-icon" style="color:#0284c7;"></i>
          <span class="nav-text">QR Presensi</span>
        </div>
        <span class="nav-count-badge" style="background:#f0f9ff; color:#0284c7; border-color:#bae6fd;">QR</span>
      </a>
    @endif
    @php
      $canAccessSmartGate = $isAdmin || $isStafTu || $isGuruPiket || $isPiketHariIni;
    @endphp
    @if($canAccessSmartGate)
      <a href="/smart-gate" class="nav-item {{ request()->is('smart-gate*') || request()->is('kios-rfid*') || request()->is('rfid') ? 'active' : '' }}" target="_blank">
        <div class="nav-left-part">
          <i class="bi bi-upc-scan nav-icon"></i>
          <span class="nav-text">Smart Gate Presensi</span>
        </div>
        <span class="nav-count-badge">Live</span>
      </a>
    @endif

    @if(!$isHumas && !$isPanitiaPpdb)
      {{-- Monitoring Absen Mandiri (Siswa & Orang Tua) --}}
      <a href="/cek-presensi" class="nav-item {{ request()->is('cek-presensi*') || request()->is('portal-siswa*') || request()->is('monitoring-absen*') ? 'active' : '' }}" target="_blank" title="Buka Monitoring Absen Mandiri (Cek Presensi Siswa & Orang Tua)">
        <div class="nav-left-part">
          <i class="bi bi-person-check nav-icon" style="color:#0284c7;"></i>
          <span class="nav-text">Monitoring Absen Mandiri</span>
        </div>
        <span class="nav-count-badge" style="background:#f0f9ff; color:#0284c7; border-color:#bae6fd;">Mandiri</span>
      </a>
    @endif
  </div>

  {{-- 2. OPERASIONAL HARIAN --}}
  @if($isAdmin || $isGuruPiket || $isKepsek || $isWakasis || $isWakaKurikulum || $isBK || $isWali || $isStafTu)
    <div class="nav-group">
      <div class="nav-label">Operasional Harian</div>
      @if($isAdmin || $isWakasis || $isGuruPiket)
        <a href="/piket" class="nav-item {{ request()->is('piket*') ? 'active' : '' }}" title="Akses Meja Piket & Absensi Harian">
          <div class="nav-left-part">
            <i class="bi bi-person-badge-fill nav-icon"></i>
            <span class="nav-text">Piket Harian</span>
          </div>
          @if(!$isAdmin && !$isWakasis && $isGuruPiket)
            <span class="nav-count-badge" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0; font-size:10px; font-weight:700;">Tugas Hari Ini</span>
          @endif
        </a>
      @endif
      @if($isAdmin || $isGuruPiket)
        <a href="/izin-siswa" class="nav-item {{ request()->is('izin*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-file-earmark-check-fill nav-icon"></i>
            <span class="nav-text">Perizinan Siswa</span>
          </div>
        </a>
      @endif
      @if($isAdmin || $isPimpinan || $isStafTu)
        <a href="/jadwal-piket" class="nav-item {{ request()->is('jadwal-piket*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-calendar2-check-fill nav-icon"></i>
            <span class="nav-text">Jadwal Petugas Piket</span>
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
            $sidebarDisiplinQuery = \App\Models\KasusDisiplin::where('is_active', true)->where('status_tahap', '!=', 'selesai_pembinaan');
            if ($isAdmin) {
                $sidebarDisiplinCount = (clone $sidebarDisiplinQuery)->count();
            } elseif ($isKepsek) {
                $sidebarDisiplinCount = (clone $sidebarDisiplinQuery)->where('status_tahap', 'tahap_4_kepsek')->count();
            } elseif ($isWakasis) {
                $sidebarDisiplinCount = (clone $sidebarDisiplinQuery)->where('status_tahap', 'tahap_3_wakasis')->count();
            } elseif ($isBK) {
                $sidebarDisiplinCount = (clone $sidebarDisiplinQuery)->where('status_tahap', 'tahap_2_bk')->count();
            } elseif ($isWali) {
                $sidebarDisiplinCount = (clone $sidebarDisiplinQuery)
                    ->forUser($user)
                    ->where('status_tahap', 'tahap_1_wali_kelas')
                    ->count();
            } else {
                $sidebarDisiplinCount = 0;
            }
          @endphp
          @if($sidebarDisiplinCount > 0)
            <span class="nav-count-badge">{{ $sidebarDisiplinCount }}</span>
          @endif
        </a>
      @endif

      @php
        $canAccessPtk = $isAdmin || $isKepsek || $isWakasis || $isWakaKurikulum || $isStafTu || ($user && in_array($user->role, ['waka_sarpras', 'waka_hubin']));
      @endphp
      @if($canAccessPtk)
        <a href="/guru" class="nav-item {{ request()->is('guru*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-person-badge-fill nav-icon" style="color:#0284c7;"></i>
            <span class="nav-text">Data PTK (Guru &amp; Staf)</span>
          </div>
          <span class="nav-count-badge" style="background:#f0f9ff; color:#0284c7; border-color:#bae6fd;">PTK</span>
        </a>
      @endif
    </div>
  @endif

  @if(!$isHumas && !$isPanitiaPpdb)
    {{-- 3. REKAPITULASI & LAPORAN --}}
    <div class="nav-group">
      <div class="nav-label">Rekapitulasi &amp; Laporan</div>
      <a href="/laporan" class="nav-item {{ request()->is('laporan*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-bar-chart-line-fill nav-icon"></i>
          <span class="nav-text">Rekap Presensi</span>
        </div>
      </a>
      @if($isAdmin || $isPimpinan || $isBK || $isStafTu || $isGuruPiket || $isPiketHariIni)
        <a href="/peringkat" class="nav-item {{ request()->is('peringkat*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-trophy-fill nav-icon"></i>
            <span class="nav-text">Peringkat Kehadiran</span>
          </div>
        </a>
      @endif
      @if($isAdmin || $isWakasis || $isWakaKurikulum || $isBK || $isWali || $isGuruPiket || $isPiketHariIni)
        <a href="/notifikasi" class="nav-item {{ request()->is('notifikasi*') || request()->is('pengumuman*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-bell-fill nav-icon"></i>
            <span class="nav-text">Notifikasi &amp; Broadcast</span>
          </div>
        </a>
      @endif
    </div>

    {{-- 4. KESISWAAN BINAAN (Khusus Wali Kelas) --}}
    @if($isWali && !$isAdmin && !$isStafTu)
      <div class="nav-group">
        <div class="nav-label">Kelas Binaan</div>
        <a href="/siswa?from=sirani" class="nav-item {{ request()->is('siswa*') ? 'active' : '' }}" title="Data Murid di Kelas Binaan Anda">
          <div class="nav-left-part">
            <i class="bi bi-people-fill nav-icon" style="color:#059669;"></i>
            <span class="nav-text">Data Siswa Binaan</span>
          </div>
          <span class="nav-count-badge" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0; font-size:10px;">Kelas</span>
        </a>
      </div>
    @endif

    {{-- 5. JADWAL & KALENDER --}}
    <div class="nav-group">
      <div class="nav-label">Jadwal &amp; Kalender</div>
      @if($isAdmin || $isPimpinan || $isGuruPiket || $isPiketHariIni || $isStafTu || $isGuru)
        <a href="/jadwal-sekolah" class="nav-item {{ request()->is('jadwal-sekolah*') ? 'active' : '' }}">
          <div class="nav-left-part">
            <i class="bi bi-clock-history nav-icon"></i>
            <span class="nav-text">Jam Sekolah &amp; Sesi</span>
          </div>
        </a>
      @endif
      <a href="/hari-libur" class="nav-item {{ request()->is('hari-libur*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-calendar2-week-fill nav-icon"></i>
          <span class="nav-text">Kalender Akademik</span>
        </div>
      </a>
    </div>
  @endif

  {{-- 6. PENGAWASAN & AUDIT --}}
  @if($isAdmin || $isPimpinan)
    <div class="nav-group">
      <div class="nav-label">Pengawasan</div>
      <a href="/audit" class="nav-item {{ request()->is('audit*') ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-shield-lock-fill nav-icon"></i>
          <span class="nav-text">Audit Trail Presensi</span>
        </div>
      </a>
    </div>
  {{-- 6b. PORTAL MANDIRI PTK (Biodata & Berkas Saya) --}}
  @if($user && ($user->guru_id || $isAdmin || $isGuru))
    <div class="nav-group">
      <div class="nav-label">Ruang Pribadi PTK</div>
      <a href="{{ route('ptk.profil-saya') }}" class="nav-item {{ request()->is('ptk*') ? 'active' : '' }}" title="Biodata &amp; Lemari Berkas Digital Saya">
        <div class="nav-left-part">
          <i class="bi bi-person-vcard-fill nav-icon" style="color:#2563EB;"></i>
          <span class="nav-text">Biodata &amp; Berkas Saya</span>
        </div>
        <span class="nav-count-badge" style="background:#eff6ff; color:#2563eb; border-color:#bfdbfe; font-size:10px;">E-Arsip</span>
      </a>
    </div>
  @endif

  {{-- 7. PINTASAN KE MODUL SITUAN (Tata Usaha & Data Pokok) --}}
  @if($user && $user->canAccessSituan())
    <div class="nav-group" style="margin-top:auto; padding-top:14px; border-top:1px solid var(--border);">
      <div class="nav-label">Administrasi Data Induk</div>
      <a href="{{ route('situan.index') }}" class="nav-item" title="Buka Modul SITUAN (Tata Usaha &amp; Data Pokok)">
        <div class="nav-left-part">
          <i class="bi bi-buildings-fill nav-icon" style="color:#0284c7;"></i>
          <span class="nav-text">SITUAN Tata Usaha</span>
        </div>
        <i class="bi bi-arrow-up-right" style="font-size:11px; color:var(--text-3);"></i>
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

  {{-- Tab 2 Sesuai Izin Role --}}
  @if($isAdmin || $isGuruPiket || $isPiketHariIni)
    <a href="/izin-siswa" class="mobile-nav-link {{ request()->is('izin*') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-check-fill"></i>
      <span>Perizinan</span>
    </a>
  @elseif($isBK || $isWali || $isWakasis)
    <a href="/disiplin" class="mobile-nav-link {{ request()->is('disiplin*') ? 'active' : '' }}">
      <i class="bi bi-journals"></i>
      <span>Disiplin</span>
    </a>
  @else
    <a href="/siswa" class="mobile-nav-link {{ request()->is('siswa*') ? 'active' : '' }}">
      <i class="bi bi-people-fill"></i>
      <span>Siswa</span>
    </a>
  @endif

  {{-- Tab 3: Rekap Presensi --}}
  <a href="/laporan" class="mobile-nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-line-fill"></i>
    <span>Rekap</span>
  </a>

  {{-- Tab 4 Sesuai Izin Role --}}
  @if($isAdmin || $isGuruPiket || $isPiketHariIni)
    <a href="/piket" class="mobile-nav-link {{ request()->is('piket*') ? 'active' : '' }}">
      <i class="bi bi-person-badge-fill"></i>
      <span>Piket</span>
    </a>
  @elseif($isBK || $isWali || $isWakasis || $isKepsek)
    <a href="/disiplin" class="mobile-nav-link {{ request()->is('disiplin*') ? 'active' : '' }}">
      <i class="bi bi-journals"></i>
      <span>Disiplin</span>
    </a>
  @elseif($isWakaKurikulum)
    <a href="/jadwal-piket" class="mobile-nav-link {{ request()->is('jadwal-piket*') ? 'active' : '' }}">
      <i class="bi bi-calendar2-check-fill"></i>
      <span>Piket</span>
    </a>
  @elseif($isStafTu)
    <a href="/guru" class="mobile-nav-link {{ request()->is('guru*') ? 'active' : '' }}">
      <i class="bi bi-person-badge-fill"></i>
      <span>Guru</span>
    </a>
  @else
    <a href="/notifikasi" class="mobile-nav-link {{ request()->is('notifikasi*') || request()->is('pengumuman*') ? 'active' : '' }}">
      <i class="bi bi-bell-fill"></i>
      <span>Notifikasi</span>
    </a>
  @endif

  {{-- Tab 5: Lainnya --}}
  <button type="button" class="mobile-nav-link" id="mobileMenuToggleBottom" onclick="window.toggleSmknSidebar(event)" aria-label="Buka Menu Lengkap">
    <i class="bi bi-list"></i>
    <span>Lainnya</span>
  </button>
</nav>

<script>
  window.openSmknSidebar = function(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) {
      sidebar.classList.add('mobile-open');
    }
    if (backdrop) {
      backdrop.classList.add('active');
    }
    document.body.style.overflow = 'hidden';
  };

  window.closeSmknSidebar = function(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) {
      sidebar.classList.remove('mobile-open');
    }
    if (backdrop) {
      backdrop.classList.remove('active');
    }
    document.body.style.overflow = '';
  };

  window.toggleSmknSidebar = function(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const sidebar = document.getElementById('appSidebar');
    if (sidebar && sidebar.classList.contains('mobile-open')) {
      window.closeSmknSidebar(e);
    } else {
      window.openSmknSidebar(e);
    }
  };

  window.addEventListener('resize', function() {
    if (window.innerWidth > 1024) {
      window.closeSmknSidebar();
    }
  });

  // Pastikan klik pada link navigasi di dalam drawer otomatis menutup drawer di mobile
  document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('#appSidebar .nav-item');
    navItems.forEach(item => {
      item.addEventListener('click', function() {
        if (window.innerWidth <= 1024) {
          window.closeSmknSidebar();
        }
      });
    });
  });
</script>
