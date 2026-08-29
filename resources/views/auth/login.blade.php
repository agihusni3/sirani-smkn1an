<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk Dasbor — SIRANI SMKN 1 Air Naningan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('smkn1_theme') || 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
    })();
  </script>
  <style>
    :root,
    [data-theme="light"] {
      --bg: #F8FAFC;
      --bg-2: #FFFFFF;
      --bg-3: #F1F5F9;
      --surface: rgba(0,0,0,0.03);
      --border: rgba(0,0,0,0.08);
      --border-2: rgba(0,0,0,0.14);
      --text: #0F172A;
      --text-2: #475569;
      --text-3: #94A3B8;
      --gold: #CA8A04;
      --gold-2: #EAB308;
      --gold-dim: rgba(202,138,4,0.1);
      --gold-glow: rgba(202,138,4,0.25);
      --green: #16A34A;
      --green-dim: rgba(22,163,74,0.12);
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
      --gold: #FACC15;
      --gold-2: #FDE68A;
      --gold-dim: rgba(250,204,21,0.12);
      --gold-glow: rgba(250,204,21,0.35);
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
    .bg-orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, rgba(250,204,21,0.3) 0%, transparent 70%); top: -150px; left: -150px; }
    .bg-orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(59,130,246,0.25) 0%, transparent 70%); bottom: -100px; right: -100px; }
    .bg-grid { position: fixed; inset: 0; z-index: 0; pointer-events: none; background-image: linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px); background-size: 50px 50px; }

    .login-wrap { position: relative; z-index: 10; width: 100%; max-width: 480px; }
    .login-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: 26px;
      padding: 36px 32px 30px;
      box-shadow: 0 24px 60px rgba(0,0,0,0.2);
      backdrop-filter: blur(20px);
    }

    /* ══ TAB SWITCHER ══ */
    .tab-bar {
      display: flex;
      background: var(--surface);
      border: 1px solid var(--border-2);
      border-radius: 14px;
      padding: 4px;
      margin-bottom: 22px;
      gap: 4px;
    }
    .tab-btn {
      flex: 1;
      border: none;
      background: transparent;
      color: var(--text-2);
      padding: 10px 14px;
      font-size: 13px;
      font-weight: 700;
      font-family: var(--font);
      border-radius: 10px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all .2s ease;
    }
    .tab-btn.active {
      background: #000000;
      color: #FFFFFF;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .tab-btn.active-face {
      background: #000000;
      color: #FFFFFF;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; animation: fadeIn .25s ease forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

    /* ══ CAMERA VIEWPORT LOGIN ══ */
    .face-login-box {
      position: relative;
      width: 100%;
      height: 250px;
      border-radius: 14px;
      overflow: hidden;
      background: #070B14;
      border: 1.5px solid var(--border-2);
      margin-bottom: 14px;
      box-shadow: inset 0 0 24px rgba(0,0,0,0.8);
    }
    #loginVideoFeed {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transform: scaleX(-1) translateZ(0);
      will-change: transform;
    }

    .hud-login-status {
      font-size: 12.5px;
      font-weight: 700;
      padding: 8px 14px;
      border-radius: 10px;
      background: var(--surface);
      border: 1px solid var(--border);
      color: var(--text);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 12px;
      text-align: center;
      transition: all .2s ease;
    }
    .hud-login-status.success {
      background: var(--green-dim);
      color: var(--green);
      border-color: rgba(34, 197, 94, 0.4);
    }
    .hud-login-status.error {
      background: var(--red-dim);
      color: var(--red);
      border-color: rgba(239, 68, 68, 0.4);
    }

    /* ══ ACTION BUTTONS ══ */
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
      border-color: var(--gold);
      box-shadow: 0 0 0 3px var(--gold-dim);
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
    .back-home:hover { color: var(--gold); }

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
    <div style="text-align:center; margin-bottom:24px;">
      <div style="width:56px; height:56px; margin:0 auto 12px; border-radius:14px; padding:6px; background:var(--bg-2); border:1.5px solid var(--border-2); box-shadow:0 4px 14px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center;">
        <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" style="width:100%; height:100%; object-fit:contain;" />
      </div>
      <h1 style="font-size:24px; font-weight:900; letter-spacing:-0.03em; color:var(--text); margin-bottom:3px;">SIRANI</h1>
      <p style="font-size:12px; font-weight:600; color:var(--text-3); line-height:1.4;">Sistem Informasi Responsif Absensi SMKN 1 Air Naningan</p>
    </div>

    <!-- TAB SWITCHER -->
    <div class="tab-bar" role="tablist">
      <button id="tab-manual" type="button" class="tab-btn active" onclick="switchLoginTab('manual')">
        Email &amp; Password
      </button>
      <button id="tab-face" type="button" class="tab-btn" onclick="switchLoginTab('face')">
        Login Face ID
      </button>
    </div>

    <!-- ALERTS -->
    @if(session('success'))
      <div class="alert alert-success"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error"><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>
    @endif
    @if($errors->has('email') || $errors->has('password'))
      <div class="alert alert-error">
        @foreach($errors->get('email') as $e)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $e }}</div>@endforeach
        @foreach($errors->get('password') as $e)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $e }}</div>@endforeach
      </div>
    @endif

    <!-- ══ TAB 1 (DEFAULT): MANUAL EMAIL & PASSWORD ══ -->
    <div id="panel-manual" class="tab-panel active">
      <form action="/login" method="POST">
        @csrf
        <div class="form-group">
          <label>Identitas Login (Nama / NIP / Email)</label>
          <input type="text" name="email" value="{{ old('email') }}" required autofocus placeholder="Contoh: Budi Santoso / 198503152010011008 / admin" />
        </div>
        <div class="form-group">
          <label>Kata Sandi</label>
          <input type="password" name="password" required placeholder="Masukkan kata sandi (default: password)" />
        </div>
        <button type="submit" class="btn-login">
          <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dasbor
        </button>
      </form>

      <div style="margin-top:16px; padding:12px; border-radius:10px; background:var(--surface); border:1px solid var(--border-2); font-size:11.5px; color:var(--text-2); line-height:1.5;">
        <i class="bi bi-info-circle-fill" style="color:var(--gold); margin-right:4px;"></i>
        <strong>Info Login Akun Guru / Staf:</strong><br/>
        Gunakan <strong>Nama Guru</strong> (cth: <em>Budi Santoso</em>) atau <strong>NIP</strong> dengan kata sandi: <code style="color:var(--gold); font-weight:800;">password</code> atau <code style="color:var(--gold); font-weight:800;">123456</code>.
      </div>
    </div>

    <!-- ══ TAB 2: LOGIN FACE ID ══ -->
    <div id="panel-face" class="tab-panel">
      <div class="face-login-box">
        <video id="loginVideoFeed" autoplay playsinline muted></video>
        <div class="reticle-login" id="loginReticle">
          <div class="laser-login"></div>
        </div>
      </div>

      <div id="hudLoginStatus" class="hud-login-status">
        <i class="bi bi-camera-video-fill" style="color:var(--gold);"></i>
        <span id="loginStatusText">Kamera Face ID siap diaktifkan...</span>
      </div>

      <!-- Action Button Group -->
      <div style="display:flex; gap:10px; margin-top:10px;">
        <button type="button" class="btn-login" onclick="triggerManualFaceScan()" id="btnScanNow" style="flex:1; height:44px; font-size:13.5px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
          <i class="bi bi-camera-fill" style="font-size:16px;"></i> Pindai Face ID &amp; Masuk
        </button>
        <button type="button" onclick="switchLoginCamera()" style="width:48px; height:44px; display:inline-flex; align-items:center; justify-content:center; background:var(--surface); border:1.5px solid var(--border-2); border-radius:14px; font-size:18px; color:var(--text); cursor:pointer; transition:all .2s ease;" title="Ganti Kamera Depan / Belakang" onmouseover="this.style.borderColor='var(--gold)';" onmouseout="this.style.borderColor='var(--border-2)';">
          <i class="bi bi-camera-reverse"></i>
        </button>
      </div>

      <div style="margin-top:14px; padding:10px 12px; border-radius:10px; background:var(--surface); border:1px solid var(--border-2); font-size:11.5px; color:var(--text-3); text-align:center; line-height:1.4;">
        <i class="bi bi-shield-check" style="color:var(--green); margin-right:4px;"></i>
        Posisikan wajah tegak di depan kamera. Pastikan pencahayaan ruangan cukup terang.
      </div>
    </div>

      <div style="margin-top:14px; font-size:11.5px; color:var(--text-3); line-height:1.4;">
        <i class="bi bi-shield-check" style="color:var(--gold);"></i> Pastikan wajah Anda sudah pernah didaftarkan pada <strong>Face ID</strong> di menu <strong>Data Guru &amp; Pegawai</strong>.
      </div>
    </div>

  </div>

  <!-- FOOTER CARD -->
  <div class="card-footer">
    <a href="/" class="back-home"><i class="bi bi-arrow-left"></i> Smart Gate Presensi</a>
    <a href="/cek-presensi" style="color:var(--gold); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
      <i class="bi bi-mortarboard-fill"></i> Portal Siswa &amp; Ortu
    </a>
  </div>
