{{-- Header Actions: Clean Minimalist Theme Switch + Account Dropdown (Logo/Icon Only) --}}
<div class="header-actions" style="display:inline-flex; align-items:center; gap:6px; flex-shrink:0;">

  {{-- Minimalist Theme Switcher (Logo/Icon Only) --}}
  <button type="button" class="btn btn-icon-header" onclick="window.toggleTheme()" style="width:36px; height:36px; min-width:36px; max-width:36px; padding:0; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); cursor:pointer;" title="Ganti Tema (Terang / Gelap)" aria-label="Ganti Tema">
    <i class="bi bi-sun-fill theme-toggle-icon" style="color:var(--text); font-size:14.5px;"></i>
  </button>

  @auth
  @php
    $currentUser = auth()->user();
    $canAccessPortal = $currentUser && ($currentUser->isAdmin() || $currentUser->isKepalaSekolah());
    $availableRolesData = $currentUser ? $currentUser->getAvailableRolesData() : [];
    $hasMultiRole = count($availableRolesData) > 1;
    $activeRoleMeta = $currentUser ? \App\Models\User::getRoleMetadata($currentUser->getActiveRole()) : null;
  @endphp

  @if($canAccessPortal)
    {{-- 9-Dots Modular Ecosystem Switcher --}}
    <div class="app-launcher-wrap" style="position:relative; flex-shrink:0;">
      <button type="button" class="btn btn-icon-header" onclick="window.toggleAppLauncherDropdown(event, this)" style="width:36px; height:36px; min-width:36px; max-width:36px; padding:0; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); cursor:pointer;" title="DCC SMKN 1 AN · Digital Command Center" aria-label="Switcher Modul DCC">
        <i class="bi bi-grid-3x3-gap-fill" style="color:var(--text); font-size:14.5px;"></i>
      </button>

      <div class="app-launcher-dropdown acct-dropdown" style="right:0; left:auto; top:calc(100% + 6px); width:310px; min-width:290px; max-width:min(90vw, 340px); background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); box-shadow:0 18px 45px rgba(0,0,0,0.3); z-index:99999;">
        <div style="padding:11px 14px; background:var(--bg-3); border-bottom:1px solid var(--border-2); display:flex; align-items:center; justify-content:space-between;">
          <div style="font-size:11.5px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:6px;">
            <i class="bi bi-cpu-fill" style="color:#2563eb;"></i> DCC SMKN 1 AN
          </div>
          <a href="{{ route('admin.portal') }}" style="font-size:11px; font-weight:800; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:2px;">
            Buka DCC <i class="bi bi-arrow-right-short"></i>
          </a>
        </div>
        <div style="padding:10px; display:grid; grid-template-columns:repeat(3, 1fr); gap:8px;">
          <a href="{{ route('admin.portal') }}" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:10px 4px; border-radius:10px; text-decoration:none; color:var(--text); background:var(--surface); border:1px solid var(--border); transition:all 0.15s ease;">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(37,99,235,0.1); color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:5px;">
              <i class="bi bi-speedometer2"></i>
            </div>
            <span style="font-size:10px; font-weight:800; line-height:1.2;">DCC Portal</span>
            <span style="font-size:8.5px; color:var(--text-3); margin-top:2px;">Command Center</span>
          </a>

          <a href="/dashboard" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:10px 4px; border-radius:10px; text-decoration:none; color:var(--text); background:var(--surface); border:1px solid var(--border); transition:all 0.15s ease;">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:5px;">
              <i class="bi bi-fingerprint"></i>
            </div>
            <span style="font-size:10px; font-weight:800; line-height:1.2;">SIRANI</span>
            <span style="font-size:8.5px; color:var(--text-3); margin-top:2px;">Presensi</span>
          </a>

          <a href="/admin/ppdb" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:10px 4px; border-radius:10px; text-decoration:none; color:var(--text); background:var(--surface); border:1px solid var(--border); transition:all 0.15s ease;">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(217,119,6,0.1); color:#d97706; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:5px;">
              <i class="bi bi-person-badge-fill"></i>
            </div>
            <span style="font-size:10px; font-weight:800; line-height:1.2;">PPDB 2026</span>
            <span style="font-size:8.5px; color:var(--text-3); margin-top:2px;">Penerimaan</span>
          </a>

          <a href="/admin/berita" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:10px 4px; border-radius:10px; text-decoration:none; color:var(--text); background:var(--surface); border:1px solid var(--border); transition:all 0.15s ease;">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(99,102,241,0.1); color:#6366f1; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:5px;">
              <i class="bi bi-globe-americas"></i>
            </div>
            <span style="font-size:10px; font-weight:800; line-height:1.2;">Web Profil</span>
            <span style="font-size:8.5px; color:var(--text-3); margin-top:2px;">Humas CMS</span>
          </a>

          <a href="/smart-gate" target="_blank" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:10px 4px; border-radius:10px; text-decoration:none; color:var(--text); background:var(--surface); border:1px solid var(--border); transition:all 0.15s ease;">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(14,165,233,0.1); color:#0ea5e9; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:5px;">
              <i class="bi bi-upc-scan"></i>
            </div>
            <span style="font-size:10px; font-weight:800; line-height:1.2;">Smart Gate</span>
            <span style="font-size:8.5px; color:var(--text-3); margin-top:2px;">Kiosk Live</span>
          </a>

          <a href="/audit" style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:10px 4px; border-radius:10px; text-decoration:none; color:var(--text); background:var(--surface); border:1px solid var(--border); transition:all 0.15s ease;">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(100,116,139,0.1); color:#64748b; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:5px;">
              <i class="bi bi-shield-check"></i>
            </div>
            <span style="font-size:10px; font-weight:800; line-height:1.2;">Audit Log</span>
            <span style="font-size:8.5px; color:var(--text-3); margin-top:2px;">Keamanan</span>
          </a>
        </div>
      </div>
    </div>
  @endif

  @if($hasMultiRole && $activeRoleMeta)
    {{-- Role Switcher: Minimalist Pill Trigger with Floating Menu --}}
    <div class="role-switch-wrap" style="position:relative; flex-shrink:0;">
      <button class="btn role-switch-btn" onclick="window.toggleRoleSwitchDropdown(event, this)" type="button" aria-label="Beralih Mode Peran" title="Mode Aktif: {{ $activeRoleMeta['name'] }} (Klik untuk Ganti Peran)" style="display:inline-flex; align-items:center; gap:6px; height:36px; padding:0 10px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); cursor:pointer; font-size:12px; font-weight:800; color:var(--text); transition:all 0.15s ease;">
        <i class="bi {{ $activeRoleMeta['icon'] }}" style="color:#000000; font-size:13.5px;"></i>
        <span class="role-switch-badge" style="max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; letter-spacing:-0.01em;">{{ $activeRoleMeta['badge'] }}</span>
        <i class="bi bi-chevron-down" style="font-size:9.5px; opacity:0.5; margin-left:1px;"></i>
      </button>

      <div class="role-switch-dropdown acct-dropdown" style="right:0; left:auto; top:calc(100% + 6px); width:280px; min-width:260px; max-width:min(90vw, 320px); background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); box-shadow:0 16px 45px rgba(0,0,0,0.28); z-index:99999;">
        <div style="padding:12px 14px; background:var(--bg-3); border-bottom:1px solid var(--border-2); display:flex; align-items:center; justify-content:space-between;">
          <div style="font-size:12px; font-weight:800; color:var(--text); display:flex; align-items:center; gap:6px;">
            <i class="bi bi-arrow-repeat" style="color:#10b981; font-size:14px;"></i> Beralih Mode Peran
          </div>
          <span style="font-size:10px; font-weight:700; color:var(--text-3); background:var(--bg-2); padding:1px 6px; border-radius:4px; border:1px solid var(--border);">
            {{ count($availableRolesData) }} Peran
          </span>
        </div>
        <div style="padding:8px; max-height:300px; overflow-y:auto;">
          @foreach($availableRolesData as $rData)
            <form action="{{ route('switch-role') }}" method="POST" style="margin:0;">
              @csrf
              <input type="hidden" name="role" value="{{ $rData['role'] }}">
              <button type="submit" class="acct-dropdown-item {{ $rData['is_active'] ? 'active-role-item' : '' }}" style="width:100%; text-align:left; padding:9px 12px; font-size:12px; font-weight:{{ $rData['is_active'] ? '800' : '600' }}; color:var(--text); background:{{ $rData['is_active'] ? 'rgba(0,0,0,0.06)' : 'none' }}; border:1px solid {{ $rData['is_active'] ? 'var(--border-2)' : 'transparent' }}; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; margin-bottom:4px; box-sizing:border-box;">
                <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                  <i class="bi {{ $rData['icon'] }}" style="font-size:14px; color:{{ $rData['is_active'] ? '#000000' : 'var(--text-2)' }};"></i>
                  <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $rData['name'] }}</div>
                </div>
                @if($rData['is_active'])
                  <span style="display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:50%; background:#10B981; color:#ffffff; font-size:10px; font-weight:900; flex-shrink:0;">
                    <i class="bi bi-check"></i>
                  </span>
                @endif
              </button>
            </form>
          @endforeach
        </div>
      </div>
    </div>
  @endif

  {{-- Account Dropdown (Logo/Avatar Only) --}}
  <div class="acct-wrap" style="position:relative; flex-shrink:0;">
    <button class="acct-btn btn-icon-header" onclick="window.toggleAcctDropdown(event, this)" type="button" aria-label="Akun & Logout" title="{{ auth()->user()?->name ?? 'Admin' }} (Klik untuk Menu / Logout)" style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; min-width:36px; max-width:36px; flex-shrink:0; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0; cursor:pointer;">
      <span style="width:24px; height:24px; border-radius:50%; background:rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.12); color:#000000; display:inline-flex; align-items:center; justify-content:center; font-size:11.5px; font-weight:800;">
        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
      </span>
    </button>

    <div class="acct-dropdown" style="right:0; left:auto; top:calc(100% + 6px); width:270px; min-width:260px; max-width:min(90vw, 320px); background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-md); box-shadow:0 16px 45px rgba(0,0,0,0.28); z-index:99999;">
      <div class="acct-dropdown-header" style="padding:14px 16px; background:var(--bg-3); border-bottom:1px solid var(--border-2);">
        <div>
          <div class="acct-dropdown-name" style="font-size:13.5px; font-weight:800; color:var(--text);">{{ auth()->user()?->name ?? 'Administrator' }}</div>
          <div class="acct-dropdown-email" style="font-size:11px; color:var(--text-3); margin-top:2px;">
            @if(auth()->user()?->username)
              <span style="font-weight:700; color:var(--text-2);">@ {{ auth()->user()->username }}</span>
            @endif
            @if(auth()->user()?->email)
              · {{ auth()->user()->email }}
            @endif
          </div>
          @php
            $u = auth()->user();
            $roleLabel = $u ? $u->role_display_name : 'Pengguna';
          @endphp
          <div style="margin-top:6px;">
            <span class="acct-dropdown-role" style="font-size:10px; font-weight:700; font-family:var(--font-mono); background:rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.12); color:#000000; padding:2px 8px; border-radius:6px;">{{ $roleLabel }}</span>
          </div>
        </div>
      </div>
      <div style="padding:8px; background:var(--bg-2);">
        @if(auth()->user()?->guru)
          <button type="button" onclick="openModalKartuGuruSaya()" class="acct-dropdown-item" style="width:100%; text-align:left; padding:9px 12px; font-size:12px; font-weight:800; color:#0284c7; background:rgba(2,132,199,0.08); border:1px solid rgba(2,132,199,0.2); border-radius:6px; cursor:pointer; display:flex; align-items:center; gap:8px; white-space:nowrap; box-sizing:border-box; margin-bottom:6px;">
            <i class="bi bi-qr-code-scan" style="font-size:14.5px; color:#0284c7;"></i> Kartu &amp; QR Presensi Saya
          </button>
        @endif

        @if($hasMultiRole)
          <div style="padding:6px 8px 2px; font-size:10px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.5px; border-top:1px solid var(--border-2); margin-top:4px;">
            Pilihan Mode Peran
          </div>
          <div style="margin-bottom:6px;">
            @foreach($availableRolesData as $rData)
              <form action="{{ route('switch-role') }}" method="POST" style="margin:0;">
                @csrf
                <input type="hidden" name="role" value="{{ $rData['role'] }}">
                <button type="submit" class="acct-dropdown-item" style="width:100%; text-align:left; padding:7px 10px; font-size:11.5px; font-weight:{{ $rData['is_active'] ? '800' : '600' }}; color:var(--text); background:{{ $rData['is_active'] ? 'rgba(0,0,0,0.04)' : 'none' }}; border:none; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; box-sizing:border-box;">
                  <span style="display:flex; align-items:center; gap:6px;">
                    <i class="bi {{ $rData['icon'] }}" style="font-size:12px; color:{{ $rData['is_active'] ? '#000000' : 'var(--text-2)' }};"></i>
                    {{ $rData['badge'] }}
                  </span>
                  @if($rData['is_active'])
                    <i class="bi bi-check-circle-fill" style="color:#10B981; font-size:12px;"></i>
                  @endif
                </button>
              </form>
            @endforeach
          </div>
        @endif

        <button type="button" onclick="openModalProfilMandiri()" class="acct-dropdown-item" style="width:100%; text-align:left; padding:9px 12px; font-size:12px; font-weight:700; color:var(--text); background:none; border:none; border-radius:6px; cursor:pointer; display:flex; align-items:center; gap:8px; white-space:nowrap; box-sizing:border-box;">
          <i class="bi bi-person-gear" style="font-size:14.5px; color:#000000;"></i> Pengaturan Akun &amp; Password
        </button>

        <form action="{{ route('logout') }}" method="POST" style="margin:0">
          @csrf
          <button type="submit" class="acct-dropdown-item acct-logout" style="width:100%; text-align:left; padding:9px 12px; font-size:12px; font-weight:700; color:#EF4444; background:none; border:none; border-radius:6px; cursor:pointer; display:flex; align-items:center; gap:8px; white-space:nowrap; box-sizing:border-box;">
            <i class="bi bi-box-arrow-right" style="font-size:14px;"></i> Keluar (Logout)
          </button>
        </form>
      </div>
    </div>
  </div>
  @endauth

