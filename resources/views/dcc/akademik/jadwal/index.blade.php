@extends('dcc.akademik.layout')

@php
  $user = auth()->user();
  $canEditJadwal = $canEditJadwal ?? ($user && ($user->isAdmin() || $user->isWakaKurikulum() || $user->hasAvailableRole('admin') || $user->hasAvailableRole('waka_kurikulum')));
  $currentGuruId = $currentGuruId ?? ($user?->guru_id ?? ($user?->guru?->id ?? null));
  $currentStep = ($tab === 'distribusi' ? 2 : ($tab === 'piket' ? 4 : 3));
@endphp

@section('title', $tab === 'distribusi' ? 'Langkah 2: SK Pembagian Tugas Guru' : ($tab === 'piket' ? 'Langkah 4: Jadwal Guru Piket' : 'Langkah 3: Jadwal Pelajaran & Roster'))
@section('breadcrumb', $tab === 'distribusi' ? 'SK Pembagian Tugas' : ($tab === 'piket' ? 'Jadwal Guru Piket' : 'Jadwal Pelajaran'))

@section('content')

{{-- Header Card SK Pembagian Tugas --}}
@if($tab === 'distribusi')
  @php $subtab = request('subtab', 'matriks'); @endphp
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:16px 20px; background:#ffffff;">
      <div>
        <h2 style="font-weight:800; font-size:18px; color:#000000; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-person-lines-fill"></i>
          SK Pembagian Tugas Mengajar &amp; Tugas Tambahan Guru
        </h2>
        <div style="font-size:12px; color:#6b7280; margin-top:3px;">
          Tahun Ajaran <b>{{ $ta?->tahun_ajaran ?? '2025/2026' }}</b> · Semester <b>{{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}</b>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        {{-- Switch Semester --}}
        <div class="btn-group" role="group">
          <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'subtab' => $subtab, 'semester' => 1]) }}" class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:6px 12px;">
            Sem 1 (Ganjil)
          </a>
          <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'subtab' => $subtab, 'semester' => 2]) }}" class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:6px 12px;">
            Sem 2 (Genap)
          </a>
        </div>

        {{-- Cetak SK Resmi --}}
        <a href="{{ route('akademik.jadwal.cetak-sk', ['semester' => $semester]) }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:12.5px; font-weight:700; white-space:nowrap;">
          <i class="bi bi-file-earmark-text me-1"></i> Cetak Dokumen SK (PDF)
        </a>

        @if($canEditJadwal)
        {{-- Tambah Alokasi Baru --}}
        <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahDistribusi" style="font-size:12.5px; font-weight:700; white-space:nowrap;">
          <i class="bi bi-plus-lg me-1"></i> Tambah Alokasi
        </button>
        @endif
      </div>
    </div>

    {{-- Subtab Distribusi Langsung di Header Card --}}
    <div style="display:flex; border-bottom:1px solid #e5e7eb; background:#f9fafb; padding:0 16px; overflow-x:auto;">
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'subtab' => 'matriks', 'semester' => $semester]) }}"
         style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $subtab == 'matriks' ? '#000000' : 'transparent' }}; color: {{ $subtab == 'matriks' ? '#000000' : '#6b7280' }};">
        <i class="bi bi-grid-3x3"></i> Matriks Mapel × Rombel
      </a>
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'subtab' => 'beban', 'semester' => $semester]) }}"
         style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $subtab == 'beban' ? '#000000' : 'transparent' }}; color: {{ $subtab == 'beban' ? '#000000' : '#6b7280' }};">
        <i class="bi bi-person-check"></i> Rekap Beban Guru &amp; Tugas Tambahan
      </a>
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi', 'subtab' => 'daftar', 'semester' => $semester]) }}"
         style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $subtab == 'daftar' ? '#000000' : 'transparent' }}; color: {{ $subtab == 'daftar' ? '#000000' : '#6b7280' }};">
        <i class="bi bi-list-ul"></i> Daftar Rinci Alokasi
      </a>
    </div>
  </div>

@elseif($tab === 'piket')
  {{-- ========================================================================= --}}
  {{-- PENUGASAN JADWAL GURU PIKET                                               --}}
  {{-- ========================================================================= --}}
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:16px 20px; background:#ffffff;">
      <div>
        <h2 style="font-weight:800; font-size:18px; color:#000; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-person-badge"></i>
          Penugasan Jadwal Waka &amp; Guru Piket Harian
        </h2>
        <div style="font-size:12px; color:#6b7280; margin-top:3px;">
          Tahun Ajaran <b>{{ $ta?->tahun_ajaran ?? '2025/2026' }}</b> · Semester <b>{{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}</b> — Pengawasan kehadiran guru, keterlambatan siswa, dan ketertiban KBM harian.
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <div class="btn-group" role="group">
          <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket', 'semester' => 1]) }}" class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:6px 12px;">
            Sem 1 (Ganjil)
          </a>
          <a href="{{ route('akademik.jadwal.index', ['tab' => 'piket', 'semester' => 2]) }}" class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" style="font-size:12px; padding:6px 12px;">
            Sem 2 (Genap)
          </a>
        </div>
        @if($canEditJadwal || ($user && ($user->isWakaKesiswaan() || $user->hasAvailableRole('waka_kesiswaan'))))
        <form action="{{ route('akademik.jadwal.piket.sync') }}" method="POST" style="margin:0;">
          @csrf
          <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
          <input type="hidden" name="semester" value="{{ $semester }}">
          <button type="submit" class="ak-btn" style="font-size:12px; font-weight:700; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;" title="Sinkronkan ulang seluruh penugasan piket ke Meja Piket SIRANI">
            <i class="bi bi-arrow-repeat me-1"></i> Sinkronkan ke SIRANI
          </button>
        </form>
        @endif
        <a href="{{ route('piket.index') }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:12.5px; font-weight:700;" title="Buka layar operasional Meja Piket">
          <i class="bi bi-box-arrow-up-right me-1"></i> Buka Meja Piket SIRANI
        </a>
        <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester]) }}" class="ak-btn ak-btn-secondary" style="font-size:12.5px; font-weight:700;">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Roster Jadwal
        </a>
      </div>
    </div>
  </div>

