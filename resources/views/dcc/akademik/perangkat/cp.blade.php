@extends('dcc.akademik.layout')

@section('title', 'Capaian Pembelajaran (CP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.cp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
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

{{-- Pemilih Semester & Perangkat --}}
@include('dcc.akademik.perangkat.partials.selector', ['showMapelSelector' => true])

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px; margin-bottom:18px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px; margin-bottom:18px;">
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
    // Buka form otomatis jika ada error validasi atau session open-form
    $openFormDefault = $errors->any() || session('open_form');
  @endphp

  {{-- ===== WRAPPER UTAMA dengan panel slide ===== --}}
  <div id="cpWrapper" style="position:relative; transition:all 0.3s ease;">

    {{-- ===== KONTEN UTAMA (selalu tampil) ===== --}}
    <div id="cpMain">

      {{-- Card Identitas + Tombol Edit --}}
      <div class="akademik-card" style="margin-bottom:18px; padding:16px 20px; border-left:4px solid var(--ak-primary);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
          <div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
              <span class="ak-badge ak-badge-primary" style="font-size:12px; font-weight:800;">{{ $mapel?->kode_mapel }}</span>
              <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:700;">Kelas {{ $activePerangkat->tingkat }} · Fase {{ $activePerangkat->fase }}</span>
              <span class="badge" style="background:#f1f5f9; color:#475569; font-size:12px;">Semester {{ $activePerangkat->semester == 1 ? 'Ganjil' : 'Genap' }}</span>
              @if($hasCustomCp)
                <span class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-size:11.5px; font-weight:700;">
                  <i class="bi bi-check-circle-fill me-1"></i> Telah Diinput Guru
                </span>
              @else
                <span class="badge" style="background:#fffbeb; color:#92400e; border:1px solid #fde68a; font-size:11.5px; font-weight:700;">
                  <i class="bi bi-info-circle me-1"></i> Belum Diinput
                </span>
              @endif
            </div>
            <h2 style="font-weight:900; font-size:20px; color:var(--ak-dark); margin:0 0 4px 0;">{{ $mapel?->nama_mapel }}</h2>
            <div style="font-size:12px; color:#64748b;">
              Guru Pengampu: <strong style="color:#1e293b;">{{ $activePerangkat->guru?->nama }}</strong>
              &nbsp;·&nbsp; NIP: {{ $activePerangkat->guru?->nip ?? '-' }}
            </div>
          </div>

          {{-- Tombol Edit --}}
          @if($canEdit)
          <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <form action="{{ route('akademik.perangkat.cp.salin-template', $activePerangkat->id) }}" method="POST" style="display:inline;"
                  onsubmit="return confirm('Salin rumusan standar resmi BSKAP 032/2024?')">
              @csrf
              <button type="submit" class="ak-btn ak-btn-secondary" style="padding:7px 14px; font-size:12.5px;">
                <i class="bi bi-lightning-charge me-1"></i> Salin Template BSKAP
              </button>
            </form>
            <button type="button" id="btnOpenEdit" class="ak-btn ak-btn-primary" onclick="toggleEditPanel(true)" style="padding:7px 16px; font-size:12.5px; font-weight:700;">
              <i class="bi bi-pencil-square me-1"></i>
              {{ $hasCustomCp ? 'Edit Capaian Pembelajaran' : 'Input Capaian Pembelajaran' }}
            </button>
          </div>
          @endif
        </div>
      </div>

      {{-- Card CP Aktif --}}
      <div class="akademik-card" style="margin-bottom:18px; border-top:4px solid #0284c7;">
        <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
          <h3 class="akademik-card-title" style="font-size:14.5px; display:flex; align-items:center; gap:8px;">
            <span class="badge bg-primary" style="font-size:11px;">Fase {{ $activePerangkat->fase }}</span>
            Capaian Pembelajaran Aktif
          </h3>
          @if($hasCustomCp)
            <span style="font-size:11px; color:#059669; font-weight:700;"><i class="bi bi-person-check-fill me-1"></i>Rumusan Guru</span>
          @endif
        </div>
        <div class="akademik-card-body" style="padding:18px 22px; font-size:13.5px; line-height:1.75; color:#1e293b;">
          @if(!empty($activeCpText))
            <div style="white-space:pre-line; background:#f8fafc; border-left:4px solid #0284c7; padding:14px 18px; border-radius:8px;">{{ $activeCpText }}</div>
          @else
            <div style="padding:28px; text-align:center; color:#94a3b8; font-style:italic;">
              <i class="bi bi-journal-x" style="font-size:30px; display:block; margin-bottom:8px; opacity:0.5;"></i>
              Capaian Pembelajaran belum diinput.
              @if($canEdit)
                <div style="margin-top:10px;">
                  <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" onclick="toggleEditPanel(true)">
                    <i class="bi bi-pencil-square me-1"></i> Input Sekarang
                  </button>
                </div>
              @endif
            </div>
          @endif

          @if(!empty($activePerangkat->rasional_tujuan))
            <div style="margin-top:18px; padding-top:16px; border-top:1px dashed #e2e8f0;">
              <h4 style="font-size:12.5px; font-weight:800; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                <i class="bi bi-bullseye text-primary me-1"></i> Rasional & Tujuan:
              </h4>
              <div style="white-space:pre-line; font-size:13px; color:#475569; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px;">
                {{ $activePerangkat->rasional_tujuan }}
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- Card Elemen Kompetensi --}}
      <div class="akademik-card">
        <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
          <h3 class="akademik-card-title" style="font-size:14.5px;">
            <i class="bi bi-diagram-3 text-primary me-2"></i> Elemen Kompetensi
          </h3>
          <div style="display:flex; align-items:center; gap:8px;">
            @if($hasCustomElemen)
              <span class="badge" style="background:#ecfdf5; color:#065f46; font-size:11px;">
                <i class="bi bi-check me-1"></i> {{ count($activeElemen) }} Elemen
              </span>
            @endif
            @if($canEdit)
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="toggleEditPanel(true)" style="font-size:12px;">
                <i class="bi bi-pencil me-1"></i> Kelola Elemen
              </button>
            @endif
          </div>
        </div>
        <div class="akademik-card-body" style="padding:0;">
          @if(!empty($activeElemen) && count($activeElemen) > 0)
            <div class="akademik-table-wrap">
              <table class="akademik-table">
                <thead>
                  <tr>
                    <th style="width:40px; text-align:center;">No</th>
                    <th style="width:230px;">Nama Elemen CP</th>
                    <th>Deskripsi Capaian Pembelajaran Elemen</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($activeElemen as $idx => $elem)
                    <tr>
                      <td style="text-align:center; font-weight:800; color:#94a3b8;">{{ $loop->iteration }}</td>
                      <td style="font-weight:700; color:var(--ak-dark); font-size:13px;">
                        {{ is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem }}
                      </td>
                      <td style="font-size:12.5px; line-height:1.6; color:#334155;">
                        {{ is_array($elem) ? ($elem['deskripsi'] ?? $elem['capaian'] ?? '-') : '-' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div style="padding:36px; text-align:center; color:#64748b;">
              <i class="bi bi-diagram-2" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
              <div style="font-weight:700; font-size:13.5px; color:#475569;">Belum Ada Elemen CP</div>
              <div style="font-size:12px; color:#94a3b8; margin-top:3px;">
                Klik <strong>"Kelola Elemen"</strong> di atas untuk menambahkan elemen kompetensi.
              </div>
            </div>
          @endif
        </div>
      </div>

    </div>{{-- /#cpMain --}}

  </div>{{-- /#cpWrapper --}}

  {{-- ===================== DRAWER/PANEL FORM EDIT ===================== --}}
  {{-- Overlay backdrop --}}
  <div id="cpOverlay" onclick="toggleEditPanel(false)"
       style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:1040; transition:opacity 0.3s;"></div>

  {{-- Panel dari kanan --}}
  @if($canEdit)
  <div id="cpEditPanel"
       style="position:fixed; top:0; right:0; height:100vh; width:520px; max-width:95vw;
              background:#fff; z-index:1050; box-shadow:-8px 0 40px rgba(0,0,0,0.18);
              display:flex; flex-direction:column; transform:translateX(100%);
              transition:transform 0.32s cubic-bezier(0.4,0,0.2,1); overflow:hidden;">

    {{-- Header panel --}}
    <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed); padding:16px 20px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
      <div>
        <div style="font-size:15px; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-pencil-square"></i>
          {{ $hasCustomCp ? 'Edit Capaian Pembelajaran' : 'Input Capaian Pembelajaran' }}
        </div>
        <div style="font-size:11.5px; color:#c4b5fd; margin-top:2px;">
          {{ $mapel?->nama_mapel }} · Kelas {{ $activePerangkat->tingkat }} (Fase {{ $activePerangkat->fase }})
        </div>
      </div>
      <button type="button" onclick="toggleEditPanel(false)"
              style="background:rgba(255,255,255,0.15); border:none; color:#fff; width:32px; height:32px; border-radius:8px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    {{-- Body panel (scrollable) --}}
    <form action="{{ route('akademik.perangkat.cp.store', $activePerangkat->id) }}" method="POST" id="formInputCp"
          style="flex:1; display:flex; flex-direction:column; overflow:hidden;">
      @csrf
      <div style="flex:1; overflow-y:auto; padding:20px 22px;">

        {{-- Info template tersedia --}}
        @if(!empty($templateCpText))
          <div style="display:flex; justify-content:space-between; align-items:center; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px;">
            <div style="font-size:12px; color:#1e40af;">
              <i class="bi bi-lightning-charge-fill me-1"></i> Tersedia teks acuan resmi SK BSKAP 032/2024.
            </div>
            <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="loadTemplateToForm()" style="font-size:11.5px; background:#fff;">
              Muat ke Form
            </button>
          </div>
        @endif

        {{-- Teks CP Utama --}}
        <div style="margin-bottom:18px;">
          <label class="ak-form-label">
            Teks Capaian Pembelajaran Fase {{ $activePerangkat->fase }} (Kelas {{ $activePerangkat->tingkat }})
            <span class="text-danger">*</span>
          </label>
          <textarea name="capaian_pembelajaran" id="cpTextInput" class="ak-textarea" rows="7" required
            placeholder="Tuliskan rumusan Capaian Pembelajaran yang harus dicapai peserta didik...">{{ old('capaian_pembelajaran', $activePerangkat->capaian_pembelajaran ?: $activeCpText) }}</textarea>
          <div style="font-size:11px; color:#64748b; margin-top:4px;">
            Rumusan CP ini menjadi dasar perumusan ATP dan modul ajar.
          </div>
        </div>

        {{-- Rasional & Tujuan --}}
        <div style="margin-bottom:20px;">
          <label class="ak-form-label">
            Rasional &amp; Tujuan Mata Pelajaran
            <span style="color:#94a3b8; font-weight:400; font-size:11px;">(Opsional)</span>
          </label>
          <textarea name="rasional_tujuan" class="ak-textarea" rows="3"
            placeholder="Latar belakang pentingnya mapel ini serta sasaran kompetensi lulusan SMK...">{{ old('rasional_tujuan', $activePerangkat->rasional_tujuan) }}</textarea>
        </div>

        {{-- Elemen CP Repeater --}}
        <div style="border-top:1px solid #e2e8f0; padding-top:18px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div>
              <label class="ak-form-label" style="margin-bottom:2px;">Elemen Kompetensi &amp; Capaian</label>
              <div style="font-size:11px; color:#64748b;">Tambahkan elemen untuk memetakan Tujuan Pembelajaran.</div>
            </div>
            <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="addElemenRow()" style="font-size:12px; white-space:nowrap;">
              <i class="bi bi-plus-circle me-1"></i> + Tambah
            </button>
          </div>

          <div id="elemenContainer" style="display:flex; flex-direction:column; gap:10px;">
            @if(!empty($elemenForForm) && count($elemenForForm) > 0)
              @foreach($elemenForForm as $i => $el)
                @php
                  $namaEl      = is_array($el) ? ($el['nama'] ?? $el['elemen'] ?? '') : $el;
                  $deskripsiEl = is_array($el) ? ($el['deskripsi'] ?? $el['capaian'] ?? '') : '';
                @endphp
                <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                  <div style="display:grid; grid-template-columns:1fr 36px; gap:8px; margin-bottom:6px; align-items:center;">
                    <input type="text" name="elemen_cp[{{ $i }}][nama]" class="ak-input" value="{{ $namaEl }}" placeholder="Nama Elemen" style="font-size:12px;">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeElemenRow(this)" style="padding:4px 6px;" title="Hapus">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                  <textarea name="elemen_cp[{{ $i }}][deskripsi]" class="ak-textarea" rows="2" placeholder="Deskripsi: Peserta didik mampu..." style="font-size:12px;">{{ $deskripsiEl }}</textarea>
                </div>
              @endforeach
            @else
              <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                <div style="display:grid; grid-template-columns:1fr 36px; gap:8px; margin-bottom:6px; align-items:center;">
                  <input type="text" name="elemen_cp[0][nama]" class="ak-input" placeholder="Nama Elemen" style="font-size:12px;">
                  <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeElemenRow(this)" style="padding:4px 6px;" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <textarea name="elemen_cp[0][deskripsi]" class="ak-textarea" rows="2" placeholder="Deskripsi: Peserta didik mampu..." style="font-size:12px;"></textarea>
              </div>
            @endif
          </div>
        </div>

      </div>{{-- /scrollable body --}}

      {{-- Footer Aksi --}}
      <div style="border-top:1px solid #e2e8f0; padding:14px 20px; background:#fafafa; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0;">
        <button type="button" class="ak-btn ak-btn-secondary" onclick="toggleEditPanel(false)">
          <i class="bi bi-x me-1"></i> Tutup
        </button>
        <button type="submit" class="ak-btn ak-btn-primary" style="background:linear-gradient(135deg,#7c3aed,#4f46e5); border:none; padding:9px 22px; font-weight:700;">
          <i class="bi bi-check2-circle me-1"></i> Simpan CP
        </button>
      </div>

    </form>
  </div>
  @endif

@else
  {{-- Belum ada perangkat aktif --}}
  <div class="akademik-card" style="padding:40px 24px; text-align:center; color:#64748b; margin-bottom:20px;">
    <i class="bi bi-journal-text" style="font-size:42px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:800; font-size:16px; color:var(--ak-dark); margin-bottom:6px;">Belum Ada Folder Perangkat Terpilih</div>
    <div style="font-size:13px; max-width:540px; margin:0 auto 16px auto; line-height:1.6;">
      Buat folder perangkat ajar terlebih dahulu untuk mulai menginput Capaian Pembelajaran.
    </div>
    @if($isGuru || $isAdminOrWaka)
      <a href="{{ route('akademik.perangkat.index') }}" class="ak-btn ak-btn-primary" style="display:inline-flex; align-items:center; gap:8px;">
        <i class="bi bi-plus-circle"></i> Buat Folder Perangkat
      </a>
    @endif
  </div>

  @if($selectedMapel)
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#f8fafc;">
        <h3 class="akademik-card-title" style="font-size:14.5px;">
          <i class="bi bi-book text-primary me-2"></i> Referensi CP: {{ $selectedMapel->nama_mapel }}
        </h3>
      </div>
      <div class="akademik-card-body" style="padding:20px; font-size:13px; line-height:1.7; color:#334155;">
        <div style="margin-bottom:12px;">
          <strong class="badge bg-primary me-2">Fase E (Kelas X)</strong>
          <div>{{ $selectedMapel->capaian_pembelajaran_fase_e ?: ($selectedMapel->deskripsi_cp ?: 'Belum ada data.') }}</div>
        </div>
        <div style="border-top:1px dashed #e2e8f0; padding-top:12px;">
          <strong class="badge me-2" style="background:#7c3aed; color:#fff;">Fase F (Kelas XI–XII)</strong>
          <div>{{ $selectedMapel->capaian_pembelajaran_fase_f ?: 'Belum ada data.' }}</div>
        </div>
      </div>
    </div>
  @endif
@endif

@endsection

@push('scripts')
<script>
let elemenIndex = {{ !empty($activePerangkat) && !empty($elemenForForm ?? null) ? max(1, count($elemenForForm ?? [])) + 5 : 6 }};

function toggleEditPanel(open) {
  const panel   = document.getElementById('cpEditPanel');
  const overlay = document.getElementById('cpOverlay');
  if (!panel) return;
  if (open) {
    panel.style.transform   = 'translateX(0)';
    overlay.style.display   = 'block';
    document.body.style.overflow = 'hidden';
  } else {
    panel.style.transform   = 'translateX(100%)';
    overlay.style.display   = 'none';
    document.body.style.overflow = '';
  }
}

function addElemenRow(nama = '', deskripsi = '') {
  const container = document.getElementById('elemenContainer');
  if (!container) return;
  const row = document.createElement('div');
  row.className = 'elemen-row';
  row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;';
  row.innerHTML = `
    <div style="display:grid; grid-template-columns:1fr 36px; gap:8px; margin-bottom:6px; align-items:center;">
      <input type="text" name="elemen_cp[${elemenIndex}][nama]" class="ak-input" value="${nama}" placeholder="Nama Elemen" style="font-size:12px;">
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeElemenRow(this)" style="padding:4px 6px;" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
    <textarea name="elemen_cp[${elemenIndex}][deskripsi]" class="ak-textarea" rows="2" placeholder="Deskripsi: Peserta didik mampu..." style="font-size:12px;">${deskripsi}</textarea>
  `;
  container.appendChild(row);
  elemenIndex++;
}

function removeElemenRow(btn) {
  const rows = document.querySelectorAll('.elemen-row');
  if (rows.length > 1) {
    btn.closest('.elemen-row').remove();
  } else {
    btn.closest('.elemen-row').querySelectorAll('input, textarea').forEach(i => i.value = '');
  }
}

function loadTemplateToForm() {
  const templateCp    = @json($templateCpText ?? '');
  const templateElems = @json($templateElemenCp ?? []);
  if (templateCp) {
    const el = document.getElementById('cpTextInput');
    if (el) el.value = templateCp;
  }
  if (templateElems && templateElems.length > 0) {
    const container = document.getElementById('elemenContainer');
    if (container) {
      container.innerHTML = '';
      templateElems.forEach(el => {
        const nama = (typeof el === 'object' && el !== null) ? (el.nama || el.elemen || '') : el;
        const desk = (typeof el === 'object' && el !== null) ? (el.deskripsi || el.capaian || '') : '';
        addElemenRow(nama, desk);
      });
    }
  }
  alert('Teks acuan resmi SK BSKAP berhasil dimuat ke formulir.');
}

// Auto-buka panel jika ada error validasi
@if($errors->any() || session('open_form'))
document.addEventListener('DOMContentLoaded', () => toggleEditPanel(true));
@endif

// Tutup dengan ESC
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') toggleEditPanel(false);
});
</script>
@endpush
