@extends('akademik.layout')

@section('title', 'Kalender Pendidikan & RPE Sekolah — Akademik SMKN 1 Air Naningan')
@section('breadcrumb')
  <span>Kalender Pendidikan (Kaldik) &amp; RPE</span>
@endsection

@push('styles')
<style>
  /* ─── KONTROL TOP PANEL ─── */
  .kaldik-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }

  .kaldik-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
  }
  .kaldik-meta-chip strong {
    font-size: 15px;
    font-weight: 800;
    color: var(--ak-dark);
  }

  /* ─── GRID BULANAN SEMESTER (3x2) ─── */
  .kaldik-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
  }

  .kaldik-card-month {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .kaldik-card-month:hover {
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.07);
  }

  .kaldik-month-top {
    background: #f8fafc;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .kaldik-month-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--ak-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* ─── TABEL KALENDER BULANAN ─── */
  .kaldik-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    text-align: center;
  }
  .kaldik-table th {
    padding: 6px 2px;
    font-weight: 800;
    color: #475569;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    font-size: 10.5px;
  }
  .kaldik-table th.sun, .kaldik-table td.sun {
    color: #dc2626;
  }
  .kaldik-table td {
    padding: 3px 1px;
    border-bottom: 1px solid #f8fafc;
    border-right: 1px solid #f8fafc;
    height: 28px;
    vertical-align: middle;
  }
  .kaldik-table td.day-cell {
    cursor: pointer;
    transition: all 0.12s ease;
  }
  .kaldik-table td.day-cell:hover {
    box-shadow: inset 0 0 0 1.5px #4f46e5;
    border-radius: 4px;
  }

  .kaldik-col-pekan {
    width: 32px;
    font-weight: 800;
    font-size: 10px;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0 !important;
    cursor: pointer;
  }
  .kaldik-col-pekan:hover {
    filter: brightness(0.92);
  }

  .kaldik-day-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 21px;
    height: 21px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 11px;
    color: #1e293b;
    transition: transform 0.1s ease;
  }
  .kaldik-table td.day-cell:hover .kaldik-day-badge {
    transform: scale(1.15);
  }
  .kaldik-day-badge.efektif {
    background: #eff6ff;
    color: #1d4ed8;
  }
  .kaldik-day-badge.agenda {
    color: #ffffff;
    font-weight: 800;
  }

  /* ─── LIST AGENDA KHUSUS DI CARD BULAN ─── */
  .kaldik-agenda-list {
    padding: 8px 12px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
  }
  .kaldik-agenda-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    background: #fffbeb;
    border: 1px solid #fde68a;
  }

  /* ─── TABEL REKAPITULASI RPE ─── */
  .rpe-summary-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
  }
  .rpe-summary-table th {
    background: #f8fafc;
    color: #334155;
    font-weight: 800;
    padding: 10px 12px;
    border-bottom: 2px solid #e2e8f0;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .rpe-summary-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  .rpe-summary-table tr:hover td {
    background: #f8fafc;
  }
  .rpe-summary-table tfoot td {
    background: #f1f5f9;
    font-weight: 800;
    border-top: 2px solid #cbd5e1;
    color: var(--ak-dark);
  }

  @media print {
    body { background: #ffffff !important; }
    .no-print, .ak-sidebar, .ak-navbar, .akademik-sidebar { display: none !important; }
    .kaldik-header-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
    .kaldik-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 10px !important; }
  }
</style>
@endpush

@section('content')