@else
  {{-- ========================================================================= --}}
  {{-- PENYUSUNAN JADWAL PELAJARAN (ROSTER MINGGUAN)                             --}}
  {{-- ========================================================================= --}}
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:16px 20px; background:#ffffff;">
      <div>
        <h2 style="font-weight:800; font-size:18px; color:#000; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-calendar3-week"></i>
          Penyusunan Jadwal Pelajaran (Roster Mingguan)
        </h2>
        <div style="font-size:12px; color:#6b7280; margin-top:3px;">
          Tahun Ajaran <b>{{ $ta?->tahun_ajaran ?? '2025/2026' }}</b> · Semester <b>{{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}</b> — Disusun berdasarkan SK Pembagian Tugas dengan proteksi anti-bentrok guru &amp; lab.
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

        {{-- Dropdown Cetak Dokumen Resmi Roster --}}
        <div class="dropdown">
          <button class="ak-btn ak-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-size:12.5px; font-weight:700;">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen Roster
          </button>
          <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.12); border:1px solid #e2e8f0; font-size:13px; min-width:260px; padding:6px 0;">
            <li>
              <a class="dropdown-item py-2" href="{{ route('akademik.jadwal.cetak', ['semester' => $semester]) }}" target="_blank">
                <i class="bi bi-grid-3x3-gap-fill text-indigo me-2"></i> <b>Roster Jadwal Sekolah</b>
                <div style="font-size:11px; color:#64748b; margin-left:24px;">Matriks Keseluruhan Rombel &amp; Guru</div>
              </a>
            </li>
            <li>
              <a class="dropdown-item py-2" href="{{ route('akademik.jadwal.cetak-kelas', ['semester' => $semester]) }}" target="_blank">
                <i class="bi bi-mortarboard-fill text-success me-2"></i> <b>Jadwal per Rombel / Kelas</b>
                <div style="font-size:11px; color:#64748b; margin-left:24px;">Siap Ditempel di Papan Pengumuman Kelas</div>
              </a>
            </li>
            <li>
              <a class="dropdown-item py-2" href="{{ route('akademik.jadwal.cetak-lab', ['semester' => $semester]) }}" target="_blank">
                <i class="bi bi-display-fill text-info me-2"></i> <b>Jadwal Ruang Lab Komputer</b>
                <div style="font-size:11px; color:#64748b; margin-left:24px;">Siap Ditempel di Pintu Masuk Lab</div>
              </a>
            </li>
          </ul>
        </div>

        @if($canEditJadwal)
        {{-- Sinkron Kode Guru Hirarki --}}
        <form action="{{ route('akademik.jadwal.sync-kode-hierarki') }}" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="ak-btn" style="font-size:12.5px; font-weight:700; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;" title="Sinkronkan nomor urut kode guru otomatis sesuai hirarki struktural sekolah (Kepsek #1, Wakakur #2, Wakasis #3, Waka Sarpras #4, Waka Hubin #5, Kaprog #6+, dst)">
            <i class="bi bi-diagram-3-fill me-1"></i> Sinkron Hirarki Guru
          </button>
        </form>

        {{-- Tombol Otomatisasi Jadwal 1-Klik --}}
        <button type="button" class="ak-btn" data-bs-toggle="modal" data-bs-target="#modalAutoScheduler" style="font-size:12.5px; background:linear-gradient(135deg, #059669, #10b981); color:#ffffff; border:none; box-shadow:0 4px 12px rgba(16,185,129,0.3); font-weight:700;">
          <i class="bi bi-magic me-1"></i> ✨ Otomatisasi Jadwal (1-Klik)
        </button>

        {{-- Tombol Isi Blok Jadwal --}}
        <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalFormulasiBlok" style="font-size:12.5px; font-weight:700;">
          <i class="bi bi-lightning-charge-fill me-1"></i> + Formulasi Blok Jam
        </button>
        @else
        <span class="ak-badge ak-badge-secondary" style="font-size:12px; font-weight:700; padding:6px 12px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;">
          <i class="bi bi-eye me-1"></i> Mode Baca
        </span>
        @endif
      </div>
    </div>

    {{-- Navigation Tabs Roster --}}
    <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; padding:0 16px; overflow-x:auto;">
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester]) }}"
         style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'roster' ? 'var(--ak-primary)' : 'transparent' }}; color: {{ $tab == 'roster' ? 'var(--ak-primary)' : '#64748b' }};">
        <i class="bi bi-grid-3x3-gap-fill"></i> Matriks Roster Jadwal
      </a>
      @if($canEditJadwal)
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'formulasi', 'semester' => $semester]) }}"
         style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'formulasi' ? 'var(--ak-primary)' : 'transparent' }}; color: {{ $tab == 'formulasi' ? 'var(--ak-primary)' : '#64748b' }};">
        <i class="bi bi-lightning-charge-fill"></i> Formulasi Blok Cepat
      </a>
      <a href="{{ route('akademik.jadwal.index', ['tab' => 'pukul', 'semester' => $semester]) }}"
         style="padding:12px 18px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; border-bottom: 2.5px solid {{ $tab == 'pukul' ? '#f59e0b' : 'transparent' }}; color: {{ $tab == 'pukul' ? '#b45309' : '#64748b' }};">
        <i class="bi bi-clock-history"></i> Atur Pukul KBM &amp; Istirahat
      </a>
      @endif
    </div>
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px; font-weight:600;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- TAB PUKUL: Atur Waktu KBM + Istirahat (Fully Flexible) --}}
@if($tab === 'pukul')
<div class="akademik-card" style="margin-bottom:24px; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.05); overflow:hidden; background:#fff;">
  <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #e2e8f0; padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <h3 style="font-weight:800; font-size:16px; margin:0; color:#1e293b; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clock-history text-primary"></i> Atur Pukul KBM &amp; Jam Istirahat
        </h3>
        <div style="font-size:12.5px; color:#64748b; margin-top:3px;">
          Kelola nama sesi dan rentang waktu KBM atau istirahat per hari secara bebas dan fleksibel.
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        {{-- Info legend --}}
        <div style="display:flex; gap:6px; align-items:center; font-size:11.5px;">
          <span style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 10px; border-radius:6px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
            <i class="bi bi-mortarboard-fill"></i> Jam KBM
          </span>
          <span style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; padding:4px 10px; border-radius:6px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
            <i class="bi bi-cup-hot-fill"></i> Istirahat
          </span>
          <span style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; padding:4px 10px; border-radius:6px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
            <i class="bi bi-flag-fill"></i> Khusus
          </span>
        </div>
        <form action="{{ route('akademik.jadwal.update-pukul') }}" method="POST" style="margin:0;" onsubmit="return confirm('⚠️ Reset SEMUA pukul dan istirahat ke standar resmi SMKN 1 Air Naningan?\n\nSemua perubahan manual akan hilang!')">
          @csrf
          <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
          <input type="hidden" name="semester" value="{{ $semester }}">
          <input type="hidden" name="reset_default" value="1">
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px; font-weight:700; border-radius:8px; padding:6px 14px; background:#f8fafc; border:1px solid #cbd5e1; color:#475569;">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Standar
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="akademik-card-body" style="padding:0; overflow-x:auto;">
    <form action="{{ route('akademik.jadwal.update-pukul') }}" method="POST" id="formAturPukul">
      @csrf
      <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
      <input type="hidden" name="semester" value="{{ $semester }}">

      @php $globalRowIdx = 0; @endphp

      @foreach($jadwalWaktuFull as $hKbm => $rows)
      <div style="margin-bottom:0; border-bottom:2px solid #e2e8f0;">
        {{-- Header Hari --}}
        <div style="padding:10px 20px; background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; display:flex; align-items:center; gap:12px; position:sticky; top:0; z-index:5;">
          <span style="font-weight:900; font-size:14px; letter-spacing:0.8px;">{{ $hKbm }}</span>
          <span style="background:rgba(255,255,255,0.18); padding:2px 10px; border-radius:20px; font-size:11.5px; font-weight:700; color:#e0e7ff;">{{ count($rows) }} Sesi</span>
          <button type="button" onclick="tambahIstirahat('{{ $hKbm }}')" style="margin-left:auto; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.35); color:#fff; padding:5px 14px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s;">
            <i class="bi bi-plus-circle-fill"></i> Tambah Istirahat
          </button>
        </div>

        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
              <th style="padding:10px 16px; text-align:center; width:70px; color:#64748b; font-weight:800; font-size:11px; letter-spacing:0.5px;">SESI</th>
              <th style="padding:10px 16px; text-align:left; width:340px; color:#64748b; font-weight:800; font-size:11px; letter-spacing:0.5px;">LABEL / NAMA SESI</th>
              <th style="padding:10px 16px; text-align:left; color:#64748b; font-weight:800; font-size:11px; letter-spacing:0.5px;">PUKUL <span style="font-weight:500; color:#94a3b8; text-transform:none;">(format: HH.MM - HH.MM)</span></th>
              <th style="padding:10px 16px; text-align:center; width:60px; color:#64748b;"></th>
            </tr>
          </thead>
          <tbody id="tbody-{{ $hKbm }}">
            @foreach($rows as $row)
              @php
                $tipe    = $row['tipe'] ?? 'jam';
                $jamKe   = $row['jam_ke'] ?? 0;
                $urutan  = $row['urutan'] ?? $loop->index;
                $label   = $row['label'] ?? ($tipe === 'istirahat' ? 'Istirahat' : 'Jam ' . $jamKe);
                $pukul   = $row['pukul'] ?? '';

                // Warna baris per tipe
                $bgRow = match($tipe) {
                  'istirahat' => '#fff5f5',
                  'khusus'    => '#f0fdf4',
                  default     => ($loop->odd ? '#ffffff' : '#f8fafc'),
                };
                $badgeStyle = match($tipe) {
                  'istirahat' => 'background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;',
                  'khusus'    => 'background:#dcfce7; color:#15803d; border:1px solid #86efac;',
                  default     => 'background:#dbeafe; color:#1e40af; border:1px solid #93c5fd;',
                };
                $rowId = $globalRowIdx;
              @endphp
              <tr style="background:{{ $bgRow }}; border-bottom:1px solid #edf2f7;" id="row-{{ $rowId }}" data-tipe="{{ $tipe }}">
                {{-- Hidden fields --}}
                <input type="hidden" name="rows[{{ $rowId }}][hari]" value="{{ $hKbm }}">
                <input type="hidden" name="rows[{{ $rowId }}][jam_ke]" value="{{ $jamKe }}">
                <input type="hidden" name="rows[{{ $rowId }}][tipe]" value="{{ $tipe }}">
                <input type="hidden" name="rows[{{ $rowId }}][urutan]" value="{{ $urutan }}">

                <td style="padding:10px 16px; text-align:center;">
                  <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:28px; padding:0 8px; border-radius:6px; font-size:12px; font-weight:800; {{ $badgeStyle }}">
                    @if($tipe === 'istirahat') ☕ @elseif($tipe === 'khusus') 📢 @else {{ $jamKe }} @endif
                  </span>
                </td>
                <td style="padding:8px 16px;">
                  <input type="text" name="rows[{{ $rowId }}][label]" value="{{ $label }}"
                    style="border:1.5px solid {{ $tipe === 'istirahat' ? '#fca5a5' : ($tipe === 'khusus' ? '#86efac' : '#cbd5e1') }}; border-radius:8px; padding:7px 12px; font-size:13px; font-weight:700; width:100%; color:{{ $tipe === 'istirahat' ? '#991b1b' : ($tipe === 'khusus' ? '#15803d' : '#1e293b') }}; background:{{ $tipe === 'istirahat' ? '#fff1f2' : ($tipe === 'khusus' ? '#f0fdf4' : '#ffffff') }}; transition:all 0.2s;"
                    placeholder="Nama sesi / kegiatan">
                </td>
                <td style="padding:8px 16px;">
                  <input type="text" name="rows[{{ $rowId }}][pukul]" value="{{ $pukul }}"
                    style="border:1.5px solid #cbd5e1; border-radius:8px; padding:7px 14px; font-size:13.5px; font-weight:800; width:220px; color:#0f172a; font-family:var(--font-mono, monospace); letter-spacing:0.5px; background:#ffffff;"
                    placeholder="07.15 - 08.00">
                </td>
                <td style="padding:8px 16px; text-align:center;">
                  @if($tipe === 'istirahat')
                    <button type="button" onclick="hapusBaris({{ $rowId }})"
                      style="background:#fef2f2; border:1px solid #fecaca; color:#ef4444; border-radius:8px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all 0.2s;" title="Hapus baris istirahat ini">
                      <i class="bi bi-trash"></i>
                    </button>
                  @endif
                </td>
              </tr>
              @php $globalRowIdx++; @endphp
            @endforeach
          </tbody>
        </table>
      </div>
      @endforeach

      <div style="padding:16px 24px; background:#ffffff; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; position:sticky; bottom:0; z-index:10; box-shadow:0 -4px 12px rgba(0,0,0,0.04);">
        <div style="font-size:12.5px; color:#64748b; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-info-circle-fill text-primary"></i> Pukul istirahat tidak mempengaruhi slot KBM, hanya tampilan di roster dan berkas cetak.
        </div>
        <div style="display:flex; gap:10px;">
          <a href="{{ route('akademik.jadwal.index', ['tab'=>'roster','semester'=>$semester]) }}" class="ak-btn ak-btn-secondary" style="font-weight:700; padding:9px 18px; border-radius:8px;">Batal</a>
          <button type="submit" class="ak-btn ak-btn-primary" style="font-weight:800; font-size:13px; padding:9px 22px; border-radius:8px; box-shadow:0 2px 8px rgba(30,64,175,0.25);">
            <i class="bi bi-floppy-fill me-1"></i> Simpan Konfigurasi Pukul
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// Hapus baris istirahat dari tampilan (row akan di-remove dan tidak ikut submit)
function hapusBaris(rowId) {
  if (!confirm('Hapus baris istirahat ini dari jadwal hari ini?')) return;
  const row = document.getElementById('row-' + rowId);
  if (row) {
    // Set inputs to empty agar server skip baris ini
    row.querySelectorAll('input[name$="[pukul]"]').forEach(i => i.value = '');
    row.style.opacity = '0.3';
    row.style.textDecoration = 'line-through';
    row.style.pointerEvents = 'none';
    // Disable inputs
    row.querySelectorAll('input').forEach(i => i.disabled = true);
    row.style.display = 'none';
  }
}

