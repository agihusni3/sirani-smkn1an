<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Perangkat Pembelajaran — {{ $perangkat->mataPelajaran?->nama_mapel }}</title>
  <style>
    @page { size: A4 portrait; margin: 15mm 18mm 15mm 20mm; }
    * { box-sizing: border-box; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.45; color: #000; margin: 0; padding: 0; }
    .kop-surat { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
    .kop-logo { width: 65px; height: 65px; margin-right: 10px; }
    .kop-text { flex: 1; text-align: center; line-height: 1.2; }
    .kop-instansi { font-size: 10.5pt; font-weight: bold; text-transform: uppercase; }
    .kop-sekolah { font-size: 13pt; font-weight: 900; text-transform: uppercase; margin: 3px 0; }
    .kop-alamat { font-size: 8.5pt; font-style: italic; }
    .section-title { text-align: center; font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 20px 0 10px 0; }
    .sub-title { font-size: 10.5pt; font-weight: bold; background: #f0f0f0; padding: 4px 8px; border-left: 4px solid #1e3a5f; margin: 14px 0 6px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table th, table td { border: 1px solid #000; padding: 5px 8px; font-size: 10pt; vertical-align: top; }
    table th { background: #dce8f5; font-weight: bold; text-align: center; }
    .tabel-info td { border: none !important; padding: 2px 6px; font-size: 10.5pt; }
    .area-ttd td { border: none !important; font-size: 10.5pt; padding: 3px; vertical-align: top; }
    .page-break { page-break-after: always; }
    .narasi { text-align: justify; text-indent: 28px; margin-bottom: 10px; }
    .qr-box { border: 1px solid #888; padding: 6px 10px; display: inline-block; text-align: center; font-size: 7.5pt; }
  </style>
</head>
<body>

{{-- SAMPUL --}}
<div style="text-align:center; padding: 30px 20px;">
  <table class="tabel-info" style="border:none !important;">
    <tr>
      <td style="text-align:center; padding:0 !important; border:none !important;">
        <div style="font-size:11pt; font-weight:bold; text-transform:uppercase;">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
        <div style="font-size:16pt; font-weight:900; text-transform:uppercase; margin:6px 0;">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
        <div style="font-size:9pt; font-style:italic;">{{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung' }}</div>
        <div style="border-bottom: 3px double #000; margin: 10px 0;"></div>
      </td>
    </tr>
  </table>

  <div style="margin: 50px 0 30px 0;">
    <div style="font-size:15pt; font-weight:bold; text-transform:uppercase; margin-bottom:6px;">PERANGKAT PEMBELAJARAN</div>
    <div style="font-size:12pt; margin-bottom:4px;">Kurikulum Merdeka (Kepmendikbudristek No. 12/2024)</div>
    <div style="border: 2px solid #000; padding: 10px 30px; margin: 16px auto; display:inline-block;">
      <div style="font-size:16pt; font-weight:900;">{{ $perangkat->mataPelajaran?->nama_mapel }}</div>
      <div style="font-size:11pt;">Kelas {{ $perangkat->tingkat }} / Fase {{ $perangkat->fase }}</div>
      <div style="font-size:10.5pt;">Semester {{ $perangkat->semester == 1 ? 'Ganjil' : 'Genap' }}</div>
      <div style="font-size:10pt;">Tahun Ajaran {{ $perangkat->tahunAjaran?->nama ?? date('Y') . '/' . (date('Y')+1) }}</div>
    </div>
  </div>

  <table class="tabel-info" style="max-width:420px; margin:0 auto 24px auto; border:none !important;">
    <tr><td style="width:150px; border:none!important;">Guru Pengampu</td><td style="width:10px; border:none!important;">:</td><td style="border:none!important;"><strong>{{ $perangkat->guru?->nama }}</strong></td></tr>
    <tr><td style="border:none!important;">NIP / NUPTK</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->guru?->nip ?? '-' }}</td></tr>
    <tr><td style="border:none!important;">Mata Pelajaran</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->mataPelajaran?->nama_mapel }}</td></tr>
  </table>
</div>
<div class="page-break"></div>

{{-- LEMBAR PENGESAHAN --}}
<div class="kop-surat">
  <div class="kop-text">
    <div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
    <div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div>
    <div class="kop-alamat">{{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung' }}</div>
  </div>
</div>

<div class="section-title">LEMBAR PENGESAHAN PERANGKAT PEMBELAJARAN<br>
  <span style="font-size:11pt;">TAHUN AJARAN {{ $perangkat->tahunAjaran?->nama ?? '2026/2027' }}</span>
</div>

<div class="narasi">Setelah melalui proses telaah, verifikasi internal, dan supervisi akademik oleh Tim Pengembang Kurikulum {{ $sekolah->nama_sekolah ?? 'SMK Negeri 1 Air Naningan' }}, maka Perangkat Pembelajaran Kurikulum Merdeka (Kepmendikbudristek No. 12/2024 dan BSKAP No. 032/H/KR/2024) yang disusun oleh:</div>

<table class="tabel-info">
  <tr><td style="width:30px;border:none!important;">1.</td><td style="width:220px;border:none!important;">Nama Guru Pengampu</td><td style="width:12px;border:none!important;">:</td><td style="border:none!important;"><strong>{{ $perangkat->guru?->nama }}</strong></td></tr>
  <tr><td style="border:none!important;">2.</td><td style="border:none!important;">NIP / NUPTK</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->guru?->nip ?? '-' }}</td></tr>
  <tr><td style="border:none!important;">3.</td><td style="border:none!important;">Mata Pelajaran</td><td style="border:none!important;">:</td><td style="border:none!important;"><strong>{{ $perangkat->mataPelajaran?->nama_mapel }}</strong> ({{ $perangkat->mataPelajaran?->kode_mapel }})</td></tr>
  <tr><td style="border:none!important;">4.</td><td style="border:none!important;">Tingkat / Fase</td><td style="border:none!important;">:</td><td style="border:none!important;">Kelas {{ $perangkat->tingkat }} / Fase {{ $perangkat->fase }}</td></tr>
  <tr><td style="border:none!important;">5.</td><td style="border:none!important;">Semester</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->semester == 1 ? 'Ganjil (Satu)' : 'Genap (Dua)' }}</td></tr>
</table>

<table>
  <thead>
    <tr><th style="width:35px;">No</th><th>Komponen Perangkat Pembelajaran</th><th style="width:160px;">Keterangan</th><th style="width:100px;">Status</th></tr>
  </thead>
  <tbody>
    <tr><td style="text-align:center;">1</td><td>Capaian Pembelajaran (CP) &amp; Elemen</td><td style="text-align:center;">Fase {{ $perangkat->fase }}</td><td style="text-align:center;font-weight:bold;">Lengkap</td></tr>
    <tr><td style="text-align:center;">2</td><td>Alur Tujuan Pembelajaran (ATP)</td><td style="text-align:center;">{{ $atpItems->count() }} Butir ({{ $atpItems->sum('alokasi_jp') }} JP)</td><td style="text-align:center;font-weight:bold;">Lengkap</td></tr>
    <tr><td style="text-align:center;">3</td><td>Program Tahunan &amp; Program Semester</td><td style="text-align:center;">{{ $perangkat->rpe_pekan_efektif ?? 18 }} Pekan</td><td style="text-align:center;font-weight:bold;">Lengkap</td></tr>
    <tr><td style="text-align:center;">4</td><td>Modul Ajar / RPP Merdeka &amp; LKPD</td><td style="text-align:center;">{{ $modulAjars->count() }} Modul</td><td style="text-align:center;font-weight:bold;">Lengkap</td></tr>
    <tr><td style="text-align:center;">5</td><td>Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)</td><td style="text-align:center;">{{ $kktpItems->count() }} Kriteria</td><td style="text-align:center;font-weight:bold;">Lengkap</td></tr>
  </tbody>
