@extends('dcc.akademik.layout')

@section('title', 'Kalender Pendidikan & RPE Sekolah — Akademik SMKN 1 Air Naningan')
@section('breadcrumb')
  <span>Kalender Pendidikan (Kaldik) &amp; RPE</span>
@endsection

@push('styles')
<style>
  .kaldik-month-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }
  .kaldik-month-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }
  .kaldik-month-card:hover {
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
  }
  .kaldik-month-header {
    background: #f8fafc;
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .kaldik-month-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--ak-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .kaldik-cal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    text-align: center;
  }
  .kaldik-cal-table th {
    padding: 6px 2px;
    font-weight: 800;
    color: #475569;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    font-size: 10.5px;
  }
  .kaldik-cal-table th.sun, .kaldik-cal-table td.sun {
    color: #dc2626;
  }
  .kaldik-cal-table td {
    padding: 4px 1px;
    border-bottom: 1px solid #f8fafc;
    border-right: 1px solid #f8fafc;
    height: 30px;
    vertical-align: middle;
  }
  .kaldik-col-mg {
    width: 32px;
    font-weight: 800;
    font-size: 10px;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0 !important;
  }
  .kaldik-day-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 11px;
    color: #1e293b;
  }
  .kaldik-day-num.efektif {
    background: #eff6ff;
    color: #1d4ed8;
  }
  .kaldik-day-num.non-efektif {
    color: #ffffff;
    font-weight: 800;
  }
  .kaldik-agenda-box {
    padding: 12px 14px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
  }
  .kaldik-agenda-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 11.5px;
  }
</style>
@endpush

@section('content')
<div class="akademik-page-head" style="margin-bottom:20px;">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-calendar2-week-fill text-primary me-2"></i>
      Kalender Pendidikan (Kaldik) &amp; Penetapan RPE Sekolah
    </h1>
    <div class="akademik-page-desc">
      Master acuan kalender pendidikan semester, rincian pekan efektif KBM, pekan asesmen, dan libur resmi SMKN 1 Air Naningan yang terintegrasi otomatis ke Prota &amp; Promes seluruh guru.
    </div>
  </div>

  <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
    @if($kalender && $canManage)
      <form action="{{ route('akademik.kalender.toggle-lock', $kalender->id) }}" method="POST" style="margin:0;">
        @csrf
        @if($kalender->is_locked)
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12.5px;" title="Buka kunci agar dapat disesuaikan">
            <i class="bi bi-unlock me-1"></i> Buka Kunci Kaldik
          </button>
        @else
          <button type="submit" class="ak-btn ak-btn-success" style="font-size:12.5px;" title="Kunci kalender sebagai acuan resmi satu sekolah">
            <i class="bi bi-lock-fill me-1"></i> Kunci Acuan Resmi Sekolah
          </button>
        @endif
      </form>
    @endif

    @if($canManage)
      <form action="{{ route('akademik.kalender.generate') }}" method="POST" style="margin:0;" onsubmit="return confirm('Generate template standar SMK? Jika data kalender semester ini sudah ada, butir pekan akan diatur ulang sesuai template standar.')">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <button type="submit" class="ak-btn ak-btn-primary" style="font-size:12.5px;">
          <i class="bi bi-magic me-1"></i> {{ $kalender ? 'Reset Template Standar SMK' : 'Buat Template Standar SMK' }}
        </button>
      </form>
    @endif
  </div>
</div>

