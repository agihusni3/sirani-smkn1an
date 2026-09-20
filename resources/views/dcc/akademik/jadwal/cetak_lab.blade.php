<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JADWAL PENGGUNAAN {{ $resourceLabel }} - SMKN 1 AIR NANINGAN</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @page {
      size: portrait;
      margin: 12mm 12mm;
    }
    * { box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
    body {
      margin: 0;
      padding: 20px;
      font-size: 10.5pt;
      color: #000;
      background: #fff;
    }
    .no-print {
      margin-bottom: 20px;
      padding: 12px 18px;
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    @media print {
      .no-print { display: none !important; }
      body { padding: 0; }
    }
    .btn-action {
      background: #0284c7;
      color: #fff;
      padding: 7px 14px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      border: none;
    }
    .btn-secondary { background: #64748b; }

    .kop-lab {
      text-align: center;
      border-bottom: 2.5px solid #000;
      padding-bottom: 8px;
      margin-bottom: 14px;
    }
    .kop-lab h2 {
      margin: 0;
      font-size: 14pt;
      font-weight: 900;
      letter-spacing: 1px;
    }
    .kop-lab h3 {
      margin: 3px 0 0 0;
      font-size: 12pt;
      font-weight: 800;
      color: #0284c7;
    }

    .tabel-lab {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
      margin-bottom: 14px;
    }
    .tabel-lab th, .tabel-lab td {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
      vertical-align: middle;
    }
    .tabel-lab th {
      background: #e0f2fe;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 10pt;
    }
    @media print {
      .tabel-lab th { background: #bae6fd !important; -webkit-print-color-adjust: exact; }
    }

    .slot-isi {
      background: #f0f9ff;
      border-radius: 4px;
      padding: 4px;
    }
    .slot-kelas {
      font-weight: 900;
      font-size: 11pt;
      color: #0369a1;
    }
    .slot-mapel {
      font-weight: 700;
      font-size: 9pt;
      color: #0f172a;
    }
    .slot-guru {
      font-size: 8pt;
      color: #475569;
    }
    .slot-kosong {
      color: #94a3b8;
      font-size: 9pt;
      font-style: italic;
    }

    .tanda-tangan-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      margin-top: 24px;
      font-size: 10.5pt;
      text-align: center;
    }
  </style>
</head>
<body>

  {{-- Kontrol Navigasi --}}
  <div class="no-print">
    <div style="display:flex; align-items:center; gap:12px;">
      <span style="font-weight:700;"><i class="bi bi-door-open-fill text-primary me-1"></i> Pilih Ruang Praktik / Lab:</span>
      <form action="{{ route('akademik.jadwal.cetak-lab') }}" method="GET" style="margin:0; display:flex; gap:8px;">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <select name="resource_key" onchange="this.form.submit()" style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-weight:700;">
          @foreach(\App\Models\AkademikMataPelajaran::RESOURCES as $rk => $rl)
            <option value="{{ $rk }}" {{ $resourceKey == $rk ? 'selected' : '' }}>{{ $rl }}</option>
          @endforeach
        </select>
      </form>
    </div>
    <div style="display:flex; gap:8px;">
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester]) }}" class="btn-action btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
      <button onclick="window.print()" class="btn-action">
        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
      </button>
    </div>
  </div>

  <div class="kop-lab">
    <h2>SMK NEGERI 1 AIR NANINGAN</h2>
    <h3>JADWAL PENGGUNAAN {{ strtoupper($resourceLabel) }}</h3>
    <div style="font-size:10.5pt; font-weight:600; margin-top:2px;">
      Tahun Pelajaran {{ $ta?->tahun_ajaran ?? '2025/2026' }} — Semester {{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}
    </div>
  </div>

  <table class="tabel-lab">
    <thead>
      <tr>
        <th style="width:45px;">Jam Ke</th>
        <th style="width:75px;">Pukul</th>
        <th style="width:19%;">SENIN</th>
        <th style="width:19%;">SELASA</th>
        <th style="width:19%;">RABU</th>
        <th style="width:19%;">KAMIS</th>
        <th style="width:19%;">JUMAT</th>
      </tr>
    </thead>
    <tbody>
      @for($j = 1; $j <= 11; $j++)
        <tr>
          <td style="font-weight:800; background:#f8fafc;">{{ $j }}</td>
          <td style="font-size:8.5pt; background:#f8fafc; font-weight:600;">
            {{ $jadwalWaktu['SENIN'][$j] ?? '' }}
          </td>

          @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'] as $hari)
            @php
              $slot = $labMatrix[$hari][$j] ?? null;
            @endphp

            @if($slot && $slot->rombel)
              <td style="background:#f0f9ff;">
                <div class="slot-isi">
                  <div class="slot-kelas">{{ $slot->rombel->nama_rombel }}</div>
                  <div class="slot-mapel">{{ $slot->singkatan_mapel ?? $slot->mataPelajaran?->nama_mapel }}</div>
                  @if($slot->guru)
                    <div class="slot-guru">{{ $slot->guru->nama }}</div>
                  @endif
                </div>
              </td>
            @elseif($hari === 'JUMAT' && $j > 5)
              <td style="background:#f8fafc; color:#cbd5e1;">-</td>
            @else
              <td class="slot-kosong">Tersedia</td>
            @endif
          @endforeach
        </tr>
      @endfor
    </tbody>
  </table>

  {{-- Tanda Tangan --}}
  <div class="tanda-tangan-grid">
    <div>
      Mengetahui,<br>
      <b>Kepala SMKN 1 Air Naningan</b>
      <br><br><br><br>
      <b><u>{{ $sekolah->nama_kepala_sekolah ?? 'Aprida, S.Si.' }}</u></b><br>
      NIP. {{ $sekolah->nip_kepala_sekolah ?? '197904172008012019' }}
    </div>
    <div>
      Air Naningan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
      <b>Pengelola / Kepala Laboratorium</b>
      <br><br><br><br>
      <b><u>.....................................................</u></b><br>
      NIP. .................................................
    </div>
  </div>

</body>
</html>
