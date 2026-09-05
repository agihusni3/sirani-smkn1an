@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $statusQuery = request('status');
  $jurusanQuery = request('jurusan_id');

  // Hitung jumlah pendaftar per status untuk badge
  $countMenunggu = \App\Models\PpdbPendaftar::whereIn('status', ['menunggu', 'menunggu_verifikasi', 'draft'])->count();
  $countValid = \App\Models\PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid'])->count();
  $countDiterima = \App\Models\PpdbPendaftar::where('status', 'diterima')->count();
  $countDitolak = \App\Models\PpdbPendaftar::where('status', 'ditolak')->count();
  $jurusansNav = \App\Models\Jurusan::all();
@endphp

{{-- Mobile Top Bar khusus Modul PPDB --}}
<div class="mobile-topbar no-print">
  <div style="display:flex; align-items:center; gap:10px;">
    <div style="width:34px; height:34px; border-radius:8px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; padding:3px;">
      <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div>
      <span style="font-weight:900; font-size:16px; letter-spacing:-0.02em; display:block; line-height:1.1; color:#d97706;">PPDB 2026</span>
      <span style="font-size:10px; color:var(--text-3); font-weight:600;">Panitia Penerimaan Siswa</span>
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

{{-- Sidebar Navigation: Khusus Workspace Modul PPDB 2026 --}}
<aside class="sidebar" id="appSidebar">
  <div class="brand" style="margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
    <div class="brand-left" style="display:flex; align-items:center; gap:10px;">
      <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, rgba(217,119,6,0.15), rgba(245,158,11,0.05)); border:1px solid rgba(217,119,6,0.25); display:flex; align-items:center; justify-content:center; padding:4px;">
        <i class="bi bi-mortarboard-fill" style="color:#d97706; font-size:18px;"></i>
      </div>
      <div class="brand-text">
        <div style="font-weight:900; font-size:16.5px; letter-spacing:-0.02em; color:#d97706;">PPDB 2026</div>
        <div style="font-size:11px; color:var(--text-3); font-weight:600;">Panitia Seleksi Calon Siswa</div>
      </div>
    </div>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" onclick="window.closeSmknSidebar()" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- Tombol Navigasi Kembali ke DCC SMKN 1 AN --}}
  <div style="margin-bottom:16px;">
    <a href="{{ route('admin.portal') }}" class="btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:8px 12px; font-size:11.5px; font-weight:800; background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color:#ffffff; border:1px solid rgba(255,255,255,0.12); border-radius:var(--r-sm); text-decoration:none; box-shadow:0 3px 10px rgba(0,0,0,0.12); box-sizing:border-box;" title="Buka Digital Command Center SMKN 1 AN">
      <i class="bi bi-grid-3x3-gap-fill" style="color:#38bdf8; font-size:13px;"></i>
      <span>DCC SMKN 1 AN</span>
    </a>
  </div>

  {{-- 1. MENU UTAMA PPDB --}}
  <div class="nav-group">
    <div class="nav-label">Navigasi PPDB</div>
    
    <a href="{{ route('admin.ppdb.index') }}" class="nav-item {{ (request()->is('admin/ppdb') && empty($statusQuery) && empty($jurusanQuery)) ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-speedometer2 nav-icon" style="color:#d97706;"></i>
        <span class="nav-text">Dasbor &amp; Statistik</span>
      </div>
    </a>
  </div>

  {{-- 2. TAHAPAN VERIFIKASI & SELEKSI --}}
  <div class="nav-group">
    <div class="nav-label">Verifikasi &amp; Seleksi Berkas</div>

    <a href="{{ route('admin.ppdb.index', ['status' => 'menunggu']) }}" class="nav-item {{ $statusQuery === 'menunggu' ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-clock-history nav-icon" style="color:#ef4444;"></i>
        <span class="nav-text">Menunggu Cek</span>
      </div>
      @if($countMenunggu > 0)
        <span class="nav-count-badge" style="background:#fee2e2; color:#ef4444; border-color:#fca5a5; font-weight:800;">{{ $countMenunggu }}</span>
      @endif
    </a>

    <a href="{{ route('admin.ppdb.index', ['status' => 'berkas_valid']) }}" class="nav-item {{ $statusQuery === 'berkas_valid' ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-patch-check-fill nav-icon" style="color:#0284c7;"></i>
        <span class="nav-text">Berkas Valid</span>
      </div>
      @if($countValid > 0)
        <span class="nav-count-badge" style="background:#e0f2fe; color:#0284c7; border-color:#bae6fd; font-weight:800;">{{ $countValid }}</span>
      @endif
    </a>

    <a href="{{ route('admin.ppdb.index', ['status' => 'diterima']) }}" class="nav-item {{ $statusQuery === 'diterima' ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-check-circle-fill nav-icon" style="color:#10b981;"></i>
        <span class="nav-text">Calon Siswa Diterima</span>
      </div>
      @if($countDiterima > 0)
        <span class="nav-count-badge" style="background:#ecfdf5; color:#10b981; border-color:#a7f3d0; font-weight:800;">{{ $countDiterima }}</span>
      @endif
    </a>

    <a href="{{ route('admin.ppdb.index', ['status' => 'ditolak']) }}" class="nav-item {{ $statusQuery === 'ditolak' ? 'active' : '' }}">
      <div class="nav-left-part">
        <i class="bi bi-x-circle-fill nav-icon" style="color:#64748b;"></i>
        <span class="nav-text">Ditolak / Draf</span>
      </div>
      @if($countDitolak > 0)
        <span class="nav-count-badge" style="background:#f1f5f9; color:#64748b; border-color:#cbd5e1; font-weight:800;">{{ $countDitolak }}</span>
      @endif
    </a>
  </div>

  {{-- 3. JURUSAN & PEMETAAN KUOTA --}}
  <div class="nav-group">
    <div class="nav-label">Peminatan Jurusan</div>
    @foreach($jurusansNav as $j)
      <a href="{{ route('admin.ppdb.index', ['jurusan_id' => $j->id]) }}" class="nav-item {{ $jurusanQuery == $j->id ? 'active' : '' }}">
        <div class="nav-left-part">
          <i class="bi bi-tag-fill nav-icon" style="color:var(--text-3);"></i>
          <span class="nav-text">{{ $j->kode_jurusan }} — {{ $j->nama_jurusan }}</span>
        </div>
      </a>
    @endforeach
  </div>

  {{-- 4. TAUTAN & PORTAL LUAR --}}
  <div class="nav-group">
    <div class="nav-label">Akses Publik &amp; Modul Lain</div>

    <a href="{{ route('ppdb.formulir') }}" target="_blank" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-box-arrow-up-right nav-icon" style="color:#d97706;"></i>
        <span class="nav-text">Form Pendaftaran Publik</span>
      </div>
    </a>

    <a href="{{ route('ppdb.index') }}" target="_blank" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-globe nav-icon" style="color:#2563eb;"></i>
        <span class="nav-text">Portal Informasi PPDB</span>
      </div>
    </a>

    <a href="/dashboard" class="nav-item">
      <div class="nav-left-part">
        <i class="bi bi-fingerprint nav-icon" style="color:#10b981;"></i>
        <span class="nav-text">Buka Modul SIRANI</span>
      </div>
    </a>
  </div>
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
