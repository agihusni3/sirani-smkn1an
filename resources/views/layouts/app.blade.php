<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SITUAN — Tata Usaha SMKN 1 Air Naningan')</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/situan-app.css') }}?v={{ file_exists(public_path('css/situan-app.css')) ? filemtime(public_path('css/situan-app.css')) : time() }}">

  <style>
    body.situan-body {
      background-color: var(--surface, #f8fafc);
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }
    .situan-layout {
      display: flex;
      min-height: 100vh;
    }
    .situan-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
      background: var(--surface, #f8fafc);
    }
    .situan-content {
      padding: 12px 16px;
      max-width: 100%;
    }
    @media (min-width: 768px) {
      .situan-content {
        padding: 20px 28px;
      }
    }
  </style>
  @stack('styles')
</head>
<body class="situan-body">

<div class="situan-layout">
  @include('partials.sidebar_situan')
  
  <main class="situan-main">
    <div class="situan-content">
      {{-- Flash Message Alerts --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
          <i class="bi bi-check-circle-fill text-success fs-5"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
          <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
          <div>{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
          <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Terdapat beberapa kesalahan input:</div>
          <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @yield('content')
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  window.toggleSituanSidebar = function() {
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
@stack('scripts')
</body>
</html>