// Tambah baris istirahat baru untuk hari tertentu
let newRowCounter = {{ $globalRowIdx ?? 999 }};
function tambahIstirahat(hari) {
  const tbody = document.getElementById('tbody-' + hari);
  if (!tbody) return;

  const idx = newRowCounter++;
  const tr = document.createElement('tr');
  tr.id = 'row-' + idx;
  tr.dataset.tipe = 'istirahat';
  tr.style.cssText = 'background:#fff5f5; border-bottom:1px solid #edf2f7;';

  tr.innerHTML = `
    <input type="hidden" name="rows[${idx}][hari]" value="${hari}">
    <input type="hidden" name="rows[${idx}][jam_ke]" value="-9">
    <input type="hidden" name="rows[${idx}][tipe]" value="istirahat">
    <input type="hidden" name="rows[${idx}][urutan]" value="999">
    <td style="padding:10px 16px; text-align:center;">
      <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:28px; padding:0 8px; border-radius:6px; font-size:12px; font-weight:800; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;">☕</span>
    </td>
    <td style="padding:8px 16px;">
      <input type="text" name="rows[${idx}][label]" value="Istirahat"
        style="border:1.5px solid #fca5a5; border-radius:8px; padding:7px 12px; font-size:13px; font-weight:700; width:100%; color:#991b1b; background:#fff1f2;"
        placeholder="Nama istirahat (mis: Sholat Dzuhur)">
    </td>
    <td style="padding:8px 16px;">
      <input type="text" name="rows[${idx}][pukul]" value=""
        style="border:1.5px solid #cbd5e1; border-radius:8px; padding:7px 14px; font-size:13.5px; font-weight:800; width:220px; color:#0f172a; font-family:var(--font-mono, monospace); letter-spacing:0.5px; background:#ffffff;"
        placeholder="12.40 - 13.10" required>
    </td>
    <td style="padding:8px 16px; text-align:center;">
      <button type="button" onclick="hapusBaris(${idx})"
        style="background:#fef2f2; border:1px solid #fecaca; color:#ef4444; border-radius:8px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px;" title="Hapus baris istirahat ini">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  `;

  // Append ke akhir tbody hari ini
  tbody.appendChild(tr);
  // Focus input pukul
  tr.querySelector('input[name$="[pukul]"]')?.focus();
}
</script>
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
        @if($canEditJadwal)
          Klik pada sel jadwal mana pun untuk mengubah atau mengosongkan slot secara instan.
        @else
          Mode Baca: Matriks jadwal KBM mingguan resmi seluruh rombel dan pendidik SMKN 1 Air Naningan.
        @endif
      </div>
    </div>
    {{-- Filter Hari & Sorot Guru --}}
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <div style="display:flex; gap:6px; align-items:center;">
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

      {{-- Sorot Guru Dropdown --}}
      <div style="display:flex; align-items:center; gap:6px; background:#f8fafc; padding:3px 8px; border-radius:8px; border:1px solid #e2e8f0;">
        <label for="filterHighlightGuru" style="font-size:11.5px; font-weight:700; color:#475569; white-space:nowrap; margin:0;">
          <i class="bi bi-funnel-fill text-primary me-1"></i> Sorot Guru:
        </label>
        <select id="filterHighlightGuru" class="ak-select" onchange="highlightGuruSchedule(this.value)" style="font-size:11.5px; padding:3px 8px; width:auto; min-width:170px; height:28px;">
          <option value="">-- Tampilkan Semua --</option>
          @if($currentGuruId)
            @php $me = $gurus->firstWhere('id', $currentGuruId); @endphp
            @if($me)
              <option value="{{ $me->id }}">⭐ Jadwal Saya ({{ $me->nama }})</option>
            @endif
          @endif
          @foreach($gurus as $g)
            @if($g->id != $currentGuruId)
              <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
            @endif
          @endforeach
        </select>
      </div>
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
      $defaultScheduleTimes = \App\Models\AkademikJadwalWaktu::defaultSchedule();
    @endphp

    <table class="roster-table">
      <colgroup>
        <col style="width: 3.5%;">
        <col style="width: 3.5%;">
        <col style="width: 8%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 4.5%;">
        <col style="width: 4.5%;">
        <col style="width: 16%;">
      </colgroup>
      <thead>
        <tr>
          <th rowspan="2" class="roster-th-dark">HARI</th>
          <th rowspan="2" class="roster-th-dark">JAM</th>
          <th rowspan="2" class="roster-th-dark">PUKUL</th>
          <th colspan="3" class="roster-th-kelas-x roster-col-divider">KELAS X / PROGRAM KEAHLIAN</th>
          <th colspan="3" class="roster-th-kelas-xi roster-col-divider">KELAS XI / PROGRAM KEAHLIAN</th>
          <th colspan="2" class="roster-th-kelas-xii roster-col-divider">KELAS XII / PKL</th>
          <th rowspan="2" class="roster-th-piket"><i class="bi bi-shield-check me-1"></i> PETUGAS PIKET</th>
        </tr>
        <tr>
          <th class="roster-th-sub th-sub-aphp"><span class="sub-pill pill-aphp">APHP</span></th>
          <th class="roster-th-sub th-sub-rpl"><span class="sub-pill pill-rpl">RPL</span></th>
          <th class="roster-th-sub th-sub-tsm roster-col-divider"><span class="sub-pill pill-tsm">TSM</span></th>
          <th class="roster-th-sub th-sub-aphp"><span class="sub-pill pill-aphp">APHP</span></th>
          <th class="roster-th-sub th-sub-rpl"><span class="sub-pill pill-rpl">RPL</span></th>
          <th class="roster-th-sub th-sub-tsm roster-col-divider"><span class="sub-pill pill-tsm">TSM</span></th>
          <th class="roster-th-sub th-sub-pkl"><span class="sub-pill pill-pkl">APHP</span></th>
          <th class="roster-th-sub th-sub-pkl roster-col-divider"><span class="sub-pill pill-pkl">RPL</span></th>
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
              <div class="roster-day-badge day-badge-{{ strtolower($day) }}">
                {{ $day }}
              </div>
            </th>
            <td class="roster-cell-jam">0</td>
            <td class="roster-cell-pukul">
              {{ $day === 'SENIN' ? '07.15 - 08.15' : ($isJumat ? '07.15 - 08.00' : '07.15 - 07.30') }}
            </td>
            <td colspan="8" class="{{ $day === 'SENIN' ? 'roster-banner-upacara' : ($isJumat ? 'roster-banner-jumat' : 'roster-banner-apel') }} roster-col-divider">
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
              <div class="roster-piket-box">
                <div class="roster-piket-card-waka">
                  <span class="roster-piket-role-tag waka">Waka Piket</span>
                  <div class="roster-piket-name-waka" title="{{ $piket?->wakaPiket?->nama ?? '-' }}">
                    {{ $piket?->wakaPiket?->nama ?? '-' }}
                  </div>
                </div>
                <div class="roster-piket-card-guru">
                  <span class="roster-piket-role-tag guru">Guru Piket</span>
                  <div class="roster-piket-guru-list">
                    @forelse($piket?->guru_list ?? [] as $gp)
                      <div class="roster-piket-guru-item" title="{{ $gp->nama }}">
                        <i class="bi bi-person-check-fill text-success" style="font-size:10.5px; flex-shrink:0;"></i>
                        <span>{{ $gp->nama }}</span>
                      </div>
                    @empty
                      <div style="font-style:italic; color:#94a3b8; font-size:10px;">Belum diatur</div>
                    @endforelse
                  </div>
                </div>
              </div>
            </td>
          </tr>

          {{-- Jam 1 s/d Max --}}
          @for($jam = 1; $jam <= $maxJam; $jam++)
            {{-- Istirahat Pertama --}}
            @if(($day !== 'JUMAT' && $jam == 4) || ($day === 'JUMAT' && $jam == 4))
              <tr class="roster-row-break">
                <td class="roster-cell-jam">-</td>
                <td class="roster-cell-pukul">
                  {{ $day === 'SENIN' ? '10.00 - 10.20' : ($day === 'JUMAT' ? '09.40 - 10.00' : '09.50 - 10.05') }}
                </td>
                <td colspan="6" class="roster-banner-istirahat roster-col-divider">
                  <i class="bi bi-cup-hot-fill me-1"></i> ISTIRAHAT PERTAMA
                </td>
              </tr>
            @endif

            {{-- Istirahat Kedua --}}
            @if($day !== 'JUMAT' && $jam == 8)
              <tr class="roster-row-break">
                <td class="roster-cell-jam">-</td>
                <td class="roster-cell-pukul">
                  {{ $day === 'SENIN' ? '12.40 - 13.10' : '12.25 - 12.55' }}
                </td>
                <td colspan="6" class="roster-banner-istirahat roster-col-divider">
                  <i class="bi bi-cup-hot-fill me-1"></i> ISTIRAHAT KEDUA
                </td>
              </tr>
            @endif

            @php
              $jamPukul = $slotsMatrix[$day][$jam][$rombelX_APHP?->id]->pukul 
                ?? ($jadwalWaktu[$day][$jam] ?? ($defaultScheduleTimes[$day][$jam] ?? ''));
            @endphp
            <tr>
              <td class="roster-cell-jam">{{ $jam }}</td>
              <td class="roster-cell-pukul">{{ $jamPukul }}</td>

              {{-- Render 6 Class Columns --}}
              @php
                $rombelList = [
                  ['r' => $rombelX_APHP, 'jurusan' => 'aphp', 'is_last' => false],
                  ['r' => $rombelX_RPL,  'jurusan' => 'rpl',  'is_last' => false],
                  ['r' => $rombelX_TSM,  'jurusan' => 'tsm',  'is_last' => true],
                  ['r' => $rombelXI_APHP,'jurusan' => 'aphp', 'is_last' => false],
                  ['r' => $rombelXI_RPL, 'jurusan' => 'rpl',  'is_last' => false],
                  ['r' => $rombelXI_TSM, 'jurusan' => 'tsm',  'is_last' => true],
                ];
              @endphp

              @foreach($rombelList as $item)
                @php
                  $rmb = $item['r'];
                  $slot = ($rmb) ? ($slotsMatrix[$day][$jam][$rmb->id] ?? null) : null;
                  $isLocked = (bool)($slot?->is_locked);
                  $slotId = $slot?->id ?? 0;
                  $jurusan = $item['jurusan'];
                  $isLast = $item['is_last'];
                @endphp
                <td class="roster-cell-slot slot-{{ $jurusan }} {{ $isLast ? 'roster-col-divider' : '' }} {{ $isLocked ? 'roster-cell-locked' : '' }} {{ !$canEditJadwal ? 'roster-cell-readonly' : '' }}"
                    @if($canEditJadwal)
                    onclick="openSlotModal('{{ $day }}', {{ $jam }}, {{ $rmb?->id ?? 0 }}, '{{ addslashes($rmb?->nama_rombel ?? '') }}', '{{ $slot?->guru_id ?? '' }}', '{{ $slot?->mata_pelajaran_id ?? '' }}', '{{ addslashes($slot?->kegiatan_khusus ?? '') }}', {{ $isLocked ? 'true' : 'false' }}, {{ $slotId }})"
                    title="{{ $slot ? ($slot->guru?->nama . ' - ' . ($slot->mataPelajaran?->nama_mapel ?? $slot->singkatan_mapel) . ($isLocked ? ' [🔒 DIKUNCI / KEEP]' : '')) : 'Klik untuk atur slot ini' }}"
                    @else
                    title="{{ $slot ? (($slot->guru?->nama ?? 'Guru') . ' - ' . ($slot->mataPelajaran?->nama_mapel ?? $slot->singkatan_mapel ?? $slot->kegiatan_khusus)) : 'Kosong' }}"
                    @endif
                    data-guru-id="{{ $slot?->guru_id ?? '' }}"
                    data-kode-guru="{{ $slot?->kode_guru ?? '' }}">
                  @if($slot && ($slot->guru_id || $slot->singkatan_mapel || $slot->kegiatan_khusus))
                    <div class="roster-slot-card">
                      @if($slot->kode_guru)
                        <span class="roster-guru-badge">{{ $slot->kode_guru }}</span>
                      @endif
                      <span class="roster-mapel-name">
                        {{ $slot->singkatan_mapel ?? $slot->kegiatan_khusus }}
                      </span>
                      @if($slot->resource_key === 'LAB_KOMPUTER')
                        <span class="roster-lab-badge badge-lab-komp" title="Praktik di Lab Komputer">LAB KOMP</span>
                      @elseif($slot->resource_key === 'LAB_APHP')
                        <span class="roster-lab-badge badge-lab-aphp" title="Praktik di Lab APHP">LAB APHP</span>
                      @elseif($slot->resource_key === 'BENGKEL_TSM')
                        <span class="roster-lab-badge badge-bengkel" title="Praktik di Bengkel TSM">BENGKEL TSM</span>
                      @elseif($slot->resource_key)
                        <span class="roster-lab-badge badge-custom" title="{{ $slot->resource_key }}">{{ $slot->resource_key }}</span>
                      @endif
                      @if($isLocked && $canEditJadwal)
                        <span class="roster-lock-badge" title="Slot ini dikunci (KEEP)">🔒</span>
                      @endif
                    </div>
                  @else
                    @if($canEditJadwal)
                      <div class="roster-empty-slot">
                        <i class="bi bi-plus"></i>
                        <span>Isi</span>
                      </div>
                    @else
                      <div class="roster-empty-slot" style="color:#cbd5e1; font-size:12px;">
                        <span>—</span>
                      </div>
                    @endif
                  @endif
                </td>
              @endforeach

              {{-- Kelas XII (PKL) spanning full day --}}
              @if($jam === 1)
                <td rowspan="{{ $pklRowSpan }}" colspan="2" class="roster-cell-pkl-unified">
                  <div class="roster-pkl-unified-box">
                    <span class="roster-pkl-title">PRAKTIK KERJA LAPANGAN</span>
                    <span class="roster-pkl-sub">(PKL)</span>
                  </div>
                </td>
              @endif
            </tr>
          @endfor
        @endforeach

        {{-- Sabtu --}}
        @if(!$hariFilter || $hariFilter === 'SABTU')
          <tr>
            <th class="roster-day-side" style="padding:10px;">
              <div class="roster-day-badge day-badge-sabtu" style="writing-mode:horizontal-tb; transform:none; padding:4px 8px;">
                SABTU
              </div>
            </th>
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

@if($canEditJadwal)
{{-- Tombol Ringkas Opsi Reset Jadwal --}}
<div style="display:flex; justify-content:flex-end; margin-top:8px; margin-bottom:16px;">
  <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#panelResetJadwal" style="font-size:11.5px; font-weight:700; border-radius:6px; display:inline-flex; align-items:center; gap:6px;">
    <i class="bi bi-trash3"></i> Opsi Hapus / Reset Jadwal
  </button>