{{-- 1. HEADER HALAMAN & AKSI TERPUSAT (SATU TEMPAT, TANPA DUPLIKAT) --}}
<div class="akademik-page-head" style="margin-bottom:16px;">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-calendar2-week-fill text-primary me-2"></i>
      Kalender Pendidikan (Kaldik) &amp; Penetapan RPE
    </h1>
    <div class="akademik-page-desc">
      Acuan resmi kalender pendidikan sekolah, rincian pekan efektif (RPE), dan penetapan agenda semester SMKN 1 Air Naningan.
    </div>
  </div>

  <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    @if($canManage && (!$kalender || !$kalender->is_locked))
      <button type="button" class="ak-btn ak-btn-primary" style="font-size:12px;" onclick="openTambahAgendaModal()">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Agenda
      </button>
    @endif

    @if($kalender && $canManage)
      <form action="{{ route('akademik.kalender.toggle-lock', $kalender->id) }}" method="POST" style="margin:0;">
        @csrf
        @if($kalender->is_locked)
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px;" title="Buka kunci agar dapat disesuaikan kembali">
            <i class="bi bi-unlock me-1"></i> Buka Kunci
          </button>
        @else
          <button type="submit" class="ak-btn ak-btn-success" style="font-size:12px;" title="Kunci kalender sebagai acuan resmi sekolah">
            <i class="bi bi-lock-fill me-1"></i> Kunci Acuan Resmi
          </button>
        @endif
      </form>
    @endif

    <button type="button" class="ak-btn ak-btn-secondary" style="font-size:12px;" onclick="window.print()" title="Cetak Format Lembar A4">
      <i class="bi bi-printer me-1"></i> Cetak Kaldik &amp; RPE
    </button>

    @if($canManage && (!$kalender || !$kalender->is_locked))
      <form action="{{ route('akademik.kalender.generate') }}" method="POST" style="margin:0;" onsubmit="return confirm('Reset ulang kalender ke template standar SMK?')">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px;" title="Reset ke Template Standar SMK">
          <i class="bi bi-arrow-counterclockwise me-1"></i> {{ $kalender ? 'Reset Template' : 'Buat Template' }}
        </button>
      </form>
    @endif
  </div>
</div>

