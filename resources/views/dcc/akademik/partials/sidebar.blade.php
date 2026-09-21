@php
  $user = auth()->user();
  $isAdmin = $user ? $user->isAdmin() : false;
  $isWakaKurikulum = $user ? $user->isWakaKurikulum() : false;
  $isKepsek = $user ? $user->isKepalaSekolah() : false;
  $isWakaKesiswaan = $user ? $user->isWakaKesiswaan() : false;
  $isWakaHubin = $user ? $user->isWakaHubin() : false;
  $isKaprog = $user ? $user->isKaprog() : false;
  $isWaliKelas = $user ? $user->isWaliKelas() : false;
  $isGuru = $user ? $user->isGuru() : false;
  $isGuruPiket = $user ? $user->isGuruPiket() : false;

  // Pemetaan Hak Akses Menu Akademik & KBM
  $canManageMatpel = $isAdmin || $isWakaKurikulum || $isKepsek || $isKaprog;
  $canManageSk = $isAdmin || $isWakaKurikulum || $isKepsek;
  $canViewPiket = $isAdmin || $isWakaKurikulum || $isWakaKesiswaan || $isGuruPiket || $isKepsek;
  $canManagePerangkat = $isAdmin || $isWakaKurikulum || $isKepsek || $isKaprog || $isGuru;
  $canManageJurnal = $isAdmin || $isWakaKurikulum || $isKepsek || $isGuru;
  $canManageNilai = $isAdmin || $isWakaKurikulum || $isGuru;
  $canViewLeger = $isAdmin || $isWakaKurikulum || $isKepsek || $isKaprog || $isWaliKelas || $isGuru;
  $canManageAsesmen = $isAdmin || $isWakaKurikulum || $isKaprog || $isGuru;
  $canManagePkl = $isAdmin || $isWakaHubin || $isKaprog || $isKepsek || $isGuru;
  $canManageP5bk = $isAdmin || $isWakaKurikulum || $isKepsek || $isKaprog || $isWaliKelas || $isGuru;
@endphp

{{-- Backdrop Overlay for Mobile Drawer --}}
<div class="akademik-sidebar-backdrop" id="akademikBackdrop" onclick="window.closeAkademikSidebar()" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:89; backdrop-filter:blur(3px);"></div>