</div>

<div class="collapse" id="panelResetJadwal" style="margin-bottom:20px;">
  <div class="akademik-card" style="border:1.5px solid #fee2e2;">
    <div class="akademik-card-header" style="background:linear-gradient(135deg,#fef2f2,#fff5f5); border-bottom:1px solid #fecaca; padding:10px 16px;">
      <h4 style="font-weight:800; font-size:13px; margin:0; color:#b91c1c; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-trash3-fill"></i> Hapus / Reset Jadwal
      </h4>
      <div style="font-size:11px; color:#dc2626; margin-top:2px;">Hapus slot jadwal berdasarkan hari atau kelas. Slot yang dikunci (🔒 KEEP) tidak akan terhapus.</div>
    </div>
    <div class="akademik-card-body" style="padding:12px 16px;">
      <form action="{{ route('akademik.jadwal.clear') }}" method="POST" onsubmit="return confirmHapus(this)">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <div style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
          <div>
            <label style="font-size:11px; font-weight:700; color:#64748b; display:block; margin-bottom:3px;">Filter Hari (Opsional)</label>
            <select name="hari" class="ak-select" style="min-width:130px; font-size:12px;">
              <option value="">Semua Hari</option>
              <option value="SENIN">SENIN</option>
              <option value="SELASA">SELASA</option>
              <option value="RABU">RABU</option>
              <option value="KAMIS">KAMIS</option>
              <option value="JUMAT">JUMAT</option>
            </select>
          </div>
          <div>
            <label style="font-size:11px; font-weight:700; color:#64748b; display:block; margin-bottom:3px;">Filter Kelas (Opsional)</label>
            <select name="rombel_id" class="ak-select" style="min-width:150px; font-size:12px;">
              <option value="">Semua Kelas</option>
              @foreach($rombels as $r)
                <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:11.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; color:#92400e; background:#fffbeb; border:1px solid #fde68a; padding:5px 10px; border-radius:6px;">
              <input type="checkbox" name="keep_locked" value="1" checked style="width:14px; height:14px;">
              Pertahankan Slot Terkunci 🔒
            </label>
          </div>
          <button type="submit" class="ak-btn" style="background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; font-weight:700; font-size:12px; border:none; padding:7px 16px;">
            <i class="bi bi-trash3 me-1"></i> Hapus Jadwal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

{{-- Panel Legenda Guru (Hirarki Struktural) & Legenda Mapel --}}
<div style="display:grid; grid-template-columns: 1.3fr 1fr; gap:16px; margin-bottom:24px;">
  {{-- Legenda Guru (Hirarki Struktural) --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center; padding:10px 14px;">
      <h4 style="font-weight:800; font-size:13px; margin:0; color:var(--ak-dark); display:flex; align-items:center; gap:6px;">
        <i class="bi bi-diagram-3-fill text-primary"></i> Daftar Kode Guru Sesuai Hirarki Struktural (1 - {{ $gurus->count() }})
      </h4>
      <span style="font-size:11px; color:#64748b; font-weight:600;">Otomatis Mengikuti Jabatan</span>
    </div>
    <div class="akademik-card-body" style="padding:0; max-height:360px; overflow-y:auto;">
      <table class="table table-sm table-hover mb-0" style="font-size:11.5px; vertical-align:middle;">
        <thead style="background:#f1f5f9; position:sticky; top:0; z-index:2;">
          <tr>
            <th style="width:48px; text-align:center;">Kode</th>
            <th>Nama Pendidik & Tenaga Kependidikan</th>
            <th style="width:200px;">Jabatan / Peran Struktural</th>
          </tr>
        </thead>
        <tbody>
          @foreach($gurus as $g)
            @php
              $roleBadgeStyle = match(true) {
                $g->kode_nomor === 1 => 'background:#fef3c7; color:#92400e; border:1px solid #fde68a;',
                $g->kode_nomor >= 2 && $g->kode_nomor <= 5 => 'background:#e0e7ff; color:#3730a3; border:1px solid #c7d2fe;',
                $g->kode_nomor >= 6 && $g->kode_nomor <= 8 => 'background:#dcfce7; color:#166534; border:1px solid #bbf7d0;',
                $g->kode_nomor >= 9 && $g->kode_nomor <= 13 => 'background:#ede9fe; color:#5b21b6; border:1px solid #ddd6fe;',
                $g->kode_nomor >= 14 && $g->kode_nomor <= 18 => 'background:#ffedd5; color:#9a3412; border:1px solid #fed7aa;',
                default => 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;',
              };
            @endphp
            <tr>
              <td style="text-align:center;">
                <span class="roster-guru-badge">{{ $g->kode_nomor ?? '-' }}</span>
              </td>
              <td style="font-weight:700; color:#1e293b;">
                {{ $g->nama }}
              </td>
              <td>
                <span style="display:inline-block; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:12px; white-space:nowrap; {{ $roleBadgeStyle }}">
                  {{ $g->peran_struktural }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Legenda Mata Pelajaran --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc; padding:10px 14px;">
      <h4 style="font-weight:800; font-size:13px; margin:0; color:var(--ak-dark); display:flex; align-items:center; gap:6px;">
        <i class="bi bi-book-half text-success"></i> Daftar Singkatan Mata Pelajaran
      </h4>
    </div>
    <div class="akademik-card-body" style="padding:12px; max-height:360px; overflow-y:auto;">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; font-size:11px;">
        @foreach($mapels as $m)
          <div style="display:flex; align-items:center; gap:6px; padding:5px 8px; background:#f8fafc; border-radius:6px; border:1px solid #e2e8f0;">
            <code style="font-weight:900; color:#0369a1; font-size:11px; flex-shrink:0;">{{ $m->singkatan_mapel ?? $m->kode_mapel }}</code>
            <span style="font-weight:600; color:#334155; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $m->nama_mapel }}">{{ $m->nama_mapel }}</span>
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
          <select name="rombel_id" id="form_rombel" class="ak-select" required onchange="onFormRombelChanged()">
            @foreach($rombels as $r)
              <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
        <div>
          <label class="ak-form-label">Mata Pelajaran (Dari SK Kurikulum) <span class="text-danger">*</span></label>
          <select name="mata_pelajaran_id" id="form_mapel" class="ak-select" required onchange="onFormMapelChanged()">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach($mapels as $m)
              <option value="{{ $m->id }}" data-resource="{{ $m->resource_key }}">
                {{ $m->singkatan_mapel ?? $m->kode_mapel }} - {{ $m->nama_mapel }}
                @if($m->resource_key) [{{ $m->resource_label }}] @endif
              </option>
            @endforeach
          </select>
          <div id="form_sk_info" style="display:none; font-size:11.5px; color:#15803d; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:5px 10px; margin-top:6px; font-weight:700;"></div>
        </div>
        <div>
          <label class="ak-form-label">Guru Pengampu (Otomatis dari SK) <span class="text-danger">*</span></label>
          <select name="guru_id" id="form_guru" class="ak-select" required onchange="checkLiveConflict()">
            <option value="">-- Pilih Guru --</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
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
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <div>
      <h3 style="font-weight:800; font-size:16px; margin:0; color:var(--ak-dark); display:flex; align-items:center; gap:8px;">
        <i class="bi bi-person-badge-fill text-success"></i> Penugasan Waka &amp; Guru Piket Mingguan
      </h3>
      <div style="font-size:12px; color:#64748b; margin-top:2px;">
        Atur jadwal petugas piket harian untuk menjaga kedisiplinan dan kelancaran KBM di SMKN 1 Air Naningan.
      </div>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
      <span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #86efac; font-size:11.5px; font-weight:700; padding:6px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
        <i class="bi bi-patch-check-fill text-success"></i> Terhubung &amp; Sinkron Otomatis ke Meja Piket SIRANI
      </span>
    </div>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    <table class="table table-bordered mb-0" style="font-size:12.5px;">
      <thead style="background:#f8fafc;">
        <tr>
          <th style="width:120px;">Hari</th>
          <th style="width:250px;">Waka Piket</th>
          <th>Daftar Guru Piket</th>
          @if($canEditJadwal || ($user && ($user->isWakaKesiswaan() || $user->hasAvailableRole('waka_kesiswaan'))))
          <th style="width:90px; text-align:center;">Aksi</th>
          @endif
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
            @if($canEditJadwal || ($user && ($user->isWakaKesiswaan() || $user->hasAvailableRole('waka_kesiswaan'))))
            <td style="text-align:center;">
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalPiket{{ $d }}" title="Ubah Petugas Piket">
                <i class="bi bi-pencil"></i> Ubah
              </button>
            </td>
            @endif
          </tr>

          @if($canEditJadwal || ($user && ($user->isWakaKesiswaan() || $user->hasAvailableRole('waka_kesiswaan'))))
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
          @endif
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Banner Semua Alur Kurikulum Selesai --}}
<div class="akademik-card" style="margin-top:24px; background:linear-gradient(135deg, #f8fafc, #f1f5f9); border:1px solid #cbd5e1; padding:20px 24px; border-radius:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
  <div>
    <div style="font-weight:900; font-size:16px; color:#1e293b; display:flex; align-items:center; gap:8px;">
      <i class="bi bi-patch-check-fill text-primary"></i> 4 Tahapan Alur Kurikulum Terintegrasi
    </div>
    <div style="font-size:13px; color:#475569; margin-top:4px; max-width:650px;">
      Struktur Mata Pelajaran, SK Pembagian Tugas Guru, Jadwal Roster KBM, dan Penugasan Guru Piket telah siap digunakan untuk operasional sekolah SMKN 1 Air Naningan.
    </div>
  </div>
  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'semester' => $semester]) }}" class="ak-btn ak-btn-secondary" style="font-size:13px; font-weight:700;">
      <i class="bi bi-arrow-left me-1"></i> Lihat Roster Jadwal
    </a>
    <a href="{{ route('akademik.dashboard') }}" class="ak-btn ak-btn-primary" style="font-size:13px; font-weight:700; padding:10px 22px;">
      <i class="bi bi-speedometer2 me-1"></i> Dashboard Akademik
    </a>
  </div>
</div>
@endif

{{-- ========================================================================= --}}
{{-- TAB 4: DISTRIBUSI MENGAJAR (BEBAN JJM GURU)                              --}}
{{-- ========================================================================= --}}
@if($tab === 'distribusi')
@php
  $subtab = request('subtab', 'matriks');
@endphp

{{-- SUBTAB 1: MATRIKS MAPEL X ROMBEL --}}
@if($subtab === 'matriks')
<style>
  .matrix-table-wrap {
    overflow-x: auto;
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
    max-height: calc(100vh - 270px);
    position: relative;
    -webkit-overflow-scrolling: touch;
  }
  .matrix-table {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    color: #000000;
  }
  .matrix-table th, .matrix-table td {
    border: 1px solid #e5e7eb;
    color: #000000;
  }
  .matrix-table tbody tr:hover td {
    background-color: #f9fafb !important;
  }
  .matrix-table tbody tr:hover td.matrix-sticky-col {
    background-color: #f3f4f6 !important;
  }
</style>