{{-- Filter Tahun Ajaran & Semester --}}
<div class="akademik-card" style="margin-bottom:20px; padding:16px 20px;">
  <form method="GET" action="{{ route('akademik.kalender.index') }}" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin:0;">
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <div style="display:flex; align-items:center; gap:8px;">
        <label style="font-size:12.5px; font-weight:700; color:#475569; margin:0;">Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" class="ak-input" style="font-size:12.5px; height:34px; padding:2px 10px; width:180px;" onchange="this.form.submit()">
          @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}" {{ $selectedTa?->id == $ta->id ? 'selected' : '' }}>
              {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:flex; align-items:center; gap:6px;">
        <label style="font-size:12.5px; font-weight:700; color:#475569; margin:0;">Semester:</label>
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $selectedTa?->id, 'semester' => 1]) }}" 
           class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
           style="font-size:12px; height:32px; padding:4px 14px;">
           Semester 1 (Ganjil)
        </a>
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $selectedTa?->id, 'semester' => 2]) }}" 
           class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
           style="font-size:12px; height:32px; padding:4px 14px;">
           Semester 2 (Genap)
        </a>
      </div>
    </div>

    <div style="display:flex; align-items:center; gap:10px;">
      @if($kalender)
        @if($kalender->is_locked)
          <span class="ak-badge ak-badge-success" style="font-size:12px; padding:6px 12px;">
            <i class="bi bi-shield-check me-1"></i> DITETAPKAN RESMI WAKA KURIKULUM
          </span>
        @else
          <span class="ak-badge ak-badge-warning" style="font-size:12px; padding:6px 12px;">
            <i class="bi bi-pencil-square me-1"></i> Draf Penyesuaian (Belum Dikunci)
          </span>
        @endif
      @else
        <span class="ak-badge ak-badge-secondary" style="font-size:12px; padding:6px 12px;">
          <i class="bi bi-info-circle me-1"></i> Belum Dibuat
        </span>
      @endif
    </div>
  </form>
</div>

