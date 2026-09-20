@extends('dcc.akademik.layout')

@section('title', 'Jadwal Pelajaran & KBM (Roster Wakakur)')
@section('breadcrumb', 'Jadwal & Distribusi')

@section('content')
{{-- Header Card --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div>
      <h2 style="font-weight:900; font-size:20px; color:var(--ak-dark); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-calendar3-week text-primary"></i>
        Jadwal Pelajaran &amp; KBM (Formulasi Wakakur)
      </h2>
      <div style="font-size:12.5px; color:#64748b; margin-top:3px;">
        Tahun Ajaran <b>{{ $ta?->tahun_ajaran ?? '2025/2026' }}</b> · Semester <b>{{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}</b>
      </div>
    </div>
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
      {{-- Switch Semester --}}
      <div class="btn-group" role="group">
        <a href="{{ route('akademik.jadwal.index', ['tab' => $tab, 'semester' => 1]) }}" class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:6px 12px;">
          Sem 1 (Ganjil)
        </a>
        <a href="{{ route('akademik.jadwal.index', ['tab' => $tab, 'semester' => 2]) }}" class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:6px 12px;">
          Sem 2 (Genap)
        </a>
      </div>

      {{-- Tombol Cetak Format Resmi --}}
      <a href="{{ route('akademik.jadwal.cetak', ['semester' => $semester]) }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:12.5px;">
        <i class="bi bi-printer me-1"></i> Cetak Jadwal Resmi
      </a>

      {{-- Tombol Otomatisasi Jadwal 1-Klik --}}
      <button type="button" class="ak-btn" data-bs-toggle="modal" data-bs-target="#modalAutoScheduler" style="font-size:12.5px; background:linear-gradient(135deg, #059669, #10b981); color:#ffffff; border:none; box-shadow:0 4px 12px rgba(16,185,129,0.3); font-weight:700;">
        <i class="bi bi-magic me-1"></i> ✨ Otomatisasi Jadwal (1-Klik)
      </button>

      {{-- Tombol Isi Blok Jadwal --}}
      <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalFormulasiBlok" style="font-size:12.5px;">
        <i class="bi bi-lightning-charge-fill me-1"></i> + Formulasi Blok Jam
      </button>
    </div>
  </div>

  {{-- Navigation Tabs --}}
  <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; padding:0 16px; overflow-x:auto;">
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester]) }}"
       style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'roster' ? 'var(--ak-primary)' : 'transparent' }}; color: {{ $tab == 'roster' ? 'var(--ak-primary)' : '#64748b' }};">
      <i class="bi bi-grid-3x3-gap-fill"></i> Matriks Roster Jadwal
    </a>
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'formulasi', 'semester' => $semester]) }}"
       style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'formulasi' ? 'var(--ak-primary)' : 'transparent' }}; color: {{ $tab == 'formulasi' ? 'var(--ak-primary)' : '#64748b' }};">
      <i class="bi bi-lightning-charge-fill"></i> Formulasi Blok Cepat
    </a>
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket', 'semester' => $semester]) }}"
       style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'piket' ? 'var(--ak-primary)' : 'transparent' }}; color: {{ $tab == 'piket' ? 'var(--ak-primary)' : '#64748b' }};">
      <i class="bi bi-person-badge-fill"></i> Jadwal Guru Piket
    </a>
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'semester' => $semester]) }}"
       style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'distribusi' ? 'var(--ak-primary)' : 'transparent' }}; color: {{ $tab == 'distribusi' ? 'var(--ak-primary)' : '#64748b' }};">
      <i class="bi bi-journal-text"></i> Distribusi Mengajar (JJM)
    </a>
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'pukul', 'semester' => $semester]) }}"
       style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'pukul' ? '#f59e0b' : 'transparent' }}; color: {{ $tab == 'pukul' ? '#b45309' : '#64748b' }};">
      <i class="bi bi-clock-history"></i> Atur Pukul KBM
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px; font-weight:600;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- TAB PUKUL: Atur Waktu KBM (aktif diluar tag roster) --}}
@if($tab === 'pukul')
<div class="akademik-card" style="margin-bottom:24px;">
  <div class="akademik-card-header" style="background:linear-gradient(135deg,#fffbeb,#fef9ee); border-bottom:1px solid #fde68a;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div>
        <h3 style="font-weight:900; font-size:16px; margin:0; color:#92400e; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clock-history text-warning"></i> Atur Pukul (Jam Pelajaran) KBM
        </h3>
        <div style="font-size:12px; color:#b45309; margin-top:2px;">Wakakur dapat mengubah waktu (pukul) setiap slot jam pelajaran per hari. Klik Simpan untuk menyimpan konfigurasi.</div>
      </div>
      <div style="display:flex; gap:8px;">
        <form action="{{ route('akademik.jadwal.update-pukul') }}" method="POST" style="margin:0;" onsubmit="return confirm('Reset semua pukul ke standar resmi SMKN 1 Air Naningan?')">
          @csrf
          <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
          <input type="hidden" name="semester" value="{{ $semester }}">
          <input type="hidden" name="reset_default" value="1">
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px;">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Default
          </button>
        </form>
      </div>
    </div>
  </div>
  <div class="akademik-card-body" style="padding:0; overflow-x:auto;">
    <form action="{{ route('akademik.jadwal.update-pukul') }}" method="POST">
      @csrf
      <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
      <input type="hidden" name="semester" value="{{ $semester }}">
      @php
        $hariKbm = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'];
        $maxJamPerHari = ['SENIN' => 11, 'SELASA' => 11, 'RABU' => 11, 'KAMIS' => 11, 'JUMAT' => 5];
        $wIdx = 0;
      @endphp
      <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
        <thead>
          <tr style="background:#f8fafc;">
            <th style="padding:10px 14px; text-align:left; border-bottom:2px solid #e2e8f0; font-weight:800; color:#334155; width:70px;">HARI</th>
            <th style="padding:10px 14px; text-align:center; border-bottom:2px solid #e2e8f0; font-weight:800; color:#334155; width:50px;">JAM KE-</th>
            <th style="padding:10px 14px; text-align:left; border-bottom:2px solid #e2e8f0; font-weight:800; color:#92400e;">PUKUL (Edit bebas)</th>
            <th style="padding:10px 14px; text-align:left; border-bottom:2px solid #e2e8f0; font-weight:700; color:#64748b;">Default Resmi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($hariKbm as $hKbm)
            @php $maxJam = $maxJamPerHari[$hKbm]; @endphp
            {{-- Jam 0 (Upacara/Apel) --}}
            <tr style="background:#f0fdf4;">
              <td rowspan="{{ $maxJam + 1 }}" style="padding:10px 14px; border-bottom:1px solid #e2e8f0; font-weight:900; color:#1e40af; vertical-align:middle; font-size:13px; border-right:2px solid #e2e8f0;">{{ $hKbm }}</td>
              <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0; text-align:center; font-weight:700; color:#16a34a;">0</td>
              <td style="padding:6px 14px; border-bottom:1px solid #e2e8f0;">
                <input type="hidden" name="waktu_data[{{ $hKbm }}][0]" value="{{ $jadwalWaktu[$hKbm][0] ?? '' }}">
                <span style="font-size:12px; color:#64748b; font-style:italic;">{{ $jadwalWaktu[$hKbm][0] ?? 'Upacara/Apel/Mengaji' }}</span>
              </td>
              <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0; color:#94a3b8; font-size:11.5px;">—</td>
            </tr>
            @for($jk = 1; $jk <= $maxJam; $jk++)
              @php
                $currentPukul = $jadwalWaktu[$hKbm][$jk] ?? '';
                // Default per hari
                $defaultPukul = '';
                if ($hKbm === 'SENIN') {
                  $map = [1=>'08.15-08.55',2=>'08.55-09.35',3=>'09.35-10.00',4=>'10.20-11.00',5=>'11.00-11.40',6=>'11.40-12.20',7=>'12.20-12.40',8=>'13.10-13.50',9=>'13.50-14.30',10=>'14.30-15.10',11=>'15.10-15.50'];
                } elseif ($hKbm === 'JUMAT') {
                  $map = [1=>'08.00-08.40',2=>'08.40-09.20',3=>'09.20-09.40',4=>'10.00-10.40',5=>'10.40-11.20'];
                } else {
                  $map = [1=>'07.30-08.10',2=>'08.10-08.50',3=>'08.50-09.30',4=>'09.30-09.50',5=>'10.05-10.45',6=>'10.45-11.25',7=>'11.25-12.05',8=>'12.05-12.25',9=>'12.55-13.35',10=>'13.35-14.15',11=>'14.15-14.55'];
                }
                $defaultPukul = $map[$jk] ?? '';
                $isIstirahat1 = ($jk == 4 && $hKbm !== 'JUMAT') || ($jk == 3 && $hKbm === 'JUMAT') || ($jk == 3 && $hKbm === 'SENIN');
                $isIstirahat2 = ($jk == 8 && $hKbm !== 'JUMAT');
              @endphp
              @if($isIstirahat1 && !($hKbm === 'SENIN' && $jk == 3))
                <tr style="background:#fee2e2;">
                  <td style="padding:6px 14px; border-bottom:1px solid #fecaca; text-align:center; font-weight:700; color:#991b1b;">—</td>
                  <td colspan="2" style="padding:6px 14px; border-bottom:1px solid #fecaca; color:#991b1b; font-weight:700; font-size:11.5px;">
                    ☕ ISTIRAHAT PERTAMA
                  </td>
                </tr>
              @elseif($isIstirahat2)
                <tr style="background:#fee2e2;">
                  <td style="padding:6px 14px; border-bottom:1px solid #fecaca; text-align:center; font-weight:700; color:#991b1b;">—</td>
                  <td colspan="2" style="padding:6px 14px; border-bottom:1px solid #fecaca; color:#991b1b; font-weight:700; font-size:11.5px;">
                    ☕ ISTIRAHAT KEDUA
                  </td>
                </tr>
              @endif
              <tr style="background:{{ $loop->odd ? '#ffffff' : '#f8fafc' }};">
                <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0; text-align:center; font-weight:800; color:#0369a1;">{{ $jk }}</td>
                <td style="padding:6px 14px; border-bottom:1px solid #e2e8f0;">
                  <input type="text" name="waktu_data[{{ $hKbm }}][{{ $jk }}]" value="{{ $currentPukul ?: $defaultPukul }}"
                    style="border:1.5px solid #cbd5e1; border-radius:6px; padding:5px 10px; font-size:12px; font-weight:700; width:165px; color:#1e40af; font-family:monospace;"
                    placeholder="HH.MM-HH.MM">
                </td>
                <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0; color:#94a3b8; font-size:11.5px; font-family:monospace;">{{ $defaultPukul }}</td>
              </tr>
            @endfor
          @endforeach
        </tbody>
      </table>
      <div style="padding:14px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
        <a href="{{ route('akademik.jadwal.index', ['tab'=>'roster','semester'=>$semester]) }}" class="ak-btn ak-btn-secondary">Batal</a>
        <button type="submit" class="ak-btn ak-btn-primary" style="font-weight:800;">
          <i class="bi bi-floppy-fill me-1"></i> Simpan Semua Pukul
        </button>
      </div>
    </form>
  </div>