<div class="akademik-card" style="margin-bottom:24px;">
  <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #e5e7eb; padding:14px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <h3 style="font-weight:800; font-size:16px; margin:0; color:#000000;">
          Matriks Pemetaan Pembagian Tugas Mengajar
        </h3>
        <div style="font-size:12px; color:#4b5563; margin-top:2px;">
          Peta alokasi guru pengampu per mata pelajaran dan rombel.
        </div>
      </div>
      <div>
        <input type="text" id="filterMatrixMapel" onkeyup="filterMatrixRows()" placeholder="Cari mapel..."
               style="padding:5px 12px; font-size:12px; border-radius:6px; border:1px solid #d1d5db; background:#fff; width:180px; outline:none; color:#000000;">
      </div>
    </div>
  </div>

  <div class="akademik-card-body matrix-table-wrap" style="padding:0;">
    <table id="matrixTable" class="matrix-table">
      <thead>
        <tr style="position:sticky; top:0; z-index:20; background:#f9fafb; border-bottom:2px solid #e5e7eb;">
          <th style="width:40px; text-align:center; position:sticky; left:0; z-index:30; background:#f9fafb; padding:10px 6px; color:#000;">No</th>
          <th style="min-width:200px; position:sticky; left:40px; z-index:30; background:#f9fafb; padding:10px 14px; color:#000;">
            Mata Pelajaran
          </th>
          <th style="width:75px; text-align:center; background:#f9fafb; padding:10px 6px; color:#000;">Beban</th>
          <th style="width:100px; text-align:center; background:#f9fafb; padding:10px 6px; color:#000;">Ruang/Lab</th>
          @foreach($rombels as $rb)
            <th style="min-width:120px; text-align:center; background:#f9fafb; padding:10px 8px; color:#000; font-weight:700;">
              {{ $rb->nama_rombel }}
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php
          $no = 1;
          $totalJpPerRombel = [];
          foreach($rombels as $rb) { $totalJpPerRombel[$rb->id] = 0; }
        @endphp
        @foreach($mapels as $m)
          <tr>
            <td class="matrix-sticky-col" style="text-align:center; font-weight:600; color:#000; position:sticky; left:0; z-index:10; background:#fff; width:40px; padding:8px 4px;">
              {{ $no++ }}
            </td>
            <td class="mapel-name-cell matrix-sticky-col" style="position:sticky; left:40px; z-index:10; background:#fff; padding:8px 12px;">
              <div style="font-weight:700; color:#000; font-size:12.5px;">
                {{ $m->nama_mapel }}
                @if(!empty($m->tingkat_array))
                  <span style="font-weight:400; color:#4b5563; font-size:11px; margin-left:4px;">({{ implode(',', $m->tingkat_array) }})</span>
                @endif
              </div>
            </td>
            <td style="text-align:center; padding:8px 6px; background:#fff; color:#000; font-weight:600;">
              {{ $m->jumlah_jam_per_minggu }} JP
            </td>
            <td style="text-align:center; padding:8px 6px; background:#fff; color:#000;">
              {{ $m->resource_key ? $m->resource_label : 'Kelas' }}
            </td>

            @foreach($rombels as $rb)
              @php
                $dist = $matrixDistribusi[$m->id][$rb->id] ?? null;
                if ($dist) {
                  $totalJpPerRombel[$rb->id] += (int) $dist->total_jam_per_minggu;
                }
                // Cek apakah mapel ini sesuai tingkat rombel
                $isApplicable = in_array(strtoupper($rb->tingkat), $m->tingkat_array)
                  || ($rb->tingkat == '10' && in_array('X', $m->tingkat_array))
                  || ($rb->tingkat == '11' && in_array('XI', $m->tingkat_array))
                  || ($rb->tingkat == '12' && in_array('XII', $m->tingkat_array));
                
                // Cek kesesuaian jurusan untuk mapel kejuruan
                if ($m->jurusan_id && $rb->jurusan_id && $m->jurusan_id != $rb->jurusan_id) {
                  $isApplicable = false;
                }
              @endphp

              <td style="vertical-align:middle; padding:8px 10px; background:#fff;">
                @if($dist)
                  <div style="display:flex; align-items:center; justify-content:space-between; gap:6px;">
                    <span style="font-weight:700; color:#000; font-size:12px; line-height:1.2;">
                      {{ $dist->guru?->nama ?? '-' }}
                    </span>
                    <form action="{{ route('akademik.jadwal.destroy', $dist->id) }}" method="POST" onsubmit="return confirm('Hapus alokasi {{ $m->nama_mapel }} untuk {{ $rb->nama_rombel }}?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-link p-0 text-dark" style="font-size:12px; color:#000; opacity:0.4; text-decoration:none;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.4'" title="Hapus Alokasi">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </form>
                  </div>
                @elseif($isApplicable)
                  <div style="text-align:center;">
                    <button type="button"
                            onclick="openQuickAssign({{ $m->id }}, '{{ addslashes($m->nama_mapel) }}', {{ $rb->id }}, '{{ addslashes($rb->nama_rombel) }}', {{ $m->jumlah_jam_per_minggu }})"
                            style="background:none; border:none; color:#000; font-size:11.5px; font-weight:600; cursor:pointer; padding:2px 4px; text-decoration:underline;">
                      + Tugaskan
                    </button>
                  </div>
                @else
                  <div style="text-align:center; color:#9ca3af; font-size:13px;">—</div>
                @endif
              </td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background:#f9fafb; font-weight:700; border-top:2px solid #e5e7eb;">
          <td colspan="4" style="text-align:right; padding:10px 14px; font-size:12px; position:sticky; left:0; background:#f9fafb; z-index:15; color:#000;">
            TOTAL JAM :
          </td>
          @foreach($rombels as $rb)
            @php $totalJam = $totalJpPerRombel[$rb->id] ?? 0; @endphp
            <td style="text-align:center; padding:10px 6px; font-size:12px; color:#000; font-weight:700;">
              {{ $totalJam }} JP
            </td>
          @endforeach
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endif

{{-- SUBTAB 2: REKAP BEBAN GURU (PERMENDIKBUD 15/2018 & DAPODIK) --}}
@if($subtab === 'beban')
{{-- KPI Summary Cards --}}
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:20px;">
  <div class="akademik-card" style="padding:16px; border-left:4px solid #3b82f6;">
    <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Total Guru Aktif</div>
    <div style="font-size:24px; font-weight:900; color:var(--ak-dark); margin-top:4px;">{{ $rekapBebanGuru->count() }} Guru</div>
    <div style="font-size:11px; color:#3b82f6; margin-top:2px;">SMK Negeri 1 Air Naningan</div>
  </div>
  <div class="akademik-card" style="padding:16px; border-left:4px solid #10b981;">
    <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Memenuhi Beban (24–40 JP)</div>
    <div style="font-size:24px; font-weight:900; color:#059669; margin-top:4px;">
      {{ $rekapBebanGuru->where('status', 'memenuhi')->count() }} Guru
    </div>
    <div style="font-size:11px; color:#10b981; margin-top:2px;">Syarat Sertifikasi / Info GTK Terpenuhi ✅</div>
  </div>
  <div class="akademik-card" style="padding:16px; border-left:4px solid #f59e0b;">
    <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Kurang Jam (&lt; 24 JP)</div>
    <div style="font-size:24px; font-weight:900; color:#d97706; margin-top:4px;">
      {{ $rekapBebanGuru->where('status', 'kurang')->count() }} Guru
    </div>
    <div style="font-size:11px; color:#f59e0b; margin-top:2px;">Butuh penambahan jam / tugas tambahan</div>
  </div>
  <div class="akademik-card" style="padding:16px; border-left:4px solid #8b5cf6;">
    <div style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Total Jam Pelajaran Terbagi</div>
    <div style="font-size:24px; font-weight:900; color:#6d28d9; margin-top:4px;">
      {{ $rekapBebanGuru->sum('jtm_murni') }} JP
    </div>
    <div style="font-size:11px; color:#8b5cf6; margin-top:2px;">Jam Tatap Muka Murni (JTM)</div>
  </div>
</div>

<div class="akademik-card">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <div>
      <h3 style="font-weight:800; font-size:15px; margin:0; color:var(--ak-dark);">
        <i class="bi bi-award-fill text-primary me-1"></i> Rekapitulasi Pemenuhan Beban Kerja Guru &amp; Tugas Tambahan
      </h3>
      <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
        Berdasarkan Permendikbud Nomor 15 Tahun 2018. Standar beban kerja guru: <b>24 s/d 40 JP Tatap Muka Ekuivalen</b> per minggu.
      </div>
    </div>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    <div class="akademik-table-wrap">
      <table class="akademik-table">
        <thead>
          <tr>
            <th style="width:40px; text-align:center;">No</th>
            <th style="width:50px; text-align:center;">Kode</th>
            <th>Nama Guru &amp; Identitas</th>
            <th>Mata Pelajaran yang Diampu</th>
            <th style="width:70px; text-align:center;">JTM Murni</th>
            <th>Tugas Tambahan (Ekuivalensi Jam)</th>
            <th style="width:80px; text-align:center;">Total Eqv</th>
            <th style="width:130px; text-align:center;">Status Beban</th>
            @if($canEditJadwal)
            <th style="width:110px; text-align:center;">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach($rekapBebanGuru as $idx => $bg)
            <tr>
              <td style="text-align:center; font-weight:700;">{{ $idx + 1 }}</td>
              <td style="text-align:center;">
                <span class="roster-guru-badge">{{ $bg->guru->kode_nomor ?? '-' }}</span>
              </td>
              <td>
                <div style="font-weight:800; color:var(--ak-dark);">{{ $bg->guru->nama }}</div>
                <div style="font-size:11px; color:#64748b;">
                  NIP: {{ $bg->guru->nip ?? 'Non-NIP' }} · {{ $bg->guru->golongan_pangkat ?? 'GTT' }}
                </div>
              </td>
              <td>
                @if($bg->mapels->isEmpty())
                  <span style="font-size:11.5px; color:#94a3b8; font-style:italic;">Belum ada mapel diampu</span>
                @else
                  <div style="font-size:12px; font-weight:600; color:var(--ak-primary);">
                    {{ $bg->mapels->implode(', ') }}
                  </div>
                  <div style="font-size:10.5px; color:#64748b;">{{ $bg->total_rombel }} Rombel Diajar</div>
                @endif
              </td>
              <td style="text-align:center; font-weight:800; font-size:13px;">
                {{ $bg->jtm_murni }} JP
              </td>
              <td>
                @if(empty($bg->tugas_tambahan))
                  <span style="font-size:11.5px; color:#94a3b8; font-style:italic;">-</span>
                @else
                  <div style="display:flex; flex-direction:column; gap:2px;">
                    @foreach($bg->tugas_tambahan as $tt)
                      <span style="font-size:11.5px; font-weight:600; color:var(--ak-dark);">
                        • {{ $tt['nama'] }} <b class="text-primary">(+{{ $tt['jp'] }} JP)</b>
                      </span>
                    @endforeach
                  </div>
                @endif
              </td>
              <td style="text-align:center; font-weight:900; font-size:14px; background:#f8fafc; color:var(--ak-dark);">
                {{ $bg->total_ekuivalen }} JP
              </td>
              <td style="text-align:center;">
                @if($bg->status === 'memenuhi')
                  <span class="ak-badge ak-badge-success" style="font-size:11px; font-weight:800; padding:4px 8px;">
                    <i class="bi bi-check-circle me-1"></i> MEMENUHI
                  </span>
                @elseif($bg->status === 'lebih')
                  <span class="ak-badge ak-badge-danger" style="font-size:11px; font-weight:800; padding:4px 8px;">
                    <i class="bi bi-exclamation-octagon me-1"></i> OVERLOAD ({{ $bg->total_ekuivalen }} JP)
                  </span>
                @else
                  <span class="ak-badge ak-badge-warning" style="font-size:11px; font-weight:800; padding:4px 8px;">
                    <i class="bi bi-exclamation-triangle me-1"></i> KURANG ({{ 24 - $bg->total_ekuivalen }} JP)
                  </span>
                @endif
              </td>
              @if($canEditJadwal)
              <td style="text-align:center;">
                <button type="button" class="btn btn-sm btn-outline-primary"
                  onclick="openModalTugasTambahan({{ $bg->guru->id }}, '{{ addslashes($bg->guru->nama) }}', '{{ addslashes($bg->guru->tugas_tambahan ?? '') }}', '{{ addslashes($bg->guru->sk_tugas_tambahan ?? '') }}')"
                  style="font-size:11px; padding:3px 8px; border-radius:6px; font-weight:700; display:inline-flex; align-items:center; gap:4px;"
                  title="Atur Tugas Tambahan & Ekuivalensi JP">
                  <i class="bi bi-pencil-square"></i> Atur Tugas
                </button>
              </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- SUBTAB 3: DAFTAR RINCI ALOKASI (EXISTING LIST) --}}
