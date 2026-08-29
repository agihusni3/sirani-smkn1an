<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Piagam Penghargaan Kehadiran — {{ $nama }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Great+Vibes&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @page {
      size: A4 landscape;
      margin: 0;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #CBD5E1;
      color: #0F172A;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
      min-height: 100vh;
    }

    /* ── Floating Customizer Toolbar (No Print) ── */
    .toolbar-wrapper {
      width: 100%;
      max-width: 280mm;
      margin-bottom: 16px;
      background: #0F172A;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      padding: 14px 20px;
      color: #F8FAFC;
    }
    .toolbar-main {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }
    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .toolbar-btn {
      background: #1E293B;
      color: #F8FAFC;
      border: 1px solid #334155;
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      transition: all .2s;
    }
    .toolbar-btn:hover {
      background: #334155;
      color: #FFFFFF;
    }
    .btn-print {
      background: linear-gradient(135deg, #CA8A04, #EAB308);
      color: #0F172A;
      border: none;
      padding: 9px 22px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 900;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(202,138,4,0.4);
    }
    .btn-print:hover {
      transform: translateY(-1px);
    }

    /* ── Controls Panel ── */
    .controls-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      padding-top: 14px;
      margin-top: 14px;
      border-top: 1px solid #334155;
      font-size: 11.5px;
    }
    .control-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .control-label {
      color: #94A3B8;
      font-weight: 700;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .control-input {
      background: #1E293B;
      border: 1px solid #475569;
      color: #FFFFFF;
      padding: 6px 10px;
      border-radius: 6px;
      font-size: 12px;
    }
    .toggle-chip {
      background: #1E293B;
      border: 1px solid #475569;
      color: #CBD5E1;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      user-select: none;
    }
    .toggle-chip.active {
      background: #059669;
      border-color: #10B981;
      color: #FFFFFF;
      font-weight: 800;
    }

    /* ═══════════════════════════════════════════════════════════════════ */
    /* ── CERTIFICATE CANVAS CONTAINER ── */
    /* ═══════════════════════════════════════════════════════════════════ */
    .cert-canvas-wrapper {
      position: relative;
      width: 280mm;
      height: 196mm;
      background: #FFFFFF;
      box-shadow: 0 12px 40px rgba(0,0,0,0.3);
      overflow: hidden;
    }

    /* Background Template Layer */
    .cert-background-img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: fill;
      z-index: 1;
      pointer-events: none;
    }

    /* Default Clean Border */
    .cert-default-border {
      position: absolute;
      top: 10mm;
      left: 10mm;
      right: 10mm;
      bottom: 10mm;
      border: 2px solid #CA8A04;
      outline: 6px solid #0F172A;
      outline-offset: 4px;
      pointer-events: none;
      z-index: 2;
    }

    /* Content Layout Layer */
    .cert-content-layer {
      position: relative;
      z-index: 5;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 16mm 20mm;
    }

    /* Header Kop */
    .cert-kop-block {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      border-bottom: 2px double #CBD5E1;
      padding-bottom: 8px;
    }
    .cert-logo {
      width: 50px;
      height: 50px;
      object-fit: contain;
    }
    .cert-kop-text {
      text-align: center;
    }
    .cert-kop-text h3 {
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 1.5px;
      color: #64748B;
      text-transform: uppercase;
    }
    .cert-kop-text h2 {
      font-size: 15px;
      font-weight: 900;
      letter-spacing: 1px;
      color: #0F172A;
      text-transform: uppercase;
      margin: 1px 0;
    }
    .cert-kop-text p {
      font-size: 9.5px;
      color: #64748B;
    }

    /* Main Certificate Body */
    .cert-body-block {
      text-align: center;
      padding: 0 10px;
      transition: transform .2s ease;
    }
    .cert-title-main {
      font-family: 'Cinzel', serif;
      font-size: 24px;
      font-weight: 900;
      color: #CA8A04;
      letter-spacing: 3px;
      text-transform: uppercase;
      margin-bottom: 2px;
    }
    .cert-title-sub {
      font-size: 11.5px;
      font-weight: 800;
      letter-spacing: 1.5px;
      color: #0F172A;
      text-transform: uppercase;
      margin-bottom: 2px;
    }
    .cert-nomor-surat {
      font-size: 10px;
      font-family: monospace;
      color: #64748B;
      margin-bottom: 12px;
    }
    .cert-greeting {
      font-size: 11.5px;
      color: #475569;
      margin-bottom: 6px;
    }
    .cert-name-text {
      font-size: 24px;
      font-weight: 900;
      color: #0F172A;
      text-transform: uppercase;
      display: inline-block;
      padding: 0 20px 4px 20px;
      margin-bottom: 4px;
      letter-spacing: 0.5px;
    }
    .cert-identity-text {
      font-size: 12px;
      font-weight: 700;
      color: #64748B;
      margin-bottom: 10px;
    }
    .cert-statement-text {
      font-size: 12px;
      line-height: 1.6;
      color: #334155;
      max-width: 88%;
      margin: 0 auto;
    }

    /* Footer Signatures */
    .cert-footer-block {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding: 0 24px 6px 24px;
    }
    .cert-seal-box {
      width: 65px;
      height: 65px;
      border-radius: 50%;
      border: 2px dashed #CA8A04;
      background: rgba(202,138,4,0.06);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: 9px;
      font-weight: 900;
      color: #CA8A04;
      text-transform: uppercase;
    }
    .cert-sign-box {
      text-align: center;
      min-width: 220px;
    }
    .cert-sign-date {
      font-size: 11px;
      color: #475569;
      margin-bottom: 3px;
    }
    .cert-sign-title {
      font-size: 11px;
      font-weight: 800;
      color: #0F172A;
      text-transform: uppercase;
      margin-bottom: 42px;
    }
    .cert-sign-name {
      font-size: 13px;
      font-weight: 900;
      color: #0F172A;
      text-decoration: underline;
    }
    .cert-sign-nip {
      font-size: 10.5px;
      font-family: monospace;
      color: #64748B;
    }

    @media print {
      body {
        background: transparent;
        padding: 0;
      }
      .toolbar-wrapper {
        display: none !important;
      }
      .cert-canvas-wrapper {
        box-shadow: none;
        margin: 0;
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body>

@php
  $hasCustomBg = !empty($sekolah->template_piagam);
  $customBgUrl = $hasCustomBg ? asset('storage/' . $sekolah->template_piagam) : '';
  $cfg = json_decode($sekolah->template_piagam_config ?? '{}', true) ?: [];
  
  $showBg = $cfg['showBg'] ?? true;
  $showKop = $cfg['showKop'] ?? true;
  $showBorder = $cfg['showBorder'] ?? (!$hasCustomBg);
  $showSeal = $cfg['showSeal'] ?? true;
  $bodyOffsetY = $cfg['bodyOffsetY'] ?? 0;
  $nameSize = $cfg['nameSize'] ?? 24;
  $nameColor = $cfg['nameColor'] ?? '#0F172A';
  $fontFamily = $cfg['fontFamily'] ?? "'Cinzel', serif";
@endphp

<!-- ══ Floating Customizer Toolbar ══ -->
<div class="toolbar-wrapper no-print">
  <div class="toolbar-main">
    <div class="toolbar-actions">
      <a href="{{ route('peringkat.index') }}" class="toolbar-btn">
        <i class="bi bi-arrow-left"></i> Leaderboard
      </a>

      {{-- Upload Background Template Sendiri --}}
      <label class="toolbar-btn" style="background:#0284C7; border-color:#0284C7; cursor:pointer;" title="Upload gambar blank template JPG/PNG">
        <i class="bi bi-cloud-arrow-up-fill"></i> Upload Template Background
        <input type="file" id="inputCustomTemplate" accept="image/png,image/jpeg,image/webp" style="display:none;" onchange="handleTemplateUpload(this)" />
      </label>

      @if($hasCustomBg)
        <button type="button" class="toolbar-btn" style="color:#EF4444;" onclick="resetTemplate()">
          <i class="bi bi-trash3-fill"></i> Hapus Template
        </button>
      @endif

      <button type="button" class="toolbar-btn" onclick="toggleControls()">
        <i class="bi bi-sliders"></i> Atur Tata Letak
      </button>
    </div>

    <div class="toolbar-actions">
      <button type="button" class="btn-print" onclick="window.print()">
        <i class="bi bi-printer-fill"></i> Cetak Piagam
      </button>
    </div>
  </div>

  <!-- ══ Panel Kontrol Tata Letak & Visibilitas ══ -->
  <div class="controls-grid" id="controlsPanel" style="display:none;">
    {{-- Toggle Background Gambar --}}
    <div class="control-item">
      <span class="control-label">Background Template:</span>
      <button type="button" class="toggle-chip {{ $showBg ? 'active' : '' }}" id="btnToggleBg" onclick="toggleElement('showBg')">
        <i class="bi bi-image"></i> <span id="textToggleBg">{{ $showBg ? 'Tampil (Kertas Polos)' : 'Sembunyi (Kertas Blangko)' }}</span>
      </button>
    </div>

    {{-- Toggle Kop Instansi --}}
    <div class="control-item">
      <span class="control-label">Kop Surat Sekolah:</span>
      <button type="button" class="toggle-chip {{ $showKop ? 'active' : '' }}" id="btnToggleKop" onclick="toggleElement('showKop')">
        <i class="bi bi-building"></i> <span id="textToggleKop">{{ $showKop ? 'Tampilkan Kop' : 'Sembunyikan Kop' }}</span>
      </button>
    </div>

    {{-- Toggle Bingkai Bawaan --}}
    <div class="control-item">
      <span class="control-label">Bingkai Default:</span>
      <button type="button" class="toggle-chip {{ $showBorder ? 'active' : '' }}" id="btnToggleBorder" onclick="toggleElement('showBorder')">
        <i class="bi bi-border"></i> <span id="textToggleBorder">{{ $showBorder ? 'Bingkai Aktif' : 'Tanpa Bingkai' }}</span>
      </button>
    </div>

    {{-- Toggle Cap Stempel --}}
    <div class="control-item">
      <span class="control-label">Cap Stempel:</span>
      <button type="button" class="toggle-chip {{ $showSeal ? 'active' : '' }}" id="btnToggleSeal" onclick="toggleElement('showSeal')">
        <i class="bi bi-patch-check"></i> <span id="textToggleSeal">{{ $showSeal ? 'Cap Aktif' : 'Cap Tersembunyi' }}</span>
      </button>
    </div>

    {{-- Posisi Vertikal Teks (Geser Atas/Bawah) --}}
    <div class="control-item">
      <span class="control-label">Posisi Teks (Geser Y): <span id="valBodyOffsetY">{{ $bodyOffsetY }}px</span></span>
      <input type="range" class="control-input" min="-60" max="100" value="{{ $bodyOffsetY }}" oninput="updateOffsetY(this.value)" />
    </div>

    {{-- Ukuran Font Nama --}}
    <div class="control-item">
      <span class="control-label">Ukuran Nama: <span id="valNameSize">{{ $nameSize }}px</span></span>
      <input type="range" class="control-input" min="18" max="40" value="{{ $nameSize }}" oninput="updateNameSize(this.value)" />
    </div>

    {{-- Warna Font Nama --}}
    <div class="control-item">
      <span class="control-label">Warna Nama:</span>
      <input type="color" class="control-input" style="height:32px; padding:2px; cursor:pointer; width:100%;" value="{{ $nameColor }}" oninput="updateNameColor(this.value)" />
    </div>

    {{-- Simpan Posisi Default --}}
    <div class="control-item" style="justify-content:flex-end;">
      <button type="button" class="toolbar-btn" style="background:#059669; border-color:#059669; justify-content:center;" onclick="saveConfigToServer()">
        <i class="bi bi-check2-circle"></i> Simpan Setelan Default
      </button>
    </div>
  </div>
</div>

<!-- ══ Certificate Canvas ══ -->
<div class="cert-canvas-wrapper" id="certCanvas">
  <!-- Template Background Image Layer -->
  <img src="{{ $customBgUrl }}" alt="Template Piagam" class="cert-background-img" id="certBgImg" style="{{ ($hasCustomBg && $showBg) ? 'display:block;' : 'display:none;' }}" />

  <!-- Default Frame Border Layer -->
  <div class="cert-default-border" id="certDefaultBorder" style="{{ $showBorder ? 'display:block;' : 'display:none;' }}"></div>

  <!-- Text & Elements Content Layer -->
  <div class="cert-content-layer">
    <!-- Header Instansi Kop -->
    <div class="cert-kop-block" id="certKopBlock" style="{{ $showKop ? 'display:flex;' : 'display:none;' }}">
      <img src="/img/logo.png" alt="Logo Sekolah" class="cert-logo" />
      <div class="cert-kop-text">
        <h3>PEMERINTAH PROVINSI LAMPUNG · DINAS PENDIDIKAN DAN KEBUDAYAAN</h3>
        <h2>{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</h2>
        <p>{{ $sekolah->alamat ?? 'Air Naningan, Kabupaten Tanggamus, Lampung' }} · NPSN: {{ $sekolah->npsn ?? '69888998' }}</p>
      </div>
    </div>

    <!-- Isi Piagam -->
    <div class="cert-body-block" id="certBodyBlock" style="transform: translateY({{ $bodyOffsetY }}px);">
      <div class="cert-title-main" id="certTitleMain">PIAGAM PENGHARGAAN</div>
      <div class="cert-title-sub">KEDISIPLINAN &amp; KEHADIRAN TELADAN</div>
      <div class="cert-nomor-surat">Nomor: 421.5 / {{ $tipe === 'siswa' ? 'DISIPLIN-SISWA' : 'DISIPLIN-GURU' }} / {{ date('Y') }} / #{{ $rank }}</div>

      <div class="cert-greeting">Diberikan dengan bangga dan rasa hormat kepada:</div>
      <div class="cert-name-text" id="certNameText" style="font-size:{{ $nameSize }}px; color:{{ $nameColor }};">
        {{ $nama }}
      </div>
      <div class="cert-identity-text">{{ $nomorInduk }} &nbsp;·&nbsp; {{ $instansi }}</div>

      <div class="cert-statement-text">
        Atas prestasi, loyalitas, serta kedisiplinan luar biasa sebagai <strong>{{ strtoupper($predikat) }} (Peringkat #{{ $rank }})</strong> dengan tingkat kehadiran <strong>{{ $persen }}%</strong> pada periode <strong>{{ $periode }}</strong> di {{ $sekolah->nama_sekolah ?? 'SMKN 1 Air Naningan' }}.
        @if(!empty($avgMasuk) && $avgMasuk !== '-')
          <div style="font-size:11px; color:#475569; margin-top:6px; font-style:italic;">
            (Rata-rata Waktu Kedatangan: <strong>{{ $avgMasuk }}</strong> · Akumulasi Jam Efektif: <strong>{{ $durasi }}</strong>)
          </div>
        @endif
      </div>
    </div>

    <!-- Footer & Tanda Tangan -->
    <div class="cert-footer-block">
      <div class="cert-seal-box" id="certSealBox" style="{{ $showSeal ? 'display:flex;' : 'display:none;' }}">
        <i class="bi bi-patch-check-fill" style="font-size:22px; margin-bottom:2px;"></i>
        <span>TERVERIFIKASI</span>
      </div>

      <div class="cert-sign-box">
        <div class="cert-sign-date">Air Naningan, {{ $tanggalCetak }}</div>
        <div class="cert-sign-title">Kepala Sekolah,</div>
        <div class="cert-sign-name">{{ $sekolah->nama_kepala_sekolah ?? 'H. AGUNG WIDODO, M.Pd' }}</div>
        <div class="cert-sign-nip">NIP: {{ $sekolah->nip_kepala_sekolah ?? '197505122000031002' }}</div>
      </div>
    </div>
  </div>
</div>

<script>
  let configState = {
    showBg: {{ $showBg ? 'true' : 'false' }},
    showKop: {{ $showKop ? 'true' : 'false' }},
    showBorder: {{ $showBorder ? 'true' : 'false' }},
    showSeal: {{ $showSeal ? 'true' : 'false' }},
    bodyOffsetY: {{ $bodyOffsetY }},
    nameSize: {{ $nameSize }},
    nameColor: "{{ $nameColor }}"
  };

  function toggleControls() {
    const p = document.getElementById('controlsPanel');
    p.style.display = (p.style.display === 'none' ? 'grid' : 'none');
  }

  function toggleElement(key) {
    configState[key] = !configState[key];
    
    if (key === 'showBg') {
      document.getElementById('btnToggleBg').classList.toggle('active', configState.showBg);
      document.getElementById('textToggleBg').innerText = configState.showBg ? 'Tampil (Kertas Polos)' : 'Sembunyi (Kertas Blangko)';
      document.getElementById('certBgImg').style.display = configState.showBg ? 'block' : 'none';
    } else if (key === 'showKop') {
      document.getElementById('btnToggleKop').classList.toggle('active', configState.showKop);
      document.getElementById('textToggleKop').innerText = configState.showKop ? 'Tampilkan Kop' : 'Sembunyikan Kop';
      document.getElementById('certKopBlock').style.display = configState.showKop ? 'flex' : 'none';
    } else if (key === 'showBorder') {
      document.getElementById('btnToggleBorder').classList.toggle('active', configState.showBorder);
      document.getElementById('textToggleBorder').innerText = configState.showBorder ? 'Bingkai Aktif' : 'Tanpa Bingkai';
      document.getElementById('certDefaultBorder').style.display = configState.showBorder ? 'block' : 'none';
    } else if (key === 'showSeal') {
      document.getElementById('btnToggleSeal').classList.toggle('active', configState.showSeal);
      document.getElementById('textToggleSeal').innerText = configState.showSeal ? 'Cap Aktif' : 'Cap Tersembunyi';
      document.getElementById('certSealBox').style.display = configState.showSeal ? 'flex' : 'none';
    }
  }

  function updateOffsetY(val) {
    configState.bodyOffsetY = parseInt(val);
    document.getElementById('valBodyOffsetY').innerText = val + 'px';
    document.getElementById('certBodyBlock').style.transform = `translateY(${val}px)`;
  }

  function updateNameSize(val) {
    configState.nameSize = parseInt(val);
    document.getElementById('valNameSize').innerText = val + 'px';
    document.getElementById('certNameText').style.fontSize = val + 'px';
  }

  function updateNameColor(val) {
    configState.nameColor = val;
    document.getElementById('certNameText').style.color = val;
  }

  // Upload Background Template Image
  function handleTemplateUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    // Local instant preview
    const reader = new FileReader();
    reader.onload = (e) => {
      const bgImg = document.getElementById('certBgImg');
      bgImg.src = e.target.result;
      bgImg.style.display = 'block';
      configState.showBg = true;
      configState.showBorder = false;
      document.getElementById('btnToggleBg').classList.add('active');
      document.getElementById('btnToggleBorder').classList.remove('active');
      document.getElementById('certDefaultBorder').style.display = 'none';
    };
    reader.readAsDataURL(file);

    // Ajax upload to backend
    const formData = new FormData();
    formData.append('template_gambar', file);

    fetch("{{ route('peringkat.upload-template') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      },
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Template berhasil diunggah dan disimpan!');
      }
    })
    .catch(err => console.error(err));
  }

  function saveConfigToServer() {
    fetch("{{ route('peringkat.save-template-config') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ config: configState })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('Setelan posisi & tata letak piagam berhasil disimpan!');
      }
    })
    .catch(err => console.error(err));
  }

  function resetTemplate() {
    if (!confirm('Hapus gambar template kustom dan kembali ke format standar?')) return;

    fetch("{{ route('peringkat.reset-template') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(r => r.json())
    .then(data => {
      window.location.reload();
    });
  }
</script>

</body>
</html>
