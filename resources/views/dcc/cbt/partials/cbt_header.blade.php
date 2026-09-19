@php
  $user = Auth::user();
  $roleLabel = 'GURU';
  if ($user) {
    if (in_array($user->role, ['admin', 'kepala_sekolah', 'waka_kurikulum']) ||
        ($user->roles && in_array($user->roles, ['admin', 'kepala_sekolah', 'waka_kurikulum']))) {
        $roleLabel = 'SUPERVISOR';
    }
  }
  $currentRoute = Route::currentRouteName();
@endphp

<header class="cbt-navbar-top">
  <div style="display:flex; align-items:center; gap:16px;">
    <a href="{{ route('admin.cbt.dashboard') }}" class="cbt-brand-section">
      <img src="{{ asset('images/logo.png') }}" alt="Logo SMK" class="cbt-brand-logo" onerror="this.src='{{ asset('apple-touch-icon.png') }}'" />
      <div class="cbt-brand-text">
        <h1>DCC ASESMEN & BELAJAR TUNTAS</h1>
        <span>SMK NEGERI 1 AIR NANINGAN</span>
      </div>
    </a>
  </div>

  <div class="cbt-user-profile">
    <span class="cbt-role-badge">{{ $roleLabel }}</span>
    <div style="text-align:right; display:none; @media (min-width: 640px) { display:block; }">
      <div style="font-weight:700; font-size:13px; color:#ffffff; line-height:1.2;">
        {{ $user->name ?? 'Tenaga Pendidik' }}
      </div>
      <div style="font-size:11px; color:#93c5fd;">{{ $user->email ?? '' }}</div>
    </div>
    <div class="cbt-user-avatar">
      {{ strtoupper(substr($user->name ?? 'G', 0, 1)) }}
    </div>
    <a href="{{ url('/admin/dashboard') }}" title="Kembali ke Dashboard Utama SIRANI" style="color:#93c5fd; text-decoration:none; margin-left:8px; font-size:18px;">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</header>

<!-- Sub-Navbar Horizontal Navigation (Identik Kejar.id) -->
<nav class="cbt-subbar">
  <a href="{{ route('admin.cbt.dashboard') }}" class="cbt-tab-item {{ $currentRoute === 'admin.cbt.dashboard' ? 'active' : '' }}">
    <i class="bi bi-grid-fill"></i> Beranda
  </a>
  <a href="{{ route('admin.cbt.bank.index') }}" class="cbt-tab-item {{ str_contains($currentRoute, 'admin.cbt.bank') || str_contains($currentRoute, 'admin.cbt.soal') ? 'active' : '' }}">
    <i class="bi bi-collection-fill"></i> Bank Soal
  </a>
  <a href="{{ route('admin.cbt.jadwal.index') }}" class="cbt-tab-item {{ str_contains($currentRoute, 'admin.cbt.jadwal') ? 'active' : '' }}">
    <i class="bi bi-calendar-check-fill"></i> Jadwal & Sesi Ujian
  </a>
  <a href="{{ route('admin.cbt.cetak.kartu') }}" class="cbt-tab-item {{ $currentRoute === 'admin.cbt.cetak.kartu' ? 'active' : '' }}">
    <i class="bi bi-person-badge-fill"></i> Cetak Kartu Ujian
  </a>
  <a href="{{ route('cbt.siswa.login') }}" target="_blank" class="cbt-tab-item" style="margin-left:auto; color:#0284c7;">
    <i class="bi bi-box-arrow-up-right"></i> Buka Portal Ujian Siswa
  </a>
</nav>
