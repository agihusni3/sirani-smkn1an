@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $isStafTu = $user ? $user->isStafTu() : false;
  $isWakasis = $user ? $user->isWakaKesiswaan() : false;
  $isWakaKurikulum = $user ? $user->isWakaKurikulum() : false;
  $isWali = $user ? $user->isWaliKelas() : false;

  $countGuru = \App\Models\Guru::where('status', 'aktif')->count();
  $countSiswa = \App\Models\Siswa::where('status', 'aktif')->count();
  $countRombel = \App\Models\Rombel::count();
@endphp

{{-- Mobile Top Bar khusus Modul SITUAN --}}
<div class="situan-mobile-bar no-print" style="display:none;">
  <div style="display:flex; align-items:center; gap:10px;">
    <div style="width:34px; height:34px; border-radius:8px; background:#ffffff; border:1.5px solid #0284c7; display:flex; align-items:center; justify-content:center; padding:3px;">
      <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div>
      <span style="font-weight:900; font-size:15px; letter-spacing:-0.02em; display:block; line-height:1.1; color:#0284c7;">SITUAN</span>
      <span style="font-size:10px; color:#000000; font-weight:700;">Data Pokok &amp; TU</span>
    </div>
  </div>
  <div style="display:flex; align-items:center; gap:6px;">
    @include('partials.header_actions')
    <button type="button" class="situan-hamburger-btn" onclick="window.toggleSituanSidebar(event)" aria-label="Buka Menu SITUAN" style="width:36px; height:36px; border-radius:8px; background:#ffffff; border:1.5px solid #e2e8f0; color:#000000; display:flex; align-items:center; justify-content:center; font-size:18px; cursor:pointer;">
      <i class="bi bi-list"></i>
    </button>
  </div>
</div>

{{-- Backdrop Overlay for Mobile Drawer --}}
<div class="situan-sidebar-backdrop" id="situanBackdrop" onclick="window.closeSituanSidebar()" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:89; backdrop-filter:blur(3px);"></div>