{{-- 2. PANEL FILTER & RANGKUMAN METRIK TERPADU --}}
<div class="kaldik-header-card">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
    {{-- Form Filter Periode --}}
    <form method="GET" action="{{ route('akademik.kalender.index') }}" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin:0;">
      <div style="display:flex; align-items:center; gap:6px;">
        <label style="font-size:12px; font-weight:700; color:#475569; margin:0;">Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" class="ak-input" style="font-size:12px; height:32px; padding:2px 8px; width:170px;" onchange="this.form.submit()">
          @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}" {{ $selectedTa?->id == $ta->id ? 'selected' : '' }}>
              {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:flex; align-items:center; gap:4px;">
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $selectedTa?->id, 'semester' => 1]) }}" 
           class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
           style="font-size:11.5px; height:30px; padding:3px 12px;">
           Semester 1 (Ganjil)
        </a>
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $selectedTa?->id, 'semester' => 2]) }}" 
           class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
           style="font-size:11.5px; height:30px; padding:3px 12px;">
           Semester 2 (Genap)
        </a>
      </div>
    </form>

    {{-- Status Kunci / Penetapan --}}
    <div>
      @if($kalender)
        @if($kalender->is_locked)
          <span class="ak-badge ak-badge-success" style="font-size:11.5px; padding:5px 12px;">
            <i class="bi bi-shield-check me-1"></i> DITETAPKAN RESMI SEKOLAH
          </span>
        @else
          <span class="ak-badge ak-badge-warning" style="font-size:11.5px; padding:5px 12px;">
            <i class="bi bi-pencil-square me-1"></i> Draf Penyesuaian Waka Kurikulum
          </span>
        @endif
      @else
        <span class="ak-badge ak-badge-secondary" style="font-size:11.5px; padding:5px 12px;">
          Belum Dibuat
        </span>
      @endif
    </div>
  </div>

  @if($kalender)
    {{-- Baris Metrik Pekan Efektif & Legenda Ringkas --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <div class="kaldik-meta-chip">
          <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Total:</span>
          <strong>{{ $kalender->total_pekan }} Pekan</strong>
        </div>

        <div class="kaldik-meta-chip" style="background:#eff6ff; border-color:#bfdbfe;">
          <span style="font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase;">Efektif KBM:</span>
          <strong style="color:#1d4ed8;">{{ $kalender->pekan_efektif }} Pekan</strong>
        </div>

        <div class="kaldik-meta-chip" style="background:#fffbeb; border-color:#fde68a;">
          <span style="font-size:11px; font-weight:700; color:#92400e; text-transform:uppercase;">Non-Efektif / Cadangan:</span>
          <strong style="color:#b45309;">{{ $kalender->pekan_cadangan }} Pekan</strong>
        </div>
      </div>

      {{-- Legenda Ringkas & Bersih --}}
      <div style="display:flex; align-items:center; gap:12px; font-size:11px; color:#475569;">
        <span style="display:inline-flex; align-items:center; gap:5px;">
          <span style="width:10px; height:10px; border-radius:3px; background:#2563eb; display:inline-block;"></span>
          <span>KBM Efektif</span>
        </span>
        <span style="display:inline-flex; align-items:center; gap:5px;">
          <span style="width:10px; height:10px; border-radius:3px; background:#f59e0b; display:inline-block;"></span>
          <span>Asesmen / Khusus</span>
        </span>
        <span style="display:inline-flex; align-items:center; gap:5px;">
          <span style="width:10px; height:10px; border-radius:3px; background:#ef4444; display:inline-block;"></span>
          <span>Hari Libur</span>
        </span>
      </div>
    </div>
  @endif
</div>

@if($kalender)
  {{-- 3. KALENDER VISUAL 6 BULAN (KOMPAK & PROPORSIONAL) --}}
  <div class="kaldik-grid">
    @foreach($calendarMonths as $m)
      @php
        $specialAgendas = $m['kaldik_items']->where('jenis', '!=', 'efektif');
      @endphp
      <div class="kaldik-card-month">
        {{-- Header Bulan --}}
        <div class="kaldik-month-top">
          <div>
            <h3 class="kaldik-month-title">
              <i class="bi bi-calendar-month text-primary"></i>
              {{ strtoupper($m['name']) }} {{ $m['year'] }}
            </h3>
            <div style="font-size:10.5px; color:#64748b; margin-top:1px;">
              {{ $m['days_in_month'] }} Hari · <strong>{{ $m['efektif_count'] }} Pekan Efektif KBM</strong>
            </div>
          </div>
          <span class="badge" style="background:#e0e7ff; color:#3730a3; font-weight:800; font-size:10px;">
            {{ $m['kaldik_items']->first()?->minggu_ke_semester ? ('Pekan ' . $m['kaldik_items']->first()->minggu_ke_semester . '-' . $m['kaldik_items']->last()->minggu_ke_semester) : '' }}
          </span>
        </div>

        {{-- Tabel Kalender --}}
        <table class="kaldik-table">
          <thead>
            <tr>
              <th class="kaldik-col-pekan" title="Klik pekan untuk kelola">Mg</th>
              <th title="Senin">Sn</th>
              <th title="Selasa">Sl</th>
              <th title="Rabu">Rb</th>
              <th title="Kamis">Km</th>
              <th title="Jumat">Jm</th>
              <th title="Sabtu" class="sun">Sb</th>
              <th title="Minggu" class="sun">Mn</th>
            </tr>
          </thead>
          <tbody>
            @foreach($m['weeks'] as $wData)
              @php
                $kItem = $wData['kaldik_item'];
                $isEfektif = $kItem ? $kItem->isEfektif() : true;
                $warna = $kItem ? ($kItem->warna ?: ($isEfektif ? '#2563eb' : '#f59e0b')) : '#2563eb';
              @endphp
              <tr>
                {{-- Kolom Pekan --}}
                <td class="kaldik-col-pekan" 
                    style="background:{{ $warna }}; color:#ffffff; font-weight:900;" 
                    title="Pekan {{ $wData['week_number'] }}: {{ $kItem?->keterangan ?? 'KBM Efektif' }}"
                    @if($canManage && !$kalender->is_locked && $kItem)
                      onclick="openEditPekanModal({{ $kItem->id }}, '{{ $kItem->bulan }}', {{ $kItem->minggu_ke }}, '{{ $kItem->jenis }}', '{{ $kItem->kategori }}', '{{ addslashes($kItem->keterangan ?? '') }}', '{{ $kItem->tanggal_mulai?->format('Y-m-d') ?? '' }}', '{{ $kItem->tanggal_selesai?->format('Y-m-d') ?? '' }}')"
                    @endif>
                  M{{ $wData['week_number'] }}
                </td>

                {{-- Kolom Hari --}}
                @foreach($wData['days_data'] as $dayIdx => $dInfo)
                  @if($dInfo === null)
                    <td class="{{ $dayIdx >= 5 ? 'sun' : '' }}"></td>
                  @else
                    @php
                      $dayNum = $dInfo['day_num'];
                      $dateStr = $dInfo['date_str'];
                      $isWeekend = $dInfo['is_weekend'];
                      $dItem = $dInfo['agenda_item'];
                      $hasSpecific = $dInfo['has_specific_date'];
                      $dayEfektif = $dItem ? $dItem->isEfektif() : true;
                      $dayColor = $dItem ? ($dItem->warna ?: ($dayEfektif ? '#2563eb' : '#f59e0b')) : '#2563eb';
                      $dayTitle = $dItem ? ($dItem->keterangan . ($dItem->formatRentangTanggal() ? ' (' . $dItem->formatRentangTanggal() . ')' : '')) : ($isWeekend ? 'Akhir Pekan' : 'KBM Efektif');
                    @endphp
                    <td class="day-cell {{ $isWeekend ? 'sun' : '' }}" 
                        style="{{ !$dayEfektif ? 'background:' . $dayColor . '15;' : '' }}"
                        title="{{ $dayTitle }}"
                        @if($canManage && !$kalender->is_locked)
                          @if($hasSpecific && $dItem)
                            onclick="openEditPekanModal({{ $dItem->id }}, '{{ $dItem->bulan }}', {{ $dItem->minggu_ke }}, '{{ $dItem->jenis }}', '{{ $dItem->kategori }}', '{{ addslashes($dItem->keterangan ?? '') }}', '{{ $dItem->tanggal_mulai?->format('Y-m-d') ?? '' }}', '{{ $dItem->tanggal_selesai?->format('Y-m-d') ?? '' }}')"
                          @else
                            onclick="openTambahAgendaTanggal('{{ $dateStr }}')"
                          @endif
                        @endif>
                      @if(!$dayEfektif)
                        <span class="kaldik-day-badge agenda" style="background:{{ $dayColor }};">
                          {{ $dayNum }}
                        </span>
                      @else
                        <span class="kaldik-day-badge {{ !$isWeekend ? 'efektif' : '' }}">
                          {{ $dayNum }}
                        </span>
                      @endif
                    </td>
                  @endif
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Keterangan Agenda Khusus Bulan Ini --}}
        @if($specialAgendas->isNotEmpty())
          <div class="kaldik-agenda-list">
            @foreach($specialAgendas as $item)
              @php
                $itemColor = $item->warna ?: '#f59e0b';
              @endphp
              <div class="kaldik-agenda-row">
                <div style="display:flex; align-items:center; gap:6px; min-width:0;">
                  <span style="width:16px; height:16px; border-radius:3px; background:{{ $itemColor }}; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:8.5px; font-weight:900; flex-shrink:0;">
                    M{{ $item->minggu_ke }}
                  </span>
                  <div style="min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <span style="font-weight:700; color:var(--ak-dark);" title="{{ $item->keterangan }}">
                      {{ $item->keterangan }}
                    </span>
                    @if($item->tanggal_mulai)
                      <span style="font-size:9.5px; color:#64748b; margin-left:4px;">
                        ({{ $item->formatRentangTanggal() }})
                      </span>
                    @endif
                  </div>
                </div>

                @if($canManage && !$kalender->is_locked)
                  <div style="display:flex; align-items:center; gap:2px; flex-shrink:0;">
                    <button type="button" 
                            class="ak-btn ak-btn-secondary" 
                            style="padding:1px 5px; font-size:9.5px; height:18px;" 
                            title="Edit Agenda"
                            onclick="openEditPekanModal({{ $item->id }}, '{{ $item->bulan }}', {{ $item->minggu_ke }}, '{{ $item->jenis }}', '{{ $item->kategori }}', '{{ addslashes($item->keterangan ?? '') }}', '{{ $item->tanggal_mulai?->format('Y-m-d') ?? '' }}', '{{ $item->tanggal_selesai?->format('Y-m-d') ?? '' }}')">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('akademik.kalender.reset-item', $item->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Kembalikan pekan ini ke KBM Efektif?')">
                      @csrf
                      <button type="submit" class="ak-btn ak-btn-secondary" style="padding:1px 5px; font-size:9.5px; height:18px; color:#ef4444;" title="Reset ke KBM Normal">
                        <i class="bi bi-arrow-counterclockwise"></i>
                      </button>
                    </form>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div style="padding:6px 10px; text-align:center; font-size:10.5px; color:#16a34a; background:#f0fdf4; border-top:1px solid #dcfce7;">
            <i class="bi bi-check2-circle me-1"></i> Full KBM Efektif Tatap Muka
          </div>
        @endif
      </div>
    @endforeach
  </div>

  {{-- 4. MATRIKS PENETAPAN RPE RESMI (RINCIAN PEKAN EFEKTIF PER BULAN) --}}
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center; padding:12px 18px;">
      <h3 class="akademik-card-title" style="font-size:14px; margin:0;">
        <i class="bi bi-table text-primary me-2"></i>
        Matriks Penetapan Rincian Pekan Efektif (RPE) Semester {{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}
      </h3>
      <span class="badge" style="background:#dbeafe; color:#1e40af; font-weight:800; font-size:11px;">
        Standar RPE Waka Kurikulum
      </span>
    </div>

    <div class="akademik-card-body" style="padding:0;">
      <div class="akademik-table-wrap">
        <table class="rpe-summary-table">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">No</th>
              <th style="width:140px;">Bulan</th>
              <th style="width:120px; text-align:center;">Jumlah Pekan</th>
              <th style="width:150px; text-align:center;">Pekan Efektif KBM</th>
              <th style="width:160px; text-align:center;">Pekan Tidak Efektif</th>
              <th>Keterangan Agenda / Penjelasan Non-KBM</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totPekan = 0;
              $totEfektif = 0;
              $totNonEfektif = 0;
            @endphp
            @foreach($rpeSummary as $idx => $rpe)
              @php
                $totPekan += $rpe['total_pekan'];
                $totEfektif += $rpe['efektif'];
                $totNonEfektif += $rpe['non_efektif'];
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700;">{{ $loop->iteration }}</td>
                <td style="font-weight:800; color:var(--ak-dark);">
                  {{ $rpe['bulan'] }} {{ $rpe['year'] }}
                </td>
                <td style="text-align:center; font-weight:700;">
                  {{ $rpe['total_pekan'] }} Pekan
                </td>
                <td style="text-align:center;">
                  <span class="badge" style="background:#eff6ff; color:#1d4ed8; font-weight:800; font-size:11px; padding:3px 8px;">
                    {{ $rpe['efektif'] }} Pekan
                  </span>
                </td>
                <td style="text-align:center;">
                  @if($rpe['non_efektif'] > 0)
                    <span class="badge" style="background:#fffbeb; color:#92400e; font-weight:800; font-size:11px; padding:3px 8px;">
                      {{ $rpe['non_efektif'] }} Pekan
                    </span>
                  @else
                    <span style="color:#94a3b8;">-</span>
                  @endif
                </td>
                <td>
                  @if($rpe['agendas']->isNotEmpty())
                    <div style="display:flex; flex-direction:column; gap:3px;">
                      @foreach($rpe['agendas'] as $ag)
                        <div style="font-size:11.5px; color:#334155;">
                          <span class="badge" style="background:{{ $ag->warna ?: '#f59e0b' }}; color:#fff; font-size:9px; padding:1px 4px; margin-right:4px;">
                            M{{ $ag->minggu_ke }}
                          </span>
                          <strong>{{ $ag->keterangan }}</strong>
                          @if($ag->tanggal_mulai)
                            <span style="color:#64748b; font-size:10.5px;">({{ $ag->formatRentangTanggal() }})</span>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @else
                    <span style="color:#16a34a; font-weight:600; font-size:11px;">
                      <i class="bi bi-check me-1"></i>KBM Efektif Penuh
                    </span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2" style="text-align:center; font-weight:800;">TOTAL SEMESTER {{ $semester }}</td>
              <td style="text-align:center;">{{ $totPekan }} Pekan</td>
              <td style="text-align:center; color:#1d4ed8;">{{ $totEfektif }} Pekan</td>
              <td style="text-align:center; color:#92400e;">{{ $totNonEfektif }} Pekan</td>
              <td style="font-size:11px; font-weight:normal; color:#475569;">
                Rincian Pekan Efektif (RPE) resmi menjadi dasar perhitungan alokasi jam tatap muka PROTA &amp; PROMES guru.
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- 5. CATATAN KEBIJAKAN KURIKULUM (KOMPAK) --}}
  <div class="akademik-card" style="padding:14px 18px; margin-bottom:20px;">
    <h3 style="font-size:13px; font-weight:800; color:var(--ak-dark); margin:0 0 8px;">
      <i class="bi bi-card-text text-primary me-2"></i> Catatan Kebijakan Kaldik Waka Kurikulum
    </h3>
    @if($canManage && !$kalender->is_locked)
      <form action="{{ route('akademik.kalender.catatan', $kalender->id) }}" method="POST" style="margin:0;">
        @csrf
        <div style="display:flex; gap:10px; align-items:flex-start;">
          <textarea name="catatan" class="ak-input" rows="2" style="font-size:12px; flex:1;" placeholder="Tuliskan catatan arahan khusus pelaksanaan KBM, asesmen, atau libur...">{{ $kalender->catatan }}</textarea>
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px; height:36px;">
            <i class="bi bi-floppy me-1"></i> Simpan Catatan
          </button>
        </div>
      </form>
    @else
      <div style="font-size:12px; color:#475569; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px;">
        {{ $kalender->catatan ?: 'Belum ada catatan kebijakan khusus.' }}
      </div>
    @endif
  </div>

