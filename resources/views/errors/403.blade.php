<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Akses Dibatasi (403) · DCC SMKN 1 AN</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    :root {
      --bg: #090d16;
      --card: #0f172a;
      --border: rgba(255, 255, 255, 0.1);
      --text: #f8fafc;
      --text-sub: #94a3b8;
      --font: 'Plus Jakarta Sans', sans-serif;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background-color: var(--bg);
      background-image: 
        radial-gradient(at 50% 0%, rgba(239, 68, 68, 0.12) 0px, transparent 60%),
        radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.08) 0px, transparent 60%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: var(--font);
      color: var(--text);
      padding: 24px;
    }
    .error-card {
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--border);
      border-radius: 24px;
      max-width: 520px;
      width: 100%;
      padding: 40px 32px;
      text-align: center;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }
    .error-icon-box {
      width: 80px;
      height: 80px;
      margin: 0 auto 24px;
      border-radius: 22px;
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(185, 28, 28, 0.1));
      border: 1px solid rgba(239, 68, 68, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 38px;
      color: #f87171;
      box-shadow: 0 12px 28px -4px rgba(239, 68, 68, 0.3);
    }
    .error-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 800;
      color: #f87171;
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.25);
      padding: 4px 12px;
      border-radius: 20px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      margin-bottom: 16px;
    }
    .error-title {
      font-size: 24px;
      font-weight: 900;
      letter-spacing: -0.02em;
      margin-bottom: 12px;
      color: #ffffff;
    }
    .error-message {
      font-size: 14px;
      line-height: 1.6;
      color: var(--text-sub);
      margin-bottom: 28px;
    }
    .btn-return-portal {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      height: 48px;
      border-radius: 14px;
      background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
      color: #ffffff;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
      box-shadow: 0 10px 24px -4px rgba(2, 132, 199, 0.4);
      transition: all 0.2s ease;
    }
    .btn-return-portal:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 28px -4px rgba(2, 132, 199, 0.5);
      filter: brightness(1.08);
    }
    .footer-note {
      margin-top: 24px;
      font-size: 11px;
      color: #64748b;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="error-card">
    <div class="error-icon-box">
      <i class="bi bi-shield-lock-fill"></i>
    </div>
    <div class="error-tag">
      <i class="bi bi-exclamation-triangle-fill"></i> Kode 403 · Hak Akses Terbatas
    </div>
    <h1 class="error-title">Otorisasi Tidak Mencukupi</h1>
    <p class="error-message">
      {{ $exception->getMessage() ?: 'Akun Anda saat ini tidak memiliki wewenang atau hak akses peran yang sah untuk mengoperasikan modul tersebut.' }}
      <br><br>
      Silakan gunakan modul lain yang telah diizinkan atau hubungi <strong>Super Administrator</strong> jika Anda membutuhkan penugasan peran tambahan.
    </p>
    <a href="{{ route('admin.portal') }}" class="btn-return-portal">
      <i class="bi bi-command"></i> Kembali ke DCC SMKN 1 AN
    </a>
    <div class="footer-note">
      Digital Command Center · SMKN 1 Air Naningan
    </div>
  </div>
</body>
</html>
