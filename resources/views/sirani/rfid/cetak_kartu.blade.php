<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Kartu Identitas e-KTP Standar - {{ $sekolah->nama_sekolah ?? 'SMKN 1 AIR NANINGAN' }}</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  {{-- Library Generator 2D QR Code --}}
  <script src="/qrcode.min.js"></script>

  <style>
    :root {
      --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: var(--font-main);
      background: #0f172a;
      color: #0f172a;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* ─── FLOATING TOOLBAR CONTROL (SCREEN ONLY) ─── */
    .no-print-toolbar {
      position: sticky;
      top: 0;
      z-index: 999;
      background: #090d16;
      color: #ffffff;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.5);
    }

    .toolbar-left {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    .toolbar-title {
      font-size: 15px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .toolbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn-tool {
      background: rgba(255, 255, 255, 0.08);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
    }

    .btn-tool:hover {
      background: rgba(255, 255, 255, 0.18);
      color: #ffffff;
    }

    .btn-print {
      background: #ffffff;
      color: #0f172a;
      border-color: #ffffff;
      font-weight: 800;
      box-shadow: 0 2px 10px rgba(255, 255, 255, 0.2);
    }

    .btn-print:hover {
      background: #f1f5f9;
      color: #0f172a;
    }

    .select-tool {
      background: #1e293b;
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 7px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      outline: none;
    }

    /* ─── PRINT SHEET CONTAINER (A4 GRID PRESISI: 2 Kolom x 4 Baris = 8 Kartu) ─── */
    .sheet-page {
      width: 210mm;
      min-height: 297mm;
      margin: 20px auto;
      background: #ffffff;
      padding: 10mm 15mm;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
      display: grid;
      grid-template-columns: repeat(2, 85.6mm);
      grid-auto-rows: 53.98mm;
      gap: 6mm 8mm;
      justify-content: center;
      align-content: start;
    }

    /* ─── INDIVIDUAL ID CARD DESIGN (STANDAR RESMI e-KTP / ISO 7810 ID-1: 85.60mm x 53.98mm) ─── */
    .id-card {
      width: 85.6mm;
      height: 53.98mm;
      border: 1px solid #cbd5e1;
      border-radius: 3.18mm;
      background: #ffffff;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      page-break-inside: avoid;
      break-inside: avoid;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
    }

    /* Subtle Card Background Curve */
    .id-card::before {
      content: "";
      position: absolute;
      right: -15mm;
      bottom: -15mm;
      width: 48mm;
      height: 48mm;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(15, 23, 42, 0.03) 0%, transparent 70%);
      pointer-events: none;
    }

    /* ── Card Header (Pemerintah + Dinas + Sekolah) ── */
    .card-header {
      padding: 2.2mm 3mm 1.8mm;
      display: flex;
      align-items: center;
      gap: 2.5mm;
      position: relative;
    }

    /* Tema Guru: Soft Sapphire & Ocean Sky (BIRU UNTUK GURU) */
    .card-header-guru {
      background: linear-gradient(135deg, #0f2744 0%, #1d4ed8 60%, #0284c7 100%);
      color: #ffffff;
      border-bottom: 2px solid #7dd3fc;
    }

    /* Tema Siswa: Soft Sage Teal & Emerald Silk (HIJAU UNTUK SISWA) */
    .card-header-siswa {
      background: linear-gradient(135deg, #042f2e 0%, #0f766e 60%, #14b8a6 100%);
      color: #ffffff;
      border-bottom: 2px solid #5eead4;
    }

    .card-logo-wrap {
      width: 10.5mm;
      height: 10.5mm;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.45));
    }

    .card-logo {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .card-header-text {
      flex: 1;
      text-align: left;
      line-height: 1.15;
      min-width: 0;
    }

    .card-instansi {
      font-size: 4.4pt;
      font-weight: 700;
      color: #e2e8f0;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .card-dinas {
      font-size: 4.6pt;
      font-weight: 800;
      color: #cbd5e1;
      text-transform: uppercase;
      letter-spacing: .02em;
    }

    .card-school-name {
      font-size: 6.8pt;
      font-weight: 900;
      color: #ffffff;
      letter-spacing: .02em;
      text-transform: uppercase;
      margin-top: 0.2mm;
    }

    .card-badge-wrap {
      margin-top: 0.4mm;
    }

    /* Badge Guru: Soft Pastel Sky Blue */
    .card-title-badge {
      display: inline-block;
      font-size: 4.3pt;
      font-weight: 800;
      color: #0284c7;
      background: #f0f9ff;
      border: 0.5px solid #bae6fd;
      padding: 0.2mm 1.5mm;
      border-radius: 0.8mm;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    /* Badge Siswa: Soft Pastel Mint */
    .card-title-badge-siswa {
      background: #f0fdfa;
      color: #0f766e;
      border: 0.5px solid #99f6e4;
    }

    /* ── Card Body (Foto + Data + QR Code Grid) ── */
    .card-body {
      display: flex;
      align-items: center;
      gap: 2.4mm;
      flex: 1;
      padding: 1.6mm 2.8mm 1.2mm;
      min-height: 0;
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    /* Foto Frame 3x4 Proporsional */
    .card-photo-wrap {
      width: 16.5mm;
      height: 21.5mm;
      border: 1.2px solid #0284c7;
      border-radius: 2mm;
      overflow: hidden;
      background: #f8fafc;
      flex-shrink: 0;
      position: relative;
      box-shadow: 0 1px 4px rgba(15, 23, 42, 0.1);
    }

    .card-photo-wrap-siswa {
      border-color: #0f766e;
    }

    .card-photo {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Avatar Inisial Soft Gradient */
    .card-avatar-initials {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0284c7 0%, #1e3a8a 100%);
      color: #ffffff;
      font-size: 10pt;
      font-weight: 900;
      font-family: var(--font-mono);
      letter-spacing: .05em;
    }

    .card-avatar-initials-siswa {
      background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%);
      color: #ffffff;
    }

    /* Info Tengah */
    .card-info {
      flex: 1;
      min-width: 0;
      line-height: 1.18;
    }

    .card-name {
      font-size: 7.6pt;
      font-weight: 900;
      color: #0284c7;
      line-height: 1.15;
      margin-bottom: 0.8mm;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      border-bottom: 1px dashed #cbd5e1;
      padding-bottom: 0.4mm;
    }

    .card-name-siswa {
      color: #0f766e;
    }

    .card-meta-table {
      width: 100%;
      border-collapse: collapse;
    }

    .card-meta-row {
      font-size: 5.8pt;
      font-weight: 600;
      color: #334155;
    }

    .card-meta-label {
      color: #64748b;
      font-weight: 700;
      width: 12.5mm;
      text-transform: uppercase;
      font-size: 5pt;
      vertical-align: top;
      padding-bottom: 0.6mm;
    }

    .card-meta-colon {
      width: 2mm;
      text-align: center;
      color: #64748b;
      font-weight: 700;
      vertical-align: top;
      padding-bottom: 0.6mm;
    }

    .card-meta-val {
      font-weight: 800;
      font-family: var(--font-mono);
      color: #0f172a;
      vertical-align: top;
      padding-bottom: 0.6mm;
      word-break: break-word;
    }

    /* Polos tanpa border dan tanpa background */
    .card-meta-text-clean {
      font-family: var(--font-main);
      font-weight: 700;
      color: #1e293b;
      background: transparent !important;
      border: none !important;
      padding: 0 !important;
      font-size: 5.8pt;
      line-height: 1.2;
      display: inline;
    }

    /* Area QR Code (Kanan) Soft Pastel Frame */
    .card-qr-section {
      width: 17.5mm;
      flex-shrink: 0;
      background: #f0f9ff;
      border: 1px solid #e0f2fe;
      border-radius: 1.8mm;
      padding: 0.8mm;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .card-qr-section-siswa {
      background: #f0fdfa;
      border-color: #ccfbf1;
    }

    .card-qr-tag {
      font-size: 4.2pt;
      font-weight: 800;
      color: #0284c7;
      text-transform: uppercase;
      letter-spacing: .03em;
      margin-bottom: 0.4mm;
    }

    .card-qr-tag-siswa {
      color: #0f766e;
    }

    .qr-box-wrap {
      width: 13.5mm;
      height: 13.5mm;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      padding: 0.3mm;
      border: 0.5px solid #0284c7;
      border-radius: 0.8mm;
    }

    .qr-box-wrap-siswa {
      border-color: #0f766e;
    }

    .qr-box-wrap img, .qr-box-wrap canvas {
      width: 100% !important;
      height: 100% !important;
    }

    .card-code-text {
      font-family: var(--font-mono);
      font-size: 4.8pt;
      font-weight: 900;
      color: #0284c7;
      margin-top: 0.4mm;
      letter-spacing: .02em;
      white-space: nowrap;
      max-width: 16.5mm;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .card-code-text-siswa {
      color: #0f766e;
    }

    /* ── Card Footer Aksen ── */
    .card-footer {
      background: linear-gradient(135deg, #0c1a30 0%, #1e3a8a 100%);
      color: #ffffff;
      padding: 0.8mm 3mm;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 4.5pt;
      font-weight: 700;
      letter-spacing: .03em;
    }

    .card-footer-siswa {
      background: linear-gradient(135deg, #042f2e 0%, #0f766e 100%);
    }

    .footer-left {
      display: flex;
      align-items: center;
      gap: 1mm;
      color: #e2e8f0;
    }

    .footer-left i {
      color: #7dd3fc;
      font-size: 5.5pt;
    }

    .card-footer-siswa .footer-left i {
      color: #5eead4;
    }

    .footer-right {
      font-family: var(--font-mono);
      color: #7dd3fc;
      font-weight: 800;
    }

    .card-footer-siswa .footer-right {
      color: #5eead4;
    }

    /* ─── PRINT MEDIA QUERY (PRESISI 100% KERTAS A4) ─── */
    @media print {
      body {
        background: #ffffff !important;
      }

      .no-print, .no-print-toolbar {
        display: none !important;
      }

      .sheet-page {
        margin: 0 !important;
        padding: 8mm 12mm !important;
        box-shadow: none !important;
        page-break-after: always;
      }

      .id-card {
        box-shadow: none !important;
        border: 1px solid #94a3b8 !important;
      }
    }
  </style>
</head>
<body>

{{-- FLOATING TOP TOOLBAR (SCREEN ONLY) --}}
<div class="no-print-toolbar no-print">
  <div class="toolbar-left">
    <a href="{{ route('rfid.index', ['tab' => $tab]) }}" class="btn-tool" title="Kembali ke Manajemen Kartu">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div class="toolbar-title">
      <i class="bi bi-person-vcard-fill" style="color:#7dd3fc;"></i>
      <span>Cetak Kartu Presensi e-KTP ({{ $tab === 'siswa' ? 'Siswa' : 'Guru & Pegawai' }})</span>
      @if(!empty($selectedIds))
        <span style="background:#22c55e; color:#ffffff; font-size:11.5px; font-weight:800; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px; margin-left:6px;">
          <i class="bi bi-check2-circle"></i> {{ $items->count() }} Kartu Terpilih
        </span>
      @endif
    </div>
  </div>

  <div class="toolbar-right">
    {{-- Filter Tab --}}
    <a href="{{ route('rfid.cetak', ['tab' => 'siswa', 'format' => $format]) }}" class="btn-tool {{ $tab === 'siswa' ? 'btn-print' : '' }}">
      <i class="bi bi-people-fill"></i> Kartu Siswa (Hijau)
    </a>
    <a href="{{ route('rfid.cetak', ['tab' => 'guru', 'format' => $format]) }}" class="btn-tool {{ $tab === 'guru' ? 'btn-print' : '' }}">
      <i class="bi bi-person-badge-fill"></i> Kartu Guru (Biru)
    </a>

    {{-- Filter Rombel (jika tab siswa) --}}
    @if($tab === 'siswa')
      <select class="select-tool" onchange="location.href='{{ route('rfid.cetak', ['tab' => 'siswa', 'format' => $format]) }}&rombel_id=' + this.value">
        <option value="">-- Semua Rombel ({{ $items->count() }} Siswa) --</option>
        @foreach($rombels as $r)
          <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
        @endforeach
      </select>
    @endif

    {{-- Tombol Cetak --}}
    <button type="button" onclick="window.print()" class="btn-tool btn-print">
      <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
    </button>
  </div>
</div>

{{-- SHEET KARTU IDENTITAS DIGITAL (A4 PRINT READY - STANDAR e-KTP 85.6mm x 54mm) --}}
<div class="sheet-page">
  @forelse($items as $item)
    @php
      $isSiswa = ($tab === 'siswa');
      if ($isSiswa) {
          $nama = $item->nama;
          $nisOrNip = $item->nisn ?: '-';
          $labelNis = 'NISN';
          $rombelNama = $item->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
          $jurusanNama = $item->siswaRombels->first()?->rombel?->jurusan?->nama_jurusan ?? '-';
          $fotoField = $item->foto;
          $fotoUrl = ($fotoField && file_exists(public_path('storage/' . $fotoField))) ? asset('storage/' . $fotoField) : null;
          $codeValue = $item->kartuRfid?->uid ?? ($item->nisn ?: 'SISWA-'.$item->id);
          $cardCategory = 'KARTU PELAJAR & PRESENSI';
      } else {
          $nama = $item->nama;
          $nisOrNip = $item->nip ?: '-';
          $labelNis = 'NIP/NUPTK';
          $rombelNama = $item->jabatan ?? 'Guru / Pendidik';
          $jurusanNama = $item->label_kepegawaian ?? 'Pegawai';
          $fotoField = $item->foto;
          $fotoUrl = ($fotoField && file_exists(public_path('storage/' . $fotoField))) ? asset('storage/' . $fotoField) : null;
          $codeValue = $item->kartuRfid?->uid ?? ($item->nip ?: 'GURU-'.$item->id);
          $cardCategory = 'KARTU IDENTITAS GURU & STAF';
      }

      $hasCustomPhoto = !empty($fotoUrl);
      
      // Bersihkan inisial nama dari gelar
      $cleanName = preg_replace('/\b(Drs|Dra|Ir|Prof|Dr|H|Hj)\.\s*/i', '', $nama);
      $cleanName = preg_replace('/,.*$/', '', $cleanName);
      $cleanName = trim($cleanName);
      
      $initials = collect(explode(' ', $cleanName ?: $nama))
          ->filter(fn($part) => !empty($part))
          ->map(fn($part) => mb_substr($part, 0, 1))
          ->take(2)
          ->join('');
    @endphp

    <div class="id-card">
      {{-- Card Header: Kop 3 Tingkat Resmi --}}
      <div class="card-header {{ $isSiswa ? 'card-header-siswa' : 'card-header-guru' }}">
        <div class="card-logo-wrap">
          <img src="{{ !empty($sekolah->logo_sekolah) ? asset('storage/'.$sekolah->logo_sekolah) : '/img/logo.png' }}" alt="Logo" class="card-logo" onerror="this.src='/img/logo.png';" />
        </div>
        <div class="card-header-text">
          <div class="card-instansi">PEMERINTAH PROVINSI LAMPUNG</div>
          <div class="card-dinas">{{ $sekolah->nama_dinas ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
          <div class="card-school-name">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
          <div class="card-badge-wrap">
            <span class="card-title-badge {{ $isSiswa ? 'card-title-badge-siswa' : '' }}">{{ $cardCategory }}</span>
          </div>
        </div>
      </div>

      {{-- Card Body: Foto + Data + QR Code --}}
      <div class="card-body">
        {{-- Foto Profil --}}
        <div class="card-photo-wrap {{ $isSiswa ? 'card-photo-wrap-siswa' : '' }}">
          @if($hasCustomPhoto)
            <img src="{{ $fotoUrl }}" alt="{{ $nama }}" class="card-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
            <div class="card-avatar-initials {{ $isSiswa ? 'card-avatar-initials-siswa' : '' }}" style="display:none;">{{ $initials }}</div>
          @else
            <div class="card-avatar-initials {{ $isSiswa ? 'card-avatar-initials-siswa' : '' }}">{{ $initials }}</div>
          @endif
        </div>

        {{-- Identitas Rapi --}}
        <div class="card-info">
          <div class="card-name {{ $isSiswa ? 'card-name-siswa' : '' }}" title="{{ $nama }}">{{ $nama }}</div>
          
          <table class="card-meta-table">
            <tr class="card-meta-row">
              <td class="card-meta-label">{{ $labelNis }}</td>
              <td class="card-meta-colon">:</td>
              <td class="card-meta-val">{{ $nisOrNip }}</td>
            </tr>
            <tr class="card-meta-row">
              <td class="card-meta-label">{{ $isSiswa ? 'Kelas' : 'Jabatan' }}</td>
              <td class="card-meta-colon">:</td>
              <td class="card-meta-val"><span class="card-meta-text-clean" style="font-weight:800;">{{ $rombelNama }}</span></td>
            </tr>
            <tr class="card-meta-row">
              <td class="card-meta-label">{{ $isSiswa ? 'Jurusan' : 'Status' }}</td>
              <td class="card-meta-colon">:</td>
              <td class="card-meta-val"><span class="card-meta-text-clean">{{ $jurusanNama }}</span></td>
            </tr>
          </table>
        </div>

        {{-- QR Code Presensi Resmi --}}
        <div class="card-qr-section {{ $isSiswa ? 'card-qr-section-siswa' : '' }}">
          <div class="card-qr-tag {{ $isSiswa ? 'card-qr-tag-siswa' : '' }}">Scan Presensi</div>
          <div class="qr-box-wrap {{ $isSiswa ? 'qr-box-wrap-siswa' : '' }}" id="qr_wrap_{{ $item->id }}" data-qr-val="{{ $codeValue }}"></div>
          <div class="card-code-text {{ $isSiswa ? 'card-code-text-siswa' : '' }}" title="{{ $codeValue }}">{{ $codeValue }}</div>
        </div>
      </div>

      {{-- Card Footer --}}
      <div class="card-footer {{ $isSiswa ? 'card-footer-siswa' : '' }}">
        <div class="footer-left">
          <i class="bi bi-broadcast-pin"></i>
          <span>RFID / SMART CARD SYSTEM</span>
        </div>
        <div class="footer-right">
          <span>SMKN 1 AN • TANGGAMUS</span>
        </div>
      </div>
    </div>
  @empty
    <div style="grid-column:1 / -1; text-align:center; padding:60px 20px; color:#94a3b8;">
      <i class="bi bi-person-x" style="font-size:44px; display:block; margin-bottom:10px;"></i>
      <strong style="font-size:14px; color:#ffffff;">Tidak ada data {{ $tab === 'siswa' ? 'siswa' : 'guru & staf' }} yang ditemukan.</strong>
    </div>
  @endforelse
</div>

<script>
  // Render 2D QR Codes High Precision
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.qr-box-wrap').forEach(el => {
      const val = el.getAttribute('data-qr-val');
      if (val) {
        new QRCode(el, {
          text: val,
          width: 52,
          height: 52,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.M
        });
      }
    });
  });
</script>
</body>
</html>