</div>

<script src="/face-api.min.js"></script>
<script>
  let registeredTeachers = [];
  let isFaceProcessing = false;
  let activeLoginStream = null;
  let loginFacingMode = 'user';
  let faceLoginSuccess = false;
  let activePythonEngine = false;

  function switchLoginTab(tab) {
    const tabFace = document.getElementById('tab-face');
    const tabManual = document.getElementById('tab-manual');
    const panelFace = document.getElementById('panel-face');
    const panelManual = document.getElementById('panel-manual');

    if (tab === 'face') {
      tabFace.className = 'tab-btn active-face';
      tabManual.className = 'tab-btn';
      panelFace.className = 'tab-panel active';
      panelManual.className = 'tab-panel';
      startLoginCamera();
    } else {
      tabManual.className = 'tab-btn active';
      tabFace.className = 'tab-btn';
      panelManual.className = 'tab-panel active';
      panelFace.className = 'tab-panel';
      stopLoginCamera();
    }
  }

  async function startLoginCamera() {
    const video = document.getElementById('loginVideoFeed');
    stopLoginCamera();

    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: {
          width: { ideal: 640 },
          height: { ideal: 480 },
          facingMode: loginFacingMode
        }
      });
      activeLoginStream = stream;
      video.srcObject = stream;
      video.style.transform = (loginFacingMode === 'user') ? 'scaleX(-1) translateZ(0)' : 'scaleX(1) translateZ(0)';
      updateLoginStatus('Kamera Aktif · Posisikan wajah di dalam kotak pemindai', 'normal');
    } catch (e) {
      console.warn(e);
      updateLoginStatus('Akses kamera tidak diizinkan di browser ini', 'error');
    }
  }

  function stopLoginCamera() {
    if (activeLoginStream) {
      activeLoginStream.getTracks().forEach(t => t.stop());
      activeLoginStream = null;
    }
  }

  function switchLoginCamera() {
    loginFacingMode = (loginFacingMode === 'user') ? 'environment' : 'user';
    startLoginCamera();
  }

  function updateLoginStatus(text, state) {
    const hud = document.getElementById('hudLoginStatus');
    const label = document.getElementById('loginStatusText');
    if (label) label.textContent = text;
    if (hud) hud.className = 'hud-login-status' + (state === 'success' ? ' success' : (state === 'error' ? ' error' : ''));
  }

  function captureLoginFrameBase64(quality = 0.88) {
    const video = document.getElementById('loginVideoFeed');
    if (!video || !video.videoWidth) return null;
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    return canvas.toDataURL('image/jpeg', quality);
  }

  // Muat Descriptors Wajah Guru & Staf
  async function loadLoginDescriptors() {
    try {
      const res = await fetch('/api/v1/face-descriptors');
      const json = await res.json();
      if (json.success) {
        registeredTeachers = (json.data || []).filter(item => item.type === 'guru');
        activePythonEngine = (json.engine === 'insightface_arcface_r100');

        if (registeredTeachers.length === 0) {
          updateLoginStatus('Belum ada wajah guru yang direkam di Data Guru', 'normal');
        } else {
          updateLoginStatus(`AI Siap · ${registeredTeachers.length} Wajah Guru Terdaftar`, 'normal');
        }
      }
    } catch (e) {
      console.error(e);
      updateLoginStatus('Gagal menghubungkan basis data biometrik', 'error');
    }
  }

  // Ekstraksi 128 Vektor Descriptor (Deep Neural Network ResNet / Fallback)
  async function extractDeepLoginDescriptor(videoElem) {
    if (typeof faceapi !== 'undefined') {
      try {
        await Promise.all([
          faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
          faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
          faceapi.nets.faceRecognitionNet.loadFromUri('/models')
        ]);
        const detection = await faceapi
          .detectSingleFace(videoElem, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.30 }))
          .withFaceLandmarks()
          .withFaceDescriptor();

        if (detection && detection.descriptor) {
          return Array.from(detection.descriptor);
        }
      } catch (err) {
        console.warn("Face-API login detection fallback:", err);
      }
    }
    return null;
  }

  // Fungsi Universal Jarak Vektor Biometrik (Mendukung 128-D & 512-D secara adaptif)
  function calculateUniversalDistance(vecA, vecB) {
    if (!vecA || !vecB || !vecA.length || !vecB.length) return 999;
    
    // Jika dimensi sama persis
    if (vecA.length === vecB.length) {
      let sum = 0;
      for (let i = 0; i < vecA.length; i++) {
        const diff = vecA[i] - vecB[i];
        sum += diff * diff;
      }
      return Math.sqrt(sum);
    }
    
    // Jika dimensi berbeda (128-D vs 512-D), hitung Cosine Similarity pada prefix yang bersesuaian
    const minLen = Math.min(vecA.length, vecB.length);
    let dot = 0, normA = 0, normB = 0;
    for (let i = 0; i < minLen; i++) {
      dot += vecA[i] * vecB[i];
      normA += vecA[i] * vecA[i];
      normB += vecB[i] * vecB[i];
    }
    const denom = Math.sqrt(normA) * Math.sqrt(normB);
    const cosSim = denom > 0 ? (dot / denom) : 0;
    return Math.sqrt(Math.max(0, 2 * (1 - cosSim)));
  }

  function startLoginLoop() {
    const video = document.getElementById('loginVideoFeed');
    const reticle = document.getElementById('loginReticle');

    setInterval(async () => {
      if (faceLoginSuccess || isFaceProcessing || !video.videoWidth || registeredTeachers.length === 0) {
        return;
      }

      isFaceProcessing = true;
      try {
        // ═══ MODE A: PYTHON INSIGHTFACE ARCFACE R100 (512-D) ═══
        if (activePythonEngine) {
          const imageBase64 = captureLoginFrameBase64(0.88);
          if (!imageBase64) return;

          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          const res = await fetch('/login/face', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              image_base64: imageBase64,
              candidates: registeredTeachers.map(c => ({
                id: c.id,
                type: 'guru',
                nama: c.nama,
                embedding: c.embedding
              }))
            })
          });

          const data = await res.json();

          if (data.success) {
            faceLoginSuccess = true;
            reticle.classList.add('active-match');
            handleLoginSuccess(data);
          } else {
            reticle.classList.remove('active-match');
            if (data.reason === 'borderline') {
              updateLoginStatus(data.message, 'normal');
            } else if (data.status === 'no_user_account') {
              updateLoginStatus(data.message, 'error');
            }
          }
          return;
        }

        // ═══ MODE B: UNIVERSAL BROWSER DESCRIPTOR MATCHING ═══
        const currentDescriptor = await extractDeepLoginDescriptor(video);

        if (!currentDescriptor) {
          reticle.classList.remove('active-match');
          return;
        }

        let bestMatch = null;
        let lowestDist = 999;
        const DISTANCE_THRESHOLD = 0.58;

        for (const candidate of registeredTeachers) {
          if (!candidate.embedding) continue;
          const dist = calculateUniversalDistance(currentDescriptor, candidate.embedding);

          if (dist < lowestDist) {
            lowestDist = dist;
            bestMatch = candidate;
          }
        }

        const pct = Math.max(0, Math.min(100, Math.round((1 - (lowestDist / 0.65)) * 100)));

        if (bestMatch && lowestDist <= DISTANCE_THRESHOLD) {
          reticle.classList.add('active-match');
          updateLoginStatus(`Wajah Cocok: ${bestMatch.nama} (${pct}%). Mengotentikasi...`, 'success');
          faceLoginSuccess = true;
          await executeFaceLoginFallback(bestMatch.id, bestMatch.nama);
        } else if (bestMatch && lowestDist <= 0.68) {
          reticle.classList.remove('active-match');
          updateLoginStatus(`Mendeteksi: ${bestMatch.nama} (${pct}% Cocok · Dekatkan wajah)`, 'normal');
        } else {
          reticle.classList.remove('active-match');
        }
      } catch (err) {
        console.error(err);
      } finally {
        isFaceProcessing = false;
      }
    }, 450);
  }

  // Tombol Scan Manual
  async function triggerManualFaceScan() {
    const video = document.getElementById('loginVideoFeed');
    if (!video || !video.videoWidth) {
      alert("Kamera belum siap.");
      return;
    }

    if (registeredTeachers.length === 0) {
      alert("Belum ada data wajah guru yang terdaftar di sistem. Silakan rekam wajah terlebih dahulu di menu Data Guru atau gunakan login Email & Password.");
      return;
    }

    updateLoginStatus("Memindai biometrik wajah...", "normal");

    if (activePythonEngine) {
      const imageBase64 = captureLoginFrameBase64(0.92);
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      const res = await fetch('/login/face', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          image_base64: imageBase64,
          candidates: registeredTeachers.map(c => ({
            id: c.id,
            type: 'guru',
            nama: c.nama,
            embedding: c.embedding
          }))
        })
      });

      const data = await res.json();
      if (data.success) {
        faceLoginSuccess = true;
        document.getElementById('loginReticle')?.classList.add('active-match');
        handleLoginSuccess(data);
      } else {
        updateLoginStatus(data.message || 'Wajah tidak cocok. Coba posisikan wajah lebih tegak.', 'error');
      }
      return;
    }

    // Fallback manual scan
    const currentDescriptor = await extractDeepLoginDescriptor(video);
    if (!currentDescriptor) {
      updateLoginStatus("Wajah tidak terdeteksi di kamera.", "error");
      return;
    }

    let bestMatch = null;
    let lowestDist = 999;
    for (const candidate of registeredTeachers) {
      if (!candidate.embedding) continue;
      const dist = calculateUniversalDistance(currentDescriptor, candidate.embedding);
      if (dist < lowestDist) {
        lowestDist = dist;
        bestMatch = candidate;
      }
    }

    if (bestMatch && lowestDist <= 0.58) {
      faceLoginSuccess = true;
      document.getElementById('loginReticle')?.classList.add('active-match');
      await executeFaceLoginFallback(bestMatch.id, bestMatch.nama);
    } else {
      updateLoginStatus("Wajah tidak cocok. Pastikan wajah sudah terdaftar di Data Guru.", "error");
    }
  }

  function handleLoginSuccess(data) {
    const nama = data.user?.name || 'Pengguna';
    updateLoginStatus(`Wajah Dikenali: ${nama}. Selamat datang!`, 'success');

    let redirected = false;
    const doRedirect = () => {
      if (!redirected) {
        redirected = true;
        window.location.href = data.redirect || '/dashboard';
      }
    };

    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
      const utter = new SpeechSynthesisUtterance(`Selamat datang, ${nama}.`);
      utter.lang = 'id-ID';
      utter.rate = 1.0;
      utter.onend = () => setTimeout(doRedirect, 250);
      utter.onerror = () => setTimeout(doRedirect, 800);
      window.speechSynthesis.speak(utter);
      setTimeout(doRedirect, 3000);
    } else {
      setTimeout(doRedirect, 1000);
    }
  }

  async function executeFaceLoginFallback(guruId, nama) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
      const res = await fetch('/login/face', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ guru_id: guruId, _token: csrf })
      });
      const data = await res.json();

      if (data.success) {
        handleLoginSuccess(data);
      } else {
        updateLoginStatus(data.message || 'Gagal verifikasi Face ID', 'error');
        faceLoginSuccess = false;
      }
    } catch (e) {
      console.error(e);
      updateLoginStatus('Terjadi kendala saat verifikasi Face ID', 'error');
      faceLoginSuccess = false;
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadLoginDescriptors();
    startLoginLoop();
  });
</script>
</body>
</html>
