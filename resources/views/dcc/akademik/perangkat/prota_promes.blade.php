@extends('dcc.akademik.layout')

@section('title', 'Program Tahunan & Semester (Prota/Promes) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.prota-promes') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Prota &amp; Promes</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-calendar3 text-primary me-2"></i>
      3. RPE, Program Tahunan (Prota) &amp; Program Semester (Promes)
    </h1>
    <div class="akademik-page-desc">
      Kalkulasi Rincian Pekan Efektif (RPE) berdasarkan Kalender Pendidikan resmi sekolah dan distribusi jam mengajar per minggu sepanjang semester.
    </div>
  </div>
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)
  @php
    $kaldikMap = $kaldikItems ? $kaldikItems->keyBy('minggu_ke_semester') : collect([]);
    
    // Kumpulkan nomor minggu global yang efektif
    if ($kaldikItems && $kaldikItems->count() > 0) {
        $effectiveGlobalWeeks = $kaldikItems->where('jenis', 'efektif')->pluck('minggu_ke_semester')->values()->all();
    } else {
        $effectiveGlobalWeeks = range(1, min($rpePekanEfektif, 25));
    }
  @endphp

  {{-- Kartu Ringkasan RPE --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:18px 22px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
          <h2 style="font-size:16px; font-weight:800; color:var(--ak-dark); margin:0;">
            Rincian Pekan Efektif (RPE) Semester {{ $activePerangkat->semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}
          </h2>
          @if($kalender)
            @if($isKaldikResmi)
              <span class="ak-badge ak-badge-success" style="font-size:11px; font-weight:700;">
                <i class="bi bi-shield-check me-1"></i> Terkoneksi Kaldik Resmi Waka Kurikulum
              </span>
            @else
              <span class="ak-badge ak-badge-warning" style="font-size:11px; font-weight:700;">
                <i class="bi bi-clock-history me-1"></i> Kaldik Waka Kurikulum (Draf)
              </span>
            @endif
          @else
            <span class="ak-badge ak-badge-secondary" style="font-size:11px; font-weight:700;">
              <i class="bi bi-info-circle me-1"></i> Standar Default Estimasi
            </span>
          @endif
        </div>
        <div style="font-size:12px; color:#64748b;">
          Standar KBM SMKN 1 Air Naningan · Mata Pelajaran: <b>{{ $activePerangkat->mataPelajaran?->nama_mapel }}</b> (Kelas {{ $activePerangkat->tingkat }})
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $activePerangkat->tahun_ajaran_id, 'semester' => $activePerangkat->semester]) }}" class="ak-btn ak-btn-secondary" style="font-size:12px; padding:5px 12px;">
          <i class="bi bi-calendar2-week me-1"></i> Lihat Kaldik Sekolah
        </a>

        {{-- Form Ubah Pekan Efektif jika tidak terkunci atau role admin/waka --}}
        @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
          <form action="{{ route('akademik.perangkat.update-info', $activePerangkat->id) }}" method="POST" style="margin:0; display:flex; align-items:center; gap:8px;">
            @csrf
            @method('PUT')
            <div style="display:flex; align-items:center; gap:6px;">
              <label style="font-size:12px; font-weight:700; color:#475569; margin:0;">Pekan Efektif:</label>
              <input type="number" name="rpe_pekan_efektif" class="ak-input" value="{{ $rpePekanEfektif }}" min="10" max="25" style="width:65px; height:32px; font-size:12px; text-align:center;">
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              <label style="font-size:12px; font-weight:700; color:#475569; margin:0;">Cadangan:</label>
              <input type="number" name="rpe_pekan_cadangan" class="ak-input" value="{{ $rpeCadangan }}" min="0" max="10" style="width:60px; height:32px; font-size:12px; text-align:center;">
            </div>
            <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px; padding:4px 10px;" title="Simpan penyesuaian khusus RPE mapel">
              <i class="bi bi-floppy me-1"></i> Update
            </button>
          </form>
        @endif
      </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-top:16px;">
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Jumlah Pekan Semester</div>
        <div style="font-size:20px; font-weight:900; color:var(--ak-dark); margin-top:2px;">{{ $totalPekan }} Pekan</div>
        <div style="font-size:11px; color:#64748b; margin-top:2px;">Rentang 6 Bulan</div>
      </div>
      <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase;">Pekan Efektif KBM</div>
        <div style="font-size:20px; font-weight:900; color:#1e40af; margin-top:2px;">{{ $rpePekanEfektif }} Pekan</div>
        <div style="font-size:11px; color:#1e40af; margin-top:2px;">Aktif Tatap Muka Mapel</div>
      </div>
      <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:700; color:#92400e; text-transform:uppercase;">Cadangan / Non-Efektif</div>
        <div style="font-size:20px; font-weight:900; color:#92400e; margin-top:2px;">{{ $rpeCadangan }} Pekan</div>
        <div style="font-size:11px; color:#92400e; margin-top:2px;">MPLS, Ujian, Rapor, Libur</div>
      </div>
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:700; color:#166534; text-transform:uppercase;">Total JP KBM Semester Ini</div>
        <div style="font-size:20px; font-weight:900; color:#166534; margin-top:2px;">
          {{ $totalJpSemester }} <span style="font-size:12px; font-weight:700;">JP ({{ $jamPerMinggu }} JP/Mgg)</span>
        </div>
        <div style="font-size:11px; color:#166534; margin-top:2px;">{{ $rpePekanEfektif }} Pekan × {{ $jamPerMinggu }} JP</div>
      </div>
    </div>
  </div>

  {{-- Tabel Program Semester (Promes) --}}
  <div class="akademik-card" style="margin-bottom:24px;">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:15px;">
        <i class="bi bi-calendar-range text-primary me-2"></i>
        Distribusi Program Semester (Promes) — Semester {{ $activePerangkat->semester == 1 ? '1 (Juli - Des)' : '2 (Jan - Jun)' }}
      </h3>
      <div style="display:flex; align-items:center; gap:8px;">
        <span class="badge" style="background:#e0e7ff; color:#3730a3; font-weight:700; font-size:11.5px;">
          {{ $atpItems->count() }} Capaian Materi ({{ $atpItems->sum('alokasi_jp') }} JP)
        </span>
      </div>
    </div>

    <div class="akademik-card-body" style="padding:0;">
      @php
        $months = $activePerangkat->semester == 1
          ? ['Juli' => 4, 'Agustus' => 5, 'September' => 4, 'Oktober' => 4, 'November' => 4, 'Desember' => 4]
          : ['Januari' => 4, 'Februari' => 4, 'Maret' => 4, 'April' => 4, 'Mei' => 5, 'Juni' => 4];
      @endphp

      <div class="akademik-table-wrap">
        <table class="akademik-table" style="font-size:12px;">
          <thead>
            <tr>
              <th rowspan="3" style="width:40px; text-align:center;">No</th>
              <th rowspan="3" style="width:80px;">Kode TP</th>
              <th rowspan="3">Materi Pokok &amp; Tujuan Pembelajaran</th>
              <th rowspan="3" style="width:60px; text-align:center;">JP</th>
              @foreach($months as $mName => $weeks)
                <th colspan="{{ $weeks }}" style="text-align:center; background:#f1f5f9; border-left:1px solid #cbd5e1; font-weight:800;">
                  {{ $mName }}
                </th>
              @endforeach
            </tr>
            <tr>
              @php $headWeekCounter = 1; @endphp
              @foreach($months as $mName => $weeks)
                @for($w = 1; $w <= $weeks; $w++)
                  @php
                    $kaldikItem = $kaldikMap->get($headWeekCounter);
                    $isEfektifHead = $kaldikItem ? $kaldikItem->isEfektif() : true;
                  @endphp
                  <th style="width:24px; text-align:center; padding:3px 2px; font-size:10px; border-left:{{ $w==1 ? '1px solid #cbd5e1' : 'none' }}; background:{{ $isEfektifHead ? '#ffffff' : '#fff7ed' }}; color:{{ $isEfektifHead ? '#0f172a' : '#c2410c' }};">
                    {{ $w }}
                  </th>
                  @php $headWeekCounter++; @endphp
                @endfor
              @endforeach
            </tr>
            {{-- Baris Keterangan Agenda Kaldik Sekolah --}}
            <tr style="background:#f8fafc; font-size:9.5px;">
              @php $agendaWeekCounter = 1; @endphp
              @foreach($months as $mName => $weeks)
                @for($w = 1; $w <= $weeks; $w++)
                  @php
                    $kaldikItem = $kaldikMap->get($agendaWeekCounter);
                    $isEfektifAgenda = $kaldikItem ? $kaldikItem->isEfektif() : true;
                    $labelAgenda = $kaldikItem ? $kaldikItem->getLabelSingkat() : 'KBM';
                    $warnaAgenda = $kaldikItem ? ($kaldikItem->warna ?: ($isEfektifAgenda ? '#2563eb' : '#f59e0b')) : '#2563eb';
                  @endphp
                  <th style="padding:3px 1px; text-align:center; border-left:{{ $w==1 ? '1px solid #cbd5e1' : 'none' }}; border-bottom:2px solid #cbd5e1; background:{{ $isEfektifAgenda ? '#eff6ff' : '#fff7ed' }};" title="{{ $kaldikItem?->keterangan ?? 'KBM Efektif' }}">
                    <span style="display:inline-block; font-size:8.5px; font-weight:800; color:{{ $isEfektifAgenda ? '#1e40af' : '#c2410c' }};">
                      {{ $labelAgenda }}
                    </span>
                  </th>
                  @php $agendaWeekCounter++; @endphp
                @endfor
              @endforeach
            </tr>
          </thead>
          <tbody>
            @php
              // Alokasi indeks pekan efektif yang sudah terpakai
              $effectiveWeekPointer = 0;
              $totalEffectiveAvailable = count($effectiveGlobalWeeks);
            @endphp
            @forelse($atpItems as $idx => $atp)
              @php
                $weeksNeeded = max(1, (int) ceil($atp->alokasi_jp / max(1, $jamPerMinggu)));
                // Tentukan minggu-minggu global efektif yang dialokasikan untuk TP ini
                $allocatedGlobalWeeks = [];
                for ($k = 0; $k < $weeksNeeded; $k++) {
                    if (isset($effectiveGlobalWeeks[$effectiveWeekPointer])) {
                        $allocatedGlobalWeeks[] = $effectiveGlobalWeeks[$effectiveWeekPointer];
                        $effectiveWeekPointer++;
                    }
                }
              @endphp
              <tr>
                <td style="text-align:center; font-weight:800;">{{ $loop->iteration }}</td>
                <td>
                  <span class="ak-badge ak-badge-primary" style="font-size:11px; font-weight:800;">{{ $atp->kode_tp }}</span>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark);">{{ $atp->materi_pokok }}</div>
                  <div style="font-size:11.5px; color:#64748b;">{{ $atp->tujuan_pembelajaran }}</div>
                </td>
                <td style="text-align:center; font-weight:900; color:var(--ak-primary);">
                  {{ $atp->alokasi_jp }}
                </td>

                {{-- Kolom Mingguan --}}
                @php $weekCounter = 1; @endphp
                @foreach($months as $mName => $weeks)
                  @for($w = 1; $w <= $weeks; $w++)
                    @php
                      $kaldikItem = $kaldikMap->get($weekCounter);
                      $isNonEfektifWeek = $kaldikItem && !$kaldikItem->isEfektif();
                      $isAllocatedHere = in_array($weekCounter, $allocatedGlobalWeeks);
                    @endphp
                    <td style="text-align:center; padding:2px; border-left:{{ $w==1 ? '1px solid #cbd5e1' : 'none' }}; background:{{ $isNonEfektifWeek ? '#fef3c7' : ($isAllocatedHere ? '#dbeafe' : 'transparent') }};" title="{{ $isNonEfektifWeek ? ($kaldikItem?->keterangan ?: 'Pekan Non-Efektif') : ($isAllocatedHere ? ($atp->materi_pokok . ' (' . $jamPerMinggu . ' JP)') : '') }}">
                      @if($isNonEfektifWeek)
                        <span style="font-weight:800; color:#b45309; font-size:8.5px; opacity:0.8;">
                          {{ $kaldikItem->getLabelSingkat() }}
                        </span>
                      @elseif($isAllocatedHere)
                        <span style="font-weight:900; color:#1d4ed8; font-size:10.5px;">{{ $jamPerMinggu }}</span>
                      @endif
                    </td>
                    @php $weekCounter++; @endphp
                  @endfor
                @endforeach
              </tr>
            @empty
              <tr>
                <td colspan="30" style="text-align:center; padding:30px; color:#64748b;">
                  Belum ada data materi ATP. Silakan rumuskan Tujuan Pembelajaran di menu <b>TP &amp; ATP</b> terlebih dahulu.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

@else
  <div class="akademik-card" style="padding:40px 20px; text-align:center; color:#64748b;">
    <i class="bi bi-calendar-x" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size:12.5px;">Silakan buat folder perangkat ajar terlebih dahulu menggunakan tombol di atas.</div>
  </div>
@endif

@endsection
