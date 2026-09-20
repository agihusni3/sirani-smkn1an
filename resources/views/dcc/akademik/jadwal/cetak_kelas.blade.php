<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JADWAL PELAJARAN KELAS - SMKN 1 AIR NANINGAN</title>
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
      .page-break { page-break-before: always; }
    }
    .btn-action {
      background: #2563eb;
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

    /* Kop Surat Ringkas */
    .kop-kelas {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 6px;
      margin-bottom: 12px;
    }
    .kop-kelas h2 {
      margin: 0;
      font-size: 14pt;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .kop-kelas h3 {
      margin: 2px 0 0 0;
      font-size: 11pt;
      font-weight: 700;
      color: #334155;
    }

    .info-kelas-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 700;
      font-size: 11pt;
      margin-bottom: 8px;
      padding: 6px 10px;
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      border-radius: 4px;
    }

    .tabel-jadwal {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
      margin-bottom: 14px;
    }
    .tabel-jadwal th, .tabel-jadwal td {
      border: 1px solid #000;
      padding: 4px;
      text-align: center;
      vertical-align: middle;
    }
    .tabel-jadwal th {
      background: #e2e8f0;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 10pt;
    }
    @media print {
      .tabel-jadwal th { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
    }

    .slot-mapel {
      font-weight: 800;
      font-size: 10pt;
      color: #0f172a;
    }
    .slot-guru {
      font-size: 8.5pt;
      color: #334155;
      margin-top: 1px;
    }
    .slot-lab {
      font-size: 8pt;
      background: #0284c7;
      color: #fff;
      padding: 1px 4px;
      border-radius: 3px;
      font-weight: 800;
      display: inline-block;
      margin-top: 2px;
    }

    .tanda-tangan-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      margin-top: 20px;
      font-size: 10pt;
      text-align: center;
    }
  </style>
</head>
<body>

  {{-- Kontrol Atas --}}
  <div class="no-print">
    <div style="display:flex; align-items:center; gap:12px;">
      <span style="font-weight:700;"><i class="bi bi-mortarboard-fill text-primary me-1"></i> Pilih Kelas / Rombel:</span>
      <form action="{{ route('akademik.jadwal.cetak-kelas') }}" method="GET" style="margin:0; display:flex; gap:8px;">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <select name="rombel_id" onchange="this.form.submit()" style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-weight:700;">
          <option value="">-- Cetak Semua Kelas (Multi-Halaman) --</option>
          @foreach($rombels as $r)
            <option value="{{ $r->id }}" {{ $selectedRombelId == $r->id ? 'selected' : '' }}>Kelas {{ $r->nama_rombel }}</option>
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

  @foreach($activeRombels as $rombelIdx => $rombel)
    @if($rombelIdx > 0)
      <div class="page-break"></div>
    @endif

    <div class="kop-kelas">
      <h2>SMK NEGERI 1 AIR NANINGAN</h2>
      <h3>JADWAL PELAJARAN TAHUN {{ $ta?->tahun_ajaran ?? '2025/2026' }} — SEMESTER {{ $semester == 1 ? 'GANJIL' : 'GENAP' }}</h3>
    </div>

    <div class="info-kelas-bar">
      <div>KELAS: <u>{{ $rombel->nama_rombel }}</u></div>
      <div>WALI KELAS: <u>{{ $rombel->waliKelas?->nama ?? 'Belum Ditentukan' }}</u></div>
      <div>BERLAKU: SEMESTER {{ $semester == 1 ? '1 (SATU)' : '2 (DUA)' }}</div>
    </div>

    <table class="tabel-jadwal">
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
          @php
            // Label khusus Senin
            $isSeninUpacara = ($j === 1);
            $isJumatKhusus = ($j > 5);
          @endphp
          <tr>
            <td style="font-weight:800; background:#f8fafc;">{{ $j }}</td>
            <td style="font-size:8.5pt; background:#f8fafc; font-weight:600;">
              {{ $jadwalWaktu['SENIN'][$j] ?? '' }}
            </td>

            {{-- Kolom Hari: SENIN, SELASA, RABU, KAMIS, JUMAT --}}
            @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'] as $hari)
              @php
                $slot = $slotsMatrix[$rombel->id][$hari][$j] ?? null;
              @endphp

              @if($hari === 'SENIN' && $j === 1 && (!$slot || empty($slot->singkatan_mapel)))
                <td style="background:#fee2e2; color:#b91c1c; font-weight:800; font-size:9pt;">
                  UPACARA BENDERA
                </td>
              @elseif($hari === 'JUMAT' && $j > 5)
                <td style="background:#f1f5f9; color:#94a3b8; font-size:8.5pt;">-</td>
              @elseif($slot && ($slot->singkatan_mapel || $slot->kegiatan_khusus))
                <td style="background:#fff;">
                  <div class="slot-mapel">
                    {{ $slot->singkatan_mapel ?? $slot->kegiatan_khusus }}
                  </div>
                  @if($slot->guru)
                    <div class="slot-guru">{{ $slot->guru->nama }}</div>
                  @endif
                  @if($slot->resource_key === 'LAB_KOMPUTER')
                    <span class="slot-lab">🖥️ Lab Komputer</span>
                  @elseif($slot->resource_key)
                    <span class="slot-lab" style="background:#4f46e5;">{{ $slot->resource_key }}</span>
                  @endif
                </td>
              @else
                <td style="color:#cbd5e1; font-size:9pt;">-</td>
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
        <b>Wali Kelas {{ $rombel->nama_rombel }}</b>
        <br><br><br><br>
        <b><u>{{ $rombel->waliKelas?->nama ?? '.........................................' }}</u></b><br>
        NIP. {{ $rombel->waliKelas?->nip ?? '....................................' }}
      </div>
    </div>
  @endforeach

</body>
</html>
