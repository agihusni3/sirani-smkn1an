@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $countBerita = \App\Models\BeritaSekolah::count();
  $countBanner = \App\Models\WebsiteBanner::where('is_active', true)->count();
@endphp

{{-- Mobile Top Bar khusus Modul Humas & Web --}}
<div class="mobile-topbar no-print">
  <div style="display:flex; align-items:center; gap:10px;">
    <div style="width:34px; height:34px; border-radius:8px; background:var(--bg-2, #ffffff); border:1px solid var(--border-2, #c8dfdb); display:flex; align-items:center; justify-content:center; padding:3px;">
      <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div>
      <span style="font-weight:900; font-size:16px; letter-spacing:-0.02em; display:block; line-height:1.1; color:#3368a0;">HUMAS &amp; WEB</span>
      <span style="font-size:10px; color:var(--text-3, #64748b); font-weight:700;">Publikasi &amp; Informasi</span>
    </div>
  </div>
  <div style="display:flex; align-items:center; gap:6px;">
    @include('partials.header_actions')
    <button type="button" class="mobile-hamburger-btn" id="mobileMenuToggle" onclick="window.toggleSmknSidebar(event)" aria-label="Buka Menu" style="color:#3368a0; border-color:var(--border-2, #c8dfdb);">
      <i class="bi bi-list"></i>
    </button>
  </div>
</div>

{{-- Backdrop Overlay for Mobile Drawer --}}
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="window.closeSmknSidebar()"></div>

{{-- Sidebar Navigation: Khusus Workspace Modul Humas & Website Profil --}}
<aside class="sidebar" id="appSidebar">
  <div class="brand" style="margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid var(--border-2, #c8dfdb); display:flex; justify-content:space-between; align-items:center;">
    <div class="brand-left" style="display:flex; align-items:center; gap:10px;">
      <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, rgba(51,104,160,0.15), rgba(102,163,191,0.15)); border:1px solid var(--border-2, #c8dfdb); display:flex; align-items:center; justify-content:center; padding:4px;">
        <i class="bi bi-globe-americas" style="color:#3368a0; font-size:18px;"></i>
      </div>
      <div class="brand-text">
        <div style="font-weight:900; font-size:16.5px; letter-spacing:-0.02em; color:#3368a0;">HUMAS &amp; WEB</div>
        <div style="font-size:11px; color:var(--text-3, #64748b); font-weight:700;">Portal Publikasi SMKN 1 AN</div>
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
    <div class="nav-label">Manajemen Publikasi</div>
    
    <a href="{{ route('admin.berita.index') }}" class="nav-item {{ (request()->is('admin/berita') && !request()->is('admin/berita/log*')) ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-newspaper nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text">Kelola Berita &amp; Rilis</span>
      </div>
      <span class="nav-count-badge">{{ $countBerita }}</span>
    </a>

    <a href="{{ route('admin.berita.create') }}" class="nav-item {{ request()->is('admin/berita/create') ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-pen-fill nav-icon" style="color:#66a3bf;"></i>
        <span class="nav-text">Tulis Berita Baru</span>
      </div>
    </a>

    <a href="{{ route('admin.berita.log') }}" class="nav-item {{ request()->is('admin/berita/log*') ? 'active' : '' }}" title="Riwayat &amp; Log Publikasi Konten Humas &amp; Website">
      <div class="nav-left-part">
        <i class="bi bi-journal-text nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text">Log Riwayat Web</span>
      </div>
    </a>
  </div>

  {{-- 2. TAMPILAN WEBSITE & BANNER --}}
  <div class="nav-group">
    <div class="nav-label">Etalase Website</div>

    <a href="{{ route('admin.banner.index') }}" class="nav-item {{ request()->is('admin/banner*') ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-images nav-icon" style="color:#66a3bf;"></i>
        <span class="nav-text">Hero Slider &amp; Banner</span>
      </div>
      <span class="nav-count-badge">{{ $countBanner }}</span>
    </a>

    @if($isAdmin)
      <a href="{{ route('admin.statistik.web') }}" class="nav-item {{ request()->is('admin/statistik-web*') ? 'active' : '' }}" title="Statistik &amp; Grafik Pengunjung Website (Khusus Administrator)">
        <div class="nav-left-part">
          <i class="bi bi-graph-up-arrow nav-icon" style="color:#3368a0;"></i>
          <span class="nav-text">Grafik Pengunjung</span>
        </div>
        <span class="nav-count-badge" style="background:#3368a0; color:#ffffff; font-weight:800;">Admin</span>
      </a>
    @endif
  </div>

  {{-- 3. TAUTAN & MODUL LAIN --}}
  <div class="nav-group">
    <div class="nav-label">Akses Luar &amp; Modul Lain</div>

    <a href="{{ route('web.beranda') }}" target="_blank" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-box-arrow-up-right nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text">Lihat Website Utama</span>
      </div>
    </a>

    <a href="{{ route('web.berita.index') }}" target="_blank" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-collection-play-fill nav-icon" style="color:#66a3bf;"></i>
        <span class="nav-text">Kabar Sekolah Publik</span>
      </div>
    </a>

    <a href="/sirani" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-fingerprint nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text">Buka Modul SIRANI</span>
      </div>
    </a>

    <a href="/admin/ppdb" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-mortarboard-fill nav-icon" style="color:#3368a0;"></i>
        <span class="nav-text">Buka Modul PPDB 2026</span>
      </div>
    </a>
  </div>

  <style>
    #appSidebar .nav-label {
      color: var(--text-3, #64748b) !important;
      font-weight: 800;
      font-size: 10.5px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    #appSidebar .nav-text {
      color: var(--text, #0f172a) !important;
      font-weight: 600;
      transition: color 0.15s ease;
    }
    #appSidebar .nav-item:hover .nav-text {
      color: #3368a0 !important;
    }
    #appSidebar .nav-item.active {
      background: rgba(51, 104, 160, 0.12) !important;
      border-left: 3.5px solid #3368a0 !important;
    }
    #appSidebar .nav-item.active .nav-icon {
      color: #3368a0 !important;
    }
    #appSidebar .nav-item.active .nav-text {
      color: #3368a0 !important;
      font-weight: 800 !important;
    }
    [data-theme="dark"] #appSidebar .nav-text {
      color: #f1f5f9 !important;
    }
    [data-theme="dark"] #appSidebar .nav-label {
      color: #94a3b8 !important;
    }
    [data-theme="dark"] #appSidebar .nav-item.active {
      background: rgba(51, 104, 160, 0.25) !important;
      border-left: 3.5px solid #60a5fa !important;
    }
    [data-theme="dark"] #appSidebar .nav-item.active .nav-icon {
      color: #60a5fa !important;
    }
    [data-theme="dark"] #appSidebar .nav-item.active .nav-text {
      color: #93c5fd !important;
      font-weight: 800 !important;
    }
    [data-theme="dark"] #appSidebar .nav-item:hover .nav-text {
      color: #60a5fa !important;
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
