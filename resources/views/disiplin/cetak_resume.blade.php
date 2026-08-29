<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resume Kesiswaan: {{ $siswa->nama }} — SMKN 1 Air Naningan</title>
  <style>
    @page {
      size: A4;
      margin: 15mm 20mm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      color: #000;
      line-height: 1.4;
      font-size: 12pt;
      margin: 0;
      padding: 20px;
      background: #fff;
    }
    .kop-surat {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 3px double #000;
      padding-bottom: 8px;
      margin-bottom: 18px;
      width: 100%;
    }
    .kop-logo-box {
      width: 68px;
      min-width: 68px;
      max-width: 68px;
      height: 68px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo-box img {
      max-width: 68px;
      max-height: 68px;
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
      font-size: 11pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1.15;
    }
    .kop-text h2 {
      margin: 1px 0;
      font-size: 15pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      line-height: 1.25;
    }
    .kop-text p {
      margin: 2px 0 0 0;
      font-size: 8pt;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: #222222;
      line-height: 1.3;
    }

    .judul-doc {
      text-align: center;
      margin-bottom: 20px;
    }
    .judul-doc h4 {
      margin: 0;
      font-size: 13pt;
      font-weight: bold;
      text-decoration: underline;
      text-transform: uppercase;
    }
    .judul-doc span {
      font-size: 10.5pt;
      font-family: monospace;
    }

    .section-title {
      font-weight: bold;
      font-size: 11.5pt;
      margin: 14px 0 6px 0;
      text-transform: uppercase;
      background: #f0f0f0;
      padding: 3px 6px;
      border-left: 4px solid #000;
    }

    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      font-size: 11pt;
    }
    table.data-table td {
      padding: 3px 6px;
      vertical-align: top;
    }
    table.border-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      font-size: 10.5pt;
    }
    table.border-table th, table.border-table td {
      border: 1px solid #000;
      padding: 5px 8px;
      text-align: left;
    }
    table.border-table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }

    .signature-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-top: 30px;
      font-size: 11pt;
      page-break-inside: avoid;
    }
    .signature-box {
      text-align: center;
    }
    .signature-space {
      height: 65px;
    }

    .no-print-bar {
      background: #0F172A;
      color: #fff;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: sans-serif;
    }
    @media print {
      .no-print-bar { display: none !important; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

<div class="no-print-bar">
  <div style="font-size:14px; font-weight:bold;">Dokumen Resume Yuridis Rekam Jejak Kesiswaan — Siap Cetak (A4)</div>
  <div style="display:flex; gap:8px;">
    <button type="button" onclick="window.print()" style="background:#CA8A04; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-weight:bold; cursor:pointer;">
      🖨️ Cetak Dokumen A4
    </button>
    <button type="button" onclick="window.close()" style="background:#334155; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">
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

<div class="judul-doc">
  <h4>RESUME REKAM JEJAK &amp; PEMBINAAN KEDISIPLINAN SISWA</h4>
  <span>Nomor: 421.5/RESUME-DISIPLIN/{{ \Carbon\Carbon::today()->format('Y/m') }}/{{ str_pad($siswa->id, 3, '0', STR_PAD_LEFT) }}</span>
</div>

{{-- 1. IDENTITAS SISWA --}}
<div class="section-title">1. IDENTITAS SISWA &amp; WALI MURID</div>
<table class="data-table">
  <tr>
    <td style="width:25%;">Nama Lengkap</td>
    <td style="width:2%;">:</td>
    <td style="width:73%;"><strong>{{ $siswa->nama }}</strong></td>
  </tr>
  <tr>
    <td>Nomor Induk Siswa (NIS/NISN)</td>
    <td>:</td>
    <td>{{ $siswa->nis }} / {{ $siswa->nisn ?: '-' }}</td>
  </tr>
  <tr>
    <td>Kelas / Kompetensi Keahlian</td>
    <td>:</td>
    <td>{{ $rombel->nama_rombel ?? '-' }} / {{ $rombel?->jurusan?->nama_jurusan ?? '-' }}</td>
  </tr>
  <tr>
    <td>Wali Kelas</td>
    <td>:</td>
    <td>{{ $wali->nama ?? 'Belum Ditentukan' }} (NIP: {{ $wali->nip ?? '-' }})</td>
  </tr>
  <tr>
    <td>Nama Orang Tua / Wali</td>
    <td>:</td>
    <td>{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua' }} (No. Telp/WA: {{ $siswa->nomor_hp_ortu ?: '-' }})</td>
  </tr>
</table>

{{-- 2. REKAPITULASI PELANGGARAN SISTEM --}}
<div class="section-title">2. REKAPITULASI PELANGGARAN KEDISIPLINAN (DATA SISTEM)</div>
<table class="border-table">
  <thead>
    <tr>
      <th style="width:25%;">Jenis Pelanggaran</th>
      <th style="width:20%; text-align:center;">Jumlah Terdata</th>
      <th style="width:25%; text-align:center;">Bobot Poin</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Ketidakhadiran Tanpa Izin (Alpha)</td>
      <td style="text-align:center; font-weight:bold;">{{ $totalAlpha }} Hari</td>
      <td style="text-align:center;">{{ $totalAlpha * 15 }} Poin</td>
      <td>Evaluasi gerbang &amp; harian sore</td>
    </tr>
    <tr>
      <td>Meninggalkan Sekolah Tanpa Izin (Bolos)</td>
      <td style="text-align:center; font-weight:bold;">{{ $totalBolos }} Kali</td>
      <td style="text-align:center;">{{ $totalBolos * 20 }} Poin</td>
      <td>Tidak tap pulang / dispensasi</td>
    </tr>
    <tr>
      <td>Keterlambatan Hadir</td>
      <td style="text-align:center; font-weight:bold;">{{ $totalTerlambat }} Kali</td>
      <td style="text-align:center;">{{ $totalTerlambat * 5 }} Poin</td>
      <td>Melebihi jam batas toleransi</td>
    </tr>
    <tr style="font-weight:bold; background:#f9f9f9;">
      <td colspan="2">TOTAL AKUMULASI POIN BERSIH</td>
      <td style="text-align:center; color:#000;">{{ $kasus->poin_bersih }} Poin</td>
      <td>Status Tahap: <strong>{{ str_replace('_', ' ', strtoupper($kasus->status_tahap)) }}</strong></td>
    </tr>
  </tbody>
</table>

{{-- 3. KRONOLOGI TAHAPAN PEMBINAAN --}}
<div class="section-title">3. RIWAYAT KRONOLOGIS TAHAPAN PEMBINAAN</div>
<table class="border-table">
  <thead>
    <tr>
      <th style="width:15%;">Tanggal</th>
      <th style="width:25%;">Kegiatan / Tahap</th>
      <th>Uraian Hasil Pembinaan &amp; Kesepakatan</th>
      <th style="width:20%;">Petugas Pelaksana</th>
    </tr>
  </thead>
  <tbody>
    @forelse($kasus->logs as $l)
      <tr>
        <td>{{ \Carbon\Carbon::parse($l->tanggal_kegiatan)->translatedFormat('d/m/Y') }}</td>
        <td><strong>{{ $l->judul_kegiatan }}</strong><br><small>({{ str_replace('_', ' ', strtoupper($l->tahap)) }})</small></td>
        <td>{{ $l->uraian_tindakan }}</td>
        <td>{{ $l->petugas_nama }}<br><small>{{ $l->petugas_role }}</small></td>
      </tr>
    @empty
      <tr>
        <td colspan="4" style="text-align:center; font-style:italic;">Belum ada catatan log interaksi lanjutan.</td>
      </tr>
    @endforelse
  </tbody>
</table>

{{-- 4. BUKTI FISIK TERLAMPIR --}}
<div class="section-title">4. DAFTAR BERKAS BUKTI FISIK DIGITAL TERVERIFIKASI</div>
<table class="data-table">
  @forelse($kasus->dokumens as $idx => $d)
    <tr>
      <td style="width:5%;">[{{ $idx + 1 }}]</td>
      <td style="width:40%;"><strong>{{ $d->judul_dokumen }}</strong></td>
      <td style="width:25%;">Kategori: {{ ucwords(str_replace('_', ' ', $d->kategori)) }}</td>
      <td style="width:30%;">Diupload: {{ $d->created_at->translatedFormat('d F Y') }}</td>
    </tr>
  @empty
    <tr>
      <td colspan="4" style="font-style:italic; color:#555;">Tidak ada berkas bukti fisik terlampir.</td>
    </tr>
  @endforelse
</table>

{{-- 5. LEMBAR PENGESAHAN & TANDA TANGAN --}}
<div class="signature-grid">
  <div class="signature-box">
    <div>Wali Kelas,</div>
    <div class="signature-space"></div>
    <div style="font-weight:bold; text-decoration:underline;">{{ $wali->nama ?? '..................................' }}</div>
    <div>NIP. {{ $wali->nip ?? '..................................' }}</div>
  </div>

  <div class="signature-box">
    <div>Guru Bimbingan &amp; Konseling (BK),</div>
    <div class="signature-space"></div>
    <div style="font-weight:bold; text-decoration:underline;">{{ $kasus->diverifikasi_oleh ?: '..................................' }}</div>
    <div>NIP. ..................................</div>
  </div>

  <div class="signature-box">
    <div>Waka Bidang Kesiswaan,</div>
    <div class="signature-space"></div>
    <div style="font-weight:bold; text-decoration:underline;">..................................</div>
    <div>NIP. ..................................</div>
  </div>

  <div class="signature-box">
    <div>Mengetahui &amp; Mengesahkan,<br>Kepala SMKN 1 Air Naningan</div>
    <div class="signature-space"></div>
    <div style="font-weight:bold; text-decoration:underline;">{{ $sekolah->nama_kepsek ?? '..................................' }}</div>
    <div>NIP. {{ $sekolah->nip_kepsek ?? '..................................' }}</div>
  </div>
</div>

</body>
</html>