<aside class="akademik-sidebar" id="akademikSidebar">
  
  {{-- 1. Brand Header --}}
  <div class="akademik-sidebar-header">
    <a href="{{ route('akademik.dashboard') }}" class="akademik-brand">
      <div class="akademik-brand-badge">
        <i class="bi bi-journal-bookmark-fill"></i>
      </div>
      <div>
        <div class="akademik-brand-name">AKADEMIK &amp; KBM</div>
        <div class="akademik-brand-tag">Kurikulum &amp; Penilaian</div>
      </div>
    </a>
    <button type="button" class="akademik-sidebar-close d-lg-none" onclick="window.closeAkademikSidebar()" aria-label="Tutup">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- 2. Shortcut DCC (Data Control Center) --}}
  <a href="{{ route('admin.portal') }}" class="akademik-dcc-pill" title="Kembali ke Pusat Kontrol Data DCC">
    <div style="display:flex; align-items:center; gap:8px;">
      <i class="bi bi-command"></i>
      <span>Pusat Kontrol Data DCC</span>
    </div>
    <i class="bi bi-chevron-right" style="font-size:10px; opacity:0.6;"></i>
  </a>

  {{-- 3. Navigation Sections --}}
  <div class="akademik-nav-body">

    {{-- Utama --}}
    <div class="akademik-nav-group">
      <div class="akademik-nav-group-title">Utama</div>
      <a href="{{ route('akademik.dashboard') }}" class="akademik-nav-link {{ request()->routeIs('akademik.dashboard') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dasbor Akademik</span>
        </div>
      </a>
    </div>

    {{-- Alur Kerja Waka Kurikulum (SOP Resmi) --}}
    <div class="akademik-nav-group">
      <div class="akademik-nav-group-title">Alur Kerja Kurikulum (Wakakur)</div>
      
      @if($canManageMatpel)
      <a href="{{ route('akademik.matpel.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.matpel.*') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-1-circle-fill" style="font-size:15px; color:#2563eb;"></i>
          <span>1. Mata Pelajaran &amp; CP</span>
        </div>
      </a>
      @endif

      @if($canManageSk)
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="akademik-nav-link {{ (request()->routeIs('akademik.jadwal.*') && request('tab') === 'distribusi') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-2-circle-fill" style="font-size:15px; color:#4f46e5;"></i>
          <span>2. SK Pembagian Tugas</span>
        </div>
      </a>
      @endif

      <a href="{{ route('akademik.jadwal.index', ['tab' => 'jadwal']) }}" class="akademik-nav-link {{ (request()->routeIs('akademik.jadwal.*') && (in_array(request('tab', 'jadwal'), ['jadwal', 'roster', 'formulasi', 'pukul']))) ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-3-circle-fill" style="font-size:15px; color:#059669;"></i>
          <span>3. Jadwal Roster (Kelas &amp; Lab)</span>
        </div>
      </a>

      @if($canViewPiket)
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket']) }}" class="akademik-nav-link {{ (request()->routeIs('akademik.jadwal.*') && request('tab') === 'piket') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-4-circle-fill" style="font-size:15px; color:#d97706;"></i>
          <span>4. Jadwal Guru Piket</span>
        </div>
      </a>
      @endif
    </div>

    {{-- Pembelajaran Harian --}}
    @if($canManagePerangkat || $canManageJurnal)
    <div class="akademik-nav-group">
      <div class="akademik-nav-group-title">Pembelajaran &amp; KBM</div>

      @if($canManagePerangkat)
      <a href="{{ route('akademik.perangkat.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.perangkat.*') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-folder-check" style="font-size:16px; color:#0284c7;"></i>
          <span>Perangkat Pembelajaran</span>
        </div>
        <span class="akademik-nav-badge" style="background:#e0f2fe; color:#0369a1; font-weight:700;">Kurmer</span>
      </a>
      @endif

      @if($canManageJurnal)
      <a href="{{ route('akademik.jurnal.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.jurnal.*') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-pencil-square"></i>
          <span>Jurnal KBM Harian</span>
        </div>
      </a>
      @endif
    </div>
    @endif

    {{-- Evaluasi & Penilaian --}}
    @if($canManageNilai || $canViewLeger || $canManageAsesmen)
    <div class="akademik-nav-group">
      <div class="akademik-nav-group-title">Evaluasi &amp; Asesmen</div>

      @if($canManageNilai)
      <a href="{{ route('akademik.nilai.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.nilai.index') || request()->routeIs('akademik.nilai.input') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-clipboard2-data"></i>
          <span>Input Nilai Formatif &amp; Sumatif</span>
        </div>
      </a>
      @endif

      @if($canViewLeger)
      <a href="{{ route('akademik.nilai.leger') }}" class="akademik-nav-link {{ request()->routeIs('akademik.nilai.leger') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-table"></i>
          <span>Leger Nilai Kelas</span>
        </div>
      </a>
      @endif

      {{-- Asesmen Penilaian Berbasis Online --}}
      @if($canManageAsesmen)
      <a href="{{ route('akademik.asesmen.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.asesmen.*') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-laptop"></i>
          <span>Asesmen Penilaian Online</span>
        </div>
        <span class="akademik-nav-badge">CBT</span>
      </a>
      @endif
    </div>
    @endif

    {{-- Vokasi SMK --}}
    @if($canManagePkl || $canManageP5bk)
    <div class="akademik-nav-group">
      <div class="akademik-nav-group-title">Khas Vokasi SMK</div>

      @if($canManagePkl)
      <a href="{{ route('akademik.pkl.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.pkl.*') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-briefcase"></i>
          <span>Praktik Kerja Lapangan (PKL)</span>
        </div>
      </a>
      @endif

      @if($canManageP5bk)
      <a href="{{ route('akademik.p5bk.index') }}" class="akademik-nav-link {{ request()->routeIs('akademik.p5bk.*') ? 'active' : '' }}">
        <div class="akademik-nav-link-left">
          <i class="bi bi-award"></i>
          <span>Projek Penguatan P5BK</span>
        </div>
      </a>
      @endif
    </div>
    @endif

  </div>

  {{-- Footer Profile --}}
  <div style="padding:14px 16px; border-top:1px solid var(--ak-slate-200); font-size:12px; color:var(--ak-slate-600); display:flex; align-items:center; justify-content:space-between;">
    <div style="display:flex; align-items:center; gap:8px;">
      <div style="width:28px; height:28px; border-radius:50%; background:#e0e7ff; color:#4f46e5; display:flex; align-items:center; justify-content:center; font-weight:700;">
        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
      </div>
      <div>
        <div style="font-weight:700; color:var(--ak-dark); line-height:1.2;">{{ Str::limit(auth()->user()?->name ?? 'User', 14) }}</div>
        <div style="font-size:10.5px;">{{ auth()->user()?->role_display_name ?? 'Staf' }}</div>
      </div>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="margin:0;">
      @csrf
      <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer;" title="Keluar">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </form>
  </div>
</aside>