</table>

<div class="area-ttd">
  <table>
    <tr>
      <td style="width:50%; border:none!important;">Mengetahui / Menyetujui:<br><strong>Waka Bidang Kurikulum</strong><br><br><br><br><strong style="text-decoration:underline;">{{ $sekolah->nama_waka_kurikulum ?? 'SUPRAPTO, S.Pd.' }}</strong><br>NIP. {{ $sekolah->nip_waka_kurikulum ?? '19820514 200902 1 003' }}</td>
      <td style="width:50%; padding-left:20px; border:none!important;">Air Naningan, {{ now()->translatedFormat('d F Y') }}<br>Guru Mata Pelajaran,<br><br><br><br><strong style="text-decoration:underline;">{{ $perangkat->guru?->nama }}</strong><br>NIP. {{ $perangkat->guru?->nip ?? '-' }}</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center; padding-top:24px; border:none!important;">Mengesahkan:<br><strong>Kepala {{ $sekolah->nama_sekolah ?? 'SMK Negeri 1 Air Naningan' }}</strong><br><br><br><br><strong style="text-decoration:underline; font-size:12pt;">{{ $sekolah->nama_kepala_sekolah ?? 'APRIDA, S.Pd., M.M.' }}</strong><br>Pembina Tk. I / IV.b<br>NIP. {{ $sekolah->nip_kepala_sekolah ?? '19750412 200501 2 007' }}</td>
    </tr>
  </table>
