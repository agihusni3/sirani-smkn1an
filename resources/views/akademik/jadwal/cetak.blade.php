<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JADWAL PELAJARAN SMKN 1 AIR NANINGAN - T.A {{ $ta?->tahun_ajaran ?? '2025/2026' }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @page {
      size: landscape;
      margin: 8mm 6mm;
    }
    * {
      box-sizing: border-box;
      font-family: Arial, Helvetica, sans-serif;
    }
    body {
      margin: 0;
      padding: 10px;
      font-size: 11px;
      color: #000;
      background: #fff;
    }
    .no-print {
      margin-bottom: 12px;
      padding: 10px 14px;
      background: #f1f5f9;
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
      background: #4f46e5;
      color: #fff;
      padding: 8px 16px;
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
    .btn-secondary {
      background: #e2e8f0;
      color: #334155;
    }
    .header-title {
      text-align: center;
      font-weight: 900;
      font-size: 14px;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
      text-transform: uppercase;
    }
    .main-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5px;
      border: 1.5px solid #000;
    }
    .main-table th, .main-table td {
      border: 1px solid #000;
      padding: 2px 3px;
      text-align: center;
      vertical-align: middle;
    }
    .main-table th {
      font-weight: 800;
      text-transform: uppercase;
    }
    .th-hari { background: #86efac; } /* Hijau muda */
    .th-jam { background: #93c5fd; }
    .th-pukul { background: #93c5fd; }
    .th-aphp { background: #fef08a; } /* Kuning muda */
    .th-rpl { background: #bae6fd; } /* Biru muda */
    .th-tsm { background: #fed7aa; } /* Orange muda */
    .th-piket { background: #86efac; }
    .th-legend { background: #60a5fa; color: #fff; }

    .bg-upacara { background: #ef4444; color: #fff; font-weight: 800; font-size: 9.5px; letter-spacing: 0.5px; }
    .bg-apel { background: #ef4444; color: #fff; font-weight: 800; font-size: 9px; }
    .bg-istirahat { background: #dc2626; color: #fff; font-weight: 800; font-size: 9px; }
    .bg-lampung { background: #ef4444; color: #fff; font-weight: 800; font-size: 9px; }
    .bg-pkl { background: #fef08a; color: #000; font-weight: 900; font-size: 10px; writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 2px; }

    .cell-slot {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 3px;
      width: 100%;
      height: 100%;
      min-height: 16px;
    }
    .kode-guru-box {
      display: inline;
      font-weight: 900;
      font-size: 9px;
      margin-right: 3px;
      background: transparent;
      border: none;
    }
    .mapel-text {
      font-weight: 800;
      font-size: 8.5px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 70px;
    }

    .piket-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8.5px;
      text-align: left;
    }
    .piket-table td {
      border: none;
      padding: 1px 2px;
      vertical-align: top;
    }
    .legend-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8px;
    }
    .legend-table th, .legend-table td {
      border: 1px solid #64748b;
      padding: 1px 3px;
    }
    .signature-box {
      margin-top: 15px;
      float: right;
      text-align: center;
      font-size: 10px;
      width: 250px;
    }
  </style>
</head>
<body>

  {{-- Action bar for browser --}}
  <div class="no-print">
    <div>
      <a href="{{ route('akademik.jadwal.index') }}" class="btn-action btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali ke Aplikasi
      </a>
      <span style="font-size:13px; font-weight:700; margin-left:14px;">
        Jadwal Pelajaran SMKN 1 Air Naningan - Semester {{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }} T.A {{ $ta?->tahun_ajaran ?? '2025/2026' }}
      </span>
    </div>
    <div>
      <button onclick="window.print()" class="btn-action">
        <i class="bi bi-printer"></i> Cetak / Simpan PDF
      </button>
    </div>
  </div>

  <div class="header-title">
    JADWAL PELAJARAN SMKN 1 AIR NANINGAN TAHUN AJARAN {{ $ta?->tahun_ajaran ?? '2025/2026' }}
  </div>

  <table class="main-table">
    <thead>
      <tr>
        <th rowspan="2" class="th-hari" style="width:40px;">HARI</th>
        <th rowspan="2" class="th-jam" style="width:30px;">JAM KE</th>
        <th rowspan="2" class="th-pukul" style="width:70px;">PUKUL</th>
        <th colspan="3" class="th-aphp" style="background:#fef08a;">KELAS X / PROGRAM KEAHLIAN</th>
        <th colspan="3" class="th-aphp" style="background:#fef08a;">KELAS XI / PROGRAM KEAHLIAN</th>
        <th colspan="2" class="th-aphp" style="background:#fef08a;">KELAS XII / PROGRAM KEAHLIAN</th>
        <th rowspan="2" class="th-piket" style="width:130px;">GURU PIKET</th>
        <th rowspan="2" class="th-legend" style="width:20px;">NO</th>
        <th rowspan="2" class="th-legend" style="width:140px;">DAFTAR NAMA GURU</th>
      </tr>
      <tr>
        <th class="th-aphp" style="width:75px;">APHP</th>
        <th class="th-rpl" style="width:75px;">RPL</th>
        <th class="th-tsm" style="width:75px;">TSM</th>
        <th class="th-aphp" style="width:75px;">APHP</th>
        <th class="th-rpl" style="width:75px;">RPL</th>
        <th class="th-tsm" style="width:75px;">TSM</th>
        <th class="th-aphp" style="width:45px;">APHP</th>
        <th class="th-rpl" style="width:45px;">RPL</th>
      </tr>
    </thead>
    <tbody>
      @php
        $days = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'];
        $rombelX_APHP = $rombels->firstWhere('nama_rombel', 'X APHP');
        $rombelX_RPL = $rombels->firstWhere('nama_rombel', 'X RPL');
        $rombelX_TSM = $rombels->firstWhere('nama_rombel', 'X TSM');
        $rombelXI_APHP = $rombels->firstWhere('nama_rombel', 'XI APHP');
        $rombelXI_RPL = $rombels->firstWhere('nama_rombel', 'XI RPL');
        $rombelXI_TSM = $rombels->firstWhere('nama_rombel', 'XI TSM');
        $rombelXII_APHP = $rombels->firstWhere('nama_rombel', 'XII APHP');
        $rombelXII_RPL = $rombels->firstWhere('nama_rombel', 'XII RPL');

        $guruIdx = 0;
        $totalGurus = $gurus->count();
      @endphp

      @foreach($days as $day)
        @php
          $piket = $guruPikets->get($day);
          $isJumat = ($day === 'JUMAT');
          $maxJam = $isJumat ? 5 : 11;
          $totalRowsForDay = $isJumat ? 7 : 14;
          $pklRowSpan = $isJumat ? 6 : 13;
        @endphp

        {{-- JAM 0 --}}
        <tr>
          <th rowspan="{{ $totalRowsForDay }}" class="th-hari" style="font-size:12px; font-weight:900; letter-spacing:1px;">
            @for($i=0; $i<strlen($day); $i++)
              <div>{{ $day[$i] }}</div>
            @endfor
          </th>
          <td>0</td>
          <td style="font-size:8.5px;">{{ $day === 'SENIN' ? '07.15 - 08.15' : ($day === 'JUMAT' ? '07.15 - 08.00' : '07.15 - 07.30') }}</td>
          <td colspan="8" class="{{ $day === 'SENIN' ? 'bg-upacara' : ($day === 'JUMAT' ? 'bg-lampung' : 'bg-apel') }}">
            {{ $day === 'SENIN' ? 'UPACARA BENDERA' : ($day === 'JUMAT' ? 'LAMPUNG MENGAJI / SEHAT / BERSIH / KREATIF' : 'APEL PAGI') }}
          </td>
          {{-- Guru Piket Column --}}
          <td rowspan="{{ $totalRowsForDay }}" style="vertical-align:top; text-align:left; padding:4px;">
            <div style="font-weight:800; font-size:8.5px; color:#1e293b; margin-bottom:2px;">WAKA PIKET:</div>
            <div style="font-size:8px; margin-bottom:6px; font-weight:700;">{{ $piket?->wakaPiket?->nama ?? '-' }}</div>
            <div style="font-weight:800; font-size:8.5px; color:#1e293b; margin-bottom:2px;">GURU PIKET:</div>
            @forelse($piket?->guru_list ?? [] as $gp)
              <div style="font-size:8px;">• {{ $gp->nama }}</div>
            @empty
              <div style="font-size:8px; color:#64748b; font-style:italic;">-</div>
            @endforelse
          </td>
          {{-- Guru Legend 1st row of day --}}
          @php $gRow = $gurus->get($guruIdx++); @endphp
          <td style="font-weight:800;">{{ $gRow?->kode_nomor ?? '-' }}</td>
          <td style="text-align:left; font-size:8.5px; font-weight:600;">{{ $gRow?->nama ?? '-' }}</td>
        </tr>

        {{-- JAM 1 s/d MAX --}}
        @for($jam = 1; $jam <= $maxJam; $jam++)
          {{-- Sisipkan Baris Istirahat 1 --}}
          @if(($day !== 'JUMAT' && $jam == 4) || ($day === 'JUMAT' && $jam == 4))
            <tr class="bg-istirahat">
              <td>-</td>
              <td style="font-size:8.5px;">{{ $day === 'SENIN' ? '10.00 - 10.20' : ($day === 'JUMAT' ? '09.40 - 10.00' : '09.50 - 10.05') }}</td>
              <td colspan="6">ISTIRAHAT PERTAMA</td>
              @php $gRow = $gurus->get($guruIdx++); @endphp
              <td style="font-weight:800; background:#fff; color:#000;">{{ $gRow?->kode_nomor ?? '-' }}</td>
              <td style="text-align:left; font-size:8.5px; font-weight:600; background:#fff; color:#000;">{{ $gRow?->nama ?? '-' }}</td>
            </tr>
          @endif

          {{-- Sisipkan Baris Istirahat 2 (hanya Senin-Kamis setelah jam 7) --}}
          @if($day !== 'JUMAT' && $jam == 8)
            <tr class="bg-istirahat">
              <td>-</td>
              <td style="font-size:8.5px;">{{ $day === 'SENIN' ? '12.40 - 13.10' : '12.25 - 12.55' }}</td>
              <td colspan="6">ISTIRAHAT KEDUA</td>
              @php $gRow = $gurus->get($guruIdx++); @endphp
              <td style="font-weight:800; background:#fff; color:#000;">{{ $gRow?->kode_nomor ?? '-' }}</td>
              <td style="text-align:left; font-size:8.5px; font-weight:600; background:#fff; color:#000;">{{ $gRow?->nama ?? '-' }}</td>
            </tr>
          @endif

          <tr>
            <td style="font-weight:800;">{{ $jam }}</td>
            <td style="font-size:8px;">
              {{ $slotsMatrix[$day][$jam][$rombelX_APHP?->id]->pukul ?? '' }}
            </td>

            {{-- X APHP --}}
            @php $sX_APHP = $slotsMatrix[$day][$jam][$rombelX_APHP?->id] ?? null; @endphp
            <td style="background:#fef9c3;">
              @if($sX_APHP)
                <div class="cell-slot">
                  @if($sX_APHP->kode_guru)<span class="kode-guru-box">{{ $sX_APHP->kode_guru }}</span>@endif
                  <span class="mapel-text">{{ $sX_APHP->singkatan_mapel }}</span>
                </div>
              @endif
            </td>

            {{-- X RPL --}}
            @php $sX_RPL = $slotsMatrix[$day][$jam][$rombelX_RPL?->id] ?? null; @endphp
            <td style="background:#e0f2fe;">
              @if($sX_RPL)
                <div class="cell-slot">
                  @if($sX_RPL->kode_guru)<span class="kode-guru-box">{{ $sX_RPL->kode_guru }}</span>@endif
                  <span class="mapel-text">{{ $sX_RPL->singkatan_mapel }}</span>
                </div>
              @endif
            </td>

            {{-- X TSM --}}
            @php $sX_TSM = $slotsMatrix[$day][$jam][$rombelX_TSM?->id] ?? null; @endphp
            <td style="background:#ffedd5;">
              @if($sX_TSM)
                <div class="cell-slot">
                  @if($sX_TSM->kode_guru)<span class="kode-guru-box">{{ $sX_TSM->kode_guru }}</span>@endif
                  <span class="mapel-text">{{ $sX_TSM->singkatan_mapel }}</span>
                </div>
              @endif
            </td>

            {{-- XI APHP --}}
            @php $sXI_APHP = $slotsMatrix[$day][$jam][$rombelXI_APHP?->id] ?? null; @endphp
            <td style="background:#fef9c3;">
              @if($sXI_APHP)
                <div class="cell-slot">
                  @if($sXI_APHP->kode_guru)<span class="kode-guru-box">{{ $sXI_APHP->kode_guru }}</span>@endif
                  <span class="mapel-text">{{ $sXI_APHP->singkatan_mapel }}</span>
                </div>
              @endif
            </td>

            {{-- XI RPL --}}
            @php $sXI_RPL = $slotsMatrix[$day][$jam][$rombelXI_RPL?->id] ?? null; @endphp
            <td style="background:#e0f2fe;">
              @if($sXI_RPL)
                <div class="cell-slot">
                  @if($sXI_RPL->kode_guru)<span class="kode-guru-box">{{ $sXI_RPL->kode_guru }}</span>@endif
                  <span class="mapel-text">{{ $sXI_RPL->singkatan_mapel }}</span>
                </div>
              @endif
            </td>

            {{-- XI TSM --}}
            @php $sXI_TSM = $slotsMatrix[$day][$jam][$rombelXI_TSM?->id] ?? null; @endphp
            <td style="background:#ffedd5;">
              @if($sXI_TSM)
                <div class="cell-slot">
                  @if($sXI_TSM->kode_guru)<span class="kode-guru-box">{{ $sXI_TSM->kode_guru }}</span>@endif
                  <span class="mapel-text">{{ $sXI_TSM->singkatan_mapel }}</span>
                </div>
              @endif
            </td>

            {{-- XII APHP (PKL) --}}
            @if($jam === 1)
              <td rowspan="{{ $pklRowSpan }}" class="bg-pkl" style="background:#fef08a;">
                P R A K T I K &nbsp; K E R J A &nbsp; L A P A N G A N
              </td>
              <td rowspan="{{ $pklRowSpan }}" class="bg-pkl" style="background:#bae6fd;">
                P R A K T I K &nbsp; K E R J A &nbsp; L A P A N G A N
              </td>
            @endif

            {{-- Guru Legend row --}}
            @php $gRow = $gurus->get($guruIdx++); @endphp
            <td style="font-weight:800;">{{ $gRow?->kode_nomor ?? '-' }}</td>
            <td style="text-align:left; font-size:8.5px; font-weight:600;">{{ $gRow?->nama ?? '-' }}</td>
          </tr>
        @endfor
      @endforeach

      {{-- SABTU --}}
      <tr>
        <th class="th-hari" style="font-size:11px; font-weight:900;">SABTU</th>
        <td colspan="10" style="background:#ef4444; color:#fff; font-weight:800; padding:6px; letter-spacing:2px;">
          EKSTRAKURIKULER
        </td>
        <td>-</td>
        <td>-</td>
      </tr>
    </tbody>
  </table>

  {{-- Legenda Mata Pelajaran & Tanda Tangan --}}
  <div style="display:flex; justify-content:space-between; margin-top:10px; gap:20px;">
    {{-- Legenda Mapel --}}
    <div style="flex:1;">
      <div style="font-weight:800; font-size:9.5px; margin-bottom:4px; text-transform:uppercase;">
        KODE &amp; DAFTAR MATA PELAJARAN:
      </div>
      <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:6px; font-size:8px;">
        <div>
          <div style="font-weight:800; background:#e2e8f0; padding:2px 4px;">KELOMPOK UMUM</div>
          @foreach($mapels->where('jenis', 'umum')->take(9) as $m)
            <div><b>{{ $m->kode_mapel }}</b>: {{ $m->nama_mapel }}</div>
          @endforeach
        </div>
        <div>
          <div style="font-weight:800; background:#e2e8f0; padding:2px 4px;">KEJURUAN &amp; KIK</div>
          @foreach($mapels->where('jenis', 'kejuruan')->take(9) as $m)
            <div><b>{{ $m->kode_mapel }}</b>: {{ $m->nama_mapel }}</div>
          @endforeach
        </div>
        <div>
          <div style="font-weight:800; background:#e2e8f0; padding:2px 4px;">MAPEL PILIHAN &amp; KONSENTRASI</div>
          @foreach($mapels->whereIn('jenis', ['pilihan', 'pkl'])->take(9) as $m)
            <div><b>{{ $m->kode_mapel }}</b>: {{ $m->nama_mapel }}</div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Tanda Tangan Kepala Sekolah --}}
    <div class="signature-box">
      <div>Air Naningan, 10 Juli 2026</div>
      <div style="font-weight:700;">Kepala SMK Negeri 1 Air Naningan</div>
      <div style="height:55px;"></div>
      <div style="font-weight:900; text-decoration:underline; font-size:11px;">APRIDA, S.Si</div>
      <div style="font-size:9.5px;">NIP. 197904172008012019</div>
    </div>
  </div>

</body>
</html>
