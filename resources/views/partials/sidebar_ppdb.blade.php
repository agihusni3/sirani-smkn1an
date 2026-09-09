@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $statusQuery = request('status');
  $jurusanQuery = request('jurusan_id');

  // Hitung jumlah pendaftar per status untuk badge
  $countTotal = \App\Models\PpdbPendaftar::count();
  $countMenunggu = \App\Models\PpdbPendaftar::whereIn('status', ['menunggu', 'menunggu_verifikasi', 'draft'])->count();
  $countValid = \App\Models\PpdbPendaftar::whereIn('status', ['terverifikasi', 'berkas_valid'])->count();
  $countDiterima = \App\Models\PpdbPendaftar::where('status', 'diterima')->count();
  $countDitolak = \App\Models\PpdbPendaftar::where('status', 'ditolak')->count();
  $jurusansNav = \App\Models\Jurusan::all();

  // Helper ikon modern per jurusan
  $getJurusanIcon = function($kode, $nama) {
      $str = strtoupper($kode . ' ' . $nama);
      if (str_contains($str, 'RPL') || str_contains($str, 'PERANGKAT LUNAK') || str_contains($str, 'KOMPUTER')) {
          return 'bi-code-square';
      }
      if (str_contains($str, 'APHP') || str_contains($str, 'PENGOLAHAN') || str_contains($str, 'PERTANIAN')) {
          return 'bi-flower1';
      }
      if (str_contains($str, 'TSM') || str_contains($str, 'SEPEDA MOTOR') || str_contains($str, 'OTOMOTIF') || str_contains($str, 'MESIN')) {
          return 'bi-gear-wide-connected';
      }
      return 'bi-tag';
  };
@endphp