</div>
<div class="page-break"></div>

{{-- BAB I: CP --}}
<div class="kop-surat"><div class="kop-text"><div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div><div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div></div></div>
<div class="section-title">BAB I — CAPAIAN PEMBELAJARAN (CP)</div>

<div class="sub-title">A. Identitas Mata Pelajaran</div>
<table class="tabel-info">
  <tr><td style="width:200px;border:none!important;">Mata Pelajaran</td><td style="width:12px;border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->mataPelajaran?->nama_mapel }}</td></tr>
  <tr><td style="border:none!important;">Kode Mapel</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->mataPelajaran?->kode_mapel }}</td></tr>
  <tr><td style="border:none!important;">Kelas / Fase</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $perangkat->tingkat }} / Fase {{ $perangkat->fase }}</td></tr>
</table>

<div class="sub-title">B. Capaian Pembelajaran</div>
<div style="padding:8px 12px; border:1px solid #ccc; margin-bottom:12px; background:#fafafa; font-size:10pt;">{!! format_narasi_kbm($perangkat->capaian_pembelajaran ?? $perangkat->mataPelajaran?->capaian_pembelajaran_fase_e ?? '—', ['line_height' => '1.5', 'margin_bottom' => '8px']) !!}</div>

@if($perangkat->rasional_tujuan)
<div class="sub-title">C. Rasional &amp; Tujuan</div>
<div style="padding:8px 12px; border:1px solid #ccc; margin-bottom:12px; background:#fafafa; font-size:10pt;">{!! format_narasi_kbm($perangkat->rasional_tujuan, ['line_height' => '1.5', 'margin_bottom' => '8px']) !!}</div>
@endif

@php $elemenCp = is_array($perangkat->elemen_cp) ? $perangkat->elemen_cp : (json_decode($perangkat->elemen_cp ?? '[]', true) ?? []); @endphp
@if(count($elemenCp) > 0)
<div class="sub-title">D. Elemen Kompetensi CP</div>
<table>
  <thead><tr><th style="width:35px;">No</th><th style="width:180px;">Nama Elemen</th><th>Deskripsi</th></tr></thead>
  <tbody>
    @foreach($elemenCp as $i => $el)
    <tr><td style="text-align:center;">{{ $i+1 }}</td><td><strong>{{ $el['nama'] ?? '-' }}</strong></td><td>{{ $el['deskripsi'] ?? '-' }}</td></tr>
    @endforeach
  </tbody>
</table>
@endif
<div class="page-break"></div>

{{-- BAB II: ATP --}}
<div class="kop-surat"><div class="kop-text"><div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div><div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div></div></div>
<div class="section-title">BAB II — ALUR TUJUAN PEMBELAJARAN (ATP)</div>
<div class="sub-title">{{ $perangkat->mataPelajaran?->nama_mapel }} | Kelas {{ $perangkat->tingkat }} Semester {{ $perangkat->semester == 1 ? 'Ganjil' : 'Genap' }}</div>

