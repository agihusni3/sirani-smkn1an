<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Masuk Asesmen Siswa — SMKN 1 Air Naningan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: linear-gradient(135deg, #0b132b 0%, #1c2541 50%, #1e3a8a 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 24px 16px;
      color: #0f172a;
    }
    .cbt-login-card {
      width: 100%;
      max-width: 440px;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .cbt-login-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
      color: #ffffff;
      padding: 32px 28px 24px;
      text-align: center;
      position: relative;
    }
    .cbt-logo-badge {
      width: 68px;
      height: 68px;
      background: #ffffff;
      border-radius: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      margin-bottom: 14px;
      padding: 8px;
    }
    .cbt-logo-badge img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    .cbt-title {
      font-size: 20px;
      font-weight: 900;
      letter-spacing: -0.5px;
      margin-bottom: 4px;
    }
    .cbt-subtitle {
      font-size: 13px;
      color: #bfdbfe;
      font-weight: 500;
    }
    .cbt-login-body {
      padding: 32px 28px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 800;
      color: #334155;
      margin-bottom: 8px;
    }
    .form-input-wrap {
      position: relative;
    }
    .form-input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 16px;
    }
    .form-input {
      width: 100%;
      padding: 12px 14px 12px 42px;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      font-size: 14.5px;
      font-family: inherit;
      color: #0f172a;
      transition: all 0.2s ease;
      background: #f8fafc;
    }
    .form-input:focus {
      outline: none;
      border-color: #2563eb;
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }
    .form-hint {
      font-size: 11.5px;
      color: #64748b;
      margin-top: 5px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .btn-submit {
      width: 100%;
      padding: 13px 20px;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 800;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s ease;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
      margin-top: 10px;
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
    }
    .btn-submit:active {
      transform: translateY(0);
    }
    .alert-box {
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 20px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }
    .alert-info {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      color: #1e40af;
    }
    .cbt-login-footer {
      padding: 16px 28px 24px;
      text-align: center;
      background: #f8fafc;
      border-top: 1px solid #f1f5f9;
      font-size: 12.5px;
      color: #64748b;
    }
    .cbt-login-footer a {
      color: #2563eb;
      text-decoration: none;
      font-weight: 700;
    }
    .cbt-login-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="cbt-login-card">
    
    <div class="cbt-login-header">
      <div class="cbt-logo-badge">
        <img src="{{ asset('logo.png') }}" alt="Logo SMKN 1 Air Naningan" onerror="this.src='{{ asset('img/logo.png') }}'">
      </div>
      <h1 class="cbt-title">Portal Asesmen Siswa</h1>
      <p class="cbt-subtitle">CBT &amp; Ujian Daring SMKN 1 Air Naningan</p>
    </div>

    <div class="cbt-login-body">
      
      {{-- Notifikasi Error / Info --}}
      @if(session('error'))
        <div class="alert-box alert-danger">
          <i class="bi bi-exclamation-triangle-fill" style="font-size:16px;"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @if(session('info'))
        <div class="alert-box alert-info">
          <i class="bi bi-info-circle-fill" style="font-size:16px;"></i>
          <div>{{ session('info') }}</div>
        </div>
      @endif

      <form action="{{ route('portal.asesmen.masuk') }}" method="POST">
        @csrf

        {{-- NISN --}}
        <div class="form-group">
          <label class="form-label" for="inputNisn">Nomor Induk Siswa Nasional (NISN)</label>
          <div class="form-input-wrap">
            <i class="bi bi-person-vcard"></i>
            <input type="text" name="nisn" id="inputNisn" class="form-input" 
                   placeholder="Contoh: 20261001 atau 0081234567" 
                   value="{{ old('nisn') }}" required autofocus autocomplete="off">
          </div>
          <div class="form-hint">
            <i class="bi bi-shield-check text-primary"></i> Masukkan 10 digit NISN atau NIS terdaftar Anda.
          </div>
        </div>

        {{-- Tanggal Lahir --}}
        <div class="form-group">
          <label class="form-label" for="inputTanggalLahir">Tanggal Lahir Siswa</label>
          <div class="form-input-wrap">
            <i class="bi bi-calendar-event"></i>
            <input type="text" name="tanggal_lahir" id="inputTanggalLahir" class="form-input" 
                   placeholder="Contoh: 22101991" 
                   value="{{ old('tanggal_lahir') }}" 
                   inputmode="numeric" 
                   maxlength="10" 
                   required autocomplete="off">
          </div>
          <div class="form-hint">
            <i class="bi bi-info-circle-fill text-primary"></i> Ketik 8 digit format <strong>ddmmyyyy</strong> (Contoh: <strong>22101991</strong> untuk 22 Oktober 1991).
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <span>Masuk Ruang Asesmen</span>
          <i class="bi bi-arrow-right"></i>
        </button>

      </form>

    </div>

    <div class="cbt-login-footer">
      <div>Kembali ke <a href="{{ route('web.beranda') }}"><i class="bi bi-house-door me-1"></i>Beranda Website Sekolah</a></div>
    </div>

  </div>

</body>
</html>