</div>

{{-- Universal Modal Pengaturan Akun & Password Mandiri --}}
@auth
@if(!defined('MODAL_PROFIL_MANDIRI_RENDERED'))
@php define('MODAL_PROFIL_MANDIRI_RENDERED', true); @endphp
<div id="modalProfilMandiri" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; padding:16px;">
  <div class="modal-card" style="background:var(--surface); border:1px solid var(--border-2); border-radius:16px; max-width:460px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.25);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border-2);">
      <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-person-gear" style="color:#000000;"></i> Pengaturan Akun &amp; Password
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalProfilMandiri()" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('profil.update') }}" method="POST">
      @csrf
      
      <div style="margin-bottom:14px;">
        <label style="font-weight:700; font-size:12px; color:var(--text); display:block; margin-bottom:5px;">Nama Lengkap <span style="color:var(--red);">*</span></label>
        <input type="text" name="name" value="{{ auth()->user()?->name }}" required class="input-field" style="width:100%; height:38px; padding:0 12px; font-size:13px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
      </div>

      <div style="margin-bottom:14px;">
        <label style="font-weight:700; font-size:12px; color:var(--text); display:block; margin-bottom:5px;">
          Username / ID Pengguna <span style="color:var(--red);">*</span>
        </label>
        <input type="text" name="username" value="{{ auth()->user()?->username }}" required placeholder="Contoh: agihusni atau nama Anda" class="input-field" style="width:100%; height:38px; padding:0 12px; font-size:13px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text); font-weight:600;" />
        <div style="font-size:11px; color:var(--text-3); margin-top:4px; line-height:1.4;">
          💡 Bebas menggunakan nama masing-masing atau nama panggilan (tidak wajib email).
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <label style="font-weight:700; font-size:12px; color:var(--text); display:block; margin-bottom:5px;">
          Email <span style="font-size:10.5px; color:var(--text-3); font-weight:500;">(Opsional)</span>
        </label>
        <input type="email" name="email" value="{{ auth()->user()?->email }}" placeholder="contoh@smkn1airnaningan.sch.id (opsional)" class="input-field" style="width:100%; height:38px; padding:0 12px; font-size:13px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); color:var(--text);" />
      </div>

      <div style="background:var(--bg-2); border:1px solid var(--border-2); border-radius:12px; padding:12px 14px; margin-bottom:18px;">
        <div style="font-weight:800; font-size:12px; color:var(--text); margin-bottom:8px;">Ganti Kata Sandi (Kosongkan jika tidak diubah)</div>
        
        <div style="margin-bottom:10px;">
          <input type="password" name="password" placeholder="Kata sandi baru (min. 4 karakter)" autocomplete="new-password" class="input-field" style="width:100%; height:36px; padding:0 10px; font-size:12px; background:var(--surface); border:1px solid var(--border-2); border-radius:6px; color:var(--text);" />
        </div>

        <div>
          <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" autocomplete="new-password" class="input-field" style="width:100%; height:36px; padding:0 10px; font-size:12px; background:var(--surface); border:1px solid var(--border-2); border-radius:6px; color:var(--text);" />
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalProfilMandiri()" style="height:36px; padding:0 14px; font-size:12px; font-weight:700; border-radius:var(--r-sm);">Batal</button>
        <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1px solid #000000; font-weight:800; padding:0 16px; height:36px; font-size:12px; border-radius:var(--r-sm); cursor:pointer;">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- Universal Modal Kartu & QR Presensi Guru Mandiri --}}
