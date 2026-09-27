<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lembar Pengesahan Perangkat Pembelajaran — {{ $perangkat->mataPelajaran?->nama_mapel }}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 15mm 20mm 15mm 20mm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 12pt;
      line-height: 1.4;
      color: #000;
      margin: 0;
      padding: 0;
      background: #fff;
    }
    .kop-surat {
      display: flex;
      align-items: center;
      border-bottom: 3px double #000;
      padding-bottom: 8px;
      margin-bottom: 20px;
    }
    .kop-logo {
      width: 75px;
      height: 75px;
      object-fit: contain;
    }
    .kop-text {
      flex: 1;
      text-align: center;
      line-height: 1.2;
    }
    .kop-instansi {
      font-size: 13pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .kop-sekolah {
      font-size: 15pt;
      font-weight: 900;
      text-transform: uppercase;
      margin: 3px 0;
      letter-spacing: 0.8px;
    }
    .kop-alamat {
      font-size: 9pt;
      font-style: italic;
      color: #222;
    }
    .judul-dokumen {
      text-align: center;
      margin: 25px 0 20px 0;
    }
    .judul-utama {
      font-size: 14pt;
      font-weight: bold;
      text-decoration: underline;
      text-transform: uppercase;
      margin-bottom: 4px;
    }
    .nomor-surat {
      font-size: 11pt;
      font-weight: bold;
    }
    .narasi {
      text-align: justify;
      margin-bottom: 16px;
      text-indent: 30px;
    }
    .tabel-data {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .tabel-data td {
      padding: 4px 6px;
      vertical-align: top;
      font-size: 11.5pt;
    }
    .tabel-komponen {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0 25px 0;
    }
    .tabel-komponen th, .tabel-komponen td {
      border: 1px solid #000;
      padding: 6px 10px;
      font-size: 10.5pt;
    }
    .tabel-komponen th {
      background: #f1f5f9;
      text-align: center;
      font-weight: bold;
    }
    .area-ttd {
      width: 100%;
      margin-top: 30px;
      page-break-inside: avoid;
    }
    .area-ttd table {
      width: 100%;
      border-collapse: collapse;
    }
    .area-ttd td {
      vertical-align: top;
      font-size: 11pt;
      line-height: 1.3;
    }
    .qr-box {
      border: 1px solid #000;
      padding: 8px 12px;
      display: inline-block;
      text-align: center;
      font-size: 8pt;
      background: #fafafa;
    }
    .print-bar {
      background: #1e293b;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #fff;
      font-family: sans-serif;
      font-size: 13px;
    }
    .btn-print {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 6px 16px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    @media print {
      .print-bar { display: none !important; }
      body { background: transparent; }
    }
  </style>
</head>
<body>

  <div class="print-bar">
    <div>
      <strong>Pratinjau Cetak: Lembar Pengesahan Perangkat Pembelajaran SMK</strong> (Format A4 Standar Dinas)
    </div>
    <div>
      <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
    </div>
  </div>

  <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    
    {{-- KOP SURAT RESMI --}}
    <div class="kop-surat">
      <img src="{{ $sekolah->logo_provinsi ? asset('storage/' . $sekolah->logo_provinsi) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Coat_of_arms_of_Lampung.png/150px-Coat_of_arms_of_Lampung.png' }}" class="kop-logo" alt="Logo Lampung">
      <div class="kop-text">
        <div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG<br>DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
        <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div class="kop-alamat">
          {{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung' }}<br>
          NPSN: {{ $sekolah->npsn ?? '69896425' }} | Surel: {{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }} | Laman: https://smkn1airnaningan.sch.id
        </div>
      </div>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="judul-dokumen">
      <div class="judul-utama">LEMBAR PENGESAHAN PERANGKAT PEMBELAJARAN</div>
      <div class="nomor-surat">TAHUN AJARAN {{ $perangkat->tahunAjaran?->nama ?? '2026/2027' }}</div>
    </div>

    {{-- NARASI PENGESAHAN --}}
    <div class="narasi">
      Setelah melalui proses telaah, verifikasi internal, dan supervisi akademik oleh Tim Pengembang Kurikulum SMK Negeri 1 Air Naningan, maka Perangkat Pembelajaran Kurikulum Merdeka (Kepmendikbudristek No. 12/2024 dan BSKAP No. 032/H/KR/2024) yang disusun oleh:
    </div>

    {{-- TABEL IDENTITAS GURU --}}
    <table class="tabel-data">
      <tr>
        <td style="width: 25px;">1.</td>
        <td style="width: 220px;">Nama Guru Pengampu</td>
        <td style="width: 15px;">:</td>
        <td><strong>{{ $perangkat->guru?->nama }}</strong></td>
      </tr>
      <tr>
        <td>2.</td>
        <td>NIP / NUPTK</td>
        <td>:</td>
        <td>{{ $perangkat->guru?->nip ?? '-' }}</td>
      </tr>
      <tr>
        <td>3.</td>
        <td>Mata Pelajaran</td>
        <td>:</td>
        <td><strong>{{ $perangkat->mataPelajaran?->nama_mapel }}</strong> ({{ $perangkat->mataPelajaran?->kode_mapel }})</td>
      </tr>
      <tr>
        <td>4.</td>
        <td>Tingkat Kelas / Fase</td>
        <td>:</td>
        <td>Kelas {{ $perangkat->tingkat }} / Fase {{ $perangkat->fase }}</td>
      </tr>
      <tr>
        <td>5.</td>
        <td>Semester</td>
        <td>:</td>
        <td>{{ $perangkat->semester == 1 ? 'Ganjil (Satu)' : 'Genap (Dua)' }}</td>
      </tr>
      <tr>
        <td>6.</td>
        <td>Beban Tatap Muka</td>
        <td>:</td>
        <td>{{ $perangkat->distribusiMengajar?->total_jam_per_minggu ?? 4 }} Jam Pelajaran (JP) / Minggu</td>
      </tr>
    </table>

    <div style="text-align: justify; margin-bottom: 8px;">
      Dinyatakan telah <strong>LENGKAP</strong>, memenuhi standar kompetensi, dan <strong>DISAHKAN</strong> sebagai pedoman operasional pelaksanaan Kegiatan Belajar Mengajar (KBM) serta asesmen pembelajaran di kelas dan laboratorium/bengkel SMK Negeri 1 Air Naningan.
    </div>

    {{-- TABEL RINCIAN KOMPONEN DOKUMEN --}}
    <table class="tabel-komponen">
      <thead>
        <tr>
          <th style="width: 35px;">No</th>
          <th>Nama Komponen Perangkat Ajar</th>
          <th style="width: 140px;">Keterangan / Butir</th>
          <th style="width: 100px;">Status Telaah</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="text-align: center;">1</td>
          <td>Capaian Pembelajaran (CP BSKAP 032/2024) &amp; Elemen</td>
          <td style="text-align: center;">Fase {{ $perangkat->fase }} Terpetakan</td>
          <td style="text-align: center; font-weight: bold;">Lengkap (V)</td>
        </tr>
        <tr>
          <td style="text-align: center;">2</td>
          <td>Alur Tujuan Pembelajaran (ATP) &amp; Materi Pokok</td>
          <td style="text-align: center;">{{ $perangkat->atpItems->count() }} Butir TP ({{ $perangkat->atpItems->sum('alokasi_jp') }} JP)</td>
          <td style="text-align: center; font-weight: bold;">Lengkap (V)</td>
        </tr>
        <tr>
          <td style="text-align: center;">3</td>
          <td>Rincian Pekan Efektif (RPE), Prota &amp; Promes</td>
          <td style="text-align: center;">{{ $perangkat->rpe_pekan_efektif }} Pekan Efektif</td>
          <td style="text-align: center; font-weight: bold;">Lengkap (V)</td>
        </tr>
        <tr>
          <td style="text-align: center;">4</td>
          <td>Modul Ajar (MA) / RPP Merdeka &amp; LKPD Praktik</td>
          <td style="text-align: center;">{{ $perangkat->modulAjars->count() }} Modul Unit Ajar</td>
          <td style="text-align: center; font-weight: bold;">Lengkap (V)</td>
        </tr>
        <tr>
          <td style="text-align: center;">5</td>
          <td>Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)</td>
          <td style="text-align: center;">Pendekatan Interval / Rubrik</td>
          <td style="text-align: center; font-weight: bold;">Lengkap (V)</td>
        </tr>
      </tbody>
    </table>

    {{-- AREA TANDA TANGAN RESMI --}}
    <div class="area-ttd">
      <table>
        <tr>
          <td style="width: 50%;">
            Mengetahui / Menyetujui:<br>
            <strong>Waka Bidang Kurikulum</strong><br><br><br><br>
            <strong style="text-decoration: underline;">SUPRAPTO, S.Pd.</strong><br>
            NIP. 19820514 200902 1 003
          </td>
          <td style="width: 50%; padding-left: 20px;">
            Air Naningan, {{ $perangkat->tanggal_pengesahan ? $perangkat->tanggal_pengesahan->translatedFormat('d F Y') : date('d F Y') }}<br>
            Guru Mata Pelajaran,<br><br><br><br>
            <strong style="text-decoration: underline;">{{ $perangkat->guru?->nama }}</strong><br>
            NIP. {{ $perangkat->guru?->nip ?? '-' }}
          </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center; padding-top: 30px;">
            Mengesahkan:<br>
            <strong>Kepala SMK Negeri 1 Air Naningan</strong><br><br>
            
            {{-- QR Code Token Keabsahan Resmi --}}
            <div class="qr-box">
              <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-bottom: 2px;">
                DCC VERIFIED E-DOCUMENT
              </div>
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(url('/verifikasi-perangkat/' . $qrToken)) }}" style="width: 75px; height: 75px; margin: 2px 0;" alt="QR Code Keabsahan">
              <div style="font-family: monospace; font-size: 7.5pt; color: #475569;">
                ID: {{ $qrToken }}
              </div>
            </div>
            <br><br>
            <strong style="text-decoration: underline; font-size: 12pt;">{{ $sekolah->nama_kepala_sekolah ?? 'APRIDA, S.Pd., M.M.' }}</strong><br>
            Pembina Tk. I / IV.b<br>
            NIP. {{ $sekolah->nip_kepala_sekolah ?? '19750412 200501 2 007' }}
          </td>
        </tr>
      </table>
    </div>

  </div>

</body>
</html>
