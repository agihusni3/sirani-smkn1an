<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Tenaga Pendidik & Kependidikan - SMKN 1 Air Naningan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 15mm 15mm 15mm;
    }
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #525659;
      font-family: 'Times New Roman', Times, serif;
      color: #000000;
      font-size: 10pt;
      line-height: 1.35;
      -webkit-font-smoothing: antialiased;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 24px 0 40px;
    }
    .print-actions-bar {
      width: 210mm;
      max-width: 95vw;
      background: #1E293B;
      border: 1px solid #334155;
      border-radius: 12px;
      padding: 12px 18px;
      margin-bottom: 18px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }
    .print-title-info {
      color: #FFFFFF;
      font-size: 13.5px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .badge-a4 {
      background: rgba(234, 179, 8, 0.2);
      color: #FACC15;
      border: 1px solid rgba(234, 179, 8, 0.4);
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }
    .btn-action-group {
      display: flex;
      gap: 8px;
    }
    .btn-print {
      background: linear-gradient(135deg, #FACC15, #EAB308);
      color: #0F172A;
      font-weight: 800;
      font-size: 13px;
      padding: 8px 18px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(234, 179, 8, 0.4);
    }
    .btn-back {
      background: rgba(255,255,255,0.1);
      color: #FFFFFF;
      font-weight: 700;
      font-size: 13px;
      padding: 8px 14px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.2);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }

    /* KERTAS A4 PORTRAIT */
    .a4-sheet {
      width: 210mm;
      min-height: 297mm;
      background: #FFFFFF;
      padding: 12mm 15mm 15mm 15mm;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      position: relative;
    }

    /* KOP SURAT DINAS */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      text-align: center;
      margin-bottom: 2px;
      width: 100%;
    }
    .kop-logo-left, .kop-logo-right {
      width: 68px;
      min-width: 68px;
      max-width: 68px;
      height: 68px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo-left img, .kop-logo-right img {
      max-width: 68px;
      max-height: 68px;
      width: auto;
      height: auto;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }
    .kop-text {
      flex: 1;
      text-align: center;
      padding: 0 6px;
      margin: 0;
      min-width: 0;
    }
    .kop-instansi {
      font-size: 11pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.15;
    }
    .kop-dinas {
      font-size: 12pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.2;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.25;
      margin-top: 1px;
    }
    .kop-alamat {
      font-size: 8pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #222222;
      line-height: 1.3;
      margin-top: 2px;
    }
    .kop-border {
      border-top: 2.5px solid #000000;
      border-bottom: 0.8px solid #000000;
      height: 3.5px;
      margin: 5px 0 14px;
    }

    /* JUDUL */
    .judul-laporan {
      text-align: center;
      margin-bottom: 12px;
    }
    .judul-laporan h2 {
      font-size: 12.5pt;
      font-weight: 800;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .judul-laporan .sub-judul {
      font-size: 10pt;
      font-weight: 600;
      margin-top: 2px;
    }

    /* TABEL DATA */
    .table-data {
      width: 100%;
      border-collapse: collapse;
      font-size: 9pt;
      margin-bottom: 16px;
    }
    .table-data th, .table-data td {
      border: 1px solid #000000;
      padding: 4px 6px;
    }
    .table-data th {
      background-color: #F1F5F9;
      text-align: center;
      font-weight: 700;
    }
    .table-data td.text-center {
      text-align: center;
    }

    /* TANDA TANGAN */
    .ttd-container {
      width: 100%;
      margin-top: 18px;
      font-size: 10pt;
      page-break-inside: avoid;
    }
    .ttd-table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
    }
    .ttd-table td {
      width: 50%;
      vertical-align: top;
      padding: 4px;
    }
    .ttd-space {
      height: 50px;
    }
    .ttd-name {
      font-weight: 700;
      text-decoration: underline;
    }

    @media print {
      body {
        background: transparent;
        padding: 0;
      }
      .no-print, .print-actions-bar {
        display: none !important;
      }
      .a4-sheet {
        box-shadow: none;
        padding: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body>

  {{-- FLOATING ACTION TOOLBAR --}}
  <div class="print-actions-bar no-print">
    <div class="print-title-info">
      <i class="bi bi-file-earmark-pdf-fill" style="color:#FACC15; font-size:18px;"></i>
      <span>Daftar Guru &amp; Tenaga Kependidikan (Format Standar A4)</span>
      <span class="badge-a4">A4 Portrait</span>
    </div>
    <div class="btn-action-group">
      <a href="{{ url('/guru') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali ke Data Guru
      </a>
      <button type="button" onclick="window.print()" class="btn-print">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF (A4)
      </button>
    </div>
  </div>

  {{-- LEMBAR A4 RESMI --}}
  <div class="a4-sheet">

    {{-- KOP SURAT DINAS --}}
    <div class="kop-container">
      <div class="kop-logo-left">
        <img src="/img/logo_prov_lampung.png" alt="Logo Provinsi Lampung" onerror="this.onerror=null; this.src='/img/logo_prov_lampung.svg';" />
      </div>
      <div class="kop-text">
        <div class="kop-instansi">{{ $sekolah->nama_instansi_atas ?? 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
        <div class="kop-dinas">{{ $sekolah->nama_dinas ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
        <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div class="kop-alamat">
          {{ $sekolah->alamat_lengkap ?? $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}<br />
          Email: {{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }} · Website: {{ $sekolah->website ?? 'smkn1airnaningan.sch.id' }}
        </div>
      </div>
      <div class="kop-logo-right">
        @if(!empty($sekolah->logo_sekolah))
          <img src="{{ asset('storage/'.$sekolah->logo_sekolah) }}" alt="Logo SMK" onerror="this.onerror=null; this.src='/img/logo.png';" />
        @else
          <img src="/img/logo.png" alt="Logo SMK" />
        @endif
      </div>
    </div>
    <div class="kop-border"></div>

    {{-- JUDUL LAPORAN --}}
    <div class="judul-laporan">
      <h2>DAFTAR PENDIDIK &amp; TENAGA KEPENDIDIKAN</h2>
      <div class="sub-judul">Tahun Pelajaran 2026/2027 · SMKN 1 Air Naningan</div>
    </div>

    {{-- TABEL GURU --}}
    <table class="table-data">
      <thead>
        <tr>
          <th style="width:4%;">No</th>
          <th style="width:18%;">NIP</th>
          <th>Nama Lengkap Guru / Staf</th>
          <th style="width:20%;">Jabatan / Peran</th>
          <th style="width:16%;">Kontak WhatsApp</th>
          <th style="width:12%;">Akun Login</th>
          <th style="width:8%;">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($gurus as $idx => $g)
          <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center" style="font-family:'JetBrains Mono', monospace; font-weight:600;">{{ $g->nip ?? '-' }}</td>
            <td><strong>{{ $g->nama }}</strong></td>
            <td>{{ $g->jabatan }}</td>
            <td class="text-center" style="font-family:'JetBrains Mono', monospace;">{{ $g->no_hp ?: '-' }}</td>
            <td style="font-family:'JetBrains Mono', monospace; font-size:8.5pt;">{{ $g->user ? $g->user->email : '-' }}</td>
            <td class="text-center">{{ strtoupper($g->status) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center" style="padding:16px; color:#666;">Tidak ada data guru / pegawai terdaftar.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div style="font-size:8.5pt; color:#444; margin-bottom:10px;">
      <em>* Total Terdaftar: {{ $gurus->count() }} Guru &amp; Pegawai. Dicetak otomatis dari SIRANI (Sistem Informasi Responsif Absensi &amp; Penegakan Disiplin) SMKN 1 Air Naningan pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.</em>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="ttd-container">
      <table class="ttd-table">
        <tr>
          <td>
            Mengetahui,<br />
            Kepala SMK Negeri 1 Air Naningan
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ $sekolah->nama_kepala_sekolah ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</div>
            <div>NIP. {{ $sekolah->nip_kepala_sekolah ?? '19750510 200003 1 005' }}</div>
          </td>
          <td>
            Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br />
            Kepala Tata Usaha / Kepegawaian
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ auth()->user()->name ?? 'Staf Administrasi Kepegawaian' }}</div>
            <div>NIP/NUPTK. -</div>
          </td>
        </tr>
      </table>
    </div>

  </div>

</body>
</html>