@if($atpItems->count() > 0)
<table>
  <thead><tr><th style="width:35px;">No</th><th style="width:70px;">Kode TP</th><th>Tujuan Pembelajaran</th><th>Materi Pokok</th><th style="width:45px;">JP</th></tr></thead>
  <tbody>
    @foreach($atpItems as $atp)
    <tr>
      <td style="text-align:center;">{{ $loop->iteration }}</td>
      <td style="text-align:center;"><strong>{{ $atp->kode_tp }}</strong></td>
      <td>{{ $atp->tujuan_pembelajaran }}</td>
      <td>{{ $atp->materi_pokok }}</td>
      <td style="text-align:center;">{{ $atp->alokasi_jp }}</td>
    </tr>
    @endforeach
    <tr><td colspan="4" style="text-align:right;font-weight:bold;">Total JP:</td><td style="text-align:center;font-weight:bold;">{{ $atpItems->sum('alokasi_jp') }}</td></tr>
  </tbody>
</table>
@else
<p style="text-align:center; color:#666; font-style:italic;">Belum ada butir ATP.</p>
@endif
<div class="page-break"></div>

{{-- BAB III: PROTA/PROMES --}}
<div class="kop-surat"><div class="kop-text"><div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div><div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div></div></div>
<div class="section-title">BAB III — PROGRAM TAHUNAN &amp; PROGRAM SEMESTER</div>

@php
  $rpeEfektif  = $perangkat->rpe_pekan_efektif ?? 18;
  $rpeCadangan = $perangkat->rpe_pekan_cadangan ?? 2;
  $jamMinggu   = $perangkat->distribusiMengajar?->total_jam_per_minggu ?? 4;
  $totalJpSmt  = $rpeEfektif * $jamMinggu;
@endphp
<div class="sub-title">A. Rincian Pekan Efektif (RPE)</div>
<table class="tabel-info">
  <tr><td style="width:260px;border:none!important;">Pekan Efektif</td><td style="width:12px;border:none!important;">:</td><td style="border:none!important;">{{ $rpeEfektif }} pekan</td></tr>
  <tr><td style="border:none!important;">Pekan Cadangan / Penilaian</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $rpeCadangan }} pekan</td></tr>
  <tr><td style="border:none!important;">JP per Minggu</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $jamMinggu }} JP</td></tr>
  <tr><td style="border:none!important;"><strong>Total JP Tersedia Semester Ini</strong></td><td style="border:none!important;">:</td><td style="border:none!important;"><strong>{{ $totalJpSmt }} JP</strong></td></tr>
  <tr><td style="border:none!important;">Total JP Terpakai (ATP)</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $atpItems->sum('alokasi_jp') }} JP</td></tr>
</table>

<div class="sub-title">B. Program Semester — Distribusi Materi</div>
@if($atpItems->count() > 0)
<table>
  <thead><tr><th style="width:35px;">No</th><th>Materi Pokok</th><th style="width:45px;">JP</th><th style="width:90px;">Perkiraan Bulan</th></tr></thead>
  <tbody>
    @foreach($atpItems as $atp)
    <tr>
      <td style="text-align:center;">{{ $loop->iteration }}</td>
      <td>{{ $atp->materi_pokok }}<br><small>{{ $atp->kode_tp }}: {{ \Illuminate\Support\Str::limit($atp->tujuan_pembelajaran, 70) }}</small></td>
      <td style="text-align:center;">{{ $atp->alokasi_jp }}</td>
      <td style="text-align:center;">—</td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
<p style="text-align:center; color:#666; font-style:italic;">Isi ATP terlebih dahulu untuk menghasilkan distribusi promes.</p>
@endif
<div class="page-break"></div>

{{-- BAB IV: MODUL AJAR --}}
<div class="kop-surat"><div class="kop-text"><div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div><div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div></div></div>
<div class="section-title">BAB IV — MODUL AJAR / RPP MERDEKA</div>

