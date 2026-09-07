@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $countBerita = \App\Models\BeritaSekolah::count();
  $countBanner = \App\Models\WebsiteBanner::where('is_active', true)->count();
@endphp

{{-- Mobile Top Bar khusus Modul Humas & Web --}}
<div class="mobile-topbar no-print" style="background:#f2efe7; border-bottom:1px solid #c8dfdb;">
  <div style="display:flex; align-items:center; gap:10px;">
    <div style="width:34px; height:34px; border-radius:8px; background:#ffffff; border:1px solid #c8dfdb; display:flex; align-items:center; justify-content:center; padding:3px;">
      <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div>
      <span style="font-weight:900; font-size:16px; letter-spacing:-0.02em; display:block; line-height:1.1; color:#3368a0;">HUMAS &amp; WEB</span>
      <span style="font-size:10px; color:#000000; font-weight:700;">Publikasi &amp; Informasi</span>
    </div>
  </div>
  <div style="display:flex; align-items:center; gap:6px;">
    @include('partials.header_actions')
    <button type="button" class="mobile-hamburger-btn" id="mobileMenuToggle" onclick="window.toggleSmknSidebar(event)" aria-label="Buka Menu" style="color:#3368a0; border-color:#c8dfdb;">
      <i class="bi bi-list"></i>
    </button>
  </div>
</div>

{{-- Backdrop Overlay for Mobile Drawer --}}
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="window.closeSmknSidebar()"></div>

{{-- Sidebar Navigation: Khusus Workspace Modul Humas & Website Profil --}}
<aside class="sidebar" id="appSidebar">
  <div class="brand" style="margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid #c8dfdb; display:flex; justify-content:space-between; align-items:center;">
    <div class="brand-left" style="display:flex; align-items:center; gap:10px;">
      <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, rgba(51,104,160,0.15), rgba(102,163,191,0.15)); border:1px solid #c8dfdb; display:flex; align-items:center; justify-content:center; padding:4px;">
        <i class="bi bi-globe-americas" style="color:#3368a0; font-size:18px;"></i>
      </div>
      <div class="brand-text">
        <div style="font-weight:900; font-size:16.5px; letter-spacing:-0.02em; color:#3368a0;">HUMAS &amp; WEB</div>
        <div style="font-size:11px; color:#000000; font-weight:700;">Portal Publikasi SMKN 1 AN</div>
      </div>
    </div>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" onclick="window.closeSmknSidebar()" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- Tombol Navigasi Kembali ke DCC SMKN 1 AN --}}
  <div style="margin-bottom:16px;">
    <a href="{{ route('admin.portal') }}" class="btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:8px 12px; font-size:11.5px; font-weight:800; background:linear-gradient(135deg, #3368a0 0%, #1e293b 100%); color:#ffffff; border:1px solid #66a3bf; border-radius:var(--r-sm); text-decoration:none; box-shadow:0 3px 10px rgba(51,104,160,0.15); box-sizing:border-box;" title="Buka Data Control Center (DCC) SMKN 1 AN">
      <i class="bi bi-command" style="color:#c8dfdb; font-size:13.5px;"></i>
      <span>DCC SMKN 1 AN</span>
    </a>
  </div>

  {{-- 1. BERITA & ARTIKEL SEKOLAH --}}
  <div class="nav-group">
    <div class="nav-label" style="color:#000000; font-weight:800;">Manajemen Publikasi</div>
    
    <a href="{{ route('admin.berita.index') }}" class="nav-item {{ (request()->is('admin/berita') && !request()->is('admin/berita/log*')) ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-newspaper nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Kelola Berita &amp; Rilis</span>
      </div>
      <span class="nav-count-badge" style="background:#c8dfdb; color:#3368a0; border-color:#66a3bf; font-weight:800;">{{ $countBerita }}</span>
    </a>

    <a href="{{ route('admin.berita.create') }}" class="nav-item {{ request()->is('admin/berita/create') ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-pen-fill nav-icon" style="color:#66a3bf;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Tulis Berita Baru</span>
      </div>
    </a>

    <a href="{{ route('admin.berita.log') }}" class="nav-item {{ request()->is('admin/berita/log*') ? 'active' : '' }}" title="Riwayat &amp; Log Publikasi Konten Humas &amp; Website">
      <div class="nav-left-part">
        <i class="bi bi-journal-text nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Log Riwayat Web</span>
      </div>
    </a>
  </div>

  {{-- 2. TAMPILAN WEBSITE & BANNER --}}
  <div class="nav-group">
    <div class="nav-label" style="color:#000000; font-weight:800;">Etalase Website</div>

    <a href="{{ route('admin.banner.index') }}" class="nav-item {{ request()->is('admin/banner*') ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-images nav-icon" style="color:#66a3bf;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Hero Slider &amp; Banner</span>
      </div>
      <span class="nav-count-badge" style="background:#f2efe7; color:#3368a0; border-color:#c8dfdb; font-weight:800;">{{ $countBanner }}</span>
    </a>

    @if($isAdmin)
      <a href="{{ route('admin.statistik.web') }}" class="nav-item {{ request()->is('admin/statistik-web*') ? 'active' : '' }}" title="Statistik &amp; Grafik Pengunjung Website (Khusus Administrator)">
        <div class="nav-left-part">
          <i class="bi bi-graph-up-arrow nav-icon" style="color:#3368a0;"></i>
          <span class="nav-text" style="color:#000000; font-weight:700;">Grafik Pengunjung</span>
        </div>
        <span class="nav-count-badge" style="background:#3368a0; color:#ffffff; font-weight:800;">Admin</span>
      </a>
    @endif
  </div>

  {{-- 3. TAUTAN & MODUL LAIN --}}
  <div class="nav-group">
    <div class="nav-label" style="color:#000000; font-weight:800;">Akses Luar &amp; Modul Lain</div>

    <a href="{{ route('web.beranda') }}" target="_blank" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-box-arrow-up-right nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Lihat Website Utama</span>
      </div>
    </a>

    <a href="{{ route('web.berita.index') }}" target="_blank" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-collection-play-fill nav-icon" style="color:#66a3bf;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Kabar Sekolah Publik</span>
      </div>
    </a>

    <a href="/sirani" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-fingerprint nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Buka Modul SIRANI</span>
      </div>
    </a>

    <a href="/admin/ppdb" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-mortarboard-fill nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text" style="color:#000000; font-weight:700;">Buka Modul PPDB 2026</span>
  </div>

  <style>
    #appSidebar .nav-item.active {
      background: rgba(51, 104, 160, 0.08) !important;
      border-left: 3.5px solid #3368a0 !important;
    }
    #appSidebar .nav-item.active .nav-icon {
      color: #3368a0 !important;
    }
    #appSidebar .nav-item.active .nav-text {
      color: #000000 !important;
      font-weight: 900 !important;
    }
  </style>
</aside>

<script>
window.toggleSmknSidebar = function(e) {
  if (e) e.stopPropagation();
  const sidebar = document.getElementById('appSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (sidebar) sidebar.classList.toggle('open');
  if (backdrop) backdrop.classList.toggle('open');
};

window.closeSmknSidebar = function() {
  const sidebar = document.getElementById('appSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (sidebar) sidebar.classList.remove('open');
  if (backdrop) backdrop.classList.remove('open');
};
</script>
