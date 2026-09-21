<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Capaian Pembelajaran (CP) — {{ $perangkat->mataPelajaran?->nama_mapel }}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 15mm 20mm 15mm 20mm;
    }
    * { box-sizing: border-box; }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 11pt;
      line-height: 1.45;
      color: #000;
      margin: 0;
      padding: 0;
    }

    /* KOP SURAT DINAS BAKU SITUAN */
    .table-kop {
      width: 100%;
      border-collapse: collapse;
      border: none !important;
      margin-bottom: 0;
    }
    .table-kop td {
      border: none !important;
      padding: 0;
      vertical-align: middle;
    }
    .kop-logo-left, .kop-logo-right {
      width: 65px;
      text-align: center;
    }
    .kop-logo-left img, .kop-logo-right img {
      width: 58px;
      height: auto;
      max-height: 72px;
    }
    .kop-text {
      text-align: center;
      padding: 0 8px;
    }
    .kop-instansi {
      font-size: 10.5pt;
      font-weight: bold;
      text-transform: uppercase;
      line-height: 1.2;
      margin-bottom: 1px;
    }
    .kop-dinas {
      font-size: 11.5pt;
      font-weight: bold;
      text-transform: uppercase;
      line-height: 1.2;
      margin-bottom: 1px;
    }
    .kop-sekolah {
      font-size: 14.5pt;
      font-weight: 900;
      text-transform: uppercase;
      line-height: 1.2;
      margin-bottom: 2px;
    }
    .kop-alamat {
      font-size: 7.5pt;
      font-style: italic;
      line-height: 1.25;
      color: #111;
    }
    .kop-kontak {
      font-size: 7pt;
      line-height: 1.25;
      color: #222;
      margin-top: 1px;
    }
    .kop-border {
      border-top: 2.5px solid #000;
      border-bottom: 0.8px solid #000;
      height: 3px;
      margin: 5px 0 14px 0;
    }

    /* JUDUL DOKUMEN */
    .judul-dokumen {
      text-align: center;
      margin-bottom: 16px;
    }
    .judul-utama {
      font-size: 12.5pt;
      font-weight: bold;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.5px;
    }
    .judul-sub {
      font-size: 10.5pt;
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 3px;
    }

    /* TABEL IDENTITAS */
    .tabel-identitas {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
      border: 1px solid #000;
    }
    .tabel-identitas td {
      padding: 4px 8px;
      font-size: 10pt;
      border: 1px solid #ccc;
    }
    .tabel-identitas td.label {
      width: 170px;
      background: #f1f5f9;
      font-weight: bold;
      color: #1e293b;
    }

    /* SUB-SECTION TITLE */
    .sub-section-title {
      font-size: 10.5pt;
      font-weight: bold;
      background: #e2e8f0;
      padding: 4px 8px;
      border-left: 4px solid #1e3a5f;
      margin: 14px 0 8px 0;
      text-transform: uppercase;
    }

    /* KONTEN TEKS NARASI CP */
    .narasi-box {
      border: 1px solid #cbd5e1;
      background: #fafafa;
      padding: 10px 14px;
      margin-bottom: 14px;
      font-size: 10pt;
      line-height: 1.6;
    }

    /* TABEL ELEMEN KOMPETENSI */
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }
    table.data-table th, table.data-table td {
      border: 1px solid #000;
      padding: 5px 8px;
      font-size: 9.5pt;
      vertical-align: top;
    }
    table.data-table th {
      background: #dce8f5;
      font-weight: bold;
      text-align: center;
    }

    /* AREA TANDA TANGAN */
    .area-ttd {
      width: 100%;
      margin-top: 24px;
      page-break-inside: avoid;
    }
    .area-ttd table {
      width: 100%;
      border: none !important;
      border-collapse: collapse;
    }
    .area-ttd td {
      border: none !important;
      font-size: 10pt;
      vertical-align: top;
      padding: 0;
    }
  </style>
