<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIRANI</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    /* STANDAR CETAK DINAS A4 RESMI DENGAN MARGIN KONSISTEN DI SELURUH HALAMAN */
    @page {
      size: A4 portrait;
      margin: 12mm 12mm 14mm 12mm;
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
      page-break-inside: auto;
      break-inside: auto;
    }
    .table-data thead {
      display: table-header-group;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .table-data thead tr {
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .table-data tbody {
      display: table-row-group;
    }
    .table-data tr {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
    .table-data th, .table-data td {
      border: 1px solid #000000;
      padding: 4px 4px;
      color: #000000;
      word-wrap: break-word;
      overflow-wrap: break-word;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
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
    .table-data th.col-stat,
    .table-data td.col-stat {
      padding: 3px 1px !important;
      font-size: 7.5pt;
      text-align: center;
      line-height: 1.15;
      color: #000000;
    }
    .table-data th.col-name,
    .table-data td.col-name {
      padding: 4px 6px !important;
      font-size: 8.5pt !important;
      text-align: left;
      line-height: 1.25;
      color: #000000;
      font-weight: 700;
    }

    /* LEMBAR TANDA TANGAN */
    .ttd-container {
      width: 100%;
      margin-top: 16px;
      font-size: 9.5pt;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
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
      height: 48px;
    }
    .ttd-name {
      font-weight: 800;
      text-decoration: underline;
      color: #000000;
    }

    @media print {
      @page {
        size: A4 portrait;
        margin: 12mm 12mm 14mm 12mm;
      }
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
        padding: 0 !important; /* Margin konsisten dikelola oleh @page */
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        border: none !important;
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

    @if($kategori === 'siswa' && isset($rombels) && $rombels->isNotEmpty())
      <div class="print-rombel-filter" style="display:flex; align-items:center; gap:8px;">
        <label for="pilihRombelCetak" style="color:#CBD5E1; font-size:12px; font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:5px;">
          <i class="bi bi-funnel-fill" style="color:#FACC15;"></i> Pilih Kelas:
        </label>
        @if($isWaliKelas ?? false)
          <select id="pilihRombelCetak" disabled style="background:#0F172A; color:#94A3B8; border:1px solid #334155; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:700; outline:none; cursor:not-allowed;">
            <option value="{{ $rombelId }}">{{ $rombel->nama_rombel ?? 'Kelas Anda' }} (Wali Kelas)</option>
          </select>
        @else
          <select id="pilihRombelCetak" onchange="gantiRombelCetak(this.value)" style="background:#0F172A; color:#FFFFFF; border:1.5px solid #FACC15; padding:6px 14px; border-radius:6px; font-size:12.5px; font-weight:700; outline:none; cursor:pointer; box-shadow:0 0 10px rgba(250, 204, 21, 0.2);">
            <option value="">-- Semua Kelas / Rombel --</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}" {{ (string)$rombelId === (string)$r->id ? 'selected' : '' }}>
                {{ $r->nama_rombel }}
              </option>
            @endforeach
          </select>
        @endif
      </div>
    @endif

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
    @include('partials.kop_surat')

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
        @if($kategori === 'siswa')
          <td style="width:15%;"><strong>Rombel / Kelas</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:26%;"><strong>{{ $rombel ? $rombel->nama_rombel : 'Semua Kelas / Rombel' }}</strong></td>
        @else
          <td style="width:15%;"><strong>Tahun Pelajaran</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:26%;">2026/2027</td>
        @endif
      </tr>
      @if($kategori === 'siswa')
        <tr>
          <td style="width:14%;"><strong>Tahun Pelajaran</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:43%;">2026/2027</td>
          <td style="width:15%;"><strong>Wali Kelas</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:26%;">{{ $rombel ? ($rombel->waliKelas->nama ?? '-') : '-' }}</td>
        </tr>
      @endif
    </table>

    {{-- KONTEN TABEL: REKAP AGREGAT ATAU LOG HARIAN --}}
    @if(in_array($periode, ['mingguan', 'bulanan', 'tahunan']))
      <table class="table-data">
        <thead>
          <tr>
            <th rowspan="2" style="width:3.5%; padding:3px 1px; font-size:7.5pt;">No</th>
            <th rowspan="2" style="width:10%; padding:3px 2px; font-size:7.5pt;">{{ $kategori === 'siswa' ? 'NISN' : 'NIP' }}</th>
            <th rowspan="2" class="col-name" style="width:47%;">Nama Lengkap</th>
            <th rowspan="2" style="width:8.5%; text-align:center; padding:3px 2px; font-size:7.5pt;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
            <th colspan="6" style="width:21%; padding:3px 2px; font-size:7.5pt;">Kehadiran &amp; Ketidakhadiran</th>
            <th rowspan="2" style="width:5%; text-align:center; padding:3px 1px; font-size:7.5pt;">Total</th>
            <th rowspan="2" style="width:5%; text-align:center; padding:3px 1px; font-size:7.5pt;">% Hadir</th>
          </tr>
          <tr>
            <th class="col-stat" style="width:3.5%;" title="Hadir Tepat">H</th>
            <th class="col-stat" style="width:3.5%;" title="Terlambat">T</th>
            <th class="col-stat" style="width:3.5%;" title="Sakit">S</th>
            <th class="col-stat" style="width:3.5%;" title="Izin Resmi">I</th>
            <th class="col-stat" style="width:3.5%;" title="Tanpa Keterangan (Alpha)">A</th>
            <th class="col-stat" style="width:3.5%;" title="Bolos / Tidak Tap Pulang">B</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rekapData as $r)
            <tr>
              <td class="text-center" style="padding:3px 1px; font-size:7.5pt;">{{ $r->no }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; font-size:7pt; font-weight:700; color:#000000; padding:3px 2px; letter-spacing:-0.2px;">{{ $kategori === 'siswa' ? ($r->nisn ?? '-') : $r->nip }}</td>
              <td class="col-name">{{ $r->nama }}</td>
              <td class="text-center" style="font-weight:700; color:#000000; padding:3px 2px; font-size:7.5pt;">{{ $kategori === 'siswa' ? $r->rombel : $r->jabatan }}</td>
              <td class="col-stat" style="font-weight:{{ $r->hadir > 0 ? '700' : '400' }};">{{ $r->hadir }}</td>
              <td class="col-stat" style="font-weight:{{ $r->terlambat > 0 ? '800' : '400' }};">{{ $r->terlambat }}</td>
              <td class="col-stat" style="font-weight:{{ $r->sakit > 0 ? '700' : '400' }};">{{ $r->sakit }}</td>
              <td class="col-stat" style="font-weight:{{ $r->izin > 0 ? '700' : '400' }};">{{ $r->izin }}</td>
              <td class="col-stat" style="font-weight:{{ $r->alpha > 0 ? '800' : '400' }};">{{ $r->alpha }}</td>
              <td class="col-stat" style="font-weight:{{ $r->bolos > 0 ? '800' : '400' }};">{{ $r->bolos }}</td>
              <td class="col-stat" style="font-weight:700;">{{ $r->total_hari }}</td>
              <td class="col-stat" style="font-weight:800;">{{ $r->persen }}%</td>
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
            <th style="width:3.5%; padding:3px 1px; font-size:7.5pt;">No</th>
            <th style="width:8.5%; padding:3px 2px; font-size:7.5pt;">Tanggal</th>
            <th style="width:10%; padding:3px 2px; font-size:7.5pt;">{{ $kategori === 'siswa' ? 'NISN' : 'NIP' }}</th>
            <th class="col-name" style="width:36%;">Nama Lengkap</th>
            <th style="width:8.5%; text-align:center; padding:3px 2px; font-size:7.5pt;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
            <th style="width:8%; padding:3px 2px; font-size:7.5pt;">Status</th>
            <th style="width:6.5%; padding:3px 1px; font-size:7.5pt;">Masuk</th>
            <th style="width:6.5%; padding:3px 1px; font-size:7.5pt;">Pulang</th>
            <th style="width:12.5%; padding:3px 4px; font-size:7.5pt;">Alasan / Keterangan</th>
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
              <td class="col-name">{{ $kategori === 'siswa' ? ($lap->siswaRombel->siswa->nama ?? ($lap->siswa->nama ?? '-')) : ($lap->guru->nama ?? '-') }}</td>
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

  <script>
    function gantiRombelCetak(rombelId) {
      const url = new URL(window.location.href);
      if (rombelId) {
        url.searchParams.set('rombel_id', rombelId);
      } else {
        url.searchParams.delete('rombel_id');
      }
      window.location.href = url.toString();
    }
  </script>
</body>
</html>