@else
  {{-- Empty State (Saat Kalender Belum Dibuat) --}}
  <div class="akademik-card" style="padding:40px 20px; text-align:center;">
    <i class="bi bi-calendar2-range" style="font-size:42px; color:#94a3b8; display:block; margin-bottom:12px;"></i>
    <h3 style="font-size:16px; font-weight:800; color:var(--ak-dark); margin:0 0 6px;">
      Kalender Pendidikan Semester {{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }} Belum Ditetapkan
    </h3>
    <p style="font-size:12.5px; color:#64748b; max-width:500px; margin:0 auto 18px;">
      Klik tombol di bawah untuk membuat template resmi standar SMK secara otomatis (MPLS, KBM, STS, SAS/SAT, UKK, Rapor &amp; Libur).
    </p>

    @if($canManage)
      <form action="{{ route('akademik.kalender.generate') }}" method="POST" style="margin:0;">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <button type="submit" class="ak-btn ak-btn-primary" style="font-size:13px; padding:7px 20px;">
          <i class="bi bi-magic me-1"></i> Buat Template Standar SMK
        </button>
      </form>
    @endif
  </div>
@endif

{{-- ─── MODAL TUNGGAL TERPADU: KELOLA AGENDA KALDIK (TAMBAH / EDIT PEKAN) ─── --}}
<div class="modal fade" id="modalAgendaKaldik" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
      <form id="formAgendaKaldik" method="POST" action="{{ route('akademik.kalender.store-agenda') }}">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="hidden" id="agendaItemId" name="item_id" value="">

        <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:12px 16px;">
          <h5 class="modal-title" style="font-size:14px; font-weight:800; color:var(--ak-dark);" id="modalAgendaTitle">
            <i class="bi bi-calendar-event text-primary me-2"></i> Kelola Agenda Kaldik
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" style="padding:14px 16px; display:flex; flex-direction:column; gap:12px;">
          {{-- Pilihan Tanggal --}}
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label style="font-size:11.5px; font-weight:800; color:#334155; margin:0;">
                <i class="bi bi-calendar2-range text-primary me-1"></i> Rentang Tanggal Pelaksanaan:
              </label>
              <label style="display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#4338ca; cursor:pointer; margin:0; background:#e0e7ff; padding:2px 8px; border-radius:6px;">
                <input type="checkbox" id="modalIsSingleDay" name="is_single_day" value="1" onchange="toggleSingleDayModal()">
                <span>1 Hari Saja</span>
              </label>
            </div>

            <div id="modalGridTanggal" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
              <div>
                <label id="modalLabelMulai" style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:3px; display:block;">Tanggal Mulai:</label>
                <input type="date" name="tanggal_mulai" id="modalTanggalMulai" class="ak-input" style="font-size:12px; height:36px;" onchange="handleModalTanggalChange()">
              </div>
              <div id="modalColSelesai">
                <label style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:3px; display:block;">Tanggal Selesai:</label>
                <input type="date" name="tanggal_selesai" id="modalTanggalSelesai" class="ak-input" style="font-size:12px; height:36px;" onchange="handleModalTanggalChange()">
              </div>
            </div>

            <div id="modalTanggalHelper" style="font-size:11px; color:#4f46e5; font-weight:700; margin-top:6px; display:none;">
              <i class="bi bi-check2-circle me-1"></i> <span id="modalTanggalHelperText"></span>
            </div>
          </div>

          {{-- Alokasi Bulan & Pekan --}}
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
            <div>
              <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px; display:block;">Alokasi Bulan:</label>
              <select name="bulan" id="modalBulan" class="ak-input" style="font-size:12px;" required>
                @if($semester == 1)
                  <option value="Juli">Juli</option>
                  <option value="Agustus">Agustus</option>
                  <option value="September">September</option>
                  <option value="Oktober">Oktober</option>
                  <option value="November">November</option>
                  <option value="Desember">Desember</option>
                @else
                  <option value="Januari">Januari</option>
                  <option value="Februari">Februari</option>
                  <option value="Maret">Maret</option>
                  <option value="April">April</option>
                  <option value="Mei">Mei</option>
                  <option value="Juni">Juni</option>
                @endif
              </select>
            </div>

            <div>
              <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px; display:block;">Pekan ke-:</label>
              <select name="minggu_ke" id="modalMingguKe" class="ak-input" style="font-size:12px;" required>
                <option value="1">Pekan 1 (M1)</option>
                <option value="2">Pekan 2 (M2)</option>
                <option value="3">Pekan 3 (M3)</option>
                <option value="4">Pekan 4 (M4)</option>
                <option value="5">Pekan 5 (M5)</option>
              </select>
            </div>
          </div>

          {{-- Kategori Kegiatan --}}
          <div>
            <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px; display:block;">Kategori Agenda:</label>
            <select name="kategori" id="modalKategori" class="ak-input" style="font-size:12px;" required onchange="handleModalKategoriChange()">
              <option value="kbm">KBM Efektif Tatap Muka</option>
              <option value="mpls">MPLS &amp; Masa Orientasi Jurusan</option>
              <option value="sts">Sumatif Tengah Semester (STS)</option>
              <option value="sas">Sumatif Akhir Semester (SAS / ASAS)</option>
              <option value="sat">Sumatif Akhir Tahun (SAT)</option>
              <option value="ukk">Uji Kompetensi Keahlian (UKK) Kejuruan</option>
              <option value="pkl">Praktik Kerja Lapangan (PKL)</option>
              <option value="rapor">Pengolahan Nilai &amp; Pembagian Rapor</option>
              <option value="libur">Hari Libur Semester / Nasional</option>
              <option value="lainnya">Agenda Khusus Sekolah Lainnya</option>
            </select>
          </div>

          {{-- Nama Kegiatan --}}
          <div>
            <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px; display:block;">Nama Agenda / Keterangan:</label>
            <input type="text" name="keterangan" id="modalKeterangan" class="ak-input" style="font-size:12px;" placeholder="Contoh: Sumatif Tengah Semester (STS)" required>
          </div>

          {{-- Dampak Terhadap KBM --}}
          <div>
            <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px; display:block;">Dampak KBM:</label>
            <select name="jenis" id="modalJenis" class="ak-input" style="font-size:12px;" required>
              <option value="efektif">Efektif (KBM Tatap Muka Tetap Berjalan)</option>
              <option value="non_efektif">Non-Efektif (Memotong Jam Tatap Muka / Libur)</option>
            </select>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:10px 16px; display:flex; justify-content:space-between; align-items:center;">
          <div id="modalLeftActions">
            {{-- Tombol Reset ke KBM akan muncul jika mode edit --}}
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal" style="font-size:11.5px;">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary" style="font-size:11.5px;">
              <i class="bi bi-check-lg me-1"></i> Simpan Agenda
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const namaBulanMap = {
    1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
    5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
    9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
  };

  function toggleSingleDayModal() {
    const isSingle = document.getElementById('modalIsSingleDay').checked;
    const colSelesai = document.getElementById('modalColSelesai');
    const grid = document.getElementById('modalGridTanggal');
    const labelMulai = document.getElementById('modalLabelMulai');
    const tglMulai = document.getElementById('modalTanggalMulai');
    const tglSelesai = document.getElementById('modalTanggalSelesai');

    if (isSingle) {
      if (colSelesai) colSelesai.style.display = 'none';
      if (grid) grid.style.gridTemplateColumns = '1fr';
      if (labelMulai) labelMulai.innerText = 'Pilih Tanggal:';
      if (tglMulai && tglSelesai && tglMulai.value) {
        tglSelesai.value = tglMulai.value;
      }
    } else {
      if (colSelesai) colSelesai.style.display = 'block';
      if (grid) grid.style.gridTemplateColumns = '1fr 1fr';
      if (labelMulai) labelMulai.innerText = 'Tanggal Mulai:';
    }
  }

  function handleModalTanggalChange() {
    const isSingle = document.getElementById('modalIsSingleDay').checked;
    const tglMulai = document.getElementById('modalTanggalMulai').value;
    const tglSelesaiInput = document.getElementById('modalTanggalSelesai');

    if (isSingle && tglMulai) {
      tglSelesaiInput.value = tglMulai;
    }

    if (!tglMulai) return;

    const parts = tglMulai.split('-');
    if (parts.length === 3) {
      const year = parseInt(parts[0]);
      const month = parseInt(parts[1]);
      const day = parseInt(parts[2]);

      const bulanName = namaBulanMap[month];
      const bulanSelect = document.getElementById('modalBulan');
      if (bulanSelect && bulanName) {
        bulanSelect.value = bulanName;
      }

      // Hitung pekan dalam bulan
      const firstDay = new Date(year, month - 1, 1);
      let startDayOfWeek = firstDay.getDay(); // 0: Sun, 1: Mon
      if (startDayOfWeek === 0) startDayOfWeek = 7;
      const weekNum = Math.min(5, Math.max(1, Math.ceil((day + startDayOfWeek - 1) / 7)));

      const mingguSelect = document.getElementById('modalMingguKe');
      if (mingguSelect) {
        mingguSelect.value = weekNum;
      }

      const helper = document.getElementById('modalTanggalHelper');
      const helperText = document.getElementById('modalTanggalHelperText');
      if (helper && helperText) {
        helper.style.display = 'block';
        helperText.innerText = 'Dialokasikan ke ' + (bulanName || '') + ' (Pekan ' + weekNum + ')';
      }
    }
  }

  function handleModalKategoriChange() {
    const kat = document.getElementById('modalKategori').value;
    const jenis = document.getElementById('modalJenis');
    if (kat === 'kbm') {
      jenis.value = 'efektif';
    } else {
      jenis.value = 'non_efektif';
    }
  }

  function openTambahAgendaModal() {
    const form = document.getElementById('formAgendaKaldik');
    form.action = "{{ route('akademik.kalender.store-agenda') }}";
    document.getElementById('agendaItemId').value = '';
    document.getElementById('modalAgendaTitle').innerHTML = '<i class="bi bi-calendar-plus text-primary me-2"></i> Tambah Agenda Kaldik Baru';

    document.getElementById('modalTanggalMulai').value = '';
    document.getElementById('modalTanggalSelesai').value = '';
    document.getElementById('modalIsSingleDay').checked = false;
    document.getElementById('modalKategori').value = 'sts';
    document.getElementById('modalKeterangan').value = '';
    document.getElementById('modalJenis').value = 'non_efektif';
    document.getElementById('modalTanggalHelper').style.display = 'none';
    document.getElementById('modalLeftActions').innerHTML = '';

    toggleSingleDayModal();
    const modal = new bootstrap.Modal(document.getElementById('modalAgendaKaldik'));
    modal.show();
  }

  function openTambahAgendaTanggal(dateStr) {
    openTambahAgendaModal();
    document.getElementById('modalTanggalMulai').value = dateStr;
    document.getElementById('modalTanggalSelesai').value = dateStr;
    document.getElementById('modalIsSingleDay').checked = true;
    toggleSingleDayModal();
    handleModalTanggalChange();
  }

  function openEditPekanModal(id, bulan, mingguKe, jenis, kategori, keterangan, tglMulai = '', tglSelesai = '') {
    const form = document.getElementById('formAgendaKaldik');
    form.action = "{{ url('dcc/akademik/kalender/item') }}/" + id;
    document.getElementById('agendaItemId').value = id;
    document.getElementById('modalAgendaTitle').innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i> Edit Agenda: Pekan ' + mingguKe + ' (' + bulan + ')';

    document.getElementById('modalBulan').value = bulan;
    document.getElementById('modalMingguKe').value = mingguKe;
    document.getElementById('modalJenis').value = jenis;
    document.getElementById('modalKategori').value = kategori;
    document.getElementById('modalKeterangan').value = keterangan;

    const editMulai = document.getElementById('modalTanggalMulai');
    const editSelesai = document.getElementById('modalTanggalSelesai');
    const editSingle = document.getElementById('modalIsSingleDay');

    if (tglMulai) {
      editMulai.value = tglMulai;
      editSelesai.value = tglSelesai || tglMulai;
      editSingle.checked = (tglMulai === (tglSelesai || tglMulai));
    } else {
      editMulai.value = '';
      editSelesai.value = '';
      editSingle.checked = false;
    }
    toggleSingleDayModal();
    handleModalTanggalChange();

    // Sediakan tombol reset ke KBM di kiri modal
    document.getElementById('modalLeftActions').innerHTML = `
      <form action="{{ url('dcc/akademik/kalender/item') }}/${id}/reset" method="POST" style="margin:0;" onsubmit="return confirm('Reset pekan ini kembali ke KBM Efektif normal?')">
        @csrf
        <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:11px; color:#ef4444;" title="Kembalikan ke KBM Efektif">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke KBM Normal
        </button>
      </form>
    `;

    const modal = new bootstrap.Modal(document.getElementById('modalAgendaKaldik'));
    modal.show();
  }
</script>
@endpush

@endsection