@if(!defined('MODAL_KARTU_GURU_SAYA_RENDERED') && auth()->user()->guru)
@php
  define('MODAL_KARTU_GURU_SAYA_RENDERED', true);
  $gSaya = auth()->user()->guru->loadMissing('kartuRfid');
  $codeSaya = $gSaya->kartuRfid?->uid ?? ($gSaya->nip ?: 'GURU-'.$gSaya->id);
  $linkMobileSaya = url('/kartu-digital-guru/' . $gSaya->id);
  $sekolahAktif = \App\Models\PengaturanSekolah::getAktif();
  $namaSekolahAktif = $sekolahAktif->nama_sekolah ?? 'SMKN 1 AIR NANINGAN';
@endphp
<div id="modalKartuGuruSaya" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.75); backdrop-filter:blur(4px); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div class="modal-card" style="background:var(--surface); border:1px solid var(--border-2); border-radius:18px; max-width:440px; width:100%; padding:20px; box-shadow:0 25px 50px rgba(0,0,0,0.35); text-align:center;">
    
    {{-- Header Modal --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border-2);">
      <div style="text-align:left;">
        <span style="font-size:10px; font-weight:800; color:#0284c7; text-transform:uppercase; letter-spacing:0.05em; display:block;">AKSES PRESENSI RESMI</span>
        <h3 style="font-size:15px; font-weight:900; color:var(--text); margin:0;">Kartu &amp; QR Presensi Saya</h3>
      </div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalKartuGuruSaya()" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- Kartu Mini Elegan (Soft Sapphire Theme) --}}
    <div style="background:linear-gradient(135deg, #0f2744 0%, #1d4ed8 60%, #0284c7 100%); border-radius:14px; padding:16px 14px; color:#ffffff; margin-bottom:16px; box-shadow:0 8px 24px rgba(2,132,199,0.25); text-align:left; position:relative; overflow:hidden;">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.2); padding-bottom:8px;">
        <div style="display:flex; align-items:center; gap:8px;">
          <img src="{{ !empty($sekolahAktif->logo_sekolah) ? asset('storage/'.$sekolahAktif->logo_sekolah) : '/img/logo.png' }}" alt="Logo" style="width:24px; height:24px; object-fit:contain;" onerror="this.src='/img/logo.png';" />
          <span style="font-size:10px; font-weight:900; letter-spacing:0.04em; text-transform:uppercase;">{{ $namaSekolahAktif }}</span>
        </div>
        <span style="font-size:9px; font-weight:800; background:#f0f9ff; color:#0284c7; padding:2px 8px; border-radius:10px;">GURU &amp; STAF</span>
      </div>

      <div style="display:flex; gap:12px; align-items:center;">
        <img src="{{ $gSaya->foto_url ?? '/img/user-default.png' }}" alt="{{ $gSaya->nama }}" style="width:50px; height:50px; border-radius:10px; object-fit:cover; border:1.5px solid #7dd3fc; background:#ffffff;" onerror="this.src='/img/user-default.png'" />
        <div style="flex:1; min-width:0;">
          <div style="font-size:14px; font-weight:900; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $gSaya->nama }}">{{ $gSaya->nama }}</div>
          <div style="font-size:11px; color:#e0f2fe; margin-top:2px; font-family:var(--font-mono);">NIP: {{ $gSaya->nip ?: '-' }}</div>
          <div style="font-size:11px; color:#bae6fd; margin-top:1px;">{{ $gSaya->jabatan ?? 'Pendidik' }}</div>
        </div>
      </div>
    </div>

    {{-- Area 2D QR Code Presensi --}}
    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:14px; padding:16px; margin-bottom:16px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
      <div style="font-size:11px; font-weight:800; color:#0284c7; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">
        <i class="bi bi-qr-code-scan"></i> SCANNER GERBANG &amp; KIOSK
      </div>
      
      <div style="background:#ffffff; padding:10px; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 4px 12px rgba(0,0,0,0.06); display:inline-flex; align-items:center; justify-content:center;">
        <div id="modalQrGuruWrap" style="width:140px; height:140px; display:flex; align-items:center; justify-content:center;"></div>
      </div>

      <div style="margin-top:10px; font-family:var(--font-mono); font-size:13px; font-weight:900; color:var(--text); letter-spacing:0.03em;">
        {{ $codeSaya }}
      </div>
      <div style="font-size:11px; color:var(--text-3); margin-top:2px;">
        Tunjukkan QR Code ini ke scanner kamera gerbang sekolah saat presensi datang/pulang.
      </div>
    </div>

    {{-- Aksi Cepat: Fullscreen, Simpan QR, Kirim WA --}}
    <div style="display:flex; flex-direction:column; gap:8px;">
      <div style="display:flex; gap:8px;">
        <a href="{{ $linkMobileSaya }}" target="_blank" class="btn" style="flex:1; background:#0284c7; color:#ffffff; border:none; font-weight:800; font-size:12px; padding:10px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none;">
          <i class="bi bi-phone"></i> Buka Layar Penuh
        </a>
        <button type="button" onclick="downloadQrGuruModal()" class="btn btn-outline" style="flex:1; font-weight:800; font-size:12px; padding:10px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
          <i class="bi bi-download"></i> Simpan Gambar QR
        </button>
      </div>

      @if($gSaya->no_hp)
        <button type="button" id="btnKirimWaGuruSaya" onclick="kirimWaGuruSaya('{{ $gSaya->id }}')" class="btn" style="width:100%; background:#16a34a; color:#ffffff; border:none; font-weight:800; font-size:12px; padding:9px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:6px; cursor:pointer;">
          <i class="bi bi-whatsapp"></i> Kirim Akses Kartu ke WhatsApp Saya
        </button>
      @endif
    </div>

  </div>
