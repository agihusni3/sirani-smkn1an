@extends('dcc.akademik.layout')

@section('title', 'Capaian Pembelajaran (CP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.cp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size:10px;"></i>
  <span>Capaian Pembelajaran (CP)</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-bookmark-star text-primary me-2"></i>
      1. Capaian Pembelajaran (CP)
    </h1>
    <div class="akademik-page-desc">
      Capaian Pembelajaran (CP) dan elemen kompetensi mata pelajaran Kurikulum Merdeka oleh Guru Pengampu.
    </div>
  </div>
</div>

{{-- Selector Bar --}}
@include('dcc.akademik.perangkat.partials.selector', ['showMapelSelector' => true])

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px; margin-bottom:16px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px; margin-bottom:16px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if($activePerangkat)
  @php
    $mapel           = $activePerangkat->mataPelajaran;
    $hasCustomCp     = !empty($activePerangkat->capaian_pembelajaran);
    $activeCpText    = $activePerangkat->resolved_cp;
    $activeElemen    = $activePerangkat->resolved_elemen_cp;
    $hasCustomElemen = !empty($activePerangkat->elemen_cp) && count($activePerangkat->elemen_cp) > 0;
    $elemenForForm   = !empty($activePerangkat->elemen_cp) ? $activePerangkat->elemen_cp : $activeElemen;
  @endphp

  {{-- ===== HEADER CARD: Identitas + Tombol Aksi ===== --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:18px 22px; border-left:4px solid var(--ak-primary);">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      
      {{-- Info Kiri --}}
      <div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
          <span class="ak-badge ak-badge-primary" style="font-size:12px; font-weight:800;">{{ $mapel?->kode_mapel }}</span>
          <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:700;">
            Kelas {{ $activePerangkat->tingkat }} &middot; Fase {{ $activePerangkat->fase }}
          </span>
          <span class="badge" style="background:#f1f5f9; color:#475569; font-size:12px;">
            Semester {{ $activePerangkat->semester == 1 ? 'Ganjil' : 'Genap' }}
          </span>
          @if($hasCustomCp)
            <span class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-size:11.5px; font-weight:700;">
              <i class="bi bi-check-circle-fill me-1"></i> CP Telah Diinput Guru
            </span>
          @else
            <span class="badge" style="background:#fffbeb; color:#92400e; border:1px solid #fde68a; font-size:11.5px; font-weight:700;">
              <i class="bi bi-info-circle me-1"></i> Belum Diinput
            </span>
          @endif
        </div>
        <h2 style="font-weight:900; font-size:20px; color:var(--ak-dark); margin:0 0 3px 0;">{{ $mapel?->nama_mapel }}</h2>
        <div style="font-size:12px; color:#64748b;">
          Guru Pengampu: <strong style="color:#1e293b;">{{ $activePerangkat->guru?->nama }}</strong>
          &ensp;&middot;&ensp; NIP: {{ $activePerangkat->guru?->nip ?? '-' }}
        </div>
      </div>

      {{-- Tombol Aksi --}}
      @if($canEdit)
      <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <form action="{{ route('akademik.perangkat.cp.salin-template', $activePerangkat->id) }}" method="POST" style="display:inline;"
              onsubmit="return confirm('Salin rumusan standar resmi BSKAP 032/2024 ke dokumen ini?')">
          @csrf
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12.5px; padding:7px 14px;">
            <i class="bi bi-lightning-charge me-1"></i> Salin Template BSKAP
          </button>
        </form>
        <button type="button" id="btnOpenEdit" onclick="togglePanel(true)"
                class="ak-btn ak-btn-primary" style="font-size:12.5px; padding:7px 16px; font-weight:700;">
          <i class="bi bi-pencil-square me-1"></i>
          {{ $hasCustomCp ? 'Edit Capaian Pembelajaran' : 'Input Capaian Pembelajaran' }}
        </button>
      </div>
      @endif
    </div>
  </div>

  {{-- ===== SECTION 1: TEKS CP ===== --}}
  <div class="akademik-card" style="margin-bottom:20px; border-top:4px solid #0284c7;">
    <div class="akademik-card-header" style="background:#f0f9ff; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:14.5px; display:flex; align-items:center; gap:8px; margin:0;">
        <i class="bi bi-file-text text-primary"></i>
        Teks Capaian Pembelajaran
        <span class="badge bg-primary" style="font-size:11px; font-weight:600;">Fase {{ $activePerangkat->fase }}</span>
      </h3>
      @if($hasCustomCp)
        <span style="font-size:11.5px; color:#059669; font-weight:700; display:flex; align-items:center; gap:4px;">
          <i class="bi bi-person-check-fill"></i> Rumusan Guru
        </span>
      @else
        <span style="font-size:11.5px; color:#92400e; font-weight:600; display:flex; align-items:center; gap:4px;">
          <i class="bi bi-info-circle"></i> Acuan Standar Nasional
        </span>
      @endif
    </div>
    <div class="akademik-card-body" style="padding:0;">
      @if(!empty($activeCpText))
        <div style="padding:22px 26px; font-size:13.5px; line-height:1.85; color:#1e293b; background:#fff;">{!! format_narasi_kbm($activeCpText) !!}</div>
      @else
        <div style="padding:40px; text-align:center; color:#94a3b8;">
          <i class="bi bi-journal-x" style="font-size:36px; display:block; margin-bottom:12px; opacity:0.5;"></i>
          <div style="font-weight:700; font-size:14px; color:#475569; margin-bottom:6px;">Belum Ada Capaian Pembelajaran</div>
          <div style="font-size:12.5px; margin-bottom:14px;">Guru pengampu belum menginput teks CP untuk perangkat ini.</div>
          @if($canEdit)
            <button type="button" class="ak-btn ak-btn-primary" onclick="togglePanel(true)">
              <i class="bi bi-pencil-square me-1"></i> Input Sekarang
            </button>
          @endif
        </div>
      @endif
    </div>
  </div>

  {{-- ===== SECTION 2: RASIONAL (jika ada) ===== --}}
  @if(!empty($activePerangkat->rasional_tujuan))
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header" style="background:#f8fafc;">
      <h3 class="akademik-card-title" style="font-size:14.5px; display:flex; align-items:center; gap:8px; margin:0;">
        <i class="bi bi-bullseye text-primary"></i> Rasional &amp; Tujuan Mata Pelajaran
      </h3>
    <div class="akademik-card-body" style="padding:22px 26px; font-size:13.5px; line-height:1.85; color:#334155; background:#fff;">{!! format_narasi_kbm($activePerangkat->rasional_tujuan) !!}</div>
  </div>
  @endif

  {{-- ===== SECTION 3: ELEMEN KOMPETENSI ===== --}}
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:14.5px; display:flex; align-items:center; gap:8px; margin:0;">
        <i class="bi bi-diagram-3 text-primary"></i>
        Elemen Kompetensi CP
        @if(!empty($activeElemen) && count($activeElemen) > 0)
          <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-size:11px; font-weight:700;">
            {{ count($activeElemen) }} Elemen
          </span>
        @endif
      </h3>
      @if($canEdit)
        <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="togglePanel(true)" style="font-size:12px;">
          <i class="bi bi-pencil me-1"></i> Kelola Elemen
        </button>
      @endif
    </div>
    <div class="akademik-card-body" style="padding:0;">
      @if(!empty($activeElemen) && count($activeElemen) > 0)
        <div class="akademik-table-wrap">
          <table class="akademik-table">
            <thead>
              <tr>
                <th style="width:46px; text-align:center;">No</th>
                <th style="width:260px;">Nama Elemen CP</th>
                <th>Deskripsi Capaian Pembelajaran Elemen</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeElemen as $idx => $elem)
              <tr>
                <td style="text-align:center; font-weight:800; color:#94a3b8; font-size:13px;">{{ $loop->iteration }}</td>
                <td style="font-weight:700; color:var(--ak-dark); font-size:13px; vertical-align:top; padding-top:14px;">
                  {{ is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem }}
                </td>
                <td style="font-size:13px; line-height:1.7; color:#334155; text-align:justify;">
                  {{ is_array($elem) ? ($elem['deskripsi'] ?? $elem['capaian'] ?? '-') : '-' }}
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div style="padding:40px; text-align:center; color:#64748b;">
          <i class="bi bi-diagram-2" style="font-size:34px; color:#cbd5e1; display:block; margin-bottom:10px;"></i>
          <div style="font-weight:700; font-size:13.5px; color:#475569; margin-bottom:4px;">Belum Ada Elemen Kompetensi</div>
          <div style="font-size:12px; color:#94a3b8;">
            Tambahkan elemen CP melalui tombol
            @if($canEdit)
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="togglePanel(true)" style="font-size:12px; margin-left:4px;">
                <i class="bi bi-pencil me-1"></i> Kelola Elemen
              </button>
            @else
              <strong>"Edit Capaian Pembelajaran"</strong>.
            @endif
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- ===================== SLIDE-IN PANEL EDIT CP ===================== --}}
  {{-- Overlay --}}
  <div id="cpOverlay" onclick="togglePanel(false)"
       style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:1040; backdrop-filter:blur(2px); transition:opacity 0.3s;">
  </div>

  {{-- Panel Drawer --}}
  @if($canEdit)
  <div id="cpEditPanel"
       style="position:fixed; top:0; right:0; height:100vh; width:540px; max-width:95vw;
              background:#fff; z-index:1050;
              box-shadow:-12px 0 50px rgba(0,0,0,0.2);
              display:flex; flex-direction:column;
              transform:translateX(100%);
              transition:transform 0.32s cubic-bezier(0.4,0,0.2,1);">

    {{-- Header Panel --}}
    <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed); padding:18px 22px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
      <div>
        <div style="font-size:15px; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-pencil-square"></i>
          {{ $hasCustomCp ? 'Edit Capaian Pembelajaran' : 'Input Capaian Pembelajaran' }}
        </div>
        <div style="font-size:12px; color:#c4b5fd; margin-top:3px;">
          {{ $mapel?->nama_mapel }} &middot; Kelas {{ $activePerangkat->tingkat }} (Fase {{ $activePerangkat->fase }})
        </div>
      </div>
      <button type="button" onclick="togglePanel(false)"
              style="background:rgba(255,255,255,0.15); border:none; color:#fff; width:34px; height:34px; border-radius:8px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
              onmouseover="this.style.background='rgba(255,255,255,0.25)'"
              onmouseout="this.style.background='rgba(255,255,255,0.15)'">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    {{-- Info acuan BSKAP --}}
    @if(!empty($templateCpText))
    <div style="background:#eff6ff; border-bottom:1px solid #bfdbfe; padding:10px 22px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-shrink:0;">
      <div style="font-size:12px; color:#1e40af; display:flex; align-items:center; gap:6px;">
        <i class="bi bi-lightning-charge-fill"></i>
        Tersedia acuan resmi SK BSKAP 032/2024.
      </div>
      <button type="button" onclick="loadTemplateToForm()"
              style="background:#fff; border:1px solid #bfdbfe; color:#1d4ed8; font-size:12px; font-weight:600; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap;">
        Muat ke Form
      </button>
    </div>
    @endif

    {{-- Body Form (scrollable) --}}
    <form action="{{ route('akademik.perangkat.cp.store', $activePerangkat->id) }}" method="POST"
          style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
      @csrf
      <div style="flex:1; overflow-y:auto; padding:20px 22px;">

        {{-- Textarea CP --}}
        <div style="margin-bottom:18px;">
          <label class="ak-form-label" style="margin-bottom:6px;">
            Teks Capaian Pembelajaran Fase {{ $activePerangkat->fase }} (Kelas {{ $activePerangkat->tingkat }})
            <span class="text-danger">*</span>
          </label>
          <textarea name="capaian_pembelajaran" id="cpTextInput" class="ak-textarea" rows="8" required
            style="font-size:13px; line-height:1.7; resize:vertical;"
            placeholder="Tuliskan rumusan Capaian Pembelajaran yang harus dicapai peserta didik pada mata pelajaran ini...">{{ old('capaian_pembelajaran', $activePerangkat->capaian_pembelajaran ?: $activeCpText) }}</textarea>
          <div style="font-size:11px; color:#64748b; margin-top:5px;">
            <i class="bi bi-info-circle me-1"></i>
            Rumusan CP menjadi dasar perumusan ATP dan Modul Ajar.
          </div>
        </div>

        {{-- Rasional --}}
        <div style="margin-bottom:22px;">
          <label class="ak-form-label" style="margin-bottom:6px;">
            Rasional &amp; Tujuan Mata Pelajaran
            <span style="color:#94a3b8; font-weight:400; font-size:11px; margin-left:4px;">(Opsional)</span>
          </label>
          <textarea name="rasional_tujuan" class="ak-textarea" rows="3"
            style="font-size:13px; line-height:1.7; resize:vertical;"
            placeholder="Latar belakang pentingnya mata pelajaran ini dan sasaran kompetensi lulusan SMK...">{{ old('rasional_tujuan', $activePerangkat->rasional_tujuan) }}</textarea>
        </div>

        {{-- Elemen CP Repeater --}}
        <div style="border-top:1px solid #e2e8f0; padding-top:20px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; gap:10px;">
            <div>
              <div style="font-size:13.5px; font-weight:700; color:#1e293b; margin-bottom:2px;">
                Elemen Kompetensi &amp; Capaian
              </div>
              <div style="font-size:11.5px; color:#64748b;">Tambahkan elemen untuk memetakan Tujuan Pembelajaran.</div>
            </div>
            <button type="button" onclick="addElemenRow()"
                    style="background:#f1f5f9; border:1px solid #e2e8f0; color:#475569; font-size:12px; font-weight:600; padding:6px 12px; border-radius:8px; cursor:pointer; white-space:nowrap; display:flex; align-items:center; gap:5px;">
              <i class="bi bi-plus-circle"></i> Tambah Elemen
            </button>
          </div>

          <div id="elemenContainer" style="display:flex; flex-direction:column; gap:10px;">
            @if(!empty($elemenForForm) && count($elemenForForm) > 0)
              @foreach($elemenForForm as $i => $el)
                @php
                  $namaEl      = is_array($el) ? ($el['nama'] ?? $el['elemen'] ?? '') : $el;
                  $deskripsiEl = is_array($el) ? ($el['deskripsi'] ?? $el['capaian'] ?? '') : '';
                @endphp
                <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                  <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                    <input type="text" name="elemen_cp[{{ $i }}][nama]" class="ak-input" value="{{ $namaEl }}"
                           placeholder="Nama Elemen (misal: Berpikir Komputasional)"
                           style="flex:1; font-size:12.5px; font-weight:600;">
                    <button type="button" onclick="removeElemenRow(this)"
                            style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; width:32px; height:32px; border-radius:7px; cursor:pointer; font-size:13px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                  <textarea name="elemen_cp[{{ $i }}][deskripsi]" class="ak-textarea" rows="2"
                            placeholder="Deskripsi: Peserta didik mampu..."
                            style="font-size:12.5px; line-height:1.6; resize:vertical;">{{ $deskripsiEl }}</textarea>
                </div>
              @endforeach
            @else
              <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                  <input type="text" name="elemen_cp[0][nama]" class="ak-input"
                         placeholder="Nama Elemen (misal: Elemen 1)"
                         style="flex:1; font-size:12.5px; font-weight:600;">
                  <button type="button" onclick="removeElemenRow(this)"
                          style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; width:32px; height:32px; border-radius:7px; cursor:pointer; font-size:13px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <textarea name="elemen_cp[0][deskripsi]" class="ak-textarea" rows="2"
                          placeholder="Deskripsi: Peserta didik mampu..."
                          style="font-size:12.5px; line-height:1.6; resize:vertical;"></textarea>
              </div>
            @endif
          </div>
        </div>

      </div>{{-- /scrollable --}}

      {{-- Footer Simpan --}}
      <div style="border-top:1px solid #e2e8f0; padding:14px 22px; background:#fafafa; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0;">
        <button type="button" onclick="togglePanel(false)" class="ak-btn ak-btn-secondary" style="padding:8px 18px; font-size:13px;">
          <i class="bi bi-x me-1"></i> Tutup
        </button>
        <button type="submit" class="ak-btn" style="background:linear-gradient(135deg,#7c3aed,#4f46e5); border:none; color:#fff; padding:9px 24px; font-size:13.5px; font-weight:700; border-radius:8px; cursor:pointer; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-check2-circle"></i> Simpan Capaian Pembelajaran
        </button>
      </div>

    </form>
  </div>
  @endif

