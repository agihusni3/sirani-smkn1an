<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Rekapitulasi Presensi {{ ucfirst($kategori) }} - {{ $periodeText }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    @page {
      size: A4 portrait;
      margin: 10mm 12mm 12mm 12mm;
    }
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #334155;
      font-family: 'Times New Roman', Times, serif;
      color: #000000;
      font-size: 9.5pt;
      line-height: 1.3;
      -webkit-font-smoothing: antialiased;
      margin: 0;
      padding: 20px 0 40px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow-x: auto;
    }
    .print-actions-bar {
      width: 210mm;
      max-width: 95vw;
      background: #1E293B;
      border: 1px solid #334155;
      border-radius: 12px;
      padding: 12px 18px;
      margin-bottom: 16px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      box-sizing: border-box;
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
      font-size: 13px;
      padding: 8px 18px;
      border-radius: 8px;
      border: 1px solid #000000;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
    .btn-print:hover {
      background: #262626;
      border-color: #262626;
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
    .print-sheet-wrapper {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      display: flex;
      justify-content: center;
      padding-bottom: 30px;
    }

    /* KERTAS A4 PORTRAIT STANDAR CETAK */
    .a4-sheet {
      width: 210mm;
      min-width: 210mm;
      min-height: 297mm;
      background: #FFFFFF;
      color: #000000;
      padding: 12mm 14mm 14mm 14mm;
      box-shadow: 0 4px 25px rgba(0,0,0,0.35);
      position: relative;
      box-sizing: border-box;
      margin: 0 auto;
    }

    @media (max-width: 820px) {
      body {
        padding: 10px 0 30px;
        align-items: stretch;
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

    /* KOP SURAT DINAS */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      text-align: center;
      margin-bottom: 2px;
      width: 100%;
      color: #000000;
    }
    .kop-logo-left, .kop-logo-right {
      width: 64px;
      min-width: 64px;
      max-width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo-left img, .kop-logo-right img {
      max-width: 64px;
      max-height: 64px;
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
      color: #000000;
    }
    .kop-instansi {
      font-size: 10.5pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.15;
      color: #000000;
    }
    .kop-dinas {
      font-size: 11.5pt;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.2;
      color: #000000;
    }
    .kop-sekolah {
      font-size: 14pt;
      font-weight: 800;
      text-transform: uppercase;
      line-height: 1.25;
      margin-top: 1px;
      color: #000000;
    }
    .kop-alamat {
      font-size: 7.5pt;
      font-family: 'Times New Roman', Times, serif;
      color: #000000;
      line-height: 1.25;
      margin-top: 2px;
    }
    .kop-border {
      border-top: 2.5px solid #000000;
      border-bottom: 0.8px solid #000000;
      height: 3.5px;
      margin: 5px 0 12px;
    }

    /* JUDUL LAPORAN */
    .judul-laporan {
      text-align: center;
      margin-bottom: 10px;
      color: #000000;
    }
    .judul-laporan h2 {
      font-size: 12pt;
      font-weight: 800;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.3px;
      color: #000000;
    }
    .judul-laporan .sub-judul {
      font-size: 10pt;
      font-weight: 700;
      margin-top: 2px;
      color: #000000;
    }

    /* METADATA */
    .meta-table {
      width: 100%;
      font-size: 9pt;
      margin-bottom: 8px;
      color: #000000;
    }
    .meta-table td {
      padding: 2px 3px;
      vertical-align: top;
      color: #000000;
    }

    /* TABEL DATA */
    .table-data {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 8pt;
      margin-bottom: 14px;
      color: #000000;
    }
    .table-data th, .table-data td {
      border: 1px solid #000000;
      padding: 4px 4px;
      color: #000000;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }
    .table-data th {
      background-color: #F1F5F9;
      text-align: center;
      font-weight: 800;
      color: #000000;
    }
    .table-data td.text-center {
      text-align: center;
    }

    /* LEMBAR TANDA TANGAN */
    .ttd-container {
      width: 100%;
      margin-top: 16px;
      font-size: 9.5pt;
      page-break-inside: avoid;
      color: #000000;
    }
    .ttd-table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
      color: #000000;
    }
    .ttd-table td {
      width: 50%;
      vertical-align: top;
      padding: 4px;
      color: #000000;
    }
    .ttd-space {
      height: 50px;
    }
    .ttd-name {
      font-weight: 800;
      text-decoration: underline;
      color: #000000;
    }

    @media print {
      html, body {
        background: #FFFFFF !important;
        color: #000000 !important;
        padding: 0 !important;
        margin: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      * {
        color: #000000 !important;
        text-shadow: none !important;
      }
      .no-print, .print-actions-bar {
        display: none !important;
      }
      .a4-sheet {
        box-shadow: none !important;
        padding: 0 !important;
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
      }
      .table-data th {
        background-color: #F1F5F9 !important;
        color: #000000 !important;
        border: 1px solid #000000 !important;
      }
      .table-data td {
        color: #000000 !important;
        border: 1px solid #000000 !important;
      }
    }
  </style>
</head>
<body>

  {{-- FLOATING ACTION TOOLBAR --}}
  <div class="print-actions-bar no-print">
    <div class="print-title-info">
      <i class="bi bi-file-earmark-pdf-fill" style="color:#FACC15; font-size:18px;"></i>
      <span>Laporan Rekapitulasi Presensi {{ strtoupper($kategori) }}</span>
      <span class="badge-a4">Format A4 Portrait</span>
    </div>
    <div class="btn-action-group">
      <a href="{{ url('/laporan') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali ke Filter
      </a>
      <button type="button" onclick="window.print()" class="btn-print">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF (A4)
      </button>
    </div>
  </div>

  {{-- WRAPPER LEMBAR A4 RESMI --}}
  <div class="print-sheet-wrapper">
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
      <h2>LAPORAN REKAPITULASI PRESENSI &amp; KEDISIPLINAN {{ strtoupper($kategori) }}</h2>
      <div class="sub-judul">{{ $periodeText }}</div>
    </div>

    {{-- METADATA INFO --}}
    <table class="meta-table" style="width:100%; margin-bottom:10px;">
      <tr>
        <td style="width:14%;"><strong>Kategori</strong></td>
        <td style="width:1%;">:</td>
        <td style="width:43%;">{{ $kategori === 'siswa' ? 'Peserta Didik (Siswa)' : 'Tenaga Pendidik & Kependidikan (Guru/Staf)' }}</td>
        @if($kategori === 'siswa' && $rombel)
          <td style="width:15%;"><strong>Rombel / Kelas</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:26%;">{{ $rombel->nama_rombel }}</td>
        @else
          <td style="width:15%;"><strong>Tahun Pelajaran</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:26%;">2026/2027</td>
        @endif
      </tr>
      @if($kategori === 'siswa' && $rombel)
        <tr>
          <td style="width:14%;"><strong>Tahun Pelajaran</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:43%;">2026/2027</td>
          <td style="width:15%;"><strong>Wali Kelas</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:26%;">{{ $rombel->waliKelas->nama ?? '-' }}</td>
        </tr>
      @endif
    </table>

    {{-- KONTEN TABEL: REKAP AGREGAT ATAU LOG HARIAN --}}
    @if(in_array($periode, ['mingguan', 'bulanan', 'tahunan']))
      <table class="table-data">
        <thead>
          <tr>
            <th rowspan="2" style="width:4%;">No</th>
            <th rowspan="2" style="width:12%;">{{ $kategori === 'siswa' ? 'NISN' : 'NIP' }}</th>
            <th rowspan="2">Nama Lengkap</th>
            <th rowspan="2" style="width:13%; text-align:center;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
            <th colspan="6">Kehadiran &amp; Ketidakhadiran</th>
            <th rowspan="2" style="width:7%; text-align:center;">Total</th>
            <th rowspan="2" style="width:7%; text-align:center;">% Hadir</th>
          </tr>
          <tr>
            <th style="width:5%;">H</th>
            <th style="width:5%;">T</th>
            <th style="width:5%;">S</th>
            <th style="width:5%;">I</th>
            <th style="width:5%;">A</th>
            <th style="width:5%;">B</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rekapData as $r)
            <tr>
              <td class="text-center">{{ $r->no }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; font-weight:700; color:#000000;">{{ $kategori === 'siswa' ? ($r->nisn ?? '-') : $r->nip }}</td>
              <td><strong>{{ $r->nama }}</strong></td>
              <td class="text-center" style="font-weight:700; color:#000000;">{{ $kategori === 'siswa' ? $r->rombel : $r->jabatan }}</td>
              <td class="text-center" style="font-weight:{{ $r->hadir > 0 ? '700' : '400' }}; color:#000000;">{{ $r->hadir }}</td>
              <td class="text-center" style="font-weight:{{ $r->terlambat > 0 ? '800' : '400' }}; color:#000000;">{{ $r->terlambat }}</td>
              <td class="text-center" style="font-weight:{{ $r->sakit > 0 ? '700' : '400' }}; color:#000000;">{{ $r->sakit }}</td>
              <td class="text-center" style="font-weight:{{ $r->izin > 0 ? '700' : '400' }}; color:#000000;">{{ $r->izin }}</td>
              <td class="text-center" style="font-weight:{{ $r->alpha > 0 ? '800' : '400' }}; color:#000000;">{{ $r->alpha }}</td>
              <td class="text-center" style="font-weight:{{ $r->bolos > 0 ? '800' : '400' }}; color:#000000;">{{ $r->bolos }}</td>
              <td class="text-center" style="font-weight:700; color:#000000;">{{ $r->total_hari }}</td>
              <td class="text-center" style="font-weight:800; color:#000000;">{{ $r->persen }}%</td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="text-center" style="padding:16px; color:#000000;">Tidak ada catatan data presensi pada rentang periode ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    @else
      {{-- TABEL RINCIAN HARIAN / INDIVIDU --}}
      <table class="table-data">
        <thead>
          <tr>
            <th style="width:4%;">No</th>
            <th style="width:9%;">Tanggal</th>
            <th style="width:13%;">{{ $kategori === 'siswa' ? 'NISN' : 'NIP' }}</th>
            <th style="width:22%;">Nama Lengkap</th>
            <th style="width:10%; text-align:center;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
            <th style="width:9%;">Status</th>
            <th style="width:7%;">Masuk</th>
            <th style="width:7%;">Pulang</th>
            <th style="width:19%;">Alasan / Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($laporans as $i => $lap)
            @php
              $ket = $lap->keterangan ?: (isset($izinMap) && $izinMap ? ($izinMap->get($lap->pemilik_id)?->keterangan ?? '-') : '-');
            @endphp
            <tr>
              <td class="text-center">{{ $i + 1 }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; color:#000000; font-size:7.5pt;">{{ \Carbon\Carbon::parse($lap->tanggal)->format('d/m/Y') }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; font-weight:700; color:#000000; font-size:7.5pt;">
                {{ $kategori === 'siswa' ? ($lap->siswaRombel->siswa->nisn ?? ($lap->siswa->nisn ?? '-')) : ($lap->guru->nip ?? '-') }}
              </td>
              <td style="word-break:break-word;"><strong>{{ $kategori === 'siswa' ? ($lap->siswaRombel->siswa->nama ?? ($lap->siswa->nama ?? '-')) : ($lap->guru->nama ?? '-') }}</strong></td>
              <td class="text-center" style="font-weight:700; color:#000000;">{{ $kategori === 'siswa' ? ($lap->siswaRombel->rombel->nama_rombel ?? ($lap->siswa->siswaRombels->first()?->rombel?->nama_rombel ?? '-')) : ($lap->guru->jabatan ?? '-') }}</td>
              <td class="text-center" style="font-weight:800; color:#000000;">
                {{ strtoupper($lap->status) }}
              </td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; color:#000000; font-size:7.5pt;">{{ $lap->jam_masuk ?? '-' }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; color:#000000; font-size:7.5pt;">{{ $lap->jam_pulang ?? '-' }}</td>
              <td style="font-size:8pt; color:#000000; word-break:break-word;">{{ $ket !== '-' ? $ket : ($lap->status === 'bolos' ? 'Tidak tap pulang' : ($lap->status === 'alpha' ? 'Tanpa keterangan' : '-')) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center" style="padding:16px; color:#000000;">Tidak ada catatan data presensi harian pada tanggal ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    @endif

    <div style="font-size:8pt; color:#000000; margin-bottom:10px;">
      <em>* Dokumen ini dicetak otomatis dari SIRANI (Sistem Informasi Responsif Absensi &amp; Penegakan Disiplin) SMKN 1 Air Naningan pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB.</em>
    </div>

    {{-- TANDA TANGAN PENGESAHAN --}}
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
            Koordinator Piket &amp; Kesiswaan
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ auth()->user()->name ?? 'Petugas Piket SMKN 1 AN' }}</div>
            <div>NIP/NUPTK. -</div>
          </td>
        </tr>
      </table>
    </div>

  </div>
  </div>

</body>
</html>
