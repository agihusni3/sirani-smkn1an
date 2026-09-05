<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surat Keterangan Bebas Masalah — {{ $siswa->nama }} ({{ $siswa->nisn ?: $siswa->id }})</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Tinos:ital,wght@0,400;0,700;1,400&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    body {
      background-color: #525659;
      font-family: 'Tinos', 'Times New Roman', Times, serif;
      color: #000000;
      line-height: 1.35;
      font-size: 11pt;
      padding: 20px 10px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* FLOATING TOOLBAR */
    .print-actions-bar {
      width: 210mm;
      max-width: 100%;
      margin-bottom: 14px;
      background: #1E293B;
      padding: 12px 18px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.35);
      font-family: 'Plus Jakarta Sans', sans-serif;
      z-index: 100;
    }
    .toolbar-top-row {
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
      background: #000000;
      color: #FFFFFF;
      font-weight: 800;
      border: 1px solid #000000;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 12px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
    .btn-print:hover {
      background: #262626;
      border-color: #262626;
      transform: translateY(-1px);
    }
    .btn-back {
      background: rgba(255,255,255,0.1);
      color: #F1F5F9;
      font-weight: 700;
      border: 1px solid rgba(255,255,255,0.2);
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 12px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .btn-back:hover {
      background: rgba(255,255,255,0.2);
    }

    /* A4 PAPER CANVAS */
    .a4-page {
      width: 210mm;
      min-height: 297mm;
      background: #FFFFFF;
      padding: 16mm 20mm 16mm 22mm;
      box-shadow: 0 0 25px rgba(0,0,0,0.5);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* KOP DINAS */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding-bottom: 8px;
      position: relative;
    }
    .kop-logo-left {
      width: 68px;
      height: 68px;
      object-fit: contain;
      flex-shrink: 0;
    }
    .kop-text-center {
      text-align: center;
      flex: 1;
    }
    .kop-instansi-atas {
      font-size: 12pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .kop-dinas {
      font-size: 11pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #000000;
      margin: 1px 0;
    }
    .kop-alamat {
      font-size: 8.5pt;
      line-height: 1.25;
      font-style: italic;
      color: #222222;
    }
    .kop-divider-double {
      border: 0;
      border-top: 3px double #000000;
      margin-top: 4px;
      margin-bottom: 12px;
    }

    /* SURAT CONTENT */
    .surat-title-block {
      text-align: center;
      margin-bottom: 12px;
    }
    .surat-main-title {
      font-size: 13pt;
      font-weight: 800;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .surat-nomor {
      font-size: 10.5pt;
      font-weight: 700;
      margin-top: 2px;
      font-family: 'JetBrains Mono', monospace;
    }

    .surat-paragraph {
      text-align: justify;
      margin-bottom: 8px;
      line-height: 1.35;
    }

    /* DATA BIODATA TABLE */
    .bio-table {
      width: 100%;
      margin: 6px 0 10px;
      border-collapse: collapse;
    }
    .bio-table td {
      padding: 2.5px 4px;
      vertical-align: top;
      font-size: 10.5pt;
    }
    .bio-label {
      width: 190px;
      font-weight: 600;
    }
    .bio-sep {
      width: 15px;
      text-align: center;
    }
    .bio-val {
      font-weight: 700;
    }

    /* REKAP TABLE */
    .rekap-box {
      border: 1.5px solid #000000;
      border-radius: 4px;
      padding: 8px 10px;
      margin: 8px 0 12px;
      background: #FAFAFA;
    }
    .rekap-box-title {
      font-size: 10pt;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .grid-stats {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 6px;
      text-align: center;
    }
    .stat-item {
      background: #FFFFFF;
      border: 1px solid #CCCCCC;
      padding: 4px 2px;
      border-radius: 4px;
    }
    .stat-item-val {
      font-size: 13pt;
      font-weight: 800;
      font-family: 'JetBrains Mono', monospace;
    }
    .stat-item-lbl {
      font-size: 8pt;
      font-weight: 700;
      text-transform: uppercase;
      color: #333333;
    }

    /* STATUS PERNYATAAN BEBAS */
    .statement-box {
      border: 2px solid #16A34A;
      background: #F0FDF4;
      padding: 8px 12px;
      border-radius: 6px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .statement-box.has-kasus {
      border-color: #DC2626;
      background: #FEF2F2;
    }
    .statement-text {
      font-size: 10pt;
      font-weight: 700;
      line-height: 1.3;
    }

    /* SIGNATURE 4 BLOCKS */
    .sig-section {
      margin-top: 14px;
    }
    .sig-date-row {
      text-align: right;
      font-size: 10.5pt;
      margin-bottom: 8px;
    }
    .sig-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px 20px;
      text-align: center;
      font-size: 10pt;
    }
    .sig-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      min-height: 80px;
    }
    .sig-name {
      font-weight: 800;
      text-decoration: underline;
      margin-top: 50px;
    }
    .sig-nip {
      font-size: 9pt;
      font-family: 'JetBrains Mono', monospace;
    }

    /* FOOTER */
    .a4-footer {
      font-size: 8pt;
      color: #666666;
      border-top: 1px dashed #CCCCCC;
      padding-top: 4px;
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .print-sheet-wrapper {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      display: flex;
      justify-content: center;
      padding-bottom: 30px;
    }

    @media (max-width: 820px) {
      body {
        padding: 10px 0 30px;
        align-items: stretch;
        overflow-x: auto;
      }
      .print-actions-bar {
        width: calc(100% - 16px);
        margin: 0 8px 14px;
        position: sticky;
        top: 6px;
        z-index: 1000;
        padding: 10px 14px;
      }
      .print-title-info {
        font-size: 12px;
      }
      .btn-action-group {
        width: 100%;
        display: flex;
        gap: 8px;
      }
      .btn-back {
        flex: 1;
        justify-content: center;
        padding: 8px 10px;
        font-size: 12px;
      }
      .btn-print {
        flex: 1.5;
        justify-content: center;
        padding: 8px 12px;
        font-size: 12px;
      }
      .print-sheet-wrapper {
        justify-content: flex-start;
        padding: 0 8px 30px;
      }
    }

    /* PRINT STYLES */
    @media print {
      body {
        background: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .no-print {
        display: none !important;
      }
      .a4-page {
        box-shadow: none !important;
        margin: 0 !important;
        width: 100% !important;
        min-height: 100% !important;
        padding: 12mm 18mm 12mm 18mm !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;
      }
    }
  </style>
</head>
<body>

  {{-- FLOATING ACTIONS TOOLBAR (BROWSER ONLY) --}}
  <div class="print-actions-bar no-print">
    <div class="toolbar-top-row">
      <div class="print-title-info">
        <i class="bi bi-file-earmark-check-fill" style="color:#FACC15; font-size:18px;"></i>
        <span>Surat Bebas Masalah &amp; Resume Presensi 3 Tahun</span>
        <span class="badge-a4">Format Resmi A4</span>
      </div>
      <div class="btn-action-group">
        <a href="/siswa?tab=alumni" class="btn-back">
          <i class="bi bi-arrow-left"></i> Kembali ke Data Siswa
        </a>
        <button type="button" onclick="window.print()" class="btn-print">
          <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
        </button>
      </div>
    </div>
  </div>

  {{-- WRAPPER LEMBAR CETAK RESMI --}}
  <div class="print-sheet-wrapper">
    <div class="a4-page">
    <div>
      {{-- 1. KOP SURAT DINAS RESMI --}}
      <div class="kop-container">
        <img src="{{ $sekolah->logo_url ?: '/img/logo.png' }}" alt="Logo Sekolah" class="kop-logo-left" />
        <div class="kop-text-center">
          <div class="kop-instansi-atas">{{ $sekolah->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
          <div class="kop-dinas">{{ $sekolah->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
          <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}</div>
          <div class="kop-alamat">
            {{ $sekolah->alamat_lengkap ?: 'Jl. Raya Air Naningan No. 01, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}<br />
            NPSN: {{ $sekolah->npsn ?: '69900000' }} | Email: {{ $sekolah->email ?: 'smkn1airnaningan@gmail.sch.id' }} | Web: {{ $sekolah->website ?: 'smkn1airnaningan.sch.id' }}
          </div>
        </div>
      </div>
      <hr class="kop-divider-double" />

      {{-- 2. JUDUL SURAT RESMI --}}
      <div class="surat-title-block">
        <div class="surat-main-title">SURAT KETERANGAN BEBAS KASUS DISIPLIN &amp; RESUME PRESENSI</div>
        <div class="surat-nomor">Nomor: {{ $nomorSurat }}</div>
      </div>

      {{-- 3. PEMBUKA SURAT --}}
      <p class="surat-paragraph">
        Yang bertanda tangan di bawah ini, Tim Bimbingan Konseling dan Kesiswaan <strong>{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</strong>, menerangkan dengan sebenarnya bahwa peserta didik:
      </p>

      {{-- 4. BIODATA PESERTA DIDIK --}}
      <table class="bio-table">
        <tr>
          <td class="bio-label">Nama Lengkap Siswa</td>
          <td class="bio-sep">:</td>
          <td class="bio-val" style="font-size:11.5pt; text-transform:uppercase;">{{ $siswa->nama }}</td>
        </tr>
        <tr>
          <td class="bio-label">Nomor Induk Siswa Nasional (NISN)</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $siswa->nisn ?: '-' }}</td>
        </tr>
        <tr>
          <td class="bio-label">Program Keahlian / Rombel Terakhir</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $rombelAktif->nama_rombel ?? 'Alumni SMKN 1 Air Naningan' }}</td>
        </tr>
        <tr>
          <td class="bio-label">Status Kelulusan / Keberadaan</td>
          <td class="bio-sep">:</td>
          <td class="bio-val" style="color:{{ $siswa->status === 'lulus' ? '#16A34A' : '#CA8A04' }};">
            {{ strtoupper($siswa->status === 'lulus' ? 'LULUS / ALUMNI' : $siswa->status) }}
          </td>
        </tr>
      </table>

      {{-- 5. RESUME PRESENSI KUMULATIF --}}
      <div class="rekap-box">
        <div class="rekap-box-title">
          <i class="bi bi-bar-chart-fill"></i> I. Rekapitulasi Riwayat Kehadiran Kumulatif di Sekolah
        </div>
        <div class="grid-stats">
          <div class="stat-item">
            <div class="stat-item-val" style="color:#16A34A;">{{ $stats['hadir'] }}</div>
            <div class="stat-item-lbl">Hadir Tepat</div>
          </div>
          <div class="stat-item">
            <div class="stat-item-val" style="color:#CA8A04;">{{ $stats['terlambat'] }}</div>
            <div class="stat-item-lbl">Terlambat</div>
          </div>
          <div class="stat-item">
            <div class="stat-item-val" style="color:#2563EB;">{{ $stats['sakit'] }}</div>
            <div class="stat-item-lbl">Sakit (S)</div>
          </div>
          <div class="stat-item">
            <div class="stat-item-val" style="color:#0891B2;">{{ $stats['izin'] }}</div>
            <div class="stat-item-lbl">Izin (I)</div>
          </div>
          <div class="stat-item">
            <div class="stat-item-val" style="color:#DC2626;">{{ $stats['alpha'] }}</div>
            <div class="stat-item-lbl">Alpha (A)</div>
          </div>
          <div class="stat-item" style="background:#FEF9C3; border-color:#CA8A04;">
            <div class="stat-item-val" style="color:#854D0E;">{{ $stats['persen'] }}%</div>
            <div class="stat-item-lbl">Persentase</div>
          </div>
        </div>
      </div>

      {{-- 6. STATUS PERNYATAAN BEBAS KASUS --}}
      <div style="margin-bottom:6px; font-weight:800; font-size:10pt; text-transform:uppercase;">
        II. Catatan Rekam Jejak Penegakan Disiplin &amp; Pembinaan Karakter
      </div>
      @if($isBebasMasalah)
        <div class="statement-box">
          <div style="font-size:24px; color:#16A34A;"><i class="bi bi-patch-check-fill"></i></div>
          <div class="statement-text" style="color:#14532D;">
            BERSIH &amp; BEBAS TANGGUNGAN KASUS DISIPLIN.<br />
            <span style="font-weight:400; font-size:9.5pt; color:#166534;">
              Siswa yang bersangkutan selama menempuh pendidikan di SMK Negeri 1 Air Naningan menunjukkan sikap, integritas, dan perilaku yang BAIK, serta TIDAK MEMILIKI TANGGUNGAN SANKSI PEMBINAAN KESISWAAN.
            </span>
          </div>
        </div>
      @else
        <div class="statement-box has-kasus">
          <div style="font-size:24px; color:#DC2626;"><i class="bi bi-exclamation-octagon-fill"></i></div>
          <div class="statement-text" style="color:#7F1D1D;">
            MASIH MEMILIKI {{ $kasusAktif }} TANGGUNGAN KASUS PEMBINAAN AKTIF.<br />
            <span style="font-weight:400; font-size:9.5pt; color:#991B1B;">
              Siswa wajib menyelesaikan proses pembinaan dengan Guru BK / Wali Kelas sebelum surat bebas tanggungan disahkan.
            </span>
          </div>
        </div>
      @endif

      <p class="surat-paragraph" style="margin-top:6px;">
        Demikian Surat Keterangan Bebas Masalah &amp; Resume Presensi ini diterbitkan dengan sebenarnya untuk dipergunakan sebagaimana mestinya sebagai syarat pengambilan ijazah, dokumen kelulusan, ataupun lampiran kelengkapan melamar pekerjaan di Dunia Usaha / Dunia Industri (DU/DI).
      </p>
    </div>

    {{-- 7. PENGESAHAN 4 PIHAK RESMI --}}
    <div class="sig-section">
      <div class="sig-date-row">
        Air Naningan, {{ $tanggalSurat }}
      </div>

      <div class="sig-grid">
        {{-- Kolom 1: Wali Kelas --}}
        <div class="sig-box">
          <div>Wali Kelas,</div>
          <div>
            <div class="sig-name">{{ $waliKelas->nama ?? '................................................' }}</div>
            <div class="sig-nip">NIP. {{ $waliKelas->nip ?? '................................' }}</div>
          </div>
        </div>

        {{-- Kolom 2: Guru BK / Konselor --}}
        <div class="sig-box">
          <div>Guru Bimbingan Konseling (BK),</div>
          <div>
            <div class="sig-name">................................................</div>
            <div class="sig-nip">NIP. ................................</div>
          </div>
        </div>

        {{-- Kolom 3: Waka Kesiswaan --}}
        <div class="sig-box" style="margin-top:10px;">
          <div>Waka Bidang Kesiswaan,</div>
          <div>
            <div class="sig-name">................................................</div>
            <div class="sig-nip">NIP. ................................</div>
          </div>
        </div>

        {{-- Kolom 4: Mengetahui Kepala Sekolah --}}
        <div class="sig-box" style="margin-top:10px;">
          <div>Mengetahui,<br />Kepala SMKN 1 Air Naningan</div>
          <div>
            <div class="sig-name">{{ $sekolah->nama_kepala_sekolah ?: 'Dra. H. Maryono, M.Pd.' }}</div>
            <div class="sig-nip">NIP. {{ $sekolah->nip_kepala_sekolah ?: '19700101 199501 1 001' }}</div>
          </div>
        </div>
      </div>

      {{-- FOOTER WATERMARK / VALIDASI --}}
      <div class="a4-footer">
        <div>Dokumen Resmi Sistem Informasi Responsif Absensi &amp; Disiplin (SIRANI)</div>
        <div>Dicetak Otomatis pada {{ date('d/m/Y H:i') }} WIB | ID: {{ $nomorSurat }}</div>
      </div>
    </div>
  </div>
  </div>

</body>
</html>