@else
  {{-- Belum ada perangkat aktif --}}
  <div class="akademik-card" style="padding:50px 30px; text-align:center; color:#64748b;">
    <i class="bi bi-journal-text" style="font-size:48px; color:#cbd5e1; display:block; margin-bottom:14px;"></i>
    <div style="font-weight:800; font-size:16px; color:var(--ak-dark); margin-bottom:8px;">Belum Ada Folder Perangkat Terpilih</div>
    <div style="font-size:13px; max-width:500px; margin:0 auto 18px auto; line-height:1.7;">
      Buat folder perangkat ajar terlebih dahulu untuk mulai menginput Capaian Pembelajaran pada Semester {{ $semester }}.
    </div>
    @if($isGuru || $isAdminOrWaka)
      <a href="{{ route('akademik.perangkat.index') }}" class="ak-btn ak-btn-primary" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; font-size:13.5px;">
        <i class="bi bi-plus-circle"></i> Buat Folder Perangkat
      </a>
    @endif
  </div>

  @if($selectedMapel)
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#f8fafc;">
        <h3 class="akademik-card-title" style="font-size:14.5px;">
          <i class="bi bi-book text-primary me-2"></i> Referensi CP Master: {{ $selectedMapel->nama_mapel }}
        </h3>
      </div>
      <div class="akademik-card-body" style="padding:22px 26px;">
        @if($selectedMapel->capaian_pembelajaran_fase_e)
          <div style="margin-bottom:16px;">
            <div class="badge bg-primary mb-2">Fase E (Kelas X)</div>
            <div style="font-size:13.5px; line-height:1.8; color:#334155;">{!! format_narasi_kbm($selectedMapel->capaian_pembelajaran_fase_e) !!}</div>
          </div>
        @endif
        @if($selectedMapel->capaian_pembelajaran_fase_f)
          <div style="border-top:1px dashed #e2e8f0; padding-top:16px;">
            <div class="badge mb-2" style="background:#7c3aed; color:#fff;">Fase F (Kelas XI–XII)</div>
            <div style="font-size:13.5px; line-height:1.8; color:#334155;">{!! format_narasi_kbm($selectedMapel->capaian_pembelajaran_fase_f) !!}</div>
          </div>
        @endif
      </div>
    </div>
  @endif