@if($subtab === 'daftar')
<div class="akademik-card">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
    <h3 style="font-weight:800; font-size:15px; margin:0; color:var(--ak-dark);">
      Daftar Alokasi Pengampu &amp; Mata Pelajaran (Rincian)
    </h3>
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
            @if($canEditJadwal)
            <th style="width:90px; text-align:center;">Aksi</th>
            @endif
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
              @if($canEditJadwal)
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
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $canEditJadwal ? 8 : 7 }}" style="text-align:center; padding:30px; color:#64748b;">
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
@endif

@if($canEditJadwal)
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
              <select name="rombel_id" id="m_rombel" class="ak-select" required onchange="onModalRombelChanged()">
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Mata Pelajaran (Dari SK) <span class="text-danger">*</span></label>
              <select name="mata_pelajaran_id" id="m_mapel" class="ak-select" required onchange="onModalMapelChanged()">
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapels as $m)
                  <option value="{{ $m->id }}" data-resource="{{ $m->resource_key }}">
                    {{ $m->singkatan_mapel ?? $m->kode_mapel }} - {{ $m->nama_mapel }}
                    @if($m->resource_key) [{{ $m->resource_label }}] @endif
                  </option>
                @endforeach
              </select>
              <div id="m_sk_info" style="display:none; font-size:11.5px; color:#15803d; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:4px 8px; margin-top:4px; font-weight:700;"></div>
            </div>
            <div>
              <label class="ak-form-label">Guru Pengampu <span class="text-danger">*</span></label>
              <select name="guru_id" id="m_guru" class="ak-select" required onchange="checkModalConflict()">
                <option value="">-- Pilih Guru --</option>
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
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
            <select name="guru_id" id="slot_guru_id" class="ak-select" onchange="checkSlotConflict()">
              <option value="">-- Tanpa Guru / Kosongkan --</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}">[{{ $g->kode_nomor ?? '-' }}] {{ $g->nama }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" id="slot_mapel_id" class="ak-select" onchange="checkSlotConflict()">
              <option value="">-- Tanpa Mapel / Kosongkan --</option>
              @foreach($mapels as $m)
                <option value="{{ $m->id }}">
                  {{ $m->singkatan_mapel ?? $m->kode_mapel }} - {{ $m->nama_mapel }}
                  @if($m->resource_key) [{{ $m->resource_label }}] @endif
                </option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Kegiatan Khusus (Opsional)</label>
            <input type="text" name="kegiatan_khusus" id="slot_kegiatan" class="ak-input" placeholder="Misal: UPACARA, APEL, TEFA, PKL...">
          </div>

          {{-- Slot Conflict Alert Container --}}
          <div id="slotConflictAlert" style="display:none; margin-bottom:14px; padding:10px 14px; background:#fee2e2; border:1.5px solid #f87171; border-radius:8px; color:#b91c1c; font-size:12.5px; font-weight:700;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <span id="slotConflictMessage"></span>
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

{{-- MODAL: QUICK ASSIGN GURU DARI MATRIKS MAPEL X ROMBEL --}}
<div class="modal fade" id="modalQuickAssignDistribusi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.jadwal.store') }}" method="POST" onsubmit="return validateQaGuruForm();">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="hidden" name="mata_pelajaran_id" id="qa_mapel_id">
        <input type="hidden" name="rombel_ids[]" id="qa_rombel_id">
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">
            <i class="bi bi-person-plus-fill text-primary me-1"></i> Tugaskan Guru Pengampu
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="background:#f1f5f9; padding:10px 14px; border-radius:8px; margin-bottom:14px; border:1px solid #cbd5e1;">
            <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Mata Pelajaran:</div>
            <div id="qa_mapel_name" style="font-weight:800; font-size:13.5px; color:var(--ak-dark); margin-top:1px;"></div>
            <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700; margin-top:6px;">Rombel / Kelas Sasaran:</div>
            <div id="qa_rombel_name" style="font-weight:800; font-size:13.5px; color:var(--ak-primary); margin-top:1px;"></div>
          </div>

          {{-- Pilih Guru Pengampu (Live Search Dropdown) --}}
          <div style="margin-bottom:14px; position:relative;">
            <label class="ak-form-label">Pilih Guru Pengampu <span class="text-danger">*</span></label>
            <input type="hidden" name="guru_id" id="qa_guru_id" required>

            <div style="position:relative;" id="qaGuruSearchWrap">
              <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px; pointer-events:none;"></i>
              <input 
                type="text" 
                id="qaGuruSearchInput" 
                placeholder="Ketik untuk mencari nama guru, kode, atau NIP..." 
                autocomplete="off"
                onfocus="showQaGuruDropdown()"
                oninput="filterQaGuruSearch(this.value)"
                class="ak-input"
                style="padding-left:36px; padding-right:32px; font-weight:600; font-size:13px; height:42px; border-radius:8px;" 
              />
              <button 
                type="button" 
                id="qaGuruClearBtn" 
                onclick="clearQaGuruSelection()" 
                style="display:none; position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94a3b8; cursor:pointer; padding:4px 6px; font-size:15px;"
                title="Hapus pilihan"
              >
                <i class="bi bi-x-circle-fill"></i>
              </button>

              {{-- Live Search Dropdown Panel --}}
              <div id="qaGuruDropdownMenu" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#ffffff; border:1px solid #cbd5e1; border-radius:10px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.12), 0 8px 10px -6px rgba(0,0,0,0.08); z-index:1065; max-height:220px; overflow-y:auto;">
                <div id="qaGuruItemsContainer">
                  @foreach($gurus as $g)
                    <div 
                      class="qa-guru-item" 
                      data-id="{{ $g->id }}"
                      data-name="{{ $g->nama }}"
                      data-kode="{{ $g->kode_nomor ?? '' }}"
                      data-nip="{{ $g->nip ?? '' }}"
                      onclick="selectQaGuru('{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ $g->kode_nomor ?? '-' }}')"
                      style="padding:10px 14px; cursor:pointer; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; transition:background 0.15s;"
                      onmouseover="this.style.background='#f8faff'"
                      onmouseout="this.style.background='#ffffff'"
                    >
                      <div>
                        <div style="font-weight:700; font-size:13px; color:#1e293b;">
                          {{ $g->nama }}
                        </div>
                        <div style="font-size:11px; color:#64748b; margin-top:1px;">
                          NIP: {{ $g->nip ?: 'Non-NIP' }}
                        </div>
                      </div>
                      <span style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:11px; font-weight:800; padding:2px 8px; border-radius:6px; font-family:monospace;">
                        #{{ $g->kode_nomor ?? '-' }}
                      </span>
                    </div>
                  @endforeach
                </div>
                <div id="qaGuruEmptyMsg" style="display:none; padding:16px; text-align:center; color:#94a3b8; font-size:12px;">
                  <i class="bi bi-search me-1"></i> Guru tidak ditemukan
                </div>
              </div>
            </div>
          </div>

          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Beban Jam per Minggu (JP) <span class="text-danger">*</span></label>
            <input type="number" name="total_jam_per_minggu" id="qa_total_jam" class="ak-input" min="1" max="20" required>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Catatan Tambahan (Opsional)</label>
            <input type="text" name="catatan" class="ak-input" placeholder="Misal: Teori di Kelas, Praktik di Lab Komputer...">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Alokasi SK</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL: ATUR TUGAS TAMBAHAN & EKUIVALENSI BEBAN GURU --}}
<div class="modal fade" id="modalTugasTambahan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.jadwal.update-tugas-tambahan') }}" method="POST">
        @csrf
        <input type="hidden" name="guru_id" id="tt_guru_id">
        <div class="modal-header" style="background:linear-gradient(135deg, #f8fafc, #edf2f7); border-bottom:1px solid #e2e8f0;">
          <h5 class="modal-title" style="font-weight:800; font-size:16px; color:var(--ak-dark); margin:0;">
            Atur Tugas Tambahan &amp; Ekuivalensi Jam (Permendikbud 15/2018)
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" style="padding:20px;">
          <div style="background:#f0fdf4; border:1.5px solid #bbf7d0; padding:12px 16px; border-radius:10px; margin-bottom:16px;">
            <div style="font-size:11px; font-weight:800; color:#15803d; text-transform:uppercase;">Guru Pendidik:</div>
            <div id="tt_guru_nama" style="font-size:16px; font-weight:900; color:#14532d; margin-top:2px;"></div>
            <div style="font-size:11.5px; color:#166534; margin-top:4px;">
              Tugas tambahan dihitung ekuivalensi jamnya secara otomatis untuk mencukupi beban <b>24–40 JP/minggu</b>.
            </div>
          </div>

          {{-- Pilihan Tugas Tambahan & Tombol Buka Master CRUD --}}
          <div style="margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <label class="ak-form-label" style="font-size:12px; font-weight:800; color:#334155; margin:0;">
                Pilihan Tugas Tambahan (Klik untuk Pilih / Batalkan):
              </label>
              <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="toggleMasterCrudDrawer()" style="font-size:11.5px; font-weight:800; color:var(--ak-primary);">
                <span id="btnTextMasterDrawer">Kelola Master &amp; Jam Dinamis &darr;</span>
              </button>
            </div>

            <div id="tt_preset_container" style="display:flex; flex-wrap:wrap; gap:6px;">
              @foreach($masterTugasTambahan as $mt)
                <span class="tt-preset-chip" data-id="{{ $mt->id }}" data-task="{{ $mt->nama_tugas }}" data-jp="{{ $mt->ekuivalensi_jam }}" onclick="toggleTugasChip('{{ addslashes($mt->nama_tugas) }}')">
                  {{ $mt->nama_tugas }} (+{{ $mt->ekuivalensi_jam }} JP)
                </span>
              @endforeach
            </div>
          </div>

          {{-- DRAWER CRUD MASTER TUGAS TAMBAHAN (Dapat Tambah, Edit Jam, & Hapus Dinamis) --}}
          <div id="masterCrudDrawer" style="display:none; margin-bottom:16px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:14px;">
            <div style="font-size:12px; font-weight:800; color:#0f172a; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
              <span>Kelola Master Tugas &amp; Ekuivalensi Jam Dinamis</span>
              <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" onclick="toggleMasterCrudDrawer()" style="font-size:11px;">Tutup</button>
            </div>

            {{-- Form Tambah Tugas Baru ke Master --}}
            <div style="display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
              <input type="text" id="new_master_nama" class="ak-input" placeholder="Nama Tugas Tambahan Baru..." style="flex:1; min-width:180px; height:34px; font-size:12px;">
              <div style="display:flex; align-items:center; gap:4px;">
                <input type="number" id="new_master_jp" class="ak-input" value="2" min="1" max="40" style="width:65px; height:34px; font-size:12px; text-align:center;" title="Ekuivalensi Jam (JP)">
                <span style="font-size:11.5px; font-weight:700; color:#64748b;">JP</span>
              </div>
              <button type="button" onclick="submitAddMasterTugas()" class="ak-btn ak-btn-primary" style="height:34px; padding:0 12px; font-size:12px; font-weight:700;">
                + Tambah ke Master
              </button>
            </div>

            {{-- Tabel Daftar Master Tugas yang Ada --}}
            <div style="max-height:160px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:8px; background:#ffffff;">
              <table style="width:100%; font-size:11.5px; border-collapse:collapse;">
                <tbody id="master_tugas_tbody">
                  @foreach($masterTugasTambahan as $mt)
                    <tr id="row_mt_{{ $mt->id }}" style="border-bottom:1px solid #f1f5f9;">
                      <td style="padding:6px 10px; font-weight:700; color:#334155;">{{ $mt->nama_tugas }}</td>
                      <td style="padding:6px 10px; width:75px; text-align:center;">
                        <span class="ak-badge ak-badge-info" style="font-size:10px; font-weight:800;">+{{ $mt->ekuivalensi_jam }} JP</span>
                      </td>
                      <td style="padding:6px 10px; width:95px; text-align:right;">
                        <button type="button" class="btn btn-sm btn-link p-0 text-primary me-2" onclick="editMasterPrompt({{ $mt->id }}, '{{ addslashes($mt->nama_tugas) }}', {{ $mt->ekuivalensi_jam }})" style="font-size:11px; font-weight:700;">Ubah</button>
                        <button type="button" class="btn btn-sm btn-link p-0 text-danger" onclick="deleteMasterTugas({{ $mt->id }}, '{{ addslashes($mt->nama_tugas) }}')" style="font-size:11px; font-weight:700;">Hapus</button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div style="font-size:10.5px; color:#64748b; margin-top:5px;">
              Perubahan master akan langsung otomatis memperbarui ekuivalensi jam pada kartu pilihan dan perhitungan SK.
            </div>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Daftar Tugas Tambahan Guru Ini (Pisahkan dengan koma jika lebih dari satu)</label>
            <input type="text" name="tugas_tambahan" id="tt_tugas_tambahan" class="ak-input" style="font-weight:600; font-size:13px;" placeholder="Pilih dari daftar di atas atau ketik langsung tugas spesifik" oninput="updateTugasChipsActiveState()">
            <div style="font-size:11px; color:#64748b; margin-top:3px;">
              Klik pilihan di atas atau ketik manual. Anda juga dapat menentukan jam kustom dengan mengetik format <i>Nama Tugas (+X JP)</i>.
            </div>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Nomor SK Penugasan / SK Kepala Sekolah (Opsional)</label>
            <input type="text" name="sk_tugas_tambahan" id="tt_sk_tugas_tambahan" class="ak-input" placeholder="Contoh: 800/015/SMK.01/2026">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">
            Simpan Tugas Tambahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<style>
