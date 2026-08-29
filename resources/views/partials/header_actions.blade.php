{{-- Header Actions: Clean Minimalist Theme Switch + Account Dropdown --}}
<div class="header-actions" style="display:flex; align-items:center; gap:10px;">

  {{-- Minimalist Theme Switcher --}}
  <button type="button" class="btn" id="themeToggleQuick" style="padding:6px 12px; font-size:12px; font-weight:700; background:var(--surface); border:1px solid var(--border); color:var(--text-2); border-radius:var(--r-sm); cursor:pointer;" title="Ganti Tema">
    <span id="themeToggleText">Tema</span>
  </button>

  {{-- Account Dropdown --}}
  <div class="acct-wrap" id="acctWrap" style="position:relative;">
    <button class="acct-btn" id="acctBtn" type="button" aria-label="Akun" title="{{ auth()->user()->name ?? 'Admin' }}" style="display:inline-flex; align-items:center; gap:8px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:5px 12px; font-size:12.5px; font-weight:700; color:var(--text); cursor:pointer;">
      <span style="width:22px; height:22px; border-radius:50%; background:var(--gold-dim); color:var(--gold); display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:800;">
        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
      </span>
      <span style="max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->name ?? 'Pengguna' }}</span>
    </button>

    <div class="acct-dropdown" id="acctDropdown">
      <div class="acct-dropdown-header" style="padding:14px 16px; border-bottom:1px solid var(--border);">
        <div>
          <div class="acct-dropdown-name" style="font-size:13.5px; font-weight:800; color:var(--text);">{{ auth()->user()->name ?? 'Administrator' }}</div>
          <div class="acct-dropdown-email" style="font-size:11px; color:var(--text-3); margin-top:2px;">{{ auth()->user()->email ?? '' }}</div>
          @php
            $u = auth()->user();
            $roleLabel = $u ? $u->role_display_name : 'Pengguna';
          @endphp
          <div style="margin-top:6px;">
            <span class="acct-dropdown-role" style="font-size:10px; font-weight:700; font-family:var(--font-mono); background:var(--gold-dim); color:var(--gold); padding:2px 8px; border-radius:6px;">{{ $roleLabel }}</span>
          </div>
        </div>
      </div>
      <div style="padding:4px;">
        <form action="{{ route('logout') }}" method="POST" style="margin:0">
          @csrf
          <button type="submit" class="acct-dropdown-item acct-logout" style="width:100%; text-align:left; padding:9px 12px; font-size:12.5px; font-weight:700; color:#EF4444; background:none; border:none; border-radius:6px; cursor:pointer;">
            Keluar (Logout)
          </button>
        </form>
      </div>
    </div>
  </div>

</div>

<script>
(function() {
  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('smkn1_theme', theme);
    const label = document.getElementById('themeToggleText');
    if (label) label.textContent = (theme === 'dark') ? 'Gelap' : 'Terang';
  }

  const currentTheme = localStorage.getItem('smkn1_theme') || 'light';
  applyTheme(currentTheme);

  document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('themeToggleQuick');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        const next = (document.documentElement.getAttribute('data-theme') === 'dark') ? 'light' : 'dark';
        applyTheme(next);
      });
    }

    const btn  = document.getElementById('acctBtn');
    const drop = document.getElementById('acctDropdown');
    const wrap = document.getElementById('acctWrap');

    if (btn && drop) {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        drop.classList.toggle('open');
      });

      document.addEventListener('click', (e) => {
        if (wrap && !wrap.contains(e.target)) {
          drop.classList.remove('open');
        }
      });
    }
  });
})();
</script>