</div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px; font-weight:600;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- ========================================================================= --}}
{{-- TAB 1: MATRIKS ROSTER JADWAL SEKOLAH (FORMAT WAKAKUR)                     --}}
{{-- ========================================================================= --}}
@if($tab === 'roster')
<div class="akademik-card" style="margin-bottom:24px;">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <div>
      <h3 style="font-weight:800; font-size:15px; margin:0; color:var(--ak-dark);">
        Matriks Jadwal Pelajaran Mingguan
      </h3>
      <div style="font-size:12px; color:#64748b;">
        Klik pada sel jadwal mana pun untuk mengubah atau mengosongkan slot secara instan.
      </div>
    </div>
    {{-- Filter Hari --}}
    <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
      <span style="font-size:12px; font-weight:700; color:#64748b;">Filter Hari:</span>
      @php $hariArr = ['' => 'Semua Hari', 'SENIN' => 'Senin', 'SELASA' => 'Selasa', 'RABU' => 'Rabu', 'KAMIS' => 'Kamis', 'JUMAT' => 'Jumat']; @endphp
      @foreach($hariArr as $key => $lbl)
        <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester, 'hari' => $key]) }}"
           class="ak-btn {{ $hariFilter === $key ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
           style="font-size:11.5px; padding:4px 10px;">
          {{ $lbl }}
        </a>
      @endforeach
    </div>
  </div>

  <div class="akademik-card-body" style="padding:0; overflow-x:auto;">
    @php
      $rombelX_APHP = $rombels->firstWhere('nama_rombel', 'X APHP');
      $rombelX_RPL = $rombels->firstWhere('nama_rombel', 'X RPL');
      $rombelX_TSM = $rombels->firstWhere('nama_rombel', 'X TSM');
      $rombelXI_APHP = $rombels->firstWhere('nama_rombel', 'XI APHP');
      $rombelXI_RPL = $rombels->firstWhere('nama_rombel', 'XI RPL');
      $rombelXI_TSM = $rombels->firstWhere('nama_rombel', 'XI TSM');
      $rombelXII_APHP = $rombels->firstWhere('nama_rombel', 'XII APHP');
      $rombelXII_RPL = $rombels->firstWhere('nama_rombel', 'XII RPL');

      $displayDays = $hariFilter ? [strtoupper($hariFilter)] : ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'];
    @endphp

    <table class="roster-table">
      <thead>
        <tr>
          <th rowspan="2" class="roster-th-dark" style="width:48px;">HARI</th>
          <th rowspan="2" class="roster-th-dark" style="width:38px;">JAM</th>
          <th rowspan="2" class="roster-th-dark" style="width:78px;">PUKUL</th>
          <th colspan="3" class="roster-th-kelas-x">KELAS X / PROGRAM KEAHLIAN</th>
          <th colspan="3" class="roster-th-kelas-xi">KELAS XI / PROGRAM KEAHLIAN</th>
          <th colspan="2" class="roster-th-kelas-xii">KELAS XII / PKL</th>
          <th rowspan="2" class="roster-th-piket" style="width:175px;"><i class="bi bi-shield-check me-1"></i> PETUGAS PIKET</th>
        </tr>
        <tr>
          <th class="roster-th-sub-aphp" style="width:115px;">APHP</th>
          <th class="roster-th-sub-rpl" style="width:115px;">RPL</th>
          <th class="roster-th-sub-tsm" style="width:115px;">TSM</th>
          <th class="roster-th-sub-aphp" style="width:115px;">APHP</th>
          <th class="roster-th-sub-rpl" style="width:115px;">RPL</th>
          <th class="roster-th-sub-tsm" style="width:115px;">TSM</th>
          <th class="roster-th-sub-aphp" style="width:70px;">APHP</th>
          <th class="roster-th-sub-rpl" style="width:70px;">RPL</th>
        </tr>
      </thead>
      <tbody>
        @foreach($displayDays as $day)
          @php
            $piket = $guruPikets->get($day);
            $isJumat = ($day === 'JUMAT');
            $maxJam = $isJumat ? 5 : 11;
            $totalDayRows = $isJumat ? 7 : 14;
            $pklRowSpan = $isJumat ? 6 : 13;
          @endphp

          {{-- Jam 0 (Upacara / Apel / Lampung Mengaji) --}}
          <tr>
            <th rowspan="{{ $totalDayRows }}" class="roster-day-side">
              <div class="roster-day-letters">
                @for($i=0; $i<strlen($day); $i++)
                  <span>{{ $day[$i] }}</span>
                @endfor
              </div>
            </th>
            <td class="roster-cell-jam">0</td>
            <td class="roster-cell-pukul">
              {{ $day === 'SENIN' ? '07.15 - 08.15' : ($isJumat ? '07.15 - 08.00' : '07.15 - 07.30') }}
            </td>
            <td colspan="8" class="{{ $day === 'SENIN' ? 'roster-banner-upacara' : ($isJumat ? 'roster-banner-jumat' : 'roster-banner-apel') }}">
              @if($day === 'SENIN')
                <i class="bi bi-flag-fill me-1"></i> UPACARA BENDERA
              @elseif($isJumat)
                <i class="bi bi-book-half me-1"></i> LAMPUNG MENGAJI / SEHAT / BERSIH / KREATIF
              @else
                <i class="bi bi-sun-fill me-1"></i> APEL PAGI
              @endif
            </td>
            {{-- Petugas Piket --}}
            <td rowspan="{{ $totalDayRows }}" class="roster-cell-piket">
              <div class="roster-piket-badge-waka">Waka Piket</div>
              <div class="roster-piket-name-waka">{{ $piket?->wakaPiket?->nama ?? '-' }}</div>
              <div class="roster-piket-badge-guru">Guru Piket</div>
              <div class="roster-piket-list">
                @forelse($piket?->guru_list ?? [] as $gp)
                  <div class="roster-piket-item">
                    <i class="bi bi-person-check-fill text-success" style="font-size:11px; margin-top:2px;"></i>
                    <span>{{ $gp->nama }}</span>
                  </div>
                @empty
                  <div style="font-style:italic; color:#94a3b8; font-size:10.5px;">Belum diatur</div>
                @endforelse
              </div>
            </td>
          </tr>

          {{-- Jam 1 s/d Max --}}
          @for($jam = 1; $jam <= $maxJam; $jam++)
            {{-- Istirahat Pertama --}}
            @if(($day !== 'JUMAT' && $jam == 4) || ($day === 'JUMAT' && $jam == 4))
              <tr>
                <td class="roster-cell-jam" style="background:#fee2e2; color:#991b1b;">-</td>
                <td class="roster-cell-pukul" style="background:#fee2e2; color:#991b1b;">
                  {{ $day === 'SENIN' ? '10.00 - 10.20' : ($day === 'JUMAT' ? '09.40 - 10.00' : '09.50 - 10.05') }}
                </td>
                <td colspan="6" class="roster-banner-istirahat">
                  <i class="bi bi-cup-hot-fill me-1"></i> ISTIRAHAT PERTAMA
                </td>
              </tr>
            @endif

            {{-- Istirahat Kedua --}}
            @if($day !== 'JUMAT' && $jam == 8)
              <tr>
                <td class="roster-cell-jam" style="background:#fee2e2; color:#991b1b;">-</td>
                <td class="roster-cell-pukul" style="background:#fee2e2; color:#991b1b;">
                  {{ $day === 'SENIN' ? '12.40 - 13.10' : '12.25 - 12.55' }}
                </td>
                <td colspan="6" class="roster-banner-istirahat">
                  <i class="bi bi-cup-hot-fill me-1"></i> ISTIRAHAT KEDUA
                </td>
              </tr>
            @endif

            <tr>
              <td class="roster-cell-jam">{{ $jam }}</td>
              <td class="roster-cell-pukul">
                {{ $slotsMatrix[$day][$jam][$rombelX_APHP?->id]->pukul ?? '' }}
              </td>

              {{-- Render 6 Class Columns --}}
              @php
                $rombelList = [
                  ['r' => $rombelX_APHP, 'bg' => '#fffbeb'],
                  ['r' => $rombelX_RPL,  'bg' => '#f0f9ff'],
                  ['r' => $rombelX_TSM,  'bg' => '#fff7ed'],
                  ['r' => $rombelXI_APHP,'bg' => '#fffbeb'],
                  ['r' => $rombelXI_RPL, 'bg' => '#f0f9ff'],
                  ['r' => $rombelXI_TSM, 'bg' => '#fff7ed'],
                ];
              @endphp

              @foreach($rombelList as $item)
                @php
                  $rmb = $item['r'];
                  $slot = ($rmb) ? ($slotsMatrix[$day][$jam][$rmb->id] ?? null) : null;
                  $isLocked = (bool)($slot?->is_locked);
                  $slotId = $slot?->id ?? 0;
                @endphp
                <td class="roster-cell-slot {{ $isLocked ? 'roster-cell-locked' : '' }}"
                    style="background:{{ $isLocked ? '#fffbeb' : $item['bg'] }};"
                    onclick="openSlotModal('{{ $day }}', {{ $jam }}, {{ $rmb?->id ?? 0 }}, '{{ addslashes($rmb?->nama_rombel ?? '') }}', '{{ $slot?->guru_id ?? '' }}', '{{ $slot?->mata_pelajaran_id ?? '' }}', '{{ addslashes($slot?->kegiatan_khusus ?? '') }}', {{ $isLocked ? 'true' : 'false' }}, {{ $slotId }})"
                    title="{{ $slot ? ($slot->guru?->nama . ' - ' . ($slot->mataPelajaran?->nama_mapel ?? $slot->singkatan_mapel) . ($isLocked ? ' [🔒 DIKUNCI / KEEP]' : '')) : 'Klik untuk atur slot ini' }}">
                  @if($slot && ($slot->guru_id || $slot->singkatan_mapel || $slot->kegiatan_khusus))
                    <div class="roster-slot-card">
                      @if($slot->kode_guru)
                        <span class="roster-guru-badge">{{ $slot->kode_guru }}</span>
                      @endif
                      <span class="roster-mapel-name">
                        {{ $slot->singkatan_mapel ?? $slot->kegiatan_khusus }}
                      </span>
                      @if($isLocked)
                        <span class="roster-lock-badge" title="Slot ini dikunci (KEEP)">🔒</span>
                      @endif
                    </div>
                  @else
                    <div class="roster-empty-slot">
                      <i class="bi bi-plus-lg"></i>
                      <span>Isi</span>
                    </div>
                  @endif
                </td>
              @endforeach

              {{-- Kelas XII (PKL) spanning full day --}}
              @if($jam === 1)
                <td rowspan="{{ $pklRowSpan }}" class="roster-cell-pkl-aphp">
                  P R A K T I K &nbsp; K E R J A &nbsp; L A P A N G A N
                </td>
                <td rowspan="{{ $pklRowSpan }}" class="roster-cell-pkl-rpl">
                  P R A K T I K &nbsp; K E R J A &nbsp; L A P A N G A N
                </td>
              @endif
            </tr>
          @endfor
        @endforeach

        {{-- Sabtu --}}
        @if(!$hariFilter || $hariFilter === 'SABTU')
          <tr>
            <th class="roster-day-side" style="padding:10px;">SABTU</th>
            <td colspan="10" style="background:linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color:#fff; font-weight:800; padding:10px; font-size:12px; letter-spacing:2px; text-transform:uppercase;">
              <i class="bi bi-trophy-fill me-2"></i> EKSTRAKURIKULER & PENGEMBANGAN DIRI
            </td>
            <td class="roster-cell-piket" style="text-align:center; color:#94a3b8; vertical-align:middle;">-</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