@endif

@endsection

@push('scripts')
<script>
let elemenIndex = {{ !empty($activePerangkat) && !empty($elemenForForm ?? null) ? max(1, count($elemenForForm ?? [])) + 5 : 6 }};

function togglePanel(open) {
  const panel   = document.getElementById('cpEditPanel');
  const overlay = document.getElementById('cpOverlay');
  if (!panel) return;
  if (open) {
    panel.style.transform        = 'translateX(0)';
    overlay.style.display        = 'block';
    document.body.style.overflow = 'hidden';
  } else {
    panel.style.transform        = 'translateX(100%)';
    overlay.style.display        = 'none';
    document.body.style.overflow = '';
  }
}

function addElemenRow(nama = '', deskripsi = '') {
  const container = document.getElementById('elemenContainer');
  if (!container) return;
  const row = document.createElement('div');
  row.className = 'elemen-row';
  row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;';
  row.innerHTML = `
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
      <input type="text" name="elemen_cp[${elemenIndex}][nama]" class="ak-input" value="${nama}"
             placeholder="Nama Elemen (misal: Elemen ${elemenIndex + 1})"
             style="flex:1; font-size:12.5px; font-weight:600;">
      <button type="button" onclick="removeElemenRow(this)"
              style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; width:32px; height:32px; border-radius:7px; cursor:pointer; font-size:13px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-trash"></i>
      </button>
    </div>
    <textarea name="elemen_cp[${elemenIndex}][deskripsi]" class="ak-textarea" rows="2"
              placeholder="Deskripsi: Peserta didik mampu..."
              style="font-size:12.5px; line-height:1.6; resize:vertical;">${deskripsi}</textarea>
  `;
  container.appendChild(row);
  elemenIndex++;
}

