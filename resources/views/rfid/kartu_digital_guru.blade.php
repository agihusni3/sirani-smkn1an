<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Kartu Presensi Digital - {{ $guru->nama }}</title>
  
  <meta name="theme-color" content="#090d16">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" href="/img/logo.png">

  <script>
    (function() {
      const savedTheme = localStorage.getItem('sirani_theme') || 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
    })();
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <script src="/jsbarcode.min.js"></script>
  <script src="/qrcode.min.js"></script>
  <script src="/html2canvas.min.js"></script>

  <style>
    :root, [data-theme="dark"] {
      --font-main: 'Plus Jakarta Sans', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
      --bg-dark: #090d16;
      --card: #131b2e;
      --border: rgba(255, 255, 255, 0.1);
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --blue: #38bdf8;
      --theme-btn-bg: rgba(255, 255, 255, 0.08);
      --theme-btn-border: rgba(255, 255, 255, 0.15);
      --theme-btn-color: #f8fafc;
    }

    [data-theme="light"] {
      --font-main: 'Plus Jakarta Sans', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
      --bg-dark: #f8fafc;
      --card: #ffffff;
      --border: #e2e8f0;
      --text: #0f172a;
      --text-muted: #64748b;
      --blue: #0284c7;
      --theme-btn-bg: #ffffff;
      --theme-btn-border: #cbd5e1;
      --theme-btn-color: #0f172a;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: var(--font-main);
      background: var(--bg-dark);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 16px;
      transition: background-color .25s ease, color .25s ease;
      position: relative;
    }

    .top-bar {
      width: 100%;
      max-width: 390px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
      padding: 0 4px;
    }

    .top-title {
      font-size: 14px;
      font-weight: 900;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .theme-toggle-btn {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      background: var(--theme-btn-bg);
      border: 1px solid var(--theme-btn-border);
      color: var(--theme-btn-color);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 15px;
      transition: all .2s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .theme-toggle-btn:active {
      transform: scale(0.92);
    }

    .card-container {
      width: 100%;
      max-width: 390px;
    }

    .barcode-display-box {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 20px 16px;
      margin-bottom: 14px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      transition: background-color .25s ease, border-color .25s ease;
    }

    .scan-hint {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 600;
      text-align: center;
      line-height: 1.35;
    }

    .btn-mobile {
      width: 100%;
      height: 46px;
      border-radius: 12px;
      font-size: 13.5px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: transform .1s ease;
    }

    .btn-mobile:active {
      transform: scale(0.97);
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <div class="top-title">
      <i class="bi bi-person-badge" style="color:var(--blue);"></i>
      <span>Kartu Presensi Guru</span>
    </div>
    <button type="button" id="btnThemeToggle" onclick="toggleThemeMode()" class="theme-toggle-btn" title="Ganti Mode Gelap / Terang">
      <i id="themeIcon" class="bi bi-sun-fill" style="color:#f59e0b;"></i>
    </button>
  </div>

  <div class="card-container">
    
    {{-- AREA KARTU DIGITAL UTUH (DITAMPILKAN & DIUNDUH SECARA IDENTIK) --}}
    <div id="kartuDigitalArea" style="padding:14px; border-radius:24px; background:var(--bg-dark); transition:background-color .25s ease;">
      
      {{-- Profil Pass Card (Soft Sapphire Blue) --}}
      <div style="background:linear-gradient(135deg, #0f2744 0%, #1d4ed8 60%, #0284c7 100%); border-radius:18px; padding:16px 18px; color:#ffffff; margin-bottom:14px; box-shadow:0 10px 30px rgba(0,0,0,0.25); text-align:left; position:relative; overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.2); padding-bottom:8px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <img src="{{ !empty($sekolah->logo_sekolah) ? asset('storage/'.$sekolah->logo_sekolah) : '/img/logo.png' }}" alt="Logo" style="width:24px; height:24px; object-fit:contain;" onerror="this.src='/img/logo.png';" />
            <span style="font-size:11px; font-weight:900; letter-spacing:0.03em; text-transform:uppercase;">{{ $sekolah->nama_sekolah ?? 'SMKN 1 AIR NANINGAN' }}</span>
          </div>
          <span style="font-size:9px; font-weight:800; background:#f0f9ff; color:#0284c7; padding:2px 8px; border-radius:8px;">GURU &amp; STAF</span>
        </div>

        <div style="display:flex; gap:14px; align-items:center;">
          @php
            $cleanName = preg_replace('/\b(Drs|Dra|Ir|Prof|Dr|H|Hj)\.\s*/i', '', $guru->nama);
            $cleanName = preg_replace('/,.*$/', '', $cleanName);
            $cleanName = trim($cleanName);
            $initials = collect(explode(' ', $cleanName ?: $guru->nama))
                ->filter(fn($part) => !empty($part))
                ->map(fn($part) => mb_substr($part, 0, 1))
                ->take(2)
                ->join('');
            $hasCustomPhoto = !empty($guru->foto) && file_exists(public_path('storage/' . $guru->foto));
          @endphp

          @if($hasCustomPhoto)
            <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->nama }}" style="width:54px; height:54px; border-radius:12px; object-fit:cover; border:2px solid rgba(255,255,255,0.7); background:#ffffff; flex-shrink:0;" />
          @else
            <div style="width:54px; height:54px; border-radius:12px; border:2px solid rgba(255,255,255,0.7); background:#2563eb; color:#ffffff; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:900; letter-spacing:0.5px; flex-shrink:0; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
              {{ $initials }}
            </div>
          @endif

          <div style="min-width:0; flex:1;">
            <div style="font-size:15px; font-weight:900; color:#ffffff; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $guru->nama }}</div>
            <div style="font-size:12px; font-family:var(--font-mono); color:#e0f2fe; margin-top:3px; font-weight:700;">NIP: {{ $guru->nip ?: '-' }}</div>
            <div style="font-size:11.5px; color:#ffffff; opacity:0.9; margin-top:2px;">{{ $guru->jabatan ?? 'Guru / Pendidik' }} · {{ $guru->label_kepegawaian ?? 'Pegawai' }}</div>
          </div>
        </div>
      </div>

      {{-- QR Code Scanner Box --}}
      <div class="barcode-display-box" style="margin-bottom:0;">
        <div style="font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:12px;">
          <i class="bi bi-broadcast-pin"></i> SCANNER GERBANG &amp; KIOSK
        </div>
        
        <div style="background:#ffffff; padding:12px; border-radius:14px; border:1px solid #cbd5e1; box-shadow:0 4px 16px rgba(0,0,0,0.06); display:inline-flex; align-items:center; justify-content:center;">
          <div id="qrContainer"></div>
        </div>

        <div style="font-family:var(--font-mono); font-size:14px; font-weight:900; color:var(--text); letter-spacing:.05em; margin-top:10px;">
          {{ $codeValue }}
        </div>

        <div class="scan-hint" style="margin-top:10px;">
          <i class="bi bi-brightness-high-fill" style="color:#eab308;"></i> Tingkatkan kecerahan layar HP saat memindai pada scanner gerbang sekolah.
        </div>
      </div>

    </div>

    {{-- TOMBOL AKSI --}}
    <div class="btn-action-bar" style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
      <button type="button" id="btnDownloadCard" onclick="downloadFullCard('KARTU_PRESENSI_{{ preg_replace('/[^a-zA-Z0-9_-]/', '_', $guru->nama) }}')" class="btn-mobile" style="background:#ffffff; color:#000000; box-shadow:0 4px 14px rgba(0,0,0,0.15);">
        <i class="bi bi-download"></i> Simpan Gambar Kartu ke Galeri HP
      </button>
      <button type="button" id="btnKirimWaGatewayGuru" onclick="kirimWaGatewayGuru()" class="btn-mobile" style="background:linear-gradient(135deg,#16a34a,#15803d); color:#ffffff; box-shadow:0 4px 14px rgba(22,163,74,0.35);">
        <i class="bi bi-whatsapp"></i> Kirim via WhatsApp Gateway
      </button>
    </div>
  </div>

  <script>
    const _GURU_ID   = {{ $guru->id }};
    const _GURU_NAMA = @json($guru->nama);
    const _GURU_HP   = @json($guru->no_hp ?? '');

    async function kirimWaGatewayGuru() {
      @if(!$guru->no_hp)
        if (!confirm('Nomor WhatsApp guru ini belum terisi di data guru.\nKirim tetap dilanjutkan (mungkin akan gagal di server)?\n\nNama: ' + _GURU_NAMA)) return;
      @endif

      const btn = document.getElementById('btnKirimWaGatewayGuru');
      const origHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengirim via Gateway...';

      try {
        const res = await fetch('{{ route("kartu.digital.kirim.wa") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
          },
          body: JSON.stringify({ type: 'guru', id: _GURU_ID })
        });
        const json = await res.json();
        if (json.success) {
          alert('✅ ' + json.message);
        } else {
          alert('❌ Gagal: ' + (json.message || 'Terjadi kesalahan'));
        }
      } catch (e) {
        console.error(e);
        alert('❌ Gagal terhubung ke server. Pastikan Anda sedang login.');
      } finally {
        btn.disabled = false;
        btn.innerHTML = origHtml;
      }
    }

    async function downloadFullCard(filename) {
      const cardElement = document.getElementById('kartuDigitalArea');
      if (!cardElement) return;

      const btn = document.getElementById('btnDownloadCard');
      const origHtml = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Merender Gambar Kartu...';
      }

      try {
        // Beri sedikit jeda agar DOM dan canvas QR stabil
        await new Promise(r => setTimeout(r, 80));

        const isDark = (document.documentElement.getAttribute('data-theme') || 'dark') === 'dark';
        const bgColor = isDark ? '#090d16' : '#f8fafc';

        // Render area kartu dengan resolusi tinggi (scale 3 = Retina 300 DPI)
        const canvas = await html2canvas(cardElement, {
          scale: 3,
          useCORS: true,
          allowTaint: true,
          backgroundColor: bgColor,
          logging: false,
          scrollX: 0,
          scrollY: -window.scrollY
        });

        const safeFilename = (filename || 'KARTU_PRESENSI_GURU') + '.png';

        // Jika browser HP mendukung Web Share API file sharing
        if (navigator.canShare && window.File) {
          try {
            canvas.toBlob(async (blob) => {
              if (blob) {
                const file = new File([blob], safeFilename, { type: 'image/png' });
                if (navigator.canShare({ files: [file] })) {
                  await navigator.share({
                    files: [file],
                    title: 'Kartu Presensi Guru',
                    text: 'Kartu Presensi Digital SMKN 1 Air Naningan - ' + _GURU_NAMA
                  });
                  return;
                }
              }
              triggerDownloadLink(canvas, safeFilename);
            }, 'image/png');
            return;
          } catch (e) {
            // User membatalkan share atau share gagal, fallback ke direct download
            triggerDownloadLink(canvas, safeFilename);
            return;
          }
        }

        // Direct download browser standar
        triggerDownloadLink(canvas, safeFilename);

      } catch (err) {
        console.error('Error saat merender kartu:', err);
        // Fallback jika html2canvas terkendala
        downloadQrFallback(filename);
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
      }
    }

    function triggerDownloadLink(canvas, filename) {
      const link = document.createElement('a');
      link.download = filename;
      link.href = canvas.toDataURL('image/png');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function downloadQrFallback(filename) {
      const srcCanvas = document.querySelector('#qrContainer canvas');
      if (srcCanvas) {
        triggerDownloadLink(srcCanvas, (filename || 'QR_PRESENSI') + '.png');
      } else {
        alert('Gagal mengunduh gambar kartu. Silakan muat ulang halaman.');
      }
    }

    function toggleThemeMode() {
      const current = document.documentElement.getAttribute('data-theme') || 'dark';
      const next = (current === 'dark') ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('sirani_theme', next);
      updateThemeIcon(next);
    }

    function updateThemeIcon(theme) {
      const icon = document.getElementById('themeIcon');
      if (!icon) return;
      if (theme === 'light') {
        icon.className = 'bi bi-moon-stars-fill';
        icon.style.color = '#0284c7';
      } else {
        icon.className = 'bi bi-sun-fill';
        icon.style.color = '#f59e0b';
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const curTheme = document.documentElement.getAttribute('data-theme') || 'dark';
      updateThemeIcon(curTheme);

      const codeVal = "{{ $codeValue }}";
      new QRCode(document.getElementById("qrContainer"), {
        text: codeVal,
        width: 185,
        height: 185,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    });
  </script>
</body>
</html>