{{-- Panel Hapus Jadwal Massal --}}
<div class="akademik-card" style="margin-bottom:20px; border:1.5px solid #fee2e2;">
  <div class="akademik-card-header" style="background:linear-gradient(135deg,#fef2f2,#fff5f5); border-bottom:1px solid #fecaca;">
    <h4 style="font-weight:800; font-size:13.5px; margin:0; color:#b91c1c; display:flex; align-items:center; gap:8px;">
      <i class="bi bi-trash3-fill"></i> Hapus / Reset Jadwal
    </h4>
    <div style="font-size:11.5px; color:#dc2626; margin-top:2px;">Hapus slot jadwal berdasarkan hari atau kelas. Slot yang dikunci (🔒 KEEP) tidak akan terhapus.</div>
  </div>
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.jadwal.clear') }}" method="POST" onsubmit="return confirmHapus(this)">
      @csrf
      <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
      <input type="hidden" name="semester" value="{{ $semester }}">
      <div style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="font-size:11.5px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Filter Hari (Opsional)</label>
          <select name="hari" class="ak-select" style="min-width:130px; font-size:12.5px;">
            <option value="">Semua Hari</option>
            <option value="SENIN">SENIN</option>
            <option value="SELASA">SELASA</option>
            <option value="RABU">RABU</option>
            <option value="KAMIS">KAMIS</option>
            <option value="JUMAT">JUMAT</option>
          </select>
        </div>
        <div>
          <label style="font-size:11.5px; font-weight:700; color:#64748b; display:block; margin-bottom:4px;">Filter Kelas (Opsional)</label>
          <select name="rombel_id" class="ak-select" style="min-width:150px; font-size:12.5px;">
            <option value="">Semua Kelas</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <label style="font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; color:#92400e; background:#fffbeb; border:1px solid #fde68a; padding:6px 12px; border-radius:8px;">
            <input type="checkbox" name="keep_locked" value="1" checked style="width:15px; height:15px;">
            Pertahankan Slot Terkunci 🔒
          </label>
        </div>
        <button type="submit" class="ak-btn" style="background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; font-weight:700; font-size:12.5px; border:none; box-shadow:0 4px 12px rgba(220,38,38,0.3); display:flex; align-items:center; gap:6px;">
          <i class="bi bi-trash3"></i> Hapus Jadwal
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Panel Legenda Guru & Legenda Mapel --}}
<div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:24px;">
  {{-- Legenda Guru (1 - 24) --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc;">
      <h4 style="font-weight:800; font-size:13.5px; margin:0; color:var(--ak-dark);">
        <i class="bi bi-person-lines-fill text-primary me-1"></i> Daftar Kode Nomor Guru (1 - 24)
      </h4>
    </div>
    <div class="akademik-card-body" style="padding:10px; max-height:280px; overflow-y:auto;">
      <table class="table table-sm table-striped mb-0" style="font-size:11px;">
        <thead>
          <tr>
            <th style="width:40px; text-align:center;">Kode</th>
            <th>Nama Lengkap Guru</th>
          </tr>
        </thead>
        <tbody>
          @foreach($gurus as $g)
            <tr>
              <td style="text-align:center; font-weight:900; color:var(--ak-primary);">{{ $g->kode_nomor ?? '-' }}</td>
              <td style="font-weight:600;">{{ $g->nama }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Legenda Mata Pelajaran --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc;">
      <h4 style="font-weight:800; font-size:13.5px; margin:0; color:var(--ak-dark);">
        <i class="bi bi-book-half text-success me-1"></i> Daftar Singkatan Mata Pelajaran
      </h4>
    </div>
    <div class="akademik-card-body" style="padding:12px; max-height:280px; overflow-y:auto;">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; font-size:11px;">
        @foreach($mapels as $m)
          <div style="display:flex; align-items:center; gap:6px; padding:4px 6px; background:#f8fafc; border-radius:6px; border:1px solid #e2e8f0;">
            <code style="font-weight:900; color:#0369a1; font-size:11px;">{{ $m->singkatan_mapel ?? $m->kode_mapel }}</code>
            <span style="font-weight:600; color:#334155; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $m->nama_mapel }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

{{-- ========================================================================= --}}
{{-- TAB 2: FORMULASI BLOK CEPAT (FAST BATCH SCHEDULER)                        --}}
{{-- ========================================================================= --}}
@if($tab === 'formulasi')
<div class="akademik-card" style="max-width:800px; margin:0 auto 24px;">
  <div class="akademik-card-header">
    <h3 style="font-weight:800; font-size:16px; margin:0; color:var(--ak-dark);">
      <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Formulasi Cepat Blok Jam Pelajaran
    </h3>
    <div style="font-size:12px; color:#64748b;">
      Isi jadwal 2, 3, atau 4 jam sekaligus untuk kelas dan guru tertentu. Sistem otomatis mendeteksi bentrok jadwal guru!
    </div>
  </div>
  <div class="akademik-card-body">
    <form action="{{ route('akademik.jadwal.blok.store') }}" method="POST">
      @csrf
      <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
      <input type="hidden" name="semester" value="{{ $semester }}">

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
        <div>
          <label class="ak-form-label">Hari KBM <span class="text-danger">*</span></label>
          <select name="hari" id="form_hari" class="ak-select" required onchange="checkLiveConflict()">
            <option value="SENIN">SENIN</option>
            <option value="SELASA">SELASA</option>
            <option value="RABU">RABU</option>
            <option value="KAMIS">KAMIS</option>
            <option value="JUMAT">JUMAT</option>
          </select>
        </div>
        <div>
          <label class="ak-form-label">Pilih Rombel / Kelas <span class="text-danger">*</span></label>
          <select name="rombel_id" id="form_rombel" class="ak-select" required onchange="checkLiveConflict()">
            @foreach($rombels as $r)
              <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
        <div>
          <label class="ak-form-label">Guru Pengampu <span class="text-danger">*</span></label>
          <select name="guru_id" id="form_guru" class="ak-select" required onchange="checkLiveConflict()">
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="ak-form-label">Mata Pelajaran <span class="text-danger">*</span></label>
          <select name="mata_pelajaran_id" id="form_mapel" class="ak-select" required>
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach($mapels as $m)
              <option value="{{ $m->id }}">{{ $m->singkatan_mapel ?? $m->kode_mapel }} - {{ $m->nama_mapel }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
        <div>
          <label class="ak-form-label">Dari Jam Ke- <span class="text-danger">*</span></label>
          <select name="jam_mulai" id="form_jam_mulai" class="ak-select" required onchange="checkLiveConflict()">
            @for($i=1; $i<=11; $i++)
              <option value="{{ $i }}">Jam Ke-{{ $i }}</option>
            @endfor
          </select>
        </div>
        <div>
          <label class="ak-form-label">Sampai Jam Ke- <span class="text-danger">*</span></label>
          <select name="jam_selesai" id="form_jam_selesai" class="ak-select" required onchange="checkLiveConflict()">
            @for($i=1; $i<=11; $i++)
              <option value="{{ $i }}" {{ $i == 3 ? 'selected' : '' }}>Jam Ke-{{ $i }}</option>
            @endfor
          </select>
        </div>
      </div>

      {{-- Conflict Alert Container --}}
      <div id="conflictAlert" style="display:none; margin-bottom:14px; padding:10px 14px; background:#fee2e2; border:1.5px solid #f87171; border-radius:8px; color:#b91c1c; font-size:12.5px; font-weight:700;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="conflictMessage"></span>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="submit" class="ak-btn ak-btn-primary" style="padding:10px 24px; font-size:13.5px;">
          <i class="bi bi-check2-circle me-1"></i> Terapkan Blok ke Jadwal
        </button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- ========================================================================= --}}
{{-- TAB 3: JADWAL GURU PIKET                                                  --}}
{{-- ========================================================================= --}}
@if($tab === 'piket')
<div class="akademik-card" style="margin-bottom:24px;">
  <div class="akademik-card-header">
    <h3 style="font-weight:800; font-size:16px; margin:0; color:var(--ak-dark);">
      <i class="bi bi-person-badge-fill text-success me-1"></i> Penugasan Waka &amp; Guru Piket Mingguan
    </h3>
    <div style="font-size:12px; color:#64748b;">
      Atur jadwal petugas piket harian untuk menjaga kedisiplinan dan kelancaran KBM di SMKN 1 Air Naningan.
    </div>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    <table class="table table-bordered mb-0" style="font-size:12.5px;">
      <thead style="background:#f8fafc;">
        <tr>
          <th style="width:120px;">Hari</th>
          <th style="width:250px;">Waka Piket</th>
          <th>Daftar Guru Piket</th>
          <th style="width:90px; text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'] as $d)
          @php $pkt = $guruPikets->get($d); @endphp
          <tr>
            <td style="font-weight:800; color:var(--ak-primary); font-size:13px;">{{ $d }}</td>
            <td style="font-weight:700;">
              @if($pkt?->wakaPiket)
                <i class="bi bi-shield-check text-primary me-1"></i> {{ $pkt->wakaPiket->nama }}
              @else
                <span style="color:#94a3b8; font-style:italic;">Belum diatur</span>
              @endif
            </td>
            <td>
              <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @forelse($pkt?->guru_list ?? [] as $gp)
                  <span class="ak-badge ak-badge-secondary" style="font-size:11.5px;">
                    <i class="bi bi-person-check text-success me-1"></i> {{ $gp->nama }}
                  </span>
                @empty
                  <span style="color:#94a3b8; font-style:italic; font-size:12px;">Belum ada guru piket</span>
                @endforelse
              </div>
            </td>
            <td style="text-align:center;">
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalPiket{{ $d }}" title="Ubah Petugas Piket">
                <i class="bi bi-pencil"></i> Ubah
              </button>
            </td>
          </tr>

          {{-- Modal Edit Piket --}}
          <div class="modal fade" id="modalPiket{{ $d }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content" style="border-radius:14px;">
                <form action="{{ route('akademik.jadwal.piket.store') }}" method="POST">
                  @csrf
                  <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
                  <input type="hidden" name="semester" value="{{ $semester }}">
                  <input type="hidden" name="hari" value="{{ $d }}">

                  <div class="modal-header">
                    <h5 class="modal-title" style="font-weight:800; font-size:16px;">Atur Petugas Piket - {{ $d }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div style="margin-bottom:12px;">
                      <label class="ak-form-label">Waka Piket</label>
                      <select name="waka_piket_id" class="ak-select">
                        <option value="">-- Pilih Waka Piket --</option>
                        @foreach($gurus as $g)
                          <option value="{{ $g->id }}" {{ $pkt?->waka_piket_id == $g->id ? 'selected' : '' }}>
                            {{ $g->nama }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div style="margin-bottom:12px;">
                      <label class="ak-form-label">Guru Piket (Pilih Beberapa)</label>
                      <div style="max-height:160px; overflow-y:auto; border:1px solid #cbd5e1; border-radius:8px; padding:8px; display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                        @foreach($gurus as $g)
                          <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                            <input type="checkbox" name="guru_ids[]" value="{{ $g->id }}" {{ in_array($g->id, $pkt?->guru_ids ?? []) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                            <span>{{ $g->nama }}</span>
                          </label>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="ak-btn ak-btn-primary">Simpan Petugas Piket</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- ========================================================================= --}}
{{-- TAB 4: DISTRIBUSI MENGAJAR (BEBAN JJM GURU)                              --}}
{{-- ========================================================================= --}}
@if($tab === 'distribusi')
{{-- Rekap JJM per Guru --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-header">
    <h3 style="font-weight:800; font-size:15px; margin:0; color:var(--ak-dark);">
      <i class="bi bi-clock-history text-primary me-1"></i> Rekap Jam Jabatan Mengajar (JJM) per Guru
    </h3>
  </div>
  <div class="akademik-card-body" style="padding:14px 20px;">
    @if($rekapJjm->isEmpty())
      <div style="font-size:13px; color:#64748b;">Belum ada alokasi jam mengajar yang tercatat.</div>
    @else
      <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap:12px;">
        @foreach($rekapJjm as $r)
          <div style="background:#f8fafc; border:1px solid var(--ak-slate-200); border-radius:10px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-weight:700; font-size:13px; color:var(--ak-dark);">{{ $r->guru?->nama ?? '-' }}</div>
              <div style="font-size:11px; color:#64748b;">{{ $r->total_rombel }} Rombel Diajar</div>
            </div>
            <div style="text-align:right;">
              <span class="ak-badge {{ $r->total_jam >= 24 ? 'ak-badge-success' : 'ak-badge-primary' }}" style="font-size:12.5px; font-weight:800;">
                {{ $r->total_jam }} JP/Mgg
              </span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- Tabel Detail Distribusi Mengajar --}}
<div class="akademik-card">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <h3 style="font-weight:800; font-size:15px; margin:0; color:var(--ak-dark);">
      Daftar Alokasi Pengampu &amp; Mata Pelajaran
    </h3>
    <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahDistribusi">
      <i class="bi bi-plus-circle me-1"></i> Tambah Distribusi
    </button>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    <div class="akademik-table-wrap">
      <table class="akademik-table">
        <thead>
          <tr>
            <th style="width:40px;">No</th>
            <th>Nama Guru Pengampu</th>
            <th>Mata Pelajaran</th>
            <th>Rombel / Kelas</th>
            <th>Semester</th>
            <th>Beban Mengajar</th>
            <th>Catatan</th>
            <th style="width:90px; text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($distribusis as $idx => $d)
            <tr>
              <td>{{ $distribusis->firstItem() + $idx }}</td>
              <td>
                <div style="font-weight:700; color:var(--ak-dark);">{{ $d->guru?->nama ?? '-' }}</div>
                <div style="font-size:11px; color:#64748b;">NIP: {{ $d->guru?->nip ?? 'Non-NIP' }}</div>
              </td>
              <td>
                <div style="font-weight:700; color:var(--ak-primary);">{{ $d->mataPelajaran?->nama_mapel ?? '-' }}</div>
                <div style="font-size:11px; color:#64748b;">Kode: {{ $d->mataPelajaran?->kode_mapel }} · {{ $d->mataPelajaran?->jenis_label }}</div>
              </td>
              <td>
                <div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
                  @foreach($d->rombels as $rb)
                    <span class="ak-badge ak-badge-secondary" style="font-size:11.5px; font-weight:700;">{{ $rb->nama_rombel }}</span>
                  @endforeach
                  @if($d->rombels->count() > 1)
                    <span style="font-size:11px; font-weight:600; color:#64748b; margin-left:2px;">({{ $d->rombels->count() }} Rombel)</span>
                  @endif
                </div>
              </td>
              <td>
                <span class="ak-badge ak-badge-primary">Sem {{ $d->semester }}</span>
              </td>
              <td>
                <div style="font-weight:800; color:var(--ak-dark);">{{ $d->total_jam_per_minggu }} JP / Rombel</div>
                @if($d->rombels->count() > 1)
                  <div style="font-size:11.5px; font-weight:800; color:var(--ak-primary); margin-top:2px;">
                    Total: {{ $d->total_jam_akumulasi }} JP/Mgg
                  </div>
                @endif
              </td>
              <td style="font-size:12px; color:#64748b;">{{ $d->catatan ?? '-' }}</td>
              <td style="text-align:center;">
                <div style="display:flex; justify-content:center; gap:6px;">
                  <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditDistribusi{{ $d->id }}" title="Ubah Rombel / Beban Jam">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <form action="{{ route('akademik.jadwal.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus alokasi {{ $d->mataPelajaran?->nama_mapel }} untuk {{ $d->guru?->nama }} ({{ $d->rombels->count() }} rombel)?')" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" value="{{ $d->ids_string }}">
                    <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>

                {{-- Modal Edit Distribusi Mengajar --}}
                <div class="modal fade" id="modalEditDistribusi{{ $d->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content" style="border-radius:14px; text-align:left;">
                      <form action="{{ route('akademik.jadwal.update', $d->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="ids_string" value="{{ $d->ids_string }}">

                        <div class="modal-header">
                          <h5 class="modal-title" style="font-weight:800; font-size:16px;">Ubah Alokasi Pengampu &amp; Rombel</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div style="margin-bottom:12px;">
                            <label class="ak-form-label">Guru Pengampu</label>
                            <select name="guru_id" class="ak-select" required>
                              @foreach($gurus as $g)
                                <option value="{{ $g->id }}" {{ $d->guru_id == $g->id ? 'selected' : '' }}>
                                  {{ $g->nama }} ({{ $g->nip ?? 'Non-NIP' }})
                                </option>
                              @endforeach
                            </select>
                          </div>
                          <div style="margin-bottom:12px;">
                            <label class="ak-form-label">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="ak-select" required>
                              @foreach($mapels as $m)
                                <option value="{{ $m->id }}" {{ $d->mata_pelajaran_id == $m->id ? 'selected' : '' }}>
                                  {{ $m->kode_mapel }} - {{ $m->nama_mapel }} ({{ $m->tingkat_label }})
                                </option>
                              @endforeach
                            </select>
                          </div>
                          <div style="margin-bottom:14px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                              <label class="ak-form-label" style="margin:0;">Pilih Rombel / Kelas yang Diampu <span class="text-danger">*</span></label>
                              <div style="font-size:11px;">
                                <button type="button" class="btn btn-link p-0 text-decoration-none me-2" onclick="document.querySelectorAll('.edit-rombel-{{ $d->id }}').forEach(c => c.checked = true)" style="font-size:11px; font-weight:700; color:var(--ak-primary);">Pilih Semua</button>
                                <button type="button" class="btn btn-link p-0 text-decoration-none text-muted" onclick="document.querySelectorAll('.edit-rombel-{{ $d->id }}').forEach(c => c.checked = false)" style="font-size:11px;">Kosongkan</button>
                              </div>
                            </div>
                            <div style="max-height:160px; overflow-y:auto; border:1px solid var(--ak-slate-300); border-radius:10px; padding:10px; background:#fafafa; display:grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap:6px;">
                              @foreach($rombels as $r)
                                <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:6px 10px; border-radius:8px; border:1px solid #e2e8f0; margin:0;">
                                  <input type="checkbox" name="rombel_ids[]" value="{{ $r->id }}" class="edit-rombel-{{ $d->id }}" {{ in_array($r->id, $d->rombel_ids) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                  <span>{{ $r->nama_rombel }}</span>
                                </label>
                              @endforeach
                            </div>
                          </div>
                          <div style="margin-bottom:12px;">
                            <label class="ak-form-label" style="margin-bottom:6px;">Pilihan Semester</label>
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                              <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; margin:0;">
                                <input type="radio" name="semester" value="1" {{ $d->semester == 1 ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                <span>Semester 1 (Ganjil)</span>
                              </label>
                              <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; margin:0;">
                                <input type="radio" name="semester" value="2" {{ $d->semester == 2 ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                <span>Semester 2 (Genap)</span>
                              </label>
                            </div>
                          </div>
                          <div style="margin-bottom:12px;">
                            <label class="ak-form-label">Beban Jam per Rombel (JP/Mgg)</label>
                            <input type="number" name="total_jam_per_minggu" class="ak-input" value="{{ $d->total_jam_per_minggu }}" min="1" max="20" required>
                          </div>
                          <div style="margin-bottom:12px;">
                            <label class="ak-form-label">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="catatan" class="ak-input" value="{{ $d->catatan }}" placeholder="Misal: Teori di Lab RPL, Praktik Bengkel...">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="ak-btn ak-btn-primary">Perbarui Distribusi</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:#64748b;">
                Belum ada data distribusi mengajar untuk semester ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="padding:16px 20px;">
      {{ $distribusis->links() }}
    </div>
  </div>
</div>
@endif

{{-- ========================================================================= --}}
{{-- MODAL: FORMULASI BLOK JAM (FAST BATCH SCHEDULER)                          --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalFormulasiBlok" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.jadwal.blok.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <input type="hidden" name="semester" value="{{ $semester }}">

        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">
            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Formulasi Blok Jam Pelajaran
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Hari KBM <span class="text-danger">*</span></label>
              <select name="hari" id="m_hari" class="ak-select" required onchange="checkModalConflict()">
                <option value="SENIN">SENIN</option>
                <option value="SELASA">SELASA</option>
                <option value="RABU">RABU</option>
                <option value="KAMIS">KAMIS</option>
                <option value="JUMAT">JUMAT</option>
              </select>
            </div>
            <div>
              <label class="ak-form-label">Rombel / Kelas <span class="text-danger">*</span></label>
              <select name="rombel_id" id="m_rombel" class="ak-select" required onchange="checkModalConflict()">
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Guru Pengampu <span class="text-danger">*</span></label>
              <select name="guru_id" id="m_guru" class="ak-select" required onchange="checkModalConflict()">
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="ak-form-label">Mata Pelajaran <span class="text-danger">*</span></label>
              <select name="mata_pelajaran_id" id="m_mapel" class="ak-select" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapels as $m)
                  <option value="{{ $m->id }}">{{ $m->singkatan_mapel ?? $m->kode_mapel }} - {{ $m->nama_mapel }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Dari Jam Ke- <span class="text-danger">*</span></label>
              <select name="jam_mulai" id="m_jam_mulai" class="ak-select" required onchange="checkModalConflict()">
                @for($i=1; $i<=11; $i++)
                  <option value="{{ $i }}">Jam Ke-{{ $i }}</option>
                @endfor
              </select>
            </div>
            <div>
              <label class="ak-form-label">Sampai Jam Ke- <span class="text-danger">*</span></label>
              <select name="jam_selesai" id="m_jam_selesai" class="ak-select" required onchange="checkModalConflict()">
                @for($i=1; $i<=11; $i++)
                  <option value="{{ $i }}" {{ $i == 3 ? 'selected' : '' }}>Jam Ke-{{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>

          {{-- Conflict Alert Container --}}
          <div id="modalConflictAlert" style="display:none; margin-bottom:12px; padding:8px 12px; background:#fee2e2; border:1px solid #f87171; border-radius:8px; color:#b91c1c; font-size:12px; font-weight:700;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="modalConflictMessage"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Terapkan ke Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL: EDIT / ATUR SLOT INDIVIDUAL                                        --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalEditSlot" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.jadwal.slot.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="hidden" name="hari" id="slot_hari">
        <input type="hidden" name="jam_ke" id="slot_jam">
        <input type="hidden" name="rombel_id" id="slot_rombel_id">

        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">
            Atur Slot Jadwal (<span id="slot_info_label"></span>)
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Guru Pengampu</label>
            <select name="guru_id" id="slot_guru_id" class="ak-select">
              <option value="">-- Tanpa Guru / Kosongkan --</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" id="slot_mapel_id" class="ak-select">
              <option value="">-- Tanpa Mapel / Kosongkan --</option>
              @foreach($mapels as $m)
                <option value="{{ $m->id }}">{{ $m->singkatan_mapel ?? $m->kode_mapel }} - {{ $m->nama_mapel }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Kegiatan Khusus (Opsional)</label>
            <input type="text" name="kegiatan_khusus" id="slot_kegiatan" class="ak-input" placeholder="Misal: UPACARA, APEL, TEFA, PKL...">
          </div>

          {{-- Kunci Slot (Keep) --}}
          <div style="margin-top:14px; padding:10px 14px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0; font-size:12.5px; font-weight:700; color:#92400e;">
              <input type="checkbox" name="is_locked" id="slot_is_locked" value="1" style="width:16px; height:16px; cursor:pointer;">
              <span>🔒 Kunci Slot Ini (Keep / Jangan digeser sistem otomatis)</span>
            </label>
            <div style="font-size:11px; color:#b45309; margin-top:3px; margin-left:24px;">
              Jika dicentang, slot ini menjadi permanen (di-keep) dan tidak akan diubah atau ditimpa oleh sistem penyusun jadwal otomatis.
            </div>
          </div>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
          <div style="display:flex; gap:6px;">
            <button type="button" id="btnToggleLockSlot" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="toggleLockCurrentSlot()" style="font-size:12px; display:none;">
              🔒 Kunci / Buka Slot
            </button>
            <button type="button" id="btnDeleteSlot" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" onclick="deleteCurrentSlot()" style="font-size:12px; display:none;">
              <i class="bi bi-trash"></i> Kosongkan Jam
            </button>
          </div>
          <div style="display:flex; gap:8px;">
            <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary">Simpan Slot Ini</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL: OTOMATISASI JADWAL 1-KLIK (AUTO-SCHEDULER ENGINE)                  --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalAutoScheduler" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius:16px;">
      <form action="{{ route('akademik.jadwal.auto-generate') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <input type="hidden" name="semester" value="{{ $semester }}">

        <div class="modal-header" style="background:linear-gradient(135deg, #065f46, #059669); color:#ffffff; border-top-left-radius:16px; border-top-right-radius:16px;">
          <h5 class="modal-title" style="font-weight:900; font-size:16px; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-magic"></i> ✨ Otomatisasi Penyusunan Jadwal Pelajaran (1-Klik)
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body" style="padding:24px;">
          {{-- Penjelasan Sistem --}}
          <div style="padding:14px 18px; border-radius:12px; background:#ecfdf5; border:1px solid #a7f3d0; margin-bottom:20px; color:#065f46; font-size:12.5px; line-height:1.55;">
            <div style="font-weight:800; font-size:13.5px; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
              <i class="bi bi-cpu-fill" style="font-size:16px;"></i> Cara Kerja Algoritma Auto-Scheduler:
            </div>
            Sistem akan membaca beban mengajar masing-masing guru dari tab <b>Distribusi Mengajar (JJM)</b>, kemudian memetakan ke jam-jam KBM kosong secara berurutan (blok 2–4 JP) dengan aturan cerdas:
            <ul style="margin:6px 0 0; padding-left:18px;">
              <li><b>Zero Conflict:</b> Guru tidak akan pernah bentrok di kelas berbeda pada jam yang sama.</li>
              <li><b>Preserve Locked Slots:</b> Slot yang berstatus <b>DIKUNCI (KEEP 🔒)</b> oleh Wakakur / guru tetap aman dan tidak akan digeser.</li>
              <li><b>Sesi Alami:</b> Jam pelajaran disusun berurutan dan tidak terpotong oleh jam istirahat.</li>
              <li><b>Kelas XII PKL:</b> Otomatis dilindungi tetap berstatus Praktik Kerja Lapangan.</li>
            </ul>
          </div>

          {{-- Pilihan Mode Generate --}}
          <div style="margin-bottom:20px;">
            <label class="ak-form-label" style="font-weight:800; margin-bottom:8px;">Pilih Mode Otomatisasi:</label>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
              <label style="border:1.5px solid #a7f3d0; background:#f0fdf4; border-radius:12px; padding:14px; cursor:pointer; display:flex; gap:10px; align-items:flex-start;">
                <input type="radio" name="mode" value="regenerate_unlocked" checked style="width:17px; height:17px; margin-top:2px;">
                <div>
                  <div style="font-weight:800; font-size:13px; color:#065f46;">Susun Ulang Slot Bebas (Rekomendasi)</div>
                  <div style="font-size:11.5px; color:#047857; margin-top:2px; line-height:1.4;">
                    Mereset slot yang tidak terkunci lalu menyusun ulang secara optimal. <b>Slot yang DIKUNCI (KEEP 🔒) tetap dipertahankan.</b>
                  </div>
                </div>
              </label>

              <label style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:12px; padding:14px; cursor:pointer; display:flex; gap:10px; align-items:flex-start;">
                <input type="radio" name="mode" value="fill_empty" style="width:17px; height:17px; margin-top:2px;">
                <div>
                  <div style="font-weight:800; font-size:13px; color:var(--ak-dark);">Hanya Isi Slot Kosong</div>
                  <div style="font-size:11.5px; color:#64748b; margin-top:2px; line-height:1.4;">
                    Hanya mengisi kotak yang masih kosong. Semua slot yang sudah terisi (baik terkunci maupun belum) tidak akan diubah.
                  </div>
                </div>
              </label>
            </div>
          </div>

          {{-- Pilihan Hari KBM --}}
          <div style="margin-bottom:20px;">
            <label class="ak-form-label" style="font-weight:800; margin-bottom:6px;">Hari KBM yang Disusun:</label>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'] as $h)
                <label style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
                  <input type="checkbox" name="hari_list[]" value="{{ $h }}" checked style="width:15px; height:15px;">
                  <span>{{ $h }}</span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- Pilihan Kelas / Rombel --}}
          <div style="margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="ak-form-label" style="font-weight:800; margin:0;">Pilih Kelas / Rombel yang Diproses:</label>
              <div style="font-size:11.5px;">
                <button type="button" class="btn btn-link p-0 text-decoration-none me-2" onclick="document.querySelectorAll('.check-auto-rombel').forEach(c => c.checked = true)" style="font-size:11.5px; font-weight:700; color:var(--ak-primary);">Pilih Semua</button>
                <button type="button" class="btn btn-link p-0 text-decoration-none text-muted" onclick="document.querySelectorAll('.check-auto-rombel').forEach(c => c.checked = false)" style="font-size:11.5px;">Kosongkan</button>
              </div>
            </div>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap:8px;">
              @foreach($rombels as $r)
                @php $isPkl = str_contains($r->nama_rombel, 'XII'); @endphp
                <label style="background:{{ $isPkl ? '#fef3c7' : '#ffffff' }}; border:1px solid {{ $isPkl ? '#fde68a' : '#cbd5e1' }}; border-radius:8px; padding:8px 12px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px;">
                  <input type="checkbox" name="rombel_ids[]" value="{{ $r->id }}" class="check-auto-rombel" {{ $isPkl ? '' : 'checked' }} style="width:15px; height:15px;">
                  <span>{{ $r->nama_rombel }}</span>
                  @if($isPkl)
                    <span style="font-size:10px; color:#d97706;">(PKL)</span>
                  @endif
                </label>
              @endforeach
            </div>
          </div>
        </div>

        <div class="modal-footer" style="background:#f8fafc; border-bottom-left-radius:16px; border-bottom-right-radius:16px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn" style="background:linear-gradient(135deg,#059669,#10b981); color:#ffffff; font-weight:800; font-size:13px; padding:10px 20px; box-shadow:0 4px 12px rgba(16,185,129,0.3); border:none;">
            <i class="bi bi-magic me-1"></i> ✨ Mulai Susun Jadwal Otomatis
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL: TAMBAH DISTRIBUSI MENGAJAR (JJM)                                   --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalTambahDistribusi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.jadwal.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">Tambah Distribusi Mengajar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          {{-- Guru Pengampu (Multi-select Checkbox Grid) --}}
          <div style="margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="ak-form-label" style="margin:0;">
                Pilih Guru Pengampu <span class="text-danger">*</span>
                <span style="font-weight:400; font-size:11.5px; color:#64748b;">(Dapat memilih 1 atau beberapa guru)</span>
              </label>
              <div style="font-size:11.5px;">
                <button type="button" class="btn btn-link p-0 text-decoration-none me-2" onclick="document.querySelectorAll('.check-guru-dist').forEach(c => c.checked = true)" style="font-size:11.5px; font-weight:700; color:var(--ak-primary);">Pilih Semua</button>
                <button type="button" class="btn btn-link p-0 text-decoration-none text-muted" onclick="document.querySelectorAll('.check-guru-dist').forEach(c => c.checked = false)" style="font-size:11.5px;">Kosongkan</button>
              </div>
            </div>
            <div style="max-height:140px; overflow-y:auto; border:1px solid var(--ak-slate-300); border-radius:10px; padding:10px 12px; background:#fafafa; display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:8px;">
              @foreach($gurus as $g)
                <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:7px 10px; border-radius:8px; border:1px solid #e2e8f0; margin:0; transition:all 0.15s;">
                  <input type="checkbox" name="guru_ids[]" value="{{ $g->id }}" class="check-guru-dist" style="cursor:pointer; width:16px; height:16px;">
                  <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $g->nama }} ({{ $g->nip ?? 'Non-NIP' }})">
                    {{ $g->nama }}
                  </span>
                </label>
              @endforeach
            </div>
            <div style="font-size:11px; color:#64748b; margin-top:4px;">
              Centang satu atau beberapa guru yang mengampu mata pelajaran ini (bisa team teaching / mengampu bersama).
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Mata Pelajaran <span class="text-danger">*</span></label>
            <select name="mata_pelajaran_id" class="ak-select" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              @foreach($mapels as $m)
                <option value="{{ $m->id }}">{{ $m->kode_mapel }} - {{ $m->nama_mapel }} ({{ $m->tingkat_label }})</option>
              @endforeach
            </select>
          </div>

          {{-- Rombel / Kelas (Multi-select Checkbox Grid) --}}
          <div style="margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="ak-form-label" style="margin:0;">
                Pilih Rombel / Kelas <span class="text-danger">*</span>
                <span style="font-weight:400; font-size:11.5px; color:#64748b;">(Dapat memilih beberapa rombel)</span>
              </label>
              <div style="font-size:11.5px;">
                <button type="button" class="btn btn-link p-0 text-decoration-none me-2" onclick="document.querySelectorAll('.check-rombel-dist').forEach(c => c.checked = true)" style="font-size:11.5px; font-weight:700; color:var(--ak-primary);">Pilih Semua</button>
                <button type="button" class="btn btn-link p-0 text-decoration-none text-muted" onclick="document.querySelectorAll('.check-rombel-dist').forEach(c => c.checked = false)" style="font-size:11.5px;">Kosongkan</button>
              </div>
            </div>

            <div style="max-height:170px; overflow-y:auto; border:1px solid var(--ak-slate-300); border-radius:10px; padding:10px 12px; background:#fafafa; display:grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap:8px;">
              @foreach($rombels as $r)
                <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:600; cursor:pointer; background:#ffffff; padding:7px 10px; border-radius:8px; border:1px solid #e2e8f0; margin:0; transition:all 0.15s;">
                  <input type="checkbox" name="rombel_ids[]" value="{{ $r->id }}" class="check-rombel-dist" style="cursor:pointer; width:16px; height:16px;">
                  <span>{{ $r->nama_rombel }}</span>
                </label>
              @endforeach
            </div>
            <div style="font-size:11px; color:#64748b; margin-top:4px;">
              Centang satu atau beberapa rombel yang diajar oleh guru untuk mata pelajaran ini.
            </div>
          </div>

          {{-- Semester (Pilihan Opsi Radio) --}}
          <div style="margin-bottom:14px;">
            <label class="ak-form-label" style="margin-bottom:6px;">
              Pilihan Semester <span class="text-danger">*</span>
            </label>
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:8px;">
              <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:9px 10px; border-radius:8px; border:1px solid #cbd5e1; margin:0; transition:all 0.15s;">
                <input type="radio" name="semester_opsi" value="1" {{ ($semester ?? 1) == 1 ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                <span>Semester 1 (Ganjil)</span>
              </label>
              <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:9px 10px; border-radius:8px; border:1px solid #cbd5e1; margin:0; transition:all 0.15s;">
                <input type="radio" name="semester_opsi" value="2" {{ ($semester ?? 1) == 2 ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                <span>Semester 2 (Genap)</span>
              </label>
              <label style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:600; cursor:pointer; background:#ffffff; padding:9px 10px; border-radius:8px; border:1px solid #cbd5e1; margin:0; transition:all 0.15s;">
                <input type="radio" name="semester_opsi" value="both" style="cursor:pointer; width:15px; height:15px;">
                <span>Kedua Semester</span>
              </label>
            </div>
            <div style="font-size:11px; color:#64748b; margin-top:4px;">
              Pilih Semester 1, Semester 2, atau langsung kedua semester sekaligus.
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Beban Jam per Rombel (JP/Mgg)</label>
            <input type="number" name="total_jam_per_minggu" class="ak-input" value="4" min="1" max="20" required>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Catatan Tambahan (Opsional)</label>
            <input type="text" name="catatan" class="ak-input" placeholder="Misal: Teori di Lab RPL, Praktik Bengkel...">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Distribusi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentActiveSlotId = 0;

function openSlotModal(hari, jam, rombelId, rombelName, guruId, mapelId, kegiatan, isLocked, slotId) {
  currentActiveSlotId = slotId || 0;
  document.getElementById('slot_hari').value = hari;
  document.getElementById('slot_jam').value = jam;
  document.getElementById('slot_rombel_id').value = rombelId;
  document.getElementById('slot_info_label').innerText = hari + ' · Jam Ke-' + jam + ' · ' + rombelName;
  document.getElementById('slot_guru_id').value = guruId || '';
  document.getElementById('slot_mapel_id').value = mapelId || '';
  document.getElementById('slot_kegiatan').value = kegiatan || '';
  
  const lockCheckbox = document.getElementById('slot_is_locked');
  if (lockCheckbox) lockCheckbox.checked = !!isLocked;

  const btnToggle = document.getElementById('btnToggleLockSlot');
  const btnDel = document.getElementById('btnDeleteSlot');
  if (currentActiveSlotId > 0) {
    if (btnToggle) {
      btnToggle.style.display = 'inline-block';
      btnToggle.innerHTML = isLocked ? '🔓 Buka Kunci Slot' : '🔒 Kunci Slot (Keep)';
    }
    if (btnDel) {
      btnDel.style.display = 'inline-block';
    }
  } else {
    if (btnToggle) btnToggle.style.display = 'none';
    if (btnDel) btnDel.style.display = 'none';
  }

  const modalEl = document.getElementById('modalEditSlot');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}

function toggleLockCurrentSlot() {
  if (!currentActiveSlotId) return;
  fetch(`/dcc/akademik/jadwal/toggle-lock/${currentActiveSlotId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      window.location.reload();
    }
  })
  .catch(() => {
    window.location.reload();
  });
}

function deleteCurrentSlot() {
  if (!currentActiveSlotId) return;
  if (!confirm('Kosongkan slot jam pelajaran ini?')) return;

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `/dcc/akademik/jadwal/slot/${currentActiveSlotId}`;

  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = '_token';
  csrf.value = '{{ csrf_token() }}';
  form.appendChild(csrf);

  const method = document.createElement('input');
  method.type = 'hidden';
  method.name = '_method';
  method.value = 'DELETE';
  form.appendChild(method);

  document.body.appendChild(form);
  form.submit();
}

function checkLiveConflict() {
  const guru = document.getElementById('form_guru')?.value;
  const hari = document.getElementById('form_hari')?.value;
  const rombel = document.getElementById('form_rombel')?.value;
  const jamMulai = document.getElementById('form_jam_mulai')?.value;
  const jamSelesai = document.getElementById('form_jam_selesai')?.value;
  const alertBox = document.getElementById('conflictAlert');
  const alertMsg = document.getElementById('conflictMessage');

  if (!guru || !hari || !rombel || !alertBox) return;

  fetch("{{ route('akademik.jadwal.check-conflict') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      tahun_ajaran_id: {{ $ta?->id ?? 1 }},
      semester: {{ $semester }},
      guru_id: guru,
      hari: hari,
      rombel_id: rombel,
      jam_mulai: jamMulai,
      jam_selesai: jamSelesai
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.conflict) {
      alertMsg.innerText = data.message;
      alertBox.style.display = 'block';
    } else {
      alertBox.style.display = 'none';
    }
  })
  .catch(() => {
    alertBox.style.display = 'none';
  });
}

function checkModalConflict() {
  const guru = document.getElementById('m_guru')?.value;
  const hari = document.getElementById('m_hari')?.value;
  const rombel = document.getElementById('m_rombel')?.value;
  const jamMulai = document.getElementById('m_jam_mulai')?.value;
  const jamSelesai = document.getElementById('m_jam_selesai')?.value;
  const alertBox = document.getElementById('modalConflictAlert');
  const alertMsg = document.getElementById('modalConflictMessage');

  if (!guru || !hari || !rombel || !alertBox) return;

  fetch("{{ route('akademik.jadwal.check-conflict') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      tahun_ajaran_id: {{ $ta?->id ?? 1 }},
      semester: {{ $semester }},
      guru_id: guru,
      hari: hari,
      rombel_id: rombel,
      jam_mulai: jamMulai,
      jam_selesai: jamSelesai
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.conflict) {
      alertMsg.innerText = data.message;
      alertBox.style.display = 'block';
    } else {
      alertBox.style.display = 'none';
    }
  })
  .catch(() => {
    alertBox.style.display = 'none';
  });
}
</script>
<script>
// Konfirmasi hapus jadwal massal dengan detail
function confirmHapus(form) {
  const hariSel = form.querySelector('[name="hari"]');
  const rombelSel = form.querySelector('[name="rombel_id"]');
  const hariText = hariSel?.options[hariSel.selectedIndex]?.text || 'Semua Hari';
  const rombelText = rombelSel?.options[rombelSel.selectedIndex]?.text || 'Semua Kelas';
  const keepLocked = form.querySelector('[name="keep_locked"]')?.checked;
  const lockedNote = keepLocked ? '\n\nSlot yang DIKUNCI (🔒 KEEP) tetap aman.' : '\n\n⚠️ TERMASUK slot yang dikunci (KEEP)!';
  return confirm(`⚠️ HAPUS JADWAL\n\nHari: ${hariText}\nKelas: ${rombelText}${lockedNote}\n\nLanjutkan menghapus?`);
}
</script>
@endsection