function removeElemenRow(btn) {
  const rows = document.querySelectorAll('.elemen-row');
  if (rows.length > 1) {
    btn.closest('.elemen-row').remove();
  } else {
    btn.closest('.elemen-row').querySelectorAll('input, textarea').forEach(el => el.value = '');
  }
}

function loadTemplateToForm() {
  const cp    = @json($templateCpText ?? '');
  const elems = @json($templateElemenCp ?? []);
  if (cp) {
    const ta = document.getElementById('cpTextInput');
    if (ta) ta.value = cp;
  }
  if (elems && elems.length > 0) {
    const container = document.getElementById('elemenContainer');
    if (container) {
      container.innerHTML = '';
      elems.forEach(el => {
        const nama = (typeof el === 'object' && el) ? (el.nama || el.elemen || '') : el;
        const desk = (typeof el === 'object' && el) ? (el.deskripsi || el.capaian || '') : '';
        addElemenRow(nama, desk);
      });
    }
  }
  alert('Teks acuan resmi SK BSKAP berhasil dimuat. Anda dapat mengedit sebelum menyimpan.');
}

// Auto-buka jika ada error validasi
@if(isset($errors) && $errors->any())
document.addEventListener('DOMContentLoaded', () => togglePanel(true));
@endif

// Tutup dengan ESC
document.addEventListener('keydown', e => { if (e.key === 'Escape') togglePanel(false); });
</script>
@endpush