@if($kalender)
  {{-- Kartu Ringkasan RPE Resmi --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:18px 22px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
      <div>
        <h2 style="font-size:16px; font-weight:800; color:var(--ak-dark); margin:0 0 2px;">
          Rincian Pekan Efektif (RPE) Baku Sekolah
        </h2>
        <div style="font-size:12px; color:#64748b;">
          Tahun Ajaran <b>{{ $selectedTa?->nama }}</b> — Semester <b>{{ $semester == 1 ? '1 (Ganjil: Juli - Des)' : '2 (Genap: Jan - Jun)' }}</b>
        </div>
      </div>
      <div style="font-size:12px; color:#0369a1; background:#f0f9ff; border:1px solid #bae6fd; padding:6px 14px; border-radius:8px;">
        <i class="bi bi-link-45deg me-1"></i> Otomatis menjadi acuan dasar perhitungan Jam Pelajaran pada menu <b>Prota &amp; Promes</b> guru
      </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:14px;">
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Pekan Semester</div>
        <div style="font-size:24px; font-weight:900; color:var(--ak-dark); margin-top:3px;">
          {{ $kalender->total_pekan }} <span style="font-size:13px; font-weight:700; color:#64748b;">Pekan</span>
        </div>
        <div style="font-size:11px; color:#64748b; margin-top:2px;">6 Bulan Kalender</div>
      </div>

      <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase; letter-spacing:0.5px;">Pekan Efektif KBM</div>
        <div style="font-size:24px; font-weight:900; color:#1e40af; margin-top:3px;">
          {{ $kalender->pekan_efektif }} <span style="font-size:13px; font-weight:700; color:#1e40af;">Pekan</span>
        </div>
        <div style="font-size:11px; color:#1e40af; margin-top:2px;">KBM Tatap Muka Aktif</div>
      </div>

      <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:0.5px;">Pekan Non-Efektif / Cadangan</div>
        <div style="font-size:24px; font-weight:900; color:#92400e; margin-top:3px;">
          {{ $kalender->pekan_cadangan }} <span style="font-size:13px; font-weight:700; color:#92400e;">Pekan</span>
        </div>
        <div style="font-size:11px; color:#92400e; margin-top:2px;">MPLS, Asesmen, Rapor &amp; Libur</div>
      </div>

      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#166534; text-transform:uppercase; letter-spacing:0.5px;">Simulasi JP Mapel 4 JP/Mgg</div>
        <div style="font-size:24px; font-weight:900; color:#166534; margin-top:3px;">
          {{ $kalender->pekan_efektif * 4 }} <span style="font-size:13px; font-weight:700; color:#166534;">JP / Smt</span>
        </div>
        <div style="font-size:11px; color:#166534; margin-top:2px;">Rumus: Pekan Efektif × JP/Mgg</div>
      </div>
    </div>
  </div>

  {{-- Legenda Warna Kalender Pendidikan --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:12px 18px; background:#f8fafc;">
    <div style="font-size:11.5px; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:8px; letter-spacing:0.5px;">
      <i class="bi bi-palette-fill me-1 text-primary"></i> Keterangan Warna Kalender Pendidikan:
    </div>
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; font-size:12px;">
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#2563eb; display:inline-block;"></span>
        <b>KBM Efektif</b>
      </span>
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#f59e0b; display:inline-block;"></span>
        <b>MPLS / Orientasi</b>
      </span>
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#8b5cf6; display:inline-block;"></span>
        <b>Sumatif Tengah Semester (STS)</b>
      </span>
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#ec4899; display:inline-block;"></span>
        <b>Sumatif Akhir Semester (SAS / SAT)</b>
      </span>
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#f97316; display:inline-block;"></span>
        <b>Uji Kompetensi Keahlian (UKK)</b>
      </span>
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#10b981; display:inline-block;"></span>
        <b>Pembagian Rapor</b>
      </span>
      <span style="display:inline-flex; align-items:center; gap:5px;">
        <span style="width:12px; height:12px; border-radius:3px; background:#ef4444; display:inline-block;"></span>
        <b>Libur Semester / Nasional</b>
      </span>
    </div>
  </div>

  {{-- KALENDER BULANAN GRID (BENTUK KALENDER RESMI SEKOLAH) --}}
  <div class="kaldik-month-grid">
    @foreach($calendarMonths as $m)
      <div class="kaldik-month-card">
        {{-- Header Bulan --}}
        <div class="kaldik-month-header">
          <div>
            <h3 class="kaldik-month-title">
              <i class="bi bi-calendar-month text-primary"></i>
              {{ strtoupper($m['name']) }} {{ $m['year'] }}
            </h3>
            <div style="font-size:11px; color:#64748b; margin-top:2px;">
              {{ $m['days_in_month'] }} Hari · {{ $m['efektif_count'] }} Pekan Efektif KBM
            </div>
          </div>
          <span class="badge" style="background:#e0e7ff; color:#3730a3; font-weight:700; font-size:11px;">
            {{ $m['kaldik_items']->first()?->minggu_ke_semester ? ('Pekan ' . $m['kaldik_items']->first()->minggu_ke_semester . '-' . $m['kaldik_items']->last()->minggu_ke_semester) : '' }}
          </span>
        </div>

        {{-- Tabel Kalender Bulanan --}}
        <table class="kaldik-cal-table">
          <thead>
            <tr>
              <th class="kaldik-col-mg" title="Pekan Ke-">Mg</th>
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
                <td class="kaldik-col-mg" style="background:{{ $warna }}; color:#ffffff; font-weight:900;" title="{{ $kItem?->keterangan }}">
                  M{{ $wData['week_number'] }}
                </td>

                {{-- Kolom 7 Hari (Senin - Minggu) --}}
                @foreach($wData['days'] as $dayIdx => $dayNum)
                  @php
                    $isWeekend = ($dayIdx >= 5);
                  @endphp
                  <td class="{{ $isWeekend ? 'sun' : '' }}" style="{{ $dayNum && !$isEfektif ? 'background:' . $warna . '15;' : '' }}">
                    @if($dayNum)
                      @if(!$isEfektif)
                        <span class="kaldik-day-num non-efektif" style="background:{{ $warna }};" title="{{ $kItem?->keterangan }}">
                          {{ $dayNum }}
                        </span>
                      @else
                        <span class="kaldik-day-num {{ !$isWeekend ? 'efektif' : '' }}">
                          {{ $dayNum }}
                        </span>
                      @endif
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Agenda / Keterangan Pekan Bulan Ini --}}
        <div class="kaldik-agenda-box">
          <div style="font-size:10.5px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
            Agenda Pekan:
          </div>
          @foreach($m['kaldik_items'] as $item)
            @php
              $isEf = $item->isEfektif();
              $itemColor = $item->warna ?: ($isEf ? '#2563eb' : '#f59e0b');
            @endphp
            <div class="kaldik-agenda-item" style="background:{{ $isEf ? '#f8fafc' : '#fff7ed' }}; border:1px solid {{ $isEf ? '#e2e8f0' : '#fed7aa' }};">
              <div style="display:flex; align-items:center; gap:7px; min-width:0;">
                <span style="width:20px; height:20px; border-radius:4px; background:{{ $itemColor }}; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:9.5px; font-weight:900; flex-shrink:0;">
                  M{{ $item->minggu_ke }}
                </span>
                <div style="min-width:0;">
                  <span class="badge" style="background:{{ $itemColor }}; color:#ffffff; font-size:8.5px; font-weight:800; padding:1px 5px;">
                    {{ $item->getLabelSingkat() }}
                  </span>
                  <span style="font-size:11px; font-weight:700; color:var(--ak-dark); margin-left:4px;" title="{{ $item->keterangan }}">
                    {{ $item->keterangan ?: ($isEf ? 'KBM Efektif Tatap Muka' : 'Agenda Non-KBM') }}
                  </span>
                </div>
              </div>

              @if($canManage && !$kalender->is_locked)
                <button type="button" 
                        class="ak-btn ak-btn-secondary" 
                        style="padding:2px 6px; font-size:10px; height:22px; flex-shrink:0;" 
                        onclick="openEditPekanModal({{ $item->id }}, '{{ $item->bulan }}', {{ $item->minggu_ke }}, '{{ $item->jenis }}', '{{ $item->kategori }}', '{{ addslashes($item->keterangan ?? '') }}')">
                  <i class="bi bi-pencil"></i>
                </button>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  {{-- Catatan Kebijakan Kurikulum --}}
  <div class="akademik-card" style="padding:16px 20px;">
    <h3 style="font-size:14px; font-weight:800; color:var(--ak-dark); margin:0 0 8px;">
      <i class="bi bi-card-text text-primary me-2"></i> Catatan Kebijakan Kaldik &amp; KBM Waka Kurikulum
    </h3>
    @if($canManage && !$kalender->is_locked)
      <form action="{{ route('akademik.kalender.catatan', $kalender->id) }}" method="POST" style="margin:0;">
        @csrf
        <div style="display:flex; gap:10px; align-items:flex-start;">
          <textarea name="catatan" class="ak-input" rows="2" style="font-size:12.5px; flex:1;" placeholder="Tuliskan catatan khusus Kaldik, misalnya instruksi pelaksanaan STS/SAS, agenda uji sertifikasi kompetensi, atau penyesuaian jam KBM...">{{ $kalender->catatan }}</textarea>
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12.5px; height:38px;">
            <i class="bi bi-floppy me-1"></i> Simpan Catatan
          </button>
        </div>
      </form>
    @else
      <div style="font-size:13px; color:#475569; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px;">
        {{ $kalender->catatan ?: 'Belum ada catatan kebijakan khusus.' }}
      </div>
    @endif
  </div>

@else
  {{-- Empty State: Belum ada Kalender --}}
  <div class="akademik-card" style="padding:50px 20px; text-align:center;">
    <i class="bi bi-calendar2-range" style="font-size:42px; color:#94a3b8; display:block; margin-bottom:14px;"></i>
    <h3 style="font-size:17px; font-weight:800; color:var(--ak-dark); margin:0 0 6px;">
      Kalender Pendidikan Semester {{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }} Belum Dibuat
    </h3>
    <p style="font-size:13px; color:#64748b; max-width:540px; margin:0 auto 20px;">
      Kalender Pendidikan dan Penetapan RPE semester ini belum diterbitkan oleh Waka Kurikulum. Klik tombol di bawah untuk membuat template otomatis standar SMK (MPLS, KBM, STS, SAS/SAT, UKK, Rapor &amp; Libur).
    </p>

    @if($canManage)
      <form action="{{ route('akademik.kalender.generate') }}" method="POST" style="margin:0;">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <button type="submit" class="ak-btn ak-btn-primary" style="font-size:13px; padding:8px 20px;">
          <i class="bi bi-magic me-1"></i> Buat Template Standar Kaldik SMK Sekarang
        </button>
      </form>
    @endif
  </div>
@endif

{{-- Modal Edit Pekan --}}
<div class="modal fade" id="modalEditPekan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
      <form id="formEditPekan" method="POST">
        @csrf
        <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:14px 18px;">
          <h5 class="modal-title" style="font-size:15px; font-weight:800; color:var(--ak-dark);" id="modalEditPekanTitle">
            Edit Status Pekan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="padding:16px 18px; display:flex; flex-direction:column; gap:12px;">
          <div>
            <label style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px; display:block;">Jenis Pekan:</label>
            <select name="jenis" id="inputJenis" class="ak-input" style="font-size:12.5px;" onchange="handleJenisChange()">
              <option value="efektif">Efektif (KBM Tatap Muka Aktif)</option>
              <option value="non_efektif">Non-Efektif (Asesmen, MPLS, Agenda Khusus, Libur)</option>
            </select>
          </div>

          <div>
            <label style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px; display:block;">Kategori Agenda:</label>
            <select name="kategori" id="inputKategori" class="ak-input" style="font-size:12.5px;">
              <option value="kbm">KBM Tatap Muka</option>
              <option value="mpls">MPLS &amp; Pengenalan Budaya Kerja</option>
              <option value="sts">Sumatif Tengah Semester (STS)</option>
              <option value="sas">Sumatif Akhir Semester (SAS / ASAS)</option>
              <option value="sat">Sumatif Akhir Tahun (SAT)</option>
              <option value="ukk">Uji Kompetensi Keahlian (UKK) Kejuruan</option>
              <option value="pkl">Praktik Kerja Lapangan (PKL)</option>
              <option value="rapor">Pengolahan Nilai &amp; Pembagian Rapor</option>
              <option value="libur">Hari Libur Semester / Nasional</option>
              <option value="lainnya">Agenda Lainnya</option>
            </select>
          </div>

          <div>
            <label style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px; display:block;">Keterangan / Nama Kegiatan:</label>
            <input type="text" name="keterangan" id="inputKeterangan" class="ak-input" style="font-size:12.5px;" placeholder="Contoh: KBM Efektif Pekan 1 atau Sumatif Tengah Semester">
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 18px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal" style="font-size:12px;">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary" style="font-size:12px;">
            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function openEditPekanModal(id, bulan, mingguKe, jenis, kategori, keterangan) {
    const form = document.getElementById('formEditPekan');
    form.action = "{{ url('dcc/akademik/kalender/item') }}/" + id;
    
    document.getElementById('modalEditPekanTitle').innerText = 'Edit Pekan ke-' + mingguKe + ' (' + bulan + ')';
    document.getElementById('inputJenis').value = jenis;
    document.getElementById('inputKategori').value = kategori;
    document.getElementById('inputKeterangan').value = keterangan;

    const modal = new bootstrap.Modal(document.getElementById('modalEditPekan'));
    modal.show();
  }

  function handleJenisChange() {
    const jenis = document.getElementById('inputJenis').value;
    const kategori = document.getElementById('inputKategori');
    if (jenis === 'efektif') {
      kategori.value = 'kbm';
    } else if (kategori.value === 'kbm') {
      kategori.value = 'sts';
    }
  }
</script>
@endpush

@endsection