.tt-preset-chip {
  cursor: pointer;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  background: #f8fafc;
  color: #334155;
  border: 1px solid #cbd5e1;
  user-select: none;
  transition: all 0.15s ease;
  display: inline-flex;
  align-items: center;
}
.tt-preset-chip:hover {
  background: #e0e7ff;
  border-color: #6366f1;
  color: #4338ca;
}
.tt-preset-chip.active {
  background: #4f46e5 !important;
  color: #ffffff !important;
  border-color: #4338ca !important;
}
</style>

<script>
let currentActiveSlotId = 0;

function highlightGuruSchedule(guruId) {
  const cells = document.querySelectorAll('.roster-cell-slot');
  if (!guruId) {
    cells.forEach(c => {
      c.style.opacity = '1';
      c.style.outline = 'none';
      c.style.boxShadow = 'none';
    });
    return;
  }

  cells.forEach(c => {
    const cGuru = c.getAttribute('data-guru-id');
    if (cGuru === String(guruId)) {
      c.style.opacity = '1';
      c.style.outline = '2.5px solid #2563eb';
      c.style.outlineOffset = '-2px';
      c.style.boxShadow = '0 4px 12px rgba(37,99,235,0.35)';
      c.style.zIndex = '3';
    } else {
      c.style.opacity = '0.35';
      c.style.outline = 'none';
      c.style.boxShadow = 'none';
      c.style.zIndex = '1';
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const filterGuru = document.getElementById('filterHighlightGuru');
  if (filterGuru && filterGuru.value) {
    highlightGuruSchedule(filterGuru.value);
  }
});

function openSlotModal(hari, jam, rombelId, rombelName, guruId, mapelId, kegiatan, isLocked, slotId) {
  @if(!$canEditJadwal)
    return;
  @endif

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

  // Cek potensi bentrok awal
  checkSlotConflict();
}

function toggleLockCurrentSlot() {
  @if(!$canEditJadwal)
    return;
  @endif

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
  @if(!$canEditJadwal)
    return;
  @endif

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
  const mapel = document.getElementById('form_mapel')?.value;
  const hari = document.getElementById('form_hari')?.value;
  const rombel = document.getElementById('form_rombel')?.value;
  const jamMulai = document.getElementById('form_jam_mulai')?.value;
  const jamSelesai = document.getElementById('form_jam_selesai')?.value;
  const alertBox = document.getElementById('conflictAlert');
  const alertMsg = document.getElementById('conflictMessage');

  if ((!guru && !mapel) || !hari || !rombel || !alertBox) {
    if (alertBox) alertBox.style.display = 'none';
    return;
  }

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
      mata_pelajaran_id: mapel,
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
  const mapel = document.getElementById('m_mapel')?.value;
  const hari = document.getElementById('m_hari')?.value;
  const rombel = document.getElementById('m_rombel')?.value;
  const jamMulai = document.getElementById('m_jam_mulai')?.value;
  const jamSelesai = document.getElementById('m_jam_selesai')?.value;
  const alertBox = document.getElementById('modalConflictAlert');
  const alertMsg = document.getElementById('modalConflictMessage');

  if ((!guru && !mapel) || !hari || !rombel || !alertBox) {
    if (alertBox) alertBox.style.display = 'none';
    return;
  }

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
      mata_pelajaran_id: mapel,
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

function checkSlotConflict() {
  const guru = document.getElementById('slot_guru_id')?.value;
  const mapel = document.getElementById('slot_mapel_id')?.value;
  const hari = document.getElementById('slot_hari')?.value;
  const rombel = document.getElementById('slot_rombel_id')?.value;
  const jam = document.getElementById('slot_jam')?.value;
  const alertBox = document.getElementById('slotConflictAlert');
  const alertMsg = document.getElementById('slotConflictMessage');

  if ((!guru && !mapel) || !hari || !rombel || !alertBox) {
    if (alertBox) alertBox.style.display = 'none';
    return;
  }

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
      mata_pelajaran_id: mapel,
      hari: hari,
      rombel_id: rombel,
      jam_mulai: jam,
      jam_selesai: jam
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
    if (alertBox) alertBox.style.display = 'none';
  });
}

// Quick Assign Modal Handler (Matriks Mapel x Rombel)
function openQuickAssign(mapelId, mapelName, rombelId, rombelName, defaultJp) {
  document.getElementById('qa_mapel_id').value = mapelId;
  document.getElementById('qa_rombel_id').value = rombelId;
  document.getElementById('qa_mapel_name').innerText = mapelName;
  document.getElementById('qa_rombel_name').innerText = rombelName;
  document.getElementById('qa_total_jam').value = defaultJp || 2;

  clearQaGuruSelection();

  const modalEl = document.getElementById('modalQuickAssignDistribusi');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();

  setTimeout(() => {
    document.getElementById('qaGuruSearchInput')?.focus();
  }, 400);
}

// ── Search Guru Pengampu Quick Assign Handler ──
function showQaGuruDropdown() {
  const dd = document.getElementById('qaGuruDropdownMenu');
  if (dd) dd.style.display = 'block';
}

function filterQaGuruSearch(query) {
  const q = (query || '').toLowerCase().trim();
  const dd = document.getElementById('qaGuruDropdownMenu');
  if (dd) dd.style.display = 'block';

  const items = document.querySelectorAll('.qa-guru-item');
  let visibleCount = 0;
  items.forEach(el => {
    const name = (el.dataset.name || '').toLowerCase();
    const kode = (el.dataset.kode || '').toLowerCase();
    const nip = (el.dataset.nip || '').toLowerCase();
    if (!q || name.includes(q) || kode.includes(q) || nip.includes(q)) {
      el.style.display = 'flex';
      visibleCount++;
    } else {
      el.style.display = 'none';
    }
  });

  const emptyMsg = document.getElementById('qaGuruEmptyMsg');
  if (emptyMsg) {
    emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
  }

  const clearBtn = document.getElementById('qaGuruClearBtn');
  if (clearBtn) {
    clearBtn.style.display = query ? 'block' : 'none';
  }

  // Jika teks diubah manual dan bukan format terplih, reset id
  const hiddenId = document.getElementById('qa_guru_id');
  if (hiddenId && hiddenId.value && !query.includes('[')) {
    hiddenId.value = '';
    const input = document.getElementById('qaGuruSearchInput');
    if (input) {
      input.style.borderColor = '';
      input.style.background = '';
    }
  }
}

function selectQaGuru(id, nama, kode) {
  document.getElementById('qa_guru_id').value = id;
  const input = document.getElementById('qaGuruSearchInput');
  if (input) {
    input.value = `[#${kode}] ${nama}`;
    input.style.borderColor = '#10b981';
    input.style.background = '#f0fdf4';
  }
  const clearBtn = document.getElementById('qaGuruClearBtn');
  if (clearBtn) clearBtn.style.display = 'block';

  const dd = document.getElementById('qaGuruDropdownMenu');
  if (dd) dd.style.display = 'none';
}

function clearQaGuruSelection() {
  const hiddenId = document.getElementById('qa_guru_id');
  if (hiddenId) hiddenId.value = '';

  const input = document.getElementById('qaGuruSearchInput');
  if (input) {
    input.value = '';
    input.style.borderColor = '';
    input.style.background = '';
    input.focus();
  }
  const clearBtn = document.getElementById('qaGuruClearBtn');
  if (clearBtn) clearBtn.style.display = 'none';

  filterQaGuruSearch('');
}

function validateQaGuruForm() {
  const gId = document.getElementById('qa_guru_id')?.value;
  if (!gId) {
    alert('⚠️ Silakan cari dan pilih guru pengampu dari daftar hasil pencarian.');
    document.getElementById('qaGuruSearchInput')?.focus();
    showQaGuruDropdown();
    return false;
  }
  return true;
}

// Tutup dropdown saat klik di luar area input search
document.addEventListener('click', function(e) {
  const wrap = document.getElementById('qaGuruSearchWrap');
  const dd = document.getElementById('qaGuruDropdownMenu');
  if (wrap && !wrap.contains(e.target) && dd) {
    dd.style.display = 'none';
  }
});

// Live Search Filter untuk Matriks Mapel x Rombel
function filterMatrixRows() {
  const q = (document.getElementById('filterMatrixMapel')?.value || '').toLowerCase().trim();
  const rows = document.querySelectorAll('#matrixTable tbody tr');
  rows.forEach(tr => {
    const text = tr.querySelector('.mapel-name-cell')?.innerText?.toLowerCase() || '';
    tr.style.display = text.includes(q) ? '' : 'none';
  });
}

// Dynamic Auto-fill Rombel & SK Mapel Handler (Form Blok Cepat)
let formRombelCache = {};
function onFormRombelChanged() {
  const rombelId = document.getElementById('form_rombel')?.value;
  if (!rombelId) return;

  fetch(`/dcc/akademik/jadwal/rombel-alokasi/${rombelId}?semester={{ $semester }}&tahun_ajaran_id={{ $ta?->id ?? 1 }}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.alokasi && data.alokasi.length > 0) {
        formRombelCache = {};
        const mapelSel = document.getElementById('form_mapel');
        if (mapelSel) {
          mapelSel.innerHTML = '<option value="">-- Pilih Mata Pelajaran (Dari SK) --</option>';
          data.alokasi.forEach(item => {
            formRombelCache[item.mata_pelajaran_id] = item;
            const opt = document.createElement('option');
            opt.value = item.mata_pelajaran_id;
            let statusBadge = item.is_lengkap ? ' [✅ Selesai]' : ` [Sisa ${item.sisa_jam} JP]`;
            if (item.resource_key) statusBadge += ` [${item.resource_label}]`;
            opt.textContent = `${item.singkatan_mapel} - ${item.nama_mapel}${statusBadge}`;
            mapelSel.appendChild(opt);
          });
        }
      }
    })
    .catch(() => {});

  checkLiveConflict();
}

function onFormMapelChanged() {
  const mapelId = document.getElementById('form_mapel')?.value;
  const item = formRombelCache[mapelId];
  const infoBox = document.getElementById('form_sk_info');

  if (item) {
    // Otomatis pilih Guru pengampu dari SK
    const guruSel = document.getElementById('form_guru');
    if (guruSel && item.guru_id) {
      guruSel.value = item.guru_id;
    }

    // Tampilkan status alokasi
    if (infoBox) {
      infoBox.style.display = 'block';
      infoBox.innerHTML = `📋 <b>SK Wakakur:</b> Diampu oleh <u>${item.nama_guru}</u> (${item.jam_terjadwal}/${item.total_jam} JP terjadwal · Sisa <b>${item.sisa_jam} JP</b>)`;
    }

    // Otomatis hitung jam selesai jika sisa jam > 0
    const jamMulai = parseInt(document.getElementById('form_jam_mulai')?.value || '1');
    const jamSelesaiSel = document.getElementById('form_jam_selesai');
    if (jamSelesaiSel && item.sisa_jam > 0) {
      const blockSize = Math.min(item.sisa_jam, 4);
      jamSelesaiSel.value = Math.min(11, jamMulai + blockSize - 1);
    }
  } else {
    if (infoBox) infoBox.style.display = 'none';
  }

  checkLiveConflict();
}

// Modal Blok Jam Dynamic Handlers
let modalRombelCache = {};
function onModalRombelChanged() {
  const rombelId = document.getElementById('m_rombel')?.value;
  if (!rombelId) return;

  fetch(`/dcc/akademik/jadwal/rombel-alokasi/${rombelId}?semester={{ $semester }}&tahun_ajaran_id={{ $ta?->id ?? 1 }}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.alokasi && data.alokasi.length > 0) {
        modalRombelCache = {};
        const mapelSel = document.getElementById('m_mapel');
        if (mapelSel) {
          mapelSel.innerHTML = '<option value="">-- Pilih Mapel (Dari SK) --</option>';
          data.alokasi.forEach(item => {
            modalRombelCache[item.mata_pelajaran_id] = item;
            const opt = document.createElement('option');
            opt.value = item.mata_pelajaran_id;
            let statusBadge = item.is_lengkap ? ' [✅ Selesai]' : ` [Sisa ${item.sisa_jam} JP]`;
            if (item.resource_key) statusBadge += ` [${item.resource_label}]`;
            opt.textContent = `${item.singkatan_mapel} - ${item.nama_mapel}${statusBadge}`;
            mapelSel.appendChild(opt);
          });
        }
      }
    })
    .catch(() => {});

  checkModalConflict();
}

