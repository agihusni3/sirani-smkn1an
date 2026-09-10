<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lembar Disposisi — Agenda #{{ str_pad((string)$surat->nomor_agenda, 3, '0', STR_PAD_LEFT) }}/{{ $surat->tahun_agenda }}</title>
  <style>
    @page {
      size: A5 portrait;
      margin: 0;
    }
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 10.5pt;
      color: #000;
      background: #fff;
      margin: 0;
      padding: 10mm 12mm;
    }
    .judul-lembar {
      text-align: center;
      font-weight: bold;
      font-size: 12pt;
      text-transform: uppercase;
      text-decoration: underline;
      margin-bottom: 12px;
      letter-spacing: 1px;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
    }
    table.data-table th, table.data-table td {
      border: 1px solid #000;
      padding: 5px 8px;
      font-size: 10pt;
      vertical-align: top;
    }
    .checkbox-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px 10px;
      font-size: 9.5pt;
    }
    .checkbox-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .box-check {
      width: 12px;
      height: 12px;
      border: 1px solid #000;
      display: inline-block;
      text-align: center;
      line-height: 11px;
      font-weight: bold;
      font-size: 9px;
    }
    .ttd-wrap {
      float: right;
      width: 200px;
      text-align: center;
      margin-top: 10px;
      font-size: 10pt;
    }
    .no-print-bar {
      background: #1e293b;
      color: #fff;
      padding: 10px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      border-radius: 6px;
      font-family: sans-serif;
      font-size: 12px;
    }
    @media print {
      .no-print-bar { display: none; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

  <div class="no-print-bar">
    <div><strong>Pratinjau Lembar Disposisi Resmi (Format A5)</strong> · SMKN 1 Air Naningan</div>
    <div>
      <button type="button" onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:bold;">
        🖨️ Cetak Lembar Disposisi
      </button>
    </div>
  </div>

  {{-- KOP SURAT RESMI STANDAR --}}
  @include('partials.kop_surat', ['compact' => true, 'sekolah' => $sekolah])

  <div class="judul-lembar">LEMBAR DISPOSISI KEPALA SEKOLAH</div>

  <table class="data-table">
    <tr>
      <td style="width:50%;">
        <strong>Nomor Agenda:</strong> #{{ str_pad((string)$surat->nomor_agenda, 3, '0', STR_PAD_LEFT) }}/{{ $surat->tahun_agenda }}
      </td>
      <td style="width:50%;">
        <strong>Tingkat Urgensi:</strong> {{ strtoupper($surat->tingkat_urgensi) }}
      </td>
    </tr>
    <tr>
      <td>
        <strong>Tanggal Penerimaan:</strong> {{ $surat->tanggal_diterima->translatedFormat('d F Y') }}
      </td>
      <td>
        <strong>Tanggal Surat Asal:</strong> {{ $surat->tanggal_surat->translatedFormat('d F Y') }}
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <strong>Nomor Surat Pengirim:</strong> {{ $surat->nomor_surat_asal }}
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <strong>Asal Instansi / Pengirim:</strong> {{ $surat->pengirim }}
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <strong>Perihal / Isi Ringkas:</strong><br>
        <span style="font-size:10.5pt; font-weight:bold;">{{ $surat->perihal }}</span>
      </td>
    </tr>
  </table>

  @php
    $disposisi = $surat->disposisis->first();
    $flags = $disposisi ? ($disposisi->instruksi_flags ?? []) : [];
  @endphp

  <table class="data-table">
    <tr style="background:#f8fafc;">
      <td style="width:50%; font-weight:bold;">DITERUSKAN KEPADA:</td>
      <td style="width:50%; font-weight:bold;">PETUNJUK / INSTRUKSI KEPSEK:</td>
    </tr>
    <tr>
      <td>
        <div style="font-size:10.5pt; font-weight:bold; color:#0f172a; margin-bottom:8px;">
          {{ $disposisi ? $disposisi->penerima?->name : '........................................................' }}
        </div>
        <div style="font-size:9pt; color:#475569;">
          Jabatan: {{ $disposisi ? strtoupper(str_replace('_', ' ', $disposisi->penerima?->role)) : 'Waka / Staf' }}
        </div>
        @if($disposisi && $disposisi->batas_waktu)
          <div style="margin-top:10px; font-size:9pt;">
            <strong>Batas Waktu Selesai:</strong> {{ \Carbon\Carbon::parse($disposisi->batas_waktu)->translatedFormat('d F Y') }}
          </div>
        @endif
      </td>
      <td>
        <div class="checkbox-grid">
          <div class="checkbox-item"><span class="box-check">{{ in_array('tindak_lanjuti', $flags) ? '✓' : '' }}</span> Tindak Lanjuti</div>
          <div class="checkbox-item"><span class="box-check">{{ in_array('pelajari', $flags) ? '✓' : '' }}</span> Pelajari / Telaah</div>
          <div class="checkbox-item"><span class="box-check">{{ in_array('hadiri_wakilkan', $flags) ? '✓' : '' }}</span> Hadiri / Wakilkan</div>
          <div class="checkbox-item"><span class="box-check">{{ in_array('siapkan_laporan', $flags) ? '✓' : '' }}</span> Siapkan Laporan</div>
          <div class="checkbox-item"><span class="box-check">{{ in_array('koordinasikan', $flags) ? '✓' : '' }}</span> Koordinasikan</div>
          <div class="checkbox-item"><span class="box-check">{{ in_array('arsipkan', $flags) ? '✓' : '' }}</span> Arsipkan / Ketahui</div>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <strong>Catatan Khusus Kepala Sekolah:</strong>
        <div style="min-height:45px; padding-top:4px; font-style:italic;">
          {{ $disposisi?->catatan_kepsek ?: '(Tidak ada catatan tambahan)' }}
        </div>
      </td>
    </tr>
  </table>

  <div class="ttd-wrap">
    Air Naningan, {{ $surat->tanggal_diterima ? $surat->tanggal_diterima->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
    Kepala SMKN 1 Air Naningan,<br><br><br><br>
    <strong><u>{{ !empty($sekolah?->nama_kepala_sekolah) ? $sekolah->nama_kepala_sekolah : ($disposisi?->pemberi?->name ?: 'Kepala Sekolah') }}</u></strong><br>
    NIP. {{ !empty($sekolah?->nip_kepala_sekolah) ? $sekolah->nip_kepala_sekolah : '-' }}
  </div>

</body>
</html>
