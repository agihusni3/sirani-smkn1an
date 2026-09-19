<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal Ujian CBT Siswa — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body style="background:linear-gradient(135deg, #0a4d80 0%, #0072bc 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; font-family:'Plus Jakarta Sans', sans-serif;">

  <div style="background:#ffffff; border-radius:16px; width:100%; max-width:420px; box-shadow:0 20px 40px rgba(0,0,0,0.25); overflow:hidden;">
    
    <div style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:28px 24px 20px; text-align:center;">
      <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Air Naningan" style="width:64px; height:64px; object-fit:contain; margin-bottom:12px;" onerror="this.src='{{ asset('apple-touch-icon.png') }}'" />
      <h2 style="margin:0 0 4px; font-size:18px; font-weight:900; color:#0f172a; letter-spacing:-0.2px;">
        PORTAL UJIAN ONLINE (CBT)
      </h2>
      <p style="margin:0; font-size:12.5px; color:#64748b; font-weight:600;">
        SMK NEGERI 1 AIR NANINGAN
      </p>
    </div>

    <div style="padding:28px 24px;">

      @if(session('error'))
        <div style="background:#fee2e2; border:1px solid #fecaca; color:#991b1b; padding:12px 14px; border-radius:8px; margin-bottom:18px; font-size:13px; font-weight:600; display:flex; gap:8px; align-items:center;">
          <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if(session('success'))
        <div style="background:#dcfce7; border:1px solid #bbf7d0; color:#166534; padding:12px 14px; border-radius:8px; margin-bottom:18px; font-size:13px; font-weight:600; display:flex; gap:8px; align-items:center;">
          <i class="bi bi-check-circle-fill flex-shrink-0"></i>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      <form action="{{ route('cbt.siswa.login.submit') }}" method="POST">
        @csrf

        <div style="margin-bottom:18px;">
          <label style="display:block; font-size:12.5px; font-weight:800; color:#334155; margin-bottom:6px;">
            Nomor Induk Siswa Nasional (NISN) *
          </label>
          <div style="position:relative;">
            <input type="text" name="nisn" value="{{ old('nisn') }}" required autofocus placeholder="Masukkan 10 digit NISN Anda" class="form-control" style="font-size:14px; padding:12px 14px; border-radius:10px; font-weight:700; letter-spacing:1px;" />
          </div>
          <span style="display:block; font-size:11px; color:#94a3b8; margin-top:4px;">
            NISN tercantum di Kartu Pelajar atau tanyakan ke Pengawas.
          </span>
        </div>

        <div style="margin-bottom:24px;">
          <label style="display:block; font-size:12.5px; font-weight:800; color:#334155; margin-bottom:6px;">
            Password CBT (Opsional)
          </label>
          <input type="password" name="password" placeholder="Kosongkan jika tidak ada password khusus" class="form-control" style="font-size:14px; padding:12px 14px; border-radius:10px;" />
        </div>

        <button type="submit" class="btn btn-primary w-100" style="background:#0072bc; border-color:#0072bc; font-size:14.5px; font-weight:800; padding:12px; border-radius:10px;">
          <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Portal Ujian
        </button>

      </form>

      <div style="margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0; text-align:center;">
        <span style="font-size:11.5px; color:#94a3b8;">
          Sistem Ujian Terintegrasi SIRANI SMKN 1 Air Naningan &copy; {{ date('Y') }}
        </span>
      </div>

    </div>

  </div>

</body>
</html>