</head>
<body>

  {{-- 1. KOP SURAT DINAS BAKU SITUAN --}}
  @include('dcc.akademik.perangkat.export.partials.kop_surat_a4')

  {{-- 2. JUDUL DOKUMEN --}}
  <div class="judul-dokumen">
    <div class="judul-utama">CAPAIAN PEMBELAJARAN (CP)</div>
    <div class="judul-sub">
      KURIKULUM MERDEKA TAHUN AJARAN {{ $perangkat->tahunAjaran?->nama ?? '2026/2027' }}
    </div>
  </div>

  {{-- 3. IDENTITAS PERANGKAT & MATA PELAJARAN --}}
  <table class="tabel-identitas">
    <tr>
      <td class="label">Satuan Pendidikan</td>
      <td>: {{ $sekolah->nama_sekolah ?? 'SMK Negeri 1 Air Naningan' }} (NPSN: {{ $sekolah->npsn ?? '70011825' }})</td>
      <td class="label">Fase / Kelas</td>
      <td>: Fase {{ $perangkat->fase }} / Kelas {{ $perangkat->tingkat }}</td>
    </tr>
    <tr>
      <td class="label">Mata Pelajaran</td>
      <td>: <strong>{{ $perangkat->mataPelajaran?->nama_mapel }}</strong> ({{ $perangkat->mataPelajaran?->kode_mapel }})</td>
      <td class="label">Semester</td>
      <td>: {{ $perangkat->semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}</td>
    </tr>
    <tr>
      <td class="label">Guru Pengampu</td>
      <td>: <strong>{{ $perangkat->guru?->nama }}</strong></td>
      <td class="label">NIP / NUPTK</td>
      <td>: {{ $perangkat->guru?->nip ?? '-' }}</td>
    </tr>
    <tr>
      <td class="label">Status Perumusan</td>
      <td>: {{ !empty($perangkat->capaian_pembelajaran) ? 'Rumusan Resmi Guru Pengampu' : 'Standar Acuan BSKAP No. 032/H/KR/2024' }}</td>
      <td class="label">Status Validasi</td>
      <td>: {{ $perangkat->status === 'disahkan' ? 'Telah Disahkan (Resmi)' : ucfirst($perangkat->status ?? 'Draft') }}</td>
    </tr>
  </table>

  {{-- 4. CAPAIAN PEMBELAJARAN (CP) --}}
  <div class="sub-section-title">A. Capaian Pembelajaran Fase {{ $perangkat->fase }}</div>
  <div class="narasi-box">
    {!! format_narasi_kbm($activeCpText, ['line_height' => '1.6', 'margin_bottom' => '8px', 'font_size' => '10pt']) !!}
  </div>

  {{-- 5. RASIONAL & TUJUAN MATA PELAJARAN (jika ada) --}}
  @if(!empty($perangkat->rasional_tujuan))
    <div class="sub-section-title">B. Rasional &amp; Tujuan Mata Pelajaran</div>
    <div class="narasi-box">
      {!! format_narasi_kbm($perangkat->rasional_tujuan, ['line_height' => '1.6', 'margin_bottom' => '8px', 'font_size' => '10pt']) !!}
    </div>
  @endif

  {{-- 6. ELEMEN KOMPETENSI CP --}}
  @php
    $elemenList = !empty($perangkat->elemen_cp) && is_array($perangkat->elemen_cp) && count($perangkat->elemen_cp) > 0
      ? $perangkat->elemen_cp
      : $activeElemen;
  @endphp

  <div class="sub-section-title">{{ !empty($perangkat->rasional_tujuan) ? 'C.' : 'B.' }} Elemen Kompetensi Capaian Pembelajaran</div>
  @if(!empty($elemenList) && count($elemenList) > 0)
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:35px;">No</th>
          <th style="width:165px;">Nama Elemen CP</th>
          <th>Deskripsi Capaian Pembelajaran Elemen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($elemenList as $i => $elem)
          <tr>
            <td style="text-align:center; font-weight:bold;">{{ $loop->iteration }}</td>
            <td><strong>{{ is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem }}</strong></td>
            <td style="text-align:justify;">
              {{ is_array($elem) ? ($elem['deskripsi'] ?? $elem['capaian'] ?? '-') : '-' }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <div style="padding:10px; border:1px dashed #cbd5e1; text-align:center; color:#64748b; font-style:italic; margin-bottom:14px; font-size:9.5pt;">
      Belum ada rincian elemen kompetensi yang diinputkan.
    </div>
  @endif

  {{-- 7. LEMBAR PENGESAHAN & TANDA TANGAN --}}
  <div class="area-ttd">
    <table>
      <tr>
        <td style="width:50%;">
          <div>Mengetahui,</div>
          <div>Kepala SMK Negeri 1 Air Naningan</div>
          <div style="height:55px;"></div>
          <div><strong>{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</strong></div>
          <div>NIP. {{ $sekolah->nip_kepala_sekolah ?? '197904172008012019' }}</div>
        </td>
        <td style="width:50%; text-align:right;">
          <div style="display:inline-block; text-align:left;">
            <div>Air Naningan, {{ \Carbon\Carbon::parse($perangkat->tanggal_pengesahan ?? now())->translatedFormat('d F Y') }}</div>
            <div>Guru Pengampu Mata Pelajaran</div>
            <div style="height:55px;"></div>
            <div><strong>{{ $perangkat->guru?->nama }}</strong></div>
            <div>NIP. {{ $perangkat->guru?->nip ?? '-' }}</div>
          </div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