</div>
@endif
@endauth

<script>
window.openModalProfilMandiri = function() {
  document.querySelectorAll('.acct-dropdown').forEach(d => d.classList.remove('open'));
  const modal = document.getElementById('modalProfilMandiri');
  if (modal) {
    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
    modal.classList.add('active', 'open');
    modal.style.display = 'flex';
  }
};

window.closeModalProfilMandiri = function() {
  const modal = document.getElementById('modalProfilMandiri');
  if (modal) {
    modal.classList.remove('active', 'open');
    modal.style.display = 'none';
  }
};

window.openModalKartuGuruSaya = function() {
  document.querySelectorAll('.acct-dropdown').forEach(d => d.classList.remove('open'));
  const modal = document.getElementById('modalKartuGuruSaya');
  if (modal) {
    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
    modal.classList.add('active', 'open');
    modal.style.display = 'flex';

    // Render QR Code jika belum
    const wrap = document.getElementById('modalQrGuruWrap');
    if (wrap && !wrap.hasChildNodes()) {
      if (typeof QRCode !== 'undefined') {
        new QRCode(wrap, {
          text: "{{ $codeSaya ?? 'GURU' }}",
          width: 140,
          height: 140,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.M
        });
      } else {
        // Fallback library CDN jika belum terload di halaman tertentu
        const script = document.createElement('script');
        script.src = "/qrcode.min.js";
        script.onload = () => {
          new QRCode(wrap, {
            text: "{{ $codeSaya ?? 'GURU' }}",
            width: 140,
            height: 140,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
          });
        };
        document.head.appendChild(script);
      }
    }
  }
};

