<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Masuk — SIRANI (SMKN 1 Air Naningan)</title>
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
  <style>
    :root, [data-theme="light"] {
      --bg: #F1F5F9;
      --bg-2: #FFFFFF;
      --bg-3: #E2E8F0;
      --surface: rgba(0,0,0,0.03);
      --border: rgba(0,0,0,0.08);
      --border-2: rgba(0,0,0,0.14);
      --text: #0F172A;
      --text-2: #475569;
      --text-3: #94A3B8;
      --gold: #000000;
      --gold-dim: rgba(0,0,0,0.06);
      --navy: #000000;
      --green: #16A34A;
      --green-dim: rgba(22,163,74,0.1);
      --red: #DC2626;
      --red-dim: rgba(220,38,38,0.1);
      --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }
    [data-theme="dark"] {
      --bg: #070B14;
      --bg-2: #0D1422;
      --bg-3: #151D2E;
      --surface: rgba(255,255,255,0.04);
      --border: rgba(255,255,255,0.08);
      --border-2: rgba(255,255,255,0.14);
      --text: #F1F5F9;
      --text-2: #94A3B8;
      --text-3: #64748B;
      --gold: #FFFFFF;
      --gold-dim: rgba(255,255,255,0.08);
      --navy: #FFFFFF;
      --green: #22C55E;
      --green-dim: rgba(34,197,94,0.15);
      --red: #EF4444;
      --red-dim: rgba(239,68,68,0.15);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: var(--font);
      min-height: 100vh;
      min-height: 100dvh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px 14px;
      padding-top: calc(20px + env(safe-area-inset-top, 0px));
      padding-bottom: calc(20px + env(safe-area-inset-bottom, 0px));
      overflow-x: hidden;
      transition: background .25s ease, color .25s ease;
    }

    .bg-orb { position: fixed; pointer-events: none; z-index: 0; border-radius: 50%; filter: blur(120px); opacity: .35; }
    .bg-orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(0,0,0,0.15) 0%, transparent 70%); top: -150px; left: -150px; }
    .bg-orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(0,0,0,0.1) 0%, transparent 70%); bottom: -100px; right: -100px; }
    .bg-grid { position: fixed; inset: 0; z-index: 0; pointer-events: none; background-image: linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 50px 50px; }

    .login-wrap { position: relative; z-index: 10; width: 100%; max-width: 440px; }
    .login-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: 24px;
      padding: 36px 32px 30px;
      box-shadow: 0 24px 60px rgba(0,0,0,0.18);
      backdrop-filter: blur(20px);
    }

    .btn-login {
      width: 100%;
      height: 46px;
      border-radius: 12px;
      border: 1px solid #000000;
      background: #000000;
      color: #FFFFFF;
      font-family: var(--font);
      font-size: 14px;
      font-weight: 800;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
      transition: all .2s ease;
    }
    .btn-login:hover {
      background: #262626;
      border-color: #262626;
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
    }

    .form-group { margin-bottom: 16px; text-align: left; }
    .form-group label { display: block; font-size: 12.5px; font-weight: 700; color: var(--text-2); margin-bottom: 6px; }
    .form-group input {
      width: 100%;
      height: 44px;
      border-radius: 12px;
      background: var(--surface);
      border: 1.5px solid var(--border-2);
      padding: 0 14px;
      font-family: var(--font);
      font-size: 14px;
      color: var(--text);
      outline: none;
      transition: border-color .2s ease, box-shadow .2s ease;
    }
    .form-group input:focus {
      border-color: #000000;
      box-shadow: 0 0 0 3px rgba(0,0,0,0.12);
    }

    .card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid var(--border-2);
      font-size: 12.5px;
    }
    .back-home {
      color: var(--text-3);
      text-decoration: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      transition: color .2s ease;
    }
    .back-home:hover { color: #000000; }

    .alert {
      padding: 12px 16px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
    }
    .alert-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(34, 197, 94, 0.3); }
    .alert-error { background: var(--red-dim); color: var(--red); border: 1px solid rgba(239, 68, 68, 0.3); }
  </style>
</head>
<body>

<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="bg-grid"></div>

<div class="login-wrap">
  <div class="login-card">

    <!-- BRAND -->
    <div style="text-align:center; margin-bottom:28px;">
      <div style="width:56px; height:56px; margin:0 auto 12px; border-radius:14px; padding:6px; background:var(--bg-2); border:1.5px solid var(--border-2); box-shadow:0 4px 14px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center;">
        <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" style="width:100%; height:100%; object-fit:contain;" />
      </div>
      <h1 style="font-size:24px; font-weight:900; letter-spacing:-0.03em; color:var(--text); margin-bottom:3px;">SIRANI</h1>
      <p style="font-size:12px; font-weight:600; color:var(--text-3); line-height:1.4;">Sistem Informasi Responsif Absensi SMKN 1 Air Naningan</p>
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
        <label>Identitas Login (Username / NIP / Email)</label>
        <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan Username, NIP, atau Email" />
      </div>
      <div class="form-group">
        <label>Kata Sandi</label>
        <input type="password" name="password" required placeholder="Masukkan kata sandi" />
      </div>
      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dasbor
      </button>
    </form>

    <div class="card-footer">
      <a href="/portal-siswa" class="back-home">
        <i class="bi bi-person-badge"></i> Portal Siswa
      </a>
      <a href="/cek-presensi" class="back-home">
        <i class="bi bi-people"></i> Portal Ortu
      </a>
    </div>

  </div>
</div>

</body>
</html>
