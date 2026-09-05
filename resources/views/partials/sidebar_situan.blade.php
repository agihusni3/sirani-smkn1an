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
<div class="mobile-topbar no-print">
  <div style="display:flex; align-items:center; gap:10px;">
    <div style="width:34px; height:34px; border-radius:8px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; padding:3px;">
      <img src="/img/logo.png" alt="Logo" style="width:100%; height:100%; object-fit:contain;" />
    </div>
    <div>
      <span style="font-weight:900; font-size:16px; letter-spacing:-0.02em; display:block; line-height:1.1; color:#0284c7;">SITUAN</span>
      <span style="font-size:10px; color:var(--text-3); font-weight:600;">Tata Usaha &amp; Data Pokok</span>
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

{{-- Sidebar Navigation: Khusus Workspace Modul SITUAN (Tata Usaha & Data Pokok) --}}
<aside class="sidebar" id="appSidebar">
  <div class="brand" style="margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
    <div class="brand-left" style="display:flex; align-items:center; gap:10px;">
      <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, rgba(2,132,199,0.18), rgba(3,105,161,0.06)); border:1px solid rgba(2,132,199,0.25); display:flex; align-items:center; justify-content:center; padding:4px;">
        <i class="bi bi-buildings-fill" style="color:#0284c7; font-size:18px;"></i>
      </div>
      <div class="brand-text">
        <div style="font-weight:900; font-size:16.5px; letter-spacing:-0.02em; color:#0284c7;">SITUAN</div>
        <div style="font-size:11px; color:var(--text-3); font-weight:600;">Tata Usaha &amp; Data Pokok</div>
      </div>
    </div>
    <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" onclick="window.closeSmknSidebar()" aria-label="Tutup Menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  {{-- Tombol Navigasi Kembali ke DCC SMKN 1 AN --}}
  <div style="margin-bottom:16px;">
    <a href="{{ route('admin.portal') }}" class="btn" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:8px 12px; font-size:11.5px; font-weight:800; background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color:#ffffff; border:1px solid rgba(255,255,255,0.12); border-radius:var(--r-sm); text-decoration:none; box-shadow:0 3px 10px rgba(0,0,0,0.12); box-sizing:border-box;" title="Buka Digital Command Center SMKN 1 AN">
      <i class="bi bi-command" style="color:#38bdf8; font-size:13.5px;"></i>
      <span>DCC SMKN 1 AN</span>
    </a>
  </div>

  {{-- 1. MENU UTAMA SITUAN --}}
  <div class="nav-group">
    <div class="nav-label">Navigasi Utama</div>
    
    <a href="{{ route('situan.index') }}" class="nav-item {{ (request()->is('situan') || request()->is('situan/dashboard')) ? 'active' : '' }}" title="Dasbor Statistik Administrasi &amp; Data Pokok">
      <div class="nav-left-part">
        <i class="bi bi-speedometer2 nav-icon" style="color:#0284c7;"></i>
        <span class="nav-text">Dasbor Tata Usaha</span>
      </div>
    </a>
  </div>

  {{-- 2. MASTER KEPEGAWAIAN (PTK) --}}
  @if($isAdmin || $isKepsek || $isStafTu || $isWakaKurikulum || $isWakasis)
    <div class="nav-group">
      <div class="nav-label">Master Kepegawaian (PTK)</div>

      <a href="/guru" class="nav-item {{ request()->is('guru*') ? 'active' : '' }}" title="Kelola Data Guru, Staf &amp; Tenaga Kependidikan">
        <div class="nav-left-part">
          <i class="bi bi-person-badge-fill nav-icon" style="color:#0284c7;"></i>
          <span class="nav-text">Data Guru &amp; Pegawai</span>
        </div>
        <span class="nav-count-badge" style="background:#e0f2fe; color:#0284c7; border-color:#bae6fd;">{{ $countGuru }}</span>
      </a>
    </div>
  @endif

  {{-- 3. MASTER KESISWAAN & AKADEMIK --}}
  <div class="nav-group">
    <div class="nav-label">Master Kesiswaan &amp; Kelas</div>

    <a href="/siswa" class="nav-item {{ request()->is('siswa*') ? 'active' : '' }}" title="Kelola Data Induk Peserta Didik">
      <div class="nav-left-part">
        <i class="bi bi-people-fill nav-icon" style="color:#10b981;"></i>
        <span class="nav-text">{{ $isWali && !$isAdmin && !$isStafTu ? 'Data Siswa Binaan' : 'Data Siswa' }}</span>
      </div>
      @if($isWali && !$isAdmin && !$isStafTu)
        <span class="nav-count-badge" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0; font-size:10px;">Kelas</span>
      @else
        <span class="nav-count-badge" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0;">{{ $countSiswa }}</span>
      @endif
    </a>

    @if(!$isWali || $isAdmin || $isStafTu)
      <a href="/rombel" class="nav-item {{ request()->is('rombel*') ? 'active' : '' }}" title="Kelola Rombongan Belajar, Jurusan &amp; Tahun Ajaran">
        <div class="nav-left-part">
          <i class="bi bi-building nav-icon" style="color:#d97706;"></i>
          <span class="nav-text">Rombongan Belajar</span>
        </div>
        <span class="nav-count-badge" style="background:#fef3c7; color:#d97706; border-color:#fde68a;">{{ $countRombel }}</span>
      </a>
    @endif

    @if($isAdmin || $isWakasis || $isStafTu || $isWakaKurikulum)
      <a href="/siklus-siswa" class="nav-item {{ request()->is('siklus-siswa*') ? 'active' : '' }}" title="Transisi Kenaikan Kelas, PKL &amp; Kelulusan Alumni">
        <div class="nav-left-part">
          <i class="bi bi-arrow-repeat nav-icon" style="color:#6366f1;"></i>
          <span class="nav-text">Siklus Akademik</span>
        </div>
      </a>
    @endif

    @if($isAdmin || $isStafTu)
      <a href="/kartu-rfid" class="nav-item {{ request()->is('kartu-rfid*') || request()->is('manajemen-rfid*') ? 'active' : '' }}" title="Pusat Cetak Kartu QR Barcode &amp; Registrasi RFID">
        <div class="nav-left-part">
          <i class="bi bi-person-vcard-fill nav-icon" style="color:#0ea5e9;"></i>
          <span class="nav-text">Kartu Barcode &amp; RFID</span>
        </div>
      </a>
    @endif
  </div>

  {{-- 4. ADMINISTRASI & KELEMBAGAAN --}}
  @if($isAdmin || $isKepsek || $isStafTu)
    <div class="nav-group">
      <div class="nav-label">Administrasi Kelembagaan</div>

      <a href="{{ route('situan.log') }}" class="nav-item {{ request()->is('situan/log*') ? 'active' : '' }}" title="Riwayat &amp; Log Mutasi Data Tata Usaha">
        <div class="nav-left-part">
          <i class="bi bi-journal-text nav-icon" style="color:#0284c7;"></i>
          <span class="nav-text">Riwayat &amp; Log TU</span>
        </div>
      </a>

      @if($isAdmin)
        <a href="/pengaturan-sekolah" class="nav-item {{ request()->is('pengaturan-sekolah*') ? 'active' : '' }}" title="Profil Lembaga, Identitas Sekolah &amp; Kop Surat Resmi">
          <div class="nav-left-part">
            <i class="bi bi-bank2 nav-icon" style="color:#475569;"></i>
            <span class="nav-text">Profil Lembaga</span>
          </div>
        </a>

        <a href="/backup" class="nav-item {{ request()->is('backup*') ? 'active' : '' }}" title="Pencadangan Database &amp; Pemulihan Data">
          <div class="nav-left-part">
            <i class="bi bi-database-down nav-icon" style="color:#475569;"></i>
            <span class="nav-text">Cadangan Database</span>
          </div>
        </a>
      @endif

      @if($isAdmin || $isKepsek || $isWakasis)
        <a href="/audit" class="nav-item {{ request()->is('audit*') ? 'active' : '' }}" title="Audit Trail Global DCC">
          <div class="nav-left-part">
            <i class="bi bi-shield-lock-fill nav-icon" style="color:#475569;"></i>
            <span class="nav-text">Audit Global (DCC)</span>
          </div>
        </a>
      @endif
    </div>
  @endif

  {{-- 5. LINTAS MODUL EKOSISTEM --}}
  <div class="nav-group" style="margin-top:auto; padding-top:14px; border-top:1px solid var(--border);">
    <div class="nav-label">Modul Terhubung</div>

    <a href="/dashboard" class="nav-item" title="Buka Modul Presensi &amp; Absensi SIRANI">
      <div class="nav-left-part">
        <i class="bi bi-fingerprint nav-icon" style="color:#10b981;"></i>
        <span class="nav-text">SIRANI Presensi</span>
      </div>
      <i class="bi bi-arrow-up-right" style="font-size:11px; color:var(--text-3);"></i>
    </a>

    @if($user && ($user->canAccessPpdb() || $isAdmin || $isKepsek))
      <a href="/admin/ppdb" class="nav-item" title="Buka Modul Penerimaan Peserta Didik Baru">
        <div class="nav-left-part">
          <i class="bi bi-mortarboard-fill nav-icon" style="color:#d97706;"></i>
          <span class="nav-text">PPDB 2026</span>
        </div>
        <i class="bi bi-arrow-up-right" style="font-size:11px; color:var(--text-3);"></i>
      </a>
    @endif

    @if($user && ($user->canAccessWebHumas() || $isAdmin || $isKepsek))
      <a href="/admin/berita" class="nav-item" title="Buka Modul Humas &amp; Publikasi Web">
        <div class="nav-left-part">
          <i class="bi bi-globe-americas nav-icon" style="color:#6366f1;"></i>
          <span class="nav-text">Humas &amp; Web</span>
        </div>
        <i class="bi bi-arrow-up-right" style="font-size:11px; color:var(--text-3);"></i>
      </a>
    @endif
  </div>
</aside>
