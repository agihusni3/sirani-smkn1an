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
      size: A4 landscape;
      margin: 10mm 12mm 12mm 12mm;
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
      width: 297mm;
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

    /* KERTAS A4 LANDSCAPE */
    .a4-sheet {
      width: 297mm;
      min-height: 210mm;
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
      margin: 6px 0 14px;
    }

    /* JUDUL LAPORAN */
    .judul-laporan {
      text-align: center;
      margin-bottom: 12px;
    }
    .judul-laporan h2 {
      font-size: 13pt;
      font-weight: 800;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .judul-laporan .sub-judul {
      font-size: 10.5pt;
      font-weight: 600;
      margin-top: 3px;
    }

    /* METADATA */
    .meta-table {
      width: 100%;
      font-size: 10pt;
      margin-bottom: 10px;
    }
    .meta-table td {
      padding: 2px 4px;
      vertical-align: top;
    }

    /* TABEL DATA */
    .table-data {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
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

    /* LEMBAR TANDA TANGAN */
    .ttd-container {
      width: 100%;
      margin-top: 18px;
      font-size: 10.5pt;
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
      height: 52px;
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
      <span>Laporan Rekapitulasi Presensi {{ strtoupper($kategori) }} (Format Standar A4)</span>
      <span class="badge-a4">A4 Landscape</span>
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
      <h2>LAPORAN REKAPITULASI PRESENSI &amp; KEDISIPLINAN {{ strtoupper($kategori) }}</h2>
      <div class="sub-judul">{{ $periodeText }}</div>
    </div>

    {{-- METADATA INFO --}}
    <table class="meta-table">
      <tr>
        <td style="width:14%;"><strong>Kategori Pengguna</strong></td>
        <td style="width:1%;">:</td>
        <td style="width:35%;">{{ $kategori === 'siswa' ? 'Peserta Didik (Siswa)' : 'Tenaga Pendidik & Kependidikan (Guru/Staf)' }}</td>
        @if($kategori === 'siswa' && $rombel)
          <td style="width:14%;"><strong>Rombel / Kelas</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:35%;">{{ $rombel->nama_rombel }} (Wali: {{ $rombel->waliKelas->nama ?? '-' }})</td>
        @else
          <td style="width:14%;"><strong>Tahun Pelajaran</strong></td>
          <td style="width:1%;">:</td>
          <td style="width:35%;">2026/2027</td>
        @endif
      </tr>
    </table>

    {{-- KONTEN TABEL: REKAP AGREGAT ATAU LOG HARIAN --}}
    @if(in_array($periode, ['mingguan', 'bulanan', 'tahunan']))
      <table class="table-data">
        <thead>
          <tr>
            <th rowspan="2" style="width:4%;">No</th>
            <th rowspan="2" style="width:12%;">{{ $kategori === 'siswa' ? 'NIS' : 'NIP' }}</th>
            <th rowspan="2">Nama Lengkap</th>
            <th rowspan="2" style="width:14%;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
            <th colspan="6">Rincian Kehadiran &amp; Ketidakhadiran</th>
            <th rowspan="2" style="width:8%;">Total Hari</th>
            <th rowspan="2" style="width:8%;">% Hadir</th>
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
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; font-weight:600;">{{ $kategori === 'siswa' ? $r->nis : $r->nip }}</td>
              <td><strong>{{ $r->nama }}</strong></td>
              <td>{{ $kategori === 'siswa' ? $r->rombel : $r->jabatan }}</td>
              <td class="text-center">{{ $r->hadir }}</td>
              <td class="text-center" style="{{ $r->terlambat > 0 ? 'color:#CA8A04; font-weight:700;' : '' }}">{{ $r->terlambat }}</td>
              <td class="text-center">{{ $r->sakit }}</td>
              <td class="text-center">{{ $r->izin }}</td>
              <td class="text-center" style="{{ $r->alpha >= 3 ? 'color:#DC2626; font-weight:800; background:#FEE2E2;' : ($r->alpha > 0 ? 'color:#DC2626; font-weight:700;' : '') }}">{{ $r->alpha }}</td>
              <td class="text-center" style="{{ $r->bolos > 0 ? 'color:#DC2626; font-weight:700;' : '' }}">{{ $r->bolos }}</td>
              <td class="text-center" style="font-weight:700;">{{ $r->total_hari }}</td>
              <td class="text-center" style="font-weight:800; {{ $r->persen < 75 ? 'color:#DC2626;' : 'color:#16A34A;' }}">{{ $r->persen }}%</td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="text-center" style="padding:16px; color:#666;">Tidak ada catatan data presensi pada rentang periode ini.</td>
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
            <th style="width:11%;">{{ $kategori === 'siswa' ? 'NIS' : 'NIP' }}</th>
            <th>Nama Lengkap</th>
            <th style="width:11%;">{{ $kategori === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
            <th style="width:9%;">Status</th>
            <th style="width:8%;">Masuk</th>
            <th style="width:8%;">Pulang</th>
            <th style="width:18%;">Alasan / Keterangan</th>
            <th style="width:10%;">Sumber</th>
          </tr>
        </thead>
        <tbody>
          @forelse($laporans as $i => $lap)
            @php
              $ket = $lap->keterangan ?: (isset($izinMap) && $izinMap ? ($izinMap->get($lap->pemilik_id)?->keterangan ?? '-') : '-');
            @endphp
            <tr>
              <td class="text-center">{{ $i + 1 }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace;">{{ \Carbon\Carbon::parse($lap->tanggal)->format('d/m/Y') }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace; font-weight:600;">
                {{ $kategori === 'siswa' ? ($lap->siswaRombel->siswa->nis ?? ($lap->siswa->nis ?? '-')) : ($lap->guru->nip ?? '-') }}
              </td>
              <td><strong>{{ $kategori === 'siswa' ? ($lap->siswaRombel->siswa->nama ?? ($lap->siswa->nama ?? '-')) : ($lap->guru->nama ?? '-') }}</strong></td>
              <td>{{ $kategori === 'siswa' ? ($lap->siswaRombel->rombel->nama_rombel ?? ($lap->siswa->siswaRombels->first()?->rombel?->nama_rombel ?? '-')) : ($lap->guru->jabatan ?? '-') }}</td>
              <td class="text-center" style="font-weight:700; {{ $lap->status === 'hadir' ? 'color:#16A34A;' : ($lap->status === 'terlambat' ? 'color:#CA8A04;' : ($lap->status === 'alpha' ? 'color:#DC2626;' : '')) }}">
                {{ strtoupper($lap->status) }}
              </td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace;">{{ $lap->jam_masuk ?? '-' }}</td>
              <td class="text-center" style="font-family:'JetBrains Mono', monospace;">{{ $lap->jam_pulang ?? '-' }}</td>
              <td style="font-size:8.5pt;">{{ $ket !== '-' ? $ket : ($lap->status === 'bolos' ? 'Tidak tap pulang' : ($lap->status === 'alpha' ? 'Tanpa keterangan' : '-')) }}</td>
              <td style="font-size:8pt; color:#555;">{{ $lap->sumber_absen === 'kios_rfid' || $lap->sumber_absen === 'kios_wajah' || $lap->sumber_absen === 'face_kiosk' || empty($lap->sumber_absen) ? 'Face ID' : ($lap->sumber_absen === 'manual_piket' ? 'Manual Piket' : $lap->sumber_absen) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center" style="padding:16px; color:#666;">Tidak ada catatan data presensi harian pada tanggal ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    @endif

    <div style="font-size:8.5pt; color:#444; margin-bottom:10px;">
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

</body>
</html>
