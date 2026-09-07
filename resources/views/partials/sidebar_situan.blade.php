@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $isStafTu = $user ? $user->isStafTu() : false;
  $isWakasis = $user ? $user->isWakaKesiswaan() : false;
  $isWakaKurikulum = $user ? $user->isWakaKurikulum() : false;
  $isWali = $user ? $user->isWaliKelas() : false;

  $countGuru = \App\Models\Guru::where('status', 'aktif')->count();
  $countSiswa = \App\Models\Siswa::whereIn('status', ['aktif', 'pkl'])->count();
  $countRombel = \App\Models\Rombel::count();
@endphp

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
    <button type="button" class="situan-close-btn" onclick="window.closeSituanSidebar()" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- 2. Shortcut DCC Command Center --}}
  <a href="{{ route('admin.portal') }}" class="situan-dcc-pill">
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
      
      <a href="{{ route('situan.index') }}" class="situan-nav-link {{ (request()->is('situan') || request()->is('situan/dashboard')) ? 'active' : '' }}">
        <div class="situan-nav-link-left">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dasbor Tata Usaha</span>
        </div>
      </a>
    </div>

    {{-- Nav Section: Master Kesiswaan & Kelas --}}
    <div class="situan-nav-group">
      <div class="situan-nav-group-title">Data Induk Kesiswaan</div>

      <a href="/siswa" class="situan-nav-link {{ request()->is('siswa*') ? 'active' : '' }}">
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
        <a href="/rombel" class="situan-nav-link {{ request()->is('rombel*') ? 'active' : '' }}">
          <div class="situan-nav-link-left">
            <i class="bi bi-diagram-3-fill"></i>
            <span>Rombongan Belajar</span>
          </div>
          <span class="situan-nav-badge">{{ $countRombel }}</span>
        </a>
      @endif

      @if($isAdmin || $isWakasis || $isStafTu || $isWakaKurikulum)
        <a href="/siklus-siswa" class="situan-nav-link {{ request()->is('siklus-siswa*') ? 'active' : '' }}">
          <div class="situan-nav-link-left">
            <i class="bi bi-journal-bookmark-fill"></i>
            <span>Buku Induk &amp; Siklus</span>
          </div>
        </a>
      @endif
    </div>

    {{-- Nav Section: Master Kepegawaian (PTK) --}}
    @if($isAdmin || $isKepsek || $isStafTu || $isWakaKurikulum || $isWakasis || ($user && in_array($user->role, ['waka_sarpras', 'waka_hubin'])))
      <div class="situan-nav-group">
        <div class="situan-nav-group-title">Data Kepegawaian</div>

        <a href="/guru" class="situan-nav-link {{ request()->is('guru*') ? 'active' : '' }}">
          <div class="situan-nav-link-left">
            <i class="bi bi-person-badge-fill"></i>
            <span>Data PTK</span>
          </div>
          <span class="situan-nav-badge">{{ $countGuru }}</span>
        </a>
      </div>
    @endif

    {{-- Nav Section: Kartu Identitas & RFID --}}
    @if($isAdmin || $isStafTu)
      <div class="situan-nav-group">
        <div class="situan-nav-group-title">Identitas &amp; Kartu</div>

        <a href="/kartu-rfid" class="situan-nav-link {{ request()->is('kartu-rfid*') || request()->is('manajemen-rfid*') ? 'active' : '' }}">
          <div class="situan-nav-link-left">
            <i class="bi bi-person-vcard-fill"></i>
            <span>Kartu &amp; Identitas</span>
          </div>
        </a>
      </div>
    @endif

    {{-- Nav Section: Legalitas & Audit Data --}}
    @if($isAdmin || $isKepsek || $isStafTu)
      <div class="situan-nav-group">
        <div class="situan-nav-group-title">Tata Usaha &amp; Audit</div>

        <a href="{{ route('situan.log') }}" class="situan-nav-link {{ request()->is('situan/log*') ? 'active' : '' }}">
          <div class="situan-nav-link-left">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat &amp; Mutasi</span>
          </div>
        </a>

        @if($isAdmin)
          <a href="/pengaturan-sekolah" class="situan-nav-link {{ request()->is('pengaturan-sekolah*') ? 'active' : '' }}">
            <div class="situan-nav-link-left">
              <i class="bi bi-bank2"></i>
              <span>Profil Sekolah</span>
            </div>
          </a>

          <a href="/backup" class="situan-nav-link {{ request()->is('backup*') ? 'active' : '' }}">
            <div class="situan-nav-link-left">
              <i class="bi bi-database-check"></i>
              <span>Cadangan Database</span>
            </div>
          </a>
        @endif
      </div>
    @endif

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
    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
      @csrf
      <button type="submit" class="situan-logout-btn" aria-label="Keluar / Logout">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </form>
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
