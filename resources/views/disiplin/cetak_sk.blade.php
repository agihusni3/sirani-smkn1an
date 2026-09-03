<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SK Kepala Sekolah: {{ $siswa->nama }} — SMKN 1 Air Naningan</title>
  <style>
    @page {
      size: A4;
      margin: 12mm 18mm 15mm 18mm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      line-height: 1.35;
      font-size: 11.5pt;
      margin: 0;
      padding: 15px 20px;
      background: #fff;
    }
    .kop-surat {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 3px double #000;
      padding-bottom: 6px;
      margin-bottom: 14px;
      width: 100%;
    }
    .kop-logo-box {
      width: 65px;
      min-width: 65px;
      max-width: 65px;
      height: 65px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo-box img {
      max-width: 65px;
      max-height: 65px;
      width: auto;
      height: auto;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }
    .kop-text {
      text-align: center;
      flex: 1;
      padding: 0 6px;
      margin: 0;
      min-width: 0;
    }
    .kop-text h3 {
      margin: 0;
      font-size: 10.5pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1.15;
    }
    .kop-text h2 {
      margin: 1px 0;
      font-size: 14pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      line-height: 1.2;
    }
    .kop-text p {
      margin: 2px 0 0 0;
      font-size: 8pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #222222;
      line-height: 1.25;
    }

    .judul-doc {
      text-align: center;
      margin-bottom: 14px;
    }
    .judul-doc h4 {
      margin: 0;
      font-size: 12pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1.2;
    }
    .judul-doc .nomor-sk {
      font-size: 10pt;
      font-weight: bold;
      margin-top: 2px;
      display: block;
    }
    .judul-doc .tentang-sk {
      font-size: 10.5pt;
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 4px;
      display: block;
    }

    .konsiderans-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
      font-size: 10.5pt;
    }
    .konsiderans-table td {
      vertical-align: top;
      padding: 2px 0;
    }

    .diktum-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      font-size: 10.5pt;
    }
    .diktum-table td {
      vertical-align: top;
      padding: 3px 0;
    }

    .data-siswa-box {
      background: #f9f9f9;
      border: 1px solid #000;
      padding: 6px 10px;
      margin: 4px 0 6px 0;
      font-size: 10pt;
    }
    .data-siswa-box table {
      width: 100%;
      border-collapse: collapse;
    }
    .data-siswa-box td {
      padding: 1.5px 0;
      vertical-align: top;
    }

    .signature-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 18px;
      page-break-inside: avoid;
    }
    .signature-box {
      text-align: left;
      width: 260px;
      font-size: 10.5pt;
    }
    .signature-space {
      height: 55px;
    }

    .tembusan-box {
      margin-top: 14px;
      font-size: 8.5pt;
      border-top: 1px solid #777;
      padding-top: 4px;
      page-break-inside: avoid;
    }
    .tembusan-box ol {
      margin: 2px 0 0 16px;
      padding: 0;
    }

    .no-print-bar {
      background: #0F172A;
      color: #fff;
      padding: 10px 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 8px;
      margin-bottom: 16px;
      font-family: sans-serif;
      flex-wrap: wrap;
      gap: 8px;
    }
    @media print {
      .no-print-bar { display: none !important; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

<div class="no-print-bar">
  <div style="font-size:13px; font-weight:bold;">
    Surat Keputusan (SK) Kepala Sekolah — Format Dokumen Dinas Resmi (A4)
  </div>
  <div style="display:flex; gap:8px;">
    <button type="button" onclick="window.print()" style="background:#000000; color:#fff; border:1px solid #fff; padding:6px 14px; border-radius:6px; font-weight:bold; cursor:pointer; font-size:12px;">
      Cetak SK Resmi
    </button>
    <button type="button" onclick="window.close()" style="background:#334155; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer;">
      Tutup
    </button>
  </div>
</div>

{{-- KOP DINAS RESMI --}}
<div class="kop-surat">
  <div class="kop-logo-box">
    <img src="/img/logo_prov_lampung.png" alt="Logo Provinsi" onerror="this.onerror=null; this.src='/img/logo_prov_lampung.svg'" />
  </div>
  <div class="kop-text">
    <h3>PEMERINTAH PROVINSI LAMPUNG</h3>
    <h3>DINAS PENDIDIKAN DAN KEBUDAYAAN</h3>
    <h2>{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</h2>
    <p>{{ $sekolah->alamat_lengkap ?? $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}</p>
    <p>Email: {{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }} · Website: {{ $sekolah->website ?? 'smkn1airnaningan.sch.id' }}</p>
  </div>
  <div class="kop-logo-box">
    @if(!empty($sekolah->logo_sekolah))
      <img src="{{ asset('storage/'.$sekolah->logo_sekolah) }}" alt="Logo Sekolah" onerror="this.onerror=null; this.src='/img/logo.png'" />
    @else
      <img src="/img/logo.png" alt="Logo Sekolah" />
    @endif
  </div>
</div>

{{-- JUDUL SURAT KEPUTUSAN --}}
<div class="judul-doc">
  <h4>KEPUTUSAN KEPALA SMK NEGERI 1 AIR NANINGAN</h4>
  <span class="nomor-sk">Nomor: 421.5 / {{ str_pad($kasus->id, 3, '0', STR_PAD_LEFT) }} / SK-DISIPLIN / SMKN1-AN / {{ \Carbon\Carbon::today()->format('m/Y') }}</span>
  <span class="tentang-sk">TENTANG<br>PENETAPAN SANKSI DAN PEMBINAAN KHUSUS KEDISIPLINAN SISWA</span>
</div>

<p style="text-align:center; font-weight:bold; margin:6px 0 10px 0; font-size:11pt;">
  KEPALA SMK NEGERI 1 AIR NANINGAN,
</p>

{{-- KONSIDERANS --}}
<table class="konsiderans-table">
  <tr>
    <td style="width:18%; font-weight:bold;">Menimbang</td>
    <td style="width:2%;">:</td>
    <td style="width:3%;">a.</td>
    <td>bahwa dalam rangka memelihara ketertiban, kelancaran proses belajar mengajar, serta menegakkan disiplin dan budi pekerti di SMK Negeri 1 Air Naningan, perlu dilakukan pembinaan berjenjang terhadap pelanggaran tata tertib sekolah;</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td>b.</td>
    <td>bahwa siswa yang bersangkutan telah melalui tahapan pembinaan persuasif oleh Wali Kelas, konseling oleh Guru BK, serta musyawarah kedisiplinan oleh Waka Kesiswaan;</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td>c.</td>
    <td>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan b, dipandang perlu menetapkan Keputusan Kepala Sekolah tentang sanksi dan tindak lanjut kedisiplinan siswa.</td>
  </tr>

  <tr>
    <td style="font-weight:bold; padding-top:4px;">Mengingat</td>
    <td style="padding-top:4px;">:</td>
    <td style="padding-top:4px;">1.</td>
    <td style="padding-top:4px;">Undang-Undang Republik Indonesia Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td>2.</td>
    <td>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 23 Tahun 2015 tentang Penumbuhan Budi Pekerti;</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td>3.</td>
    <td>Peraturan Tata Tertib dan Kode Etik Peserta Didik SMK Negeri 1 Air Naningan Tahun Pelajaran {{ $kasus->tahunAjaran->nama_tahun ?? 'Aktif' }}.</td>
  </tr>

  <tr>
    <td style="font-weight:bold; padding-top:4px;">Memperhatikan</td>
    <td style="padding-top:4px;">:</td>
    <td colspan="2" style="padding-top:4px;">
      Lembar Resume Yuridis Rekam Jejak Pelanggaran Siswa (Akumulasi {{ $totalAlpha }}x Alpha, {{ $totalBolos }}x Bolos, Total Bersih: <strong>{{ $kasus->poin_bersih }} Poin</strong>) dan Rekomendasi Sidang Kesiswaan tanggal {{ \Carbon\Carbon::parse($kasus->tanggal_sidang_wakasis ?: $kasus->updated_at)->translatedFormat('d F Y') }}.
    </td>
  </tr>
</table>

<p style="text-align:center; font-weight:bold; margin:8px 0; font-size:11pt; letter-spacing:1px;">
  MEMUTUSKAN:
</p>

{{-- DIKTUM --}}
<table class="diktum-table">
  <tr>
    <td style="width:18%; font-weight:bold;">Menetapkan</td>
    <td style="width:2%;">:</td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td style="font-weight:bold;">KESATU</td>
    <td>:</td>
    <td colspan="2">
      Menetapkan status penegakan kedisiplinan tingkat eksekutif terhadap peserta didik:
      <div class="data-siswa-box">
        <table>
          <tr>
            <td style="width:30%;">Nama Lengkap</td>
            <td style="width:2%;">:</td>
            <td style="width:68%;"><strong>{{ $siswa->nama }}</strong></td>
          </tr>
          <tr>
            <td>Nomor Induk Siswa Nasional (NISN)</td>
            <td>:</td>
            <td>{{ $siswa->nisn ?: '-' }}</td>
          </tr>
          <tr>
            <td>Kelas / Jurusan</td>
            <td>:</td>
            <td>{{ $rombel->nama_rombel ?? '-' }} / {{ $rombel?->jurusan?->nama_jurusan ?? '-' }}</td>
          </tr>
          <tr>
            <td>Wali Kelas</td>
            <td>:</td>
            <td>{{ $wali->nama ?? '-' }}</td>
          </tr>
          <tr>
            <td>Orang Tua / Wali</td>
            <td>:</td>
            <td>{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua' }}</td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
  <tr>
    <td style="font-weight:bold;">KEDUA</td>
    <td>:</td>
    <td colspan="2">
      Menetapkan ketetapan sanksi dan masa pembinaan khusus pimpinan sekolah sebagai berikut:<br>
      <div style="padding:4px 8px; border-left:3px solid #000; background:#f5f5f5; margin:4px 0; font-style:italic;">
        <strong>"{{ $kasus->keputusan_kepsek ?: ($kasus->sanksi_wakasis ?: 'Penerbitan Surat Peringatan Terakhir (SP-3) dan Pembinaan Khusus Pimpinan Sekolah bersama Orang Tua') }}"</strong>
      </div>
    </td>
  </tr>
  <tr>
    <td style="font-weight:bold;">KETIGA</td>
    <td>:</td>
    <td colspan="2">
      Menugaskan Waka Bidang Kesiswaan, Guru Bimbingan &amp; Konseling (BK), serta Wali Kelas untuk memantau, mendampingi, dan mengevaluasi kepatuhan tata tertib peserta didik selama masa pembinaan.
    </td>
  </tr>
  <tr>
    <td style="font-weight:bold;">KEEMPAT</td>
    <td>:</td>
    <td colspan="2">
      Keputusan ini mulai berlaku sejak tanggal ditetapkan, dan apabila di kemudian hari terdapat kekeliruan dalam penetapan ini, akan diadakan perbaikan sebagaimana mestinya.
    </td>
  </tr>
</table>

{{-- TANDA TANGAN KEPALA SEKOLAH --}}
<div class="signature-container">
  <div class="signature-box">
    <div>Ditetapkan di : Air Naningan</div>
    <div>Pada tanggal : {{ \Carbon\Carbon::parse($kasus->tanggal_keputusan_kepsek ?: \Carbon\Carbon::today())->translatedFormat('d F Y') }}</div>
    <div style="font-weight:bold; margin-top:4px;">Kepala SMK Negeri 1 Air Naningan,</div>
    <div class="signature-space"></div>
    <div style="font-weight:bold; text-decoration:underline;">{{ $sekolah->nama_kepsek ?? '..................................' }}</div>
    <div>NIP. {{ $sekolah->nip_kepsek ?? '..................................' }}</div>
  </div>
</div>

{{-- TEMBUSAN --}}
<div class="tembusan-box">
  <strong>Tembusan disampaikan kepada Yth:</strong>
  <ol>
    <li>Kepala Cabang Dinas Pendidikan Wilayah II Provinsi Lampung</li>
    <li>Pengawas Pembina SMK Kabupaten Tanggamus</li>
    <li>Ketua Komite SMK Negeri 1 Air Naningan</li>
    <li>Waka Bidang Kesiswaan &amp; Koordinator Guru BK</li>
    <li>Wali Kelas yang bersangkutan</li>
    <li>Orang Tua / Wali Murid</li>
    <li>Arsip Kesiswaan</li>
  </ol>
</div>

</body>
</html>