window.closeModalKartuGuruSaya = function() {
  const modal = document.getElementById('modalKartuGuruSaya');
  if (modal) {
    modal.classList.remove('active', 'open');
    modal.style.display = 'none';
  }
};

function downloadQrGuruModal() {
  const wrap = document.getElementById('modalQrGuruWrap');
  const imgOrCanvas = wrap ? (wrap.querySelector('canvas') || wrap.querySelector('img')) : null;
  if (!imgOrCanvas) {
    alert('QR Code sedang disiapkan, silakan coba 1 detik lagi.');
    return;
  }

  let dataUrl = '';
  if (imgOrCanvas.tagName.toLowerCase() === 'canvas') {
    dataUrl = imgOrCanvas.toDataURL('image/png');
  } else {
    dataUrl = imgOrCanvas.src;
  }

  const link = document.createElement('a');
  link.href = dataUrl;
  link.download = 'QR_Presensi_{{ preg_replace("/[^A-Za-z0-9]/", "_", $gSaya->nama ?? "Guru") }}.png';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function kirimWaGuruSaya(guruId) {
  const btn = document.getElementById('btnKirimWaGuruSaya');
  if (!btn) return;
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim ke WhatsApp...';

  fetch('{{ route("rfid.kirim.wa.personal") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ type: 'guru', id: guruId })
  })
  .then(r => r.json())
  .then(data => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-whatsapp"></i> Kirim Akses Kartu ke WhatsApp Saya';
    if (data.success) {
      alert('✅ ' + data.message);
    } else {
      alert('❌ ' + (data.message || 'Gagal mengirim ke WhatsApp.'));
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-whatsapp"></i> Kirim Akses Kartu ke WhatsApp Saya';
    alert('❌ Terjadi kesalahan jaringan.');
  });
}