{{-- Scoped Styles for Modern Minimalist PPDB Sidebar --}}
<style>
  .sidebar-ppdb {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
  }
  .sidebar-ppdb .nav-section {
    margin-bottom: 14px;
  }
  .sidebar-ppdb .nav-section-title {
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    padding: 8px 12px 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .sidebar-ppdb .ppdb-nav-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8.5px 12px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    text-decoration: none;
    transition: all 0.15s ease;
    margin-bottom: 2px;
    border: 1px solid transparent;
  }
  .sidebar-ppdb .ppdb-nav-link:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #f1f5f9;
  }
  .sidebar-ppdb .ppdb-nav-link.active {
    background: #f8fafc;
    color: #0f172a !important;
    font-weight: 800;
    border-color: #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    position: relative;
  }
  .sidebar-ppdb .ppdb-nav-link.active::before {
    content: '';
    position: absolute;
    left: -1px;
    top: 6px;
    bottom: 6px;
    width: 3.5px;
    background: #d97706;
    border-radius: 4px;
  }
  .sidebar-ppdb .ppdb-nav-icon {
    font-size: 15px;
    color: #64748b;
    margin-right: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    transition: color 0.15s ease;
  }
  .sidebar-ppdb .ppdb-nav-link:hover .ppdb-nav-icon,
  .sidebar-ppdb .ppdb-nav-link.active .ppdb-nav-icon {
    color: #d97706;
  }
  .sidebar-ppdb .ppdb-badge {
    font-size: 11px;
    font-weight: 800;
    padding: 1.5px 7px;
    border-radius: 20px;
    font-family: var(--font-mono, monospace);
    line-height: 1.2;
    border: 1px solid transparent;
  }
  .sidebar-ppdb .badge-muted { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
  .sidebar-ppdb .badge-amber { background: #fef3c7; color: #b45309; border-color: #fde68a; }
  .sidebar-ppdb .badge-blue  { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
  .sidebar-ppdb .badge-emerald { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
  .sidebar-ppdb .badge-gray { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
  .sidebar-ppdb .bi-leaf::before { content: "\f3cd"; }
</style>

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
<aside class="sidebar sidebar-ppdb" id="appSidebar">
  
  {{-- 1. BRAND HEADER --}}
  <div class="brand" style="margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
    <div class="brand-left" style="display:flex; align-items:center; gap:10px;">
      <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, rgba(217,119,6,0.14), rgba(245,158,11,0.04)); border:1px solid rgba(217,119,6,0.22); display:flex; align-items:center; justify-content:center; padding:4px;">
        <i class="bi bi-mortarboard-fill" style="color:#d97706; font-size:18px;"></i>
      </div>
      <div class="brand-text">
        <div style="font-weight:900; font-size:16px; letter-spacing:-0.02em; color:#0f172a;">PPDB 2026</div>
        <div style="font-size:10.5px; color:#64748b; font-weight:600;">Panitia Seleksi Calon Siswa</div>
      </div>
    </div>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" onclick="window.closeSmknSidebar()" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- 2. QUICK SWITCHER KE DCC --}}
  <div style="margin-bottom:14px;">
    <a href="{{ route('admin.portal') }}" class="btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:8px 12px; font-size:11.5px; font-weight:800; background:#0f172a; color:#ffffff; border:1px solid #1e293b; border-radius:8px; text-decoration:none; box-shadow:0 2px 6px rgba(15,23,42,0.1); box-sizing:border-box;" title="Buka Data Control Center (DCC) SMKN 1 AN">
      <i class="bi bi-command" style="color:#38bdf8; font-size:13px;"></i>
      <span>DCC SMKN 1 AN</span>
    </a>
  </div>

  {{-- 3. KELOMPOK 1: MENU UTAMA --}}
  <div class="nav-section">
    <div class="nav-section-title">Menu Utama</div>

    {{-- Dasbor --}}
    <a href="{{ route('admin.ppdb.index') }}" class="ppdb-nav-link {{ (request()->is('admin/ppdb') && empty($statusQuery) && empty($jurusanQuery) && !request()->is('admin/ppdb/log*') && !request()->is('admin/ppdb/seleksi*')) ? 'active' : '' }}">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-grid-1x2 ppdb-nav-icon"></i>
        <span>Dasbor &amp; Statistik</span>
      </div>
    </a>

    {{-- Seleksi & Ujian CBT --}}
    <a href="{{ route('admin.ppdb.seleksi') }}" class="ppdb-nav-link {{ (request()->is('admin/ppdb/seleksi*') || request()->is('admin/ppdb/soal*')) ? 'active' : '' }}" title="Seleksi Ujian CBT &amp; Penilaian Wawancara">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-laptop ppdb-nav-icon"></i>
        <span>Seleksi &amp; Ujian CBT</span>
      </div>
      <span class="ppdb-badge badge-blue">CBT</span>
    </a>

    {{-- Presensi Ujian Barcode --}}
    <a href="{{ route('admin.ppdb.presensi.kios') }}" class="ppdb-nav-link {{ request()->is('admin/ppdb/presensi-ujian*') ? 'active' : '' }}" title="Kios Presensi Barcode 2D / QR Ujian PPDB">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-qr-code-scan ppdb-nav-icon"></i>
        <span>Presensi Ujian Barcode</span>
      </div>
      <span class="ppdb-badge badge-amber">Scan</span>
    </a>

    {{-- Log Riwayat --}}
    <a href="{{ route('admin.ppdb.log') }}" class="ppdb-nav-link {{ request()->is('admin/ppdb/log*') ? 'active' : '' }}" title="Audit Log &amp; Riwayat Aktivitas Panitia">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-clock-history ppdb-nav-icon"></i>
        <span>Log Aktivitas</span>
      </div>
    </a>
  </div>

  {{-- 4. KELOMPOK 2: TAHAPAN VERIFIKASI & SELEKSI BERKAS --}}
  <div class="nav-section">
    <div class="nav-section-title">
      <span>Data Calon Siswa</span>
      <span style="font-size:10px; font-weight:700; color:#cbd5e1;">{{ $countTotal }}</span>
    </div>

    {{-- Semua Pendaftar --}}
    <a href="{{ route('admin.ppdb.index', ['status' => 'semua']) }}" class="ppdb-nav-link {{ $statusQuery === 'semua' ? 'active' : '' }}">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-people ppdb-nav-icon"></i>
        <span>Semua Pendaftar</span>
      </div>
      <span class="ppdb-badge badge-muted">{{ $countTotal }}</span>
    </a>

    {{-- Menunggu Verifikasi --}}
    <a href="{{ route('admin.ppdb.index', ['status' => 'menunggu']) }}" class="ppdb-nav-link {{ $statusQuery === 'menunggu' ? 'active' : '' }}">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-hourglass-split ppdb-nav-icon"></i>
        <span>Menunggu Verifikasi</span>
      </div>
      @if($countMenunggu > 0)
        <span class="ppdb-badge badge-amber">{{ $countMenunggu }}</span>
      @endif
    </a>

    {{-- Berkas Valid / Siap Tes --}}
    <a href="{{ route('admin.ppdb.index', ['status' => 'berkas_valid']) }}" class="ppdb-nav-link {{ $statusQuery === 'berkas_valid' ? 'active' : '' }}">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-patch-check ppdb-nav-icon"></i>
        <span>Berkas Valid</span>
      </div>
      @if($countValid > 0)
        <span class="ppdb-badge badge-blue">{{ $countValid }}</span>
      @endif
    </a>

    {{-- Calon Siswa Diterima --}}
    <a href="{{ route('admin.ppdb.index', ['status' => 'diterima']) }}" class="ppdb-nav-link {{ $statusQuery === 'diterima' ? 'active' : '' }}">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-check2-circle ppdb-nav-icon"></i>
        <span>Siswa Diterima</span>
      </div>
      @if($countDiterima > 0)
        <span class="ppdb-badge badge-emerald">{{ $countDiterima }}</span>
      @endif
    </a>

    {{-- Ditolak / Tidak Lolos --}}
    <a href="{{ route('admin.ppdb.index', ['status' => 'ditolak']) }}" class="ppdb-nav-link {{ $statusQuery === 'ditolak' ? 'active' : '' }}">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-x-circle ppdb-nav-icon"></i>
        <span>Ditolak / Draf</span>
      </div>
      @if($countDitolak > 0)
        <span class="ppdb-badge badge-gray">{{ $countDitolak }}</span>
      @endif
    </a>
  </div>

  {{-- 5. KELOMPOK 3: PEMINATAN JURUSAN --}}
  <div class="nav-section">
    <div class="nav-section-title">Jurusan Pilihan</div>

    @foreach($jurusansNav as $j)
      @php
        $jIcon = $getJurusanIcon($j->kode_jurusan, $j->nama_jurusan);
        $isActive = ($jurusanQuery == $j->id);
      @endphp
      <a href="{{ route('admin.ppdb.index', ['jurusan_id' => $j->id]) }}" class="ppdb-nav-link {{ $isActive ? 'active' : '' }}" title="{{ $j->nama_jurusan }}">
        <div style="display:flex; align-items:center; min-width:0; overflow:hidden;">
          <i class="bi {{ $jIcon }} ppdb-nav-icon"></i>
          <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            <strong>{{ $j->kode_jurusan }}</strong> — {{ $j->nama_jurusan }}
          </span>
        </div>
      </a>
    @endforeach
  </div>

  {{-- 6. KELOMPOK 4: AKSES CEPAT PUBLIK & MODUL LAIN --}}
  <div class="nav-section" style="margin-top:auto; padding-top:12px; border-top:1px dashed #e2e8f0;">
    <div class="nav-section-title">Akses Cepat</div>

    <a href="{{ route('ppdb.formulir') }}" target="_blank" class="ppdb-nav-link" title="Buka Formulir Pendaftaran Siswa Baru">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-arrow-up-right-circle ppdb-nav-icon"></i>
        <span>Formulir Publik</span>
      </div>
    </a>

    <a href="{{ route('ppdb.index') }}" target="_blank" class="ppdb-nav-link" title="Portal Informasi PPDB SMKN 1">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-globe2 ppdb-nav-icon"></i>
        <span>Portal PPDB</span>
      </div>
    </a>

    <a href="/situan" class="ppdb-nav-link" title="Buka Modul Tata Usaha SITUAN">
      <div style="display:flex; align-items:center;">
        <i class="bi bi-building ppdb-nav-icon"></i>
        <span>Modul SITUAN</span>
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
