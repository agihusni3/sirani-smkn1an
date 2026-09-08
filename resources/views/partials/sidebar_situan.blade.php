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

  {{-- 2. Shortcut DCC (Data Control Center) --}}
  <a href="{{ route('admin.portal') }}" class="situan-dcc-pill" title="Data Control Center (Pusat Kontrol Data)">
    <div style="display:flex; align-items:center; gap:8px;">
      <i class="bi bi-command"></i>
      <span>Pusat Kontrol Data</span>
    </div>
    <i class="bi bi-chevron-right" style="font-size:10px; opacity:0.6;"></i>
  </a>

  {{-- 3. Navigation Sections --}}
  <div class="situan-nav-body">

    {{-- 1. Operasional & Loket Pelayanan --}}
    <div class="situan-nav-group">
      <div class="situan-nav-group-title">Operasional &amp; Loket</div>
      
      <a href="{{ route('situan.index') }}" class="situan-nav-link {{ (request()->is('situan') || request()->is('situan/dashboard')) ? 'active' : '' }}" title="Dasbor Utama Statistik &amp; Pantauan Tata Usaha">
        <div class="situan-nav-link-left">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dasbor Tata Usaha</span>
        </div>
      </a>

      <a href="{{ route('situan.pelayanan.index') }}" class="situan-nav-link {{ request()->is('situan/pelayanan*') ? 'active' : '' }}" title="Loket Cetak Surat Keterangan Siswa Aktif, Mutasi, dan SKL Ber-QR Code">
        <div class="situan-nav-link-left">
          <i class="bi bi-file-earmark-check-fill"></i>
          <span>Loket Surat Siswa</span>
        </div>
        <span class="situan-nav-badge" style="background:#f0fdf4; color:#16a34a; border-color:#bbf7d0;">QR Valid</span>
      </a>
    </div>

    {{-- 2. Persuratan & Disposisi Dinas --}}
    <div class="situan-nav-group">
      <div class="situan-nav-group-title">Persuratan &amp; Disposisi</div>

      <a href="{{ route('situan.surat-masuk.index') }}" class="situan-nav-link {{ request()->is('situan/surat-masuk*') ? 'active' : '' }}" title="Buku Agenda Surat Masuk &amp; Lembar Disposisi Kepala Sekolah">
        <div class="situan-nav-link-left">
          <i class="bi bi-inbox-fill"></i>
          <span>Surat Masuk</span>
        </div>
        @php
          $menungguDisposisiCount = \App\Models\SuratMasuk::where('status_disposisi', 'menunggu')->count();
        @endphp
        @if($menungguDisposisiCount > 0)
          <span class="situan-nav-badge" style="background:#fef2f2; color:#ef4444; border-color:#fecaca;">{{ $menungguDisposisiCount }}</span>
        @endif
      </a>

      <a href="{{ route('situan.surat-keluar.index') }}" class="situan-nav-link {{ request()->is('situan/surat-keluar*') ? 'active' : '' }}" title="Buku Agenda Surat Keluar &amp; Generator Nomor Surat">
        <div class="situan-nav-link-left">
          <i class="bi bi-send-fill"></i>
          <span>Surat Keluar &amp; No. Agenda</span>
        </div>
      </a>

      <a href="{{ route('situan.buku-sk.index') }}" class="situan-nav-link {{ request()->is('situan/buku-sk*') ? 'active' : '' }}" title="Buku Register Surat Keputusan (SK) Kepala Sekolah">
        <div class="situan-nav-link-left">
          <i class="bi bi-journal-check"></i>
          <span>Buku Register SK</span>
        </div>
      </a>
    </div>

    {{-- 3. Kepegawaian & Kearsipan Sentral --}}
    @if($isAdmin || $isKepsek || $isStafTu || $isWakaKurikulum || $isWakasis || ($user && in_array($user->role, ['waka_sarpras', 'waka_hubin'])))
      <div class="situan-nav-group">
        <div class="situan-nav-group-title">Kepegawaian &amp; Arsip</div>

        <a href="{{ route('situan.ekabinet.index') }}" class="situan-nav-link {{ request()->is('situan/ekabinet*') ? 'active' : '' }}" title="Sentral Lemari Berkas Digital PTK, Dokumen Sekolah &amp; MoU Industri">
          <div class="situan-nav-link-left">
            <i class="bi bi-archive-fill"></i>
            <span>E-Kabinet &amp; Arsip</span>
          </div>
          <span class="situan-nav-badge" style="background:#f0fdf4; color:#16a34a; border-color:#bbf7d0;">Sentral</span>
        </a>

        <a href="{{ route('situan.radar-kgb.index') }}" class="situan-nav-link {{ request()->is('situan/radar-kgb*') ? 'active' : '' }}" title="Radar Kenaikan Gaji Berkala (KGB) &amp; Pangkat Guru/Pegawai">
          <div class="situan-nav-link-left">
            <i class="bi bi-radar"></i>
            <span>Radar KGB &amp; Pangkat</span>
          </div>
          <span class="situan-nav-badge" style="background:#eff6ff; color:#2563eb; border-color:#bfdbfe;">Berkala</span>
        </a>

        <a href="/guru" class="situan-nav-link {{ request()->is('guru*') ? 'active' : '' }}" title="Master Data Pendidik &amp; Tenaga Kependidikan">
          <div class="situan-nav-link-left">
            <i class="bi bi-person-badge-fill"></i>
            <span>Data Pokok PTK</span>
          </div>
          <span class="situan-nav-badge">{{ $countGuru }}</span>
        </a>
      </div>
    @endif

    {{-- 4. Administrasi Kesiswaan & Kelas --}}
    <div class="situan-nav-group">
      <div class="situan-nav-group-title">Kesiswaan &amp; Kelas</div>

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

      @if($isAdmin || $isStafTu)
        <a href="/kartu-rfid" class="situan-nav-link {{ request()->is('kartu-rfid*') || request()->is('manajemen-rfid*') ? 'active' : '' }}">
          <div class="situan-nav-link-left">
            <i class="bi bi-person-vcard-fill"></i>
            <span>Kartu &amp; Identitas</span>
          </div>
        </a>
      @endif
    </div>

    {{-- 5. Tata Usaha & Audit --}}
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