function onModalMapelChanged() {
  const mapelId = document.getElementById('m_mapel')?.value;
  const item = modalRombelCache[mapelId];
  const infoBox = document.getElementById('m_sk_info');

  if (item) {
    const guruSel = document.getElementById('m_guru');
    if (guruSel && item.guru_id) {
      guruSel.value = item.guru_id;
    }
    if (infoBox) {
      infoBox.style.display = 'block';
      infoBox.innerHTML = `📋 Diampu: <u>${item.nama_guru}</u> (Sisa <b>${item.sisa_jam} JP</b>)`;
    }
  } else {
    if (infoBox) infoBox.style.display = 'none';
  }

  checkModalConflict();
}

// Inisialisasi awal saat halaman dibuka
document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('form_rombel')) {
    onFormRombelChanged();
  }
});

function openModalTugasTambahan(guruId, guruNama, tugasTambahan, skTugas) {
  document.getElementById('tt_guru_id').value = guruId;
  document.getElementById('tt_guru_nama').innerText = guruNama;
  document.getElementById('tt_tugas_tambahan').value = tugasTambahan || '';
  document.getElementById('tt_sk_tugas_tambahan').value = skTugas || '';
  
  updateTugasChipsActiveState();
  
  const modalEl = document.getElementById('modalTugasTambahan');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}

function toggleTugasChip(chipName) {
  const input = document.getElementById('tt_tugas_tambahan');
  let currentVal = input.value.trim();
  let items = currentVal ? currentVal.split(',').map(s => s.trim()).filter(Boolean) : [];
  
  const existingIdx = items.findIndex(item => item.toLowerCase() === chipName.toLowerCase());
  if (existingIdx >= 0) {
    items.splice(existingIdx, 1);
  } else {
    items.push(chipName);
  }
  input.value = items.join(', ');
  updateTugasChipsActiveState();
}

function updateTugasChipsActiveState() {
  const input = document.getElementById('tt_tugas_tambahan');
  if (!input) return;
  const currentVal = input.value.toLowerCase();
  const chips = document.querySelectorAll('.tt-preset-chip');
  chips.forEach(chip => {
    const chipText = chip.getAttribute('data-task').toLowerCase();
    if (currentVal.includes(chipText)) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });
}

function toggleMasterCrudDrawer() {
  const drawer = document.getElementById('masterCrudDrawer');
  const btnText = document.getElementById('btnTextMasterDrawer');
  if (!drawer) return;
  const isShown = (drawer.style.display !== 'none');
  drawer.style.display = isShown ? 'none' : 'block';
  if (btnText) {
    btnText.innerHTML = isShown ? 'Kelola Master &amp; Jam Dinamis &darr;' : 'Tutup Panel Master &uarr;';
  }
}

async function submitAddMasterTugas() {
  const namaInput = document.getElementById('new_master_nama');
  const jpInput = document.getElementById('new_master_jp');
  const nama = namaInput?.value.trim();
  const jp = parseInt(jpInput?.value || 2);

  if (!nama) {
    alert('Harap isi nama tugas tambahan terlebih dahulu.');
    namaInput?.focus();
    return;
  }
  if (!jp || jp < 1) {
    alert('Ekuivalensi jam minimal 1 JP.');
    jpInput?.focus();
    return;
  }

  try {
    const res = await fetch('{{ route("akademik.jadwal.master-tugas.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ nama_tugas: nama, ekuivalensi_jam: jp })
    });
    const data = await res.json();
    if (data.success && data.data) {
      const item = data.data;
      const container = document.getElementById('tt_preset_container');
      if (container) {
        const span = document.createElement('span');
        span.className = 'tt-preset-chip';
        span.setAttribute('data-id', item.id);
        span.setAttribute('data-task', item.nama_tugas);
        span.setAttribute('data-jp', item.ekuivalensi_jam);
        span.onclick = function() { toggleTugasChip(item.nama_tugas); };
        span.innerText = `${item.nama_tugas} (+${item.ekuivalensi_jam} JP)`;
        container.appendChild(span);
      }

      const tbody = document.getElementById('master_tugas_tbody');
      if (tbody) {
        const tr = document.createElement('tr');
        tr.id = `row_mt_${item.id}`;
        tr.style.borderBottom = '1px solid #f1f5f9';
        tr.innerHTML = `
          <td style="padding:6px 10px; font-weight:700; color:#334155;">${escapeHtml(item.nama_tugas)}</td>
          <td style="padding:6px 10px; width:75px; text-align:center;">
            <span class="ak-badge ak-badge-info" style="font-size:10px; font-weight:800;">+${item.ekuivalensi_jam} JP</span>
          </td>
          <td style="padding:6px 10px; width:95px; text-align:right;">
            <button type="button" class="btn btn-sm btn-link p-0 text-primary me-2" onclick="editMasterPrompt(${item.id}, '${escapeQuotes(item.nama_tugas)}', ${item.ekuivalensi_jam})" style="font-size:11px; font-weight:700;">Ubah</button>
            <button type="button" class="btn btn-sm btn-link p-0 text-danger" onclick="deleteMasterTugas(${item.id}, '${escapeQuotes(item.nama_tugas)}')" style="font-size:11px; font-weight:700;">Hapus</button>
          </td>
        `;
        tbody.appendChild(tr);
      }

      namaInput.value = '';
      jpInput.value = '2';
      updateTugasChipsActiveState();
    } else {
      alert(data.message || 'Gagal menambahkan tugas.');
    }
  } catch (err) {
    alert('Terjadi kesalahan koneksi.');
  }
}

async function editMasterPrompt(id, currentNama, currentJp) {
  const newNama = prompt('Nama Tugas Tambahan:', currentNama);
  if (newNama === null) return;
  const newJpStr = prompt(`Ekuivalensi Jam / JP untuk "${newNama}":`, currentJp);
  if (newJpStr === null) return;
  const newJp = parseInt(newJpStr);
  if (isNaN(newJp) || newJp < 1) {
    alert('Jumlah JP harus berupa angka positif.');
    return;
  }

  try {
    const res = await fetch(`/dcc/akademik/jadwal/master-tugas/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ nama_tugas: newNama.trim(), ekuivalensi_jam: newJp })
    });
    const data = await res.json();
    if (data.success && data.data) {
      const item = data.data;
      const chip = document.querySelector(`.tt-preset-chip[data-id="${id}"]`);
      if (chip) {
        chip.setAttribute('data-task', item.nama_tugas);
        chip.setAttribute('data-jp', item.ekuivalensi_jam);
        chip.onclick = function() { toggleTugasChip(item.nama_tugas); };
        chip.innerText = `${item.nama_tugas} (+${item.ekuivalensi_jam} JP)`;
      }
      const row = document.getElementById(`row_mt_${id}`);
      if (row) {
        row.innerHTML = `
          <td style="padding:6px 10px; font-weight:700; color:#334155;">${escapeHtml(item.nama_tugas)}</td>
          <td style="padding:6px 10px; width:75px; text-align:center;">
            <span class="ak-badge ak-badge-info" style="font-size:10px; font-weight:800;">+${item.ekuivalensi_jam} JP</span>
          </td>
          <td style="padding:6px 10px; width:95px; text-align:right;">
            <button type="button" class="btn btn-sm btn-link p-0 text-primary me-2" onclick="editMasterPrompt(${item.id}, '${escapeQuotes(item.nama_tugas)}', ${item.ekuivalensi_jam})" style="font-size:11px; font-weight:700;">Ubah</button>
            <button type="button" class="btn btn-sm btn-link p-0 text-danger" onclick="deleteMasterTugas(${item.id}, '${escapeQuotes(item.nama_tugas)}')" style="font-size:11px; font-weight:700;">Hapus</button>
          </td>
        `;
      }
      updateTugasChipsActiveState();
    } else {
      alert(data.message || 'Gagal mengubah master tugas.');
    }
  } catch (err) {
    alert('Terjadi kesalahan koneksi.');
  }
}

async function deleteMasterTugas(id, nama) {
  if (!confirm(`Hapus tugas "${nama}" dari daftar master?`)) return;

  try {
    const res = await fetch(`/dcc/akademik/jadwal/master-tugas/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      }
    });
    const data = await res.json();
    if (data.success) {
      const chip = document.querySelector(`.tt-preset-chip[data-id="${id}"]`);
      if (chip) chip.remove();
      const row = document.getElementById(`row_mt_${id}`);
      if (row) row.remove();
      updateTugasChipsActiveState();
    } else {
      alert(data.message || 'Gagal menghapus tugas.');
    }
  } catch (err) {
    alert('Terjadi kesalahan koneksi.');
  }
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.innerText = str;
  return div.innerHTML;
}
function escapeQuotes(str) {
  return (str || '').replace(/'/g, "\\'");
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