{{-- Dedicated Modern SITUAN Sidebar --}}
<aside class="situan-sidebar" id="situanSidebar">
  
  {{-- 1. Brand Header --}}
  <div class="situan-sidebar-header">
    <a href="{{ route('situan.index') }}" class="situan-brand">
      <div class="situan-brand-badge">
        <i class="bi bi-buildings"></i>
      </div>
      <div>
        <div class="situan-brand-name">SITUAN</div>
        <div class="situan-brand-tag">Data Pokok SMKN 1 AN</div>
      </div>
    </a>
    <button type="button" class="situan-close-btn" onclick="window.closeSituanSidebar()" aria-label="Tutup Menu" style="display:none; background:none; border:none; color:#000000; font-size:18px; cursor:pointer;">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- 2. Shortcut DCC Command Center --}}
  <a href="{{ route('admin.portal') }}" class="situan-dcc-pill" title="Kembali ke Digital Command Center SMKN 1 AN">
    <div style="display:flex; align-items:center; gap:8px;">
      <i class="bi bi-command"></i>
      <span>Pusat Kendali DCC</span>
    </div>
    <i class="bi bi-chevron-right" style="font-size:10px; opacity:0.6;"></i>
  </a>

  {{-- 3. Navigation Sections --}}
  <div class="situan-nav-body">

    {{-- Nav Section: Pusat Kendali --}}
    <div class="situan-nav-group">
      <div class="situan-nav-group-title">Pusat Kendali</div>
      
      <a href="{{ route('situan.index') }}" class="situan-nav-link {{ (request()->is('situan') || request()->is('situan/dashboard')) ? 'active' : '' }}" title="Dasbor Statistik Administrasi &amp; Data Pokok">
        <div class="situan-nav-link-left">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dasbor Tata Usaha</span>
        </div>
      </a>
    </div>

    {{-- Nav Section: Master Kesiswaan & Kelas --}}
    <div class="situan-nav-group">
      <div class="situan-nav-group-title">Data Induk Kesiswaan</div>

      <a href="/siswa" class="situan-nav-link {{ request()->is('siswa*') ? 'active' : '' }}" title="Pangkalan Data Induk Peserta Didik &amp; Alumni">
        <div class="situan-nav-link-left">
          <i class="bi bi-people-fill"></i>
          <span>{{ $isWali && !$isAdmin && !$isStafTu ? 'Siswa Binaan' : 'Data Siswa & Alumni' }}</span>
        </div>
        @if($isWali && !$isAdmin && !$isStafTu)
          <span class="situan-nav-badge" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0;">Wali</span>
        @else
          <span class="situan-nav-badge">{{ $countSiswa }}</span>
        @endif
      </a>

      @if(!$isWali || $isAdmin || $isStafTu)
        <a href="/rombel" class="situan-nav-link {{ request()->is('rombel*') ? 'active' : '' }}" title="Kelola Rombongan Belajar, Jurusan &amp; Tahun Ajaran">
          <div class="situan-nav-link-left">
            <i class="bi bi-diagram-3-fill"></i>
            <span>Rombongan Belajar</span>
          </div>
          <span class="situan-nav-badge">{{ $countRombel }}</span>
        </a>
      @endif

      @if($isAdmin || $isWakasis || $isStafTu || $isWakaKurikulum)
        <a href="/siklus-siswa" class="situan-nav-link {{ request()->is('siklus-siswa*') ? 'active' : '' }}" title="Transisi Kenaikan Kelas, PKL &amp; Kelulusan Alumni">
          <div class="situan-nav-link-left">
            <i class="bi bi-journal-bookmark-fill"></i>
            <span>Buku Induk &amp; Siklus</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isStafTu)
        <a href="/kartu-rfid" class="situan-nav-link {{ request()->is('kartu-rfid*') || request()->is('manajemen-rfid*') ? 'active' : '' }}" title="Pusat Cetak Kartu Barcode &amp; Registrasi RFID">
          <div class="situan-nav-link-left">
            <i class="bi bi-person-vcard-fill"></i>
            <span>Kartu &amp; Identitas</span>
          </div>
        </a>
      @endif
    </div>

    {{-- Nav Section: Master Kepegawaian (PTK) --}}
    @if($isAdmin || $isKepsek || $isStafTu || $isWakaKurikulum || $isWakasis || ($user && in_array($user->role, ['waka_sarpras', 'waka_hubin'])))
      <div class="situan-nav-group">
        <div class="situan-nav-group-title">Pendidik &amp; Tendik (PTK)</div>

        <a href="/guru" class="situan-nav-link {{ request()->is('guru*') ? 'active' : '' }}" title="Pangkalan Data Pendidik &amp; Tenaga Kependidikan (PTK)">
          <div class="situan-nav-link-left">
            <i class="bi bi-person-badge-fill"></i>
            <span>Data PTK (Guru &amp; Staf)</span>
          </div>
          <span class="situan-nav-badge">{{ $countGuru }} PTK</span>
        </a>
      </div>
    @endif

    {{-- Nav Section: Legalitas & Audit Data --}}
    @if($isAdmin || $isKepsek || $isStafTu)
      <div class="situan-nav-group">
        <div class="situan-nav-group-title">Tata Usaha &amp; Audit</div>

        <a href="{{ route('situan.log') }}" class="situan-nav-link {{ request()->is('situan/log*') ? 'active' : '' }}" title="Riwayat &amp; Log Mutasi Data Tata Usaha">
          <div class="situan-nav-link-left">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat &amp; Mutasi</span>
          </div>
        </a>

        @if($isAdmin)
          <a href="/pengaturan-sekolah" class="situan-nav-link {{ request()->is('pengaturan-sekolah*') ? 'active' : '' }}" title="Profil Identitas Sekolah, Legalitas, &amp; Kop Surat Resmi">
            <div class="situan-nav-link-left">
              <i class="bi bi-bank2"></i>
              <span>Profil Sekolah</span>
            </div>
          </a>

          <a href="/backup" class="situan-nav-link {{ request()->is('backup*') ? 'active' : '' }}" title="Pencadangan Database &amp; Pemulihan Data">
            <div class="situan-nav-link-left">
              <i class="bi bi-database-check"></i>
              <span>Cadangan Database</span>
            </div>
          </a>
        @endif
      </div>
    @endif

    {{-- Nav Section: Modul Terhubung Ekosistem --}}
    <div class="situan-nav-group" style="margin-top:auto; padding-top:14px; border-top:1.5px solid var(--situan-border-subtle);">
      <div class="situan-nav-group-title">Pintasan Modul</div>

      <a href="/dashboard" class="situan-nav-link" title="Buka Modul Presensi &amp; Absensi SIRANI">
        <div class="situan-nav-link-left">
          <i class="bi bi-fingerprint" style="color:#0f766e;"></i>
          <span>SIRANI Presensi</span>
        </div>
        <i class="bi bi-arrow-up-right" style="font-size:11px; opacity:0.5;"></i>
      </a>

      @if($user && ($user->canAccessPpdb() || $isAdmin || $isKepsek))
        <a href="/admin/ppdb" class="situan-nav-link" title="Buka Modul Penerimaan Peserta Didik Baru">
          <div class="situan-nav-link-left">
            <i class="bi bi-mortarboard-fill" style="color:#d97706;"></i>
            <span>PPDB 2026</span>
          </div>
          <i class="bi bi-arrow-up-right" style="font-size:11px; opacity:0.5;"></i>
        </a>
      @endif

      @if($user && ($user->canAccessWebHumas() || $isAdmin || $isKepsek))
        <a href="/admin/berita" class="situan-nav-link" title="Buka Modul Humas &amp; Publikasi Web">
          <div class="situan-nav-link-left">
            <i class="bi bi-globe-americas" style="color:#6366f1;"></i>
            <span>Humas &amp; Web</span>
          </div>
          <i class="bi bi-arrow-up-right" style="font-size:11px; opacity:0.5;"></i>
        </a>
      @endif
    </div>

  </div>

  {{-- 4. Sidebar Footer User Status --}}
  <div class="situan-sidebar-footer">
    <div class="situan-user-mini">
      <div class="situan-user-avatar">
        {{ strtoupper(substr($user?->name ?? 'A', 0, 1)) }}
      </div>
      <div class="situan-user-info">
        <div class="situan-user-name">{{ $user?->name ?? 'Administrator' }}</div>
        <div class="situan-user-role">{{ $user?->role_display_name ?? 'Staf Tata Usaha' }}</div>
      </div>
    </div>
    @include('partials.header_actions')
  </div>

</aside>

<script>
  window.toggleSituanSidebar = function(e) {
    if (e) e.stopPropagation();
    const sb = document.getElementById('situanSidebar');
    const bd = document.getElementById('situanBackdrop');
    if (sb) sb.classList.toggle('open');
    if (bd) bd.style.display = sb && sb.classList.contains('open') ? 'block' : 'none';
  };
  window.closeSituanSidebar = function() {
    const sb = document.getElementById('situanSidebar');
    const bd = document.getElementById('situanBackdrop');
    if (sb) sb.classList.remove('open');
    if (bd) bd.style.display = 'none';
  };
</script>
