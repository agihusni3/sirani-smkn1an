<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Akademik & KBM') — SMKN 1 Air Naningan</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/akademik-app.css') }}?v={{ time() }}">
  @stack('styles')
</head>
<body class="akademik-body">
<div class="akademik-layout">
  @include('dcc.akademik.partials.sidebar')

  <main class="akademik-main">
    {{-- Header Topbar --}}
    <header class="akademik-topbar">
      <div class="akademik-topbar-left">
        <button type="button" class="akademik-menu-toggle" onclick="window.openAkademikSidebar()" aria-label="Buka Menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="akademik-breadcrumb">
          <a href="{{ route('admin.portal') }}">DCC</a>
          <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
          <a href="{{ route('akademik.dashboard') }}">Akademik &amp; KBM</a>
          @hasSection('breadcrumb')
            <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
            <span>@yield('breadcrumb')</span>
          @endif
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:12px;">
        <span class="ak-badge ak-badge-primary">
          <i class="bi bi-mortarboard me-1"></i> Kurikulum Merdeka
        </span>
        @include('partials.header_actions')
      </div>
    </header>

    {{-- Main View Body --}}
    <div class="akademik-content">
      {{-- Notification Alerts --}}
      @if(session('success'))
        <div style="padding:14px 18px; border-radius:10px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; margin-bottom:20px; font-size:13px; font-weight:600; display:flex; align-items:center; justify-content:space-between;">
          <div style="display:flex; align-items:center; gap:8px;">
            <i class="bi bi-check-circle-fill" style="font-size:16px;"></i>
            <span>{{ session('success') }}</span>
          </div>
          <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:#065f46; cursor:pointer;"><i class="bi bi-x"></i></button>
        </div>
      @endif

      @if(session('error'))
        <div style="padding:14px 18px; border-radius:10px; background:#ffe4e6; border:1px solid #fecdd3; color:#9f1239; margin-bottom:20px; font-size:13px; font-weight:600; display:flex; align-items:center; justify-content:space-between;">
          <div style="display:flex; align-items:center; gap:8px;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:16px;"></i>
            <span>{{ session('error') }}</span>
          </div>
          <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:#9f1239; cursor:pointer;"><i class="bi bi-x"></i></button>
        </div>
      @endif

      @if(isset($errors) && $errors->any())
        <div style="padding:14px 18px; border-radius:10px; background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; margin-bottom:20px; font-size:13px;">
          <div style="font-weight:700; margin-bottom:4px;"><i class="bi bi-exclamation-octagon me-1"></i> Terjadi kesalahan pengisian:</div>
          <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
      @yield('akademik-content')
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  window.openAkademikSidebar = function() {
    document.getElementById('akademikSidebar')?.classList.add('show');
    const backdrop = document.getElementById('akademikBackdrop');
    if (backdrop) backdrop.style.display = 'block';
  };
  window.closeAkademikSidebar = function() {
    document.getElementById('akademikSidebar')?.classList.remove('show');
    const backdrop = document.getElementById('akademikBackdrop');
    if (backdrop) backdrop.style.display = 'none';
  };

  // Robust Modal Event Delegation (Works with or without Bootstrap instance)
  document.addEventListener('click', function(e) {
    const toggleBtn = e.target.closest('[data-bs-toggle="modal"]');
    if (toggleBtn) {
      e.preventDefault();
      const targetId = toggleBtn.getAttribute('data-bs-target') || toggleBtn.getAttribute('href');
      if (targetId) {
        const modalEl = document.querySelector(targetId);
        if (modalEl) {
          if (window.bootstrap && window.bootstrap.Modal) {
            const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
            inst.show();
          } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
          }
        }
      }
    }

    const dismissBtn = e.target.closest('[data-bs-dismiss="modal"]');
    if (dismissBtn) {
      e.preventDefault();
      const modalEl = dismissBtn.closest('.modal');
      if (modalEl) {
        if (window.bootstrap && window.bootstrap.Modal) {
          const inst = bootstrap.Modal.getInstance(modalEl);
          if (inst) inst.hide();
          else {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
          }
        } else {
          modalEl.classList.remove('show');
          modalEl.style.display = 'none';
        }
      }
    }
  });
</script>
@stack('scripts')
</body>
</html>
