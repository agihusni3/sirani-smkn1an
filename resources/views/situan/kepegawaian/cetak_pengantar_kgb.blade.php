<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Surat Pengantar KGB — {{ $guru->nama }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    :root {
      --primary: #0284c7;
      --font-body: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #f1f5f9;
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      font-size: 11.5pt;
      line-height: 1.5;
      padding: 20px 0;
    }

    .screen-toolbar {
      width: 210mm;
      margin: 0 auto 16px auto;
      background: #1e293b;
      color: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      font-family: var(--font-body);
    }
    .btn-action {
      background: #0284c7;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }
    .btn-action:hover {
      background: #0369a1;
      color: #fff;
    }
    .btn-outline {
      background: transparent;
      border: 1px solid #64748b;
      color: #cbd5e1;
    }

    .page-sheet {
      width: 210mm;
      min-height: 297mm;
      padding: 20mm 25mm 20mm 25mm;
      margin: 0 auto;
      background: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
      position: relative;
    }

    /* Kop Surat */
    .kop-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 2px;
    }
    .kop-logo {
      width: 78px;
      height: 78px;
      object-fit: contain;
    }
    .kop-text {
      text-align: center;
      flex-grow: 1;
    }
    .kop-instansi-1 {
      font-size: 12pt;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-instansi-2 {
      font-size: 13.5pt;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 800;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin: 1px 0;
    }
    .kop-alamat {
      font-size: 8.5pt;
      font-family: var(--font-body);
      line-height: 1.35;
      color: #1e293b;
    }

    .kop-divider {
      border: 0;
      border-top: 3px solid #000;
      border-bottom: 1px solid #000;
      height: 5px;
      margin: 8px 0 20px 0;
    }

    .header-surat {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      font-size: 11pt;
    }
    .tujuan-surat {
      width: 260px;
    }

    .meta-table td {
      padding: 2px 4px;
      vertical-align: top;
    }

    .surat-body {
      text-align: justify;
      font-size: 11.5pt;
      line-height: 1.6;
    }
    .surat-body p {
      margin-bottom: 12px;
      text-indent: 32px;
    }

    .bio-table {
      width: 100%;
      margin: 10px 0 16px 20px;
      border-collapse: collapse;
      font-size: 11pt;
    }
    .bio-table td {
      padding: 3px 6px;
      vertical-align: top;
    }
    .bio-label {
      width: 200px;
    }
    .bio-sep {
      width: 15px;
      text-align: center;
    }
    .bio-val {
      font-weight: 600;
    }

    .lampiran-box {
      margin: 10px 0 16px 32px;
      font-size: 10.5pt;
    }
    .lampiran-box ol {
      padding-left: 20px;
    }
    .lampiran-box li {
      margin-bottom: 3px;
    }

    .signature-container {
      margin-top: 25px;
      display: flex;
      justify-content: flex-end;
    }
    .signature-box {
      width: 280px;
      text-align: center;
    }
    .sign-space {
      height: 70px;
    }
    .sign-name {
      font-weight: 700;
      text-decoration: underline;
    }

    @media print {
      body {
        background: transparent;
        padding: 0;
      }
      .screen-toolbar {
        display: none !important;
      }
      .page-sheet {
        box-shadow: none;
        margin: 0;
        width: 100%;
        min-height: 100%;
        padding: 15mm 20mm;
      }
      @page {
        size: A4 portrait;
        margin: 0;
      }
    }
  </style>
</head>
<body>

  {{-- Screen Toolbar --}}
  <div class="screen-toolbar">
    <div style="font-size:13px;">
      <i class="bi bi-file-earmark-text-fill text-info me-1"></i>
      <strong>Format Surat Dinas:</strong> Pengantar Usul Kenaikan Gaji Berkala (KGB) &bull; {{ $guru->nama }}
    </div>
    <div style="display:flex; gap:10px;">
      <a href="{{ route('situan.radar-kgb.index') }}" class="btn-action btn-outline">
        <i class="bi bi-arrow-left"></i> Kembali ke Radar
      </a>
      <button onclick="window.print()" class="btn-action">
        <i class="bi bi-printer-fill"></i> Cetak Dokumen
      </button>
    </div>
  </div>

  {{-- Page A4 --}}
  <div class="page-sheet">

    {{-- KOP SURAT --}}
    <div class="kop-container">
      <img src="{{ $sekolah->logo_url ?: '/img/logo.png' }}" alt="Logo Sekolah" class="kop-logo" />
      <div class="kop-text">
        <div class="kop-instansi-1">{{ $sekolah->nama_instansi_atas ?: 'PEMERINTAH PROVINSI LAMPUNG' }}</div>
        <div class="kop-instansi-2">{{ $sekolah->nama_dinas ?: 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
        <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?: 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div class="kop-alamat">
          {{ $sekolah->alamat_lengkap ?: 'Jl. Raya Air Naningan No. 01, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}<br>
          NPSN: {{ $sekolah->npsn ?: '69900000' }} &bull; Website: {{ $sekolah->website ?: 'smkn1airnaningan.sch.id' }} &bull; Email: {{ $sekolah->email ?: 'smkn1airnaningan@gmail.sch.id' }}
        </div>
      </div>
    </div>
    <hr class="kop-divider">

    {{-- HEADER SURAT KELUAR DINAS --}}
    <div class="header-surat">
      <div>
        <table class="meta-table">
          <tr>
            <td>Nomor</td>
            <td>:</td>
            <td><strong>{{ $generator['nomor_surat_lengkap'] }}</strong></td>
          </tr>
          <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>1 (Satu) Berkas Usulan</td>
          </tr>
          <tr>
            <td>Perihal</td>
            <td>:</td>
            <td><strong>Usul Kenaikan Gaji Berkala (KGB)</strong></td>
          </tr>
        </table>
      </div>

      <div class="tujuan-surat">
        Air Naningan, {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}<br><br>
        Kepada Yth.<br>
        <strong>Kepala Dinas Pendidikan dan Kebudayaan Provinsi Lampung</strong><br>
        c.q. Kepala Cabang Dinas Wilayah II<br>
        di -<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>Tempat</u>
      </div>
    </div>

    {{-- ISI SURAT PENGANTAR --}}
    <div class="surat-body">
      <p>
        Dengan hormat, bersama ini kami sampaikan usulan Kenaikan Gaji Berkala (KGB) bagi Pegawai Negeri Sipil / Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) di lingkungan {{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }} atas nama:
      </p>

      <table class="bio-table">
        <tr>
          <td class="bio-label">Nama Pegawai</td>
          <td class="bio-sep">:</td>
          <td class="bio-val" style="text-transform:uppercase;">{{ $guru->nama }}</td>
        </tr>
        <tr>
          <td class="bio-label">NIP / NIK</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $guru->nip ?: ($guru->nik ?: '-') }}</td>
        </tr>
        <tr>
          <td class="bio-label">NUPTK</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $guru->nuptk ?: '-' }}</td>
        </tr>
        <tr>
          <td class="bio-label">Pangkat / Golongan Ruang</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $guru->golongan_ruang ?: 'Penata Muda, III/a' }}</td>
        </tr>
        <tr>
          <td class="bio-label">Jabatan / Tugas</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $guru->jenis_ptk ?: 'Guru Mata Pelajaran' }}</td>
        </tr>
        <tr>
          <td class="bio-label">TMT KGB Terakhir</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">
            {{ $guru->tmt_kgb_terakhir ? \Carbon\Carbon::parse($guru->tmt_kgb_terakhir)->translatedFormat('d F Y') : '-' }}
          </td>
        </tr>
        <tr>
          <td class="bio-label">TMT KGB Baru (Diusulkan)</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">
            {{ $guru->tmt_kgb_terakhir ? \Carbon\Carbon::parse($guru->tmt_kgb_terakhir)->addYears(2)->translatedFormat('d F Y') : \Carbon\Carbon::today()->translatedFormat('d F Y') }}
          </td>
        </tr>
        <tr>
          <td class="bio-label">Unit Kerja</td>
          <td class="bio-sep">:</td>
          <td class="bio-val">{{ $sekolah->nama_sekolah ?: 'SMK Negeri 1 Air Naningan' }}</td>
        </tr>
      </table>

      <p>
        Sebagai bahan pertimbangan dan kelengkapan berkas pengusulan, bersama ini kami lampirkan:
      </p>

      <div class="lampiran-box">
        <ol>
          <li>Fotokopi Surat Keputusan (SK) Kenaikan Pangkat Terakhir yang telah dilegalisir;</li>
          <li>Fotokopi Surat Keputusan (SK) Kenaikan Gaji Berkala (KGB) Terakhir;</li>
          <li>Fotokopi Penilaian Prestasi Kerja Pegawai / Sasaran Kinerja Pegawai (SKP) tahun terakhir;</li>
          <li>Surat Pernyataan Melaksanakan Tugas (SPMT) dari Kepala Sekolah;</li>
          <li>Daftar Riwayat Kepangkatan dan Gaji.</li>
        </ol>
      </div>

      <p>
        Demikian permohonan ini kami sampaikan, atas perhatian dan perkenan Bapak Kepala Dinas, kami ucapkan terima kasih.
      </p>
    </div>

    {{-- TANDA TANGAN KEPALA SEKOLAH --}}
    <div class="signature-container">
      <div class="signature-box">
        <div>Kepala Sekolah,</div>
        <div class="sign-space"></div>
        <div class="sign-name">{{ $sekolah->nama_kepala_sekolah ?: 'Drs. H. PENDIDIKAN, M.Pd.' }}</div>
        <div>NIP. {{ $sekolah->nip_kepala_sekolah ?: '19750101 200003 1 002' }}</div>
      </div>
    </div>

  </div>

</body>
</html>
