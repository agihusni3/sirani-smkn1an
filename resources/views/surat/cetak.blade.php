<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $judulSurat }} — {{ $siswa->nama }} ({{ $rombel->nama_rombel ?? 'SMKN 1 Air Naningan' }})</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Tinos:ital,wght@0,400;0,700;1,400&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  <style>
    /* CSS RESET & A4 FORM-FACTOR */
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
      line-height: 1.4;
      font-size: 11.5pt;
      padding: 20px 10px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* FLOATING ACTION TOOLBAR (Hanya Tampil di Browser) */
    .print-actions-bar {
      width: 210mm;
      max-width: 100%;
      margin-bottom: 14px;
      background: #1E293B;
      padding: 14px 18px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.35);
      font-family: 'Plus Jakarta Sans', sans-serif;
      z-index: 100;
    }
    .toolbar-top-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
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
      letter-spacing: 0.5px;
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
      text-decoration: none;
      box-shadow: 0 2px 8px rgba(234, 179, 8, 0.4);
      transition: transform .15s;
    }
    .btn-print:hover {
      transform: translateY(-1px);
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
    .btn-back:hover {
      background: rgba(255,255,255,0.2);
    }

    /* INPUTAN EDIT CEPAT */
    .toolbar-inputs-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 10px;
    }
    .input-field-wrap {
      display: flex;
      flex-direction: column;
      gap: 3px;
    }
    .input-field-wrap label {
      font-size: 11px;
      font-weight: 700;
      color: #94A3B8;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .input-field-wrap input, .input-field-wrap select {
      background: #0F172A;
      border: 1px solid #334155;
      border-radius: 6px;
      padding: 6px 10px;
      color: #FFFFFF;
      font-size: 12px;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 600;
      outline: none;
    }
    .input-field-wrap input:focus, .input-field-wrap select:focus {
      border-color: #FACC15;
    }

    .toggle-lampiran-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.15);
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      grid-column: 1 / -1;
      margin-top: 4px;
    }
    .toggle-lampiran-wrap input {
      accent-color: #EAB308;
      width: 16px;
      height: 16px;
      cursor: pointer;
    }
    .toggle-lampiran-wrap span {
      color: #F8FAFC;
      font-size: 12.5px;
      font-weight: 700;
    }

    /* PHYSICAL A4 SHEET (210mm x 297mm STRICT) */
    .page-sheet {
      width: 210mm;
      min-height: 297mm;
      max-height: 297mm;
      background: #FFFFFF;
      padding: 14mm 20mm 14mm 22mm;
      box-shadow: 0 5px 25px rgba(0,0,0,0.3);
      position: relative;
      overflow: hidden;
      margin-bottom: 24px;
      page-break-after: always;
      page-break-inside: avoid;
    }
    .page-sheet:last-child {
      margin-bottom: 0;
    }

    /* KOP SURAT DINAS 2 LOGO (Kiri: Prov Lampung, Kanan: SMK) */
    .kop-wrapper {
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
      letter-spacing: 0.5px;
      text-transform: uppercase;
      line-height: 1.15;
    }
    .kop-dinas {
      font-size: 12pt;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      line-height: 1.2;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 700;
      letter-spacing: 0.8px;
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
    
    /* GARIS GANDA KOP SURAT RESMI */
    .kop-border {
      border-top: 2.5px solid #000000;
      border-bottom: 0.8px solid #000000;
      height: 3.5px;
      margin: 5px 0 14px;
    }

    /* TABEL METADATA SURAT */
    .meta-surat-table {
      width: 100%;
      margin-bottom: 12px;
      font-size: 11pt;
      border-collapse: collapse;
    }
    .meta-surat-table td {
      vertical-align: top;
      padding: 1px 0;
    }

    /* EDITABLE INLINE SPAN */
    .editable-field {
      border-bottom: 1px dashed transparent;
      transition: border-color .2s, background-color .2s;
    }
    .editable-field:hover, .editable-field:focus {
      border-bottom-color: #CA8A04;
      background-color: rgba(202, 138, 4, 0.08);
      outline: none;
    }

    .surat-tujuan-box {
      margin: 10px 0 12px;
      font-size: 11pt;
      line-height: 1.35;
    }

    /* BODY SURAT */
    .surat-body {
      font-size: 11pt;
      text-align: justify;
      line-height: 1.45;
    }
    .surat-body p {
      margin-bottom: 8px;
      text-indent: 28px;
    }
    .surat-body p.no-indent {
      text-indent: 0;
    }

    /* TABEL DETAIL SISWA */
    .student-data-table {
      margin: 6px 0 10px 24px;
      font-size: 11pt;
      border-collapse: collapse;
    }
    .student-data-table td {
      padding: 2px 4px;
      vertical-align: top;
    }

    /* TABEL AGENDA PERTEMUAN */
    .agenda-data-table {
      margin: 6px 0 10px 24px;
      font-size: 11pt;
      border-collapse: collapse;
    }
    .agenda-data-table td {
      padding: 2.5px 4px;
      vertical-align: top;
    }

    /* BAGIAN TANDA TANGAN (2 KOLOM RESMI) */
    .ttd-section {
      margin-top: 20px;
      width: 100%;
      font-size: 11pt;
      page-break-inside: avoid;
    }
    .ttd-table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
    }
    .ttd-table td {
      vertical-align: top;
      width: 50%;
      padding: 2px;
    }
    .ttd-space {
      height: 56px;
    }
    .ttd-name {
      font-weight: 700;
      text-decoration: underline;
    }
    .ttd-nip {
      font-size: 10pt;
      margin-top: 1px;
    }

    /* =======================================================
       STYLE HALAMAN 2: LAMPIRAN REKAP ABSENSI RESMI
       ======================================================= */
    .lampiran-header {
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 2px solid #000000;
    }
    .lampiran-meta-table {
      width: 100%;
      font-size: 10pt;
      margin-bottom: 4px;
    }
    .lampiran-meta-table td {
      vertical-align: top;
      padding: 1px 0;
    }
    .lampiran-title {
      font-size: 12.5pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      text-align: center;
      margin: 10px 0 8px;
      text-decoration: underline;
    }

    .lampiran-stats-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 6px;
      margin-bottom: 12px;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .lampiran-stat-box {
      border: 1px solid #000000;
      padding: 6px 4px;
      text-align: center;
      background: #FAFAFA;
    }
    .lampiran-stat-num {
      font-size: 13pt;
      font-weight: 800;
      line-height: 1;
      font-family: 'JetBrains Mono', monospace;
    }
    .lampiran-stat-label {
      font-size: 8pt;
      font-weight: 700;
      text-transform: uppercase;
      margin-top: 2px;
    }

    .log-rekap-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
      margin-top: 6px;
    }
    .log-rekap-table th {
      border: 1px solid #000000;
      padding: 4px 6px;
      background: #E2E8F0;
      font-weight: 700;
      text-align: center;
      font-size: 9pt;
      text-transform: uppercase;
    }
    .log-rekap-table td {
      border: 1px solid #000000;
      padding: 3px 6px;
      vertical-align: middle;
    }
    .log-rekap-table tr:nth-child(even) td {
      background: #F8FAFC;
    }

    /* PRINT RULES KHUSUS KERTAS A4 */
    @page {
      size: A4 portrait;
      margin: 0;
    }

    @media print {
      html, body {
        background: #FFFFFF !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 210mm !important;
      }
      .no-print {
        display: none !important;
      }
      .editable-field {
        border-bottom: none !important;
        background: transparent !important;
      }
      .page-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        width: 210mm !important;
        height: 297mm !important;
        min-height: 297mm !important;
        max-height: 297mm !important;
        padding: 14mm 20mm 14mm 22mm !important;
      }
      .page-sheet.hide-sheet {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  {{-- FLOATING ACTION TOOLBAR (Hanya Tampil di Browser) --}}
  <div class="print-actions-bar no-print">
    <div class="toolbar-top-row">
      <div class="print-title-info">
        <i class="bi bi-file-earmark-pdf-fill" style="color:#FACC15; font-size:18px;"></i>
        <span>Pratinjau Surat Resmi &amp; Lampiran Rekap Absensi</span>
        <span class="badge-a4">Format A4</span>
      </div>
      <div class="btn-action-group">
        <a href="{{ route('dashboard') }}" class="btn-back" style="font-weight:700;">
          <i class="bi bi-grid-1x2-fill"></i> Kembali ke Dasbor
        </a>
        <button type="button" onclick="window.print()" class="btn-print">
          <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF (A4)
        </button>
      </div>
    </div>

    {{-- INPUTAN EDIT CEPAT NOMOR SURAT & JADWAL --}}
    <div class="toolbar-inputs-row">
      <div class="input-field-wrap">
        <label for="inputKategoriSurat"><i class="bi bi-file-earmark-text-fill"></i> Jenis Dokumen:</label>
        <select id="inputKategoriSurat" onchange="switchJenisDokumen(this.value)">
          <option value="panggilan_ortu" {{ $kategori === 'panggilan_ortu' ? 'selected' : '' }}>✉️ Surat Panggilan Orang Tua</option>
          <option value="pembinaan" {{ $kategori === 'pembinaan' ? 'selected' : '' }}>⚠️ Surat Peringatan Kedisiplinan</option>
          <option value="berita_acara" {{ $kategori === 'berita_acara' ? 'selected' : '' }}>📜 Berita Acara Tindak Lanjut Dinamis</option>
        </select>
      </div>

      <div class="input-field-wrap">
        <label for="inputNoSurat"><i class="bi bi-hash"></i> Edit Nomor Dokumen:</label>
        <input type="text" id="inputNoSurat" value="{{ $nomorSurat }}" placeholder="Nomor Dokumen..." oninput="syncFromInput('noSurat', this.value)" />
      </div>

      <div class="input-field-wrap">
        <label for="inputHariTgl"><i class="bi bi-calendar-event"></i> Hari / Tanggal:</label>
        <input type="text" id="inputHariTgl" value="{{ $hariTanggal }}" placeholder="Hari, Tanggal..." oninput="syncFromInput('hariTgl', this.value)" />
      </div>

      <div class="input-field-wrap">
        <label for="inputTempat"><i class="bi bi-geo-alt"></i> Tempat / Ruang:</label>
        <input type="text" id="inputTempat" value="{{ $tempat }}" placeholder="Ruang BK / Wali Kelas..." oninput="syncFromInput('tempat', this.value)" />
      </div>

      {{-- TOGGLE LAMPIRAN HALAMAN 2 --}}
      <label class="toggle-lampiran-wrap">
        <input type="checkbox" id="toggleLampiranCheck" checked onchange="toggleLampiranSheet(this.checked)" />
        <span><i class="bi bi-file-earmark-spreadsheet-fill" style="color:#FACC15; margin-right:4px;"></i> Sertakan Lembar Lampiran Rekap Absensi Siswa (Halaman 2)</span>
      </label>
    </div>
  </div>

  {{-- ========================================================
       HALAMAN 1: LEMBAR SURAT RESMI / BERITA ACARA DINAMIS
       ======================================================== --}}
  <div class="page-sheet" id="sheetSurat">

    {{-- KOP SURAT RESMI DINAS (KIRI: LOGO PROVINSI LAMPUNG, KANAN: LOGO SMK) --}}
    <div class="kop-wrapper">
      {{-- LOGO KIRI: PROVINSI LAMPUNG --}}
      <div class="kop-logo-left">
        <img src="/img/logo_prov_lampung.png" alt="Logo Provinsi Lampung" onerror="this.onerror=null; this.src='/img/logo_prov_lampung.svg'" />
      </div>

      {{-- TEKS KOP DINAS --}}
      <div class="kop-text">
        <div class="kop-instansi">{{ $sekolah->nama_instansi_atas ?? 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
        <div class="kop-dinas">{{ $sekolah->nama_dinas ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
        <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div class="kop-alamat">
          {{ $sekolah->alamat_lengkap ?? $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}<br />
          Email: {{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }} · Website: {{ $sekolah->website ?? 'smkn1airnaningan.sch.id' }}
        </div>
      </div>

      {{-- LOGO KANAN: SMKN 1 AIR NANINGAN --}}
      <div class="kop-logo-right">
        @if(!empty($sekolah->logo_sekolah))
          <img src="{{ asset('storage/'.$sekolah->logo_sekolah) }}" alt="Logo SMKN 1 Air Naningan" onerror="this.onerror=null; this.src='/img/logo.png';" />
        @else
          <img src="/img/logo.png" alt="Logo SMKN 1 Air Naningan" />
        @endif
      </div>
    </div>
    <div class="kop-border"></div>

    @if($kategori === 'berita_acara')
      {{-- ======================================================
           LAYOUT KHUSUS: BERITA ACARA TINDAK LANJUT DINAMIS
           ====================================================== --}}
      <div style="text-align:center; margin-bottom:12px;">
        <h3 style="font-size:12.5pt; font-weight:800; text-decoration:underline; text-transform:uppercase; margin-bottom:2px;">
          BERITA ACARA TINDAK LANJUT &amp; KOMITMEN PEMBINAAN KESISWAAN
        </h3>
        <div style="font-size:10.5pt; font-family:'JetBrains Mono', monospace; font-weight:700;">
          Nomor: <span id="displayNoSurat" class="editable-field" contenteditable="true" oninput="syncFromElement('inputNoSurat', this.innerText)" title="Klik untuk edit">{{ $nomorSurat }}</span>
        </div>
      </div>

      <div class="surat-body" style="font-size:10.5pt; line-height:1.4;">
        <p class="no-indent">
          Pada hari ini, <strong><span id="displayHariTgl" class="editable-field" contenteditable="true" oninput="syncFromElement('inputHariTgl', this.innerText)">{{ $hariTanggal }}</span></strong>, bertempat di <strong><span id="displayTempat" class="editable-field" contenteditable="true" oninput="syncFromElement('inputTempat', this.innerText)">{{ $tempat }}</span></strong>, telah diselenggarakan pertemuan koordinasi, penanganan masalah, dan tindak lanjut pembinaan kedisiplinan belajar siswa antara pihak sekolah dengan pihak orang tua/wali murid:
        </p>

        <table class="student-data-table" style="width:100%; margin:6px 0; font-size:10pt;">
          <tr>
            <td colspan="3" style="background:#F1F5F9; font-weight:800; padding:2.5px 6px; font-size:9.5pt; text-transform:uppercase; border-left:3px solid #CA8A04;">I. IDENTITAS SISWA YANG DIBINA</td>
          </tr>
          <tr>
            <td style="width:175px;">Nama Lengkap Siswa</td>
            <td style="width:8px;">:</td>
            <td><strong>{{ $siswa->nama }}</strong></td>
          </tr>
          <tr>
            <td>Nomor Induk Siswa (NIS)</td>
            <td>:</td>
            <td>{{ $siswa->nis }} / NISN: {{ $siswa->nisn ?: '-' }}</td>
          </tr>
          <tr>
            <td>Kelas / Keahlian</td>
            <td>:</td>
            <td><strong>{{ $rombel->nama_rombel ?? '-' }}</strong> · {{ $rombel->jurusan->nama_jurusan ?? ($siswa->jurusan->nama_jurusan ?? '-') }}</td>
          </tr>

          <tr>
            <td colspan="3" style="background:#F1F5F9; font-weight:800; padding:2.5px 6px; font-size:9.5pt; text-transform:uppercase; border-left:3px solid #CA8A04;">II. ORANG TUA / WALI YANG HADIR</td>
          </tr>
          <tr>
            <td>Nama Orang Tua / Wali</td>
            <td>:</td>
            <td><strong><span id="displayNamaWaliHadir" class="editable-field" contenteditable="true">{{ $namaWaliHadir }}</span></strong></td>
          </tr>
          <tr>
            <td>Kontak / WhatsApp</td>
            <td>:</td>
            <td>{{ $siswa->no_hp_ortu ?: '-' }}</td>
          </tr>

          <tr>
            <td colspan="3" style="background:#F1F5F9; font-weight:800; padding:2.5px 6px; font-size:9.5pt; text-transform:uppercase; border-left:3px solid #CA8A04;">III. PIHAK PEMBINA SEKOLAH</td>
          </tr>
          <tr>
            <td>Wali Kelas / Guru BK</td>
            <td>:</td>
            <td><strong>{{ $waliKelas ? $waliKelas->nama : 'Wali Kelas ' . ($rombel->nama_rombel ?? '') }}</strong></td>
          </tr>
          <tr>
            <td>NIP Guru Pembina</td>
            <td>:</td>
            <td>{{ $waliKelas && $waliKelas->nip ? $waliKelas->nip : '-' }}</td>
          </tr>
        </table>

        <p class="no-indent" style="font-weight:800; margin-top:8px; margin-bottom:3px; font-size:10pt;">
          IV. POIN HASIL MUSYAWARAH, TINDAK LANJUT &amp; KESEPAKATAN KOMITMEN:
        </p>
        <div style="border:1px solid #000000; padding:8px 10px; background:#FAFAFA; font-size:10pt; line-height:1.45; margin-bottom:8px;">
          <div id="displayCatatanHasil" class="editable-field" contenteditable="true" style="white-space:pre-wrap;" title="Klik untuk mengedit catatan hasil bimbingan">{{ $catatanHasil }}</div>
        </div>

        <p class="no-indent" style="font-size:10pt;">
          Demikian Berita Acara ini dibuat dengan sebenarnya dan ditandatangani bersama dengan penuh kesadaran dan tanggung jawab demi perbaikan kedisiplinan dan masa depan belajar siswa di SMKN 1 Air Naningan.
        </p>
      </div>

      {{-- 4 TANDA TANGAN (KOLOM SEIMBANG) --}}
      <div class="ttd-section" style="margin-top:12px;">
        <table class="ttd-table" style="font-size:10pt;">
          <tr>
            <td style="width:50%; padding-bottom:14px;">
              Siswa yang Bersangkutan,
              <div class="ttd-space" style="height:44px;"></div>
              <div class="ttd-name">{{ $siswa->nama }}</div>
              <div class="ttd-nip">NIS. {{ $siswa->nis }}</div>
            </td>
            <td style="width:50%; padding-bottom:14px;">
              Orang Tua / Wali Siswa,
              <div class="ttd-space" style="height:44px;"></div>
              <div class="ttd-name">{{ $namaWaliHadir }}</div>
              <div class="ttd-nip">(Tanda Tangan &amp; Nama Terang)</div>
            </td>
          </tr>
          <tr>
            <td style="width:50%;">
              Wali Kelas {{ $rombel->nama_rombel ?? '' }},
              <div class="ttd-space" style="height:44px;"></div>
              <div class="ttd-name">{{ $waliKelas ? $waliKelas->nama : '( ........................................ )' }}</div>
              <div class="ttd-nip">{{ $waliKelas && $waliKelas->nip ? 'NIP. '.$waliKelas->nip : 'NIP. -' }}</div>
            </td>
            <td style="width:50%;">
              Mengetahui,<br />
              <strong>Kepala Sekolah</strong>
              <div class="ttd-space" style="height:44px;"></div>
              <div class="ttd-name">{{ $sekolah->nama_kepala_sekolah ?: 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</div>
              <div class="ttd-nip">NIP. {{ $sekolah->nip_kepala_sekolah ?: '19750510 200003 1 005' }}</div>
            </td>
          </tr>
        </table>
      </div>

    @else
      {{-- ======================================================
           LAYOUT SURAT PANGGILAN / PERINGATAN RESMI
           ====================================================== --}}
      <table class="meta-surat-table">
        <tr>
          <td style="width:13%;">Nomor</td>
          <td style="width:2%;">:</td>
          <td style="width:47%;">
            <strong><span id="displayNoSurat" class="editable-field" contenteditable="true" oninput="syncFromElement('inputNoSurat', this.innerText)" title="Klik untuk mengedit">{{ $nomorSurat }}</span></strong>
          </td>
          <td style="width:38%; text-align:right;">
            {{ $sekolah->kecamatan }}, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
          </td>
        </tr>
        <tr>
          <td>Lampiran</td>
          <td>:</td>
          <td><strong id="displayTeksLampiran">1 (Satu) Berkas Rekap Absensi</strong></td>
          <td></td>
        </tr>
        <tr>
          <td>Perihal</td>
          <td>:</td>
          <td><u><strong>{{ $judulSurat }}</strong></u></td>
          <td></td>
        </tr>
      </table>

      <div class="surat-tujuan-box">
        Kepada Yth.<br />
        <strong>Bapak / Ibu Orang Tua / Wali dari ananda: {{ $siswa->nama }}</strong><br />
        di -<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tempat
      </div>

      {{-- ISI SURAT --}}
      <div class="surat-body">
        <p>
          Dengan hormat,<br />
          Sehubungan dengan catatan hasil evaluasi kedisiplinan dan monitoring presensi kehadiran belajar ananda di sekolah <em>(rincian rekapitulasi terlampir)</em>, maka melalui surat ini kami mengharap kehadiran Bapak/Ibu Orang Tua / Wali dari siswa:
        </p>

        <table class="student-data-table">
          <tr>
            <td style="width:150px;">Nama Siswa</td>
            <td style="width:10px;">:</td>
            <td><strong>{{ $siswa->nama }}</strong></td>
          </tr>
          <tr>
            <td>NIS / NISN</td>
            <td>:</td>
            <td>{{ $siswa->nis }} / {{ $siswa->nisn ?: '-' }}</td>
          </tr>
          <tr>
            <td>Kelas / Rombel</td>
            <td>:</td>
            <td><strong>{{ $rombel->nama_rombel ?? '-' }}</strong></td>
          </tr>
          <tr>
            <td>Kompetensi Keahlian</td>
            <td>:</td>
            <td>{{ $rombel->jurusan->nama_jurusan ?? ($siswa->jurusan->nama_jurusan ?? '-') }}</td>
          </tr>
          <tr>
            <td>Nama Wali Murid</td>
            <td>:</td>
            <td>{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua Siswa' }}</td>
          </tr>
        </table>

        <p class="no-indent">
          Untuk dapat hadir ke sekolah guna koordinasi dan bimbingan bersama pihak sekolah pada:
        </p>

        <table class="agenda-data-table">
          <tr>
            <td style="width:150px;">Hari / Tanggal</td>
            <td style="width:10px;">:</td>
            <td><strong><span id="displayHariTgl" class="editable-field" contenteditable="true" oninput="syncFromElement('inputHariTgl', this.innerText)">{{ $hariTanggal }}</span></strong></td>
          </tr>
          <tr>
            <td>Waktu</td>
            <td>:</td>
            <td><strong><span id="displayWaktu" class="editable-field" contenteditable="true" oninput="syncFromElement('inputWaktu', this.innerText)">{{ $waktu }}</span></strong></td>
          </tr>
          <tr>
            <td>Tempat</td>
            <td>:</td>
            <td><span id="displayTempat" class="editable-field" contenteditable="true" oninput="syncFromElement('inputTempat', this.innerText)">{{ $tempat }}</span></td>
          </tr>
          <tr>
            <td>Bertemu dengan</td>
            <td>:</td>
            <td>{{ $menghadap }}</td>
          </tr>
          <tr>
            <td>Keperluan / Agenda</td>
            <td>:</td>
            <td><strong>{{ $keperluan }}</strong></td>
          </tr>
        </table>

        <p>
          Mengingat pentingnya pertemuan ini demi kelancaran proses pendidikan dan masa depan putra/putri Bapak/Ibu, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktu yang telah ditentukan.
        </p>

        <p class="no-indent">
          Demikian surat undangan ini kami sampaikan. Atas perhatian, kerja sama, dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.
        </p>
      </div>

      {{-- TANDA TANGAN RESMI (2 KOLOM SEIMBANG) --}}
      <div class="ttd-section">
        <table class="ttd-table">
          <tr>
            <td>
              Mengetahui,<br />
              <strong>Kepala Sekolah</strong>
              <div class="ttd-space"></div>
              <div class="ttd-name">{{ $sekolah->nama_kepala_sekolah ?: 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</div>
              <div class="ttd-nip">NIP. {{ $sekolah->nip_kepala_sekolah ?: '19750510 200003 1 005' }}</div>
            </td>
            <td>
              {{ $sekolah->kecamatan }}, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}<br />
              <strong>Wali Kelas {{ $rombel->nama_rombel ?? '' }}</strong>
              <div class="ttd-space"></div>
              <div class="ttd-name">{{ $waliKelas ? $waliKelas->nama : '( ........................................ )' }}</div>
              <div class="ttd-nip">{{ $waliKelas && $waliKelas->nip ? 'NIP. '.$waliKelas->nip : 'NIP. -' }}</div>
            </td>
          </tr>
        </table>
      </div>
    @endif

  </div>

  {{-- ========================================================
       HALAMAN 2: LEMBAR LAMPIRAN REKAPITULASI ABSENSI SISWA
       ======================================================== --}}
  <div class="page-sheet" id="sheetLampiran">

    {{-- HEADER LAMPIRAN --}}
    <div class="lampiran-header">
      <table class="lampiran-meta-table">
        <tr>
          <td style="width:14%;">Lampiran Surat No</td>
          <td style="width:2%;">:</td>
          <td style="width:48%;"><strong id="lampiranNoSurat">{{ $nomorSurat }}</strong></td>
          <td style="width:36%; text-align:right;">
            Tanggal: {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
          </td>
        </tr>
        <tr>
          <td>Tentang</td>
          <td>:</td>
          <td colspan="2">Rekapitulasi Catatan Presensi Kehadiran Siswa</td>
        </tr>
      </table>
    </div>

    <h3 class="lampiran-title">REKAPITULASI CATATAN KEHADIRAN &amp; KEDISIPLINAN SISWA</h3>

    {{-- DATA SINGKAT SISWA --}}
    <table class="student-data-table" style="margin:4px 0 10px 0; font-size:10.5pt; width:100%;">
      <tr>
        <td style="width:140px;">Nama Siswa</td>
        <td style="width:10px;">:</td>
        <td style="width:40%;"><strong>{{ $siswa->nama }}</strong></td>
        <td style="width:110px;">Kelas / Rombel</td>
        <td style="width:10px;">:</td>
        <td><strong>{{ $rombel->nama_rombel ?? '-' }}</strong></td>
      </tr>
      <tr>
        <td>NIS / NISN</td>
        <td>:</td>
        <td>{{ $siswa->nis }} / {{ $siswa->nisn ?: '-' }}</td>
        <td>Wali Kelas</td>
        <td>:</td>
        <td>{{ $waliKelas->nama ?? '-' }}</td>
      </tr>
      <tr>
        <td>Periode Evaluasi</td>
        <td>:</td>
        <td><strong>Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanSelected)->translatedFormat('F Y') }}</strong></td>
        <td>Wali Murid</td>
        <td>:</td>
        <td>{{ $siswa->nama_ortu ?: '-' }}</td>
      </tr>
    </table>

    {{-- 5 KOTAK STATISTIK BULANAN --}}
    <div class="lampiran-stats-grid">
      <div class="lampiran-stat-box">
        <div class="lampiran-stat-num">{{ $stats['hadir'] }}</div>
        <div class="lampiran-stat-label">Hadir Tepat Waktu</div>
      </div>
      <div class="lampiran-stat-box">
        <div class="lampiran-stat-num" style="color:#B45309;">{{ $stats['terlambat'] }}</div>
        <div class="lampiran-stat-label">Terlambat</div>
      </div>
      <div class="lampiran-stat-box">
        <div class="lampiran-stat-num" style="color:#1D4ED8;">{{ $stats['izin'] + $stats['sakit'] }}</div>
        <div class="lampiran-stat-label">Izin / Sakit</div>
      </div>
      <div class="lampiran-stat-box">
        <div class="lampiran-stat-num" style="color:#DC2626;">{{ $stats['alpha'] }}</div>
        <div class="lampiran-stat-label">Alpha / Bolos</div>
      </div>
      <div class="lampiran-stat-box" style="background:#FEF9C3;">
        <div class="lampiran-stat-num" style="color:#854D0E;">{{ $stats['persen'] }}%</div>
        <div class="lampiran-stat-label">Skor Disiplin</div>
      </div>
    </div>

    {{-- TABEL RINCIAN LOG ABSENSI HARIAN --}}
    <table class="log-rekap-table">
      <thead>
        <tr>
          <th style="width:30px;">No</th>
          <th style="width:130px;">Hari / Tanggal</th>
          <th style="width:80px;">Jam Masuk</th>
          <th style="width:80px;">Jam Pulang</th>
          <th style="width:90px;">Status</th>
          <th>Catatan / Keterangan Sistem</th>
        </tr>
      </thead>
      <tbody>
        @forelse($absensis->take(18) as $index => $abs)
          <tr>
            <td style="text-align:center; font-family:'JetBrains Mono', monospace;">{{ $index + 1 }}</td>
            <td><strong>{{ \Carbon\Carbon::parse($abs->tanggal)->translatedFormat('l, d M Y') }}</strong></td>
            <td style="text-align:center; font-family:'JetBrains Mono', monospace;">
              {{ $abs->jam_masuk ? substr($abs->jam_masuk, 0, 5).' WIB' : '—' }}
            </td>
            <td style="text-align:center; font-family:'JetBrains Mono', monospace;">
              {{ $abs->jam_pulang ? substr($abs->jam_pulang, 0, 5).' WIB' : '—' }}
            </td>
            <td style="text-align:center; font-weight:700; text-transform:uppercase; font-size:8.5pt;">
              @if($abs->status === 'hadir')
                Hadir
              @elseif($abs->status === 'terlambat')
                <span style="color:#B45309;">Terlambat</span>
              @elseif($abs->status === 'izin')
                <span style="color:#1D4ED8;">Izin</span>
              @elseif($abs->status === 'sakit')
                <span style="color:#1D4ED8;">Sakit</span>
              @elseif($abs->status === 'alpha')
                <span style="color:#DC2626; font-weight:800;">Alpha</span>
              @endif
            </td>
            <td style="font-size:8.5pt; color:#334155;">
              {{ $abs->catatan ?: ($abs->status === 'alpha' ? 'Tidak ada catatan hadir di Smart Gate Face ID' : 'Terekam Smart Gate Face ID') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center; padding:16px; color:#64748B;">
              Belum ada riwayat catatan absensi pada periode bulan ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- TANDA TANGAN LAMPIRAN --}}
    <div class="ttd-section" style="margin-top:16px;">
      <table class="ttd-table" style="font-size:10pt;">
        <tr>
          <td>
            Mengetahui,<br />
            <strong>Guru Bimbingan &amp; Konseling (BK)</strong>
            <div class="ttd-space" style="height:44px;"></div>
            <div class="ttd-name">( ................................................. )</div>
            <div class="ttd-nip">NIP. -</div>
          </td>
          <td>
            {{ $sekolah->kecamatan }}, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}<br />
            <strong>Wali Kelas {{ $rombel->nama_rombel ?? '' }}</strong>
            <div class="ttd-space" style="height:44px;"></div>
            <div class="ttd-name">{{ $waliKelas ? $waliKelas->nama : '( ................................................. )' }}</div>
            <div class="ttd-nip">{{ $waliKelas && $waliKelas->nip ? 'NIP. '.$waliKelas->nip : 'NIP. -' }}</div>
          </td>
        </tr>
      </table>
    </div>

  </div>

  {{-- JAVASCRIPT LOGIC --}}
  <script>
    function syncFromInput(type, val) {
      if (type === 'noSurat') {
        const el1 = document.getElementById('displayNoSurat');
        const el2 = document.getElementById('lampiranNoSurat');
        if (el1) el1.innerText = val || '-';
        if (el2) el2.innerText = val || '-';
      } else if (type === 'hariTgl') {
        const el = document.getElementById('displayHariTgl');
        if (el) el.innerText = val || '-';
      } else if (type === 'waktu') {
        const el = document.getElementById('displayWaktu');
        if (el) el.innerText = val || '-';
      } else if (type === 'tempat') {
        const el = document.getElementById('displayTempat');
        if (el) el.innerText = val || '-';
      }
    }

    function syncFromElement(inputId, val) {
      const input = document.getElementById(inputId);
      if (input) input.value = val.trim();
      const elLampiran = document.getElementById('lampiranNoSurat');
      if (elLampiran) elLampiran.innerText = val.trim();
    }

    function switchJenisDokumen(val) {
      const url = new URL(window.location.href);
      url.searchParams.set('kategori', val);
      window.location.href = url.toString();
    }

    function toggleLampiranSheet(isChecked) {
      const sheet = document.getElementById('sheetLampiran');
      const teksLampiran = document.getElementById('displayTeksLampiran');
      if (sheet) {
        if (isChecked) {
          sheet.style.display = 'block';
          sheet.classList.remove('hide-sheet');
          if (teksLampiran) teksLampiran.innerText = '1 (Satu) Berkas Rekap Absensi';
        } else {
          sheet.style.display = 'none';
          sheet.classList.add('hide-sheet');
          if (teksLampiran) teksLampiran.innerText = '-';
        }
      }
    }
  </script>

</body>
</html>