window.toggleTheme = function() {
  const current = document.documentElement.getAttribute('data-theme') || 'light';
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('smkn1_theme', next);
  document.querySelectorAll('.theme-toggle-icon').forEach(icon => {
    icon.className = (next === 'dark') ? 'bi bi-moon-stars-fill theme-toggle-icon' : 'bi bi-sun-fill theme-toggle-icon';
  });
};

window.toggleRoleSwitchDropdown = function(e, btn) {
  if (e && e.stopPropagation) e.stopPropagation();
  const wrap = btn.closest('.role-switch-wrap');
  const dropdown = wrap ? wrap.querySelector('.role-switch-dropdown') : null;
  document.querySelectorAll('.acct-dropdown').forEach(d => {
    if (d !== dropdown) d.classList.remove('open');
  });
  if (dropdown) dropdown.classList.toggle('open');
};

window.toggleAcctDropdown = function(e, btn) {
  if (e && e.stopPropagation) e.stopPropagation();
  const wrap = btn.closest('.acct-wrap');
  const dropdown = wrap ? wrap.querySelector('.acct-dropdown') : null;
  document.querySelectorAll('.acct-dropdown').forEach(d => {
    if (d !== dropdown) d.classList.remove('open');
  });
  if (dropdown) dropdown.classList.toggle('open');
};

window.toggleAppLauncherDropdown = function(e, btn) {
  if (e && e.stopPropagation) e.stopPropagation();
  const wrap = btn.closest('.app-launcher-wrap');
  const dropdown = wrap ? wrap.querySelector('.app-launcher-dropdown') : null;
  document.querySelectorAll('.acct-dropdown').forEach(d => {
    if (d !== dropdown) d.classList.remove('open');
  });
  if (dropdown) dropdown.classList.toggle('open');
};

document.addEventListener('click', function(e) {
  if (!e.target.closest('.acct-wrap') && !e.target.closest('.role-switch-wrap') && !e.target.closest('.app-launcher-wrap')) {
    document.querySelectorAll('.acct-dropdown').forEach(d => d.classList.remove('open'));
  }
});

(function() {
  const currentTheme = localStorage.getItem('smkn1_theme') || 'light';
  document.documentElement.setAttribute('data-theme', currentTheme);
  document.querySelectorAll('.theme-toggle-icon').forEach(icon => {
    icon.className = (currentTheme === 'dark') ? 'bi bi-moon-stars-fill theme-toggle-icon' : 'bi bi-sun-fill theme-toggle-icon';
  });
})();
</script>