@if($modulAjars->count() > 0)
  @foreach($modulAjars as $modul)
  <div class="sub-title">Modul {{ $loop->iteration }}: {{ $modul->judul_modul }}</div>
  <table class="tabel-info">
    <tr><td style="width:180px;border:none!important;">Pertemuan Ke</td><td style="width:12px;border:none!important;">:</td><td style="border:none!important;">{{ $modul->pertemuan_ke_mulai }}–{{ $modul->pertemuan_ke_selesai }}</td></tr>
    <tr><td style="border:none!important;">Alokasi Waktu</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $modul->alokasi_jp }} JP</td></tr>
    <tr><td style="border:none!important;">Model Pembelajaran</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $modul->model_pembelajaran ?? '-' }}</td></tr>
    <tr><td style="border:none!important;">Metode Pembelajaran</td><td style="border:none!important;">:</td><td style="border:none!important;">{{ $modul->metode_pembelajaran ?? '-' }}</td></tr>
  </table>
  @if($modul->pemahaman_bermakna) <div class="sub-title" style="font-size:9.5pt;">Pemahaman Bermakna</div><div style="padding:6px 10px; border:1px solid #ccc; margin-bottom:8px; font-size:9.5pt;">{!! format_narasi_kbm($modul->pemahaman_bermakna, ['line_height' => '1.5', 'margin_bottom' => '6px']) !!}</div> @endif
  @if($modul->pertanyaan_pemantik) <div class="sub-title" style="font-size:9.5pt;">Pertanyaan Pemantik</div><div style="padding:6px 10px; border:1px solid #ccc; margin-bottom:8px; font-size:9.5pt;">{!! format_narasi_kbm($modul->pertanyaan_pemantik, ['line_height' => '1.5', 'margin_bottom' => '6px']) !!}</div> @endif
  @if($modul->kegiatan_pendahuluan) <div class="sub-title" style="font-size:9.5pt;">Kegiatan Pendahuluan</div><div style="padding:6px 10px; border:1px solid #ccc; margin-bottom:8px; font-size:9.5pt;">{!! format_narasi_kbm($modul->kegiatan_pendahuluan, ['line_height' => '1.5', 'margin_bottom' => '6px']) !!}</div> @endif
  @if($modul->kegiatan_inti) <div class="sub-title" style="font-size:9.5pt;">Kegiatan Inti</div><div style="padding:6px 10px; border:1px solid #ccc; margin-bottom:8px; font-size:9.5pt;">{!! format_narasi_kbm($modul->kegiatan_inti, ['line_height' => '1.5', 'margin_bottom' => '6px']) !!}</div> @endif
  @if($modul->kegiatan_penutup) <div class="sub-title" style="font-size:9.5pt;">Kegiatan Penutup</div><div style="padding:6px 10px; border:1px solid #ccc; margin-bottom:8px; font-size:9.5pt;">{!! format_narasi_kbm($modul->kegiatan_penutup, ['line_height' => '1.5', 'margin_bottom' => '6px']) !!}</div> @endif
  @if(!$loop->last) <div class="page-break"></div> @endif
  @endforeach
@else
<p style="text-align:center; color:#666; font-style:italic;">Belum ada Modul Ajar yang diisi.</p>
@endif
<div class="page-break"></div>

{{-- BAB V: KKTP --}}
<div class="kop-surat"><div class="kop-text"><div class="kop-instansi">PEMERINTAH PROVINSI LAMPUNG — DINAS PENDIDIKAN DAN KEBUDAYAAN</div><div class="kop-sekolah">{{ $sekolah->nama_sekolah ?? 'SMK NEGERI 1 AIR NANINGAN' }}</div></div></div>
<div class="section-title">BAB V — KRITERIA KETERCAPAIAN TUJUAN PEMBELAJARAN (KKTP)</div>

@if($kktpItems->count() > 0)
<table>
  <thead><tr><th style="width:35px;">No</th><th>Tujuan Pembelajaran</th><th style="width:100px;">Pendekatan</th><th>Kriteria Tuntas</th><th>Rencana Remedial</th></tr></thead>
  <tbody>
    @foreach($kktpItems as $kktp)
    <tr>
      <td style="text-align:center;">{{ $loop->iteration }}</td>
      <td>{{ $kktp->atpItem?->kode_tp }}: {{ \Illuminate\Support\Str::limit($kktp->atpItem?->tujuan_pembelajaran, 80) }}</td>
      <td style="text-align:center; text-transform:capitalize;">{{ str_replace('_', ' ', $kktp->pendekatan) }}</td>
      <td>{{ $kktp->keterangan_tuntas ?? '—' }}</td>
      <td>{{ $kktp->keterangan_remedial ?? '—' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
<p style="text-align:center; color:#666; font-style:italic;">Belum ada KKTP yang diisi.</p>
@endif

</body>
</html>
