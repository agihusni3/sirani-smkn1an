<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Login DCC — Digital Command Center (SMKN 1 Air Naningan)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function() {
      const saved = localStorage.getItem('smkn1_theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  <link rel="stylesheet" href="{{ asset('css/auth-login.css') }}?v={{ filemtime(public_path('css/auth-login.css')) }}">
</head>
<body>

<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="bg-grid"></div>

<div class="login-wrap">
  <div class="login-card">

    <!-- BRAND -->
    <div style="text-align:center; margin-bottom:28px;">
      <div style="width:58px; height:58px; margin:0 auto 12px; border-radius:16px; padding:6px; background:var(--bg-2); border:1.5px solid var(--border-2); box-shadow:0 4px 14px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center;">
        <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" style="width:100%; height:100%; object-fit:contain;" />
      </div>
      <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(56, 189, 248, 0.1); border:1px solid rgba(56, 189, 248, 0.25); color:#38bdf8; font-size:10.5px; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; padding:3px 10px; border-radius:999px; margin-bottom:10px;">
        <i class="bi bi-command"></i> DIGITAL COMMAND CENTER
      </div>
      <h1 style="font-size:24px; font-weight:900; letter-spacing:-0.03em; color:var(--text); margin-bottom:4px;">DCC SMKN 1 AN</h1>
      <p style="font-size:12px; font-weight:600; color:var(--text-3); line-height:1.4;">Pusat Komando &amp; Akses Layanan Terpadu Ekosistem Digital SMKN 1 Air Naningan</p>
    </div>

    <!-- ALERTS -->
    @if(session('success'))
      <div class="alert alert-success"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error"><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>
    @endif
    @if(isset($errors) && ($errors->has('email') || $errors->has('password') || $errors->has('username')))
      <div class="alert alert-error">
        @foreach($errors->get('email') as $e)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $e }}</div>@endforeach
        @foreach($errors->get('username') as $e)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $e }}</div>@endforeach
        @foreach($errors->get('password') as $e)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $e }}</div>@endforeach
      </div>
    @endif

    <!-- FORM LOGIN -->
    <form action="/login" method="POST">
      @csrf
      <div class="form-group">
        <label>Identitas Akun DCC (Username / NIP / Email)</label>
        <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan Username, NIP, atau Email" />
      </div>
      <div class="form-group">
        <label>Kata Sandi</label>
        <input type="password" name="password" required placeholder="Masukkan kata sandi akun DCC" />
      </div>
      <button type="submit" class="btn-login">
        <i class="bi bi-command"></i> Masuk ke DCC
      </button>
    </form>

    <div class="card-footer">
      <a href="/" class="back-home" title="Kembali ke Beranda Website Sekolah">
        <i class="bi bi-arrow-left"></i> Web Sekolah
      </a>
      <a href="/portal-siswa" class="back-home" title="Portal Siswa Mandiri">
        <i class="bi bi-person-badge"></i> Portal Siswa
      </a>
      <a href="/cek-presensi" class="back-home" title="Portal Presensi Siswa &amp; Orang Tua">
        <i class="bi bi-people"></i> Portal Ortu
      </a>
    </div>

  </div>
</div>

</body>
</html>
