<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; color: #000000; }
    .kop-title { font-size: 14pt; font-weight: bold; text-align: center; }
    .kop-sub { font-size: 10pt; text-align: center; }
    .title-doc { font-size: 12pt; font-weight: bold; text-align: center; text-decoration: underline; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th { background-color: #dbeafe; border: 1px solid #000000; font-weight: bold; text-align: center; font-size: 10pt; height: 32px; vertical-align: middle; }
    td { border: 1px solid #000000; padding: 6px 8px; vertical-align: middle; font-size: 9.5pt; }
    .col-center { text-align: center; }
    .col-text { mso-number-format: '\@'; text-align: center; font-weight: bold; }
    .col-barcode { text-align: center; vertical-align: middle; width: 120px; }
  </style>
</head>
<body>

  <table style="border:none; margin-bottom: 10px;">
    <tr>
      <td colspan="5" style="border:none;" class="kop-title">
        {{ !empty($sekolah->nama_sekolah) ? strtoupper($sekolah->nama_sekolah) : 'SMK NEGERI 1 AIR NANINGAN' }}
      </td>
    </tr>
    <tr>
      <td colspan="5" style="border:none;" class="kop-sub">
        {{ $sekolah->alamat ?? 'Kecamatan Air Naningan, Kabupaten Tanggamus, Lampung' }}
      </td>
    </tr>
    <tr>
      <td colspan="5" style="border:none; height: 10px;"></td>
    </tr>
    <tr>
      <td colspan="5" style="border:none;" class="title-doc">
        DAFTAR BARCODE 2D (QR CODE) PRESENSI SISWA
      </td>
    </tr>
    <tr>
      <td colspan="5" style="border:none; text-align: center; font-size: 9pt;">
        Kelas / Rombel: {{ $rombel ? $rombel->nama_rombel : 'Semua Rombel Terpilih' }} | Total: {{ count($siswas) }} Siswa | Tanggal: {{ date('d/m/Y') }}
      </td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th style="width: 45px;">No</th>
        <th style="width: 120px;">NISN</th>
        <th style="width: 250px;">Nama Lengkap Siswa</th>
        <th style="width: 130px;">Rombel / Kelas</th>
        <th style="width: 130px;">Barcode 2D (QR Code)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($siswas as $idx => $s)
        @php
          $nisnClean = !empty($s->nisn) ? trim($s->nisn) : (!empty($s->nis) ? trim($s->nis) : (string)$s->id);
          $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? '-';
          $qrDataUri = \App\Services\BarcodeService::getBarcode2DDataUri($nisnClean, 3);
        @endphp
        <tr>
          <td class="col-center">{{ $idx + 1 }}</td>
          <td class="col-text">{{ $s->nisn ?: '-' }}</td>
          <td style="font-weight: bold; text-transform: uppercase;">{{ $s->nama }}</td>
          <td class="col-center">{{ $rombelNama }}</td>
          <td class="col-barcode">
            @if($qrDataUri)
              <img src="{{ $qrDataUri }}" width="62" height="62" alt="QR {{ $nisnClean }}" /><br/>
              <span style="font-size: 8pt; font-family: monospace; font-weight: bold;">{{ $nisnClean }}</span>
            @else
              <span>-</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="col-center" style="padding: 20px;">Tidak ada data siswa ditemukan.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

</body>
</html>
